<?php
/**
 * User Model
 *
 * Store the users meta data
 *
 * @package TokenLite
 * @author Softnio
 * @version 1.0
 */
namespace App\Models;

use Auth;
use Illuminate\Database\Eloquent\Model;

class Referral extends Model
{

    
    /*
     * Table Name Specified
     */
    protected $table = 'referrals';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'invete',
        'inveted',
        'level',      
    ];


}
