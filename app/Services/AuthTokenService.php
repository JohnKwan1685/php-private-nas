<?php

namespace App\Services;

use App\Models\User;
use Laravel\Sanctum\PersonalAccessToken;

final class AuthTokenService
{
  /**
   * @return array{token_type: string, access_token: string, refresh_token: string, expires_at: mixed}
   */
  public function issue(User $user): array
  {
    $accessToken = $user->createToken(
      'access-token',
      ['access'],
      now()->addHour(),
    );

    $refreshToken = $user->createToken(
      'refresh-token',
      ['refresh'],
      now()->addDays(30),
    );

    return [
      'token_type' => 'Bearer',
      'access_token' => $accessToken->plainTextToken,
      'refresh_token' => $refreshToken->plainTextToken,
      'expires_at' => $accessToken->accessToken->expires_at,
    ];
  }

  /**
   * @return array{token_type: string, access_token: string, refresh_token: string, expires_at: mixed}|null
   */
  public function refresh(string $plainTextToken): ?array
  {
    $refreshToken = PersonalAccessToken::findToken($plainTextToken);

    if ($refreshToken === null || ! $refreshToken->can('refresh')) {
      return null;
    }

    if ($refreshToken->expires_at !== null && $refreshToken->expires_at->isPast()) {
      return null;
    }

    $user = $refreshToken->tokenable;
    $refreshToken->delete();

    return $this->issue($user);
  }
}
