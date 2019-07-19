@extends('layouts.admin')
@section('title', 'Payment Methods')

@php
$mnl = $payments->manual;
@endphp

@section('content')
    <div class="page-content">
        <div class="container">
            <div class="card content-area">
                <div class="card-innr">
                    <div class="card-head d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Payment Methods</h4>
                        <a href="javascript:void(0)" class="btn btn-sm btn-auto btn-outline btn-primary get_pm_manage" data-type="manage_currency"><em class="fas fa-coins"></em><span class=" d-sm-inline-block d-none">Manage Currency</span></a>
                    </div>
                    <div class="gaps-1x"></div>
                    <div class="card-text">
                        <h5 class="card-title-md text-primary">Offline Payment Gateway</h5>
                        <p>All contributors allow to send their payment for token purchase. So double check the address before entering it and be sure you have access of these wallet. You can use all of them or individually by enable each wallet..</p>
                    </div>
                    <div class="gaps-2x"></div>
                    <div class="row">
                        <div class="col-12">
                            <form action="{{ route('admin.ajax.payments.update') }}" method="POST" id="pm_manual_form">
                                @csrf
                                    <input type="hidden" name="req_type" value="manual">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="input-item input-with-label">
                                            <label class="input-item-label">Method Title</label>
                                            <input class="input-bordered" value="{{ $mnl->title }}" type="text" name="mnl_title">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="input-item input-with-label">
                                            <label class="input-item-label">Description</label>
                                            <input class="input-bordered" value="{{ $mnl->details }}" placeholder="You can send paymeny direct to our wallets; We will manually verify" type="text" name="mnl_details">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="sap"></div>
                                    </div>
                                </div>
                                <div class="fake-class">
                                    <div class="payment-wallet-head">
                                        <div class="input-item">
                                            <input class="input-switch switch-toggle" data-switch="switch-to-ethWallet" type="checkbox" {{ $mnl->secret->eth->status == 'active' ? 'checked' : '' }} id="ethWallet" name="mnl_eth">
                                            <label for="ethWallet"></label>
                                        </div>
                                        <div class="input-item flex-grow-1">
                                            <a href="#" class="switch-toggle-link" data-switch="switch-to-ethWallet"></a>
                                            <h5 class="payment-wallet-title">Ethereum Wallet</h5>
                                            <span class="payment-wallet-des">Your personal ETH address to get payment</span>
                                        </div>
                                    </div>{{-- .payment-wallet --}}
                                    <div class="switch-content switch-to-ethWallet">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="input-item input-with-label">
                                                    <label class="input-item-label">Wallet Address</label>
                                                    <input class="input-bordered" placeholder="Enter your wallet address; be sure you have access of the wallet." type="text" name="eth_address" value="{{ $mnl->secret->eth->address }}">
                                                </div>
                                            </div>
                                            <div class="col-6 col-md-3">
                                                <div class="input-item input-with-label">
                                                    <label class="input-item-label">Gas Limit</label>
                                                     <input class="input-bordered" placeholder="Optional" type="text" name="eth_lmt" value="{{ $mnl->secret->eth->limit }}">
                                                </div>
                                            </div>
                                            <div class="col-6 col-md-3">
                                                <div class="input-item input-with-label">
                                                    <label class="input-item-label">Gas price</label>
                                                   <input class="input-bordered" placeholder="Optional" type="text" value="{{ $mnl->secret->eth->price }}" name="eth_price">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="sap"></div>
                                </div>
                                <div class="fake-class">
                                    <div class="payment-wallet-head">
                                        <div class="input-item">
                                            <input class="input-switch switch-toggle" data-switch="switch-to-btcWallet" type="checkbox" {{ $mnl->secret->btc->status == 'active' ? 'checked' : '' }} id="btcWallet" name="mnl_btc">
                                            <label for="btcWallet"></label>
                                        </div>
                                        <div class="input-item flex-grow-1">
                                            <input class="input-switch switch-toggle" data-switch="switch-to-btcWallet" type="checkbox" {{ $mnl->secret->btc->status == 'active' ? 'checked' : '' }} id="btcWallet" name="mnl_btc">
                                            <h5 class="payment-wallet-title">Bitcoin Wallet</h5>
                                            <span class="payment-wallet-des">Your personal BTC public address to get payment</span>
                                        </div>
                                    </div>{{-- .payment-wallet --}}
                                    <div class="switch-content switch-to-btcWallet">
                                        <div class="input-item input-with-label wide-max-sm">
                                            <label class="input-item-label">Wallet Address</label>
                                            <input class="input-bordered" placeholder="Enter your wallet address; be sure you have access of the wallet." type="text" name="btc_address" value="{{ $mnl->secret->btc->address }}">
                                        </div>
                                    </div>
                                    <div class="sap"></div>
                                </div>
                                <div class="fake-class">
                                    <div class="payment-wallet-head">
                                        <div class="input-item">
                                            <input class="input-switch switch-toggle" data-switch="switch-to-ltcWallet" type="checkbox" {{ $mnl->secret->ltc->status == 'active' ? 'checked' : '' }} id="ltcWallet" name="mnl_ltc">
                                            <label for="ltcWallet"></label>
                                        </div>
                                        <div class="input-item flex-grow-1">
                                            <a href="#" class="switch-toggle-link" data-switch="switch-to-ltcWallet"></a>
                                            <h5 class="payment-wallet-title">Litecoin Wallet</h5>
                                            <span class="payment-wallet-des">Your personal LTC address to get payment</span>
                                        </div>
                                    </div>{{-- .payment-wallet --}}
                                    <div class="switch-content switch-to-ltcWallet">
                                        <div class="input-item input-with-label wide-max-sm">
                                            <label class="input-item-label">Wallet Address</label>
                                             <input class="input-bordered" placeholder="Enter your wallet address; be sure you have access of the wallet." type="text" name="ltc_address" value="{{ $mnl->secret->ltc->address }}">
                                        </div>
                                    </div>
                                    <div class="sap"></div>
                                </div>
                                <div class="fake-class">
                                    <div class="payment-wallet-head">
                                        <div class="input-item">
                                             <input class="input-switch switch-toggle" data-switch="switch-to-bankWallet" type="checkbox" {{ $mnl->secret->bank->status == 'active' ? 'checked' : '' }} id="bankWallet" name="mnl_bank">
                                            <label for="bankWallet"></label>
                                        </div>
                                        <div class="input-item flex-grow-1">
                                            <a href="#" class="switch-toggle-link" data-switch="switch-to-bankWallet"></a>
                                            <h5 class="payment-wallet-title">Bank Account</h5>
                                            <span class="payment-wallet-des">Your personal BTC public address to get payment</span>
                                        </div>
                                    </div>
                                    <div class="switch-content switch-to-bankWallet">
                                        <div class="row">
                                            <div class="col-sm-6 col-lg-4">
                                                <div class="input-item input-with-label">
                                                    <label class="input-item-label">Account Name</label>
                                                     <input class="input-bordered required" name="bank_account_name" value="{{ $mnl->secret->bank->bank_account_name }}" type="text" placeholder="Place here your Bank Account Holder Name">
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-lg-4">
                                                <div class="input-item input-with-label">
                                                    <label class="input-item-label">Account Number</label>
                                                     <input class="input-bordered required number" name="bank_account_number" value="{{ $mnl->secret->bank->bank_account_number }}" type="number" placeholder="Place here your Bank Account number">
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-lg-4">
                                                <div class="input-item input-with-label">
                                                    <label class="input-item-label">Bank Name</label>
                                                    <input class="input-bordered required" name="bank_name" value="{{ $mnl->secret->bank->bank_name }}" type="text" placeholder="Bank Name">
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-lg-4">
                                                <div class="input-item input-with-label">
                                                    <label class="input-item-label">Bank Routing Number</label>
                                                    <input class="input-bordered number" name="routing_number" value="{{ $mnl->secret->bank->routing_number }}" type="number" placeholder="Bank Routing number">
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-lg-4">
                                                <div class="input-item input-with-label">
                                                    <label class="input-item-label">IBAN Number</label>
                                                     <input class="input-bordered" name="iban" value="{{ $mnl->secret->bank->iban }}" type="text" placeholder="Bank IBAN number">
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-lg-4">
                                                <div class="input-item input-with-label">
                                                    <label class="input-item-label">SWIFT or BIC Code of your bank</label>
                                                    <input class="input-bordered" name="swift_bic" value="{{ $mnl->secret->bank->swift_bic }}" type="text" placeholder="SWIFT/BIC">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>{{-- .payment-wallet --}}
                                <div class="gaps-1x"></div>
                                <div class="d-flex pb-1">
                                    <button class="btn btn-md btn-primary save-disabled" disabled type="submit">UPDATE</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>{{-- .container --}}
    </div>{{-- .page-content --}}
@endsection
