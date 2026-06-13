@extends('visitor.layout.app')

@section('title', 'Sodai')

@section('content')
<!-- Ekka Cart Start -->
<div class="ec-side-cart-overlay"></div>
<div id="ec-side-cart" class="ec-side-cart">
    <div class="ec-cart-inner">
        <div class="ec-cart-top">
            <div class="ec-cart-title">
                <span class="cart_title">My Cart</span>
                <button class="ec-close">×</button>
            </div>
            <ul class="eccart-pro-items">
                <li>
                    <a href="product-left-sidebar.html" class="sidecart_pro_img"><img
                            src="visitor/images/product-image/35_1.jpg" alt="product"></a>
                    <div class="ec-pro-content">
                        <a href="single-product-left-sidebar.html" class="cart_pro_title">Hooded Neck full slive
                            T-Shirt</a>
                        <span class="cart-price"><span>$84.00</span> x 1</span>
                        <div class="qty-plus-minus">
                            <input class="qty-input" type="text" name="ec_qtybtn" value="1" />
                        </div>
                        <a href="#" class="remove">×</a>
                    </div>
                </li>
                <li>
                    <a href="product-left-sidebar.html" class="sidecart_pro_img"><img
                            src="visitor/images/product-image/36_1.jpg" alt="product"></a>
                    <div class="ec-pro-content">
                        <a href="product-left-sidebar.html" class="cart_pro_title">Perfume royal msz</a>
                        <span class="cart-price"><span>$125.00</span> x 1</span>
                        <div class="qty-plus-minus">
                            <input class="qty-input" type="text" name="ec_qtybtn" value="1" />
                        </div>
                        <a href="#" class="remove">×</a>
                    </div>
                </li>
                <li>
                    <a href="product-left-sidebar.html" class="sidecart_pro_img"><img
                            src="visitor/images/product-image/37_1.jpg" alt="product"></a>
                    <div class="ec-pro-content">
                        <a href="product-left-sidebar.html" class="cart_pro_title">Lime Crime Velvetines Liquid Matte
                            Lipstick</a>
                        <span class="cart-price"><span>$48.00</span> x 1</span>
                        <div class="qty-plus-minus">
                            <input class="qty-input" type="text" name="ec_qtybtn" value="1" />
                        </div>
                        <a href="#" class="remove">×</a>
                    </div>
                </li>
            </ul>
        </div>
        <div class="ec-cart-bottom">
            <div class="cart-sub-total">
                <table class="table cart-table">
                    <tbody>
                        <tr>
                            <td class="text-left">Sub-Total :</td>
                            <td class="text-right">$300.00</td>
                        </tr>
                        <tr>
                            <td class="text-left">VAT (20%) :</td>
                            <td class="text-right">$60.00</td>
                        </tr>
                        <tr>
                            <td class="text-left">Total :</td>
                            <td class="text-right primary-color">$360.00</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="cart_btn">
                <a href="cart.html" class="btn btn-primary">View Cart</a>
                <a href="checkout.html" class="btn btn-secondary">Checkout</a>
            </div>
        </div>
    </div>
</div>
<!-- Ekka Cart End -->

<!-- Main Slider Start -->
<div class="ec-main-slider section section-space-pb">
    <div class="ec-slider swiper-container main-slider-nav main-slider-dot">
        <!-- Main slider -->
        <div class="swiper-wrapper">
            <div class="ec-slide-item swiper-slide d-flex slide-1">
                <div class="container align-self-center">
                    <div class="row">
                        <div class="col-sm-12 align-self-center">
                            <div class="ec-slide-content slider-animation">
                                <h2 class="ec-slide-stitle">Stylish & comfort</h2>
                                <h1 class="ec-slide-title">Living sofa</h1>
                                <div class="ec-slide-desc">
                                    <p>starting at $ <b>2999</b>.99</p>
                                    <a href="#" class="btn btn-lg btn-primary">Shop Now <i
                                            class="ecicon eci-angle-double-right" aria-hidden="true"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="ec-slide-item swiper-slide d-flex slide-2">
                <div class="container align-self-center">
                    <div class="row">
                        <div class="col-sm-12 align-self-center">
                            <div class="ec-slide-content slider-animation">
                                <h2 class="ec-slide-stitle">Home appliances</h2>
                                <h1 class="ec-slide-title">Furniture</h1>
                                <div class="ec-slide-desc">
                                    <p>starting at $ <b>1900</b>.00</p>
                                    <a href="#" class="btn btn-lg btn-primary">Shop Now <i
                                            class="ecicon eci-angle-double-right" aria-hidden="true"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="swiper-pagination swiper-pagination-white"></div>
        <div class="swiper-buttons">
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
        </div>
    </div>
</div>
<!-- Main Slider End -->

<!--  category Section Start -->
<section class="section ec-category-section section-space-p">
    <div class="container">
        <div class="row d-none">
            <div class="col-md-12">
                <div class="section-title">
                    <h2 class="ec-title">Top Category</h2>
                </div>
            </div>
        </div>
        <div class="row margin-minus-b-15 margin-minus-t-15">
            <div class="ec_cat_content ec_cat_content_1 col-sm-12 col-md-6 col-lg-3">
                <div class="ec_cat_inner">
                    <div class="ec-category-image">
                        <img src="visitor/images/category-image/6.jpg" alt="" />
                    </div>
                    <div class="ec-category-desc">
                        <h3>phone</h3>
                        <ul>
                            <li><a href="#">Bumpers</a></li>
                            <li><a href="#">Doors</a></li>
                            <li><a href="#">Fenders</a></li>
                            <li><a href="#">Mirrors</a></li>
                        </ul>
                        <a href="#" class="cat-show-all">Show All <i class="ecicon eci-angle-double-right"
                                aria-hidden="true"></i></a>
                    </div>
                </div>
            </div>
            <div class="ec_cat_content ec_cat_content_2 col-sm-12 col-md-6 col-lg-3">
                <div class="ec_cat_inner">
                    <div class="ec-category-image">
                        <img src="visitor/images/category-image/7.jpg" alt="" />
                    </div>
                    <div class="ec-category-desc">
                        <h3>Furniture</h3>
                        <ul>
                            <li><a href="#">Bumpers</a></li>
                            <li><a href="#">Doors</a></li>
                            <li><a href="#">Fenders</a></li>
                            <li><a href="#">Mirrors</a></li>
                        </ul>
                        <a href="#" class="cat-show-all">Show All <i class="ecicon eci-angle-double-right"
                                aria-hidden="true"></i></a>
                    </div>
                </div>
            </div>
            <div class="ec_cat_content ec_cat_content_3 col-sm-12 col-md-6 col-lg-3">
                <div class="ec_cat_inner">
                    <div class="ec-category-image">
                        <img src="visitor/images/category-image/8.jpg" alt="" />
                    </div>
                    <div class="ec-category-desc">
                        <h3>Clothing</h3>
                        <ul>
                            <li><a href="#">Bumpers</a></li>
                            <li><a href="#">Doors</a></li>
                            <li><a href="#">Fenders</a></li>
                            <li><a href="#">Mirrors</a></li>
                        </ul>
                        <a href="#" class="cat-show-all">Show All <i class="ecicon eci-angle-double-right"
                                aria-hidden="true"></i></a>
                    </div>
                </div>
            </div>
            <div class="ec_cat_content ec_cat_content_4 col-sm-12 col-md-6 col-lg-3">
                <div class="ec_cat_inner">
                    <div class="ec-category-image">
                        <img src="visitor/images/category-image/9.jpg" alt="" />
                    </div>
                    <div class="ec-category-desc">
                        <h3>Beauty</h3>
                        <ul>
                            <li><a href="#">Bumpers</a></li>
                            <li><a href="#">Doors</a></li>
                            <li><a href="#">Fenders</a></li>
                            <li><a href="#">Mirrors</a></li>
                        </ul>
                        <a href="#" class="cat-show-all">Show All <i class="ecicon eci-angle-double-right"
                                aria-hidden="true"></i></a>
                    </div>
                </div>
            </div>
            <div class="ec_cat_content ec_cat_content_5 col-sm-12 col-md-6 col-lg-3">
                <div class="ec_cat_inner">
                    <div class="ec-category-image">
                        <img src="visitor/images/category-image/10.jpg" alt="" />
                    </div>
                    <div class="ec-category-desc">
                        <h3>Watches</h3>
                        <ul>
                            <li><a href="#">Bumpers</a></li>
                            <li><a href="#">Doors</a></li>
                            <li><a href="#">Fenders</a></li>
                            <li><a href="#">Mirrors</a></li>
                        </ul>
                        <a href="#" class="cat-show-all">Show All <i class="ecicon eci-angle-double-right"
                                aria-hidden="true"></i></a>
                    </div>
                </div>
            </div>
            <div class="ec_cat_content ec_cat_content_6 col-sm-12 col-md-6 col-lg-3">
                <div class="ec_cat_inner">
                    <div class="ec-category-image">
                        <img src="visitor/images/category-image/11.jpg" alt="" />
                    </div>
                    <div class="ec-category-desc">
                        <h3>Jewellery</h3>
                        <ul>
                            <li><a href="#">Bumpers</a></li>
                            <li><a href="#">Doors</a></li>
                            <li><a href="#">Fenders</a></li>
                            <li><a href="#">Mirrors</a></li>
                        </ul>
                        <a href="#" class="cat-show-all">Show All <i class="ecicon eci-angle-double-right"
                                aria-hidden="true"></i></a>
                    </div>
                </div>
            </div>
            <div class="ec_cat_content ec_cat_content_7 col-sm-12 col-md-6 col-lg-3">
                <div class="ec_cat_inner">
                    <div class="ec-category-image">
                        <img src="visitor/images/category-image/12.jpg" alt="" />
                    </div>
                    <div class="ec-category-desc">
                        <h3>Fashion</h3>
                        <ul>
                            <li><a href="#">Bumpers</a></li>
                            <li><a href="#">Doors</a></li>
                            <li><a href="#">Fenders</a></li>
                            <li><a href="#">Mirrors</a></li>
                        </ul>
                        <a href="#" class="cat-show-all">Show All <i class="ecicon eci-angle-double-right"
                                aria-hidden="true"></i></a>
                    </div>
                </div>
            </div>
            <div class="ec_cat_content ec_cat_content_8 col-sm-12 col-md-6 col-lg-3">
                <div class="ec_cat_inner">
                    <div class="ec-category-image">
                        <img src="visitor/images/category-image/13.jpg" alt="" />
                    </div>
                    <div class="ec-category-desc">
                        <h3>Electronic</h3>
                        <ul>
                            <li><a href="#">Bumpers</a></li>
                            <li><a href="#">Doors</a></li>
                            <li><a href="#">Fenders</a></li>
                            <li><a href="#">Mirrors</a></li>
                        </ul>
                        <a href="#" class="cat-show-all">Show All <i class="ecicon eci-angle-double-right"
                                aria-hidden="true"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!--category Section End -->

<!--  offer Section Start -->
<section class="section ec-offer-secti section-space-m">
    <h2 class="d-none">Offer</h2>
    <div class="container">
        <div class="row ec-pos ec-ofr-bnr">
            <!-- <img src="visitor/images/offer-image/offer_bg.jpg" alt="" class="offer_bg" /> -->
            <div class="ec-offer-inner">
                <div class="col-xl-5 col-md-6 col-sm-6 align-self-center ec-offer-content">
                    <h3 class="ec-offer-stitle">Start Today !</h3>
                    <h2 class="ec-offer-title">The Trends Benefits</h2>
                    <span class="ec-offer-desc">Select From Wide Range of Collection</span>
                    <span class="ec-offer-btn"><a href="#" class="btn btn-lg btn-primary">Shop Now <i
                                class="ecicon eci-angle-double-right" aria-hidden="true"></i></a></span>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- offer Section End -->

