@extends('admin.include.vertical', ['title' => 'Products'])

@section('styles')
@endsection

@section('content')
    @include('admin.include.partials.page-title', ['subtitle' => 'Ecommerce', 'title' => 'Products'])

    <div class="row">
        <div class="col-12">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-3">
                    <i class="me-2" data-lucide="circle-check"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show mb-3">
                    <i class="me-2" data-lucide="triangle-alert"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="d-flex justify-content-between align-items-center mb-3">
                <div></div>
                @admincan('product.create')
                    <a href="{{ route('admin.ecommerce.product.create') }}" class="btn btn-primary">
                        <i class="fs-sm me-1" data-lucide="plus"></i>Add Product
                    </a>
                @endadmincan
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Product List</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Name</th>
                                    <th>SKU</th>
                                    <th>Category</th>
                                    <th>Price</th>
                                    <th>Stock</th>
                                    <th>Status</th>
                                    <th>Featured</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($products as $product)
                                    <tr>
                                        <td>
                                            @if ($product->thumbnail)
                                                <img src="{{ Storage::url($product->thumbnail) }}" alt="{{ $product->name }}" class="rounded" width="44" height="44" style="object-fit:cover">
                                            @else
                                                <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width:44px;height:44px">
                                                    <i data-lucide="image" class="text-muted"></i>
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="fw-semibold">{{ $product->name }}</div>
                                            <small class="text-muted">{{ Str::limit($product->short_description, 50) }}</small>
                                        </td>
                                        <td>{{ $product->sku }}</td>
                                        <td>{{ $product->category?->name ?? '—' }}</td>
                                        <td>${{ number_format($product->price, 2) }}</td>
                                        <td>{{ $product->stock_quantity }}</td>
                                        <td>
                                            <span class="badge {{ $product->is_active ? 'bg-success' : 'bg-danger' }}">{{ $product->is_active ? 'Active' : 'Inactive' }}</span>
                                        </td>
                                        <td>
                                            <span class="badge {{ $product->is_featured ? 'bg-primary' : 'bg-secondary' }}">{{ $product->is_featured ? 'Featured' : 'Standard' }}</span>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                <a href="{{ route('admin.ecommerce.product.show', $product) }}" class="btn btn-default btn-icon btn-sm rounded-circle" title="View">
                                                    <i class="fs-lg" data-lucide="eye"></i>
                                                </a>
                                                @admincan('product.edit')
                                                    <a href="{{ route('admin.ecommerce.product.edit', $product) }}" class="btn btn-default btn-icon btn-sm rounded-circle" title="Edit">
                                                        <i class="fs-lg" data-lucide="square-pen"></i>
                                                    </a>
                                                @endadmincan
                                                @admincan('product.delete')
                                                    <form action="{{ route('admin.ecommerce.product.destroy', $product) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-default btn-icon btn-sm rounded-circle" title="Delete" onclick="return confirm('Delete this product?')">
                                                            <i class="fs-lg" data-lucide="trash-2"></i>
                                                        </button>
                                                    </form>
                                                @endadmincan
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center text-muted py-4">No products found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include("admin.include.partials.customizer") @include("admin.include.partials.footer-scripts")
@endsection

@section("scripts")
    @vite(["resources/js/pages/custom-table.js"])
@endsection
