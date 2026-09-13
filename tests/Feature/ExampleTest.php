<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
  public function test_the_api_returns_validation_errors_for_an_incomplete_login(): void
  {
    $response = $this->postJson('/api/2026-09/login', []);

    $response
      ->assertStatus(422)
      ->assertJsonPath('code', 'VALIDATION_FAILED');
  }
}
