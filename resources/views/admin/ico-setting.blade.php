@extends('layouts.admin')
@section('title', 'ICO Setting')

@section('content')
<div class="page-content">
    <div class="container">
        <div class="row">
            <div class="main-content col-lg-12">
                <div class="content-area card">
                    <div class="card-innr">
                        <div class="card-head">
                            <h4 class="card-title">ICO Settings </h4>
                        </div>
                        <div class="gaps-1x"></div>
                        <div class="card-text ico-setting setting-token-details">
                            <h3 class="card-title-md text-primary">Token Details</h3>
                            <form action="{{ route('admin.ajax.stages.settings.update') }}" class="validate-modern"  method="POST" id="stage_setting_details_form">
                                @csrf
                                <input type="hidden" name="req_type" value="token_details">
                                <div class="row">
                                    <div class="col-xl-3 col-sm-6">
                                        <div class="input-item input-with-label">
                                            <label class="input-item-label">Token Name</label>
                                            <div class="input-wrap">
                                                <input class="input-bordered" required="" type="text" name="token_name" value="{{ token('name') }}" minlength="4">
                                                <span class="input-note">Enter name of token without spaces. Lower and uppercase can be used.</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xl-3 col-sm-6">
                                        <div class="input-item input-with-label">
                                            <label class="input-item-label">Token Symbol</label>
                                            <div class="input-wrap">
                                                <input class="input-bordered" required type="text" name="token_symbol" value="{{ token('symbol') }}" minlength="2">
                                                <span class="input-note">Usually 3-4 Letters like ETH, BTC, WISH etc.</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xl-3 col-sm-6">
                                        <div class="input-item input-with-label">
                                            <label class="input-item-label">Decimal Minimum</label>
                                            <input class="input-bordered" type="number" name="token_decimal_min" value="{{ token('decimal_min') }}" min="2" max="6">
                                            <span class="input-note">Minimum number of decimal point for calculation. 2-6 are accepted.</span>
                                        </div>
                                    </div>
                                    <div class="col-xl-3 col-sm-6">
                                        <div class="input-item input-with-label">
                                            <label class="input-item-label">Decimal Maximum</label>
                                            <input class="input-bordered" type="number" name="token_decimal_max" value="{{ token('decimal_max') }}" min="4" max="10">
                                            <span class="input-note">Maximum number of decimal point for calculation. 4-10 are accepted.</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="gaps-1x"></div>
                                <div class="d-flex">
                                    <button class="btn btn-primary save-disabled" type="submit" disabled><i class="ti ti-reload"></i><span>Update</span></button>
                                </div>
                            </form>
                        </div>
                        <div class="gaps-3-5x"></div>
                        <div class="sap"></div>
                        <div class="gaps-2-5x"></div>
                        <div class="card-text ico-setting setting-token-purchase">
                            <h4 class="card-title-md text-primary">Token Purchase</h4>
                            <form action="{{ route('admin.ajax.stages.settings.update') }}" method="POST" id="stage_setting_purchase_form">
                                @csrf
                                <input type="hidden" name="req_type" value="token_purchase">
                                <div class="row">

                                    <div class="col-xl-3 col-sm-6">
                                        <div class="input-item input-with-label">
                                            <label class="input-item-label">Default Selection</label>
                                            <select class="select select-block select-bordered active_method" name="token_default_method">
                                                @foreach($pm_gateways as $pmg => $pmval)
                                                @if(get_setting('pmc_active_'.$pmg) == 1)
                                                <option {{ token('default_method') == strtoupper($pmg) ? 'selected ' : '' }}value="{{ strtoupper($pmg) }}">{{ $pmval }}</option>
                                                @endif
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-xl-3 col-sm-6">
                                        <div class="input-item input-with-label">
                                            <label class="input-item-label">Per Token Price</label>
                                            <div class="gaps-1x"></div>
                                            <input class="input-switch" name="token_price_show" type="checkbox" {{ token('price_show') == 1 ? 'checked' : '' }} id="show-user">
                                            <label for="show-user">Show</label>
                                        </div>
                                    </div>
                                    <div class="col-xl-3 col-sm-6">
                                        <div class="input-item input-with-label">
                                            <label class="input-item-label">KYC before Purchase</label>
                                            <div class="gaps-1x"></div>
                                            <input class="input-switch" name="token_before_kyc" type="checkbox" {{ token('before_kyc') == 1 ? 'checked' : '' }} id="private-sale">
                                            <label for="private-sale">Enable Verification</label>
                                        </div>
                                    </div>
                                    <div class="col-xl-3 col-sm-6">
                                        <div class="input-item input-with-label">
                                            <label class="input-item-label">Token Price show in </label>
                                            <select class="select select-block select-bordered active_method" name="token_default_in_userpanel">
                                                @foreach($pm_gateways as $pmg => $pmval)
                                                @if(get_setting('pmc_active_'.$pmg) == 1 && base_currency() != $pmg)
                                                <option {{ token('default_in_userpanel') == strtoupper($pmg) ? 'selected ' : '' }}value="{{ strtoupper($pmg) }}"> {{ base_currency(true) }} -> {{ strtoupper($pmg) }}</option>
                                                @endif
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <label class="input-item-label">Purchase With</label>
                                <ul class="d-flex flex-wrap checkbox-list">
                                    @foreach($pm_gateways as $pmg => $pmval)
                                    @if(get_setting('pmc_active_'.$pmg) == 1)
                                    <li>
                                        <div class="input-item text-left">
                                            <input class="input-checkbox all_methods" name="token_purchase_{{ $pmg }}" id="pw-{{ $pmg }}" {{ (token('purchase_'.$pmg) == 1) ? 'checked ' : ' '}} {{token('default_method') == strtoupper($pmg) ? 'disabled ' : ' ' }}  type="checkbox">
                                            <label for="pw-{{ $pmg }}">{{ $pmval .' ('.strtoupper($pmg).')'}}</label>
                                        </div>
                                    </li>
                                    @endif
                                    @endforeach
                                </ul>
                                <div class="gaps-1x"></div>
                                <div class="d-flex">
                                    <button class="btn btn-primary save-disabled" type="submit" disabled><i class="ti ti-reload"></i><span>Update</span></button>
                                </div>
                            </form>
                        </div>
                    </div>{{-- .card-innr --}}
                </div>{{-- .card --}}

            </div>{{-- .col --}}
        </div>{{-- .container --}}
    </div>{{-- .container --}}
</div>{{-- .page-content --}}
@endsection
