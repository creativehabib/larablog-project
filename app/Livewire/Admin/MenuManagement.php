<?php

namespace App\Http\Livewire\Admin;

use App\Models\Menu;
use App\Models\MenuItem;
use Livewire\Component;

class MenuManagement extends Component
{
    public $menus;
    public $selectedMenu;
    public $menuItems = [];

    // নতুন আইটেম যোগ করার জন্য
    public $title, $url;

    public function mount()
    {
        $this->menus = Menu::all();
        // ডিফল্টভাবে প্রথম মেনুটি লোড করুন
        if ($this->menus->isNotEmpty()) {
            $this->selectMenu($this->menus->first()->id);
        }
    }

    public function selectMenu($menuId)
    {
        $this->selectedMenu = Menu::with('items.children')->find($menuId);
        $this->menuItems = $this->selectedMenu->items->toArray();
    }

    public function addMenuItem()
    {
        $this->validate([
            'title' => 'required',
            'url' => 'required|url',
        ]);

        MenuItem::create([
            'menu_id' => $this->selectedMenu->id,
            'title' => $this->title,
            'url' => $this->url,
            'order' => MenuItem::where('menu_id', $this->selectedMenu->id)->whereNull('parent_id')->max('order') + 1,
        ]);

        $this->reset('title', 'url');
        $this->selectMenu($this->selectedMenu->id); // মেনু রিফ্রেশ করুন
    }

    public function updateMenuOrder($items)
    {
        $this->updateChildrenOrder($items);
        session()->flash('success', 'Menu order updated successfully!');
        $this->selectMenu($this->selectedMenu->id);
    }

    // এটি একটি Recursive ফাংশন যা সাব-মেনু আপডেট করে
    private function updateChildrenOrder($items, $parentId = null)
    {
        foreach ($items as $index => $item) {
            MenuItem::find($item['id'])->update([
                'order' => $index + 1,
                'parent_id' => $parentId
            ]);

            if (isset($item['children']) && count($item['children']) > 0) {
                $this->updateChildrenOrder($item['children'], $item['id']);
            }
        }
    }

    public function render()
    {
        return view('livewire.admin.menu-management');
    }
}
