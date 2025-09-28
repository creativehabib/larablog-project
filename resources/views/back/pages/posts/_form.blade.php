@php
    use Illuminate\Support\Facades\Storage;

    $selectedCategory = old('post_category_id', $post->post_category_id ?? '');
    $selectedSubcategory = old('post_subcategory_id', $post->post_subcategory_id ?? '');
@endphp
<div class="card mb-4">
    <div class="card-body">
        <div class="form-group">
            <label for="title">পোস্ট শিরোনাম <span class="text-danger">*</span></label>
            <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $post->title ?? '') }}" required>
            @error('title')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <label for="slug">স্লাগ</label>
            <input type="text" name="slug" id="slug" class="form-control @error('slug') is-invalid @enderror" value="{{ old('slug', $post->slug ?? '') }}" placeholder="automatic-slug">
            <small class="form-text text-muted">শিরোনাম অনুযায়ী স্বয়ংক্রিয়ভাবে তৈরি হবে, তবে চাইলে পরিবর্তন করতে পারেন।</small>
            @error('slug')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="post_category_id">পোস্ট ক্যাটাগরি <span class="text-danger">*</span></label>
                <select name="post_category_id" id="post_category_id" class="form-control @error('post_category_id') is-invalid @enderror" required>
                    <option value="">-- ক্যাটাগরি নির্বাচন করুন --</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" @selected($category->id == $selectedCategory)>{{ $category->name }}</option>
                    @endforeach
                </select>
                @error('post_category_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group col-md-6">
                <label for="post_subcategory_id">সাব-ক্যাটাগরি</label>
                <select name="post_subcategory_id" id="post_subcategory_id" class="form-control @error('post_subcategory_id') is-invalid @enderror">
                    <option value="">-- সাব-ক্যাটাগরি নির্বাচন করুন --</option>
                    @foreach($categories as $category)
                        @foreach($category->subcategories as $subcategory)
                            <option value="{{ $subcategory->id }}" data-parent="{{ $category->id }}" @selected($subcategory->id == $selectedSubcategory)>{{ $subcategory->name }}</option>
                        @endforeach
                    @endforeach
                </select>
                @error('post_subcategory_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="form-group">
            <label for="excerpt">সংক্ষিপ্ত বিবরণ</label>
            <textarea name="excerpt" id="excerpt" rows="3" class="form-control @error('excerpt') is-invalid @enderror" placeholder="পোস্টের ছোট্ট সারসংক্ষেপ লিখুন">{{ old('excerpt', $post->excerpt ?? '') }}</textarea>
            @error('excerpt')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <label for="body">পোস্ট বর্ণনা <span class="text-danger">*</span></label>
            <textarea name="body" id="body" rows="10" class="form-control @error('body') is-invalid @enderror" required>{{ old('body', $post->body ?? '') }}</textarea>
            @error('body')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header">মিডিয়া</div>
    <div class="card-body">
        <div class="form-group">
            <label for="thumbnail">ফিচারড ইমেজ</label>
            <input type="file" name="thumbnail" id="thumbnail" class="form-control-file @error('thumbnail') is-invalid @enderror" accept="image/*">
            @error('thumbnail')
                <div class="text-danger small">{{ $message }}</div>
            @enderror
            @if(!empty($post->thumbnail_path))
                <div class="mt-3">
                    <p class="mb-1">বর্তমান ইমেজ:</p>
                    <img src="{{ Storage::disk('public')->exists($post->thumbnail_path ?? '') ? Storage::url($post->thumbnail_path) : asset($post->thumbnail_path) }}" alt="Thumbnail" class="img-thumbnail" style="max-height: 150px;">
                </div>
            @endif
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header">অপশন</div>
    <div class="card-body">
        <div class="custom-control custom-switch mb-2">
            <input type="checkbox" class="custom-control-input" id="is_featured" name="is_featured" value="1" @checked(old('is_featured', $post->is_featured ?? false))>
            <label class="custom-control-label" for="is_featured">ফিচারড পোস্ট হিসেবে চিহ্নিত করুন</label>
        </div>
        <div class="custom-control custom-switch mb-2">
            <input type="checkbox" class="custom-control-input" id="allow_comments" name="allow_comments" value="1" @checked(old('allow_comments', ($post->allow_comments ?? true)))>
            <label class="custom-control-label" for="allow_comments">মন্তব্য করার অনুমতি দিন</label>
        </div>
        <div class="custom-control custom-switch">
            <input type="checkbox" class="custom-control-input" id="is_indexable" name="is_indexable" value="1" @checked(old('is_indexable', ($post->is_indexable ?? true)))>
            <label class="custom-control-label" for="is_indexable">সার্চ ইঞ্জিনে ইনডেক্স করুন</label>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header">মেটা তথ্য</div>
    <div class="card-body">
        <div class="form-group">
            <label for="meta_title">মেটা টাইটেল</label>
            <input type="text" name="meta_title" id="meta_title" class="form-control @error('meta_title') is-invalid @enderror" value="{{ old('meta_title', $post->meta_title ?? '') }}" placeholder="SEO টাইটেল">
            @error('meta_title')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <label for="meta_description">মেটা বর্ণনা</label>
            <textarea name="meta_description" id="meta_description" rows="3" class="form-control @error('meta_description') is-invalid @enderror" placeholder="SEO বর্ণনা">{{ old('meta_description', $post->meta_description ?? '') }}</textarea>
            @error('meta_description')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group mb-0">
            <label for="meta_keywords">মেটা কীওয়ার্ড</label>
            <input type="text" name="meta_keywords" id="meta_keywords" class="form-control @error('meta_keywords') is-invalid @enderror" value="{{ old('meta_keywords', $post->meta_keywords ?? '') }}" placeholder="কমা (,) দিয়ে আলাদা করুন">
            @error('meta_keywords')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>
