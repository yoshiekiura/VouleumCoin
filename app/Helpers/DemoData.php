<?php
namespace App\Helpers;

use App\Models\EmailTemplate;
use App\Models\IcoMeta;
use App\Models\IcoStage;
use App\Models\KYC;
use App\Models\Page;
use App\Models\PaymentMethod;
use App\Models\User;
use Illuminate\Support\Facades\Schema;

class DemoData extends IcoHandler
{

    /**
     * Create the class instance
     */
    public function __construct()
    {
        $this->init();
    }

    /**
     * Initialize The DemoData
     */
    public function init()
    {  
        $check_dt = \IcoHandler::checkDB();
        if(empty($check_dt)){       
       
            if (file_exists(storage_path('installed')) && !request()->is('install/*') && Schema::hasTable('settings')) {
                $this->add_settings();
                $this->add_kyc_settings();
                $this->add_purchase_settings();
                $this->add_payment_method();
                $this->add_stage_data();
                $this->add_pages();
                $this->add_email_templates();
                // Check is an Super Admin is available or not?
                if (save_gmeta('super_admin')->value == null) {
                    $id = save_gmeta('site_super_admin')->value;
                    $user = User::where(['id' => $id, 'status' => 'active', 'role' => 'admin'])->first();
                    if (!$user) {
                        save_gmeta('site_super_admin', 1, 1);
                    }
                }
            }
        }
    }

    /**
     * Add Default Settings
     * Which is necessary
     */
    private function add_settings()
    {
        // App Info
        if (get_setting('site_name', null) == null) {
            add_setting('site_name', config('app.name', 'TokenLite'));
        }
        if (get_setting('site_email', null) == null) {
            add_setting('site_email', 'info@yourdomain.com');
        }
        if (get_setting('site_base_currency', null) == null) {
            add_setting('site_base_currency', 'USD');
        }
        if (get_setting('site_copyright', null) == null) {
            add_setting('site_copyright', 'All Right Reserved.');
        }
        if (get_setting('site_support_address', null) == null) {
            add_setting('site_support_address', '');
        }
        if (get_setting('site_support_phone', null) == null) {
            add_setting('site_support_phone', '');
        }
        if (get_setting('site_support_email', null) == null) {
            add_setting('site_support_email', '');
        }
        if (get_setting('token_default_in_userpanel', null) == null) {
            add_setting('token_default_in_userpanel', 'ETH');
        }
        if (get_setting('main_website_url', null) == null) {
            add_setting('main_website_url',null);
        }
        if (get_setting('pm_automatic_rate_time', null) == null) {
            add_setting('pm_automatic_rate_time', 30);
        }
        if (get_setting('user_dashboard_color', null) == null) {
            add_setting('user_dashboard_color', "style");
        }
        if (get_setting('site_date_format', null) == null) {
            add_setting('site_date_format', "d M, Y");
        }
        if (get_setting('site_time_format', null) == null) {
            add_setting('site_time_format', "h:i A");
        }
        if (get_setting('site_timezone', null) == null) {
            add_setting('site_timezone', "UTC");
        }

        // Email Credentials
        if (get_setting('site_mail_driver', null) == null) {
            add_setting('site_mail_driver', 'mail');
        }
        if (get_setting('site_mail_host', null) == null) {
            add_setting('site_mail_host', '');
        }
        if (get_setting('site_mail_port', null) == null) {
            add_setting('site_mail_port', '587');
        }
        if (get_setting('site_mail_encryption', null) == null) {
            add_setting('site_mail_encryption', 'tls');
        }
        if (get_setting('site_mail_from_address', null) == null) {
            add_setting('site_mail_from_address', 'noreply@yourdomain.com');
        }
        if (get_setting('site_mail_from_name', null) == null) {
            add_setting('site_mail_from_name', 'TokenLite');
        }
        if (get_setting('site_mail_username', null) == null) {
            add_setting('site_mail_username', '');
        }
        if (get_setting('site_mail_password', null) == null) {
            add_setting('site_mail_password', '');
        }
        if (get_setting('site_mail_footer', null) == null) {
            add_setting('site_mail_footer', "Best Regards\n[[site_name]]");
        }
    }
    /**
     * Add KYC Settings
     * Which is necessary
     */
    private function add_kyc_settings()
    {
        $kycs = KYC::kyc_fields();
        foreach ($kycs as $field => $value) {
            $val = is_array($value) ? json_encode($value) : $value;
            if (get_setting($field, null) == null) {
                add_setting($field, $val);
            }
        }
    }
    /**
     * Add Payment Methods
     * For run the application smoothly
     */
    private function add_payment_method()
    {
        $manual = [
            'eth' => [
                'status' => 'inactive',
                'address' => null,
                'limit' => null,
                'price' => null,
            ],
            'btc' => [
                'status' => 'inactive',
                'address' => null,
            ],
            'ltc' => [
                'status' => 'inactive',
                'address' => null,
            ],
            'bank' => [
                'status' => 'inactive',
                'bank_account_name' => null,
                'bank_account_number' => null,
                'bank_name' => null,
                'routing_number' => null,
                'iban' => null,
                'swift_bic' => null,
            ],
        ];

        if (PaymentMethod::check('manual')) {
            $man = new PaymentMethod();
            $man->payment_method = 'manual';
            $man->title = 'Manual Payment';
            $man->description = 'You can send payment direct to our wallets. We will manually verify.';
            $man->data = json_encode($manual);
            $man->status = 'inactive';
            $man->save();
        }
    }

