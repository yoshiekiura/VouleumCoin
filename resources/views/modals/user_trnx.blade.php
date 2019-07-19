@php
$data = json_decode($tnx->extra);
if($tnx->payment_method == 'paypal'){
    $pay_url = (isset($data->url) ? $data->url : route('user.token'));
}elseif($tnx->payment_method == 'coingate'){
    $pay_url = (isset($data->url) ? $data->url : route('user.token'));
}elseif($tnx->payment_method == 'coinbase'){
    $pay_url = (isset($data->hosted_url) ? $data->hosted_url : route('user.token'));
}else{
    $pay_url = route('user.token');
}
$bank = get_b_data('manual');

$j = json_decode($tnx->checked_by);
$tnx_cur = $tnx->currency;
@endphp
<div class="modal fade" id="transaction-details" tabindex="-1">
    <div class="modal-dialog modal-dialog-md modal-dialog-centered">
        <div class="modal-content">
            @if($tnx)
            <a href="#" class="modal-close" data-dismiss="modal" aria-label="Close"><em class="ti ti-close"></em></a>
            @endif

            <div class="popup-body popup-body-md">
                @if($tnx)
                @if($tnx->status=='pending' || $tnx->status == 'onhold')
                <div class="content-area">
                    <h4 class="popup-title">{{__('Confirmation Your Payment')}}</h4>
                    <form action="{{ route('user.ajax.payment.update') }}" method="POST" id="payment-confirm" class="validate" autocomplete="off">
                        @csrf
                        <input type="hidden" name="trnx_id" value="{{ $tnx->id }}">
                        <p class="lead">{{__('Your order no.')}} <strong>{{ $tnx->tnx_id }}</strong> {{__('has been placed, waiting for payment.')}}</p>
                        <p>{{__('To receiving')}} <strong><span class="token-total">{{ $tnx->total_tokens }}</span> {{ token('symbol') }}</strong> {{__('tokens')}}
                            @if($tnx->total_bonus > 0)
                            {{__('including bonus')}} <strong><span class="token-bonuses">{{ $tnx->total_bonus }}</span> {{ token('symbol') }}</strong>
                            @endif
                            {{__('require payment amount of')}} <strong><span class="final-pay">{{ $tnx->amount }}</span> <span class="pay-currency ucap">{{ $tnx->currency }}</span></strong>.</p>
                            @if($tnx->payment_method == 'manual' && $tnx->currency != 'usd')
                            <label for="address-copy" class="input-item-label">{{__('Please make your Payment to the bellow Address')}}</label>
                            <div class="copy-wrap mgb-0-5x">
                                <span class="copy-feedback"></span>
                                <em class="fab fa-ethereum"></em>
                                <input id="address-copy" type="text" class="copy-address" value="{{ $tnx->payment_method == 'manual' ? get_pm('manual')->$tnx_cur->address : '' }}" placeholder="{{ $tnx->payment_method == 'manual' ? get_pm('manual')->$tnx_cur->address : '' }}" disabled="">
                                <button type="button" class="copy-trigger copy-clipboard" data-clipboard-text="{{ $tnx->payment_method == 'manual' ? get_pm('manual')->$tnx_cur->address : '' }}"><em class="ti ti-files"></em></button>
                            </div><!-- .copy-wrap -->
                            <p><strong>{{__('To speed up verification preprocessing')}}</strong> {{__('please enter')}} <strong>your wallet address</strong> {{__('from where you’ll transferring your ethereum to our address.')}}</p>
                            <div class="input-item input-with-label">
                                <label for="token-address" class="input-item-label">{{__('Enter your wallet address')}}</label>
                                <input type="text" id="token-address" name="payment_address" class="input-bordered" placeholder="Insert your Payment address" >
                            </div>
                            <ul class="d-flex flex-wrap align-items-center guttar-30px">
                                <li><button type="submit" name="action" value="confirm" class="btn btn-primary payment-btn">{{__('Confirm Payment')}}</button></li>
                                <li class="pdt-1x pdb-1x"><button type="submit" name="action" value="cancel" class="btn btn-cancel payment-cancel-btn btn-danger-alt payment-btn"> {{__('Cancel Order')}}</button></li>
                            </ul>
                            <div class="gaps-2x"></div>
                            <div class="note note-md note-info note-plane">
                                <em class="fas fa-info-circle"></em>
                                <p>{{__('Do not make payment through exchange (Kraken, Bitfinex). You can use MyEtherWallet, MetaMask, Mist wallets etc.')}}</p>
                            </div>
                            <div class="gaps-1x"></div>
                            <div class="note note-md note-info note-plane">
                                <em class="fas fa-info-circle"></em>
                                <p>{{__('The tokens balance will appear in your account only after you transaction gets 3 confirmations and approved our team.')}}</p>
                            </div>
                            <div class="gaps-1x"></div>
                            <div class="note note-md note-danger note-plane">
                                <em class="fas fa-info-circle"></em>
                                <p>{{__('In case you send a different amount, number of')}} {{ token_symbol() }} {{__('tokens will update accordingly.')}}</p>
                            </div>
                            @elseif($tnx->payment_method == 'manual' && $tnx->currency == 'usd')
                            <table class="table table-flat">
                                <thead>
                                    <th colspan="2"><h2>{{__('Bank Details for Payment')}}</h2></th>
                                </thead>
                                <tbody>
                                    <tr>
                                        <th>{{__('Bank Name')}}</th>
                                        <td>{{ $bank->bank_name }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{__('Account Holder Name')}}</th>
                                        <td>{{ $bank->bank_account_name }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{__('Account Number')}}</th>
                                        <td>{{ $bank->bank_account_number }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{__('Routing Number')}}</th>
                                        <td>{{ $bank->routing_number }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{__('IBAN')}}</th>
                                        <td>{{ $bank->iban }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{__('Swift/BIC')}}</th>
                                        <td>{{ $bank->swift_bic }}</td>
                                    </tr>
                                </tbody>
                            </table>
                            <button type="submit" name="action" value="cancel" class="btn btn-cancel btn-sm btn-danger-alt payment-cancel-btn btn-link payment-btn">{{__('Cancel Order')}}</button>

                            <div class="gaps-2x"></div>
                            <div class="note note-md note-info note-plane">
                                <em class="fas fa-info-circle"></em>
                                <p>{{__('When you pay the amount via bank account then use this transaction id')}} (#{{ $tnx->tnx_id }}) {{__('as reference.')}}</p>
                            </div>
                            <div class="gaps-1x"></div>
                            <div class="note note-md note-danger note-plane">
                                <em class="fas fa-info-circle"></em>
                                <p>{{__('Make this payment within 24 hours, If you are not pay within 24 hours then we will cancel this transaction.')}}</p>
                            </div>

                            <div class="gaps-2x"></div>
                        </form>
                        @else
                        <div class="gaps-1x"></div>
                        <ul class="d-flex flex-wrap align-items-center guttar-30px">
                            <li><a class="btn btn-primary" href="{{ $pay_url }}" target="_blank" >{{__('Pay via')}} {{ ucfirst($tnx->payment_method) }} <em class="ti ti-arrow-right"></em></a></li>
                            <li class="pdt-1x pdb-1x"><button type="submit" name="action" value="cancel" class="btn btn-cancel btn-sm btn-danger-alt payment-cancel-btn btn-link payment-btn">{{__('Cancel Order')}}</button></li>
                        </ul>
                        <div class="gaps-2x"></div>
                        @endif
                    </form>
                </div>
                @else
                <div class="content-area">
                    <div class="card-head d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">{{__('Transaction Details')}}</h4>
                        <div class="trans-status">
                            @if($tnx->status == 'approved')
                            <span class="badge badge-success ucap">{{__('Approved')}}</span>
                            @elseif($tnx->status == 'pending')
                            <span class="badge badge-warning ucap">{{__('Pending')}}</span>
                            @elseif($tnx->status == 'onhold')
                            <span class="badge badge-info ucap">{{__('Progress')}}</span>
                            @else
                            <span class="badge badge-danger ucap">{{__('Rejected')}}</span>
                            @endif
                        </div>
                    </div>
                    <div class="trans-details">
                        <p>{{__('The order no.')}} <strong class="text-info">{{ $tnx->tnx_id }}</strong> {{__('was placed on')}} <strong>{{ _date($tnx->tnx_time) }}</strong>.</p>

                        @if($tnx->status == 'approved')
                        <p class="status-text text-light"><strong>{{__('You have successfully paid')}}</strong>
                            @if($tnx->payment_method=='manual' && $tnx->wallet_address !=NULL)
                            {{__('via')}} <strong class="text-dark">{{ show_str($tnx->wallet_address) }}</strong> {{__('wallet')}}
                            @else
                            {{__('via')}} <strong class="text-dark">{{ ucfirst($tnx->payment_method) }}</strong>
                            @endif
                        .</p>
                        @endif
                    </div>
                    @if($tnx->status == 'rejected' || $tnx->status == 'canceled')
                    <p class="status-text text-light mt-4">{{__('Sorry! Your order has been')}} <strong>{{__('canceled')}}</strong> {{__('due to payment.')}}</p>
                    @if($tnx->checked_time != NUll)
                    <p class="small text-light">{{__('The transaction was canceled')}}{{ isset($j->name) ? ' by '.$j->name : '' }} at {{ $tnx->checked_time != NUll ? _date($tnx->checked_time) : '' }}.</p>
                    @endif
                    @else
                    <div class="gaps-1x"></div>
                    <h6 class="card-sub-title">{{__('Token Details')}}</h6>
                    <ul class="data-details-list">
                        <li>
                            <div class="data-details-head">{{__('Token Types')}}</div>
                            <div class="data-details-des">{{ ucfirst($tnx->tnx_type) }}</div>
                        </li>
                        <li>
                            <div class="data-details-head">{{__('Token of Stage')}}</div>
                            <div class="data-details-des"><strong>{{ $tnx->ico_stage->name }}</strong></div>
                        </li>
                        <li>
                            <div class="data-details-head">{{__('Token Amount (T)')}}</div>
                            <div class="data-details-des">
                                <span>{{ number_format($tnx->tokens) }} {{ token_symbol() }}</span>
                            </div>
                        </li>{{-- li --}}
                        <li>
                            <div class="data-details-head">{{__('Bonus Tokens (B)')}}</div>
                            <div class="data-details-des">
                                <span>{{ number_format($tnx->total_bonus) }} {{ token_symbol() }}</span>
                                <span>({{ $tnx->bonus_on_token }} + {{ $tnx->bonus_on_base }})</span>
                            </div>
                        </li>{{-- li --}}
                        <li>
                            <div class="data-details-head">{{__('Total Tokens')}}</div>
                            <div class="data-details-des">
                                <span><strong>{{ number_format($tnx->total_tokens) }} {{ token_symbol() }}</strong></span>
                                <span>(T+B)</span>
                            </div>
                        </li>{{-- li --}}

                        <li>
                            <div class="data-details-head">{{__('Total Payment')}}</div>
                            <div class="data-details-des">
                                <span><strong>{{ number_format($tnx->receive_amount) }} {{ strtoupper($tnx->receive_currency) }}</strong></span>
                                <span>(T+B)</span>
                            </div>
                        </li>{{-- li --}}
                    </ul>
                    @endif
                </div>
                @endif
                @else
                <div class="content-area text-center">
                    <div class="status status-error">
                        <em class="ti ti-alert"></em>
                    </div>
                    <h3>{{__('Oops!!!')}}</h3>
                    <p>{{__('Sorry, seems there is an issues occurred and we couldn’t process your request. Please contact us with your order no.')}} <strong>{{ $tnx->tnx_id }}</strong>, {{__('if you continue to having the issues.')}}</p>
                    <div class="gaps-2x"></div>
                    <a href="#" data-dismiss="modal" data-toggle="modal" class="btn btn-light-alt">{{__('Close')}}</a>
                    <div class="gaps-3x"></div>
                </div>
                @endif

            </div>
        </div>
    </div>



    <script type="text/javascript">
        (function($) {
            var $_p_form = $('form#payment-confirm');
            if ($_p_form.length > 0) {
                purchase_form_submit($_p_form);
            }
    // Make pay
    var $make_pay = $('.make-pay'),
    $pay_done = $('.pay-done'),
    $tranx_payment_details = $('.tranx-payment-details'),
    $tranx_purchase_details = $('.tranx-purchase-details');
    if($make_pay.length > 0){
        $make_pay.on('click',function(){
            $tranx_payment_details.addClass('active');
            if($tranx_purchase_details.hasClass('active')){
                $tranx_purchase_details.removeClass('active').fadeOut(200);
            }
            return false;
        });
    }
    if($pay_done.length > 0){
        $pay_done.on('click',function(){
            $tranx_purchase_details.addClass('active');
            if($tranx_payment_details.hasClass('active')){
                $tranx_payment_details.removeClass('active').fadeOut(200);
            }
            return false;
        });
    }
    // Copyto clipboard In Modal
    var clipboardModal = new ClipboardJS('.copy-trigger', {
        container: document.querySelector('.modal')
    });
    clipboardModal.on('success', function(e) {
        feedback(e.trigger, 'success'); e.clearSelection();
    }).on('error', function(e) {
        feedback(e.trigger, 'fail');
    });
})(jQuery);
</script>
