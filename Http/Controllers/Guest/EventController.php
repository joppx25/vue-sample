<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Services\EventService;
use Illuminate\Http\Request;

class EventController extends Controller
{
    /** @var EventService $eventService */
    protected $eventService;

    /**
     * contruct
     * EventService $eventService
     */
    public function __construct(EventService $eventService)
    {
        $this->eventService = $eventService;
    }


    /**
     * event form
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function eventForm($id)
    {
        if (!$this->eventService->checkEvent($id)) {
            return abort(404);
        }

        return view('guest.booking.event', compact('id'));
    }

    /**
     * Get all event
     *
     * @param Request $request
     * @return mixed
     */
    public function getAllEvent(Request $request)
    {
        $datas = $this->eventService->getEvent($request->id);

        return response()->json($datas);
    }
}
