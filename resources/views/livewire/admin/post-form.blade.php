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
                                {{-- ID টি "content" করা হয়েছে --}}
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
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- থাম্বনেইল সেকশন (মিডিয়া লাইব্রেরি টেমপ্লেট অনুযায়ী) --}}
                    @if ($content_type === Post::CONTENT_TYPE_ARTICLE)
                        {{-- আপনার লাইভওয়্যার কম্পোনেন্টে $cover_image প্রপার্টি থাকতে হবে --}}
                        <div class="form-group border p-2" x-data="{ imageUrl: @entangle('cover_image').live }">
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

    {{-- মিডিয়া মডালটি এখানে কল করা হয়েছে --}}
    <x-media-modal />
</div>

{{--
==================================================================
    জাভাস্ক্রিপ্ট সমাধান (সংশোধিত এবং সম্পূর্ণ)
==================================================================
--}}
@push('scripts')
    @once
        {{-- CKEditor সোর্স --}}
        <script src="{{ asset('ckeditor/ckeditor.js') }}"></script>

        {{-- Livewire এবং CKEditor ইন্টিগ্রেশন (সংশোধিত) --}}
        <script>
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
            window.selectingThumbnail = false; // গ্লোবাল ভেরিয়েবল

            const destroyEditor = () => {
                if (CKEDITOR.instances.content) {
                    try {
                        CKEDITOR.instances.content.destroy(true);
                    } catch (e) { /* ইগনোর */ }
                }
                editorInstance = null;
            };

            const initializeEditor = () => {
                const container = document.querySelector('[data-post-description-editor]');
                if (!container) return; // 'ভিডিও' মোডে থাকলে কিছুই করবে না

                const textarea = container.querySelector('#content');
                if (!textarea || CKEDITOR.instances.content) return; // যদি এডিটর আগে থেকেই চালু থাকে

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
                    wordcount: { showCharCount: true, showWordCount: true },
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

                // সিলেকশন সেভ করা
                editorInstance.on('selectionChange', function () {
                    const selection = editorInstance.getSelection();
                    const ranges = selection ? selection.getRanges() : [];
                    if (ranges.length) {
                        savedSelection = ranges[0].clone();
                    }
                });

                //
                // ================== মূল সমাধান (বাগ #২) ==================
                //
                // মডাল খোলার জন্য কাস্টম কমান্ড
                editorInstance.addCommand('openMediaModal', {
                    exec: function () {
                        imageToReplace = null;
                        // এই লাইনটিই মূল সমাধান।
                        // এটি নিশ্চিত করে যে এডিটর থেকে ক্লিক করলে থাম্বনেইল সিলেক্ট হবে না।
                        window.selectingThumbnail = false;
                        window.dispatchEvent(new CustomEvent('open-media-modal'));
                    }
                });

                // 'ImageManager' বাটন যোগ করা
                editorInstance.ui.addButton('ImageManager', {
                    label: 'Image',
                    command: 'openMediaModal',
                    toolbar: 'insert',
                    icon: 'image'
                });

                // ছবি রিপ্লেস করার জন্য ডাবল ক্লিক
                editorInstance.on('doubleclick', function (evt) {
                    const element = evt.data.element;
                    if (element && element.is('img')) {
                        imageToReplace = element;
                        // ডাবল ক্লিক করলেও থাম্বনেইল সিলেক্ট হবে না
                        window.selectingThumbnail = false;
                        window.dispatchEvent(new CustomEvent('open-media-modal'));
                    }
                });
                // ================== সমাধান শেষ ==================
                //

                // Livewire-এর সাথে ডেটা সিঙ্ক করা
                editorInstance.on('change', function () {
                    const component = getComponent();
                    if (component) {
                        component.set('description', editorInstance.getData());
                    }
                });
            };

            // -----------------------------------------------------------------
            // ইভেন্ট লিসেনার (এটি আপনার পাঠানো নোটিফিকেশন যোগ করবে)
            // -----------------------------------------------------------------
            const handleImageSelection = (event) => {
                const component = getComponent();
                if (!component) {
                    console.error('POST-FORM: Livewire component not found.');
                    alert('Error: Component not found. Image cannot be inserted.');
                    return;
                }

                // ইভেন্ট থেকে 'detail' অবজেক্টটি বের করা
                let detail = event.detail;
                if (Array.isArray(detail) && detail.length > 0) {
                    detail = detail[0]; // যদি Livewire ইভেন্ট অ্যারে হিসেবে আসে
                }

                if (typeof detail === 'string') {
                    detail = { url: detail, path: detail }; // যদি শুধু URL স্ট্রিং আসে
                }

                if (typeof detail !== 'object' || detail === null) {
                    // console.warn('POST-FORM: Invalid event detail received:', event.detail);
                    // alert('Error: Invalid data received from media library.');
                    // যদি কোনো detail না থাকে (যেমন Livewire ইভেন্ট), তাহলেও থামুন
                    return;
                }

                const imageUrl = detail.url ?? detail.full_url ?? detail.path;
                const imagePath = detail.path ?? detail.url;

                if (!imageUrl) {
                    console.error('POST-FORM: No URL found in event detail', detail);
                    alert('Error: No URL found for the selected image.');
                    return;
                }

                // --- URL ব্যবহার করুন ---

                // ক) থাম্বনেইল সেট করুন
                if (window.selectingThumbnail) {
                    console.log('POST-FORM: Inserting as THUMBNAIL');
                    component.call('setCoverImageFromLibrary', imagePath, imageUrl);
                    window.selectingThumbnail = false; // ফ্ল্যাগ রিসেট করুন
                    // alert('Thumbnail updated successfully!'); // নোটিফিকেশন
                    return;
                }

                // খ) CKEditor-এ ছবি যুক্ত করুন
                console.log('POST-FORM: Inserting into CKEDITOR');
                if (imageToReplace) {
                    imageToReplace.setAttribute('src', imageUrl);
                } else if (editorInstance) {
                    editorInstance.focus();
                    if (savedSelection) editorInstance.getSelection().selectRanges([savedSelection]);
                    editorInstance.insertHtml('<img src="' + imageUrl + '" alt="" />');
                }

                // গ) এডিটরের ডেটা লাইভওয়্যারের সাথে সিঙ্ক করুন
                if (editorInstance) {
                    component.set('description', editorInstance.getData());
                }

                // alert('Image inserted into description successfully!'); // নোটিফিকেশন

                // ঘ) অবস্থা রিসেট করুন
                imageToReplace = null;
                savedSelection = null;
            };

            // --- ব্রাউজার ইভেন্ট লিসেনার ---
            // এটি 'image-selected-browser' নামের নতুন ইভেন্ট শুনবে
            window.removeEventListener('image-selected-browser', handleImageSelection);
            window.addEventListener('image-selected-browser', handleImageSelection);


            // --- Livewire লাইফসাইকেল হুক ---
            Livewire.hook('message.processed', () => {
                destroyEditor();
                initializeEditor();
            });

            Livewire.hook('element.removed', () => {
                destroyEditor();
            });

            // পৃষ্ঠা লোড হলে প্রথমবার চালু করুন
            initializeEditor();
            });
        </script>
    @endonce
@endpush
