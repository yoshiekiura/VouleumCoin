@php
$bank = get_b_data('manual');
@endphp
<a href="#" class="modal-close" data-dismiss="modal"><em class="ti ti-close"></em></a>
<div class="popup-body popup-body">
    <h4 class="lead text-primary">{{__('Your Order no.')}} <strong>{{ $transaction->tnx_id }}</strong> {{__('has been placed successfully.')}} </h4>
    <p>{{__('The tokens balance will appear in your account only after your transaction gets approved by our team.')}}
    </p>
    <h4 class="popup-title">{{__('Bank Details for Payment')}}</h4>
    <div class="popup-content">
        <p class="lead">{{__('Please make payment of')}} {{ $transaction->amount }} <span class="pay-currency ucap"> {{$transaction->currency}}</span> to receiving <strong><span class="token-total"> {{ $transaction->total_tokens.' '. token('symbol')}} </span></strong> {{ __('tokens')}}
            @if($transaction->total_bonus > 0)
            {{__('including bonus')}}<span class="token-bonuses"> {{ $transaction->total_bonus}}</span><strong> {{token('symbol')}}</strong>
            @endif
        .</p>

        <form action="{{ route('user.ajax.payment.update') }}" method="POST" id="payment-confirm" class="validate-modern" autocomplete="off">
            @csrf
            <input type="hidden" name="trnx_id" value="{{ $transaction->id }}">
            <table class="table table-flat">
                <thead>
                    <th colspan="2"></th>
                </thead>
                <tbody>
                    @if(!empty($bank->bank_name))
                    <tr>
                        <th>{{__('Bank Name')}}</th>
                        <td>{{ $bank->bank_name }}</td>
                    </tr>
                    @endif
                    @if(!empty($bank->bank_account_name))
                    <tr>
                        <th>{{__('Account Holder Name')}}</th>
                        <td>{{ $bank->bank_account_name }}</td>
                    </tr>
                    @endif
                    @if(!empty($bank->bank_account_number))
                    <tr>
                        <th>{{__('Account Number')}}</th>
                        <td>{{ $bank->bank_account_number }}</td>
                    </tr>
                    @endif
                    @if(!empty($bank->routing_number))
                    <tr>
                        <th>{{__('Routing Number')}}</th>
                        <td>{{ $bank->routing_number }}</td>
                    </tr>
                    @endif
                    @if(!empty($bank->iban))
                    <tr>
                        <th>{{__('IBAN')}}</th>
                        <td>{{ $bank->iban }}</td>
                    </tr>
                    @endif
                    @if(!empty($bank->swift_bic))
                    <tr>
                        <th>{{__('Swift/BIC')}}</th>
                        <td>{{ $bank->swift_bic }}</td>
                    </tr>
                    @endif
                </tbody>
            </table>
            <a class="mr-3 btn btn-info" href="{{ route('user.transactions') }}">{{__('View Transaction')}}</a>
            <button type="submit" name="action" value="cancel" class="btn btn-cancel btn-danger-alt payment-cancel-btn payment-btn">{{__('Cancel Order')}}</button>

            <div class="gaps-2x"></div>
        </form>
        <div class="gaps-2x"></div>
        <div class="note note-md note-danger note-plane">
            <em class="fas fa-info-circle"></em> 
            <p>{{__('Use this transaction id')}} (#{{ $transaction->tnx_id }}) {{__('as reference. Make this payment within 24 hours, If we will not get this payment within 24 hours, then we will cancel this transaction.')}}</p>
        </div>
    </div>
</div>
<script type="text/javascript">
    (function($) {
        var $_p_form = $('form#payment-confirm');
        if ($_p_form.length > 0) {
            purchase_form_submit($_p_form);
        }
        $('.close-modal, .modal-close').on('click', function(e){
            e.preventDefault();
            var $link = $(this).attr('href');
            $(this).parents('.modal').modal('hide');
            window.location.reload();
        });
    })(jQuery);
</script>