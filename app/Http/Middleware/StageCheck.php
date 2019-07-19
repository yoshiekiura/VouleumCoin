<?php

namespace App\Http\Middleware;

use Closure;

class StageCheck
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
        $current_date = date('Y-m-d H:i:s');
        if ($current_date >= active_stage()->start_date && $current_date <= active_stage()->end_date) {
            return $next($request);
        } elseif (active_stage()->start_date >= $current_date && $current_date <= active_stage()->end_date) {
            return $next($request);
        } else {
            $chk_stg = active_stage()->end_date == def_datetime('datetime_e') ? ['warning' => 'ICO token sell not Started yet.'] : ['warning' => __('messages.stage.expired')];
            return redirect(route('user.home'))->with($chk_stg);
        }
    }
}
