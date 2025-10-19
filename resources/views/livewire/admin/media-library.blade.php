@php
    use App\Models\MediaItem;
    use Illuminate\Support\Str;
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
                        <input id="media-search" type="search" class="form-control border-left-0" placeholder="Search media..." wire:model.live.debounce.500ms="search">
                    </div>
                </div>
                <div class="col-xl-3 col-lg-4">
                    <label for="media-type-filter" class="form-label mb-1 text-muted small text-uppercase">Filter</label>
                    <select id="media-type-filter" class="custom-select" wire:change="setTypeFilter($event.target.value)">
                        <option value="all" @selected($typeFilter === 'all')>All files</option>
                        <option value="{{ MediaItem::TYPE_IMAGE }}" @selected($typeFilter === MediaItem::TYPE_IMAGE)>Images</option>
                        <option value="{{ MediaItem::TYPE_VIDEO }}" @selected($typeFilter === MediaItem::TYPE_VIDEO)>Videos</option>
                        <option value="{{ MediaItem::TYPE_DOCUMENT }}" @selected($typeFilter === MediaItem::TYPE_DOCUMENT)>Documents</option>
                        <option value="{{ MediaItem::TYPE_AUDIO }}" @selected($typeFilter === MediaItem::TYPE_AUDIO)>Audio</option>
                        <option value="{{ MediaItem::TYPE_ARCHIVE }}" @selected($typeFilter === MediaItem::TYPE_ARCHIVE)>Archives</option>
                        <option value="{{ MediaItem::TYPE_OTHER }}" @selected($typeFilter === MediaItem::TYPE_OTHER)>Other</option>
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
        <div class="card-body">
            <div class="border border-dashed rounded-3 p-4 position-relative text-center upload-drop-zone" :class="{ 'is-dropping': dropping }"
                 x-on:dragover.prevent="dropping = true" x-on:dragleave.prevent="dropping = false" x-on:drop.prevent="handleDrop($event)">
                <input type="file" multiple class="position-absolute w-100 h-100 top-0 start-0 opacity-0" x-ref="fileInput" wire:model="uploads">
                <div class="py-5">
                    <div class="display-6 text-primary mb-2"><i class="fas fa-cloud-upload-alt"></i></div>
                    <h5 class="mb-1">Drop files here or click to upload</h5>
                    <p class="text-muted mb-0">You can upload multiple images, videos or documents up to 15MB each.</p>
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
            @if ($mediaItems->isEmpty())
                <div class="p-5 text-center text-muted">
                    <i class="fas fa-images fa-2x mb-3"></i>
                    <h5 class="mb-1">No media files found</h5>
                    <p class="mb-0">Upload new files to build your media library.</p>
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
                            @foreach ($mediaItems as $media)
                                <tr wire:key="media-row-{{ $media->id }}">
                                    <td>
                                        @if ($media->type === MediaItem::TYPE_IMAGE)
                                            <img src="{{ $media->url() }}" alt="{{ $media->original_name }}" class="rounded" style="width:48px;height:48px;object-fit:cover;">
                                        @else
                                            <span class="badge bg-secondary text-uppercase">{{ strtoupper($media->type) }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-truncate" style="max-width: 220px" title="{{ $media->original_name }}">{{ $media->original_name }}</div>
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
                                            <button type="button" class="btn btn-outline-primary" wire:click="startEditing({{ $media->id }})">Edit</button>
                                            <button type="button" class="btn btn-outline-danger" x-on:click.prevent="confirmDelete({{ $media->id }})">Delete</button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="row g-3 p-3">
                        @foreach ($mediaItems as $media)
                            <div class="col-xl-3 col-lg-4 col-md-6" wire:key="media-card-{{ $media->id }}">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="ratio ratio-4x3 bg-light rounded-top position-relative overflow-hidden">
                                        @if ($media->type === MediaItem::TYPE_IMAGE)
                                            <img src="{{ $media->url() }}" alt="{{ $media->original_name }}" class="w-100 h-100" style="object-fit: cover;">
                                        @else
                                            <div class="d-flex flex-column align-items-center justify-content-center h-100 text-muted">
                                                <i class="fas fa-file fa-2x mb-2"></i>
                                                <span class="badge bg-dark text-uppercase">{{ strtoupper($media->type) }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="card-body">
                                        <h6 class="card-title text-truncate" title="{{ $media->original_name }}">{{ $media->original_name }}</h6>
                                        <p class="small text-muted mb-3">{{ strtoupper(pathinfo($media->file_name, PATHINFO_EXTENSION)) }} · {{ $media->sizeForHumans(1) }} @if($media->width && $media->height) · {{ $media->width }}×{{ $media->height }} @endif</p>
                                        <div class="d-flex gap-2">
                                            <button type="button" class="btn btn-outline-primary btn-sm flex-grow-1" wire:click="startEditing({{ $media->id }})"><i class="fas fa-edit me-1"></i> Edit</button>
                                            <button type="button" class="btn btn-outline-danger btn-sm" x-on:click.prevent="confirmDelete({{ $media->id }})"><i class="fas fa-trash"></i></button>
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
                    }
                }));
            });
        </script>
    @endpush
@endonce
