<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;

final class ApiResponse
{
  public static function success(
    string $code,
    mixed $data = null,
    ?string $message = null,
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
    ?string $message,
    mixed $data,
    string $nextPageCursor,
    int $status = 200,
  ): JsonResponse {
    $response = [
      'code' => $code,
      'data' => $data,
      'message' => null,
      'nextPageCursor' => $nextPageCursor,
    ];

    if ($message !== null && config('app.debug')) {
      $response['message'] = $message;
    }

    return response()->json($response, $status);
  }
}
