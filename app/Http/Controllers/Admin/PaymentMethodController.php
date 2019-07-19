<?php

namespace App\Http\Controllers\Admin;

/**
 * Payment Method Controller
 *
 * Manage the Method Controller
 *
 * @package TokenLite
 * @author Softnio
 * @version 1.0.0
 */
use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use App\Models\Setting;
use IcoHandler;
use Illuminate\Http\Request;

class PaymentMethodController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     * @version 1.0.0
     * @since 1.0
     * @return void
     */
    public function index()
    {
        $payments = PaymentMethod::get_data();
        $gateway = PaymentMethod::Currency;
        return view('admin.payments-methods', compact('payments', 'gateway'));
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     * @throws \Throwable
     * @version 1.0.0
     * @since 1.0
     */
    public function show(Request $request)
    {
        $type = $request->input('req_type');

        if ($type == 'manage_currency') {
            $gateway = PaymentMethod::Currency;
            return view('modals.pm_manage', compact('gateway'))->render();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     * @version 1.0.0
     * @since 1.0
     */
    public function update(Request $request)
    {
        $type = $request->input('req_type');
        $ret['msg'] = 'info';
        $ret['message'] = __('messages.nothing');

        if ($type == 'manual') {
            $mnl_status = 'active';
            $old = PaymentMethod::get_single_data('manual');
            $eth = $old->secret->eth ? $old->secret->eth : null;
            $btc = $old->secret->btc ? $old->secret->btc : null;
            $ltc = $old->secret->ltc ? $old->secret->ltc : null;
            $bank = $old->secret->bank ? $old->secret->bank : null;
            $gateway_data = ['eth' => [
                'status' => isset($request->mnl_eth) ? 'active' : 'inactive',
                'address' => $request->input('eth_address') ? $request->input('eth_address') : $eth->address,
                'limit' => $request->input('eth_lmt') ? $request->input('eth_lmt') : $eth->limit,
                'price' => $request->input('eth_price') ? $request->input('eth_price') : $eth->price,
            ],

                'bank' => [
                    'status' => isset($request->mnl_bank) ? 'active' : 'inactive',
                    'bank_account_name' => $request->input('bank_account_name') ? $request->input('bank_account_name') : $bank->bank_account_name,
                    'bank_account_number' => $request->input('bank_account_number') ? $request->input('bank_account_number') : $bank->bank_account_number,
                    'bank_name' => $request->input('bank_name') ? $request->input('bank_name') : $bank->bank_name,
                    'routing_number' => $request->input('routing_number') ? $request->input('routing_number') : $bank->routing_number,
                    'iban' => $request->input('iban') ? $request->input('iban') : $bank->iban,
                    'swift_bic' => $request->input('swift_bic') ? $request->input('swift_bic') : $bank->swift_bic,
                ],

                'btc' => [
                    'status' => isset($request->mnl_btc) ? 'active' : 'inactive',
                    'address' => $request->input('btc_address') ? $request->input('btc_address') : $btc->address,
                ],
                'ltc' => [
                    'status' => isset($request->mnl_ltc) ? 'active' : 'inactive',
                    'address' => $request->input('ltc_address') ? $request->input('ltc_address') : $ltc->address,
                ],
            ];

            if (isset($request->mnl_eth)) {
                $is_valid = ['res' => IcoHandler::validate_address($request->input('eth_address'), 'eth'), 'name' => 'Ethereum'];
            } elseif (isset($request->mnl_btc)) {
                $is_valid = ['res' => IcoHandler::validate_address($request->input('btc_address'), 'btc'), 'name' => 'Bitcoin'];
            } elseif (isset($request->mnl_ltc)) {
                $is_valid = ['res' => IcoHandler::validate_address($request->input('ltc_address'), 'ltc'), 'name' => 'Litecoin'];
            } elseif (isset($request->mnl_bank)) {
                $is_valid = ['res' => true, 'name' => 'Bank'];
                $mnl_status = 'active';
            } else {
                $is_valid = ['res' => true, 'name' => ' '];
                $mnl_status = 'inactive';
            }
            if ($is_valid['res']) {
                // if address is valid then do it
                $mnl = PaymentMethod::where('payment_method', 'manual')->first();
                if (!$mnl) {
                    $mnl = new PaymentMethod();
                    $mnl->payment_method = 'manual';
                }
                $mnl->title = $request->input('mnl_title');
                $mnl->description = $request->input('mnl_details');
                $mnl->status = $mnl_status;
                $mnl->data = json_encode($gateway_data);
                $mnl->save();

                if ($mnl) {
                    $ret['msg'] = 'success';
                    $ret['message'] = __('messages.update.success', ['what' => 'Manual Payment Method']);
                } else {
                    $ret['msg'] = 'error';
                    $ret['message'] = __('messages.update.failed', ['what' => 'Manual Payment Method']);
                }
            } else {
                $ret['msg'] = 'warning';
                $ret['message'] = __('messages.invalid.address_is', ['is' => $is_valid['name']]);
            }
        }

        if ($type == 'currency_manage') {
            if (base_currency(true) != $request->input('base_currency')) {
                Setting::updateValue('token_default_in_userpanel', base_currency(true));
            }
            Setting::updateValue('site_base_currency', $request->input('base_currency'));
            Setting::updateValue('pm_exchange_method', $request->input('exchange_method'));
            $rate = PaymentMethod::automatic_rate(base_currency(true));
            $check_time = get_setting('pm_exchange_auto_lastcheck', now()->subMinutes(10));
            $current_time = now();

            foreach (PaymentMethod::Currency as $gt => $val) {
                $auto_currency = strtoupper($gt);
                if ($request->input('exchange_method') == 'automatic') {
                    Setting::updateValue('pm_automatic_rate_time', $request->input('automatic_rate_time'));
                    if ((strtotime($check_time)) <= strtotime($current_time)) {
                        Setting::updateValue('pm_exchange_auto_lastcheck', now());
                        $new_rate = (isset($rate->$auto_currency) ? $rate->$auto_currency : 1);
                        Setting::updateValue('pmc_auto_rate_' . strtolower($gt), $new_rate);
                    }
                }
                $val = $request->input('pmc_rate_' . strtolower($gt)) == null ? 1 : $request->input('pmc_rate_' . strtolower($gt));
                Setting::updateValue('pmc_rate_' . strtolower($gt), $val);
            }
            $ret['msg'] = 'success';
            $ret['message'] = __('messages.update.success', ['what' => 'Payment Currencies']);
        }

        if ($ret['msg'] == 'success') {
            $ret['link'] = route('admin.payments.setup');
        }

        $ret['link'] = null;
        if ($request->ajax()) {
            return response()->json($ret);
        }
        return back()->with([$ret['msg'] => $ret['message']]);
    }
}