<!-- Product tab Area Start -->
<section class="section ec-product-tab section-space-p">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="section-title">
                    <h2 class="ec-title">New Products</h2>
                </div>
            </div>

            <!-- Tab Start -->
            <div class="col-md-12 ec-pro-tab">
                <ul class="ec-pro-tab-nav nav justify-content-end">
                    <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab"
                            href="#tab-pro-new-arrivals">Sale Products</a></li>
                    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-pro-special-offer">Top
                            Rated Products</a></li>
                    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab"
                            href="#tab-pro-best-sellers">Latest Products</a></li>
                </ul>
            </div>
            <!-- Tab End -->
        </div>
        <div class="row margin-minus-b-15">
            <div class="col">
                <div class="tab-content">
                    <!-- 1st Product tab start -->
                    <div class="tab-pane fade show active" id="tab-pro-new-arrivals">
                        <div class="row">
                            <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6 ec-product-content">
                                <div class="ec-product-inner">
                                    <div class="ec-pro-image-outer">
                                        <div class="ec-pro-image">
                                            <a href="product-left-sidebar.html" class="image">
                                                <img class="main-image" src="visitor/images/product-image/29_1.jpg"
                                                    alt="Product" />
                                                <img class="hover-image" src="visitor/images/product-image/29_4.jpg"
                                                    alt="Product" />
                                            </a>
                                            <span class="flags">
                                                <span class="new">New</span>
                                            </span>
                                            <div class="ec-pro-actions">
                                                <a class="ec-btn-group wishlist" title="Wishlist"><i
                                                        class="fi-rr-heart"></i></a>
                                                <a href="#" class="ec-btn-group quickview"
                                                    data-link-action="quickview" title="Quick view"
                                                    data-bs-toggle="modal" data-bs-target="#ec_quickview_modal"><i
                                                        class="fi-rr-eye"></i></a>
                                                <a href="compare.html" class="ec-btn-group compare"
                                                    title="Compare"><i class="fi fi-rr-arrows-repeat"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="ec-pro-content">
                                        <a href="shop-left-sidebar-col-3.html">
                                            <h6 class="ec-pro-stitle">Toys</h6>
                                        </a>
                                        <h5 class="ec-pro-title"><a href="product-left-sidebar.html">Baby toy teddy
                                                bear</a></h5>
                                        <div class="ec-pro-rat-price">
                                            <span class="ec-price">
                                                <span class="new-price">$199.00</span>
                                                <span class="old-price">$200.00</span>
                                            </span>
                                            <span class="ec-pro-rating">
                                                <i class="ecicon eci-star fill"></i>
                                                <i class="ecicon eci-star fill"></i>
                                                <i class="ecicon eci-star fill"></i>
                                                <i class="ecicon eci-star fill"></i>
                                                <i class="ecicon eci-star"></i>
                                            </span>
                                        </div>
                                        <div class="ec-pro-btn">
                                            <button title="Add To Cart" class="add-to-cart">Add To Cart</button>
                                            <button title="Select Option" class="select-option">Select
                                                Option</button>
                                            <div class="ec-pro-option">
                                                <div class="ec-pro-opt-inner">
                                                    <div class="ec-pro-color">
                                                        <ul class="ec-opt-swatch ec-change-img">
                                                            <li class="active"><a href="#"
                                                                    class="ec-opt-clr-img"
                                                                    data-src="visitor/images/product-image/29_1.jpg"
                                                                    data-src-hover="visitor/images/product-image/29_1.jpg"
                                                                    data-tooltip="Gray"><span
                                                                        style="background-color:#a88960;"></span></a>
                                                            </li>
                                                            <li><a href="#" class="ec-opt-clr-img"
                                                                    data-src="visitor/images/product-image/29_2.jpg"
                                                                    data-src-hover="visitor/images/product-image/29_2.jpg"
                                                                    data-tooltip="Orange"><span
                                                                        style="background-color:#f778fc;"></span></a>
                                                            </li>
                                                            <li><a href="#" class="ec-opt-clr-img"
                                                                    data-src="visitor/images/product-image/29_3.jpg"
                                                                    data-src-hover="visitor/images/product-image/29_3.jpg"
                                                                    data-tooltip="Green"><span
                                                                        style="background-color:#8b83ff;"></span></a>
                                                            </li>
                                                            <li><a href="#" class="ec-opt-clr-img"
                                                                    data-src="visitor/images/product-image/29_4.jpg"
                                                                    data-src-hover="visitor/images/product-image/29_4.jpg"
                                                                    data-tooltip="Sky Blue"><span
                                                                        style="background-color:#5ea561;"></span></a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6 ec-product-content">
                                <div class="ec-product-inner">
                                    <div class="ec-pro-image-outer">
                                        <div class="ec-pro-image">
                                            <a href="product-left-sidebar.html" class="image">
                                                <img class="main-image" src="visitor/images/product-image/30_1.jpg"
                                                    alt="Product" />
                                                <img class="hover-image" src="visitor/images/product-image/30_3.jpg"
                                                    alt="Product" />
                                            </a>
                                            <span class="flags">
                                                <span class="sale">Sale</span>
                                            </span>
                                            <div class="ec-pro-actions">
                                                <a class="ec-btn-group wishlist" title="Wishlist"><i
                                                        class="fi-rr-heart"></i></a>
                                                <a href="#" class="ec-btn-group quickview"
                                                    data-link-action="quickview" title="Quick view"
                                                    data-bs-toggle="modal" data-bs-target="#ec_quickview_modal"><i
                                                        class="fi-rr-eye"></i></a>
                                                <a href="compare.html" class="ec-btn-group compare"
                                                    title="Compare"><i class="fi fi-rr-arrows-repeat"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="ec-pro-content">
                                        <a href="shop-left-sidebar-col-3.html">
                                            <h6 class="ec-pro-stitle">Accessories</h6>
                                        </a>
                                        <h5 class="ec-pro-title"><a href="product-left-sidebar.html">Living room
                                                lighting</a></h5>
                                        <div class="ec-pro-rat-price">
                                            <span class="ec-price">
                                                <span class="new-price">$152.00</span>
                                                <span class="old-price">$180.00</span>
                                            </span>
                                            <span class="ec-pro-rating">
                                                <i class="ecicon eci-star fill"></i>
                                                <i class="ecicon eci-star fill"></i>
                                                <i class="ecicon eci-star fill"></i>
                                                <i class="ecicon eci-star fill"></i>
                                                <i class="ecicon eci-star"></i>
                                            </span>
                                        </div>
                                        <div class="ec-pro-btn">
                                            <button title="Add To Cart" class="add-to-cart">Add To Cart</button>
                                            <button title="Select Option" class="select-option">Select
                                                Option</button>
                                            <div class="ec-pro-option">
                                                <div class="ec-pro-opt-inner">
                                                    <div class="ec-pro-color">
                                                        <ul class="ec-opt-swatch ec-change-img">
                                                            <li class="active"><a href="#"
                                                                    class="ec-opt-clr-img"
                                                                    data-src="visitor/images/product-image/30_1.jpg"
                                                                    data-src-hover="visitor/images/product-image/30_1.jpg"
                                                                    data-tooltip="Gray"><span
                                                                        style="background-color:#e9e9e9;"></span></a>
                                                            </li>
                                                            <li><a href="#" class="ec-opt-clr-img"
                                                                    data-src="visitor/images/product-image/30_2.jpg"
                                                                    data-src-hover="visitor/images/product-image/30_2.jpg"
                                                                    data-tooltip="Orange"><span
                                                                        style="background-color:#b6f0ff;"></span></a>
                                                            </li>
                                                            <li><a href="#" class="ec-opt-clr-img"
                                                                    data-src="visitor/images/product-image/30_3.jpg"
                                                                    data-src-hover="visitor/images/product-image/30_3.jpg"
                                                                    data-tooltip="Green"><span
                                                                        style="background-color:#a2ffcc;"></span></a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6 ec-product-content">
                                <div class="ec-product-inner">
                                    <div class="ec-pro-image-outer">
                                        <div class="ec-pro-image">
                                            <a href="product-left-sidebar.html" class="image">
                                                <img class="main-image" src="visitor/images/product-image/31_1.jpg"
                                                    alt="Product" />
                                                <img class="hover-image" src="visitor/images/product-image/31_2.jpg"
                                                    alt="Product" />
                                            </a>
                                            <div class="ec-pro-actions">
                                                <a class="ec-btn-group wishlist" title="Wishlist"><i
                                                        class="fi-rr-heart"></i></a>
                                                <a href="#" class="ec-btn-group quickview"
                                                    data-link-action="quickview" title="Quick view"
                                                    data-bs-toggle="modal" data-bs-target="#ec_quickview_modal"><i
                                                        class="fi-rr-eye"></i></a>
                                                <a href="compare.html" class="ec-btn-group compare"
                                                    title="Compare"><i class="fi fi-rr-arrows-repeat"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="ec-pro-content">
                                        <a href="shop-left-sidebar-col-3.html">
                                            <h6 class="ec-pro-stitle">Furniture</h6>
                                        </a>
                                        <h5 class="ec-pro-title"><a href="product-left-sidebar.html">Bed room night
                                                lamp</a></h5>
                                        <div class="ec-pro-rat-price">
                                            <span class="ec-price">
                                                <span class="new-price">$59.00</span>
                                                <span class="old-price">$87.00</span>
                                            </span>
                                            <span class="ec-pro-rating">
                                                <i class="ecicon eci-star fill"></i>
                                                <i class="ecicon eci-star fill"></i>
                                                <i class="ecicon eci-star fill"></i>
                                                <i class="ecicon eci-star fill"></i>
                                                <i class="ecicon eci-star"></i>
                                            </span>
                                        </div>
                                        <div class="ec-pro-btn">
                                            <button title="Add To Cart" class="add-to-cart">Add To Cart</button>
                                            <button title="Select Option" class="select-option">Select
                                                Option</button>
                                            <div class="ec-pro-option">
                                                <div class="ec-pro-opt-inner">
                                                    <div class="ec-pro-color">
                                                        <ul class="ec-opt-swatch ec-change-img">
                                                            <li class="active"><a href="#"
                                                                    class="ec-opt-clr-img"
                                                                    data-src="visitor/images/product-image/31_1.jpg"
                                                                    data-src-hover="visitor/images/product-image/31_1.jpg"
                                                                    data-tooltip="Gray"><span
                                                                        style="background-color:#d8bd74;"></span></a>
                                                            </li>
                                                            <li><a href="#" class="ec-opt-clr-img"
                                                                    data-src="visitor/images/product-image/31_2.jpg"
                                                                    data-src-hover="visitor/images/product-image/31_2.jpg"
                                                                    data-tooltip="Orange"><span
                                                                        style="background-color:#cecece;"></span></a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6 ec-product-content">
                                <div class="ec-product-inner">
                                    <div class="ec-pro-image-outer">
                                        <div class="ec-pro-image">
                                            <a href="product-left-sidebar.html" class="image">
                                                <img class="main-image" src="visitor/images/product-image/32_1.jpg"
                                                    alt="Product" />
                                                <img class="hover-image" src="visitor/images/product-image/32_1.jpg"
                                                    alt="Product" />
                                            </a>
                                            <span class="flags">
                                                <span class="sale">Sale</span>
                                            </span>
                                            <div class="ec-pro-actions">
                                                <a class="ec-btn-group wishlist" title="Wishlist"><i
                                                        class="fi-rr-heart"></i></a>
                                                <a href="#" class="ec-btn-group quickview"
                                                    data-link-action="quickview" title="Quick view"
                                                    data-bs-toggle="modal" data-bs-target="#ec_quickview_modal"><i
                                                        class="fi-rr-eye"></i></a>
                                                <a href="compare.html" class="ec-btn-group compare"
                                                    title="Compare"><i class="fi fi-rr-arrows-repeat"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="ec-pro-content">
                                        <a href="shop-left-sidebar-col-3.html">
                                            <h6 class="ec-pro-stitle">Clothes</h6>
                                        </a>
                                        <h5 class="ec-pro-title"><a href="product-left-sidebar.html">Long slive
                                                t-shirt for men</a></h5>
                                        <div class="ec-pro-rat-price">
                                            <span class="ec-price">
                                                <span class="new-price">$489.00</span>
                                                <span class="old-price">$599.00</span>
                                            </span>
                                            <span class="ec-pro-rating">
                                                <i class="ecicon eci-star fill"></i>
                                                <i class="ecicon eci-star fill"></i>
                                                <i class="ecicon eci-star fill"></i>
                                                <i class="ecicon eci-star fill"></i>
                                                <i class="ecicon eci-star"></i>
                                            </span>
                                        </div>
                                        <div class="ec-pro-btn">
                                            <button title="Add To Cart" class="add-to-cart">Add To Cart</button>
                                            <button title="Select Option" class="select-option">Select
                                                Option</button>
                                            <div class="ec-pro-option">
                                                <div class="ec-pro-opt-inner">
                                                    <div class="ec-pro-color">
                                                        <ul class="ec-opt-swatch ec-change-img">
                                                            <li class="active"><a href="#"
                                                                    class="ec-opt-clr-img"
                                                                    data-src="visitor/images/product-image/32_1.jpg"
                                                                    data-src-hover="visitor/images/product-image/32_0.jpg"
                                                                    data-tooltip="Gray"><span
                                                                        style="background-color:#ac96fd;"></span></a>
                                                            </li>
                                                            <li><a href="#" class="ec-opt-clr-img"
                                                                    data-src="visitor/images/product-image/32_2.jpg"
                                                                    data-src-hover="visitor/images/product-image/32_2.jpg"
                                                                    data-tooltip="Orange"><span
                                                                        style="background-color:#6ec991;"></span></a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                    <div class="ec-pro-size">
                                                        <ul class="ec-opt-size">
                                                            <li class="active"><a href="#" class="ec-opt-sz"
                                                                    data-old="$410.00" data-new="$399.00"
                                                                    data-tooltip="Small">S</a></li>
                                                            <li><a href="#" class="ec-opt-sz"
                                                                    data-old="$425.00" data-new="$499.00"
                                                                    data-tooltip="Medium">M</a>
                                                            </li>
                                                            <li><a href="#" class="ec-opt-sz"
                                                                    data-old="$450.00" data-new="$550.00"
                                                                    data-tooltip="Large">L</a>
                                                            </li>
                                                            <li><a href="#" class="ec-opt-sz"
                                                                    data-old="$599.00" data-new="$489.00"
                                                                    data-tooltip="Extra Large">XL</a></li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6 ec-product-content">
                                <div class="ec-product-inner">
                                    <div class="ec-pro-image-outer">
                                        <div class="ec-pro-image">
                                            <a href="product-left-sidebar.html" class="image">
                                                <img class="main-image" src="visitor/images/product-image/33_1.jpg"
                                                    alt="Product" />
                                                <img class="hover-image" src="visitor/images/product-image/33_3.jpg"
                                                    alt="Product" />
                                            </a>
                                            <span class="flags">
                                                <span class="sale">Sale</span>
                                            </span>
                                            <div class="ec-pro-actions">
                                                <a class="ec-btn-group wishlist" title="Wishlist"><i
                                                        class="fi-rr-heart"></i></a>
                                                <a href="#" class="ec-btn-group quickview"
                                                    data-link-action="quickview" title="Quick view"
                                                    data-bs-toggle="modal" data-bs-target="#ec_quickview_modal"><i
                                                        class="fi-rr-eye"></i></a>
                                                <a href="compare.html" class="ec-btn-group compare"
                                                    title="Compare"><i class="fi fi-rr-arrows-repeat"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="ec-pro-content">
                                        <a href="shop-left-sidebar-col-3.html">
                                            <h6 class="ec-pro-stitle">Digital</h6>
                                        </a>
                                        <h5 class="ec-pro-title"><a href="product-left-sidebar.html">Apple SE
                                                Smartwatch (GPS, 40mm)</a></h5>
                                        <div class="ec-pro-rat-price">
                                            <span class="ec-price">
                                                <span class="new-price">$500.00</span>
                                                <span class="old-price">$650.00</span>
                                            </span>
                                            <span class="ec-pro-rating">
                                                <i class="ecicon eci-star fill"></i>
                                                <i class="ecicon eci-star fill"></i>
                                                <i class="ecicon eci-star fill"></i>
                                                <i class="ecicon eci-star fill"></i>
                                                <i class="ecicon eci-star"></i>
                                            </span>
                                        </div>
                                        <div class="ec-pro-btn">
                                            <button title="Add To Cart" class="add-to-cart">Add To Cart</button>
                                            <button title="Select Option" class="select-option">Select
                                                Option</button>
                                            <div class="ec-pro-option">
                                                <div class="ec-pro-opt-inner">
                                                    <div class="ec-pro-color">
                                                        <ul class="ec-opt-swatch ec-change-img">
                                                            <li class="active"><a href="#"
                                                                    class="ec-opt-clr-img"
                                                                    data-src="visitor/images/product-image/33_1.jpg"
                                                                    data-src-hover="visitor/images/product-image/33_1.jpg"
                                                                    data-tooltip="Gray"><span
                                                                        style="background-color:#cecece;"></span></a>
                                                            </li>
                                                            <li><a href="#" class="ec-opt-clr-img"
                                                                    data-src="visitor/images/product-image/33_2.jpg"
                                                                    data-src-hover="visitor/images/product-image/33_2.jpg"
                                                                    data-tooltip="Orange"><span
                                                                        style="background-color:#fcb0b0;"></span></a>
                                                            </li>
                                                            <li><a href="#" class="ec-opt-clr-img"
                                                                    data-src="visitor/images/product-image/33_3.jpg"
                                                                    data-src-hover="visitor/images/product-image/33_3.jpg"
                                                                    data-tooltip="Green"><span
                                                                        style="background-color:#8fc9ff;"></span></a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6 ec-product-content">
                                <div class="ec-product-inner">
                                    <div class="ec-pro-image-outer">
                                        <div class="ec-pro-image">
                                            <a href="product-left-sidebar.html" class="image">
                                                <img class="main-image" src="visitor/images/product-image/34_1.jpg"
                                                    alt="Product" />
                                                <img class="hover-image" src="visitor/images/product-image/34_2.jpg"
                                                    alt="Product" />
                                            </a>
                                            <span class="flags">
                                                <span class="new">New</span>
                                            </span>
                                            <div class="ec-pro-actions">
                                                <a class="ec-btn-group wishlist" title="Wishlist"><i
                                                        class="fi-rr-heart"></i></a>
                                                <a href="#" class="ec-btn-group quickview"
                                                    data-link-action="quickview" title="Quick view"
                                                    data-bs-toggle="modal" data-bs-target="#ec_quickview_modal"><i
                                                        class="fi-rr-eye"></i></a>
                                                <a href="compare.html" class="ec-btn-group compare"
                                                    title="Compare"><i class="fi fi-rr-arrows-repeat"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="ec-pro-content">
                                        <a href="shop-left-sidebar-col-3.html">
                                            <h6 class="ec-pro-stitle">Accessories</h6>
                                        </a>
                                        <h5 class="ec-pro-title"><a href="product-left-sidebar.html">Portable Room Air
                                                Purifier</a></h5>
                                        <div class="ec-pro-rat-price">
                                            <span class="ec-price">
                                                <span class="new-price">$599.00</span>
                                                <span class="old-price">$650.00</span>
                                            </span>
                                            <span class="ec-pro-rating">
                                                <i class="ecicon eci-star fill"></i>
                                                <i class="ecicon eci-star fill"></i>
                                                <i class="ecicon eci-star fill"></i>
                                                <i class="ecicon eci-star fill"></i>
                                                <i class="ecicon eci-star"></i>
                                            </span>
                                        </div>
                                        <div class="ec-pro-btn">
                                            <button title="Add To Cart" class="add-to-cart">Add To Cart</button>
                                            <button title="Select Option" class="select-option">Select
                                                Option</button>
                                            <div class="ec-pro-option">
                                                <div class="ec-pro-opt-inner">
                                                    <div class="ec-pro-color">
                                                        <ul class="ec-opt-swatch ec-change-img">
                                                            <li class="active"><a href="#"
                                                                    class="ec-opt-clr-img"
                                                                    data-src="visitor/images/product-image/34_1.jpg"
                                                                    data-src-hover="visitor/images/product-image/34_1.jpg"
                                                                    data-tooltip="Gray"><span
                                                                        style="background-color:#e2e2e2;"></span></a>
                                                            </li>
                                                            <li><a href="#" class="ec-opt-clr-img"
                                                                    data-src="visitor/images/product-image/34_3.jpg"
                                                                    data-src-hover="visitor/images/product-image/34_3.jpg"
                                                                    data-tooltip="Orange"><span
                                                                        style="background-color:#f8c9c9;"></span></a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6 ec-product-content">
                                <div class="ec-product-inner">
                                    <div class="ec-pro-image-outer">
                                        <div class="ec-pro-image">
                                            <a href="product-left-sidebar.html" class="image">
                                                <img class="main-image" src="visitor/images/product-image/35_1.jpg"
                                                    alt="Product" />
                                                <img class="hover-image" src="visitor/images/product-image/35_3.jpg"
                                                    alt="Product" />
                                            </a>
                                            <div class="ec-pro-actions">
                                                <a class="ec-btn-group wishlist" title="Wishlist"><i
                                                        class="fi-rr-heart"></i></a>
                                                <a href="#" class="ec-btn-group quickview"
                                                    data-link-action="quickview" title="Quick view"
                                                    data-bs-toggle="modal" data-bs-target="#ec_quickview_modal"><i
                                                        class="fi-rr-eye"></i></a>
                                                <a href="compare.html" class="ec-btn-group compare"
                                                    title="Compare"><i class="fi fi-rr-arrows-repeat"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="ec-pro-content">
                                        <a href="shop-left-sidebar-col-3.html">
                                            <h6 class="ec-pro-stitle">Clothes</h6>
                                        </a>
                                        <h5 class="ec-pro-title"><a href="product-left-sidebar.html">Hooded Neck full
                                                slive T-Shirt</a></h5>
                                        <div class="ec-pro-rat-price">
                                            <span class="ec-price">
                                                <span class="new-price">$49.00</span>
                                                <span class="old-price">$65.00</span>
                                            </span>
                                            <span class="ec-pro-rating">
                                                <i class="ecicon eci-star fill"></i>
                                                <i class="ecicon eci-star fill"></i>
                                                <i class="ecicon eci-star fill"></i>
                                                <i class="ecicon eci-star fill"></i>
                                                <i class="ecicon eci-star"></i>
                                            </span>
                                        </div>
                                        <div class="ec-pro-btn">
                                            <button title="Add To Cart" class="add-to-cart">Add To Cart</button>
                                            <button title="Select Option" class="select-option">Select
                                                Option</button>
                                            <div class="ec-pro-option">
                                                <div class="ec-pro-opt-inner">
                                                    <div class="ec-pro-color">
                                                        <ul class="ec-opt-swatch ec-change-img">
                                                            <li class="active"><a href="#"
                                                                    class="ec-opt-clr-img"
                                                                    data-src="visitor/images/product-image/35_1.jpg"
                                                                    data-src-hover="visitor/images/product-image/35_1.jpg"
                                                                    data-tooltip="Gray"><span
                                                                        style="background-color:#fd9191;"></span></a>
                                                            </li>
                                                            <li><a href="#" class="ec-opt-clr-img"
                                                                    data-src="visitor/images/product-image/35_2.jpg"
                                                                    data-src-hover="visitor/images/product-image/35_2.jpg"
                                                                    data-tooltip="Orange"><span
                                                                        style="background-color:#8991ff;"></span></a>
                                                            </li>
                                                            <li><a href="#" class="ec-opt-clr-img"
                                                                    data-src="visitor/images/product-image/35_3.jpg"
                                                                    data-src-hover="visitor/images/product-image/35_3.jpg"
                                                                    data-tooltip="Green"><span
                                                                        style="background-color:#da9343;"></span></a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                    <div class="ec-pro-size">
                                                        <ul class="ec-opt-size">
                                                            <li class="active"><a href="#" class="ec-opt-sz"
                                                                    data-old="$65.00" data-new="$49.00"
                                                                    data-tooltip="Small">S</a></li>
                                                            <li><a href="#" class="ec-opt-sz" data-old="$70.00"
                                                                    data-new="$55.00" data-tooltip="Medium">M</a>
                                                            </li>
                                                            <li><a href="#" class="ec-opt-sz" data-old="$79.00"
                                                                    data-new="$60.00" data-tooltip="Large">L</a>
                                                            </li>
                                                            <li><a href="#" class="ec-opt-sz" data-old="$82.00"
                                                                    data-new="$63.00"
                                                                    data-tooltip="Extra Large">XL</a></li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6 ec-product-content">
                                <div class="ec-product-inner">
                                    <div class="ec-pro-image-outer">
                                        <div class="ec-pro-image">
                                            <a href="product-left-sidebar.html" class="image">
                                                <img class="main-image" src="visitor/images/product-image/36_1.jpg"
                                                    alt="Product" />
                                                <img class="hover-image" src="visitor/images/product-image/36_2.jpg"
                                                    alt="Product" />
                                            </a>
                                            <div class="ec-pro-actions">
                                                <a class="ec-btn-group wishlist" title="Wishlist"><i
                                                        class="fi-rr-heart"></i></a>
                                                <a href="#" class="ec-btn-group quickview"
                                                    data-link-action="quickview" title="Quick view"
                                                    data-bs-toggle="modal" data-bs-target="#ec_quickview_modal"><i
                                                        class="fi-rr-eye"></i></a>
                                                <a href="compare.html" class="ec-btn-group compare"
                                                    title="Compare"><i class="fi fi-rr-arrows-repeat"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="ec-pro-content">
                                        <a href="shop-left-sidebar-col-3.html">
                                            <h6 class="ec-pro-stitle">Accessories</h6>
                                        </a>
                                        <h5 class="ec-pro-title"><a href="product-left-sidebar.html">Headphones
                                                bluetooth nano</a></h5>
                                        <div class="ec-pro-rat-price">
                                            <span class="ec-price">
                                                <span class="new-price">$199.00</span>
                                                <span class="old-price">$200.00</span>
                                            </span>
                                            <span class="ec-pro-rating">
                                                <i class="ecicon eci-star fill"></i>
                                                <i class="ecicon eci-star fill"></i>
                                                <i class="ecicon eci-star fill"></i>
                                                <i class="ecicon eci-star fill"></i>
                                                <i class="ecicon eci-star"></i>
                                            </span>
                                        </div>
                                        <div class="ec-pro-btn">
                                            <button title="Add To Cart" class="add-to-cart">Add To Cart</button>
                                            <button title="Select Option" class="select-option">Select
                                                Option</button>
                                            <div class="ec-pro-option">
                                                <div class="ec-pro-opt-inner">
                                                    <div class="ec-pro-size">
                                                        <ul class="ec-opt-size">
                                                            <li class="active"><a href="#" class="ec-opt-sz"
                                                                    data-old="$200.00" data-new="$199.00"
                                                                    data-tooltip="Small">Orange</a></li>
                                                            <li><a href="#" class="ec-opt-sz"
                                                                    data-old="$210.00" data-new="$209.00"
                                                                    data-tooltip="Medium">Rose</a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- ec 1st Product tab end -->
                    <!-- ec 2nd Product tab start -->
                    <div class="tab-pane fade" id="tab-pro-special-offer">
                        <div class="row">
                            <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6 ec-product-content">
                                <div class="ec-product-inner">
                                    <div class="ec-pro-image-outer">
                                        <div class="ec-pro-image">
                                            <a href="product-left-sidebar.html" class="image">
                                                <img class="main-image" src="visitor/images/product-image/31_1.jpg"
                                                    alt="Product" />
                                                <img class="hover-image" src="visitor/images/product-image/31_2.jpg"
                                                    alt="Product" />
                                            </a>
                                            <div class="ec-pro-actions">
                                                <a class="ec-btn-group wishlist" title="Wishlist"><i
                                                        class="fi-rr-heart"></i></a>
                                                <a href="#" class="ec-btn-group quickview"
                                                    data-link-action="quickview" title="Quick view"
                                                    data-bs-toggle="modal" data-bs-target="#ec_quickview_modal"><i
                                                        class="fi-rr-eye"></i></a>
                                                <a href="compare.html" class="ec-btn-group compare"
                                                    title="Compare"><i class="fi fi-rr-arrows-repeat"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="ec-pro-content">
                                        <a href="shop-left-sidebar-col-3.html">
                                            <h6 class="ec-pro-stitle">Furniture</h6>
                                        </a>
                                        <h5 class="ec-pro-title"><a href="product-left-sidebar.html">Bed room night
                                                lamp</a></h5>
                                        <div class="ec-pro-rat-price">
                                            <span class="ec-price">
                                                <span class="new-price">$59.00</span>
                                                <span class="old-price">$87.00</span>
                                            </span>
                                            <span class="ec-pro-rating">
                                                <i class="ecicon eci-star fill"></i>
                                                <i class="ecicon eci-star fill"></i>
                                                <i class="ecicon eci-star fill"></i>
                                                <i class="ecicon eci-star fill"></i>
                                                <i class="ecicon eci-star"></i>
                                            </span>
                                        </div>
                                        <div class="ec-pro-btn">
                                            <button title="Add To Cart" class="add-to-cart">Add To Cart</button>
                                            <button title="Select Option" class="select-option">Select
                                                Option</button>
                                            <div class="ec-pro-option">
                                                <div class="ec-pro-opt-inner">
                                                    <div class="ec-pro-color">
                                                        <ul class="ec-opt-swatch ec-change-img">
                                                            <li class="active"><a href="#"
                                                                    class="ec-opt-clr-img"
                                                                    data-src="visitor/images/product-image/31_1.jpg"
                                                                    data-src-hover="visitor/images/product-image/31_1.jpg"
                                                                    data-tooltip="Gray"><span
                                                                        style="background-color:#d8bd74;"></span></a>
                                                            </li>
                                                            <li><a href="#" class="ec-opt-clr-img"
                                                                    data-src="visitor/images/product-image/31_2.jpg"
                                                                    data-src-hover="visitor/images/product-image/31_2.jpg"
                                                                    data-tooltip="Orange"><span
                                                                        style="background-color:#cecece;"></span></a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6 ec-product-content">
                                <div class="ec-product-inner">
                                    <div class="ec-pro-image-outer">
                                        <div class="ec-pro-image">
                                            <a href="product-left-sidebar.html" class="image">
                                                <img class="main-image" src="visitor/images/product-image/32_1.jpg"
                                                    alt="Product" />
                                                <img class="hover-image" src="visitor/images/product-image/32_1.jpg"
                                                    alt="Product" />
                                            </a>
                                            <span class="flags">
                                                <span class="sale">Sale</span>
                                            </span>
                                            <div class="ec-pro-actions">
                                                <a class="ec-btn-group wishlist" title="Wishlist"><i
                                                        class="fi-rr-heart"></i></a>
                                                <a href="#" class="ec-btn-group quickview"
                                                    data-link-action="quickview" title="Quick view"
                                                    data-bs-toggle="modal" data-bs-target="#ec_quickview_modal"><i
                                                        class="fi-rr-eye"></i></a>
                                                <a href="compare.html" class="ec-btn-group compare"
                                                    title="Compare"><i class="fi fi-rr-arrows-repeat"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="ec-pro-content">
                                        <a href="shop-left-sidebar-col-3.html">
                                            <h6 class="ec-pro-stitle">Clothes</h6>
                                        </a>
                                        <h5 class="ec-pro-title"><a href="product-left-sidebar.html">Long slive
                                                t-shirt for men</a></h5>
                                        <div class="ec-pro-rat-price">
                                            <span class="ec-price">
                                                <span class="new-price">$489.00</span>
                                                <span class="old-price">$599.00</span>
                                            </span>
                                            <span class="ec-pro-rating">
                                                <i class="ecicon eci-star fill"></i>
                                                <i class="ecicon eci-star fill"></i>
                                                <i class="ecicon eci-star fill"></i>
                                                <i class="ecicon eci-star fill"></i>
                                                <i class="ecicon eci-star"></i>
                                            </span>
                                        </div>
                                        <div class="ec-pro-btn">
                                            <button title="Add To Cart" class="add-to-cart">Add To Cart</button>
                                            <button title="Select Option" class="select-option">Select
                                                Option</button>
                                            <div class="ec-pro-option">
                                                <div class="ec-pro-opt-inner">
                                                    <div class="ec-pro-color">
                                                        <ul class="ec-opt-swatch ec-change-img">
                                                            <li class="active"><a href="#"
                                                                    class="ec-opt-clr-img"
                                                                    data-src="visitor/images/product-image/32_1.jpg"
                                                                    data-src-hover="visitor/images/product-image/32_0.jpg"
                                                                    data-tooltip="Gray"><span
                                                                        style="background-color:#ac96fd;"></span></a>
                                                            </li>
                                                            <li><a href="#" class="ec-opt-clr-img"
                                                                    data-src="visitor/images/product-image/32_2.jpg"
                                                                    data-src-hover="visitor/images/product-image/32_2.jpg"
                                                                    data-tooltip="Orange"><span
                                                                        style="background-color:#6ec991;"></span></a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                    <div class="ec-pro-size">
                                                        <ul class="ec-opt-size">
                                                            <li class="active"><a href="#" class="ec-opt-sz"
                                                                    data-old="$410.00" data-new="$399.00"
                                                                    data-tooltip="Small">S</a></li>
                                                            <li><a href="#" class="ec-opt-sz"
                                                                    data-old="$425.00" data-new="$499.00"
                                                                    data-tooltip="Medium">M</a>
                                                            </li>
                                                            <li><a href="#" class="ec-opt-sz"
                                                                    data-old="$450.00" data-new="$550.00"
                                                                    data-tooltip="Large">L</a>
                                                            </li>
                                                            <li><a href="#" class="ec-opt-sz"
                                                                    data-old="$599.00" data-new="$489.00"
                                                                    data-tooltip="Extra Large">XL</a></li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6 ec-product-content">
                                <div class="ec-product-inner">
                                    <div class="ec-pro-image-outer">
                                        <div class="ec-pro-image">
                                            <a href="product-left-sidebar.html" class="image">
                                                <img class="main-image" src="visitor/images/product-image/33_1.jpg"
                                                    alt="Product" />
                                                <img class="hover-image" src="visitor/images/product-image/33_3.jpg"
                                                    alt="Product" />
                                            </a>
                                            <span class="flags">
                                                <span class="sale">Sale</span>
                                            </span>
                                            <div class="ec-pro-actions">
                                                <a class="ec-btn-group wishlist" title="Wishlist"><i
                                                        class="fi-rr-heart"></i></a>
                                                <a href="#" class="ec-btn-group quickview"
                                                    data-link-action="quickview" title="Quick view"
                                                    data-bs-toggle="modal" data-bs-target="#ec_quickview_modal"><i
                                                        class="fi-rr-eye"></i></a>
                                                <a href="compare.html" class="ec-btn-group compare"
                                                    title="Compare"><i class="fi fi-rr-arrows-repeat"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="ec-pro-content">
                                        <a href="shop-left-sidebar-col-3.html">
                                            <h6 class="ec-pro-stitle">Digital</h6>
                                        </a>
                                        <h5 class="ec-pro-title"><a href="product-left-sidebar.html">Apple SE
                                                Smartwatch (GPS, 40mm)</a></h5>
                                        <div class="ec-pro-rat-price">
                                            <span class="ec-price">
                                                <span class="new-price">$500.00</span>
                                                <span class="old-price">$650.00</span>
                                            </span>
                                            <span class="ec-pro-rating">
                                                <i class="ecicon eci-star fill"></i>
                                                <i class="ecicon eci-star fill"></i>
                                                <i class="ecicon eci-star fill"></i>
                                                <i class="ecicon eci-star fill"></i>
                                                <i class="ecicon eci-star"></i>
                                            </span>
                                        </div>
                                        <div class="ec-pro-btn">
                                            <button title="Add To Cart" class="add-to-cart">Add To Cart</button>
                                            <button title="Select Option" class="select-option">Select
                                                Option</button>
                                            <div class="ec-pro-option">
                                                <div class="ec-pro-opt-inner">
                                                    <div class="ec-pro-color">
                                                        <ul class="ec-opt-swatch ec-change-img">
                                                            <li class="active"><a href="#"
                                                                    class="ec-opt-clr-img"
                                                                    data-src="visitor/images/product-image/33_1.jpg"
                                                                    data-src-hover="visitor/images/product-image/33_1.jpg"
                                                                    data-tooltip="Gray"><span
                                                                        style="background-color:#cecece;"></span></a>
                                                            </li>
                                                            <li><a href="#" class="ec-opt-clr-img"
                                                                    data-src="visitor/images/product-image/33_2.jpg"
                                                                    data-src-hover="visitor/images/product-image/33_2.jpg"
                                                                    data-tooltip="Orange"><span
                                                                        style="background-color:#fcb0b0;"></span></a>
                                                            </li>
                                                            <li><a href="#" class="ec-opt-clr-img"
                                                                    data-src="visitor/images/product-image/33_3.jpg"
                                                                    data-src-hover="visitor/images/product-image/33_3.jpg"
                                                                    data-tooltip="Green"><span
                                                                        style="background-color:#8fc9ff;"></span></a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6 ec-product-content">
                                <div class="ec-product-inner">
                                    <div class="ec-pro-image-outer">
                                        <div class="ec-pro-image">
                                            <a href="product-left-sidebar.html" class="image">
                                                <img class="main-image" src="visitor/images/product-image/34_1.jpg"
                                                    alt="Product" />
                                                <img class="hover-image" src="visitor/images/product-image/34_2.jpg"
                                                    alt="Product" />
                                            </a>
                                            <span class="flags">
                                                <span class="new">New</span>
                                            </span>
                                            <div class="ec-pro-actions">
                                                <a class="ec-btn-group wishlist" title="Wishlist"><i
                                                        class="fi-rr-heart"></i></a>
                                                <a href="#" class="ec-btn-group quickview"
                                                    data-link-action="quickview" title="Quick view"
                                                    data-bs-toggle="modal" data-bs-target="#ec_quickview_modal"><i
                                                        class="fi-rr-eye"></i></a>
                                                <a href="compare.html" class="ec-btn-group compare"
                                                    title="Compare"><i class="fi fi-rr-arrows-repeat"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="ec-pro-content">
                                        <a href="shop-left-sidebar-col-3.html">
                                            <h6 class="ec-pro-stitle">Accessories</h6>
                                        </a>
                                        <h5 class="ec-pro-title"><a href="product-left-sidebar.html">Portable Room
                                                Air Purifier</a></h5>
                                        <div class="ec-pro-rat-price">
                                            <span class="ec-price">
                                                <span class="new-price">$599.00</span>
                                                <span class="old-price">$650.00</span>
                                            </span>
                                            <span class="ec-pro-rating">
                                                <i class="ecicon eci-star fill"></i>
                                                <i class="ecicon eci-star fill"></i>
                                                <i class="ecicon eci-star fill"></i>
                                                <i class="ecicon eci-star fill"></i>
                                                <i class="ecicon eci-star"></i>
                                            </span>
                                        </div>
                                        <div class="ec-pro-btn">
                                            <button title="Add To Cart" class="add-to-cart">Add To Cart</button>
                                            <button title="Select Option" class="select-option">Select
                                                Option</button>
                                            <div class="ec-pro-option">
                                                <div class="ec-pro-opt-inner">
                                                    <div class="ec-pro-color">
                                                        <ul class="ec-opt-swatch ec-change-img">
                                                            <li class="active"><a href="#"
                                                                    class="ec-opt-clr-img"
                                                                    data-src="visitor/images/product-image/34_1.jpg"
                                                                    data-src-hover="visitor/images/product-image/34_1.jpg"
                                                                    data-tooltip="Gray"><span
                                                                        style="background-color:#e2e2e2;"></span></a>
                                                            </li>
                                                            <li><a href="#" class="ec-opt-clr-img"
                                                                    data-src="visitor/images/product-image/34_3.jpg"
                                                                    data-src-hover="visitor/images/product-image/34_3.jpg"
                                                                    data-tooltip="Orange"><span
                                                                        style="background-color:#f8c9c9;"></span></a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6 ec-product-content">
                                <div class="ec-product-inner">
                                    <div class="ec-pro-image-outer">
                                        <div class="ec-pro-image">
                                            <a href="product-left-sidebar.html" class="image">
                                                <img class="main-image" src="visitor/images/product-image/29_1.jpg"
                                                    alt="Product" />
                                                <img class="hover-image" src="visitor/images/product-image/29_4.jpg"
                                                    alt="Product" />
                                            </a>
                                            <span class="flags">
                                                <span class="new">New</span>
                                            </span>
                                            <div class="ec-pro-actions">
                                                <a class="ec-btn-group wishlist" title="Wishlist"><i
                                                        class="fi-rr-heart"></i></a>
                                                <a href="#" class="ec-btn-group quickview"
                                                    data-link-action="quickview" title="Quick view"
                                                    data-bs-toggle="modal" data-bs-target="#ec_quickview_modal"><i
                                                        class="fi-rr-eye"></i></a>
                                                <a href="compare.html" class="ec-btn-group compare"
                                                    title="Compare"><i class="fi fi-rr-arrows-repeat"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="ec-pro-content">
                                        <a href="shop-left-sidebar-col-3.html">
                                            <h6 class="ec-pro-stitle">Toys</h6>
                                        </a>
                                        <h5 class="ec-pro-title"><a href="product-left-sidebar.html">Baby toy teddy
                                                bear</a></h5>
                                        <div class="ec-pro-rat-price">
                                            <span class="ec-price">
                                                <span class="new-price">$199.00</span>
                                                <span class="old-price">$200.00</span>
                                            </span>
                                            <span class="ec-pro-rating">
                                                <i class="ecicon eci-star fill"></i>
                                                <i class="ecicon eci-star fill"></i>
                                                <i class="ecicon eci-star fill"></i>
                                                <i class="ecicon eci-star fill"></i>
                                                <i class="ecicon eci-star"></i>
                                            </span>
                                        </div>
                                        <div class="ec-pro-btn">
                                            <button title="Add To Cart" class="add-to-cart">Add To Cart</button>
                                            <button title="Select Option" class="select-option">Select
                                                Option</button>
                                            <div class="ec-pro-option">
                                                <div class="ec-pro-opt-inner">
                                                    <div class="ec-pro-color">
                                                        <ul class="ec-opt-swatch ec-change-img">
                                                            <li class="active"><a href="#"
                                                                    class="ec-opt-clr-img"
                                                                    data-src="visitor/images/product-image/29_1.jpg"
                                                                    data-src-hover="visitor/images/product-image/29_1.jpg"
                                                                    data-tooltip="Gray"><span
                                                                        style="background-color:#a88960;"></span></a>
                                                            </li>
                                                            <li><a href="#" class="ec-opt-clr-img"
                                                                    data-src="visitor/images/product-image/29_2.jpg"
                                                                    data-src-hover="visitor/images/product-image/29_2.jpg"
                                                                    data-tooltip="Orange"><span
                                                                        style="background-color:#f778fc;"></span></a>
                                                            </li>
                                                            <li><a href="#" class="ec-opt-clr-img"
                                                                    data-src="visitor/images/product-image/29_3.jpg"
                                                                    data-src-hover="visitor/images/product-image/29_3.jpg"
                                                                    data-tooltip="Green"><span
                                                                        style="background-color:#8b83ff;"></span></a>
                                                            </li>
                                                            <li><a href="#" class="ec-opt-clr-img"
                                                                    data-src="visitor/images/product-image/29_4.jpg"
                                                                    data-src-hover="visitor/images/product-image/29_4.jpg"
                                                                    data-tooltip="Sky Blue"><span
                                                                        style="background-color:#5ea561;"></span></a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6 ec-product-content">
                                <div class="ec-product-inner">
                                    <div class="ec-pro-image-outer">
                                        <div class="ec-pro-image">
                                            <a href="product-left-sidebar.html" class="image">
                                                <img class="main-image" src="visitor/images/product-image/30_1.jpg"
                                                    alt="Product" />
                                                <img class="hover-image" src="visitor/images/product-image/30_3.jpg"
                                                    alt="Product" />
                                            </a>
                                            <span class="flags">
                                                <span class="sale">Sale</span>
                                            </span>
                                            <div class="ec-pro-actions">
                                                <a class="ec-btn-group wishlist" title="Wishlist"><i
                                                        class="fi-rr-heart"></i></a>
                                                <a href="#" class="ec-btn-group quickview"
                                                    data-link-action="quickview" title="Quick view"
                                                    data-bs-toggle="modal" data-bs-target="#ec_quickview_modal"><i
                                                        class="fi-rr-eye"></i></a>
                                                <a href="compare.html" class="ec-btn-group compare"
                                                    title="Compare"><i class="fi fi-rr-arrows-repeat"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="ec-pro-content">
                                        <a href="shop-left-sidebar-col-3.html">
                                            <h6 class="ec-pro-stitle">Accessories</h6>
                                        </a>
                                        <h5 class="ec-pro-title"><a href="product-left-sidebar.html">Living room
                                                lighting</a></h5>
                                        <div class="ec-pro-rat-price">
                                            <span class="ec-price">
                                                <span class="new-price">$152.00</span>
                                                <span class="old-price">$180.00</span>
                                            </span>
                                            <span class="ec-pro-rating">
                                                <i class="ecicon eci-star fill"></i>
                                                <i class="ecicon eci-star fill"></i>
                                                <i class="ecicon eci-star fill"></i>
                                                <i class="ecicon eci-star fill"></i>
                                                <i class="ecicon eci-star"></i>
                                            </span>
                                        </div>
                                        <div class="ec-pro-btn">
                                            <button title="Add To Cart" class="add-to-cart">Add To Cart</button>
                                            <button title="Select Option" class="select-option">Select
                                                Option</button>
                                            <div class="ec-pro-option">
                                                <div class="ec-pro-opt-inner">
                                                    <div class="ec-pro-color">
                                                        <ul class="ec-opt-swatch ec-change-img">
                                                            <li class="active"><a href="#"
                                                                    class="ec-opt-clr-img"
                                                                    data-src="visitor/images/product-image/30_1.jpg"
                                                                    data-src-hover="visitor/images/product-image/30_1.jpg"
                                                                    data-tooltip="Gray"><span
                                                                        style="background-color:#e9e9e9;"></span></a>
                                                            </li>
                                                            <li><a href="#" class="ec-opt-clr-img"
                                                                    data-src="visitor/images/product-image/30_2.jpg"
                                                                    data-src-hover="visitor/images/product-image/30_2.jpg"
                                                                    data-tooltip="Orange"><span
                                                                        style="background-color:#b6f0ff;"></span></a>
                                                            </li>
                                                            <li><a href="#" class="ec-opt-clr-img"
                                                                    data-src="visitor/images/product-image/30_3.jpg"
                                                                    data-src-hover="visitor/images/product-image/30_3.jpg"
                                                                    data-tooltip="Green"><span
                                                                        style="background-color:#a2ffcc;"></span></a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6 ec-product-content">
                                <div class="ec-product-inner">
                                    <div class="ec-pro-image-outer">
                                        <div class="ec-pro-image">
                                            <a href="product-left-sidebar.html" class="image">
                                                <img class="main-image" src="visitor/images/product-image/35_1.jpg"
                                                    alt="Product" />
                                                <img class="hover-image" src="visitor/images/product-image/35_3.jpg"
                                                    alt="Product" />
                                            </a>
                                            <div class="ec-pro-actions">
                                                <a class="ec-btn-group wishlist" title="Wishlist"><i
                                                        class="fi-rr-heart"></i></a>
                                                <a href="#" class="ec-btn-group quickview"
                                                    data-link-action="quickview" title="Quick view"
                                                    data-bs-toggle="modal" data-bs-target="#ec_quickview_modal"><i
                                                        class="fi-rr-eye"></i></a>
                                                <a href="compare.html" class="ec-btn-group compare"
                                                    title="Compare"><i class="fi fi-rr-arrows-repeat"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="ec-pro-content">
                                        <a href="shop-left-sidebar-col-3.html">
                                            <h6 class="ec-pro-stitle">Clothes</h6>
                                        </a>
                                        <h5 class="ec-pro-title"><a href="product-left-sidebar.html">Hooded Neck
                                                full slive T-Shirt</a></h5>
                                        <div class="ec-pro-rat-price">
                                            <span class="ec-price">
                                                <span class="new-price">$49.00</span>
                                                <span class="old-price">$65.00</span>
                                            </span>
                                            <span class="ec-pro-rating">
                                                <i class="ecicon eci-star fill"></i>
                                                <i class="ecicon eci-star fill"></i>
                                                <i class="ecicon eci-star fill"></i>
                                                <i class="ecicon eci-star fill"></i>
                                                <i class="ecicon eci-star"></i>
                                            </span>
                                        </div>
                                        <div class="ec-pro-btn">
                                            <button title="Add To Cart" class="add-to-cart">Add To Cart</button>
                                            <button title="Select Option" class="select-option">Select
                                                Option</button>
                                            <div class="ec-pro-option">
                                                <div class="ec-pro-opt-inner">
                                                    <div class="ec-pro-color">
                                                        <ul class="ec-opt-swatch ec-change-img">
                                                            <li class="active"><a href="#"
                                                                    class="ec-opt-clr-img"
                                                                    data-src="visitor/images/product-image/35_1.jpg"
                                                                    data-src-hover="visitor/images/product-image/35_1.jpg"
                                                                    data-tooltip="Gray"><span
                                                                        style="background-color:#fd9191;"></span></a>
                                                            </li>
                                                            <li><a href="#" class="ec-opt-clr-img"
                                                                    data-src="visitor/images/product-image/35_2.jpg"
                                                                    data-src-hover="visitor/images/product-image/35_2.jpg"
                                                                    data-tooltip="Orange"><span
                                                                        style="background-color:#8991ff;"></span></a>
                                                            </li>
                                                            <li><a href="#" class="ec-opt-clr-img"
                                                                    data-src="visitor/images/product-image/35_3.jpg"
                                                                    data-src-hover="visitor/images/product-image/35_3.jpg"
                                                                    data-tooltip="Green"><span
                                                                        style="background-color:#da9343;"></span></a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                    <div class="ec-pro-size">
                                                        <ul class="ec-opt-size">
                                                            <li class="active"><a href="#" class="ec-opt-sz"
                                                                    data-old="$65.00" data-new="$49.00"
                                                                    data-tooltip="Small">S</a></li>
                                                            <li><a href="#" class="ec-opt-sz"
                                                                    data-old="$70.00" data-new="$55.00"
                                                                    data-tooltip="Medium">M</a>
                                                            </li>
                                                            <li><a href="#" class="ec-opt-sz"
                                                                    data-old="$79.00" data-new="$60.00"
                                                                    data-tooltip="Large">L</a>
                                                            </li>
                                                            <li><a href="#" class="ec-opt-sz"
                                                                    data-old="$82.00" data-new="$63.00"
                                                                    data-tooltip="Extra Large">XL</a></li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6 ec-product-content">
                                <div class="ec-product-inner">
                                    <div class="ec-pro-image-outer">
                                        <div class="ec-pro-image">
                                            <a href="product-left-sidebar.html" class="image">
                                                <img class="main-image" src="visitor/images/product-image/36_1.jpg"
                                                    alt="Product" />
                                                <img class="hover-image" src="visitor/images/product-image/36_2.jpg"
                                                    alt="Product" />
                                            </a>
                                            <div class="ec-pro-actions">
                                                <a class="ec-btn-group wishlist" title="Wishlist"><i
                                                        class="fi-rr-heart"></i></a>
                                                <a href="#" class="ec-btn-group quickview"
                                                    data-link-action="quickview" title="Quick view"
                                                    data-bs-toggle="modal" data-bs-target="#ec_quickview_modal"><i
                                                        class="fi-rr-eye"></i></a>
                                                <a href="compare.html" class="ec-btn-group compare"
                                                    title="Compare"><i class="fi fi-rr-arrows-repeat"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="ec-pro-content">
                                        <a href="shop-left-sidebar-col-3.html">
                                            <h6 class="ec-pro-stitle">Accessories</h6>
                                        </a>
                                        <h5 class="ec-pro-title"><a href="product-left-sidebar.html">Headphones
                                                bluetooth nano</a></h5>
                                        <div class="ec-pro-rat-price">
                                            <span class="ec-price">
                                                <span class="new-price">$199.00</span>
                                                <span class="old-price">$200.00</span>
                                            </span>
                                            <span class="ec-pro-rating">
                                                <i class="ecicon eci-star fill"></i>
                                                <i class="ecicon eci-star fill"></i>
                                                <i class="ecicon eci-star fill"></i>
                                                <i class="ecicon eci-star fill"></i>
                                                <i class="ecicon eci-star"></i>
                                            </span>
                                        </div>
                                        <div class="ec-pro-btn">
                                            <button title="Add To Cart" class="add-to-cart">Add To Cart</button>
                                            <button title="Select Option" class="select-option">Select
                                                Option</button>
                                            <div class="ec-pro-option">
                                                <div class="ec-pro-opt-inner">
                                                    <div class="ec-pro-size">
                                                        <ul class="ec-opt-size">
                                                            <li class="active"><a href="#" class="ec-opt-sz"
                                                                    data-old="$200.00" data-new="$199.00"
                                                                    data-tooltip="Small">Orange</a></li>
                                                            <li><a href="#" class="ec-opt-sz"
                                                                    data-old="$210.00" data-new="$209.00"
                                                                    data-tooltip="Medium">Rose</a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- ec 2nd Product tab end -->
                    <!-- ec 3rd Product tab start -->
                    <div class="tab-pane fade" id="tab-pro-best-sellers">
                        <div class="row">

                            <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6 ec-product-content">
                                <div class="ec-product-inner">
                                    <div class="ec-pro-image-outer">
                                        <div class="ec-pro-image">
                                            <a href="product-left-sidebar.html" class="image">
                                                <img class="main-image" src="visitor/images/product-image/35_1.jpg"
                                                    alt="Product" />
                                                <img class="hover-image" src="visitor/images/product-image/35_3.jpg"
                                                    alt="Product" />
                                            </a>
                                            <div class="ec-pro-actions">
                                                <a class="ec-btn-group wishlist" title="Wishlist"><i
                                                        class="fi-rr-heart"></i></a>
                                                <a href="#" class="ec-btn-group quickview"
                                                    data-link-action="quickview" title="Quick view"
                                                    data-bs-toggle="modal" data-bs-target="#ec_quickview_modal"><i
                                                        class="fi-rr-eye"></i></a>
                                                <a href="compare.html" class="ec-btn-group compare"
                                                    title="Compare"><i class="fi fi-rr-arrows-repeat"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="ec-pro-content">
                                        <a href="shop-left-sidebar-col-3.html">
                                            <h6 class="ec-pro-stitle">Clothes</h6>
                                        </a>
                                        <h5 class="ec-pro-title"><a href="product-left-sidebar.html">Hooded Neck
                                                full slive T-Shirt</a></h5>
                                        <div class="ec-pro-rat-price">
                                            <span class="ec-price">
                                                <span class="new-price">$49.00</span>
                                                <span class="old-price">$65.00</span>
                                            </span>
                                            <span class="ec-pro-rating">
                                                <i class="ecicon eci-star fill"></i>
                                                <i class="ecicon eci-star fill"></i>
                                                <i class="ecicon eci-star fill"></i>
                                                <i class="ecicon eci-star fill"></i>
                                                <i class="ecicon eci-star"></i>
                                            </span>
                                        </div>
                                        <div class="ec-pro-btn">
                                            <button title="Add To Cart" class="add-to-cart">Add To Cart</button>
                                            <button title="Select Option" class="select-option">Select
                                                Option</button>
                                            <div class="ec-pro-option">
                                                <div class="ec-pro-opt-inner">
                                                    <div class="ec-pro-color">
                                                        <ul class="ec-opt-swatch ec-change-img">
                                                            <li class="active"><a href="#"
                                                                    class="ec-opt-clr-img"
                                                                    data-src="visitor/images/product-image/35_1.jpg"
                                                                    data-src-hover="visitor/images/product-image/35_1.jpg"
                                                                    data-tooltip="Gray"><span
                                                                        style="background-color:#fd9191;"></span></a>
                                                            </li>
                                                            <li><a href="#" class="ec-opt-clr-img"
                                                                    data-src="visitor/images/product-image/35_2.jpg"
                                                                    data-src-hover="visitor/images/product-image/35_2.jpg"
                                                                    data-tooltip="Orange"><span
                                                                        style="background-color:#8991ff;"></span></a>
                                                            </li>
                                                            <li><a href="#" class="ec-opt-clr-img"
                                                                    data-src="visitor/images/product-image/35_3.jpg"
                                                                    data-src-hover="visitor/images/product-image/35_3.jpg"
                                                                    data-tooltip="Green"><span
                                                                        style="background-color:#da9343;"></span></a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                    <div class="ec-pro-size">
                                                        <ul class="ec-opt-size">
                                                            <li class="active"><a href="#" class="ec-opt-sz"
                                                                    data-old="$65.00" data-new="$49.00"
                                                                    data-tooltip="Small">S</a></li>
                                                            <li><a href="#" class="ec-opt-sz"
                                                                    data-old="$70.00" data-new="$55.00"
                                                                    data-tooltip="Medium">M</a>
                                                            </li>
                                                            <li><a href="#" class="ec-opt-sz"
                                                                    data-old="$79.00" data-new="$60.00"
                                                                    data-tooltip="Large">L</a>
                                                            </li>
                                                            <li><a href="#" class="ec-opt-sz"
                                                                    data-old="$82.00" data-new="$63.00"
                                                                    data-tooltip="Extra Large">XL</a></li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6 ec-product-content">
                                <div class="ec-product-inner">
                                    <div class="ec-pro-image-outer">
                                        <div class="ec-pro-image">
                                            <a href="product-left-sidebar.html" class="image">
                                                <img class="main-image" src="visitor/images/product-image/36_1.jpg"
                                                    alt="Product" />
                                                <img class="hover-image" src="visitor/images/product-image/36_2.jpg"
                                                    alt="Product" />
                                            </a>
                                            <div class="ec-pro-actions">
                                                <a class="ec-btn-group wishlist" title="Wishlist"><i
                                                        class="fi-rr-heart"></i></a>
                                                <a href="#" class="ec-btn-group quickview"
                                                    data-link-action="quickview" title="Quick view"
                                                    data-bs-toggle="modal" data-bs-target="#ec_quickview_modal"><i
                                                        class="fi-rr-eye"></i></a>
                                                <a href="compare.html" class="ec-btn-group compare"
                                                    title="Compare"><i class="fi fi-rr-arrows-repeat"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="ec-pro-content">
                                        <a href="shop-left-sidebar-col-3.html">
                                            <h6 class="ec-pro-stitle">Accessories</h6>
                                        </a>
                                        <h5 class="ec-pro-title"><a href="product-left-sidebar.html">Headphones
                                                bluetooth nano</a></h5>
                                        <div class="ec-pro-rat-price">
                                            <span class="ec-price">
                                                <span class="new-price">$199.00</span>
                                                <span class="old-price">$200.00</span>
                                            </span>
                                            <span class="ec-pro-rating">
                                                <i class="ecicon eci-star fill"></i>
                                                <i class="ecicon eci-star fill"></i>
                                                <i class="ecicon eci-star fill"></i>
                                                <i class="ecicon eci-star fill"></i>
                                                <i class="ecicon eci-star"></i>
                                            </span>
                                        </div>
                                        <div class="ec-pro-btn">
                                            <button title="Add To Cart" class="add-to-cart">Add To Cart</button>
                                            <button title="Select Option" class="select-option">Select
                                                Option</button>
                                            <div class="ec-pro-option">
                                                <div class="ec-pro-opt-inner">
                                                    <div class="ec-pro-size">
                                                        <ul class="ec-opt-size">
                                                            <li class="active"><a href="#" class="ec-opt-sz"
                                                                    data-old="$200.00" data-new="$199.00"
                                                                    data-tooltip="Small">Orange</a></li>
                                                            <li><a href="#" class="ec-opt-sz"
                                                                    data-old="$210.00" data-new="$209.00"
                                                                    data-tooltip="Medium">Rose</a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6 ec-product-content">
                                <div class="ec-product-inner">
                                    <div class="ec-pro-image-outer">
                                        <div class="ec-pro-image">
                                            <a href="product-left-sidebar.html" class="image">
                                                <img class="main-image" src="visitor/images/product-image/32_1.jpg"
                                                    alt="Product" />
                                                <img class="hover-image" src="visitor/images/product-image/32_1.jpg"
                                                    alt="Product" />
                                            </a>
                                            <span class="flags">
                                                <span class="sale">Sale</span>
                                            </span>
                                            <div class="ec-pro-actions">
                                                <a class="ec-btn-group wishlist" title="Wishlist"><i
                                                        class="fi-rr-heart"></i></a>
                                                <a href="#" class="ec-btn-group quickview"
                                                    data-link-action="quickview" title="Quick view"
                                                    data-bs-toggle="modal" data-bs-target="#ec_quickview_modal"><i
                                                        class="fi-rr-eye"></i></a>
                                                <a href="compare.html" class="ec-btn-group compare"
                                                    title="Compare"><i class="fi fi-rr-arrows-repeat"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="ec-pro-content">
                                        <a href="shop-left-sidebar-col-3.html">
                                            <h6 class="ec-pro-stitle">Clothes</h6>
                                        </a>
                                        <h5 class="ec-pro-title"><a href="product-left-sidebar.html">Long slive
                                                t-shirt for men</a></h5>
                                        <div class="ec-pro-rat-price">
                                            <span class="ec-price">
                                                <span class="new-price">$489.00</span>
                                                <span class="old-price">$599.00</span>
                                            </span>
                                            <span class="ec-pro-rating">
                                                <i class="ecicon eci-star fill"></i>
                                                <i class="ecicon eci-star fill"></i>
                                                <i class="ecicon eci-star fill"></i>
                                                <i class="ecicon eci-star fill"></i>
                                                <i class="ecicon eci-star"></i>
                                            </span>
                                        </div>
                                        <div class="ec-pro-btn">
                                            <button title="Add To Cart" class="add-to-cart">Add To Cart</button>
                                            <button title="Select Option" class="select-option">Select
                                                Option</button>
                                            <div class="ec-pro-option">
                                                <div class="ec-pro-opt-inner">
                                                    <div class="ec-pro-color">
                                                        <ul class="ec-opt-swatch ec-change-img">
                                                            <li class="active"><a href="#"
                                                                    class="ec-opt-clr-img"
                                                                    data-src="visitor/images/product-image/32_1.jpg"
                                                                    data-src-hover="visitor/images/product-image/32_0.jpg"
                                                                    data-tooltip="Gray"><span
                                                                        style="background-color:#ac96fd;"></span></a>
                                                            </li>
                                                            <li><a href="#" class="ec-opt-clr-img"
                                                                    data-src="visitor/images/product-image/32_2.jpg"
                                                                    data-src-hover="visitor/images/product-image/32_2.jpg"
                                                                    data-tooltip="Orange"><span
                                                                        style="background-color:#6ec991;"></span></a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                    <div class="ec-pro-size">
                                                        <ul class="ec-opt-size">
                                                            <li class="active"><a href="#" class="ec-opt-sz"
                                                                    data-old="$410.00" data-new="$399.00"
                                                                    data-tooltip="Small">S</a></li>
                                                            <li><a href="#" class="ec-opt-sz"
                                                                    data-old="$425.00" data-new="$499.00"
                                                                    data-tooltip="Medium">M</a>
                                                            </li>
                                                            <li><a href="#" class="ec-opt-sz"
                                                                    data-old="$450.00" data-new="$550.00"
                                                                    data-tooltip="Large">L</a>
                                                            </li>
                                                            <li><a href="#" class="ec-opt-sz"
                                                                    data-old="$599.00" data-new="$489.00"
                                                                    data-tooltip="Extra Large">XL</a></li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6 ec-product-content">
                                <div class="ec-product-inner">
                                    <div class="ec-pro-image-outer">
                                        <div class="ec-pro-image">
                                            <a href="product-left-sidebar.html" class="image">
                                                <img class="main-image" src="visitor/images/product-image/33_1.jpg"
                                                    alt="Product" />
                                                <img class="hover-image" src="visitor/images/product-image/33_3.jpg"
                                                    alt="Product" />
                                            </a>
                                            <span class="flags">
                                                <span class="sale">Sale</span>
                                            </span>
                                            <div class="ec-pro-actions">
                                                <a class="ec-btn-group wishlist" title="Wishlist"><i
                                                        class="fi-rr-heart"></i></a>
                                                <a href="#" class="ec-btn-group quickview"
                                                    data-link-action="quickview" title="Quick view"
                                                    data-bs-toggle="modal" data-bs-target="#ec_quickview_modal"><i
                                                        class="fi-rr-eye"></i></a>
                                                <a href="compare.html" class="ec-btn-group compare"
                                                    title="Compare"><i class="fi fi-rr-arrows-repeat"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="ec-pro-content">
                                        <a href="shop-left-sidebar-col-3.html">
                                            <h6 class="ec-pro-stitle">Digital</h6>
                                        </a>
                                        <h5 class="ec-pro-title"><a href="product-left-sidebar.html">Apple SE
                                                Smartwatch (GPS, 40mm)</a></h5>
                                        <div class="ec-pro-rat-price">
                                            <span class="ec-price">
                                                <span class="new-price">$500.00</span>
                                                <span class="old-price">$650.00</span>
                                            </span>
                                            <span class="ec-pro-rating">
                                                <i class="ecicon eci-star fill"></i>
                                                <i class="ecicon eci-star fill"></i>
                                                <i class="ecicon eci-star fill"></i>
                                                <i class="ecicon eci-star fill"></i>
                                                <i class="ecicon eci-star"></i>
                                            </span>
                                        </div>
                                        <div class="ec-pro-btn">
                                            <button title="Add To Cart" class="add-to-cart">Add To Cart</button>
                                            <button title="Select Option" class="select-option">Select
                                                Option</button>
                                            <div class="ec-pro-option">
                                                <div class="ec-pro-opt-inner">
                                                    <div class="ec-pro-color">
                                                        <ul class="ec-opt-swatch ec-change-img">
                                                            <li class="active"><a href="#"
                                                                    class="ec-opt-clr-img"
                                                                    data-src="visitor/images/product-image/33_1.jpg"
                                                                    data-src-hover="visitor/images/product-image/33_1.jpg"
                                                                    data-tooltip="Gray"><span
                                                                        style="background-color:#cecece;"></span></a>
                                                            </li>
                                                            <li><a href="#" class="ec-opt-clr-img"
                                                                    data-src="visitor/images/product-image/33_2.jpg"
                                                                    data-src-hover="visitor/images/product-image/33_2.jpg"
                                                                    data-tooltip="Orange"><span
                                                                        style="background-color:#fcb0b0;"></span></a>
                                                            </li>
                                                            <li><a href="#" class="ec-opt-clr-img"
                                                                    data-src="visitor/images/product-image/33_3.jpg"
                                                                    data-src-hover="visitor/images/product-image/33_3.jpg"
                                                                    data-tooltip="Green"><span
                                                                        style="background-color:#8fc9ff;"></span></a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6 ec-product-content">
                                <div class="ec-product-inner">
                                    <div class="ec-pro-image-outer">
                                        <div class="ec-pro-image">
                                            <a href="product-left-sidebar.html" class="image">
                                                <img class="main-image" src="visitor/images/product-image/31_1.jpg"
                                                    alt="Product" />
                                                <img class="hover-image" src="visitor/images/product-image/31_2.jpg"
                                                    alt="Product" />
                                            </a>
                                            <div class="ec-pro-actions">
                                                <a class="ec-btn-group wishlist" title="Wishlist"><i
                                                        class="fi-rr-heart"></i></a>
                                                <a href="#" class="ec-btn-group quickview"
                                                    data-link-action="quickview" title="Quick view"
                                                    data-bs-toggle="modal" data-bs-target="#ec_quickview_modal"><i
                                                        class="fi-rr-eye"></i></a>
                                                <a href="compare.html" class="ec-btn-group compare"
                                                    title="Compare"><i class="fi fi-rr-arrows-repeat"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="ec-pro-content">
                                        <a href="shop-left-sidebar-col-3.html">
                                            <h6 class="ec-pro-stitle">Furniture</h6>
                                        </a>
                                        <h5 class="ec-pro-title"><a href="product-left-sidebar.html">Bed room night
                                                lamp</a></h5>
                                        <div class="ec-pro-rat-price">
                                            <span class="ec-price">
                                                <span class="new-price">$59.00</span>
                                                <span class="old-price">$87.00</span>
                                            </span>
                                            <span class="ec-pro-rating">
                                                <i class="ecicon eci-star fill"></i>
                                                <i class="ecicon eci-star fill"></i>
                                                <i class="ecicon eci-star fill"></i>
                                                <i class="ecicon eci-star fill"></i>
                                                <i class="ecicon eci-star"></i>
                                            </span>
                                        </div>
                                        <div class="ec-pro-btn">
                                            <button title="Add To Cart" class="add-to-cart">Add To Cart</button>
                                            <button title="Select Option" class="select-option">Select
                                                Option</button>
                                            <div class="ec-pro-option">
                                                <div class="ec-pro-opt-inner">
                                                    <div class="ec-pro-color">
                                                        <ul class="ec-opt-swatch ec-change-img">
                                                            <li class="active"><a href="#"
                                                                    class="ec-opt-clr-img"
                                                                    data-src="visitor/images/product-image/31_1.jpg"
                                                                    data-src-hover="visitor/images/product-image/31_1.jpg"
                                                                    data-tooltip="Gray"><span
                                                                        style="background-color:#d8bd74;"></span></a>
                                                            </li>
                                                            <li><a href="#" class="ec-opt-clr-img"
                                                                    data-src="visitor/images/product-image/31_2.jpg"
                                                                    data-src-hover="visitor/images/product-image/31_2.jpg"
                                                                    data-tooltip="Orange"><span
                                                                        style="background-color:#cecece;"></span></a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6 ec-product-content">
                                <div class="ec-product-inner">
                                    <div class="ec-pro-image-outer">
                                        <div class="ec-pro-image">
                                            <a href="product-left-sidebar.html" class="image">
                                                <img class="main-image" src="visitor/images/product-image/34_1.jpg"
                                                    alt="Product" />
                                                <img class="hover-image" src="visitor/images/product-image/34_2.jpg"
                                                    alt="Product" />
                                            </a>
                                            <span class="flags">
                                                <span class="new">New</span>
                                            </span>
                                            <div class="ec-pro-actions">
                                                <a class="ec-btn-group wishlist" title="Wishlist"><i
                                                        class="fi-rr-heart"></i></a>
                                                <a href="#" class="ec-btn-group quickview"
                                                    data-link-action="quickview" title="Quick view"
                                                    data-bs-toggle="modal" data-bs-target="#ec_quickview_modal"><i
                                                        class="fi-rr-eye"></i></a>
                                                <a href="compare.html" class="ec-btn-group compare"
                                                    title="Compare"><i class="fi fi-rr-arrows-repeat"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="ec-pro-content">
                                        <a href="shop-left-sidebar-col-3.html">
                                            <h6 class="ec-pro-stitle">Accessories</h6>
                                        </a>
                                        <h5 class="ec-pro-title"><a href="product-left-sidebar.html">Portable Room
                                                Air Purifier</a></h5>
                                        <div class="ec-pro-rat-price">
                                            <span class="ec-price">
                                                <span class="new-price">$599.00</span>
                                                <span class="old-price">$650.00</span>
                                            </span>
                                            <span class="ec-pro-rating">
                                                <i class="ecicon eci-star fill"></i>
                                                <i class="ecicon eci-star fill"></i>
                                                <i class="ecicon eci-star fill"></i>
                                                <i class="ecicon eci-star fill"></i>
                                                <i class="ecicon eci-star"></i>
                                            </span>
                                        </div>
                                        <div class="ec-pro-btn">
                                            <button title="Add To Cart" class="add-to-cart">Add To Cart</button>
                                            <button title="Select Option" class="select-option">Select
                                                Option</button>
                                            <div class="ec-pro-option">
                                                <div class="ec-pro-opt-inner">
                                                    <div class="ec-pro-color">
                                                        <ul class="ec-opt-swatch ec-change-img">
                                                            <li class="active"><a href="#"
                                                                    class="ec-opt-clr-img"
                                                                    data-src="visitor/images/product-image/34_1.jpg"
                                                                    data-src-hover="visitor/images/product-image/34_1.jpg"
                                                                    data-tooltip="Gray"><span
                                                                        style="background-color:#e2e2e2;"></span></a>
                                                            </li>
                                                            <li><a href="#" class="ec-opt-clr-img"
                                                                    data-src="visitor/images/product-image/34_3.jpg"
                                                                    data-src-hover="visitor/images/product-image/34_3.jpg"
                                                                    data-tooltip="Orange"><span
                                                                        style="background-color:#f8c9c9;"></span></a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6 ec-product-content">
                                <div class="ec-product-inner">
                                    <div class="ec-pro-image-outer">
                                        <div class="ec-pro-image">
                                            <a href="product-left-sidebar.html" class="image">
                                                <img class="main-image" src="visitor/images/product-image/29_1.jpg"
                                                    alt="Product" />
                                                <img class="hover-image" src="visitor/images/product-image/29_4.jpg"
                                                    alt="Product" />
                                            </a>
                                            <span class="flags">
                                                <span class="new">New</span>
                                            </span>
                                            <div class="ec-pro-actions">
                                                <a class="ec-btn-group wishlist" title="Wishlist"><i
                                                        class="fi-rr-heart"></i></a>
                                                <a href="#" class="ec-btn-group quickview"
                                                    data-link-action="quickview" title="Quick view"
                                                    data-bs-toggle="modal" data-bs-target="#ec_quickview_modal"><i
                                                        class="fi-rr-eye"></i></a>
                                                <a href="compare.html" class="ec-btn-group compare"
                                                    title="Compare"><i class="fi fi-rr-arrows-repeat"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="ec-pro-content">
                                        <a href="shop-left-sidebar-col-3.html">
                                            <h6 class="ec-pro-stitle">Toys</h6>
                                        </a>
                                        <h5 class="ec-pro-title"><a href="product-left-sidebar.html">Baby toy teddy
                                                bear</a></h5>
                                        <div class="ec-pro-rat-price">
                                            <span class="ec-price">
                                                <span class="new-price">$199.00</span>
                                                <span class="old-price">$200.00</span>
                                            </span>
                                            <span class="ec-pro-rating">
                                                <i class="ecicon eci-star fill"></i>
                                                <i class="ecicon eci-star fill"></i>
                                                <i class="ecicon eci-star fill"></i>
                                                <i class="ecicon eci-star fill"></i>
                                                <i class="ecicon eci-star"></i>
                                            </span>
                                        </div>
                                        <div class="ec-pro-btn">
                                            <button title="Add To Cart" class="add-to-cart">Add To Cart</button>
                                            <button title="Select Option" class="select-option">Select
                                                Option</button>
                                            <div class="ec-pro-option">
                                                <div class="ec-pro-opt-inner">
                                                    <div class="ec-pro-color">
                                                        <ul class="ec-opt-swatch ec-change-img">
                                                            <li class="active"><a href="#"
                                                                    class="ec-opt-clr-img"
                                                                    data-src="visitor/images/product-image/29_1.jpg"
                                                                    data-src-hover="visitor/images/product-image/29_1.jpg"
                                                                    data-tooltip="Gray"><span
                                                                        style="background-color:#a88960;"></span></a>
                                                            </li>
                                                            <li><a href="#" class="ec-opt-clr-img"
                                                                    data-src="visitor/images/product-image/29_2.jpg"
                                                                    data-src-hover="visitor/images/product-image/29_2.jpg"
                                                                    data-tooltip="Orange"><span
                                                                        style="background-color:#f778fc;"></span></a>
                                                            </li>
                                                            <li><a href="#" class="ec-opt-clr-img"
                                                                    data-src="visitor/images/product-image/29_3.jpg"
                                                                    data-src-hover="visitor/images/product-image/29_3.jpg"
                                                                    data-tooltip="Green"><span
                                                                        style="background-color:#8b83ff;"></span></a>
                                                            </li>
                                                            <li><a href="#" class="ec-opt-clr-img"
                                                                    data-src="visitor/images/product-image/29_4.jpg"
                                                                    data-src-hover="visitor/images/product-image/29_4.jpg"
                                                                    data-tooltip="Sky Blue"><span
                                                                        style="background-color:#5ea561;"></span></a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6 ec-product-content">
                                <div class="ec-product-inner">
                                    <div class="ec-pro-image-outer">
                                        <div class="ec-pro-image">
                                            <a href="product-left-sidebar.html" class="image">
                                                <img class="main-image" src="visitor/images/product-image/30_1.jpg"
                                                    alt="Product" />
                                                <img class="hover-image" src="visitor/images/product-image/30_3.jpg"
                                                    alt="Product" />
                                            </a>
                                            <span class="flags">
                                                <span class="sale">Sale</span>
                                            </span>
                                            <div class="ec-pro-actions">
                                                <a class="ec-btn-group wishlist" title="Wishlist"><i
                                                        class="fi-rr-heart"></i></a>
                                                <a href="#" class="ec-btn-group quickview"
                                                    data-link-action="quickview" title="Quick view"
                                                    data-bs-toggle="modal" data-bs-target="#ec_quickview_modal"><i
                                                        class="fi-rr-eye"></i></a>
                                                <a href="compare.html" class="ec-btn-group compare"
                                                    title="Compare"><i class="fi fi-rr-arrows-repeat"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="ec-pro-content">
                                        <a href="shop-left-sidebar-col-3.html">
                                            <h6 class="ec-pro-stitle">Accessories</h6>
                                        </a>
                                        <h5 class="ec-pro-title"><a href="product-left-sidebar.html">Living room
                                                lighting</a></h5>
                                        <div class="ec-pro-rat-price">
                                            <span class="ec-price">
                                                <span class="new-price">$152.00</span>
                                                <span class="old-price">$180.00</span>
                                            </span>
                                            <span class="ec-pro-rating">
                                                <i class="ecicon eci-star fill"></i>
                                                <i class="ecicon eci-star fill"></i>
                                                <i class="ecicon eci-star fill"></i>
                                                <i class="ecicon eci-star fill"></i>
                                                <i class="ecicon eci-star"></i>
                                            </span>
                                        </div>
                                        <div class="ec-pro-btn">
                                            <button title="Add To Cart" class="add-to-cart">Add To Cart</button>
                                            <button title="Select Option" class="select-option">Select
                                                Option</button>
                                            <div class="ec-pro-option">
                                                <div class="ec-pro-opt-inner">
                                                    <div class="ec-pro-color">
                                                        <ul class="ec-opt-swatch ec-change-img">
                                                            <li class="active"><a href="#"
                                                                    class="ec-opt-clr-img"
                                                                    data-src="visitor/images/product-image/30_1.jpg"
                                                                    data-src-hover="visitor/images/product-image/30_1.jpg"
                                                                    data-tooltip="Gray"><span
                                                                        style="background-color:#e9e9e9;"></span></a>
                                                            </li>
                                                            <li><a href="#" class="ec-opt-clr-img"
                                                                    data-src="visitor/images/product-image/30_2.jpg"
                                                                    data-src-hover="visitor/images/product-image/30_2.jpg"
                                                                    data-tooltip="Orange"><span
                                                                        style="background-color:#b6f0ff;"></span></a>
                                                            </li>
                                                            <li><a href="#" class="ec-opt-clr-img"
                                                                    data-src="visitor/images/product-image/30_3.jpg"
                                                                    data-src-hover="visitor/images/product-image/30_3.jpg"
                                                                    data-tooltip="Green"><span
                                                                        style="background-color:#a2ffcc;"></span></a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- ec 3rd Product tab end -->
                </div>
            </div>
        </div>
    </div>
