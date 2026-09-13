<?php

namespace App\Exceptions\Api;

use App\Enums\ApiCode;
use App\Http\Responses\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Throwable;

final class ValidationExceptionHandler implements ApiExceptionHandlerContract
{
  public function supports(Throwable $exception): bool
  {
    return $exception instanceof ValidationException;
  }

  public function handle(Throwable $exception): JsonResponse
  {
    /** @var ValidationException $exception */
    return ApiResponse::failed(
      ApiCode::VALIDATION_FAILED->value,
      $exception->getMessage(),
      config('app.debug') ? $exception->errors() : null,
      '',
      422,
    );
  }
}
