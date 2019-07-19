@extends('layouts.admin')
@section('title', 'Website Settings ')
@section('content')
<div class="page-content">
    <div class="container">
        <div class="row">
            <div class="main-content col-lg-12">
                <div class="content-area card">
                    <div class="card-innr">
                        <div class="card-head">
                            <h4 class="card-title">Website Settings</h4>
                        </div>
                        <ul class="nav nav-tabs nav-tabs-line" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-toggle="tab" href="#tokenDetails">Site Info</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#general">General Settings</a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#social">Social Links</a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#extra">API Settings</a>
                            </li>
                        </ul>{{-- .nav-tabs-line --}}

                        <div class="tab-content" id="ico-setting">
                            <div class="tab-pane fade show active " id="tokenDetails">

                                <form action="{{ route('admin.ajax.settings.update') }}" class="validate-modern" method="POST" id="update_settings">
                                    @csrf
                                    <input type="hidden" name="type" value="site_info">
                                    <div class="d-flex align-items-center justify-content-between pdb-1x">
                                        <h5 class="card-title-md text-primary">Website Information</h5>
                                    </div>
                                    <div class="row">
                                        <div class="col-xl-4 col-sm-6">
                                            <div class="input-item input-with-label">
                                                <label class="input-item-label">Site Name</label>
                                                <div class="input-wrap">
                                                    <input class="input-bordered" required="" type="text" data-validation="required" name="site_name" value="{{ get_setting('site_name') }}">
                                                </div>
                                                <span class="input-note">Enter name of Website. Display in Website and Email.</span>
                                            </div>
                                        </div>
                                        <div class="col-xl-4 col-sm-6">
                                            <div class="input-item input-with-label">
                                                <label class="input-item-label">Site Email</label>
                                                <div class="input-wrap">
                                                    <input class="input-bordered" required="" type="text" data-validation="required" name="site_email" value="{{ get_setting('site_email') }}">
                                                </div>
                                                <span class="input-note">Using for Contact and Sending Email.</span>
                                            </div>
                                        </div>
                                        <div class="col-xl-4 col-sm-6">
                                            <div class="input-item input-with-label">
                                                <label class="input-item-label">Site Copyright</label>
                                                <input class="input-bordered" type="text" name="site_copyright" value="{{  get_setting('site_copyright')  }}">
                                                <span class="input-note">Copyright text for site.</span>
                                            </div>
                                        </div>

                                        <div class="col-xl-4 col-sm-6">
                                            <div class="input-item input-with-label">
                                                <label class="input-item-label">Contact Address</label>
                                                <input class="input-bordered" type="text" data-validation="required" name="site_support_address" value="{{ get_setting('site_support_address') }}">
                                                <span class="input-note">Enter the support address.</span>
                                            </div>
                                        </div>
                                        <div class="col-xl-4 col-sm-6">
                                            <div class="input-item input-with-label">
                                                <label class="input-item-label">Contact Phone</label>
                                                <div class="input-wrap">
                                                    <input class="input-bordered" minlength="10" type="text" data-validation="required" name="site_support_phone" value="{{ get_setting('site_support_phone') }}">
                                                    <span class="input-note">Using for Contact and Support.</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xl-4 col-sm-6">
                                            <div class="input-item input-with-label">
                                                <label class="input-item-label">Support Email</label>
                                                <input class="input-bordered" type="text" name="site_support_email" value="{{  get_setting('site_support_email')  }}">
                                                <span class="input-note">Contact and Support Email.</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="gaps-1x"></div>
                                    <div class="d-flex">
                                        <button type="submit" class="btn btn-primary save-disabled" disabled><i class="ti ti-reload mr-2"></i>Update</button>
                                    </div>
                                </form>
                            </div>
                            <div class="tab-pane fade " id="general">
                                <form action="{{ route('admin.ajax.settings.update') }}" class="validate-modern" method="post" id="update_general_settings">
                                    @csrf
                                    <input type="hidden" name="type" value="general">
                                    <div class="d-flex align-items-center justify-content-between pdb-1x">
                                        <h5 class="card-title-md text-primary">Application Settings</h5>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-3 col-sm-6">
                                            <div class="input-item input-with-label">
                                                <label for="site-timezone" class="input-item-label">Time Zone</label>
                                                <select name="site_timezone" id="site-timezone" class="select select-block select-bordered">
                                                    @foreach($timezones as $timezone => $hrf)
                                                    <option value="{{ $timezone }}" {{ ($timezone == get_setting('site_timezone', 'UTC') ? 'selected' : '') }}>{{ $hrf }}</option>
                                                    @endforeach
                                                </select>
                                                <span class="input-note">Set application timezone.</span>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-sm-6">
                                            <div class="input-item input-with-label">
                                                <label class="input-item-label">Date Format</label>
                                                <select name="site_date_format" id="site_date_format" class="select select-block select-bordered">
                                                    <option {{ (get_setting('site_date_format') == 'd M, Y' ? 'selected' : '') }} value="d M, Y">{{ date('d M, Y') }}</option>
                                                    <option {{ (get_setting('site_date_format') == 'M d, Y' ? 'selected' : '') }} value="M d, Y">{{ date('M d, Y') }}</option>
                                                    <option {{ (get_setting('site_date_format') == 'd M, y' ? 'selected' : '') }} value="d M, y">{{ date('d M, y') }}</option>
                                                    <option {{ (get_setting('site_date_format') == 'm-d-Y' ? 'selected' : '') }} value="m-d-Y">{{ date('m-d-Y') }}</option>
                                                    <option {{ (get_setting('site_date_format') == 'd-m-Y' ? 'selected' : '') }} value="d-m-Y">{{ date('d-m-Y') }}</option>
                                                    <option {{ (get_setting('site_date_format') == 'Y-m-d' ? 'selected' : '') }} value="Y-m-d">{{ date('Y-m-d') }}</option>
                                                    <option {{ (get_setting('site_date_format') == 'm-d-y' ? 'selected' : '') }} value="m-d-y">{{ date('m-d-y') }}</option>
                                                    <option {{ (get_setting('site_date_format') == 'y-m-d' ? 'selected' : '') }} value="y-m-d">{{ date('y-m-d') }}</option>
                                                </select>
                                                <span class="input-note">Application date format</span>
                                            </div>
                                        </div>

                                        <div class="col-md-3 col-sm-6">
                                            <div class="input-item input-with-label">
                                                <label class="input-item-label">Time Format</label>
                                                <div class="input-wrap input-wrap-switch">
                                                    <select name="site_time_format" id="site_time_format" class="select select-block select-bordered">
                                                        <option {{ (get_setting('site_time_format') == 'h:i A' ? 'selected' : '') }} value="h:i A">11:12 AM</option>
                                                        <option {{ (get_setting('site_time_format') == 'H:i' ? 'selected' : '') }} value="H:i">15:30 (24 hr)</option>
                                                        <option {{ (get_setting('site_time_format') == 'H:i:s' ? 'selected' : '') }} value="H:i:s">15:30:25 (24 hr)</option>
                                                    </select>
                                                </div>
                                                <span class="input-note">Application time format</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="input-item input-with-label">
                                                <label class="input-item-label">Main Site URL</label>
                                                <div class="input-wrap">
                                                    <input class="input-bordered" type="url" name="main_website_url" value="{{  get_setting('main_website_url')  }}">
                                                </div>
                                                <span class="input-note">Set your main website url.</span>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-sm-6">
                                            <div class="input-item input-with-label">
                                                <label class="input-item-label">User Panel Theme</label>
                                                <div class="input-wrap">
                                                    <select name="user_dashboard_color" id="user_dashboard_color" class="select select-block select-bordered">
                                                        @foreach (config('icoapp.themes') as $theme =>$tm_name)
                                                        <option {{(get_setting('user_dashboard_color', 'style') == $theme)?'selected ':''}}value="{{ $theme }}">{{$tm_name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <span class="input-note">Color scheme for user.</span>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-sm-6">
                                            <div class="input-item input-with-label">
                                                <label class="input-item-label">Maintenance Mode</label>
                                                <div class="input-wrap input-wrap-switch">
                                                    <input class="input-switch" type="checkbox" name="site_maintenance" id="site_maintenance" {{ get_setting('site_maintenance') == 1 ? 'checked' : '' }}>
                                                    <label for="site_maintenance">Enable</label>
                                                </div>
                                                <span class="input-note">Make site offline.</span>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="input-item input-with-label">
                                                <label class="input-item-label">Maintenance Text</label>
                                                <div class="input-wrap">
                                                    <textarea class="input-bordered" name="site_maintenance_text" id="site_maintenance_text" cols="30" rows="1">{{ get_setting('site_maintenance_text') }}</textarea>
                                                </div>
                                                <div class="input-note">Admin Login on maintenance mode: <strong class="text-primary">{{ route('admin.login') }}</strong>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="gaps-1x"></div>
                                    <div class="d-flex">
                                        <button type="submit" class="btn btn-primary save-disabled" disabled><i class="ti ti-reload mr-2"></i>Update</button>
                                    </div>
                                </form>
                            </div>
                            <div class="tab-pane fade" id="social">
                                <form action="{{ route('admin.ajax.settings.update') }}" class="validate-modern" method="post" id="update_social_settings">
                                    @csrf
                                    <input type="hidden" name="type" value="social_links">
                                    <div class="d-flex align-items-center justify-content-between pdb-1x">
                                        <h5 class="card-title-md text-primary">Social Profile Links</h5>
                                    </div>
                                    <div class="row">
                                        @php
                                        $links = json_decode( get_setting('site_social_links') );
                                        @endphp
                                        <div class="col-xl-6 col-sm-12 col-md-6">

                                            <div class="input-item input-with-label">
                                                <label class="input-item-label">Facebook</label>
                                                <div class="input-wrap">
                                                    <input class="input-bordered" type="url" placeholder="https://www.facebook.com/user-name" data-validation="required" name="ss_fb" value="{{ $links ? $links->facebook : '' }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xl-6 col-sm-12 col-md-6">

                                            <div class="input-item input-with-label">
                                                <label class="input-item-label">Twitter</label>
                                                <div class="input-wrap">
                                                    <input class="input-bordered" type="url" placeholder="https://twitter.com/user-name" data-validation="required" name="ss_tt" value="{{ $links ? $links->twitter : '' }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xl-6 col-sm-12 col-md-6">

                                            <div class="input-item input-with-label">
                                                <label class="input-item-label">Linked In</label>
                                                <div class="input-wrap">
                                                    <input class="input-bordered" type="url" placeholder="https://www.linkedin.com/user-name" data-validation="required" name="ss_ln" value="{{ $links ? $links->linkedin : '' }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xl-6 col-sm-12 col-md-6">

                                            <div class="input-item input-with-label">
                                                <label class="input-item-label">Github</label>
                                                <div class="input-wrap">
                                                    <input class="input-bordered" type="url" placeholder="https://www.github.com/user-name" data-validation="required" name="ss_gh" value="{{ $links ? $links->github : '' }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="gaps-1x"></div>
                                    <div class="d-flex">
                                        <button type="submit" class="btn btn-primary save-disabled" disabled><i class="ti ti-reload mr-2"></i>Update</button>
                                    </div>
                                </form>
                            </div>

                            <div class="tab-pane fade" id="extra">
                                <form action="{{ route('admin.ajax.settings.update') }}" method="post" id="update_api_settings">
                                    @csrf
                                    <input type="hidden" name="type" value="api_credetial">
                                    <div class="d-flex align-items-center justify-content-between pdb-1x">
                                        <h5 class="card-title-md text-primary">API Credentials</h5>
                                    </div>
                                    <div class="row">
                                        <div class="col-xl-6 col-sm-12 col-md-6">
                                            <div class="input-item input-with-label">
                                                <label class="input-item-label">Facebook Client ID</label>
                                                <input class="input-bordered" type="text" name="api_fb_id" value="{{ get_setting('site_api_fb_id') }}">
                                            </div>
                                        </div>
                                        <div class="col-xl-6 col-sm-12 col-md-6">
                                            <div class="input-item input-with-label">
                                                <label class="input-item-label">Facebook Client Secret</label>
                                                <input class="input-bordered" type="password" name="api_fb_secret" value="{{ get_setting('site_api_fb_secret') }}">
                                            </div>
                                        </div>
                                        <div class="col-sm-12"><p class="text-info">In your App set this redirect URL: <strong>{{ config('services.facebook.redirect') }}</strong></p></div>
                                        <div class="gaps-1x"></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xl-6 col-sm-12 col-md-6">
                                            <div class="input-item input-with-label">
                                                <label class="input-item-label">Google Client ID</label>
                                                <input class="input-bordered" type="text" name="api_google_id" value="{{ get_setting('site_api_google_id') }}">
                                            </div>
                                        </div>
                                        <div class="col-xl-6 col-sm-12 col-md-6">
                                            <div class="input-item input-with-label">
                                                <label class="input-item-label">Google Client Secret</label>
                                                <input class="input-bordered" type="password" name="api_google_secret" value="{{ get_setting('site_api_google_secret') }}">
                                            </div>
                                        </div>
                                        <div class="col-sm-12"><p class="text-info">In your App set this redirect URL: <strong>{{ config('services.google.redirect') }}</strong></p></div>
                                        <div class="gaps-1x"></div>
                                    </div>
                                    <div class="gaps-1x"></div>
                                    <div class="d-flex">
                                        <button type="submit" class="btn btn-primary save-disabled" disabled><i class="ti ti-reload mr-2"></i>Update</button>
                                    </div>
                                </form>
                            </div>

                        </div>
                    </div>{{-- .card-innr --}}
                </div>{{-- .card --}}

            </div>{{-- .col --}}
        </div>{{-- .container --}}
    </div>{{-- .container --}}
</div>{{-- .page-content --}}
@endsection

@push('header')
<link rel="stylesheet" type="text/css" href="{{ asset('assets/plugins/trumbowyg/ui/trumbowyg.min.css') }}">
@endpush
@push('footer')
<script type="text/javascript" src="{{ asset('assets/plugins/trumbowyg/trumbowyg.min.js') }}"></script>
<script type="text/javascript">
    if ($('.editor').length > 0) {
        $('.editor').trumbowyg();
    }
</script>
@endpush