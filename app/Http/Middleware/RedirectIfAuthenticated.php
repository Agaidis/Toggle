<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
<<<<<<< HEAD
use Illuminate\Http\Request;
=======
>>>>>>> 74ba0951e6b64f358c0d3b230295efb4db24237a
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  ...$guards
     * @return mixed
     */
<<<<<<< HEAD
    public function handle(Request $request, Closure $next, ...$guards)
=======
    public function handle($request, Closure $next, ...$guards)
>>>>>>> 74ba0951e6b64f358c0d3b230295efb4db24237a
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                return redirect(RouteServiceProvider::HOME);
            }
        }

        return $next($request);
    }
}
