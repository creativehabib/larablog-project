@extends('back.layout.pages-layout')
@section('pageTitle', $pageTitle ??  'Page title here')
@section('content')
    <div class="card">
        <div class="card-body p-4 p-md-5">

            <div class="form-group border-bottom pb-3 mb-4">
                <div class="custom-control custom-switch custom-switch-lg">
                    <input type="checkbox" class="custom-control-input" id="enableSitemap" checked>
                    <label class="custom-control-label" for="enableSitemap">Enable sitemap?</label>
                </div>
                <small class="form-text text-muted mt-1">
                    When enabled, a sitemap.xml file will be automatically generated and accessible at
                    <a href="http://stories.test/sitemap.xml" target="_blank">http://stories.test/sitemap.xml</a>
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
                                <a href="#" class="btn btn-outline-primary btn-sm mt-1 py-0">View Sitemap</a>
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

            <div class="alert alert-custom-success d-flex" role="alert">
                <i class="fas fa-check-circle mr-2 mt-1"></i>
                <div class="small">
                    The sitemap updates automatically whenever you create, edit, or delete content on your website.
                </div>
            </div>

            <div class="form-group mt-4">
                <label for="sitemapItems" class="font-weight-bold">Sitemap items per page</label>
                <input type="number" class="form-control" id="sitemapItems" value="1000">
                <small class="form-text text-muted">
                    The number of items to include in each sitemap page. Larger values may improve sitemap generation performance but could cause issues with very large sites. Default: 1000
                </small>
            </div>

            <div class="form-group">
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input" id="enableIndexNow">
                    <label class="custom-control-label" for="enableIndexNow">Enable IndexNow?</label>
                </div>
                <small class="form-text text-muted">
                    Automatically notify search engines (Bing, Yandex, Seznam, Naver) when your content is updated using the modern IndexNow protocol for instant indexing.
                </small>
            </div>

        </div> <div class="card-footer text-right">
            <button class="btn btn-primary px-4">
                <i class="fas fa-save mr-1"></i> Save settings
            </button>
        </div>
    </div>
@endsection
