@extends('admin.include.vertical', ['title' => 'Reports'])

@section('content')
    @include('admin.include.partials.page-title', ['subtitle' => 'Analytics', 'title' => 'Reports'])

    {{-- ═══════════════════════════════════════════
         LAST 30 DAYS SNAPSHOT
    ═══════════════════════════════════════════ --}}
    <div class="row row-cols-xxl-4 row-cols-md-2 row-cols-1 g-2">
        <div class="col">
            <div class="card mb-1">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between gap-2 mb-3">
                        <h3 class="mb-0">${{ number_format($summary['revenue_30d'], 2) }}</h3>
                        <div class="avatar-md flex-shrink-0">
                            <span class="avatar-title text-bg-success rounded-circle fs-22">
                                <i data-lucide="banknote"></i>
                            </span>
                        </div>
                    </div>
                    <p class="mb-0 text-uppercase fs-xs fw-bold">Revenue (30d)</p>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card mb-1">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between gap-2 mb-3">
                        <h3 class="mb-0">{{ $summary['orders_30d'] }}</h3>
                        <div class="avatar-md flex-shrink-0">
                            <span class="avatar-title text-bg-info rounded-circle fs-22">
                                <i data-lucide="shopping-cart"></i>
                            </span>
                        </div>
                    </div>
                    <p class="mb-0 text-uppercase fs-xs fw-bold">Orders (30d)</p>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card mb-1">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between gap-2 mb-3">
                        <h3 class="mb-0">{{ $summary['items_30d'] }}</h3>
                        <div class="avatar-md flex-shrink-0">
                            <span class="avatar-title text-bg-primary rounded-circle fs-22">
                                <i data-lucide="package"></i>
                            </span>
                        </div>
                    </div>
                    <p class="mb-0 text-uppercase fs-xs fw-bold">Items Sold (30d)</p>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card mb-1">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between gap-2 mb-3">
                        <h3 class="mb-0">{{ $summary['low_stock_count'] }}</h3>
                        <div class="avatar-md flex-shrink-0">
                            <span class="avatar-title text-bg-danger rounded-circle fs-22">
                                <i data-lucide="package-x"></i>
                            </span>
                        </div>
                    </div>
                    <p class="mb-0 text-uppercase fs-xs fw-bold">Low / Out of Stock Variants</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row row-cols-xxl-3 row-cols-md-2 row-cols-1 g-3 mt-1">
        @php
            $reportCards = [
                [
                    'title' => 'Sales by Date',
                    'desc' => 'Daily revenue, orders and units sold across any date range.',
                    'icon' => 'chart-line',
                    'route' => 'admin.reports.sales-by-date',
                ],
                [
                    'title' => 'Top Products',
                    'desc' => 'Best sellers ranked by units sold and revenue.',
                    'icon' => 'trophy',
                    'route' => 'admin.reports.top-products',
                ],
                [
                    'title' => 'Low Stock',
                    'desc' => 'Variants at or below their threshold that need restocking.',
                    'icon' => 'home-search',
                    'route' => 'admin.reports.low-stock',
                ],
            ];
        @endphp

        @foreach ($reportCards as $card)
            <div class="col">
                <a href="{{ route($card['route']) }}" class="text-decoration-none text-body">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center gap-2 my-3">
                                <div class="avatar-xxl flex-shrink-0">
                                    <span class="avatar-title text-bg-secondary bg-opacity-90 rounded-circle fs-48">
                                        <i class="ti ti-{{ $card['icon'] }}"></i>
                                    </span>
                                </div>
                                <div>
                                    <h4 class="mb-0">{{ $card['title'] }}</h4>
                                    <p class="mb-0">{{ $card['desc'] }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>

    @if ($summary['best_seller_name'])
        <div class="row">
            <div class="col-12">
                <div class="alert alert-primary mb-0">
                    <i class="me-2" data-lucide="medal"></i>
                    Best seller of the last 30 days:
                    <strong>{{ $summary['best_seller_name'] }}</strong>
                    — {{ $summary['best_seller_qty'] }} units sold.
                </div>
            </div>
        </div>
    @endif

    @include('admin.include.partials.footer-scripts')
@endsection

@section('scripts')
@endsection
