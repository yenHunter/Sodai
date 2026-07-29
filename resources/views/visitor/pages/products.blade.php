@extends('visitor.layout.app', ['title' => 'Sodai - Products', 'bodyClass' => 'shop_page'])

@section('styles')
@endsection

@section('content')
    <!-- Ec breadcrumb start -->
    <div class="sticky-header-next-sec  ec-breadcrumb section-space-mb">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="row ec_breadcrumb_inner">
                        <div class="col-md-6 col-sm-12">
                            <h2 class="ec-breadcrumb-title">Shop</h2>
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <ul class="ec-breadcrumb-list">
                                <li class="ec-breadcrumb-item"><a href="{{ route('visitor.index') }}">Home</a></li>
                                <li class="ec-breadcrumb-item active">Shop</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Ec breadcrumb end -->

    <form method="GET" action="{{ route('visitor.products.index') }}" id="filterForm">
        <section class="ec-page-content section-space-p">
            <div class="container">
                <div class="row">
                    <div class="ec-shop-rightside col-lg-12 col-md-12">
                        <!-- Shop Top Start -->
                        <div class="ec-pro-list-top d-flex">
                            <div class="col-md-6 ec-grid-list">
                                <div class="ec-gl-btn">
                                    <button type="button" class="btn sidebar-toggle-icon"><i
                                            class="fi-rr-filter"></i></button>
                                    <button type="button" class="btn btn-grid-50 active"><i
                                            class="fi-rr-apps"></i></button>
                                    <button type="button" class="btn btn-list-50"><i class="fi-rr-list"></i></button>
                                </div>
                                @if (
                                    !empty($filters['category']) ||
                                        !empty($filters['color']) ||
                                        !empty($filters['size']) ||
                                        $filters['price_min'] ||
                                        $filters['price_max']
                                )
                                    <a href="{{ route('visitor.products.index') }}" class="btn btn-sm btn-light ms-2">Clear
                                        Filters</a>
                                @endif
                            </div>
                            <div class="col-md-6 ec-sort-select">
                                <span class="sort-by">Sort by</span>
                                <div class="ec-select-inner">
                                    <select name="sort" id="ec-select" class="filter-auto-submit">
                                        <option value="relevance" {{ $filters['sort'] == 'relevance' ? 'selected' : '' }}>
                                            Relevance</option>
                                        <option value="name_asc" {{ $filters['sort'] == 'name_asc' ? 'selected' : '' }}>
                                            Name, A to Z</option>
                                        <option value="name_desc" {{ $filters['sort'] == 'name_desc' ? 'selected' : '' }}>
                                            Name, Z to A</option>
                                        <option value="price_asc" {{ $filters['sort'] == 'price_asc' ? 'selected' : '' }}>
                                            Price, low to high</option>
                                        <option value="price_desc" {{ $filters['sort'] == 'price_desc' ? 'selected' : '' }}>
                                            Price, high to low</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <!-- Shop Top End -->

                        <!-- Shop content Start -->
                        <div class="shop-pro-content">
                            <div class="shop-pro-inner">
                                <div class="row">
                                    @forelse ($products as $product)
                                        <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6 mb-6 pro-gl-content">
                                            <div class="ec-product-inner">
                                                <div class="ec-pro-image-outer">
                                                    <div class="ec-pro-image">
                                                        <a href="#" class="image">
                                                            <img class="main-image"
                                                                src="{{ $product->thumbnail_url ?? asset('visitor/images/product-image/6_1.jpg') }}"
                                                                alt="{{ $product->name }}" />
                                                        </a>
                                                        @if ($product->has_discount)
                                                            <span
                                                                class="percentage">{{ round($product->discount_percentage, 2) }}%</span>
                                                        @endif
                                                        <span class="flags">
                                                            <span class="sale">Sale</span>
                                                        </span>
                                                        <div class="ec-pro-actions">
                                                            <a href="#" class="ec-btn-group compare"
                                                                title="Quick view" data-bs-toggle="modal"
                                                                data-link-action="quickview"
                                                                data-bs-target="#ec_quickview_modal">
                                                                <i class="fi-rr-eye"></i>
                                                            </a>
                                                            <button title="Add To Cart" class="add-to-cart" type="button">
                                                                <i class="fi-rr-shopping-basket"></i> Add To Cart
                                                            </button>
                                                            <a class="ec-btn-group wishlist" title="Wishlist"><i
                                                                    class="fi-rr-heart"></i></a>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="ec-pro-content">
                                                    <h5 class="ec-pro-title">
                                                        <a href="#">{{ $product->name }}</a>
                                                    </h5>
                                                    <div class="ec-pro-rating">
                                                        <i class="ecicon eci-star fill"></i>
                                                        <i class="ecicon eci-star fill"></i>
                                                        <i class="ecicon eci-star fill"></i>
                                                        <i class="ecicon eci-star fill"></i>
                                                        <i class="ecicon eci-star"></i>
                                                    </div>
                                                    <div class="ec-pro-list-desc">
                                                        <p>{{ $product->short_description }}</p>
                                                    </div>
                                                    <span class="ec-price">
                                                        @if ($product->has_discount)
                                                            <span
                                                                class="old-price">${{ number_format($product->price, 2) }}</span>
                                                            <span
                                                                class="new-price">${{ number_format($product->final_price, 2) }}</span>
                                                        @else
                                                            <span
                                                                class="new-price">${{ number_format($product->price, 2) }}</span>
                                                        @endif
                                                    </span>
                                                    <div class="ec-pro-option">
                                                        <div class="ec-pro-color">
                                                            <span class="ec-pro-opt-label">Color</span>
                                                            <ul class="ec-opt-swatch ec-change-img">
                                                                <li class="active"><a href="#" class="ec-opt-clr-img"
                                                                        data-src="assets/images/product-image/6_1.jpg"
                                                                        data-src-hover="assets/images/product-image/6_1.jpg"
                                                                        data-tooltip="Gray"><span
                                                                            style="background-color:#e8c2ff;"></span></a>
                                                                </li>
                                                                <li><a href="#" class="ec-opt-clr-img"
                                                                        data-src="assets/images/product-image/6_2.jpg"
                                                                        data-src-hover="assets/images/product-image/6_2.jpg"
                                                                        data-tooltip="Orange"><span
                                                                            style="background-color:#9cfdd5;"></span></a>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                        <div class="ec-pro-size">
                                                            <span class="ec-pro-opt-label">Size</span>
                                                            <ul class="ec-opt-size">
                                                                <li class="active"><a href="#" class="ec-opt-sz"
                                                                        data-old="$25.00" data-new="$20.00"
                                                                        data-tooltip="Small">S</a></li>
                                                                <li><a href="#" class="ec-opt-sz" data-old="$27.00"
                                                                        data-new="$22.00" data-tooltip="Medium">M</a></li>
                                                                <li><a href="#" class="ec-opt-sz" data-old="$35.00"
                                                                        data-new="$30.00"
                                                                        data-tooltip="Extra Large">XL</a></li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="col-12 text-center py-5">
                                            <p class="mb-0">No products found matching your filters.</p>
                                        </div>
                                    @endforelse
                                </div>
                            </div>

                            @include('visitor.partials.catalog-pagination', ['products' => $products])
                        </div>
                        <!--Shop content End -->
                    </div>

                    <!-- Sidebar Area Start -->
                    <div class="filter-sidebar-overlay"></div>
                    <div class="ec-shop-leftside filter-sidebar">
                        <div class="ec-sidebar-heading">
                            <h1>Filter Products By</h1>
                            <a class="filter-cls-btn" href="javascript:void(0)">×</a>
                        </div>
                        <div class="ec-sidebar-wrap">

                            <!-- Category Filter -->
                            @if ($categories->isNotEmpty())
                                <div class="ec-sidebar-block">
                                    <div class="ec-sb-title">
                                        <h3 class="ec-sidebar-title">Category</h3>
                                    </div>
                                    <div class="ec-sb-block-content">
                                        <ul>
                                            @foreach ($categories->take(6) as $category)
                                                <li>
                                                    <div class="ec-sidebar-block-item">
                                                        <input type="checkbox" name="category[]"
                                                            value="{{ $category->id }}" class="filter-auto-submit"
                                                            {{ in_array($category->id, $filters['category']) ? 'checked' : '' }} />
                                                        <a href="#">{{ $category->name }}</a><span
                                                            class="checked"></span>
                                                    </div>
                                                </li>
                                            @endforeach
                                            @if ($categories->count() > 6)
                                                <li id="ec-more-toggle-content" style="padding: 0; display: none;">
                                                    <ul>
                                                        @foreach ($categories->skip(6) as $category)
                                                            <li>
                                                                <div class="ec-sidebar-block-item">
                                                                    <input type="checkbox" name="category[]"
                                                                        value="{{ $category->id }}"
                                                                        class="filter-auto-submit"
                                                                        {{ in_array($category->id, $filters['category']) ? 'checked' : '' }} />
                                                                    <a href="#">{{ $category->name }}</a><span
                                                                        class="checked"></span>
                                                                </div>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </li>
                                                <li>
                                                    <div class="ec-sidebar-block-item ec-more-toggle">
                                                        <span class="checked"></span><span id="ec-more-toggle">More
                                                            Categories</span>
                                                    </div>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                </div>
                            @endif

                            <!-- Size Filter -->
                            @if (!empty($sizes))
                                <div class="ec-sidebar-block">
                                    <div class="ec-sb-title">
                                        <h3 class="ec-sidebar-title">Size</h3>
                                    </div>
                                    <div class="ec-sb-block-content">
                                        <ul>
                                            @foreach ($sizes as $size)
                                                <li>
                                                    <div class="ec-sidebar-block-item">
                                                        <input type="checkbox" name="size[]"
                                                            value="{{ $size }}" class="filter-auto-submit"
                                                            {{ in_array($size, $filters['size']) ? 'checked' : '' }} />
                                                        <a href="#">{{ $size }}</a><span
                                                            class="checked"></span>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            @endif

                            <!-- Color Filter -->
                            @if (!empty($colors))
                                <div class="ec-sidebar-block ec-sidebar-block-clr">
                                    <div class="ec-sb-title">
                                        <h3 class="ec-sidebar-title">Color</h3>
                                    </div>
                                    <div class="ec-sb-block-content">
                                        <ul>
                                            @foreach ($colors as $color)
                                                <li class="{{ in_array($color, $filters['color']) ? 'active' : '' }}">
                                                    <label class="ec-sidebar-block-item" title="{{ $color }}"
                                                        style="cursor:pointer">
                                                        <input type="checkbox" name="color[]"
                                                            value="{{ $color }}" class="filter-auto-submit d-none"
                                                            {{ in_array($color, $filters['color']) ? 'checked' : '' }} />
                                                        <span style="background-color: {{ strtolower($color) }};"></span>
                                                    </label>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            @endif

                            <!-- Price Filter -->
                            <div class="ec-sidebar-block">
                                <div class="ec-sb-title">
                                    <h3 class="ec-sidebar-title">Price</h3>
                                </div>
                                <div class="ec-sb-block-content es-price-slider">
                                    <div class="ec-price-filter">
                                        <div id="ec-sliderPrice" class="filter__slider-price"
                                            data-min="{{ floor($priceBounds['min']) }}"
                                            data-max="{{ ceil($priceBounds['max']) }}" data-step="1"
                                            data-current-min="{{ $filters['price_min'] ?? floor($priceBounds['min']) }}"
                                            data-current-max="{{ $filters['price_max'] ?? ceil($priceBounds['max']) }}">
                                        </div>
                                        <div class="ec-price-input">
                                            <label class="filter__label">
                                                <input type="text" class="filter__input" id="priceMinDisplay"
                                                    readonly>
                                            </label>
                                            <span class="ec-price-divider"></span>
                                            <label class="filter__label">
                                                <input type="text" class="filter__input" id="priceMaxDisplay"
                                                    readonly>
                                            </label>
                                        </div>
                                        <input type="hidden" name="price_min" id="priceMinInput"
                                            value="{{ $filters['price_min'] }}">
                                        <input type="hidden" name="price_max" id="priceMaxInput"
                                            value="{{ $filters['price_max'] }}">
                                        <button type="submit" class="btn btn-sm btn-primary mt-2 w-100">Apply Price
                                            Filter</button>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </section>
    </form>

    <!-- Modal -->
    <div class="modal fade" id="ec_quickview_modal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <button type="button" class="btn-close qty_close" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-5 col-sm-12 col-xs-12">
                            <!-- Swiper -->
                            <div class="qty-product-cover">
                                <div class="qty-slide">
                                    <img class="img-responsive" src="{{ asset('visitor/images/product-image/3_1.jpg') }}"
                                        alt="">
                                </div>
                                <div class="qty-slide">
                                    <img class="img-responsive" src="{{ asset('visitor/images/product-image/3_2.jpg') }}"
                                        alt="">
                                </div>
                                <div class="qty-slide">
                                    <img class="img-responsive" src="{{ asset('visitor/images/product-image/3_3.jpg') }}"
                                        alt="">
                                </div>
                                <div class="qty-slide">
                                    <img class="img-responsive" src="{{ asset('visitor/images/product-image/3_4.jpg') }}"
                                        alt="">
                                </div>
                                <div class="qty-slide">
                                    <img class="img-responsive" src="{{ asset('visitor/images/product-image/3_5.jpg') }}"
                                        alt="">
                                </div>
                            </div>
                            <div class="qty-nav-thumb">
                                <div class="qty-slide">
                                    <img class="img-responsive" src="{{ asset('visitor/images/product-image/3_1.jpg') }}"
                                        alt="">
                                </div>
                                <div class="qty-slide">
                                    <img class="img-responsive" src="{{ asset('visitor/images/product-image/3_2.jpg') }}"
                                        alt="">
                                </div>
                                <div class="qty-slide">
                                    <img class="img-responsive" src="{{ asset('visitor/images/product-image/3_3.jpg') }}"
                                        alt="">
                                </div>
                                <div class="qty-slide">
                                    <img class="img-responsive" src="{{ asset('visitor/images/product-image/3_4.jpg') }}"
                                        alt="">
                                </div>
                                <div class="qty-slide">
                                    <img class="img-responsive" src="{{ asset('visitor/images/product-image/3_5.jpg') }}"
                                        alt="">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-7 col-sm-12 col-xs-12">
                            <div class="quickview-pro-content">
                                <h5 class="ec-quick-title"><a href="product-left-sidebar.html">Handbag leather purse
                                        for
                                        women</a>
                                </h5>
                                <div class="ec-quickview-rating">
                                    <i class="ecicon eci-star fill"></i>
                                    <i class="ecicon eci-star fill"></i>
                                    <i class="ecicon eci-star fill"></i>
                                    <i class="ecicon eci-star fill"></i>
                                    <i class="ecicon eci-star"></i>
                                </div>

                                <div class="ec-quickview-desc">Lorem Ipsum is simply dummy text of the printing and
                                    typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever
                                    since the 1500s,</div>
                                <div class="ec-quickview-price">
                                    <span class="old-price">$100.00</span>
                                    <span class="new-price">$80.00</span>
                                </div>

                                <div class="ec-pro-variation">
                                    <div class="ec-pro-variation-inner ec-pro-variation-color">
                                        <span>Color</span>
                                        <div class="ec-pro-color">
                                            <ul class="ec-opt-swatch">
                                                <li><span style="background-color:#ebbf60;"></span></li>
                                                <li><span style="background-color:#75e3ff;"></span></li>
                                                <li><span style="background-color:#11f7d8;"></span></li>
                                                <li><span style="background-color:#acff7c;"></span></li>
                                                <li><span style="background-color:#e996fa;"></span></li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="ec-pro-variation-inner ec-pro-variation-size ec-pro-size">
                                        <span>Size</span>
                                        <div class="ec-pro-variation-content">
                                            <ul class="ec-opt-size">
                                                <li class="active"><a href="#" class="ec-opt-sz"
                                                        data-tooltip="Small">S</a></li>
                                                <li><a href="#" class="ec-opt-sz" data-tooltip="Medium">M</a>
                                                </li>
                                                <li><a href="#" class="ec-opt-sz" data-tooltip="Large">X</a></li>
                                                <li><a href="#" class="ec-opt-sz" data-tooltip="Extra Large">XL</a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="ec-quickview-qty">
                                    <div class="qty-plus-minus">
                                        <input class="qty-input" type="text" name="ec_qtybtn" value="1" />
                                    </div>
                                    <div class="ec-quickview-cart ">
                                        <button class="btn btn-primary"><i class="fi-rr-shopping-basket"></i> Add To
                                            Cart</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal end -->
@endsection

@section('scripts')
    {{-- @vite(['resources/js/pages/visitor-products.js']) --}}
@endsection
