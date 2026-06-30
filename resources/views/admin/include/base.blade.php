<!DOCTYPE html>

<html @yield("html_attribute") lang="en">

<head>
    <meta charset="utf-8" />
    <title>{{ $title }}</title>
    <meta content="width=device-width, initial-scale=1" name="viewport" />

    @include("admin.include.partials.title-meta")

    @yield("styles")

    @include("admin.include.partials.head-css")

</head>

<body @yield("body_attribute")>

    @yield("content")

    @yield("scripts")

</body>

</html>
