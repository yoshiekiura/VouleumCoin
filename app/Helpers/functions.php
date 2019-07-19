<?php

use App\Helpers\IcoHandler;
use App\Helpers\TokenCalculate;
use App\Models\EmailTemplate;
use App\Models\GlobalMeta;
use App\Models\IcoStage;
use App\Models\KYC;
use App\Models\Page;
use App\Models\PaymentMethod;
use App\Models\Setting;
use App\Models\User;
use App\Notifications\TnxStatus;
use Carbon\Carbon;
use Illuminate\Support\Facades\Notification;

/**
 * Custom Helper Functions
 *
 *
 * @package TokenLite
 * @author Softnio
 * @version 1.0.0
 * @since 1.0
 * @return void
 */

/* @function application_installed()  @version v.1.0.0  @since 1.0 */
if (!function_exists('application_installed')) {
    function application_installed($full_check = false)
    {
        if(file_exists(storage_path('installed'))){
            if($full_check === true){
                try {
                    \DB::connection()->getPdo();
                    return  true; /*(Schema::hasTable('migrations') && Schema::hasTable('users'))*/;
                } catch (\Exception $e) {
                    return false;
                }
            }
            return true;
        }
        return false;
    }
}
/* @function site_info()  @version v.1.0.0  @since 1.0 */
if (!function_exists('site_info')) {
    function site_info($output = '', $echo = false)
    {
        $name = (get_setting('site_name')) ? get_setting('site_name') : config('settings.site_name');
        $desc = get_setting('site_description');
        $email = get_setting('site_email');

        $return = $name;
        if ($output == 'desc') {
            $return = $desc;
        }

        if ($output == 'email') {
            $return = $email;
        }

        if ($echo === false) {
            return $return;
        }

        echo $return;
    }
}

/* @function site_logo()  @version v.1.0.0  @since 1.0 */
if (!function_exists('site_logo')) {
    function site_logo($type = '', $ver = 'dark', $echo = false)
    {
        if (empty($type)) {
            $type = 'default';
        }

        if ($type != 'default' && $type != 'retina') {
            return false;
        }

        $default_dark = [
            'default' => 'assets/images/logo.png',
            'retina' => 'assets/images/logo2x.png',
        ];

        $default_light = [
            'default' => 'assets/images/logo-light.png',
            'retina' => 'assets/images/logo-light2x.png',
        ];

        $default = ($ver == 'light') ? $default_light : $default_dark;

        $output = asset($default[$type]);

        if ($echo === true) {
            echo $output;
        } else {
            return $output;
        }
    }
}

/* @function base_currency()  @version v.1.0.0  @since 1.0 */
if (!function_exists('base_currency')) {
    function base_currency($upper = false)
    {
        $return = (get_setting('site_base_currency')) ? strtolower(get_setting('site_base_currency')) : 'usd';
        if ($upper == true) {
            return strtoupper($return);
        }
        return $return;
    }
}

/* @function def_datetime()  @version v.1.0.0  @since 1.0 */
if (!function_exists('def_datetime')) {
    function def_datetime($get = '')
    {
        if (!$get) {
            return false;
        }

        $data = [
            'date' => '2000-01-01',
            'time_s' => '00:00:00',
            'time_e' => '23:59:00',
        ];
        $return = [
            'date' => $data['date'],
            'time' => $data['time_s'],
            'time_s' => $data['time_s'],
            'time_e' => $data['time_e'],
            'datetime' => $data['date'] . ' ' . $data['time_s'],
            'datetime_s' => $data['date'] . ' ' . $data['time_s'],
            'datetime_e' => $data['date'] . ' ' . $data['time_e'],
        ];
        return $return[$get];
    }
}

/* @function show_str()  @version v.1.0.0  @since 1.0 */
if (!function_exists('show_str')) {
    function show_str($string, $length = 5)
    {
        return IcoHandler::string_compact($string, $length);
    }
}

/* @function has_wallet()  @version v.1.0.0  @since 1.0 */
if (!function_exists('has_wallet')) {
    function has_wallet($get = false)
    {
        return IcoHandler::check_user_wallet($get);
    }
}

/* @function manual_payment()  @version v.1.0.0  @since 1.0 */
if (!function_exists('manual_payment')) {
    function manual_payment($type, $ext = '', $active = true)
    {
        return IcoHandler::get_manual_payment($type, $ext, $active);
    }
}

/* @function app_info()  @version v.1.0.0  @since 1.0 */
if (!function_exists('app_info')) {
    function app_info($output = '')
    {
        return IcoHandler::panel_info($output);
    }
}

/* @function css_class()  @version v.1.0.0  @since 1.0 */
if (!function_exists('css_class')) {
    function css_class($str = '', $key = '', $args = array())
    {
        return IcoHandler::css_class_generate($str, $key, $args);
    }
}

