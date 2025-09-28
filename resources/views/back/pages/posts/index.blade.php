@extends('back.layout.pages-layout')
@section('pageTitle', $pageTitle ?? 'Posts')
@section('content')
    <header class="page-title-bar">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <h1 class="page-title">Posts</h1>
                <p class="text-muted">Manage blog posts, featured options, and metadata.</p>
            </div>
            <a href="{{ route('admin.posts.create') }}" class="btn btn-primary">Create New Post</a>
        </div>
    </header>

    <div class="page-section">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <livewire:admin.posts-table />
    </div>
@endsection
