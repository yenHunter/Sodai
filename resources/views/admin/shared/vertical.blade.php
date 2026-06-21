<!DOCTYPE html>

<html @yield("html_attribute")>

<head>
    @include("admin.shared.partials.title-meta")

    @yield("styles")

    @include("admin.shared.partials.head-css")
</head>

<body>
    <div class="wrapper">

        @include("admin.shared.partials.topbar")

        @include("admin.shared.partials.sidenav")

        <div class="content-page">
            <div class="container-fluid">

                @yield("content")

            </div>

            @include("admin.shared.partials.footer")

        </div>

        @include("admin.shared.partials.customizer")

    </div>

    @yield("scripts")

</body>

</html>