/* @function token()  @version v.1.0.0  @since 1.0 */
if (!function_exists('token')) {
    function token($params = '')
    {
        return IcoHandler::get_token_settings($params);
    }
}

/* @function token_symbol()  @version v.1.0.0  @since 1.0 */
if (!function_exists('token_symbol')) {
    function token_symbol()
    {
        return IcoHandler::get_token_settings('symbol');
    }
}

/* @function min_decimal()  @version v.1.0.0  @since 1.0 */
if (!function_exists('min_decimal')) {
    function min_decimal()
    {
        $decimal = IcoHandler::get_token_settings('decimal_min');
        return ($decimal) ? $decimal : 0;
    }
}

/* @function max_decimal()  @version v.1.0.0  @since 1.0 */
if (!function_exists('max_decimal')) {
    function max_decimal()
    {
        $decimal = IcoHandler::get_token_settings('decimal_max');
        return ($decimal) ? $decimal : 2;
    }
}

/* @function token_method()  @version v.1.0.0  @since 1.0 */
if (!function_exists('token_method')) {
    function token_method()
    {
        $token_method = IcoHandler::get_token_settings('default_method');
        return ($token_method) ? $token_method : strtoupper(base_currency());
    }
}

/* @function is_method_valid()  @version v.1.0.0  @since 1.0 */
if (!function_exists('is_method_valid')) {
    function is_method_valid($name = '', $output = '')
    {
        $is_valid = $is_fallback = false;
        $def_method = token_method();

        $act_method = [
            'ETH' => (token('purchase_eth')) ? 1 : 0,
            'LTC' => (token('purchase_ltc')) ? 1 : 0,
            'BTC' => (token('purchase_btc')) ? 1 : 0,
            'USD' => (token('purchase_usd')) ? 1 : 0,
            'EUR' => (token('purchase_eur')) ? 1 : 0,
            'GBP' => (token('purchase_gpb')) ? 1 : 0,
        ];
        if ($act_method[$def_method] === 1) {
            $is_fallback = true;
        }
        if (empty($name)) {
            $is_valid = (in_array(1, array_values($act_method))) ? true : false;
        } else {
            $is_valid = (isset($act_method[strtoupper($name)])) ? $act_method[strtoupper($name)] : false;
        }
        // Return
        if ($output == 'fallback') {
            return $is_fallback;
        }
        if ($output == 'array') {
            return $act_method;
        }
        return $is_valid;
    }
}

/* @function get_emailt()  @version v.1.0.0  @since 1.0 */
function get_emailt($name = '', $get = '')
{
    $data = EmailTemplate::get_template($name);
    $result = (!empty($get) ? $data->$get : $data);

    return $result;
}

/* @function notify_admin()  @version v.1.0.0  @since 1.0 */
function notify_admin($tnx, $name = '')
{
    $admins =User::join('user_metas', 'users.id', '=', 'user_metas.userId')->where(['users.role' => 'admin', 'users.status' => 'active', 'user_metas.notify_admin' => 1])->select('users.*')->get();
    $to_all = get_setting('send_notification_to', 'all');
    $admin = (is_numeric($to_all) ? User::find($to_all) : null);
    if ($to_all == 'all') {
        $when = now()->addMinutes(2);
        Notification::send($admins, new TnxStatus($tnx, $name));
    } elseif ($admin) {
        $admin->notify((new TnxStatus($tnx, $name)));
    }
    $mails = get_setting('send_notification_mails');
    if ($mails) {
        $mails = explode(',', $mails);
        Notification::route('mail', $mails)->notify((new TnxStatus($tnx, $name)));
    }
    return $to_all;
}

/* @function token_rate()  @version v.1.0.0  @since 1.0 */
if (!function_exists('token_rate')) {
    function token_rate($amount, $currency = '')
    {
        if (empty($amount)) {
            return 0;
        }

        $currency = ($currency == '') ? base_currency() : $currency;
        $res = Setting::exchange_rate($amount, $currency);
        return $res;
    }
}

/* @function token_calc()  @version v.1.0.0  @since 1.0 */
if (!function_exists('token_calc')) {
    function token_calc($amount, $output = 'total')
    {
        if (empty($amount)) {
            return 0;
        }

        $res = new TokenCalculate();
        return $res->calc_token($amount, $output);
    }
}

/* @function _format()  @version v.1.0.0  @since 1.0 */
if (!function_exists('_format')) {
    function _format($attr = [])
    {
        $number = isset($attr['number']) ? $attr['number'] : 0;
        $point = isset($attr['point']) ? $attr['point'] : '.';
        $thousand = isset($attr['thousand']) ? $attr['thousand'] : '';
        $decimal = isset($attr['decimal']) ? $attr['decimal'] : 'round';
        $trim = isset($attr['trim']) ? $attr['trim'] : true;

        $site_decimal = ($decimal == 'round') ? token('decimal_max') : $decimal;
        $site_decimal = ($site_decimal == 'decimal') ? token('decimal_min') : $site_decimal;

        $ret = number_format($number, $site_decimal, $point, $thousand);
        $ret = ($trim == true) ? rtrim($ret, '0') : $ret;
        $ret = (substr($ret, -1)) == '.' ? str_replace('.', '', $ret) : $ret;
        return $ret;
    }
}

