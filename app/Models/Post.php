<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class Post extends Model
{
    use HasFactory;

    public const CONTENT_TYPE_ARTICLE = 'article';

    public const CONTENT_TYPE_VIDEO = 'video';

    public const VIDEO_PROVIDER_YOUTUBE = 'youtube';

    public const VIDEO_PROVIDER_VIMEO = 'vimeo';

    protected $fillable = [
        'user_id',
        'category_id',
        'sub_category_id',
        'title',
        'slug',
        'content_type',
        'thumbnail_path',
        'video_url',
        'video_provider',
        'video_id',
        'description',
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
        'content_type' => 'string',
        'video_url' => 'string',
        'video_provider' => 'string',
        'video_id' => 'string',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function subCategory(): BelongsTo
    {
        return $this->belongsTo(SubCategory::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    protected function thumbnailUrl(): Attribute
    {
        return Attribute::get(function () {
            return $this->thumbnail_path ? Storage::url($this->thumbnail_path) : null;
        });
    }

    protected function videoEmbedUrl(): Attribute
    {
        return Attribute::get(function () {
            return static::videoEmbedUrlFor($this->video_provider, $this->video_id);
        });
    }

    protected function videoEmbedHtml(): Attribute
    {
        return Attribute::get(function () {
            return static::videoEmbedHtmlFor($this->video_provider, $this->video_id, $this->title);
        });
    }

    protected function excerpt(): Attribute
    {
        return Attribute::get(function () {
            return Str::limit(strip_tags((string) $this->description), 160);
        });
    }

    public function isVideo(): bool
    {
        return $this->content_type === self::CONTENT_TYPE_VIDEO;
    }

    public function isArticle(): bool
    {
        return $this->content_type === self::CONTENT_TYPE_ARTICLE;
    }

    public static function videoEmbedUrlFor(?string $provider, ?string $videoId): ?string
    {
        if (! $provider || ! $videoId) {
            return null;
        }

        return match ($provider) {
            self::VIDEO_PROVIDER_YOUTUBE => 'https://www.youtube.com/embed/'.$videoId,
            self::VIDEO_PROVIDER_VIMEO => 'https://player.vimeo.com/video/'.$videoId,
            default => null,
        };
    }

    public static function videoEmbedHtmlFor(?string $provider, ?string $videoId, ?string $title = null): ?HtmlString
    {
        $embedUrl = static::videoEmbedUrlFor($provider, $videoId);

        if (! $embedUrl) {
            return null;
        }

        $videoTitle = $title ?: 'Embedded video';

        $iframe = sprintf(
            '<iframe src="%s" title="%s" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen loading="lazy"></iframe>',
            $embedUrl,
            e($videoTitle)
        );

        return new HtmlString('<div class="video-embed">'.$iframe.'</div>');
    }
}
