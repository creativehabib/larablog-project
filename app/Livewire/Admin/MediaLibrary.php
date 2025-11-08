<?php

namespace App\Livewire\Admin;

use App\Models\MediaFile;
use App\Models\MediaFolder;
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
    public int $currentFolderId = 0;
    public int $parentFolderId = 0;
    public string $newFolderName = '';

    public bool $selectMode = false;
    public ?int $selectedMediaId = null;
    public array $selectedMediaDetails = [];
    public string $selectEvent = 'image-selected';

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
        'currentFolderId' => ['except' => 0, 'as' => 'folder'],
    ];

    protected $rules = [
        'uploads.*' => 'file|max:15360', // 15 MB
    ];

    protected $messages = [
        'uploads.*.max' => 'Each file must be 15MB or less.',
    ];

    public function mount(bool $selectMode = false, string $selectEvent = 'image-selected'): void
    {
        $this->selectMode = $selectMode;
        $this->selectEvent = $selectEvent !== '' ? $selectEvent : 'image-selected';
        $this->sanitizeCurrentFolder();
    }

    public function updatedCurrentFolderId(): void
    {
        $this->sanitizeCurrentFolder();
        if ($this->selectMode) {
            $this->clearSelection();
        }
    }

    protected function sanitizeCurrentFolder(): void
    {
        if ($this->currentFolderId <= 0) {
            $this->currentFolderId = 0;
            $this->parentFolderId = 0;
            return;
        }

        $folder = MediaFolder::find($this->currentFolderId);
        if (! $folder) {
            $this->currentFolderId = 0;
            $this->parentFolderId = 0;
            return;
        }

        $this->parentFolderId = (int) ($folder->parent_id ?? 0);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
        if ($this->selectMode) {
            $this->clearSelection();
        }
    }

    public function updatedSearch(): void
    {
        $this->search = trim($this->search);
    }

    public function updatingTypeFilter(): void
    {
        $this->resetPage();
        if ($this->selectMode) {
            $this->clearSelection();
        }
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
            MediaFile::TYPE_IMAGE,
            MediaFile::TYPE_VIDEO,
            MediaFile::TYPE_AUDIO,
            MediaFile::TYPE_DOCUMENT,
            MediaFile::TYPE_ARCHIVE,
            MediaFile::TYPE_OTHER,
        ];

        $this->typeFilter = in_array($filter, $allowed, true) ? $filter : 'all';
    }

    public function navigateToFolder(int $folderId): void
    {
        if ($folderId <= 0) {
            $this->currentFolderId = 0;
            $this->parentFolderId = 0;
            $this->resetPage();
            if ($this->selectMode) {
                $this->clearSelection();
            }
            return;
        }

        $folder = MediaFolder::find($folderId);
        if (! $folder) {
            $this->dispatch('showToastr', type: 'error', message: 'Folder not found.');
            return;
        }

        $this->currentFolderId = $folder->id;
        $this->parentFolderId = (int) ($folder->parent_id ?? 0);
        $this->resetPage();
        if ($this->selectMode) {
            $this->clearSelection();
        }
    }

    public function goToParent(): void
    {
        if ($this->currentFolderId === 0) {
            return;
        }

        $this->navigateToFolder($this->parentFolderId);
    }

    public function createFolder(): void
    {
        if (Auth::user()->cannot('media.create')) {
            $this->dispatch('showToastr', type: 'error', message: 'You do not have permission to create folders.');
            return;
        }

        $this->newFolderName = trim($this->newFolderName);

        $this->validate([
            'newFolderName' => 'required|string|max:120',
        ], [
            'newFolderName.required' => 'Folder name is required.',
        ]);

        $name = $this->newFolderName;

        $duplicateExists = MediaFolder::where('parent_id', $this->currentFolderId)
            ->whereRaw('LOWER(name) = ?', [strtolower($name)])
            ->exists();

        if ($duplicateExists) {
            $this->addError('newFolderName', 'A folder with this name already exists in this location.');
            return;
        }

        $this->resetErrorBag('newFolderName');

        MediaFolder::create([
            'name' => $name,
            'parent_id' => $this->currentFolderId,
            'user_id' => Auth::id(),
        ]);

        $this->newFolderName = '';
        $this->dispatch('showToastr', type: 'success', message: 'Folder created successfully.');
    }

    #[On('mediaPickerOpened')]
    public function handlePickerOpened(): void
    {
        if (! $this->selectMode) {
            return;
        }

        $this->clearSelection();
        $this->resetPage();
    }

    public function selectMedia(int $mediaId): void
    {
        if (! $this->selectMode) {
            return;
        }

        $media = MediaFile::find($mediaId);

        if (! $media) {
            $this->dispatch('showToastr', type: 'error', message: 'The selected media file could not be found.');
            return;
        }

        $this->selectedMediaId = $media->id;
        $resolvedUrl = $this->resolveUrl($media);

        $this->selectedMediaDetails = [
            'id' => $media->id,
            'name' => $media->displayName(),
            'url' => $resolvedUrl,
            'full_url' => $resolvedUrl,
            'path' => $media->path,
            'type' => $media->type,
            'mime_type' => $media->mime_type,
            'size' => $media->sizeForHumans(1),
            'dimensions' => $media->width && $media->height ? sprintf('%d×%d', $media->width, $media->height) : null,
        ];
    }

    public function clearSelection(): void
    {
        $this->selectedMediaId = null;
        $this->selectedMediaDetails = [];
    }

    public function confirmSelection(): void
    {
        if (! $this->selectMode || ! $this->selectedMediaId) {
            return;
        }

        $details = $this->selectedMediaDetails;

        if (empty($details)) {
            $media = MediaFile::find($this->selectedMediaId);

            if (! $media) {
                $this->dispatch('showToastr', type: 'error', message: 'The selected media file could not be found.');
                $this->clearSelection();
                return;
            }

            $resolvedUrl = $this->resolveUrl($media);

            $details = [
                'id' => $media->id,
                'name' => $media->displayName(),
                'url' => $resolvedUrl,
                'full_url' => $resolvedUrl,
                'path' => $media->path,
                'type' => $media->type,
                'mime_type' => $media->mime_type,
            ];
        }

        if (! isset($details['mimeType']) && isset($details['mime_type'])) {
            $details['mimeType'] = $details['mime_type'];
        }

        $details['__eventToken'] = $details['__eventToken'] ?? (string) Str::uuid();

        $this->dispatch($this->selectEvent, $details);
        $this->dispatch('mediaPickerClosed');
        $this->clearSelection();
    }

    public function renameFolder(int $folderId, string $newName): void
    {
        if ($folderId <= 0) {
            return;
        }

        if (Auth::user()->cannot('media.edit')) {
            $this->dispatch('showToastr', type: 'error', message: 'You do not have permission to rename folders.');
            return;
        }

        $newName = trim($newName);
        if ($newName === '') {
            $this->dispatch('showToastr', type: 'error', message: 'Folder name cannot be empty.');
            return;
        }

        $folder = MediaFolder::findOrFail($folderId);

        $duplicateExists = MediaFolder::where('parent_id', $folder->parent_id)
            ->whereRaw('LOWER(name) = ?', [strtolower($newName)])
            ->where('id', '!=', $folderId)
            ->exists();

        if ($duplicateExists) {
            $this->dispatch('showToastr', type: 'error', message: 'Another folder with this name already exists.');
            return;
        }

        $folder->update(['name' => $newName]);

        $this->dispatch('showToastr', type: 'success', message: 'Folder renamed successfully.');
    }

    public function deleteFolder(int $folderId): void
    {
        if ($folderId <= 0) {
            return;
        }

        if (Auth::user()->cannot('media.delete')) {
            $this->dispatch('showToastr', type: 'error', message: 'You do not have permission to delete folders.');
            return;
        }

        $folder = MediaFolder::findOrFail($folderId);
        $parentId = (int) ($folder->parent_id ?? 0);

        $this->removeFolder($folder);

        if ($this->currentFolderId === $folderId) {
            $this->navigateToFolder($parentId);
        } else {
            $this->resetPage();
        }

        $this->dispatch('showToastr', type: 'success', message: 'Folder deleted successfully.');
    }

    public function startEditing(int $mediaId): void
    {
        if (Auth::user()->cannot('media.edit')) {
            $this->dispatch('showToastr', type: 'error', message: 'You do not have permission to edit media.');
            return;
        }

        $media = MediaFile::findOrFail($mediaId);

        $this->editingId = $media->id;
        $this->editingAltText = (string) ($media->alt_text ?? '');
        $this->editingCaption = (string) ($media->caption ?? '');
        $this->resizeWidth = $media->width;
        $this->resizeHeight = $media->height;

        $this->dispatch(
            'openMediaEditor',
            id: $media->id,
            url: $this->resolveUrl($media),
            isImage: $media->type === MediaFile::TYPE_IMAGE,
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

        $media = MediaFile::findOrFail($mediaId);
        $this->deleteMediaFile($media);
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

        $media = MediaFile::findOrFail($mediaId);
        $altText = trim((string) Arr::get($payload, 'altText', ''));
        $caption = trim((string) Arr::get($payload, 'caption', ''));

        $media->update([
            'alt_text' => $altText !== '' ? $altText : null,
            'caption' => $caption !== '' ? $caption : null,
        ]);

        if ($media->type === MediaFile::TYPE_IMAGE) {
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
        $currentFolderId = max(0, $this->currentFolderId);

        $folders = MediaFolder::query()
            ->where('parent_id', $currentFolderId)
            ->withCount(['files', 'children'])
            ->when($this->search !== '', function ($query) {
                $searchTerm = '%'.$this->search.'%';
                $query->where('name', 'like', $searchTerm);
            })
            ->orderBy('name')
            ->get();

        $mediaItems = MediaFile::query()
            ->where('folder_id', $currentFolderId)
            ->when($this->typeFilter !== 'all', function ($query) {
                $query->where('type', $this->typeFilter);
            })
            ->when($this->search !== '', function ($query) {
                $searchTerm = '%'.$this->search.'%';
                $query->where(function ($nested) use ($searchTerm) {
                    $nested->where('name', 'like', $searchTerm)
                        ->orWhere('original_name', 'like', $searchTerm)
                        ->orWhere('file_name', 'like', $searchTerm)
                        ->orWhere('alt_text', 'like', $searchTerm)
                        ->orWhere('caption', 'like', $searchTerm);
                });
            })
            ->orderBy('created_at', $this->sortDirection === 'asc' ? 'asc' : 'desc')
            ->paginate($this->perPage);

        return view('livewire.admin.media-library', [
            'mediaItems' => $mediaItems,
            'folders' => $folders,
        ]);
    }

    #[Computed]
    public function breadcrumbs(): array
    {
        $trail = [
            ['id' => 0, 'name' => 'Root'],
        ];

        if ($this->currentFolderId === 0) {
            return $trail;
        }

        $stack = [];
        $folder = MediaFolder::find($this->currentFolderId);

        while ($folder) {
            $stack[] = [
                'id' => $folder->id,
                'name' => $folder->name,
            ];

            if (! $folder->parent_id) {
                break;
            }

            $folder = MediaFolder::find($folder->parent_id);
        }

        if (empty($stack)) {
            return $trail;
        }

        return array_merge($trail, array_reverse($stack));
    }

    #[Computed]
    public function libraryStats(): array
    {
        $totals = MediaFile::selectRaw('type, COUNT(*) as aggregate')
            ->groupBy('type')
            ->pluck('aggregate', 'type')
            ->all();

        $totalCount = array_sum($totals);

        return [
            'total' => $totalCount,
            'images' => $totals[MediaFile::TYPE_IMAGE] ?? 0,
            'videos' => $totals[MediaFile::TYPE_VIDEO] ?? 0,
            'documents' => $totals[MediaFile::TYPE_DOCUMENT] ?? 0,
            'audio' => $totals[MediaFile::TYPE_AUDIO] ?? 0,
            'archives' => $totals[MediaFile::TYPE_ARCHIVE] ?? 0,
            'other' => $totals[MediaFile::TYPE_OTHER] ?? 0,
        ];
    }

    protected function removeFolder(MediaFolder $folder): void
    {
        $folder->loadMissing('children', 'files');

        foreach ($folder->children as $child) {
            $this->removeFolder($child);
        }

        foreach ($folder->files as $file) {
            $this->deleteMediaFile($file);
        }

        $folder->delete();
    }

    protected function deleteMediaFile(MediaFile $media): void
    {
        $disk = Storage::disk($media->disk);
        if ($disk->exists($media->path)) {
            $disk->delete($media->path);
        }

        $media->delete();
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

        $this->sanitizeCurrentFolder();

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
        if ($type === MediaFile::TYPE_IMAGE) {
            $dimensions = @getimagesize($file->getRealPath());
            if (is_array($dimensions)) {
                $width = $dimensions[0] ?? null;
                $height = $dimensions[1] ?? null;
            }
        }

        MediaFile::create([
            'disk' => $disk,
            'folder_id' => $this->currentFolderId,
            'path' => $path,
            'file_name' => $fileName,
            'name' => $originalName,
            'original_name' => $originalName,
            'mime_type' => $mimeType,
            'type' => $type,
            'size' => $size,
            'width' => $width,
            'height' => $height,
            'user_id' => Auth::id(),
        ]);
    }

    protected function determineType(?string $mime, ?string $extension = null): string
    {
        $type = $this->determineTypeFromMime($mime);

        if ($type !== MediaFile::TYPE_OTHER) {
            return $type;
        }

        if ($extension) {
            $extension = strtolower($extension);
            $typeFromExtension = $this->determineTypeFromExtension($extension);

            if ($typeFromExtension !== MediaFile::TYPE_OTHER) {
                return $typeFromExtension;
            }
        }

        return MediaFile::TYPE_OTHER;
    }

    protected function determineTypeFromMime(?string $mime): string
    {
        if (! $mime) {
            return MediaFile::TYPE_OTHER;
        }

        if (str_starts_with($mime, 'image/')) {
            return MediaFile::TYPE_IMAGE;
        }

        if (str_starts_with($mime, 'video/')) {
            return MediaFile::TYPE_VIDEO;
        }

        if (str_starts_with($mime, 'audio/')) {
            return MediaFile::TYPE_AUDIO;
        }

        if (Str::contains($mime, ['pdf', 'msword', 'spreadsheet', 'presentation']) ||
            str_contains($mime, 'text/')) {
            return MediaFile::TYPE_DOCUMENT;
        }

        if (Str::contains($mime, ['zip', 'rar', 'tar', 'gzip'])) {
            return MediaFile::TYPE_ARCHIVE;
        }

        return MediaFile::TYPE_OTHER;
    }

    protected function determineTypeFromExtension(string $extension): string
    {
        if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg', 'heic', 'heif'], true)) {
            return MediaFile::TYPE_IMAGE;
        }

        if (in_array($extension, ['mp4', 'mov', 'avi', 'mkv', 'webm', 'wmv', 'flv'], true)) {
            return MediaFile::TYPE_VIDEO;
        }

        if (in_array($extension, ['mp3', 'wav', 'ogg', 'm4a', 'flac', 'aac'], true)) {
            return MediaFile::TYPE_AUDIO;
        }

        if (in_array($extension, ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'rtf', 'csv'], true)) {
            return MediaFile::TYPE_DOCUMENT;
        }

        if (in_array($extension, ['zip', 'rar', 'tar', 'gz', 'gzip', '7z'], true)) {
            return MediaFile::TYPE_ARCHIVE;
        }

        return MediaFile::TYPE_OTHER;
    }

    protected function manipulateImage(MediaFile $media, $cropData, $resizeData): void
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

    protected function resolveUrl(MediaFile $media): string
    {
        return $media->url();
    }
}