    /**
     * Add Demo Stage + Add Stage Basic Settings Data
     * For run the application smoothly
     */
    private function add_stage_data()
    {
        $i = 1;
        $chk = new IcoStage();
        $_count = $chk->count();
        if ($_count < 3) {
            $chk->name = 'Demo Stage ' . ($_count + 1) . '';
            $chk->start_date = ($_count == 0) ? now() : now()->addMonth($i);
            $chk->end_date = ($_count == 0) ? now()->addMonth() : now()->addMonth($i+1);
            $chk->total_tokens = '850000';
            $chk->base_price = '0.2';
            $chk->min_purchase = '100';
            $chk->max_purchase = '10000';
            $chk->display_mode = 'normal';
            $chk->save();
            IcoMeta::create([
                'stage_id' => $chk->id,
                'option_name' => 'bonus_option',
                'option_value' => self::default_ico_meta('bonus_option', 'json'),
            ]);
            IcoMeta::create([
                'stage_id' => $chk->id,
                'option_name' => 'price_option',
                'option_value' => self::default_ico_meta('price_option', 'json'),
            ]);
            if (get_setting('actived_stage', null) == null) {
                add_setting('actived_stage', $chk->id);
            }

            $i++;
            $this->add_stage_data();
        }

        if (get_setting('token_name', null) == null) {
            add_setting('token_name', 'TokenLite');
        }
        if (get_setting('token_symbol', null) == null) {
            add_setting('token_symbol', 'TLE');
        }
        if (get_setting('token_decimal_min', null) == null) {
            add_setting('token_decimal_min', '2');
        }
        if (get_setting('token_decimal_min', null) == null) {
            add_setting('token_decimal_min', '2');
        }
        if (get_setting('token_decimal_max', null) == null) {
            add_setting('token_decimal_max', '6');
        }
        if (get_setting('token_price_show', null) == null) {
            add_setting('token_price_show', 1);
        }
        if (get_setting('token_before_kyc', null) == null) {
            add_setting('token_before_kyc', 0);
        }
    }

