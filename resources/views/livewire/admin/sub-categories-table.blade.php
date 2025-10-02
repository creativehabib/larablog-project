@php use Illuminate\Support\Str; @endphp
<div>
    <header class="page-title-bar">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">Sub Categories</h1>
                <p class="text-muted">Organise additional layers for your categories.</p>
            </div>
            <a href="{{ route('admin.subcategories.create') }}" class="btn btn-primary">Create Sub Category</a>
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
                            placeholder="Search sub categories..."
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
                                <th scope="col">Parent Category</th>
                                <th scope="col">Slug</th>
                                <th scope="col">Updated At</th>
                                <th scope="col" class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($subCategories as $subcategory)
                                <tr wire:key="subcategory-{{ $subcategory->id }}">
                                    <td>{{ $loop->iteration + ($subCategories->currentPage() - 1) * $subCategories->perPage() }}</td>
                                    <td>
                                        @if ($subcategory->image_path)
                                            <img src="{{ asset('storage/' . $subcategory->image_path) }}" alt="{{ $subcategory->name }}" class="img-thumbnail" style="max-width: 60px;">
                                        @else
                                            <span class="text-muted">No image</span>
                                        @endif
                                    </td>
                                    <td>
                                        <strong>{{ $subcategory->name }}</strong>
                                        @if ($subcategory->description)
                                            <div class="text-muted small">{{ Str::limit($subcategory->description, 80) }}</div>
                                        @endif
                                    </td>
                                    <td>{{ $subcategory->category?->name ?? '—' }}</td>
                                    <td><code>{{ $subcategory->slug }}</code></td>
                                    <td>{{ $subcategory->updated_at->format('d M, Y') }}</td>
                                    <td class="text-right">
                                        @can('category.edit')
                                            <a href="{{ route('admin.subcategories.edit', $subcategory) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                                        @endcan

                                        @can('category.delete')
                                            <button
                                                type="button"
                                                class="btn btn-sm btn-outline-danger"
                                                wire:click="deleteSubCategory({{ $subcategory->id }})"
                                                onclick="confirm('Are you sure you want to delete this sub category?') || event.stopImmediatePropagation()"
                                            >Delete</button>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted">No sub categories found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{ $subCategories->links() }}
            </div>
        </div>
    </div>
</div>
