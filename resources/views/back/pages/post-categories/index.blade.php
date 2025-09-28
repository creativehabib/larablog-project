@extends('back.layout.pages-layout')
@section('pageTitle', 'পোস্ট ক্যাটাগরি')
@section('content')
    <header class="page-title-bar">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">ক্যাটাগরি ম্যানেজ করুন</h1>
                <p class="text-muted mb-0">নতুন ক্যাটাগরি তৈরি ও পুরোনো ক্যাটাগরি আপডেট করুন।</p>
            </div>
            <a href="{{ route('admin.post-categories.create') }}" class="btn btn-primary">নতুন ক্যাটাগরি</a>
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
                                <th>নাম</th>
                                <th>স্লাগ</th>
                                <th>পোস্ট সংখ্যা</th>
                                <th>সাব-ক্যাটাগরি</th>
                                <th class="text-right">অ্যাকশন</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($categories as $category)
                                <tr>
                                    <td>
                                        <strong>{{ $category->name }}</strong>
                                    </td>
                                    <td>{{ $category->slug }}</td>
                                    <td>{{ $category->posts_count }}</td>
                                    <td>{{ $category->subcategories_count }}</td>
                                    <td class="text-right">
                                        <a href="{{ route('admin.post-categories.edit', $category) }}" class="btn btn-sm btn-outline-primary">এডিট</a>
                                        <form action="{{ route('admin.post-categories.destroy', $category) }}" method="POST" class="d-inline-block" onsubmit="return confirm('ক্যাটাগরি মুছে ফেলবেন?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">ডিলিট</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">কোন ক্যাটাগরি পাওয়া যায়নি।</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($categories->hasPages())
                <div class="card-footer d-flex justify-content-end">
                    {{ $categories->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
