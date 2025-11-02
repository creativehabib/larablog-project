<div>
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5>Add Menu Item</h5>
                </div>
                <div class="card-body">
                    <form wire:submit.prevent="addMenuItem">
                        <div class="form-group">
                            <label for="title">Title</label>
                            <input type="text" class="form-control" wire:model="title" placeholder="Enter title">
                            @error('title') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-group">
                            <label for="url">URL</label>
                            <input type="text" class="form-control" wire:model="url" placeholder="https://example.com">
                            @error('url') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <button type="submit" class="btn btn-primary">Add Item</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5>Manage Menu</h5>
                    <div class="form-group mb-0">
                        <label for="menuSelect">Select Menu:</label>
                        <select class="form-control" wire:change="selectMenu($event.target.value)">
                            @foreach($menus as $menu)
                                <option value="{{ $menu->id }}" @if($selectedMenu && $selectedMenu->id == $menu->id) selected @endif>{{ $menu->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="card-body">
                    @if(session()->has('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <div class="dd" id="nestable">
                        <ol class="dd-list">
                            @foreach($selectedMenu->items as $item)
                                <li class="dd-item" data-id="{{ $item->id }}">
                                    <div class="dd-handle">{{ $item->title }}</div>
                                    @if($item->children->isNotEmpty())
                                        <ol class="dd-list">
                                            @foreach($item->children as $child)
                                                <li class="dd-item" data-id="{{ $child->id }}">
                                                    <div class="dd-handle">{{ $child->title }}</div>
                                                </li>
                                            @endforeach
                                        </ol>
                                    @endif
                                </li>
                            @endforeach
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/nestable2/1.6.0/jquery.nestable.min.js"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/nestable2/1.6.0/jquery.nestable.min.css" />

        <script>
            $(document).ready(function() {
                // Nestable চালু করুন
                $('#nestable').nestable({
                    maxDepth: 3 // আপনি কতগুলো সাব-মেনু লেভেল চান
                }).on('change', function(e) {
                    // যখন ড্র্যাগ-এন্ড-ড্রপ করা হয়, তখন এই ফাংশনটি কাজ করে
                    var list = e.length ? e : $(e.target);
                    var output = list.nestable('serialize'); // মেনুর নতুন স্ট্রাকচার JSON ফরম্যাটে নিন

                    // Livewire কম্পোনেন্টের updateMenuOrder মেথডকে কল করুন
                @this.call('updateMenuOrder', output);
                });
            });
        </script>
    @endpush
</div>