/* @function is_json()  @version v.1.0.0  @since 1.0 */
if (!function_exists('is_json')) {
    function is_json($string)
    {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }
}

/* @function get_setting()  @version v.1.0.0  @since 1.0 */
if (!function_exists('get_setting')) {
    function get_setting($name, $if_null = null)
    {
        $result = Setting::getValue($name);
        return ($result != null ? $result : $if_null);
    }
}
/* @function add_setting()  @version v.1.0.0  @since 1.0 */
if (!function_exists('add_setting')) {
    function add_setting($name, $value)
    {
        $result = Setting::updateValue($name, $value);
        return $result ? get_setting($name, $value) : null;
    }
}
/* @function save_gmeta() -GlobalMeta  @version v.1.0.0  @since 1.0 */
if (!function_exists('save_gmeta')) {
    function save_gmeta($name, $value = null, $pid = null, $extra = null)
    {
        $result = GlobalMeta::save_meta($name, $value, $pid, $extra);
        return $result;
    }
}
/* @function is_super_admin() -GlobalMeta  @version v.1.0.0  @since 1.0 */
if (!function_exists('is_super_admin')) {
    function is_super_admin()
    {
        $all_super_admins = GlobalMeta::get_super_admins();
        $response = true;
        if (!in_array(auth()->id(), $all_super_admins)) {
            // $response = false;
        }
        return $response;
    }
}

/* @function email_setting()  @version v.1.0.0  @since 1.0 */
if (!function_exists('email_setting')) {
    function email_setting($name, $if_null = '')
    {
        $data = [
            'driver' => get_setting('site_mail_driver'),
            'host' => get_setting('site_mail_host'),
            'port' => get_setting('site_mail_port'),
            'from_address' => get_setting('site_mail_from_address'),
            'from_email' => get_setting('site_mail_from_address'),
            'from_name' => get_setting('site_mail_from_name'),
            'encryption' => get_setting('site_mail_encryption'),
            'user_name' => get_setting('site_mail_username'),
            'password' => get_setting('site_mail_password'),
        ];
        return (isset($data[$name]) && $data[$name] != null) ? $data[$name] : $if_null;
    }
}

/* @function field_value()  @version v.1.0.0  @since 1.0 */
if (!function_exists('field_value')) {
    function field_value($field, $key = '')
    {
        if (empty($field)) {
            return false;
        }

        $get_value = get_setting($field);

        if ($get_value) {
            if (!empty($key)) {
                $data = json_decode($get_value, true);
                return $data[$key] == '1' ?? true;
            } else {
                return $get_value == '1' ?? true;
            }
        } else {
            return false;
        }
    }
}

/* @function field_value()  @version v.1.0.0  @since 1.0 */
if (!function_exists('field_value_custom')) {
    function field_value_text($field, $text = '')
    {
        if (empty($field)) {
            return null;
        }

        $get_value = get_setting($field);

        if ($get_value) {
            if (!empty($text)) {
                $data = json_decode($get_value, true);
                return $data[$text];
            } else {
                return json_decode($get_value, true);
            }
        } else {
            return null;
        }
    }
}

/* @function required_mark()  @version v.1.0.0  @since 1.0 */
if (!function_exists('required_mark')) {
    function required_mark($name)
    {
        $a = '';
        if (field_value($name, 'req')) {
            $a = '<span class="text-require text-danger">*</span>';
        }
        return $a;
    }
}

/* @function __status()  @version v.1.0.0  @since 1.0 */
if (!function_exists('__status')) {
    function __status($name, $get)
    {
        $all_status = [
            'pending' => (object) [
                'icon' => 'progress',
                'text' => 'Progress',
                'status' => 'info',
            ],
            'missing' => (object) [
                'icon' => 'pending',
                'text' => 'Missing',
                'status' => 'warning',
            ],
            'approved' => (object) [
                'icon' => 'approved',
                'text' => 'Approved',
                'status' => 'success',
            ],
            'rejected' => (object) [
                'icon' => 'canceled',
                'text' => 'Rejected',
                'status' => 'danger',
            ],
            'canceled' => (object) [
                'icon' => 'canceled',
                'text' => 'Canceled',
                'status' => 'danger',
            ],
            'onhold' => (object) [
                'icon' => 'pending',
                'text' => 'On Hold',
                'status' => 'info',
            ],
            'suspend' => (object) [
                'icon' => 'canceled',
                'text' => 'Suspended',
                'status' => 'danger',
                'null' => null,
            ],
            'active' => (object) [
                'icon' => 'success',
                'text' => 'Active',
                'status' => 'success',
                'null' => null,
            ],
            'default' => (object) [
                'icon' => 'pending',
                'text' => 'Pending',
                'status' => 'info',
                'null' => null,
            ],
            'purchase' => (object) [
                'icon' => 'purchase',
                'text' => 'Purchase',
                'status' => 'success',
                'null' => null,
            ],
            'bonus' => (object) [
                'icon' => 'bonus',
                'text' => 'Bonus',
                'status' => 'warning',
                'null' => null,
            ],
            'referral' => (object) [
                'icon' => 'referral',
                'text' => 'Referral',
                'status' => 'primary',
                'null' => null,
            ],
        ];
        return (isset($all_status[$name]) ? $all_status[$name]->$get : (isset($all_status['default']->$get) ? $all_status['default']->$get : $all_status['default']->null));
    }
}

