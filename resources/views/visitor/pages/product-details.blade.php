@extends('visitor.layout.app', ['title' => $product->name, 'bodyClass' => 'product_page'])

@section('content')
    <div class="sticky-header-next-sec ec-breadcrumb section-space-mb">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="row ec_breadcrumb_inner">
                        <div class="col-md-6 col-sm-12">
                            <h2 class="ec-breadcrumb-title">{{ $product->name }}</h2>
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <ul class="ec-breadcrumb-list">
                                <li class="ec-breadcrumb-item"><a href="{{ route('visitor.index') }}">Home</a></li>
                                <li class="ec-breadcrumb-item"><a href="{{ route('visitor.products.index') }}">Shop</a></li>
                                <li class="ec-breadcrumb-item active">{{ $product->name }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <section class="ec-page-content section-space-p">
        <div class="container">
            <div class="row">
                <div class="ec-pro-rightside ec-common-rightside col-lg-12 col-md-12">
                    <div class="single-pro-block">
                        <div class="single-pro-inner">
                            <div class="row">
                                <div class="single-pro-img single-pro-img-no-sidebar">
                                    <div class="single-product-scroll">
                                        <div class="single-product-cover" id="variant-gallery-cover">
                                            @forelse ($variantMatrix['all_images'] as $image)
                                                <div class="single-slide zoom-image-hover">
                                                    <img class="img-responsive" src="{{ $image['url'] }}"
                                                        alt="{{ $product->name }}">
                                                </div>
                                            @empty
                                                <div class="single-slide zoom-image-hover">
                                                    <img class="img-responsive"
                                                        src="{{ $product->thumbnail_url ?? asset('visitor/images/product-image/1_1.jpg') }}"
                                                        alt="{{ $product->name }}">
                                                </div>
                                            @endforelse
                                        </div>
                                        <div class="single-nav-thumb" id="variant-gallery-thumb">
                                            @forelse ($variantMatrix['all_images'] as $image)
                                                <div class="single-slide">
                                                    <img class="img-responsive" src="{{ $image['url'] }}"
                                                        alt="{{ $product->name }}">
                                                </div>
                                            @empty
                                                <div class="single-slide">
                                                    <img class="img-responsive"
                                                        src="{{ $product->thumbnail_url ?? asset('visitor/images/product-image/1_1.jpg') }}"
                                                        alt="{{ $product->name }}">
                                                </div>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>

                                <div class="single-pro-desc single-pro-desc-no-sidebar">
                                    <div class="single-pro-content">
                                        <h5 class="ec-single-title">{{ $product->name }}</h5>

                                        <div class="ec-single-rating-wrap">
                                            <div class="ec-single-rating">
                                                @for ($i = 1; $i <= 5; $i++)
                                                    <i
                                                        class="ecicon eci-star{{ $i <= round($product->average_rating) ? ' fill' : '-o' }}"></i>
                                                @endfor
                                            </div>
                                            <span class="ec-read-review">
                                                <a href="#ec-spt-nav-review">
                                                    {{ $product->review_count > 0 ? $product->review_count . ' Review(s)' : 'Be the first to review this product' }}
                                                </a>
                                            </span>
                                        </div>

                                        <div class="ec-single-desc">{{ $product->short_description }}</div>

                                        <div class="ec-single-price-stoke">
                                            <div class="ec-single-price">
                                                <span class="ec-single-ps-title">Price</span>
                                                <span class="new-price"
                                                    id="variant-price">${{ number_format($product->final_price, 2) }}</span>
                                                <span class="old-price d-none" id="variant-old-price"></span>
                                            </div>
                                            <div class="ec-single-stoke">
                                                <span class="ec-single-ps-title"
                                                    id="variant-stock-status">{{ $product->stock_status }}</span>
                                                <span class="ec-single-sku" id="variant-sku">SKU#:
                                                    {{ $product->defaultVariant->sku ?? '—' }}</span>
                                            </div>
                                        </div>
                                        <div class="ec-single-weight d-none" id="variant-weight-wrap">
                                            <span class="ec-single-ps-title">Weight: </span>
                                            <span id="variant-weight"></span>
                                        </div>

                                        @if (!empty($variantMatrix['option_groups']) && count($variantMatrix['option_groups']))
                                            <div class="ec-pro-variation" id="variant-options"
                                                data-default-key="{{ $variantMatrix['default_key'] }}">
                                                @foreach ($variantMatrix['option_groups'] as $group)
                                                    <div class="ec-pro-variation-inner ec-pro-variation-{{ \Illuminate\Support\Str::slug($group['name']) }}"
                                                        data-option-name="{{ $group['name'] }}">
                                                        <span>{{ $group['name'] }}</span>
                                                        <div class="ec-pro-variation-content">
                                                            <ul>
                                                                @foreach ($group['values'] as $value)
                                                                    @php
                                                                        $swatch = $value['swatch'];
                                                                        $isColorSwatch =
                                                                            $swatch &&
                                                                            preg_match(
                                                                                '/^#([A-Fa-f0-9]{3}){1,2}$/',
                                                                                $swatch,
                                                                            );
                                                                        $isImageSwatch = $swatch && !$isColorSwatch;
                                                                    @endphp
                                                                    <li class="variant-value-option"
                                                                        data-value-id="{{ $value['id'] }}"
                                                                        title="{{ $value['value'] }}">
                                                                        @if ($isColorSwatch)
                                                                            <span
                                                                                style="background-color: {{ $swatch }};"></span>
                                                                        @elseif ($isImageSwatch)
                                                                            <span
                                                                                style="background-image: url('{{ asset('storage/' . $swatch) }}'); background-size: cover; background-position: center;"></span>
                                                                        @else
                                                                            <span>{{ $value['value'] }}</span>
                                                                        @endif
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>

                                            <script id="variant-matrix-data" type="application/json">
                                                {!! json_encode($variantMatrix['combinations']) !!}
                                            </script>
                                        @endif

                                        <form id="addToCartForm" action="{{ route('visitor.cart.add') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="product_variant_id" id="selected_variant_id"
                                                value="{{ optional($product->defaultVariant)->id }}">
                                            <div class="ec-single-qty">
                                                <div class="qty-plus-minus">
                                                    <input class="qty-input" type="number" name="quantity" min="1"
                                                        value="1" />
                                                </div>
                                                <div class="ec-single-cart">
                                                    <button class="btn btn-primary" type="submit" id="addToCartBtn"
                                                        {{ $product->is_out_of_stock ? 'disabled' : '' }}>
                                                        Add To Cart
                                                    </button>
                                                </div>
                                                <div class="ec-single-wishlist">
                                                    @auth('customer')
                                                        <a href="#" class="ec-btn-group wishlist toggle-wishlist"
                                                            data-product-id="{{ $product->id }}" title="Wishlist"><i
                                                                class="fi-rr-heart"></i></a>
                                                    @else
                                                        <a href="{{ route('visitor.login') }}" class="ec-btn-group wishlist"
                                                            title="Login to add to wishlist"><i class="fi-rr-heart"></i></a>
                                                    @endauth
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabs -->
                    <div class="ec-single-pro-tab">
                        <div class="ec-single-pro-tab-wrapper">
                            <div class="ec-single-pro-tab-nav">
                                <ul class="nav nav-tabs" role="tablist">
                                    <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab"
                                            data-bs-target="#ec-spt-nav-details" role="tab">Detail</a></li>
                                    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab"
                                            data-bs-target="#ec-spt-nav-review" role="tab">Reviews</a></li>
                                </ul>
                            </div>
                            <div class="tab-content ec-single-pro-tab-content">
                                <div id="ec-spt-nav-details" class="tab-pane fade show active">
                                    <div class="ec-single-pro-tab-desc">
                                        {!! $product->description !!}
                                    </div>
                                </div>
                                <div id="ec-spt-nav-review" class="tab-pane fade">
                                    <div class="row">
                                        <div class="ec-t-review-wrapper">
                                            @forelse ($product->reviews as $review)
                                                <div class="ec-t-review-item">
                                                    <div class="ec-t-review-avtar">
                                                        <img src="{{ $review->user?->avatar_url ?? asset('visitor/images/review-image/1.jpg') }}"
                                                            alt="">
                                                    </div>
                                                    <div class="ec-t-review-content">
                                                        <div class="ec-t-review-top">
                                                            <div class="ec-t-review-name">
                                                                {{ $review->user?->name ?? 'Customer' }}</div>
                                                            <div class="ec-t-review-rating">
                                                                @for ($i = 1; $i <= 5; $i++)
                                                                    <i
                                                                        class="ecicon eci-star{{ $i <= $review->rating ? ' fill' : '-o' }}"></i>
                                                                @endfor
                                                            </div>
                                                        </div>
                                                        <div class="ec-t-review-bottom">
                                                            <p>{{ $review->comment }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            @empty
                                                <p>No reviews yet.</p>
                                            @endforelse
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

    @if ($product->relatedProducts->isNotEmpty())
        <section class="section ec-releted-product section-space-p">
            <div class="container">
                <div class="row">
                    <div class="col-md-12 text-center">
                        <div class="section-title">
                            <h2 class="ec-bg-title">Related products</h2>
                            <h2 class="ec-title">Related products</h2>
                        </div>
                    </div>
                </div>
                <div class="row margin-minus-b-30">
                    @foreach ($product->relatedProducts as $related)
                        <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6 mb-6 pro-gl-content">
                            <div class="ec-product-inner">
                                <div class="ec-pro-image-outer">
                                    <div class="ec-pro-image">
                                        <a href="{{ route('visitor.products.show', $related->slug) }}" class="image">
                                            <img class="main-image"
                                                src="{{ $related->thumbnail_url ?? asset('visitor/images/product-image/6_1.jpg') }}"
                                                alt="{{ $related->name }}" />
                                        </a>
                                    </div>
                                </div>
                                <div class="ec-pro-content">
                                    <h5 class="ec-pro-title"><a
                                            href="{{ route('visitor.products.show', $related->slug) }}">{{ $related->name }}</a>
                                    </h5>
                                    <span class="ec-price"><span
                                            class="new-price">{{ $related->price_range_label }}</span></span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection

@section('scripts')
    @vite(['resources/js/pages/visitor-product-details.js'])
@endsection
