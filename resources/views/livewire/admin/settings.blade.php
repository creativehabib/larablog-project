<div>
    @php
        $navItems = [
            'general_settings' => ['icon' => 'user', 'label' => 'General Settings'],
            'logo_favicon' => ['icon' => 'settings', 'label' => 'Logo & Favicon'],
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
