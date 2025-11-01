<div>
    <header class="page-title-bar">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item active">
                    <a href="#"><i class="breadcrumb-icon fa fa-angle-left mr-2"></i> Settings</a>
                </li>
            </ol>
        </nav>
        <h1 class="page-title">Sitemap Settings</h1>
        <p class="text-muted">Configure sitemap visibility, pagination behaviour and IndexNow notifications.</p>
    </header>

    <div class="card">
        <form wire:submit.prevent="save">
            <div class="card-body p-4 p-md-5">
                <div class="form-group border-bottom pb-3 mb-4">
                    <div class="custom-control custom-switch custom-switch-lg">
                        <input type="checkbox" class="custom-control-input" id="enableSitemap" wire:model.defer="sitemap_enabled">
                        <label class="custom-control-label" for="enableSitemap">Enable sitemap?</label>
                    </div>
                    <small class="form-text text-muted mt-1">
                        When enabled, a sitemap.xml file will be automatically generated and accessible at
                        <a href="{{ $sitemapUrl }}" target="_blank" rel="noopener">{{ $sitemapUrl }}</a>
                        to help search engines better index your site.
                    </small>
                </div>

                <div class="sitemap-info-box p-3 mb-4">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="icon-circle bg-light-primary">
                                <i class="fas fa-info-circle"></i>
                            </span>
                        </div>
                        <div class="col">
                            <h5 class="mb-1">How Sitemap Works</h5>
                            <p class="text-muted small mb-0">
                                Your sitemap is automatically generated and updated whenever content changes. It helps search engines discover and index your website content more efficiently.
                            </p>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <div class="d-flex align-items-center">
                                <span class="icon-circle bg-light-secondary mr-3">
                                    <i class="fas fa-link"></i>
                                </span>
                                <div>
                                    <strong>Sitemap URL</strong><br>
                                    <a href="{{ $sitemapUrl }}" class="btn btn-outline-primary btn-sm mt-1 py-0" target="_blank" rel="noopener">View Sitemap</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <span class="icon-circle bg-light-success mr-3">
                                    <i class="fas fa-sync-alt"></i>
                                </span>
                                <div>
                                    <strong>Automatic Generation</strong><br>
                                    <small class="text-muted">Updates automatically when content changes</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @if (! $sitemap_enabled)
                    <div class="alert alert-warning d-flex" role="alert">
                        <i class="fas fa-exclamation-triangle mr-2 mt-1"></i>
                        <div class="small">
                            The sitemap is currently disabled. Search engines will no longer be able to crawl sitemap.xml while this setting is off.
                        </div>
                    </div>
                @else
                    <div class="alert alert-custom-success d-flex" role="alert">
                        <i class="fas fa-check-circle mr-2 mt-1"></i>
                        <div class="small">
                            The sitemap updates automatically whenever you create, edit, or delete content on your website.
                        </div>
                    </div>
                @endif

                <div class="form-group mt-4">
                    <label for="sitemapItems" class="font-weight-bold">Sitemap items per page</label>
                    <input type="number" class="form-control" id="sitemapItems" wire:model.defer="sitemap_items_per_page" min="1" max="50000">
                    <small class="form-text text-muted">
                        The number of items to include in each sitemap page. Larger values may improve sitemap generation performance but could cause issues with very large sites. Default: 1000
                    </small>
                    @error('sitemap_items_per_page')<span class="text-danger small">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="enableIndexNow" wire:model.defer="sitemap_enable_index_now">
                        <label class="custom-control-label" for="enableIndexNow">Enable IndexNow?</label>
                    </div>
                    <small class="form-text text-muted">
                        Automatically notify search engines (Bing, Yandex, Seznam, Naver) when your content is updated using the modern IndexNow protocol for instant indexing.
                    </small>
                </div>
            </div>
            <div class="card-footer text-right">
                <button class="btn btn-primary px-4" type="submit" wire:loading.attr="disabled">
                    <span wire:loading.remove> <i class="fas fa-save mr-1"></i> Save settings</span>
                    <span wire:loading class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                </button>
            </div>
        </form>
    </div>
</div>
