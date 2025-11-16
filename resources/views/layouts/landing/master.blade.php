<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Toko Daging Sawangan</title>
    <meta name="description" content="Toko Daging Sawangan">
    <meta name="keywords" content="TDS, Toko Daging Sawangan">

    <link href="{{ asset('') }}assets_landing/img/logotds.png" rel="icon">
    <link href="{{ asset('') }}assets_landing/img/logotds.png" rel="apple-touch-icon">

    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Open+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,300;1,400;1,500;1,600;1,700;1,800&family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">

    <link href="{{ asset('') }}assets_landing/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('') }}assets_landing/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('') }}assets_landing/vendor/aos/aos.css" rel="stylesheet">
    <link href="{{ asset('') }}assets_landing/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
    <link href="{{ asset('') }}assets_landing/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

    <link href="{{ asset('') }}assets_landing/css/main.css" rel="stylesheet">
</head>

<body class="index-page">
    @include('layouts.landing.header')

    <main class="main">
        @yield('content')
    </main>

    @include('layouts.landing.footer')

    <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i
            class="bi bi-arrow-up-short"></i></a>

    <div id="preloader">
        <div></div>
        <div></div>
        <div></div>
        <div></div>
    </div>

    <script src="{{ asset('') }}assets_landing/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('') }}assets_landing/vendor/php-email-form/validate.js"></script>
    <script src="{{ asset('') }}assets_landing/vendor/aos/aos.js"></script>
    <script src="{{ asset('') }}assets_landing/vendor/glightbox/js/glightbox.min.js"></script>
    <script src="{{ asset('') }}assets_landing/vendor/waypoints/noframework.waypoints.js"></script>
    <script src="{{ asset('') }}assets_landing/vendor/purecounter/purecounter_vanilla.js"></script>
    <script src="{{ asset('') }}assets_landing/vendor/swiper/swiper-bundle.min.js"></script>
    <script src="{{ asset('') }}assets_landing/vendor/imagesloaded/imagesloaded.pkgd.min.js"></script>
    <script src="{{ asset('') }}assets_landing/vendor/isotope-layout/isotope.pkgd.min.js"></script>

    <script src="{{ asset('') }}assets_landing/js/main.js"></script>
</body>
