<?php

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('web')
                ->prefix('api/mock')
                ->group(base_path('routes/mock.php'));
        }
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );

        $exceptions->render(function (Throwable $e, Request $request) {
            $isProduction = app()->isProduction();

            if ($e instanceof ValidationException) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => $e->errors(),
                ], 422);
            }

            if ($e instanceof NotFoundHttpException) {
                return response()->json([
                    'success' => false,
                    'message' => "Resource not found.",
                    'errors' => []
                ], 404);
            }

            $status = $e instanceof HttpExceptionInterface
                ? $e->getStatusCode()
                : 500;

            return response()->json([
                'success' => false,
                'message' => $isProduction ? 'Internal server error!' : $e->getMessage(),
                'errors' => [
                    'exception' => $isProduction ? null : class_basename($e)
                ],
            ], $status);
        });
    })->create();
