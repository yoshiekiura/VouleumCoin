@extends('layouts.user')
@section('title', __('Referrals'))

@section('content')
@include('layouts.messages')
 

<div class="container">
<div class="row ">
                        <div  class="col-md-12 animated fadeInRight	 ">
                        <h4 class="card-title">{{__('Referrals')}}</h4>
                       &nbsp;&nbsp;&nbsp;  <h5>Share your unique referral link with others. When others register for Monifinex, you get credit for the referral.

                        </h5>
                       </div>
                        
                        <div class="col-md-12 animated fadeInLeft">
                           <div class="input-item input-with-label">
                          <label for="full-name" class="input-item-label">{{__('Referral Link:')}}</label>
                                
                     <div class="input-group">
                              <input id="ref_url" class="form-control"   name="url" type="text"
                              value="http://www.voulumcoin/register?ref={{$user->referral}}">
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
</div>
</div> 




<div class="col-md-12 "  style="margin-top:30px; margin-bottom:60px;">  
    <div class="col-md-8  animated fadeInDown">   
       <h4 class="card-title">{{__('Genealogy')}}</h4>
       <br>
        @if(count($referral)>0)
       <table class="table">
    <thead>
      <tr>
       
        <th>My inveted</th>
        <th>level</th>
      </tr>
    </thead>
    <tbody>
         
        @foreach($referral as $items)
                <tr>
          <td>{{ $items->inveted }}</td>
          <td>{{  $items->level }}</td>
        </tr>
        <tr>
    @endforeach
             
    </tbody>
  </table>
  @else  <p>you don't have any records ... </p>  
           @endif
</div>
    
 
<div class="col-md-4  animated fadeInUp ">
 <h4> Benefit from your generation </h4>
<ul class="list-group">
  <li class="list-group-item d-flex justify-content-between align-items-center">
   Generation  1 
    <span class="badge badge-primary badge-pill"> 10% </span>
  </li>
   <li class="list-group-item d-flex justify-content-between align-items-center">
   Generation  2
    <span class="badge badge-primary badge-pill"> 7% </span>
  </li> <li class="list-group-item d-flex justify-content-between align-items-center">
   Generation  3 
    <span class="badge badge-primary badge-pill"> 5% </span>
  </li> <li class="list-group-item d-flex justify-content-between align-items-center">
   Generation  4
    <span class="badge badge-primary badge-pill">4%</span>
  </li> <li class="list-group-item d-flex justify-content-between align-items-center">
   Generation  5
    <span class="badge badge-primary badge-pill">3%</span>
  </li> <li class="list-group-item d-flex justify-content-between align-items-center">
   Generation  6
    <span class="badge badge-primary badge-pill">2%</span>
  </li> <li class="list-group-item d-flex justify-content-between align-items-center">
   Generation  7
    <span class="badge badge-primary badge-pill">1%</span>
  </li> 
</ul>

</div>
</div>
                
                
</div>
@endsection
