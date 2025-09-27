<div>
    @php
        // Define navigation items in one place to avoid repetition (DRY Principle)
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
                            <p>Content for general settings goes here...</p>
                        </div>

                        <div class="tab-pane {{ $tab == 'logo_favicon' ? 'active show' : '' }}" id="logo_favicon">
                            <h6>LOGO & FAVICON</h6>
                            <hr>
                            <p>Content for logo & favicon goes here...</p>
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
