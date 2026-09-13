<?php

use App\Http\Controllers\Api\FileController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function (): void {
  Route::post('/files', [FileController::class, 'upload'])
    ->name('files.upload');

  Route::get('/files/{file}/download', [FileController::class, 'download'])
    ->name('files.download');

  Route::delete('/files/{file}', [FileController::class, 'delete'])
    ->name('files.delete');

  Route::get('/files/{file}', [FileController::class, 'view'])
    ->name('files.view');
});