/* @function _date()  @version v.1.0.0  @since 1.0 */
if (!function_exists('_date')) {
    function _date($date, $format = null)
    {
        $setting_format = get_setting('site_date_format', 'd M Y') . ' ' . get_setting('site_time_format', 'h:iA');

        $_format = (empty($format)) ? $setting_format : $format;
        $result = (!empty($date)) ? $date : now();

        return (!empty($date) ? date($_format, strtotime($result)) : null);
    }
}

/* @function _date()  @version v.1.0.0  @since 1.0 */
if (!function_exists('_cdate')) {
    function _cdate($date)
    {
        $date = Carbon::parse($date);
        return $date;
    }
}

/* @function active_stage()  @version v.1.0.0  @since 1.0 */
if (!function_exists('active_stage')) {
    function active_stage($id = '')
    {
        if (get_setting('actived_stage') != '' && is_numeric(get_setting('actived_stage'))) {
            $stage = IcoStage::where('status', '!=', 'deleted')->where('id', get_setting('actived_stage'))->first();
            if (!$stage) {
                $stage = IcoStage::where('status', '!=', 'deleted')->orderBy('id', 'DESC')->first();
            }
        } elseif ($id != '') {
            $stage = IcoStage::where('status', '!=', 'deleted')->find($id);
        } else {
            $stage = IcoStage::where('status', '!=', 'deleted')->first();
        }

        return $stage;
    }
}

/* @function active_stage_status()  @version v.1.0.0  @since 1.0 */
if (!function_exists('active_stage_status')) {
    function active_stage_status($stage='')
    {
        $stage = (empty($stage)) ? active_stage() : $stage;
        $status     = false; 
        $start_date = strtotime( $stage->start_date ); 
        $end_date   = strtotime( $stage->end_date );
        $today_date = time();

        if ($today_date >= $start_date && $today_date <= $end_date) 
        {
            if ($stage->sales_token >= $stage->total_tokens) {
                $status = 'completed';
            }elseif ($stage->status =='paused') {
                $status = 'paused';
            } else {
                $status = 'running';
            }
        } 
        elseif ($today_date < $start_date) 
        {
            $status = 'upcoming';
        }
        elseif ($today_date > $end_date) 
        {
            if ($stage->sales_token > 0) {
                $status = 'completed';
            } else {
                $status = 'expired';
            }
        }
        return $status;
    }
}

/* @function is_upcoming()  @version v.1.0.0  @since 1.0 */
if (!function_exists('is_upcoming')) {
    function is_upcoming($stage='')
    {
        return (active_stage_status($stage) =='upcoming') ? true : false;
    }
}

/* @function is_completed()  @version v.1.0.0  @since 1.0 */
if (!function_exists('is_completed')) {
    function is_completed($stage='')
    {
        return (active_stage_status($stage) =='completed') ? true : false;
    }
}

/* @function is_completed()  @version v.1.0.0  @since 1.0 */
if (!function_exists('is_running')) {
    function is_running($stage='')
    {
        return (active_stage_status($stage) =='running') ? true : false;
    }
}

/* @function stage_date()  @version v.1.0.0  @since 1.0 */
if (!function_exists('stage_date')) {
    function stage_date($date)
    {
        $d = _date($date, 'Y-m-d');
        if ($d != def_datetime('date')) {
            return _date($d, 'm/d/Y');
        } else {
            return '';
        }
    }
}

/* @function stage_time()  @version v.1.0.0  @since 1.0 */
if (!function_exists('stage_time')) {
    function stage_time($time, $attr = 'start')
    {
        $d = _date($time, 'Y-m-d H:i:s');

        $se = ($attr == 'start') ? '_s' : '_e';

        if ($d != def_datetime('datetime' . $se)) {
            return _date($time, 'h:i A');
        } else {
            return '';
        }
    }
}

