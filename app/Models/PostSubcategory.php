<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PostSubcategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'post_category_id',
        'name',
        'slug',
        'description',
    ];

    protected static function booted(): void
    {
        static::creating(function (PostSubcategory $subcategory) {
            if (empty($subcategory->slug)) {
                $subcategory->slug = static::generateUniqueSlug($subcategory->name);
            }
        });

        static::updating(function (PostSubcategory $subcategory) {
            if ($subcategory->isDirty('name') && !$subcategory->isDirty('slug')) {
                $subcategory->slug = static::generateUniqueSlug($subcategory->name, $subcategory->id);
            }
        });
    }

    public static function generateUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($name);
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

    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}
