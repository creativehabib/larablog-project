@php use App\Models\Post; @endphp
<div>
    <form wire:submit.prevent="save" class="card card-fluid">
        <div class="card-body">
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="postTitle">Post Title <span class="text-danger">*</span></label>
                    <input type="text" id="postTitle" class="form-control @error('title') is-invalid @enderror" wire:model.defer="title" placeholder="Enter post title">
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group col-md-3">
                    <label for="postSlug">Slug</label>
                    <input type="text" id="postSlug" class="form-control @error('slug') is-invalid @enderror" wire:model.defer="slug" placeholder="Auto generated from title">
                    @error('slug')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group col-md-3">
                    <label for="contentType">Post Type <span class="text-danger">*</span></label>
                    <select id="contentType" class="form-control @error('content_type') is-invalid @enderror" wire:model.live="content_type">
                        <option value="{{ Post::CONTENT_TYPE_ARTICLE }}">Article</option>
                        <option value="{{ Post::CONTENT_TYPE_VIDEO }}">Video</option>
                    </select>
                    @error('content_type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="postCategory">Category <span class="text-danger">*</span></label>
                    <select id="postCategory" class="form-control @error('category_id') is-invalid @enderror" wire:model.live="category_id">
                        <option value="">Select category</option>
                        @foreach ($this->categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group col-md-6">
                    <label for="postSubCategory">Sub Category</label>
                    <select id="postSubCategory" class="form-control @error('sub_category_id') is-invalid @enderror" wire:model="sub_category_id">
                        <option value="">None</option>
                        @foreach ($this->availableSubCategories as $subCategory)
                            <option value="{{ $subCategory->id }}">{{ $subCategory->name }}</option>
                        @endforeach
                    </select>
                    @error('sub_category_id')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            @if ($content_type === Post::CONTENT_TYPE_VIDEO)
                <div class="card card-body border mb-4">
                    <h5 class="card-title mb-3">Video Details</h5>
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label for="videoSource">Video Source <span class="text-danger">*</span></label>
                            <select id="videoSource" class="form-control @error('video_source') is-invalid @enderror" wire:model.live="video_source">
                                <option value="{{ Post::VIDEO_SOURCE_EMBED }}">Embed Code</option>
                                <option value="{{ Post::VIDEO_SOURCE_UPLOAD }}">Direct Upload</option>
                            </select>
                            @error('video_source')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group col-md-4">
                            <label for="videoDuration">Duration</label>
                            <input type="text" id="videoDuration" class="form-control @error('video_duration') is-invalid @enderror" wire:model.defer="video_duration" placeholder="e.g. 12:45 or 8 min">
                            <small class="form-text text-muted">Optional – helps viewers understand the runtime.</small>
                            @error('video_duration')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group col-md-4">
                            <label for="videoPlaylist">Playlist / Series</label>
                            <select id="videoPlaylist" class="form-control @error('video_playlist_id') is-invalid @enderror" wire:model="video_playlist_id">
                                <option value="">None</option>
                                @foreach ($this->videoPlaylists as $playlist)
                                    <option value="{{ $playlist->id }}">{{ $playlist->name }}</option>
                                @endforeach
                            </select>
                            @error('video_playlist_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    @if ($video_source === Post::VIDEO_SOURCE_EMBED)
                        <div class="form-group">
                            <label for="videoEmbedCode">Embed Code</label>
                            <textarea id="videoEmbedCode" rows="4" class="form-control @error('video_embed_code') is-invalid @enderror" wire:model.live.debounce.500ms="video_embed_code" placeholder="Paste the iframe embed code from YouTube, Vimeo, etc."></textarea>
                            <small class="form-text text-muted">Embedding keeps your server fast and avoids heavy bandwidth usage.</small>
                            @error('video_embed_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="videoUrl">Video URL</label>
                            <input type="url" id="videoUrl" class="form-control @error('video_url') is-invalid @enderror" wire:model.live.debounce.500ms="video_url" placeholder="https://www.youtube.com/watch?v=...">
                            <small class="form-text text-muted">Optional fallback – we’ll detect the platform automatically.</small>
                            @error('video_url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @if ($video_provider)
                                <p class="text-muted small mt-2 mb-0">Detected platform: {{ ucfirst($video_provider) }}</p>
                            @endif
                        </div>
                    @else
                        <div class="form-group">
                            <label for="videoUpload">Video File @if (! $existingVideoPath)<span class="text-danger">*</span>@endif</label>
                            <input type="file" id="videoUpload" class="form-control-file @error('video_upload') is-invalid @enderror" wire:model="video_upload" accept="video/mp4,video/quicktime,video/webm">
                            <small class="form-text text-muted">Upload MP4, MOV or WEBM files (max 200MB). Direct uploads use more storage and bandwidth.</small>
                            @error('video_upload')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <div class="mt-3">
                                @if ($existingVideoPath && ! $video_upload)
                                    <div class="d-flex flex-column flex-md-row align-items-md-center gap-2">
                                        <span class="text-muted small">Current file: <code>{{ $existingVideoPath }}</code></span>
                                        <button type="button" class="btn btn-sm btn-outline-danger" wire:click="removeExistingVideo">Remove current video</button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    @if ($video_preview_html)
                        <div class="mt-3">
                            <p class="text-muted small mb-2">Preview</p>
                            {!! $video_preview_html !!}
                        </div>
                    @endif
                </div>
            @endif

            @if ($content_type === Post::CONTENT_TYPE_ARTICLE)
                <div class="form-group">
                    <label for="postDescription">Post Description <span class="text-danger">*</span></label>
                    <div wire:ignore data-post-description-editor>
                        <textarea id="postDescription" class="form-control">{!! $description !!}</textarea>
                    </div>
                    <input type="hidden" id="postDescriptionData" wire:model="description">
                    @error('description')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            @endif

            <div class="form-row">
                @if ($content_type === Post::CONTENT_TYPE_ARTICLE)
                    <div class="form-group col-md-6">
                        <label for="thumbnail">Post Thumbnail</label>
                        <input type="file" id="thumbnail" class="form-control-file @error('thumbnail') is-invalid @enderror" wire:model="thumbnail" accept="image/*">
                        @error('thumbnail')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <div class="mt-3">
                            @if ($thumbnail)
                                <p class="text-muted small mb-2">Preview:</p>
                                <img src="{{ $thumbnail->temporaryUrl() }}" alt="Thumbnail preview" class="img-thumbnail" style="max-height: 180px;">
                            @elseif ($existingThumbnail)
                                <p class="text-muted small mb-2">Current thumbnail:</p>
                                <div class="d-flex align-items-center gap-3">
                                    <img src="{{ asset('storage/' . $existingThumbnail) }}" alt="Current thumbnail" class="img-thumbnail" style="max-height: 180px;">
                                    <button type="button" class="btn btn-sm btn-outline-danger" wire:click="removeExistingThumbnail">Remove</button>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
                <div class="form-group {{ $content_type === Post::CONTENT_TYPE_ARTICLE ? 'col-md-6' : 'col-12' }}">
                    <label>Post Options</label>
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="isFeatured" wire:model.defer="is_featured">
                        <label class="custom-control-label" for="isFeatured">Mark as featured post</label>
                    </div>
                    <div class="custom-control custom-switch mt-2">
                        <input type="checkbox" class="custom-control-input" id="allowComments" wire:model.defer="allow_comments">
                        <label class="custom-control-label" for="allowComments">Allow comments on this post</label>
                    </div>
                    <div class="custom-control custom-switch mt-2">
                        <input type="checkbox" class="custom-control-input" id="isIndexable" wire:model.defer="is_indexable">
                        <label class="custom-control-label" for="isIndexable">Allow search engines to index</label>
                    </div>
                </div>
            </div>

            <div class="card card-body border mt-4">
                <h5 class="card-title">Meta Information</h5>
                <div class="form-group">
                    <label for="metaTitle">Meta Title</label>
                    <input type="text" id="metaTitle" class="form-control @error('meta_title') is-invalid @enderror" wire:model.defer="meta_title" placeholder="Custom meta title">
                    @error('meta_title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="metaDescription">Meta Description</label>
                    <textarea id="metaDescription" rows="3" class="form-control @error('meta_description') is-invalid @enderror" wire:model.defer="meta_description" placeholder="Short summary for search engines"></textarea>
                    @error('meta_description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="metaKeywords">Meta Keywords</label>
                    <input type="text" id="metaKeywords" class="form-control @error('meta_keywords') is-invalid @enderror" wire:model.defer="meta_keywords" placeholder="Comma separated keywords">
                    @error('meta_keywords')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
        <div class="card-footer d-flex justify-content-end">
            <a href="{{ route('admin.posts.index') }}" class="btn btn-link">Cancel</a>
            <button type="submit" class="btn btn-primary ml-2">
                {{ $post ? 'Update Post' : 'Create Post' }}
            </button>
        </div>
    </form>
</div>

@pushOnce('scripts')
    <script src="{{ asset('ckeditor/ckeditor.js') }}"></script>
    <script>
        document.addEventListener('livewire:init', () => {
            const debounce = (callback, wait = 300) => {
                let timeoutId;

                return (...args) => {
                    if (timeoutId) {
                        clearTimeout(timeoutId);
                    }

                    timeoutId = setTimeout(() => {
                        callback(...args);
                    }, wait);
                };
            };

            let editorInstance = null;
            const editorState = {
                hiddenField: null,
                lastSetValue: '',
            };

            const syncHiddenField = (value) => {
                if (!editorState.hiddenField) {
                    return;
                }

                if (editorState.hiddenField.value === value) {
                    return;
                }

                editorState.hiddenField.value = value;
                editorState.hiddenField.dispatchEvent(new Event('input', { bubbles: true }));
            };

            const syncToLivewire = (data) => {
                const normalized = data || '';

                if (normalized === editorState.lastSetValue) {
                    return;
                }

                editorState.lastSetValue = normalized;
                syncHiddenField(normalized);
            };

            const destroyEditor = () => {
                if (editorInstance && editorInstance.status === 'ready') {
                    editorInstance.destroy();
                }

                editorInstance = null;
                editorState.hiddenField = null;
                editorState.lastSetValue = '';
            };

            const initializeEditor = () => {
                const container = document.querySelector('[data-post-description-editor]');
                if (!container) {
                    return;
                }

                const textarea = container.querySelector('#postDescription');
                if (!textarea || textarea.dataset.initialized) {
                    return;
                }

                if (typeof CKEDITOR === 'undefined') {
                    console.error('CKEditor 4 is not loaded.');
                    return;
                }

                const hiddenField = document.getElementById('postDescriptionData');

                textarea.dataset.initialized = 'true';

                editorState.hiddenField = hiddenField || null;
                editorState.lastSetValue = hiddenField?.value || '';

                editorInstance = CKEDITOR.replace(textarea.id, {
                    height: 360,
                    removePlugins: 'easyimage,cloudservices',
                    extraAllowedContent: '*(*){*}',
                });

                const emitChange = debounce(() => {
                    if (!editorInstance) {
                        return;
                    }

                    const data = editorInstance.getData();
                    syncToLivewire(data);
                });

                editorInstance.on('change', emitChange);
                editorInstance.on('instanceReady', () => {
                    emitChange();
                });

                if (!window.__postDescriptionBeforeUnloadBound) {
                    window.addEventListener('beforeunload', () => {
                        const instance = CKEDITOR.instances.postDescription;
                        if (instance && instance.status === 'ready') {
                            instance.destroy();
                        }
                    });
                    window.__postDescriptionBeforeUnloadBound = true;
                }
            };

            const handleSyncEvent = (content) => {
                const normalized = content || '';

                if (!editorInstance) {
                    return;
                }

                if (editorInstance.status !== 'ready') {
                    editorInstance.on('instanceReady', () => {
                        handleSyncEvent(normalized);
                    });
                    return;
                }

                const currentData = editorInstance.getData();
                if (currentData === normalized) {
                    editorState.lastSetValue = normalized;
                    syncHiddenField(normalized);
                    return;
                }

                editorState.lastSetValue = normalized;
                syncHiddenField(normalized);
                editorInstance.setData(normalized);
            };

            if (!window.__postDescriptionSyncListener) {
                Livewire.on('syncPostEditor', handleSyncEvent);
                window.__postDescriptionSyncListener = true;
            }

            Livewire.hook('element.removed', ({ el }) => {
                if (!el) {
                    return;
                }

                let textarea = null;
                if (el.id === 'postDescription') {
                    textarea = el;
                } else if (typeof el.querySelector === 'function') {
                    textarea = el.querySelector('#postDescription');
                }

                if (textarea && textarea.dataset.initialized) {
                    const instance = CKEDITOR.instances.postDescription;
                    if (instance) {
                        instance.destroy();
                    }
                    delete textarea.dataset.initialized;
                    destroyEditor();
                }
            });

            Livewire.hook('message.processed', () => {
                initializeEditor();
            });

            initializeEditor();
        });
    </script>
@endpushOnce
