@extends('layouts.user')
@section('title', __('User Transactions'))

@push('header')
<script type="text/javascript">
    var view_transaction_url = "{{ route('user.ajax.transactions.view') }}";
</script>
@endpush

@section('content')
<div class="page-content">
    <div class="container">
        <div class="card content-area">
            <div class="card-innr">
                <div class="card-head">
                    <h4 class="card-title">{{__('Transactions list')}}</h4>
                </div>
                <table class="data-table dt-init user-tnx">
                    <thead>
                        <tr class="data-item data-head">
                            <th class="data-col filter-data dt-tnxno">{{__('Tranx NO')}}</th>
                            <th class="data-col dt-token">{{__('Tokens')}}</th>
                            <th class="data-col dt-amount">{{('Amount')}}</th>
                            <th class="data-col dt-usd-amount">{{ base_currency(true) }} {{__('Amount')}}</th>
                            <th class="data-col dt-account">{{__('From')}}</th>
                            <th class="data-col dt-type"><div class="dt-type-text">Type</div></th>
                            <th class="data-col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($trnxs as $trnx)
                        <tr class="data-item tnx-item-{{ $trnx->id }}">
                            <td class="data-col dt-tnxno">
                                <div class="d-flex align-items-center">
                                    <div class="data-state data-state-{{ str_replace(['progress','canceled'], ['pending','canceled'], __status($trnx->status, 'icon')) }}">
                                        <span class="d-none">{{ ucfirst($trnx->status) }}</span>
                                    </div>
                                    <div class="fake-class">
                                        <span class="lead tnx-id">{{ $trnx->tnx_id }}</span>
                                        <span class="sub sub-date">{{_date($trnx->tnx_time)}}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="data-col dt-token">
                                <span class="lead token-amount">+{{ $trnx->total_tokens }}</span>
                                <span class="sub sub-symbol">{{ token_symbol() }}</span>
                            </td>
                            <td class="data-col dt-amount">
                                <span class="lead amount-pay">{{ round($trnx->amount, min_decimal()) }}</span>
                                <span class="sub sub-symbol">{{ strtoupper($trnx->currency) }} <em class="fas fa-info-circle" data-toggle="tooltip" data-placement="bottom" title="1 {{ token('symbol') }} = {{ $trnx->currency_rate.' '.strtoupper($trnx->currency) }}"></em></span>
                            </td>
                            <td class="data-col dt-usd-amount">
                                <span class="lead amount-pay">{{ $trnx->base_amount }}</span>
                                <span class="sub sub-symbol">{{ base_currency(true) }} <em class="fas fa-info-circle" data-toggle="tooltip" data-placement="bottom" title="1 {{ token('symbol') }} = {{ $trnx->base_currency_rate.' '.base_currency(true) }}"></em></span>
                            </td>
                            <td class="data-col dt-account">
                                <span class="lead user-info">{{ strlen($trnx->wallet_address) > 6 ? show_str($trnx->wallet_address) : ($trnx->wallet_address != NULL ? $trnx->wallet_address : '~') }}</span>
                                <span class="sub sub-date">{{ _date($trnx->created_at) }}</span>
                            </td>
                            <td class="data-col dt-type">
                                <span class="dt-type-md badge badge-outline badge-{{$trnx->tnx_type == 'purchase' ? 'success' : 'info' }} badge-md">{{ ucfirst($trnx->tnx_type) }}</span>
                                <span class="dt-type-sm badge badge-sq badge-outline badge-{{ __status($trnx->status, 'status') }} badge-md">{{ ucfirst(substr($trnx->tnx_type, 0,1)) }}</span>
                            </td>
                            <td class="data-col text-right">
                                @if($trnx->status == 'pending' || $trnx->status == 'onhold')
                                <div class="relative d-inline-block d-md-none">
                                    <a href="#" class="btn btn-light-alt btn-xs btn-icon toggle-tigger"><em class="ti ti-more-alt"></em></a>
                                    <div class="toggle-class dropdown-content dropdown-content-center-left pd-2x">
                                        <ul class="data-action-list">
                                            <li><a href="javascript:void(0)" class="btn btn-auto btn-primary btn-xs view-transaction" data-id="{{ $trnx->id }}"><span>{{__('Pay')}} <span class="d-none d-xl-inline-block">{{__('Now')}}</span></span><em class="ti ti-wallet"></em></a></li>
                                            <li><a href="{{ route('user.ajax.transactions.delete', $trnx->id) }}" class="btn btn-danger-alt btn-xs btn-icon user_tnx_trash" data-tnx_id="{{ $trnx->id }}"><em class="ti ti-trash"></em></a></li>
                                        </ul>
                                    </div>
                                </div>

                                <ul class="data-action-list d-none d-md-inline-flex">
                                    <li><a href="javascript:void(0)" class="btn btn-auto btn-primary btn-xs view-transaction" data-id="{{ $trnx->id }}"><span>{{__('Pay')}} <span class="d-none d-xl-inline-block">{{__('Now')}}</span></span><em class="ti ti-wallet"></em></a></li>
                                    <li><a href="{{ route('user.ajax.transactions.delete', $trnx->id) }}" class="btn btn-danger-alt btn-xs btn-icon user_tnx_trash" data-tnx_id="{{ $trnx->id }}"><em class="ti ti-trash"></em></a></li>
                                </ul>
                                @else
                                <a href="javascript:void(0)" class="view-transaction btn btn-light-alt btn-xs btn-icon" data-id="{{ $trnx->id }}"><em class="ti ti-eye"></em></a>
                                @if($trnx->status == 'rejected' || $trnx->status == 'canceled')
                                <a href="{{ route('user.ajax.transactions.delete', $trnx->id) }}" class="btn btn-danger-alt btn-xs btn-icon user_tnx_trash" data-tnx_id="{{ $trnx->id }}"><em class="ti ti-trash"></em></a>
                                @endif
                                @endif
                            </td>
                        </tr>{{-- .data-item --}}
                        @endforeach
                    </tbody>
                </table>
            </div><!-- .card-innr -->
        </div><!-- .card -->
    </div><!-- .container -->
</div><!-- .page-content -->
@endsection
