@extends('admin.include.vertical', ['title' => 'Low Stock'])

@section('content')
    @include('admin.include.partials.page-title', ['subtitle' => 'Reports', 'title' => 'Low Stock'])

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header justify-content-between align-items-center border-dashed">
                    <h4 class="card-title mb-0">Variants Needing Restock</h4>
                    <span class="text-muted fs-sm">
                        {{ $lowStock->count() }} variant{{ $lowStock->count() === 1 ? '' : 's' }} at or below threshold
                    </span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-custom table-centered table-hover w-100 mb-0">
                            <thead class="bg-light align-middle bg-opacity-25 thead-sm">
                                <tr class="text-uppercase fs-xxs">
                                    <th>Product</th>
                                    <th>SKU</th>
                                    <th>Stock</th>
                                    <th>Threshold</th>
                                    <th class="text-center" style="width: 1%">Status</th>
                                    <th class="text-center" style="width: 1%">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($lowStock as $row)
                                    <tr>
                                        <td>
                                            <a class="link-reset fw-semibold"
                                                href="{{ route('admin.ecommerce.product.show', $row['product']) }}">
                                                {{ $row['product']->name }}
                                            </a>
                                            @if ($row['variant']->options_label)
                                                <span class="text-muted fs-xs d-block">{{ $row['variant']->options_label }}</span>
                                            @endif
                                        </td>
                                        <td class="text-muted">{{ $row['variant']->sku ?? '—' }}</td>
                                        <td>
                                            <span class="badge {{ $row['is_out_of_stock'] ? 'badge-soft-danger' : 'badge-soft-warning' }}">
                                                {{ $row['stock_quantity'] }} unit{{ $row['stock_quantity'] === 1 ? '' : 's' }}
                                            </span>
                                        </td>
                                        <td class="text-muted">{{ $row['threshold'] }}</td>
                                        <td class="text-center">
                                            @if ($row['is_out_of_stock'])
                                                <span class="text-danger fw-semibold">Out of Stock</span>
                                            @else
                                                <span class="text-warning fw-semibold">Low Stock</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <a class="btn btn-default btn-icon btn-sm rounded-circle"
                                                href="{{ route('admin.ecommerce.product.show', $row['product']) }}"
                                                title="Manage stock">
                                                <i class="fs-lg" data-lucide="package-plus"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">
                                            <i class="ti ti-circle-check text-success me-1"></i>
                                            All variants are sufficiently stocked.
                                        </td>
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
