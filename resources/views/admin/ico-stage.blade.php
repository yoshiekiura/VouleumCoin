@extends('layouts.admin')
@section('title', 'ICO Stage')

@section('content')
<div class="page-content">
    <div class="container">
        <div class="card content-area">
            <div class="card-innr">
                <div class="card-head d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Available ICO Stage</h4>
                </div>
                <div class="gaps-1-5x"></div>
                <div class="row guttar-vr-30px">
                    @forelse($stages as $stage)
                    <div class="col-xl-4 col-md-6">
                        <div class="stage-item stage-card {{(get_setting('actived_stage') == $stage->id)?'stage-item-actived':'' }}">
                            <div class="stage-head">
                                <div class="stage-title">
                                    <h6>Stage Name 
                                    @if((date('Y-m-d H:i:s') >= $stage->start_date) && (date('Y-m-d H:i:s') <= $stage->end_date) && (get_setting('actived_stage') == $stage->id) && ($stage->status != 'paused'))
                                    <span class="badge badge-success">Runing</span>
                                    @elseif((date('Y-m-d H:i:s') >= $stage->start_date && date('Y-m-d H:i:s') <= $stage->end_date) && ($stage->status == 'paused'))
                                    <span class="badge badge-purple">Paused</span>
                                    @elseif((date('Y-m-d H:i:s') >= $stage->start_date && date('Y-m-d H:i:s') <= $stage->end_date) && ($stage->status != 'paused'))
                                    <span class="badge badge-secondary">Inactive</span>
                                    @elseif($stage->start_date > date('Y-m-d H:i:s') && date('Y-m-d H:i:s') < $stage->end_date)
                                    <span class="badge badge-warning">Upcoming</span>
                                    @elseif(($stage->start_date > date('Y-m-d H:i:s')) && (date('Y-m-d H:i:s') < $stage->end_date))
                                    <span class="badge badge-info">Completed</span>
                                    @else
                                    <span class="badge badge-danger">Expired</span>
                                    @endif
                                    <h4>{{$stage->name}}</h4>
                                </div>

                                {{--Update--}}
                                {{--TokenLite v1.0.1 | Copyright Softnio. --}}
                                <div class="stage-action">
                                    <a href="#" class="toggle-tigger rotate"><em class="ti ti-more-alt"></em></a>
                                    <div class="toggle-class dropdown-content dropdown-content-top-left">
                                        <ul class="dropdown-list">
                                            <li><a href="{{route('admin.stages.edit',$stage->id)}}">Update Stage</a></li>

                                            @if(get_setting('actived_stage') != $stage->id)
                                            <form action="{{ route('admin.ajax.active.stage') }}" method="POST">
                                                @csrf
                                                <li><a href="javascript:void(0);" id="update_stage" data-type = "active_stage" data-id="{{$stage->id}}">Make as Active</a></li>
                                                <input class="input-bordered" type="hidden" name="actived_stage" value="{{ $stage->id }}">
                                            </form>
                                            @endif
                                            @if($stage->status != 'paused')
                                            <form action="{{ route('admin.ajax.pause.stage') }}" method="POST">
                                                @csrf
                                                <li><a href="javascript:void(0);" id="update_stage" data-type = "pause_stage" data-id="{{$stage->id}}">Sales Pause</a></li>
                                                <input class="input-bordered" type="hidden" name="stage_id" value="{{ $stage->id }}">
                                                <input class="input-bordered" type="hidden" name="type" value="paused">
                                            </form>
                                            @endif
                                            @if($stage->status == 'paused')
                                            <form action="{{ route('admin.ajax.pause.stage') }}" method="POST">
                                                @csrf
                                                <li><a href="javascript:void(0);" id="update_stage" data-type = "resume_stage" data-id="{{$stage->id}}">Sales Resume</a></li>
                                                <input class="input-bordered" type="hidden" name="stage_id" value="{{ $stage->id }}">
                                                <input class="input-bordered" type="hidden" name="type" value="active">
                                            </form>
                                            @endif
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="stage-info stage-info-status">
                                <div class="stage-info-graph">
                                    @if(!($stage->start_date > date('Y-m-d H:i:s') && date('Y-m-d H:i:s') < $stage->end_date))
                                    <div class="progress-pie progress-circle">
                                        <input class="knob" data-thickness=".125" data-width="100%" data-fgColor="#2b56f5" data-bgColor="#c8d2e5" value="{{round((($stage->sales_token * 100) / $stage->total_tokens), 0)}}">
                                        <div class="progress-txt"><span class="progress-amount">{{round((($stage->sales_token * 100) / $stage->total_tokens), 0)}}</span>% <span class="progress-status">Sold</span></div>
                                    </div>
                                    @else
                                    <div class="progress-soon progress-circle">
                                        <div class="progress-txt"><span class="progress-status">Coming Soon</span></div>
                                    </div>
                                    @endif
                                </div>
                                <div class="stage-info-txt">
                                    <h6>Token Issued</h6>
                                    <span class="stage-info-total h2">{{$stage->total_tokens}}</span>
                                    <div class="stage-info-count">Sold <span>{{$stage->sales_token}}</span> Tokens</div>
                                </div>
                            </div>
                            <div class="stage-info">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="stage-info-txt">
                                            <h6>Base Price</h6>
                                            <div class="h2 stage-info-number">{{$stage->base_price}}<small>USD</small></div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="stage-info-txt">
                                            <h6>Base Bonus</h6>
                                            <div class="h2 stage-info-number">{{ get_base_bonus($stage->id) }}<small>%</small></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="stage-date">
                                <div class="row">
                                    <div class="col-6">
                                        <h6>Start Date</h6>
                                        <h5>{{ _date($stage->start_date, get_setting('site_date_format')) }} <small>{{ _date($stage->start_date, get_setting('site_time_format')) }}</small></h5>
                                    </div>
                                    <div class="col-6">
                                        <h6>End Date</h6>
                                        <h5>{{ _date($stage->end_date, get_setting('site_date_format')) }} <small>{{ _date($stage->end_date, get_setting('site_time_format')) }}</small></h5>
                                    </div>
                                </div>
                            </div>
                        </div>{{-- .stage-card --}}
                    </div>{{-- .col --}}
                    @empty
                    <span>No Stage Found</span>
                    @endforelse

                </div>
                <div class="gaps-0-5x"></div>
            </div>
        </div>
    </div>{{-- .container --}}
</div>{{-- .page-content --}}
@endsection