/* @function set_id()  @version v.1.0.0  @since 1.0 */
if (!function_exists('set_id')) {
    function set_id($number, $type = 'user')
    {
        if ($type == 'user') {
            return config('icoapp.user_prefix', 'UD') . sprintf('%05s', $number);
        }
        if ($type == 'trnx') {
            return config('icoapp.tnx_prefix', 'TNX') . sprintf('%06s', $number);
        }
    }
}

/* @function set_added_by()  @version v.1.0.0  @since 1.0 */
if (!function_exists('set_added_by')) {
    function set_added_by($number, $type = 'system')
    {
        return __prefix($type) . sprintf('%05s', $number);
    }
}

/* @function __prefix()  @version v.1.0.0  @since 1.0 */
if (!function_exists('__prefix')) {
    function __prefix($type)
    {
        $data = [
            'system' => "SYS-",
            'admin' => "ADM-",
            'manager' => "MNG-",
            'sub_admin' => "SAD-",
        ];
        return (isset($data[$type]) ? $data[$type] : 'UD-');
    }
}

/* @function get_pm()  @version v.1.0.0  @since 1.0 */
if (!function_exists('get_pm')) {
    /**
     * @param string $name
     * @param bool $everything
     */
    function get_pm($name = '', $everything = false)
    {
        return PaymentMethod::get_data($name, $everything);
    }
}

/* @function get_pm()  @version v.1.0.0  @since 1.0 */
if (!function_exists('get_b_data')) {
    function get_b_data($name = '', $everything = false)
    {
        return PaymentMethod::get_bank_data($name, $everything);
    }
}

/* @function is_mail_setting_exist()  @version v.1.0.0  @since 1.0 */
if (!function_exists('is_mail_setting_exist')) {
    function is_mail_setting_exist()
    {
        $driver = get_setting('site_mail_driver');
        $host = get_setting('site_mail_host');
        $port = get_setting('site_mail_port');
        $address = get_setting('site_mail_from_address', 'info@yourdomain.com');
        $username = get_setting('site_mail_username');
        $password = get_setting('site_mail_password');
        $encryption = get_setting('site_mail_encryption', 'tls');
        if ($driver != null && $host != null && $port != null && $address != null && $username != null && $password != null && $encryption != null) {
            return true;
        } else {
            return false;
        }
    }
}

/* @function is_payment_method_exist()  @version v.1.0.0  @since 1.0 */
if (!function_exists('is_payment_method_exist')) {
    function is_payment_method_exist($method = '')
    {
        $data = PaymentMethod::get_data();
        $manual = $data->manual;

        // Manual active or not
        $is_active_manual_eth = ($manual->status == 'active' && $manual->secret->eth->address != null && $manual->secret->eth->status == 'active') ? true : false;
        $is_active_manual_btc = ($manual->status == 'active' && $manual->secret->btc->address != null && $manual->secret->btc->status == 'active') ? true : false;
        $is_active_manual_ltc = ($manual->status == 'active' && $manual->secret->ltc->address != null && $manual->secret->ltc->status == 'active') ? true : false;
        $is_active_manual_bank = ($manual->status == 'active' && $manual->secret->bank->bank_account_name != null && $manual->secret->bank->bank_account_number != null && $manual->secret->bank->bank_name != null && $manual->secret->bank->status == 'active') ? true : false;

        $is_active_manual = ($manual->status == 'active' && ($is_active_manual_eth || $is_active_manual_btc || $is_active_manual_ltc || $is_active_manual_bank)) ? true : false;

        $is_payment_method_exist = ($is_active_manual) ? true : false;

        // Return Manual
        if ($method == 'manual' || $method == 'manual_eth' || $method == 'manual_btc' || $method == 'manual_ltc' || $method == 'manual_bank') {
            if ($method == 'manual_eth') {
                return $is_active_manual_eth;
            }

            if ($method == 'manual_btc') {
                return $is_active_manual_btc;
            }

            if ($method == 'manual_ltc') {
                return $is_active_manual_ltc;
            }

            if ($method == 'manual_bank') {
                return $is_active_manual_bank;
            }

            return $is_active_manual;
        }

        return ($method == 'array') ? $data : $is_payment_method_exist;
    }
}

/* @function short_to_full()  @version v.1.0.0  @since 1.0 */
if (!function_exists('short_to_full')) {
    function short_to_full($name)
    {
        if ($name == 'eth') {
            return 'Etherum';
        }

        if ($name == 'btc') {
            return 'Bitcoin';
        }

        if ($name == 'ltc') {
            return 'Litecoin';
        }

        if ($name == 'ppl') {
            return 'PayPal';
        }

        if ($name == 'usd') {
            return 'Bank';
        }

        return '';
    }
}

/* @function transaction_by()  @version v.1.0.0  @since 1.0 */
if (!function_exists('transaction_by')) {
    function transaction_by($data)
    {
        if ($data == null) {
            return 'Not mentioned.';
        } else {
            $id = abs((int) filter_var($data, FILTER_SANITIZE_NUMBER_INT));
            return $id != null ? User::FindOrFail($id)->name : 'System';
        }
    }
}

