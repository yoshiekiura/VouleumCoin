<?php

namespace App\Notifications;

use App\Models\EmailTemplate as ET;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class TnxStatus extends Notification implements ShouldQueue
{
    use Queueable;

    protected $tnx_data = null;
    protected $template = null;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($tnx_data, $template)
    {
        $this->tnx_data = $tnx_data;
        $this->template = $template;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $from_name = email_setting('from_name', get_setting('site_name'));
        $from_email = email_setting('from_email', get_setting('site_email'));

        $template = ET::get_template('order-'.$this->template);
        $transaction = $this->tnx_data;
        $user = $this->tnx_data->tnxUser;

        $template->message = $this->replace_shortcode($template->message);
        $template->regards = ($template->regards == 'true' ? get_setting('site_mail_footer', "Best Regards, \n[[site_name]]") : '');
        
        return (new MailMessage)
                    ->greeting($this->replace_shortcode($template->greeting))
                    ->salutation($this->replace_shortcode($template->regards))
                    ->from($from_email, $from_name)
                    ->subject($this->replace_shortcode($template->subject))
                    ->markdown('mail.transaction', compact('template', 'transaction', 'user'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }

    /**
     * Get the short-code and replace with data.
     *
     * @param  mixed  $code
     * @return void
     */
    public function replace_shortcode($code)
    {
        $shortcode =array(
            "\n",
            '[[token_name]]',
            '[[token_symbol]]',
            '[[site_name]]',
            '[[site_email]]',
            '[[site_url]]',

            '[[order_details]]',
            '[[order_id]]',
            '[[support_email]]',
            '[[site_login]]',
            '[[user_name]]',
            '[[user_email]]',
            '[[payment_amount]]',
            '[[payment_from]]',
            '[[payment_gateway]]',
            '[[total_tokens]]',
        );
        $replace = array(
            "<br>",
            token('name'),
            token('symbol'),
            site_info('name', false),
            site_info('email', false),
            url('/'),

            $this->get_blade('order_details', $this->tnx_data),
            $this->tnx_data->tnx_id,
            get_setting('site_support_email'),
            $this->get_blade('button', ['url'=>url('/login'), 'title'=>'Login Here']),
            $this->tnx_data->tnxUser->name,
            $this->tnx_data->tnxUser->email,
            $this->tnx_data->amount.' '.token_symbol(),
            $this->tnx_data->wallet_address,
            ucfirst($this->tnx_data->payment_method),
            $this->tnx_data->total_tokens.' '.token_symbol(),
        );
        $return = str_replace($shortcode, $replace, $code);
        return $return;
    }

    public function get_blade($name='', $data='')
    {
        $blade = '';
        if ($name == 'button' && $data != null) {
            $blade = '<table width="100%" border="0" cellpadding="0" cellspacing="0"><tr><td align="center"><table border="0" cellpadding="0" cellspacing="0"><tr><td><a href="'.(isset($data['url']) ? $data['url'] : url('/')).'" class="button button-green" target="_blank">'.(isset($data['title']) ? $data['title'] : site_info('name')).'</a></td></tr></table></td></tr></table>';
        }
        $currency = strtolower($this->tnx_data->currency);
        $bank = get_b_data('manual');
        if ($name == 'order_details') {
            $pm = $this->tnx_data->payment_method;
            
            if ($pm == 'manual' && $currency != 'usd') {
                $mnl = get_pm('manual');
                $pay_address = '<tr><td>Payment to Address</td><td>:</td><td><strong>'.$mnl->$currency->address.' ('.strtoupper($currency).')</strong></td></tr>';
            } elseif ($pm == 'manual' && $currency == 'usd') {
                $pay_address = "<tr><td>Payment to Address</td><td>:</td><td><strong>
                Account Name:" .(!empty($bank->bank_account_name) ? $bank->bank_account_name : '')."<br>
                Account Number: " .(!empty($bank->bank_account_number) ? $bank->bank_account_number : '')."<br>
                Bank Name: " .(!empty($bank->bank_name) ? $bank->bank_name : '')."<br>
                Routing Number: " .(!empty($bank->routing_number) ? $bank->routing_number : '')."<br>
                IBAN: " .(!empty($bank->iban) ? $bank->iban : '')."<br>
                SWIFT/BIC: " .(!empty($bank->swift_bic) ? $bank->swift_bic : '')."<br></strong></td></tr>";
            } else {
                $data = json_decode($this->tnx_data->extra);
                if ($pm == 'paypal') {
                    $pay_url = (isset($data->url) ? $data->url : null);
                } elseif ($pm == 'coingate') {
                    $pay_url = (isset($data->url) ? $data->url : null);
                } elseif ($pm == 'coinbase') {
                    $pay_url = (isset($data->hosted_url) ? $data->hosted_url : null);
                } else {
                    $pay_url = route('user.token');
                }
                $online = get_pm(strtolower($pm));
                $pay_address = ((empty($online) || $pay_url == null) ? '' : '<tr><td>Payment to </td><td>:</td><td><a href="'.$pay_url.'" target="_blank">Pay via '.ucfirst($pm).'</a></td></tr>');
            }
            $blade = '<table class="table order"><thead><th colspan="3">Order details are follows:</th></thead><tbody class="text-left"><tr><td width="150">Order ID</td><td width="15">:</td><td><strong>#'.$this->tnx_data->tnx_id.'</strong></td></tr><tr><td>ICO Stage</td><td>:</td><td><strong>'.$this->tnx_data->ico_stage->name.'</strong></td></tr><tr><td>Token Number</td><td>:</td><td><strong>'.$this->tnx_data->tokens.' '.token_symbol().'</strong></td></tr><tr><td>Bonus </td><td>:</td><td><strong>'.$this->tnx_data->total_bonus.' '.token_symbol().'</strong> </td></tr><tr><td>Total Token</td><td>:</td><td><strong>'.$this->tnx_data->total_tokens.' '.token_symbol().'</strong> </td></tr><tr><td>Payment Amount</td><td>:</td><td><strong>'.$this->tnx_data->amount.' '.strtoupper($this->tnx_data->currency).'</strong></td></tr><tr><td>Payment Status</td><td>:</td><td><strong>'.ucfirst($this->tnx_data->status).'</strong></td></tr><tr><td>Payment Method</td><td>:</td><td><strong>'.($currency != 'usd' ? ucfirst($this->tnx_data->payment_method) : 'BANK').'</strong></td></tr>'.((!str_contains($this->template, 'admin') && ($this->tnx_data->status == 'pending' || $this->tnx_data->status == 'onhold')) ? $pay_address : '').'</tbody></table>';
        }


        return $blade;
    }
}
