<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  /**
   * Run the migrations.
   */
  public function up(): void
  {
    if (Schema::hasTable('users')) {
      Schema::table('users', function (Blueprint $table): void {
        $table->dropUnique(['email']);
        $table->dropColumn(['email', 'email_verified_at']);
      });
    }

    Schema::dropIfExists('password_reset_tokens');
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    if (Schema::hasTable('users') && ! Schema::hasColumn('users', 'email')) {
      Schema::table('users', function (Blueprint $table): void {
        $table->string('email')->unique();
        $table->timestamp('email_verified_at')->nullable();
      });
    }

    if (! Schema::hasTable('password_reset_tokens')) {
      Schema::create('password_reset_tokens', function (Blueprint $table): void {
        $table->string('email')->primary();
        $table->string('token');
        $table->timestamp('created_at')->nullable();
      });
    }
  }
};
