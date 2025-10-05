<?php

namespace App\Livewire\Admin;

use App\Models\Category;
use App\Models\MediaItem;
use App\Models\Post;
use App\Models\SubCategory;
use App\Models\VideoPlaylist;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

class PostForm extends Component
{
    use WithFileUploads;

    public ?Post $post = null;

    public ?string $title = '';
    public ?string $slug = '';
    public string $content_type = Post::CONTENT_TYPE_ARTICLE;
    public ?string $description = '';
    public ?string $category_id = '';
    public ?string $sub_category_id = '';
    public ?string $video_url = null;
    public ?string $video_provider = null;
    public ?string $video_id = null;
    public string $video_source = Post::VIDEO_SOURCE_EMBED;
    public ?string $video_embed_code = null;
    public $video_upload;
    public ?string $existingVideoPath = null;
    public ?string $video_duration = null;
    public ?string $video_playlist_id = '';
    public ?string $video_preview_html = null;
    public bool $is_featured = false;
    public bool $allow_comments = true;
    public bool $is_indexable = true;
    public ?string $meta_title = null;
    public ?string $meta_description = null;
    public ?string $meta_keywords = null;
    public $thumbnail;
    public ?string $existingThumbnail = null;
    public ?int $thumbnail_media_id = null;
    public ?array $thumbnailSelection = null;

    #[Locked]
    public ?string $thumbnailPendingDeletion = null;

    public bool $autoGenerateSlug = true;

    protected ?string $lastSyncedDescription = null;

    public function mount(?Post $post = null): void
    {

        $this->post = $post;

        if ($post) {
            $this->title = $post->title;
            $this->slug = $post->slug;
            $this->content_type = $post->content_type ?? Post::CONTENT_TYPE_ARTICLE;
            $this->description = $post->description;
            $this->category_id = (string) $post->category_id;
            $this->sub_category_id = $post->sub_category_id ? (string) $post->sub_category_id : '';
            $this->video_url = $post->video_url;
            $this->video_provider = $post->video_provider;
            $this->video_id = $post->video_id;
            $this->video_source = $post->video_source ?: ($post->video_path ? Post::VIDEO_SOURCE_UPLOAD : Post::VIDEO_SOURCE_EMBED);
            $this->video_embed_code = $post->video_embed_code;
            $this->existingVideoPath = $post->video_path;
            $this->video_duration = $post->video_duration;
            $this->video_playlist_id = $post->video_playlist_id ? (string) $post->video_playlist_id : '';
            $this->video_preview_html = $post->video_embed_html?->toHtml();
            $this->is_featured = (bool) $post->is_featured;
            $this->allow_comments = (bool) $post->allow_comments;
            $this->is_indexable = (bool) $post->is_indexable;
            $this->meta_title = $post->meta_title;
            $this->meta_description = $post->meta_description;
            $this->meta_keywords = $post->meta_keywords;
            $this->existingThumbnail = $post->thumbnail_path;
            $this->autoGenerateSlug = false;
        }

        $this->lastSyncedDescription = $this->description;
    }

    public function updatedTitle($value): void
    {
        if ($this->autoGenerateSlug) {
            $this->slug = $this->generateUniqueSlug($value);
        }

        if ($this->content_type === Post::CONTENT_TYPE_VIDEO) {
            if ($this->video_provider && $this->video_id && $this->video_source !== Post::VIDEO_SOURCE_UPLOAD && blank($this->video_embed_code)) {
                $this->video_preview_html = Post::videoEmbedHtmlFor($this->video_provider, $this->video_id, $value)?->toHtml();
            } else {
                $this->updateVideoPreview();
            }
        }
    }

    public function updatedSlug($value): void
    {
        $this->autoGenerateSlug = false;
        $this->slug = $this->generateUniqueSlug($value ?: $this->title);
    }

