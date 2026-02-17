<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Yajra\DataTables\DataTablesServiceProvider; //  Yajra DataTables ekledik
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\PermissionMiddleware;
//use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        //api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        // ===================================================================
        // >> YENİ EKLENEN KISIM BURASI <<
        // CSRF korumasından hariç tutulacak URL'leri buraya ekliyoruz.
        $middleware->validateCsrfTokens(except: [
            'envanter/kaydet',
        ]);
        // ===================================================================

        // Mevcut alias tanımlarınız aşağıda olduğu gibi kalıyor.
        $middleware->alias([
            'auth' => \App\Http\Middleware\Authenticate::class,
            'roles' => \App\Http\Middleware\Role::class,
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'pdks.permission' => \App\Http\Middleware\EnsurePdksPermission::class,
            //'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
            'auth.session' => \Illuminate\Session\Middleware\AuthenticateSession::class,
            'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
            'can' => \Illuminate\Auth\Middleware\Authorize::class,
            //'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
            'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
            //'signed' => \App\Http\Middleware\ValidateSignature::class,
            'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
            'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
            //'auth.token' => \App\Http\Middleware\VerifyApiToken::class,
        ]);
    })
    ->withProviders([ //  Buraya Yajra DataTables ekledik
        DataTablesServiceProvider::class,
    ])
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
