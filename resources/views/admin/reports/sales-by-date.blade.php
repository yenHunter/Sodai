@extends('admin.include.vertical', ['title' => 'Sales by Date'])

@section('content')
    @include('admin.include.partials.page-title', ['subtitle' => 'Reports', 'title' => 'Sales by Date'])

    <div class="row">
        <div class="col-12">

            {{-- ═══════════════════════════════════════════
                 DATE FILTER
            ═══════════════════════════════════════════ --}}
            <form method="GET" action="{{ route('admin.reports.sales-by-date') }}">
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="row g-2 align-items-end">
                            <div class="col-md-4">
                                <label class="form-label" for="dateFrom">From</label>
                                <input type="date" class="form-control" id="dateFrom" name="date_from"
                                    value="{{ $from }}" max="{{ $to }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" for="dateTo">To</label>
                                <input type="date" class="form-control" id="dateTo" name="date_to"
                                    value="{{ $to }}" min="{{ $from }}">
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fs-sm me-1" data-lucide="filter"></i> Apply
                                </button>
                                <a href="{{ route('admin.reports.sales-by-date') }}" class="btn btn-light">Reset</a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            {{-- ═══════════════════════════════════════════
                 PERIOD TOTALS
            ═══════════════════════════════════════════ --}}
            <div class="row row-cols-xxl-4 row-cols-md-2 row-cols-1 g-1">
                <div class="col">
                    <div class="card mb-1">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between gap-2 mb-3">
                                <h3 class="mb-0">${{ number_format($totals['revenue'], 2) }}</h3>
                                <div class="avatar-md flex-shrink-0">
                                    <span class="avatar-title text-bg-success rounded-circle fs-22">
                                        <i data-lucide="banknote"></i>
                                    </span>
                                </div>
                            </div>
                            <p class="mb-0 text-uppercase fs-xs fw-bold">Total Revenue</p>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card mb-1">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between gap-2 mb-3">
                                <h3 class="mb-0">${{ number_format($totals['discounts'], 2) }}</h3>
                                <div class="avatar-md flex-shrink-0">
                                    <span class="avatar-title text-bg-danger rounded-circle fs-22">
                                        <i data-lucide="ticket-percent"></i>
                                    </span>
                                </div>
                            </div>
                            <p class="mb-0 text-uppercase fs-xs fw-bold">Discounts Given</p>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card mb-1">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between gap-2 mb-3">
                                <h3 class="mb-0">{{ $totals['orders'] }}</h3>
                                <div class="avatar-md flex-shrink-0">
                                    <span class="avatar-title text-bg-info rounded-circle fs-22">
                                        <i data-lucide="shopping-cart"></i>
                                    </span>
                                </div>
                            </div>
                            <p class="mb-0 text-uppercase fs-xs fw-bold">Orders</p>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card mb-1">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between gap-2 mb-3">
                                <h3 class="mb-0">${{ number_format($totals['avg_order_value'], 2) }}</h3>
                                <div class="avatar-md flex-shrink-0">
                                    <span class="avatar-title text-bg-primary rounded-circle fs-22">
                                        <i data-lucide="receipt"></i>
                                    </span>
                                </div>
                            </div>
                            <p class="mb-0 text-uppercase fs-xs fw-bold">Avg Order Value</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ═══════════════════════════════════════════
                 DAILY BREAKDOWN
            ═══════════════════════════════════════════ --}}
            <div class="card">
                <div class="card-header justify-content-between align-items-center border-dashed">
                    <h4 class="card-title mb-0">Daily Breakdown</h4>
                    <div class="d-flex align-items-center gap-3">
                        <span class="text-muted fs-sm">
                            Counts orders in processing, shipped or delivered status only.
                        </span>
                        <a class="btn btn-sm btn-primary" download
                            href="{{ route('admin.reports.sales-by-date.export', request()->only(['date_from', 'date_to'])) }}">
                            <i class="me-1" data-lucide="download"></i> Export CSV
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-custom table-centered table-hover w-100 mb-0">
                            <thead class="bg-light align-middle bg-opacity-25 thead-sm">
                                <tr class="text-uppercase fs-xxs">
                                    <th>Date</th>
                                    <th>Orders</th>
                                    <th>Items Sold</th>
                                    <th>Revenue</th>
                                    <th>Discounts</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($sales as $day)
                                    <tr>
                                        <td class="fw-semibold">{{ \Illuminate\Support\Carbon::parse($day['date'])->format('M j, Y') }}</td>
                                        <td>{{ $day['orders_count'] }}</td>
                                        <td>{{ $day['items_sold'] }}</td>
                                        <td>${{ number_format($day['revenue'], 2) }}</td>
                                        <td>${{ number_format($day['discounts'], 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">No sales in this period.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            @if ($sales->isNotEmpty())
                                <tfoot class="bg-light bg-opacity-25 fw-semibold">
                                    <tr>
                                        <td>Total ({{ $sales->count() }} days)</td>
                                        <td>{{ $totals['orders'] }}</td>
                                        <td>{{ $totals['items'] }}</td>
                                        <td>${{ number_format($totals['revenue'], 2) }}</td>
                                        <td>${{ number_format($totals['discounts'], 2) }}</td>
                                    </tr>
                                </tfoot>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('admin.include.partials.footer-scripts')
@endsection

@section('scripts')
@endsection
