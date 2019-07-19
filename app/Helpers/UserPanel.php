<?php
/**
 * CryptoCurrency Address Validation
 *
 * This class retrieve the address is valid or not.
 *
 * @package TokenLite
 * @author Softnio
 * @version 1.0
 */
namespace App\Helpers;

use DB;

/**
 * UserPanel Class
 */
class UserPanel
{

    /**
     * user_info()
     *
     * @version 1.0.0
     * @since 1.0
     * @return void
     */
    public static function user_info($data = null, $atttr = '')
    {
        $atttr_def = array('id' => '', 'class' => '', 'vers' => '');
        $opt_atttr = parse_args($atttr, $atttr_def);
        extract($opt_atttr);
        $g_id = ($id) ? ' id="' . $id . '"' : '';
        $g_cls = ($class) ? css_class($class) : '';

        $return = '<div' . $g_id . ' class="user-dropdown-head' . $g_cls . '">
        <h6 class="user-dropdown-name">' . $data->name . '<span>(' . set_id($data->id()) . ')</span></h6>
        <span class="user-dropdown-email">' . $data->email . '</span>
        </div>

        <div class="user-status">
        <h6 class="user-status-title">' . __('Token balance') . '</h6>
        <div class="user-status-balance">' . number_format($data->tokenBalance) . ' <small>' . token('symbol') . '</small></div>
        </div>';

        return $return;
    }

    /**
     * user_balance()
     *
     * @version 1.0.0
     * @since 1.0
     * @return void
     */
    public static function user_balance($data = null, $atttr = '')
    {
        $atttr_def = array('id' => '', 'class' => '', 'vers' => '');
        $opt_atttr = parse_args($atttr, $atttr_def);
        extract($opt_atttr);
        $g_id = ($id) ? ' id="' . $id . '"' : '';
        $g_cls = ($class) ? css_class($class) : '';

        $return = '<div' . $g_id . ' class="user-status' . $g_cls . '">
        <h6 class="user-status-title">' . __('Token balance') . '</h6>
        <div class="user-status-balance">' . number_format($data->tokenBalance) . ' <small>' . token('symbol') . '</small></div>
        </div>';

        return $return;
    }

    /**
     * user_balance_card()
     *
     * @version 1.0.0
     * @since 1.0
     * @return void
     */
    public static function user_balance_card($data = null, $atttr = '')
    {
        $user = auth()->user();

        $atttr_def = array('id' => '', 'class' => '', 'vers' => '');
        $opt_atttr = parse_args($atttr, $atttr_def);
        extract($opt_atttr);
        $g_id = ($id) ? ' id="' . $id . '"' : '';
        $g_cls = ($class) ? css_class($class) : '';

        $ver_cls = ($vers == 'side') ? ' token-balance-with-icon' : '';
        $ver_icon = ($vers == 'side') ? '<div class="token-balance-icon"><img src="' . asset('assets/images/token-symbol-light.png') . '" alt=""></div>' : '';

        $base_cur = base_currency();
        $base_con = $data->$base_cur ? $data->$base_cur : $user->contributed;
        $base_out = ($base_con && $base_cur) ? '<li class="token-balance-sub"><span class="lead">' . $base_con . '</span><span class="sub">' . strtoupper($base_cur) . '</span></li>' : '';

        $eth_con = (get_setting('pmc_active_eth') == 1 && $base_cur != 'eth') ? round($data->eth, 4) : 0;
        $eth_cur = 'ETH';
        $eth_out = 'eth' != $base_cur ? '<li class="token-balance-sub"><span class="lead">' . ($eth_con > 0 ? $eth_con : '~') . '</span><span class="sub">' . $eth_cur . '</span></li>' : '';

        $btc_con = (get_setting('pmc_active_btc') == 1 && $base_cur != 'btc') ? round($data->btc, 4) : 0;
        $btc_cur = 'BTC';
        $btc_out = 'btc' != $base_cur ? '<li class="token-balance-sub"><span class="lead">' . ($btc_con > 0 ? $btc_con : '~') . '</span><span class="sub">' . $btc_cur . '</span></li>' : '';

        $usd_con = (get_setting('pmc_active_usd') == 1 && $base_cur != 'usd') ? round($data->usd, 4) : 0;
        $usd_cur = 'USD';
        $usd_out = 'usd' != $base_cur ? '<li class="token-balance-sub"><span class="lead">' . ($usd_con > 0 ? $usd_con : '~') . '</span><span class="sub">' . $usd_cur . '</span></li>' : '';

        $contribute = ($base_out || $eth_out || $btc_out || $usd_out) ? '<div class="token-balance token-balance-s2"><h6 class="card-sub-title">' . __('Your Contribution in') . '</h6><ul class="token-balance-list">' . $base_out . $usd_out . $eth_out . $btc_out . '</ul></div>' : '';

        $return = '<div' . $g_id . ' class="token-statistics card card-token' . $g_cls . '">
        <div class="card-innr"><div class="token-balance' . $ver_cls . '">' . $ver_icon . '
        <div class="token-balance-text"><h6 class="card-sub-title">' . __('Token Balance') . '</h6>
        <span class="lead">' . number_format($user->tokenBalance) . ' <span>' . token('symbol') . '</span></span>
        </div>
        </div>' . $contribute . '</div></div>';

        return $return;
    }

