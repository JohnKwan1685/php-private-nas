<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\FileResource;
use App\Http\Responses\ApiResponse;
use App\Models\File as FileModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
  public function upload(Request $request): JsonResponse
  {
    $request->validate([
      'file' => ['required', 'file'],
    ]);

    $uploadedFile = $request->file('file');
    $path = $uploadedFile->store('files', 'local');

    $file = FileModel::create([
      'name' => $uploadedFile->getClientOriginalName(),
      'path' => $path,
      'mime_type' => $uploadedFile->getMimeType(),
      'size' => $uploadedFile->getSize(),
      'user_id' => 1,
    ]);

    return ApiResponse::success(
      'FILE_UPLOADED',
      'File uploaded successfully.',
      FileResource::make($file)->resolve($request),
    );
  }

  public function download(FileModel $file)
  {
    return Storage::download($file->path, $file->name);
  }

  public function view(FileModel $file)
  {
    $disk = Storage::disk('local');

    abort_unless($disk->exists($file->path), 404);

    return response()->file(
      Storage::path($file->path),
      ['Content-Type' => $file->mime_type],
    );
  }

  public function delete(FileModel $file): JsonResponse
  {
    $disk = Storage::disk('local');

    if ($disk->exists($file->path) && !$disk->delete($file->path)) {
      abort(500, 'Unable to delete the file.');
    }

    $file->delete();

    return ApiResponse::success(
      'FILE_DELETED',
      'File deleted successfully.',
      [],
    );
  }
}

