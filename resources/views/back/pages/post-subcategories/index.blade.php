@extends('back.layout.pages-layout')
@section('pageTitle', 'পোস্ট সাব-ক্যাটাগরি')
@section('content')
    <header class="page-title-bar">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">সাব-ক্যাটাগরি ম্যানেজ করুন</h1>
                <p class="text-muted mb-0">পোস্ট সাজাতে সাব-ক্যাটাগরি ব্যবহার করুন।</p>
            </div>
            <a href="{{ route('admin.post-subcategories.create') }}" class="btn btn-primary">নতুন সাব-ক্যাটাগরি</a>
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
                                <th>মূল ক্যাটাগরি</th>
                                <th>স্লাগ</th>
                                <th class="text-right">অ্যাকশন</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($subcategories as $subcategory)
                                <tr>
                                    <td><strong>{{ $subcategory->name }}</strong></td>
                                    <td>{{ $subcategory->category->name ?? '-' }}</td>
                                    <td>{{ $subcategory->slug }}</td>
                                    <td class="text-right">
                                        <a href="{{ route('admin.post-subcategories.edit', $subcategory) }}" class="btn btn-sm btn-outline-primary">এডিট</a>
                                        <form action="{{ route('admin.post-subcategories.destroy', $subcategory) }}" method="POST" class="d-inline-block" onsubmit="return confirm('সাব-ক্যাটাগরি মুছে ফেলবেন?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">ডিলিট</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">কোন সাব-ক্যাটাগরি পাওয়া যায়নি।</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($subcategories->hasPages())
                <div class="card-footer d-flex justify-content-end">
                    {{ $subcategories->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
