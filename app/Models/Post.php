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

    public const VIDEO_SOURCE_EMBED = 'embed';

    public const VIDEO_SOURCE_UPLOAD = 'upload';

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
        'video_source',
        'video_embed_code',
        'video_path',
        'video_duration',
        'video_playlist_id',
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
        'video_source' => 'string',
        'video_embed_code' => 'string',
        'video_path' => 'string',
        'video_duration' => 'string',
        'video_playlist_id' => 'integer',
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

    public function playlist(): BelongsTo
    {
        return $this->belongsTo(VideoPlaylist::class, 'video_playlist_id');
    }

    protected function thumbnailUrl(): Attribute
    {
        return Attribute::get(function () {
            if ($this->thumbnail_path) {
                return Storage::url($this->thumbnail_path);
            }

            if ($this->isVideo()) {
                return $this->videoThumbnailUrl();
            }

            return null;
        });
    }

    protected function videoEmbedUrl(): Attribute
    {
        return Attribute::get(function () {
            if ($this->video_source === self::VIDEO_SOURCE_UPLOAD && $this->video_path) {
                return Storage::url($this->video_path);
            }

            if ($this->video_source === self::VIDEO_SOURCE_EMBED || (! $this->video_source && $this->video_embed_code)) {
                $embedUrl = $this->extractUrlFromEmbedCode();

                if ($embedUrl) {
                    return $embedUrl;
                }
            }

            return static::videoEmbedUrlFor($this->video_provider, $this->video_id);
        });
    }

    protected function videoEmbedHtml(): Attribute
    {
        return Attribute::get(function () {
            if ($this->content_type !== self::CONTENT_TYPE_VIDEO) {
                return null;
            }

            if ($this->video_source === self::VIDEO_SOURCE_UPLOAD && $this->video_path) {
                $poster = $this->thumbnail_path ? Storage::url($this->thumbnail_path) : null;
                $videoUrl = Storage::url($this->video_path);

                $attributes = [
                    'src' => $videoUrl,
                    'controls' => true,
                    'preload' => 'metadata',
                ];

                if ($poster) {
                    $attributes['poster'] = $poster;
                }

                $attributeString = collect($attributes)
                    ->map(function ($value, $key) {
                        if (is_bool($value)) {
                            return $value ? $key : null;
                        }

                        return sprintf('%s="%s"', $key, e($value));
                    })
                    ->filter()
                    ->implode(' ');

                $title = e($this->title ?? 'Embedded video');

                return new HtmlString('<div class="video-embed"><video '.$attributeString.' title="'.$title.'"></video></div>');
            }

            if ($this->video_source === self::VIDEO_SOURCE_EMBED || (! $this->video_source && $this->video_embed_code)) {
                $code = $this->sanitizedEmbedCode();

                if ($code) {
                    return new HtmlString('<div class="video-embed">'.$code.'</div>');
                }
            }

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

    public function videoThumbnailUrl(): ?string
    {
        if (! $this->isVideo()) {
            return null;
        }

        if ($this->video_source === self::VIDEO_SOURCE_UPLOAD) {
            return $this->thumbnail_path ? Storage::url($this->thumbnail_path) : null;
        }

        if (! $this->video_provider || ! $this->video_id) {
            return null;
        }

        return match ($this->video_provider) {
            self::VIDEO_PROVIDER_YOUTUBE => 'https://img.youtube.com/vi/'.$this->video_id.'/hqdefault.jpg',
            self::VIDEO_PROVIDER_VIMEO => 'https://vumbnail.com/'.$this->video_id.'.jpg',
            default => null,
        };
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

    protected function sanitizedEmbedCode(): ?string
    {
        if (! $this->video_embed_code) {
            return null;
        }

        $code = trim((string) $this->video_embed_code);

        if ($code === '') {
            return null;
        }

        $code = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $code) ?? '';
        $code = preg_replace('/on[a-z]+\s*=\s*"[^"]*"/i', '', $code) ?? $code;
        $code = preg_replace("/on[a-z]+\s*=\s*'[^']*'/i", '', $code) ?? $code;

        $trimmed = trim($code);

        return $trimmed !== '' ? $trimmed : null;
    }

    protected function extractUrlFromEmbedCode(): ?string
    {
        $code = $this->sanitizedEmbedCode();

        if (! $code) {
            return null;
        }

        if (preg_match('/<iframe[^>]*src\s*=\s*["\']([^"\']+)["\']/i', $code, $matches)) {
            $url = trim((string) ($matches[1] ?? ''));

            return $url !== '' ? $url : null;
        }

        return null;
    }
}
