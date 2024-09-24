@extends('front.layout.master') @section('main_content')

<div class="blank-div"></div>

<!--register section start-->
<div class="login-wrapper signup-wrapper">
    <div class="container">
        <div class="row">
            <div class="col-sm-6 col-md-6 col-lg-6 respo-padd-none">
                <div class="login-banner-block">
                    <h1>Create Your Enterprise Admin Account</h1>
                    <h5>Already have a Account - <a href="{{url('/login')}}">Login Now <i class="fa fa-long-arrow-right"></i></a></h5>
                </div>
            </div>
            <div class="col-sm-6 col-md-6 col-lg-6 login-bg">
                <div class="login-section login2 change">
                    <h1>Sign Up</h1>
                    <h5>It's free and always will be.</h5>

                    <div class="login-tabs">

                        @include('front.layout._operation_status')

                        <div class="login-form-block">
                            <form name="frm-login" id="frm_login" method="post" autocomplete="off" action="{{url('/process_enterprise_admin_register')}}" data-parsley-validate>

                            <div id="div_part_one">
                            
                                {{ csrf_field() }}

                                <div class="form-group login">
                                    <input type="text" class="input-box" placeholder="Enterprise Name" name="enterprise_name" id="enterprise_name" data-parsley-required-message="Please enter enterprise name" data-parsley-required="true" data-parsley-errors-container="#err_enterprise_name" />
                                    <span class="bar"></span>
                                    <div class="error" id="err_enterprise_name"></div>
                                </div>

                                <div class="form-group login">
                                    <input type="text" class="input-box" placeholder="Email Address" name="email" id="email" data-parsley-required-message="Please enter email address" data-parsley-required="true" data-parsley-errors-container="#err_email"/>
                                    <span class="bar"></span>
                                    <div class="error" id="err_email"></div>
                                </div>

                                <div class="form-group login">
                                    <input type="text" class="input-box" placeholder="Full Address" id="address" name="address" data-parsley-required-message="Please enter full address" data-parsley-required="true" data-parsley-errors-container="#err_address" />
                                    <span class="bar"></span>
                                    <div class="error" id="err_address"></div>
                                </div>

                                <div class="row">
                                    <div class="col-xs-4 col-sm-4 col-md-4 col-lg-3">
                                        <div class="form-group login">
                                            <input type="text" class="input-box" placeholder="Code" id="country_code" name="txt_country_code" data-parsley-required-message="Please enter code" data-parsley-required="true" data-parsley-errors-container="#err_country_code" onkeypress="return isNumberKey(event);" value="{{ isset($arr_prev_data['country_code']) ? $arr_prev_data['country_code'] : '' }}" />
                                            <span class="bar"></span>
                                            <div id="err_country_code" class="error"></div>
                                        </div>
                                    </div>
                                    <div class="col-xs-8 col-md-8 col-lg-9">
                                        <div class="form-group login">
                                            <input type="text" class="input-box" placeholder="Mobile Number" id="mobile_no" name="mobile_no" data-parsley-required-message="Please enter mobile number" data-parsley-required="true" data-parsley-errors-container="#err_mobile_no" value="{{ isset($arr_prev_data['mobile_no']) ? $arr_prev_data['mobile_no'] : '' }}"/>
                                            <span class="bar"></span>
                                            <div id="err_mobile_no" class="error"></div>
                                        </div>
                                    </div>
                                </div>
                                
                                
                                <div class="form-group login">
                                    <input type="text" class="input-box" placeholder="Enter Zip Code" id="postal_code" name="post_code" data-parsley-required-message="Please enter zip code" data-parsley-required="true" data-parsley-errors-container="#err_post_code" />
                                    <span class="bar"></span>
                                    <div class="error" id="err_post_code"></div>
                                    <input name="country_name" id="country" type="hidden" />
                                    <input name="state_name" id="administrative_area_level_1" type="hidden" />
                                    <input name="city_name" id="locality" type="hidden" />
                                    <input name="lat" id="lat" type="hidden" />
                                    <input name="long" id="lng" type="hidden" />
                                </div>

                            </div>

                            <div id="back_btn_div" style="display: none;" class="sign-radio-back-top"><a onclick="showPrevForm();" href="javascript:void(0)"><i class="fa fa-long-arrow-left"></i> Back</a></div>
                                <div class="clearfix"></div>

                            <div id="div_part_two" style="display:none">
                            
                                <div class="form-group login">
                                    <div class="eye-icon-fro">
                                        <span class="eye-icon-clo" onclick="showPassword(this,1)"><i class="fa fa-eye"></i></span>
                                    </div>
                                    <input type="password" class="input-box" placeholder="Enter Password" name="password" id="new_password" data-parsley-required-message="Please enter password" data-parsley-required="false" data-parsley-errors-container="#err_password" data-parsley-minlength="8" data-parsley-pattern="((?=.*\d)(?=.*[a-z A-Z])(?=.*[!@#$%]).{8,})"/>
                                    <span class="bar"></span>
                                    <div class="error" id="err_password"></div>
                                </div>
                              
                                <div id="password_message_div" class="form-inner-info-colo-wrapp">
                                    <div class="form-inner-colo-head">Your password must have:</div>
                                    <div id="minimum_characters" class="form-inner-colo-sub"><i class="fa fa-circle"></i> Minimum 8 characters</div>
                                    <div id="uppercase_characters" class="form-inner-colo-sub"><i class="fa fa-circle"></i> Uppercase characters (A-Z)</div>
                                    <div id="lowercase_characters" class="form-inner-colo-sub"><i class="fa fa-circle"></i> Lowercase characters (a-z)</div>
                                    <div id="number_characters" class="form-inner-colo-sub "><i class="fa fa-circle"></i> Numbers (0-9)</div>
                                    <div id="special_characters" class="form-inner-colo-sub"><i class="fa fa-circle"></i> Special characters (~*!@$#%_+.?:,)</div>
                                </div>

                                <div class="form-group login">
                                    <div class="eye-icon-fro">
                                        <span class="eye-icon-clo" onclick="showPassword(this,2)"><i class="fa fa-eye"></i></span>
                                    </div>
                                    <input type="password" class="input-box" placeholder="Enter Confirm Password" name="confirm_password" id="confirm_password" data-parsley-required="false" data-parsley-errors-container="#err_confirm_password" data-parsley-required-message="Please confirm enter password" data-parsley-minlength="6" data-parsley-equalto="#new_password" />
                                    <span class="bar"></span>
                                    <div class="error" id="err_confirm_password"></div>
                                </div>
                              
                                <input type="hidden" name="user_type" value="ENTERPRISE_ADMIN">
                                <div class="check-box">
                                    <p>
                                        <input class="filled-in" id="terms_condition" type="checkbox" onclick="showTerms(this);" data-parsley-required-message="Please agree terms and conditions" data-parsley-required="false" data-parsley-errors-container="#err_terms_condition">
                                        <label for="terms_condition">I agree to the terms of use</label>
                                    </p>
                                    <div class="clearfix"></div>
                                    <div class="error" id="err_terms_condition"></div>
                                </div>

                            </div>

                            <div class="clearfix"></div>
                            <div id="btn_next_page" class="login-btn"> <button type="button">Next</button></div>
                            <div id="btn_register_now" style="display:none" class="login-btn"> <button type="submit">Register Now</button></div>

                                <div class="or-block">
                                    <span>&nbsp;</span>
                                    <p>Or</p>
                                </div>
                            </form>
                            <div class="clearfix"></div>
                            <h5 class="dont-have-account-txt">Already have a account<a href="{{url('/login')}}"> Login Now</a></h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- terms conditions popup start here -->
<div class="terms-conditions-popup-wrapper">
    <div id="terms-conditions-popup" class="modal fade" role="dialog" data-backdrop="static">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    {{-- <button type="button" class="close" data-dismiss="modal">&times;</button> --}}
                    <div class="terms-title top">Terms &amp; Conditions</div>
                </div>
                <div class="modal-body">
                    <div class="terms-condi-inner-height">
                        
                            <div class="terms-conditions">
                                {!! isset($terms_conditions->page_desc) ? $terms_conditions->page_desc : '' !!}
                            </div>
                       
                    </div>

                    <div class="terms-popup-btn-wrapper"> 
                        <div class="join-popup">
                            <a href="javascript:void(0)" onclick="AcceptTerms()" class="schedul-btn">Accept</a>
                        </div>
                        
                        <div class="join-popup">
                            <a href="javascript:void(0)" onclick="RejectTerms()" class="schedul-btn">Reject</a>
                        </div>
                        <div class="clearfix"></div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<!--register section end-->
<script src="https://maps.googleapis.com/maps/api/js?v=3&key={{ isset($google_map_api_key) ? $google_map_api_key : 'AIzaSyCccvQtzVx4aAt05YnfzJDSWEzPiVnNVsY' }}&libraries=places&callback=initAutocomplete" async defer>
</script>

<script type="text/javascript" language="javascript" src="{{url('/')}}/js/front/cleave.min.js"></script>
<script type="text/javascript" language="javascript" src="{{url('/')}}/js/front/cleave-phone.i18n.js"></script>

<script type="text/javascript">

function isNumberKey(evt) {
    var charCode = (evt.which) ? evt.which : evt.keyCode;

    if (charCode == 190 || charCode == 46  || charCode == 43) 
      return true;

    if (charCode > 31 && (charCode < 48 || charCode > 57 )) 
    return false;

    return true;
}

function showPassword(ref,type) {
    if(type == 1){
        if($('#new_password').attr('type') == 'password'){
            $('#new_password').attr('type','text');
            $(ref).addClass('span-enable');
        }
        else if($('#new_password').attr('type') == 'text'){
            $('#new_password').attr('type','password');
            $(ref).removeClass('span-enable');
        }
    }
    if(type == 2){
        if($('#confirm_password').attr('type') == 'password'){
            $('#confirm_password').attr('type','text');
            $(ref).addClass('span-enable');
        }
        else if($('#confirm_password').attr('type') == 'text'){
            $('#confirm_password').attr('type','password');
            $(ref).removeClass('span-enable');
        }
    }
}
var tmp_new_password     = '';
var tmp_confirm_password = '';

$('#new_password').keyup(function(){

    var new_password = $('#new_password').val();

    if(new_password!='')
    {
        var uppercase_characters = /[A-Z]/g;
        if(new_password.match(uppercase_characters)) {  
            $('#uppercase_characters').removeClass('red');
            $('#uppercase_characters').addClass('green');
        } else {
            $('#uppercase_characters').removeClass('green');
            $('#uppercase_characters').addClass('red');
        }
        
        var lowercase_characters = /[a-z]/g;
        if(new_password.match(lowercase_characters)) {  
            $('#lowercase_characters').removeClass('red');
            $('#lowercase_characters').addClass('green');
        } else {
            $('#lowercase_characters').removeClass('green');
            $('#lowercase_characters').addClass('red');
        }

        // var minimum_characters = /[a-z]/g;
        if(new_password.length >= 8) {  
            $('#minimum_characters').removeClass('red');
            $('#minimum_characters').addClass('green');
        } else {
            $('#minimum_characters').removeClass('green');
            $('#minimum_characters').addClass('red');
        }

        var number_characters = /[0-9]/g;
        if(new_password.match(number_characters)) {  
            $('#number_characters').removeClass('red');
            $('#number_characters').addClass('green');
        } else {
            $('#number_characters').removeClass('green');
            $('#number_characters').addClass('red');
        }

        var special_characters = /[!@#$%^&*()]/g;

        if(new_password.match(special_characters)) {  
            $('#special_characters').removeClass('red');
            $('#special_characters').addClass('green');
        } else {
            $('#special_characters').removeClass('green');
            $('#special_characters').addClass('red');
        }
    }
    
});



// btn_next_page
// btn_register_now
$('#btn_next_page').click(function(){
        
    if($('#new_password').val()!='')
    {
        tmp_new_password = $('#new_password').val();
        $('#new_password').val('');
    }
    if($('#confirm_password').val()!='')
    {
        tmp_confirm_password = $('#confirm_password').val();
        $('#confirm_password').val('')
    }

    if($('#frm_login').parsley().validate()){
        
        $('#new_password').attr('data-parsley-required', 'true');
        $('#confirm_password').attr('data-parsley-required', 'true');
        $('#terms_condition').attr('data-parsley-required', 'true');
        
        // $('#new_password').attr('data-parsley-pattern','((?=.*\d)(?=.*[a-z A-Z])(?=.*[!@#$%]).{8,})');
        // $('#frm_login').parsley();

        if(tmp_new_password!='')
        {
            $('#new_password').val(tmp_new_password);
        }
        if(tmp_confirm_password!='')
        {
            $('#confirm_password').val(tmp_confirm_password);
        }

        $('#div_part_one').slideUp();
        $('#div_part_two').slideDown();

        $('#back_btn_div').show();
        $('#btn_next_page').hide();
        $('#btn_register_now').show();
    }

});

    function showPrevForm() {
        
        $('#new_password').attr('data-parsley-required', 'false');
        $('#confirm_password').attr('data-parsley-required', 'false');
        $('#terms_condition').attr('data-parsley-required', 'false');

        $('#div_part_one').slideDown();
        $('#div_part_two').slideUp();
        
        $('#back_btn_div').hide();
        $('#btn_next_page').show();
        $('#btn_register_now').hide();
    }

    function showTerms(ref) {
        if($(ref).prop('checked')){
            $('#terms-conditions-popup').modal('toggle');
        }
    }

    function AcceptTerms() 
    {
        $('#terms-conditions-popup').modal('toggle');
    }
    function RejectTerms() 
    {
        $('#terms_condition').prop('checked',false);
        $('#terms_condition').attr('data-parsley-required', 'false');
        $('#terms-conditions-popup').modal('toggle');
    }
    
    var country_code = '{{ isset($country_code) && $country_code!='' ? $country_code : 'US ' }}';

    var cleavePhone = new Cleave('#mobile_no', {
        phone: true,
        phoneRegionCode: 'US'
    });
</script>

<script type="text/javascript">

    var BASE_URL = "{{url('/')}}";

    var COUNTRY_CODES_JSON_FILE = BASE_URL + '/assets/country_codes.json';

    var glob_autocomplete;
    var glob_component_form = {
        street_number: 'short_name',
        route: 'long_name',
        locality: 'long_name',
        administrative_area_level_1: 'long_name',
        country: 'long_name',
        postal_code: 'short_name',
    };

    var glob_options = {};
    glob_options.types = ['address'];

    function initAutocomplete(country_code) {
        glob_autocomplete = false;
        glob_autocomplete = initGoogleAutoComponent($('#address')[0], glob_options, glob_autocomplete);
    }


    function initGoogleAutoComponent(elem, options, autocomplete_ref) {
        autocomplete_ref = new google.maps.places.Autocomplete(elem, options);
        autocomplete_ref = createPlaceChangeListener(autocomplete_ref, fillInAddress);

        return autocomplete_ref;
    }


    function createPlaceChangeListener(autocomplete_ref, fillInAddress) {
        autocomplete_ref.addListener('place_changed', fillInAddress);
        return autocomplete_ref;
    }

    function destroyPlaceChangeListener(autocomplete_ref) {
        google.maps.event.clearInstanceListeners(autocomplete_ref);
    }

    function fillInAddress() {
        $('#country_code').val('');

        var place = glob_autocomplete.getPlace();

        $('#lat').val(place.geometry.location.lat());
        $('#lng').val(place.geometry.location.lng());

        for (var component in glob_component_form) {
            $("#" + component).val("");
            $("#" + component).attr('disabled', false);
        }

        if (place.address_components.length > 0) {
            $.each(place.address_components, function(index, elem) {

                var addressType = elem.types[0];

                if (addressType != undefined) {

                    if (addressType == 'country') {
                        set_country_code_value(elem.short_name);
                    }
                    if (glob_component_form[addressType] != undefined) {
                        var val = elem[glob_component_form[addressType]];
                        $("#" + addressType).val(val);
                    }
                }
            });
        }
    }

    function set_country_code_value(country_code) {
        $('#country_code').val('');
        $.getJSON(COUNTRY_CODES_JSON_FILE, function(data) {
            if (data != undefined) {
                $.each(data, function(index, value) {
                    if ((value.code != undefined) && (value.code === country_code)) {
                        if (value.dial_code != undefined) {
                            $('#country_code').val(value.dial_code);
                        }
                    }
                });
            }
        });
    }
</script>
@stop