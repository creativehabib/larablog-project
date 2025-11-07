@php
    use App\Models\MediaFile;
    use Illuminate\Support\Str;
@endphp

@php
    $hasFolders = isset($folders) && $folders->isNotEmpty();
    $hasMedia = $mediaItems->isNotEmpty();
@endphp

@php
    $isSelecting = $selectMode ?? false;
    $selectedId = $selectedMediaId ?? null;
    $selectedDetails = $selectedMediaDetails ?? [];
@endphp

<div class="media-library" x-data="mediaLibraryComponent()" x-init="init()">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-center">
                <div class="col-xl-3 col-lg-4">
                    <div class="text-muted text-uppercase small fw-semibold">Library Overview</div>
                    <div class="h4 mb-0">{{ $this->libraryStats['total'] ?? 0 }} Files</div>
                    <div class="small text-muted">Images {{ $this->libraryStats['images'] ?? 0 }} · Videos {{ $this->libraryStats['videos'] ?? 0 }} · Documents {{ $this->libraryStats['documents'] ?? 0 }}</div>
                </div>
                <div class="col-xl-3 col-lg-4">
                    <label for="media-search" class="form-label mb-1 text-muted small text-uppercase">Search</label>
                    <div class="input-group">
                        <span class="input-group-text border-right-0"><i class="fas fa-search"></i></span>
                        <input id="media-search" type="search" class="form-control border-left-0" placeholder="Search files or folders..." wire:model.live.debounce.500ms="search">
                    </div>
                </div>
                <div class="col-xl-3 col-lg-4">
                    <label for="media-type-filter" class="form-label mb-1 text-muted small text-uppercase">Filter</label>
                    <select id="media-type-filter" class="custom-select" wire:change="setTypeFilter($event.target.value)">
                        <option value="all" @selected($typeFilter === 'all')>All files</option>
                        <option value="{{ MediaFile::TYPE_IMAGE }}" @selected($typeFilter === MediaFile::TYPE_IMAGE)>Images</option>
                        <option value="{{ MediaFile::TYPE_VIDEO }}" @selected($typeFilter === MediaFile::TYPE_VIDEO)>Videos</option>
                        <option value="{{ MediaFile::TYPE_DOCUMENT }}" @selected($typeFilter === MediaFile::TYPE_DOCUMENT)>Documents</option>
                        <option value="{{ MediaFile::TYPE_AUDIO }}" @selected($typeFilter === MediaFile::TYPE_AUDIO)>Audio</option>
                        <option value="{{ MediaFile::TYPE_ARCHIVE }}" @selected($typeFilter === MediaFile::TYPE_ARCHIVE)>Archives</option>
                        <option value="{{ MediaFile::TYPE_OTHER }}" @selected($typeFilter === MediaFile::TYPE_OTHER)>Other</option>
                    </select>
                </div>
                <div class="col-xl-3 col-lg-12">
                    <label class="form-label mb-1 text-muted small text-uppercase">View</label>
                    <div class="d-flex flex-wrap gap-2">
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-outline-secondary @if($viewMode === 'grid') active @endif" wire:click="setViewMode('grid')"><i class="fas fa-th-large me-1"></i> Grid</button>
                            <button type="button" class="btn btn-outline-secondary @if($viewMode === 'list') active @endif" wire:click="setViewMode('list')"><i class="fas fa-list me-1"></i> List</button>
                        </div>
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-outline-secondary @if($sortDirection === 'desc') active @endif" wire:click="setSortDirection('desc')" title="Newest first"><i class="fas fa-sort-amount-down"></i></button>
                            <button type="button" class="btn btn-outline-secondary @if($sortDirection === 'asc') active @endif" wire:click="setSortDirection('asc')" title="Oldest first"><i class="fas fa-sort-amount-up"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
            <nav aria-label="breadcrumb" class="flex-grow-1">
                <ol class="breadcrumb mb-0">
                    @foreach ($this->breadcrumbs as $crumb)
                        @if ($loop->last)
                            <li class="breadcrumb-item active" aria-current="page">{{ $crumb['name'] }}</li>
                        @else
                            <li class="breadcrumb-item"><a href="#" wire:click.prevent="navigateToFolder({{ $crumb['id'] }})">{{ $crumb['name'] }}</a></li>
                        @endif
                    @endforeach
                </ol>
            </nav>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-outline-secondary" wire:click="goToParent" @disabled($this->currentFolderId === 0)>
                    <i class="fas fa-level-up-alt me-1"></i> Up one level
                </button>
            </div>
        </div>
        <div class="card-footer bg-white">
            <form class="d-flex flex-column flex-md-row gap-2" wire:submit.prevent="createFolder">
                <div class="flex-grow-1">
                    <input type="text" class="form-control" placeholder="New folder name" wire:model.defer="newFolderName">
                    @error('newFolderName')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <button type="submit" class="btn btn-outline-primary">
                    <i class="fas fa-folder-plus me-1"></i> Create Folder
                </button>
            </form>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="border border-dashed rounded-3 p-4 position-relative text-center upload-drop-zone" :class="{ 'is-dropping': dropping }"
                 x-on:dragover.prevent="dropping = true" x-on:dragleave.prevent="dropping = false" x-on:drop.prevent="handleDrop($event)">
                <input type="file" multiple class="position-absolute w-100 h-100 top-0 start-0 opacity-0" x-ref="fileInput" wire:model="uploads">
                <div class="py-5">
                    <div class="display-6 text-primary mb-2"><i class="fas fa-cloud-upload-alt"></i></div>
                    <h5 class="mb-1">Drop files here or click to upload</h5>
                    <p class="text-muted mb-0">Files will be uploaded to the current folder. Maximum size 15MB each.</p>
                </div>
            </div>
            <div class="mt-3" wire:loading.flex wire:target="uploads">
                <span class="spinner-border spinner-border-sm me-2" role="status"></span> Uploading files...
            </div>
            @error('uploads.*')
            <div class="alert alert-danger mt-3">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            @if (! $hasFolders && ! $hasMedia)
                <div class="p-5 text-center text-muted">
                    <i class="fas fa-folder-open fa-2x mb-3"></i>
                    <h5 class="mb-1">This folder is empty</h5>
                    <p class="mb-0">Upload files or create a new folder to get started.</p>
                </div>
            @else
                @if ($viewMode === 'list')
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                            <tr>
                                <th style="width: 60px"></th>
                                <th>Name</th>
                                <th>Details</th>
                                <th>Alt Text</th>
                                <th>Caption</th>
                                <th class="text-end">Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if ($hasFolders)
                                @foreach ($folders as $folder)
                                    <tr wire:key="folder-row-{{ $folder->id }}">
                                        <td>
                                            <div class="d-flex align-items-center justify-content-center text-warning">
                                                <i class="fas fa-folder fa-lg"></i>
                                            </div>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-link p-0 fw-semibold text-start text-truncate" style="max-width: 220px" wire:click="navigateToFolder({{ $folder->id }})" title="{{ $folder->name }}">
                                                <i class="fas fa-folder-open me-2"></i> {{ $folder->name }}
                                            </button>
                                            <div class="text-muted small">{{ $folder->children_count }} folders · {{ $folder->files_count }} files</div>
                                        </td>
                                        <td class="text-muted small">Folder</td>
                                        <td class="small text-muted">—</td>
                                        <td class="small text-muted">—</td>
                                        <td class="text-end">
                                            <div class="btn-group btn-group-sm" role="group">
                                                <button type="button" class="btn btn-outline-secondary" x-on:click.prevent="promptRenameFolder({{ $folder->id }}, @js($folder->name))">Rename</button>
                                                <button type="button" class="btn btn-outline-danger" x-on:click.prevent="deleteFolder({{ $folder->id }})">Delete</button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif

                            @foreach ($mediaItems as $media)
                                @php $rowSelected = $isSelecting && $selectedId === $media->id; @endphp
                                <tr wire:key="media-row-{{ $media->id }}"
                                    @if ($isSelecting) wire:click="selectMedia({{ $media->id }})" style="cursor: pointer;" @endif
                                    @class([
                                        'media-selectable-row' => $isSelecting,
                                        'table-primary' => $rowSelected,
                                    ])>
                                    <td>
                                        @if ($media->type === MediaFile::TYPE_IMAGE)
                                            <img src="{{ $media->url() }}" alt="{{ $media->displayName() }}" class="rounded" style="width:48px;height:48px;object-fit:cover;">
                                        @else
                                            <span class="badge bg-secondary text-uppercase">{{ strtoupper($media->type) }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-truncate" style="max-width: 220px" title="{{ $media->displayName() }}">{{ $media->displayName() }}</div>
                                        <div class="text-muted small">{{ $media->file_name }}</div>
                                    </td>
                                    <td>
                                        <div class="text-muted small">
                                            {{ strtoupper(pathinfo($media->file_name, PATHINFO_EXTENSION)) }} · {{ $media->sizeForHumans(1) }}
                                            @if($media->width && $media->height)
                                                · {{ $media->width }}×{{ $media->height }}
                                            @endif
                                        </div>
                                    </td>
                                    <td class="small text-muted">{{ $media->alt_text ?? '—' }}</td>
                                    <td class="small text-muted">{{ $media->caption ? Str::limit($media->caption, 40) : '—' }}</td>
                                    <td class="text-end">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button type="button" class="btn btn-outline-primary" wire:click.stop="startEditing({{ $media->id }})">Edit</button>
                                            <button type="button" class="btn btn-outline-danger" x-on:click.prevent.stop="confirmDelete({{ $media->id }})">Delete</button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="row g-3 p-3">
                        @if ($hasFolders)
                            @foreach ($folders as $folder)
                                <div class="col-xl-3 col-lg-4 col-md-6" wire:key="folder-card-{{ $folder->id }}">
                                    <div class="card h-100 border-0 shadow-sm">
                                        <div class="ratio ratio-4x3 bg-light rounded-top position-relative overflow-hidden">
                                            <div class="d-flex flex-column align-items-center justify-content-center h-100 text-warning">
                                                <i class="fas fa-folder-open fa-3x"></i>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <h6 class="card-title text-truncate" title="{{ $folder->name }}">{{ $folder->name }}</h6>
                                            <p class="small text-muted mb-3">{{ $folder->children_count }} folders · {{ $folder->files_count }} files</p>
                                            <div class="d-flex gap-2">
                                                <button type="button" class="btn btn-outline-primary btn-sm flex-grow-1" wire:click="navigateToFolder({{ $folder->id }})"><i class="fas fa-folder-open me-1"></i> Open</button>
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-outline-secondary btn-sm" x-on:click.prevent="promptRenameFolder({{ $folder->id }}, @js($folder->name))"><i class="fas fa-edit"></i></button>
                                                    <button type="button" class="btn btn-outline-danger btn-sm" x-on:click.prevent="deleteFolder({{ $folder->id }})"><i class="fas fa-trash"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif

                        @foreach ($mediaItems as $media)
                            @php $cardSelected = $isSelecting && $selectedId === $media->id; @endphp
                            <div class="col-xl-3 col-lg-4 col-md-6" wire:key="media-card-{{ $media->id }}">
                                <div class="card h-100 shadow-sm {{ $isSelecting ? 'media-selectable-card border' : 'border-0' }} {{ $cardSelected ? 'selected border-primary shadow-lg' : '' }}"
                                     @if ($isSelecting) wire:click="selectMedia({{ $media->id }})" style="cursor: pointer;" @endif>
                                    <div class="ratio ratio-4x3 bg-light rounded-top position-relative overflow-hidden">
                                        @if ($media->type === MediaFile::TYPE_IMAGE)
                                            <img src="{{ $media->url() }}" alt="{{ $media->displayName() }}" class="w-100 h-100" style="object-fit: cover;">
                                        @else
                                            <div class="d-flex flex-column align-items-center justify-content-center h-100 text-muted">
                                                <i class="fas fa-file fa-2x mb-2"></i>
                                                <span class="badge bg-dark text-uppercase">{{ strtoupper($media->type) }}</span>
                                            </div>
                                        @endif
                                        @if ($isSelecting)
                                            <span class="position-absolute top-0 end-0 m-2 badge {{ $cardSelected ? 'bg-primary' : 'bg-light text-dark border' }}">
                                                <i class="fas {{ $cardSelected ? 'fa-check' : 'fa-image' }}"></i>
                                            </span>
                                        @endif
                                    </div>
                                    <div class="card-body">
                                        <h6 class="card-title text-truncate" title="{{ $media->displayName() }}">{{ $media->displayName() }}</h6>
                                        <p class="small text-muted mb-3">{{ strtoupper(pathinfo($media->file_name, PATHINFO_EXTENSION)) }} · {{ $media->sizeForHumans(1) }} @if($media->width && $media->height) · {{ $media->width }}×{{ $media->height }} @endif</p>
                                        <div class="d-flex gap-2">
                                            <button type="button" class="btn btn-outline-primary btn-sm flex-grow-1" wire:click.stop="startEditing({{ $media->id }})"><i class="fas fa-edit me-1"></i> Edit</button>
                                            <button type="button" class="btn btn-outline-danger btn-sm" x-on:click.prevent.stop="confirmDelete({{ $media->id }})"><i class="fas fa-trash"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                <div class="p-3 border-top">
                    {{ $mediaItems->links() }}
                </div>
            @endif
        </div>
    </div>

    @if ($isSelecting)
        @php
            $selectedType = $selectedDetails['type'] ?? null;
            $selectedLabel = $selectedType ? Str::headline($selectedType) : null;
        @endphp
        <div class="card shadow-sm mt-4">
            <div class="card-body d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-3 flex-grow-1">
                    <div class="rounded border bg-light d-flex align-items-center justify-content-center" style="width:72px;height:72px;">
                        @if ($selectedId && ($selectedDetails['type'] ?? null) === MediaFile::TYPE_IMAGE && ! empty($selectedDetails['url']))
                            <img src="{{ $selectedDetails['url'] }}" alt="Selected media" class="img-fluid rounded" style="max-height:70px;max-width:70px;object-fit:cover;">
                        @elseif($selectedId)
                            <i class="fas fa-file fa-2x text-muted"></i>
                        @else
                            <i class="fas fa-images fa-2x text-muted"></i>
                        @endif
                    </div>
                    <div class="min-w-0">
                        <div class="fw-semibold text-truncate">
                            {{ $selectedId ? ($selectedDetails['name'] ?? 'Selected media') : 'No media selected' }}
                        </div>
                        <div class="text-muted small">
                            @if ($selectedId)
                                {{ $selectedLabel ?? 'Media file' }}
                                @if (! empty($selectedDetails['size']))
                                    · {{ $selectedDetails['size'] }}
                                @endif
                                @if (! empty($selectedDetails['dimensions']))
                                    · {{ $selectedDetails['dimensions'] }}
                                @endif
                            @else
                                Choose a file from the library to insert into your post.
                            @endif
                        </div>
                    </div>
                </div>
                <div class="d-flex gap-2 flex-shrink-0">
                    <button type="button" class="btn btn-outline-secondary" wire:click="clearSelection" @disabled(! $selectedId)>Clear selection</button>
                    <button type="button" class="btn btn-primary" wire:click="confirmSelection" @disabled(! $selectedId)>Insert</button>
                </div>
            </div>
        </div>
    @endif

    <div class="modal fade" id="mediaEditorModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Media</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row g-4">
                        <div class="col-lg-8">
                            <div class="bg-light rounded p-2 position-relative" style="min-height: 360px;">
                                <img id="media-editor-image" x-ref="imagePreview" class="img-fluid rounded w-100" :class="{ 'd-none': !isImage }" alt="Editable preview">
                                <template x-if="!isImage">
                                    <div id="media-editor-non-image" x-ref="nonImageMessage" class="h-100 w-100 d-flex align-items-center justify-content-center flex-column text-muted" x-cloak>
                                        <i class="fas fa-file fa-3x mb-3"></i>
                                        <p class="mb-0">Cropping &amp; resizing are available for images only.</p>
                                    </div>
                                </template>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="mb-3">
                                <label class="form-label">Alt Text</label>
                                <input type="text" class="form-control" x-ref="altInput">
                                <small class="text-muted">Used for accessibility and SEO.</small>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Caption</label>
                                <textarea class="form-control" rows="3" x-ref="captionInput"></textarea>
                                <small class="text-muted">Optional description displayed with the media.</small>
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="media-url-input">File URL</label>
                                <div class="input-group">
                                    <input type="text" id="media-url-input" class="form-control" x-bind:value="fullUrl" x-ref="urlInput" readonly>
                                    <button class="btn btn-outline-secondary" type="button" x-on:click="copyUrlToClipboard()" title="Copy URL">
                                        <i class="fas" :class="urlCopied ? 'fa-check text-success' : 'fa-copy'"></i>
                                    </button>
                                </div>
                                <small class="text-success" x-show="urlCopied" x-transition x-cloak>Copied to clipboard!</small>
                            </div>
                            <template x-if="isImage">
                                <div class="border rounded p-3 mb-3">
                                    <h6 class="fw-semibold">Resize</h6>
                                    <div class="row g-2 align-items-center">
                                        <div class="col">
                                            <label class="form-label small text-muted">Width (px)</label>
                                            <input type="number" min="1" class="form-control form-control-sm" x-model.number="resizeWidth">
                                        </div>
                                        <div class="col">
                                            <label class="form-label small text-muted">Height (px)</label>
                                            <input type="number" min="1" class="form-control form-control-sm" x-model.number="resizeHeight">
                                        </div>
                                    </div>
                                    <div class="form-check mt-2">
                                        <input class="form-check-input" type="checkbox" id="media-editor-lock-ratio" x-model="lockRatio">
                                        <label class="form-check-label" for="media-editor-lock-ratio">Lock aspect ratio</label>
                                    </div>
                                    <button type="button" class="btn btn-link btn-sm px-0" x-on:click="resetCrop()">Reset crop</button>
                                </div>
                            </template>
                            <div class="small text-muted">
                                <div><strong>File:</strong> <span x-text="fileName"></span></div>
                                <div><strong>Type:</strong> <span x-text="mimeType"></span></div>
                                <div><strong>Dimensions:</strong> <span x-text="dimensions"></span></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" data-dismiss="modal" aria-label="Close">Close</button>
                    <button type="button" class="btn btn-primary" x-on:click="saveChanges()">Save changes</button>
                </div>
            </div>
        </div>
    </div>
</div>

@once
    @push('styles')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/cropperjs@1.6.2/dist/cropper.min.css" integrity="sha384-lU3u69ptYPu5GYEfzKGUSf02Pp3t/AEUReyX4OA974odKwpcWLfSomKs6S38QGlw" crossorigin="anonymous">
        <style>
            .upload-drop-zone {
                transition: all .2s ease-in-out;
                cursor: pointer;
            }

            .upload-drop-zone.is-dropping {
                background: rgba(59, 130, 246, 0.08);
                border-color: rgba(59, 130, 246, 0.6);
            }

            .upload-drop-zone input[type="file"] {
                cursor: pointer;
            }

            .media-selectable-card {
                transition: all .18s ease-in-out;
                border-style: dashed;
            }

            .media-selectable-card:hover {
                border-color: rgba(59, 130, 246, 0.5);
                box-shadow: 0 0.75rem 1.5rem rgba(59, 130, 246, 0.18);
            }

            .media-selectable-card.selected {
                border-style: solid;
            }

            .media-selectable-row {
                transition: background-color .15s ease-in-out;
            }

            .media-selectable-row:hover {
                background-color: rgba(59, 130, 246, 0.08);
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/cropperjs@1.6.2/dist/cropper.min.js" integrity="sha384-/RkNvztNFVQVwKP1HjqCOUMIqFZ3VAbwY/jGj33jjXNMTvEihQ/HVbUaz2YsmiPg" crossorigin="anonymous"></script>
        {{-- ======================================================= --}}
        {{-- START: পরিবর্তন করা হয়েছে এই SCRIPT অংশে --}}
        {{-- ======================================================= --}}
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('mediaLibraryComponent', () => ({
                    dropping: false,
                    cropper: null,
                    isImage: false,
                    lockRatio: true,
                    resizeWidth: null,
                    resizeHeight: null,
                    originalWidth: null,
                    originalHeight: null,
                    fileName: '',
                    mimeType: '',
                    mediaId: null,
                    dimensions: '—',
                    suppressRatioUpdate: false,
                    fullUrl: '',
                    urlCopied: false,

                    init() {
                        // Watchers for resize inputs
                        this.$watch('resizeWidth', (value) => {
                            if (!this.isImage || !this.lockRatio || this.suppressRatioUpdate) return;
                            if (!value || !this.originalWidth || !this.originalHeight) return;

                            const ratio = this.originalHeight / this.originalWidth;
                            this.suppressRatioUpdate = true;
                            this.resizeHeight = Math.round(value * ratio);
                            if (this.cropper) {
                                this.cropper.setData({ width: value, height: this.resizeHeight });
                            }
                            this.dimensions = `${Math.round(value)}×${Math.round(this.resizeHeight || 0)}`;
                            this.suppressRatioUpdate = false;
                        });

                        this.$watch('resizeHeight', (value) => {
                            if (!this.isImage || !this.lockRatio || this.suppressRatioUpdate) return;
                            if (!value || !this.originalWidth || !this.originalHeight) return;

                            const ratio = this.originalWidth / this.originalHeight;
                            this.suppressRatioUpdate = true;
                            this.resizeWidth = Math.round(value * ratio);
                            if (this.cropper) {
                                this.cropper.setData({ width: this.resizeWidth, height: value });
                            }
                            this.dimensions = `${Math.round(this.resizeWidth || 0)}×${Math.round(value)}`;
                            this.suppressRatioUpdate = false;
                        });

                        // Initialize and cache the modal instance
                        const modalEl = document.getElementById('mediaEditorModal');
                        let modalInstance = null;
                        if (window.bootstrap && window.bootstrap.Modal) {
                            modalInstance = new bootstrap.Modal(modalEl);
                        } else if (window.jQuery) {
                            // Fallback for jQuery-based Bootstrap
                            modalInstance = {
                                show: () => window.jQuery(modalEl).modal('show'),
                                hide: () => window.jQuery(modalEl).modal('hide'),
                            };
                        }

                        // Event Listener to open the modal
                        window.addEventListener('openMediaEditor', (event) => {
                            const detail = event.detail[0] || event.detail; // Supports Livewire v3+ and older versions

                            this.mediaId = detail.id || null;
                            this.isImage = Boolean(detail.isImage);
                            this.fileName = detail.url ? detail.url.split('/').pop() : '';
                            this.mimeType = detail.mimeType || '';
                            this.resizeWidth = detail.width || null;
                            this.resizeHeight = detail.height || null;
                            this.originalWidth = detail.width || null;
                            this.originalHeight = detail.height || null;
                            this.dimensions = detail.width && detail.height ? `${detail.width}×${detail.height}` : '—';
                            this.$refs.altInput.value = detail.altText || '';
                            this.$refs.captionInput.value = detail.caption || '';

                            this.fullUrl = detail.url || '';
                            this.urlCopied = false;

                            if (modalInstance) {
                                modalInstance.show();
                            }

                            this.$nextTick(() => {
                                this.setupEditor(detail);
                            });
                        });


                        // Event Listener to close the modal
                        window.addEventListener('mediaEditorClosed', () => {
                            if (modalInstance) {
                                modalInstance.hide();
                            }
                            this.destroyCropper();
                        });

                        // Safety cleanup hook for Livewire updates
                        if (typeof Livewire !== 'undefined') {
                            Livewire.hook('message.processed', () => {
                                if (document.body.classList.contains('modal-open') === false) {
                                    document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
                                }
                            });
                        }
                    },

                    // ===============================================
                    // START: পরিবর্তিত copyUrlToClipboard ফাংশন
                    // ===============================================
                    copyUrlToClipboard() {
                        if (!this.fullUrl) return;
                        const inputEl = this.$refs.urlInput;
                        try {
                            inputEl.select();
                            inputEl.setSelectionRange(0, 999999);
                            const successful = document.execCommand('copy');
                            if (successful) {
                                this.urlCopied = true;
                                setTimeout(() => {
                                    this.urlCopied = false;
                                }, 2000); // ২ সেকেন্ড পর মেসেজ হাইড করুন
                            } else {
                                alert('Failed to copy URL. Please copy manually.');
                            }
                        } catch (err) {
                            console.error('Failed to copy URL: ', err);
                            alert('Failed to copy URL. Please copy manually.');
                        }
                        window.getSelection().removeAllRanges();
                        inputEl.blur();
                    },
                    // ===============================================
                    // END: পরিবর্তিত copyUrlToClipboard ফাংশন
                    // ===============================================

                    handleDrop(event) {
                        this.dropping = false;
                        const files = event.dataTransfer?.files;
                        if (files && files.length) {
                            this.$refs.fileInput.files = files;
                            this.$refs.fileInput.dispatchEvent(new Event('change', { bubbles: true }));
                        }
                    },

                    setupEditor(detail) {
                        const imageEl = this.$refs.imagePreview;
                        this.destroyCropper();

                        if (!this.isImage) {
                            if (imageEl) imageEl.src = '';
                            return;
                        }

                        if (imageEl) {
                            imageEl.src = detail.url ? `${detail.url}?t=${Date.now()}` : '';
                            imageEl.onload = () => {
                                this.originalWidth = imageEl.naturalWidth;
                                this.originalHeight = imageEl.naturalHeight;
                                this.resizeWidth = imageEl.naturalWidth;
                                this.resizeHeight = imageEl.naturalHeight;
                                this.dimensions = `${imageEl.naturalWidth}×${imageEl.naturalHeight}`;
                                this.initCropper(imageEl);
                            };
                        }
                    },

                    initCropper(imageEl) {
                        this.cropper = new Cropper(imageEl, {
                            viewMode: 1,
                            autoCropArea: 1,
                            responsive: true,
                            checkOrientation: false,
                            crop: () => {
                                if (!this.cropper) return;
                                const data = this.cropper.getData();
                                this.suppressRatioUpdate = true;
                                this.resizeWidth = Math.round(data.width);
                                this.resizeHeight = Math.round(data.height);
                                this.dimensions = `${this.resizeWidth}×${this.resizeHeight}`;
                                this.suppressRatioUpdate = false;
                            }
                        });
                    },

                    destroyCropper() {
                        if (this.cropper) {
                            this.cropper.destroy();
                            this.cropper = null;
                        }
                    },

                    resetCrop() {
                        if (this.cropper) {
                            this.cropper.reset();
                        }
                    },

                    async saveChanges() {
                        const altText = this.$refs.altInput ? this.$refs.altInput.value : '';
                        const caption = this.$refs.captionInput ? this.$refs.captionInput.value : '';

                        const payload = {
                            id: this.mediaId,
                            altText,
                            caption,
                            crop: null,
                            resize: null,
                        };

                        if (this.isImage && this.cropper) {
                            const data = this.cropper.getData(true);
                            payload.crop = {
                                x: data.x,
                                y: data.y,
                                width: data.width,
                                height: data.height,
                            };
                            if (this.resizeWidth && this.resizeHeight) {
                                payload.resize = {
                                    width: Math.round(this.resizeWidth),
                                    height: Math.round(this.resizeHeight),
                                };
                            }
                        }

                        await this.$wire.call('saveMediaEditor', payload);
                        window.dispatchEvent(new CustomEvent('mediaEditorClosed'));
                    },

                    confirmDelete(id) {
                        if (confirm('Are you sure you want to delete this media file?')) {
                            this.$wire.call('deleteMedia', id);
                        }
                    },

                    promptRenameFolder(id, currentName) {
                        const nextName = prompt('Enter a new folder name', currentName || '');
                        if (nextName === null) {
                            return;
                        }
                        const trimmed = nextName.trim();
                        if (trimmed === '') {
                            alert('Folder name cannot be empty.');
                            return;
                        }
                        this.$wire.call('renameFolder', id, trimmed);
                    },

                    deleteFolder(id) {
                        if (confirm('Deleting a folder will remove all nested folders and files. Continue?')) {
                            this.$wire.call('deleteFolder', id);
                        }
                    }
                }));
            });
        </script>
    @endpush
@endonce
