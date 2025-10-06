<?php

use Illuminate\Session\TokenMismatchException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Http\Request;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;


return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
            'role_or_permission' => RoleOrPermissionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        
        // Handle 419 Page Expired
        $exceptions->render(function (TokenMismatchException $e, Request $request) {
            return redirect()->back()->withInput()->with('error', 'Your session expired, please try again.');
        });

        // Handle 404 Not Found
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            // if no "back" URL exists, fallback to dashboard/home
            return redirect()->back()->with('error', 'The page you tried to access was not found.')
                   ?: redirect('/dashboard');
        });

    })->create();
