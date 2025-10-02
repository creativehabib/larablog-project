@extends('back.layout.pages-layout')
@section('pageTitle', $pageTitle ?? 'Users')
@section('content')

    <header class="page-title-bar d-flex align-items-center justify-content-between flex-wrap">
        <div>
            <h1 class="page-title mb-0">Users</h1>
            <p class="text-muted mb-0">User Management</p>
        </div>
        <div class="mt-3 mt-md-0">
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                <span class="oi oi-plus mr-1"></span> Create User
            </a>
        </div>
    </header>
    <div class="page-section">
        <div class="card">
        <div class="card-body">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Name</th>
                    <th scope="col">Email</th>
                    <th scope="col">Role</th>
                    <th scope="col" class="text-center">Actions</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($users as $user)
                    <tr>
                        <th scope="row">{{ $loop->iteration }}</th>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @if ($user->roles->isNotEmpty())
                                <span class="badge badge-primary">{{ $user->roles->first()->name }}</span>
                            @else
                                <span class="badge badge-secondary">No Role</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted">No users found.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
            <div class="mt-3">
                {{ $users->links() }}
            </div>
        </div>
    </div>
    </div>
@endsection
