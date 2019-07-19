<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * KYC Model
 *
 *  Manage the User Submitted KYC
 *
 * @package TokenLite
 * @author Softnio
 * @version 1.0
 * @method static orderBy(string $string, string $string1)
 * @method static FindOrFail($id)
 */
class KYC extends Model
{
    /*
     * Table Name
     */
    protected $table = 'kycs';
    /*
     * Status Data
     */
    const KYC_STATUS = ['pending', 'approved', 'missing', 'rejected'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'userId', 'firstName', 'email',
    ];

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     *
     * Relation with user
     *
     * @version 1.0.0
     * @since 1.0
     * @return void
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'userId', 'id');
    }
    /**
     *
     * Relation with approver
     *
     * @version 1.0.0
     * @since 1.0
     * @return void
     */
    public function checker_info()
    {
        return $this->belongsTo('App\Models\User', 'reviewedBy', 'id');
    }

    /**
     * KYC Submission Rules
     *
     * @return Rules
     *
     * @version 1.0.0
     * @since 1.0
     * @return void
     */
    public static function rules()
    {
        $wallet = field_value_text('kyc_wallet_opt', 'wallet_opt');
        is_array($wallet) ? true : $wallet = array();
        $custom = field_value_text('kyc_wallet_custom');
        if ($custom['cw_name'] == null || $custom['cw_text'] == null) {
            unset($custom);
            $custom = array();
        }

        is_array($custom) ? true : $custom = array();

        $wallet_count = count($wallet);
        $custom_count = count($custom);

        return [
            'first_name' => (field_value('kyc_firstname', 'show') && field_value('kyc_firstname', 'req')) ? 'required|string|min:2' : 'nullable',
            'last_name' => (field_value('kyc_lastname', 'show') && field_value('kyc_lastname', 'req')) ? 'required|string|min:2' : 'nullable',
            'phone' => (field_value('kyc_phone', 'show') && field_value('kyc_phone', 'req')) ? 'required|string|min:5' : 'nullable',
            'dob' => (field_value('kyc_dob', 'show') && field_value('kyc_dob', 'req')) ? 'required|date|date_format:"m/d/Y"' : 'nullable',
            'gender' => (field_value('kyc_gender', 'show') && field_value('kyc_gender', 'req')) ? 'required|string|min:2' : 'nullable',
            'telegram' => (field_value('kyc_telegram', 'show') && field_value('kyc_telegram', 'req')) ? 'required|string|min:2' : 'nullable',

            'email' => auth()->guest() ? ((field_value('kyc_email', 'show') && field_value('kyc_email', 'req')) ? 'required|string|email|max:255|unique:users' : 'nullable') : 'nullable',
            'password' => auth()->guest() ? 'required|string|min:6' : 'nullable',

            'country' => (field_value('kyc_country', 'show') && field_value('kyc_country', 'req')) ? 'required|string|min:4' : 'nullable',
            'state' => (field_value('kyc_state', 'show') && field_value('kyc_state', 'req')) ? 'required|string|min:2' : 'nullable',
            'city' => (field_value('kyc_city', 'show') && field_value('kyc_city', 'req')) ? 'required|string|min:2' : 'nullable',
            'zip' => (field_value('kyc_zip', 'show') && field_value('kyc_zip', 'req')) ? 'required|min:3' : '',
            'address_1' => (field_value('kyc_address1', 'show') && field_value('kyc_address1', 'req')) ? 'required|string|min:4' : 'nullable',
            'address_2' => (field_value('kyc_address2', 'show') && field_value('kyc_address2', 'req')) ? 'required|string|min:4' : 'nullable',

            'wallet_address' => (field_value('kyc_wallet', 'show') && field_value('kyc_wallet', 'req') && ($wallet_count >= 1 || $custom_count == 2)) ? 'required|string|min:10' : 'nullable',

            'documentType' => 'required',
            'passport_image' => get_setting('kyc_document_passport') ? 'required_without_all:id_front,nid_hand,id_back,license,license_hand' : 'nullable',
            'passport_image_hand' => get_setting('kyc_document_passport') ? 'required_without_all:id_front,nid_hand,id_back,license,license_hand' : 'nullable',
            'license' => get_setting('kyc_document_driving') ? 'required_without_all:id_front,id_back,nid_hand,passport_image, passport_image_hand' : 'nullable',
            'license_hand' => get_setting('kyc_document_driving') ? 'required_without_all:id_front,id_back,nid_hand,passport_image, passport_image_hand' : 'nullable',
            'id_front' => get_setting('kyc_document_nidcard') ? 'required_without_all:passport_image,passport_image_hand,license,license_hand' : 'nullable',
            'id_back' => get_setting('kyc_document_nidcard') ? 'required_without_all:passport_image,passport_image_hand,license,license_hand' : 'nullable',
            'nid_hand' => get_setting('kyc_document_nidcard') ? 'required_without_all:passport_image,passport_image_hand,license,license_hand' : 'nullable',
        ];
    }

    /**
     * Get KYC Fields
     *
     * @return Boolean    /**
     *
     * Relation with category
     *
     * @version 1.0.0
     * @since 1.0
     * @return void
     */
    public static function kyc_fields($name = '')
    {
        $fields = [
            'kyc_public' => 1,
            'kyc_before_email' => 0,
            'kyc_firstname' => array('show' => 1, 'req' => 1),
            'kyc_lastname' => array('show' => 1, 'req' => 1),
            'kyc_email' => array('show' => 1, 'req' => 1),
            'kyc_phone' => array('show' => 1, 'req' => 0),
            'kyc_dob' => array('show' => 1, 'req' => 0),
            'kyc_gender' => array('show' => 1, 'req' => 1),
            'kyc_country' => array('show' => 1, 'req' => 1),
            'kyc_state' => array('show' => 1, 'req' => 1),
            'kyc_city' => array('show' => 1, 'req' => 1),
            'kyc_zip' => array('show' => 1, 'req' => 1),
            'kyc_address1' => array('show' => 1, 'req' => 1),
            'kyc_address2' => array('show' => 1, 'req' => 0),
            'kyc_telegram' => array('show' => 1, 'req' => 0),
            'kyc_document_passport' => 1,
            'kyc_document_nidcard' => 1,
            'kyc_document_driving' => 1,
            'kyc_wallet' => array('show' => 1, 'req' => 1),
            'kyc_wallet_custom' => array('cw_name' => null, 'cw_text' => null),
            'kyc_wallet_note' => __('Address should be ERC20-compliant.'),
            'kyc_wallet_opt' => array('wallet_opt' => ['ethereurm', 'bitcoin', 'litecoin', 'dash', 'waves']),

        ];
        if ($name == '') {
            return $fields;
        } else {
            return in_array($name, $fields);
        }
    }

}
