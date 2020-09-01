<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Services\EventBookingService;
use Illuminate\Http\Request;
use App\Mails\Booking\BookingMail;
use App\Requests\Guest\ContactFormRequest;

class BookingController extends Controller
{
    /** @var EventBookingService $eventBookingService */
    protected $eventBookingService;

    /**
     * contruct
     * EventBookingService
     */
    public function __construct(EventBookingService $eventBookingService)
    {
        $this->eventBookingService = $eventBookingService;
    }

    /**
     * contact form
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function contactForm(Request $request)
    {
        $datas = $request->all();

        return view('guest.booking.contact_form', compact('datas'));
    }

    /**
     * Submit Booking
     *
     * @param Request $request
     * @throws \Exception
     */
    public function submitBooking(ContactFormRequest $request)
    {
        try {
            $allData = $request->all();
            if(\Cookie::get('adcode')){
                $allData['adcode'] = \Cookie::get('adcode');
            }
            $booking = $this->eventBookingService->submitEventBooking($allData);

            if (isset($booking['message'])) {
                return response()->json($booking);
            }

            $booking = $this->formatDateTime($booking);
            $booking->email_subject = __('common.email_subject_create_booking', ['name' => $booking->full_name]);
            $booking->email_template = 'mails.booking.create-booking';

            // sending mail to user and admin
            foreach ([$booking->email, config('mail.admin.email_add')] as $email) {
                sendMail(
                    $email,
                    new BookingMail(['booking' => $booking])
                );
            }

            return response()->json($booking);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Thank you page
     *
     * @param integer $bookingId
     * @return mixed
     */
    public function thankYou($bookingId)
    {
        $details = $this->fetchDetails($bookingId);

        return view('guest.booking.thank_you', compact('details'));
    }

    /**
     * Booking details
     *
     * @param integer $bookingId
     * @return mixed
     */
    public function bookingDetails($bookingId)
    {
        $details = $this->fetchDetails($bookingId);

        return view('guest.booking.details', compact('details'));
    }

    /**
     * Booking cancel
     *
     * @param Request $request
     * @return mixed
     */
    public function cancel(Request $request)
    {
        $booking = $this->eventBookingService->getBookingDetails($request->booking_id);
        $checkBooking = $this->eventBookingService->checkBookingDetails($booking);

        if ($checkBooking['status']) {
            $this->eventBookingService->cancelBooking($booking);

            $booking = $this->formatDateTime($booking);
            $booking->email_subject = __('common.email_subject_cancel_booking', ['name' => $booking->full_name]);
            $booking->email_template = 'mails.booking.cancel-booking';

            // sending mail to user and admin
            foreach ([$booking->email, config('mail.admin.email_add')] as $email) {
                sendMail(
                    $email,
                    new BookingMail(['booking' => $booking])
                );
            }
        }

        return response()->json($checkBooking);
    }

    /**
     * Edit booking form
     *
     * @param Integer $bookingId
     * @return mixed
     */
    public function editForm($bookingId)
    {
        $details = $this->fetchDetails($bookingId);

        return view('guest.booking.edit', compact('details'));
    }

    /**
     * Submit booking update
     *
     * @param Request $request
     * @return mixed
     * @throws \Exception
     */
    public function submitUpdate(Request $request)
    {
        try {
            $result = $this->eventBookingService->submitUpdateBooking($request->all());

            if (isset($result['message'])) {
                return response()->json($result);
            }

            $booking = $this->fetchDetails($result['booking_id']);

            $booking->email_subject = __('common.email_subject_update_booking', ['name' => $booking->full_name]);
            $booking->email_template = 'mails.booking.update-booking';

            // sending mail to user and admin
            foreach ([$booking->email, config('mail.admin.email_add')] as $email) {
                sendMail(
                    $email,
                    new BookingMail(['booking' => $booking])
                );
            }

            return response()->json($booking);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Fetch booking details
     *
     * @param Integer $bookingId
     * @return mixed
     */
    private function fetchDetails($bookingId)
    {
        $details = $this->eventBookingService->getBookingDetails($bookingId);
        $checkBooking = $this->eventBookingService->checkBookingDetails($details);

        if (empty($details) || $details->status == 1) {
            return abort(404);
        }

        $details = $this->formatDateTime($details);
        $details['is_modified'] = $checkBooking;

        return $details;
    }

    /**
     * Format Data and Time
     *
     * @param Object $details
     * @return mixed
     */
    private function formatDateTime($details)
    {
        $details->scheduleInfo->date = dateFormat($details->scheduleInfo->date, 'Y年m月d日') . ' ('.weekName($details->scheduleInfo->date).')';
        $details->scheduleInfo->start_time = dateFormat($details->scheduleInfo->start_time, 'H:i');
        $details->scheduleInfo->end_time = dateFormat($details->scheduleInfo->end_time, 'H:i');

        return $details;
    }

    /**
     * Terms and condition
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function terms()
    {
        return view('guest.booking.terms_of_use');
    }
}