    public static function default_ico_meta($which, $type = 'object')
    {
        $end = now()->addDays(25)->format('Y-m-d H:i:s');

        $prices = [
            'tire_1' => [
                'price' => 0,
                'min_purchase' => 0,
                'start_date' => def_datetime('datetime'),
                'end_date' => def_datetime('datetime_e'),
                'status' => 0,
            ],
            'tire_2' => [
                'price' => 0,
                'min_purchase' => 0,
                'start_date' => def_datetime('datetime'),
                'end_date' => def_datetime('datetime_e'),
                'status' => 0,
            ],
            'tire_3' => [
                'price' => 0,
                'min_purchase' => 0,
                'start_date' => def_datetime('datetime'),
                'end_date' => def_datetime('datetime_e'),
                'status' => 0,
            ],

        ];

        $bonuses = [
            'base' => [
                'amount' => 25,
                'start_date' => def_datetime('datetime'),
                'end_date' => def_datetime('datetime_e'),
                'status' => 1,
            ],
            'bonus_amount' => [
                'status' => 1,
                'tire_1' => [
                    'amount' => 15,
                    'token' => 2500,
                ],
                'tire_2' => [
                    'amount' => null,
                    'token' => null,
                ],
                'tire_3' => [
                    'amount' => null,
                    'token' => null,
                ],
            ],
        ];

        if ($which == 'price_option') {
            $result = json_encode($prices);
        }
        if ($which == 'bonus_option') {
            $result = json_encode($bonuses);
        }
        if ($type == 'json') {
            return $result;
        }
        return json_decode($result);
    }
    private function add_purchase_settings()
    {
        foreach (PaymentMethod::Currency as $key => $value) {
            if (get_setting('token_purchase_' . $key, null) == null) {
                add_setting('token_purchase_' . $key, 1);
            }
            if (get_setting('pmc_active_' . $key, null) == null) {
                add_setting('pmc_active_' . $key, 1);
            }
        }
        if (get_setting('pm_exchange_method', null) == null) {
            add_setting('pm_exchange_method', 'automatic');
        }
        if (get_setting('pm_exchange_auto_lastcheck', null) == null) {
            add_setting('pm_exchange_auto_lastcheck', now()->subMinutes(10));
        }

        if (get_setting('token_calculate', null) == null) {
            add_setting('token_calculate', "normal");
        }

        if (get_setting('token_calculate_note', null) == null) {
            add_setting('token_calculate_note', "normal");
        }
        if (get_setting('token_default_method', null) == null) {
            add_setting('token_default_method', "ETH");
        }
    }

    /**
     * Save the default page data
     * Necessary
     */
    private function add_pages()
    {
        $data = self::default_pages();

        foreach ($data as $key => $value) {
            $check = Page::where('slug', $key)->count();
            if ($check <= 0) {
                Page::create($value);
            }
        }
    }
    public static function default_pages($get = null)
    {
        $data = [
            'home_top' => [
                'title' => 'Thank you for your interest to our [[site_name]]',
                'slug' => 'home_top',
                'custom_slug' => 'home-top-block',
                'menu_title' => 'Welcome block',
                'description' => "You can contribute [[token_symbol]] token go through Buy Token page. \n\n You can get a quick response to any questions, and chat with the project in our Telegram: https://t.me/icocrypto \n\n <strong>Don’t hesitate to invite your friends!</strong> \n\n [[whitepaper_download_button]]",
                'status' => 'active',
                'public' => 0,
            ],
            'how_buy' => [
                'title' => 'How to buy?',
                'slug' => 'how_buy',
                'custom_slug' => 'how-to-buy',
                'menu_title' => 'How to buy?',
                'description' => 'Login with your email and password then go to Buy Token!',
                'status' => 'active',
                'public' => 0,
            ],
            'faq' => [
                'title' => 'FAQ ',
                'slug' => 'faq',
                'custom_slug' => 'faq',
                'menu_title' => 'FAQ ',
                'description' => 'Frequently Ask Questions...',
                'status' => 'active',
                'public' => 0,
            ],
            'privacy' => [
                'title' => 'Privacy and Policy',
                'slug' => 'privacy',
                'custom_slug' => 'privacy-policy',
                'menu_title' => 'Privacy and Policy',
                'description' => '[[site_name]] Privacy and Policies...',
                'status' => 'active',
                'public' => 1,
            ],
            'terms' => [
                'title' => 'Terms and Condition',
                'slug' => 'terms',
                'custom_slug' => 'terms-and-condition',
                'menu_title' => 'Terms and Condition',
                'description' => '[[site_name]] Terms and Condition...',
                'status' => 'active',
                'public' => 1,
            ],
            'distribution' => [
                'title' => 'ICO Distribution',
                'slug' => 'distribution',
                'custom_slug' => 'ico-distribution',
                'menu_title' => 'ICO Distribution',
                'description' => 'Distribution page content',
                'status' => 'hide',
                'public' => 0,
            ],
            'custom_page' => [
                'title' => 'Custom Page',
                'slug' => 'custom_page',
                'custom_slug' => 'custom-page',
                'menu_title' => 'Custom Page',
                'description' => 'Details about the page!',
                'status' => 'hide',
                'public' => 0,
            ],
        ];
        if ($get != null) {
            return (object) $data[$get];
        }
        return $data;
    }

