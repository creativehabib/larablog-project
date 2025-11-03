<div>
    <div class="row">
        <div class="col-xl-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Menus</h5>
                </div>
                <div class="card-body">
                    @if(!empty($menus))
                        <div class="form-group mb-3">
                            <label class="form-label">Select menu</label>
                            <select class="form-control" wire:model.live="selectedMenuId">
                                <option value="">— Select a Menu —</option>
                                @foreach($menus as $menu)
                                    <option value="{{ $menu['id'] }}">{{ $menu['name'] }} ({{ $menu['location'] }})</option>
                                @endforeach
                            </select>
                            @error('selectedMenuId') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                    @endif

                    <form wire:submit.prevent="createMenu" class="border-top pt-3 mt-3">
                        <h6 class="mb-3">Create new menu</h6>
                        <div class="form-group mb-3">
                            <label class="form-label">Menu name</label>
                            <input type="text" class="form-control" wire:model.defer="newMenuName" placeholder="Primary navigation">
                            @error('newMenuName') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">Location</label>
                            <input list="menu-locations" class="form-control" wire:model.defer="newMenuLocation" placeholder="primary">
                            <datalist id="menu-locations">
                                @foreach($locationSuggestions as $locationKey => $label)
                                    <option value="{{ $locationKey }}">{{ $label }}</option>
                                @endforeach
                            </datalist>
                            <small class="form-text text-muted">Locations help you reuse menus in different parts of the site.</small>
                            @error('newMenuLocation') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Create menu</button>
                    </form>

                    @if($selectedMenu)
                        <form wire:submit.prevent="updateMenu" class="border-top pt-3 mt-4">
                            <h6 class="mb-3">Menu settings</h6>
                            <div class="form-group mb-3">
                                <label class="form-label">Menu name</label>
                                <input type="text" class="form-control" wire:model.defer="editMenuName">
                                @error('editMenuName') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">Location</label>
                                <input list="menu-locations" class="form-control" wire:model.defer="editMenuLocation">
                                @error('editMenuLocation') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                            <div class="d-flex flex-wrap align-items-center">
                                <button type="submit" class="btn btn-primary mr-2 mb-2">Save changes</button>
                                <button type="button" class="btn btn-outline-danger mb-2"
                                        wire:click="deleteMenu({{ $selectedMenuId }})"
                                        onclick="confirm('This will delete the menu and all of its items. Continue?') || event.stopImmediatePropagation()">
                                    Delete menu
                                </button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Add menu items</h5>
                </div>
                <div class="card-body">
                    @if(! $selectedMenu)
                        <p class="text-muted mb-0">Create a menu first to start adding links.</p>
                    @else
                        <ul class="nav nav-tabs" id="menu-item-tabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a
                                    class="nav-link {{ $activeTab === 'custom-link' ? 'active' : '' }}"
                                    id="tab-custom-link"
                                    data-toggle="tab"
                                    href="#panel-custom-link"
                                    role="tab"
                                    wire:click.prevent="$set('activeTab', 'custom-link')"
                                >
                                    Custom link
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a
                                    class="nav-link {{ $activeTab === 'categories' ? 'active' : '' }}"
                                    id="tab-categories"
                                    data-toggle="tab"
                                    href="#panel-categories"
                                    role="tab"
                                    wire:click.prevent="$set('activeTab', 'categories')"
                                >
                                    Categories
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a
                                    class="nav-link {{ $activeTab === 'posts' ? 'active' : '' }}"
                                    id="tab-posts"
                                    data-toggle="tab"
                                    href="#panel-posts"
                                    role="tab"
                                    wire:click.prevent="$set('activeTab', 'posts')"
                                >
                                    Posts
                                </a>
                            </li>
                        </ul>
                        <div class="tab-content pt-3">
                            <div class="tab-pane fade {{ $activeTab === 'custom-link' ? 'show active' : '' }}" id="panel-custom-link" role="tabpanel" aria-labelledby="tab-custom-link">
                                <form wire:submit.prevent="addCustomLink">
                                    <div class="form-group mb-3">
                                        <label class="form-label">Navigation label</label>
                                        <input type="text" class="form-control" wire:model.defer="customTitle">
                                        @error('customTitle') <span class="text-danger small">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="form-group mb-3">
                                        <label class="form-label">URL</label>
                                        <input type="text" class="form-control" wire:model.defer="customUrl" placeholder="/relative or https://absolute">
                                        @error('customUrl') <span class="text-danger small">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="form-group mb-4">
                                        <label class="form-label">Open link in</label>
                                        <select class="form-control" wire:model.defer="customTarget">
                                            @foreach($availableTargets as $value => $label)
                                                <option value="{{ $value }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                        @error('customTarget') <span class="text-danger small">{{ $message }}</span> @enderror
                                    </div>
                                    <button type="submit" class="btn btn-primary">Add to menu</button>
                                </form>
                            </div>
                            <div class="tab-pane fade {{ $activeTab === 'categories' ? 'show active' : '' }}" id="panel-categories" role="tabpanel" aria-labelledby="tab-categories">
                                <form wire:submit.prevent="addCategoriesToMenu">
                                    <div class="form-group mb-3">
                                        <label class="form-label">Search categories</label>
                                        <input type="text" class="form-control" placeholder="Type to filter..." wire:model.live.debounce.500ms="categorySearch">
                                    </div>
                                    <div class="menu-picker" style="max-height: 200px; overflow-y: auto;">
                                        @forelse($this->categoryOptions as $category)
                                            <div class="form-check" wire:key="category-option-{{ $category->id }}">
                                                <input class="form-check-input" type="checkbox" value="{{ $category->id }}" id="category-{{ $category->id }}" wire:model.live="selectedCategories">
                                                <label class="form-check-label" for="category-{{ $category->id }}">{{ $category->name }}</label>
                                            </div>
                                        @empty
                                            <p class="text-muted small mb-0">No categories found.</p>
                                        @endforelse
                                    </div>
                                    @error('selectedCategories')
                                    <span class="text-danger small d-block mt-2">{{ $message }}</span>
                                    @enderror
                                    <button type="submit" class="btn btn-primary mt-3" wire:loading.attr="disabled" @disabled(empty($selectedCategories))>Add to menu</button>
                                </form>
                            </div>
                            <div class="tab-pane fade {{ $activeTab === 'posts' ? 'show active' : '' }}" id="panel-posts" role="tabpanel" aria-labelledby="tab-posts">
                                <form wire:submit.prevent="addPostsToMenu">
                                    <div class="form-group mb-3">
                                        <label class="form-label">Search posts</label>
                                        <input type="text" class="form-control" placeholder="Type to filter..." wire:model.live.debounce.500ms="postSearch">
                                    </div>
                                    <div class="menu-picker" style="max-height: 200px; overflow-y: auto;">
                                        @forelse($this->postOptions as $post)
                                            <div class="form-check" wire:key="post-option-{{ $post->id }}">
                                                <input class="form-check-input" type="checkbox" value="{{ $post->id }}" id="post-{{ $post->id }}" wire:model.live="selectedPosts">
                                                <label class="form-check-label" for="post-{{ $post->id }}">{{ $post->title }}</label>
                                            </div>
                                        @empty
                                            <p class="text-muted small mb-0">No posts found.</p>
                                        @endforelse
                                    </div>
                                    @error('selectedPosts')
                                    <span class="text-danger small d-block mt-2">{{ $message }}</span>
                                    @enderror
                                    <button type="submit" class="btn btn-primary mt-3" wire:loading.attr="disabled" @disabled(empty($selectedPosts))>Add to menu</button>
                                </form>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-xl-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                    <div>
                        <h5 class="card-title mb-0">Menu structure</h5>
                        <small class="text-muted">Drag and drop items to reorder. Nest items to create dropdowns.</small>
                    </div>
                    @if($selectedMenu)
                        <span class="badge badge-light text-uppercase">{{ $selectedMenu['location'] }}</span>
                    @endif
                </div>
                <div class="card-body">
                    <div wire:key="menu-structure-{{ $selectedMenuId ?? 'none' }}">
                        @if(session()->has('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        @if(! $selectedMenu)
                            <p class="text-muted mb-0">Create a menu to start organising links.</p>
                        @elseif(empty($selectedMenu['items']))
                            <p class="text-muted mb-0">This menu does not have any items yet.</p>
                        @else
                            <div id="menuNestable" data-menu-structure class="dd">
                                {{-- === (সমাধান) $editingItemId এবং $availableTargets এখানে পাস করুন === --}}
                                @include('livewire.admin.partials.menu-items', [
                                    'items' => $selectedMenu['items'],
                                    'editingItemId' => $editingItemId,
                                    'availableTargets' => $availableTargets
                                ])
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- এই @push('scripts') সেকশনটি আপনার মেইন লেআউট ফাইলে (@stack('scripts')) রেন্ডার হবে --}}
    @push('scripts')
        {{-- Nestable.js লাইব্রেরি (আপনার মেইন লেআউটে jQuery থাকতে হবে) --}}
        <script src="https://cdnjs.cloudflare.com/ajax/libs/nestable2/1.6.0/jquery.nestable.min.js"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/nestable2/1.6.0/jquery.nestable.min.css" />

        <style>
            /* Nestable.js-এর জন্য কিছু স্টাইল ফিক্স */
            .dd { max-width: 100%; }
            .dd-handle { height: auto; }
            .dd3-content {
                height: auto;
                padding: 10px 15px;
                display: block;
            }
            .dd-placeholder {
                background: #f2f2f2;
                border: 1px dashed #b6bcbf;
                box-sizing: border-box;
                min-height: 50px;
                margin: 5px 0;
            }
            .dd3-item > button { margin-left: 40px; } /* Drag handle-এর জন্য জায়গা */
            .dd-list { list-style: none; padding-left: 0; }
            .dd3-content .btn-group { margin-left: auto; }
            .dd3-content .me-3 { margin-right: 1rem; }

            /* Livewire লোডিং স্টাইল */
            .opacity-50 { opacity: 0.5; }
            /* বুটস্ট্র্যাপ ৪-এর জন্য রাইট মার্জিন ফিক্স */
            .mr-2 { margin-right: 0.5rem !important; }
        </style>

        <script>
            document.addEventListener('livewire:init', () => {

                let nestableInstance = null;

                function initializeNestable() {
                    if (nestableInstance) {
                        nestableInstance.nestable('destroy');
                    }

                    nestableInstance = $('#menuNestable').nestable({
                        maxDepth: 3
                    });

                    const serializeItems = (items) => {
                        return items.map(item => {
                            const children = Array.isArray(item.children) ? serializeItems(item.children) : [];

                            return {
                                id: item.id,
                                ...(children.length ? { children } : {})
                            };
                        });
                    };

                    nestableInstance.on('change', function(e) {
                        var list = e.length ? e : $(e.target);
                        var output = list.nestable('serialize');
                        var serialized = Array.isArray(output) ? serializeItems(output) : [];

                        // Livewire-এর 'menuOrderUpdated' ইভেন্টে ডেটা পাঠান (JSON Circular Structure এরর এড়ানোর জন্য)
                        Livewire.dispatch('menuOrderUpdated', { items: serialized });
                    });
                }

                // পেজ প্রথমবার লোড হলে Nestable চালু করুন
                initializeNestable();

                // Livewire যখন মেনু রিফ্রেশ করে
                Livewire.on('refreshNestable', () => {
                    initializeNestable();
                });

            });
        </script>
    @endpush
</div>
