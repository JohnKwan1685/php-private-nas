<?php

namespace App\Exceptions\Api;

use App\Enums\ApiCode;
use App\Http\Responses\ApiResponse;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Throwable;

final class AuthenticationExceptionHandler implements ApiExceptionHandlerContract
{
  public function supports(Throwable $exception): bool
  {
    return $exception instanceof AuthenticationException;
  }

  public function handle(Throwable $exception): JsonResponse
  {
    return ApiResponse::failed(
      ApiCode::AUTHENTICATION_REQUIRED->value,
      'Authentication is required.',
      null,
      '',
      401,
    );
  }
}
