<?php

namespace App\Services;

use App\Models\Staff;
use App\Repositories\Eloquent\EventRepository;
use App\Repositories\Eloquent\ScheduleRepository;
use App\Exceptions\GeneralException;
use Illuminate\Support\Facades\DB;

/**
 * Class EventService.
 */
class EventService
{

    /**
     * @var EventRepository $eventRepo
     */
    protected $eventRepo;

    /**
     * @var ScheduleRepository $scheduleRepo
     */
    protected $scheduleRepo;

    /**
     * EventService constructor.
     *
     * @param EventRepository $eventRepo
     * @param ScheduleRepository $scheduleRepo
     */
    public function __construct(EventRepository $eventRepo, ScheduleRepository $scheduleRepo)
    {
        $this->eventRepo    = $eventRepo;
        $this->scheduleRepo = $scheduleRepo;
    }

    /**
     * Return event list by page
     *
     * @param array $data
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getEventByPage($data)
    {
        $query = $this->eventRepo->buildQueryByAttributes([])
                                 ->select(
                                     'events.*',
                                     DB::raw('(SELECT SUM(count_booking) from schedules where schedules.event_id = events.id) as count_booking'),
                                     DB::raw('(SELECT SUM(capacity) from schedules where schedules.event_id = events.id) as capacity')
                                 )
                                 ->orderBy('events.created_at', 'desc')
                                 ->with('schedules');

        if (!empty($data['eventName'])) {
            $query = $query->whereRaw('events.title like "%' . $data['eventName'] . '%"');
        }

        return $query->paginate(10);
    }

    /**
     * Remove an event
     *
     * @param int $id
     * @return mixed
     */
    public function deleteEvent($id)
    {
        return $this->eventRepo->destroy(['id' => $id]);
    }

    /**
     * Remove a schedule
     *
     * @param int $id
     * @return mixed
     */
    public function deleteSchedule($id)
    {
        return $this->scheduleRepo->destroy(['id' => $id]);
    }

    /**
     * Return the event detail of certain event id pass
     *
     * @param int $id event id pass
     * @return EventRepository|\Illuminate\Database\Eloquent\Model|mixed|object|null
     */
    public function getEventDetails(Int $id)
    {
        return $this->eventRepo
            ->findByAttributes(['id' => $id]);
    }

    /**
     * Get event schedules
     *
     * @param int $id
     * @return EventRepository|\Illuminate\Database\Eloquent\Model|mixed|object|null
     */
    public function getEventSchedules($id)
    {
        return $this->eventRepo
            ->findByAttributes(['id' => $id])
            ->load('schedulesWithOld');
    }

    /**
     * Prepare event and schedules data
     *
     * @param int $eventId
     * @return array
     */
    public function prepareEventAndSchedulesData($eventId)
    {
        $eventScheduleData  = $this->getEventSchedules($eventId);
        $schedules          = $eventScheduleData->schedulesWithOld;
        $event = [
            'id'               => $eventScheduleData->id,
            'title'            => $eventScheduleData->title,
            'place'            => $eventScheduleData->place,
            'access_direction' => $eventScheduleData->access_direction,
            'description'      => $eventScheduleData->description,
            'cancel_time'      => $eventScheduleData->cancel_time,
            'count_booking'    => $eventScheduleData->count_booking,
            'staff_id'         => $eventScheduleData->staff_id,
            'google_map_url'   => $eventScheduleData->google_map_url
        ];

        $eventSchedules = [];
        foreach ($schedules as $schedule) {
            list($startHour, $startMin) = explode(':', $schedule['start_time']);
            list($endHour, $endMin)     = explode(':', $schedule['end_time']);

            $eventSchedules[] = [
                'id'               => $schedule['id'],
                'date'             => $schedule['date'],
                'start_time'       => $schedule['start_time'],
                'start_hour'       => $startHour,
                'start_min'        => $startMin,
                'end_time'         => $schedule['end_time'],
                'end_hour'         => $endHour,
                'end_min'          => $endMin,
                'capacity'         => $schedule['capacity'],
                'originalCapacity' => $schedule['capacity'],
                'count_booking'    => $schedule['count_booking'],
                'isEditMode'       => false,
            ];
        }

        return [
            $event,
            $eventSchedules,
            Staff::all()
        ];
    }

