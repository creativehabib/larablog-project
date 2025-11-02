<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Menu;
use App\Models\MenuItem;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        $primaryMenu = Menu::firstOrCreate(
            ['location' => 'primary'],
            ['name' => 'Primary Navigation']
        );

        if (! $primaryMenu->items()->exists()) {
            $order = 1;

            MenuItem::create([
                'menu_id' => $primaryMenu->id,
                'title' => 'প্রচ্ছদ',
                'url' => route('home'),
                'target' => '_self',
                'order' => $order++,
            ]);

            $featuredCategories = Category::query()
                ->orderBy('name')
                ->take(4)
                ->get();

            foreach ($featuredCategories as $category) {
                MenuItem::create([
                    'menu_id' => $primaryMenu->id,
                    'title' => $category->name,
                    'url' => route('categories.show', $category),
                    'target' => '_self',
                    'order' => $order++,
                ]);
            }

            MenuItem::create([
                'menu_id' => $primaryMenu->id,
                'title' => 'মতামত জরিপ',
                'url' => route('polls.index'),
                'target' => '_self',
                'order' => $order++,
            ]);

            MenuItem::create([
                'menu_id' => $primaryMenu->id,
                'title' => 'আরএসএস ফিড',
                'url' => route('feed'),
                'target' => '_self',
                'order' => $order,
            ]);
        }

        $footerMenu = Menu::firstOrCreate(
            ['location' => 'footer'],
            ['name' => 'Footer Links']
        );

        if (! $footerMenu->items()->exists()) {
            $order = 1;

            $contactEmail = general_settings('site_email') ?? config('mail.from.address', 'info@example.com');

            MenuItem::create([
                'menu_id' => $footerMenu->id,
                'title' => 'প্রচ্ছদ',
                'url' => route('home'),
                'target' => '_self',
                'order' => $order++,
            ]);

            MenuItem::create([
                'menu_id' => $footerMenu->id,
                'title' => 'সাইটম্যাপ',
                'url' => route('sitemap.index'),
                'target' => '_self',
                'order' => $order++,
            ]);

            MenuItem::create([
                'menu_id' => $footerMenu->id,
                'title' => 'মতামত জরিপ',
                'url' => route('polls.index'),
                'target' => '_self',
                'order' => $order++,
            ]);

            MenuItem::create([
                'menu_id' => $footerMenu->id,
                'title' => 'যোগাযোগ',
                'url' => 'mailto:' . $contactEmail,
                'target' => '_self',
                'order' => $order,
            ]);
        }

        forget_menu_cache();
    }
}
