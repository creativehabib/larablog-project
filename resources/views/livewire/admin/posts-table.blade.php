@php use Illuminate\Support\Str; @endphp
<div>
    <div class="card card-fluid">
        <div class="card-body">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3 gap-3">
                <div class="w-100 w-md-50">
                    <input type="search" wire:model.debounce.500ms="search" class="form-control" placeholder="Search posts by title, slug or meta title">
                </div>
                <div class="text-muted small">
                    Showing {{ $posts->firstItem() ?? 0 }} - {{ $posts->lastItem() ?? 0 }} of {{ $posts->total() }} posts
                </div>
            </div>

            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Featured</th>
                            <th>Comments</th>
                            <th>Indexing</th>
                            <th>Updated</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($posts as $post)
                            <tr wire:key="post-{{ $post->id }}">
                                <td>{{ $loop->iteration + ($posts->currentPage() - 1) * $posts->perPage() }}</td>
                                <td>
                                    <div class="font-weight-bold">{{ $post->title }}</div>
                                    <div class="text-muted small"><code>{{ $post->slug }}</code></div>
                                    @if ($post->meta_title)
                                        <div class="text-muted small">Meta: {{ Str::limit($post->meta_title, 45) }}</div>
                                    @endif
                                </td>
                                <td>
                                    <div>{{ $post->category?->name ?? '—' }}</div>
                                    <div class="text-muted small">
                                        {{ $post->subCategory?->name ?? 'No sub category' }}
                                    </div>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm {{ $post->is_featured ? 'btn-success' : 'btn-outline-secondary' }}" wire:click="toggleFeatured({{ $post->id }})">
                                        {{ $post->is_featured ? 'Featured' : 'Not Featured' }}
                                    </button>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm {{ $post->allow_comments ? 'btn-success' : 'btn-outline-secondary' }}" wire:click="toggleComments({{ $post->id }})">
                                        {{ $post->allow_comments ? 'Allowed' : 'Disabled' }}
                                    </button>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm {{ $post->is_indexable ? 'btn-success' : 'btn-outline-secondary' }}" wire:click="toggleIndexable({{ $post->id }})">
                                        {{ $post->is_indexable ? 'Index' : 'No Index' }}
                                    </button>
                                </td>
                                <td>{{ $post->updated_at?->format('d M, Y') }}</td>
                                <td class="text-right">
                                    <a href="{{ route('admin.posts.edit', $post) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                                    <button type="button" class="btn btn-sm btn-outline-danger" wire:click="deletePost({{ $post->id }})" onclick="confirm('Are you sure you want to delete this post?') || event.stopImmediatePropagation()">Delete</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted">No posts found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div>
                {{ $posts->links() }}
            </div>
        </div>
    </div>
</div>
