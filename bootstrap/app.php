<?php

use App\Http\Middleware\SetLocale;
use App\Http\Middleware\CheckBannedStatus;
use App\Http\Middleware\AdminOrModeratorMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Register middleware aliases here
        $middleware->alias([
            // ... existing aliases (e.g., 'auth', 'guest', etc.)
            'admin_or_moderator' => AdminOrModeratorMiddleware::class,
            'check_banned' => CheckBannedStatus::class,
            'set_locale' => SetLocale::class,
        ]);

        $middleware->web(append: [
            SetLocale::class,
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
