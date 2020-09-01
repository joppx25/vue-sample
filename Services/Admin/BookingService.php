<?php

namespace App\Services\Admin;

use App\Repositories\Eloquent\EventBookingRepository;
use Carbon\Carbon;
use Illuminate\Pagination\Paginator;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BookingExport;
use Sheets;

class BookingService
{
    /** @var EventBookingRepository $eventBookingRepo */
    protected $eventBookingRepo;

    /** @var array $csvData */
    protected $csvData;

    /**
     * UserService constructor.
     * @param EventBookingRepository $eventBookingRepo
     */
    public function __construct(
        EventBookingRepository $eventBookingRepo
    ) {
        $this->eventBookingRepo = $eventBookingRepo;
        $this->csvData = [];
        $this->spreadSheetData = [];
    }

    /**
     * @param $request
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getBookings($request)
    {
        if (!empty($request['page'])) {
            $page = $request['page'];
            Paginator::currentPageResolver(function () use ($page) {
                return $page;
            });
        }

        $result = $this->eventBookingRepo->buildQueryByAttributes([], 'created_at', 'desc')
                                         ->join('schedules', 'event_bookings.schedule_id', '=', 'schedules.id')
                                         ->join('events', 'schedules.event_id', '=', 'events.id')
                                         ->select(['event_bookings.*', 'schedules.date', 'schedules.start_time'])
                                         ->with([
                                                    'scheduleInfo:id,event_id,date,start_time,end_time',
                                                    'scheduleInfo.eventInfo:id,title'
                                                ])
                                         ->orderBy('schedules.date', 'asc')
                                         ->orderBy('schedules.start_time', 'asc');

        if (!empty($request['eventName']) && $request['eventName'] != 'null') {
            $result->whereRaw('events.title like "%' . $request['eventName'] . '%"');
        }

        $startDate = empty($request['dataForm']) || $request['dateFrom'] == 'null' ? null : date('Y-m-d');
        if (!empty($request['dateFrom']) && $request['dateFrom'] != 'null') {
            $startDate = date('Y-m-d', strtotime($request['dateFrom']));
        }
        if (!empty($startDate)) {
            $result->whereRaw('schedules.date >= "'. $startDate .'"');
        }

        if (!empty($request['dateTo']) && $request['dateTo'] != 'null') {
            $result->whereRaw('schedules.date <= "'. date('Y-m-d', strtotime($request['dateTo'])) .'"');
        }
        
        return $result->paginate(10);
    }

    /**
     * @param $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadCsv($request)
    {
        $request['page'] = 1;
        // headers
        $this->csvData[] = [
            __('labels.status'),
            __('labels.booking_id'),
            __('labels.schedule_date'),
            __('labels.schedule_time'),
            __('labels.event_title'),
            __('labels.full_name'),
            __('labels.email'),
            __('labels.tel'),
            __('labels.booking_date'),
            __('labels.introducer'),
            __('labels.memo'),
            __('labels.adcode'),
        ];

        $result = $this->getBookings($request);
        $this->csvData($this->getBookings($request));
        if ($result->lastPage() > 1) {
            for ($i = 2; $i <= $result->lastPage(); $i++) {
                $request['page'] = $i;
                $this->csvData($this->getBookings($request));
            }
        }
        $headers = [
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Content-type'        => 'text/csv; charset=shift-jis;',
            'Content-Disposition' => 'attachment; filename=' . 'bookings-' . date('Y-m-d H:i:s') . '.csv',
            'Expires'             => '0',
            'Pragma'              => 'public'
        ];
        mb_convert_variables('SHIFT-JIS', 'UTF-8', $this->csvData);
        $data = $this->csvData;

        $callback = function () use ($data) {
            $fh = fopen('php://output', 'w');
            foreach ($data as $row) {
                fputcsv($fh, $row);
            }
            fclose($fh);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * @param $data
     * @return mixed
     */
    public function updateBookingMemo($data)
    {
        return $this->eventBookingRepo->update(['memo' => $data['memo']], $data['id']);
    }

    /*** PRIVATE FUNCTIONS ****/

    /**
     * csv data download
     *
     * @param $datas
     */
    protected function csvData($datas)
    {

        foreach ($datas as $data) {
            $this->csvData[] = [
                eventBookingStatus($data->status),
                $data->booking_id,
                $data->scheduleInfo->date,
                $data->scheduleInfo->start_time . ' - ' . $data->scheduleInfo->end_time,
                $data->scheduleInfo->eventInfo->title,
                $data->full_name,
                $data->email,
                $data->tel,
                $data->created_at->format('Y/m/d H:i:s'),
                $data->introducer ?? '',
                $data->memo ?? '',
                $data->adcode ?? '',
            ];
        }
    }


    /**
     * @param $data
     * @return mixed
     */
    public function updateBookingStatus($data)
    {
        return $this->eventBookingRepo->update(['status' => $data['status']], $data['id']);
    }

    /**
     * get all bookings and put to spread sheet
     */
    public function getAllBookings()
    {
        $request['page'] = 1;
        $this->csvData[] = [
            __('labels.status'),
            __('labels.booking_id'),
            __('labels.schedule_date'),
            __('labels.schedule_time'),
            __('labels.event_title'),
            __('labels.full_name'),
            __('labels.email'),
            __('labels.tel'),
            __('labels.booking_date'),
            __('labels.introducer'),
            __('labels.memo'),
            __('labels.adcode'),
        ];
        $result = $this->getBookings($request);
    
        $this->csvData($this->getBookings($request));
        if ($result->lastPage() > 1) {
            for ($i = 2; $i <= $result->lastPage(); $i++) {
                $request['page'] = $i;
                $this->csvData($this->getBookings($request));
            }
        }

        Sheets::spreadsheet(config('sheets.post_spreadsheet_id'))
                        ->sheet(config('sheets.post_sheet_id'))
                        ->update($this->csvData);
    }
}