</section>
<!-- ec Product tab Area End -->

<!-- ec Banner Section Start -->
<section class="ec-banner section section-space-p">
    <h2 class="d-none">Banner</h2>
    <div class="container">
        <div class="row">
            <div class="ec-banner-inner">
                <div class="ec-banner-block ec-banner-block-1">
                    <div class="banner-block">
                        <img src="visitor/images/banner/19.jpg" alt="" />
                        <div class="banner-content">
                            <div class="banner-text">
                                <span class="ec-banner-disc">25% discount</span>
                                <span class="ec-banner-title">New Security Camera</span>
                                <span class="ec-banner-stitle">Starting @ $225</span>
                            </div>
                            <span class="ec-banner-btn"><a href="#">Shop Now <i
                                        class="ecicon eci-angle-double-right" aria-hidden="true"></i></a></span>
                        </div>
                    </div>
                </div>
                <div class="ec-banner-block ec-banner-block-2">
                    <div class="banner-block">
                        <img src="visitor/images/banner/20.jpg" alt="" />
                        <div class="banner-content">
                            <div class="banner-text">
                                <span class="ec-banner-disc">45% discount</span>
                                <span class="ec-banner-title">Small bottle grinder</span>
                                <span class="ec-banner-stitle">Starting @ $225</span>
                            </div>
                            <span class="ec-banner-btn"><a href="#">Shop Now <i
                                        class="ecicon eci-angle-double-right" aria-hidden="true"></i></a></span>
                        </div>
                    </div>
                </div>
                <div class="ec-banner-block ec-banner-block-3">
                    <div class="banner-block">
                        <img src="visitor/images/banner/21.jpg" alt="" />
                        <div class="banner-content">
                            <div class="banner-text">
                                <span class="ec-banner-disc">Upto 20% off</span>
                                <span class="ec-banner-title">Glossy red lipstick</span>
                                <span class="ec-banner-stitle">Starting @ $225</span>
                            </div>
                            <span class="ec-banner-btn"><a href="#">Shop Now <i
                                        class="ecicon eci-angle-double-right" aria-hidden="true"></i></a></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- ec Banner Section End -->

