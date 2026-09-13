<?php

use App\Exceptions\Api\ApiExceptionRenderer;
use App\Exceptions\Api\AuthenticationExceptionHandler;
use App\Exceptions\Api\FallbackExceptionHandler;
use App\Exceptions\Api\HttpExceptionHandler;
use App\Exceptions\Api\ModelNotFoundExceptionHandler;
use App\Exceptions\Api\ValidationExceptionHandler;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
  ->withRouting(
    web: __DIR__ . '/../routes/web.php',
    api: __DIR__ . '/../routes/api.php',
    commands: __DIR__ . '/../routes/console.php',
    health: '/up',
  )
  ->withMiddleware(function (Middleware $middleware): void {
    //
  })
  ->withExceptions(function (Exceptions $exceptions): void {
    $renderer = new ApiExceptionRenderer([
      new AuthenticationExceptionHandler(),
      new ModelNotFoundExceptionHandler(),
      new ValidationExceptionHandler(),
      new HttpExceptionHandler(),
      new FallbackExceptionHandler(),
    ]);

    $exceptions->render(
      fn(Throwable $exception, Request $request) => $renderer->render($exception, $request),
    );
  })->create();
