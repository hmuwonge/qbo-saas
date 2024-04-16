<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;

class CheckAuthToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $otherDate = Carbon::now()->subMinutes(10);
        $nowDate = Carbon::now();

        $result = $nowDate->gt(Carbon::createFromDate(auth()->user()->auth_expiry));
        if ($result) {
            return redirect()->route('dashboard.integrator');
        }

        return $next($request);
    }
}
