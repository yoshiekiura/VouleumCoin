@php
$option = '';
$wallet = field_value_text('kyc_wallet_opt', 'wallet_opt');

$custom = field_value_text('kyc_wallet_custom');
if($custom['cw_name'] == NULL || $custom['cw_text'] == NULL){
    unset($custom);
    $custom = array();
}

is_array($custom) ? true : $custom = array();
is_array($wallet) ? true : $wallet = array();

$wallets = array();
foreach ($wallet as $wal)
    $wallets[$wal] = $wal;

(count($custom)==2)?$wallets[$custom['cw_name']] = $custom['cw_text']:'';


$wallet_count = count($wallet);

if($wallet_count>0){
    foreach($wallets as $wallet_opt => $value){
        $option .= '<option value="'.strtolower($value).'">'.ucfirst($value).'</option>';
    }
}

@endphp

<div class="form-step form-step1">
    <div class="form-step-head card-innr">
        <div class="step-head">
            <div class="step-number">01</div>
            <div class="step-head-text">
                <h4>{{__('Personal Details')}}</h4>
                <p>{{__('Your simple personal information required for identification')}}</p>
            </div>
        </div>
    </div>{{-- .step-head --}}
    <div class="form-step-fields card-innr">
        <div class="note note-plane note-light-alt note-md pdb-1x">
            <em class="fas fa-info-circle"></em>
            <p>{{__('Please type carefully and fill out the form with your personal details. You are not allow to edit the details once you submitted the application.')}}</p>
        </div>
        <div class="row">
            @if(field_value('kyc_firstname', 'show'))
            <div class="col-md-6">
                <div class="input-item input-with-label">
                    <label for="first-name" class="input-item-label">{{__('First Name')}}  {!! required_mark('kyc_firstname') !!}</label>
                    <div class="input-wrap">
                        <input {{ field_value('kyc_firstname', 'req' ) == '1' ? 'required ' : '' }}class="input-bordered" type="text" value = "{{ isset($user_kyc) ? $user_kyc->firstName : ''}}" id="first-name" name="first_name">
                    </div>
                </div>{{-- .input-item --}}
            </div>{{-- .col --}}
            @endif
            @if(field_value('kyc_lastname', 'show' ))
            <div class="col-md-6">
                <div class="input-item input-with-label">
                    <label for="last-name" class="input-item-label">{{__('Last Name')}} {!! required_mark('kyc_lastname') !!}</label>
                    <div class="input-wrap">
                        <input {{ field_value('kyc_lastname', 'req' ) == '1' ? 'required ' : '' }}class="input-bordered" value = "{{ isset($user_kyc) ? $user_kyc->lastName : ''}}" type="text" id="last-name" name="last_name">
                    </div>
                </div>{{-- .input-item --}}
            </div>{{-- .col --}}
            @endif
            @if(field_value('kyc_email', 'show' ) && isset($input_email) && $input_email == true)
            <div class="col-md-6">
                <div class="input-item input-with-label">
                    <label for="email" class="input-item-label">{{__('Email Address')}} {!! required_mark('kyc_email') !!}</label>
                    <div class="input-wrap">
                        <input {{ field_value('kyc_email', 'req' ) == '1' ? 'required ' : '' }}class="input-bordered" value = "{{ isset($user_kyc) ? $user_kyc->email : ''}}" type="email" id="email" name="email">
                    </div>
                </div>{{-- .input-item --}}
            </div>{{-- .col --}}
            @endif

            @if(!isset($user_kyc))
            <div class="col-md-6">
                <div class="input-item input-with-label">
                    <label for="password" class="input-item-label">{{__('Password')}} 
                        <span class="text-require text-danger">*</span>
                    </label>
                    <div class="input-wrap">
                        <input required class="input-bordered" placeholder="*******" type="password" minlength="6" id="password" name="password">
                    </div>
                </div>{{-- .input-item --}}
            </div>{{-- .col --}}
            @endif

            @if(field_value('kyc_phone', 'show' ))
            <div class="col-md-6">
                <div class="input-item input-with-label">
                    <label for="phone-number" class="input-item-label">{{__('Phone Number ')}}{!! required_mark('kyc_phone') !!}</label>
                    <div class="input-wrap">
                        <input {{ field_value('kyc_phone', 'req' ) == '1' ? 'required ' : '' }}class="input-bordered" type="text" value = "{{ isset($user_kyc) ? $user_kyc->phone : ''}}" id="phone-number" name="phone">
                    </div>
                </div>{{-- .input-item --}}
            </div>{{-- .col --}}
            @endif
            @if(field_value('kyc_dob', 'show' ))
            <div class="col-md-6">
                <div class="input-item input-with-label">
                    <label for="date-of-birth" class="input-item-label">{{__('Date of Birth')}} {!! required_mark('kyc_dob') !!}</label>
                    <div class="input-wrap">
                        <input {{ field_value('kyc_dob', 'req' ) == '1' ? 'required ' : '' }}class="input-bordered date-picker-dob" type="text" value = "{{ isset($user_kyc) ? $user_kyc->dob : ''}}" id="date-of-birth" name="dob">
                    </div>
                </div>{{-- .input-item --}}
            </div>{{-- .col --}}
            @endif
            @if(field_value('kyc_gender', 'show' ))
            <div class="col-md-6">
                <div class="input-item input-with-label">
                    <label for="gender" class="input-item-label">{{__('Gender')}} {!! required_mark('kyc_gender') !!}</label>
                    <div class="input-wrap">
                        <select {{ field_value('kyc_gender', 'req' ) == '1' ? 'required ' : '' }}class="select-bordered select-block" name="gender" id="gender">
                            <option value="">{{__('Select Gender')}}</option>
                            <option {{( isset($user_kyc) ? $user_kyc->gender : '' == 'male')?"selected":"" }} value="male">{{__('Male')}}</option>
                            <option {{( isset($user_kyc) ? $user_kyc->gender : '' == 'female')?"selected":"" }} value="female">{{__('Female')}}</option>
                            <option {{( isset($user_kyc) ? $user_kyc->gender : '' == 'other')?"selected":"" }} value="other">{{__('Other')}}</option>
                        </select>
                    </div>
                </div>{{-- .input-item --}}
            </div>{{-- .col --}}
            @endif
            @if(field_value('kyc_telegram', 'show' ))
            <div class="col-md-6">
                <div class="input-item input-with-label">
                    <label for="telegram" class="input-item-label">{{__('Telegram Username')}}  {!! required_mark('kyc_telegram') !!}</label>
                    <div class="input-wrap">
                        <input {{ field_value('kyc_telegram', 'req' ) == '1' ? 'required ' : '' }}class="input-bordered" type="text" value = "{{ isset($user_kyc) ? $user_kyc->telegram : ''}}" id="telegram" name="telegram">
                    </div>
                </div>{{-- .input-item --}}
            </div>{{-- .col --}}
            @endif
        </div>{{-- .row --}}
        <h4 class="text-secondary mgt-0-5x">{{__('Your Address')}}</h4>
        <div class="row">
            @if(field_value('kyc_country', 'show' ))
            <div class="col-md-6">
                <div class="input-item input-with-label">
                    <label for="country" class="input-item-label">{{__('Country')}} {!! required_mark('kyc_country') !!}</label>
                    <div class="input-wrap">
                        <select {{ field_value('kyc_country', 'req' ) == '1' ? 'required ' : '' }}class="select-bordered select-block" name="country" id="country">
                            <option value="">{{__('Select Country')}}</option>
                            @foreach($countries as $country)
                            @if( isset($user_kyc) ? $user_kyc->country : '' == $country)
                            <option selected="" value="{{ $country }}">{{ $country }}</option>
                            @else
                            <option value="{{ $country }}">{{ $country }}</option>
                            @endif
                            @endforeach
                        </select>
                    </div>
                </div>{{-- .input-item --}}
            </div>{{-- .col --}}
            @endif
            @if(field_value('kyc_state', 'show' ))
            <div class="col-md-6">
                <div class="input-item input-with-label">
                    <label for="state" class="input-item-label">{{__('State')}} {!! required_mark('kyc_state') !!}</label>
                    <div class="input-wrap">
                        <input {{ field_value('kyc_state', 'req' ) == '1' ? 'required ' : '' }}class="input-bordered" type="text" value = "{{ isset($user_kyc) ? $user_kyc->state : ''}}" id="state" name="state">
                    </div>
                </div>{{-- .input-item --}}
            </div>{{-- .col --}}
            @endif
            @if(field_value('kyc_city', 'show' ))
            <div class="col-md-6">
                <div class="input-item input-with-label">
                    <label for="city" class="input-item-label">{{__('City')}} {!! required_mark('kyc_city') !!}</label>
                    <div class="input-wrap">
                        <input {{ field_value('kyc_city', 'req' ) == '1' ? 'required ' : '' }}class="input-bordered" type="text" value = "{{ isset($user_kyc) ? $user_kyc->city : ''}}" id="city" name="city">
                    </div>
                </div>{{-- .input-item --}}
            </div>{{-- .col --}}
            @endif
            @if(field_value('kyc_zip', 'show' ))
            <div class="col-md-6">
                <div class="input-item input-with-label">
                    <label for="zip" class="input-item-label">{{__('Zip / Postal Code')}} {!! required_mark('kyc_zip') !!}</label>
                    <div class="input-wrap">
                        <input {{ field_value('kyc_zip', 'req' ) == '1' ? 'required ' : '' }}class="input-bordered" type="text" value = "{{ isset($user_kyc) ? $user_kyc->zip : ''}}" id="zip" name="zip">
                    </div>
                </div>{{-- .input-item --}}
            </div>{{-- .col --}}
            @endif
            @if(field_value('kyc_address1', 'show' ))
            <div class="col-md-6">
                <div class="input-item input-with-label">
                    <label for="address_1" class="input-item-label">{{__('Address Line 1')}} {!! required_mark('kyc_address1') !!}</label>
                    <div class="input-wrap">
                        <input {{ field_value('kyc_address1', 'req' ) == '1' ? 'required ' : '' }}class="input-bordered" type="text" value = "{{ isset($user_kyc) ? $user_kyc->address1 : ''}}" id="address_1" name="address_1">
                    </div>
                </div>{{-- .input-item --}}
            </div>{{-- .col --}}
            @endif
            @if(field_value('kyc_address2', 'show' ))
            <div class="col-md-6">
                <div class="input-item input-with-label">
                    <label for="address_2" class="input-item-label">{{__('Address Line 2')}} {!! required_mark('kyc_address2') !!}</label>
                    <div class="input-wrap">
                        <input {{ field_value('kyc_address2', 'req' ) == '1' ? 'required ' : '' }}class="input-bordered" type="text"  value = "{{ isset($user_kyc) ? $user_kyc->address2 : ''}}" id="address_2" name="address_2">
                    </div>
                </div>{{-- .input-item --}}
            </div>{{-- .col --}}
            @endif
        </div>{{-- .row --}}
    </div>{{-- .step-fields --}}
