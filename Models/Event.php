<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

/**
 * Class Event
 * @package App\Models
 */
class Event extends Model
{
    /**
     * fields for events table
     * @var array
     */
    protected $fillable = [
        'title',
        'description',
        'place',
        'access_direction',
        'cancel_time',
        'filename',
        'staff_id',
        'google_map_url',
    ];

    /**
     * HasMany Relationship
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function schedules()
    {
        return $this->hasMany('App\Models\Schedule', 'event_id', 'id')
                    ->where(DB::raw("CONCAT(`date`, ' ', `end_time`)"), '>=', date('Y-m-d H:i:00'))
                    ->orderBy('date', 'asc')
                    ->orderBy('start_time', 'asc');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function schedulesWithOld()
    {
        return $this->hasMany('App\Models\Schedule', 'event_id', 'id')
                    ->orderBy('date', 'asc')
                    ->orderBy('start_time', 'asc');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function staffInfo()
    {
        return $this->hasOne('App\Models\Staff', 'id', 'staff_id');
    }
}
