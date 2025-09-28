@extends('back.layout.pages-layout')
@section('pageTitle', $pageTitle ?? 'Edit Category')
@section('content')
    <header class="page-title-bar">
        <h1 class="page-title">Edit Category</h1>
        <a href="{{ route('admin.categories.index') }}" class="btn btn-link">&larr; Back to categories</a>
    </header>

    <div class="page-section">
        <div class="card card-fluid">
            <div class="card-body">
                <form action="{{ route('admin.categories.update', $category) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label for="name">Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $category->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="slug">Slug</label>
                        <input type="text" name="slug" id="slug" class="form-control @error('slug') is-invalid @enderror" value="{{ old('slug', $category->slug) }}">
                        @error('slug')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea name="description" id="description" rows="4" class="form-control @error('description') is-invalid @enderror">{{ old('description', $category->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="image">Image</label>
                        <div class="mb-2">
                            @if ($category->image_path)
                                <img src="{{ asset('storage/' . $category->image_path) }}" alt="{{ $category->name }}" class="img-thumbnail" style="max-width: 120px;">
                            @else
                                <span class="text-muted">No image uploaded</span>
                            @endif
                        </div>
                        <input type="file" name="image" id="image" class="form-control-file @error('image') is-invalid @enderror" accept="image/*">
                        @error('image')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Uploading a new image will replace the previous one.</small>
                    </div>

                    <div class="form-group mb-0">
                        <button type="submit" class="btn btn-primary">Update Category</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
