@extends('back.layout.pages-layout')
@section('pageTitle', $pageTitle ?? 'Create Permission')
@section('content')

    <div class="page-header">
        <h1 class="page-title">Create New Permission</h1>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.permissions.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="name" class="form-label">Permission Name</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" placeholder="e.g., post.create">
                    @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="group_name" class="form-label">Group Name</label>
                    <input type="text" class="form-control @error('group_name') is-invalid @enderror" id="group_name" name="group_name" value="{{ old('group_name') }}" placeholder="e.g., Post">
                    @error('group_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary">Create Permission</button>
                <a href="{{ route('admin.permissions.index') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>

@endsection
