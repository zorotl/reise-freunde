<?php

namespace App\Services;

use Illuminate\Support\Facades\Route;

class RouteConflictChecker
{
    public static function getConflicts(): array
    {
        $routes = collect(Route::getRoutes())->filter(fn($route) => in_array('GET', $route->methods()));
        $staticRoutes = [];
        $dynamicRoutes = [];

        foreach ($routes as $route) {
            $uri = $route->uri();
            if (str_contains($uri, '{')) {
                $dynamicRoutes[] = $uri;
            } else {
                $staticRoutes[] = $uri;
            }
        }

        $conflicts = [];

        foreach ($dynamicRoutes as $dynamic) {
            $pattern = preg_replace('/{[^}]+}/', '[^/]+', $dynamic);
            $pattern = "#^$pattern$#";
            foreach ($staticRoutes as $static) {
                if (preg_match($pattern, $static)) {
                    $conflicts[] = [
                        'static' => $static,
                        'dynamic' => $dynamic,
                    ];
                }
            }
        }

        return $conflicts;
    }
}
