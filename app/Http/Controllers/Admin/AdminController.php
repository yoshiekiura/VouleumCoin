<?php

namespace App\Http\Controllers\Admin;
/**
 * Admin Controller
 *
 *
 * @package TokenLite
 * @author Softnio
 * @version 1.0.0
 */
use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\User;
use App\Models\UserMeta;
use App\Notifications\PasswordChange;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Validator;

class AdminController extends Controller
{

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $get_tnx = ($request->get('chart') ? $request->get('chart') : 7) - 1;
        $get_user = ($request->get('user') ? $request->get('user') : 15) - 1;
        $stage = \App\Models\IcoStage::dashboard();
        $users = User::dashboard($get_user);
        $trnxs = \App\Models\Transaction::dashboard($get_tnx);

        if(isset($request->user)){
            $data = $users;
        }elseif(isset($request->chart)){
            $data = $trnxs;
        }else{
            $data = null;
        }
        if($request->ajax()){
            return response()->json((empty($data) ? [] : $data));
        }

        return view('admin.dashboard', compact('stage', 'users', 'trnxs'));
    }

    /**
     * Show the application User Profile.
     *
     * @return \Illuminate\Http\Response
     * @version 1.0.0
     * @since 1.0
     * @return void
     */
    public function profile()
    {
        $user = Auth::user();
        $userMeta = UserMeta::getMeta($user->id);
        return view('admin.profile', compact('user', 'userMeta'));
    }

    /**
     * Show the application User Profile.
     *
     * @return \Illuminate\Http\Response
     * @version 1.0.0
     * @since 1.0
     * @return void
     */
    public function profile_update(Request $request)
    {
        $type = $request->input('action_type');
        $ret['msg'] = 'info';
        $ret['message'] = __('messages.nothing');

        if ($type == 'personal_data') {
            $validator = Validator::make($request->all(), [
                'name' => 'required|min:4',
            ]);

            if ($validator->fails()) {
                $msg = '';
                if ($validator->errors()->has('name')) {
                    $msg = $validator->errors()->first();
                } elseif ($validator->errors()->has('email')) {
                    $msg = $validator->errors()->first();
                } else {
                    $msg = __('messages.something_wrong');
                }

                $ret['msg'] = 'warning';
                $ret['message'] = $msg;
                return response()->json($ret);
            } else {
                $user = User::FindOrFail(Auth::id());
                $user->name = $request->input('name');
                //$user->email = $request->input('email');
                $user->mobile = $request->input('mobile');
                $user_saved = $user->save();

                if ($user) {
                    $ret['msg'] = 'success';
                    $ret['message'] = __('messages.update.success', ['what' => 'Profile']);
                } else {
                    $ret['msg'] = 'danger';
                    $ret['message'] = __('messages.update.warning');
                }
            }
        }

        if ($type == 'notification') {
            $notify_admin = $newsletter = $unusual = 0;

            if (isset($request['newsletter'])) {
                $newsletter = 1;
            }
            if (isset($request['unusual'])) {
                $unusual = 1;
            }

            $user = User::FindOrFail(Auth::id());
            if ($user) {
                $userMeta = UserMeta::where('userId', $user->id)->first();
                if ($userMeta == null) {
                    $userMeta = new UserMeta();
                    $userMeta->userId = $user->id;
                }
                $userMeta->notify_admin = $notify_admin;
                $userMeta->newsletter = $newsletter;
                $userMeta->unusual = $unusual;
                $userMeta->save();
                $ret['msg'] = 'success';
                $ret['message'] = __('messages.update.success', ['what' => 'Notification']);
            } else {
                $ret['msg'] = 'danger';
                $ret['message'] = __('messages.update.warning');
            }
        }
        if ($type == 'security') {

            $save_activity = $mail_pwd = 'FALSE';
            $unusual = $notify_admin = 0;

            if (isset($request['notify_admin'])) {
                $notify_admin = 1;
            }
            if (isset($request['save_activity'])) {
                $save_activity = 'TRUE';
            }
            if (isset($request['mail_pwd'])) {
                $mail_pwd = 'TRUE';
            }

            $mail_pwd = 'TRUE';

            if (isset($request['unusual'])) {
                $unusual = 1;
            }

            $user = User::FindOrFail(Auth::id());
            if ($user) {
                $userMeta = UserMeta::where('userId', $user->id)->first();
                if ($userMeta == null) {
                    $userMeta = new UserMeta();
                    $userMeta->userId = $user->id;
                }
                $userMeta->unusual = $unusual;
                $userMeta->pwd_chng = $mail_pwd;
                $userMeta->save_activity = $save_activity;
                $userMeta->notify_admin = $notify_admin;
                $userMeta->save();
                $ret['msg'] = 'success';
                $ret['message'] = __('messages.update.success', ['what' => 'Security']);
            } else {
                $ret['msg'] = 'danger';
                $ret['message'] = __('messages.update.warning');
            }
        }
        if ($type == 'pwd_change') {
            //validate data
            $validator = Validator::make($request->all(), [
                'old-password' => 'required|min:6',
                'new-password' => 'required|min:6',
                're-password' => 'required|min:6|same:new-password',
            ]);
            if ($validator->fails()) {
                $msg = '';
                if ($validator->errors()->has('old-password')) {
                    $msg = $validator->errors()->first();
                } elseif ($validator->errors()->has('new-password')) {
                    $msg = $validator->errors()->first();
                } elseif ($validator->errors()->has('re-password')) {
                    $msg = $validator->errors()->first();
                } else {
                    $msg = __('messages.something_wrong');
                }

                $ret['msg'] = 'warning';
                $ret['message'] = $msg;
                return response()->json($ret);
            } else {
                $user = User::FindOrFail(Auth::id());
                if ($user) {
                    if (!Hash::check($request->input('old-password'), $user->password)) {
                        $ret['msg'] = 'warning';
                        $ret['message'] = __('messages.password.old_err');
                    } else {
                        $userMeta = UserMeta::where('userId', $user->id)->first();
                        $userMeta->pwd_temp = Hash::make($request->input('new-password'));
                        $cd = Carbon::now();
                        $userMeta->email_expire = $cd->copy()->addMinutes(60);
                        $userMeta->email_token = str_random(65);
                        if ($userMeta->save()) {
                           try {
                                $user->notify(new PasswordChange($user, $userMeta));
                                $ret['msg'] = 'success';
                                $ret['message'] = __('messages.password.changed');
                            } catch (\Exception $e) {
                                $ret['msg'] = 'warning';
                                $ret['message'] = __('messages.email.password_change',['email' => get_setting('site_email')]);
                            }
                        } else {
                            $ret['msg'] = 'danger';
                            $ret['message'] = __('messages.something_wrong');
                        }
                    }
                } else {
                    $ret['msg'] = 'danger';
                    $ret['message'] = __('messages.something_wrong');
                }
            }
        }

        if ($request->ajax()) {
            return response()->json($ret);
        }
        return back()->with([$ret['msg'] => $ret['message']]);
    }

    public function password_confirm($token)
    {
        $user = Auth::user();
        $userMeta = UserMeta::where('userId', $user->id)->first();
        if ($token == $userMeta->email_token) {
            if (_date($userMeta->email_expire, 'Y-m-d H:i:s') >= date('Y-m-d H:i:s')) {
                $user->password = $userMeta->pwd_temp;
                $user->save();
                $userMeta->pwd_temp = null;
                $userMeta->email_token = null;
                $userMeta->email_expire = null;
                $userMeta->save();

                $ret['msg'] = 'success';
                $ret['message'] = __('messages.password.success');
            } else {
                $ret['msg'] = 'danger';
                $ret['message'] = __('messages.password.failed');
            }
        } else {
            $ret['msg'] = 'danger';
            $ret['message'] = __('messages.password.token');
        }

        return redirect()->route('admin.profile')->with([$ret['msg'] => $ret['message']]);
    }

    /**
     * Show the user account activity page.
     * and Delete Activity
     *
     * @return \Illuminate\Http\Response
     * @version 1.0.0
     * @since 1.0
     * @return void
     */
    public function activity()
    {
        $user = Auth::user();
        $activities = Activity::where('user_id', $user->id)->orderBy('created_at', 'DESC')->get();
        return view('admin.activity', compact('user', 'activities'));
    }
    /**
     * Delete activity
     *
     * @version 1.0.0
     * @since 1.0
     * @return void
     */
    public function activity_delete(Request $request)
    {
        $id = $request->input('delete_activity');
        $ret['msg'] = 'info';
        $ret['message'] = __('messages.nothing');

        if ($id !== 'all') {
            $remove = Activity::where('id', $id)->where('user_id', Auth::id())->delete();
        } else {
            $remove = Activity::where('user_id', Auth::id())->delete();
        }
        if ($remove) {
            $ret['msg'] = 'success';
            $ret['message'] = __('messages.delete.delete', ['what' => 'Activity']);
        } else {
            $ret['msg'] = 'danger';
            $ret['message'] = __('messages.something_wrong');
        }
        if ($request->ajax()) {
            return response()->json($ret);
        }
        return back()->with([$ret['msg'] => $ret['message']]);
    }
}
