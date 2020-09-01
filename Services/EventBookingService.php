<?php

namespace App\Services;

use App\Models\EventBooking;
use App\Models\Schedule;
use App\Repositories\Eloquent\EventBookingRepository;
use App\Repositories\Eloquent\ScheduleRepository;
use App\Repositories\Eloquent\StaffRepository;
use Carbon\Carbon;
use Illuminate\Support\Str;
use DB;
use Datetime;

/**
 * Class EventBookingService.
 */
class EventBookingService
{
    /** @var EventBookingRepository $eventBookingRepo */
    protected $eventBookingRepo;

    /** @var ScheduleRepository $eventBookingRepo */
    protected $scheduleRepo;

    /** @var StaffRepository $staffRepo */
    protected $staffRepo;

    /**
     * EventBookingService constructor.
     *
     * @param EventBookingRepository $eventBookingRepo
     * @param ScheduleRepository $scheduleRepo
     * @param StaffRepository $staffRepo
     */
    public function __construct(
        EventBookingRepository $eventBookingRepo,
        ScheduleRepository $scheduleRepo,
        StaffRepository $staffRepo
    ) {
        $this->eventBookingRepo = $eventBookingRepo;
        $this->scheduleRepo = $scheduleRepo;
        $this->staffRepo = $staffRepo;
    }

