<div>
    <header class="page-title-bar">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <h1 class="page-title">User Management</h1>
                <p class="text-muted mb-0">Manage user roles, statuses, and access to the admin panel.</p>
            </div>
        </div>
    </header>

    <div class="page-section">
        @if (session()->has('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if (session()->has('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="card card-fluid">
            <div class="card-body">
                <div class="form-row align-items-center mb-3">
                    <div class="col-sm-4 my-1">
                        <input
                            type="search"
                            class="form-control"
                            placeholder="Search users by name, email, or username"
                            wire:model.live.debounce.400ms="search"
                        >
                    </div>
                    <div class="col-sm-3 my-1">
                        <select class="form-control" wire:model.live="roleFilter">
                            <option value="">All roles</option>
                            @foreach ($roleOptions as $option)
                                <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-3 my-1">
                        <select class="form-control" wire:model.live="statusFilter">
                            <option value="">All statuses</option>
                            @foreach ($statusOptions as $option)
                                <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    @if ($search !== '' || $roleFilter !== '' || $statusFilter !== '')
                        <div class="col-auto my-1">
                            <button type="button" class="btn btn-outline-secondary" wire:click="resetFilters">Clear filters</button>
                        </div>
                    @endif
                </div>

                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>User</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Joined</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($users as $user)
                                @php
                                    $statusKey = $user->statusKey();
                                    $statusClasses = [
                                        'active' => 'badge-success',
                                        'pending' => 'badge-warning',
                                        'inactive' => 'badge-secondary',
                                        'rejected' => 'badge-danger',
                                    ];
                                    $statusClass = $statusClasses[$statusKey] ?? 'badge-secondary';
                                @endphp
                                <tr wire:key="user-{{ $user->id }}">
                                    <td>{{ $loop->iteration + ($users->currentPage() - 1) * $users->perPage() }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $user->avatar }}" alt="{{ $user->name }}" class="rounded-circle mr-2" width="40" height="40">
                                            <div>
                                                <div class="font-weight-bold">{{ $user->name }}</div>
                                                <div class="text-muted small">{{ $user->email }}</div>
                                                <div class="text-muted small">@{{ $user->username }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <select class="form-control form-control-sm" wire:change="changeRole({{ $user->id }}, $event.target.value)">
                                            @foreach ($roleOptions as $option)
                                                <option value="{{ $option['value'] }}" @selected($option['value'] === $user->roleKey())>{{ $option['label'] }}</option>
                                            @endforeach
                                        </select>
                                        <div class="text-muted small mt-1">{{ $user->roleSummary() }}</div>
                                    </td>
                                    <td>
                                        <span class="badge {{ $statusClass }} text-uppercase">{{ $user->statusLabel() }}</span>
                                        <select class="form-control form-control-sm mt-2" wire:change="changeStatus({{ $user->id }}, $event.target.value)">
                                            @foreach ($statusOptions as $option)
                                                <option value="{{ $option['value'] }}" @selected($option['value'] === $statusKey)>{{ $option['label'] }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>{{ $user->created_at?->format('d M, Y') }}</td>
                                    <td class="text-right">
                                        <a href="mailto:{{ $user->email }}" class="btn btn-sm btn-outline-secondary">Contact</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">No users found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div>
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
