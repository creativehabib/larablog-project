@php
    $selectedPermissions = old('permissions', isset($role) ? $role->permissions->pluck('name')->all() : []);
@endphp
<div class="form-group">
    <label for="name">Role Name <span class="text-danger">*</span></label>
    <input
        type="text"
        name="name"
        id="name"
        value="{{ old('name', $role->name ?? '') }}"
        class="form-control @error('name') is-invalid @enderror"
        required
    >
    @error('name')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>


<div class="form-group">
    <label class="d-block">Assign Permissions</label>
    <p class="text-muted small mb-3">Select the permissions that should be granted to this role.</p>
    <hr>
    <div class="form-check custom-control custom-checkbox mb-2">
        {{-- FIX: This checkbox is only for JS control, no name or value needed --}}
        <input type="checkbox" class="custom-control-input" id="checkPermissionAll">
        <label class="custom-control-label" for="checkPermissionAll">All Permissions</label>
    </div>
    <hr>

    {{-- FIX: Use a different variable for the inner loop item ($permission) --}}
    @foreach($groupedPermissions as $groupName => $permissions)
        <div class="row mb-3">
            <div class="col-md-3">
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input group-checkbox" id="checkGroup-{{ Str::slug($groupName) }}" data-group="{{ Str::slug($groupName) }}">
                    <label class="custom-control-label" for="checkGroup-{{ Str::slug($groupName) }}">{{ $groupName }}</label>
                </div>
            </div>
            <div class="col-md-9 group-permissions-container" data-group-container="{{ Str::slug($groupName) }}">
                @foreach($permissions->sortBy('name') as $permission)
                    <div class="custom-control custom-checkbox mr-3 mb-2">
                        <input
                            type="checkbox"
                            class="custom-control-input permission-checkbox"
                            name="permissions[]"
                            id="checkPermission-{{ $permission->id }}"
                            value="{{ $permission->name }}"
                            {{ in_array($permission->name, $selectedPermissions) ? 'checked' : '' }}
                        >
                        <label class="custom-control-label" for="checkPermission-{{ $permission->id }}">{{ $permission->name }}</label>
                    </div>
                @endforeach
            </div>
        </div>
        <hr class="my-1">
    @endforeach
</div>


<div class="form-group mb-0">
    <button type="submit" class="btn btn-primary">
        {{ $submitLabel ?? __('Save Role') }}
    </button>

    <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary">
        Cancel
    </a>
</div>

{{-- Add this script section to your page --}}
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const checkAll = document.getElementById('checkPermissionAll');
            const groupCheckboxes = document.querySelectorAll('.group-checkbox');
            const permissionCheckboxes = document.querySelectorAll('.permission-checkbox');

            // "All Permissions" checkbox functionality
            checkAll.addEventListener('change', function () {
                permissionCheckboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
                groupCheckboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
            });

            // "Group" checkbox functionality
            groupCheckboxes.forEach(groupCheckbox => {
                groupCheckbox.addEventListener('change', function () {
                    const group = this.getAttribute('data-group');
                    const permissionsInGroup = document.querySelectorAll(`[data-group-container="${group}"] .permission-checkbox`);
                    permissionsInGroup.forEach(checkbox => {
                        checkbox.checked = this.checked;
                    });
                    updateCheckAllState();
                });
            });

            // Individual permission checkbox functionality
            permissionCheckboxes.forEach(permissionCheckbox => {
                permissionCheckbox.addEventListener('change', function () {
                    updateGroupCheckboxState(this);
                    updateCheckAllState();
                });
            });

            // Helper function to update a group's checkbox state
            function updateGroupCheckboxState(permissionCheckbox) {
                const container = permissionCheckbox.closest('.group-permissions-container');
                const group = container.getAttribute('data-group-container');
                const groupCheckbox = document.querySelector(`.group-checkbox[data-group="${group}"]`);
                const allInGroup = container.querySelectorAll('.permission-checkbox');
                const allCheckedInGroup = container.querySelectorAll('.permission-checkbox:checked');
                groupCheckbox.checked = allInGroup.length === allCheckedInGroup.length;
            }

            // Helper function to update the "All Permissions" checkbox state
            function updateCheckAllState() {
                checkAll.checked = permissionCheckboxes.length === document.querySelectorAll('.permission-checkbox:checked').length;
            }

            // Initial state check on page load
            groupCheckboxes.forEach(groupCheckbox => {
                const group = groupCheckbox.getAttribute('data-group');
                const container = document.querySelector(`[data-group-container="${group}"]`);
                const allInGroup = container.querySelectorAll('.permission-checkbox');
                const allCheckedInGroup = container.querySelectorAll('.permission-checkbox:checked');
                groupCheckbox.checked = allInGroup.length > 0 && allInGroup.length === allCheckedInGroup.length;
            });
            updateCheckAllState();

        });
    </script>
@endpush
