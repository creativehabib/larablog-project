@extends('back.layout.pages-layout')
@section('pageTitle', $pageTitle ?? 'Permissions')
@section('content')
    <header class="page-title-bar d-flex align-items-center justify-content-between flex-wrap">
        <div>
            <h1 class="page-title mb-0">Permissions</h1>
            <p class="text-muted mb-0">User Management</p>
        </div>
        <div class="mt-3 mt-md-0">
            <a href="{{ route('admin.permissions.create') }}" class="btn btn-primary">
                <span class="oi oi-plus mr-1"></span> Add Permission
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
                    <th scope="col">Group Name</th>
                    <th scope="col" class="text-center">Actions</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($permissions as $permission)
                    <tr>
                        <th scope="row">{{ $loop->iteration }}</th>
                        <td>{{ $permission->name }}</td>
                        <td>{{ $permission->group_name }}</td>
                        <td class="text-center">
                            <a href="{{ route('admin.permissions.edit', $permission) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                            <form action="{{ route('admin.permissions.destroy', $permission) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this permission?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted">No permissions found.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
            <div class="mt-3">
                {{ $permissions->links() }}
            </div>
        </div>
    </div>
    </div>

@endsection
