<?php

namespace App\Livewire\Admin;

use App\Models\Category;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Post;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class MenuManagement extends Component
{
    public array $menus = [];
    public ?array $selectedMenu = null;
    public ?int $selectedMenuId = null;

    // মেনু গ্রুপ CRUD
    public string $newMenuName = '';
    public string $newMenuLocation = '';
    public string $editMenuName = '';
    public string $editMenuLocation = '';

    // মেনু আইটেম যোগ করা
    public string $customTitle = '';
    public string $customUrl = '';
    public string $customTarget = '_self';
    public array $availableTargets = [
        '_self' => 'Same tab',
        '_blank' => 'New tab',
    ];
    public array $selectedCategories = [];
    public array $selectedPosts = [];
    public string $categorySearch = '';
    public string $postSearch = '';
    public string $activeTab = 'custom-link';
    public array $locationSuggestions = [];

    // === (সমাধান) ইডিটিং-এর জন্য আলাদা প্রপার্টি ===
    public ?int $editingItemId = null;
    public string $editTitle = '';
    public string $editUrl = '';
    public string $editTarget = '_self';


    public function mount(): void
    {
         $this->ensureAuthorized('menu.view');

        if (defined(Menu::class . '::AVAILABLE_LOCATIONS')) {
            $this->locationSuggestions = Menu::AVAILABLE_LOCATIONS;
        }

        $this->customTarget = array_key_first($this->availableTargets);
        $this->loadMenus();

        if(count($this->menus) > 0) {
            $this->selectMenu($this->menus[0]['id']);
        }
    }

    public function updatedSelectedMenuId($value): void
    {
        $this->selectedMenuId = filled($value) ? (int) $value : null;
        if ($this->selectedMenuId) {
            $this->selectMenu($this->selectedMenuId);
            return;
        }
        $this->selectedMenu = null;
        $this->editMenuName = '';
        $this->editMenuLocation = '';
        $this->dispatch('refreshNestable');
    }

    public function selectMenu(int $menuId): void
    {
        $this->loadMenus($menuId);
        $this->cancelEditing();
    }

    public function createMenu(): void
    {
         $this->ensureAuthorized('menu.create');
        $validated = $this->validate([
            'newMenuName' => ['required', 'string', 'max:255'],
            'newMenuLocation' => ['required', 'string', 'max:255', 'unique:menus,location'],
        ]);
        $menu = Menu::create([
            'name' => $validated['newMenuName'],
            'location' => $validated['newMenuLocation'],
        ]);
        $this->reset('newMenuName', 'newMenuLocation');
         forget_menu_cache($menu->location);
        $this->loadMenus($menu->id);
        session()->flash('success', 'Menu created successfully.');
    }

    public function updateMenu(): void
    {
        if (! $this->selectedMenuId) return;
         $this->ensureAuthorized('menu.edit');
        $this->validate([
            'editMenuName' => ['required', 'string', 'max:255'],
            'editMenuLocation' => ['required', 'string', 'max:255', 'unique:menus,location,' . $this->selectedMenuId],
        ]);
        $menu = Menu::findOrFail($this->selectedMenuId);
        $previousLocation = $menu->location;
        $menu->update([
            'name' => $this->editMenuName,
            'location' => $this->editMenuLocation,
        ]);
         forget_menu_cache($previousLocation);
         forget_menu_cache($menu->location);
        $this->loadMenus($menu->id);
        session()->flash('success', 'Menu updated successfully.');
    }

    public function deleteMenu(int $menuId): void
    {
         $this->ensureAuthorized('menu.delete');
        $menu = Menu::findOrFail($menuId);
        $location = $menu->location;
        $menu->delete();
         forget_menu_cache($location);
        $this->loadMenus();
        session()->flash('success', 'Menu deleted successfully.');
    }

    public function addCustomLink(): void
    {
        if (! $this->ensureSelectedMenu()) return;
         $this->ensureAuthorized('menu.edit');

        $this->validate([
            'customTitle' => ['required', 'string', 'max:255'],
            'customUrl' => ['required', 'string', 'max:2048'], // 'url' থেকে 'string' করা হয়েছে
            'customTarget' => ['required', 'in:' . implode(',', array_keys($this->availableTargets))],
        ]);

        $this->createMenuItem($this->customTitle, $this->customUrl, $this->customTarget);
        $this->reset('customTitle', 'customUrl');
        $this->customTarget = array_key_first($this->availableTargets);
        $this->afterMenuItemsMutated('Menu item added successfully.');
    }

    public function addCategoriesToMenu(): void
    {
        if (! $this->ensureSelectedMenu()) return;
         $this->ensureAuthorized('menu.edit');
        $this->validate([
            'selectedCategories' => ['required', 'array', 'min:1'],
            'selectedCategories.*' => ['integer', 'exists:categories,id'],
        ]);
        $categories = Category::whereIn('id', $this->selectedCategories)->get();
        foreach ($categories as $category) {
            $this->createMenuItem($category->name, route('categories.show', $category));
        }
        $this->selectedCategories = [];
        $this->afterMenuItemsMutated('Selected categories added to the menu.');
    }

    public function addPostsToMenu(): void
    {
        if (! $this->ensureSelectedMenu()) return;
         $this->ensureAuthorized('menu.edit');
        $this->validate([
            'selectedPosts' => ['required', 'array', 'min:1'],
            'selectedPosts.*' => ['integer', 'exists:posts,id'],
        ]);
        $posts = Post::whereIn('id', $this->selectedPosts)->get();
        foreach ($posts as $post) {
            $this->createMenuItem($post->title, post_permalink($post));
        }
        $this->selectedPosts = [];
        $this->afterMenuItemsMutated('Selected posts added to the menu.');
    }

    public function startEditing(int $itemId): void
    {
        if (! $this->ensureSelectedMenu()) return;
         $this->ensureAuthorized('menu.edit');

        $item = MenuItem::where('menu_id', $this->selectedMenuId)->findOrFail($itemId);
        $this->editingItemId = (int) $item->id;

        $this->editTitle = $item->title;
        $this->editUrl = $item->url;
        $this->editTarget = $item->target ?? array_key_first($this->availableTargets);

        $this->resetValidation();
    }

    public function cancelEditing(): void
    {
        $this->editingItemId = null;
        $this->reset('editTitle', 'editUrl', 'editTarget');
        $this->editTarget = array_key_first($this->availableTargets);
        $this->resetValidation();
    }

    public function updateMenuItem(?int $itemId = null): void
    {
        $itemId = $itemId ?? $this->editingItemId;
        $itemId = $itemId ? (int) $itemId : null;

        if (! $itemId || ! $this->ensureSelectedMenu()) {
            return;
        }

        $this->editingItemId = $itemId;

         $this->ensureAuthorized('menu.edit');

        // $editingItem অ্যারের বদলে সরাসরি প্রপার্টি ভ্যালিডেট করুন
        $validated = $this->validate([
            'editTitle' => ['required', 'string', 'max:255'],
            'editUrl' => ['required', 'string', 'max:2048'],
            'editTarget' => ['required', 'in:' . implode(',', array_keys($this->availableTargets))],
        ]);

        $item = MenuItem::where('menu_id', $this->selectedMenuId)->findOrFail($itemId);

        $item->update([
            'title' => $validated['editTitle'],
            'url' => $validated['editUrl'],
            'target' => $validated['editTarget'],
        ]);

        $this->cancelEditing();
        $this->afterMenuItemsMutated('Menu item updated successfully.');
    }

    public function deleteMenuItem(int $itemId): void
    {
        if (! $this->ensureSelectedMenu()) return;
         $this->ensureAuthorized('menu.delete');
        $item = MenuItem::where('menu_id', $this->selectedMenuId)->findOrFail($itemId);
        $item->delete();
        $this->afterMenuItemsMutated('Menu item removed successfully.');
    }

    #[On('menuOrderUpdated')]
    public function updateMenuOrder($items): void
    {
        if (! $this->ensureSelectedMenu()) return;
        // $this->ensureAuthorized('menu.edit');
        $items = $this->normalizeMenuOrderPayload($items);
        if (empty($items)) return;
        DB::transaction(fn () => $this->persistOrder($items));
        $this->afterMenuItemsMutated('Menu order updated successfully.');
    }

    #[Computed]
    public function categoryOptions()
    {
        return Category::query()
            ->orderBy('name')
            ->when($this->categorySearch, fn ($q) => $q->where('name', 'like', '%' . $this->categorySearch . '%'))
            ->take(50)
            ->get(['id', 'name', 'slug']);
    }

    #[Computed]
    public function postOptions()
    {
        return Post::query()
            ->orderByDesc('created_at')
            ->when($this->postSearch, fn ($q) => $q->where('title', 'like', '%' . $this->postSearch . '%'))
            ->take(50)
            ->get(['id', 'title', 'slug']);
    }

    public function render()
    {
        return view('livewire.admin.menu-management');
    }

    protected function loadMenus(?int $selectedMenuId = null): void
    {
        $menus = Menu::query()
            ->with(['items' => fn ($q) => $q->whereNull('parent_id')->orderBy('order')->with('children')])
            ->orderBy('name')
            ->get();

        $this->menus = $menus->map(function (Menu $menu) {
            return [
                'id' => $menu->id,
                'name' => $menu->name,
                'location' => $menu->location,
                'items' => $this->transformItems($menu->items),
            ];
        })->toArray();

        if (empty($this->menus)) {
            $this->selectedMenu = null; $this->selectedMenuId = null; $this->editMenuName = ''; $this->editMenuLocation = '';
            $this->activeTab = 'custom-link';
            $this->dispatch('refreshNestable');
            return;
        }

        $menusCollection = collect($this->menus);
        $selected = $menusCollection->firstWhere('id', $selectedMenuId) ?? $menusCollection->first();

        $this->selectedMenu = $selected;
        $this->selectedMenuId = $selected['id'];
        $this->editMenuName = $selected['name'];
        $this->editMenuLocation = $selected['location'];
        $this->dispatch('refreshNestable');
    }

    protected function transformItems($items): array
    {
        return collect($items)->map(function ($item) {
            $children = $item instanceof MenuItem ? $item->children : ($item['children'] ?? []);
            return [
                'id' => $item instanceof MenuItem ? $item->id : $item['id'],
                'title' => $item instanceof MenuItem ? $item->title : $item['title'],
                'url' => $item instanceof MenuItem ? $item->url : $item['url'],
                'target' => $item instanceof MenuItem ? $item->target : ($item['target'] ?? '_self'),
                'children' => $this->transformItems($children),
            ];
        })->toArray();
    }

    protected function ensureAuthorized(string $permission): void
    {
         abort_unless(auth()->user()?->can($permission), Response::HTTP_FORBIDDEN);
    }

    protected function ensureSelectedMenu(): bool
    {
        if (! $this->selectedMenuId) {
            $this->addError('selectedMenuId', 'Create or select a menu before performing this action.');
            return false;
        }
        return true;
    }

    protected function createMenuItem(string $title, string $url, string $target = '_self', ?int $parentId = null): MenuItem
    {
        return MenuItem::create([
            'menu_id' => $this->selectedMenuId,
            'title' => $title,
            'url' => $url,
            'target' => $target,
            'parent_id' => $parentId,
            'order' => $this->nextOrder($parentId),
        ]);
    }

    protected function nextOrder(?int $parentId = null): int
    {
        $query = MenuItem::query()->where('menu_id', $this->selectedMenuId);
        $query->where(fn ($q) => $parentId ? $q->where('parent_id', $parentId) : $q->whereNull('parent_id'));
        return ((int) $query->max('order')) + 1;
    }

    protected function persistOrder(array $items, ?int $parentId = null): void
    {
        foreach (array_values($items) as $index => $item) {
            $itemId = $item['id'] ?? null;
            $children = $item['children'] ?? [];
            if (! $itemId) continue;
            $menuItem = MenuItem::where('menu_id', $this->selectedMenuId)->find($itemId);
            if (! $menuItem) continue;
            $menuItem->update(['order' => $index + 1, 'parent_id' => $parentId]);
            if (! empty($children)) {
                $this->persistOrder($children, $menuItem->id);
            }
        }
    }

    protected function normalizeMenuOrderPayload($payload): array
    {
        if (is_string($payload)) $payload = json_decode($payload, true);
        if (is_object($payload)) $payload = (array) $payload;
        if (Arr::isAssoc($payload) && isset($payload['items'])) $payload = $payload['items'];
        return is_array($payload) ? $payload : [];
    }

    protected function afterMenuItemsMutated(string $message): void
    {
        $location = $this->selectedMenu['location'] ?? null;
        if ($location) {
             forget_menu_cache($location);
        }
        $this->loadMenus($this->selectedMenuId);
        session()->flash('success', $message);
    }
}