    public function updatedContentType($value): void
    {
        if ($value === Post::CONTENT_TYPE_VIDEO) {
            $this->content_type = Post::CONTENT_TYPE_VIDEO;
            if (! in_array($this->video_source, [Post::VIDEO_SOURCE_EMBED, Post::VIDEO_SOURCE_UPLOAD], true)) {
                $this->video_source = Post::VIDEO_SOURCE_EMBED;
            }

            $this->updateVideoPreview();

            return;
        }

        $this->content_type = Post::CONTENT_TYPE_ARTICLE;
        $this->video_url = null;
        $this->video_provider = null;
        $this->video_id = null;
        $this->video_source = Post::VIDEO_SOURCE_EMBED;
        $this->video_embed_code = null;
        $this->video_upload = null;
        $this->existingVideoPath = null;
        $this->video_duration = null;
        $this->video_playlist_id = '';
        $this->video_preview_html = null;
        $this->resetErrorBag(['video_url', 'video_embed_code', 'video_upload']);
    }

    public function updatedVideoUrl($value): void
    {
        $this->video_url = $value;
        $this->updateVideoPreview();
    }

    public function updatedVideoSource($value): void
    {
        if (! in_array($value, [Post::VIDEO_SOURCE_EMBED, Post::VIDEO_SOURCE_UPLOAD], true)) {
            $this->video_source = Post::VIDEO_SOURCE_EMBED;
        } else {
            $this->video_source = $value;
        }

        if ($this->video_source === Post::VIDEO_SOURCE_UPLOAD) {
            $this->video_provider = null;
            $this->video_id = null;
        }

        $this->updateVideoPreview();
    }

    public function updatedVideoEmbedCode($value): void
    {
        $this->video_embed_code = $value;
        $this->updateVideoPreview();
    }

    public function updatedVideoUpload($value): void
    {
        $this->video_upload = $value;
        $this->updateVideoPreview();
    }

    public function updatedThumbnail($value): void
    {
        $this->thumbnail = $value;
        $this->thumbnail_media_id = null;
        $this->thumbnailSelection = null;

        if ($this->content_type === Post::CONTENT_TYPE_VIDEO) {
            $this->updateVideoPreview();
        }
    }

    public function removeExistingVideo(): void
    {
        if (! $this->existingVideoPath) {
            return;
        }

        Storage::disk('public')->delete($this->existingVideoPath);
        $this->existingVideoPath = null;

        if ($this->post) {
            $this->post->update(['video_path' => null]);
        }

        $this->dispatch('showToastr', type: 'success', message: 'Video file removed successfully.');

        $this->updateVideoPreview();
    }

    public function updatedCategoryId($value): void
    {
        if ($value === null || $value === '') {
            $this->sub_category_id = '';
            return;
        }

        if ($this->sub_category_id) {
            $isValid = SubCategory::where('id', $this->sub_category_id)
                ->where('category_id', $value)
                ->exists();

            if (! $isValid) {
                $this->sub_category_id = '';
            }
        }
    }

    public function removeExistingThumbnail(): void
    {
        if (! $this->existingThumbnail) {
            return;
        }

        Storage::disk('public')->delete($this->existingThumbnail);
        $this->existingThumbnail = null;
        $this->thumbnail_media_id = null;
        $this->thumbnailSelection = null;
        $this->thumbnailPendingDeletion = null;

        if ($this->post) {
            $this->post->update(['thumbnail_path' => null]);
        }

        $this->dispatch('showToastr', type: 'success', message: 'Thumbnail removed successfully.');
    }

    #[On('postThumbnailSelected')]
    public function applyThumbnailSelection($payload = []): void
    {
        if (! is_array($payload)) {
            $payload = (array) $payload;
        }

        $mediaId = (int) Arr::get($payload, 'id');

        if (! $mediaId) {
            return;
        }

        $this->setThumbnailFromLibrary($mediaId);
    }

    public function clearThumbnailSelection(): void
    {
        $this->thumbnail_media_id = null;
        $this->thumbnailSelection = null;
        $this->thumbnailPendingDeletion = null;

        $this->dispatch('showToastr', type: 'info', message: 'Thumbnail selection cleared.');
    }

    protected function setThumbnailFromLibrary(int $mediaId): void
    {
        $media = MediaItem::find($mediaId);

        if (! $media || $media->type !== MediaItem::TYPE_IMAGE) {
            $this->dispatch('showToastr', type: 'error', message: 'Please choose a valid image from the media library.');
            return;
        }

        if ($this->existingThumbnail && $this->existingThumbnail !== $media->path) {
            $this->thumbnailPendingDeletion = $this->existingThumbnail;
            $this->existingThumbnail = null;
        }

        $this->thumbnail = null;
        $this->thumbnail_media_id = $media->id;
        $this->thumbnailSelection = [
            'id' => $media->id,
            'path' => $media->path,
            'url' => $media->url(),
            'alt_text' => $media->alt_text,
            'width' => $media->width,
            'height' => $media->height,
            'original_name' => $media->original_name,
        ];
    }

