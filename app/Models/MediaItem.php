<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class MediaItem extends Model
{
    use HasFactory;

    public const TYPE_IMAGE = 'image';
    public const TYPE_VIDEO = 'video';
    public const TYPE_AUDIO = 'audio';
    public const TYPE_DOCUMENT = 'document';
    public const TYPE_ARCHIVE = 'archive';
    public const TYPE_OTHER = 'file';

    protected $fillable = [
        'disk',
        'path',
        'file_name',
        'original_name',
        'mime_type',
        'type',
        'size',
        'width',
        'height',
        'alt_text',
        'caption',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function url(): string
    {
        return Storage::disk($this->disk)->url($this->path);
    }

    public function sizeForHumans(int $precision = 2): string
    {
        $bytes = (int) $this->size;
        if ($bytes <= 0) {
            return '0 B';
        }

        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $pow = min((int) floor(log($bytes, 1024)), count($units) - 1);
        $value = $bytes / (1024 ** $pow);

        return sprintf('%s %s', number_format($value, $precision), $units[$pow]);
    }
}