    /**
     * Submit event booking
     *
     * @param array $data
     * @return mixed
     * @throws \Exception
     */
    public function submitEventBooking($data)
    {
        $data['booking_id'] = (string) Str::uuid();

        try {
            return DB::transaction(function () use ($data) {
                $schedule = $this->scheduleRepo->getLockForUpdateById($data['schedule_id']);
                $eventBooking = $this->eventBookingRepo->findByAttributes(['email' => $data['email'], 'schedule_id' => $data['schedule_id']]);

                if ($eventBooking) {
                    return [
                        'status' => false,
                        'message' => trans('labels.already_booked'),
                    ];
                }

                if ($schedule->capacity > $schedule->count_booking) {
                    $this->scheduleRepo->update(['count_booking' => DB::raw('count_booking + 1')], $data['schedule_id']);
                    $booking = $this->eventBookingRepo->create($data);
                } else {
                    return [
                        'status' => true,
                        'message' => trans('labels.booking_full'),
                    ];
                }

                return $booking;
            });
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Get Booking Details
     *
     * @param int $bookingId
     * @return mixed
     */
    public function getBookingDetails($bookingId)
    {
        $details = $this->eventBookingRepo->findByAttributes(['booking_id' => $bookingId]);
        if (!$details) {
            return abort(404);
        }

        return $details->load(['scheduleInfo', 'scheduleInfo.eventInfo']);
    }

    /**
     * Delete Booking
     *
     * @param array $booking
     * @return mixed
     * @throws \Exception
     */
    public function cancelBooking($booking)
    {
        try {
            return DB::transaction(function () use ($booking) {

                $this->scheduleRepo->getLockForUpdateById($booking->schedule_id);
                $this->scheduleRepo->update(['count_booking' => DB::raw('count_booking - 1')], $booking->schedule_id);
                $this->eventBookingRepo->update(['status' => BOOKING_CANCEL_STATUS], $booking->id);

                return $booking;
            });
        } catch (\Exception $e) {
            throw new \Exception(__('message.error_message'));
        }
    }

    /**
     * Check booking details
     *
     * @param object $booking
     * @return array
     * @throws \Exception
     */
    public function checkBookingDetails($booking)
    {
        $date = new DateTime($booking->scheduleInfo->date . ' ' . $booking->scheduleInfo->start_time);
        $date->modify('-' . $booking->scheduleInfo->eventInfo->cancel_time . ' hour');
        $currentDate = new DateTime('now');

        if ($date < $currentDate) {
            return [
                'status' => false,
                'message' => trans('labels.cannot_modified')
            ];
        }

        return [
            'status' => true,
            'message' => 'Validate'
        ];
    }

    /**
     * Update booking
     *
     * @param array $booking
     * @return mixed
     * @throws \Exception
     */
    public function submitUpdateBooking($booking)
    {
        try {
            return DB::transaction(function () use ($booking) {
                $schedule = $this->scheduleRepo->getLockForUpdateById($booking['schedule_id']);
                $bookingDetails = $this->getBookingDetails($booking['booking_id']);
                $bookingModification = $this->checkBookingDetails($bookingDetails);
                $eventBooking = $this->eventBookingRepo->findByAttributes(['email' => $bookingDetails->email, 'schedule_id' => $booking['schedule_id']]);

                if ($booking['schedule_id'] != $booking['old_schedule_id'] && $eventBooking) {
                    return [
                        'flag' => 'booked',
                        'message' => trans('labels.already_booked'),
                    ];
                }

                if (!$bookingModification['status']) {
                    return [
                        'flag' => 'past',
                        'message' => trans('labels.cannot_modified'),
                    ];
                }

                if ($bookingDetails['schedule_id'] != $booking['old_schedule_id']) {
                    return [
                        'flag' => 'past',
                        'message' => trans('labels.cannot_modified'),
                    ];
                }

                if ($booking['schedule_id'] == $booking['old_schedule_id'] || $schedule->capacity > $schedule->count_booking) {
                    // add count booking
                    $this->scheduleRepo->update(['count_booking' => DB::raw('count_booking + 1')], $booking['schedule_id']);
                    $this->eventBookingRepo->update(['schedule_id' => $booking['schedule_id']], $booking['id']);

                    // minus count booking
                    $this->scheduleRepo->getLockForUpdateById($booking['old_schedule_id']);
                    $this->scheduleRepo->update(['count_booking' => DB::raw('count_booking - 1')], $booking['old_schedule_id']);
                } else {
                    return [
                        'flag' => 'full',
                        'message' => trans('labels.booking_full'),
                    ];
                }

                return $booking;
            });
        } catch (\Exception $e) {
            throw new \Exception(__('message.error_message'));
        }
    }

    /**
     * Get tomorrow's event bookings that will be used in sending the booking reminder mail
     *
     * @param int $duration
     * @return array
     */
    public function getBookingsForReminder($duration)
    {
        $bookingsForReminder = [];
        $defaultStaffInfo = $this->staffRepo->findByAttributes(['id' => config('staff.id')])->first();

        $date = Carbon::now()->addDays($duration)->format('Y-m-d');
        $schedules = Schedule::whereHas('bookings')
            ->where('date', $date)
            ->with([
                'bookings' => function ($query) {
                    $query->where('status', BOOKING_JOINED_STATUS);
                },
                'eventInfo'
            ])
            ->get();

        foreach ($schedules as $schedule) {
            $eventInfo = $schedule->eventInfo;
            $staffInfo = $eventInfo->staffInfo ?? $defaultStaffInfo;

            foreach ($schedule->bookings as $booking) {
                $bookingData = [
                    'email' => $booking->email ?? '',
                    'full_name' => $booking->full_name ?? '',
                    'staff_name' => $staffInfo->full_name ?? '',
                    'staff_contact_number' => $staffInfo->contact_number ?? '',
                    'event_id' => $eventInfo->id ?? '',
                    'event_title' => $eventInfo->title ?? '',
                    'event_date' => $schedule->date ?? '',
                    'event_day_of_week' => weekName($schedule->date) ?? '',
                    'event_start_time' => dateFormat($schedule->start_time, 'H:i') ?? '',
                    'event_end_time' => dateFormat($schedule->end_time, 'H:i') ?? '',
                    'place' => $eventInfo->place ?? '',
                    'google_map_url' => $eventInfo->google_map_url ?? '',
                ];

                $bookingsForReminder[] = $bookingData;
            }
        }

        return $bookingsForReminder;
    }
}
