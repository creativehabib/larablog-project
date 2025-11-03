@props(['items' => [], 'level' => 0, 'editingItemId' => null, 'availableTargets' => []])

@if(!empty($items))
    <ol class="dd-list">
        @foreach($items as $item)
            @php
                $hasChildren = !empty($item['children']);
                $isEditing = $editingItemId === $item['id'];
            @endphp
            <li class="dd-item dd3-item" data-id="{{ $item['id'] }}" wire:key="menu-item-{{ $item['id'] }}">
                <div class="dd3-content">
                    <div class="menu-item-header d-flex justify-content-between align-items-center">
                        <div class="drag-handle dd-handle me-3">
                            <strong>{{ $item['title'] }}</strong>
                        </div>
                        <button
                            type="button"
                            class="btn btn-sm text-decoration-none"
                            wire:click="toggleEditing({{ $item['id'] }})"
                        >
                            <span class="mr-1">{{ $isEditing ? 'Hide options' : 'Edit item' }}</span>
                            <i class="fas fa-chevron-{{ $isEditing ? 'up' : 'down' }}"></i>
                        </button>
                    </div>

                    @if($isEditing)
                        <div class="mt-3 border-top p-3">
                            <form wire:key="menu-item-edit-form-{{ $item['id'] }}"
                                  wire:submit.prevent="updateMenuItem({{ $item['id'] }})"
                                  wire:loading.class="opacity-50">
                                <input type="hidden" wire:model.defer="editingItemId">
                                <div class="form-group mb-2">
                                    <label class="form-label">Navigation label</label>
                                    <input type="text" class="form-control" wire:model.defer="editTitle">
                                    @error('editTitle') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                                <div class="form-group mb-2">
                                    <label class="form-label">URL</label>
                                    <input type="text" class="form-control" wire:model.defer="editUrl">
                                    @error('editUrl') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                                <div class="form-group mb-3">
                                    <label class="form-label">Open link in</label>
                                    <select class="form-control" wire:model.defer="editTarget">
                                        @foreach($availableTargets as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('editTarget') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                                <div class="d-flex flex-wrap align-items-center gap-1">
                                    <button type="submit" class="btn btn-primary btn-sm " wire:loading.attr="disabled" wire:target="updateMenuItem">
                                        <span wire:loading.remove wire:target="updateMenuItem">Save</span>
                                        <span wire:loading wire:target="updateMenuItem">Saving...</span>
                                    </button>
                                    <button type="button" class="btn btn-outline-danger btn-sm"
                                            wire:click="deleteMenuItem({{ $item['id'] }})"
                                            wire:loading.attr="disabled"
                                            onclick="confirm('Are you sure you want to remove this item?') || event.stopImmediatePropagation()">
                                        Remove
                                    </button>
                                    <button type="button" class="btn btn-light btn-sm " wire:click="cancelEditing" wire:loading.attr="disabled">Cancel</button>
                                </div>
                            </form>
                        </div>
                    @endif
                </div>

                @if($hasChildren)
                    @include('livewire.admin.partials.menu-items', [
                        'items' => $item['children'],
                        'level' => $level + 1,
                        'editingItemId' => $editingItemId,
                        'availableTargets' => $availableTargets
                    ])
                @endif
            </li>
        @endforeach
    </ol>
@endif
