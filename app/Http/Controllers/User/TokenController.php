<?php

namespace App\Http\Controllers\User;

/**
 * Token Controller
 *
 *
 * @package TokenLite
 * @author Softnio
 * @version 1.0.0
 */
use App\Helpers\TokenCalculate as TC;
use App\Http\Controllers\Controller;
use App\Models\IcoStage;
use App\Models\PaymentMethod;
use App\Models\Setting;
use App\Models\Transaction;
use App\Models\User;
use App\Notifications\TnxStatus;
use Auth;
use Carbon\Carbon;
use IcoHandler;
use Illuminate\Http\Request;
use Validator;

class TokenController extends Controller
{
    /**
     * Create a class instance
     *
     * @return \Illuminate\Http\Middleware\StageCheck
     */
    public function __construct()
    {
        $this->middleware('stage');
    }
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
        if (token('before_kyc') == '1') {
            $check = User::find(Auth::id());
            if ($check && !isset($check->kyc_info->status)) {
                return redirect(route('user.kyc'))->with(['warning' => __('messages.kyc.mandatory')]);
            } else {
                if ($check->kyc_info->status != 'approved') {
                    return redirect(route('user.kyc.application'))->with(['warning' => __('messages.kyc.mandatory')]);
                }
            }
        }

        $stage = active_stage();
        $tc = new TC();
        $currencies = Setting::active_currency();
        $currencies['base'] = base_currency();
        $bonus = $tc->get_current_bonus(null);
        $bonus_amount = $tc->get_current_bonus('amount');
        $price = Setting::exchange_rate($tc->get_current_price());
        $minimum = $tc->get_current_price('min');
        $active_bonus = $tc->get_current_bonus('active');
        $pm_currency = PaymentMethod::Currency;
        $pm_active = PaymentMethod::where('status', 'active')->get();

        $contribution = Transaction::user_contribution();

       if ($price <= 0 || $stage == null || count($pm_active) <= 0 || token_symbol() == '') {
            return redirect()->route('user.home')->with(['info' => __('messages.ico_not_setup')]);
        }

