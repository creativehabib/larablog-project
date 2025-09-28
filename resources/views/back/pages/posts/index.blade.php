@extends('back.layout.pages-layout')
@section('pageTitle', 'সব পোস্ট')
@section('content')
    <header class="page-title-bar">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">সব পোস্ট</h1>
                <p class="text-muted mb-0">আপনার সব আর্টিকেল এখানে ম্যানেজ করতে পারেন।</p>
            </div>
            <a href="{{ route('admin.posts.create') }}" class="btn btn-primary">নতুন পোস্ট</a>
        </div>
    </header>

    <div class="page-section">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped mb-0">
                        <thead>
                            <tr>
                                <th style="width: 45%">শিরোনাম</th>
                                <th>ক্যাটাগরি</th>
                                <th>সাব-ক্যাটাগরি</th>
                                <th>ফিচারড</th>
                                <th>ইনডেক্স</th>
                                <th>আপডেট</th>
                                <th class="text-right">অ্যাকশন</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($posts as $post)
                                <tr>
                                    <td>
                                        <strong>{{ $post->title }}</strong>
                                        <div class="small text-muted">/{{ $post->slug }}</div>
                                    </td>
                                    <td>{{ $post->category->name ?? 'N/A' }}</td>
                                    <td>{{ $post->subcategory->name ?? '-' }}</td>
                                    <td>
                                        @if($post->is_featured)
                                            <span class="badge badge-success">হ্যাঁ</span>
                                        @else
                                            <span class="badge badge-light">না</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($post->is_indexable)
                                            <span class="badge badge-info">Index</span>
                                        @else
                                            <span class="badge badge-secondary">Noindex</span>
                                        @endif
                                    </td>
                                    <td>{{ $post->updated_at->format('d M, Y') }}</td>
                                    <td class="text-right">
                                        <a href="{{ route('admin.posts.edit', $post) }}" class="btn btn-sm btn-outline-primary">এডিট</a>
                                        <form action="{{ route('admin.posts.destroy', $post) }}" method="POST" class="d-inline-block" onsubmit="return confirm('পোস্ট মুছে ফেলতে চান?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">ডিলিট</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">কোন পোস্ট পাওয়া যায়নি।</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($posts->hasPages())
                <div class="card-footer d-flex justify-content-end">
                    {{ $posts->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
