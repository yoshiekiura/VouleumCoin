@extends('layouts.admin')
@section('title', 'User List')
@section('content')

<div class="page-content">
    <div class="container">
        <div class="card content-area content-area-mh">
            <div class="card-innr">
                <div class="card-head has-aside">
                    <h4 class="card-title">User List</h4>
                    <div class="relative d-inline-block d-md-none">
                        <a href="#" class="btn btn-light-alt btn-xs btn-icon toggle-tigger"><em class="ti ti-more-alt"></em></a>
                        <div class="toggle-class dropdown-content dropdown-content-center-left pd-2x">
                            <div class="card-opt data-action-list">
                                <ul class="btn-grp btn-grp-block guttar-20px guttar-vr-10px">
                                    <li><a class="btn btn-auto btn-info btn-outline btn-sm" href="{{ route('admin.users.wallet.change') }}">Wallet Change Request</a></li>
                                    <li>
                                        <a href="#" class="btn btn-auto btn-sm btn-primary" data-toggle="modal" data-target="#addUser">
                                            <em class="fas fa-plus-circle"> </em>
                                            <span>Add <span class="d-none d-md-inline-block">User</span></span>
                                        </a>
                                    </li>
                                    <form action="{{ route('admin.ajax.users.delete') }}" method="POST">
                                        <li><a href="javascript:void(0)" title="Delete all unvarified users" data-toggle="tooltip" class="btn btn-danger btn-icon btn-outline btn-xs delete-unverified-user mr-md-2"> <em class="ti ti-trash"></em> </a></li>
                                    </form>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="card-opt data-action-list d-none d-md-inline-flex">
                        <ul class="btn-grp btn-grp-block guttar-20px">
                            <li><a class="btn btn-info btn-outline btn-sm" href="{{ route('admin.users.wallet.change') }}">Wallet Change Request</a></li>
                            <li>
                                <a href="#" class="btn btn-auto btn-sm btn-primary" data-toggle="modal" data-target="#addUser">
                                    <em class="fas fa-plus-circle"> </em><span>Add <span class="d-none d-md-inline-block">User</span></span>
                                </a>
                            </li>
                            <form action="{{ route('admin.ajax.users.delete') }}" method="POST">
                                <li><a href="javascript:void(0)" title="Delete all unvarified users" data-toggle="tooltip" class="btn btn-danger btn-icon btn-outline btn-xs delete-unverified-user mr-md-2"> <em class="ti ti-trash"></em> </a></li>
                            </form>
                        </ul>
                    </div>

                </div>
                <div class="gaps-1x"></div>
                <div class="row">
                    <div class="col-md-12 ">
                        <div class="float-right position-relative">
                            <a href="#" class="btn dt-filter-text btn-light-alt btn-xs btn-icon toggle-tigger"> <em class="ti ti-settings"></em> </a>
                            <div class="toggle-class toggle-datatable-filter dropdown-content dropdown-content-top-left dropdown-dt-filter-text text-left">
                                <ul class="pdt-1x pdb-1x">
                                    <li class="pd-1x pdl-2x pdr-2x">
                                        <input class="data-filter input-checkbox input-checkbox-sm" type="radio" name="filter" id="all" checked value="">
                                        <label for="all">All</label>
                                    </li>
                                    <li class="pd-1x pdl-2x pdr-2x">
                                        <input class="data-filter input-checkbox input-checkbox-sm" type="radio" name="filter" id="admin" value="_admin_">
                                        <label for="admin">Admin User</label>
                                    </li>
                                    <li class="pd-1x pdl-2x pdr-2x">
                                        <input class="data-filter input-checkbox input-checkbox-sm" type="radio" name="filter" value="_user_" id="user">
                                        <label for="user">Regular User</label>
                                    </li>
                                    <li class="pd-1x pdl-2x pdr-2x">
                                        <input class="data-filter input-checkbox input-checkbox-sm" type="radio" name="filter" value="suspend" id="suspend">
                                        <label for="suspend">Suspended</label>
                                    </li>
                                    <li class="pd-1x pdl-2x pdr-2x">
                                        <input class="data-filter input-checkbox input-checkbox-sm" type="radio" name="filter" value="active" id="active">
                                        <label for="active">Actived Only</label>
                                    </li>
                                    <li class="pd-1x pdl-2x pdr-2x">
                                        <input class="data-filter input-checkbox input-checkbox-sm" type="radio" name="filter" value="eVerified" id="eVerified">
                                        <label for="eVerified">Email Verified</label>
                                    </li>
                                    <li class="pd-1x pdl-2x pdr-2x">
                                        <input class="data-filter input-checkbox input-checkbox-sm" type="radio" name="filter" value="kVerified" id="kVerified">
                                        <label for="kVerified"> KYC Verified</label>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <table class="data-table dt-filter-init user-list">
                    <thead>
                        <tr class="data-item data-head">
                            <th class="data-col filter-data dt-user">User</th>
                            <th class="data-col dt-email">Email</th>
                            <th class="data-col dt-token">Tokens</th>
                            <th class="data-col dt-verify">Verified Status</th>
                            <th class="data-col dt-login">Last Login</th>
                            <th class="data-col dt-status">Status</th>
                            <th class="data-col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr class="data-item">
                            <td class="data-col dt-user">
                                <div class="d-flex align-items-center">
                                    <span class="d-none">_{{ $user->role }}_</span>
                                    <span class="d-none">{{$user->status}}</span> @if($user->email_verified_at != null)
                                    <span class="d-none">eVerified</span> @endif @if(isset($user->kyc_info->status)) @if($user->kyc_info->status == 'approved')
                                    <span class="d-none">kVerified</span> @endif @endif
                                    <div class="fake-class">
                                        <span class="lead user-name">{{ $user->name }}</span>
                                        <span class="sub user-id">
                                            UD{{ sprintf("%04s", $user->id) }}
                                            @if($user->role == 'admin') 
                                            <span class="badge badge-xs badge-dim badge-{{($user->type != 'demo')?'success':'danger'}}">ADMIN</span>
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <td class="data-col dt-email">
                                <span class="sub sub-s2 sub-email">{{ explode_user_for_demo($user->email, auth()->user()->type ) }}</span>
                            </td>
                            <td class="data-col dt-token">
                                <span class="lead lead-btoken">{{ number_format($user->tokenBalance) }}</span>
                            </td>
                            <td class="data-col dt-verify">
                                <ul class="data-vr-list">
                                    <li><div class="data-state data-state-sm data-state-{{ $user->email_verified_at !== null ? 'approved' : 'pending'}}"></div> Email</li>
                                    @php if(isset($user->kyc_info->status)){ $user->kyc_info->status = str_replace('rejected', 'canceled',$user->kyc_info->status); } @endphp @if($user->role != 'admin')
                                    <li><div class="data-state data-state-sm data-state-{{ !empty($user->kyc_info) ? $user->kyc_info->status : 'missing' }}"></div> KYC</li>
                                    @endif
                                </ul>
                            </td>
                            <td class="data-col dt-login">
                                <span class="sub sub-s2 sub-time">{{ _date($user->lastLogin) }}</span>
                            </td>
                            <td class="data-col dt-status">
                                <span class="dt-status-md badge badge-outline badge-md badge-{{ __status($user->status,'status') }}">{{ __status($user->status,'text') }}</span>
                                <span class="dt-status-sm badge badge-sq badge-outline badge-md badge-{{ __status($user->status,'status') }}">{{ substr(__status($user->status,'text'), 0, 1) }}</span>
                            </td>
                            <td class="data-col text-right">
                                <div class="relative d-inline-block">
                                    <a href="#" class="btn btn-light-alt btn-xs btn-icon toggle-tigger"><em class="ti ti-more-alt"></em></a>
                                    <div class="toggle-class dropdown-content dropdown-content-top-left">
                                        <ul class="dropdown-list more-menu-{{$user->id}}">
                                            <li><a href="{{ route('admin.users.view', [$user->id, 'view_user'] ) }}"><em class="ti ti-eye"></em> View Details</a></li>
                                            <li><a class="user-email-action" href="#EmailUser" data-uid="{{ $user->id }}" data-toggle="modal"><em class="far fa-envelope"></em>Send Email</a></li>
                                            @if($user->id != save_gmeta('site_super_admin')->value)
                                            <li><a class="user-form-action user-action" href="#" data-type="reset_pwd" data-uid="{{ $user->id }}" ><em class="fas fa-shield-alt"></em>Reset Pass</a></li>
                                            @endif

                                            @if(Auth::id() != $user->id && $user->id != save_gmeta('site_super_admin')->value) @if($user->status != 'suspend')
                                            <li><a href="#" data-uid="{{ $user->id }}" data-type="suspend_user" class="user-action front"><em class="fas fa-ban"></em>Suspend</a></li>

                                            @else
                                            <li><a href="#" id="front" data-uid="{{ $user->id }}" data-type="active_user" class="user-action"><em class="fas fa-ban"></em>Active</a></li>
                                            @endif @endif
                                        </ul>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <!-- .data-item -->
                        @endforeach
                    </tbody>
                </table>
            </div>
            <!-- .card-innr -->
        </div>
        <!-- .card -->
    </div>
    <!-- .container -->
