<?php

namespace App\Exceptions\Api;

use App\Enums\ApiCode;
use App\Http\Responses\ApiResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Throwable;

final class ModelNotFoundExceptionHandler implements ApiExceptionHandlerContract
{
  public function supports(Throwable $exception): bool
  {
    return $exception instanceof ModelNotFoundException;
  }

  public function handle(Throwable $exception): JsonResponse
  {
    return ApiResponse::failed(
      ApiCode::RESOURCE_NOT_FOUND->value,
      'The requested resource was not found.',
      null,
      '',
      404,
    );
  }
}
