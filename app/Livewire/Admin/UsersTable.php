<?php

namespace App\Livewire\Admin;

use App\Models\Role;
use App\Models\User;
use App\UserStatus;
use App\UserType;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class UsersTable extends Component
{
    use WithPagination;

    #[Url(as: 'search', except: '')]
    public string $search = '';

    #[Url(as: 'role', except: '')]
    public string $roleFilter = '';

    #[Url(as: 'status', except: '')]
    public string $statusFilter = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingRoleFilter(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->roleFilter = '';
        $this->statusFilter = '';
    }

    public function changeRole(int $userId, string $role): void
    {
        $this->ensureCanManageUsers();

        $role = trim($role);

        if (! in_array($role, $this->availableRoleValues(), true)) {
            session()->flash('error', 'Invalid role selected.');

            return;
        }

        $user = User::findOrFail($userId);

        if ($user->roleKey() === $role) {
            return;
        }

        $user->syncRoles($role);

        session()->flash('success', 'User role updated successfully.');
    }

    public function changeStatus(int $userId, string $status): void
    {
        $this->ensureCanManageUsers();

        $status = trim($status);

        if (! in_array($status, $this->availableStatusValues(), true)) {
            session()->flash('error', 'Invalid status selected.');

            return;
        }

        $user = User::findOrFail($userId);

        if ($user->statusKey() === $status) {
            return;
        }

        $user->status = UserStatus::from($status);
        $user->save();

        session()->flash('success', 'User status updated successfully.');
    }

    public function render()
    {
        $users = User::query()
            ->when($this->search !== '', function ($query) {
                $query->where(function ($innerQuery) {
                    $searchTerm = "%{$this->search}%";

                    $innerQuery->where('name', 'like', $searchTerm)
                        ->orWhere('email', 'like', $searchTerm)
                        ->orWhere('username', 'like', $searchTerm);
                });
            })
            ->when($this->roleFilter !== '', function ($query) {
                $query->where('type', $this->roleFilter);
            })
            ->when($this->statusFilter !== '', function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('livewire.admin.users-table', [
            'users' => $users,
            'roleOptions' => $this->roleOptions(),
            'statusOptions' => $this->statusOptions(),
        ]);
    }

    /**
     * Ensure the authenticated user can manage users.
     */
    protected function ensureCanManageUsers(): void
    {
        $user = Auth::user();

        if (! $user || ! $user->hasRole(UserType::SuperAdmin->value)) {
            abort(403);
        }
    }

    /**
     * @return list<string>
     */
    protected function availableRoleValues(): array
    {
        return Role::query()->pluck('slug')->all();
    }

    /**
     * @return list<string>
     */
    protected function availableStatusValues(): array
    {
        return array_map(static fn (UserStatus $status) => $status->value, UserStatus::cases());
    }

    /**
     * @return array<int, array{value: string, label: string}>
     */
    protected function roleOptions(): array
    {
        return Role::query()
            ->orderBy('name')
            ->get()
            ->map(fn (Role $role) => [
                'value' => $role->slug,
                'label' => $role->name,
            ])
            ->all();
    }

    /**
     * @return array<int, array{value: string, label: string}>
     */
    protected function statusOptions(): array
    {
        return array_map(function (UserStatus $status) {
            $key = $status->value;
            $label = ucfirst(str_replace('_', ' ', $key));

            return [
                'value' => $key,
                'label' => $label,
            ];
        }, UserStatus::cases());
    }
}
