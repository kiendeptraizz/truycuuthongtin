<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin.auth' => \App\Http\Middleware\AdminAuth::class,
            'prevent.caching' => \App\Http\Middleware\PreventCaching::class,
            'telegram.security' => \App\Http\Middleware\TelegramBotSecurity::class,
            'normalize.customer.name' => \App\Http\Middleware\NormalizeCustomerName::class,
        ]);

        // Exclude CSRF verification for admin login POST route and Telegram webhook
        $middleware->validateCsrfTokens(except: [
            '/admin/login',
            '/telegram/webhook',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
