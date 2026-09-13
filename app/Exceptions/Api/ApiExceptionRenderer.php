<?php

namespace App\Exceptions\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

final class ApiExceptionRenderer
{
  /**
   * @param list<ApiExceptionHandlerContract> $handlers
   */
  public function __construct(
    private readonly array $handlers,
  ) {}

  public function render(Throwable $exception, Request $request): ?JsonResponse
  {
    if (! $request->is('api/*')) {
      return null;
    }

    foreach ($this->handlers as $handler) {
      if ($handler->supports($exception)) {
        return $handler->handle($exception);
      }
    }

    return null;
  }
}
