@extends('visitor.layout.app', ['title' => 'Offers', 'bodyClass' => ''])

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
                            <h2 class="ec-breadcrumb-title">Hot Offer</h2>
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <!-- ec-breadcrumb-list start -->
                            <ul class="ec-breadcrumb-list">
                                <li class="ec-breadcrumb-item"><a href="index.html">Home</a></li>
                                <li class="ec-breadcrumb-item active">Offer</li>
                            </ul>
                            <!-- ec-breadcrumb-list end -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Ec breadcrumb end -->

    <!-- Start Offer section -->
    <section class="labels section-space-p">
        <div class="container">
            <div class="row">
                <div class="col-md-12 text-center">
                    <div class="section-title">
                        <h2 class="ec-bg-title">Hot Offers</h2>
                        <h2 class="ec-title">Hot Offers</h2>
                        <p class="sub-title">Browse The Collection of Top Categories</p>
                    </div>
                </div>
            </div>
            <div class="ec-line-offer" style="background-image: url('assets/images/offer-image/offer-banner-06.jpg');">
                <div class="ec-line-offer-info">
                    <div class="container">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>On Furniture</h6>
                                <h2 class="offer-upto">Upto <span>45%</span> OFF</h2>
                                <p class="offer-desc">Lorem Ipsum is simply dummy text of the printing and typesetting
                                    industry. Lorem Ipsum has been the industry's.</p>
                                <div class="offer-btn"><a class="btn-shop-now">SHOP NOW!</a></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- End Offer section -->
@endsection

@section('scripts')
@endsection
