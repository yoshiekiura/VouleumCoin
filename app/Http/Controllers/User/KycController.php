<?php

namespace App\Http\Controllers\User;
/**
 * Kyc Controller
 *
 *
 * @package TokenLite
 * @author Softnio
 * @version 1.0.0
 */
use App\Http\Controllers\Controller;
use App\Models\KYC;
use App\Models\User;
use App\Models\UserMeta;
use App\Notifications\KycStatus;
use Auth;
use IcoHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Validator;

class KycController extends Controller
{
    public function __construct()
    {
        if( application_installed()){
            if (get_setting('kyc_before_email') == '1') {
                return $this->middleware('verified')->except(['index']);
            }
        }
    }

    /**
     * Show the kyc status
     *
     * @return \Illuminate\Http\Response
     * @version 1.0.0
     * @since 1.0
     * @return void
     */
    public function index()
    {
        $user_kyc = Auth::user()->kyc_info;

        return view('user.kyc', compact('user_kyc'));
    }

    /**
     * Show the kyc status
     *
     * @return \Illuminate\Http\Response
     * @version 1.0.0
     * @since 1.0
     * @return void
     */
    public function view()
    {
        $kyc = Auth::user()->kyc_info;

        return view('user.kyc_details', compact('kyc'));
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
     * Show the kyc application
     *
     * @return \Illuminate\Http\Response
     * @version 1.0.0
     * @since 1.0
     * @return void
     */
    public function application()
    {
        if (isset(Auth::user()->kyc_info->status)) {
            if (Auth::user()->kyc_info->status == 'pending') {
                return redirect()->route('user.kyc')->with(['info' => __('messages.kyc.wait')]);
            }
        }
        $countries = \IcoHandler::getCountries();
        $user_kyc = Auth::user()->kyc_info;
        if ($user_kyc == null) {
            $user_kyc = new KYC();
        }

        return view('user.kyc_application', compact('user_kyc', 'countries'));
    }

    /**
     * Submit the kyc form
     *
     * @return \Illuminate\Http\Response
     * @version 1.0.0
     * @since 1.0
     * @return void
     */
    public function submit(Request $request)
    {
        $validator = Validator::make($request->all(), KYC::rules());
        if ($validator->fails()) {
            $msg = '';
            if ($validator->errors()->first()) {
                $msg = $validator->errors()->first();
            } else {
                $msg = __('messages.somthing_wrong');
            }

            $ret['msg'] = 'warning';
            $ret['message'] = $msg;
            return response()->json($ret);
        } else {
            $doc1 = $doc2 = $doc3 = '';
            if ($request->input('documentType') == 'passport') {
                $doc1 = $request->input('passport_image');
                $doc2 = $request->input('passport_image_hand');
            }
            if ($request->input('documentType') == 'nidcard') {
                $doc1 = $request->input('id_front');
                $doc2 = $request->input('id_back');
                $doc3 = $request->input('nid_hand');
            }
            if ($request->input('documentType') == 'driving') {
                $doc1 = $request->input('license');
                $doc2 = $request->input('license_hand');
            }
            $user = Auth::user();
            if (!$user) {
                $user = User::create([
                    'name' => $request->input('first_name') . ' ' . $request->input('last_name'),
                    'email' => $request->input('email'),
                    'password' => Hash::make( $request->input('password')),
                    'lastLogin' => date('Y-m-d H:i:s'),
                    'type' => 'user',
                    'registerMethod' => 'KYC',
                ]);
                if ($user) {
                    UserMeta::create([
                        'userId' => $user->id,
                    ]);
                }
            }

            $kyc_submit = new KYC();
            $kyc_submit->userId = $user->id;
            $kyc_submit->firstName = $request->input('first_name');
            $kyc_submit->lastName = $request->input('last_name');
            $kyc_submit->email = $user->email;
            $kyc_submit->phone = $request->input('phone');
            $kyc_submit->dob = $request->input('dob');
            $kyc_submit->gender = $request->input('gender');
            $kyc_submit->telegram = $request->input('telegram');

            $kyc_submit->country = $request->input('country');
            $kyc_submit->state = $request->input('state');
            $kyc_submit->city = $request->input('city');
            $kyc_submit->zip = $request->input('zip');
            $kyc_submit->address1 = $request->input('address_1');
            $kyc_submit->address2 = $request->input('address_2');

            $kyc_submit->documentType = $request->input('documentType');
            $kyc_submit->document = $doc1;
            $kyc_submit->document2 = $doc2;
            $kyc_submit->document3 = $doc3;
            $kyc_submit->status = 'pending';

            $kyc_submit->walletName = $request->input('wallet_name');
            $kyc_submit->walletAddress = $request->input('wallet_address');
            // $kyc_submit->save();

            $is_valid = IcoHandler::validate_address($request->input('wallet_address'), $request->input('wallet_name'));
            if ($is_valid) {
                if ($kyc_submit->save()) {
                    try{
                        $user->notify(new KycStatus($kyc_submit));
                        // Notification::send($user, new KycStatus($kyc_submit));
                        $ret['msg'] = 'success';
                        $ret['message'] = __('messages.kyc.forms.submitted');
                        $ret['link'] = route('user.kyc') . '?thank_you=true';
                    }catch(\Exception $e){
                        $ret['msg'] = 'success';
                        $ret['message'] = __('messages.kyc.forms.submitted');
                        $ret['link'] = route('user.kyc') . '?thank_you=true';
                    }
                } else {
                    $ret['msg'] = 'error';
                    $ret['message'] = __('messages.kyc.forms.failed');
                }
            } else {
                if (empty(auth()->user())) {
                    $user->delete();
                }
                $ret['msg'] = 'warning';
                $ret['message'] = __('messages.invalid.address');
            }
            if ($request->ajax()) {
                return response()->json($ret);
            }
            return back()->with([$ret['msg'] => $ret['message']]);
        }
    }

    /**
     * Upload the user kyc documents
     *
     * @return \Illuminate\Http\Response
     * @version 1.0.0
     * @since 1.0
     * @return void
     */
    public function upload(Request $request)
    {
        if (! is_dir(storage_path('app/kyc-files'))) {
            mkdir(storage_path('app/kyc-files'));
        }

        if (isset($request->action)) {
            if ($request->input('action') == 'delete' && !empty($request->input('file'))) {
                if (is_file(storage_path('app/' . $request->input('file')))) {
                    unlink(storage_path('app/' . $request->input('file')));
                    return response()->json(['status' => 'File Removed']);
                }
            }
        }

        //passport upload
        if (isset($_FILES['kyc_file_upload'])) {
            $cleanData = Validator::make($request->all(), ['kyc_file_upload' => 'required|mimetypes:image/jpeg,image/png,application/pdf']);
            if ($cleanData->fails()) {
                $ret['msg'] = 'warning';
                $ret['message'] = __('messages.upload.invalid');
            } else {
                $id_front = $request->file('kyc_file_upload')->store('kyc-files');
                if ($id_front) {
                    $ret['msg'] = 'success';
                    $ret['message'] = __('messages.upload.success', ['what' => "Passport"]);
                    $ret['file_name'] = $id_front;
                } else {
                    $ret['msg'] = 'error';
                    $ret['message'] = __('messages.upload.failed', ['what' => "Passport"]);
                }
            }
            return response()->json($ret);
        }

        //ID Back upload
        if (isset($_FILES['id_back'])) {
            $cleanData = Validator::make($request->all(), ['id_back' => 'required|mimetypes:image/jpeg,image/png,application/pdf']);
            if ($cleanData->fails()) {
                $ret['msg'] = 'warning';
                $ret['message'] = 'Invalid File Type';
            } else {
                $id_back = $request->file('id_back')->store('kyc-files');
                if ($id_back) {
                    $ret['msg'] = 'success';
                    $ret['message'] = __('messages.upload.success', ['what' => "File"]);
                    $ret['file_name'] = $id_back;
                } else {
                    $ret['msg'] = 'error';
                    $ret['message'] = __('messages.upload.failed', ['what' => "File"]);
                }
            }
            return response()->json($ret);
        }

        //Utility upload
        if (isset($_FILES['utility'])) {
            $cleanData = Validator::make($request->all(), ['utility' => 'required|mimetypes:image/jpeg,image/png,application/pdf']);
            if ($cleanData->fails()) {
                $ret['msg'] = 0;
                $ret['message'] = 'Invalid File Type';
            } else {
                $utility = $request->file('utility')->store('kyc-files');
                if ($utility) {
                    $ret['msg'] = 1;
                    $ret['message'] = $utility;
                } else {
                    $ret['msg'] = 0;
                    $ret['message'] = 'An error occurred uploading Utility image';
                }
            }
            return response()->json($ret);
        }

        //selfie upload
        if (isset($_FILES['selfie'])) {
            $cleanData = Validator::make($request->all(), ['selfie' => 'required|mimetypes:image/jpeg,image/png,application/pdf']);
            if ($cleanData->fails()) {
                $ret['msg'] = 0;
                $ret['message'] = 'Invalid File Type';
            } else {
                $selfie = $request->file('selfie')->store('kyc-files');
                if ($selfie) {
                    $ret['msg'] = 1;
                    $ret['message'] = $selfie;
                } else {
                    $ret['msg'] = 0;
                    $ret['message'] = 'An error occurred uploading selfie image';
                }
            }
            return response()->json($ret);
        }

        //certificate upload
        if (isset($_FILES['certificate'])) {
            $cleanData = Validator::make($request->all(), ['certificate' => 'required|mimetypes:image/jpeg,image/png,application/pdf']);
            if ($cleanData->fails()) {
                $ret['msg'] = 0;
                $ret['message'] = 'Invalid File Type';
            } else {
                $certificate = $request->file('certificate')->store('kyc-files');
                if ($certificate) {
                    $ret['msg'] = 1;
                    $ret['message'] = $certificate;
                } else {
                    $ret['msg'] = 0;
                    $ret['message'] = 'An error occurred uploading certificate image';
                }
            }
            return response()->json($ret);
        }

        if (!$request->ajax()) {
            return back();
        }
    }
}
