<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserMeta;
use App\Models\Referral;
use App\Notifications\ConfirmEmail;
use Carbon\Carbon;
use Cookie;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use DB;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
     */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/register/success';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

  
    
    public function showRegistrationForm()
    {
        if (application_installed(true) == false) {
            return redirect(url('/install'));
        }
        return view('auth.register');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        $term = get_page('terms', 'status') == 'active' ? 'required' : 'nullable';
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'terms' => [$term],
        ], [
            'terms.required' => __('messages.agree'),
            'email.unique' => 'The email address you have entered is already registered. Did you <a href="' . route('password.request') . '">forget your login</a> information? ',
        ]);

    }
    protected function getRandom()
    {
       return $random=Str::random(10);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        $random=Str::random(10);
        $have_user = User::where('role', 'admin')->count();
        $type = ($have_user >= 1) ? 'user' : 'admin';
        $email_verified = ($have_user >= 1) ? null : now();
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'lastLogin' => date('Y-m-d H:i:s'),
            'role' => $type,
            'referral'=>$random,
        ]);
        if ($user) {
            if ($have_user <= 0) {
                    save_gmeta('site_super_admin', $user->id, $user->id);
                                  }
            $user->email_verified_at = $email_verified;
            
           


    /**************************************************************************************** */ 
           
                if (isset ($data['referral'])) {
                   

                    $x=$data['referral'];

                    $user_sp = DB::table('users')->where('referral', $x)->first();

                  
                
                    //level 1
                       if ($user_sp->level === 0 or $user_sp->level === 7   ) {
                                $referral = Referral::create([
                                    'invete'=>$data['referral'],
                                    'inveted'=>$random,
                                    'level'=>1,
                                                        ]); 
                               $user->level= $user_sp->level+1; 
                                $referral->save(); 
                                           }
                    
                        //level 2
                   elseif ($user_sp->level === 1) {

                        $referral = Referral::create([
                            'invete'=>$data['referral'],
                            'inveted'=>$random,
                            'level'=>1,
                                                ]);

                    $father_invete=DB::table('referrals')->where('inveted', $x)
                                                         ->where('level',1)
                                                         ->first();

                        $referral_2 = Referral::create([
                            'invete'=>$father_invete->invete,
                            'inveted'=>$random,
                            'level'=>2,
                                               ]);
                                                     
                        $user->level= $user_sp->level+1; 
                            $referral->save();
                            $referral_2->save();
                        }

                        //level 3
                        elseif ($user_sp->level === 2) {

                           

                            $referral = Referral::create([
                                'invete'=>$data['referral'],
                                'inveted'=>$random,
                                'level'=>1,
                                                    ]);
    
                        $father_invete=DB::table('referrals')->where('inveted', $x)
                                                             ->where('level',2)
                                                             ->first();
    
                            $referral_2 = Referral::create([
                                'invete'=>$father_invete->invete,
                                'inveted'=>$random,
                                'level'=>2,
                                                   ]);

                        $father_invete=DB::table('referrals')->where('invete', $x)
                                                             ->where('level',1)
                                                             ->first();
                                                   
                            $referral_3 = Referral::create([
                                'invete'=>$father_invete->invete,
                                'inveted'=>$random,
                                'level'=>3,
                                                   ]);
                                                         
                            $user->level= $user_sp->level+1; 
                                $referral->save();
                                $referral_2->save();
                                $referral_3->save();
                            }

                             //level 4
                        elseif ($user_sp->level === 3) {

                           

                            $referral = Referral::create([
                                'invete'=>$data['referral'],
                                'inveted'=>$random,
                                'level'=>1,
                                                    ]);
    
                        $father_invete=DB::table('referrals')->where('inveted', $x)
                                                             ->where('level',1)
                                                             ->first();
    
                            $referral_2 = Referral::create([
                                'invete'=>$father_invete->invete,
                                'inveted'=>$random,
                                'level'=>2,
                                                   ]);

                        $father_invete=DB::table('referrals')->where('invete', $x)
                                                             ->where('level',2)
                                                             ->first();
                                                   
                            $referral_3 = Referral::create([
                                'invete'=>$father_invete->invete,
                                'inveted'=>$random,
                                'level'=>3,
                                                   ]);
                        $father_invete=DB::table('referrals')->where('invete', $x)
                                                             ->where('level',3)
                                                             ->first();
                                         
                  $referral_4 = Referral::create([
                      'invete'=>$father_invete->invete,
                      'inveted'=>$random,
                      'level'=>4,
                                         ]);        
                            $user->level= $user_sp->level+1; 
                                $referral->save();
                                $referral_2->save();
                                $referral_3->save();
                                $referral_4->save();
                            }
        //level 5
                        elseif ($user_sp->level === 4) {

                              $referral = Referral::create([
                                        'invete'=>$data['referral'],
                                        'inveted'=>$random,
                                        'level'=>1,
                                                       ]);
    
                        $father_invete=DB::table('referrals')->where('inveted', $x)
                                                             ->where('level',1)
                                                             ->first();
    
                            $referral_2 = Referral::create([
                                'invete'=>$father_invete->invete,
                                'inveted'=>$random,
                                'level'=>2,
                                                   ]);

                        $father_invete=DB::table('referrals')->where('invete', $x)
                                                             ->where('level',2)
                                                             ->first();
                                                   
                            $referral_3 = Referral::create([
                                'invete'=>$father_invete->invete,
                                'inveted'=>$random,
                                'level'=>3,
                                                   ]);
                        $father_invete=DB::table('referrals')->where('invete', $x)
                                                             ->where('level',3)
                                                             ->first();
                                         
                  $referral_4 = Referral::create([
                      'invete'=>$father_invete->invete,
                      'inveted'=>$random,
                      'level'=>4,
                                         ]); 
                     $father_invete=DB::table('referrals')->where('invete', $x)
                                                             ->where('level',4)
                                                             ->first();
                                         
                  $referral_5 = Referral::create([
                      'invete'=>$father_invete->invete,
                      'inveted'=>$random,
                      'level'=>5,
                                         ]);        
                        $user->level= $user_sp->level+1; 
                                $referral->save();
                                $referral_2->save();
                                $referral_3->save();
                                $referral_4->save();
                                $referral_5->save();
                            }
                             //level 6
                        elseif ($user_sp->level === 5) {

                           

                            $referral = Referral::create([
                                'invete'=>$data['referral'],
                                'inveted'=>$random,
                                'level'=>1,
                                                    ]);
    
                        $father_invete=DB::table('referrals')->where('inveted', $x)
                                                             ->where('level',1)
                                                             ->first();
    
                            $referral_2 = Referral::create([
                                'invete'=>$father_invete->invete,
                                'inveted'=>$random,
                                'level'=>2,
                                                   ]);

                        $father_invete=DB::table('referrals')->where('invete', $x)
                                                             ->where('level',2)
                                                             ->first();
                                                   
                            $referral_3 = Referral::create([
                                'invete'=>$father_invete->invete,
                                'inveted'=>$random,
                                'level'=>3,
                                                   ]);
                        $father_invete=DB::table('referrals')->where('invete', $x)
                                                             ->where('level',3)
                                                             ->first();
                                         
                  $referral_4 = Referral::create([
                      'invete'=>$father_invete->invete,
                      'inveted'=>$random,
                      'level'=>4,
                                         ]); 
                    $father_invete=DB::table('referrals')->where('invete', $x)
                                                             ->where('level',4)
                                                             ->first();
                                         
                  $referral_5 = Referral::create([
                      'invete'=>$father_invete->invete,
                      'inveted'=>$random,
                      'level'=>5,
                                         ]); 
                                         
                                         $father_invete=DB::table('referrals')->where('invete', $x)
                                         ->where('level',5)
                                         ->first();
                     
                            $referral_6 = Referral::create([
                            'invete'=>$father_invete->invete,
                            'inveted'=>$random,
                            'level'=>6,
                                                ]); 

                        $user->level= $user_sp->level+1; 
                                $referral->save();
                                $referral_2->save();
                                $referral_3->save();
                                $referral_4->save();
                                $referral_6->save();
                            }
                              //level 7
                        elseif ($user_sp->level === 6) {

                           

                            $referral = Referral::create([
                                'invete'=>$data['referral'],
                                'inveted'=>$random,
                                'level'=>1,
                                                    ]);
    
                        $father_invete=DB::table('referrals')->where('inveted', $x)
                                                             ->where('level',1)
                                                             ->first();
    
                            $referral_2 = Referral::create([
                                'invete'=>$father_invete->invete,
                                'inveted'=>$random,
                                'level'=>2,
                                                   ]);

                        $father_invete=DB::table('referrals')->where('invete', $x)
                                                             ->where('level',2)
                                                             ->first();
                                                   
                            $referral_3 = Referral::create([
                                'invete'=>$father_invete->invete,
                                'inveted'=>$random,
                                'level'=>3,
                                                   ]);
                        $father_invete=DB::table('referrals')->where('invete', $x)
                                                             ->where('level',3)
                                                             ->first();
                                         
                  $referral_4 = Referral::create([
                      'invete'=>$father_invete->invete,
                      'inveted'=>$random,
                      'level'=>4,
                                         ]); 
                    $father_invete=DB::table('referrals')->where('invete', $x)
                                                             ->where('level',4)
                                                             ->first();
                                         
                  $referral_5 = Referral::create([
                      'invete'=>$father_invete->invete,
                      'inveted'=>$random,
                      'level'=>5,
                                         ]); 
                                         
                                         $father_invete=DB::table('referrals')->where('invete', $x)
                                         ->where('level',5)
                                         ->first();
                     
                            $referral_6 = Referral::create([
                            'invete'=>$father_invete->invete,
                            'inveted'=>$random,
                            'level'=>6,
                                                ]); 
                            $father_invete=DB::table('referrals')->where('invete', $x)
                            ->where('level',6)
                            ->first();
                            
                                   $referral_7 = Referral::create([
                                   'invete'=>$father_invete->invete,
                                   'inveted'=>$random,
                                   'level'=>7,
                                                       ]); 
                        $user->level= $user_sp->level+1; 
                                $referral->save();
                                $referral_2->save();
                                $referral_3->save();
                                $referral_4->save();
                                $referral_6->save();
                                $referral_7->save();
                            }
                            
                            else $user->level=0;

               }
            


           //check that Invete does not exist .
           /*
           $Same_invete=Referral::where('invete',$random)->count();
           if($Same_invete >=1) {
              getRandom();
                               }
           else {
        //---------------condition of inveted referral
       
    


/***************************************************************************** */
$user->save();

 
          /* 
          if (Cookie::has('ico_referral_from')) {
                $ref = (int) Cookie::get('ico_referral_from');
                  
                $ref_user = User::where('referral', $ref)->first();
                if ($ref_user) {
                    $user->referral = $ref_user->id;
                    $user->referralInfo = json_encode([
                        'user' => $ref_user->id,
                        'name' => $ref_user->name,
                        'time' => now(),
                    ]);
                }
                Cookie::queue(Cookie::forget('ico_referral_from'));
            }
            */
            
            
            

            $meta = UserMeta::create([
                'userId' => $user->id,
            ]);
            $meta->notify_admin = ($type=='user')?0:1;
            $meta->email_token = str_random(65);
            $cd = Carbon::now(); //->toDateTimeString();
            $meta->email_expire = $cd->copy()->addMinutes(75);
            $meta->save();

            if ($user->email_verified_at == null) {
                try {
                    $user->notify(new ConfirmEmail($user));
                } catch (\Exception $e) {
                    session('warning', 'User Registered, but confirmation email is not send!');
                }
            }
        }
        return $user ;
    }
}
