<!DOCTYPE html>

<html @yield("html_attribute")>

<head>
    @include("admin.include.partials.title-meta")

    @yield("styles")

    @include("admin.include.partials.head-css")
</head>

<body>
    <div class="wrapper">

        @include("admin.include.partials.topbar")

        @include("admin.include.partials.sidenav")

        <div class="content-page">
            <div class="container-fluid">

                @yield("content")

            </div>

            @include("admin.include.partials.footer")

        </div>

        @include("admin.include.partials.customizer")

    </div>

    @yield("scripts")

</body>

</html>
