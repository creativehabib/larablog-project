@php
    $selectedPermissions = collect(old('permissions', isset($role) ? $role->permissions->pluck('slug')->all() : []))->map(fn ($value) => (string) $value)->all();
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
    <p class="text-muted small mb-3">Select the permissions that should be granted to this role. "All Permissions" overrides individual selections.</p>
    <hr>
    <div class="">
        <div class="form-check custom-control custom-checkbox mb-2">
            <input type="checkbox" class="custom-control-input" id="checkPermissionAll" name="permissions" value="">
            <label class="custom-control-label" for="checkPermissionAll">All</label>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-md-3">
            <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" id="checkPermission" value="Roles">
                <label class="custom-control-label" for="checkPermission">Roles</label>
            </div>
        </div>
        <div class="col-md-9">
            <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" name="permission0" id="checkPermission0" value="Permission">
                <label class="custom-control-label" for="checkPermission0">Role List</label>
            </div>
            <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" name="permission0" id="checkPermission0" value="Permission">
                <label class="custom-control-label" for="checkPermission0">Role Create</label>
            </div>
            <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" name="permission0" id="checkPermission0" value="Permission">
                <label class="custom-control-label" for="checkPermission0">Role Edit</label>
            </div>
            <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" name="permission0" id="checkPermission0" value="Permission">
                <label class="custom-control-label" for="checkPermission0">Role Delete</label>
            </div>
        </div>

    </div>
</div>


<div class="form-group mb-0">
    <button type="submit" class="btn btn-primary">
        {{ $submitLabel ?? __('Save Role') }}
    </button>

    <button type="reset" class="btn btn-outline-secondary">
        {{ __('Reset Role') }}
    </button>
</div>