/* @function approved_by()  @version v.1.0.0  @since 1.0 */
if (!function_exists('approved_by')) {
    function approved_by($data)
    {
        if ($data == null) {
            return 'Not Reviewed Yet.';
        }
        $data = is_json($data) ? json_decode($data) : $data;
        $return = $data;

        if (isset($data->name)) {
            $return = $data->name;
        } elseif (isset($data->id)) {
            $id = is_numeric($data->id) ? $data->id : (is_numeric($data) ? $data : 0);
            $user = User::find($id);
            if ($user) {
                $return = $user->name;
            }
        } else {
            $id = is_numeric($data) ? $data : 0;
            $user = User::find($id);
            if ($user) {
                $return = $user->name;
            }
        }
        return $return;
    }
}

/* @function token_price()  @version v.1.0.0  @since 1.0 */
if (!function_exists('token_price')) {
    function token_price($number, $currency = 'usd')
    {
        $price = get_setting('pmc_rate_' . $currency);
        if ($price == null) {
            $price = 0;
        }
        if (base_currency() == $currency) {
            $price = active_stage()->base_price;
        }

        $result = ((int) $number * (double) $price);

        return $result == 0 ? '~' : $result;
    }
}

/* @function active_currency()  @version v.1.0.0  @since 1.0 */
if (!function_exists('active_currency')) {
    function active_currency($active = '')
    {
        $currencies = PaymentMethod::Currency;
        $currency = [];
        foreach ($currencies as $pmg => $pmval) {
            if (get_setting('pmc_active_' . $pmg) == 1) {
                array_push($currency, $pmg);
            }
        }

        return $active ? (in_array(strtolower($active), $currency) ? true : false) : $currency;
    }
}

/* @function get_exc_rate  @version v.1.0.0  @since 1.0 */
if (!function_exists('get_exc_rate')) {
    function get_exc_rate($currency = '')
    {
        return Setting::active_currency($currency);
    }
}

/* @function get_whitepaper()  @version v.1.0.0  @since 1.0 */
if (!function_exists('get_whitepaper')) {
    function get_whitepaper($out='')
    {
        $return = '';
        $wpaper_link = (get_setting('site_white_paper') != '') ? route('public.white.paper') : '';
        if ($wpaper_link) {
            if ($out=='link') {
                $return = '<a href="'.$wpaper_link.'" target="_blank">'. __('Download Whitepaper') .'</a>';
            } elseif ($out=='button') {
                $return = '<a href="'.$wpaper_link.'" target="_blank" class="btn btn-primary"><em class="fas fa-download mr-3"></em>'. __('Download Whitepaper') .'</a>'; 
            } else {
                $return = $wpaper_link;
            }
        }
        
        return $return;
    }
}

/* @function replace_shortcode()  @version v.1.0.0  @since 1.0 */
if (!function_exists('replace_shortcode')) {
    function replace_shortcode($string)
    {
        $whitepaper = get_whitepaper();
        
        $shortcode = array(
            '[[token_name]]',
            '[[token_symbol]]',
            '[[site_name]]',
            '[[site_email]]',
            '[[user_name]]',
            '[[site_url]]',
            '[[whitepaper_download_link]]',
            '[[whitepaper_download_button]]'
        );
        $replace = array(
            token('name'),
            token('symbol'),
            site_info('name', false),
            site_info('email', false),
            (auth()->check() ? auth()->user()->name : 'User'),
            url('/'),
            get_whitepaper('link'),
            get_whitepaper('button')
        );

        $return = str_replace($shortcode, $replace, $string);
        return $return;
    }
}

/* @function replace_with()  @version v.1.0.0  @since 1.0 */
if (!function_exists('replace_with')) {
    function replace_with($string, $where, $replace)
    {
        $return = str_replace($where, $replace, $string);
        return $return;
    }
}

/* @function kyc_status()  @version v.1.0.0  @since 1.0 */
if (!function_exists('kyc_status')) {
    function kyc_status($id)
    {
        $kyc = KYC::FindOrFail($id);
        return $kyc->status != null ? ucfirst($kyc->status) : 'Pending';
    }
}

/* @function get_page()  @version v.1.0.0  @since 1.0 */
if (!function_exists('get_page')) {
    function get_page($slug, $get = '')
    {
        $data = Page::get_page($slug, $get);
        $return = ($data != null ? $data : '');
        return ($get == null ? $return : replace_shortcode($return));
    }
}

/* @function get_page()  @version v.1.0.0  @since 1.0 */
if (!function_exists('get_slug')) {
    function get_slug($slug)
    {
        $data = Page::get_slug($slug);
        $return = ($data != null ? $data : $slug);
        return $return;
    }
}

