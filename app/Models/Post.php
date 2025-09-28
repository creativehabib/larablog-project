<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'post_category_id',
        'post_subcategory_id',
        'title',
        'slug',
        'excerpt',
        'body',
        'thumbnail_path',
        'is_featured',
        'allow_comments',
        'is_indexable',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'allow_comments' => 'boolean',
        'is_indexable' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (Post $post) {
            if (empty($post->slug)) {
                $post->slug = static::generateUniqueSlug($post->title);
            }
        });

        static::updating(function (Post $post) {
            if ($post->isDirty('title') && !$post->isDirty('slug')) {
                $post->slug = static::generateUniqueSlug($post->title, $post->id);
            }
        });
    }

    public static function generateUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($title);
        $slug = $baseSlug;
        $counter = 2;

        while (static::query()
            ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
            ->where('slug', $slug)
            ->exists()) {
            $slug = $baseSlug.'-'.$counter++;
        }

        return $slug;
    }

    public function category()
    {
        return $this->belongsTo(PostCategory::class, 'post_category_id');
    }

    public function subcategory()
    {
        return $this->belongsTo(PostSubcategory::class, 'post_subcategory_id');
    }
}
