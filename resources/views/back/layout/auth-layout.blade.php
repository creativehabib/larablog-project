<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title> @yield('pageTitle') | Looper - Bootstrap 4 Admin Theme </title>
    <meta property="og:title" content="Sign In">
    <meta name="author" content="Beni Arisandi">
    <meta property="og:locale" content="en_US">
    <meta name="description" content="Responsive admin theme build on top of Bootstrap 4">
    <meta property="og:description" content="Responsive admin theme build on top of Bootstrap 4">
    <link rel="canonical" href="{{ url()->current() }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:site_name" content="Looper - Bootstrap 4 Admin Theme">

    <link rel="apple-touch-icon" sizes="144x144" href="{{ asset('assets/apple-touch-icon.png') }}">
    <link rel="shortcut icon" href="{{ asset('assets/favicon.ico') }}">
    <meta name="theme-color" content="#3063A0">

    <link href="https://fonts.googleapis.com/css?family=Fira+Sans:400,500,600" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('assets/vendor/@fortawesome/fontawesome-free/css/all.min.css') }}">

    <link rel="stylesheet" href="{{ asset('assets/stylesheets/theme.min.css') }}" data-skin="default">
    <link rel="stylesheet" href="{{ asset('assets/stylesheets/theme-dark.min.css') }}" data-skin="dark">
    <link rel="stylesheet" href="{{ asset('assets/stylesheets/custom.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" integrity="sha512-HzAa3eAiZyz6dVQb8sFZahuyN9KPh4Mx5BdfAa0AXf9DibKqMfcxwF94h1+NbXH1+X0N82djr1YG8G0dX4k5mQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script>
        var skin = localStorage.getItem('skin') || 'default';
        var disabledSkinStylesheet = document.querySelector('link[data-skin]:not([data-skin="' + skin + '"])');
        disabledSkinStylesheet.setAttribute('rel', '');
        disabledSkinStylesheet.setAttribute('disabled', true);
        document.documentElement.classList.add('loading');
    </script>
    @stack('styles')
</head>
<body>
<main class="auth auth-floated">
   @yield('content')
</main>

<script src="{{ asset('assets/vendor/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('assets/vendor/popper.js/umd/popper.min.js') }}"></script>
<script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.min.js') }}"></script>

<script src="{{ asset('assets/vendor/particles.js/particles.js') }}"></script>
<script>
    $(document).on('theme:init', () => {
        particlesJS.load('announcement', '{{ asset('assets/javascript/pages/particles.json') }}');
    })
</script>

<script src="{{ asset('assets/javascript/theme.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js" integrity="sha512-3z9R4lYFtrFgt5SuvrL18BMo/6IKxhpcJn/qdNqxabWWMwBLT1R59OAqxCLv7saEh1PQuIrn7Z0eR0kL6xC/Aw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
@include('components.toastr')
@stack('scripts')
</body>
</html>
