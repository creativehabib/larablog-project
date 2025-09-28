@extends('back.layout.pages-layout')
@section('pageTitle', 'সাব-ক্যাটাগরি সম্পাদনা')
@section('content')
    <header class="page-title-bar">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">সাব-ক্যাটাগরি সম্পাদনা</h1>
                <p class="text-muted mb-0">সাব-ক্যাটাগরির তথ্য আপডেট করুন।</p>
            </div>
            <a href="{{ route('admin.post-subcategories.index') }}" class="btn btn-outline-secondary">সব সাব-ক্যাটাগরি</a>
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

        <form action="{{ route('admin.post-subcategories.update', $postSubcategory) }}" method="POST">
            @csrf
            @method('PUT')
            @include('back.pages.post-subcategories._form', ['postSubcategory' => $postSubcategory, 'categories' => $categories])
            <div class="d-flex justify-content-between mt-3">
                <a href="{{ route('admin.post-subcategories.index') }}" class="btn btn-outline-secondary">বাতিল</a>
                <button type="submit" class="btn btn-primary">পরিবর্তন সংরক্ষণ করুন</button>
            </div>
        </form>
        <form action="{{ route('admin.post-subcategories.destroy', $postSubcategory) }}" method="POST" class="mt-3" onsubmit="return confirm('সাব-ক্যাটাগরি ডিলিট করবেন?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-outline-danger">সাব-ক্যাটাগরি ডিলিট</button>
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
