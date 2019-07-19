<?php

namespace App\Http\Controllers\Admin;

/**
 * Transactions Controller
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
use Illuminate\Http\Request;
use Validator;

class TransactionController extends Controller
{
    public function index(Request $request, $status = '')
    {
        $trnxs = Transaction::where('status', '!=', 'deleted')->where('status', '!=', 'new')->orderBy('id', 'DESC')->get();
        $pmethods = PaymentMethod::where('status', 'active')->get();
        $stages = IcoStage::where('status', 'active')->get();
        $pm_currency = PaymentMethod::Currency;
        $users = User::where('status', 'active')->where('email_verified_at', '!=', 'null')->where('role', '!=', 'admin')->get();
        return view('admin.transactions', compact('trnxs', 'users', 'stages', 'pmethods', 'pm_currency'));
    }

    /**
     * Display the specified resource.
     *
     * @param string $trnx_id
     * @return \Illuminate\Http\Response
     * @throws \Throwable
     * @version 1.0.0
     * @since 1.0
     */
    public function show($trnx_id = '')
    {
        if ($trnx_id == '') {
            return __('messages.wrong');
        } else {
            $trnx = Transaction::FindOrFail($trnx_id);
            return view('admin.trnx_details', compact('trnx'))->render();
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
        if (version_compare(phpversion(), '7.1', '>=')) {
            ini_set('precision', 17);
            ini_set('serialize_precision', -1);
        }

        $type = $request->input('req_type');
        $id = $request->input('tnx_id');
        $ret['msg'] = 'info';
        $ret['message'] = __('messages.nothing');
        if ($id != null) {
            $trnx = Transaction::find($id);
        }

        if ($type == 'canceled') {
            if ($trnx) {
                $old_status = $trnx->status;
                if ($old_status != 'deleted') {
                    if ($old_status == 'approved') {
                        $ret['msg'] = 'info';
                        $ret['message'] = __('messages.trnx.admin.already_approved');
                    } else {
                        $trnx->status = 'canceled';
                        $trnx->checked_by = json_encode(['name' => Auth::user()->name, 'id' => Auth::id()]);
                        $trnx->checked_time = date('Y-m-d H:i:s');
                        $trnx->save();

                        if ($old_status == 'pending' || $old_status == 'onhold') {
                            IcoStage::token_add_to_account($trnx, 'sub');
                        }
                        $when = now()->addMinutes(1);

                        try {
                            $trnx->tnxUser->notify((new TnxStatus($trnx, 'canceled-user')));
                            if (get_emailt('order-canceled-admin', 'notify') == 1) {
                                notify_admin($trnx, 'canceled-admin');
                            }

                            $ret['msg'] = 'success';
                            $ret['message'] = __('messages.trnx.admin.canceled');
                        } catch (\Exception $e) {
                            $ret['msg'] = 'warning';
                            $ret['message'] = __('messages.trnx.admin.canceled').' '.__('messages.email.token_update');
                            ;
                        }

                        // Notification::send($trnx->tnxUser, new TnxStatus($trnx));
                    }
                } else {
                    $ret['msg'] = 'warning';
                    $ret['message'] = __('messages.trnx.admin.already_deleted');
                }
            }
        }

        if ($type == 'deleted') {
            if ($trnx) {
                $old_status = $trnx->status;
                if ($old_status == 'canceled') {
                    $trnx->status = 'deleted';
                    $trnx->checked_by = json_encode(['name' => Auth::user()->name, 'id' => Auth::id()]);
                    $trnx->checked_time = date('Y-m-d H:i:s');
                    $trnx->save();

                    $ret['msg'] = 'success';
                    $ret['message'] = __('messages.trnx.admin.deleted');
                } else {
                    $ret['msg'] = 'warning';
                    $ret['message'] = 'Canceled the transaction first!';
                }
            }
        }

        if ($type == 'approved') {
            $validator = Validator::make($request->all(), [
                'amount' => 'gt:0',
            ]);

            if ($validator->fails()) {
                if ($validator->errors()->has('amount')) {
                    $msg = $validator->errors()->first();
                } else {
                    $msg = __('messages.something_wrong');
                }

                $ret['msg'] = 'warning';
                $ret['message'] = $msg;
            } else {
                $chk_adjust = $request->input('chk_adjust');
                $receive_amount = round($request->input('amount'), min_decimal());
                $adjust_token = round($request->input('adjusted_token'), min_decimal());
                $token = round($request->input('token'), min_decimal());
                $base_bonus = round($request->input('base_bonus'), min_decimal());
                $token_bonus = round($request->input('token_bonus'), min_decimal());
                if ($trnx) {
                    $old_status = $trnx->status;
                    $old_tokens = $trnx->total_tokens;
                    $old_base_amount = $trnx->base_amount;

                    if ($old_status != 'deleted') {
                        if ($chk_adjust == 1) {
                            $trnx->tokens = $token;
                            $trnx->base_amount = $token * $trnx->base_currency_rate;
                            $trnx->total_bonus = $base_bonus + $token_bonus;
                            $trnx->bonus_on_base = $base_bonus;
                            $trnx->bonus_on_token = $token_bonus;
                            $trnx->total_tokens = $adjust_token;
                            $trnx->amount = $receive_amount;

                            if ($old_status != 'canceled') {
                                $adjust_stage_token = $old_tokens - $trnx->total_tokens;
                                $adjust_base_amount = $old_base_amount - $trnx->base_amount;

                                if ($adjust_stage_token < 0) {
                                    IcoStage::token_adjust_to_stage($trnx, abs($adjust_stage_token), abs($adjust_base_amount), 'add');
                                } elseif ($adjust_stage_token > 0) {
                                    IcoStage::token_adjust_to_stage($trnx, abs($adjust_stage_token), abs($adjust_base_amount), 'sub');
                                }
                            }
                        }

                        $trnx->receive_currency = $trnx->currency;
                        $trnx->receive_amount = $receive_amount;
                        $trnx->status = 'approved';
                        $trnx->checked_by = json_encode(['name' => Auth::user()->name, 'id' => Auth::id()]);
                        $trnx->checked_time = date('Y-m-d H:i:s');
                        $trnx->save();
                    

                        if ($old_status == 'canceled') {
                            IcoStage::token_add_to_account($trnx, 'add');
                        }

                        IcoStage::token_add_to_account($trnx, null, 'add');

                        try {
                            $trnx->tnxUser->notify((new TnxStatus($trnx, 'successful-user')));
                            $ret['msg'] = 'success';
                            $ret['message'] = __('messages.trnx.admin.approved');
                        } catch (\Exception $e) {
                            $ret['msg'] = 'warning';
                            $ret['message'] = __('messages.trnx.admin.approved').' '.__('messages.email.token_update');
                        }
                    } else {
                        $ret['msg'] = 'warning';
                        $ret['message'] = __('messages.trnx.admin.already_deleted');
                    }
                }
            }
        }

        $ret['data'] = $trnx;
        if ($request->ajax()) {
            return response()->json($ret);
        }
        return back()->with([$ret['msg'] => $ret['message']]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * @version 1.0.0
     * @since 1.0
     * @return void
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
            'total_tokens' => 'required|integer|min:1',
        ], [
            'total_tokens.required' => "Token amount is required!.",
        ]);

        if ($validator->fails()) {
            if ($validator->errors()->has('total_tokens')) {
                $msg = $validator->errors()->first();
            } else {
                $msg = __('messages.something_wrong');
            }

            $ret['msg'] = 'warning';
            $ret['message'] = $msg;
        } else {
            $tc = new TC();
            $token = $request->input('total_tokens');
            $currency = strtolower($request->input('currency'));
            $currency_rate = Setting::exchange_rate($tc->get_current_price(), $currency);
            $base_currency = strtolower(base_currency());
            $base_currency_rate = Setting::exchange_rate($tc->get_current_price(), $base_currency);
            $all_currency_rate = json_encode(Setting::exchange_rate($tc->get_current_price()));
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
                $_payment_to = $request->input('payment_method', 'manual') == 'manual' ? (get_pm('manual')->$currency->address != null ? get_pm('manual')->$currency->address : '') : $request->input('payment_method');
            } else {
                $_payment_to = get_b_data('manual')->bank_name != null ? get_b_data('manual')->bank_name : '';
            }

            $save_data = [
                'created_at' => Carbon::now()->toDateTimeString(),
                'tnx_id' => set_id(rand(100, 999), 'trnx'),
                'tnx_type' => $request->input('type'),
                'tnx_time' => _date($request->input('tnx_date', now()), "Y-m-d H:i:s"),
                'tokens' => $trnx_data['token'],
                'bonus_on_base' => $trnx_data['bonus_on_base'],
                'bonus_on_token' => $trnx_data['bonus_on_token'],
                'total_bonus' => $trnx_data['total_bonus'],
                'total_tokens' => $trnx_data['total_tokens'],
                'stage' => (int) $request->input('stage', active_stage()->id),
                'user' => $request->input('user'),
                'amount' => $request->input('amount') != '' ? $request->input('amount') : $trnx_data['amount'],
                'base_amount' => $trnx_data['base_price'],
                'base_currency' => $base_currency,
                'base_currency_rate' => $base_currency_rate,
                'currency' => $currency,
                'currency_rate' => $currency_rate,
                'all_currency_rate' => $all_currency_rate,
                'payment_method' => $request->input('payment_method', 'manual'),
                'payment_to' => $_payment_to,
                'payment_id' => rand(1000, 9999),
                'details' => 'Tokens Purchase',
                'status' => 'onhold',
            ];
            $iid = Transaction::insertGetId($save_data);

            if ($iid != null) {
                $ret['msg'] = 'info';
                $ret['message'] = __('messages.trnx.manual.success');

                $address = $request->input('wallet_address');
                $transaction = Transaction::where('id', $iid)->first();
                $transaction->tnx_id = set_id($iid, 'trnx');
                $transaction->wallet_address = $address;
                $transaction->extra = json_encode(['address' => $address]);
                $transaction->status = 'approved';
                $transaction->save();

                IcoStage::token_add_to_account($transaction, 'add');

                $transaction->checked_by = json_encode(['name' => Auth::user()->name, 'id' => Auth::id()]);

                $transaction->added_by = set_added_by(Auth::id(), Auth::user()->role);
                $transaction->checked_time = now();
                $transaction->save();
                // Start adding
                IcoStage::token_add_to_account($transaction, '', 'add');

                $ret['link'] = route('admin.transactions');
                $ret['msg'] = 'success';
                $ret['message'] = __('messages.token.success');
            } else {
                $ret['msg'] = 'error';
                $ret['message'] = __('messages.token.failed');
                Transaction::where('id', $iid)->delete();
            }
        }

        if ($request->ajax()) {
            return response()->json($ret);
        }
        return back()->with([$ret['msg'] => $ret['message']]);
    }

    /**
    * Adjustment modal function for token verified.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    * @version 1.0.0
    * @since 1.0
    * @return void
    */

    public function adjustment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tnx_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            if ($validator->errors()->has('tnx_id')) {
                $msg = $validator->errors()->first();
            } else {
                $msg = __('messages.something_wrong');
            }

            $ret['msg'] = 'warning';
            $ret['message'] = $msg;
        } else {
            $trnx = Transaction::findOrFail($request->tnx_id);
            $ret['modal'] = view('modals.adjustment_token', compact('trnx'))->render();
        }
        if ($request->ajax()) {
            return response()->json($ret);
        }
        return back()->with([$ret['msg'] => $ret['message']]);
    }
}
