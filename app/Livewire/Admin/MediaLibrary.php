<?php

namespace App\Livewire\Admin;

use App\Models\MediaItem;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class MediaLibrary extends Component
{
    use WithFileUploads;
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public string $viewMode = 'grid';
    public string $search = '';
    public string $typeFilter = 'all';
    public string $sortDirection = 'desc';
    public int $perPage = 20;

    /**
     * @var array<int, \Livewire\TemporaryUploadedFile>
     */
    public array $uploads = [];

    public ?int $editingId = null;
    public string $editingAltText = '';
    public string $editingCaption = '';
    public ?int $resizeWidth = null;
    public ?int $resizeHeight = null;

    protected $queryString = [
        'viewMode' => ['except' => 'grid'],
        'typeFilter' => ['except' => 'all'],
        'search' => ['except' => ''],
    ];

    protected $rules = [
        'uploads.*' => 'file|max:15360', // 15 MB
    ];

    protected $messages = [
        'uploads.*.max' => 'Each file must be 15MB or less.',
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->search = trim($this->search);
    }

    public function updatingTypeFilter(): void
    {
        $this->resetPage();
    }

    public function updatingSortDirection(): void
    {
        $this->resetPage();
    }

    public function updatedViewMode($value): void
    {
        if (! in_array($value, ['grid', 'list'], true)) {
            $this->viewMode = 'grid';
        }
    }

    public function updatedUploads(): void
    {
        $this->handleUploads();
    }

    public function setViewMode(string $mode): void
    {
        $this->viewMode = in_array($mode, ['grid', 'list'], true) ? $mode : 'grid';
    }

    public function setSortDirection(string $direction): void
    {
        $this->sortDirection = $direction === 'asc' ? 'asc' : 'desc';
    }

    public function setTypeFilter(string $filter): void
    {
        $allowed = [
            'all',
            MediaItem::TYPE_IMAGE,
            MediaItem::TYPE_VIDEO,
            MediaItem::TYPE_AUDIO,
            MediaItem::TYPE_DOCUMENT,
            MediaItem::TYPE_ARCHIVE,
            MediaItem::TYPE_OTHER,
        ];

        $this->typeFilter = in_array($filter, $allowed, true) ? $filter : 'all';
    }

    public function startEditing(int $mediaId): void
    {
        if (Auth::user()->cannot('media.edit')) {
            $this->dispatch('showToastr', type: 'error', message: 'You do not have permission to edit media.');
            return;
        }

        $media = MediaItem::findOrFail($mediaId);

        $this->editingId = $media->id;
        $this->editingAltText = (string) ($media->alt_text ?? '');
        $this->editingCaption = (string) ($media->caption ?? '');
        $this->resizeWidth = $media->width;
        $this->resizeHeight = $media->height;

        $this->dispatch(
            'openMediaEditor',
            id: $media->id,
            url: $this->resolveUrl($media),
            isImage: $media->type === MediaItem::TYPE_IMAGE,
            width: $media->width,
            height: $media->height,
            altText: $this->editingAltText,
            caption: $this->editingCaption,
            mimeType: $media->mime_type,
        );
    }

    public function deleteMedia(int $mediaId): void
    {
        if (Auth::user()->cannot('media.delete')) {
            $this->dispatch('showToastr', type: 'error', message: 'You do not have permission to delete media.');
            return;
        }

        $media = MediaItem::findOrFail($mediaId);

        $disk = Storage::disk($media->disk);
        if ($disk->exists($media->path)) {
            $disk->delete($media->path);
        }

        $media->delete();
        $this->dispatch('showToastr', type: 'success', message: 'Media item removed successfully.');
        $this->resetPage();
    }

    #[On('mediaEditorCancel')]
    public function cancelEditing(): void
    {
        $this->resetEditing();
    }

    #[On('mediaEditorSave')]
    public function saveMediaEditor($payload = []): void
    {
        if (! is_array($payload)) {
            $payload = (array) $payload;
        }

        $mediaId = (int) Arr::get($payload, 'id');
        if (! $mediaId) {
            return;
        }

        if (Auth::user()->cannot('media.edit')) {
            $this->dispatch('showToastr', type: 'error', message: 'You do not have permission to edit media.');
            return;
        }

        $media = MediaItem::findOrFail($mediaId);
        $altText = trim((string) Arr::get($payload, 'altText', ''));
        $caption = trim((string) Arr::get($payload, 'caption', ''));

        $media->update([
            'alt_text' => $altText !== '' ? $altText : null,
            'caption' => $caption !== '' ? $caption : null,
        ]);

        if ($media->type === MediaItem::TYPE_IMAGE) {
            $cropData = Arr::get($payload, 'crop');
            $resizeData = Arr::get($payload, 'resize');
            $this->manipulateImage($media, $cropData, $resizeData);
        }

        $this->dispatch('showToastr', type: 'success', message: 'Media details updated successfully.');
        $this->dispatch('mediaEditorClosed');
        $this->resetEditing();
    }

    public function render()
    {
        $mediaItems = MediaItem::query()
            ->when($this->typeFilter !== 'all', function ($query) {
                $query->where('type', $this->typeFilter);
            })
            ->when($this->search !== '', function ($query) {
                $searchTerm = '%'.$this->search.'%';
                $query->where(function ($nested) use ($searchTerm) {
                    $nested->where('original_name', 'like', $searchTerm)
                        ->orWhere('file_name', 'like', $searchTerm)
                        ->orWhere('alt_text', 'like', $searchTerm)
                        ->orWhere('caption', 'like', $searchTerm);
                });
            })
            ->orderBy('created_at', $this->sortDirection === 'asc' ? 'asc' : 'desc')
            ->paginate($this->perPage);

        return view('livewire.admin.media-library', [
            'mediaItems' => $mediaItems,
        ]);
    }

    #[Computed]
    public function libraryStats(): array
    {
        $totals = MediaItem::selectRaw('type, COUNT(*) as aggregate')
            ->groupBy('type')
            ->pluck('aggregate', 'type')
            ->all();

        $totalCount = array_sum($totals);

        return [
            'total' => $totalCount,
            'images' => $totals[MediaItem::TYPE_IMAGE] ?? 0,
            'videos' => $totals[MediaItem::TYPE_VIDEO] ?? 0,
            'documents' => $totals[MediaItem::TYPE_DOCUMENT] ?? 0,
            'audio' => $totals[MediaItem::TYPE_AUDIO] ?? 0,
            'archives' => $totals[MediaItem::TYPE_ARCHIVE] ?? 0,
            'other' => $totals[MediaItem::TYPE_OTHER] ?? 0,
        ];
    }

    protected function handleUploads(): void
    {
        if (empty($this->uploads)) {
            return;
        }

        if (Auth::user()->cannot('media.create')) {
            $this->dispatch('showToastr', type: 'error', message: 'You do not have permission to upload media.');
            $this->uploads = [];
            return;
        }

        $this->validate();

        foreach ($this->uploads as $file) {
            $this->storeUploadedFile($file);
        }

        $this->dispatch('showToastr', type: 'success', message: 'Media uploaded successfully.');
        $this->uploads = [];
        $this->resetPage();
    }

    protected function storeUploadedFile(UploadedFile $file): void
    {
        $disk = 'public';
        $originalName = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $fileName = Str::uuid()->toString().($extension ? '.'.$extension : '');
        $path = $file->storeAs('media-library', $fileName, $disk);

        $mimeType = $file->getClientMimeType();
        $size = $file->getSize() ?: 0;
        $type = $this->determineType($mimeType, $extension);

        $width = null;
        $height = null;
        if ($type === MediaItem::TYPE_IMAGE) {
            $dimensions = @getimagesize($file->getRealPath());
            if (is_array($dimensions)) {
                $width = $dimensions[0] ?? null;
                $height = $dimensions[1] ?? null;
            }
        }

        MediaItem::create([
            'disk' => $disk,
            'path' => $path,
            'file_name' => $fileName,
            'original_name' => $originalName,
            'mime_type' => $mimeType,
            'type' => $type,
            'size' => $size,
            'width' => $width,
            'height' => $height,
        ]);
    }

    protected function determineType(?string $mime, ?string $extension = null): string
    {
        $type = $this->determineTypeFromMime($mime);

        if ($type !== MediaItem::TYPE_OTHER) {
            return $type;
        }

        if ($extension) {
            $extension = strtolower($extension);
            $typeFromExtension = $this->determineTypeFromExtension($extension);

            if ($typeFromExtension !== MediaItem::TYPE_OTHER) {
                return $typeFromExtension;
            }
        }

        return MediaItem::TYPE_OTHER;
    }

    protected function determineTypeFromMime(?string $mime): string
    {
        if (! $mime) {
            return MediaItem::TYPE_OTHER;
        }

        if (str_starts_with($mime, 'image/')) {
            return MediaItem::TYPE_IMAGE;
        }

        if (str_starts_with($mime, 'video/')) {
            return MediaItem::TYPE_VIDEO;
        }

        if (str_starts_with($mime, 'audio/')) {
            return MediaItem::TYPE_AUDIO;
        }

        if (Str::contains($mime, ['pdf', 'msword', 'spreadsheet', 'presentation']) ||
            str_contains($mime, 'text/')) {
            return MediaItem::TYPE_DOCUMENT;
        }

        if (Str::contains($mime, ['zip', 'rar', 'tar', 'gzip'])) {
            return MediaItem::TYPE_ARCHIVE;
        }

        return MediaItem::TYPE_OTHER;
    }

    protected function determineTypeFromExtension(string $extension): string
    {
        if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg', 'heic', 'heif'], true)) {
            return MediaItem::TYPE_IMAGE;
        }

        if (in_array($extension, ['mp4', 'mov', 'avi', 'mkv', 'webm', 'wmv', 'flv'], true)) {
            return MediaItem::TYPE_VIDEO;
        }

        if (in_array($extension, ['mp3', 'wav', 'ogg', 'm4a', 'flac', 'aac'], true)) {
            return MediaItem::TYPE_AUDIO;
        }

        if (in_array($extension, ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'rtf', 'csv'], true)) {
            return MediaItem::TYPE_DOCUMENT;
        }

        if (in_array($extension, ['zip', 'rar', 'tar', 'gz', 'gzip', '7z'], true)) {
            return MediaItem::TYPE_ARCHIVE;
        }

        return MediaItem::TYPE_OTHER;
    }

    protected function manipulateImage(MediaItem $media, $cropData, $resizeData): void
    {
        $disk = Storage::disk($media->disk);
        if (! $disk->exists($media->path)) {
            return;
        }

        $absolutePath = $disk->path($media->path);
        $imageResource = $this->createImageResource($absolutePath, $media->mime_type);

        if (! $imageResource) {
            return;
        }

        if (is_array($cropData)) {
            $cropArray = [
                'x' => max(0, (int) round(Arr::get($cropData, 'x', 0))),
                'y' => max(0, (int) round(Arr::get($cropData, 'y', 0))),
                'width' => max(1, (int) round(Arr::get($cropData, 'width', imagesx($imageResource)))),
                'height' => max(1, (int) round(Arr::get($cropData, 'height', imagesy($imageResource)))),
            ];

            $cropped = imagecrop($imageResource, $cropArray);
            if ($cropped !== false) {
                imagedestroy($imageResource);
                $imageResource = $cropped;
            }
        }

        if (is_array($resizeData)) {
            $targetWidth = (int) Arr::get($resizeData, 'width', 0);
            $targetHeight = (int) Arr::get($resizeData, 'height', 0);

            if ($targetWidth > 0 && $targetHeight > 0) {
                $resized = imagecreatetruecolor($targetWidth, $targetHeight);

                if (in_array($media->mime_type, ['image/png', 'image/webp'], true)) {
                    imagealphablending($resized, false);
                    imagesavealpha($resized, true);
                }

                imagecopyresampled(
                    $resized,
                    $imageResource,
                    0,
                    0,
                    0,
                    0,
                    $targetWidth,
                    $targetHeight,
                    imagesx($imageResource),
                    imagesy($imageResource)
                );

                imagedestroy($imageResource);
                $imageResource = $resized;
            }
        }

        $this->writeImageResource($imageResource, $absolutePath, $media->mime_type);
        imagedestroy($imageResource);

        clearstatcache(true, $absolutePath);
        $dimensions = @getimagesize($absolutePath);

        $media->update([
            'width' => $dimensions[0] ?? null,
            'height' => $dimensions[1] ?? null,
            'size' => $disk->size($media->path),
        ]);
    }

    protected function createImageResource(string $path, ?string $mime)
    {
        return match ($mime) {
            'image/jpeg', 'image/jpg' => @imagecreatefromjpeg($path),
            'image/png' => @imagecreatefrompng($path),
            'image/gif' => @imagecreatefromgif($path),
            'image/webp' => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($path) : null,
            default => @imagecreatefromstring((string) @file_get_contents($path)),
        };
    }

    protected function writeImageResource($image, string $path, ?string $mime): void
    {
        switch ($mime) {
            case 'image/png':
                imagepng($image, $path);
                break;
            case 'image/gif':
                imagegif($image, $path);
                break;
            case 'image/webp':
                if (function_exists('imagewebp')) {
                    imagewebp($image, $path, 90);
                    break;
                }
                // fallthrough
            case 'image/jpeg':
            case 'image/jpg':
            default:
                imagejpeg($image, $path, 90);
                break;
        }
    }

    protected function resetEditing(): void
    {
        $this->editingId = null;
        $this->editingAltText = '';
        $this->editingCaption = '';
        $this->resizeWidth = null;
        $this->resizeHeight = null;
    }

    protected function resolveUrl(MediaItem $media): string
    {
        return $media->url();
    }
}
