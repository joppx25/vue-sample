<?php

namespace App\Http\Controllers\Admin;

use App\Models\Staff;
use Image;
use Storage;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Requests\EventScheduleRequest;
use App\Services\EventService;

class EventController extends Controller
{

    /** @var EventService $eventService */
    protected $eventService;

    /**
     * EventController constructor.
     *
     * @param EventService $eventService
     */
    public function __construct(EventService $eventService)
    {
        $this->eventService = $eventService;
    }

    /**
     * event list
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view('admin.event.index');
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function listEventCalendar()
    {
        $colors = calendarColors();

        $events = $this->eventService->getAllEventList();

        $result = [];
        foreach ($events as $event) {
            $color = array_rand($colors, 1);
            foreach ($event['schedulesWithOld'] as $schedule) {
                $result[] = [
                    'title'         => "({$schedule['count_booking']}/{$schedule['capacity']}) " . $event['title'],
                    'start'         => $schedule['date'] . ' ' . $schedule['start_time'],
                    'end'           => $schedule['date'] . ' ' . $schedule['end_time'],
                    'url'           => '/admin/events/edit/' . $event['id'],
                    'color'         => $colors[$color],
                ];
            }
        }

        return response()->json($result);
    }

    /**
     * Return a json event list data
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function list(Request $request)
    {
        return response()->json($this->eventService->getEventByPage($request->all()));
    }

    /**
     * Remove certain event
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeEvent(Request $request)
    {
        $isRemoveAllowed = $this->eventService->isEventHasBooking($request->id);
        if (!$isRemoveAllowed) {
            return response()->json($this->eventService->deleteEvent($request->id));
        }
    }

    /**
     * Remove certain schedule
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeSchedule(Request $request)
    {
        $isRemoveAllowed = $this->eventService->isScheduleHasBooking($request->id);
        if (!$isRemoveAllowed) {
            return response()->json($this->eventService->deleteSchedule($request->id));
        }
    }

    /**
     * Event form
     *
     * @param int|null $cloneEventId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function eventForm($cloneEventId = null)
    {
        $eventDetails = null;
        if (!is_null($cloneEventId)) {
            $eventDetails = $this->eventService->getEventDetails($cloneEventId);
        }

        $staffs = Staff::all();

        return view('admin.event.form', compact('eventDetails', 'staffs'));
    }

    /**
     * Save event form data
     *
     * @param EventScheduleRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\GeneralException
     */
    public function createOrUpdateEvent(EventScheduleRequest $request)
    {
        $data = array_map(function ($value) {
            return !empty($value) && $value != 'null' ? $value : null;
        }, $request->all());

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = $image->getClientOriginalName();

            if ($image->getClientOriginalExtension() === 'gif') {
                $image->storeAs(EVENT_IMAGE_PATH, $filename);
            } else {
                $img = Image::make($image->getRealPath());
                $img->stream();

                Storage::disk('local')->put(EVENT_IMAGE_PATH . $filename, $img);
            }

            $data['image'] = $filename;
        } elseif (!empty($data['clonedImage'])) {
            $data['image'] = $data['clonedImage'];
        }

        $this->eventService->createOrUpdateEvent($data);

        $message = empty($data['id'])? __('common.event_created_successfully') : __('common.event_updated_successfully');
        return response()->json([
            'message' => $message
        ]);
    }

    /**
     * Display for edit of event
     *
     * @param int $eventId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($eventId)
    {
        list($event, $schedules, $staffs) = $this->eventService->prepareEventAndSchedulesData($eventId);

        return view('admin.event.form', compact('schedules', 'event', 'staffs'));
    }
}
