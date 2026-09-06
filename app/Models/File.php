<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
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
