@extends('layouts.user')
@section('title', __('Purchase Token'))

@section('content')
@php
$has_sidebar = false;
$content_class = 'col-lg-8';

$current_date = time();
$upcoming = is_upcoming();

$_b = 0; 
$base_currency = get_setting('site_base_currency');
$bc = base_currency();
$default_method = token_method();
$symbol = token_symbol();
$method = strtolower($default_method);
$min_token = ($minimum) ? $minimum : active_stage()->min_purchase;

$is_method = is_method_valid();

$sl_01 = ($is_method) ? '01 ' : '';
$sl_02 = ($sl_01) ? '02 ' : '';
$sl_03 = ($sl_02) ? '03 ' : '';


$exc_rate = (!empty($currencies)) ? json_encode($currencies) : '{}';
$token_price = (!empty($price)) ? json_encode($price) : '{}';
$amount_bonus = (!empty($bonus_amount)) ? json_encode($bonus_amount) : '{1 : 0}';
$decimal_min = (token('decimal_min')) ? token('decimal_min') : 0;
$decimal_max = (token('decimal_max')) ? token('decimal_max') : 0;

@endphp

@include('layouts.messages')
@if ($upcoming)
<div class="alert alert-dismissible fade show alert-info" role="alert">
    <a href="javascript:void(0)" class="close" data-dismiss="alert" aria-label="close">&nbsp;</a>
    {{ __('Sales Start at') }} - {{ _date(active_stage()->start_date) }}
</div>
@endif
@if(!has_wallet())
<div class="d-lg-none">
    {!! UserPanel::add_wallet_alert() !!}
