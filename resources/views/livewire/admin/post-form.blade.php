<div>
    <form wire:submit.prevent="save" class="card card-fluid">
        <div class="card-body">
            <div class="form-row">
                <div class="form-group col-md-8">
                    <label for="postTitle">Post Title <span class="text-danger">*</span></label>
                    <input type="text" id="postTitle" class="form-control @error('title') is-invalid @enderror" wire:model.defer="title" placeholder="Enter post title">
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group col-md-4">
                    <label for="postSlug">Slug</label>
                    <input type="text" id="postSlug" class="form-control @error('slug') is-invalid @enderror" wire:model.defer="slug" placeholder="Auto generated from title">
                    @error('slug')
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

            <div class="form-row">
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
                <div class="form-group col-md-6">
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
