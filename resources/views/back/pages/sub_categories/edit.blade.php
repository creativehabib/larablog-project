@extends('back.layout.pages-layout')
@section('pageTitle', $pageTitle ?? 'Edit Sub Category')
@section('content')
    <header class="page-title-bar">
        <h1 class="page-title">Edit Sub Category</h1>
        <a href="{{ route('admin.subcategories.index') }}" class="btn btn-link">&larr; Back to sub categories</a>
    </header>

    <div class="page-section">
        <div class="card card-fluid">
            <div class="card-body">
                <form action="{{ route('admin.subcategories.update', $subcategory) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label for="category_id">Parent Category <span class="text-danger">*</span></label>
                        <select name="category_id" id="category_id" class="form-control @error('category_id') is-invalid @enderror" required>
                            <option value="">Select category</option>
                            @foreach ($categories as $id => $name)
                                <option value="{{ $id }}" @selected(old('category_id', $subcategory->category_id) == $id)>{{ $name }}</option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="name">Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $subcategory->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="slug">Slug</label>
                        <input type="text" name="slug" id="slug" class="form-control @error('slug') is-invalid @enderror" value="{{ old('slug', $subcategory->slug) }}">
                        @error('slug')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea name="description" id="description" rows="4" class="form-control @error('description') is-invalid @enderror">{{ old('description', $subcategory->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="image">Image</label>
                        <div class="mb-2">
                            @if ($subcategory->image_path)
                                <img src="{{ asset('storage/' . $subcategory->image_path) }}" alt="{{ $subcategory->name }}" class="img-thumbnail" style="max-width: 120px;">
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
                        <button type="submit" class="btn btn-primary">Update Sub Category</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
