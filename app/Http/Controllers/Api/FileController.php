<?php

namespace App\Http\Controllers\Api;

use App\Enums\ApiCode;
use App\Http\Controllers\Controller;
use App\Http\Requests\FileUploadRequest;
use App\Http\Resources\FileResource;
use App\Http\Responses\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\File as FileModel;

class FileController extends Controller
{
  public function upload(FileUploadRequest $request): JsonResponse
  {
    $uploadedFile = $request->file('file');
    $path = $uploadedFile->store('files', 'local');

    $userId = Auth::id();
    abort_unless($userId !== null, 401);

    $file = FileModel::createFromUpload($uploadedFile, $path, $userId);

    return ApiResponse::success(
      ApiCode::FILE_UPLOADED->value,
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
      ApiCode::FILE_DELETED->value,
      [],
    );
  }
}
