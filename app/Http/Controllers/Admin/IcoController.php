<?php

namespace App\Http\Controllers\Admin;

use Validator;
use App\Models\IcoMeta;
use App\Models\IcoStage;
use App\Models\Setting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class IcoController extends Controller
{
    public function index()
    {
        $stages = IcoStage::where('status', '!=', 'deleted')->get();
        return view('admin.ico-stage', compact('stages'));
    }
    public function edit_stage($id)
    {
        $ico = IcoStage::findOrFail($id);
        $prices = IcoMeta::get_data($ico->id, 'price_option');
        $bonuses = IcoMeta::get_data($ico->id, 'bonus_option');
        return view('admin.ico-stage-edit', compact('ico', 'prices', 'bonuses'));
    }

    /**
     * Display ICO Stage Settings
     *
     * @return \Illuminate\Http\Response
     * @version 1.0.0
     * @since 1.0
     * @return void
     */
    public function settings()
    {
        if (get_setting('actived_stage') != '') {
            $ico = IcoStage::where('status', '!=', 'deleted')->where('id', get_setting('actived_stage'))->first();
            if (!$ico) {
                $ico = IcoStage::where('status', '!=', 'deleted')->orderBy('id', 'DESC')->first();
            }
        } else {
            $ico = IcoStage::where('status', '!=', 'deleted')->first();
        }
        $prices = IcoMeta::get_data($ico->id, 'price_option');
        $bonuses = IcoMeta::get_data($ico->id, 'bonus_option');
        $pm_gateways = \App\Models\PaymentMethod::Currency;
        return view('admin.ico-setting', compact('ico', 'prices', 'bonuses', 'pm_gateways'));
    }

     /**
     * Active the Stage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * @version 1.0.1
     * @since 1.0
     * @return void
     */

    public function active(Request $request)
    {
        $ret['msg'] = 'info';
        $ret['message'] = __('messages.nothing');

        if ($request->input('id') && $request->input('type')) {
            try{
                $status = Setting::updateValue('actived_stage', $request->input('id'));
                $ret['msg'] = 'success';
                $ret['message'] = __('messages.stage_update', ['status' => 'Activated']);
            }catch(\Exception $e){
                $ret['msg'] = 'warning';
                $ret['message'] = __('messages.something_wrong');
            }
      
        }

        if ($request->ajax()) {
            return response()->json($ret);
        }
        return back()->with([$ret['msg'] => $ret['message']]);
    }

     /**
     * Pause the Stage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * @version 1.0.1
     * @since 1.0
     * @return void
     */
    public function pause(Request $request)
    {
        $ret['msg'] = 'info';
        $ret['message'] = __('messages.nothing');
        if ($request->input('id') && $request->input('type')) {
               try{
                $stage = IcoStage::findOrFail($request->input('id'));
                $stage->status = ($request->input('type') == 'resume_stage')?'active':'paused';
                $stage->save();

                $status = ($stage->status == 'active')?'Resume' : ($stage->status == 'paused')?'Paused' : "";
                $ret['msg'] = 'success';
                $ret['message'] = __('messages.stage_update', ['status' => $status]);
            }
            catch(\Exception $e){
                $ret['msg'] = 'warning';
                $ret['message'] = __('messages.something_wrong');
            }
        }

        if ($request->ajax()) {
            return response()->json($ret);
        }
        return back()->with([$ret['msg'] => $ret['message']]);
    }

    /**
     * Update the Stage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * @version 1.0.0
     * @since 1.0
     * @return void
     */
    public function update(Request $request)
    {
        $ret['msg'] = 'info';
        $ret['message'] = __('messages.nothing');

        # Validation
        $validator = Validator::make($request->all(), [
            'name'        => 'required|min:3',
            'start_date'   => 'required|date_format:"m/d/Y"|date',
            'end_date'     => 'required|date_format:"m/d/Y"|date|after:start_date',
            'base_price'   => 'required|numeric|gt:0',
            'total_tokens' => 'required|integer|gt:0',
            'min_purchase'   => 'required|numeric|min:5',
            'max_purchase'   => 'required|numeric|min:10',
            'soft_cap'   => 'nullable|numeric|min:10',
            'hard_cap'   => 'nullable|numeric|min:50|max:total_tokens',
            'display_mode'   => 'required|string',
        ]);
        if ($validator->fails()) {
            $msg = '';
            if ($validator->errors()->has('name')) {
                $msg = $validator->errors()->first();
            } elseif ($validator->errors()->has('start_date')) {
                $msg = $validator->errors()->first();
            } elseif ($validator->errors()->has('end_date')) {
                $msg = $validator->errors()->first();
            } elseif ($validator->errors()->has('total_tokens')) {
                $msg = $validator->errors()->first();
            } elseif ($validator->errors()->has('base_price')) {
                $msg = $validator->errors()->first();
            } elseif ($validator->errors()->has('display_mode')) {
                $msg = $validator->errors()->first();
            } elseif ($validator->errors()->has('min_purchase')) {
                $msg = $validator->errors()->first();
            } elseif ($validator->errors()->has('max_purchase')) {
                $msg = $validator->errors()->first();
            } elseif ($validator->errors()->has('soft_cap')) {
                $msg = $validator->errors()->first();
            } elseif ($validator->errors()->has('hard_cap')) {
                $msg = $validator->errors()->first();
            } else {
                $msg = __('messages.something_wrong');
            }

            $ret['msg'] = 'warning';
            $ret['message'] = $msg;
            return response()->json($ret);
        } else {
            $id = $request->input('ico_id');
            $ico = IcoStage::find($id);
            if ($ico == null) {
                $ico = new IcoStage();
            }
            if ($ico) {
                $re_start_date = ($request->input('start_date')) ? $request->input('start_date') : def_datetime('date');
                $re_start_time = ($request->input('start_time')) ? $request->input('start_time') : def_datetime('time');

                $re_end_date = ($request->input('end_date')) ? $request->input('end_date') : def_datetime('date');
                $re_end_time = ($request->input('end_time')) ? $request->input('end_time') : def_datetime('time_e');

                $start_date = _date($re_start_date.' '.$re_start_time, 'Y-m-d H:i:s');
                $end_date = _date($re_end_date.' '.$re_end_time, 'Y-m-d H:i:s');
                // Update or Create
                $ico->name              = $request->input('name');
                $ico->start_date        = $start_date;
                $ico->end_date          = $end_date;
                $ico->total_tokens      = (int)$request->input('total_tokens'); // Disable to change total tokens, to change need to deep more.
                $ico->base_price        = (double)$request->input('base_price');
                $ico->min_purchase      = (int)$request->input('min_purchase');
                $ico->max_purchase      = (int)$request->input('max_purchase');
                $ico->soft_cap          = (int)$request->input('soft_cap');
                $ico->hard_cap          = (int)$request->input('hard_cap');
                $ico->display_mode      = $request->input('display_mode');
                
                $ico->status            = ($request->input('sale_pause')==NULL)?'paused':'active';

                $ret['ico'] = $ico;
                //check validity
                $save = $ico->save();
                if ($save) {
                    $ret['msg'] = 'success';
                    $ret['message'] = __('messages.update.success', ['what' => 'ICO Stage']);
                } else {
                    $ret['msg'] = 'danger';
                    $ret['message'] = __('messages.update.failed', ['what' => 'ICO Stage']);
                }
            } else {
                $ret['msg'] = 'warning';
                $ret['message'] = __('messages.errors');
            }
        }

        if ($request->ajax()) {
            return response()->json($ret);
        }
        return back()->with([$ret['msg'] => $ret['message']]);
    }

    /**
     * Update the Stage Options.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * @version 1.0.1
     * @since 1.0
     * @return void
     */
    public function update_options(Request $request)
    {
        $ret['msg'] = 'info';
        $ret['message'] = __('messages.nothing');
        $type = $request->input('req_type');
        $stage = IcoStage::find($request->input('ico_id'));
        // Update ICO Price Options
        if ($type == 'price_option') {
            $price_data = [];
            for ($i=1; $i <= 3; $i++) {

                $in = $request->input('ptire_'.$i);
                $price = (double)$request->input('ptire_'.$i.'_token_price');
                $min_purchase = (int)$request->input('ptire_'.$i.'_min_purchase');
                $start_date = $request->input('ptire_'.$i.'_start_date') ? $request->input('ptire_'.$i.'_start_date') : def_datetime('date');
                $start_time = $request->input('ptire_'.$i.'_start_time') ? $request->input('ptire_'.$i.'_start_time') : def_datetime('time_s');
                $end_date = $request->input('ptire_'.$i.'_end_date') ? $request->input('ptire_'.$i.'_end_date') : def_datetime('date');
                $end_time = $request->input('ptire_'.$i.'_end_time') ? $request->input('ptire_'.$i.'_end_time') : def_datetime('time_e');

                if($in && $price <= 0) {
                    $ret['msg'] = 'warning';
                    $ret['message'] = 'Token price should be grater than 0. (In Tire '.$i.')';
                    return response()->json($ret);
                }elseif($start_date.' '.$start_time >= $end_date.' '.$end_time) {
                    $ret['msg'] = 'warning';
                    $ret['message'] = 'Start date can not be equal or greater than end date. (In Tire '.$i.')';
                    return response()->json($ret);
                } elseif ($min_purchase > $stage->max_purchase) {
                    $ret['msg'] = 'warning';
                    $ret['message'] = 'Min purchase must be greater then ICO Stage max purchase. (In Tire '.$i.')';
                    return response()->json($ret);
                }elseif ($in && $min_purchase <= 0) {
                    $ret['msg'] = 'warning';
                    $ret['message'] = 'Min purchase should be grater than 0 (In Tire '.$i.')';
                    return response()->json($ret);
                }
                $price_data['tire_'.$i] = [
                    'price' => $price ? $price : 0,
                    'min_purchase' => $min_purchase ? $min_purchase : 0,
                    'start_date' => _date($start_date.' '.$start_time, 'Y-m-d H:i:s'),
                    'end_date' => _date($end_date.' '.$end_time, 'Y-m-d H:i:s'),
                    'status' => ($in ? 1 : 0)
                ];
            }
            $json_data = json_encode($price_data);
            $save = IcoMeta::UpdateOrCreate(['stage_id' => $request->input('ico_id'), 'option_name' => 'price_option'], [
                'stage_id' => $request->input('ico_id'),
                'option_name' => 'price_option',
                'option_value' => $json_data,
                'status' => 1
            ]);
            if ($save) {
                $ret['msg'] = 'success';
                $ret['message'] = __('messages.update.success', ['what'=>'Stage Price Option']);
            } else {
                $ret['msg'] = 'error';
                $ret['message'] = __('messages.update.failed', ['what'=>'Stage Price Option']);
            }
        }
        // Update ICO Bonus Options
        if ($type == 'bonus_option') {
            $bonus_data = $bamount = [];

            # Entire Code execute here
            $start_date = $request->input('bb_start_date') ? $request->input('bb_start_date') : def_datetime('date');
            $start_time = $request->input('bb_start_time') ? $request->input('bb_start_time') : def_datetime('time_s');
            $end_date = $request->input('bb_end_date') ? $request->input('bb_end_date') : def_datetime('date');
            $end_time = $request->input('bb_end_time') ? $request->input('bb_end_time') : def_datetime('time_e');

            if (strtotime($start_date.' '.$start_time) >= strtotime($end_date.' '.$end_time)) {
                $ret['msg'] = 'warning';
                $ret['message'] = 'Start date can not be equal or greater than end date. (In Base Tire)';
                return response()->json($ret);
            }elseif ($request->input('bb_amount') < 0) {
                $ret['msg'] = 'warning';
                $ret['message'] = 'Base bonus amount can not be less than 0 (In Tire '.$i.')';
                return response()->json($ret);
            }
            $bonus_data['base'] = [
                'amount' => $request->input('bb_amount') ? (int)$request->input('bb_amount') : 0,
                'start_date' => _date($start_date.' '.$start_time, 'Y-m-d H:i:s'),
                'end_date' => _date($end_date.' '.$end_time, 'Y-m-d H:i:s'),
                'status' => ($request->input('bb_amount') >= 1 ? 1 : 0)
            ];
            for ($i=1; $i <= 3; $i++) {
                if ($request->input('ba_amount_'.$i) < 0) {
                    $ret['msg'] = 'warning';
                    $ret['message'] = 'Amount bonus can not be less than 0 (In Tire '.$i.')';
                    return response()->json($ret);
                }

                $bamount['tire_'.$i] = [
                    'amount' => $request->input('ba_amount_'.$i) ? (int)$request->input('ba_amount_'.$i) : '',
                    'token' => $request->input('ba_token_'.$i) ? (int)$request->input('ba_token_'.$i) : ''
                ];
            }
            $bamount['status'] = $request->input('bonus_amount') ? 1 : 0;
            $bonus_data['bonus_amount'] = $bamount;

            $json_data = json_encode($bonus_data);
            $save = IcoMeta::UpdateOrCreate(['stage_id' => $request->input('ico_id'), 'option_name' => 'bonus_option'], [
                'stage_id' => $request->input('ico_id'),
                'option_name' => 'bonus_option',
                'option_value' => $json_data,
                'status' => 1
            ]);
            if ($save) {
                $ret['msg'] = 'success';
                $ret['message'] = __('messages.update.success', ['what'=>'Stage bonus Option']);
            } else {
                $ret['msg'] = 'error';
                $ret['message'] = __('messages.update.failed', ['what'=>'Stage bonus Option']);
            }
        }


        if ($request->ajax()) {
            return response()->json($ret);
        }
        return back()->with([$ret['msg'] => $ret['message']]);
    }

    /**
     * Update ICO Stage Settings
     *
     * @return \Illuminate\Http\Response
     * @version 1.0.0
     * @since 1.0
     * @return void
     */
    public function update_settings(Request $request)
    {
        $type = $request->input('req_type');
        $ret['msg'] = 'info';
        $ret['message'] = __('messages.nothing');

        if ($type == 'token_details') {
            $validator = Validator::make($request->all(), [
                'token_name' => 'required|min:4',
                'token_symbol' => 'required|min:2'
            ]);

            if ($validator->fails()) {
                $msg = '';
                if ($validator->errors()->has('token_name')) {
                    $msg = $validator->errors()->first();
                } elseif ($validator->errors()->has('token_symbol')) {
                    $msg = $validator->errors()->first();
                } else {
                    $msg = __('messages.something_wrong');
                }

                $ret['msg'] = 'warning';
                $ret['message'] = $msg;
            } else {
                if ($request->input('token_name') != null) {
                    Setting::updateValue('token_name', $request->input('token_name'));
                }
                if ($request->input('token_symbol') != null) {
                    Setting::updateValue('token_symbol', $request->input('token_symbol'));
                }
                if ($request->input('token_decimal_min') != null) {
                    Setting::updateValue('token_decimal_min', $request->input('token_decimal_min'));
                }
                if ($request->input('token_decimal_max') != null) {
                    Setting::updateValue('token_decimal_max', $request->input('token_decimal_max'));
                }

                $ret['msg'] = 'success';
                $ret['message'] = __('messages.update.success', ['what' => 'Toekn Details Settings']);
            }
        }

        if ($type == 'token_purchase') {
            $default = 'token_purchase_'.strtolower($request->input('token_default_method'));
            
            # Checkbox value set
            $token_price = isset($request->token_price_show) ? 1 : 0;
            $before_kyc = isset($request->token_before_kyc) ? 1 : 0;
            $purchase_usd = isset($request->token_purchase_usd) ? 1 : 0;
            $purchase_btc = isset($request->token_purchase_btc) ? 1 : 0;
            $purchase_eth = isset($request->token_purchase_eth) ? 1 : 0;
            $purchase_ltc = isset($request->token_purchase_ltc) ? 1 : 0;

            // if($request->input('token_default_method') != ''){
            //     $dm = $request->input('token_default_method');
            //     if($request->input('token_purchase_'.strtolower($dm)) == NULL ){
            //         $ret['msg'] = 'warning';
            //         $ret['message'] = 'You need to save default method as active currency!';
            //         return $ret;
            //     }
            // }
            Setting::updateValue('token_price_show', $token_price);
            Setting::updateValue('token_before_kyc', $before_kyc);
            Setting::updateValue('token_purchase_usd', $purchase_usd);
            Setting::updateValue('token_purchase_usd', $purchase_usd);
            Setting::updateValue('token_purchase_btc', $purchase_btc);
            Setting::updateValue('token_purchase_eth', $purchase_eth);
            Setting::updateValue('token_purchase_ltc', $purchase_ltc);
            Setting::updateValue($default, 1);

            if ($request->input('token_default_method') != '') {
                Setting::updateValue('token_default_method', $request->input('token_default_method'));
            }
            if ($request->input('token_default_in_userpanel') != '') {
                Setting::updateValue('token_default_in_userpanel', $request->input('token_default_in_userpanel'));
            }

            $ret['msg'] = 'success';
            $ret['message'] = __('messages.update.success', ['what' => 'Purchase Token Settings']);
        }


        if ($request->ajax()) {
            return response()->json($ret);
        }
        return back()->with([$ret['msg'] => $ret['message']]);
    }
}
