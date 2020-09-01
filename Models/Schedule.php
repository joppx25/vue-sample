<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Schedule
 * @package App\Models
 */
class Schedule extends Model
{
    /**
     * @var string $table
     */
    protected $table = 'schedules';

    /**
     * fields for schedules table
     * @var array $fillable
     */
    protected $fillable = [
        'event_id',
        'date',
        'start_time',
        'end_time',
        'capacity',
        'count_booking',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function eventInfo()
    {
        return $this->belongsTo('App\Models\Event', 'event_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function bookings()
    {
        return $this->hasMany('App\Models\EventBooking', 'schedule_id', 'id');
    }
}
