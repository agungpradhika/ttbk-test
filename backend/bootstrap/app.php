<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

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
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if ($request->is('api/*')) {
                $previous = $e->getPrevious();
                
                // Cek jika error terjadi karena Model tidak ditemukan (404 dari binding route)
                if ($previous instanceof ModelNotFoundException) {
                    $modelName = class_basename($previous->getModel());
                    
                    $translations = [
                        'Transaction' => 'Transaksi',
                        'Category' => 'Kategori',
                        'ChartOfAccount' => 'Bagan akun (COA)',
                    ];
                    
                    $name = $translations[$modelName] ?? 'Data';
                    
                    return response()->json([
                        'message' => "{$name} tidak ditemukan.",
                    ], 404);
                }
                
                // Respon jika URL endpoint API secara umum tidak ditemukan
                return response()->json([
                    'message' => 'Halaman atau endpoint tidak ditemukan.',
                ], 404);
            }
        });
    })->create();
