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
    if (! Schema::hasTable('users')) {
      return;
    }

    if (Schema::hasColumn('users', 'name') && ! Schema::hasColumn('users', 'username')) {
      Schema::table('users', function (Blueprint $table): void {
        $table->renameColumn('name', 'username');
      });
    }

    if (! Schema::hasColumn('users', 'account')) {
      Schema::table('users', function (Blueprint $table): void {
        $table->string('account')->nullable()->unique();
      });
    }
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    if (! Schema::hasTable('users')) {
      return;
    }

    if (Schema::hasColumn('users', 'account')) {
      Schema::table('users', function (Blueprint $table): void {
        $table->dropUnique(['account']);
        $table->dropColumn('account');
      });
    }

    if (Schema::hasColumn('users', 'username') && ! Schema::hasColumn('users', 'name')) {
      Schema::table('users', function (Blueprint $table): void {
        $table->renameColumn('username', 'name');
      });
    }
  }
};