</div>
@if(field_value('kyc_document_passport') || field_value('kyc_document_nidcard') || field_value('kyc_document_driving'))
<div class="form-step form-step2">
    @php
    $tabs = array('passport' => field_value('kyc_document_passport'), 'nidcard' => field_value('kyc_document_nidcard'), 'driving' => field_value('kyc_document_driving'));
    $i = $e = 0;
    $defaultDoc = '';
    if ($tabs['passport']) {
        $defaultDoc = 'passport';
    }elseif ($tabs['nidcard']) {
        $defaultDoc = 'nidcard';
    }elseif ($tabs['driving']) {
        $defaultDoc = 'driving';
    }
    @endphp
    <div class="form-step-head card-innr">
        <div class="step-head">
            <div class="step-number">02</div>
            <div class="step-head-text">
                <h4>{{__('Document Upload')}}</h4>
                <p>{{__('To verify your identity, please upload any of your document')}}</p>
            </div>
        </div>
    </div>{{-- .step-head --}}
    <div class="form-step-fields card-innr">
        <div class="note note-plane note-light-alt note-md pdb-0-5x">
            <em class="fas fa-info-circle"></em>
            <p>{{__('In order to complete, please upload any of the following personal document.')}}</p>
        </div>
        <div class="gaps-2x"></div>
        @if (!empty($tabs))
        <ul class="nav nav-tabs nav-tabs-bordered row flex-wrap guttar-20px" role="tablist">
            @foreach ($tabs as $tab => $opt)
            @if ($opt)
            @php
            $i++;
            $active = '';
            if ($i==1) {$active =' active'; }
            @endphp
            <li class="nav-item flex-grow-0">
                <a class="nav-link d-flex align-items-center{{ $active }}" data-toggle="tab" href="#{{ $tab }}">
                    @if ($tab=='passport' && ($opt))
                    <div class="nav-tabs-icon"><img src="{{ asset('assets/images/icon-passport.png') }}" alt=""><img src="{{ asset('assets/images/icon-passport-color.png') }}" alt=""></div><span>{{__('Passport')}}</span>
                    @endif
                    @if ($tab=='nidcard' && ($opt))
                    <div class="nav-tabs-icon"><img src="{{ asset('assets/images/icon-national-id.png') }}" alt=""><img src="{{ asset('assets/images/icon-national-id-color.png') }}" alt=""></div><span>{{__('National Card')}}</span>

                    @endif
                    @if ($tab=='driving' && ($opt))
                    <div class="nav-tabs-icon"><img src="{{ asset('assets/images/icon-license.png') }}" alt=""><img src="{{ asset('assets/images/icon-license-color.png') }}" alt=""></div><span>{{__('Driver’s License')}}</span>
                    @endif
                </a>
            </li>
            @endif
            @endforeach
        </ul>{{-- .nav-tabs-line --}}
        <input type="hidden" name="documentType" value="{{$defaultDoc}}" />
        <div class="tab-content" id="kyc-identity-upload">
            @foreach ($tabs as $tab => $opt)
            @if ($opt)
            @php
            $e++; $active = '';
            if ($e==1) {$active =' active'; }
            @endphp
            <div class="tab-pane fade show{{ $active }}" id="{{ $tab }}">
                @if ($tab=='passport' && ($opt))
                <h5 class="text-secondary font-bold">{{__('To avoid delays when verifying account, Please make sure bellow:')}}</h5>
                <ul class="list-check">
                    <li>{{__('Chosen credential must not be expired.')}}</li>
                    <li>{{__('Document should be good condition and clearly visible.')}}</li>
                    <li>{{__('Make sure that there is no light glare on the card.')}}</li>
                </ul>
                <div class="gaps-2x"></div>
                <h6 class="font-mid">{{__('Upload Here Your Passport Copy')}}</h6>
                <div class="row align-items-center">
                    <div class="col-sm-8">
                        <div class="upload-box">
                            <div class="upload-zone passport_upload">
                                <div class="dz-message" data-dz-message>
                                    <span class="dz-message-text">{{__('Drag and drop file')}}</span>
                                    <span class="dz-message-or">{{__('or')}}</span>
                                    <button type="button" class="btn btn-primary">{{__('Select')}}</button>
                                </div>
                            </div>
                            <input type="hidden" name="passport_image" />
                        </div>
                    </div>
                    <div class="col-sm-4 d-none d-sm-block">
                        <div class="mx-md-4">
                            <img src="{{ asset('assets/images/vector-passport.png') }}" alt="">
                        </div>
                    </div>
                </div>

                <div class="gaps-3x"></div>
                <h6 class="font-mid">{{__('Upload a photo holding passport by your hand')}}</h6>
                <div class="row align-items-center">
                    <div class="col-sm-8">
                        <div class="upload-box">
                            <div class="upload-zone passport_upload_hand">
                                <div class="dz-message" data-dz-message>
                                    <span class="dz-message-text">{{__('Drag and drop file')}}</span>
                                    <span class="dz-message-or">{{__('or')}}</span>
                                    <button type="button" class="btn btn-primary">{{__('Select')}}</button>
                                </div>
                            </div>
                            <input type="hidden" name="passport_image_hand" />
                        </div>
                    </div>
                    <div class="col-sm-4 d-none d-sm-block">
                        <div class="mx-md-4">
                            <img src="{{ asset('assets/images/vector-man-wih-passport.png') }}" alt="">
                        </div>
                    </div>
                </div>
                <div class="gaps-1x"></div>
                @endif

                @if ($tab=='nidcard' && ($opt))
                <h5 class="text-secondary font-bold">{{__('To avoid delays when verifying account, Please make sure bellow:')}}</h5>
                <ul class="list-check">
                    <li>{{__('Chosen credential must not be expired.')}}</li>
                    <li>{{__('Document should be good condition and clearly visible.')}}</li>
                    <li>{{__('Make sure that there is no light glare on the card.')}}</li>
                </ul>
                <div class="gaps-2x"></div>
                <h6 class="font-mid">{{__('Upload Here Your National id Front Side')}}</h6>
                <div class="row align-items-center">
                    <div class="col-sm-8">
                        <div class="upload-box">
                            <div class="upload-zone id_front">
                                <div class="dz-message" data-dz-message>
                                    <span class="dz-message-text">{{__('Drag and drop file')}}</span>
                                    <span class="dz-message-or">{{__('or')}}</span>
                                    <button type="button" class="btn btn-primary">{{__('Select')}}</button>
                                </div>
                            </div>
                            <input type="hidden" name="id_front" />
                        </div>
                    </div>
                    <div class="col-sm-4 d-none d-sm-block">
                        <div class="mx-md-4">
                            <img src="{{  asset('assets/images/vector-id-front.png') }}" alt="vector">
                        </div>
                    </div>
                </div>
                <div class="gaps-3x"></div>
                <h6 class="font-mid">{{__('Upload Here Your National id Back Side')}}</h6>
                <div class="row align-items-center">
                    <div class="col-sm-8">
                        <div class="upload-box">
                            <div class="upload-zone id_back">
                                <div class="dz-message" data-dz-message>
                                    <span class="dz-message-text">{{__('Drag and drop file')}}</span>
                                    <span class="dz-message-or">{{__('or')}}</span>
                                    <button type="button" class="btn btn-primary">{{__('Select')}}</button>
                                </div>
                            </div>
                            <input type="hidden" name="id_back" />
                        </div>
                    </div>
                    <div class="col-sm-4 d-none d-sm-block">
                        <div class="mx-md-4">
                            <img src="{{  asset('assets/images/vector-id-back.png') }}" alt="vector">
                        </div>
                    </div>
                </div>

                <div class="gaps-3x"></div>
                <h6 class="font-mid">{{__('Upload a photo holding National id card front side by your hand')}}</h6>
                <div class="row align-items-center">
                    <div class="col-sm-8">
                        <div class="upload-box">
                            <div class="upload-zone nid_hand">
                                <div class="dz-message" data-dz-message>
                                    <span class="dz-message-text">{{__('Drag and drop file')}}</span>
                                    <span class="dz-message-or">{{__('or')}}</span>
                                    <button type="button" class="btn btn-primary">{{__('Select')}}</button>
                                </div>
                            </div>
                            <input type="hidden" name="nid_hand" />
                        </div>
                    </div>
                    <div class="col-sm-4 d-none d-sm-block">
                        <div class="mx-md-4">
                            <img src="{{  asset('assets/images/vector-man-with-id.png') }}" alt="vector">
                        </div>
                    </div>
                </div>
                <div class="gaps-1x"></div>
                @endif

                @if ($tab=='driving' && ($opt))
                <h5 class="text-secondary font-bold">{{__('To avoid delays when verifying account, Please make sure bellow:')}}</h5>
                <ul class="list-check">
                    <li>{{__('Chosen credential must not be expired.')}}</li>
                    <li>{{__('Document should be good condition and clearly visible.')}}</li>
                    <li>{{__('Make sure that there is no light glare on the card.')}}</li>
                </ul>
                <div class="gaps-2x"></div>
                <h6 class="font-mid">{{__('Upload Here Your Driving License Copy')}}</h6>

                <div class="row align-items-center">
                    <div class="col-sm-8">
                        <div class="upload-box">
                            <div class="upload-zone license">
                                <div class="dz-message" data-dz-message>
                                    <span class="dz-message-text">{{__('Drag and drop file')}}</span>
                                    <span class="dz-message-or">{{__('or')}}</span>
                                    <button type="button" class="btn btn-primary">{{__('Select')}}</button>
                                </div>
                            </div>
                            <input type="hidden" name="license" />
                        </div>
                    </div>
                    <div class="col-sm-4 d-none d-sm-block">
                        <div class="mx-md-4">
                            <img src="{{  asset('assets/images/vector-id-front.png') }}" alt="vector">
                        </div>
                    </div>
                </div>

                <div class="gaps-2x"></div>
                <h6 class="font-mid">{{__('Upload a photo holding Driving License by your hand')}}</h6>

                <div class="row align-items-center">
                    <div class="col-sm-8">
                        <div class="upload-box">
                            <div class="upload-zone license_hand">
                                <div class="dz-message" data-dz-message>
                                    <span class="dz-message-text">{{__('Drag and drop file')}}</span>
                                    <span class="dz-message-or">{{__('or')}}</span>
                                    <button type="button" class="btn btn-primary">{{__('Select')}}</button>
                                </div>
                            </div>
                            <input type="hidden" name="license_hand" />
                        </div>
                    </div>
                    <div class="col-sm-4 d-none d-sm-block">
                        <div class="mx-md-4">
                            <img src="{{  asset('assets/images/vector-man-with-id.png') }}" alt="vector">
                        </div>
                    </div>
                </div>
                <div class="gaps-1x"></div>
                @endif
            </div>
            @endif
            @endforeach
        </div>{{-- .step-fields --}}
        @endif
    </div>
