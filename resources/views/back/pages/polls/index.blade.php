@extends('back.layout.pages-layout')
@section('pageTitle', $pageTitle ?? 'Polls')
@section('content')
    <header class="page-title-bar">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <h1 class="page-title">Opinion Polls</h1>
                <p class="text-muted mb-0">Create, update, or close opinion polls directly from the dashboard.</p>
            </div>
            @can('poll.create')
                <a href="{{ route('admin.polls.create') }}" class="btn btn-primary">Create New Poll</a>
            @endcan
        </div>
    </header>

    <div class="page-section">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <livewire:admin.polls-table />
    </div>
@endsection