/* @function get_page_link()  @version v.1.0.0  @since 1.0 */
if (!function_exists('get_page_link')) {
    function get_page_link($name = '', $attr = null)
    {
        $class = isset($attr['class']) ? ' class="' . $attr['class'] . '"' : '';
        $target = isset($attr['target']) ? ' target="' . $attr['target'] . '"' : '';
        $pages_slug = [
            'htb' => 'home_top',
            'hbb' => 'home_bottom',
            'htb' => 'how_buy',
            'faq' => 'faq',
            'policy' => 'privacy',
            'terms' => 'terms',
            'ref' => 'referral',
            'icod' => 'distribution',
            'cp' => 'custom_page',
        ];
        $page = get_page($pages_slug[$name]) ?? get_page($name);
        if ($page) {
            $link = '<a' . $class . $target . ' href="' . route('public.pages', $page->custom_slug) . '">' . $page->title . '</a>';
            $text = $page->title;
            if ($page->status == 'active') {
                $result = $link;
            } else {
                $result = $text;
            }
        } else {
            $result = ucfirst(str_replace('-', ' ', $pages_slug[$name]));
        }

        return $result;
    }
}

/* @function check_expire()  @version v.1.0.0  @since 1.0 */
if (!function_exists('check_expire')) {
    function check_expire($date, $current_date = '')
    {
        if ($current_date == '') {
            $current_date = date('Y-m-d');
        }

        if (_date($date, 'Y-m-d') >= $current_date) {
            return true; // That means user Subscription available.
        } else {
            return false; // That means user Subscription expired.
        }
    }
}

/* @function is_https_active()  @version v.1.0.0  @since 1.0 */
if (!function_exists('is_https_active')) {
    function is_https_active()
    {
        if (config('icoapp.force_https')) {
            return true;
        } else {
            return false;
        }
    }
}

/* @function auto_p()  @version v.1.0.0  @since 1.0 */
if (!function_exists('auto_p')) {
    function auto_p($pee, $br = true)
    {
        $pre_tags = array();

        if (trim($pee) === '') {
            return '';
        }

        $pee = $pee . "\n";
        if (strpos($pee, '<pre') !== false) {
            $pee_parts = explode('</pre>', $pee);
            $last_pee = array_pop($pee_parts);
            $pee = '';
            $i = 0;

            foreach ($pee_parts as $pee_part) {
                $start = strpos($pee_part, '<pre');
                if ($start === false) {
                    $pee .= $pee_part;
                    continue;
                }

                $name = "<pre pre-tag-$i></pre>";
                $pre_tags[$name] = substr($pee_part, $start) . '</pre>';

                $pee .= substr($pee_part, 0, $start) . $name;
                $i++;
            }

            $pee .= $last_pee;
        }

        $pee = preg_replace('|<br\s*/?>\s*<br\s*/?>|', "\n\n", $pee);

        $allblocks = '(?:table|thead|tfoot|caption|col|colgroup|tbody|tr|td|th|div|dl|dd|dt|ul|ol|li|pre|form|map|area|blockquote|address|math|style|p|h[1-6]|hr|fieldset|legend|section|article|aside|hgroup|header|footer|nav|figure|figcaption|details|menu|summary)';

        $pee = preg_replace('!(<' . $allblocks . '[\s/>])!', "\n\n$1", $pee);
        $pee = preg_replace('!(</' . $allblocks . '>)!', "$1\n\n", $pee);
        $pee = str_replace(array("\r\n", "\r"), "\n", $pee);
        $pee = IcoHandler::replace_in_html_tags($pee, array("\n" => " <!-- nl --> "));
        if (strpos($pee, '<option') !== false) {
            $pee = preg_replace('|\s*<option|', '<option', $pee);
            $pee = preg_replace('|</option>\s*|', '</option>', $pee);
        }

        $pee = preg_replace("/\n\n+/", "\n\n", $pee);
        $pees = preg_split('/\n\s*\n/', $pee, -1, PREG_SPLIT_NO_EMPTY);
        $pee = '';

        foreach ($pees as $tinkle) {
            $pee .= '<p>' . trim($tinkle, "\n") . "</p>\n";
        }

        $pee = preg_replace('|<p>\s*</p>|', '', $pee);
        $pee = preg_replace('!<p>([^<]+)</(div|address|form)>!', "<p>$1</p></$2>", $pee);
        $pee = preg_replace('!<p>\s*(</?' . $allblocks . '[^>]*>)\s*</p>!', "$1", $pee);
        $pee = preg_replace("|<p>(<li.+?)</p>|", "$1", $pee);
        $pee = preg_replace('|<p><blockquote([^>]*)>|i', "<blockquote$1><p>", $pee);
        $pee = str_replace('</blockquote></p>', '</p></blockquote>', $pee);
        $pee = preg_replace('!<p>\s*(</?' . $allblocks . '[^>]*>)!', "$1", $pee);
        $pee = preg_replace('!(</?' . $allblocks . '[^>]*>)\s*</p>!', "$1", $pee);

        if ($br) {
            $pee = str_replace(array('<br>', '<br/>'), '<br />', $pee);
            $pee = preg_replace('|(?<!<br />)\s*\n|', "<br />\n", $pee);
        }

        $pee = preg_replace('!(</?' . $allblocks . '[^>]*>)\s*<br />!', "$1", $pee);
        $pee = preg_replace('!<br />(\s*</?(?:p|li|div|dl|dd|dt|th|pre|td|ul|ol)[^>]*>)!', '$1', $pee);
        $pee = preg_replace("|\n</p>$|", '</p>', $pee);
        if (!empty($pre_tags)) {
            $pee = str_replace(array_keys($pre_tags), array_values($pre_tags), $pee);
        }

        return $pee;
    }
}

