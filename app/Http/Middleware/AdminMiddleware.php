<?php
/**
 * AdminMiddleware
 *
 * Check the user is admin or not?
 *
 * @package TokenLite
 * @author Softnio
 * @version 1.0
 */
namespace App\Http\Middleware;

use Auth;
use Closure;

class AdminMiddleware
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
        if ($user->role == 'admin') {
            return $next($request);
        } else {
            if (Auth::check() && $user->role == 'user') {
                return redirect(route('user.home'));
            } else {
                Auth::logout();
                return redirect(route('login'))->with(['danger' => 'You are not an Admin!']);
            }
        }
    }
}
