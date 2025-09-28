<div class="card">
    <div class="card-body">
        <div class="form-group">
            <label for="name">ক্যাটাগরি নাম <span class="text-danger">*</span></label>
            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $postCategory->name ?? '') }}" required>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <label for="slug">স্লাগ</label>
            <input type="text" class="form-control @error('slug') is-invalid @enderror" id="slug" name="slug" value="{{ old('slug', $postCategory->slug ?? '') }}" placeholder="category-slug">
            <small class="form-text text-muted">শিরোনাম থেকে স্বয়ংক্রিয়ভাবে তৈরি হবে।</small>
            @error('slug')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group mb-0">
            <label for="description">বিবরণ</label>
            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3" placeholder="ক্যাটাগরির সংক্ষিপ্ত বিবরণ">{{ old('description', $postCategory->description ?? '') }}</textarea>
            @error('description')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>
