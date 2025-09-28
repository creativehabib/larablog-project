@extends('back.layout.pages-layout')
@section('pageTitle', 'নতুন পোস্ট যুক্ত করুন')
@section('content')
    <header class="page-title-bar">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">নতুন পোস্ট</h1>
                <p class="text-muted mb-0">সব তথ্য পূরণ করে একটি নতুন পোস্ট তৈরি করুন।</p>
            </div>
            <a href="{{ route('admin.posts.index') }}" class="btn btn-outline-secondary">সব পোস্ট দেখুন</a>
        </div>
    </header>

    <div class="page-section">
        <form action="{{ route('admin.posts.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @php($post = $post ?? new \App\Models\Post())
            @include('back.pages.posts._form', ['post' => $post, 'categories' => $categories])
            <div class="text-right">
                <button type="submit" class="btn btn-primary">পোস্ট সংরক্ষণ করুন</button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script>
    <script>
        CKEDITOR.replace('body', {
            height: 320,
            removeButtons: 'Save,NewPage,Preview,Print,Templates'
        });

        const titleInput = document.getElementById('title');
        const slugInput = document.getElementById('slug');
        const categorySelect = document.getElementById('post_category_id');
        const subcategorySelect = document.getElementById('post_subcategory_id');
        const initialSlug = slugInput.value.trim();
        let slugTouched = initialSlug.length > 0;

        function generateSlug(value) {
            return value
                .toString()
                .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
                .toLowerCase()
                .trim()
                .replace(/[^a-z0-9\u0980-\u09FF\s-]/g, '')
                .replace(/[\s_-]+/g, '-')
                .replace(/^-+|-+$/g, '');
        }

        function filterSubcategories() {
            const categoryId = categorySelect.value;
            let hasVisible = false;

            Array.from(subcategorySelect.options).forEach(option => {
                if (!option.value) {
                    return;
                }

                const matches = !categoryId || option.dataset.parent === categoryId;
                option.hidden = !matches;

                if (!matches && option.selected) {
                    option.selected = false;
                }

                if (matches) {
                    hasVisible = true;
                }
            });

            if (!hasVisible) {
                subcategorySelect.value = '';
            }
        }

        titleInput.addEventListener('input', () => {
            if (!slugTouched) {
                slugInput.value = generateSlug(titleInput.value);
            }
        });

        slugInput.addEventListener('input', () => {
            slugTouched = slugInput.value.trim().length > 0;
        });

        slugInput.addEventListener('blur', () => {
            slugInput.value = generateSlug(slugInput.value);
            slugTouched = slugInput.value.trim().length > 0;
        });

        categorySelect.addEventListener('change', filterSubcategories);
        filterSubcategories();
    </script>
@endpush
