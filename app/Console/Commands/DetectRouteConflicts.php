<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;

class DetectRouteConflicts extends Command
{
    protected $signature = 'routes:check-conflicts';
    protected $description = 'Detect potentially conflicting Laravel routes (e.g., static vs dynamic).';

    public function handle()
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
                        'conflict_with' => $dynamic,
                    ];
                }
            }
        }

        if (empty($conflicts)) {
            $this->info('✅ No route conflicts detected.');
        } else {
            $this->warn("⚠️  Potential conflicts found:\n");
            foreach ($conflicts as $c) {
                $this->line(" - Static route [{$c['static']}] may be overridden by dynamic route [{$c['conflict_with']}]");
            }
        }

        return 0;
    }
}