    /**
     * user_token_block()
     *
     * @version 1.0.0
     * @since 1.0
     * @return void
     */
    public static function user_token_block($data = '', $atttr = '')
    {
        $atttr_def = array('id' => '', 'class' => '', 'vers' => '');
        $opt_atttr = parse_args($atttr, $atttr_def);
        extract($opt_atttr);
        $g_id = ($id) ? ' id="' . $id . '"' : '';
        $g_cls = ($class) ? css_class($class) : '';
        $base_currency = base_currency();

        $card1 = '<div class="token-info text-center">
        <img class="token-info-icon" src="' . asset('assets/images/token-symbol.png') . '" alt="">
        <div class="gaps-2x"></div>
        <h3 class="token-info-head text-light">1 ' . token_symbol() . ' = ' . _format(['number' => token_calc(1, 'price')->$base_currency]) . ' ' . base_currency(true) . '
        </h3>
        <h5 class="token-info-sub">1 ' . base_currency(true) . ' = ' . token_rate(1, token('default_in_userpanel', 'eth')) . ' ' . token('default_in_userpanel', 'ETH') . '</h5>
        </div>';
        $card2 = '<div class="token-info bdr-tl">
        <div>
        <ul class="token-info-list">
        <li><span>' . __('Token Name') . ':</span>' . token('name') . '</li>
        <li><span>' . __('Token Symbol') . ':</span>' . token_symbol() . '</li>
        </ul>';
        $card2 .= (get_setting('site_white_paper') != '' ? '<a href="' . route('public.white.paper') . '" target="_blank" class="btn btn-primary"><em class="fas fa-download mr-3"></em>' . __('Download Whitepaper') . '</a>' : '');
        $card2 .= '</div>
        </div>';

        $return = ''; 
        $status = ucfirst(active_stage_status());
        if ($vers =='buy') {
            $return .= '<div class="card card-full-height"><div class="card-innr">';
            $return .= '<h6 class="card-title card-title-sm">'. active_stage()->name .'<span class="badge badge-success ucap">' . __($status) . '</span></h6>';
            $return .= '<h3 class="text-dark">1 ' . token_symbol() . ' = ' . _format(['number' => token_calc(1, 'price')->$base_currency]) . ' ' . base_currency(true) .' <span class="d-block text-exlight ucap fs-12">1 '. base_currency(true) . ' = ' . token_rate(1, token('default_in_userpanel', 'eth')) . ' ' . token('default_in_userpanel', 'ETH') .'</span></h3>';
            $return .= '<div class="gaps-0-5x"></div><div class="d-flex align-items-center justify-content-between mb-0"><a href="'.route('user.token').'" class="btn btn-md btn-primary">'.__('Buy Token Now').'</a></div>';
                
            $return .= '</div></div>';
        } else {
            $return .= '<div' . $g_id . ' class="token-information card card-full-height' . $g_cls . '">';
            if ($vers == 'prices') {
                $return .= $card1;
            } elseif ($vers == 'info') {
                $return .= $card2;
            } else {
                $return .= '<div class="row no-gutters height-100">
                <div class="col-md-6">' . $card1 . '</div>
                <div class="col-md-6">' . $card2 . '</div>
                </div>';
            }
            $return .= '</div>';
        }

        return $return;
    }

