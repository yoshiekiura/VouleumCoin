<?php
/**
 * PaymentMethod Model
 *
 *  Manage the Payment Method Settings
 *
 * @package TokenLite
 * @author Softnio
 * @version 1.0
 */
namespace App\Models;

use App\Models\Setting;
use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    /*
     * Table Name Specified
     */
    protected $table = 'payment_methods';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['payment_method', 'symbol', 'title', 'description', 'data'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    const Currency = ['eth' => 'Ethereum', 'btc' => 'Bitcoin', 'ltc' => 'Litecoin', 'usd' => 'US Dollar'];

    public function __construct()
    {
        $auto_check = (60 * (int) get_setting('pm_automatic_rate_time', 60)); // 1 Hour

        $this->save_default();
        $this->automatic_rate_check($auto_check);
    }
    /**
     *
     * Get the data
     *
     * @version 1.0.0
     * @since 1.0
     * @return void
     */
    public static function get_data($name = '', $everything = false)
    {
        $all = self::all();
        $result = [];
        foreach ($all as $data) {
            $result[$data->payment_method] = (object) [
                'status' => $data->status,
                'title' => $data->title,
                'details' => $data->description,
                'secret' => json_decode($data->data),
            ];
        }
        if ($name !== '') {
            return ($everything == true ? $result[$name] : $result[$name]->secret);
        }
        return (object) $result;
    }

    /**
     *
     * Get the data
     *
     * @version 1.0.0
     * @since 1.0
     * @return void
     */
    public static function get_bank_data($name = '', $everything = false)
    {
        $all = json_decode(self::where('payment_method', $name)->first()->data)->bank;
        return (object) $all;
    }

    /**
     *
     * Get single data
     *
     * @version 1.0.0
     * @since 1.0
     * @return void
     */
    public static function get_single_data($name)
    {
        $data = self::where('payment_method', $name)->first();
        $data->secret = ($data != null) ? json_decode($data->data) : null;

        return ($data != null) ? $data : null;
    }
    /**
     *
     * Save the default
     *
     * @version 1.0.0
     * @since 1.0
     * @return void
     */
    public function save_default()
    {
        foreach (self::Currency as $key => $value) {
            if (Setting::getValue('pmc_active_' . $key) == '') {
                Setting::updateValue('pmc_active_' . $key, 1);
            }
            if (Setting::getValue('pmc_rate_' . $key) == '') {
                Setting::updateValue('pmc_rate_' . $key, 1);
            }
        }
    }
    /**
     *
     * Check
     *
     * @version 1.0.0
     * @since 1.0
     * @return void
     */
    public static function check($name = '')
    {
        $data = self::where('payment_method', $name)->count();
        return ($data > 0) ? false : true;
    }

    /**
     *
     * Set Exchange rates from coingate api between a several time
     *
     * @version 1.0.0
     * @since 1.0.0
     * @return void
     */
    public function automatic_rate_check($between = 3600)
    {
        $check_time = get_setting('pm_exchange_auto_lastcheck', now()->subMinutes(10));
        $current_time = now();
        if ((strtotime($check_time) + ($between)) <= strtotime($current_time)) {
            $rate = self::automatic_rate(base_currency(true));

            foreach (self::Currency as $gt => $val) {
                $auto_currency = strtoupper($gt);
                $new_rate = (isset($rate->$auto_currency) ? $rate->$auto_currency : 1);
                Setting::updateValue('pmc_auto_rate_' . strtolower($gt), $new_rate);
            }

            Setting::updateValue('pm_exchange_auto_lastcheck', now());
        }
    }

    /**
     *
     * Get automatic rates
     *
     * @version 1.0.0
     * @since 1.0.0
     * @return void
     */
    public static function automatic_rate($base = '')
    {
        $cl = new Client();
        $base_currency = base_currency(true);
        $check_time = get_setting('pm_exchange_auto_lastcheck', now()->subMinutes(5));
        $current_time = now();
        if ((strtotime($check_time)) <= strtotime($current_time)) {
            $all_currency = array_keys(self::Currency);
            $all = "";
            foreach ($all_currency as $cur) {
                $all .= strtoupper($cur) . ',';
            }
            $all = (ends_with($all, ',') ? substr($all, 0, -1) : $all);
            $data = self::default_rate();
            try {
                $response = $cl->get('https://min-api.cryptocompare.com/data/price?fsym=' . $base . '&tsyms=' . $all . '');
                $data = json_decode($response->getBody());
            } finally {
                return $data;
            }
        }
    }

    public static function default_rate()
    {
        $currencies = self::Currency;
        $old = [];
        foreach ($currencies as $cur => $value) {
            $cur = strtoupper($cur);
            $old[$cur] = get_setting('pmc_auto_rate_' . $cur);
        }
        $old['default'] = true;

        return (object) $old;
    }
}
