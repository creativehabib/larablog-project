@extends('back.layout.pages-layout')
@section('pageTitle', $pageTitle ?? 'Roles & Permissions')
@section('content')
    <header class="page-title-bar d-flex align-items-center justify-content-between flex-wrap">
        <div>
            <h1 class="page-title mb-0">Roles &amp; Permissions</h1>
            <p class="text-muted mb-0">Manage system roles and the permissions assigned to each.</p>
        </div>
        <div class="mt-3 mt-md-0">
            <a href="{{ route('admin.roles.create') }}" class="btn btn-primary">
                <span class="oi oi-plus mr-1"></span> Create Role
            </a>
        </div>
    </header>

    <div class="page-section">
        <div class="card card-fluid">
            <div class="card-body">
                <x-form-alerts />

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th scope="col">Name</th>
                                <th scope="col">Slug</th>
                                <th scope="col">Summary</th>
                                <th scope="col" class="text-center">Permissions</th>
                                <th scope="col" class="text-center">Users</th>
                                <th scope="col" class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($roles as $role)
                                <tr>
                                    <td class="align-middle font-weight-semibold">{{ $role->name }}</td>
                                    <td class="align-middle"><code>{{ $role->slug }}</code></td>
                                    <td class="align-middle" style="max-width: 320px;">
                                        {{ $role->summary ?? '—' }}
                                    </td>
                                    <td class="align-middle text-center">
                                        @if ($role->permissions_count > 0)
                                            <div class="d-flex flex-wrap justify-content-center">
                                                @foreach ($role->permissions->take(4) as $permission)
                                                    <span class="badge badge-light border mr-1 mb-1">{{ $permission->name }}</span>
                                                @endforeach
                                                @if ($role->permissions_count > 4)
                                                    <span class="badge badge-secondary mb-1">+{{ $role->permissions_count - 4 }}</span>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-muted">None</span>
                                        @endif
                                    </td>
                                    <td class="align-middle text-center">{{ $role->users_count }}</td>
                                    <td class="align-middle text-right">
                                        <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-sm btn-outline-primary">
                                            <span class="oi oi-pencil mr-1"></span> Edit
                                        </a>
                                        <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button
                                                type="submit"
                                                class="btn btn-sm btn-outline-danger"
                                                onclick="return confirm('Are you sure you want to delete this role?')"
                                                {{ $role->slug === 'superadmin' ? 'disabled' : '' }}
                                            >
                                                <span class="oi oi-trash mr-1"></span> Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">No roles found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
