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
    @stack('header')
</head>

<body class="user-dashboard page-user theme-modern">
    <div class="topbar-wrap">
        <div class="topbar is-sticky">
            <div class="container">
                <div class="d-flex justify-content-center">
                    <a class="topbar-logo" href="{{url('/')}}">
                     <img height="40" src="{{ site_logo('default', 'light') }}" srcset="{{ site_logo('retina', 'light' ) }}" alt="{{ site_info() }}">
                 </a>
             </div>
         </div><!-- .container -->
     </div><!-- .topbar -->
 </div><!-- .topbar-wrap -->

 <div class="page-content">
    <div class="container">
        @yield('content') 
    </div>{{-- .container --}}
</div>{{-- .page-content --}}

<div class="footer-bar">
    <div class="container">
        <div class="row align-items-center justify-content-center">
            <div class="col-md-8">
                {!! UserPanel::footer_links() !!}
            </div>
            <div class="col-md-4 mt-2 mt-sm-0">
                <div class="d-flex justify-content-between justify-content-md-end align-items-center guttar-25px pdt-0-5x pdb-0-5x">
                    <div class="copyright-text">&copy; {{ date('Y') }} {{ site_info('name') }}. {{ get_setting('site_copyright') }}</div>
                    @if(config('icoapp.show_languages_switcher'))
                    <div class="lang-switch relative">
                        <a href="javascript:void(0)" class="lang-switch-btn toggle-tigger">{{ Cookie::get('app_language', 'EN') }} <em class="ti ti-angle-up"></em></a>
                        <div class="toggle-class dropdown-content dropdown-content-up">
                            <ul class="lang-list">
                                @foreach(config('icoapp.supported_languages') as $lang)
                                <li><a href="{{ route('language', ['lang' => $lang]) }}">{{ strtoupper($lang) }}</a></li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>{{-- .container --}}
</div>{{-- .footer-bar --}}

@yield('modals')
<div id="ajax-modal"></div>

<script src="{{ asset('assets/js/jquery.bundle.js').css_js_ver() }}"></script>
<script src="{{ asset('assets/js/script.js').css_js_ver() }}"></script>

@stack('footer')

<script type="text/javascript">
    var base_url = "{{ url('/') }}",
    csrf_token = document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
    user_wallet_address = "{{ route('user.ajax.account.wallet') }}",
    layouts_style = "modern";

    @if (session('resent'))
    show_toast("success","{{ __('A fresh verification link has been sent to your email address.') }}");
    @endif

</script>
</body>
</html>