<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class UserMiddleware
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
        $user = Auth::user();
        if ($user->role == 'user') {
            return $next($request);
        } else {
            if (Auth::check() && $user->role == 'admin') {
                return redirect(route('admin.home'));
            } else {
                Auth::logout();
                return redirect(route('login'))->with(['danger'=>'You are not an User!']);
            }
        }
    }
}