        return view(
            'user.token',
            compact('stage', 'currencies', 'bonus', 'bonus_amount', 'price', 'minimum', 'active_bonus', 'pm_currency', 'contribution')
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     * @version 1.0.0
     * @since 1.0
     * @return void
     * @throws \Throwable
     */
    public function store(Request $request)
    {
        if (version_compare(phpversion(), '7.1', '>=')) {
            ini_set('precision', 17);
            ini_set('serialize_precision', -1);
        }

        $ret['msg'] = 'info';
        $ret['message'] = __('messages.nothing');
        $validator = Validator::make($request->all(), [
            'agree' => 'required',
            'pp_token' => 'required|integer|min:1',
            'pp_currency' => 'required',
        ], [
            'agree.required' => __('messages.agree'),
            'pp_currency.required' => __('messages.trnx.require_currency'),
            'pp_token.required' => __('messages.trnx.require_token'),
            'pp_token.min' => __('messages.trnx.minimum_token'),
        ]);

        if ($validator->fails()) {
            if ($validator->errors()->has('agree')) {
                $msg = $validator->errors()->first();
            } elseif ($validator->errors()->has('pp_token')) {
                $msg = $validator->errors()->first();
            } elseif ($validator->errors()->has('pp_currency')) {
                $msg = $validator->errors()->first();
            } else {
                $msg = __('messages.something_wrong');
            }

            $ret['msg'] = 'warning';
            $ret['message'] = $msg;
        } else {
            $tc = new TC();
            $token = $request->input('pp_token');
            $currency = strtolower($request->input('pp_currency'));
            $currency_rate = Setting::exchange_rate($tc->get_current_price(), $currency);
            $all_currency_rate = json_encode(Setting::exchange_rate($tc->get_current_price()));
            $base_currency = strtolower(base_currency());
            $base_currency_rate = Setting::exchange_rate($tc->get_current_price(), $base_currency);
            $trnx_data = [
                'token' => round($token, min_decimal()),
                'bonus_on_base' => $tc->calc_token($token, 'bonus-base'),
                'bonus_on_token' => $tc->calc_token($token, 'bonus-token'),
                'total_bonus' => $tc->calc_token($token, 'bonus'),
                'total_tokens' => $tc->calc_token($token),
                'base_price' => $tc->calc_token($token, 'price')->base,
                'amount' => round($tc->calc_token($token, 'price')->$currency, max_decimal()),
            ];

            if (strtolower($currency) != 'usd') {
                $address = get_pm('manual')->$currency->address != null ? get_pm('manual')->$currency->address : '';
            } else {
                $address = get_b_data('manual')->bank_name != null ? get_b_data('manual')->bank_name : '';
            }

            $save_data = [
                'created_at' => Carbon::now()->toDateTimeString(),
                'tnx_id' => set_id(rand(100, 999), 'trnx'),
                'tnx_type' => 'purchase',
                'tnx_time' => Carbon::now()->toDateTimeString(),
                'tokens' => $trnx_data['token'],
                'bonus_on_base' => $trnx_data['bonus_on_base'],
                'bonus_on_token' => $trnx_data['bonus_on_token'],
                'total_bonus' => $trnx_data['total_bonus'],
                'total_tokens' => $trnx_data['total_tokens'],
                'stage' => active_stage()->id,
                'user' => Auth::id(),
                'amount' => $trnx_data['amount'],
                'base_amount' => $trnx_data['base_price'],
                'base_currency' => $base_currency,
                'base_currency_rate' => $base_currency_rate,
                'currency' => $currency,
                'currency_rate' => $currency_rate,
                'all_currency_rate' => $all_currency_rate,
                'payment_method' => 'manual',
                'payment_to' => $address,
                'added_by' => set_added_by('00'),
                'details' => __('messages.trnx.purchase_token'),
                'status' => 'pending',
            ];
            $iid = Transaction::insertGetId($save_data);

            if ($iid != null) {
                $ret['trnx'] = 'true';
                $ret['msg'] = 'info';
                $ret['message'] = __('messages.trnx.manual.success');
                $transaction = Transaction::where('id', $iid)->first();
                $transaction->tnx_id = set_id($iid, 'trnx');
                $transaction->save();

                IcoStage::token_add_to_account($transaction, 'add');
                try {
                    $transaction->tnxUser->notify((new TnxStatus($transaction, 'submit-user')));

                    if (get_emailt('order-placed-admin', 'notify') == 1) {
                        notify_admin($transaction, 'placed-admin');
                    }
                } catch (\Exception $e) {
                    $ret['error'] = $e->getMessage();
                }
                

                if (strtolower($currency) == 'usd') {
                    $ret['modal'] = view('modals.payment_bank', compact('transaction'))->render();
                } else {
                    $ret['modal'] = view('modals.payment-confirm', compact('transaction'))->render();
                }
            } else {
                $ret['msg'] = 'error';
                $ret['message'] = __('messages.trnx.manual.failed');
                Transaction::where('id', $iid)->delete();
            }
        }

        if ($request->ajax()) {
            return response()->json($ret);
        }
        return back()->with([$ret['msg'] => $ret['message']]);
    }

    /**
     * Update token status
     *
     * @version 1.0.0
     * @since 1.0
     * @return void
     */
    public function update(Request $request)
    {
        # code...
        $ret['msg'] = 'info';
        $ret['message'] = __('messages.nothing');
        if ($request->input('action') == 'confirm') {
            $validator = Validator::make($request->all(), [
                'trnx_id' => 'required',
                'payment_address' => 'required',
            ], [
                'trnx_id.required' => __('messages.trnx.notfound'),
                'payment_address.required' => __('messages.invalid.address'),
            ]);
        } else {
            $validator = Validator::make($request->all(), [
                'trnx_id' => 'required',
            ], [
                'trnx_id.required' => __('messages.trnx.notfound'),
            ]);
        }

        if ($validator->fails()) {
            if ($validator->errors()->has('trnx_id')) {
                $msg = $validator->errors()->first();
            } elseif ($validator->errors()->has('payment_address')) {
                $msg = $validator->errors()->first();
            } else {
                $msg = __('messages.something_wrong');
            }

            $ret['msg'] = 'warning';
            $ret['message'] = $msg;
        } else {
            $action = $request->input('action');
            $address = $request->input('payment_address');
            $tnxns = Transaction::where('id', $request->input('trnx_id'))->first();
            $_old_status = $tnxns->status;
            $is_valid = IcoHandler::validate_address($address, $tnxns->currency);
            if ($_old_status == 'canceled' || $_old_status == 'deleted') {
                $ret['msg'] = 'warning';
                $ret['message'] = "Your transaction is already " . $_old_status . ". Sorry, we're unable to proceed the transaction.";
                if ($action != 'confirm') {
                    $ret['modal'] = view('modals.payment.canceled', compact('tnxns'))->render();
                } else {
                    $ret['modal'] = view('modals.payment.failed', compact('tnxns'))->render();
                }
            } else {
                if ($action == 'confirm' && $is_valid == true && $address != null) {
                    $tnxns->payment_id = $address;
                    $tnxns->wallet_address = $address;
                    $tnxns->extra = json_encode(['address' => $address]);
                    $tnxns->status = 'onhold';
                    $tnxns->save();
                    if ($tnxns) {
                        $ret['msg'] = 'info';
                        $ret['message'] = __('messages.trnx.reviewing');
                        $ret['modal'] = view('modals.payment-review', compact('tnxns'))->render();
                    }
                } else {
                    $ret['msg'] = 'warning';
                    $ret['message'] = __('messages.invalid.address');
                }
                if ($action == 'cancel') {
                    $tnxns->status = 'canceled';
                    $tnxns->save();

                    IcoStage::token_add_to_account($tnxns, 'sub');
                    if ($tnxns) {
                        try {
                            if (get_emailt('order-canceled-admin', 'notify') == 1) {
                                notify_admin($tnxns, 'canceled-admin');
                            }
                        }catch(\Exception $e){
                             $ret['error'] = $e->getMessage();
                        }
                        $ret['msg'] = 'warning';
                        $ret['message'] = __('messages.trnx.canceled_own');
                        $ret['modal'] = view('modals.payment-canceled', compact('tnxns'))->render();
                    }
                }
            }
        }

        if ($request->ajax()) {
            return response()->json($ret);
        }
        return back()->with([$ret['msg'] => $ret['message']]);
    }

    /**
     * Access the confirm and count
     *
     * @version 1.0.0
     * @since 1.0
     * @return void
     * @throws \Throwable
     */
    public function access(Request $request)
    {
        $tc = new TC();

        $get = $request->input('req_type');
        $min = $tc->get_current_price('min');
        $currency = $request->input('currency');
        $token = $request->input('token_amount');
        $ret['modal'] = '<a href="#" class="modal-close" data-dismiss="modal"><em class="ti ti-close"></em></a><div class="tranx-popup"><h3>' . __('messages.trnx.wrong') . '</h3></div>';
        $_data = [];
        if ($token >= $min || $token != null) {
            $_data = (object) [
                'currency_rate' => Setting::exchange_rate($tc->get_current_price(), $currency),
                'token' => round($token, min_decimal()),
                'bonus_on_base' => $tc->calc_token($token, 'bonus-base'),
                'bonus_on_token' => $tc->calc_token($token, 'bonus-token'),
                'total_bonus' => $tc->calc_token($token, 'bonus'),
                'total_tokens' => $tc->calc_token($token),
                'base_price' => $tc->calc_token($token, 'price')->base,
                'amount' => round($tc->calc_token($token, 'price')->$currency, max_decimal()),
            ];
        }
        if ($this->check($token)) {
            if ($get == 'offline') {
                if ($token < $min || $token == null) {
                    $ret['opt'] = 'true';
                    $ret['modal'] = view('modals.payment.amount', compact('currency', 'get'))->render();
                } else {
                    $ret['opt'] = 'static';
                    $ret['modal'] = view('modals.payment-offline', compact('currency', 'token', '_data'))->render();
                }
            }

            if ($get == 'online') {
                if ($token < $min || $token == null) {
                    $ret['opt'] = 'true';
                    $ret['modal'] = view('modals.payment.amount', compact('currency', 'get'))->render();
                } else {
                    $ret['opt'] = 'static';
                    $ret['modal'] = view('modals.payment.online', compact('currency', 'token', '_data'))->render();
                }
            }
        } else {
            $msg = $this->check(0, 'err');
            $ret['modal'] = '<a href="#" class="modal-close" data-dismiss="modal"><em class="ti ti-close"></em></a><div class="tranx-popup"><h3>' . $msg . '!</h3></div>';
        }
        if ($request->ajax()) {
            return response()->json($ret);
        }
        return back()->with([$ret['msg'] => $ret['message']]);
    }

    /**
     * Check the state
     *
     * @version 1.0.0
     * @since 1.0
     * @return void
     */
    private function check($token, $extra = '')
    {
        $tc = new TC();
        $stg = active_stage();
        $min = $tc->get_current_price('min');
        $available_token = ($stg->total_tokens - $stg->sales_token);

        if ($extra == 'err') {
            if ($token >= $min && $token <= $stg->max_purchase) {
                if ($token >= $min && $token > $stg->max_purchase) {
                    return 'Maximum amount reached, You can purchase maximum ' . $stg->max_purchase . ' Token per contribution.';
                } else {
                    return 'You must purchase minimum ' . $min . ' Token';
                }
            } else {
                if ($available_token >= $token) {
                    return $available_token >= 500 ? $token . ' ' . token_symbol() . ' Token is not available.' : 'Available ' . $available_token . ' ' . token_symbol() . ' Token only, You can purchase less than ' . $available_token . ' tokens.';
                }
            }
        } else {
            if ($token >= $min && $token <= $stg->max_purchase) {
                if ($available_token >= $token) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }
    }
}
