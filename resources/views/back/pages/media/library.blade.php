@extends('back.layout.pages-layout')
@section('pageTitle', $pageTitle ?? 'Media Library')
@section('content')
    <header class="page-title-bar">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <h1 class="page-title">Media Library</h1>
                <p class="text-muted mb-0">Manage images, videos, and documents used across the site.</p>
            </div>
        </div>
    </header>

    <div class="page-section">
        <livewire:admin.media-library />
    </div>
@endsection