    /**
     * add_wallet_alert()
     *
     * @version 1.0.0
     * @since 1.0
     * @return void
     */
    public static function add_wallet_alert()
    {
        return '<a href="javascript:void(0)" class="btn btn-danger btn-xl btn-between w-100 mgb-1-5x user-wallet">' . __('Add your wallet address before buy') . ' <em class="ti ti-arrow-right"></em></a>
        <div class="gaps-1x mgb-0-5x d-lg-none d-none d-sm-block"></div>';
    }

    /**
     * user_account_status()
     *
     * @version 1.0.0
     * @since 1.0
     * @return void
     */
    public static function user_account_status($data = null, $atttr = '')
    {
        $atttr_def = array('id' => '', 'class' => '', 'vers' => '');
        $opt_atttr = parse_args($atttr, $atttr_def);
        extract($opt_atttr);
        $g_id = ($id) ? ' id="' . $id . '"' : '';
        $g_cls = ($class) ? css_class($class) : '';

        $user = auth()->user();
        $heading = '<h6 class="card-title card-title-sm">' . __('Your Account Status') . '</h6><div class="gaps-1-5x"></div>';
        $email_status = $kyc_staus = '';
        if ($user->email_verified_at == null) {
            $email_status = '<li><a href="' . route('verify.resend') . '" class="btn btn-xs btn-auto btn-info">' . __('Resend Email') . '</a></li>';
        } else {
            $email_status = '<li><a href="javascript:void(0)" class="btn btn-xs btn-auto btn-success">' . __('Email Verified') . '</a></li>';
        }

        if (isset($user->kyc_info->status) && $user->kyc_info->status == 'approved') {
            $kyc_staus = '<li><a href="javascript:void(0)" class="btn btn-xs btn-auto btn-success">' . __('KYC Approved') . '</a></li>';
        } elseif (isset($user->kyc_info->status) && $user->kyc_info->status == 'pending') {
            $kyc_staus = '<li><a href="' . route('user.kyc') . '" class="btn btn-xs btn-auto btn-warning">' . __('KYC Pending') . '</a></li>';
        } else {
            $kyc_staus = '<li><a href="' . route('user.kyc') . '" class="btn btn-xs btn-auto btn-info"><span>' . __('Submit KYC') . '</span></a></li>';
        }
        $return = ($email_status || $kyc_staus) ? '<div' . $g_id . ' class="user-account-status' . $g_cls . '">' . $heading . '<ul class="btn-grp">' . $email_status . $kyc_staus . '</ul></div>' : '';
        return $return;
    }

