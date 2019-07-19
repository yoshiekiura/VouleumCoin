<?php
/**
 * Transaction Model
 *
 *  Manage the Transactions
 *
 * @package TokenLite
 * @author Softnio
 * @version 1.0
 */
namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{

    /*
     * Table Name Specified
     */
    protected $table = 'transactions';
    /**
     *
     * Relation with user
     *
     * @version 1.0.0
     * @since 1.0
     * @return void
     */
    public function tnxUser()
    {
        return $this->belongsTo('App\Models\User', 'user', 'id');
    }
    /**
     *
     * Relation with user by id
     *
     * @version 1.0.0
     * @since 1.0
     * @return void
     */
    public function user($id)
    {
        return \App\Models\User::find($id);
    }
    /**
     *
     * Relation with stage
     *
     * @version 1.0.0
     * @since 1.0
     * @return void
     */
    public function ico_stage()
    {
        return $this->belongsTo('App\Models\IcoStage', 'stage', 'id');
    }

    /**
     *
     * Dashboard data
     *
     * @version 1.0.0
     * @since 1.0
     * @return void
     */
    public static function dashboard($chart = 7)
    {
        // $tnx = self::where()
        $data['currency'] = (object) [
            'usd' => number_format(self::amount_count('USD')->total, 2),
            'eth' => number_format(self::amount_count('ETH')->total, 2),
            'ltc' => number_format(self::amount_count('LTC')->total, 2),
            'btc' => number_format(self::amount_count('BTC')->total, 2),
        ];
        $data['chart'] = self::chart($chart);

        $data['all'] = self::where('status', '!=', 'deleted')->where('status', '!=', 'new')->orderBy('created_at', 'DESC')->limit(4)->get();

        return (object) $data;
    }
    /**
     *
     * Count the amount
     *
     * @version 1.0.0
     * @since 1.0
     * @return void
     */
    public static function amount_count($name = '', $extra = '')
    {
        $data['total'] = 0;
        $all = self::where(['status' => 'approved', 'currency' => $name])->get();
        if ($extra !== '') {
            $all = self::where(['status' => 'approved', 'currency' => $name])->where($extra)->get();
        }
        foreach ($all as $tnx) {
            $data['total'] += $tnx->amount;
        }
        return (object) $data;
    }
    /**
     *
     * Chart data
     *
     * @version 1.0.0
     * @since 1.0
     * @return void
     */
    public static function chart($get = 6)
    {
        $cd = Carbon::now(); //->toDateTimeString();
        $lw = $cd->copy()->subDays($get);

        $cd = $cd->copy()->addDays(1);
        $df = $cd->diffInDays($lw);
        $transactions = self::where('status', '!=', 'deleted')
            ->whereBetween('created_at', [$lw, $cd])
            ->orderBy('created_at', 'DESC')
            ->get();
        $data['days'] = null;
        $data['data'] = null;
        $data['data_alt'] = null;
        $data['days_alt'] = null;
        for ($i = 1; $i <= $df; $i++) {
            $tokens = 0;
            foreach ($transactions as $tnx) {
                $tnxDate = date('Y-m-d', strtotime($tnx->tnx_time));
                if ($lw->format('Y-m-d') == $tnxDate) {
                    $tokens += $tnx->total_tokens;
                } else {
                    $tokens += 0;
                }
            }
            $data['data'] .= $tokens . ",";
            $data['data_alt'][$i] = $tokens;
            $data['days_alt'][$i] = ($get > 90 ? $lw->format('d M Y') : $lw->format('d M'));
            $data['days'] .= '"' . $lw->format('d M') . '",';
            $lw->addDay();
        }
        return (object) $data;
    }
    /**
     *
     * User contribution
     *
     * @version 1.0.0
     * @since 1.0
     * @return void
     */
    public static function user_contribution()
    {
        $all = self::where(['user' => auth()->id(), 'status' => 'approved'])->get();
        $total = 0;

        foreach ($all as $tnx) {
            $total += $tnx->base_amount;
        }
        $data = [];
        $curs = array_keys(\App\Models\PaymentMethod::Currency);
        foreach ($curs as $cur) {
            if (get_setting('pmc_active_' . $cur) == 1 && get_setting('pmc_rate_' . $cur) > 0) {
                $data[$cur] = token_rate($total, $cur);
            }
        }
        $data['base'] = $total;

        return (object) $data;
    }
}