    /**
     * Save the default email template
     * Necessary
     */
    private function add_email_templates()
    {
        $data = self::default_email_template();

        foreach ($data as $key => $value) {
            $check = EmailTemplate::where('slug', $key)->count();
            if ($check <= 0) {
                EmailTemplate::create($value);
            }
        }
    }
    public static function default_email_template($get = null)
    {
        $data = [
            'welcome-email' => [
                'name' => 'Welcome Email',
                'slug' => 'welcome-email',
                'subject' => 'Welcome to [[site_name]]',
                'greeting' => 'Hi [[user_name]],',
                'message' => "Thanks for joining our platform! \n\nAs a member of our platform, you can mange your account, purchase token, referrals etc. \n\nFind out more about in - [[site_url]]",
                'regards' => "true",
            ],
            'send-user-email' => [
                'name' => 'Send Email to User',
                'slug' => 'send-user-email',
                'subject' => 'New Message - [[site_name]]',
                'greeting' => 'Hi [[user_name]], ',
                'message' => "[[messages]]",
                'regards' => "true",
            ],
            'users-change-password-email' => [
                'name' => 'Password Change Email',
                'slug' => 'users-change-password-email',
                'subject' => 'Password change request on [[site_name]]',
                'greeting' => 'Hello [[user_name]],',
                'message' => "You are receiving this email because we received a password change request for your account.",
                'regards' => "true",
            ],
            'users-unusual-login-email' => [
                'name' => 'Unusual Login Email',
                'slug' => 'users-unusual-login-email    ',
                'subject' => 'Unusual Login Attempt on [[site_name]]!!!!',
                'greeting' => 'Hi [[user_name]], ',
                'message' => "Someone tried to log in too many times in your [[site_name]] account.",
                'regards' => "true",
            ],
            'users-confirm-password-email' => [
                'name' => 'Confirm Your Email',
                'slug' => 'users-confirm-password-email',
                'subject' => 'Please verify your email address - [[site_name]]',
                'greeting' => 'Welcome!',
                'message' => "Hello [[user_name]]! \n\nThank you for registering on our platform. You're almost ready to start.\n\nSimply click the button bellow to confirm your email address and active your account.",
                'regards' => "true",
            ],
            'users-reset-password-email' => [
                'name' => 'Password Reset Email by Admin',
                'slug' => 'users-reset-password-email',
                'subject' => 'Your Password is reseted on [[site_name]]',
                'greeting' => 'Hello [[user_name]],',
                'message' => 'We are reset your login password as per your requested via support.',
                'regards' => "true",
            ],
            'kyc-approved-email' => [
                'name' => 'KYC Approved Email',
                'slug' => 'kyc-approved-email',
                'subject' => 'KYC Verified: Contribute in [[site_name]] ICO',
                'greeting' => 'Hello [[user_name]],',
                'message' => "Thank you for submitting your verification request. \n\nWe are pleased to let you know that your identity (KYC) has been verified and you are granted to participate in our token sale.\n\nWe invite you to get back to contributor account and purchase token before sales end.",
                'regards' => "true",
            ],
            'kyc-rejected-email' => [
                'name' => 'KYC Rejected Email',
                'slug' => 'kyc-rejected-email',
                'subject' => 'KYC Application has been rejected - [[site_name]]',
                'greeting' => 'Hello [[user_name]],',
                'message' => "Thank you for submitting your verification request. We're having difficulties verifying your identity. \n\nThe information you had submitted was unfortunately rejected for following reason: \n[[message]]\n\nDon't be upset! Still you want to verity your identity, please get back to your account and fill form with proper information and upload correct documents to complete your identity verification process.",
                'regards' => "true",
            ],
            'kyc-missing-email' => [
                'name' => 'KYC Missing Email',
                'slug' => 'kyc-missing-email',
                'subject' => 'Identity Verification: Action Required - [[site_name]]',
                'greeting' => 'Hello [[user_name]],',
                'message' => "Thank you for submitting your verification request. We're having difficulties verifying your identity. \n\nThe information you had submitted was unfortunately rejected because of the following reason:\n[[message]]\n\nWe request to get back to your account in order to upload new documents and complete the identity verification.",
                'regards' => "true",
            ],
            'kyc-submit-email' => [
                'name' => 'KYC Submitted Email',
                'slug' => 'kyc-submit-email',
                'subject' => 'Document submitted for Identity Verification - [[site_name]]',
                'greeting' => 'Hello [[user_name]],',
                'message' => "Thank you for submitting your verification request. We've received your submitted document and other information for identity verification.\n\nWe'll review your information and if all is in order will approve your identity. If the information is incorrect or something missing, we will request this as soon as possible.",
                'regards' => "true",
            ],
            'order-submit-user' => [
                'name' => 'Token Purchase - Order Placed by Manual payment (USER)',
                'slug' => 'order-submit-user',
                'subject' => 'Order placed for Token Purchase #[[order_id]]',
                'greeting' => 'Thank you for your contribution! ',
                'message' => "You have requested to purchase [[token_symbol]] token. Your order has been received and is now being processed. You order details are show bellow for your reference. \n\n[[order_details]]\n\nIf you have not made the payment yet, please send your payments to the following address: [[payment_from]]\n\nYour order will be processed within 6 hours from the receipt of payment and token balance will appear in your account as soon as we have confirmed your payment. \n\nFeel free to contact us if you have any questions. ",
                'regards' => "true",
            ],
            'order-successful-user' => [
                'name' => 'Token Purchase - Order Successful (USER)',
                'slug' => 'order-successful-user',
                'subject' => 'Token Purchase Successful - Order #[[order_id]]',
                'greeting' => 'Congratulation [[user_name]], you order has been processed successfully. ',
                'message' => "Thank you for your contribution and purchase our [[token_symbol]] Token! \n\n[[order_details]]\n\nYour token balances now appear in your account. Please login into your and check your balance. Please note that, we will send smart contract end of the token sales. \n\nFeel free to contact us if you have any questions. \n ",
                'regards' => "true",
            ],
            'order-rejected-user' => [
                'name' => 'Token Purchase - Order Rejected by Admin (USER)',
                'slug' => 'order-rejected-user',
                'subject' => 'Canceled Order #[[order_id]]',
                'greeting' => 'Hello [[user_name]],',
                'message' => "The order (#[[order_id]]) has been canceled. \n\nWe noticed that you just tried to purchase [[token_symbol]] token, however we have not received your payment of [[payment_amount]] from your wallet ([[payment_from]]) for [[total_tokens]] Token.\n\nIf you still want to contribute please login into account and purchase the token again. \n[[site_login]]\n\nFeel free to contact us if you have any questions. \n ",
                'regards' => "true",
            ],
            'order-placed-admin' => [
                'name' => 'Token Purchase - Order Placed (ADMIN)',
                'slug' => 'order-placed-admin',
                'subject' => 'New Token Purchase Request #[[order_id]]',
                'greeting' => 'Hello Admin,',
                'message' => "You have received a token purchased request form [[user_name]].\n\n[[order_details]]\n\nOrder By: [[user_name]]\nEmail Address: [[user_email]]\n\nPlease login into account and check details of transaction and take necessary steps.\n\n\nPS. Do not reply to this email.  \nThank you.\n ",
                'regards' => "false",
            ],
            'order-canceled-admin' => [
                'name' => 'Token Purchase - Canceled by User (ADMIN)',
                'slug' => 'order-canceled-admin',
                'subject' => 'Order #[[order_id]] Canceled by Contributor',
                'greeting' => 'Hello Admin,',
                'message' => "The order (#[[order_id]]) has been canceled by [[user_name]] (contributor).\n\n\nPS. Do not reply to this email.  \nThank you.\n ",
                'regards' => "false",
            ],

        ];

        if ($get !== null) {
            return (object) $data[$get];
        }
        return $data;
    }
}
