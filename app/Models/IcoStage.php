<?php
/**
 * IcoStage Model
 *
 *  Manage the ICO Stage data
 *
 * @package TokenLite
 * @author Softnio
 * @version 1.0
 */
namespace App\Models;

use App\Models\IcoMeta;
use Illuminate\Database\Eloquent\Model;

class IcoStage extends Model
{
    /*
     * Table Name Specified
     */
    protected $table = 'ico_stages';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'start_date', 'end_date', 'total_tokens', 'base_price', 'display_mode'];

    /**
     *
     * Relation with Meta
     *
     * @version 1.0.0
     * @since 1.0
     * @return void
     */
    public function ico_meta($what = null, $which = null)
    {
        if ($what == null) {
            return $this->belongsTo('App\Models\IcoMeta', 'id', 'stage_id');
        } else {
            $meta = IcoMeta::where('stage_id', $this->id)->where('option_name', $what)->first();
            if ($which != null) {
                $res = json_decode($meta->option_value);
                return (isset($res->$which) ? $res->$which : $res);
            }
            return $meta;
        }

    }
    /**
     *
     * Dashboard data
     *
     * @version 1.0.0
     * @since 1.0
     * @return void
     */
    public static function dashboard()
    {
        $tnxs = self::check_stage(active_stage()->id);

        $data['stage'] = active_stage();

        $data['trnxs'] = (object) [
            'all' => Transaction::where('status', 'approved')->count(),
            'percent' => ceil(((active_stage()->sales_token) * 100) / active_stage()->total_tokens),
            'last_week' => $tnxs->tokens,
        ];

        $data['totalSummary'] = self::summary();

        $data['phase'] = self::ico_phase();

        return (object) $data;
    }
    /**
     *
     * ICO Phase
     *
     * @version 1.0.0
     * @since 1.0
     * @return void
     */
    public static function ico_phase()
    {
        $all = self::orderBy('created_at', 'DESC')->get();
        $data = [];
        foreach ($all as $ico) {
            $ico->extra = (object) [
                'usd' => Transaction::amount_count('usd', ['stage' => $ico->id]),
                'eth' => Transaction::amount_count('eth', ['stage' => $ico->id]),
                'ltc' => Transaction::amount_count('ltc', ['stage' => $ico->id]),
                'btc' => Transaction::amount_count('btc', ['stage' => $ico->id]),
            ];
            array_push($data, $ico);
        }
        return (object) $data;
    }
    /**
     *
     * Dashboard total tokens summary
     *
     * @version 1.0.0
     * @since 1.0
     * @return void
     */
    public static function summary($id = '')
    {
        $all = $sold = $amount = 0;
        if ($id == '') {
            $is = self::where('status', '!=', 'deleted')->get();
            foreach ($is as $i) {
                $all += $i->total_tokens;
                $sold += $i->sales_token;
                $amount += self::check_stage($i->id)->amount;
            }
        }
        $data['all'] = $all;
        $data['sold'] = $sold;
        $data['percent'] = ceil((($sold) * 100) / $all);
        $data['amount'] = $amount;
        return (object) $data;
    }
    /**
     *
     * Check the stage transaction
     *
     * @version 1.0.0
     * @since 1.0
     * @return void
     */
    public static function check_stage($stageId)
    {
        $trnxs = Transaction::where(['stage' => $stageId, 'status' => 'approved'])->get();
        $tokens_sale = $users = $amount = 0;
        if ($trnxs) {
            $tokens_sale = $trnxs->sum('total_tokens');
            $amount = $trnxs->sum('base_amount');
            $users = $trnxs->count('user');
        }

        $res['tokens'] = $tokens_sale;
        $res['amount'] = $amount;
        $res['users'] = $users;
        $res['trnxs'] = $trnxs;

        return (object) $res;
    }
    /**
     *
     * Check the stage transaction
     *
     * @version 1.0.0
     * @since 1.0
     * @return void
     */
    public static function token_add_to_account($trnx, $stage_action = '', $user_action = '')
    {
        $stage = IcoStage::where('id', $trnx->stage)->first();
        $user = User::where('id', $trnx->user)->first();
        if (!$stage) {
            return false;
        }

        if (!$user) {
            return false;
        }

        if ($stage_action == 'add' || $stage_action == 'sub') {
            if ($stage_action == 'add') {
                $stage->sales_token = number_format(((double) $stage->sales_token + (double) $trnx->total_tokens), min_decimal(), '.', '');
                $stage->sales_amount = number_format(((double) $stage->sales_amount + (double) $trnx->base_amount), max_decimal(), '.', '');
            } else {
                $stage->sales_token = number_format(((double) $stage->sales_token - (double) $trnx->total_tokens), min_decimal(), '.', '');
                $stage->sales_amount = number_format(((double) $stage->sales_amount - (double) $trnx->base_amount), max_decimal(), '.', '');
            }
            $stage->save();
            return true;
        }

        if ($user_action == 'add' || $user_action == 'sub') {
            if ($user_action == 'add') {
                $user->tokenBalance = number_format(((double) $user->tokenBalance) + (double) $trnx->total_tokens, min_decimal(), '.', '');
                $user->contributed = number_format(($user->contributed + (double) ($trnx->base_amount)), max_decimal(), '.', '');
            } else {
                $user->tokenBalance = number_format(((double) $user->tokenBalance) - (double) $trnx->total_tokens, min_decimal(), '.', '');
                $user->contributed = number_format(($user->contributed - (double) ($trnx->base_amount)), max_decimal(), '.', '');
            }
            $user->save();
            return true;
        }
        return false;
    }

    /**
     *
     * Check the stage transaction
     *
     * @version 1.0.0
     * @since 1.0
     * @return void
     */
    public static function token_adjust_to_stage($trnx, $token, $amount, $stage_action = '')
    {
        $stage = IcoStage::where('id', $trnx->stage)->first();
        if (!$stage) {
            return false;
        }

        if ($stage_action == 'add' || $stage_action == 'sub') {
            if ($stage_action == 'add') {
                $stage->sales_token = number_format(((double) $stage->sales_token + (double) $token), min_decimal(), '.', '');
                $stage->sales_amount = number_format(((double) $stage->sales_amount + (double) $amount), max_decimal(), '.', '');
            } else {
                $stage->sales_token = number_format(((double) $stage->sales_token - (double) $token), min_decimal(), '.', '');
                $stage->sales_amount = number_format(((double) $stage->sales_amount - (double) $amount), max_decimal(), '.', '');
            }
            $stage->save();
            return true;
        }
        return false;
    }

}
