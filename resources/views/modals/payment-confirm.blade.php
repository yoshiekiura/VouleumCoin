
<div class="popup-body">
    <h4 class="popup-title">{{__('Confirmation Your Payment')}}</h4>
    <div class="popup-content">
        <form action="{{ route('user.ajax.payment.update') }}" method="POST" id="payment-confirm" class="validate-modern" autocomplete="off">
            @csrf
            <input type="hidden" name="trnx_id" value="{{ $transaction->id }}">
            <p class="lead text-primary">{{__('Your Order no.')}} <strong>{{ $transaction->tnx_id }}</strong> {{__('has been placed successfully.')}} </p>

            <p>{{__('The tokens balance will appear in your account only after you transaction gets :NUM confirmations and approved our team.', ['num' => 6])}}
            </p>
            <p><strong>{{ __('To speed up verification process')}}</strong> {{__('please enter')}} <strong>{{__('your wallet address')}}</strong> {{__('from where you’ll transferring your amount to our address.')}}</p>
            
            <div class="input-item input-with-label">
                <label for="token-address" class="input-item-label">{{__('Enter Your Wallet Address')}}</label>
                <input id="token-address" type="text" name="payment_address" class="input-bordered" placeholder="{{__('Insert your payment address')}}">
            </div>

            <ul class="d-flex flex-wrap align-items-center guttar-30px">
                <li><button type="submit" name="action" value="confirm" class="btn btn-primary payment-btn">{{__('Confirm Payment')}}</button></li> 
                <li class="pdt-1x pdb-1x"><button type="submit" name="action" value="cancel" class="btn btn-cancel btn-danger-alt payment-cancel-btn payment-btn">{{__('Cancel Order')}}</button></li>
            </ul>     
            <div class="gaps-2x"></div>

            <div class="note note-md note-info note-plane">
                <em class="fas fa-info-circle"></em> 
                <p>{{__('Do not make payment through exchange (Kraken, Bitfinex). You can use MyEtherWallet, MetaMask, Mist wallets etc.')}}</p>
            </div>
            <div class="gaps-1x"></div>
            <div class="note note-md note-danger note-plane">
                <em class="fas fa-info-circle"></em> 
                <p>{{ __('In case you send a different amount, number of :TOKEN token will update accordingly.', ['token' => token_symbol()]) }}</p>
            </div>
        </form>
    </div>
</div>
<script type="text/javascript">

    (function($) {
        var $_p_form = $('form#payment-confirm');
        if ($_p_form.length > 0) {
            purchase_form_submit($_p_form);
        }

    })(jQuery);
</script>