/* @function parse_args()  @version v.1.0.0  @since 1.0 */
if (!function_exists('parse_args')) {
    function parse_args($args, $defaults = '')
    {
        if (is_object($args)) {
            $r = get_object_vars($args);
        } elseif (is_array($args)) {
            $r = &$args;
        } else {
            parse_str($args, $r);
        }

        if (is_array($defaults)) {
            return array_merge($defaults, $r);
        }

        return $r;
    }
}

/* @function css_js_ver()  @version v.1.0.0  @since 1.0 */
if (!function_exists('css_js_ver')) {
    function css_js_ver($echo = false)
    {
        $cache = true;
        $vers = (app_info('vers')) ? app_info('vers') : app_info('version');

        $version = ($cache === false) ? time() : str_replace('.', '', $vers);
        $version = '?ver=' . $version;

        if ($echo === false) {
            return $version;
        }

        echo $version;
    }
}

/* @function is_maintenance()  @version v.1.0.0  @since 1.0 */
if (!function_exists('is_maintenance')) {
    function is_maintenance()
    {
        if (\Illuminate\Support\Facades\Schema::hasTable('settings')) {
            if (get_setting('site_maintenance') == 1) {
                return true;
            }
            return false;
        }

        return false;
    }
}
/* @function arr_convert()  @version v.1.0.0  @since 1.0 */
if (!function_exists('arr_convert')) {
    function arr_convert($array = null)
    {
        $data = [];
        foreach ($array as $key => $value) {
            if ((is_array($value) || is_object($value)) && count($value) == 1) {
                $data[$key] = (array) $value[0];
            } else {
                $data[$key] = (is_array($value) ? arr_convert($value) : $value);
            }
        }
        return $data;
    }
}


/* @function ico_stage_progress()  @version v.1.0.0  @since 1.0 */
if (!function_exists('ico_stage_progress')) {
    function ico_stage_progress($type, $in_currency='token') {
        $stage = active_stage();
        $sc = ($stage->soft_cap*100 / $stage->total_tokens);
        $hc = ($stage->hard_cap*100 / $stage->total_tokens);
        $cur = ($in_currency=='token') ? token_symbol() : strtoupper($in_currency);
        if($type == 'soft'){
            $data = ($sc >= 10 && $sc <= 40 ) ? $sc : 10;
        }elseif($type == 'hard'){
            $data = ($hc >= 60 && $hc <= 90 ) ? $hc : 90;
        }elseif($type == 'raised'){
            $tp = token_price($stage->sales_token, $in_currency) > 0 ? token_price($stage->sales_token, $in_currency) : 0;
            $data = ($in_currency == 'token' ? number_format($stage->sales_token) : number_format($tp)).' '. $cur;
        }elseif($type == 'softtoken'){
            $data = ($in_currency == 'token' ? number_format($stage->soft_cap) : number_format(token_price($stage->soft_cap, $in_currency))).' '. $cur;
        }elseif($type == 'hardtoken'){
            $data = ($in_currency == 'token' ? number_format($stage->hard_cap) : number_format(token_price($stage->hard_cap, $in_currency))).' '. $cur;
        }
        return $data;
    }
}

/* @function explode_user_for_demo()  @version v.1.0.0  @since 1.0 */
if (!function_exists('explode_user_for_demo')) {
    function explode_user_for_demo($data, $user_type) {
       if($user_type == 'demo'){
            $data = substr($data, 0,3).'...'.substr($data, -3);
       }

       return $data;
    }
}

/* @function get_base_bonus()  @version v.1.0.1  @since 1.0 */
if (!function_exists('get_base_bonus')) {
    function get_base_bonus($id, $type=null) {
        $tc = new TokenCalculate();
        $bonus = NULL;
        if(!empty($id)){
            $bonus = $tc->get_current_bonus($type, $id); // Specific Base Bonus
        } else {
            $bonus = $tc->get_current_bonus($type, null); // Active Stage Bonus
        }
        return $bonus;
    }
}
