<?php

use App\Helpers\ResponseUtil;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpKernel\Exception\AuthenticationException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {

        // Handle general server errors (500) for API (only not on debug mode)
        if (! (config('app.debug'))) {
            $exceptions->renderable(function (Throwable $e, $request) {
                if ($request->expectsJson() || $request->is('api/*')) {
                    return response()->json(ResponseUtil::makeError(__('messages.exception.server_error')), 500);
                }
            });
        }

        // Handle authentication exceptions for API
        $exceptions->renderable(function (AuthenticationException $e, $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(ResponseUtil::makeError(__('messages.auth.unauthenticated')), 401);
            }
        });

        // Handle 404 Not Found exceptions for API
        $exceptions->renderable(function (NotFoundHttpException $e, $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(ResponseUtil::makeError(__('messages.exception.not_found')), 404);
            }
        });

        // Handle 405 Method Not Allowed exceptions for API
        $exceptions->renderable(function (MethodNotAllowedHttpException $e, $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(ResponseUtil::makeError(__('messages.exception.method_not_allowed')), 405);
            }
        });
    })->create();