    /**
     * user_account_wallet()
     *
     * @version 1.0.0
     * @since 1.0
     * @return void
     */
    public static function user_account_wallet($data = null, $atttr = '')
    {
        $atttr_def = array('id' => '', 'class' => '', 'vers' => '');
        $opt_atttr = parse_args($atttr, $atttr_def);
        extract($opt_atttr);
        $g_id = ($id) ? ' id="' . $id . '"' : '';
        $g_cls = ($class) ? css_class($class) : '';

        $user = auth()->user();
        $title_cls = ' card-title-sm';
        $btn_cls = ' link link-ucap';

        $uwallet = '<h6 class="card-title' . $title_cls . '">' . __('Receiving Wallet') . '</h6><div class="gaps-1x"></div>';
        $uwallet .= '<div class="d-flex justify-content-between">';
        if ($user->walletAddress) {
            $uwallet .= '<span>' . show_str($user->walletAddress, 8) . ' ';
            if ($user->wallet()=='pending') {
                $uwallet .= ' <em title="' . __('New address under review for approve.') . '" data-toggle="tooltip" class="fas fa-info-circle text-warning"></em></span>';
            }
        } else {
            $uwallet .= __('Add Your Wallet Address');
        }
        $uwallet .= '<a href="javascript:void(0)" data-toggle="modal" data-target="#edit-wallet" class="user-wallet' . $btn_cls . '">' . ($user->walletAddress != null ? __('Edit') : __('Add')) . '</a></div>';

        $return = ($uwallet) ? '<div' . $g_id . ' class="user-receive-wallet' . $g_cls . '">' . $uwallet . '</div>' : '';
        return $return;
    }

    /**
     * user_kyc_info()
     *
     * @version 1.0.0
     * @since 1.0
     * @return void
     */
    public static function user_kyc_info($data = null, $atttr = '')
    {
        $atttr_def = array('id' => '', 'class' => '', 'vers' => '');
        $opt_atttr = parse_args($atttr, $atttr_def);
        extract($opt_atttr);
        $g_id = ($id) ? ' id="' . $id . '"' : '';
        $g_cls = ($class) ? css_class($class) : '';

        $user = auth()->user();
        $title_cls = ' card-title-sm';

        $heading = '<h6 class="card-title' . $title_cls . '">' . __('Identity Verification - KYC') . '</h6>';
        $ukyc = $heading . '<p>' . __('To comply with regulation, participant will have to go through identity verification.') . '</p>';
        if (!isset($user->kyc_info->status)) {
            $ukyc .= '<p class="lead text-light pdb-0-5x">' . __('You have not submitted your documents to verify your identity (KYC).') . '</p><a href="' . route('user.kyc.application') . '" class="btn btn-sm m-2 btn-icon btn-primary">' . __('Click to Proceed') . '</a>';
        }
        if (isset($user->kyc_info->status) && $user->kyc_info->status == 'pending') {
            $ukyc .= '<p class="lead text-info pdb-0-5x">' . __('We have received your document.') . '</p><p class="small">' . __('We will review your information and if all is in order will approve your identity. You will be notified by email once we verified your identity (KYC).') . '</p>';
        }
        if (isset($user->kyc_info->status) && ($user->kyc_info->status == 'rejected' || $user->kyc_info->status == 'missing')) {
            $ukyc .= '<p class="lead text-danger pdb-0-5x">' . __('KYC Application has been rejected!') . '</p><p>' . __('We were having difficulties verifying your identity. In our verification process, we found information are incorrect or missing. Please re-submit the application again and verify your identity.') . '</p><a href="' . route('user.kyc.application') . '?state=resubmit" class="btn btn-sm m-2 btn-icon btn-primary">' . __('Resubmit') . '</a><a href="' . route('user.kyc.application.view') . '" class="btn btn-sm m-2 btn-icon btn-secondary">' . __('View KYC') . '</a>';
        }
        if (isset($user->kyc_info->status) && $user->kyc_info->status == 'approved') {
            $ukyc .= '<p class="lead text-success pdb-0-5x"><strong>' . __('Identity (KYC) has been verified.') . '</strong></p><p>' . __('One for our team verified your identity. You are eligible to participate in our token sale.') . '</p><a href="' . route('user.token') . '" class="btn btn-sm m-2 btn-icon btn-primary">' . __('Purchase Token') . '</a><a href="' . route('user.kyc.application.view') . '" class="btn btn-sm m-2 btn-icon btn-success">' . __('View KYC') . '</a>';
        }
        if (token('before_kyc') == '1') {
            $ukyc .= '<h6 class="kyc-alert text-danger">* ' . __('KYC verification required for purchase token') . '</h6>';
        }

        $return = ($ukyc) ? '<div' . $g_id . ' class="kyc-info card' . $g_cls . '"><div class="card-innr">' . $ukyc . '</div></div>' : '';
        return $return;
    }

