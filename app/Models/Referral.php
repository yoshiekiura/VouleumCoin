<?php
/**
 * Referral Model
 *
 * 
 *
 * @package Chkernit
 * @author Chkernit
 * @version 1.1
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
        'invete',
        'inveted',
        'level',      
    ];


}