<!--  Day of deal & service Section Start -->
<section class="section ec-ser-spe-section section-space-p">
    <div class="container">
        <div class="row">
            <!--  Special Section Start -->
            <div class="ec-spe-section col-lg-9 col-md-9 col-sm-9 sectopn-spc-mb">
                <div class="col-md-12">
                    <div class="section-title">
                        <h2 class="ec-title">Deal of the day</h2>
                    </div>
                </div>

                <div class="ec-spe-products">
                    <div class="ec-spe-product">
                        <div class="ec-spe-pro-inner">
                            <div class="ec-spe-pro-image-outer col-md-6 col-sm-12">
                                <div class="ec-spe-pro-image">
                                    <div class="ec-spe-pro-cover">
                                        <div class="ec-spe-pro-slide">
                                            <img class="img-responsive" src="visitor/images/product-image/8.jpg"
                                                alt="">
                                        </div>
                                        <div class="ec-spe-pro-slide">
                                            <img class="img-responsive" src="visitor/images/product-image/9.jpg"
                                                alt="">
                                        </div>
                                        <div class="ec-spe-pro-slide">
                                            <img class="img-responsive" src="visitor/images/product-image/10.jpg"
                                                alt="">
                                        </div>
                                        <div class="ec-spe-pro-slide">
                                            <img class="img-responsive" src="visitor/images/product-image/11.jpg"
                                                alt="">
                                        </div>
                                    </div>
                                    <div class="ec-spe-pro-thumb">
                                        <div class="ec-spe-pro-slide">
                                            <img class="img-responsive" src="visitor/images/product-image/8.jpg"
                                                alt="">
                                        </div>
                                        <div class="ec-spe-pro-slide">
                                            <img class="img-responsive" src="visitor/images/product-image/9.jpg"
                                                alt="">
                                        </div>
                                        <div class="ec-spe-pro-slide">
                                            <img class="img-responsive" src="visitor/images/product-image/10.jpg"
                                                alt="">
                                        </div>
                                        <div class="ec-spe-pro-slide">
                                            <img class="img-responsive" src="visitor/images/product-image/11.jpg"
                                                alt="">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="ec-spe-pro-content col-md-6 col-sm-12">
                                <div class="ec-spe-pro-rating">
                                    <span class="ec-spe-rating-icon">
                                        <i class="ecicon eci-star fill"></i>
                                        <i class="ecicon eci-star fill"></i>
                                        <i class="ecicon eci-star fill"></i>
                                        <i class="ecicon eci-star"></i>
                                        <i class="ecicon eci-star"></i>
                                    </span>
                                </div>
                                <h5 class="ec-spe-pro-title"><a href="product-left-sidebar.html">Solo Wireless smart
                                        Headphones</a></h5>
                                <div class="ec-spe-pro-desc">Lorem ipsum dolor sit amet consectetur Lorem ipsum
                                    dolor dolor sit amet consectetur Lorem ipsum dolor</div>
                                <div class="ec-spe-price">
                                    <span class="new-price">$199.00</span>
                                    <span class="old-price">$200.00</span>
                                </div>
                                <div class="ec-spe-pro-btn">
                                    <a href="#" class="btn btn-lg btn-primary">Add To Cart</a>
                                </div>
                                <div class="ec-spe-pro-progress">
                                    <span class="ec-spe-pro-progress-desc"><span>Already Sold:
                                            <b>20</b></span><span>Available: <b>40</b></span></span>
                                    <span class="ec-spe-pro-progressbar"></span>
                                </div>
                                <div class="countdowntimer">
                                    <span class="ec-spe-count-desc"> Hurry Up! Offer ends in:</span>
                                    <span id="ec-spe-count-1"></span>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="ec-spe-product">
                        <div class="ec-spe-pro-inner">
                            <div class="ec-spe-pro-image-outer col-md-6 col-sm-12">
                                <div class="ec-spe-pro-image">
                                    <div class="ec-spe-pro-cover">
                                        <div class="ec-spe-pro-slide">
                                            <img class="img-responsive" src="visitor/images/product-image/12.jpg"
                                                alt="">
                                        </div>
                                        <div class="ec-spe-pro-slide">
                                            <img class="img-responsive" src="visitor/images/product-image/13.jpg"
                                                alt="">
                                        </div>
                                        <div class="ec-spe-pro-slide">
                                            <img class="img-responsive" src="visitor/images/product-image/14.jpg"
                                                alt="">
                                        </div>
                                        <div class="ec-spe-pro-slide">
                                            <img class="img-responsive" src="visitor/images/product-image/15.jpg"
                                                alt="">
                                        </div>
                                    </div>
                                    <div class="ec-spe-pro-thumb">
                                        <div class="ec-spe-pro-slide">
                                            <img class="img-responsive" src="visitor/images/product-image/12.jpg"
                                                alt="">
                                        </div>
                                        <div class="ec-spe-pro-slide">
                                            <img class="img-responsive" src="visitor/images/product-image/13.jpg"
                                                alt="">
                                        </div>
                                        <div class="ec-spe-pro-slide">
                                            <img class="img-responsive" src="visitor/images/product-image/14.jpg"
                                                alt="">
                                        </div>
                                        <div class="ec-spe-pro-slide">
                                            <img class="img-responsive" src="visitor/images/product-image/15.jpg"
                                                alt="">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="ec-spe-pro-content col-md-6 col-sm-12">
                                <div class="ec-spe-pro-rating">
                                    <span class="ec-spe-rating-icon">
                                        <i class="ecicon eci-star fill"></i>
                                        <i class="ecicon eci-star fill"></i>
                                        <i class="ecicon eci-star fill"></i>
                                        <i class="ecicon eci-star"></i>
                                        <i class="ecicon eci-star"></i>
                                    </span>
                                </div>
                                <h5 class="ec-spe-pro-title"><a href="product-left-sidebar.html">Royal fragrance
                                        Perfume 50ml</a></h5>
                                <div class="ec-spe-pro-desc">Lorem ipsum dolor sit amet consectetur Lorem ipsum
                                    dolor dolor sit amet consectetur Lorem ipsum dolor</div>
                                <div class="ec-spe-price">
                                    <span class="new-price">$199.00</span>
                                    <span class="old-price">$200.00</span>
                                </div>
                                <div class="ec-spe-pro-btn">
                                    <a href="#" class="btn btn-lg btn-primary">Add To Cart</a>
                                </div>
                                <div class="ec-spe-pro-progress">
                                    <span class="ec-spe-pro-progress-desc"><span>Already Sold:
                                            <b>20</b></span><span>Available: <b>40</b></span></span>
                                    <span class="ec-spe-pro-progressbar"></span>
                                </div>
                                <div class="countdowntimer">
                                    <span class="ec-spe-count-desc"> Hurry Up! Offer ends in:</span>
                                    <span id="ec-spe-count-2"></span>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--  Testimonial & Special Section End -->

            <!--  services Section Start -->
            <div class="ec-services-section col-lg-3 col-md-3 col-sm-3">
                <div class="col-md-12">
                    <div class="section-title">
                        <h2 class="ec-title">Our Services</h2>
                    </div>
                </div>
                <div class="ec_ser_block">
                    <div class="ec_ser_content ec_ser_content_1 col-sm-12">
                        <div class="ec_ser_inner">
                            <div class="ec-service-image">
                                <i class="fi fi-ts-truck-moving"></i>
                            </div>
                            <div class="ec-service-desc">
                                <h2>Worldwide Delivery</h2>
                                <p>For Order Over $100</p>
                            </div>
                        </div>
                    </div>
                    <div class="ec_ser_content ec_ser_content_2 col-sm-12">
                        <div class="ec_ser_inner">
                            <div class="ec-service-image">
                                <i class="fi fi-ts-tachometer-fast"></i>
                            </div>
                            <div class="ec-service-desc">
                                <h2>Next Day delivery</h2>
                                <p>UK Orders Only</p>
                            </div>
                        </div>
                    </div>
                    <div class="ec_ser_content ec_ser_content_3 col-sm-12">
                        <div class="ec_ser_inner">
                            <div class="ec-service-image">
                                <i class="fi fi-ts-circle-phone"></i>
                            </div>
                            <div class="ec-service-desc">
                                <h2>Best Online Support</h2>
                                <p>Hours: 8AM -11PM</p>
                            </div>
                        </div>
                    </div>
                    <div class="ec_ser_content ec_ser_content_4 col-sm-12">
                        <div class="ec_ser_inner">
                            <div class="ec-service-image">
                                <i class="fi fi-ts-badge-percent"></i>
                            </div>
                            <div class="ec-service-desc">
                                <h2>Return Policy</h2>
                                <p>Easy & Free Return</p>
                            </div>
                        </div>
                    </div>
                    <div class="ec_ser_content ec_ser_content_5 col-sm-12">
                        <div class="ec_ser_inner">
                            <div class="ec-service-image">
                                <i class="fi fi-ts-donate"></i>
                            </div>
                            <div class="ec-service-desc">
                                <h2>30% money back</h2>
                                <p>For Order Over $100</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--services Section End -->

