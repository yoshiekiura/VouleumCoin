@extends('layouts.admin')
@section('title', 'ICO Setting')

@section('content')
<div class="page-content">
    <div class="container">
        @include('layouts.messages')
        <div class="row">
            <div class="main-content col-lg-12">
                <div class="content-area card">
                    <div class="card-innr">
                        <div class="card-head">
                            <h4 class="card-title">{{__('Profile Details')}}</h4>
                        </div>
                        <div class="nav nav-tabs nav-tabs-line">
                            <ul class="nav mb-0" id="myTab" role="tablist">
                                <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#accountInfo">My Profile</a></li>
                                <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#secutity">Secutity Settings</a></li>
                                <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#changePassword">Change Password</a></li>
                                <li class="nav-item"><a class="nav-link" href="{{ route('admin.profile.activity') }}">Activity</a></li>
                            </ul>
                        </div>
                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade show active" id="accountInfo">
                                <div class="w-xl-16x">
                                    <form action="{{ route('admin.ajax.profile.update') }}" method="POST" id="user_account_update">
                                        @csrf
                                        <input type="hidden" name="action_type" value="personal_data">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="input-item input-with-label">
                                                    <label for="full-name" class="input-item-label ucap">Full Name</label>
                                                    <input class="input-bordered" type="text" value="{{ $user->name }}" placeholder="Full name" id="full-name" name="name">
                                                </div><!-- .input-item -->
                                            </div>
                                            <div class="col-md-6">
                                                <div class="input-item input-with-label">
                                                    <label for="email-address" class="input-item-label ucap">Email Address</label>
                                                    <input class="input-bordered" type="text" value="{{ $user->email }}" placeholder="Email Address" id="email-address" name="email" disabled="">
                                                </div><!-- .input-item -->
                                            </div>
                                            <div class="col-md-6">
                                                <div class="input-item input-with-label">
                                                    <label for="mobile-number" class="input-item-label ucap">Mobile Number</label>
                                                    <input class="input-bordered" type="text" value="{{ $user->mobile }}" placeholder="Mobile Number" id="mobile-number" name="mobile">
                                                </div><!-- .input-item -->
                                            </div>
                                        </div><!-- .row -->
                                        <div class="gaps-1x"></div>
                                        <div class="d-sm-flex justify-content-between align-items-center">
                                            <button type="submit" class="btn btn-primary">Update</button>
                                        </div>
                                    </form>
                                </div>
                            </div> <!-- .tab-pane -->
                            <div class="tab-pane fade" id="secutity">
                                <div class="w-xl-16x">
                                    <h6 class="card-title card-title-sm text-dark">General Security Options</h6>
                                    <div class="gaps-2x"></div>
                                    <form action="{{ route('admin.ajax.profile.update') }}" method="POST" id="security">
                                        @csrf
                                        <input type="hidden" name="action_type" value="security">
                                        <ul class="btn-grp flex-column flex-wrap align-items-start w-100">
                                            <li class="d-flex align-items-center justify-content-between w-100">
                                                <input name="save_activity" class="input-switch input-switch-sm" type="checkbox" {{ $userMeta->save_activity == 'TRUE' ? 'checked' : '' }} id="activitylog"><label for="activitylog">Save my Activities Log.</label>
                                            </li>

                                            <li class="d-flex align-items-center justify-content-between w-100">
                                                <input name="unusual" class="input-switch  input-switch-sm" {{ $userMeta->unusual == 1 ? 'checked' : '' }} type="checkbox" id="unuact"><label for="unuact">Alert me by email for unusual activity</label>
                                            </li>
                                        </ul>
                                        <div class="gaps-3x"></div>
                                        <div class="pdb-1-5x">
                                        <h5 class="card-title card-title-sm text-dark">Manage Notification</h5>    
                                    </div>
                                    <div class="input-item">
                                        <input type="checkbox" name="notify_admin" class="input-switch input-switch-sm" id="notify_admin" {{ $userMeta->notify_admin == 1 ? 'checked' : '' }}>
                                        <label for="notify_admin">Get Notifications for all purchase</label>
                                    </div>
                                        <div class="d-sm-flex justify-content-between align-items-center">
                                            <button type="submit" class="btn btn-primary">Update</button>
                                            <div class="gaps-2x d-sm-none"></div>
                                        </div>
                                    </form>
                                </div>
                            </div> <!-- .tab-pane -->
                            <div class="tab-pane fade" id="changePassword">
                                <div class="w-lg-12x">
                                    <form action="{{ route('admin.ajax.profile.update') }}" method="POST" id="pwd_change">
                                        @csrf
                                        <input type="hidden" name="action_type" value="pwd_change">
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="input-item input-with-label">
                                                    <label for="swalllet" class="input-item-label">Old Password</label>
                                                    <input class="input-bordered" placeholder="Old Password" type="password" name="old-password" value="" required="required">
                                                </div><!-- .input-item -->
                                            </div><!-- .col -->
                                        </div><!-- .row -->
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="input-item input-with-label">
                                                    <label for="new-password" class="input-item-label">New Password</label>
                                                    <input class="input-bordered" id="new-password" type="password" name="new-password" placeholder="New password" required="required" minlength="6">
                                                </div><!-- .input-item -->
                                            </div><!-- .col -->
                                            <div class="col-lg-6">
                                                <div class="input-item input-with-label">
                                                    <label for="date-of-birth" class="input-item-label">Confirm New Password</label>
                                                    <input class="input-bordered" type="password" name="re-password" data-rule-equalTo="#new-password" placeholder="Confirm new password" data-msg-equalTo="Password didn't match." required="required" minlength="6">
                                                </div><!-- .input-item -->
                                            </div><!-- .col -->
                                        </div><!-- .row -->
                                        <div class="note note-plane note-info">
                                            <em class="fas fa-info-circle"></em>
                                            <p>Password should be minmum 6 character long.</p>
                                        </div>
                                        <div class="note note-plane note-danger pdb-2x">
                                            <em class="fas fa-info-circle"></em>
                                            <p>Your password will update after confirm from your email.</p>
                                        </div>
                                        <div class="gaps-1x"></div><!-- 10px gap -->
                                        <div class="d-sm-flex justify-content-between align-items-center">
                                            <button type="submit" class="btn btn-primary">Update</button>
                                            <div class="gaps-2x d-sm-none"></div>
                                        </div>
                                    </form><!-- form -->
                                </div>
                            </div>
                        </div><!-- .tab-content -->
                    </div><!-- .card-innr -->
                </div><!-- .card -->
            </div><!-- .col -->
        </div><!-- .container -->
    </div><!-- .container -->
</div><!-- .page-content -->
@endsection
