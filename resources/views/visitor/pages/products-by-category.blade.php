@extends('visitor.layout.app', ['title' => 'Sodai - ' . $category->name, 'bodyClass' => 'shop_page'])

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
                            <h2 class="ec-breadcrumb-title">{{ $category->name }}</h2>
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <ul class="ec-breadcrumb-list">
                                <li class="ec-breadcrumb-item"><a href="{{ route('visitor.index') }}">Home</a></li>
                                <li class="ec-breadcrumb-item"><a href="{{ route('visitor.products.index') }}">Shop</a></li>
                                <li class="ec-breadcrumb-item active">{{ $category->name }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Ec breadcrumb end -->

    <!-- Page detail section -->
    <section class="ec-bnr-detail margin-bottom-30 section-space-pt">
        <div class="ec-page-detail">
            <div class="container">
                <div class="ec-main-heading d-none">
                    <h2>Shop <span>Detail</span></h2>
                </div>
                <div class="row">
                    <div class="col-lg-6 col-md-12">
                        <div class="ec-cat-bnr">
                            <img style="background-size: cover; background-position: left center; background-repeat: no-repeat;"
                                    height="250px" width="100%" src="{{ Storage::url($category->image) }}" alt="{{ $category->name }}">
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-12">
                        <div class="ec-page-description">
                            <h6>{{ $category->name }}</h6>
                            <p class="m-0">
                                {{ $category->description }}
                            <p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- End detail section -->

    <form method="GET" action="{{ route('visitor.products.category', $category) }}" id="filterForm">
        <section class="ec-page-content-bnr section-space-pb">
            <div class="container">
                <div class="row">
                    <div class="ec-shop-rightside col-lg-12 col-md-12">
                        <!-- Shop Top Start -->
                        <div class="ec-pro-list-top d-flex">
                            <div class="col-md-6 ec-grid-list">
                                <div class="ec-gl-btn">
                                    <button type="button" class="btn sidebar-toggle-icon"><i class="fi-rr-filter"></i></button>
                                    <button type="button" class="btn btn-grid-50 active"><i class="fi-rr-apps"></i></button>
                                    <button type="button" class="btn btn-list-50"><i class="fi-rr-list"></i></button>
                                </div>
                                @if (!empty($filters['color']) || !empty($filters['size']) || $filters['price_min'] || $filters['price_max'])
                                    <a href="{{ route('visitor.products.category', $category) }}" class="btn btn-sm btn-light ms-2">Clear Filters</a>
                                @endif
                            </div>
                            <div class="col-md-6 ec-sort-select">
                                <span class="sort-by">Sort by</span>
                                <div class="ec-select-inner">
                                    <select name="sort" id="ec-select" class="filter-auto-submit">
                                        <option value="relevance" {{ $filters['sort'] == 'relevance' ? 'selected' : '' }}>Relevance</option>
                                        <option value="name_asc" {{ $filters['sort'] == 'name_asc' ? 'selected' : '' }}>Name, A to Z</option>
                                        <option value="name_desc" {{ $filters['sort'] == 'name_desc' ? 'selected' : '' }}>Name, Z to A</option>
                                        <option value="price_asc" {{ $filters['sort'] == 'price_asc' ? 'selected' : '' }}>Price, low to high</option>
                                        <option value="price_desc" {{ $filters['sort'] == 'price_desc' ? 'selected' : '' }}>Price, high to low</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <!-- Shop Top End -->

                        <div class="shop-pro-content">
                            <div class="shop-pro-inner">
                                <div class="row">
                                    @forelse ($products as $product)
                                        <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6 mb-6 pro-gl-content">
                                            <div class="ec-product-inner">
                                                <div class="ec-pro-image-outer">
                                                    <div class="ec-pro-image">
                                                        <a href="#" class="image">
                                                            <img class="main-image" src="{{ $product->thumbnail_url ?? asset('visitor/images/product-image/6_1.jpg') }}" alt="{{ $product->name }}" />
                                                        </a>
                                                        @if ($product->has_discount)
                                                            <span class="percentage">{{ round($product->discount_percentage) }}%</span>
                                                        @endif
                                                        <div class="ec-pro-actions">
                                                            <button title="Add To Cart" class="add-to-cart" type="button"><i class="fi-rr-shopping-basket"></i> Add To Cart</button>
                                                            <a class="ec-btn-group wishlist" title="Wishlist"><i class="fi-rr-heart"></i></a>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="ec-pro-content">
                                                    <h5 class="ec-pro-title"><a href="#">{{ $product->name }}</a></h5>
                                                    <span class="ec-price">
                                                        @if ($product->has_discount)
                                                            <span class="old-price">${{ number_format($product->price, 2) }}</span>
                                                            <span class="new-price">${{ number_format($product->final_price, 2) }}</span>
                                                        @else
                                                            <span class="new-price">${{ number_format($product->price, 2) }}</span>
                                                        @endif
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="col-12 text-center py-5">
                                            <p class="mb-0">No products found in this category matching your filters.</p>
                                        </div>
                                    @endforelse
                                </div>
                            </div>

                            @include('visitor.partials.catalog-pagination', ['products' => $products])
                        </div>
                    </div>

                    <!-- Sidebar Area Start -->
                    <div class="filter-sidebar-overlay"></div>
                    <div class="ec-shop-leftside filter-sidebar">
                        <div class="ec-sidebar-heading">
                            <h1>Filter Products By</h1>
                            <a class="filter-cls-btn" href="javascript:void(0)">×</a>
                        </div>
                        <div class="ec-sidebar-wrap">

                            <!-- Size Filter -->
                            @if (!empty($sizes))
                                <div class="ec-sidebar-block">
                                    <div class="ec-sb-title"><h3 class="ec-sidebar-title">Size</h3></div>
                                    <div class="ec-sb-block-content">
                                        <ul>
                                            @foreach ($sizes as $size)
                                                <li>
                                                    <div class="ec-sidebar-block-item">
                                                        <input type="checkbox" name="size[]" value="{{ $size }}"
                                                            class="filter-auto-submit"
                                                            {{ in_array($size, $filters['size']) ? 'checked' : '' }} />
                                                        <a href="#">{{ $size }}</a><span class="checked"></span>
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
                                    <div class="ec-sb-title"><h3 class="ec-sidebar-title">Color</h3></div>
                                    <div class="ec-sb-block-content">
                                        <ul>
                                            @foreach ($colors as $color)
                                                <li class="{{ in_array($color, $filters['color']) ? 'active' : '' }}">
                                                    <label class="ec-sidebar-block-item" title="{{ $color }}" style="cursor:pointer">
                                                        <input type="checkbox" name="color[]" value="{{ $color }}"
                                                            class="filter-auto-submit d-none"
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
                                <div class="ec-sb-title"><h3 class="ec-sidebar-title">Price</h3></div>
                                <div class="ec-sb-block-content es-price-slider">
                                    <div class="ec-price-filter">
                                        <div id="ec-sliderPrice" class="filter__slider-price"
                                            data-min="{{ floor($priceBounds['min']) }}"
                                            data-max="{{ ceil($priceBounds['max']) }}"
                                            data-step="1"
                                            data-current-min="{{ $filters['price_min'] ?? floor($priceBounds['min']) }}"
                                            data-current-max="{{ $filters['price_max'] ?? ceil($priceBounds['max']) }}"></div>
                                        <div class="ec-price-input">
                                            <label class="filter__label">
                                                <input type="text" class="filter__input" id="priceMinDisplay" readonly>
                                            </label>
                                            <span class="ec-price-divider"></span>
                                            <label class="filter__label">
                                                <input type="text" class="filter__input" id="priceMaxDisplay" readonly>
                                            </label>
                                        </div>
                                        <input type="hidden" name="price_min" id="priceMinInput" value="{{ $filters['price_min'] }}">
                                        <input type="hidden" name="price_max" id="priceMaxInput" value="{{ $filters['price_max'] }}">
                                        <button type="submit" class="btn btn-sm btn-primary mt-2 w-100">Apply Price Filter</button>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </section>
    </form>
@endsection

@section('scripts')
    {{-- @vite(['resources/js/pages/visitor-products.js']) --}}
@endsection