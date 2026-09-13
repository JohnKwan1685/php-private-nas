<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon as Date;

/**
 * @property int $id
 * @property string $name
 * @property string $path
 * @property string $mime_type
 * @property int $size
 * @property Date|null $created_at
 * @property Date|null $updated_at
 * @property int $user_id
 */
class File extends Model
{
  public static function createFromUpload(
    UploadedFile $uploadedFile,
    string $path,
    int $userId,
  ): static {
    return static::create([
      'name' => $uploadedFile->getClientOriginalName(),
      'path' => $path,
      'mime_type' => $uploadedFile->getMimeType(),
      'size' => $uploadedFile->getSize(),
      'user_id' => $userId,
    ]);
  }

  protected $fillable = [
    'name',
    'path',
    'mime_type',
    'size',
    'user_id',
  ];

  protected function casts(): array
  {
    return [
      'size' => 'integer',
      'user_id' => 'integer',
    ];
  }
}