</div>
@endif
<div class="content-area card">
    <div class="card-innr">
        <form action="javascript:void(0)" method="post" class="token-purchase">
            <div class="card-head">
                <h4 class="card-title">
                {{ __('Choose currency and calculate :TOKEN token price', ['token' => $symbol]) }}
                </h4>
            </div>
            <div class="card-text">
                <p>{{ __('You can buy our :TOKEN tokens using bellow currency to become part of our project.', ['token'=>$symbol]) }}</p>
            </div>

            @if($is_method==true)
            <div class="token-currency-choose payment-list">
                <div class="row guttar-15px">
                    @foreach($pm_currency as $gt => $full)
                    @if(token('purchase_'.$gt) == 1 || $method==$gt)
                    <div class="col-6">
                        <div class="payment-item pay-option">
                            <input class="pay-option-check pay-method" type="radio" id="pay{{ $gt }}" name="paymethod" value="{{ $gt }}" {{ $default_method == strtoupper($gt) ? 'checked' : '' }}>
                            <label class="pay-option-label" for="pay{{ $gt }}">
                                <span class="pay-title">
                                    @if($gt == 'eth' || $gt == 'ltc' || $gt == 'btc')
                                    <em class="pay-icon cf cf-{{ $gt }} pay-icon-{{ $gt }}"></em>
                                    @elseif($gt=='usd')
                                    <em class="pay-icon pay-icon-usd fas fa-dollar-sign"></em>
                                    @elseif($gt=='eur')
                                    <em class="pay-icon pay-icon-eur fas fa-euro-sign"></em>
                                    @elseif($gt=='gbp')
                                    <em class="pay-icon pay-icon-gbp fas fa-pound-sign"></em>
                                    @else
                                    <em class="pay-icon pay-icon-{{base_currency()}} fas fa-credit-card"></em>
                                    @endif
                                    <span class="pay-cur">{{ strtoupper($gt) }}</span>
                                </span>
                                @if(token('price_show')==1 && get_setting('pmc_active_'.$gt) == 1 )
                                @isset(token_calc(1, 'price')->$gt)
                                <span class="pay-amount">{{ _format( ['number'=>token_calc(1, 'price')->$gt] ) }} {{ strtoupper($gt) }}</span>
                                @endisset
                                @endif
                            </label>
                        </div>       
                    </div>
                    @endif
                    @endforeach
                </div>
            </div>
            @else 
            <div class="token-currency-default payment-item-default">
                <input class="pay-method" type="hidden" id="pay{{ base_currency() }}" name="paymethod" value="{{ base_currency() }}" checked>
            </div>
            @endif
            
            <div class="card-head">
                <h4 class="card-title">{{ __('Amount of contribute') }}</h4>
            </div>
            <div class="card-text">
                <p>{{ __('Enter your amount, you would like to contribute and calculate the amount of token you will received. The calculator helps to convert required currency to tokens.') }}</p>
            </div>
            @php
            $calc = token('calculate');
            $input_hidden_token = ($calc=='token') ? '<input class="pay-amount" type="hidden" id="pay-amount" value="">' : '';
            $input_hidden_amount = ($calc=='pay') ? '<input class="token-number" type="hidden" id="token-number" value="">' : ''; 

            $input_token_purchase = '<div class="token-pay-amount payment-get">'.$input_hidden_token.'<input class="input-bordered input-with-hint token-number" type="text" id="token-number" value="" min="'.$min_token.'" max="'.$stage->max_purchase.'"><div class="token-pay-currency"><span class="input-hint input-hint-sap payment-get-cur payment-cal-cur ucap">'.$symbol.'</span></div></div>';
            $input_pay_amount = '<div class="token-pay-amount payment-from">'.$input_hidden_amount.'<input class="input-bordered input-with-hint pay-amount" type="text" id="pay-amount" value=""><div class="token-pay-currency"><span class="input-hint input-hint-sap payment-from-cur payment-cal-cur pay-currency ucap">'.$method.'</span></div></div>';
            $input_token_purchase_num = '<div class="token-received"><div class="token-eq-sign">=</div><div class="token-received-amount"><h5 class="token-amount token-number-u">0</h5><div class="token-symbol">'.$symbol.'</div></div></div>';
            $input_pay_amount_num = '<div class="token-received token-received-alt"><div class="token-eq-sign">=</div><div class="token-received-amount"><h5 class="token-amount pay-amount-u">0</h5><div class="token-symbol pay-currency ucap">'.$method.'</div></div></div>';
            $input_sep = '<div class="token-eq-sign"><em class="fas fa-exchange-alt"></em></div>';
            @endphp
            <div class="token-contribute">
                <div class="token-calc">{!! $input_token_purchase.$input_pay_amount_num !!}</div>
            
                <div class="token-calc-note note note-plane token-note">
                    <div class="note-box">
                        <span class="note-icon">
                            <em class="fas fa-info-circle"></em>
                        </span>
                        <span class="note-text text-light"><strong class="min-amount">{{ token_calc($min_token, 'price')->$method }}</strong> <span class="pay-currency ucap">{{ $method }}</span> (<strong class="min-token">{{ $min_token }}</strong>
                        <span class="token-symbol ucap">{{ $symbol }}</span>) {{__('Minimum contribution require.')}}</span>
                    </div>
                    <div class="note-text note-text-alert"></div>
                </div>
            </div>

            @if(!empty($bonus_amount))
            <div class="token-bonus-ui">
                <div class="bonus-bar{{ ($active_bonus) ? ' with-base-bonus' : '' }}">
                    @if(!empty($active_bonus))
                    <div class="bonus-base">
                        <span class="bonus-base-title">{{__('Bonus') }}</span>
                        <span class="bonus-base-amount">{{__('On Sale')}}</span>
                        <span class="bonus-base-percent">{{ $active_bonus->amount }}%</span>
                    </div>
                    @endif
                    @php
                    $b_amt_bar = '';
                    if(!empty($bonus_amount)){
                        foreach($bonus_amount as $token => $bt_amt){
                            $_b = (100 / count($bonus_amount) );
                            $b_amt_bar .= ($bt_amt > 0 && $token > 0) ? '<div class="bonus-extra-item bonus-tire-'. $bt_amt .'" data-percent="'. round($_b, 0).'"><span class="bonus-extra-amount">'. $token .' '. $symbol .'</span><span class="bonus-extra-percent">'.$bt_amt.'%</span></div>' : '';
                        }
                    }
                    $b_amt_bar = (!empty($b_amt_bar)) ? '<div class="bonus-extra">'.$b_amt_bar.'</div>' : '';
                    @endphp
                    {!! $b_amt_bar !!}
                </div>
            </div>
            @endif
            <div class="token-overview-wrap">
                <div class="token-overview">
                    <div class="row">
                        <div class="col-md-4 col-sm-6">
                            <div class="token-bonus token-bonus-sale">
                                <span class="token-overview-title">+ {{ __('Sale Bonus') . ' ' . (empty($active_bonus) ? 0 :  $active_bonus->amount) }}%</span>
                                <span class="token-overview-value bonus-on-sale tokens-bonuses-sale">0</span>
                            </div>
                        </div>
                        @if(!empty($bonus_amount && !empty($b_amt_bar)) )
                        <div class="col-md-4 col-sm-6">
                            <div class="token-bonus token-bonus-amount">
                                <span class="token-overview-title">+ {{__('Amount Bonus')}}</span>
                                <span class="token-overview-value bonus-on-amount tokens-bonuses-amount">0</span>
                            </div>
                        </div>
                        @endif
                        <div class="col-md-4">
                            <div class="token-total">
                                <span class="token-overview-title font-bold">{{__('Total') . ' '.$symbol }}</span>
                                <span class="token-overview-value token-total-amount text-primary payment-summary-amount tokens-total">0</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="note note-plane note-danger note-sm pdt-1x pl-0">
                    <p>{{__('Your contribution will be calculated based on exchange rate at the moment your transaction is confirm.')}}</p>
                </div>
            </div>

            @if(is_payment_method_exist() && !$upcoming && ($stage->status != 'paused'))
            <div class="pay-buttons">
                @if(is_payment_method_exist('manual'))
                    <div class="pay-buttons pt-0">
                        <a data-type="offline" href="#payment-modal" class="btn btn-primary btn-between payment-btn disabled token-payment-btn offline_payment">{{__('Make Payment')}}&nbsp;<i class="ti ti-wallet"></i></a>
                    </div>
                @endif
                <div class="pay-notes">
                    <div class="note note-plane note-light note-md font-italic">
                        <em class="fas fa-info-circle"></em>
                        <p>{{__('Tokens will appear in your account after payment successfully made and approved by our team. please note that, :TOKEN Token will distributed end of token sales.', ['TOKEN' => $symbol]) }}</p>
                    </div>
                </div>
            </div>
            @else
            <div class="alert alert-info alert-center">
                {{__('Our sale start very soon. Please check after sometimes or contact with us.')}}
            </div>
            @endif
            <input type="hidden" id="data_amount" value="0">
            <input type="hidden" id="data_currency" value="{{ $default_method }}">
        </form>
    </div> {{-- .card-innr --}}
