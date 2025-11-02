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
    /**
     * Cached menus with nested items for the management UI.
     *
     * @var array<int, array<string, mixed>>
     */
    public array $menus = [];

    /**
     * Currently selected menu represented as array for easier serialization.
     */
    public ?array $selectedMenu = null;

    public ?int $selectedMenuId = null;

    public string $newMenuName = '';

    public string $newMenuLocation = '';

    public string $editMenuName = '';

    public string $editMenuLocation = '';

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

    public ?int $editingItemId = null;

    public array $editingItem = [
        'title' => '',
        'url' => '',
        'target' => '_self',
    ];

    public array $locationSuggestions = Menu::AVAILABLE_LOCATIONS;

    public function mount(): void
    {
        $this->ensureAuthorized('menu.view');

        $this->customTarget = array_key_first($this->availableTargets);

        $this->loadMenus();
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
        if (! $this->selectedMenuId) {
            return;
        }

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
        if (! $this->ensureSelectedMenu()) {
            return;
        }

        $this->ensureAuthorized('menu.edit');

        $this->validate([
            'customTitle' => ['required', 'string', 'max:255'],
            'customUrl' => ['required', 'url', 'max:2048'],
            'customTarget' => ['required', 'in:' . implode(',', array_keys($this->availableTargets))],
        ]);

        $this->createMenuItem($this->customTitle, $this->customUrl, $this->customTarget);

        $this->reset('customTitle', 'customUrl');
        $this->customTarget = array_key_first($this->availableTargets);

        $this->afterMenuItemsMutated('Menu item added successfully.');
    }

    public function addCategoriesToMenu(): void
    {
        if (! $this->ensureSelectedMenu()) {
            return;
        }

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
        if (! $this->ensureSelectedMenu()) {
            return;
        }

        $this->ensureAuthorized('menu.edit');

        $this->validate([
            'selectedPosts' => ['required', 'array', 'min:1'],
            'selectedPosts.*' => ['integer', 'exists:posts,id'],
        ]);

        $posts = Post::whereIn('id', $this->selectedPosts)->get();

        foreach ($posts as $post) {
            $this->createMenuItem($post->title, route('posts.show', $post));
        }

        $this->selectedPosts = [];

        $this->afterMenuItemsMutated('Selected posts added to the menu.');
    }

    public function startEditing(int $itemId): void
    {
        if (! $this->ensureSelectedMenu()) {
            return;
        }

        $this->ensureAuthorized('menu.edit');

        $item = MenuItem::where('menu_id', $this->selectedMenuId)->findOrFail($itemId);

        $this->editingItemId = $item->id;
        $this->editingItem = [
            'title' => $item->title,
            'url' => $item->url,
            'target' => $item->target,
        ];
    }

    public function cancelEditing(): void
    {
        $this->editingItemId = null;
        $this->editingItem = [
            'title' => '',
            'url' => '',
            'target' => array_key_first($this->availableTargets),
        ];
    }

    public function updateMenuItem(): void
    {
        if (! $this->editingItemId || ! $this->ensureSelectedMenu()) {
            return;
        }

        $this->ensureAuthorized('menu.edit');

        $this->validate([
            'editingItem.title' => ['required', 'string', 'max:255'],
            'editingItem.url' => ['required', 'url', 'max:2048'],
            'editingItem.target' => ['required', 'in:' . implode(',', array_keys($this->availableTargets))],
        ]);

        $item = MenuItem::where('menu_id', $this->selectedMenuId)->findOrFail($this->editingItemId);

        $item->update($this->editingItem);

        $this->cancelEditing();

        $this->afterMenuItemsMutated('Menu item updated successfully.');
    }

    public function deleteMenuItem(int $itemId): void
    {
        if (! $this->ensureSelectedMenu()) {
            return;
        }

        $this->ensureAuthorized('menu.delete');

        $item = MenuItem::where('menu_id', $this->selectedMenuId)->findOrFail($itemId);
        $item->delete();

        $this->afterMenuItemsMutated('Menu item removed successfully.');
    }

    #[On('menuOrderUpdated')]
    public function updateMenuOrder($items): void
    {
        if (! $this->ensureSelectedMenu()) {
            return;
        }

        $this->ensureAuthorized('menu.edit');

        $items = $this->normalizeMenuOrderPayload($items);

        if (empty($items)) {
            return;
        }

        DB::transaction(function () use ($items) {
            $this->persistOrder($items);
        });

        $this->afterMenuItemsMutated('Menu order updated successfully.');
    }

    #[Computed]
    public function categoryOptions()
    {
        return Category::query()
            ->orderBy('name')
            ->when($this->categorySearch, function ($query) {
                $query->where('name', 'like', '%' . $this->categorySearch . '%');
            })
            ->take(50)
            ->get(['id', 'name', 'slug']);
    }

    #[Computed]
    public function postOptions()
    {
        return Post::query()
            ->orderByDesc('created_at')
            ->when($this->postSearch, function ($query) {
                $query->where('title', 'like', '%' . $this->postSearch . '%');
            })
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
            ->with(['items' => function ($query) {
                $query->whereNull('parent_id')
                    ->ordered()
                    ->with('children');
            }])
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
            $this->selectedMenu = null;
            $this->selectedMenuId = null;
            $this->editMenuName = '';
            $this->editMenuLocation = '';
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

        if ($parentId) {
            $query->where('parent_id', $parentId);
        } else {
            $query->whereNull('parent_id');
        }

        $max = (int) $query->max('order');

        return $max + 1;
    }

    protected function persistOrder(array $items, ?int $parentId = null): void
    {
        $items = array_values($items);

        foreach ($items as $index => $item) {
            $itemId = null;
            $children = [];

            if (is_object($item)) {
                $item = (array) $item;
            }

            if (is_array($item)) {
                $itemId = $item['id'] ?? null;

                if (! empty($item['children']) && is_array($item['children'])) {
                    $children = $item['children'];
                }
            } elseif (is_numeric($item)) {
                $itemId = (int) $item;
            }

            if (! $itemId) {
                continue;
            }

            $menuItem = MenuItem::where('menu_id', $this->selectedMenuId)->find($itemId);

            if (! $menuItem) {
                continue;
            }

            $menuItem->update([
                'order' => $index + 1,
                'parent_id' => $parentId,
            ]);

            if (! empty($children)) {
                if (is_object($children)) {
                    $children = (array) $children;
                }

                $this->persistOrder($children, $menuItem->id);
            }
        }
    }

    protected function normalizeMenuOrderPayload($payload): array
    {
        if ($payload instanceof \Illuminate\Support\Collection) {
            $payload = $payload->toArray();
        }

        if (is_string($payload)) {
            $decoded = json_decode($payload, true);

            if (json_last_error() === JSON_ERROR_NONE) {
                $payload = $decoded;
            }
        }

        if (is_object($payload)) {
            $payload = (array) $payload;
        }

        if (is_array($payload) && Arr::isAssoc($payload) && isset($payload['items']) && is_array($payload['items'])) {
            $payload = $payload['items'];
        }

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
