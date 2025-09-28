@extends('back.layout.pages-layout')
@section('pageTitle', 'নতুন সাব-ক্যাটাগরি')
@section('content')
    <header class="page-title-bar">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">নতুন সাব-ক্যাটাগরি</h1>
                <p class="text-muted mb-0">মূল ক্যাটাগরির অধীনে সাব-ক্যাটাগরি যুক্ত করুন।</p>
            </div>
            <a href="{{ route('admin.post-subcategories.index') }}" class="btn btn-outline-secondary">সব সাব-ক্যাটাগরি</a>
        </div>
    </header>

    <div class="page-section">
        <form action="{{ route('admin.post-subcategories.store') }}" method="POST">
            @csrf
            @php($postSubcategory = $postSubcategory ?? new \App\Models\PostSubcategory())
            @include('back.pages.post-subcategories._form', ['postSubcategory' => $postSubcategory, 'categories' => $categories])
            <div class="text-right mt-3">
                <button type="submit" class="btn btn-primary">সাব-ক্যাটাগরি সংরক্ষণ করুন</button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        const nameInput = document.getElementById('name');
        const slugInput = document.getElementById('slug');
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

        nameInput.addEventListener('input', () => {
            if (!slugTouched) {
                slugInput.value = generateSlug(nameInput.value);
            }
        });

        slugInput.addEventListener('input', () => {
            slugTouched = slugInput.value.trim().length > 0;
        });

        slugInput.addEventListener('blur', () => {
            slugInput.value = generateSlug(slugInput.value);
            slugTouched = slugInput.value.trim().length > 0;
        });
    </script>
@endpush
