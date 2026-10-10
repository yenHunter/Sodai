<!-- ----- Head Section Start ----- -->
<meta charset="UTF-8">
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta http-equiv="x-ua-compatible" content="ie=edge" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0">

@php $brand = \App\Support\StorefrontBranding::common(); @endphp
<title>{{ $brand['name'] }} - {{ $title }}</title>
@if (isset($page) && $page->meta_description)
    <meta name="description" content="{{ $page->meta_description }}">
@else
    <meta name="description" content="{{ $brand['description'] }}">
@endif
<meta name="keywords" content="{{ $brand['keywords'] }}" />
<meta name="author" content="{{ $brand['author'] }}">

<!-- site Favicon -->
<link rel="icon" href="{{ $brand['favicon'] }}" sizes="32x32" />
<link rel="apple-touch-icon" href="{{ $brand['favicon'] }}" />
<meta name="msapplication-TileImage" content="{{ $brand['favicon'] }}" />

<!-- css Icon Font -->
<link rel="stylesheet" href="{{ asset('visitor/css/vendor/ecicons.min.css') }}" />

<!-- css All Plugins Files -->
<link rel="stylesheet" href="{{ asset('visitor/css/plugins/animate.css') }}" />
<link rel="stylesheet" href="{{ asset('visitor/css/plugins/swiper-bundle.min.css') }}" />
<link rel="stylesheet" href="{{ asset('visitor/css/plugins/jquery-ui.min.css') }}" />
<link rel="stylesheet" href="{{ asset('visitor/css/plugins/countdownTimer.css') }}" />
<link rel="stylesheet" href="{{ asset('visitor/css/plugins/slick.min.css') }}" />
<link rel="stylesheet" href="{{ asset('visitor/css/plugins/bootstrap.css') }}" />

<!-- Main Style -->
@yield("styles")
<link rel="stylesheet" href="{{ asset('visitor/css/style.css') }}" />
<link rel="stylesheet" href="{{ asset('visitor/css/responsive.css') }}" />

<!-- Background css -->
<link rel="stylesheet" id="bg-switcher-css" href="{{ asset('visitor/css/backgrounds/bg-4.css') }}">
<!-- ----- Head Section End ----- -->
