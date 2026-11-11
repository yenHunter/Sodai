@extends('visitor.layout.app', ['title' => 'FAQ', 'bodyClass' => ''])

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
                            <h2 class="ec-breadcrumb-title">FAQ</h2>
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <!-- ec-breadcrumb-list start -->
                            <ul class="ec-breadcrumb-list">
                                <li class="ec-breadcrumb-item"><a href="{{ route('visitor.index') }}">Home</a></li>
                                <li class="ec-breadcrumb-item active">FAQ</li>
                            </ul>
                            <!-- ec-breadcrumb-list end -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Ec breadcrumb end -->

    <!-- Ec FAQ page -->
    <section class="ec-page-content section-space-p">
        <div class="container">
            <div class="row">
                <div class="col-md-12 text-center">
                    <div class="section-title">
                        <h2 class="ec-bg-title">FAQ</h2>
                        <h2 class="ec-title">FAQ</h2>
                        <p class="sub-title mb-3">Frequently asked questions</p>
                    </div>
                </div>

                @if ($categories->isEmpty())
                    <div class="col-md-12 text-center">
                        <p class="mb-0">No FAQs are available right now.</p>
                    </div>
                @else
                    <div class="ec-faq-wrapper">
                        @foreach ($categories as $category)
                            <div class="ec-faq-container">
                                <h2 class="ec-faq-heading">{{ $category->name }}</h2>
                                <div id="ec-faq-{{ $category->slug }}">
                                    @foreach ($category->faqs as $faq)
                                        <div class="col-sm-12 ec-faq-block">
                                            <h4 class="ec-faq-title">{{ $faq->question }}</h4>
                                            <div class="ec-faq-content">
                                                <p>{!! nl2br(e($faq->answer)) !!}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </section>
    <!-- Ec FAQ page end -->
@endsection

@section('scripts')
@endsection
