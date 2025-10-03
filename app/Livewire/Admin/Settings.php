<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use App\Models\GeneralSetting;

class Settings extends Component
{
    use WithFileUploads;

    public $tab = 'general_settings';

    public array $availableRoles = [];
    public array $dashboardWidgets = [];
    public array $dashboardVisibility = [];

    protected $queryString = [
        'tab' => ['keep' => true]
    ];

    //General settings form properties
    public $site_title,
        $site_email,
        $site_description,
        $site_phone,
        $site_meta_keywords,
        $site_meta_description,
        $site_logo_path,
        $site_favicon_path,
        $site_copyright,
        $site_logo_upload,
        $site_favicon_upload;


    public function selectTab($tab)
    {
        $this->tab = $tab;
    }
    public function mount(Request $request)
    {
        $this->tab = $request->query('tab', $this->tab);

        //Populate General Settings
        $settings = GeneralSetting::take(1)->first();
        if (! is_null($settings)) {
            $this->site_title = $settings->site_title;
            $this->site_email = $settings->site_email;
            $this->site_description = $settings->site_description;
            $this->site_phone = $settings->site_phone;
            $this->site_meta_keywords = $settings->site_meta_keywords;
            $this->site_meta_description = $settings->site_meta_description;
            $this->site_logo_path = $settings->site_logo;
            $this->site_favicon_path = $settings->site_favicon;
            $this->site_copyright = $settings->site_copyright;
        }

        $this->availableRoles = Role::query()->pluck('name')->sort()->values()->toArray();
        $this->dashboardWidgets = collect(config('dashboard.widgets', []))
            ->mapWithKeys(function ($item, $key) {
                return [
                    $key => [
                        'label' => $item['label'] ?? Str::title(str_replace('_', ' ', $key)),
                        'description' => $item['description'] ?? null,
                    ],
                ];
            })->toArray();

        $storedVisibility = $settings?->dashboard_widget_visibility ?? [];

        foreach ($this->dashboardWidgets as $widgetKey => $widgetMeta) {
            $savedRoles = $storedVisibility[$widgetKey] ?? $this->availableRoles;
            $this->dashboardVisibility[$widgetKey] = array_values(array_intersect($this->availableRoles, (array) $savedRoles));
        }
    }

    public function updateSiteInfo()
    {
        $this->validate([
            'site_title' => 'required|string|max:255',
            'site_email' => 'required|email|max:255',
            'site_description' => 'nullable|string|max:500',
            'site_phone' => 'nullable|string|max:50',
            'site_meta_keywords' => 'nullable|string|max:255',
            'site_meta_description' => 'nullable|string|max:500',
            'site_copyright' => 'nullable|string|max:255',
        ]);

        $settings = GeneralSetting::first();
        $data = [
            'site_title' => $this->site_title,
            'site_email' => $this->site_email,
            'site_description' => $this->site_description,
            'site_phone' => $this->site_phone,
            'site_meta_keywords' => $this->site_meta_keywords,
            'site_meta_description' => $this->site_meta_description,
            'site_copyright' => $this->site_copyright,
        ];

        $query = $settings ? $settings->update($data) : GeneralSetting::create($data);

        if ($query) {
            $this->dispatch('showToastr', ['type' => 'success', 'message' => 'General Setting Updated Successfully']);
        } else {
            $this->dispatch('showToastr', ['type' => 'error', 'message' => 'General Setting Not Updated']);
        }
    }

    public function updateBranding()
    {
        $this->validate([
            'site_logo_upload' => 'nullable|image|mimes:png,jpg,jpeg,svg,webp|max:2048',
            'site_favicon_upload' => 'nullable|image|mimes:png,jpg,jpeg,ico,svg,webp|max:1024',
        ]);

        $settings = GeneralSetting::first();

        if (! $settings) {
            $settings = GeneralSetting::create([]);
        }

        $data = [];

        if ($this->site_logo_upload) {
            $path = $this->site_logo_upload->store('settings', 'public');

            if ($this->site_logo_path && Storage::disk('public')->exists($this->site_logo_path)) {
                Storage::disk('public')->delete($this->site_logo_path);
            }

            $data['site_logo'] = $path;
            $this->site_logo_path = $path;
            $this->site_logo_upload = null;
        }

        if ($this->site_favicon_upload) {
            $path = $this->site_favicon_upload->store('settings', 'public');

            if ($this->site_favicon_path && Storage::disk('public')->exists($this->site_favicon_path)) {
                Storage::disk('public')->delete($this->site_favicon_path);
            }

            $data['site_favicon'] = $path;
            $this->site_favicon_path = $path;
            $this->site_favicon_upload = null;
        }

        if (! empty($data)) {
            $settings->update($data);
            $this->dispatch('showToastr', ['type' => 'success', 'message' => 'Branding updated successfully']);
            return;
        }

        $this->dispatch('showToastr', ['type' => 'info', 'message' => 'Please upload a logo or favicon to update branding']);
    }

    public function updateDashboardVisibility(): void
    {
        $settings = GeneralSetting::first();

        if (! $settings) {
            $settings = GeneralSetting::create([]);
        }

        $normalized = [];

        foreach (array_keys($this->dashboardWidgets) as $widgetKey) {
            $selectedRoles = $this->dashboardVisibility[$widgetKey] ?? [];
            $normalized[$widgetKey] = array_values(array_intersect($this->availableRoles, (array) $selectedRoles));
        }

        $settings->update([
            'dashboard_widget_visibility' => $normalized,
        ]);

        $this->dashboardVisibility = $normalized;

        $this->dispatch('showToastr', ['type' => 'success', 'message' => 'Dashboard visibility preferences saved successfully']);
    }

    public function render()
    {
        return view('livewire.admin.settings', [
            'availableRoles' => $this->availableRoles,
            'dashboardWidgets' => $this->dashboardWidgets,
        ]);
    }
}
