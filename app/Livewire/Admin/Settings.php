<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;
use App\Models\GeneralSetting;
use App\Support\PermalinkManager;

class Settings extends Component
{
    use WithFileUploads;

    public $tab = 'general_settings';

    public array $availableRoles = [];
    public array $dashboardWidgets = [];
    public array $dashboardVisibility = [];

    public $permalink_structure = PermalinkManager::DEFAULT_STRUCTURE;
    public $custom_permalink_structure;
    public $category_slug_prefix_enabled = true;
    public string $cacheSize = '0 B';

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
            $this->permalink_structure = $settings->permalink_structure ?: PermalinkManager::DEFAULT_STRUCTURE;
            $this->custom_permalink_structure = $settings->custom_permalink_structure;
            $this->category_slug_prefix_enabled = $settings->category_slug_prefix_enabled ?? true;
        }

        if ($this->permalink_structure === PermalinkManager::STRUCTURE_CUSTOM && blank($this->custom_permalink_structure)) {
            $this->permalink_structure = PermalinkManager::DEFAULT_STRUCTURE;
        }

        $this->category_slug_prefix_enabled = (bool) $this->category_slug_prefix_enabled;

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

        $this->refreshCacheStatistics();
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
            Cache::forget('general_settings');
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
            Cache::forget('general_settings');
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

        Cache::forget('general_settings');

        $this->dispatch('showToastr', ['type' => 'success', 'message' => 'Dashboard visibility preferences saved successfully']);
    }

    public function clearAllCache(): void
    {
        Artisan::call('optimize:clear');
        Cache::flush();

        $this->refreshCacheStatistics();

        $this->dispatch('showToastr', ['type' => 'success', 'message' => 'All CMS caches cleared successfully']);
    }

    public function clearCompiledViews(): void
    {
        Artisan::call('view:clear');

        $this->refreshCacheStatistics();

        $this->dispatch('showToastr', ['type' => 'success', 'message' => 'Compiled views refreshed successfully']);
    }

    public function clearConfigCache(): void
    {
        Artisan::call('config:clear');

        $this->refreshCacheStatistics();

        $this->dispatch('showToastr', ['type' => 'success', 'message' => 'Configuration cache cleared successfully']);
    }

    public function clearRouteCache(): void
    {
        Artisan::call('route:clear');

        $this->refreshCacheStatistics();

        $this->dispatch('showToastr', ['type' => 'success', 'message' => 'Route cache cleared successfully']);
    }

    public function clearLogFiles(): void
    {
        $logPath = storage_path('logs');

        if (File::exists($logPath)) {
            collect(File::files($logPath))->each(static function ($file) {
                /** @var \SplFileInfo $file */
                File::delete($file->getPathname());
            });
        }

        $this->refreshCacheStatistics();

        $this->dispatch('showToastr', ['type' => 'success', 'message' => 'System log files cleared successfully']);
    }

    protected function refreshCacheStatistics(): void
    {
        $this->cacheSize = $this->calculateCacheSize();
    }

    protected function calculateCacheSize(): string
    {
        $paths = [
            storage_path('framework/cache'),
            storage_path('framework/views'),
            base_path('bootstrap/cache'),
            storage_path('logs'),
        ];

        $total = 0;

        foreach ($paths as $path) {
            $total += $this->directorySize($path);
        }

        return $this->formatBytes($total);
    }

    protected function directorySize(string $path): int
    {
        if (! File::exists($path)) {
            return 0;
        }

        return collect(File::allFiles($path))->sum(static function ($file) {
            /** @var \SplFileInfo $file */
            return $file->getSize();
        });
    }

    protected function formatBytes(int $bytes, int $precision = 2): string
    {
        if ($bytes <= 0) {
            return '0 B';
        }

        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $power = (int) floor(log($bytes, 1024));
        $power = min($power, count($units) - 1);

        $value = $bytes / (1024 ** $power);

        return number_format($value, $precision) . ' ' . $units[$power];
    }

    public function updatePermalinks(): void
    {
        $availableStructures = array_keys(PermalinkManager::availableStructures());
        $availableStructures[] = PermalinkManager::STRUCTURE_CUSTOM;

        $rules = [
            'permalink_structure' => ['required', Rule::in($availableStructures)],
            'custom_permalink_structure' => ['nullable', 'string'],
            'category_slug_prefix_enabled' => ['boolean'],
        ];

        if ($this->permalink_structure === PermalinkManager::STRUCTURE_CUSTOM) {
            $rules['custom_permalink_structure'][] = function (string $attribute, $value, $fail) {
                $normalized = PermalinkManager::sanitizeCustomStructure($value);

                if ($normalized === '') {
                    $fail('The custom permalink structure cannot be empty.');
                    return;
                }

                $tokens = PermalinkManager::extractTokens($value ?? '');
                $allowed = PermalinkManager::allowedTokens();
                $unknown = collect($tokens)->diff($allowed);

                if ($unknown->isNotEmpty()) {
                    $fail('Unknown placeholder(s): ' . $unknown->implode(', '));
                    return;
                }

                if (in_array('%postname%', $tokens, true) && in_array('%post_id%', $tokens, true)) {
                    $fail('Please use either %postname% or %post_id%, not both together.');
                    return;
                }

                if (! in_array('%postname%', $tokens, true) && ! in_array('%post_id%', $tokens, true)) {
                    $fail('The structure must include %postname% or %post_id% to identify posts.');
                    return;
                }

                if (preg_match('#https?://#i', (string) $value)) {
                    $fail('Please enter only the path portion without the domain.');
                }
            };
        }

        $this->validate($rules, [], [
            'custom_permalink_structure' => 'custom permalink structure',
        ]);

        $structure = $this->permalink_structure;
        $customStructure = null;

        if ($structure === PermalinkManager::STRUCTURE_CUSTOM) {
            $customStructure = PermalinkManager::sanitizeCustomStructure($this->custom_permalink_structure);
            $this->custom_permalink_structure = $customStructure;
        }

        $settings = GeneralSetting::first();

        if (! $settings) {
            $settings = GeneralSetting::create([]);
        }

        $settings->update([
            'permalink_structure' => $structure,
            'custom_permalink_structure' => $customStructure,
            'category_slug_prefix_enabled' => (bool) $this->category_slug_prefix_enabled,
        ]);

        Cache::forget('general_settings');
        Artisan::call('route:clear');

        $this->dispatch('showToastr', ['type' => 'success', 'message' => 'Permalink settings updated successfully']);
    }

    public function getPermalinkPreviewProperty(): string
    {
        $structure = $this->permalink_structure;
        $custom = $structure === PermalinkManager::STRUCTURE_CUSTOM ? $this->custom_permalink_structure : null;

        return PermalinkManager::previewSample($structure, $custom);
    }

    public function render()
    {
        return view('livewire.admin.settings', [
            'availableRoles' => $this->availableRoles,
            'dashboardWidgets' => $this->dashboardWidgets,
            'permalinkOptions' => PermalinkManager::availableStructures(),
            'permalinkTokens' => PermalinkManager::allowedTokens(),
        ]);
    }
}
