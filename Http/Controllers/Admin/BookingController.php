<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\BookingService;
use Illuminate\Http\Request;
use Excel;

class BookingController extends Controller
{
    /** @var BookingService $bookingService */
    protected $bookingService;

    /**
     * BookingController constructor.
     * @param BookingService $bookingService
     */
    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    /**
     * booking list
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view('admin.booking.index');
    }

    /**
     * booking create
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view('admin.booking.create');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function list(Request $request)
    {
        return response()->json($this->bookingService->getBookings($request->all()));
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function downloadCsv(Request $request)
    {
        return $this->bookingService->downloadCsv($request->all());
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function memoAddOrUpdate(Request $request)
    {
        if ($this->bookingService->updateBookingMemo($request->all())) {
            return response()->json(['message' => __('labels.update_memo')], 200);
        } else {
            return response()->json(__('labels.error'), 500);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateBookingStatus(Request $request)
    {
        if ($this->bookingService->updateBookingStatus($request->all())) {
            return response()->json(['message' => __('labels.update_status')], 200);
        } else {
            return response()->json(__('labels.error'), 500);
        }
    }

    /**
     * get all bookings
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function spreadSheet()
    {
        $this->bookingService->getAllBookings();

        return response()->json(['message' => 'Googleスプレッドシートに正常にインポートされました。'], 200);
    }
}