</div>
<!-- .page-content -->

@endsection

@section('modals')

<div class="modal fade" id="addUser" tabindex="-1">
    <div class="modal-dialog modal-dialog-md modal-dialog-centered">
        <div class="modal-content">
            <a href="#" class="modal-close" data-dismiss="modal" aria-label="Close"><em class="ti ti-close"></em></a>
            <div class="popup-body popup-body-md">
                <h3 class="popup-title">Add New User</h3>
                <form action="{{ route('admin.ajax.users.add') }}" method="POST" class="adduser-form validate-modern" id="addUserForm" autocomplete="false">
                    @csrf
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="input-item input-with-label">
                                <label class="input-item-label">User Type</label>
                                <select name="role" class="select select-bordered select-block" required="required">
                                    <option value="user">
                                        Regular
                                    </option>
                                    <option value="admin">
                                        Admin
                                    </option>

                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="input-item input-with-label">
                        <label class="input-item-label">Full Name</label>
                        <div class="input-wrap">
                            <input name="name" class="input-bordered" minlength="4" required="required" type="text" placeholder="User Full Name">
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="input-item input-with-label">
                                <label class="input-item-label">Email Address</label>
                                <div class="input-wrap">
                                    <input class="input-bordered" required="required" name="email" type="email" placeholder="Email address">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="input-item input-with-label">
                                <label class="input-item-label">Password</label>
                                <div class="input-wrap">
                                    <input name="password" class="input-bordered" minlength="6" placeholder="Automatically generated if blank" type="password" autocomplete='new-password'>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="input-item">
                        <input checked class="input-checkbox input-checkbox-sm" name="email_req" id="send-email" type="checkbox">
                        <label for="send-email">Required Email Verification
                        </label>
                    </div>
                    <div class="gaps-1x"></div>
                    <button class="btn btn-md btn-primary" type="submit">Add User</button>
                </form>
            </div>
        </div>
        <!-- .modal-content -->
    </div>
    <!-- .modal-dialog -->
