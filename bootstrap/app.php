<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\LocaleMiddleware;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;
use Illuminate\Session\Middleware\StartSession;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->statefulApi(); // necessário para cookies funcionarem no SPA
        $middleware->appendToGroup('web', StartSession::class);
        $middleware->api(prepend: [
            EnsureFrontendRequestsAreStateful::class, // injeta o middleware do Sanctum
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
