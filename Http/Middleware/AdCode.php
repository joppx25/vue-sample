<?php

namespace App\Http\Middleware;

use Closure;

class AdCode
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if($request->input('adcd')){
            $adcode = $request->input('adcd');
            \Cookie::queue('adcode', $adcode, 10000);
        }
        return $next($request);
    }
}
