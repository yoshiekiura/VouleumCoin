<?php

namespace App\Http\Controllers\Admin;
/**
 * Settings Controller
 *
 *
 * @package TokenLite
 * @author Softnio
 * @version 1.0.0
 */
use Validator;
use App\Models\Setting;
use IcoHandler;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SettingController extends Controller
{

    /**
     * Display the settings page
     *
     * @return \Illuminate\Http\Response
     * @version 1.0.0
     * @since 1.0
     * @return void
     */
    public function index()
    {
        $timezones = IcoHandler::get_timezones();
        return view('admin.settings', compact('timezones'));
    }


    /**
     * Update the settings Data
     *
     * @return \Illuminate\Http\Response
     * @version 1.0.0
     * @since 1.0
     * @return void
     */
    public function update(Request $request)
    {
        $type = $request->input('type');
        $ret['msg'] = 'info';
        $ret['message'] = __('messages.nothing');

        if ($type == 'site_info') {
            $validator = Validator::make($request->all(), [
                'site_name' => 'required|min:4',
                'site_email' => 'required|email'
            ]);

            if ($validator->fails()) {
                $msg = '';
                if ($validator->errors()->has('site_name')) {
                    $msg = $validator->errors()->first();
                } elseif ($validator->errors()->has('site_email')) {
                    $msg = $validator->errors()->first();
                } 
                elseif ($validator->errors()->has('site_support_phone')) {
                    $msg = $validator->errors()->first();
                } else {
                    $msg = __('messages.something_wrong');
                }

                $ret['msg'] = 'warning';
                $ret['message'] = $msg;
                return response()->json($ret);
            } else {
                $ret['msg'] = 'danger';
                $ret['message'] = __('messages.update.failed', ['what' => 'Settings']);
                Setting::updateValue(Setting::SITE_NAME, $request->input('site_name'));
                Setting::updateValue(Setting::SITE_EMAIL, $request->input('site_email'));
                Setting::updateValue('site_copyright', $request->input('site_copyright'));
                Setting::updateValue('site_support_address', $request->input('site_support_address'));
                Setting::updateValue('site_support_phone', $request->input('site_support_phone'));
                Setting::updateValue('site_support_email', $request->input('site_support_email'));


                $ret['msg'] = 'success';
                $ret['message'] = __('messages.update.success', ['what' => 'Settings']);
            }
        }

        if ($type == 'social_links') {
            $ret['msg'] = 'danger';
            $ret['message'] = __('messages.update.failed', ['what' => 'Social Links']);
            $ln['facebook'] = $request->input('ss_fb');
            $ln['twitter'] = $request->input('ss_tt');
            $ln['linkedin'] = $request->input('ss_ln');
            $ln['github'] = $request->input('ss_gh');
            $links = json_encode($ln);
            Setting::updateValue('site_social_links', $links);
            $ret['msg'] = 'success';
            $ret['message'] = __('messages.update.success', ['what' => 'Social Links']);
        }
        if ($type == 'general') {
            $ret['msg'] = 'danger';
            $ret['message'] = __('messages.update.failed', ['what' => 'Social Links']);

            Setting::updateValue('main_website_url', $request->input('main_website_url'));
            Setting::updateValue('site_maintenance', (isset($request->site_maintenance) ? 1 : 0));
            Setting::updateValue('site_maintenance_text', $request->input('site_maintenance_text'));
            Setting::updateValue('site_date_format', $request->input('site_date_format'));
            Setting::updateValue('site_time_format', $request->input('site_time_format'));
            if ($request->input('user_dashboard_style') || $request->input('site_timezone')) {
                Setting::updateValue('site_timezone', $request->input('site_timezone'));
                Setting::updateValue('user_dashboard_color', $request->input('user_dashboard_color'));
                \Artisan::call('config:clear');
            }

            $ret['msg'] = 'success';
            $ret['message'] = __('messages.update.success', ['what' => 'General Settings']);
        }
        if ($type == 'api_credetial') {
            $ret['msg'] = 'danger';
            $ret['message'] = __('messages.update.failed', ['what' => 'API Credentials']);

            Setting::updateValue('site_api_fb_id', $request->input('api_fb_id'));
            Setting::updateValue('site_api_fb_secret', $request->input('api_fb_secret'));
            Setting::updateValue('site_api_google_id', $request->input('api_google_id'));
            Setting::updateValue('site_api_google_secret', $request->input('api_google_secret'));

            $ret['msg'] = 'success';
            $ret['message'] = __('messages.update.success', ['what' => 'API Credentials']);
        }



        if ($request->ajax()) {
            return response()->json($ret);
        }
        return back()->with([$ret['msg'] => $ret['message']]);
    }
}
