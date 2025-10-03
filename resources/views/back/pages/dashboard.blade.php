@extends('back.layout.pages-layout')
@section('pageTitle', $pageTitle ?? 'Page title here')
@section('content')
    <header class="page-title-bar mb-4">
        <div class="d-md-flex align-items-center justify-content-between">
            <div>
                <h1 class="page-title mb-1">ড্যাশবোর্ড ওভারভিউ</h1>
                <p class="text-muted mb-0">এখান থেকে আপনার টিম, কনটেন্ট এবং সার্চ উপস্থিতি সম্পর্কে সব জরুরি তথ্য এক নজরে দেখুন।</p>
            </div>
            <div class="mt-3 mt-md-0 text-md-right">
                <span class="badge badge-primary badge-pill px-3 py-2">আজ {{ now()->format('d M, Y') }}</span>
            </div>
        </div>
    </header>

    <div class="page-section dashboard-grid">
        @php
            $currentUser = auth()->user();
            $visibilityMatrix = $dashboardWidgetVisibility ?? [];
            $canViewWidget = function (string $widgetKey) use ($currentUser, $visibilityMatrix) {
                if (! $currentUser) {
                    return false;
                }

                if ($currentUser->hasRole('Admin')) {
                    return true;
                }

                $allowedRoles = $visibilityMatrix[$widgetKey] ?? [];

                if (empty($allowedRoles)) {
                    return true;
                }

                return $currentUser->hasAnyRole($allowedRoles);
            };
        @endphp

        @php $rowOneWidgets = ['total_posts', 'categories_summary', 'team_overview', 'sitemap_coverage']; @endphp
        @if (array_filter($rowOneWidgets, fn ($widget) => $canViewWidget($widget)))
            <div class="row">
                @if ($canViewWidget('total_posts'))
                    <div class="col-sm-6 col-lg-3 mb-4">
                        <div class="card card-fluid border-left border-primary shadow-sm h-100">
                            <div class="card-body d-flex flex-column">
                                <h6 class="text-uppercase text-muted small mb-2">মোট পোস্ট</h6>
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <span class="display-4 font-weight-bold mb-0">{{ number_format($totalPosts) }}</span>
                                    <span class="badge badge-primary">{{ number_format($featuredPosts) }} ফিচার্ড</span>
                                </div>
                                <p class="text-muted small mb-0">শেষ ৬ মাসে মোট {{ array_sum($monthlyPostSeries) }} টি পোস্ট প্রকাশিত হয়েছে।</p>
                            </div>
                        </div>
                    </div>
                @endif

                @if ($canViewWidget('categories_summary'))
                    <div class="col-sm-6 col-lg-3 mb-4">
                        <div class="card card-fluid border-left border-success shadow-sm h-100">
                            <div class="card-body">
                                <h6 class="text-uppercase text-muted small mb-2">ক্যাটাগরি ও সাব-ক্যাটাগরি</h6>
                                <div class="d-flex align-items-end justify-content-between">
                                    <div>
                                        <div class="h2 font-weight-bold mb-0">{{ number_format($categoriesCount) }}</div>
                                        <div class="text-muted small">মূল বিভাগ</div>
                                    </div>
                                    <div class="text-right">
                                        <div class="h4 mb-0">{{ number_format($subCategoriesCount) }}</div>
                                        <div class="text-muted small">সাব বিভাগ</div>
                                    </div>
                                </div>

                                <p class="text-muted small mb-0 mt-3">উপযুক্ত ক্যাটাগরি ব্যবহার করলে পাঠকের জন্য কন্টেন্ট খুঁজে পাওয়া সহজ হয়।</p>
                            </div>
                        </div>
                    </div>
                @endif

                @if ($canViewWidget('team_overview'))
                    <div class="col-sm-6 col-lg-3 mb-4">
                        <div class="card card-fluid border-left border-warning shadow-sm h-100">
                            <div class="card-body">
                                <h6 class="text-uppercase text-muted small mb-2">টিম ও ইউজার</h6>
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <span class="display-4 font-weight-bold mb-0">{{ number_format($totalUsers) }}</span>
                                </div>

                                <ul class="list-unstyled mb-0 small text-muted">
                                    <li><strong>Admin:</strong> {{ number_format($roleCounts['Admin'] ?? 0) }}</li>
                                    <li><strong>Editor:</strong> {{ number_format($roleCounts['Editor'] ?? 0) }}</li>
                                    <li><strong>User:</strong> {{ number_format($roleCounts['User'] ?? 0) }}</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                @if ($canViewWidget('sitemap_coverage'))
                    <div class="col-sm-6 col-lg-3 mb-4">
                        <div class="card card-fluid border-left border-info shadow-sm h-100">
                            <div class="card-body">
                                <h6 class="text-uppercase text-muted small mb-2">সাইটম্যাপ কাভারেজ</h6>
                                <div class="d-flex justify-content-between align-items-end mb-3">
                                    <div>
                                        <div class="h3 font-weight-bold mb-0" id="sitemapIndexable">{{ number_format($indexablePosts) }}</div>
                                        <div class="text-muted small">Index করা পোস্ট</div>
                                    </div>
                                    <div class="text-right">
                                        <div class="h4 mb-0" id="sitemapNonIndexable">{{ number_format(max($totalPosts - $indexablePosts, 0)) }}</div>
                                        <div class="text-muted small">No Index</div>
                                    </div>
                                </div>

                                @php
                                    $coverage = $totalPosts > 0 ? round(($indexablePosts / $totalPosts) * 100, 1) : 0;
                                @endphp

                                <div class="progress progress-sm mb-1">
                                    <div class="progress-bar bg-info" id="sitemapCoverageBar" role="progressbar" style="width: {{ $coverage }}%;" aria-valuenow="{{ $coverage }}" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>

                                <p class="small text-muted mb-0"><span id="sitemapCoverageValue">{{ $coverage }}%</span> কনটেন্ট বর্তমানে সার্চ ইঞ্জিনে সাবমিটেড।</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        @endif

        @php $rowTwoWidgets = ['content_performance', 'role_distribution']; @endphp
        @if (array_filter($rowTwoWidgets, fn ($widget) => $canViewWidget($widget)))
            <div class="row">
                @if ($canViewWidget('content_performance'))
                    <div class="col-lg-8 mb-4">
                        <div class="card card-fluid shadow-sm h-100">
                            <div class="card-header border-0 d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="card-title mb-1">কনটেন্ট পারফরম্যান্স (শেষ ৬ মাস)</h5>
                                    <p class="text-muted small mb-0">প্রতি মাসে কতটি পোস্ট প্রকাশিত হয়েছে তা দেখুন।</p>
                                </div>
                            </div>

                            <div class="card-body">
                                <div id="monthlyPostsChart" style="min-height: 320px;"></div>
                            </div>
                        </div>
                    </div>
                @endif

                @if ($canViewWidget('role_distribution'))
                    <div class="col-lg-4 mb-4">
                        <div class="card card-fluid shadow-sm h-100">
                            <div class="card-header border-0">
                                <h5 class="card-title mb-1">টিম রোল ডিস্ট্রিবিউশন</h5>
                                <p class="text-muted small mb-0">Admin, Editor ও User সহ সকল রোলের ব্যবহারকারী সংখ্যা।</p>
                            </div>

                            <div class="card-body">
                                <div id="roleDistributionChart" style="min-height: 320px;"></div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        @endif

        @php $rowThreeWidgets = ['recent_posts', 'recent_users']; @endphp
        @if (array_filter($rowThreeWidgets, fn ($widget) => $canViewWidget($widget)))
            <div class="row">
                @if ($canViewWidget('recent_posts'))
                    <div class="col-lg-6 mb-4">
                        <div class="card card-fluid shadow-sm h-100">
                            <div class="card-header border-0 d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">সাম্প্রতিক পোস্ট আপডেট</h5>
                                <span class="badge badge-secondary">Top 5</span>
                            </div>

                            <div class="card-body">
                                <div class="list-group list-group-flush">
                                    @forelse ($recentPosts as $post)
                                        <div class="list-group-item px-0 d-flex justify-content-between align-items-start">
                                            <div>
                                                <div class="font-weight-semibold">{{ $post->title }}</div>
                                                <div class="text-muted small">{{ $post->category?->name ?? 'Uncategorized' }} · {{ $post->updated_at?->diffForHumans() }}</div>
                                            </div>
                                            <span class="badge badge-info">{{ $post->is_indexable ? 'Index' : 'No Index' }}</span>
                                        </div>
                                    @empty
                                        <p class="text-muted mb-0">এখনো কোনো পোস্ট নেই।</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if ($canViewWidget('recent_users'))
                    <div class="col-lg-6 mb-4">
                        <div class="card card-fluid shadow-sm h-100">
                            <div class="card-header border-0 d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">নতুন সদস্যদের কার্যক্রম</h5>
                                <span class="badge badge-secondary">Top 5</span>
                            </div>

                            <div class="card-body">
                                <ul class="list-unstyled mb-0">
                                    @forelse ($recentUsers as $user)
                                        <li class="media mb-3">
                                            <img src="{{ $user->avatar }}" class="mr-3 rounded-circle" width="48" height="48" alt="Avatar">
                                            <div class="media-body">
                                                <h6 class="mt-0 mb-1">{{ $user->name }}</h6>
                                                <div class="text-muted small">{{ $user->email }}</div>
                                                <div class="small">
                                                    @foreach ($user->roles as $role)
                                                        <span class="badge badge-light border">{{ $role->name }}</span>
                                                    @endforeach
                                                    <span class="text-muted">· যোগ দিয়েছেন {{ $user->created_at?->diffForHumans() }}</span>
                                                </div>
                                            </div>
                                        </li>
                                    @empty
                                        <p class="text-muted mb-0">কোনো ব্যবহারকারী পাওয়া যায়নি।</p>
                                    @endforelse
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

            </div>
        @endif

        @if ($canViewWidget('sitemap_manager'))
            <div class="row">
                <div class="col-12 mb-4">
                    <livewire:admin.sitemap-manager />
                </div>
            </div>
        @endif

    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const monthlyPostsChartEl = document.querySelector('#monthlyPostsChart');
            if (monthlyPostsChartEl) {
                const monthlyPostsChart = new ApexCharts(monthlyPostsChartEl, {
                    chart: {
                        type: 'area',
                        height: 320,
                        toolbar: { show: false }
                    },
                    stroke: {
                        curve: 'smooth',
                        width: 3
                    },
                    dataLabels: { enabled: false },
                    series: [{
                        name: 'Posts',
                        data: @json($monthlyPostSeries),
                    }],
                    xaxis: {
                        categories: @json($monthlyPostLabels),
                        labels: {
                            style: { colors: '#6c757d' }
                        }
                    },
                    yaxis: {
                        labels: {
                            style: { colors: '#6c757d' },
                            formatter: value => Math.round(value)
                        }
                    },
                    colors: ['#3f87f5'],
                    fill: {
                        type: 'gradient',
                        gradient: {
                            opacityFrom: 0.45,
                            opacityTo: 0.05,
                        }
                    }
                });
                monthlyPostsChart.render();
            }

            const roleDistributionChartEl = document.querySelector('#roleDistributionChart');
            if (roleDistributionChartEl) {
                const roleDistributionChart = new ApexCharts(roleDistributionChartEl, {
                    chart: {
                        type: 'donut',
                        height: 320
                    },
                    labels: @json($roleChartLabels),
                    series: @json($roleChartSeries),
                    legend: {
                        position: 'bottom'
                    },
                    colors: ['#247ba0', '#ff9f1c', '#2ec4b6', '#e71d36', '#011627'],
                });
                roleDistributionChart.render();
            }

            window.addEventListener('sitemapCoverageUpdated', function (event) {
                const detail = event.detail || {};
                const coverage = detail.coverage || {};
                const indexable = coverage.indexable ?? 0;
                const nonIndexable = coverage.non_indexable ?? 0;
                const percentage = coverage.coverage ?? 0;

                const indexableEl = document.getElementById('sitemapIndexable');
                const nonIndexableEl = document.getElementById('sitemapNonIndexable');
                const coverageEl = document.getElementById('sitemapCoverageValue');
                const barEl = document.getElementById('sitemapCoverageBar');

                if (indexableEl) {
                    indexableEl.textContent = new Intl.NumberFormat().format(indexable);
                }
                if (nonIndexableEl) {
                    nonIndexableEl.textContent = new Intl.NumberFormat().format(nonIndexable);
                }
                if (coverageEl) {
                    coverageEl.textContent = `${percentage}%`;
                }
                if (barEl) {
                    barEl.style.width = `${percentage}%`;
                    barEl.setAttribute('aria-valuenow', percentage);
                }
            });
        });
    </script>
@endpush