    public function save(): mixed
    {

        $data = $this->validate($this->rules());

        if (blank($data['slug'])) {
            $data['slug'] = $this->generateUniqueSlug($this->title);
        }

        if ($this->content_type === Post::CONTENT_TYPE_VIDEO && ! in_array($this->video_source, [Post::VIDEO_SOURCE_EMBED, Post::VIDEO_SOURCE_UPLOAD], true)) {
            $this->video_source = Post::VIDEO_SOURCE_EMBED;
        }

        if (! $this->category_id) {
            $this->addError('category_id', 'Please select a category.');
            return null;
        }

        $data['category_id'] = (int) $this->category_id;
        $data['sub_category_id'] = $this->sub_category_id ? (int) $this->sub_category_id : null;
        $data['content_type'] = $this->content_type;

        if ($data['sub_category_id']) {
            $isValidSubCategory = SubCategory::where('id', $data['sub_category_id'])
                ->where('category_id', $data['category_id'])
                ->exists();

            if (! $isValidSubCategory) {
                $this->addError('sub_category_id', 'Selected sub category does not belong to the chosen category.');
                return null;
            }
        }

        $data['is_featured'] = $this->is_featured;
        $data['allow_comments'] = $this->allow_comments;
        $data['is_indexable'] = $this->is_indexable;
        $data['description'] = $this->description;
        $data['meta_title'] = $this->meta_title;
        $data['meta_description'] = $this->meta_description;
        $data['meta_keywords'] = $this->meta_keywords;

        if ($this->content_type === Post::CONTENT_TYPE_VIDEO) {
            $data['video_source'] = $this->video_source;
            $duration = trim((string) $this->video_duration);
            $data['video_duration'] = $duration !== '' ? $duration : null;
            $data['video_playlist_id'] = $this->video_playlist_id ? (int) $this->video_playlist_id : null;

            if ($this->video_source === Post::VIDEO_SOURCE_UPLOAD) {
                $storedPath = $this->existingVideoPath;

                if ($this->video_upload) {
                    if ($storedPath) {
                        Storage::disk('public')->delete($storedPath);
                    }

                    $storedPath = $this->video_upload->store('videos', 'public');
                    $this->video_upload = null;
                }

                if (! $storedPath) {
                    $this->addError('video_upload', 'Please upload a video file.');

                    return null;
                }

                $data['video_path'] = $storedPath;
                $data['video_embed_code'] = null;
                $data['video_url'] = null;
                $data['video_provider'] = null;
                $data['video_id'] = null;

                $this->existingVideoPath = $storedPath;
            } else {
                $embedCode = $this->sanitizeEmbedCode($this->video_embed_code);
                $videoUrl = trim((string) $this->video_url);
                $embedUrl = $embedCode ? $this->extractVideoUrlFromEmbedCode($embedCode) : null;

                $videoData = null;

                if ($videoUrl !== '') {
                    $videoData = $this->resolveVideoData($videoUrl);
                }

                if (! $videoData && $embedUrl) {
                    $videoData = $this->resolveVideoData($embedUrl);
                }

                if (! $embedCode && ! $videoData) {
                    $this->addError('video_embed_code', 'Please provide a valid embed code or video URL.');

                    return null;
                }

                $data['video_embed_code'] = $embedCode;
                $data['video_url'] = $videoUrl !== '' ? $videoUrl : null;

                if ($videoData) {
                    [$provider, $videoId] = $videoData;
                    $data['video_provider'] = $provider;
                    $data['video_id'] = $videoId;
                } else {
                    $data['video_provider'] = null;
                    $data['video_id'] = null;
                }

                $data['video_path'] = null;

                if ($this->existingVideoPath) {
                    Storage::disk('public')->delete($this->existingVideoPath);
                    $this->existingVideoPath = null;
                }
            }
        } else {
            $data['video_url'] = null;
            $data['video_provider'] = null;
            $data['video_id'] = null;
            $data['video_source'] = null;
            $data['video_embed_code'] = null;
            $data['video_path'] = null;
            $data['video_duration'] = null;
            $data['video_playlist_id'] = null;
        }

        unset($data['video_upload']);

        if ($this->thumbnail) {
            if ($this->existingThumbnail) {
                Storage::disk('public')->delete($this->existingThumbnail);
            }

            $data['thumbnail_path'] = $this->thumbnail->store('posts', 'public');
            $this->thumbnail_media_id = null;
            $this->thumbnailSelection = null;
            $this->thumbnailPendingDeletion = null;
        } elseif ($this->thumbnailSelection) {
            $data['thumbnail_path'] = Arr::get($this->thumbnailSelection, 'path');
            $this->existingThumbnail = Arr::get($this->thumbnailSelection, 'path');
            $this->thumbnailPendingDeletion = null;
        } elseif ($autoThumbnailPath = $this->maybeFetchVideoThumbnail($data)) {
            $data['thumbnail_path'] = $autoThumbnailPath;
            $this->existingThumbnail = $autoThumbnailPath;
            $this->thumbnailPendingDeletion = null;
        }

        unset($data['thumbnail'], $data['thumbnail_media_id']);

        if ($this->thumbnailPendingDeletion) {
            Storage::disk('public')->delete($this->thumbnailPendingDeletion);
            $this->thumbnailPendingDeletion = null;
        }

        if ($this->post && $this->post->exists) {
            $this->post->update($data);
            $message = 'Post updated successfully.';
        } else {
            $userId = Auth::id();

            if (! $userId) {
                abort(403);
            }

            $data['user_id'] = $userId;

            $this->post = Post::create($data);
            $message = 'Post created successfully.';
        }

        $this->dispatch('showToastr', type: 'success', message: $message);

        return redirect()->route('admin.posts.index')->with('success', $message);
    }

