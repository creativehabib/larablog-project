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
    <label for="slug">Slug</label>
    <input
        type="text"
        name="slug"
        id="slug"
        value="{{ old('slug', $role->slug ?? '') }}"
        class="form-control @error('slug') is-invalid @enderror"
        placeholder="Leave empty to auto-generate"
    >
    @error('slug')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="form-group">
    <label for="summary">Summary</label>
    <textarea
        name="summary"
        id="summary"
        rows="3"
        class="form-control @error('summary') is-invalid @enderror"
    >{{ old('summary', $role->summary ?? '') }}</textarea>
    @error('summary')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="form-group">
    <label class="d-block">Assign Permissions</label>
    <p class="text-muted small mb-3">Select the permissions that should be granted to this role. "All Permissions" overrides individual selections.</p>
    <div class="row">
        @foreach ($permissions as $permission)
            @php
                $checkboxId = 'permission-'.$permission->id;
                $isChecked = in_array($permission->slug, $selectedPermissions, true);
                $label = $permission->name ?? \Illuminate\Support\Str::headline(str_replace(['*', '.', '_'], ' ', $permission->slug));
            @endphp
            <div class="col-md-4">
                <div class="custom-control custom-checkbox mb-2">
                    <input
                        type="checkbox"
                        class="custom-control-input"
                        id="{{ $checkboxId }}"
                        name="permissions[]"
                        value="{{ $permission->slug }}"
                        {{ $isChecked ? 'checked' : '' }}
                    >
                    <label class="custom-control-label" for="{{ $checkboxId }}">
                        {{ $label }}
                        <span class="d-block text-muted small">{{ $permission->slug }}</span>
                    </label>
                </div>
            </div>
        @endforeach
    </div>
    @error('permissions')
        <div class="text-danger small">{{ $message }}</div>
    @enderror
    @error('permissions.*')
        <div class="text-danger small">{{ $message }}</div>
    @enderror
</div>

<div class="form-group">
    <label for="new_permissions">Create Additional Permissions</label>
    <textarea
        name="new_permissions"
        id="new_permissions"
        rows="4"
        class="form-control @error('new_permissions') is-invalid @enderror"
        placeholder="One permission per line. Use the format slug|Label (label optional)."
    >{{ old('new_permissions') }}</textarea>
    @error('new_permissions')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
    <small class="form-text text-muted">Example: <code>manage_tags|Manage Tags</code> or <code>feature_posts</code></small>
</div>

<div class="form-group mb-0">
    <button type="submit" class="btn btn-primary">
        {{ $submitLabel ?? __('Save Role') }}
    </button>
</div>
