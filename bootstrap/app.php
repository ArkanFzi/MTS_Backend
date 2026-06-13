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

    // Exclude ALL api/* routes from CSRF cookie validation.
    // For SPA + Sanctum stateful auth, CSRF protection is handled via
    // the X-XSRF-TOKEN header (read from XSRF-TOKEN cookie by Axios),
    // NOT via the traditional Laravel CSRF cookie/form-field check.
    $middleware->validateCsrfTokens(except: [
        'api/*',
    ]);

    $middleware->alias([
        // Arahkan ke file yang baru Anda buat:
        'role' => \App\Http\Middleware\RoleMiddleware::class,
        'check.banned' => \App\Http\Middleware\CheckBanned::class,
    ]);
})
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
