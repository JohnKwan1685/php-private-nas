<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true;
  }

  /**
   * @return array<string, array<int, string>>
   */
  public function rules(): array
  {
    return [
      'account' => ['required', 'string'],
      'password' => ['required', 'string'],
    ];
  }

  /**
   * @return array{account: string, password: string}
   */
  public function credentials(): array
  {
    return [
      'account' => $this->validated('account'),
      'password' => $this->validated('password'),
    ];
  }
}
