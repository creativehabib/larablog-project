@extends('back.layout.pages-layout')
@section('pageTitle', $pageTitle ?? 'Edit User')
@section('content')

    <header class="page-title-bar">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="page-title mb-0">Edit User</h1>
            <a href="{{ route('admin.users.index') }}" class="btn btn-link">&larr; Back to users</a>
        </div>
    </header>

    <div class="page-section">
        <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.users.update', $user) }}" method="POST">
                @csrf @method('PUT')
                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" class="form-control" name="name" value="{{ old('name', $user->name) }}">
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" name="email" value="{{ old('email', $user->email) }}">
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" name="password">
                </div>
                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">Confirm Password</label>
                    <input type="password" class="form-control" name="password_confirmation">
                </div>
                <div class="mb-3">
                    <label for="role" class="form-label">Assign Role</label>
                    <select name="role" class="custom-select">
                        <option value="">Select a role</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role->name }}" {{ $user->hasRole($role->name) ? 'selected' : '' }}>
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Update user</button>
            </form>
        </div>
    </div>
    </div>

@endsection
