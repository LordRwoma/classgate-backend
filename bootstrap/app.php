<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Railway (dan platform PaaS sejenis: Heroku, Render, dsb) men-terminate
        // HTTPS di edge proxy lalu meneruskan ke container via HTTP biasa.
        // Tanpa ini, Laravel mengira semua request itu HTTP, sehingga URL asset
        // dari @vite() ikut digenerate dengan skema http:// -> diblokir browser
        // sebagai "Mixed Content" saat halaman dibuka via https://.
        $middleware->trustProxies(at: '*');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })->create();
