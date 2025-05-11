<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Handle AuthenticationException for API → always JSON 401
        $exceptions->renderable(function (AuthenticationException $e, $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'result'  => false,
                    'errNum'  => 401,
                    'message' => 'Unauthenticated.',
                    'data'    => (object) [],
                ], 401);
            }
        });

        // Handle AuthorizationException for API → always JSON 403
        $exceptions->renderable(function (AuthorizationException $e, $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'result'  => false,
                    'errNum'  => 403,
                    'message' => $e->getMessage() ?: 'This action is unauthorized.',
                    'data'    => (object) [],
                ], 403);
            }
        });
    })
    ->create();
