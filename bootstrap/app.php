<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
    $middleware->append(\Illuminate\Http\Middleware\HandleCors::class);

    // Enable Sanctum stateful API middleware (session + cookies for SPA)
    $middleware->statefulApi();

    // Exclude public auth routes from CSRF validation (cross-origin SPA)
    $middleware->validateCsrfTokens(except: [
        'api/auth/login',
        'api/auth/register',
        'api/auth/forgot-password',
        'api/auth/reset-password',
    ]);

    $middleware->alias([
        // Arahkan ke file yang baru Anda buat:
        'role' => \App\Http\Middleware\RoleMiddleware::class,
    ]);
})
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