    /**
     * user_logout_link()
     *
     * @version 1.0.0
     * @since 1.0
     * @return void
     */
    public static function user_logout_link($data = null, $atttr = '')
    {
        $atttr_def = array('id' => '', 'class' => '', 'vers' => '');
        $opt_atttr = parse_args($atttr, $atttr_def);
        extract($opt_atttr);
        $g_id = ($id) ? ' id="' . $id . '"' : '';
        $g_cls = ($class) ? css_class($class) : '';

        $return = '<ul' . $g_id . ' class="user-links bg-light' . $g_cls . '">
        <li><a href="' . route('log-out') . '" onclick="event.preventDefault();document.getElementById(\'logout-form\').submit();"><i class="ti ti-power-off"></i>' . __('Logout') . '</a></li>
        </ul>
        <form id="logout-form" action="' . route('logout') . '" method="POST" style="display: none;"> <input type="hidden" name="_token" value="' . csrf_token() . '"> </form>';

        return $return;
    }

    /**
     * user_menu_links()
     *
     * @version 1.0.0
     * @since 1.0
     * @return void
     */
    public static function user_menu_links($data = null, $atttr = '')
    {
        $atttr_def = array('id' => '', 'class' => '', 'vers' => '');
        $opt_atttr = parse_args($atttr, $atttr_def);
        extract($opt_atttr);
        $g_id = ($id) ? ' id="' . $id . '"' : '';
        $g_cls = ($class) ? css_class($class) : '';

        $return = '<ul' . $g_id . ' class="user-links' . $g_cls . '"><li><a href="' . route('user.account') . '"><i class="ti ti-id-badge"></i>' . __('My Profile') . '</a></li>';
        $return .= '<li><a href="' . route('user.account.activity') . '"><i class="ti ti-eye"></i>' . __('Activity') . '</a></li></ul>';

        return $return;
    }

    /**
     * kyc_footer_info()
     *
     * @version 1.0.0
     * @since 1.0
     * @return void
     */
    public static function kyc_footer_info($data = null, $atttr = '')
    {
        $atttr_def = array('id' => '', 'class' => '', 'vers' => '');
        $opt_atttr = parse_args($atttr, $atttr_def);
        extract($opt_atttr);
        $g_id = ($id) ? ' id="' . $id . '"' : '';
        $g_cls = ($class) ? css_class($class) : '';

        $email = (get_setting('site_support_email', get_setting('site_email'))) ? ' <a href="mailto:' . get_setting('site_support_email', get_setting('site_email')) . '">' . get_setting('site_support_email', get_setting('site_email')) . '</a>' : '';
        $gaps = '<div class="gaps-3x d-none d-sm-block"></div>';

        $return = ($email) ? '<p class="text-light text-center">' . ( __('Contact our support team via email') ) . ' - '.$email.'</p><div class="gaps-1x"></div>' . $gaps : '';

        return $return;
    }

