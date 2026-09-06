<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;

final class ApiResponse
{
  public static function success(
    string $code,
    string $message,
    mixed $data = null,
    string $nextPageCursor = '',
  ): JsonResponse {
    return self::make($code, $message, $data, $nextPageCursor);
  }

  public static function failed(
    string $code,
    string $message,
    mixed $data = null,
    string $nextPageCursor = '',
    int $status = 400,
  ): JsonResponse {
    return self::make($code, $message, $data, $nextPageCursor, $status);
  }

  private static function make(
    string $code,
    string $message,
    mixed $data,
    string $nextPageCursor,
    int $status = 200,
  ): JsonResponse {
    return response()->json([
      'code' => $code,
      'message' => $message,
      'data' => $data,
      'nextPageCursor' => $nextPageCursor,
    ], $status);
  }
}
