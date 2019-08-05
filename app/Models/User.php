<?php /** @noinspection ALL */

/**
 * User Model
 *
 * The User Model
 *
 * @package TokenLite
 * @author Softnio
 * @version 1.0
 */
namespace App\Models;

use App\Models\KYC;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * @property mixed walletAddress
 */
class User extends Authenticatable // implements MustVerifyEmail
{
    use Notifiable;

    /*
     * Table Name Specified
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'lastLogin', 'role','referral',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     *
     * Relation with kyc
     *
     * @version 1.0.0
     * @since 1.0
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function kyc_info()
    {
        return $this->belongsTo('App\Models\KYC', 'id', 'userId')->orderBy('created_at', 'DESC');
    }
    /**
     *
     * Relation with meta
     *
     * @version 1.0.0
     * @since 1.0
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function meta()
    {
        return $this->belongsTo('App\Models\UserMeta', 'id', 'userId');
    }

     /**
     *
     * Relation with Activity logs
     *
     * @version 1.0.0
     * @since 1.0
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function logs()
    {
        return $this->belongsTo('App\Models\Activity', 'id', 'user_id');
    }

    /**
     *
     * Relation with transaction
     *
     * @version 1.0.0
     * @since 1.0
     * @return void
     */
    public function first_tnx()
    {
        $user = $this;
        $tnx = Transaction::where('user', $user->id)->first();
        return $tnx;
    }
    /**
     *
     * Relation with referral
     *
     * @version 1.0.0
     * @since 1.0
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function referee()
    {
        return $this->belongsTo(self::class, 'referral', 'id');
    }

    /**
     *
     * Check if request to change wallet address and it's status
     *
     * @version 1.0.0
     * @since 1.0
     * @return string
     */
    public function wallet($output='status')
    {
        $wrc = GlobalMeta::where(['pid' => $this->id, 'name' => 'user_wallet_address_change_request'])->first();
        $return = false;
        if ($wrc && ($this->walletAddress != $wrc->data()->address)) {
            $return = 'pending';
        }
        $return = ($output=='current') ? $this->walletAddress : $return;
        $return = ($output=='new') ? $wrc->data()->address : $return;
        return $return;
    }

    /**
     *
     * Data of dashboard
     *
     * @version 1.0.0
     * @since 1.0
     * @param int $get
     * @return object
     */
    public static function dashboard($get = 15)
    {
        $from = [
            'y' => date('Y'),
            'm' => date('m'),
            'd' => date('d') - 7,
        ];

        $from = $from['y'] . '-' . $from['m'] . '-' . $from['d'] . ' 00:00:00';
        $to = date('Y-m-d H:i:s');
        $kyc = new KYC;

        $data['all'] = self::count();
        $data['last_week'] = self::whereBetween('created_at', [$from, $to])->count();
        $data['kyc_last_week'] = $kyc->whereBetween('created_at', [$from, $to])->count();
        $data['unverified'] = ceil(((self::where('email_verified_at', null)->count()) * 100) / self::count());
        $data['kyc_submit'] = $kyc->count();
        $data['kyc_approved'] = $kyc->where('status', 'approved')->count();
        $data['kyc_pending'] = $kyc->count() > 0 ? ceil((($kyc->where('status', 'pending')->count()) * 100) / $kyc->count()) : 0;
        $data['kyc_missing'] = $kyc->count() > 0 ? ceil((($kyc->where('status', 'missing')->count()) * 100) / $kyc->count()) : 0;

        $data['chart'] = self::chart($get);

        return (object) $data;
    }
    /**
     *
     * Chart data
     *
     * @version 1.0.0
     * @since 1.0
     * @return object
     */
    public static function chart($get = 15)
    {
        $cd = Carbon::now(); //->toDateTimeString();
        $lw = $cd->copy()->subDays($get);

        $cd = $cd->copy()->addDays(1);
        $df = $cd->diffInDays($lw);

        $data['days'] = null;
        $data['data'] = null;
        $data['data_alt'] = null;
        $data['days_alt'] = null;
        $usr = 0;
        for ($i = 1; $i <= $df; $i++) {
            $usr = self::whereDate('created_at', $lw->format('Y-m-d'))->count();
            $data['data'] .= $usr . ",";
            $data['days'] .= '"' . $lw->format('D') . '",';
            $data['data_alt'][$i] = $usr;
            $data['days_alt'][$i] = ($get > 90 ? $lw->format('d M Y') : $lw->format('d M'));
            $lw->addDay();
        }
        return (object) $data;
    }
}