    /**
     * social_links()
     *
     * @version 1.0.0
     * @since 1.0
     * @return void
     */
    public static function social_links($data = null, $atttr = '')
    {
        $atttr_def = array('id' => '', 'class' => '', 'vers' => '');
        $opt_atttr = parse_args($atttr, $atttr_def);
        extract($opt_atttr);
        $g_id = ($id) ? ' id="' . $id . '"' : '';
        $g_cls = ($class) ? css_class($class) : '';

        $link = json_decode(get_setting('site_social_links'));

        $fb = (isset($links->facebook) && $links->facebook != null) ? '<li><a href="' . $link->facebook . '"><em class="fab fa-facebook-f"></em></a></li>' : '';
        $tw = (isset($links->twitter) && $links->twitter != null) ? '<li><a href="' . $link->twitter . '""><em class="fab fa-twitter"></em></a></li>' : '';
        $in = (isset($links->linkedin) && $links->linkedin != null) ? '<li><a href="' . $link->linkedin . '"><em class="fab fa-linkedin-in"></em></a></li>' : '';
        $gh = (isset($links->github) && $links->github != null) ? '<li><a href="' . $link->github . '"><em class="fab fa-github-alt"></em></a></li>' : '';

        $return = ($fb || $tw || $in || $gh) ? '<ul' . $g_id . ' class="social-links' . $g_cls . '">' . $fb . $tw . $in . $gh . '</ul>' : '';

        return $return;
    }

    /**
     * footer_links()
     *
     * @version 1.0.0
     * @since 1.0
     * @return void
     */
    public static function footer_links($data = null, $atttr = '')
    {
        $atttr_def = array('id' => '', 'class' => '', 'vers' => '');
        $opt_atttr = parse_args($atttr, $atttr_def);
        extract($opt_atttr);
        $g_id = ($id) ? ' id="' . $id . '"' : '';
        $g_cls = ($class) ? css_class($class) : '';

        $how_to = (get_page('how_buy', 'status') == 'active') ? '<li><a href="' . route('public.pages', get_slug('how_buy')) . '">' . get_page('how_buy', 'menu_title') . '</a></li>' : '';
        $cs_page = (get_page('custom_page', 'status') == 'active') ? '<li><a href="' . route('public.pages', get_slug('custom_page')) . '">' . get_page('custom_page', 'menu_title') . '</a></li>' : '';
        $faqs = (get_page('faq', 'status') == 'active') ? '<li><a href="' . route('public.pages', get_slug('faq')) . '">' . get_page('faq', 'menu_title') . '</a></li>' : '';
        if (!auth()->check()) {
            $how_to = $faqs = $cs_page = '';
        }
        $privacy = (get_page('privacy', 'status') == 'active') ? '<li><a href="' . route('public.pages', get_slug('privacy')) . '">' . get_page('privacy', 'menu_title') . '</a></li>' : '';
        $terms = (get_page('terms', 'status') == 'active') ? '<li><a href="' . route('public.pages', get_slug('terms')) . '">' . get_page('terms', 'menu_title') . '</a></li>' : '';
        $copyrights = ($vers == 'copyright') ? '<li>&copy; ' . date('Y ') . site_info() . ' .</li>' : '';
        $lang = '';
        $return = ($privacy || $terms) ? '<ul' . $g_id . ' class="footer-links' . $g_cls . '">' . $cs_page . $how_to . $faqs . $privacy . $terms . $copyrights . $lang . '</ul>' : '';

        return (!is_maintenance() ? $return : '');
    }

    public static function language_switcher()
    {
        $l = str_replace('_', '-', app()->getLocale());

        $text = '<li class="dropdown-header"><div class="lang-switch relative"><a href="javascript:void(0)" class="lang-switch-btn toggle-tigger">'.strtoupper($l).'<em class="ti ti-angle-up"></em></a>';
        $text .= '<div class="toggle-class dropdown-content dropdown-content-up"><ul class="lang-list">';
        foreach (config('icoapp.supported_languages') as $lng) {
            $text .= '<li><a href="'.route('language').'?lang='.strtolower($lng) .'">'.strtoupper($lng).'</a></li>';
        }
        $text .= '</ul></div></div></li>';
        return $text;
    }

