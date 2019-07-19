@extends('layouts.user')
@section('title', __('Referrals'))
@php($has_sidebar = true)

@section('content')
@include('layouts.messages')
<div class="content-area card">
    <div class="card-innr">
        <div class="card-head">
            <h4 class="card-title">{{__('Referrals')}}</h4>
        </div>
        <ul class="nav nav-tabs nav-tabs-line" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" data-toggle="tab" href="#personal-data">{{__('Invite Your Buddies
')}}</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#settings">{{__('Genealogy')}}</a>
            </li>
            <li class="nav-item">
        <a class="nav-link" data-toggle="tab" href="#password">{{__('Referral Banners')}}</a>
            </li>
        </ul>{{-- .nav-tabs-line --}}
        <div class="tab-content" id="profile-details">
            <div class="tab-pane fade show active" id="personal-data">
                <form class="validate-modern" action="{{ route('user.ajax.account.update') }}" method="POST" id="nio-user-personal" autocomplete="off">
                    @csrf
                    <input type="hidden" name="action_type" value="personal_data">
                    <div class="row">
                        <div  class="col-md-12">
                       &nbsp;&nbsp;&nbsp;  <h4>Share your unique referral link with others. When others register for Monifinex, you get credit for the referral.

                        </h4>
                        </div>
                        
                        <div class="col-md-12">
                           <div class="input-item input-with-label">
                          <label for="full-name" class="input-item-label">{{__('Referral Link:')}}</label>
                                
                     <div class="input-group">

                       <input id="ref_url" class="form-control"   name="url" type="text"
                              value="http://127.0.0.1:8000/register?ref={{$user->referral}}">
                    <span class="input-group-btn">
                        <button id="copy-ref-url" onclick="coypf()" class="btn btn-primary" type="button">Copy</button>
                    </span>

                    <script>
                    function coypf(){
                      var copyText = document.getElementById("ref_url");
                      copyText.select();
                      document.execCommand("copy");
                      alert("Copied the text: " + copyText.value);
                                    }
                        </script>
                </div>    
                                
                                
                            </div>{{-- .input-item --}}
                        </div>
                       
                    </div>{{-- .row --}}
                   
                   
                </form>{{-- form --}}

            </div>{{-- .tab-pane --}}
            <div class="tab-pane fade" id="settings">
                <form class="validate-modern" action="{{ route('user.ajax.account.update') }}" method="POST" id="nio-user-settings">
                    @csrf
                    <input type="hidden" name="action_type" value="account_setting">
                    <div class="pdb-1-5x">
                        <br>
                        <h5 class="card-title card-title-sm text-dark">{{__('
GENEALOGY')}}</h5>
                    </div>
                 
                    <div class="gaps-1x"></div>
                  
                    
                    
                      <table class="table">
    <thead>
      <tr>
        <th>Firstname</th>
        <th>Lastname</th>
        <th>Email</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>USERNAME</td>
        <td>JOINING DATE</td>
        <td>BALANCE</td>
      </tr>      
      <tr class="table-primary">
        <td>Primary</td>
        <td>Joe</td>
        <td>joe@example.com</td>
      </tr>
      <tr class="table-success">
        <td>Success</td>
        <td>Doe</td>
        <td>john@example.com</td>
      </tr>
     
    </tbody>
  </table>
                    <?php
                    $a=0;
            foreach ($users as $user) {
                $a++;
               echo $user->id;
                
                                    }
                    
                    echo "<br>number of your affilaite is : ".$a;
       ?>
                </form>
            </div>{{-- .tab-pane --}}

            <div class="tab-pane fade" id="password">
                <form class="validate-modern" action="{{ route('user.ajax.account.update') }}" method="POST" id="nio-user-password">
                    @csrf
                    <input type="hidden" name="action_type" value="pwd_change">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="input-item input-with-label">
                                <label for="old-pass" class="input-item-label">{{__('Old Password')}}</label>
                                <div class="input-wrap">
                                    <input class="input-bordered" type="password" name="old-password" id="old-pass" required="required">
                                </div>
                            </div>{{-- .input-item --}}
                        </div>{{-- .col --}}
                    </div>{{-- .row --}}
                    <div class="row">
                        <div class="col-md-6">
                            <div class="input-item input-with-label">
                                <label for="new-pass" class="input-item-label">{{__('New Password')}}</label>
                                <div class="input-wrap">
                                    <input class="input-bordered" id="new-pass" type="password" name="new-password" required="required" minlength="6">
                                </div>
                            </div>{{-- .input-item --}}
                        </div>{{-- .col --}}
                        <div class="col-md-6">
                            <div class="input-item input-with-label">
                                <label for="confirm-pass" class="input-item-label">{{__('Confirm New Password')}}</label>
                                <div class="input-wrap">
                                    <input id="confirm-pass" class="input-bordered" type="password" name="re-password" data-rule-equalTo="#new-pass" data-msg-equalTo="Password not match." required="required" minlength="6">
                                </div>
                            </div>{{-- .input-item --}}
                        </div>{{-- .col --}}
                    </div>{{-- .row --}}
                    <div class="note note-plane note-info pdb-1x">
                        <em class="fas fa-info-circle"></em>
                        <p>{{__('Password should be minimum 6 letter and include lower and uppercase letter.')}}</p>
                    </div>
                    <div class="note note-plane note-danger pdb-2x">
                        <em class="fas fa-info-circle"></em>
                        <p>{{__('Your password will only change after confirm your email.')}}</p>
                    </div>
                    <div class="gaps-1x"></div>{{-- 10px gap --}}
                    <div class="d-sm-flex justify-content-between align-items-center">
                        <button type="submit" class="btn btn-primary">{{__('Update')}}</button>

                        <div class="gaps-2x d-sm-none"></div>

                    </div>
                </form>
            </div>{{-- .tab-pane --}}
        </div>{{-- .tab-content --}}
    </div>{{-- .card-innr --}}
</div>{{-- .card --}}
@endsection
