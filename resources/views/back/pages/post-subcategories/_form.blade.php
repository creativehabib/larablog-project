<div class="card">
    <div class="card-body">
        <div class="form-group">
            <label for="post_category_id">মূল ক্যাটাগরি <span class="text-danger">*</span></label>
            <select name="post_category_id" id="post_category_id" class="form-control @error('post_category_id') is-invalid @enderror" required>
                <option value="">-- মূল ক্যাটাগরি নির্বাচন করুন --</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" @selected(old('post_category_id', $postSubcategory->post_category_id ?? '') == $category->id)>{{ $category->name }}</option>
                @endforeach
            </select>
            @error('post_category_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <label for="name">সাব-ক্যাটাগরি নাম <span class="text-danger">*</span></label>
            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $postSubcategory->name ?? '') }}" required>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <label for="slug">স্লাগ</label>
            <input type="text" class="form-control @error('slug') is-invalid @enderror" id="slug" name="slug" value="{{ old('slug', $postSubcategory->slug ?? '') }}" placeholder="subcategory-slug">
            <small class="form-text text-muted">শিরোনাম থেকে স্বয়ংক্রিয়ভাবে তৈরি হবে।</small>
            @error('slug')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group mb-0">
            <label for="description">বিবরণ</label>
            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3" placeholder="সাব-ক্যাটাগরির ব্যাখ্যা">{{ old('description', $postSubcategory->description ?? '') }}</textarea>
            @error('description')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>
