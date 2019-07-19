<?php
/**
* Social Login Controller
*
* Login with social (google + facebook)
*
* @package TokenLite
* @author Softnio
* @version 1.0
*/
namespace App\Http\Controllers\Auth;

use Auth;
use Session;
use Socialite;
use App\Models\User;
use App\Models\Activity;
use App\Models\UserMeta;
use Jenssegers\Agent\Agent;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SocialAuthController extends Controller
{
    // Available social login 
    protected $available = ['facebook', 'google'];
    /**
     * Redirect to the Service for Login
     *
     * @return \Illuminate\Http\Response
     */
    public function redirect($social)
    {
        if(! in_array($social, $this->available)) {
            session()->flash('warning', __('messages.invalid.social'));
            return redirect()->route('login');
        }
        if (
            (get_setting('site_api_fb_id', env('FB_CLIENT_ID', '')) != '' && get_setting('site_api_fb_secret', env('FB_CLIENT_SECRET', '')) != '') || 
            (get_setting('site_api_google_id', env('GOOGLE_CLIENT_ID', '')) != '' && get_setting('site_api_google_secret', env('GOOGLE_CLIENT_SECRET', '')) != '')
            ) {
                return Socialite::driver($social)->redirect();
        }else{
            return back()->with(['warning' => __('messages.invalid.social')]);
        }
    }

    /**
     * Callback for Socialite
     *
     * @return \Illuminate\Http\Response
     */
    public function callback($social)
    {
        try {
            $user = Socialite::driver($social)->user();
            
            if(empty($user)){
                session()->flash('info', __('Sorry, Something is wrong, please login via your email & password!'));
                return redirect()->route('login');
            }
            
            $name = $user->getName();
            $email = $user->getEmail();
            $id = $user->getId();
            
            //check if user already exists
            $checkUser = User::where(['email'=> $email, 'social_id' => $id])->first();
            if($checkUser){
                Auth::login($checkUser, true);
                $this->save_activity();
                // return redirect()->route('home');
            }
            $checkEMail = User::where(['email'=> $email])->first();
            if($checkEMail){
                $has_social = ($checkEMail->social_id != null) ? true : false;
                $msg = ($has_social) ? 'You are already registered, try again with different social account!' : 'Sorry, Something is wrong, please try again!';
                session()->flash('warning', $msg);
                return redirect()->route('login');;
            }
            $notice = "You have not registered yet in our platform. You can sign up with your ".ucfirst($social)." account.";
            // show the confirm form 
            return view('auth.social', compact('user', 'social', 'notice'));
        } catch (\Exception $e) {
            session()->flash('warning', __('Sorry, Something is wrong, please login via your email & password!!'));
            return redirect()->route('login');
        }
    }

    /**
     * Finally register the user
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'social_id' => 'required',
        ]);
        $password = str_random(12);
        $createUser = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($password),
            'role' => 'user',
            'lastLogin' => date("Y-m-d H:i:s"),
        ]);        
        
        if($createUser){
            UserMeta::create([
                'userId' => $createUser->id
            ]);
            $social = $request->social;
            $createUser->email_verified_at = ( $social=='google' ? now() : NULL);
            $createUser->status = 'active';
            $createUser->registerMethod = $social;
            $createUser->social_id = $request->social_id;
            $createUser->save();

            Auth::login($createUser, true);
            $this->save_activity();
            
            return redirect()->route('home');
        }else{
            return redirect()->route('home');
        }
    }
    /**
     * Save user Activity
     *
     * @return activity
     */
    protected function save_activity()
    {
        if (UserMeta::getMeta(Auth::id())->save_activity == 'TRUE') {
            $agent = new Agent();

            $ret['activity'] = Activity::create([
                'user_id' => Auth::id(),
                'browser' => $agent->browser().'/'.$agent->version($agent->browser()),
                'device' => $agent->device().'/'.$agent->platform().'-'.$agent->version($agent->platform()),
                'ip' => request()->ip()
            ]);
        }
    }

}