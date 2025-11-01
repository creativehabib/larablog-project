<div>
    @php
        $navItems = [
            'general_settings' => ['icon' => 'user', 'label' => 'General Settings'],
            'logo_favicon' => ['icon' => 'settings', 'label' => 'Logo & Favicon'],
            'dashboard_visibility' => ['icon' => 'eye', 'label' => 'Dashboard Visibility'],
            'permalinks' => ['icon' => 'link', 'label' => 'Permalinks'],
            'cache_management' => ['icon' => 'broom', 'label' => 'Cache Management'],
            'security_settings' => ['icon' => 'shield', 'label' => 'Security'],
            'notification' => ['icon' => 'bell', 'label' => 'Notification'],
            'billing' => ['icon' => 'credit-card', 'label' => 'Billing'],
        ];
    @endphp

    <header class="page-title-bar">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item active">
                    <a href="#"><i class="breadcrumb-icon fa fa-angle-left mr-2"></i> Settings</a>
                </li>
            </ol>
        </nav>
    </header>
    <div class="page-section">
        <div class="row">
            <div class="col-md-3 d-none d-md-block">
                <div class="card">
                    <div class="card-body">
                        <nav class="nav flex-column nav-pills nav-gap-y-1">
                            @foreach ($navItems as $key => $item)
                                <a href="#" wire:click.prevent="selectTab('{{ $key }}')" class="nav-item nav-link has-icon nav-link-faded {{ $tab == $key ? 'active' : '' }}">
                                    <i class="fa-solid fa-{{ $item['icon'] }}"></i> {{ $item['label'] }}
                                </a>
                            @endforeach
                        </nav>
                    </div>
                </div>
            </div>

            <div class="col-md-9">
                <div class="card">
                    <div class="card-header border-bottom mb-3 d-flex d-md-none">
                        <ul class="nav nav-tabs card-header-tabs nav-gap-x-1" role="tablist">
                            @foreach ($navItems as $key => $item)
                                <li class="nav-item">
                                    <a href="#" wire:click.prevent="selectTab('{{ $key }}')" class="nav-link has-icon {{ $tab == $key ? 'active' : '' }}">
                                        <i class="fa-solid fa-{{ $item['icon'] }}"></i>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="card-body tab-content">
                        <div class="tab-pane {{ $tab == 'general_settings' ? 'active show' : '' }}" id="general_settings">
                            <h6>GENERAL SETTINGS</h6>
                            <hr>
                            <form wire:submit.prevent="updateSiteInfo()">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for=""><b>Site title</b></label>
                                            <input type="text" class="form-control" wire:model.defer="site_title" placeholder="Enter site title">
                                            @error('site_title')<span class="text-danger">{{ $message }}</span>@enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for=""><b>Site email</b></label>
                                            <input type="email" class="form-control" wire:model.defer="site_email" placeholder="Enter site email">
                                            @error('site_email')<span class="text-danger">{{ $message }}</span>@enderror
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for=""><b>Site description</b> <small>(Optional)</small></label>
                                            <textarea class="form-control" rows="3" wire:model.defer="site_description" placeholder="Write a short description about your site..."></textarea>
                                            @error('site_description')<span class="text-danger">{{ $message }}</span>@enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for=""><b>Site phone number</b></label>
                                            <input type="text" class="form-control" wire:model.defer="site_phone" placeholder="Enter site contact phone">
                                            @error('site_phone')<span class="text-danger">{{ $message }}</span>@enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for=""><b>Site Meta keywords</b> <small>(Optional)</small></label>
                                            <input type="text" class="form-control" wire:model.defer="site_meta_keywords" placeholder="Eg: ecommerce, free api, laravel">
                                            @error('site_meta_keywords')<span class="text-danger">{{ $message }}</span>@enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for=""><b>Site Meta Description</b> <small>(Optional)</small></label>
                                    <textarea class="form-control" cols="4" rows="4" wire:model.defer="site_meta_description" placeholder="Type site meta description..."></textarea>
                                    @error('site_meta_description')<span class="text-danger">{{ $message }}</span>@enderror
                                </div>
                                <div class="form-group">
                                    <label for=""><b>Site Copyright text</b> <small>(Optional)</small></label>
                                    <input type="text" class="form-control" wire:model.defer="site_copyright" placeholder="Eg: © {{ date('Y') }} LaraBlog. All rights reserved.">
                                    @error('site_copyright')<span class="text-danger">{{ $message }}</span>@enderror
                                </div>
                                <button type="submit" class="btn btn-primary">Save changes</button>
                            </form>
                        </div>

                        <div class="tab-pane {{ $tab == 'logo_favicon' ? 'active show' : '' }}" id="logo_favicon">
                            <h6>LOGO & FAVICON</h6>
                            <hr>
                            <form wire:submit.prevent="updateBranding()">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for=""><b>Site logo</b></label>
                                            <input type="file" class="form-control-file" wire:model="site_logo_upload" accept="image/*">
                                            <small class="form-text text-muted">Upload PNG, JPG, SVG or WEBP image up to 2MB.</small>
                                            @error('site_logo_upload')<span class="text-danger">{{ $message }}</span>@enderror
                                        </div>
                                        @if ($site_logo_upload)
                                            <div class="border rounded p-2 text-center">
                                                <small class="d-block text-muted mb-2">Preview</small>
                                                <img src="{{ $site_logo_upload->temporaryUrl() }}" alt="Site logo preview" class="img-fluid" style="max-height: 120px;">
                                            </div>
                                        @elseif ($site_logo_path)
                                            <div class="border rounded p-2 text-center">
                                                <small class="d-block text-muted mb-2">Current logo</small>
                                                <img src="{{ asset('storage/' . $site_logo_path) }}" alt="Current site logo" class="img-fluid" style="max-height: 120px;">
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for=""><b>Site favicon</b></label>
                                            <input type="file" class="form-control-file" wire:model="site_favicon_upload" accept="image/*">
                                            <small class="form-text text-muted">Upload PNG, JPG, ICO, SVG or WEBP image up to 1MB.</small>
                                            @error('site_favicon_upload')<span class="text-danger">{{ $message }}</span>@enderror
                                        </div>
                                        @if ($site_favicon_upload)
                                            <div class="border rounded p-2 text-center">
                                                <small class="d-block text-muted mb-2">Preview</small>
                                                <img src="{{ $site_favicon_upload->temporaryUrl() }}" alt="Site favicon preview" class="img-fluid" style="max-height: 80px; width: auto;">
                                            </div>
                                        @elseif ($site_favicon_path)
                                            <div class="border rounded p-2 text-center">
                                                <small class="d-block text-muted mb-2">Current favicon</small>
                                                <img src="{{ asset('storage/' . $site_favicon_path) }}" alt="Current site favicon" class="img-fluid" style="max-height: 80px; width: auto;">
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary">Update branding</button>
                            </form>
                        </div>

                        <div class="tab-pane {{ $tab == 'dashboard_visibility' ? 'active show' : '' }}" id="dashboard_visibility">
                            <h6>DASHBOARD VISIBILITY</h6>
                            <hr>
                            @if (empty($availableRoles))
                                <p class="text-muted mb-0">Please create at least one role to configure dashboard visibility.</p>
                            @else
                                <form wire:submit.prevent="updateDashboardVisibility">
                                    <p class="text-muted small">নির্বাচিত রোলগুলোই সংশ্লিষ্ট ড্যাশবোর্ড কার্ড দেখতে পারবে। Admin রোল সর্বদা সব কার্ড দেখতে পাবে।</p>
                                    <div class="table-responsive">
                                        <table class="table table-bordered mb-4">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th style="min-width: 220px;">ড্যাশবোর্ড আইটেম</th>
                                                    <th>যে রোলগুলো দেখতে পাবে</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($dashboardWidgets as $widgetKey => $widget)
                                                    <tr>
                                                        <td>
                                                            <div class="font-weight-semibold">{{ $widget['label'] }}</div>
                                                            @if (! empty($widget['description']))
                                                                <div class="text-muted small">{{ $widget['description'] }}</div>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <div class="d-flex flex-wrap">
                                                                @foreach ($availableRoles as $roleName)
                                                                    @php
                                                                        $inputId = 'widget-' . $widgetKey . '-' . \Illuminate\Support\Str::slug($roleName);
                                                                    @endphp
                                                                    <div class="custom-control custom-checkbox mr-3 mb-2">
                                                                        <input type="checkbox" class="custom-control-input" id="{{ $inputId }}" wire:model.defer="dashboardVisibility.{{ $widgetKey }}" value="{{ $roleName }}">
                                                                        <label class="custom-control-label" for="{{ $inputId }}">{{ $roleName }}</label>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Save visibility</button>
                                </form>
                            @endif
                        </div>

                        <div class="tab-pane {{ $tab == 'permalinks' ? 'active show' : '' }}" id="permalinks">
                            <h6>PERMALINK SETTINGS</h6>
                            <hr>
                            <form wire:submit.prevent="updatePermalinks">
                                <p class="text-muted small">আপনার পোস্টের URL কোন ফরম্যাটে থাকবে তা নির্বাচন করুন। এসইও বান্ধব URL-এর জন্য "Post name" বা কাস্টম স্ট্রাকচার ব্যবহার করুন।</p>

                                <div class="mb-4">
                                    @foreach ($permalinkOptions as $key => $option)
                                        @php
                                            $inputId = 'permalink-' . $key;
                                            $sampleUrl = \App\Support\PermalinkManager::previewSample($key);
                                        @endphp
                                        <div class="custom-control custom-radio mb-3">
                                            <input type="radio" id="{{ $inputId }}" class="custom-control-input" wire:model="permalink_structure" value="{{ $key }}">
                                            <label class="custom-control-label" for="{{ $inputId }}">
                                                <span class="font-weight-semibold d-block">{{ $option['label'] }}</span>
                                                <span class="text-muted small">{{ $sampleUrl }}</span>
                                            </label>
                                        </div>
                                    @endforeach
                                    <div class="custom-control custom-radio">
                                        <input type="radio" id="permalink-custom" class="custom-control-input" wire:model="permalink_structure" value="custom">
                                        <label class="custom-control-label" for="permalink-custom">
                                            <span class="font-weight-semibold d-block">Custom Structure</span>
                                            <span class="text-muted small">নিজের ইচ্ছামতো URL প্যাটার্ন ব্যবহার করুন (যেমন: /news/%year%/%postname%).</span>
                                        </label>
                                    </div>
                                    @error('permalink_structure')<span class="text-danger d-block mt-2">{{ $message }}</span>@enderror
                                </div>

                                @if ($permalink_structure === 'custom')
                                    <div class="form-group">
                                        <label for="custom-permalink"><b>Custom structure</b></label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">{{ rtrim(url('/'), '/') }}/</span>
                                            </div>
                                            <input type="text" id="custom-permalink" class="form-control" wire:model.lazy="custom_permalink_structure" placeholder="%category%/%postname%">
                                        </div>
                                        <small class="form-text text-muted">সম্ভাব্য ট্যাগসমূহ: {{ implode(', ', $permalinkTokens) }}</small>
                                        @error('custom_permalink_structure')<span class="text-danger">{{ $message }}</span>@enderror
                                    </div>
                                @endif

                                <div class="form-group">
                                    <label for="category-prefix-toggle"><b>Category URL prefix</b></label>
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="category-prefix-toggle" wire:model.defer="category_slug_prefix_enabled">
                                        <label class="custom-control-label" for="category-prefix-toggle">{{ $category_slug_prefix_enabled ? 'Category prefix enabled' : 'Category prefix disabled' }}</label>
                                    </div>
                                    <small class="form-text text-muted">এই অপশন চালু থাকলে ক্যাটাগরি স্লাগের আগে "category" যোগ হবে (যেমন: /category/news)। বন্ধ করলে স্লাগটি সরাসরি ব্যবহার হবে (যেমন: /news)।</small>
                                </div>

                                <div class="form-group">
                                    <label for="permalink-preview"><b>Sample URL</b></label>
                                    <div id="permalink-preview" class="alert alert-secondary mb-0">{{ $this->permalinkPreview }}</div>
                                </div>

                                <button type="submit" class="btn btn-primary">Save permalinks</button>
                            </form>
                        </div>

                        <div class="tab-pane {{ $tab == 'cache_management' ? 'active show' : '' }}" id="cache_management">
                            <div class="row">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h4 class="card-title mb-0">
                                                <i class="fas fa-sync-alt mr-2"></i> Cache Management
                                            </h4>
                                        </div>
                                        <div class="card-body">
                                            <p class="text-secondary mb-3 text-gray">
                                                Clear cache to make your site up to date. ডাটাবেস ক্যাশিং, স্ট্যাটিক ব্লকসহ সকল ক্যাশ পরিষ্কার করুন। ডেটা আপডেট করার পরও পরিবর্তন দৃশ্যমান না হলে এই কমান্ডটি চালান।
                                            </p>
                                            <div class="table-responsive">
                                                <table class="table table-condensed mb-0">
                                                    <thead class="small text-uppercase">
                                                        <tr>
                                                            <th scope="col" width="50">Type</th>
                                                            <th scope="col">Description</th>
                                                            <th scope="col" class="text-center" width="200">Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>

                                                    <tr>
                                                        <td class="align-middle">
                                                            <span class="tile bg-primary"><i class="fas fa-database"></i></span>
                                                        </td>
                                                        <td class="align-middle">
                                                            <span><strong>Clear all CMS cache</strong></span>
                                                            <div class="small text-muted mt-n1">
                                                                সমস্ত অপ্টিমাইজড ক্যাশ (config, route, view, events) এবং ডিফল্ট অ্যাপ্লিকেশন ক্যাশ একসাথে মুছে ফেলে।
                                                            </div>
                                                            <div class="status-custom"><strong>Current Size:</strong> {{ $cacheSize }}</div>
                                                        </td>
                                                        <td class="text-center align-middle">
                                                            <button wire:click="clearAllCache" wire:loading.attr="disabled" wire:target="clearAllCache" class="btn btn-primary mt-auto">
                                                                <span wire:loading.remove wire:target="clearAllCache"><i class="fas fa-trash-alt mr-1"></i> Clear</span>
                                                                <span wire:loading wire:target="clearAllCache"><i class="fa fa-spinner fa-spin mr-2"></i>Clearing...</span>
                                                            </button>
                                                        </td>
                                                    </tr>

                                                    <tr class="align-middle">
                                                        <td>
                                                            <span class="tile bg-warning">
                                                                <i class="fas fa-file-code"></i>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <strong>Refresh compiled views</strong>
                                                            <div class="small text-muted mt-n1">
                                                                ক্যাশ হওয়া ব্লেড ভিউগুলো পরিষ্কার করে সর্বশেষ টেমপ্লেট পরিবর্তনগুলো তাৎক্ষণিকভাবে প্রতিফলিত করবে।
                                                            </div>
                                                        </td>
                                                        <td class="text-center">
                                                            <button wire:click="clearCompiledViews" wire:loading.attr="disabled" wire:target="clearCompiledViews" class="btn btn-warning text-white mt-auto">
                                                                <span wire:loading.remove wire:target="clearCompiledViews"><i class="fas fa-sync-alt mr-1"></i> Refresh</span>
                                                                <span wire:loading wire:target="clearCompiledViews"><i class="fa fa-spinner fa-spin mr-2"></i>Refreshing...</span>
                                                            </button>
                                                        </td>
                                                    </tr>

                                                    <tr class="align-middle">
                                                        <td>
                                                            <span class="tile bg-success">
                                                                <i class="fas fa-cogs"></i>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <strong>Clear config cache</strong>
                                                            <div class="small text-muted mt-n1">
                                                                প্রোডাকশন পরিবেশে কনফিগ ফাইল পরিবর্তনের পর কনফিগ ক্যাশ রিফ্রেশ করতে এই অপশনটি ব্যবহার করুন।
                                                            </div>
                                                        </td>
                                                        <td class="text-center">
                                                            <button wire:click="clearConfigCache" wire:loading.attr="disabled" wire:target="clearConfigCache" class="btn btn-success mt-auto">
                                                                <span wire:loading.remove wire:target="clearConfigCache"><i class="fas fa-sync-alt mr-1"></i> Clear</span>
                                                                <span wire:loading wire:target="clearConfigCache"><i class="fa fa-spinner fa-spin mr-2"></i>Clearing...</span>
                                                            </button>
                                                        </td>
                                                    </tr>

                                                    <tr class="align-middle">
                                                        <td>
                                                            <span class="tile bg-danger">
                                                                <i class="fas fa-route"></i>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <strong>Clear route cache</strong>
                                                            <div class="small text-muted mt-n1">
                                                                রাউটিং সংক্রান্ত পরিবর্তন কার্যকর করতে রুট ক্যাশ পরিষ্কার করুন।
                                                            </div>
                                                        </td>
                                                        <td class="text-center">
                                                            <button class="btn btn-success">
                                                                <i class="fas fa-sync-alt mr-1"></i> Clear
                                                            </button>
                                                        </td>
                                                    </tr>

                                                    <tr class="align-middle">
                                                        <td>
                                                            <span class="tile bg-info">
                                                                <i class="fas fa-file-alt"></i>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <strong>Clear log</strong>
                                                            <div class="small text-muted mt-n1">
                                                                storage/logs ডিরেক্টরির সকল লগ ফাইল মুছে ফেলে ডিস্ক স্পেস খালি করুন এবং নতুন লগ সংগ্রহ করুন।
                                                            </div>
                                                        </td>
                                                        <td class="text-center">
                                                            <button wire:click="clearLogFiles" wire:loading.attr="disabled" wire:target="clearLogFiles" class="btn btn-danger mt-auto">
                                                                <span wire:loading.remove wire:target="clearLogFiles"><i class="fas fa-trash-alt mr-1"></i> Clear</span>
                                                                <span wire:loading wire:target="clearLogFiles"><i class="fa fa-spinner fa-spin mr-2"></i>Clearing...</span>
                                                            </button>
                                                        </td>
                                                    </tr>

                                                    </tbody>
                                                </table>
                                            </div>

                                        </div>
                                        <div class="card-footer">
                                            <div class="p-3">
                                                <i class="fas fa-circle-info"></i>
                                                <small class="text-secondary text-black">Clear cache after making changes to your site to ensure they appear correctly.</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane {{ $tab == 'security_settings' ? 'active show' : '' }}" id="security_settings">
                            <h6>SECURITY SETTINGS</h6>
                            <hr>
                            <p>Content for security settings goes here...</p>
                        </div>

                        <div class="tab-pane {{ $tab == 'notification' ? 'active show' : '' }}" id="notification">
                            <h6>NOTIFICATION SETTINGS</h6>
                            <hr>
                            <p>Content for notification settings goes here...</p>
                        </div>

                        <div class="tab-pane {{ $tab == 'billing' ? 'active show' : '' }}" id="billing">
                            <h6>BILLING SETTINGS</h6>
                            <hr>
                            <p>Content for billing settings goes here...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
