<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="js">
<head>
    <meta charset="utf-8">
    <meta name="apps" content="{{ app_info() }}">
    <meta name="author" content="chkernit">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>@yield('title') | {{ (site_info('name')) ? site_info('name') : app_info() }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.png') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/vendor.bundle.css').css_js_ver() }}">
    <link rel="stylesheet" href="{{ asset('assets/css/'.get_setting('user_dashboard_color','style').'.css').css_js_ver() }}">
    <link rel="stylesheet" href="{{ asset('assets/css/custom.css').css_js_ver() }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.7.2/animate.min.css">

    @stack('header')        
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</head>
<body class="user-dashboard page-user theme-modern">
    <div class="topbar-wrap">
        <div class="topbar is-sticky">
            <div class="container">
                <div class="d-flex justify-content-between align-items-center">
                    <ul class="topbar-nav d-lg-none">
                        <li class="topbar-nav-item relative">
                            <a class="toggle-nav" href="#">
                                <div class="toggle-icon">
                                    <span class="toggle-line"></span>
                                    <span class="toggle-line"></span>
                                    <span class="toggle-line"></span>
                                    <span class="toggle-line"></span>
                                </div>
                            </a>
                        </li>{{-- .topbar-nav-item --}}
                    </ul>{{-- .topbar-nav --}}

                    <a class="topbar-logo" href="{{ url('/') }}">
                        <img height="40" src="{{ site_logo('default', 'light') }}" srcset="{{ site_logo('retina', 'light' ) }}" alt="{{ site_info() }}">
                    </a>
                    <ul class="topbar-nav">
                        <li class="topbar-nav-item relative">
                            <span class="user-welcome d-none d-lg-inline-block">{{__('Welcome!')}} {{ auth()->user()->name }}</span>
                            <a class="toggle-tigger user-thumb" href="#"><em class="ti ti-user"></em></a>
                            <div class="toggle-class dropdown-content dropdown-content-right dropdown-arrow-right user-dropdown">
                                {!! UserPanel::user_balance(auth()->user()) !!}
                                {!! UserPanel::user_menu_links() !!}
                                {!! UserPanel::user_logout_link() !!}
                            </div>
                        </li>{{-- .topbar-nav-item --}}
                    </ul>{{-- .topbar-nav --}}
                </div>
            </div>{{-- .container --}}
        </div>{{-- .topbar --}}

        <div class="navbar">
            <div class="container">
                <div class="navbar-innr">
                    <ul class="navbar-menu" id="main-nav">
                        <li><a href="{{ route('user.home') }}"><em class="ikon ikon-dashboard"></em> {{__('Dashboard')}}</a></li>
                        <li><a href="{{ route('user.token') }}"><em class="ikon ikon-coins"></em> {{__('Buy Token')}}</a></li>
                        @if(get_page('distribution', 'status') == 'active')
                        <li><a href="{{ route('public.pages', 'distribution') }}"><em class="ikon ikon-distribution"></em> {{ get_page('distribution', 'title') }}</a> </li>
                        @endif
                        <li><a href="{{ route('user.transactions') }}"><em class="ikon ikon-transactions"></em> {{__('Transactions')}}</a></li>
                        <li><a href="{{ route('user.account') }}"><em class="ikon ikon-user"></em> {{__('Profile')}}</a></li>
                       
                           <li><a href="{{ route('user.referrals') }}"><span class="glyphicon glyphicon-bullhorn"></span>&nbsp; {{__('referrals')}}</a></li>
                      
                        
                        @if(get_setting('main_website_url') != NULL)
                        <li><a href="{{get_setting('main_website_url')}}" target="_blank"><em class="ikon ikon-home-link"></em> {{__('Main Site')}}</a></li>
                        @endif
                    </ul>
                    <ul class="navbar-btns">
                        @if(isset(Auth::user()->kyc_info->status) && Auth::user()->kyc_info->status == 'approved')
                        <li><span class="badge badge-outline badge-success badge-lg"><em class="text-success ti ti-files mgr-1x"></em><span class="text-success">{{__('KYC Approved')}}</span></span></li>
                        @else
                        <li><a href="{{ route('user.kyc') }}" class="btn btn-sm btn-outline btn-light"><em class="text-primary ti ti-files"></em><span>{{__('KYC Application')}}</span></a></li>
                        @endif
                    </ul>
                </div>{{-- .navbar-innr --}}
            </div>{{-- .container --}}
        </div>{{-- .navbar --}}
    </div>{{-- .topbar-wrap --}}

    <div class="page-content">
        <div class="container">
            <div class="row">
                @php
                $has_sidebar = isset($has_sidebar) ? $has_sidebar : false;
                $col_side_cls = ($has_sidebar) ? 'col-lg-4' : 'col-lg-12';
                $col_cont_cls = ($has_sidebar) ? 'col-lg-8' : 'col-lg-12';
                $col_cont_cls2 = isset($content_class) ? css_class($content_class) : null;
                $col_side_cls2 = isset($aside_class) ? css_class($aside_class) : null;
                @endphp

                <div class="main-content {{ empty($col_cont_cls2) ? $col_cont_cls : $col_cont_cls2 }}">
                    @yield('content')
                </div>

                @if ($has_sidebar==true)
                <div class="aside sidebar-right {{ empty($col_side_cls2) ? $col_side_cls : $col_side_cls2 }}">
                    @if(!has_wallet())
                    <div class="d-lg-none">
                        {!! UserPanel::add_wallet_alert() !!}
                    </div>
                    @endif
                    <div class="account-info card">
                        <div class="card-innr">
                            {!! UserPanel::user_account_status() !!}
                            <div class="gaps-2-5x"></div>
                            {!! UserPanel::user_account_wallet() !!}
                        </div>
                    </div>
                    {!! UserPanel::user_kyc_info('') !!}
                </div>{{-- .col --}}
                @else
                    @stack('sidebar')
                @endif

            </div>
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
        </div>{{-- .container --}}
    </div>{{-- .footer-bar --}}
    @yield('modals')
    <div id="ajax-modal"></div>
    <div class="page-overlay">
        <div class="spinner">
            <span class="sp sp1"></span><span class="sp sp2"></span><span class="sp sp3"></span>
        </div>
    </div>
    <script>
        var base_url = "{{ url('/') }}",
        csrf_token = document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        user_wallet_address = "{{ route('user.ajax.account.wallet') }}";
    </script>
    <script src="{{ asset('assets/js/jquery.bundle.js').css_js_ver() }}"></script>
    <script src="{{ asset('assets/js/script.js').css_js_ver() }}"></script>
    <script src="{{ asset('assets/js/app.js').css_js_ver() }}"></script>
    @stack('footer')
    <script type="text/javascript">
        @if (session('resent'))
        show_toast("success","{{ __('A fresh verification link has been sent to your email address.') }}");
        @endif
    </script>
</body>
</html>