</section>
<!--  End Day of deal & service Section Start -->

<!-- Testimonial & Ec new product section -->
<section class="section ec-new-test-product section-space-p">
    <div class="container">
        <div class="row">
            <!-- ec testimonial Start -->
            <div class="ec-test-section col-lg-3 col-md-3 col-sm-12 sectopn-spc-mb">
                <div class="col-md-12">
                    <div class="section-title">
                        <h2 class="ec-title">Testimonial</h2>
                    </div>
                </div>
                <div class="ec-test-outer">
                    <ul id="ec-testimonial-slider">
                        <li class="ec-test-item">
                            <div class="ec-test-inner">
                                <div class="ec-test-img">
                                    <img alt="testimonial" title="testimonial"
                                        src="visitor/images/testimonial/1.jpg" />
                                </div>
                                <div class="ec-test-content">
                                    <div class="ec-test-name">mark jofferson</div>
                                    <div class="ec-test-designation">- CEO & Founder Invision</div>
                                    <div class="ec-test-divider">
                                        <i class="fi-rr-quote-right"></i>
                                    </div>
                                    <div class="ec-test-desc">Lorem ipsum dolor sit amet consectetur Lorem ipsum
                                        dolor dolor sit amet.
                                    </div>
                                </div>
                            </div>
                        </li>
                        <li class="ec-test-item">
                            <div class="ec-test-inner">
                                <div class="ec-test-img">
                                    <img alt="testimonial" title="testimonial"
                                        src="visitor/images/testimonial/2.jpg" />
                                </div>
                                <div class="ec-test-content">
                                    <div class="ec-test-name">mark jofferson</div>
                                    <div class="ec-test-designation">- CEO & Founder Invision</div>
                                    <div class="ec-test-divider">
                                        <i class="fi-rr-quote-right"></i>
                                    </div>
                                    <div class="ec-test-desc">Lorem ipsum dolor sit amet consectetur Lorem ipsum
                                        dolor dolor sit amet.
                                    </div>
                                </div>
                            </div>
                        </li>
                        <li class="ec-test-item">
                            <div class="ec-test-inner">
                                <div class="ec-test-img">
                                    <img alt="testimonial" title="testimonial"
                                        src="visitor/images/testimonial/3.jpg" />
                                </div>
                                <div class="ec-test-content">
                                    <div class="ec-test-name">mark jofferson</div>
                                    <div class="ec-test-designation">- CEO & Founder Invision</div>
                                    <div class="ec-test-divider">
                                        <i class="fi-rr-quote-right"></i>
                                    </div>
                                    <div class="ec-test-desc">Lorem ipsum dolor sit amet consectetur Lorem ipsum
                                        dolor dolor sit amet.
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
            <!-- ec testimonial end -->
            <!-- New product Item Start -->
            <div class="ec-new-product col-lg-9 col-md-9 col-sm-12 margin-minus-b-15">
                <div class="col-md-12">
                    <div class="section-title">
                        <h2 class="ec-title">New Products</h2>
                    </div>
                </div>
                <div class="ec-new-product-block">
                    <div class="ec-new-product-inner">
                        <div class="col-lg-4 col-md-6 col-sm-6 col-xs-6 ec-product-content">
                            <div class="ec-product-inner">
                                <div class="ec-pro-image-outer">
                                    <div class="ec-pro-image">
                                        <a href="product-left-sidebar.html" class="image">
                                            <img class="main-image" src="visitor/images/product-image/37_1.jpg"
                                                alt="Product" />
                                            <img class="hover-image" src="visitor/images/product-image/37_2.jpg"
                                                alt="Product" />
                                        </a>
                                        <span class="flags">
                                            <span class="new">New</span>
                                        </span>
                                        <div class="ec-pro-actions">
                                            <a class="ec-btn-group wishlist" title="Wishlist"><i
                                                    class="fi-rr-heart"></i></a>
                                            <a href="#" class="ec-btn-group quickview"
                                                data-link-action="quickview" title="Quick view"
                                                data-bs-toggle="modal" data-bs-target="#ec_quickview_modal"><i
                                                    class="fi-rr-eye"></i></a>
                                            <a href="compare.html" class="ec-btn-group compare" title="Compare"><i
                                                    class="fi fi-rr-arrows-repeat"></i></a>
                                        </div>
                                    </div>
                                </div>
                                <div class="ec-pro-content">
                                    <a href="shop-left-sidebar-col-3.html">
                                        <h6 class="ec-pro-stitle">Beauty</h6>
                                    </a>
                                    <h5 class="ec-pro-title"><a href="product-left-sidebar.html">Lime Crime
                                            Velvetines Liquid Matte Lipstick</a></h5>
                                    <div class="ec-pro-rat-price">
                                        <span class="ec-price">
                                            <span class="new-price">$50.00</span>
                                            <span class="old-price">$60.00</span>
                                        </span>
                                        <span class="ec-pro-rating">
                                            <i class="ecicon eci-star fill"></i>
                                            <i class="ecicon eci-star fill"></i>
                                            <i class="ecicon eci-star fill"></i>
                                            <i class="ecicon eci-star fill"></i>
                                            <i class="ecicon eci-star"></i>
                                        </span>
                                    </div>
                                    <div class="ec-pro-btn">
                                        <button title="Add To Cart" class="add-to-cart">Add To Cart</button>
                                        <button title="Select Option" class="select-option">Select Option</button>
                                        <div class="ec-pro-option">
                                            <div class="ec-pro-opt-inner">
                                                <div class="ec-pro-size">
                                                    <ul class="ec-opt-size">
                                                        <li class="active"><a href="#" class="ec-opt-sz"
                                                                data-old="$60.00" data-new="$50.00"
                                                                data-tooltip="Small">Pink</a></li>
                                                        <li><a href="#" class="ec-opt-sz" data-old="$70.00"
                                                                data-new="$65.00" data-tooltip="Medium">Brown</a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 col-sm-6 col-xs-6 ec-product-content">
                            <div class="ec-product-inner">
                                <div class="ec-pro-image-outer">
                                    <div class="ec-pro-image">
                                        <a href="product-left-sidebar.html" class="image">
                                            <img class="main-image" src="visitor/images/product-image/35_1.jpg"
                                                alt="Product" />
                                            <img class="hover-image" src="visitor/images/product-image/35_3.jpg"
                                                alt="Product" />
                                        </a>
                                        <span class="flags">
                                            <span class="sale">Sale</span>
                                        </span>
                                        <div class="ec-pro-actions">
                                            <a class="ec-btn-group wishlist" title="Wishlist"><i
                                                    class="fi-rr-heart"></i></a>
                                            <a href="#" class="ec-btn-group quickview"
                                                data-link-action="quickview" title="Quick view"
                                                data-bs-toggle="modal" data-bs-target="#ec_quickview_modal"><i
                                                    class="fi-rr-eye"></i></a>
                                            <a href="compare.html" class="ec-btn-group compare" title="Compare"><i
                                                    class="fi fi-rr-arrows-repeat"></i></a>
                                        </div>
                                    </div>
                                </div>
                                <div class="ec-pro-content">
                                    <a href="shop-left-sidebar-col-3.html">
                                        <h6 class="ec-pro-stitle">Clothes</h6>
                                    </a>
                                    <h5 class="ec-pro-title"><a href="product-left-sidebar.html">Hooded Neck full
                                            slive T-Shirt</a></h5>
                                    <div class="ec-pro-rat-price">
                                        <span class="ec-price">
                                            <span class="new-price">$49.00</span>
                                            <span class="old-price">$65.00</span>
                                        </span>
                                        <span class="ec-pro-rating">
                                            <i class="ecicon eci-star fill"></i>
                                            <i class="ecicon eci-star fill"></i>
                                            <i class="ecicon eci-star fill"></i>
                                            <i class="ecicon eci-star fill"></i>
                                            <i class="ecicon eci-star"></i>
                                        </span>
                                    </div>
                                    <div class="ec-pro-btn">
                                        <button title="Add To Cart" class="add-to-cart">Add To Cart</button>
                                        <button title="Select Option" class="select-option">Select Option</button>
                                        <div class="ec-pro-option">
                                            <div class="ec-pro-opt-inner">
                                                <div class="ec-pro-color">
                                                    <ul class="ec-opt-swatch ec-change-img">
                                                        <li class="active"><a href="#"
                                                                class="ec-opt-clr-img"
                                                                data-src="visitor/images/product-image/35_1.jpg"
                                                                data-src-hover="visitor/images/product-image/35_1.jpg"
                                                                data-tooltip="Gray"><span
                                                                    style="background-color:#fda6a6;"></span></a>
                                                        </li>
                                                        <li><a href="#" class="ec-opt-clr-img"
                                                                data-src="visitor/images/product-image/35_2.jpg"
                                                                data-src-hover="visitor/images/product-image/35_2.jpg"
                                                                data-tooltip="Orange"><span
                                                                    style="background-color:#93beff;"></span></a>
                                                        </li>
                                                        <li><a href="#" class="ec-opt-clr-img"
                                                                data-src="visitor/images/product-image/35_3.jpg"
                                                                data-src-hover="visitor/images/product-image/35_3.jpg"
                                                                data-tooltip="Green"><span
                                                                    style="background-color:#cc9569;"></span></a>
                                                        </li>
                                                    </ul>
                                                </div>
                                                <div class="ec-pro-size">
                                                    <ul class="ec-opt-size">
                                                        <li class="active"><a href="#" class="ec-opt-sz"
                                                                data-old="$65.00" data-new="$49.00"
                                                                data-tooltip="Small">S</a></li>
                                                        <li><a href="#" class="ec-opt-sz" data-old="$70.00"
                                                                data-new="$55.00" data-tooltip="Medium">M</a></li>
                                                        <li><a href="#" class="ec-opt-sz" data-old="$82.00"
                                                                data-new="$60.00" data-tooltip="Large">L</a></li>
                                                        <li><a href="#" class="ec-opt-sz" data-old="$85.00"
                                                                data-new="$70.00" data-tooltip="Extra Large">XL</a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 col-sm-6 col-xs-6 ec-product-content m-auto">
                            <div class="ec-product-inner">
                                <div class="ec-pro-image-outer">
                                    <div class="ec-pro-image">
                                        <a href="product-left-sidebar.html" class="image">
                                            <img class="main-image" src="visitor/images/product-image/38_1.jpg"
                                                alt="Product" />
                                            <img class="hover-image" src="visitor/images/product-image/38_2.jpg"
                                                alt="Product" />
                                        </a>
                                        <span class="flags">
                                            <span class="sale">Sale</span>
                                        </span>
                                        <div class="ec-pro-actions">
                                            <a class="ec-btn-group wishlist" title="Wishlist"><i
                                                    class="fi-rr-heart"></i></a>
                                            <a href="#" class="ec-btn-group quickview"
                                                data-link-action="quickview" title="Quick view"
                                                data-bs-toggle="modal" data-bs-target="#ec_quickview_modal"><i
                                                    class="fi-rr-eye"></i></a>
                                            <a href="compare.html" class="ec-btn-group compare" title="Compare"><i
                                                    class="fi fi-rr-arrows-repeat"></i></a>
                                        </div>
                                    </div>
                                </div>
                                <div class="ec-pro-content">
                                    <a href="shop-left-sidebar-col-3.html">
                                        <h6 class="ec-pro-stitle">Accessories</h6>
                                    </a>
                                    <h5 class="ec-pro-title"><a href="product-left-sidebar.html">Bathroom and Wash
                                            Basin Hand Wash</a></h5>
                                    <div class="ec-pro-rat-price">
                                        <span class="ec-price">
                                            <span class="new-price">$199.00</span>
                                            <span class="old-price">$200.00</span>
                                        </span>
                                        <span class="ec-pro-rating">
                                            <i class="ecicon eci-star fill"></i>
                                            <i class="ecicon eci-star fill"></i>
                                            <i class="ecicon eci-star fill"></i>
                                            <i class="ecicon eci-star fill"></i>
                                            <i class="ecicon eci-star"></i>
                                        </span>
                                    </div>
                                    <div class="ec-pro-btn">
                                        <button title="Add To Cart" class="add-to-cart">Add To Cart</button>
                                        <button title="Select Option" class="select-option">Select Option</button>
                                        <div class="ec-pro-option">
                                            <div class="ec-pro-opt-inner">
                                                <div class="ec-pro-size">
                                                    <ul class="ec-opt-size">
                                                        <li class="active"><a href="#" class="ec-opt-sz"
                                                                data-old="$200.00" data-new="$199.00"
                                                                data-tooltip="Small">Gray</a></li>
                                                        <li><a href="#" class="ec-opt-sz" data-old="$210.00"
                                                                data-new="$209.00" data-tooltip="Medium">Purple</a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- New product Item end -->
        </div>
    </div>
