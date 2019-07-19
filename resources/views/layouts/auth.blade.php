<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="js">
<head>
    <meta charset="utf-8">
    <meta name="apps" content="{{ app_info() }}">
    <meta name="author" content="Softnio">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>@yield('title') | {{ (site_info('name')) ? site_info('name') : app_info() }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.png') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/vendor.bundle.css').css_js_ver() }}">
    <link rel="stylesheet" href="{{ asset('assets/css/'.get_setting('user_dashboard_color','style').'.css').css_js_ver() }}">
    <link rel="stylesheet" href="{{ asset('assets/css/custom.css').css_js_ver() }}">
    @stack('header')
</head>
@php 
$header_logo = '<div class="page-ath-header"><a href="'.url('/').'" class="page-ath-logo"><img class="page-ath-logo-img" src="'. site_logo('default', 'dark') .'" srcset="'. site_logo('retina', 'dark') .'" alt="'. site_info() .'"></a></div>';
@endphp
<body class="page-ath page-ath-modern theme-modern">

    <div class="page-ath-wrap flex-row-reverse">
        <div class="page-ath-content">
            {!! $header_logo !!}
            @yield('content')
            <div class="page-ath-footer">
                {!! UserPanel::footer_links(['lang' => true], array('vers' => 'copyright')) !!}
            </div>
        </div>
        <div class="page-ath-gfx">
            <div class="w-100 d-flex justify-content-center">
                <div class="col-md-8 col-xl-5">
                    {{-- <img src="{{ asset('assets/images/intro.png') }}" alt=""> --}}
                </div>
            </div>
        </div>
    </div>

    <script>
        var base_url = "{{ url('/') }}",
        csrf_token = document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        layouts_style = "modern";
    </script>
    <script src="{{ asset('assets/js/jquery.bundle.js').css_js_ver() }}"></script>
    <script src="{{ asset('assets/js/script.js').css_js_ver() }}"></script>
    <script type="text/javascript">
        jQuery(function(){
            var $frv = jQuery('.validate');
            if($frv.length > 0){ $frv.validate({ errorClass: "input-bordered-error error" }); }
        });
    </script>
    @stack('footer')
</body>
</html>