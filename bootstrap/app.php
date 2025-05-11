<?php

use App\Http\Middleware\SetLocale;
use App\Http\Middleware\CheckBannedStatus;
use App\Http\Middleware\AdminOrModeratorMiddleware;
use App\Http\Middleware\EnsureUserIsApproved;
//use App\Listeners\MarkUserPending;
//use Illuminate\Auth\Events\Verified;
//use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// Correctly register listener
//Event::listen(Verified::class, MarkUserPending::class);

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
            'approved' => EnsureUserIsApproved::class,
        ]);

        $middleware->web(append: [
            SetLocale::class,
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
