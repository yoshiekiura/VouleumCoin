<?php
/**
 * Settings Model
 *
 *  Manage the Website Settings
 *
 * @package TokenLite
 * @author Softnio
 * @version 1.0
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    /*
     * Table Name Specified
     */
    protected $table = 'settings';

    //declare settings key
    const SITE_NAME = "site_name",
    SITE_EMAIL = "site_email";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['field', 'value'];

    /**
     * Get value
     * @param $field
     * @param $add boolean
     * @return string    /**
     *
     *
     * @version 1.0.0
     * @since 1.0
     */
    public static function getValue($name, $add = false)
    {
        $result = self::where('field', $name)->value('value');
        if ($result == null) {
            if ($add == true) {
                $setting = self::create([$name => 'null']);
            }
            $result = '';
        }

        return $result;
    }

    /**
     * Convert price to another currency
     * @param $price
     * @param $toCurrency
     * @return $newPrice    /**
     *
     *
     * @version 1.0.0
     * @since 1.0
     * @return void
     */
    public static function active_currency($output = '')
    {
        $all_currency = array_keys(PaymentMethod::Currency);
        $currencies = [];

        foreach ($all_currency as $item) {
            if (get_setting('pmc_active_' . $item)) {
                if (get_setting('pm_exchange_method') == 'automatic') {
                    $currencies[$item] = get_setting('pmc_auto_rate_' . strtolower($item));
                } else {
                    $currencies[$item] = get_setting('pmc_rate_' . $item);
                }
            }
        }

        if (empty($output)) {
            return $currencies;
        } else {
            return isset($currencies[$output]) ? $currencies[$output] : '';
        }
    }
    /**
     *
     * Exchange rate
     *
     * @version 1.0.0
     * @since 1.0
     * @return void
     */
    public static function exchange_rate($amount, $output = '')
    {
        if (empty($amount)) {
            return false;
        }

        $return = 0;

        $base_currency = get_setting('site_base_currency');
        $decimal = token('decimal_max');
        $currency_rate = self::active_currency();
        $exchange_rate = [];

        foreach ($currency_rate as $currency => $rate) {
            $currency = strtolower($currency);
            if ($currency == strtolower($base_currency)) {
                $exchange_rate[$currency] = round(($amount * 1), $decimal);
            } else {
                $exchange_rate[$currency] = round(($amount * $rate), $decimal);
            }
        }
        $exchange_rate['base'] = $amount;

        if (empty($output)) {
            $return = $exchange_rate;
        } else {
            $output = strtolower($output);
            $return = $exchange_rate[$output];
        }
        return $return;
    }

    /**
     * Update value
     * @param $field
     * @param $value
     *
     * @version 1.0.0
     * @since 1.0
     * @return void
     */
    public static function updateValue($field, $value)
    {
        $get = self::where('field', $field)->first();
        if ($get == null) {
            $get = new self();
            $get->field = $field;
        }
        $get->value = $value;
        $get->save();
        if ($get) {
            return true;
        } else {
            return false;
        }
    }
}
