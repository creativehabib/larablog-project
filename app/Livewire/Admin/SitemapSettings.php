<?php

namespace App\Livewire\Admin;

use App\Models\GeneralSetting;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class SitemapSettings extends Component
{
    public bool $sitemap_enabled = true;

    public int $sitemap_items_per_page = 1000;

    public bool $sitemap_enable_index_now = false;

    public string $sitemapUrl;

    public function mount(): void
    {
        $settings = GeneralSetting::first();

        $this->sitemap_enabled = $settings?->sitemap_enabled ?? true;
        $this->sitemap_items_per_page = $settings?->sitemap_items_per_page ?? 1000;
        $this->sitemap_enable_index_now = $settings?->sitemap_enable_index_now ?? false;
        $this->sitemapUrl = route('sitemap.index');
    }

    public function save(): void
    {
        $validated = $this->validate([
            'sitemap_enabled' => ['boolean'],
            'sitemap_items_per_page' => ['required', 'integer', 'min:1', 'max:50000'],
            'sitemap_enable_index_now' => ['boolean'],
        ]);

        $validated['sitemap_enabled'] = (bool) $validated['sitemap_enabled'];
        $validated['sitemap_enable_index_now'] = (bool) $validated['sitemap_enable_index_now'];

        $settings = GeneralSetting::first();

        if (! $settings) {
            $settings = GeneralSetting::create([]);
        }

        $settings->update($validated);

        Cache::forget('general_settings');

        $this->dispatch('showToastr', type: 'success', message: 'Sitemap settings updated successfully.');
    }

    public function render()
    {
        return view('livewire.admin.sitemap-settings', [
            'sitemapUrl' => $this->sitemapUrl,
        ]);
    }
}
