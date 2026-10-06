@extends('admin.include.vertical', ['title' => 'Top Products'])

@section('content')
    @include('admin.include.partials.page-title', ['subtitle' => 'Reports', 'title' => 'Top Products'])

    <div class="row">
        <div class="col-12">

            {{-- ═══════════════════════════════════════════
                 DATE FILTER
            ═══════════════════════════════════════════ --}}
            <form method="GET" action="{{ route('admin.reports.top-products') }}">
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
                                <a href="{{ route('admin.reports.top-products') }}" class="btn btn-light">Reset</a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            {{-- ═══════════════════════════════════════════
                 RANKING TABLE
            ═══════════════════════════════════════════ --}}
            <div class="card">
                <div class="card-header justify-content-between align-items-center border-dashed">
                    <h4 class="card-title mb-0">Best Sellers</h4>
                    <div class="d-flex align-items-center gap-3">
                        <span class="text-muted fs-sm">
                            Ranked by units sold in processing, shipped or delivered orders.
                        </span>
                        <a class="btn btn-sm btn-primary" download
                            href="{{ route('admin.reports.top-products.export', request()->only(['date_from', 'date_to'])) }}">
                            <i class="me-1" data-lucide="download"></i> Export CSV
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-custom table-centered table-hover w-100 mb-0">
                            <thead class="bg-light align-middle bg-opacity-25 thead-sm">
                                <tr class="text-uppercase fs-xxs">
                                    <th style="width: 1%">#</th>
                                    <th>Product</th>
                                    <th>Units Sold</th>
                                    <th>Revenue</th>
                                    <th>Orders</th>
                                    <th class="text-center" style="width: 1%">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($topProducts as $index => $item)
                                    <tr>
                                        <td>
                                            @if ($index === 0)
                                                <i class="ti ti-trophy text-warning fs-lg" title="Best seller"></i>
                                            @else
                                                {{ $index + 1 }}
                                            @endif
                                        </td>
                                        <td>
                                            <span class="fw-semibold">{{ $item->product_name }}</span>
                                            @if ($item->variant_options)
                                                <span class="text-muted fs-xs d-block">{{ $item->variant_options }}</span>
                                            @endif
                                            @if ($item->product_missing)
                                                <span class="badge badge-soft-danger ms-1" title="Product record no longer exists">
                                                    deleted
                                                </span>
                                            @endif
                                        </td>
                                        <td><span class="badge badge-soft-primary">{{ $item->quantity }}</span></td>
                                        <td>${{ number_format($item->revenue, 2) }}</td>
                                        <td>{{ $item->orders_count }}</td>
                                        <td class="text-center">
                                            @if ($item->product_missing)
                                                <i class="ti ti-circle-filled fs-xs text-danger" title="Deleted product"></i>
                                            @else
                                                <i class="ti ti-circle-filled fs-xs text-success" title="Active product"></i>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">No product sales in this period.</td>
                                    </tr>
                                @endforelse
                            </tbody>
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
