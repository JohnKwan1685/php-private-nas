<?php

namespace App\Exceptions\Api;

use App\Enums\ApiCode;
use App\Http\Responses\ApiResponse;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

final class HttpExceptionHandler implements ApiExceptionHandlerContract
{
  public function supports(Throwable $exception): bool
  {
    return $exception instanceof HttpExceptionInterface;
  }

  public function handle(Throwable $exception): JsonResponse
  {
    /** @var HttpExceptionInterface $exception */
    $status = $exception->getStatusCode();

    return ApiResponse::failed(
      $status === 500
        ? ApiCode::INTERNAL_ERROR->value
        : ApiCode::HTTP_ERROR->value,
      $exception->getMessage(),
      null,
      '',
      $status,
    );
  }
}