</section>
<!-- End Testimonial & new product section -->

<!-- Ec Brand Section Start -->
<section class="section ec-brand-area section-space-p">
    <h2 class="d-none">Brand</h2>
    <div class="container">
        <div class="row">
            <div class="ec-brand-outer">
                <ul id="ec-brand-slider">
                    <li class="ec-brand-item">
                        <div class="ec-brand-img"><a href="#"><img alt="brand" title="brand"
                                    src="visitor/images/brand-image/1.png" /></a></div>
                    </li>
                    <li class="ec-brand-item">
                        <div class="ec-brand-img"><a href="#"><img alt="brand" title="brand"
                                    src="visitor/images/brand-image/2.png" /></a></div>
                    </li>
                    <li class="ec-brand-item">
                        <div class="ec-brand-img"><a href="#"><img alt="brand" title="brand"
                                    src="visitor/images/brand-image/3.png" /></a></div>
                    </li>
                    <li class="ec-brand-item">
                        <div class="ec-brand-img"><a href="#"><img alt="brand" title="brand"
                                    src="visitor/images/brand-image/4.png" /></a></div>
                    </li>
                    <li class="ec-brand-item">
                        <div class="ec-brand-img"><a href="#"><img alt="brand" title="brand"
                                    src="visitor/images/brand-image/5.png" /></a></div>
                    </li>
                    <li class="ec-brand-item">
                        <div class="ec-brand-img"><a href="#"><img alt="brand" title="brand"
                                    src="visitor/images/brand-image/6.png" /></a></div>
                    </li>
                    <li class="ec-brand-item">
                        <div class="ec-brand-img"><a href="#"><img alt="brand" title="brand"
                                    src="visitor/images/brand-image/7.png" /></a></div>
                    </li>
                    <li class="ec-brand-item">
                        <div class="ec-brand-img"><a href="#"><img alt="brand" title="brand"
                                    src="visitor/images/brand-image/8.png" /></a></div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</section>
