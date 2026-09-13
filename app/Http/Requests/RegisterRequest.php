<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true;
  }

  /**
   * @return array<string, array<int, mixed>>
   */
  public function rules(): array
  {
    return [
      'account' => ['required', 'string', 'max:255', 'unique:users,account'],
      'username' => ['required', 'string', 'max:255'],
      'password' => ['required', 'confirmed', Password::defaults()],
    ];
  }
}