</div> {{-- .content-area --}}
@push('sidebar')
<div class="aside sidebar-right col-lg-4">
    @if(!has_wallet())
    <div class="d-none d-lg-block">
        {!! UserPanel::add_wallet_alert() !!}
    </div>
    @endif
    {!! UserPanel::user_balance_card($contribution, ['vers' => 'side']) !!}
    <div class="token-sales card">
        <div class="card-innr">
            <div class="card-head">
                <h5 class="card-title card-title-sm">{{__('Token Sales')}}</h5>
            </div>
            <div class="token-rate-wrap row">
                <div class="token-rate col-md-6 col-lg-12">
                    <span class="card-sub-title">{{ $symbol }} {{__('Token Price')}}</span>
                    <h4 class="font-mid text-dark">1 {{ $symbol }} = <span>{{ _format( ['number'=>token_calc(1, 'price')->$bc] ) .' '. base_currency(true) }}</span></h4>
                </div>
                <div class="token-rate col-md-6 col-lg-12">
                    <span class="card-sub-title">{{__('Exchange Rate')}}</span>
                    @php
                    $exrpm = collect($pm_currency);
                    $exrpm = $exrpm->forget(base_currency())->take(2);
                    $exc_rate = '<span>1 '.base_currency(true) .' ';
                    foreach ($exrpm as $cur => $name) {
                        if($cur != base_currency() && get_exc_rate($cur) != '') {
                            $exc_rate .= ' = '.get_exc_rate($cur) . ' ' . strtoupper($cur);
                        }
                    }
                    $exc_rate .= '</span>';
                    @endphp
                    {!! $exc_rate !!}
                </div>
            </div>
            @if(!empty($active_bonus))
            <div class="token-bonus-current">
                <div class="fake-class">
                    <span class="card-sub-title">{{__('Current Bonus')}}</span>
                    <div class="h3 mb-0">{{ $active_bonus->amount }} %</div>
                </div>
                <div class="token-bonus-date">{{__('End at')}}<br>{{ _date($active_bonus->end_date, get_setting('site_date_format')) }}</div>
            </div>
            @endif
        </div>
    </div>
    @if($upcoming)
    <div class="card-innr">
        <div class="card-head">
            <h5 class="card-title ucap card-title-sm">{{__('Sales Start in')}}</h5>
        </div>
        <div class="countdown-clock" data-date="{{ _date(active_stage()->start_date, 'Y/m/d') }}"></div>
    </div>
    @else 
        {!! UserPanel::token_sales_progress('',  ['class' => 'mb-0']) !!}
    @endif
</div>{{-- .col.aside --}}
@endpush
@endsection
@section('modals')
<div class="modal fade modal-payment" id="payment-modal" tabindex="-1" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-dialog-md modal-dialog-centered">
        <div class="modal-content"></div>
    </div>
</div>
@endsection
@push('footer')
<script>
    var access_url = "{{ route('user.ajax.token.access') }}";
    var minimum_token = {{ $min_token }}, maximum_token ={{ $stage->max_purchase }}, token_price = {!! $token_price !!}, token_symbol = "{{ $symbol }}",
    base_bonus = {!! $bonus !!}, amount_bonus = {!! $amount_bonus !!}, decimals = {"min":{{ $decimal_min }}, "max":{{ $decimal_max }} }, base_currency = "{{ base_currency() }}", base_method = "{{ $method }}";
</script>
@endpush