@extends('visitor.layout.app', ['title' => 'Sodai - Wishlist', 'bodyClass' => 'shop_page'])

@section('styles')
@endsection

@section('content')
    <div class="sticky-header-next-sec  ec-breadcrumb section-space-mb">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="row ec_breadcrumb_inner">
                        <div class="col-md-6 col-sm-12"><h2 class="ec-breadcrumb-title">Wishlist</h2></div>
                        <div class="col-md-6 col-sm-12">
                            <ul class="ec-breadcrumb-list">
                                <li class="ec-breadcrumb-item"><a href="{{ route('visitor.index') }}">Home</a></li>
                                <li class="ec-breadcrumb-item active">Wishlist</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <section class="ec-page-content ec-vendor-uploads ec-user-account wishlist-2 section-space-p">
        <div class="container">
            <div class="row">
                @include('visitor.partials.account-sidebar')

                <div class="ec-shop-rightside col-lg-9 col-md-12">
                    @if (session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif

                    <div class="ec-vendor-dashboard-card">
                        <div class="ec-vendor-card-header">
                            <h5>Wishlist</h5>
                            <div class="ec-header-btn">
                                <a class="btn btn-lg btn-primary" href="{{ route('products.index') }}">Shop Now</a>
                            </div>
                        </div>
                        <div class="ec-vendor-card-body">
                            <div class="ec-vendor-card-table">
                                <table class="table ec-table">
                                    <thead>
                                        <tr>
                                            <th scope="col">Image</th>
                                            <th scope="col">Name</th>
                                            <th scope="col">Added On</th>
                                            <th scope="col">Price</th>
                                            <th scope="col">Status</th>
                                            <th scope="col">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="wish-empt">
                                        @forelse ($wishlist as $item)
                                            @php $product = $item->product; @endphp
                                            <tr class="pro-gl-content">
                                                <td>
                                                    @if ($product?->thumbnail)
                                                        <img class="prod-img" src="{{ Storage::url($product->thumbnail) }}" alt="{{ $product->name }}">
                                                    @endif
                                                </td>
                                                <td><span>{{ $product?->name ?? 'Product removed' }}</span></td>
                                                <td><span>{{ $item->created_at->format('d M Y') }}</span></td>
                                                <td><span>${{ number_format((float) ($product?->final_price ?? 0), 2) }}</span></td>
                                                <td>
                                                    @if (!$product)
                                                        <span class="dis">Unavailable</span>
                                                    @elseif ($product->stock_quantity > 0)
                                                        <span class="avl">Available</span>
                                                    @else
                                                        <span class="out">Out Of Stock</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="tbl-btn">
                                                        <form action="{{ route('account.wishlist.destroy', $product?->id ?? 0) }}" method="POST" class="d-inline">
                                                            @csrf @method('DELETE')
                                                            <button type="submit" class="btn btn-lg btn-primary ec-com-remove ec-remove-wish" title="Remove From List">×</button>
                                                        </form>
                                                    </span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center">Your wishlist is empty.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            @if ($wishlist->hasPages())
                                <div class="mt-3">{{ $wishlist->links() }}</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
@endsection