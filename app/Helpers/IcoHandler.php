<?php
namespace App\Helpers;

/**
 * ICO Handler Class
 *
 * This class retrieve address validation, countries names,
 *check license, active/inactive product etc.
 *
 * @package TokenLite
 * @author Softnio
 * @version 1.0
 */
use App\Helpers\AddressValidation;
use App\Helpers\IcoHandler;
use Illuminate\Support\Facades\Schema;
use DB;
use Auth;
use Closure;

class IcoHandler
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if(file_exists(storage_path('installed'))){
           return $next($request);
        }
        else{
            return redirect()->route('home');
        }
    }

    public static function _message()
    {
        $text = "<!-- TokenLite v" . config('app.version') . ". Developed by Softnio. -->\n";
        return $text;
    }

    /* @function panel_info()  @version v.1.0.0 */
    public static function panel_info($output = '')
    {
        $name = config('app.name');
        $version = config('app.version');
        $update = config('app.update');
        $return = $name;

        if ($output == 'version') {
            $return = $version;
        }

        if ($output == 'update') {
            $return = $update;
        }

        if ($output == 'vers') {
            $return = $update . $version;
        }

        return $return;
    }

    /* @function css_class_generate()  @version v.1.0.0
     * @param string $str
     * @param string $key
     * @param array $args
     * @return string
     */
    public static function css_class_generate($str = '', $key = '', $args = array())
    {
        if (empty($str)) {
            return '';
        }

        $out = '';
        $args_def = array(
            'space' => 1,
            'sep' => '-',
            'after' => '',
            'single' => '',
            'prefix' => 0,
        );
        $opt_args = parse_args($args, $args_def);
        extract($opt_args);
        $nodes = 'first last start end even odd clear';
        $junks = array('|', '/', '#', '!', ':', ';', '@', '*', '&', '$', '~', '%', '^', '_', '+', '=', '?');
        if ($single) {
            $nodes .= ' ' . $single;
        }

        if ($after) {
            $after = $sep . $after;
        }

        $strs = (is_array($str)) ? $str : explode(' ', $str);
        $excs = explode(' ', $nodes);
        $strs_len = count($strs);
        $i = 0;
        foreach ($strs as $strx) {
            $i++;
            if ($strx) {
                if (in_array($strx, $excs) || empty($key)) {
                    $strx = str_replace($junks, '-', $strx);
                    $strx = (is_numeric(substr($strx, 0, 1))) ? 'n' . $strx : $strx;
                    $out .= $strx;
                    $out .= ($i < $strs_len) ? ' ' : '';
                } else {
                    if ($prefix == true || $prefix == 1) {
                        $strx = str_replace($junks, '-', $strx);
                        $strx = (is_numeric(substr($strx, 0, 1))) ? 'n' . $strx : $strx;
                        $out .= $strx . $sep . $key . $after;
                        $out .= ($i < $strs_len) ? ' ' : '';
                    } else {
                        $strx = str_replace($junks, '-', $strx);
                        $strx = (is_numeric(substr($strx, 0, 1))) ? 'n' . $strx : $strx;
                        $out .= $key . $sep . $strx . $after;
                        $out .= ($i < $strs_len) ? ' ' : '';
                    }
                }
            }
        }

        $out = ($space == 0) ? $out : ' ' . $out;
        return $out;
    }

    /* @function validate_address()  @version v.1.0.0 */
    public static function validate_address($address, $name = '')
    {
        $name = str_replace(['ethereum', 'bitcoin', 'litecoin', 'dash', 'waves'], ['eth', 'btc', 'ltc', 'dash', 'waves'], strtolower($name));
        $validate = new AddressValidation($address);
        return $validate->validate($name);
    }

    /* @function get_token_settings()  @version v.1.0.0 */
    public static function get_token_settings($type = '')
    {
        if ($type == '') {
            return '';
        }

        if (get_setting('token_' . $type)) {
            return get_setting('token_' . $type) != '' ? get_setting('token_' . $type) : '';
        } else {
            return '';
        }
    }

    /* @function get_manual_payment()  @version v.1.0.0 */
    public static function get_manual_payment($type, $ext = '', $active = true)
    {
        if (empty($type)) {
            return false;
        }

        if ($active === true && is_payment_method_exist('manual') === false) {
            return false;
        }

        $status = is_payment_method_exist('manual_' . $type);
        if ($type == 'usd') {
            return get_b_data('manual');
        } else {
            $address = get_pm('manual')->$type->address;

            if ($ext == 'limit' || $ext == 'price') {
                $address = (get_pm('manual')->$type->$ext) ? get_pm('manual')->$type->$ext : '';
            }

            return ($status && $address) ? $address : false;
        }
    }

    /* @function string_compact()  @version v.1.0.0 */
    public static function string_compact($string, $length = 5)
    {
        return substr($string, 0, $length) . '....' . substr($string, -$length);
    }

    /* @function check_user_wallet()  @version v.1.0.0 */
    public static function check_user_wallet($get = '')
    {
        $return = $wallet = false;
        if (auth()->check()) {
            return (auth()->user()->walletAddress != null ? true : false);
        }
        return ($get === true) ? $wallet : $return;
    }

    /* @function get_html_split_regex()  @version v.1.0.0 */
    public static function get_html_split_regex()
    {
        static $regex;
        if (!isset($regex)) {
            $coms = '!' . '(?:' . '-(?!->)' . '[^\-]*+' . ')*+' . '(?:-->)?';
            $cdata = '!\[CDATA\[' . '[^\]]*+' . '(?:' . '](?!]>)' . '[^\]]*+' . ')*+' . '(?:]]>)?';
            $escaped = '(?=' . '!--' . '|' . '!\[CDATA\[' . ')' . '(?(?=!-)' . $coms . '|' . $cdata . ')';
            $regex = '/(' . '<' . '(?' . $escaped . '|' . '[^>]*>?' . ')' . ')/';
        }
        return $regex;
    }

    /* @function replace_in_html_tags()  @version v.1.0.0 */
    public static function replace_in_html_tags($hstack, $replace_pairs)
    {
        $textarr = preg_split(self::get_html_split_regex(), $hstack, -1, PREG_SPLIT_DELIM_CAPTURE);
        $changed = false;

        if (1 === count($replace_pairs)) {
            foreach ($replace_pairs as $needle => $replace);

            for ($i = 1, $c = count($textarr); $i < $c; $i += 2) {
                if (false !== strpos($textarr[$i], $needle)) {
                    $textarr[$i] = str_replace($needle, $replace, $textarr[$i]);
                    $changed = true;
                }
            }
        } else {
            $needles = array_keys($replace_pairs);

            for ($i = 1, $c = count($textarr); $i < $c; $i += 2) {
                foreach ($needles as $needle) {
                    if (false !== strpos($textarr[$i], $needle)) {
                        $textarr[$i] = strtr($textarr[$i], $replace_pairs);
                        $changed = true;
                        break;
                    }
                }
            }
        }

        if ($changed) {
            $hstack = implode($textarr);
        }

        return $hstack;
    }

    /* @function getCountries()  @version v.1.0.0 */
    public static function getCountries()
    {
        $countries = config('icoapp.countries');
        return $countries;
    }
    /* @function get_timezones()  @version v.1.0.0 */
    public static function get_timezones()
    {
        $timezone = config('icoapp.timezones');
        return $timezone;
    }

    public static function checkDB()
    {
        if( ! application_installed(true)) return [];
        $tables = ['activities', 'email_templates', 'global_metas', 'ico_metas', 'ico_stages', 'kycs', 'migrations', 'pages', 'password_resets', 'payment_methods', 'settings', 'transactions', 'users', 'user_metas'];
        $result = NULL;
        $return = NULL;
        foreach ($tables as $table) {
            $check = Schema::hasTable($table);
            $result[$table] = $check;
        }

        $return = array_keys($result, false);
        return $return;

    }
}