    /**
     * Create or update record for event table
     *
     * @param array $data
     * @return mixed
     * @throws GeneralException
     */
    public function createOrUpdateEvent($data)
    {
        DB::beginTransaction();
        try {
            $eventData = [
                'title'            => $data['title'],
                'place'            => $data['place'],
                'access_direction' => $data['access_direction'],
                'description'      => $data['description'],
                'cancel_time'      => (int)$data['cancel_time'],
                'staff_id'         => $data['staff_id'],
                'google_map_url'   => $data['google_map_url'],
            ];

            if ($data['image'] !== 'null' && !is_null($data['image'])) {
                $eventData['filename'] = $data['image'];
            }

            if (!empty($data['id'])) {
                $schedules = [];
                $eventId   = $data['id'];

                $this->eventRepo->update($eventData, $eventId);
                if (!empty($data['schedules'])) { // Since capacity is only allowed to update
                    foreach ($data['schedules'] as $schedule) {
                        if (array_key_exists('id', $schedule)) {
                            $this->scheduleRepo->update(['capacity' => $schedule['capacity']], $schedule['id']);
                            continue;
                        }

                        $schedules[] = $schedule;
                    }
                }
            } else {
                $event     = $this->eventRepo->create($eventData);
                $eventId   = $event->id;
                $schedules = $data['schedules'];
            }

            $this->createEventSchedule($schedules, $eventId);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw new GeneralException($e->getMessage());
        }
    }

    /**
     * Create an event schedule data in schedule table
     *
     * @param array $schedules
     * @param int $eventId
     */
    public function createEventSchedule($schedules, $eventId)
    {
        $scheduleData = [];
        foreach ($schedules as $schedule) {
            $startTime = $schedule['start_hour'] . ':' . $schedule['start_min'];
            $endTime = $schedule['end_hour'] . ':' . $schedule['end_min'];

            $scheduleData[] = [
                'event_id'      => $eventId,
                'date'          => $schedule['date'],
                'start_time'    => $startTime,
                'end_time'      => $endTime,
                'capacity'      => $schedule['capacity'],
                'count_booking' => (int)$schedule['count_booking'],
                'created_at'    => \Carbon\Carbon::now(),
                'updated_at'    => \Carbon\Carbon::now(),
            ];
        }

        $this->scheduleRepo->insert($scheduleData);
    }

    /**
     * Get event
     *
     * @param int $id
     * @return mixed
     */
    public function getEvent($id)
    {
        $datas                     = $this->eventRepo->findByAttributes(['id' => $id])->load('schedules')->toArray();
        $firstDate                 = $datas['schedules'][0]['date'] ?? NOW();
        $datas['mobile_date']      = dateFormat($firstDate, 'm月d日') . ' (' . weekName($firstDate) . ')';
        $datas['mobile_ymd']       = dateFormat($firstDate, 'Y年m月d日') . ' (' . weekName($firstDate) . ')';
        $details    = array_map(function ($data) use ($firstDate) {
            $data['date']          = dateFormat($firstDate, 'Y年m月d日') . ' (' . weekName($firstDate) . ')';
            $data['start_time']    = dateFormat($data['start_time'], 'H:i');
            $data['end_time']      = dateFormat($data['end_time'], 'H:i');

            return $data;
        }, $datas['schedules']);

        $datas['schedules'] = $details;

        return $datas;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getAllEventList()
    {
        return $this->eventRepo->buildQueryByAttributes([])
                               ->select(['events.id', 'events.title'])
                               ->with('schedulesWithOld:event_id,date,start_time,end_time,capacity,count_booking')
                               ->get();
    }

    /**
     * Check event
     *
     * @param int $id
     * @return mixed
     */
    public function checkEvent($id)
    {
        return $this->eventRepo->exists('id', $id);
    }

    /**
     * Return true if event has a booking for it's schedule
     *
     * @param int $eventId
     * @return bool
     */
    public function isEventHasBooking($eventId)
    {
        return $this->scheduleRepo->buildQueryByAttributes(['event_id' => $eventId])
                                     ->whereRaw('count_booking > 0')
                                     ->exists();
    }

    /**
     * Check if schedule already have booking
     *
     * @param int $scheduleId
     * @return bool
     */
    public function isScheduleHasBooking($scheduleId)
    {
        return $this->scheduleRepo->buildQueryByAttributes(['id' => $scheduleId])
                                  ->whereRaw('count_booking > 0')
                                  ->exists();
    }
}
