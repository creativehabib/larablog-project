@php use Illuminate\Support\Str; @endphp
<div>
    <header class="page-title-bar">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">Categories</h1>
                <p class="text-muted">Manage blog categories and their basic details.</p>
            </div>
            <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">Create Category</a>
        </div>
    </header>

    <div class="page-section">
        @if (session()->has('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card card-fluid">
            <div class="card-body">
                <div class="form-row align-items-center mb-3">
                    <div class="col-sm-6 my-1">
                        <input
                            type="text"
                            class="form-control"
                            placeholder="Search categories..."
                            wire:model.live.debounce.300ms="search"
                        >
                    </div>
                    @if ($search !== '')
                        <div class="col-auto my-1">
                            <button type="button" class="btn btn-outline-secondary" wire:click="$set('search', '')">Clear</button>
                        </div>
                    @endif
                </div>

                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Image</th>
                                <th scope="col">Name</th>
                                <th scope="col">Slug</th>
                                <th scope="col">Sub Categories</th>
                                <th scope="col">Updated At</th>
                                <th scope="col" class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($categories as $category)
                                <tr wire:key="category-{{ $category->id }}">
                                    <td>{{ $loop->iteration + ($categories->currentPage() - 1) * $categories->perPage() }}</td>
                                    <td>
                                        @if ($category->image_path)
                                            <img src="{{ asset('storage/' . $category->image_path) }}" alt="{{ $category->name }}" class="img-thumbnail" style="max-width: 60px;">
                                        @else
                                            <span class="text-muted">No image</span>
                                        @endif
                                    </td>
                                    <td>
                                        <strong>{{ $category->name }}</strong>
                                        @if ($category->description)
                                            <div class="text-muted small">{{ Str::limit($category->description, 80) }}</div>
                                        @endif
                                    </td>
                                    <td><code>{{ $category->slug }}</code></td>
                                    <td>{{ $category->sub_categories_count }}</td>
                                    <td>{{ $category->updated_at->format('d M, Y') }}</td>
                                    <td class="text-right">
                                        <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-outline-danger"
                                            wire:click="deleteCategory({{ $category->id }})"
                                            onclick="confirm('Are you sure you want to delete this category?') || event.stopImmediatePropagation()"
                                        >Delete</button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted">No categories found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{ $categories->links() }}
            </div>
        </div>
    </div>
</div>
