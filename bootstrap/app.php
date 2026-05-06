<?php

use App\Exceptions\ComplianceBlockException;
use App\Exceptions\WorkerUnavailableException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
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
        $middleware->alias([
            'role'      => \App\Http\Middleware\EnsureUserHasRole::class,
            'suspended' => \App\Http\Middleware\EnsureUserIsNotSuspended::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {

        $json = fn (string $message, int $status, array $extra = []) =>
            response()->json(array_merge(['success' => false, 'message' => $message], $extra), $status);

        $exceptions->render(function (ValidationException $e) use ($json) {
            return $json('Validation failed.', 422, ['errors' => $e->errors()]);
        });

        $exceptions->render(function (AuthenticationException $e) use ($json) {
            return $json($e->getMessage() ?: 'Unauthenticated.', 401);
        });

        $exceptions->render(function (AuthorizationException $e) use ($json) {
            return $json('Forbidden.', 403);
        });

        $exceptions->render(function (ModelNotFoundException $e) use ($json) {
            $model = class_basename($e->getModel());
            return $json("{$model} not found.", 404);
        });

        $exceptions->render(function (NotFoundHttpException $e) use ($json) {
            return $json('Endpoint not found.', 404);
        });

        $exceptions->render(function (ComplianceBlockException $e) use ($json) {
            return $json($e->getMessage(), 422);
        });

        $exceptions->render(function (WorkerUnavailableException $e) use ($json) {
            return $json($e->getMessage(), 409);
        });

        // Fallback for unhandled exceptions — hide details in production
        $exceptions->render(function (\Throwable $e) use ($json) {
            $message = app()->isProduction() ? 'An unexpected error occurred.' : $e->getMessage();
            return $json($message, 500);
        });

    })->create();
