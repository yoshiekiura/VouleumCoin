<?php

namespace App\Http\Controllers\Admin;
/**
 * ICO Stage Controller
 *
 *
 * @package TokenLite
 * @author Softnio
 * @version 1.0.0
 */
use App\Http\Controllers\Controller;
use App\Models\KYC;
use App\Models\Setting;
use App\Notifications\KycStatus;
use Auth;
use Illuminate\Http\Request;
use Validator;

class KycController extends Controller
{
    public function index(Request $request, $status = '')
    {
        $kycs = KYC::orderBy('created_at', 'desc')->get();
        return view('admin.kycs', compact('kycs'));
    }

    /**
     * Show the KYC Images
     *
     * @return \Illuminate\Http\Response
     * @version 1.0.0
     * @since 1.0
     * @return void
     */
    public function get_documents($id, $doc)
    {
        $filename = KYC::FindOrFail($id)->document;
        if ($doc == 2) {
            $filename = KYC::FindOrFail($id)->document2;
        }
        if ($doc == 3) {
            $filename = KYC::FindOrFail($id)->document3;
        }
        if ($filename !== null) {
            $path = storage_path('app/' . $filename);
            if (!file_exists($path)) {
                abort(404);
            }
            $file = \File::get($path);
            $type = \File::mimeType($path);
            $response = response($file, 200)->header("Content-Type", $type);

            return $response;
        } else {
            return abort(404);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     * @version 1.0.0
     * @since 1.0
     * @return void
     * @throws \Throwable
     */
    public function show($id = '', $type = '')
    {
        if ($type == 'kyc_details') {
            if ($id == '') {
                return __('messages.wrong');
            } else {
                $kyc = KYC::where('id', $id)->first();
                return view('admin.kyc_details', compact('kyc'))->render();
            }
        }
    }

    public function ajax_show(Request $request)
    {
        $type = $request->input('req_type');

        if ($type == 'kyc_settings') {
            return view('modals.kyc_settings')->render();
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

        if ($type == 'update_kyc_settings') {
            //validate data
            $validator = Validator::make($request->all(), [
                'req_type' => 'required',
            ]);
            if ($validator->fails()) {
                $msg = '';
                if ($validator->errors()->has('req_type')) {
                    $msg = $validator->errors()->first();
                } else {
                    $msg = __('messages.something_wrong');
                }

                $ret['msg'] = 'warning';
                $ret['message'] = $msg;
            } else {
                // Start Work
                $check = '';
                $i = 1;
                foreach (KYC::kyc_fields() as $kyc_field => $option) {
                    $new = $current = '';

                    $is_disable = ($kyc_field == 'kyc_firstname' || $kyc_field == 'kyc_email') ? 1 : 0;

                    $ignore = ['kyc_wallet_custom', 'kyc_wallet_opt'];

                    if (isset($request->$kyc_field) && !in_array($kyc_field, $ignore)) {
                        if (isset($request->$kyc_field)) {
                            $new = $request->$kyc_field;
                            if (is_array($request->$kyc_field)) {
                                $show = $req = 0;
                                foreach ($request->$kyc_field as $val) {
                                    if ($val == 'show') {
                                        $show = 1;
                                    }

                                    if ($val == 'req') {
                                        $req = 1;
                                    }
                                }
                                $new = array('show' => $show, 'req' => $req);
                            }
                        }

                        $current_val = get_setting($kyc_field);
                        if ($current_val != '') {
                            $current = $current_val;
                            if (is_json($current_val)) {
                                $current = json_decode($current_val, true);
                            }
                        } else {
                            // Load by default
                            $new_option = (is_array($option)) ? json_encode($option) : $option;
                            Setting::updateValue($kyc_field, $new_option);
                        }

                        if (is_array($current)) {
                            // update if array data
                            $ud_show = isset($new['show']) ? $new['show'] : $is_disable;
                            $ud_req = isset($new['req']) ? $new['req'] : $is_disable;

                            $update_value = json_encode(array('show' => $ud_show, 'req' => $ud_req));
                            Setting::updateValue($kyc_field, $update_value);
                        } else {
                            // update if non-array data
                            $value = isset($new) ? $new : 0;
                            Setting::updateValue($kyc_field, $value);
                        }
                        // $i++;
                    }
                }

                $wallet_opt = json_encode(array('wallet_opt' => $request->kyc_wallet_opt));
                Setting::updateValue('kyc_wallet_opt', $wallet_opt);
                $wallet_opt_custom = json_encode(array('cw_name' => $request->kyc_wallet_custom[0], 'cw_text' => $request->kyc_wallet_custom[1]));
                Setting::updateValue('kyc_wallet_custom', $wallet_opt_custom);

                $kyc_public = isset($request->kyc_public) ? 1 : 0;
                Setting::updateValue('kyc_public', $kyc_public);

                $kdi = isset($request->kyc_document_passport) ? 1 : 0;
                Setting::updateValue('kyc_document_passport', $kdi);

                $kdd = isset($request->kyc_document_nidcard) ? 1 : 0;
                Setting::updateValue('kyc_document_nidcard', $kdd);

                $kdp = isset($request->kyc_document_driving) ? 1 : 0;
                Setting::updateValue('kyc_document_driving', $kdp);

                $kbe = isset($request->kyc_before_email) ? 1 : 0;
                Setting::updateValue('kyc_before_email', $kbe);
                $tbk = isset($request->token_before_kyc) ? 1 : 0;
                Setting::updateValue('token_before_kyc', $tbk);

                $ret['msg'] = 'success';
                $ret['message'] = __('messages.update.success', ['what' => 'KYC Settings']);
                // End Work
            }
        }

        if ($type == 'update_kyc_status') {
            $id = $request->input('kyc_id');
            if ($id !== null) {
                $kyc = KYC::FindOrFail($id);
                $old_note = $kyc->notes != null ? $kyc->notes : '';
                $save_note = $request->input('notes') != '' ? str_replace("\n", "<br>", $request->input('notes')) : $old_note;
                if ($request->input('status') == 'rejected') {
                    $save_note = !isset($request->notes) ? 'In our verification process, we found information incorrect. It would great if you resubmit the form. If face problem in submission please contact us with support team' : $save_note;
                }
                if ($kyc) {
                    $kyc->status = $request->input('status');
                    $kyc->notes = $save_note;
                    $kyc->reviewedBy = Auth::id();
                    $kyc->reviewedAt = date('Y-m-d H:i:s');
                    $kyc->save();

                    if ($request->input('status') == 'approved') {
                        $kyc->user->walletAddress = ($kyc->user->walletAddress != null) ? $kyc->user->walletAddress : $kyc->walletAddress;
                        $kyc->user->walletType = ($kyc->user->walletType != null) ? $kyc->user->walletType : $kyc->walletName;
                        $kyc->user->mobile = ($kyc->user->mobile != null) ? $kyc->user->mobile : $kyc->phone;
                        $kyc->user->dateOfBirth = ($kyc->user->dateOfBirth != null) ? $kyc->user->dateOfBirth : $kyc->dob;
                        $kyc->user->nationality = ($kyc->user->nationality != null) ? $kyc->user->nationality : $kyc->country;
                        $kyc->user->save();
                    }

                    if ($kyc->user) {
                        try{
                            $when = now()->addMinutes(1);
                            $kyc->user->notify((new KycStatus($kyc))->delay($when));
                            // Notification::send($kyc->user, new KycStatus($kyc));

                            $ret['message'] = __('messages.kyc.' . $request->input('status'));
                        }catch(\Exception $e){

                            $ret['message'] = __('messages.kyc.' . $request->input('status')).' '.__('messages.email.kyc');
                        }
                    }

                    $ret['msg'] = ($request->input('status') == 'approved') ? 'success' : 'warning';
                    $ret['status'] = __status($request->input('status'), 'status');
                    $ret['message'] = __('messages.kyc.' . $request->input('status'));
                }
            }
        }

        if ($type == 'delete') {
            $id = $request->input('kyc_id');
            if ($id !== null) {
                $delete = KYC::find($id);
                if ($delete != null) {
                    if ($delete->document != null || $delete->document2 != null) {
                        if (starts_with($delete->document, 'kyc-files') && file_exists(storage_path('app/' . $delete->document))) {
                            (is_file(storage_path('app/' . $delete->document)) ? unlink(storage_path('app/' . $delete->document)) : '');
                        } elseif (starts_with($delete->document, 'kyc-files') && file_exists(storage_path('app/' . $delete->document2))) {
                            (is_file(storage_path('app/' . $delete->document)) ? unlink(storage_path('app/' . $delete->document2)) : '');
                        }
                    }
                    $delete->delete();
                    $ret['msg'] = 'success';
                    $ret['message'] = __('messages.delete.delete', ['what' => 'KYC']);
                } else {
                    $ret['msg'] = 'error';
                    $ret['message'] = __('messages.delete.delete_failed', ['what' => 'KYC']);
                }
            }
        }

        if ($request->ajax()) {
            return response()->json($ret);
        }
        return back()->with([$ret['msg'] => $ret['message']]);
    }
}
