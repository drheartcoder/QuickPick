<?php $user_path     = config('app.project.role_slug.enterprise_admin_role_slug'); ?>
 @extends('front.layout.master')                

    @section('main_content')

    <div class="blank-div"></div>

    <div class="email-block">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6 col-md-6 col-lg-6">
                    <div class="headding-text-bredcr">
                        Add Enterprise User
                    </div>
                </div>
                <div class="col-sm-6 col-md-6 col-lg-6">
                    <div class="bredcrum-right">
                        <a href="{{ $module_url_path }}" class="bredcrum-home"> Dashboard </a>
                        <span class="arrow-righ"><i class="fa fa-angle-right"></i></span> Add Enterprise User
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="clearfix"></div>

    <div class="latest-courses-main small-heigh my-enroll ener">
        <div class="container-fluid">
            <div class="clearfix"></div>
            <div class="row">
                <div class="col-sm-12 col-md-12 col-lg-12">
                    <div class="row">
                        @include('front.enterprise_admin.left_bar')
                        <div class="col-sm-9 col-md-10 col-lg-10">
                        @include('front.layout._operation_status')
                            <div class="edit-posted-bg-main">
                                
                                <form name="frm_add_user" id="frm_add_user" method="post" action="{{url( config('app.project.role_slug.enterprise_admin_role_slug'))}}/store_enterprise_user" data-parsley-validate>
                                    {{ csrf_field() }}
                                    
                                    <div class="row">

                                        <div id="div_part_one">

                                            <div class="col-sm-6 col-md-6 col-lg-6">
                                                <div class="form-group marg-top">
                                                    <input type="text" name="first_name" data-parsley-required-message="Please enter first name" data-parsley-required="true" data-parsley-errors-container="#err_first_name">
                                                    <label>First Name</label>
                                                    <div class="error" id="err_first_name"></div>
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-md-6 col-lg-6">
                                                <div class="form-group">
                                                    <input type="text" name="last_name" data-parsley-required-message="Please enter last name" data-parsley-required="true" data-parsley-errors-container="#err_last_name">
                                                    <label>Last Name</label>
                                                    <div class="error" id="err_last_name"></div>
                                                </div>
                                            </div>
                                            <div class="col-sm-12 col-md-12 col-lg-12">
                                                <div class="form-group">
                                                    <input type="email" name="email" data-parsley-required-message="Please enter email address" data-parsley-required="true" data-parsley-errors-container="#err_email">
                                                    <label>Email</label>
                                                    <div class="error" id="err_email"></div>
                                                </div>
                                            </div>

                                            <div class="col-sm-12 col-md-12 col-lg-12">
                                                <div class="form-group">
                                                    <input type="text" id="address" name="address" placeholder="" data-parsley-required-message="Please enter address" data-parsley-required="true" data-parsley-errors-container="#err_address" autocomplete="off">
                                                    <label>Full Address</label>
                                                    <div class="error" id="err_address"></div>
                                                </div>
                                            </div>

                                            <div class="col-sm-3 col-md-3 col-lg-3">
                                                <div class="form-group marg-top">
                                                    <input type="text" name="country_code" id="country_code" data-parsley-required-message="Please enter country code" data-parsley-required="true" data-parsley-errors-container="#err_country_code" onkeypress="return isNumberKey(event);">
                                                    <label>Country Code</label>
                                                    <div class="error" id="err_country_code"></div>
                                                </div>
                                            </div>
                                            <div class="col-sm-9 col-md-9 col-lg-9">
                                                <div class="form-group">
                                                    <input type="text" name="mobile_no" id="mobile_no" data-parsley-required-message="Please enter mobile number" data-parsley-required="true" data-parsley-errors-container="#err_mobile_no">
                                                    <label>Mobile Number</label>
                                                    <div class="error" id="err_mobile_no"></div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-sm-6 col-md-6 col-lg-6">
                                                <div class="form-group">
                                                    <input type="text" name="post_code" id="postal_code" data-parsley-required-message="Please enter zip code" data-parsley-required="true" data-parsley-errors-container="#err_postal_code">
                                                    <label>Zip Code</label>
                                                    <div class="error" id="err_postal_code"></div>

                                                    <input name="country_name" id="country" type="hidden" />
                                                    <input name="state_name" id="administrative_area_level_1" type="hidden" />
                                                    <input name="city_name" id="locality" type="hidden" />
                                                    <input name="lat" id="lat" type="hidden" />
                                                    <input name="long" id="lng" type="hidden" />

                                                </div>
                                            </div>

                                            <div class="col-sm-6 col-md-6 col-lg-6">
                                                <div class="form-group date">
                                                    <div class="form-date-icon">
                                                        <a href="javascript:void(0)"><i class="fa fa-calendar"></i></a> 
                                                    </div>
                                                    <input id="datepicker" type="text" data-parsley-required-message="Please enter date of birth" name="dob" placeholder="Date of birth" data-parsley-required="true" data-parsley-errors-container="#err_dob">
                                                    {{-- <label>Date of Birth</label> --}}
                                                    <div class="error" id="err_dob"></div>
                                                </div>
                                            </div>
                                            
                                        </div>

                                        <div id="div_part_two" style="display:none">

                                            <div class="col-sm-12 col-md-12 col-lg-12">
                                                <div class="form-group">
                                                    <div class="eye-icon-fro right-space">
                                                        <span class="eye-icon-clo" onclick="showPassword(this,0)"><i class="fa fa-eye"></i></span>
                                                    </div>
                                                    <input type="password" name="new_password" id="new_password" data-parsley-required-message="Please enter new password" data-parsley-required="false" data-parsley-errors-container="#err_new_password" data-parsley-minlength-message="Please enter 8 characters" data-parsley-minlength="8" data-parsley-pattern="((?=.*\d)(?=.*[a-z A-Z])(?=.*[!@#$%]).{8,})">
                                                    <label>New Password</label>
                                                    <div class="error" id="err_new_password"></div>
                                                </div>
                                                <div id="password_message_div" class="form-inner-info-colo-wrapp">
                                                    <div class="form-inner-colo-head">Your password must have:</div>
                                                    <div id="minimum_characters" class="form-inner-colo-sub"><i class="fa fa-circle"></i> Minimum 8 characters</div>
                                                    <div id="uppercase_characters" class="form-inner-colo-sub"><i class="fa fa-circle"></i> Uppercase characters (A-Z)</div>
                                                    <div id="lowercase_characters" class="form-inner-colo-sub"><i class="fa fa-circle"></i> Lowercase characters (a-z)</div>
                                                    <div id="number_characters" class="form-inner-colo-sub "><i class="fa fa-circle"></i> Numbers (0-9)</div>
                                                    <div id="special_characters" class="form-inner-colo-sub"><i class="fa fa-circle"></i> Special characters (~*!@$#%_+.?:,)</div>
                                                </div>
                                            </div>

                                            <div class="col-sm-12 col-md-12 col-lg-12">

                                                <div class="form-group">
                                                    <div class="eye-icon-fro right-space">
                                                        <span class="eye-icon-clo" onclick="showPassword(this,1)"><i class="fa fa-eye"></i></span>
                                                    </div>
                                                    <input type="password" name="confirm_password" id="confirm_password" data-parsley-required-message="Please enter confirm password" data-parsley-required="false" data-parsley-errors-container="#err_confirm_password"  data-parsley-equalto="#new_password" data-parsley-minlength="8">
                                                    <label>Confirm Password</label>
                                                    <div class="error" id="err_confirm_password"></div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="cancle-btn-block">

                                            <div id="btn_next_page" class="write-review-btn green margin-botto no-mar"> 
                                                <button type="button">Next</button>
                                            </div>

                                            <div id="btn_register_now" style="display:none" class="write-review-btn green margin-botto no-mar"> 
                                                <button type="button" onclick="validFormDetails(this)">Register Now</button>
                                            </div>

                                            {{-- <div class="write-review-btn green margin-botto no-mar">
                                                <button type="submit">Submit</button>
                                            </div> --}}

                                            <div id="back_btn_div" style="display: none;" class="sign-radio-back-top">
                                                <a onclick="showPrevForm();" href="javascript:void(0)"><i class="fa fa-long-arrow-left"></i> Back</a>
                                            </div>

                                        </div>
                                    </div>
                                </form>
                            </div>



                        </div>


                    </div>
                </div>
            </div>



        </div>
    </div>
<script src="https://maps.googleapis.com/maps/api/js?v=3&key={{ isset($google_map_api_key) ? $google_map_api_key : 'AIzaSyASynNXpP9v040cNSh2f_A8XVnPkQ5mUEY' }}&libraries=places&callback=initAutocomplete" async defer></script>
<script type="text/javascript" language="javascript" src="{{url('/')}}/js/front/cleave.min.js"></script>
<script type="text/javascript" language="javascript" src="{{url('/')}}/js/front/cleave-phone.i18n.js"></script>

<script type="text/javascript">
    
    var country_code = '{{ isset($country_code) && $country_code!='' ? $country_code : 'US ' }}';

    var cleavePhone = new Cleave('#mobile_no', {
        phone: true,
        phoneRegionCode: 'US'
    });

    $(function() {
        $("#datepicker, #datepicker1").datepicker({
            changeMonth: true,
            changeYear: true,
            yearRange: '1950:n',
            autoclose: true,
            maxDate: "-18Y",
        });
    });

    function isNumberKey(evt) {
        var charCode = (evt.which) ? evt.which : evt.keyCode;

        if (charCode == 190 || charCode == 46  || charCode == 43) 
          return true;

        if (charCode > 31 && (charCode < 48 || charCode > 57 )) 
        return false;

        return true;
    }

    function validFormDetails(ref)
    {
        if($('#frm_add_user').parsley().validate())
        {
            $(ref).prop('disabled', true);
            $(ref).html("<span><i class='fa fa-spinner fa-spin'></i> Processing...</span>");
            $('#frm_add_user').submit();
        }
        return false;
    }

    function showPassword(ref,type) {
        if(type == 0){
            if($('#new_password').attr('type') == 'password'){
                $('#new_password').attr('type','text');
                $(ref).addClass('span-enable');
            }
            else if($('#new_password').attr('type') == 'text'){
                $('#new_password').attr('type','password');
                $(ref).removeClass('span-enable');
            }
        }
        if(type == 1){
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

    if($('#frm_add_user').parsley().validate()){
        
            $('#new_password').attr('data-parsley-required', 'true');
            $('#confirm_password').attr('data-parsley-required', 'true');
            $('#terms_condition').attr('data-parsley-required', 'true');
        
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

    // setTimeout(function () {
        
    // },1000);

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

                        if(addressType == 'postal_code' && val!=''){
                            $('#postal_code').parent().addClass('active');
                        }
                        

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
                            $('#country_code').parent().addClass('active');
                        }
                    }
                });
            }
        });
    }
</script>

@stop