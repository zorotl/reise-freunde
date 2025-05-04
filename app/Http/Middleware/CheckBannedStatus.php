<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;

class CheckBannedStatus
{
    public function handle(Request $request, Closure $next): Response
    {
        // Ensure user is authenticated AND has loaded their grant info
        if (Auth::check() && $user = Auth::user()->loadMissing('grant')) {
            $userGrant = $user->grant;
            $now = Carbon::now();

            $isCurrentlyBanned = $userGrant && $userGrant->is_banned &&
                ($userGrant->is_banned_until === null || $userGrant->is_banned_until->greaterThan($now));

            $isBanExpired = $userGrant && $userGrant->is_banned &&
                $userGrant->is_banned_until !== null && $userGrant->is_banned_until->lessThanOrEqualTo($now);

            // Case 1: User is actively banned
            if ($isCurrentlyBanned) {
                // Allow access ONLY to 'banned' and 'logout' routes
                if ($request->routeIs('banned') || $request->routeIs('logout')) {
                    return $next($request);
                }
                // Redirect all other requests to 'banned' page
                return redirect()->route('banned');
            }

            // Case 2: Ban has expired - unban and continue
            if ($isBanExpired) {
                $userGrant->is_banned = false;
                $userGrant->is_banned_until = null;
                $userGrant->banned_reason = null;
                $userGrant->save();
                // If they were trying to access the banned page after expiry, redirect them away now
                if ($request->routeIs('banned')) {
                    return redirect()->route('dashboard');
                }
                // Otherwise, let them proceed to their original destination
                return $next($request);
            }

            // Case 3: User is NOT banned, but trying to access the 'banned' page
            if ((!$userGrant || !$userGrant->is_banned) && $request->routeIs('banned')) {
                // Redirect away from the banned page
                return redirect()->route('dashboard');
            }
        }

        // User is not logged in, or not banned and not accessing /banned -> continue
        return $next($request);
    }
}