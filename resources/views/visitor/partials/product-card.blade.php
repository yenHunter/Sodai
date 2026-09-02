@php
    $swatches = $product->color_swatch_options;
    $defaultThumb = $product->thumbnail_url ?? asset('visitor/images/product-image/6_1.jpg');
@endphp
<div class="col-lg-3 col-md-6 col-sm-6 col-xs-6 mb-6 pro-gl-content" data-animation="flipInY">
    <div class="ec-product-inner">
        <div class="ec-pro-image-outer">
            <div class="ec-pro-image">
                <a href="{{ route('visitor.products.show', $product->slug) }}" class="image">
                    <img class="main-image" src="{{ $defaultThumb }}" alt="{{ $product->name }}" />
                </a>
                @if ($product->has_discount)
                    <span class="percentage">{{ round($product->discount_percentage) }}%</span>
                @endif
                @if ($product->is_out_of_stock)
                    <span class="flags"><span class="sale">Sold Out</span></span>
                @endif
                <a href="{{ route('visitor.products.show', $product->slug) }}" class="quickview" title="View Product">
                    <i class="fi-rr-eye"></i>
                </a>
                <div class="ec-pro-actions">
                    <a href="{{ route('visitor.products.show', $product->slug) }}" class="ec-btn-group compare"
                        title="View Details">
                        <i class="fi-rr-shopping-basket"></i>
                    </a>
                    @auth('customer')
                        <a href="#" class="ec-btn-group wishlist toggle-wishlist"
                            data-product-id="{{ $product->id }}" title="Wishlist">
                            <i class="fi-rr-heart"></i>
                        </a>
                    @else
                        <a href="{{ route('visitor.login') }}" class="ec-btn-group wishlist"
                            title="Login to add to wishlist">
                            <i class="fi-rr-heart"></i>
                        </a>
                    @endauth
                </div>
            </div>
        </div>
        <div class="ec-pro-content">
            <h5 class="ec-pro-title">
                <a href="{{ route('visitor.products.show', $product->slug) }}">{{ $product->name }}</a>
            </h5>
            <div class="ec-pro-rating">
                @for ($i = 1; $i <= 5; $i++)
                    <i class="ecicon eci-star{{ $i <= round($product->average_rating) ? ' fill' : '' }}"></i>
                @endfor
            </div>
            <span class="ec-price product-card-price">
                @if ($product->has_variants)
                    <span class="new-price">{{ $product->price_range_label }}</span>
                @elseif ($product->has_discount)
                    <span class="old-price">${{ number_format((float) $product->min_price, 2) }}</span>
                    <span class="new-price">${{ number_format($product->final_price, 2) }}</span>
                @else
                    <span class="new-price">${{ number_format($product->final_price, 2) }}</span>
                @endif
            </span>

            @if ($swatches->count() > 1)
                <div class="ec-pro-option">
                    <div class="ec-pro-color">
                        <span class="ec-pro-opt-label">Color</span>
                        <ul class="ec-opt-swatch">
                            @foreach ($swatches as $swatch)
                                <li class="{{ $loop->first ? 'active' : '' }}">
                                    <a href="javascript:void(0)" class="product-swatch-option"
                                        data-thumb="{{ $swatch['thumbnail_url'] ?? $defaultThumb }}"
                                        data-price="{{ number_format($swatch['final_price'], 2) }}"
                                        data-old-price="{{ number_format($swatch['price'], 2) }}"
                                        title="{{ $swatch['label'] }}">
                                        @if ($swatch['swatch'] && preg_match('/^#([A-Fa-f0-9]{3}){1,2}$/', $swatch['swatch']))
                                            <span style="background-color: {{ $swatch['swatch'] }};"></span>
                                        @else
                                            <span
                                                style="background-image: url('{{ $swatch['thumbnail_url'] ?? $defaultThumb }}'); background-size: cover; background-position: center;"></span>
                                        @endif
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
