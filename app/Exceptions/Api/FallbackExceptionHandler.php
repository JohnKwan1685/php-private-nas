<?php

namespace App\Exceptions\Api;

use App\Enums\ApiCode;
use App\Http\Responses\ApiResponse;
use Illuminate\Http\JsonResponse;
use Throwable;

final class FallbackExceptionHandler implements ApiExceptionHandlerContract
{
  public function supports(Throwable $exception): bool
  {
    return true;
  }

  public function handle(Throwable $exception): JsonResponse
  {
    return ApiResponse::failed(
      ApiCode::INTERNAL_ERROR->value,
      $exception->getMessage(),
      null,
      '',
      500,
    );
  }
}
