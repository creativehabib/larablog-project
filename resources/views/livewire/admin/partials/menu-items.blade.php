@props(['items' => [], 'level' => 0])

@if(!empty($items))
    <ol class="dd-list">
        @foreach($items as $item)
            @php
                $hasChildren = !empty($item['children']);
                $isEditing = $editingItemId === $item['id'];
            @endphp
            <li class="dd-item dd3-item" data-id="{{ $item['id'] }}" wire:key="menu-item-{{ $item['id'] }}">
                <div class="dd-handle dd3-handle" title="Drag to reorder"></div>
                <div class="dd3-content">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="me-3">
                            <strong>{{ $item['title'] }}</strong>
                            <div class="text-muted small text-break">{{ $item['url'] }}</div>
                        </div>
                        <div class="btn-group btn-group-sm">
                            <button type="button" class="btn btn-outline-secondary" wire:click="startEditing({{ $item['id'] }})">
                                Edit
                            </button>
                            <button type="button" class="btn btn-outline-danger"
                                    wire:click="deleteMenuItem({{ $item['id'] }})"
                                    onclick="confirm('Are you sure you want to remove this item?') || event.stopImmediatePropagation()">
                                Remove
                            </button>
                        </div>
                    </div>

                    @if($isEditing)
                        <div class="mt-3 border-top pt-3">
                            <form wire:submit.prevent="updateMenuItem">
                                <div class="form-group mb-2">
                                    <label class="form-label">Navigation label</label>
                                    <input type="text" class="form-control" wire:model.defer="editingItem.title">
                                    @error('editingItem.title') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                                <div class="form-group mb-2">
                                    <label class="form-label">URL</label>
                                    <input type="text" class="form-control" wire:model.defer="editingItem.url">
                                    @error('editingItem.url') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                                <div class="form-group mb-3">
                                    <label class="form-label">Open link in</label>
                                    <select class="form-control" wire:model.defer="editingItem.target">
                                        @foreach($availableTargets as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('editingItem.target') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                                <div class="d-flex flex-wrap align-items-center">
                                    <button type="submit" class="btn btn-primary btn-sm mr-2 mb-2">Save</button>
                                    <button type="button" class="btn btn-light btn-sm mb-2" wire:click="cancelEditing">Cancel</button>
                                </div>
                            </form>
                        </div>
                    @endif
                </div>

                @if($hasChildren)
                    @include('livewire.admin.partials.menu-items', ['items' => $item['children'], 'level' => $level + 1])
                @endif
            </li>
        @endforeach
    </ol>
@endif
