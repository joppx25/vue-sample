<?php

namespace App\Http\Middleware;

use Closure;

class ParseSchedules
{
    /**
     * @var array PARSE_METHODS
     */
    const PARSED_METHODS = [
        'POST', 'PUT', 'PATCH'
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (in_array($request->getMethod(), self::PARSED_METHODS)) {
            $request->merge(['schedules' => json_decode($request->schedules, true),
                'cancel_time' => (int)$request->cancel_time
            ]);
        }

        return $next($request);
    }
}
