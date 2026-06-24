<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'permission' => \App\Http\Middleware\CheckPermission::class,
            'role' => \App\Http\Middleware\CheckRole::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if ($request->expectsJson()) {
                return null;
            }

            return response()->view('errors.404', [], 404);
        });

        $exceptions->render(function (HttpException $e, Request $request) {
            if ($request->expectsJson()) {
                return null;
            }

            if ($e->getStatusCode() !== 403) {
                return null;
            }

            return response()->view('errors.403', [
                'message' => $e->getMessage() ?: null,
            ], 403);
        });

        $exceptions->render(function (AuthorizationException|AccessDeniedHttpException $e, Request $request) {
            if ($request->expectsJson()) {
                return null;
            }

            return response()->view('errors.403', [
                'message' => $e->getMessage() ?: null,
            ], 403);
        });

        $exceptions->render(function (Throwable $e, Request $request) {
            if ($request->expectsJson() || config('app.debug')) {
                return null;
            }

            if ($e instanceof NotFoundHttpException) {
                return null;
            }

            if ($e instanceof HttpException && $e->getStatusCode() !== 500) {
                return null;
            }

            return response()->view('errors.500', [], 500);
        });
    })->create();
