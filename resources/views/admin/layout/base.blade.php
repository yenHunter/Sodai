<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description" content="Ekka - Admin Dashboard HTML Template.">

    <title>@yield('title', 'Sodai')</title>

    <!-- GOOGLE FONTS -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:wght@200;300;400;500;600;700;800&family=Poppins:wght@300;400;500;600;700;800;900&family=Roboto:wght@400;500;700;900&display=swap"
        rel="stylesheet">

    <link href="https://cdn.materialdesignicons.com/4.4.95/css/materialdesignicons.min.css" rel="stylesheet" />

    <!-- Ekka CSS -->
    <link id="ekka-css" rel="stylesheet" href="{{ asset('admin/css/ekka.css') }}" />

    <!-- FAVICON -->
    <link href="{{ asset('admin/img/favicon.png') }}" rel="shortcut icon" />
</head>

<body class="sign-inup" id="body">
    <div class="container d-flex align-items-center justify-content-center form-height-login pt-24px pb-24px">
        <div class="row justify-content-center">
            @yield('content')
        </div>
    </div>

    <!-- Javascript -->
    <script src="{{ asset('admin/plugins/jquery/jquery-3.5.1.min.js') }}"></script>
    <script src="{{ asset('admin/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('admin/plugins/jquery-zoom/jquery.zoom.min.js') }}"></script>
    <script src="{{ asset('admin/plugins/slick/slick.min.js') }}"></script>

    <!-- Ekka Custom -->
    <script src="{{ asset('admin/js/ekka.js') }}"></script>
</body>

</html>
