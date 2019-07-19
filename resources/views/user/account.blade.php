@extends('layouts.user')
@section('title', __('User Account'))
@php($has_sidebar = true)

@section('content')
@include('layouts.messages')
<div class="content-area card">
    <div class="card-innr">
        <div class="card-head">
            <h4 class="card-title">{{__('Profile Details')}}</h4>
        </div>
        <ul class="nav nav-tabs nav-tabs-line" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" data-toggle="tab" href="#personal-data">{{__('Personal Data')}}</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#settings">{{__('Settings')}}</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#password">{{__('Password')}}</a>
            </li>
        </ul>{{-- .nav-tabs-line --}}
        <div class="tab-content" id="profile-details">
            <div class="tab-pane fade show active" id="personal-data">
                <form class="validate-modern" action="{{ route('user.ajax.account.update') }}" method="POST" id="nio-user-personal" autocomplete="off">
                    @csrf
                    <input type="hidden" name="action_type" value="personal_data">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="input-item input-with-label">
                                <label for="full-name" class="input-item-label">{{__('Full Name')}}</label>
                                <div class="input-wrap">
                                    <input class="input-bordered" type="text" id="full-name" name="name" required="required" placeholder="Enter Full Name" minlength="4" value="{{ $user->name }}">
                                </div>
                            </div>{{-- .input-item --}}
                        </div>
                        <div class="col-md-6">
                            <div class="input-item input-with-label">
                                <label for="email-address" class="input-item-label">{{__('Email Address')}}</label>
                                <div class="input-wrap">
                                    <input class="input-bordered" type="text" id="email-address" name="email" required="required" placeholder="Enter Email Address" value="{{ $user->email }}" readonly>
                                </div>
                            </div>{{-- .input-item --}}
                        </div>
                        <div class="col-md-6">
                            <div class="input-item input-with-label">
                                <label for="mobile-number" class="input-item-label">{{__('Mobile Number')}}</label>
                                <div class="input-wrap">
                                    <input class="input-bordered" type="text" id="mobile-number" name="mobile" required="required" placeholder="Enter Mobile Number" value="{{ $user->mobile }}">
                                </div>
                            </div>{{-- .input-item --}}
                        </div>
                        <div class="col-md-6">
                            <div class="input-item input-with-label">
                                <label for="date-of-birth" class="input-item-label">{{__('Date of Birth')}}</label>
                                <div class="input-wrap">
                                    <input class="input-bordered date-picker-dob" type="text" id="date-of-birth" name="dateOfBirth" required="required" placeholder="m/d/Y" value="{{ ($user->dateOfBirth != NULL ? _date($user->dateOfBirth, 'm/d/Y') : '') }}">
                                </div>
                            </div>{{-- .input-item --}}
                        </div>{{-- .col --}}
                        <div class="col-md-6">
                            <div class="input-item input-with-label">
                                <label for="nationality" class="input-item-label">{{__('Nationality')}}</label>
                                <div class="input-wrap">
                                    <select class="select-bordered select-block" name="nationality" id="nationality">
                                        <option value="">{{__('Select Country')}}</option>
                                        @foreach($countries as $country)
                                        <option {{$user->nationality == $country ? 'selected ' : ''}}value="{{ $country }}">{{ $country }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>{{-- .input-item --}}
                        </div>{{-- .col --}}
                    </div>{{-- .row --}}
                    <div class="gaps-1x"></div>{{-- 10px gap --}}
                    <div class="d-sm-flex justify-content-between align-items-center">
                        <button type="submit" class="btn btn-primary">{{__('Update Profile')}}</button>
                        <div class="gaps-2x d-sm-none"></div>
                    </div>
                </form>{{-- form --}}

            </div>{{-- .tab-pane --}}
            <div class="tab-pane fade" id="settings">
                <form class="validate-modern" action="{{ route('user.ajax.account.update') }}" method="POST" id="nio-user-settings">
                    @csrf
                    <input type="hidden" name="action_type" value="account_setting">
                    <div class="pdb-1-5x">
                        <h5 class="card-title card-title-sm text-dark">{{__('Security Settings')}}</h5>
                    </div>
                    <div class="input-item">
                        <input name="save_activity" class="input-switch input-switch-sm" type="checkbox" {{ $userMeta->save_activity == 'TRUE' ? 'checked' : '' }} id="activitylog">
                        <label for="activitylog">{{__('Save my activities log')}}</label>
                    </div>
                    <div class="input-item">
                        <input class="input-switch input-switch-sm" type="checkbox" @if($userMeta->unusual == 1) checked="" @endif name="unusual" id="unuact">
                        <label for="unuact">{{__('Alert me by email for unusual activity.')}}</label>
                    </div>
                    <div class="gaps-1x"></div>
                    <div class="d-sm-flex justify-content-between align-items-center">
                        <button type="submit" class="btn btn-primary">{{__('Update')}}</button>
                        <div class="gaps-2x d-sm-none"></div>
                    </div>
                </form>
            </div>{{-- .tab-pane --}}

            <div class="tab-pane fade" id="password">
                <form class="validate-modern" action="{{ route('user.ajax.account.update') }}" method="POST" id="nio-user-password">
                    @csrf
                    <input type="hidden" name="action_type" value="pwd_change">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="input-item input-with-label">
                                <label for="old-pass" class="input-item-label">{{__('Old Password')}}</label>
                                <div class="input-wrap">
                                    <input class="input-bordered" type="password" name="old-password" id="old-pass" required="required">
                                </div>
                            </div>{{-- .input-item --}}
                        </div>{{-- .col --}}
                    </div>{{-- .row --}}
                    <div class="row">
                        <div class="col-md-6">
                            <div class="input-item input-with-label">
                                <label for="new-pass" class="input-item-label">{{__('New Password')}}</label>
                                <div class="input-wrap">
                                    <input class="input-bordered" id="new-pass" type="password" name="new-password" required="required" minlength="6">
                                </div>
                            </div>{{-- .input-item --}}
                        </div>{{-- .col --}}
                        <div class="col-md-6">
                            <div class="input-item input-with-label">
                                <label for="confirm-pass" class="input-item-label">{{__('Confirm New Password')}}</label>
                                <div class="input-wrap">
                                    <input id="confirm-pass" class="input-bordered" type="password" name="re-password" data-rule-equalTo="#new-pass" data-msg-equalTo="Password not match." required="required" minlength="6">
                                </div>
                            </div>{{-- .input-item --}}
                        </div>{{-- .col --}}
                    </div>{{-- .row --}}
                    <div class="note note-plane note-info pdb-1x">
                        <em class="fas fa-info-circle"></em>
                        <p>{{__('Password should be minimum 6 letter and include lower and uppercase letter.')}}</p>
                    </div>
                    <div class="note note-plane note-danger pdb-2x">
                        <em class="fas fa-info-circle"></em>
                        <p>{{__('Your password will only change after confirm your email.')}}</p>
                    </div>
                    <div class="gaps-1x"></div>{{-- 10px gap --}}
                    <div class="d-sm-flex justify-content-between align-items-center">
                        <button type="submit" class="btn btn-primary">{{__('Update')}}</button>

                        <div class="gaps-2x d-sm-none"></div>

                    </div>
                </form>
            </div>{{-- .tab-pane --}}
        </div>{{-- .tab-content --}}
    </div>{{-- .card-innr --}}
</div>{{-- .card --}}
@endsection
