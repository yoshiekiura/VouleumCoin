@extends('layouts.admin')
@section('title', 'Transaction Details')

@section('content')
<div class="page-content">
    <div class="container">
        <div class="card content-area">
            <div class="card-innr">
                <div class="card-head d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Transaction Details <em class="ti ti-angle-right fs-14"></em> <small class="tnx-id">{{ $trnx->tnx_id }}</small></h4>
                    <a href="{{ route('admin.transactions') }}" class="btn btn-sm btn-auto btn-primary d-sm-block d-none"><em class="fas fa-arrow-left mr-3"></em>Back</a>
                    <a href="{{ route('admin.transactions') }}" class="btn btn-icon btn-sm btn-primary d-sm-none"><em class="fas fa-arrow-left"></em></a>
                </div>
                <div class="gaps-1-5x"></div>
                <div class="data-details d-md-flex">
                    <div class="fake-class">
                        <span class="data-details-title">Tranx Date</span>
                        <span class="data-details-info">{{ _date($trnx->tnx_time) }}</span>
                    </div>
                    <div class="fake-class">
                        <span class="data-details-title">Tranx Status</span>
                        <span class="badge badge-{{ __status($trnx->status, 'status') }} ucap">{{ $trnx->status }}</span>
                    </div>
                    <div class="fake-class">
                        <span class="data-details-title">Tranaction by</span>

                        <span class="data-details-info"><strong>{{ transaction_by($trnx->added_by) }}</strong></span>
                    </div>
                    <div class="fake-class">
                        <span class="data-details-title">Tranx Approved Note</span>
                        @if($trnx->checked_by != NULL)
                        <span class="data-details-info">By <strong>{{ approved_by($trnx->checked_by) }}</strong> at {{ _date($trnx->checked_time) }}</span>
                        @else
                        <span class="data-details-info">Not Reviewed yet.</span>
                        @endif
                    </div>
                </div>
                <div class="gaps-3x"></div>
                <h6 class="card-sub-title">Transaction Info</h6>
                <ul class="data-details-list">
                    <li>
                        <div class="data-details-head">Transaction Type</div>
                        <div class="data-details-des"><strong>{{ ucfirst($trnx->tnx_type) }}</strong></div>
                    </li><!-- li -->
                    <li>
                        <div class="data-details-head">Payment Getway</div>
                        <div class="data-details-des"><strong>{{ ucfirst($trnx->payment_method) }}<small> - {{ $trnx->payment_method == 'manual' ? 'Offline Payment' : 'Geteway' }}</small></strong></div>
                    </li><!-- li -->
                    <li>
                        <div class="data-details-head">Deposit From</div>
                        <div class="data-details-des"><strong>{!! $trnx->wallet_address ? $trnx->wallet_address : '&nbsp;' !!}</strong></div>
                    </li><!-- li -->
                    <li>
                        <div class="data-details-head">Deposit To {{short_to_full($trnx->currency) !='' ? '('.short_to_full($trnx->currency).')' : '' }}</div>
                        <div class="data-details-des"><span>{!! $trnx->payment_to ? $trnx->payment_to : '&nbsp;' !!}</span></div>
                    </li>{{-- li --}}
                    <li>
                        <div class="data-details-head">Received Amount</div>
                        <div class="data-details-des">
                            <span><strong>{{ $trnx->receive_amount.' '.strtoupper($trnx->receive_currency) }}</strong> <em class="fas fa-info-circle" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="1 {{ token('symbol') }} = {{ $trnx->currency_rate.' '.strtoupper($trnx->currency) }}"></em></span>
                        </div>
                    </li><!-- li -->
                    <li>
                        <div class="data-details-head">Details</div>
                        <div class="data-details-des">{!! $trnx->details ? $trnx->details : '&nbsp;' !!}</div>
                    </li><!-- li -->
                </ul><!-- .data-details -->
                <div class="gaps-3x"></div>
                <h6 class="card-sub-title">Token Details</h6>
                <ul class="data-details-list">
                    <li>
                        <div class="data-details-head">Stage Name</div>
                        <div class="data-details-des"><strong>{{ $trnx->ico_stage->name }}</strong></div>
                    </li><!-- li -->
                    <li>
                        <div class="data-details-head">Contribution</div>
                        <div class="data-details-des">
                            <span><strong>{{ $trnx->amount.' '.strtoupper($trnx->currency) }}</strong> <em class="fas fa-info-circle" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="1 {{ token('symbol') }} = {{ $trnx->currency_rate.' '.strtoupper($trnx->currency) }}"></em></span>
                            <span><em class="fas fa-info-circle" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="1 {{ token('symbol') }} = {{ $trnx->base_currency_rate.' '.strtoupper($trnx->base_currency) }}"></em> {{ $trnx->base_amount }} {{ strtoupper($trnx->base_currency) }}</span>
                        </div>
                    </li><!-- li -->
                    <li>
                        <div class="data-details-head">Tokens Added To</div>
                        <div class="data-details-des"><strong>{{ set_id($trnx->user) }} <small>- {{ isset($trnx->tnxUser) ? explode_user_for_demo($trnx->tnxUser->email, auth()->user()->type) : '....' }}</small></strong></div>
                    </li><!-- li -->
                    <li>
                        <div class="data-details-head">Token (T)</div>
                        <div class="data-details-des">
                            <span>{{ number_format($trnx->tokens) }} {{ token_symbol() }}</span>
                        </div>
                    </li><!-- li -->
                    <li>
                        <div class="data-details-head">Bonus Tokens (B)</div>
                        <div class="data-details-des">
                            <span>{{ number_format($trnx->total_bonus) }} {{ token_symbol() }}</span>
                            <span>({{ $trnx->bonus_on_token }} + {{ $trnx->bonus_on_base }})</span>
                        </div>
                    </li><!-- li -->
                    <li>
                        <div class="data-details-head">Total Tokens</div>
                        <div class="data-details-des">
                            <span><strong>{{ number_format($trnx->total_tokens) }} {{ token_symbol() }}</strong></span>
                            <span>(T+B)</span>
                        </div>
                    </li><!-- li -->
                </ul><!-- .data-details -->
            </div>
        </div><!-- .card -->
    </div><!-- .container -->
</div><!-- .page-content -->
@endsection

