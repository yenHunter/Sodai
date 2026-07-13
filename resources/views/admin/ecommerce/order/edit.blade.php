@extends('admin.include.vertical', ['title' => 'Edit Order'])

@section('content')
    @include('admin.include.partials.page-title', ['subtitle' => 'Ecommerce', 'title' => 'Edit Order #' . $order->order_number])

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-3">
            <i class="me-2" data-lucide="triangle-alert"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-3">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('admin.ecommerce.order.update', $order) }}" method="POST" id="orderForm">
        @csrf
        @include('admin.ecommerce.order.form', ['order' => $order])
    </form>

    @include('admin.include.partials.footer-scripts')
@endsection

@section('scripts')
    <script>
        window.__existingCartItems = @json($order->items->map(fn($i) => [
            'product_id'     => $i->product_id,
            'name'           => $i->product_name,
            'sku'            => $i->product_sku,
            'thumbnail_url'  => $i->product_image ? asset('storage/' . $i->product_image) : null,
            'price'          => (float) $i->unit_price,
            'quantity'       => $i->quantity,
            'stock_quantity' => $i->product->stock_quantity ?? 0,
        ]));
    </script>
    @vite(['resources/js/pages/admin-ecommerce-order-pos.js'])
@endsection