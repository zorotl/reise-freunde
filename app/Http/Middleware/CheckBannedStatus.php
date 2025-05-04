<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;

class CheckBannedStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->loadMissing('grant')->grant) {
            $user = Auth::user(); // Get user instance
            $userGrant = $user->grant;
            $now = Carbon::now();

            // Check if the user is banned AND the ban is currently active
            if ($userGrant->is_banned && ($userGrant->is_banned_until === null || $userGrant->is_banned_until->greaterThan($now))) {

                // Allow access to the 'banned' route itself AND the 'logout' route
                if ($request->routeIs('banned') || $request->routeIs('logout')) { // <-- Allow logout route
                    return $next($request);
                }

                // Redirect banned users away from other pages
                // Check if user is already on the banned page to prevent redirect loop during logout attempt
                if (!$request->routeIs('banned')) {
                    return redirect()->route('banned');
                }

            }

            // If the user is banned BUT the ban has expired, automatically unban them
            if ($userGrant->is_banned && $userGrant->is_banned_until !== null && $userGrant->is_banned_until->lessThanOrEqualTo($now)) {
                $userGrant->is_banned = false;
                $userGrant->is_banned_until = null;
                $userGrant->banned_reason = null;
                $userGrant->save();
                // The observer will log the ban history when is_banned becomes true.
                // No automatic logging needed here when it expires.
                return $next($request); // Proceed after unbanning
            }

            // If user was banned but is now trying to access the banned page after expiration/unban, redirect away
            if (!$userGrant->is_banned && $request->routeIs('banned')) {
                return redirect()->route('dashboard');
            }
        }

        // Continue for non-banned users or guests
        return $next($request);
    }
}