@php use App\Models\Post; @endphp
<div>
    <form wire:submit.prevent="save" class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <div class="form-group">
                        <label for="postTitle">Post Title <span class="text-danger">*</span></label>
                        <input type="text" id="postTitle" class="form-control @error('title') is-invalid @enderror" wire:model.defer="title" placeholder="Enter post title">
                        @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label class="d-flex justify-content-between align-items-center" for="postSlug">
                            <span>Permalink</span>
                            @if (! $autoGenerateSlug)
                                <button type="button" class="btn btn-sm btn-link p-0" wire:click="resetSlugToAuto" wire:loading.attr="disabled">
                                    Reset to auto
                                </button>
                            @endif
                        </label>
                        <div class="border rounded px-3 py-2 bg-light">
                            <p class="small mb-1 text-break">
                                <span class="text-muted">{{ url('news') }}/</span>
                                <span class="text-body font-weight-semibold">{{ $slug ?: 'your-slug' }}</span>
                            </p>
                            @if (! $isEditingSlug)
                                <button type="button" class="btn btn-sm btn-outline-secondary mt-2" wire:click="startSlugEditing" wire:loading.attr="disabled">
                                    Edit permalink
                                </button>
                            @endif
                        </div>
                        @if ($isEditingSlug)
                            <div class="mt-2">
                                <input type="text" id="postSlug" class="form-control @error('slug') is-invalid @enderror" wire:model.defer="slug" placeholder="custom-slug">
                                <div class="d-flex flex-wrap gap-2 mt-2">
                                    <button type="button" class="btn btn-sm btn-primary" wire:click="saveSlugEdit" wire:loading.attr="disabled">Save</button>
                                    <button type="button" class="btn btn-sm btn-link text-danger" wire:click="cancelSlugEditing" wire:loading.attr="disabled">Cancel</button>
                                </div>
                            </div>
                        @endif
                        @error('slug')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    {{--Post Description--}}
                    @if ($content_type === Post::CONTENT_TYPE_VIDEO)
                        <div class="card card-body border mb-4">
                            <h5 class="card-title mb-3">Video Details</h5>
                            {{-- আপনার ভিডিও ডিটেইলস ফর্ম এখানে থাকবে --}}
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

                            {{-- এমবেড বা আপলোড কোড --}}
                            @if ($video_source === Post::VIDEO_SOURCE_EMBED)
                                <div class="form-group">
                                    <label for="videoEmbedCode">Embed Code</label>
                                    <textarea id="videoEmbedCode" rows="4" class="form-control @error('video_embed_code') is-invalid @enderror" wire:model.live.debounce.500ms="video_embed_code" placeholder="Paste the iframe embed code from YouTube, Vimeo, etc."></textarea>
                                    @error('video_embed_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            @else
                                <div class="form-group">
                                    <label for="videoUpload">Video File @if (! $existingVideoPath)<span class="text-danger">*</span>@endif</label>
                                    <input type="file" id="videoUpload" class="form-control-file @error('video_upload') is-invalid @enderror" wire:model="video_upload" accept="video/mp4,video/quicktime,video/webm">
                                    @error('video_upload')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
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
                            <label for="content">Post Description <span class="text-danger">*</span></label>

                            {{-- জাভাস্ক্রিপ্ট থেকে wire:model সেট করা হবে --}}
                            <div wire:ignore data-post-description-editor>
                                {{-- ID টি "content" করা হয়েছে --}}
                                <textarea id="content" class="form-control">{!! $description !!}</textarea>
                            </div>

                            @error('description')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    @endif
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

        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <div class="form-group d-flex justify-content-end">
                        <a href="{{ route('admin.posts.index') }}" class="btn btn-link">Cancel</a>
                        <button type="submit" class="btn btn-primary ml-2">
                            {{ $isEditing ? 'Update Post' : 'Create Post' }}
                        </button>
                    </div>
                    <div class="form-group">
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

                    {{--sub category--}}
                    <div class="form-group">
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

                    {{--Post Type--}}
                    <div class="form-group">
                        <label for="contentType">Post Type <span class="text-danger">*</span></label>
                        <select id="contentType" class="form-control @error('content_type') is-invalid @enderror" wire:model.live="content_type">
                            <option value="{{ Post::CONTENT_TYPE_ARTICLE }}">Article</option>
                            <option value="{{ Post::CONTENT_TYPE_VIDEO }}">Video</option>
                        </select>
                        @error('content_type')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- থাম্বনেইল সেকশন (মিডিয়া লাইব্রেরি টেমপ্লেট অনুযায়ী) --}}
                    @if ($content_type === Post::CONTENT_TYPE_ARTICLE)
                        {{-- আপনার লাইভওয়্যার কম্পোনেন্টে $cover_image প্রপার্টি থাকতে হবে --}}
                        <div class="form-group border p-2" x-data="{ imageUrl: @entangle('cover_image') }">
                            <label class="d-block mb-1">Post Thumbnail</label>

                            {{-- এই ব্লকটি আপনার JS-এর 'window.selectingThumbnail = true' লজিকের সাথে মিলবে --}}
                            <div x-show="!imageUrl" @click="window.selectingThumbnail = true; window.dispatchEvent(new CustomEvent('open-media-modal'))"
                                 class="border rounded p-4 text-center"
                                 style="border-style: dashed; cursor: pointer;">
                                <p class="text-muted mb-0">Select Thumbnail</p>
                            </div>

                            <div x-show="imageUrl" class="mt-2">
                                <p class="text-muted small mb-2">Current thumbnail:</p>
                                <img :src="imageUrl" class="img-thumbnail" style="max-height: 180px;" />

                                {{-- এই বাটনটি আপনার JS-এর '$wire.clearCoverImage()' লজিকের সাথে মিলবে --}}
                                <button type="button" @click="imageUrl = null; $wire.clearCoverImage()" class="btn btn-sm btn-outline-danger mt-2">
                                    Remove
                                </button>
                            </div>
                        </div>
                    @endif

                    {{--Post Options--}}
                    <div class="form-group">
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
            </div>
        </div>

    </form>

    {{-- মিডিয়া মডালটি এখানে কল করা হয়েছে --}}
    <x-media-modal />
