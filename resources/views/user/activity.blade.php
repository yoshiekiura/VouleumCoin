@extends('layouts.user')
@section('title', __('User Activity'))

@section('content')
    <div class="page-content">
        <div class="container">
            <div class="row">
                <div class="main-content col-lg-8">
                    <div class="content-area card">
                        <div class="card-innr">
                            @include('layouts.messages')
                            <div class="card-head d-flex justify-content-between">
                                <h4 class="card-title card-title-md">{{__('Account Activities Log')}}</h4>
                                <div class="float-right">
                                    <input type="hidden" id="activity_action" value="{{ route('user.ajax.account.activity.delete') }}">
                                    <a href="javascript:void(0)" class="btn btn-auto btn-primary btn-xs activity-delete" data-id="all">{{__('Clear All')}}</a>
                                </div>
                            </div>
                            <div class="card-text">
                                <p>{{__('Here is your recent activities. You can clear this log as well as disable the feature from profile settings tabs.')}} </p>
                            </div>
                            <div class="gaps-1x"></div>
                            <table class="data-table dt-init activity-table">
                                <thead>
                                <tr>
                                    <th class="activity-time"><span>{{__('Date')}}</span></th>
                                    <th class="activity-device"><span>{{__('Device')}}</span></th>
                                    <th class="activity-browser"><span>{{__('Browser')}}</span></th>
                                    <th class="activity-ip"><span>{{__('IP')}}</span></th>
                                    <th>&nbsp;</th>
                                </tr>
                                </thead>
                                <tbody id="activity-log">
                                @forelse($activities as $activity)
                                    @php
                                        $browser = explode('/', $activity->browser);
                                        $device = explode('/', $activity->device);
                                        $ip = ($activity->ip == '::1' || $activity->ip == '127.0.0.1') ? 'localhost' : $activity->ip ;
                                    @endphp
                                    <tr class="data-item activity-{{ $activity->id }}">
                                        <td class="data-col">{{ _date($activity->created_at) }}</td>
                                        <td class="data-col d-none d-sm-table-cell">{{ end($device) }}</td>
                                        <td class="data-col">{{ $browser[0] }}</td>
                                        <td class="data-col">{{ $ip }}</td>
                                        <td><a href="javascript:void(0)" class="fs-20 activity-delete" data-id="{{ $activity->id }}" title="delete activity"><em class="ti-trash"></em></a></td>
                                    </tr>
                                @empty

                                @endforelse
                                </tbody>
                            </table>

                        </div>{{-- .card-innr --}}
                    </div><!-- .card -->
                </div><!-- .col -->
                <div class="aside sidebar-right col-lg-4">
                    <div class="account-info card">
                        <div class="card-innr">
                            {!! UserPanel::user_account_status() !!}
                            <div class="gaps-2-5x"></div>
                            {!! UserPanel::user_account_wallet() !!}
                        </div>
                    </div>
                    {!! UserPanel::user_kyc_info('') !!}
                </div><!-- .col -->
            </div><!-- .container -->
        </div><!-- .container -->
    </div><!-- .page-content -->

@endsection