    protected function rules(): array
    {
        $postId = $this->post?->id;

        $videoUploadRules = ['nullable'];

        if ($this->content_type === Post::CONTENT_TYPE_VIDEO && $this->video_source === Post::VIDEO_SOURCE_UPLOAD) {
            $videoUploadRules = [
                $this->existingVideoPath ? 'nullable' : 'required',
                'file',
                'mimetypes:video/mp4,video/quicktime,video/webm',
                'max:204800',
            ];
        }

        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('posts', 'slug')->ignore($postId),
            ],
            'content_type' => ['required', Rule::in([Post::CONTENT_TYPE_ARTICLE, Post::CONTENT_TYPE_VIDEO])],
            'description' => array_filter([
                $this->content_type === Post::CONTENT_TYPE_ARTICLE ? 'required' : 'nullable',
                'string',
            ]),
            'category_id' => ['nullable', 'integer', Rule::exists('categories', 'id')],
            'sub_category_id' => ['nullable', 'integer', Rule::exists('sub_categories', 'id')],
            'video_source' => array_filter([
                $this->content_type === Post::CONTENT_TYPE_VIDEO ? 'required' : 'nullable',
                Rule::in([Post::VIDEO_SOURCE_EMBED, Post::VIDEO_SOURCE_UPLOAD]),
            ]),
            'video_embed_code' => [
                'nullable',
                'string',
                function ($attribute, $value, $fail) {
                    if ($this->content_type !== Post::CONTENT_TYPE_VIDEO || $this->video_source !== Post::VIDEO_SOURCE_EMBED) {
                        return;
                    }

                    $hasEmbed = trim((string) $value) !== '';
                    $hasUrl = trim((string) $this->video_url) !== '';

                    if (! $hasEmbed && ! $hasUrl) {
                        $fail('Please provide an embed code or video URL.');
                    }
                },
            ],
            'video_url' => array_filter([
                'nullable',
                'string',
                'max:500',
                function ($attribute, $value, $fail) {
                    if ($this->content_type !== Post::CONTENT_TYPE_VIDEO || $this->video_source !== Post::VIDEO_SOURCE_EMBED) {
                        return;
                    }

                    $normalized = trim((string) $value);

                    if ($normalized === '') {
                        return;
                    }

                    if (! filter_var($normalized, FILTER_VALIDATE_URL)) {
                        $fail('The video URL must be a valid URL.');

                        return;
                    }

                    if (! $this->resolveVideoData($normalized)) {
                        $fail('The video URL must be a valid YouTube or Vimeo link.');
                    }
                },
            ]),
            'video_upload' => $videoUploadRules,
            'video_duration' => ['nullable', 'string', 'max:50'],
            'video_playlist_id' => ['nullable', 'integer', Rule::exists('video_playlists', 'id')],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string'],
            'meta_keywords' => ['nullable', 'string'],
            'thumbnail' => ['nullable', 'image', 'max:4096'],
            'thumbnail_media_id' => ['nullable', 'integer', Rule::exists('media_items', 'id')],
        ];
    }

    protected function generateUniqueSlug(?string $value): string
    {
        $base = Str::slug($value ?? '');

        if ($base === '') {
            return '';
        }

        $slug = $base;
        $counter = 2;

        while ($this->slugExists($slug)) {
            $slug = $base.'-'.$counter;
            $counter++;
        }

        return $slug;
    }

    protected function slugExists(string $slug): bool
    {
        return Post::when($this->post, fn ($query) => $query->where('id', '!=', $this->post->id))
            ->where('slug', $slug)
            ->exists();
    }

    #[Computed]
    public function categories()
    {
        $categories = Category::query()
            ->orderBy('name')
            ->get();

        if ($this->category_id && ! $categories->contains('id', (int) $this->category_id)) {
            $selectedCategory = Category::find($this->category_id);

            if ($selectedCategory) {
                $categories->push($selectedCategory);
                $categories = $categories->sortBy('name')->values();
            }
        }

        return $categories;
    }

    #[Computed]
    public function availableSubCategories()
    {
        if (! $this->category_id) {
            return collect();
        }

        $subCategories = SubCategory::query()
            ->where('category_id', $this->category_id)
            ->orderBy('name')
            ->get();

        if ($this->sub_category_id && ! $subCategories->contains('id', (int) $this->sub_category_id)) {
            $selectedSubCategory = SubCategory::find($this->sub_category_id);

            if ($selectedSubCategory && (int) $selectedSubCategory->category_id === (int) $this->category_id) {
                $subCategories->push($selectedSubCategory);
                $subCategories = $subCategories->sortBy('name')->values();
            }
        }

        return $subCategories;
    }

    #[Computed]
    public function videoPlaylists()
    {
        $playlists = VideoPlaylist::query()
            ->orderBy('name')
            ->get();

        if ($this->video_playlist_id && ! $playlists->contains('id', (int) $this->video_playlist_id)) {
            $selectedPlaylist = VideoPlaylist::find($this->video_playlist_id);

            if ($selectedPlaylist) {
                $playlists->push($selectedPlaylist);
                $playlists = $playlists->sortBy('name')->values();
            }
        }

        return $playlists;
    }

    public function render()
    {
        if ($this->lastSyncedDescription !== $this->description) {
            $this->dispatch('syncPostEditor', $this->description ?? '');
            $this->lastSyncedDescription = $this->description;
        }

        return view('livewire.admin.post-form');
    }

    protected function updateVideoPreview(): void
    {
        $this->resetErrorBag(['video_url', 'video_embed_code', 'video_upload']);

        if ($this->content_type !== Post::CONTENT_TYPE_VIDEO) {
            $this->video_preview_html = null;

            return;
        }

        if ($this->video_source === Post::VIDEO_SOURCE_UPLOAD) {
            $sourceUrl = null;

            if ($this->video_upload) {
                try {
                    $sourceUrl = $this->video_upload->temporaryUrl();
                } catch (\Throwable) {
                    $sourceUrl = null;
                }
            } elseif ($this->existingVideoPath) {
                $sourceUrl = Storage::url($this->existingVideoPath);
            }

            if ($sourceUrl) {
                $poster = null;

                if ($this->thumbnail) {
                    try {
                        $poster = $this->thumbnail->temporaryUrl();
                    } catch (\Throwable) {
                        $poster = null;
                    }
                } elseif ($this->existingThumbnail) {
                    $poster = Storage::url($this->existingThumbnail);
                }

                $this->video_preview_html = $this->buildVideoTag($sourceUrl, $poster);
            } else {
                $this->video_preview_html = null;
            }

            $this->video_provider = null;
            $this->video_id = null;

            return;
        }

        $normalizedUrl = trim((string) $this->video_url);

        if ($normalizedUrl === '') {
            $this->video_url = null;
        }

        $embedCode = $this->sanitizeEmbedCode($this->video_embed_code);
        $videoData = null;

        if ($embedCode) {
            $this->video_preview_html = '<div class="video-embed">'.$embedCode.'</div>';
            $embedUrl = $this->extractVideoUrlFromEmbedCode($embedCode);

            if ($embedUrl) {
                $videoData = $this->resolveVideoData($embedUrl);
            }
        } else {
            $this->video_preview_html = null;

            if ($this->video_url) {
                $videoData = $this->resolveVideoData($this->video_url);

                if ($videoData) {
                    [$provider, $videoId] = $videoData;
                    $this->video_preview_html = Post::videoEmbedHtmlFor($provider, $videoId, $this->title)?->toHtml();
                }
            }
        }

        if ($videoData) {
            [$provider, $videoId] = $videoData;
            $this->video_provider = $provider;
            $this->video_id = $videoId;
        } else {
            $this->video_provider = null;
            $this->video_id = null;
        }
    }

    protected function sanitizeEmbedCode(?string $code): ?string
    {
        $normalized = trim((string) $code);

        if ($normalized === '') {
            return null;
        }

        $normalized = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $normalized) ?? '';
        $normalized = preg_replace('/on[a-z]+\s*=\s*"[^"]*"/i', '', $normalized) ?? $normalized;
        $normalized = preg_replace("/on[a-z]+\s*=\s*'[^']*'/i", '', $normalized) ?? $normalized;

        if (! Str::contains(Str::lower($normalized), '<iframe')) {
            return null;
        }

        return $normalized;
    }

    protected function extractVideoUrlFromEmbedCode(?string $code): ?string
    {
        if (! $code) {
            return null;
        }

        if (preg_match('/<iframe[^>]*src\s*=\s*["\']([^"\']+)["\']/i', $code, $matches)) {
            $url = trim((string) ($matches[1] ?? ''));

            if ($url !== '' && filter_var($url, FILTER_VALIDATE_URL)) {
                return $url;
            }
        }

        return null;
    }

    protected function buildVideoTag(string $sourceUrl, ?string $poster = null): string
    {
        $attributes = collect([
            'src' => $sourceUrl,
            'controls' => true,
            'preload' => 'metadata',
            'poster' => $poster,
            'title' => $this->title ?: 'Uploaded video',
        ])->filter();

        $attributeString = $attributes->map(function ($value, $key) {
            if (is_bool($value)) {
                return $value ? $key : null;
            }

            return sprintf('%s="%s"', $key, e($value));
        })->filter()->implode(' ');

        return '<div class="video-embed"><video '.$attributeString.'></video></div>';
    }

    protected function resolveVideoData(?string $url): ?array
    {
        $normalized = trim((string) $url);

        if ($normalized === '' || ! filter_var($normalized, FILTER_VALIDATE_URL)) {
            return null;
        }

        $parts = parse_url($normalized);

        if (! $parts || empty($parts['host'])) {
            return null;
        }

        $host = Str::lower($parts['host']);

        if (Str::startsWith($host, 'www.')) {
            $host = substr($host, 4);
        }

        $path = $parts['path'] ?? '';
        $videoId = null;
        $provider = null;

        if (Str::contains($host, 'youtu.be')) {
            $provider = Post::VIDEO_PROVIDER_YOUTUBE;
            $videoId = trim((string) $path, '/');
        } elseif (Str::contains($host, 'youtube.com')) {
            $provider = Post::VIDEO_PROVIDER_YOUTUBE;
            parse_str($parts['query'] ?? '', $queryParams);
            $videoId = $queryParams['v'] ?? null;

            if (! $videoId && ! empty($path)) {
                $segments = array_values(array_filter(explode('/', trim($path, '/'))));

                if (! empty($segments)) {
                    $first = $segments[0];

                    if (in_array($first, ['embed', 'shorts', 'v', 'watch', 'live'], true)) {
                        $videoId = $segments[1] ?? null;
                    } elseif (! in_array($first, ['channel', 'user', 'playlist', 'results', 'feed'], true)) {
                        $videoId = $first;
                    }
                }
            }
        } elseif (Str::contains($host, 'vimeo.com')) {
            $provider = Post::VIDEO_PROVIDER_VIMEO;
            $segments = array_values(array_filter(explode('/', trim((string) $path, '/'))));

            if (! empty($segments)) {
                $videoId = end($segments);
            }
        }

        if (! $provider || ! $videoId) {
            return null;
        }

        $videoId = trim((string) $videoId);

        if (! $this->isValidVideoId($provider, $videoId)) {
            return null;
        }

        return [$provider, $videoId];
    }

    protected function isValidVideoId(string $provider, string $videoId): bool
    {
        return match ($provider) {
            Post::VIDEO_PROVIDER_YOUTUBE => preg_match('/^[A-Za-z0-9_-]{6,}$/', $videoId) === 1,
            Post::VIDEO_PROVIDER_VIMEO => preg_match('/^[0-9]+$/', $videoId) === 1,
            default => false,
        };
    }

    protected function maybeFetchVideoThumbnail(array $data): ?string
    {
        if (! $this->shouldFetchVideoThumbnail($data)) {
            return null;
        }

        $provider = $data['video_provider'] ?? $this->video_provider;
        $videoId = $data['video_id'] ?? $this->video_id;

        if (! $provider || ! $videoId) {
            return null;
        }

        foreach ($this->videoThumbnailUrls($provider, $videoId) as $thumbnailUrl) {
            $storedPath = $this->downloadThumbnailFromUrl($thumbnailUrl);

            if ($storedPath) {
                return $storedPath;
            }
        }

        return null;
    }

    protected function shouldFetchVideoThumbnail(array $data): bool
    {
        if ($this->thumbnail || $this->existingThumbnail) {
            return false;
        }

        $contentType = $data['content_type'] ?? $this->content_type;

        if ($contentType !== Post::CONTENT_TYPE_VIDEO) {
            return false;
        }

        $videoSource = $data['video_source'] ?? $this->video_source;

        if ($videoSource === Post::VIDEO_SOURCE_UPLOAD) {
            return false;
        }

        $provider = $data['video_provider'] ?? $this->video_provider;
        $videoId = $data['video_id'] ?? $this->video_id;

        return filled($provider) && filled($videoId);
    }

    /**
     * @return array<int, string>
     */
    protected function videoThumbnailUrls(string $provider, string $videoId): array
    {
        return match ($provider) {
            Post::VIDEO_PROVIDER_YOUTUBE => [
                'https://img.youtube.com/vi/'.$videoId.'/maxresdefault.jpg',
                'https://img.youtube.com/vi/'.$videoId.'/sddefault.jpg',
                'https://img.youtube.com/vi/'.$videoId.'/hqdefault.jpg',
                'https://img.youtube.com/vi/'.$videoId.'/mqdefault.jpg',
            ],
            Post::VIDEO_PROVIDER_VIMEO => [
                'https://vumbnail.com/'.$videoId.'.jpg',
            ],
            default => [],
        };
    }

    protected function downloadThumbnailFromUrl(string $url): ?string
    {
        try {
            $response = Http::timeout(8)->get($url);
        } catch (\Throwable) {
            return null;
        }

        if (! $response->successful()) {
            return null;
        }

        $contents = $response->body();

        if ($contents === '') {
            return null;
        }

        $contentType = strtolower((string) ($response->header('Content-Type') ?? ''));

        if ($contentType !== '' && ! str_contains($contentType, 'image')) {
            return null;
        }

        $extension = $this->extensionFromContentType($contentType);

        if (! $extension) {
            $path = parse_url($url, PHP_URL_PATH) ?: '';
            $extension = pathinfo((string) $path, PATHINFO_EXTENSION);
        }

        $extension = $extension ? strtolower($extension) : 'jpg';

        $storagePath = 'posts/'.Str::uuid().'.'.$extension;

        Storage::disk('public')->put($storagePath, $contents);

        return $storagePath;
    }

    protected function extensionFromContentType(?string $contentType): ?string
    {
        return match ($contentType ? strtolower(trim($contentType)) : '') {
            'image/jpeg', 'image/jpg', 'image/pjpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            'image/gif' => 'gif',
            default => null,
        };
    }
}
