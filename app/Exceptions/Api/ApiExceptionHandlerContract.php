<?php

namespace App\Exceptions\Api;

use Illuminate\Http\JsonResponse;
use Throwable;

interface ApiExceptionHandlerContract
{
  public function supports(Throwable $exception): bool;

  public function handle(Throwable $exception): JsonResponse;
}
