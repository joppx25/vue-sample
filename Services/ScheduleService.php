<?php

namespace App\Services;

use App\Repositories\Eloquent\ScheduleRepository;

/**
 * Class ScheduleService.
 */
class ScheduleService
{
    /** @var ScheduleRepository $scheduleRepo */
    protected $scheduleRepo;

    /**
     * ScheduleService constructor.
     * @param ScheduleRepository $scheduleRepo
     */
    public function __construct(
        ScheduleRepository $scheduleRepo
    ) {
        $this->scheduleRepo   = $scheduleRepo;
    }
}
