<?php

use Illuminate\Support\Facades\Route;

Route::prefix('2026-09')
  ->name('api.2026-09.')
  ->group(function () {
    require __DIR__ . '/files.php';
  });