    /**
     * content_block()
     *
     * @version 1.0.0
     * @since 1.0
     * @return void
     */
    public static function content_block($data = null, $atttr = '')
    {
        $atttr_def = array('id' => '', 'class' => '', 'vers' => '');
        $opt_atttr = parse_args($atttr, $atttr_def);
        extract($opt_atttr);
        $g_id = ($id) ? ' id="' . $id . '"' : '';
        $g_cls = ($class) ? css_class($class) : '';

        $return = '';
        $img_url = (isset($image) && $image !='') ? asset('assets/images/'.$image) : '';
        if ($data == 'welcome') {
            $return .= '<div' . $g_id . ' class="card content-welcome-block' . $g_cls . '"><div class="card-innr">';
            $return .= ($img_url) ? '<div class="row guttar-vr-20px">' : '';

            if ($img_url) {
            $return .= '<div class="col-sm-5 col-md-4"><div class="card-image card-image-sm"><img width="240" src="'.$img_url.'" alt=""></div></div><div class="col-sm-7 col-md-8">';
            }
            $return .= '<div class="card-content">';
            $return .= '<h4>' . get_page('home_top', 'title') . '</h4>';
            $return .= get_page('home_top', 'description');
            $return .= '</div>';

            $return .= ($img_url) ? '</div></div>' : '';
            $return .= '<div class="d-block d-md-none gaps-0-5x mb-0"></div></div></div>';
        }

        if ($data == 'bottom') {
            $return = '<div' . $g_id . ' class="content-bottom-block card' . $g_cls . '"><div class="card-innr"><div class="table-responsive">' . get_page('home_bottom', 'description') . '</div></div></div>';
        }

        return $return;
    }

    /**
     * token_sales_progress()
     *
     * @version 1.0.0
     * @since 1.0
     * @return void
     */
    public static function token_sales_progress($data = null, $atttr = '')
    {
        $atttr_def = array('id' => '', 'class' => '', 'vers' => '');
        $opt_atttr = parse_args($atttr, $atttr_def);
        extract($opt_atttr);
        $g_id = ($id) ? ' id="' . $id . '"' : '';
        $g_cls = ($class) ? css_class($class) : '';

        $title = $progress = $progress_bar = $sales_end_in = $sales_start_in = '';

        $title .= '<div class="card-head"><h5 class="card-title card-title-sm">'. __('Token Sales Progress').'</h5></div>';

        $progress .= '<ul class="progress-info"><li><span>'.__('Raised Amount').' <br></span>'.ico_stage_progress('raised').'</li><li><span>'.__('Total Token').' <br></span>'.number_format(active_stage()->total_tokens).' '.token_symbol().'</li></ul>';

        $no_class = ((active_stage()->hard_cap < 10) && (active_stage()->soft_cap < 10)) ? ' no-had-soft' : '';

        $progress_bar = '<div class="progress-bar'.$no_class.'">';
            if(active_stage()->hard_cap >= 10) {
            $progress_bar .= '<div class="progress-hcap" data-percent="'.ico_stage_progress('hard').'"><div>'.__('Hard Cap').' <span>'.ico_stage_progress('hardtoken').'</span></div></div>';
            }
            if(active_stage()->soft_cap >= 10) {
            $progress_bar .= '<div class="progress-scap" data-percent="'.ico_stage_progress('soft').'"><div>'.__('Soft Cap').' <span>'.ico_stage_progress('softtoken').'</span></div></div>';
            }
        $progress_bar .= '<div class="progress-percent" data-percent = "'.ceil((active_stage()->sales_token * 100) / active_stage()->total_tokens).'"></div></div>';

        $sales_end_in .= '<span class="card-sub-title ucap mgb-0-5x">'.__('Sales End in').'</span><div class="countdown-clock" data-date="'._date(active_stage()->end_date, 'Y/m/d').'"></div>';

        $return = '<div' . $g_id . ' class="card card-sales-progress' . $g_cls . '"><div class="card-innr">'.$title.$progress.$progress_bar.$sales_end_in.'</div></div>';

        return $return;
    }
}
