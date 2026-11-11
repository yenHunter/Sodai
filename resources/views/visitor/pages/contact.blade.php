@extends('visitor.layout.app', ['title' => 'Contact Us', 'bodyClass' => 'contact_us_page'])

@section('styles')
    <!-- Main Style -->
    <link rel="stylesheet" href="{{ asset('visitor/css/style.css') }}" />
    <link rel="stylesheet" href="{{ asset('visitor/css/responsive.css') }}" />

    <!-- Background css -->
    <link rel="stylesheet" id="bg-switcher-css" href="{{ asset('visitor/css/backgrounds/bg-4.css') }}">
@endsection

@push('head')
    <script src="https://www.google.com/recaptcha/api.js?render={{ config('services.recaptcha.site_key') }}"></script>
@endpush

@section('content')
    <!-- Ec breadcrumb start -->
    <div class="sticky-header-next-sec  ec-breadcrumb section-space-mb">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="row ec_breadcrumb_inner">
                        <div class="col-md-6 col-sm-12">
                            <h2 class="ec-breadcrumb-title">Contact Us</h2>
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <!-- ec-breadcrumb-list start -->
                            <ul class="ec-breadcrumb-list">
                                <li class="ec-breadcrumb-item"><a href="{{ route('visitor.index') }}">Home</a></li>
                                <li class="ec-breadcrumb-item active">Contact Us</li>
                            </ul>
                            <!-- ec-breadcrumb-list end -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Ec breadcrumb end -->

    <!-- Ec Contact Us page -->
    <section class="ec-page-content section-space-p">
        <div class="container">
            <div class="row">
                <div class="ec-common-wrapper">
                    <div class="ec-contact-leftside">
                        <div class="ec-contact-container">
                            @if (session('success'))
                                <div class="alert alert-success mb-4" role="alert">{{ session('success') }}</div>
                            @endif
                            @if (session('error'))
                                <div class="alert alert-danger mb-4" role="alert">{{ session('error') }}</div>
                            @endif

                            <div class="ec-contact-form">
                                <form id="contactForm" action="{{ route('visitor.contact.store') }}" method="post">
                                    @csrf
                                    <span class="ec-contact-wrap">
                                        <label for="contactFirstName">First Name*</label>
                                        <input id="contactFirstName" type="text" name="first_name"
                                            value="{{ old('first_name') }}" placeholder="Enter your first name" required />
                                        @error('first_name')
                                            <span class="text-danger small">{{ $message }}</span>
                                        @enderror
                                    </span>
                                    <span class="ec-contact-wrap">
                                        <label for="contactLastName">Last Name*</label>
                                        <input id="contactLastName" type="text" name="last_name"
                                            value="{{ old('last_name') }}" placeholder="Enter your last name" required />
                                        @error('last_name')
                                            <span class="text-danger small">{{ $message }}</span>
                                        @enderror
                                    </span>
                                    <span class="ec-contact-wrap">
                                        <label for="contactEmail">Email*</label>
                                        <input id="contactEmail" type="email" name="email" value="{{ old('email') }}"
                                            placeholder="Enter your email address" required />
                                        @error('email')
                                            <span class="text-danger small">{{ $message }}</span>
                                        @enderror
                                    </span>
                                    <span class="ec-contact-wrap">
                                        <label for="contactPhone">Phone Number*</label>
                                        <input id="contactPhone" type="text" name="phone" value="{{ old('phone') }}"
                                            placeholder="Enter your phone number" required />
                                        @error('phone')
                                            <span class="text-danger small">{{ $message }}</span>
                                        @enderror
                                    </span>
                                    <span class="ec-contact-wrap">
                                        <label for="contactMessage">Comments/Questions*</label>
                                        <textarea id="contactMessage" name="message" placeholder="Please leave your comments here.." required>{{ old('message') }}</textarea>
                                        @error('message')
                                            <span class="text-danger small">{{ $message }}</span>
                                        @enderror
                                    </span>
                                    <span class="ec-contact-wrap ec-recaptcha">
                                        <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">
                                        <input type="hidden" id="recaptchaSiteKey"
                                            value="{{ config('services.recaptcha.site_key') }}">
                                        @error('g-recaptcha-response')
                                            <span class="text-danger small">{{ $message }}</span>
                                        @enderror
                                    </span>
                                    <span class="ec-contact-wrap ec-contact-btn">
                                        <button class="btn btn-primary" type="submit" id="contactSubmitBtn">Submit</button>
                                    </span>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="ec-contact-rightside">
                        <div class="ec_contact_map">
                            <div class="ec_map_canvas">
                                @if (setting('company', 'map_embed_url'))
                                    <iframe id="ec_map_canvas" src="{{ setting('company', 'map_embed_url') }}"></iframe>
                                @else
                                    <iframe id="ec_map_canvas"
                                        src="https://www.google.com/maps/embed?pb=!1m14!1m12!1m3!1d116833.8318789773!2d90.33728815181978!3d23.780975728157546!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!5e0!3m2!1sen!2sbd!4v1787812345216!5m2!1sen!2sbd"></iframe>
                                @endif
                            </div>
                        </div>
                        <div class="ec_contact_info">
                            <h1 class="ec_contact_info_head">Contact us</h1>
                            <ul class="align-items-center">
                                <li class="ec-contact-item"><i class="ecicon eci-map-marker"
                                        aria-hidden="true"></i><span>Address :</span>71 Pilgrim Avenue Chevy Chase, east
                                    california. east california. MD
                                    20815, USA</li>
                                <li class="ec-contact-item align-items-center"><i class="ecicon eci-phone"
                                        aria-hidden="true"></i><span>Call Us :</span><a href="tel:+440123456789">+44 0123
                                        456 789</a></li>
                                <li class="ec-contact-item align-items-center"><i class="ecicon eci-envelope"
                                        aria-hidden="true"></i><span>Email :</span><a
                                        href="mailto:example@ec-email.com">example@ec-email.com</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
    @vite(['resources/js/pages/visitor-contact.js'])
@endsection
