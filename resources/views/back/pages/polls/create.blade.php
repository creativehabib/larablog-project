@extends('back.layout.pages-layout')
@section('pageTitle', $pageTitle ?? 'Create Poll')
@section('content')
    <header class="page-title-bar">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="page-title mb-0">Create Poll</h1>
            <a href="{{ route('admin.polls.index') }}" class="btn btn-link">&larr; Back to polls</a>
        </div>
    </header>

    <div class="page-section">
        <livewire:admin.poll-form />
    </div>
@endsection
