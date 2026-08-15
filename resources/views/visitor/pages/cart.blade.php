@extends('visitor.layout.app', ['title' => 'Cart', 'bodyClass' => 'cart_page'])

@section('content')
    <div class="sticky-header-next-sec ec-breadcrumb section-space-mb">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="row ec_breadcrumb_inner">
                        <div class="col-md-6 col-sm-12"><h2 class="ec-breadcrumb-title">Cart</h2></div>
                        <div class="col-md-6 col-sm-12">
                            <ul class="ec-breadcrumb-list">
                                <li class="ec-breadcrumb-item"><a href="{{ route('visitor.index') }}">Home</a></li>
                                <li class="ec-breadcrumb-item active">Cart</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <section class="ec-page-content section-space-p">
        <div class="container">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <div class="row">
                <div class="ec-cart-leftside col-lg-8 col-md-12">
                    <div class="ec-cart-content">
                        <div class="ec-cart-inner">
                            <div class="row">
                                <div class="table-content cart-table-content">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th>Product</th>
                                                <th>Price</th>
                                                <th style="text-align: center;">Quantity</th>
                                                <th>Total</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($cart->items as $item)
                                                <tr>
                                                    <td data-label="Product" class="ec-cart-pro-name">
                                                        <a href="{{ route('visitor.products.show', $item->variant->product->slug) }}">
                                                            <img class="ec-cart-pro-img mr-4"
                                                                src="{{ $item->variant->thumbnail
                                                                        ? asset('storage/' . $item->variant->thumbnail)
                                                                        : ($item->variant->product->thumbnail_url ?? asset('visitor/images/product-image/1.jpg')) }}"
                                                                alt="" />
                                                            {{ $item->variant->product->name }}
                                                        </a>
                                                        @if ($item->variant->options_label)
                                                            <small class="d-block text-muted">{{ $item->variant->options_label }}</small>
                                                        @endif
                                                    </td>
                                                    <td data-label="Price" class="ec-cart-pro-price">
                                                        <span class="amount">${{ number_format($item->unit_price, 2) }}</span>
                                                    </td>
                                                    <td data-label="Quantity" class="ec-cart-pro-qty" style="text-align: center;">
                                                        <form action="{{ route('visitor.cart.update', $item->id) }}" method="POST">
                                                            @csrf @method('PATCH')
                                                            <div class="cart-qty-plus-minus">
                                                                <input class="cart-plus-minus" type="number" min="1"
                                                                    name="quantity" value="{{ $item->quantity }}"
                                                                    onchange="this.form.submit()" />
                                                            </div>
                                                        </form>
                                                    </td>
                                                    <td data-label="Total" class="ec-cart-pro-subtotal">${{ number_format($item->subtotal, 2) }}</td>
                                                    <td data-label="Remove" class="ec-cart-pro-remove">
                                                        <form action="{{ route('visitor.cart.destroy', $item->id) }}" method="POST">
                                                            @csrf @method('DELETE')
                                                            <button type="submit" class="border-0 bg-transparent p-0"><i class="ecicon eci-trash-o"></i></button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="text-center py-5">
                                                        Your cart is empty. <a href="{{ route('visitor.products.index') }}">Continue shopping</a>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="ec-cart-update-bottom">
                                            <a class="continue-shopping" href="{{ route('visitor.products.index') }}">Continue Shopping</a>
                                            @if ($cart->items->isNotEmpty())
                                                <a href="{{ route('visitor.checkout') }}" class="btn btn-primary">Check Out</a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="ec-cart-rightside col-lg-4 col-md-12">
                    <div class="ec-sidebar-wrap">
                        <div class="ec-sidebar-block">
                            <div class="ec-sb-title"><h3 class="ec-sidebar-title">Summary</h3></div>
                            <div class="ec-sb-block-content">
                                <div class="ec-cart-summary-bottom">
                                    <div class="ec-cart-summary">
                                        <div>
                                            <span class="text-left">Sub-Total</span>
                                            <span class="text-right">${{ number_format($total, 2) }}</span>
                                        </div>
                                        <div class="ec-cart-summary-total">
                                            <span class="text-left">Total Amount</span>
                                            <span class="text-right">${{ number_format($total, 2) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection