<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class VideoPlaylist extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $playlist) {
            if (blank($playlist->slug)) {
                $playlist->slug = static::generateUniqueSlug($playlist->name ?? '');
            }
        });
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class, 'video_playlist_id');
    }

    protected static function generateUniqueSlug(?string $value): string
    {
        $base = Str::slug($value ?? '');

        if ($base === '') {
            return (string) Str::uuid();
        }

        $slug = $base;
        $counter = 2;

        while (static::where('slug', $slug)->exists()) {
            $slug = $base.'-'.$counter;
            $counter++;
        }

        return $slug;
    }
}
