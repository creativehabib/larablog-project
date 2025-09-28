@extends('back.layout.pages-layout')
@section('pageTitle', $pageTitle ?? 'Edit Post')
@section('content')
    <header class="page-title-bar">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="page-title mb-0">Edit Post</h1>
            <a href="{{ route('admin.posts.index') }}" class="btn btn-link">&larr; Back to posts</a>
        </div>
    </header>

    <div class="page-section">
        <livewire:admin.post-form :post="$post" />
    </div>
@endsection
