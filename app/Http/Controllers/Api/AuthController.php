<?php

namespace App\Http\Controllers\Api;

use App\Enums\ApiCode;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RefreshTokenRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Responses\ApiResponse;
use App\Models\User;
use App\Services\AuthTokenService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
  public function __construct(
    private readonly AuthTokenService $tokenService,
  ) {}

  public function login(LoginRequest $request): JsonResponse
  {
    if (! Auth::attempt($request->credentials())) {
      return ApiResponse::failed(
        ApiCode::INVALID_CREDENTIALS->value,
        'The account or password is incorrect.',
        null,
        '',
        401,
      );
    }

    $user = Auth::user();

    if (! $user instanceof User) {
      return ApiResponse::failed(
        ApiCode::AUTHENTICATION_FAILED->value,
        'Unable to authenticate the user.',
        null,
        '',
        500,
      );
    }

    return ApiResponse::success(
      ApiCode::LOGIN_SUCCESS->value,
      [
        'user' => $user,
        'tokens' => $this->tokenService->issue($user),
      ],
    );
  }

  public function register(RegisterRequest $request): JsonResponse
  {
    $user = User::create($request->validated());

    return ApiResponse::success(
      ApiCode::REGISTER_SUCCESS->value,
      [
        'user' => $user,
        'tokens' => $this->tokenService->issue($user),
      ],
    );
  }

  public function refresh(RefreshTokenRequest $request): JsonResponse
  {
    $tokens = $this->tokenService->refresh(
      $request->validated('refresh_token'),
    );

    if ($tokens === null) {
      return ApiResponse::failed(
        ApiCode::INVALID_REFRESH_TOKEN->value,
        'The refresh token is invalid or expired.',
        null,
        '',
        401,
      );
    }

    return ApiResponse::success(ApiCode::TOKEN_REFRESHED->value, $tokens);
  }
}
