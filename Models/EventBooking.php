<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventBooking extends Model
{
    /**
     * @var string $table
     */
    protected $table = 'event_bookings';

    /** @var $fillable */
    protected $fillable = [
        'schedule_id',
        'booking_id',
        'full_name',
        'email',
        'tel',
        'introducer',
        'adcode',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function scheduleInfo()
    {
        return $this->belongsTo('App\Models\Schedule', 'schedule_id', 'id');
    }
}
