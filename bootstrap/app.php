<?php

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
        // $middleware->append(AdminOrModeratorMiddleware::class);
    
        // Register middleware aliases here
        $middleware->alias([
            // ... existing aliases (e.g., 'auth', 'guest', etc.)
            'admin_or_moderator' => AdminOrModeratorMiddleware::class, // <-- Add your middleware alias here
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