</div>

<div class="modal fade" id="EmailUser" tabindex="-1">
    <div class="modal-dialog modal-dialog-md modal-dialog-centered">
        <div class="modal-content">
            <a href="#" class="modal-close" data-dismiss="modal" aria-label="Close"><em class="ti ti-close"></em></a>
            <div class="popup-body popup-body-md">
                <h3 class="popup-title">Send Email to User </h3>
                <div class="msg-box"></div>
                <form class="validate-modern" id="emailToUser" action="{{ route('admin.ajax.users.email') }}" method="POST" autocomplete="off">
                    @csrf
                    <input type="hidden" name="user_id" id="user_id">
                    <div class="input-item input-with-label">
                        <label class="clear input-item-label">Email Subject</label>
                        <div class="input-wrap">
                            <input type="text" name="subject" class="input-bordered cls" placeholder="New Message">
                        </div>
                    </div>
                    <div class="input-item input-with-label">
                        <label class="clear input-item-label">Email Greeting</label>
                        <div class="input-wrap">
                            <input type="text" name="greeting" class="input-bordered cls" placeholder="Hello User">
                        </div>
                    </div>
                    <div class="input-item input-with-label">
                        <label class="clear input-item-label">Your Message</label>
                        <div class="input-wrap">
                            <textarea required="required" name="message" class="input-bordered cls input-textarea input-textarea-sm" type="text" placeholder="Write something..."></textarea>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Send Email</button>
                </form>
            </div>
        </div><!-- .modal-content -->
    </div><!-- .modal-dialog -->
</div>

@endsection