</div>
@endif

@if(field_value('kyc_wallet', 'show' ) && ($wallet_count >=1))
<div class="form-step form-step3">
    <div class="form-step-head card-innr">
        <div class="step-head">
            <div class="step-number">03</div>
            <div class="step-head-text">
                <h4>{{__('Your Paying Wallet')}}</h4>
                <p>{{__('Submit your wallet address that you are going to send funds')}}</p>
            </div>
        </div>
    </div>{{-- .step-head --}}
    <div class="form-step-fields card-innr">
        <div class="note note-plane note-light-alt note-md pdb-1x">
            <em class="fas fa-info-circle"></em>
            <p>{{__('DO NOT USE your exchange wallet address such as Kraken, Bitfinex, Bithumb, Binance etc.')}}</p>
        </div>


        @if($wallet_count >= 2)
        <div class="row">
            <div class="col-md-6">
                <div class="input-item input-with-label">
                    <label for="swalllet" class="input-item-label">{{__('Select Wallet')}} {!! required_mark('kyc_wallet') !!}</label>
                    <div class="input-wrap">
                        <select {{ field_value('kyc_wallet', 'req' ) == '1' ? 'required ' : '' }}class="select-bordered select-bordered select-block" name="wallet_name" id="swalllet">
                            {!! $option !!}
                        </select>
                    </div>
                </div>
            </div>
        </div>{{-- .row --}}
        @else
        <input type="hidden" name="wallet_name" value="{{array_keys($wallets)[0]}}">
        @endif
        <div class="input-item input-with-label">
            <label for="token-address" class="input-item-label">{{__('Enter your')}}
                @if($wallet_count ==1)
                {{array_values($wallets)[0]}}
                @endif
                {{__('wallet address')}}{!! required_mark('kyc_wallet') !!}
            </label>
            <div class="input-wrap">
                <input {{ field_value('kyc_wallet', 'req' ) == '1' ? 'required ' : '' }}class="input-bordered" type="text" id="token-address" name="wallet_address" placeholder="{{__('Your personal wallet address')}}">
            </div>
            <span class="input-note">{{__('Note:')}} {{ get_setting('kyc_wallet_note') }}</span>
        </div>{{-- .input-item --}}
    </div>{{-- .step-fields --}}