</div>

{{-- জাভাস্ক্রিপ্ট (মিডিয়া লাইব্রেরি এবং CKEditor রিলোড লজিকসহ) --}}
@pushOnce('scripts')
    <script src="{{ asset('ckeditor/ckeditor.js') }}"></script>
    <script>
        {{-- আমরা 'livewire:init' ব্যবহার করছি --}}
        document.addEventListener('livewire:init', () => {

            const componentId = '{{ $this->getId() }}';
            const getComponent = () => {
                if (typeof Livewire === 'undefined' || typeof Livewire.find !== 'function') {
                    return null;
                }

                return Livewire.find(componentId);
            };

            let editorInstance = null;
            let imageToReplace = null;
            let savedSelection = null;
            window.selectingThumbnail = false;

            const destroyEditor = () => {
                // আমরা 'content' আইডি দিয়ে ইনস্ট্যান্স খুঁজবো
                if (CKEDITOR.instances.content) {
                    try {
                        CKEDITOR.instances.content.destroy(true);
                    } catch (e) {
                        // console.warn('Error destroying CKEditor:', e);
                    }
                }
                editorInstance = null;
            };

            const initializeEditor = () => {
                const container = document.querySelector('[data-post-description-editor]');
                if (!container) {
                    // কন্টেইনার নেই (যেমন 'ভিডিও' মোড), তাই প্রস্থান করুন
                    return;
                }

                // আমরা 'content' আইডি দিয়ে টেক্সটএরিয়া খুঁজবো
                const textarea = container.querySelector('#content');

                // যদি টেক্সটএরিয়া না থাকে বা এডিটর আগে থেকেই চালু থাকে, তবে প্রস্থান করুন
                if (!textarea || CKEDITOR.instances.content) {
                    return;
                }

                if (typeof CKEDITOR === 'undefined') {
                    console.error('CKEditor 4 is not loaded.');
                    return;
                }

                editorInstance = CKEDITOR.replace(textarea.id, {
                    mathJaxLib: '//cdnjs.cloudflare.com/ajax/libs/mathjax/2.7.4/MathJax.js?config=TeX-AMS_HTML',
                    height: 200,
                    uiColor: '',
                    removePlugins: 'easyimage,cloudservices',
                    extraPlugins: 'mathjax,tableresize,wordcount,notification',
                    wordcount: {
                        showCharCount: true,
                        showWordCount: true
                    },
                    toolbar: [
                        {items: ['Undo', 'Redo']},
                        { name: 'styles', items: ['Styles', 'Format', 'Font', 'FontSize'] },
                        { name: 'document', items: ['Source', '-', 'Preview'] },
                        { name: 'clipboard', items: ['Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', ] },
                        { name: 'editing', items: ['Find', 'Replace', '-', 'SelectAll', '-', 'RemoveFormat','CopyFormatting'] },
                        { name: 'basicstyles', items: ['Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript'] },
                        { name: 'paragraph', items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock','BidiLtr', 'BidiRtl'] },
                        { name: 'links', items: ['Link', 'Unlink'] },
                        { name: 'insert', items: ['Image', 'Table', 'HorizontalRule', 'SpecialChar', 'Mathjax', '-', 'ImageManager', 'Iframe','Smiley','direction'] },
                        { name: 'colors', items: ['TextColor', 'BGColor', 'ShowBlocks'] },
                        { name: 'tools', items: ['Maximize'] }
                    ],
                    allowedContent: true,
                    extraAllowedContent: '*(*){*}',

                });

                // Template থেকে মিডিয়া লাইব্রেরির লজিক
                editorInstance.on('selectionChange', function () {
                    const selection = editorInstance.getSelection();
                    const ranges = selection ? selection.getRanges() : [];
                    if (ranges.length) {
                        savedSelection = ranges[0].clone();
                    }
                });

                editorInstance.addCommand('openMediaModal', {
                    exec: function () {
                        imageToReplace = null;
                        window.selectingThumbnail = false; // নিশ্চিত করুন থাম্বনেইল সিলেক্ট হচ্ছে না
                        window.dispatchEvent(new CustomEvent('open-media-modal'));
                    }
                });

                editorInstance.ui.addButton('ImageManager', {
                    label: 'Image',
                    command: 'openMediaModal',
                    toolbar: 'insert',
                    icon: 'image'
                });

                editorInstance.on('doubleclick', function (evt) {
                    const element = evt.data.element;
                    if (element && element.is('img')) {
                        imageToReplace = element;
                        window.selectingThumbnail = false; // নিশ্চিত করুন থাম্বনেইল সিলেক্ট হচ্ছে না
                        window.dispatchEvent(new CustomEvent('open-media-modal'));
                    }
                });

                // ডেটা সিঙ্ক করার জন্য লিসেনার
                editorInstance.on('change', function () {
                    // Debounce ব্যবহার করা ভালো, কিন্তু দ্রুত আপডেটের জন্য সরাসরি Livewire প্রোপার্টি সেট করা হচ্ছে
                    const component = getComponent();
                    if (component) {
                        component.set('description', editorInstance.getData());
                    }
                });
            };

            // Template থেকে 'image-selected' ইভেন্ট হ্যান্ডলার
            const extractSelectionPayload = (payload) => {
                if (!payload) {
                    return {};
                }

                if (payload instanceof CustomEvent) {
                    return extractSelectionPayload(payload.detail);
                }

                if (payload.detail !== undefined && payload !== payload.detail) {
                    return extractSelectionPayload(payload.detail);
                }

                if (payload.params !== undefined) {
                    return extractSelectionPayload(payload.params);
                }

                if (Array.isArray(payload)) {
                    if (payload.length === 0) {
                        return {};
                    }

                    return payload.reduce((accumulator, item) => ({
                        ...accumulator,
                        ...extractSelectionPayload(item),
                    }), {});
                }

                if (typeof payload === 'object') {
                    return payload;
                }

                return {};
            };

            const handleImageSelection = (payload) => {
                const detail = extractSelectionPayload(payload);

                if (!detail || Object.keys(detail).length === 0) {
                    return;
                }

                if (window.selectingThumbnail) {
                    // থাম্বনেইল সেট করুন (HTML-এ @entangle('cover_image') ব্যবহার করা হয়েছে)
                    const component = getComponent();
                    if (component) {
                        component.call('setCoverImageFromLibrary', detail.path ?? null, detail.url ?? null);
                    }
                    window.selectingThumbnail = false;
                    return;
                }

                const url = detail.url || detail.full_url || detail.fullUrl || detail.path;
                if (!url) {
                    return;
                }

                if (imageToReplace) {
                    imageToReplace.setAttribute('src', url);
                } else if (editorInstance) {
                    editorInstance.focus();
                    if (savedSelection) {
                        editorInstance.getSelection().selectRanges([savedSelection]);
                    }
                    editorInstance.insertHtml('<img src="' + url + '" alt="" />');
                }

                if (editorInstance) {
                    const component = getComponent();
                    if (component) {
                        component.set('description', editorInstance.getData());
                    }
                }

                imageToReplace = null;
                savedSelection = null;
            };

            const browserImageSelectedHandler = (event) => {
                handleImageSelection(event?.detail ?? event);
            };

            window.removeEventListener('image-selected', browserImageSelectedHandler);
            window.addEventListener('image-selected', browserImageSelectedHandler);

            document.removeEventListener('image-selected', browserImageSelectedHandler);
            document.addEventListener('image-selected', browserImageSelectedHandler);

            if (typeof Livewire !== 'undefined' && typeof Livewire.on === 'function') {
                Livewire.on('image-selected', (...args) => {
                    if (!args || args.length === 0) {
                        return;
                    }

                    if (args.length === 1) {
                        handleImageSelection(args[0]);
                        return;
                    }

                    handleImageSelection(args);
                });
            }


            // লাইভওয়্যার যখন DOM আপডেট করে (যেমন Post Type পরিবর্তন করলে)
            Livewire.hook('message.processed', () => {
                // পুরানো এডিটর থাকলে তা ধ্বংস করুন
                destroyEditor();
                // নতুন এডিটর চালু করার চেষ্টা করুন (যদি 'Article' মোড চালু হয়)
                initializeEditor();
            });

            // লাইভওয়্যার যখন DOM থেকে এলিমেন্ট সরায়
            Livewire.hook('element.removed', () => {
                destroyEditor();
            });

            // পৃষ্ঠা লোড হলে প্রথমবার চালু করুন
            initializeEditor();
        });
    </script>
@endpushOnce
