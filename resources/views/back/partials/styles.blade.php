<link href="https://fonts.googleapis.com/css?family=Fira+Sans:400,500,600" rel="stylesheet"><!-- End GOOGLE FONT -->
<!-- BEGIN PLUGINS STYLES -->
<link href="stacked-menu/dist/css/stacked-menu.min.css" rel="stylesheet">র
<link rel="stylesheet" href="{{ asset('assets/vendor/open-iconic/font/css/open-iconic-bootstrap.min.css')}}">
<link rel="stylesheet" href="{{ asset('assets/vendor/flatpickr/flatpickr.min.css')}}"><!-- END PLUGINS STYLES -->
<!-- BEGIN THEME STYLES -->
<link rel="stylesheet" href="{{ asset('assets/stylesheets/theme.min.css')}}" data-skin="default">
<link rel="stylesheet" href="{{ asset('assets/stylesheets/theme-dark.min.css')}}" data-skin="dark">
<link rel="stylesheet" href="{{ asset('assets/stylesheets/custom.css')}}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" integrity="sha512-HzAa3eAiZyz6dVQb8sFZahuyN9KPh4Mx5BdfAa0AXf9DibKqMfcxwF94h1+NbXH1+X0N82djr1YG8G0dX4k5mQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<script>
    var skin = localStorage.getItem('skin') || 'default';
    var disabledSkinStylesheet = document.querySelector('link[data-skin]:not([data-skin="' + skin + '"])');
    // Disable unused skin immediately
    disabledSkinStylesheet.setAttribute('rel', '');
    disabledSkinStylesheet.setAttribute('disabled', true);
    // add loading class to html immediately
    document.querySelector('html').classList.add('loading');
</script><!-- END THEME STYLES -->
@vite(['resources/css/app.css','resources/js/app.js'])
@kropifyStyles
@livewireStyles
@stack('styles')
