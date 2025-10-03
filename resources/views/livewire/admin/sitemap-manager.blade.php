@php use Illuminate\Support\Str; @endphp
<div>
    <div class="card card-fluid">
        <div class="card-header border-0 pb-0">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center">
                <div class="mb-3 mb-lg-0">
                    <h5 class="card-title mb-1">Sitemap Control Panel</h5>
                    <p class="text-muted small mb-0">এখান থেকে দ্রুত <strong>Index / No Index</strong> পরিবর্তন করুন এবং সার্চ ইঞ্জিন কাভারেজ ঠিক রাখুন।</p>
                </div>
                <div class="d-flex flex-column flex-md-row w-100 w-lg-auto">
                    <div class="flex-grow-1 mb-2 mb-md-0 mr-md-2">
                        <input type="search" class="form-control form-control-sm" placeholder="খুঁজুন: শিরোনাম, স্লাগ বা ক্যাটাগরি" wire:model.live.debounce.500ms="search">
                    </div>
                    <div class="flex-grow-1 flex-md-grow-0">
                        <select class="custom-select custom-select-sm" wire:model.live="status">
                            <option value="all">সব পোস্ট</option>
                            <option value="indexable">শুধু Index</option>
                            <option value="no-index">শুধু No Index</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-sm mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>শিরোনাম</th>
                            <th>ক্যাটাগরি</th>
                            <th>লেখক</th>
                            <th class="text-center">ইনডেক্স অবস্থা</th>
                            <th class="text-right">শেষ আপডেট</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($posts as $post)
                            <tr wire:key="sitemap-{{ $post->id }}">
                                <td>
                                    <div class="font-weight-semibold">{{ Str::limit($post->title, 55) }}</div>
                                    <div class="text-muted small"><code>{{ $post->slug }}</code></div>
                                </td>
                                <td>
                                    <div>{{ $post->category?->name ?? 'N/A' }}</div>
                                </td>
                                <td>
                                    <div>{{ $post->author?->name ?? 'Unknown' }}</div>
                                    <div class="text-muted small">{{ $post->author?->email }}</div>
                                </td>
                                <td class="text-center">
                                    <button type="button"
                                        class="btn btn-xs {{ $post->is_indexable ? 'btn-success' : 'btn-outline-secondary' }}"
                                        wire:click="toggleIndexable({{ $post->id }})"
                                        wire:loading.attr="disabled">
                                        {{ $post->is_indexable ? 'Index' : 'No Index' }}
                                    </button>
                                </td>
                                <td class="text-right text-muted small">{{ $post->updated_at?->diffForHumans() }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">কোনো ফলাফল পাওয়া যায়নি।</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="pt-3">
                {{ $posts->links() }}
            </div>
        </div>
    </div>
</div>
