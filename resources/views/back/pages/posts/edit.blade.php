@extends('back.layout.pages-layout')
@section('pageTitle', 'পোস্ট সম্পাদনা')
@section('content')
    <header class="page-title-bar">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">পোস্ট সম্পাদনা</h1>
                <p class="text-muted mb-0">পোস্টের তথ্য আপডেট করুন।</p>
            </div>
            <a href="{{ route('admin.posts.index') }}" class="btn btn-outline-secondary">সব পোস্ট</a>
        </div>
    </header>

    <div class="page-section">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <form action="{{ route('admin.posts.update', $post) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('back.pages.posts._form', ['post' => $post, 'categories' => $categories])
            <div class="d-flex justify-content-between">
                <a href="{{ route('admin.posts.index') }}" class="btn btn-outline-secondary">বাতিল</a>
                <button type="submit" class="btn btn-primary">পরিবর্তন সংরক্ষণ করুন</button>
            </div>
        </form>
        <form action="{{ route('admin.posts.destroy', $post) }}" method="POST" class="mt-3" onsubmit="return confirm('পোস্টটি সম্পূর্ণ মুছে ফেলতে চান?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-outline-danger">পোস্ট ডিলিট</button>
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

            Array.from(subcategorySelect.options).forEach(option => {
                if (!option.value) {
                    return;
                }

                const matches = !categoryId || option.dataset.parent === categoryId;
                option.hidden = !matches;

                if (!matches && option.selected) {
                    option.selected = false;
                }
            });
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
