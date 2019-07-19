<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class SuperAdmin
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
        return $next($request);

        /*$user = Auth::user();
        if($user->role == 'admin'){
            $ret['msg'] = 'info';
            $ret['message'] = __('messages.something_wrong');
        }else {
            return $next($request);
        }
        if ($request->ajax()) {
            return response()->json($ret);
        }
        return back()->with([$ret['msg'] => $ret['message']]);*/
    }
}
