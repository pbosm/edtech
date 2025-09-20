<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->group('api', [
            \App\Http\Middleware\JsonResponse::class,
        ]);
    })
    ->withExceptions(function (Illuminate\Foundation\Configuration\Exceptions $exceptions) {
        $exceptions->render(function (ValidationException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => 'Dados inválidos.',
                    'errors'  => $e->errors(),
                ], 422);
            }
        });

        $exceptions->render(function (AuthenticationException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json(['message' => 'Não autenticado.'], 401);
            }
        });

        $exceptions->render(function (AuthorizationException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json(['message' => 'Esta ação não é autorizada.'], 403);
            }
        });

        $exceptions->render(function (ModelNotFoundException $e, $request) {
            if ($request->is('api/*')) {
                $model = class_basename($e->getModel() ?? 'Recurso');
                return response()->json(['message' => "{$model} não encontrado."], 404);
            }
        });

        $exceptions->render(function (NotFoundHttpException $e, $request) {
            if ($request->is('api/*')) {
                $prev = $e->getPrevious();

                if ($prev instanceof ModelNotFoundException) {
                    $model = class_basename($prev->getModel() ?? 'Recurso');
                    return response()->json(['message' => "{$model} não encontrado."], 404);
                }
                return response()->json(['message' => 'Rota não encontrada.'], 404);
            }
        });

        $exceptions->render(function (MethodNotAllowedHttpException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json(['message' => 'Método HTTP não permitido.'], 405);
            }
        });

        $exceptions->render(function (HttpException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => $e->getMessage() ?: 'Erro na requisição.',
                ], $e->getStatusCode());
            }
        });

        $exceptions->render(function (Throwable $e, $request) {
            if ($request->is('api/*')) {
                return response()->json(['message' => 'Erro interno.'], 500);
            }
        });
    })->create();
