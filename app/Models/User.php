<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon as Date;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property int $id
 * @property string|null $account
 * @property string $username
 * @property string $password
 * @property Date|null $created_at
 * @property Date|null $updated_at
 */
class User extends Authenticatable
{
  /** @use HasFactory<UserFactory> */
  use HasApiTokens, HasFactory, Notifiable;

  /**
   * The attributes that are mass assignable.
   *
   * @var list<string>
   */
  protected $fillable = [
    'account',
    'username',
    'password',
  ];

  /**
   * The attributes that should be hidden for serialization.
   *
   * @var list<string>
   */
  protected $hidden = [
    'password',
  ];

  /**
   * Get the attributes that should be cast.
   *
   * @return array<string, string>
   */
  protected function casts(): array
  {
    return [
      'password' => 'hashed',
    ];
  }
}
