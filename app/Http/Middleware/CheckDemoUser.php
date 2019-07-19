<?php

namespace App\Http\Middleware;

use Auth;
use Closure;

class CheckDemoUser
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
        $ret['msg'] = 'info';
        $ret['message'] = __('messages.nothing');

        $user = Auth::user();
        if ($user->type == 'main') {
            return $next($request);
        } else {
            $ret['msg'] = 'warning';
            $ret['status'] = 'die';
            $ret['message'] = __('messages.demo_user');

            if ($request->ajax()) {
                return response()->json($ret);
            }
            return back()->with([$ret['msg'] => $ret['message']]);

        }
        return $next($request);

    }
}