<!-- Ec Brand Section End -->

<!-- Ec Instagram Start -->
<section class="section ec-instagram-section section-space-pt">
    <h2 class="d-none">Instagram</h2>
    <div class="ec-insta-wrapper">
        <div class="ec-insta-outer">
            <div class="insta-auto">
                <!-- instagram item -->
                <div class="ec-insta-item">
                    <div class="ec-insta-inner">
                        <a href="#" target="_blank"><img src="visitor/images/instragram-image/1.jpg"
                                alt="">

                        </a>
                    </div>
                </div>
                <!-- instagram item -->
                <div class="ec-insta-item">
                    <div class="ec-insta-inner">
                        <a href="#" target="_blank"><img src="visitor/images/instragram-image/2.jpg"
                                alt="">

                        </a>
                    </div>
                </div>
                <!-- instagram item -->
                <div class="ec-insta-item">
                    <div class="ec-insta-inner">
                        <a href="#" target="_blank"><img src="visitor/images/instragram-image/3.jpg"
                                alt="">

                        </a>
                    </div>
                </div>
                <!-- instagram item -->
                <div class="ec-insta-item">
                    <div class="ec-insta-inner">
                        <a href="#" target="_blank"><img src="visitor/images/instragram-image/4.jpg"
                                alt="">

                        </a>
                    </div>
                </div>
                <!-- instagram item -->
            </div>
        </div>
    </div>
</section>
<!-- Ec Instagram End -->

@endsection