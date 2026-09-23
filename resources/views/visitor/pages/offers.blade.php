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
            @forelse ($offers as $offer)
                <div class="ec-line-offer" style="background-image: url('{{ $offer->image_url }}');">
                    <div class="ec-line-offer-info">
                        <div class="container">
                            <div class="row">
                                <div class="col-md-6">
                                    @if ($offer->title)
                                        <h6>{{ $offer->title }}</h6>
                                    @endif
                                    @if ($offer->subtitle)
                                        <h2 class="offer-upto">{{ $offer->subtitle }}</h2>
                                    @endif
                                    @if ($offer->description)
                                        <p class="offer-desc">{{ $offer->description }}</p>
                                    @endif
                                    @if ($offer->button_text)
                                        <div class="offer-btn">
                                            <a href="{{ $offer->resolved_button_url }}"
                                                class="btn-shop-now">{{ $offer->button_text }}</a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="container text-center py-5">
                    <p class="mb-0">No active offers right now — check back soon!</p>
                </div>
            @endforelse
        </div>
    </section>
    <!-- End Offer section -->
@endsection

@section('scripts')
@endsection
