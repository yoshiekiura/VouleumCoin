<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Schema;

class Maintenance
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
        if( ! application_installed(true)) return $next($request);
        if (file_exists(storage_path('installed')) && Schema::hasTable('settings') && get_setting('site_maintenance') == 1 && (!$request->is('/') && !$request->is('admin') && !$request->is('admin/*') && !$request->is('/log-out'))) {
            return response()->view('errors.maintenance', [], 500);
        }
        return $next($request);
    }
}
