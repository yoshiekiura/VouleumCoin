@extends('layouts.admin')
@section('title', 'Transaction List')


@section('content')
<div class="page-content">
    <div class="container">
        <div class="card content-area content-area-mh">
            <div class="card-innr">
                <div class="card-head has-aside">
                    <h4 class="card-title">Admin Transactions</h4>
                    <div class="card-opt">
                        <ul class="btn-grp btn-grp-block guttar-20px">
                            <li>
                                <a href="#" class="btn btn-sm btn-auto btn-primary" data-toggle="modal" data-target="#addTnx">
                                    <em class="fas fa-plus-circle"></em><span>Add <span class="d-none d-sm-inline-block">Tokens</span></span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="gaps-1x"></div>
                <div class="row">
                    <div class="col-md-12 ">
                        <div class="float-right position-relative">
                            <a href="#" class="btn btn-light-alt btn-xs dt-filter-text btn-icon toggle-tigger"> <em class="ti ti-settings"></em> </a>
                            <div class="toggle-class toggle-datatable-filter dropdown-content dropdown-dt-filter-text dropdown-content-top-left text-left">
                                <ul class="pdt-1x pdb-1x">
                                    <li class="pd-1x pdl-2x pdr-2x">
                                        <input class="data-filter input-checkbox input-checkbox-sm" type="radio" name="filter" id="all" checked value="">
                                        <label for="all">All</label>
                                    </li>
                                    <li class="pd-1x pdl-2x pdr-2x">
                                        <input class="data-filter input-checkbox input-checkbox-sm" type="radio" name="filter" id="approved" value="approved">
                                        <label for="approved">Approved</label>
                                    </li>
                                    <li class="pd-1x pdl-2x pdr-2x">
                                        <input class="data-filter input-checkbox input-checkbox-sm" type="radio" name="filter" value="pending" id="pending">
                                        <label for="pending">Pending</label>
                                    </li>
                                    <li class="pd-1x pdl-2x pdr-2x">
                                        <input class="data-filter input-checkbox input-checkbox-sm" type="radio" name="filter" value="canceled" id="canceled">
                                        <label for="canceled">Canceled</label>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <table class="data-table dt-filter-init admin-tnx">
                    <thead>
                        <tr class="data-item data-head">
                            <th class="data-col filter-data dt-tnxno">Tranx ID</th>
                            <th class="data-col dt-token">Tokens</th>
                            <th class="data-col dt-amount">Amount</th>
                            <th class="data-col dt-usd-amount">Base Amount</th>
                            <th class="data-col dt-account">Pay From</th>
                            <th class="data-col dt-type">Type</th>
                            <th class="data-col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($trnxs as $trnx)
                        <tr class="data-item" id="tnx-item-{{ $trnx->id }}">
                            <td class="data-col dt-tnxno">
                                <div class="d-flex align-items-center">
                                    <div id="ds-{{ $trnx->id }}" data-toggle="tooltip" data-placement="top" title="{{ __status($trnx->status, 'text') }}" class="data-state data-state-{{ __status($trnx->status, 'icon') }}">
                                        <span class="d-none">{{ ucfirst($trnx->status) }}</span>
                                    </div>
                                    <div class="fake-class">
                                        <span class="lead tnx-id">{{ $trnx->tnx_id }}</span>
                                        <span class="sub sub-date">{{ _date($trnx->tnx_time) }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="data-col dt-token">
                                <span class="lead token-amount">+{{ $trnx->total_tokens }}</span>
                                <span class="sub sub-symbol">{{ token('symbol') }}</span>
                            </td>
                            <td class="data-col dt-amount">
                                <span class="lead amount-pay">{{ $trnx->amount }}</span>
                                <span class="sub sub-symbol">{{ strtoupper($trnx->currency) }} <em class="fas fa-info-circle" data-toggle="tooltip" data-placement="bottom" title="1 {{ token('symbol') }} = {{ $trnx->currency_rate.' '.strtoupper($trnx->currency) }}"></em></span>
                            </td>
                            <td class="data-col dt-usd-amount">
                                <span class="lead amount-receive">{{ $trnx->base_amount }}</span>
                                <span class="sub sub-symbol">{{ strtoupper($trnx->base_currency) }} <em class="fas fa-info-circle" data-toggle="tooltip" data-placement="bottom" title="1 {{ token('symbol') }} = {{ $trnx->base_currency_rate.' '.strtoupper($trnx->base_currency) }}"></em></span>
                            </td>
                            <td class="data-col dt-account">
                                <span class="sub sub-s2 pay-with">Pay with {{ ($trnx->currency == 'usd')? 'Bank': strtoupper($trnx->currency) }}
                                    @if($trnx->wallet_address)
                                    <em class="fas fa-info-circle" data-toggle="tooltip" data-placement="bottom" title="{{ $trnx->wallet_address }}"></em>
                                    @endif
                                </span>
                                <span class="sub sub-email">{{ set_id($trnx->user) }} <em class="fas fa-info-circle" data-toggle="tooltip" data-placement="bottom" title="{{ explode_user_for_demo($trnx->tnxUser->email, auth()->user()->type) }}"></em></span>
                            </td>
                            <td class="data-col data-type">
                                <span class="dt-type-md badge badge-outline badge-md badge-{{$trnx->id}} badge-{{__status($trnx->tnx_type,'status')}}">{{ ucfirst($trnx->tnx_type) }}</span>
                                <span class="dt-type-sm badge badge-sq badge-outline badge-md badge-{{$trnx->id}} badge-{{__status($trnx->tnx_type,'status')}}">{{ ucfirst(substr($trnx->tnx_type, 0, 1)) }}</span>
                            </td>
                            <td class="data-col text-right">
                                <div class="relative d-inline-block">
                                    <a href="#" class="btn btn-light-alt btn-xs btn-icon toggle-tigger"><em class="ti ti-more-alt"></em></a>
                                    <div class="toggle-class dropdown-content dropdown-content-top-left">
                                        <ul id="more-menu-{{ $trnx->id }}" class="dropdown-list">
                                            @if($trnx->status == 'approved')
                                            <li><a href="{{ route('admin.transactions.view', $trnx->id) }}"><em class="ti ti-eye"></em> View Details</a></li>
                                            @else
                                            <li><a href="{{ route('admin.transactions.view', $trnx->id) }}"><em class="ti ti-eye"></em> View Details</a></li>
                                            @if($trnx->payment_method == 'bank' || ($trnx->payment_method == 'manual' && $trnx->status != 'approved'))
                                            <li><a href="" id="adjust_token" data-id="{{ $trnx->id }}"><em class="far fa-check-square"></em>Approve</a></li>
                                            @endif
                                            @if($trnx->status != 'approved' && ($trnx->status == 'pending' ) || $trnx->status == 'onhold')
                                            <li id="canceled"><a href="javascript:void(0)" class="tnx-action" data-type="canceled" data-id="{{ $trnx->id }}"><em class="fas fa-ban"></em>Cancel</a></li>
                                            @endif
                                            @if($trnx->status == 'canceled')
                                            <li><a href="javascript:void(0)" class="tnx-action" data-type="deleted" data-id="{{ $trnx->id }}"><em class="fas fa-trash-alt"></em>Delete</a></li>
                                            @endif
                                            @endif
                                        </ul>
                                    </div>
                                </div>
                            </td>
                        </tr>{{-- .data-item --}}
                        @endforeach
                    </tbody>
                </table>
            </div>{{-- .card-innr --}}
        </div>{{-- .card --}}
    </div>{{-- .container --}}
</div>{{-- .page-content --}}

@endsection

@section('modals')
<div class="modal fade" id="addTnx" tabindex="-1">
    <div class="modal-dialog modal-dialog-md modal-dialog-centered">
        <div class="modal-content">
            <a href="#" class="modal-close" data-dismiss="modal" aria-label="Close"><em class="ti ti-close"></em></a>
            <div class="popup-body popup-body-md">
                <h3 class="popup-title">Manually Add Tokens</h3>
                <form action="{{ route('admin.ajax.transactions.add') }}" method="POST" class="validate-modern" id="add_token" autocomplete="off">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="input-item input-with-label">
                                <label class="input-item-label">Transaction Type</label>
                                <div class="input-wrap">
                                    <select name="type" class="select select-block select-bordered" required>
                                        <option value="purchase">Purchase</option>
                                        <option value="bonus">Bonus</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="input-item input-with-label w-sm-60">
                                <label class="input-item-label">Transaction Date</label>
                                <div class="input-wrap">
                                    <input class="input-bordered date-picker" required="" type="text" name="tnx_date">
                                    <span class="input-icon input-icon-right date-picker-icon"><em class="ti ti-calendar"></em></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="input-item input-with-label">
                                <label class="input-item-label">Token Added To</label>
                                <div class="input-wrap">
                                    <select name="user" required="" class="select select-block select-bordered">
                                        @forelse($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                        @empty
                                        <option value="">No user found</option>
                                        @endif
                                    </select>
                                    <span class="input-note">Select account to add token.</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="input-item input-with-label">
                                <label class="input-item-label">Transaction on Stage</label>
                                <div class="input-wrap">
                                    <select name="stage" class="select select-block select-bordered" required>
                                        @forelse($stages as $stage)
                                        <option value="{{ $stage->id }}">{{ $stage->name }}</option>
                                        @empty
                                        <option value="">No active stage</option>
                                        @endif
                                    </select>
                                    <span class="input-note">Select Stage where from adjust tokens.</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="input-item input-with-label">
                                <label class="input-item-label">Payment Gateway</label>
                                <div class="input-wrap">
                                    <select name="payment_method" class="select select-block select-bordered">
                                        @foreach($pmethods as $_pm)
                                        <option value="{{ $_pm->payment_method }}">{{ ucfirst($_pm->payment_method) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="input-item input-with-label">
                                <label class="input-item-label">Payment From</label>
                                <div class="input-wrap">
                                    <input class="input-bordered" type="text" name="wallet_address" placeholder="Optional">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="input-item input-with-label">
                                <label class="input-item-label">Number of Tokens</label>
                                <div class="input-wrap">
                                    <input class="input-bordered" type="number" name="total_tokens" max="{{ active_stage()->max_purchase }}" required>
                                </div>
                                <span class="input-note">Enter the number of tokens you would like to add into selected user account.</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="input-item input-with-label">
                                <label class="input-item-label">Pay Amount</label>
                                <div class="row flex-n guttar-10px">
                                    <div class="col-7">
                                        <div class="input-wrap">
                                            <input class="input-bordered" type="number" name="amount" placeholder="Optional">
                                        </div>
                                    </div>
                                    <div class="col-5">
                                        <div class="input-wrap">
                                            <select name="currency" class="select select-block select-bordered">
                                                @foreach($pm_currency as $gt => $full)
                                                @if(token('purchase_'.$gt) == 1)
                                                <option value="{{ strtoupper($gt) }}">{{ strtoupper($gt) }}</option>
                                                @endif
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <span class="input-note">Amount automatically calculate based on seleted stage if leave blank. </span>
                            </div>
                        </div>
                    </div>
                    <div class="gaps-1x"></div>
                    <button type="submit" class="btn btn-primary">Add Token</button>
                </form>
            </div>
        </div>{{-- .modal-content --}}
    </div>{{-- .modal-dialog --}}
</div>
{{-- Modal End --}}

@endsection