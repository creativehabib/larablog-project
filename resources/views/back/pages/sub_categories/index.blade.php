@php use Illuminate\Support\Str; @endphp
@extends('back.layout.pages-layout')
@section('pageTitle', $pageTitle ?? 'Sub Categories')
@section('content')
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
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card card-fluid">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.subcategories.index') }}" class="mb-3">
                    <div class="form-row align-items-center">
                        <div class="col-sm-6 my-1">
                            <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                                placeholder="Search sub categories...">
                        </div>
                        <div class="col-auto my-1">
                            <button type="submit" class="btn btn-primary">Search</button>
                        </div>
                        @if (request('search'))
                            <div class="col-auto my-1">
                                <a href="{{ route('admin.subcategories.index') }}" class="btn btn-outline-secondary">Clear</a>
                            </div>
                        @endif
                    </div>
                </form>
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
                                <tr>
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
                                        <a href="{{ route('admin.subcategories.edit', $subcategory) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                                        <form action="{{ route('admin.subcategories.destroy', $subcategory) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this sub category?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                        </form>
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
@endsection