</div>
@endif
<div class="form-step form-step-final">
    <div class="form-step-fields card-innr">
        @if(get_page('privacy', 'status') == 'active' || get_page('terms', 'status') == 'active')
        <div class="input-item">
            <input class="input-checkbox input-checkbox-md" id="term-condition" name="condition" type="checkbox" required="required" data-msg-required="You should read our terms and policy.">
            <label for="term-condition">{{__('I have read the')}} {!! get_page_link('terms', ['target'=>'_blank']) !!} {{ (get_page_link('terms') && get_page_link('policy') ? 'and' : '') }} {!! get_page_link('policy', ['target'=>'_blank']) !!}.</label>
        </div>
        @endif
        <div class="input-item">
            <input class="input-checkbox input-checkbox-md" id="info-currect" name="currect" type="checkbox" required="required" data-msg-required="Confirm that all information is correct.">
            <label for="info-currect">{{__('All the personal information I have entered is correct.')}}</label>
        </div>
        <div class="input-item">
            <input class="input-checkbox input-checkbox-md" id="certification" name="certification" type="checkbox" required="required" data-msg-required="Certify that you are individual.">
            <label for="certification">{{__('I certify that, I am participating in the token distribution event in the capacity of an individual (and beneficial owner) and not as an agent or representative of a third party corporate entity.')}}</label>
        </div>
        @if(field_value('kyc_wallet', 'show' ) && ($wallet_count >=1 || $custom_count ==2))
        <div class="input-item">
            <input class="input-checkbox input-checkbox-md" id="tokenKnow" name="tokenKnow" type="checkbox" required="required" data-msg-required="Confirm that you understand.">
            <label for="tokenKnow">{{__('I understand that, I can only in the token distribution event with the wallet address that was entered in the application form.')}}</label>
        </div>
        @endif
        <div class="gaps-1x"></div>
        <button class="btn btn-primary" type="submit">{{__('Process for Verify')}}</button>
    </div>{{-- .step-fields --}}
</div>
<div class="hiddenFiles"></div>
