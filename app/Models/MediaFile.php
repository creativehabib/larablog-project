<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class MediaFile extends Model
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
        'folder_id',
        'path',
        'file_name',
        'name',
        'original_name',
        'mime_type',
        'type',
        'size',
        'width',
        'height',
        'alt_text',
        'caption',
        'meta',
        'user_id',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function folder(): BelongsTo
    {
        return $this->belongsTo(MediaFolder::class, 'folder_id');
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

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

    public function displayName(): string
    {
        return (string) ($this->name ?: $this->original_name ?: $this->file_name);
    }
}
