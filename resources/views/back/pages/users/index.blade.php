@extends('back.layout.pages-layout')
@section('pageTitle', $pageTitle ?? 'Users')
@section('content')

    <div class="page-header d-flex align-items-center justify-content-between">
        <h1 class="page-title">Users</h1>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">Add User</a>
    </div>

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
                                <span class="badge bg-primary">{{ $user->roles->first()->name }}</span>
                            @else
                                <span class="badge bg-secondary">No Role</span>
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

@endsection
