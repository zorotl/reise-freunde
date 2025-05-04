<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon; // Import Carbon

class CheckBannedStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated and has the 'grant' relationship loaded
        if (Auth::check() && Auth::user()->loadMissing('grant')->grant) {
            $userGrant = Auth::user()->grant;
            $now = Carbon::now();

            // Check if the user is banned AND the ban is currently active
            // (either banned indefinitely OR the ban expiration date is in the future)
            if ($userGrant->is_banned && ($userGrant->is_banned_until === null || $userGrant->is_banned_until->greaterThan($now))) {

                // Allow access to the dedicated 'banned' route itself
                if ($request->routeIs('banned')) {
                    return $next($request);
                }

                // Prevent logout if already on banned page or trying to logout
                if ($request->routeIs('logout')) {
                    // Allow logout attempts to proceed
                    return $next($request);
                }

                // Redirect banned users away from other pages
                return redirect()->route('banned');
            }

            // If the user is banned BUT the ban has expired, automatically unban them
            if ($userGrant->is_banned && $userGrant->is_banned_until !== null && $userGrant->is_banned_until->lessThanOrEqualTo($now)) {
                $userGrant->is_banned = false;
                $userGrant->is_banned_until = null;
                $userGrant->banned_reason = null; // Optionally clear the reason
                $userGrant->save();
                // Proceed with the original request after unbanning
                return $next($request);
            }

            // If user was banned but is now trying to access the banned page after expiration/unban, redirect away
            if (!$userGrant->is_banned && $request->routeIs('banned')) {
                return redirect()->route('dashboard'); // Or wherever non-banned users should go
            }
        }

        // If user is not logged in, or not banned, or ban expired, continue
        return $next($request);
    }
}