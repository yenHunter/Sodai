@extends('visitor.layout.app', ['title' => 'Home', 'bodyClass' => ''])

@section('styles')
    <link rel="stylesheet" href="{{ asset('visitor/css/demo1.css') }}" />
@endsection

@section('content')
    @include('visitor.include.category-sidebar')

    <!-- Main Slider Start -->
    <div class="sticky-header-next-sec ec-main-slider section section-space-pb">
        <div class="ec-slider swiper-container main-slider-nav main-slider-dot">
            <div class="swiper-wrapper">
                @forelse ($sliderBanners as $banner)
                    <div class="ec-slide-item swiper-slide d-flex"
                        style="background-image: url('{{ $banner->image_url }}'); background-size: cover; background-position: center;">
                        <div class="container align-self-center">
                            <div class="row">
                                <div class="col-xl-6 col-lg-7 col-md-7 col-sm-7 align-self-center">
                                    <div class="ec-slide-content slider-animation">
                                        @if ($banner->title)
                                            <h1 class="ec-slide-title">{{ $banner->title }}</h1>
                                        @endif
                                        @if ($banner->subtitle)
                                            <h2 class="ec-slide-stitle">{{ $banner->subtitle }}</h2>
                                        @endif
                                        @if ($banner->description)
                                            <p>{{ $banner->description }}</p>
                                        @endif
                                        @if ($banner->button_text && $banner->button_url)
                                            <a href="{{ $banner->button_url }}" target="{{ $banner->button_target }}"
                                                class="btn btn-lg btn-secondary">{{ $banner->button_text }}</a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="ec-slide-item swiper-slide d-flex ec-slide-1">
                        <div class="container align-self-center">
                            <div class="row">
                                <div class="col-xl-6 col-lg-7 col-md-7 col-sm-7 align-self-center">
                                    <div class="ec-slide-content slider-animation">
                                        <h1 class="ec-slide-title">Welcome to {{ config('app.name') }}</h1>
                                        <a href="{{ route('visitor.products.index') }}"
                                            class="btn btn-lg btn-secondary">Shop Now</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforelse
            </div>
            <div class="swiper-pagination swiper-pagination-white"></div>
            <div class="swiper-buttons">
                <div class="swiper-button-next"></div>
                <div class="swiper-button-prev"></div>
            </div>
        </div>
    </div>
    <!-- Main Slider End -->

    <!-- New Product Start -->
    <section class="section ec-new-product section-space-p" id="arrivals">
        <div class="container">
            <div class="row">
                <div class="col-md-12 text-center">
                    <div class="section-title">
                        <h2 class="ec-bg-title">New Arrivals</h2>
                        <h2 class="ec-title">New Arrivals</h2>
                        <p class="sub-title">Browse The Collection of Top Products</p>
                    </div>
                </div>
            </div>
            <div class="row">
                @forelse ($newArrivals as $product)
                    @include('visitor.partials.product-card', ['product' => $product])
                @empty
                    <div class="col-12 text-center py-5">
                        <p class="mb-0">No products available yet.</p>
                    </div>
                @endforelse
                <div class="col-sm-12 shop-all-btn"><a href="{{ route('visitor.products.index') }}">Shop All Collection</a>
                </div>
            </div>
        </div>
    </section>
    <!-- New Product end -->

    <!-- Category Section Start -->
    @if ($categories->isNotEmpty())
        <section class="section ec-category-section section-space-p" id="categories">
            <div class="container">
                <div class="row">
                    <div class="col-md-12 text-center">
                        <div class="section-title">
                            <h2 class="ec-bg-title">Our Top Collection</h2>
                            <h2 class="ec-title">Top Categories</h2>
                            <p class="sub-title">Browse The Collection of Top Categories</p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-3">
                        <ul class="ec-cat-tab-nav nav">
                            @foreach ($categories as $index => $category)
                                <li class="cat-item">
                                    <a class="cat-link {{ $index === 0 ? 'active' : '' }}" data-bs-toggle="tab"
                                        href="#tab-cat-{{ $category->id }}">
                                        <div class="cat-icons">
                                            <img class="cat-icon" src="{{ $category->image_url }}"
                                                alt="{{ $category->name }}">
                                        </div>
                                        <div class="cat-desc">
                                            <span>{{ $category->name }}</span><span>{{ $category->products_count }}
                                                Products</span>
                                        </div>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="col-lg-9">
                        <div class="tab-content">
                            @foreach ($categories as $index => $category)
                                <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}"
                                    id="tab-cat-{{ $category->id }}">
                                    <div class="row">
                                        <img src="{{ $category->image_url }}" alt="{{ $category->name }}" />
                                    </div>
                                    <span class="panel-overlay">
                                        <a href="{{ route('visitor.products.category', $category->slug) }}"
                                            class="btn btn-primary">View All</a>
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif
    <!-- Category Section End -->

    <!-- Featured Products Start -->
    @if ($featuredProducts->isNotEmpty())
        <!--  Feature & Special Section Start -->
        <section class="section ec-fre-spe-section section-space-p" id="offers">
            <div class="container">
                <div class="row">
                    <!--  Feature Section Start -->
                    <div class="ec-fre-section col-lg-6 col-md-6 col-sm-6 margin-b-30" data-animation="slideInRight">
                        <div class="col-md-12 text-left">
                            <div class="section-title">
                                <h2 class="ec-bg-title">Feature Items</h2>
                                <h2 class="ec-title">Feature Items</h2>
                            </div>
                        </div>

                        <div class="ec-fre-products">
                            @forelse ($featureItems as $product)
                                <div class="ec-fs-product">
                                    <div class="ec-fs-pro-inner">
                                        <div class="ec-fs-pro-image-outer col-lg-6 col-md-6 col-sm-6">
                                            <div class="ec-fs-pro-image">
                                                <a href="{{ route('visitor.products.show', $product->slug) }}"
                                                    class="image">
                                                    <img class="main-image"
                                                        src="{{ $product->thumbnail_url ?? asset('visitor/images/product-image/1_1.jpg') }}"
                                                        alt="{{ $product->name }}" />
                                                </a>
                                                <a href="{{ route('visitor.products.show', $product->slug) }}"
                                                    class="quickview" title="Quick view">
                                                    <i class="fi-rr-eye"></i>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="ec-fs-pro-content col-lg-6 col-md-6 col-sm-6">
                                            <h5 class="ec-fs-pro-title"><a
                                                    href="{{ route('visitor.products.show', $product->slug) }}">{{ $product->name }}</a>
                                            </h5>
                                            <div class="ec-fs-pro-rating">
                                                <span class="ec-fs-rating-icon">
                                                    @for ($i = 1; $i <= 5; $i++)
                                                        <i
                                                            class="ecicon eci-star{{ $i <= round($product->average_rating) ? ' fill' : '' }}"></i>
                                                    @endfor
                                                </span>
                                                <span class="ec-fs-rating-text">{{ $product->review_count }}
                                                    Review{{ $product->review_count === 1 ? '' : 's' }}</span>
                                            </div>
                                            <div class="ec-fs-price">
                                                @if ($product->has_discount)
                                                    <span
                                                        class="old-price">${{ number_format((float) $product->min_price, 2) }}</span>
                                                @endif
                                                <span
                                                    class="new-price">${{ number_format($product->final_price, 2) }}</span>
                                            </div>
                                            <div class="ec-fs-pro-desc">{{ $product->short_description }}</div>
                                            <div class="ec-fs-pro-book">Total Sold:
                                                <span>{{ $product->total_sales }}</span>
                                            </div>
                                            <div class="ec-fs-pro-btn">
                                                <a href="{{ route('visitor.products.show', $product->slug) }}"
                                                    class="btn btn-lg btn-primary">View Product</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <p class="text-muted">No feature items available right now.</p>
                            @endforelse
                        </div>
                    </div>
                    <!--  Feature Section End -->

                    <!--  Special Section Start -->
                    <div class="ec-spe-section col-lg-6 col-md-6 col-sm-6" data-animation="slideInLeft">
                        <div class="col-md-12 text-left">
                            <div class="section-title">
                                <h2 class="ec-bg-title">Limited Time Offer</h2>
                                <h2 class="ec-title">Limited Time Offer</h2>
                            </div>
                        </div>

                        <div class="ec-spe-products">
                            @forelse ($limitedTimeOffers as $product)
                                <div class="ec-fs-product">
                                    <div class="ec-fs-pro-inner">
                                        <div class="ec-fs-pro-image-outer col-lg-6 col-md-6 col-sm-6">
                                            <div class="ec-fs-pro-image">
                                                <a href="{{ route('visitor.products.show', $product->slug) }}"
                                                    class="image">
                                                    <img class="main-image"
                                                        src="{{ $product->thumbnail_url ?? asset('visitor/images/product-image/8_1.jpg') }}"
                                                        alt="{{ $product->name }}" />
                                                </a>
                                                <a href="{{ route('visitor.products.show', $product->slug) }}"
                                                    class="quickview" title="Quick view">
                                                    <i class="fi-rr-eye"></i>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="ec-fs-pro-content col-lg-6 col-md-6 col-sm-6">
                                            <h5 class="ec-fs-pro-title"><a
                                                    href="{{ route('visitor.products.show', $product->slug) }}">{{ $product->name }}</a>
                                            </h5>
                                            <div class="ec-fs-pro-rating">
                                                <span class="ec-fs-rating-icon">
                                                    @for ($i = 1; $i <= 5; $i++)
                                                        <i
                                                            class="ecicon eci-star{{ $i <= round($product->average_rating) ? ' fill' : '' }}"></i>
                                                    @endfor
                                                </span>
                                                <span class="ec-fs-rating-text">{{ $product->review_count }}
                                                    Review{{ $product->review_count === 1 ? '' : 's' }}</span>
                                            </div>
                                            <div class="ec-fs-price">
                                                <span
                                                    class="old-price">${{ number_format((float) $product->min_price, 2) }}</span>
                                                <span
                                                    class="new-price">${{ number_format($product->final_price, 2) }}</span>
                                            </div>
                                            <div class="ec-fs-pro-desc">{{ $product->short_description }}</div>
                                            <div class="ec-fs-pro-book">Total Sold:
                                                <span>{{ $product->total_sales }}</span>
                                            </div>
                                            <div class="ec-fs-pro-btn">
                                                <a href="{{ route('visitor.products.show', $product->slug) }}"
                                                    class="btn btn-lg btn-primary">View Product</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <p class="text-muted">No limited time offers available right now.</p>
                            @endforelse
                        </div>
                    </div>
                    <!--  Special Section End -->
                </div>
            </div>
        </section>
        <!-- Feature & Special Section End -->
    @endif
    <!-- Featured Products End -->

    <!-- Top Rated Start -->
    @if ($topRatedProducts->isNotEmpty())
        <section class="section ec-new-product section-space-p">
            <div class="container">
                <div class="row">
                    <div class="col-md-12 text-center">
                        <div class="section-title">
                            <h2 class="ec-bg-title">Top Rated</h2>
                            <h2 class="ec-title">Top Rated Products</h2>
                        </div>
                    </div>
                </div>
                <div class="row">
                    @foreach ($topRatedProducts as $product)
                        <div class="col-lg-2 col-md-4 col-sm-6 mb-6">
                            <div class="ec-product-inner">
                                <div class="ec-pro-image-outer">
                                    <div class="ec-pro-image">
                                        <a href="{{ route('visitor.products.show', $product->slug) }}" class="image">
                                            <img class="main-image"
                                                src="{{ $product->thumbnail_url ?? asset('visitor/images/product-image/2_1.jpg') }}"
                                                alt="{{ $product->name }}" />
                                        </a>
                                    </div>
                                </div>
                                <div class="ec-pro-content">
                                    <h5 class="ec-pro-title"><a
                                            href="{{ route('visitor.products.show', $product->slug) }}">{{ $product->name }}</a>
                                    </h5>
                                    <div class="ec-pro-rating">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <i
                                                class="ecicon eci-star{{ $i <= round($product->average_rating) ? ' fill' : '' }}"></i>
                                        @endfor
                                    </div>
                                    <span class="ec-price"><span
                                            class="new-price">${{ number_format($product->final_price, 2) }}</span></span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
    <!-- Top Rated End -->

    <!--  services Section Start -->
    <section class="section ec-services-section section-space-p" id="services">
        <h2 class="d-none">Services</h2>
        <div class="container">
            <div class="row">
                <div class="ec_ser_content ec_ser_content_1 col-sm-12 col-md-6 col-lg-3" data-animation="zoomIn">
                    <div class="ec_ser_inner">
                        <div class="ec-service-image"><i class="fi fi-ts-truck-moving"></i></div>
                        <div class="ec-service-desc">
                            <h2>Free Shipping</h2>
                            <p>{{ setting('shipping', 'shipping_note', 'Free shipping on eligible orders') }}</p>
                        </div>
                    </div>
                </div>
                <div class="ec_ser_content ec_ser_content_2 col-sm-12 col-md-6 col-lg-3" data-animation="zoomIn">
                    <div class="ec_ser_inner">
                        <div class="ec-service-image"><i class="fi fi-ts-hand-holding-seeding"></i></div>
                        <div class="ec-service-desc">
                            <h2>24X7 Support</h2>
                            <p>Contact us anytime, we're here to help</p>
                        </div>
                    </div>
                </div>
                <div class="ec_ser_content ec_ser_content_3 col-sm-12 col-md-6 col-lg-3" data-animation="zoomIn">
                    <div class="ec_ser_inner">
                        <div class="ec-service-image"><i class="fi fi-ts-badge-percent"></i></div>
                        <div class="ec-service-desc">
                            <h2>Easy Returns</h2>
                            <p>Read our <a href="{{ route('visitor.return-refund-policy') }}">Return Policy</a></p>
                        </div>
                    </div>
                </div>
                <div class="ec_ser_content ec_ser_content_4 col-sm-12 col-md-6 col-lg-3" data-animation="zoomIn">
                    <div class="ec_ser_inner">
                        <div class="ec-service-image"><i class="fi fi-ts-donate"></i></div>
                        <div class="ec-service-desc">
                            <h2>Secure Payment</h2>
                            <p>Cash on delivery and more, safely handled</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--services Section End -->
@endsection

@section('scripts')
    @vite(['resources/js/pages/visitor-product-card-swatch.js'])
@endsection
