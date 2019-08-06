<?php

namespace App\Http\Controllers\User;
/**
 * User Controller
 *
 *
 * @package Chkernit
 * @author Chkernit
 * @version 1.1
 */
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Referral;
use App\Models\UserMeta;
use Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class ReferralController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
             //select all info of user
             $user = Auth::user();     
             $userMeta = UserMeta::getMeta($user->id);
    
             
            //select the referral items 
            $referral=Referral::where('invete',$user->referral)->orderBy('level', 'ASC')->get();

         // $referral = DB::select('select * from referrals where invete = ? and level= ? ORDER BY level ASC ', [$x,1]);
     
             return view('user.referrals', compact('user','userMeta','referral'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
     


    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
