<?php

use App\Exceptions\ApiValidationException;
use App\Exceptions\EndpointNotFoundException;
use App\Exceptions\ResourceNotFoundException;
use App\Exceptions\UnauthenticatedException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectGuestsTo(fn () => throw new UnauthenticatedException);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(fn (UnauthenticatedException $e): JsonResponse => $e->render());
        $exceptions->render(fn (ResourceNotFoundException $e): JsonResponse => $e->render());
        $exceptions->render(fn (EndpointNotFoundException $e): JsonResponse => $e->render());
        $exceptions->render(fn (ApiValidationException $e): JsonResponse => $e->render());

        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                throw new UnauthenticatedException;
            }
        });

        $exceptions->render(function (ValidationException $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                throw new ApiValidationException($e->errors(), $e->getMessage());
            }
        });

        $exceptions->render(function (ModelNotFoundException $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                throw new ResourceNotFoundException('Resource not found');
            }
        });

        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                throw new EndpointNotFoundException;
            }
        });

        $exceptions->render(function (Throwable $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                $statusCode = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500;

                return response()->json([
                    'message' => $e->getMessage() ?: 'An error occurred',
                    'error' => config('app.debug') ? [
                        'exception' => $e::class,
                        'file' => $e->getFile(),
                        'line' => $e->getLine(),
                        'trace' => $e->getTrace(),
                    ] : null,
                ], $statusCode >= 100 && $statusCode < 600 ? $statusCode : 500);
            }
        });
    })->create();
