@php
$bank = get_b_data('manual');
@endphp

<a href="#" class="modal-close" data-dismiss="modal"><em class="ti ti-close"></em></a>

@php
$is_bonus = ($_data->total_bonus > 0) ? __('including bonus').'<span class="token-bonuses">'. $_data->total_bonus .'</span> '. token('symbol') .'</strong> ' : '';
$txt = '<p class="lead">'.__('Please make deposit amount of').' <strong><span class="final-pay">'. $_data->amount .'</span> <span class="pay-currency ucap">'. $currency .'</span></strong> '.__('to our address and receive').' <strong><span class="token-total">'. $_data->total_tokens .'</span> '. token('symbol') .'</strong> '.__('tokens').' '.$is_bonus.__('once we received your payment').'.</p>';

@endphp

<div class="popup-body">
    <h2 class="popup-title">{{__('Payment Address for Deposit')}}</h2>
    <div class="gaps-1x"></div>
    <div class="popup-content">
        @if((is_payment_method_exist('manual') && manual_payment(strtolower($currency)) != '' && strtolower($currency) != 'usd') || (strtolower($currency) == 'usd' && $bank->status == 'active'))
        <form class="validate-modern" action="{{ route('user.ajax.payment.manual') }}" method="POST" id="offline_payment">
            @csrf
            <input type="hidden" name="pp_token" id="token_amount" value="{{ $token }}">
            <input type="hidden" name="pp_currency" id="pay_currency" value="{{ $currency }}">
            <div class="offline-payment-details">
                @if(is_payment_method_exist('manual') && strtolower($currency)=='usd')

                <p class="lead">{{__('Please make payment of')}} <strong>{{ $_data->amount }} <span class="pay-currency ucap"> {{$currency}}</span></strong> {{__('to receiving')}} <strong><span class="token-total"> {{ $_data->total_tokens.' '. token('symbol')}} </span></strong> {{ __('tokens')}}
                    @if($_data->total_bonus > 0)
                    {{__('including bonus')}} <strong><span class="token-bonuses"> {{ $_data->total_bonus}}</span> {{token('symbol')}}</strong>
                    @endif
                .</p>
                <p>{{__('You can pay via bank transfer. Bank details will email you once you placed order. The token balance will appear in your account after we received payment.')}}</p>


                @elseif(is_payment_method_exist('manual') && strtolower($currency) != 'usd')
                {!! $txt !!}
                @php 
                $wallet_name = $wallet_icon = '';
                if (manual_payment('eth') && strtolower($currency)=='eth' ) {
                    $wallet_icon = 'eth';
                    $wallet_name = 'Ethereum ';
                }
                if (manual_payment('btc') && strtolower($currency)=='btc' ) {
                    $wallet_icon = 'btc';
                    $wallet_name = 'Bitcoin ';
                }
                if (manual_payment('ltc') && strtolower($currency)=='ltc' ) {
                    $wallet_icon = 'ltc';
                    $wallet_name = 'Litecoin ';
                }
                @endphp
                <div class="gaps-1-5x"></div>
                <div class="pay-wallet-address pay-wallet-{{ strtolower($currency) }}">
                    <h6 class="font-bold">{{ __('Payment to the following :Wallet Wallet Address', ['wallet' => $wallet_name])}}</h6>
                    <div class="copy-wrap mgb-0-5x">
                        <span class="copy-feedback"></span>
                        <em class="fa cf cf-{{ $wallet_icon }}"></em>
                        <input type="text" class="copy-address" value="{{ manual_payment(strtolower($currency)) }}" disabled="">
                        <button type="button" class="copy-trigger copy-clipboard" data-clipboard-text="{{ manual_payment(strtolower($currency)) }}"><em class="ti ti-files"></em></button>
                    </div>
                    @if( (manual_payment('eth', 'limit') || manual_payment('eth', 'price'))  && strtolower($currency)=='eth' )
                    <ul class="pay-info-list row">
                        @if(manual_payment('eth', 'limit'))
                        <li class="col-sm-6"><span>{{__('SET GAS LIMIT:')}}</span> {{ manual_payment('eth', 'limit') }}</li>
                        @endif
                        @if(manual_payment('eth', 'price'))
                        <li class="col-sm-6"><span>{{__('SET GAS PRICE:')}}</span> {{ manual_payment('eth', 'price') }} {{__('Gwei')}}</li>
                        @endif
                    </ul>
                    @endif
                </div>
                @endif 
                <div class="pdt-1-5x">
                    <div class="gaps-1x"></div>
                    <div class="input-item text-left">
                        <input type="checkbox" data-msg-required="{{ __('You should accept our terms and policy.') }}" class="input-checkbox input-checkbox-md" id="agree-terms" name="agree" required>
                        <label for="agree-terms">{{ __('I hereby agree to the') }} <strong>{{ __('token purchase agreement and token sale term') }}</strong>.</label>
                    </div>
                </div>
                <div class="gaps-1x"></div>
                <button type="submit" class="btn btn-primary payment-btn">{{__('Buy Tokens Now')}}  <em class="ti ti-arrow-right mgl-4-5x"></em></button>  
                <div class="gaps-3x"></div>

                @if(is_payment_method_exist('manual') && strtolower($currency)=='usd')
                <div class="note note-md note-danger note-plane">
                    <em class="fas fa-info-circle"></em> 
                    <p>{{ __('Make this payment within :HOUR hours. If we will not get this payment within :HOUR hours, then we will cancel this transaction. In case you send a different amount, number of :TOKEN token will update accordingly.', ['token' => token_symbol(), 'hour' => '24']) }}</p>
                </div>
                
                @else
                <div class="note note-md note-info note-plane">
                    <em class="fas fa-info-circle"></em> 
                    <p>{{ __('Do not make payment through exchange (Kraken, Bitfinex). You can use MyEtherWallet, MetaMask, Mist wallets etc.') }}</p>
                </div>
                <div class="gaps-1x"></div>
                <div class="note note-md note-danger note-plane">
                    <em class="fas fa-info-circle"></em> 
                    <p>{{ __('In case you send a different amount, number of :TOKEN token will update accordingly.', ['token' => token_symbol()]) }}</p>
                </div>
                @endif
            </div>{{-- .tranx-payment-details --}}
        </form>
        @else
        <div class="offline-payment-details">
            {!! $txt !!}
            <div class="gaps-4x"></div>
            <div class="alert alert-danger text-center"><strong>{{__('Sorry!')}}</strong>, {{__('There is no payment method available for this currency. Please choose another currency or contact our support team.')}} </div>
            <div class="gaps-5x"></div>
        </div>
        @endif
    </div>
</div>


<script type="text/javascript">

    (function($) {
        var $_p_form = $('form#offline_payment');
        if ($_p_form.length > 0) {
            purchase_form_submit($_p_form);
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