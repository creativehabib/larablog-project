@props(['items' => [], 'level' => 0, 'editingItemId' => null, 'availableTargets' => []])

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
                            {{-- সম্পাদনার সময় নির্বাচিত আইটেম আইডি সিঙ্কে রাখতে হিডেন ইনপুট যোগ করা হয়েছে --}}
                            <form wire:key="menu-item-edit-form-{{ $item['id'] }}" wire:submit.prevent="updateMenuItem({{ $item['id'] }})" wire:loading.class="opacity-50">
                                @error('menuItemUpdate')
                                    <div class="alert alert-danger py-1 px-2 mb-2 small">{{ $message }}</div>
                                @enderror
                                <div class="form-group mb-2">
                                    <label class="form-label">Navigation label</label>
                                    {{-- (পরিবর্তন) editingItem.title -> editTitle --}}
                                    <input type="text" class="form-control" wire:model.defer="editTitle">
                                    @error('editTitle') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                                <div class="form-group mb-2">
                                    <label class="form-label">URL</label>
                                    {{-- (পরিবর্তন) editingItem.url -> editUrl --}}
                                    <input type="text" class="form-control" wire:model.defer="editUrl">
                                    @error('editUrl') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                                <div class="form-group mb-3">
                                    <label class="form-label">Open link in</label>
                                    {{-- (পরিবর্তন) editingItem.target -> editTarget --}}
                                    <select class="form-control" wire:model.defer="editTarget">
                                        @foreach($availableTargets as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('editTarget') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                                <div class="d-flex flex-wrap align-items-center">
                                    <button type="submit" class="btn btn-primary btn-sm mr-2 mb-2" wire:loading.attr="disabled">
                                        <span wire:loading.remove wire:target="updateMenuItem">Save</span>
                                        <span wire:loading wire:target="updateMenuItem">Saving...</span>
                                    </button>
                                    <button type="button" class="btn btn-light btn-sm mb-2" wire:click="cancelEditing" wire:loading.attr="disabled">Cancel</button>
                                </div>
                            </form>
                        </div>
                    @endif
                </div>

                @if($hasChildren)
                    {{-- (পরিবর্তন) recursive কল-এ ভ্যারিয়েবলগুলো পাস করুন --}}
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
