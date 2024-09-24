@extends('front.layout.master') @section('main_content')

<div class="blank-div"></div>

<style>
    .active-radio .check:before {
        border-radius: 100% !important;
        content: "" !important;
        display: block !important;
        height: 7px !important;
        left: 3px !important;
        margin: auto !important;
        position: absolute !important;
        top: 3px !important;
        transition: background 0.25s linear 0s !important;
        width: 7px !important;
        background: #5dc3e9 !important;
    }

    .unactive-radio .check:before {
        background: transparent !important;
    }
    .long{position: relative}

    .error.vehicle_check li.parsley-required {margin-top: -10px;}

</style>


<div class="login-wrapper signup-wrapper join-fleet driver-registretion-page">
    <div class="container">
        <div class="row">
            <div class="col-sm-6 col-md-6 col-lg-6">
                <div class="join-fleet-right-block driver-registre">
                    <div class="join-fleet-head">Driver Qualifications</div>
                    <div class="join-fleet-sub">Sedan, SUV, Cargo Van, Pick Up Truck</div>

                    <div class="join-fleet-tick-block">
                        <div class="join-fleet-tick-img"><img src="{{ url('/')}}/images/join-fleet-tick.png" alt="join-fleet-tick" /> </div>
                        <div class="join-fleet-tick-text">Experience driving vehicles listed above</div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="join-fleet-tick-block">
                        <div class="join-fleet-tick-img"><img src="{{ url('/')}}/images/join-fleet-tick.png" alt="join-fleet-tick" /> </div>
                        <div class="join-fleet-tick-text">Experience driving in the Washington, D.C. Metro area</div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="join-fleet-tick-block">
                        <div class="join-fleet-tick-img"><img src="{{ url('/')}}/images/join-fleet-tick.png" alt="join-fleet-tick" /> </div>
                        <div class="join-fleet-tick-text">Availability to make on-demand deliveries</div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="join-fleet-tick-block">
                        <div class="join-fleet-tick-img"><img src="{{ url('/')}}/images/join-fleet-tick.png" alt="join-fleet-tick" /> </div>
                        <div class="join-fleet-tick-text">Friendly and courteous</div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="join-fleet-tick-block">
                        <div class="join-fleet-tick-img"><img src="{{ url('/')}}/images/join-fleet-tick.png" alt="join-fleet-tick" /> </div>
                        <div class="join-fleet-tick-text">Valid vehicle registration and inspection (if using your vehicle)</div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="join-fleet-tick-block">
                        <div class="join-fleet-tick-img"><img src="{{ url('/')}}/images/join-fleet-tick.png" alt="join-fleet-tick" /> </div>
                        <div class="join-fleet-tick-text">Valid vehicle insurance (if using your vehicle)</div>
                        <div class="clearfix"></div>
                    </div>

                    <div class="join-fleet-sub join-truck-trailer">10′ – 26′ Truck, Trailer</div>
                    <div class="join-fleet-sub-italic two">All qualifications above AND below are required</div>

                    <div class="join-fleet-tick-block">
                        <div class="join-fleet-tick-img"><img src="{{ url('/')}}/images/join-fleet-tick.png" alt="join-fleet-tick" /> </div>
                        <div class="join-fleet-tick-text">Commercial Driver’s License (CDL)</div>
                        <div class="clearfix"></div>
                    </div>

                    <div class="join-fleet-tick-block">
                        <div class="join-fleet-tick-img"><img src="{{ url('/')}}/images/join-fleet-tick.png" alt="join-fleet-tick" /> </div>
                        <div class="join-fleet-tick-text">Department of Transportation (DOT) Number</div>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-6 col-lg-6 remove-padding-section">
                <div class="delivered-from-wrapper driver-registre">
                   <div class="delivered-left-right-driver-registre">
                    <div class="delivered-top-img-left our-flee"><img src="{{url('/images/index-delivered-top-img.png')}}" alt="index-delivered-top-img" /> </div>
                    <div class="delivered-top-img-right">
                        <div class="delivery-title">Interested in</div>
                        <div class="delivery-title-sub-blue">Becoming a Driver</div>
                        <div class="delivery-title-sub-blue-quetion">?</div>
                    </div>
                    </div>
                    <div class="clearfix"></div>
                    {{-- <div class="delivery-title-sub-phara">Simply fill out the form below and we’ll get back to you as soon as possible.</div> --}}
                </div>

                <div class="login-section login2 change">
                    <!--<h1>Interested in becoming a driver?</h1>
                    <h5>Simply fill out the form below and we’ll get back to you as soon as possible.</h5>-->

                    <div class="login-tabs">

                        @include('front.layout._operation_status')

                        <div class="login-form-block">

                            <form name="frm-login" id="frm_login" method="post" autocomplete="off" action="{{url('/process_register')}}" data-parsley-validate enctype="multipart/form-data">

                                {{ csrf_field() }}


                                <div id="div_part_one">

                                    <div class="row">
                                        <div class="col-sm-12 col-md-6 col-lg-6">
                                            <div class="form-group">
                                                <input name="first_name" id="first_name" type="text" placeholder="First Name" data-parsley-required-message="Please enter mobile number" data-parsley-required="true" data-parsley-errors-container="#err_first_name" value="{{ isset($arr_prev_data['first_name']) ? $arr_prev_data['first_name'] : '' }}"/>
                                                <div id="err_first_name" class="error"></div>
                                            </div>


                                        </div>

                                        <div class="col-sm-12 col-md-6 col-lg-6">
                                            <div class="form-group">
                                                <input type="text" name="last_name" placeholder="Last Name" data-parsley-required-message="Please enter last name" data-parsley-required="true" data-parsley-errors-container="#err_last_name" value="{{ isset($arr_prev_data['last_name']) ? $arr_prev_data['last_name'] : '' }}" />
                                                <div class="error" id="err_last_name"></div>
                                            </div>
   
                                        </div>

                                        <div class="col-sm-12 col-md-12 col-lg-12">
                                            <div class="form-group">
                                                <input type="text" name="email" id="email" placeholder="Email" data-parsley-required-message="Please enter email address" data-parsley-required="true" data-parsley-errors-container="#err_email" value="{{ isset($arr_prev_data['email']) ? $arr_prev_data['email'] : '' }}"/>
                                                <div class="error" id="err_email"></div>
                                            </div>
  
                                        </div>


                                        <div class="col-sm-12 col-md-12 col-lg-12">
                                            <div class="form-group">
                                                <input type="text" id="address" placeholder="Full Address" name="address" data-parsley-required-message="Please enter full address" data-parsley-required="true" data-parsley-errors-container="#err_address" placeholder="" />
                                                <div class="error" id="err_address"></div>
                                            </div>
  
                                        </div>
                                        <div class="col-sm-12 col-md-12 col-lg-12">
                                            <div class="row">
                                                <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
                                                    <div class="form-group">
                                                        <input type="text" placeholder="Country Code" id="country_code" name="txt_country_code" data-parsley-required-message="Please enter code" data-parsley-required="true" data-parsley-errors-container="#err_country_code" onkeypress="return isNumberKey(event);" value="{{ isset($arr_prev_data['country_code']) ? $arr_prev_data['country_code'] : '' }}"/>
                                                        <div id="err_country_code" class="error"></div>
                                                    </div>
 
                                                </div>

                                                <div class="col-xs-12 col-sm-12 col-md-8 col-lg-8">
                                                    <div class="form-group">
                                                        <input type="text" id="mobile_no" placeholder="Mobile Number" name="mobile_no" data-parsley-required-message="Please enter mobile number" data-parsley-required="true" data-parsley-errors-container="#err_mobile_no" value="{{ isset($arr_prev_data['mobile_no']) ? $arr_prev_data['mobile_no'] : '' }}"/>
                                                        <div id="err_mobile_no" class="error"></div>
                                                    </div>
    
                                                </div>
                                            </div>
                                        </div>

                                      
                                    </div>


                                    <div class="form-group date active">
                                        <div class="form-date-icon"><a href="javascript:void(0)"><i class="fa fa-calendar"></i></a> </div>
                                        <input type="text" id="datepicker" type="text" placeholder="Date of birth" name="dob" data-parsley-required-message="Please enter date of birth" data-parsley-required="true" data-parsley-errors-container="#err_datepicker"/>
                                        <div id="err_datepicker" class="error"></div>
                                    </div>

                                  <div class="form-group">
                                        <input type="text" id="postal_code" placeholder="Enter Zip Code" name="post_code" data-parsley-required-message="Please enter zip code" data-parsley-required="true" data-parsley-errors-container="#err_post_code" />
                                        <div class="error" id="err_post_code"></div>
                                    </div>

                                    <input name="country_name" id="country" type="hidden" />
                                    <input name="state_name" id="administrative_area_level_1" type="hidden" />
                                    <input name="city_name" id="locality" type="hidden" />
                                    <input name="lat" id="lat" type="hidden" />
                                    <input name="long" id="lng" type="hidden" />
                                    

                                </div>


                                <div id="back_btn_div" style="display: none;" class="sign-radio-back-top"><a onclick="showPrevForm();" href="javascript:void(0)"><i class="fa fa-long-arrow-left"></i> Back</a></div>
                                <div class="clearfix"></div>

                                <div id="div_part_two" style="display:none">
                                    <div class="row">

                                        <div class="col-sm-12 col-md-12 col-lg-12">
                                            <div class="form-group">
                                                <div class="eye-icon-fro right-space">
                                                    <span class="eye-icon-clo" onclick="showPassword(this,1)"><i class="fa fa-eye"></i></span>
                                                </div>
                                                <input type="password" name="password" id="new_password" placeholder="Enter Password" data-parsley-required-message="Please enter password" data-parsley-required="false" data-parsley-errors-container="#err_password"  data-parsley-minlength="8" data-parsley-pattern="((?=.*\d)(?=.*[a-z A-Z])(?=.*[!@#$%]).{8,})"/>
                                                <div class="error long" id="err_password"></div>
                                            </div>

                                            <div id="password_message_div" class="form-inner-info-colo-wrapp white">
                                                <div class="form-inner-colo-head">Your password must have:</div>
                                                <div id="minimum_characters" class="form-inner-colo-sub"><i class="fa fa-circle"></i> Minimum 8 characters</div>
                                                <div id="uppercase_characters" class="form-inner-colo-sub"><i class="fa fa-circle"></i> Uppercase characters (A-Z)</div>
                                                <div id="lowercase_characters" class="form-inner-colo-sub"><i class="fa fa-circle"></i> Lowercase characters (a-z)</div>
                                                <div id="number_characters" class="form-inner-colo-sub "><i class="fa fa-circle"></i> Numbers (0-9)</div>
                                                <div id="special_characters" class="form-inner-colo-sub"><i class="fa fa-circle"></i> Special characters (~*!@$#%_+.?:,)</div>
                                            </div>

                                            <!-- <div class="form-inner-info-colo-wrapp white">
                                                <div class="form-inner-colo-head">Your password must have:</div>
                                                <div class="form-inner-colo-sub"><i class="fa fa-circle"></i> Between 9-16 characters</div>
                                                <div class="form-inner-colo-sub green"><i class="fa fa-circle"></i> Uppercase characters (A-Z)</div>
                                                <div class="form-inner-colo-sub"><i class="fa fa-circle"></i> Lowercase characters (a-z)</div>
                                                <div class="form-inner-colo-sub red"><i class="fa fa-circle"></i> Numbers (0-9)</div>
                                                <div class="form-inner-colo-sub"><i class="fa fa-circle"></i> Special characters (~*!@$#%_+.?:,)</div>
                                            </div> -->
                                        
                                        </div>

                                        <div class="col-sm-12 col-md-12 col-lg-12">
                                            <div class="form-group">
                                                <div class="eye-icon-fro right-space">
                                                    <span class="eye-icon-clo" onclick="showPassword(this,2)"><i class="fa fa-eye"></i></span>
                                                </div>
                                                <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm Password" data-parsley-required="false" data-parsley-errors-container="#err_confirm_password" data-parsley-required-message="Please confirm enter password" data-parsley-minlength="6" data-parsley-equalto="#new_password" />
                                                <div class="error" id="err_confirm_password"></div>
                                            </div>
                                            <!-- <div class="form-inner-info-colo-wrapp white">
                                                    <div class="form-inner-colo-head">Your password must have:</div>
                                                    <div class="form-inner-colo-sub"><i class="fa fa-circle"></i> Between 9-16 characters</div>
                                                    <div class="form-inner-colo-sub green"><i class="fa fa-circle"></i> Uppercase characters (A-Z)</div>
                                                    <div class="form-inner-colo-sub"><i class="fa fa-circle"></i> Lowercase characters (a-z)</div>
                                                    <div class="form-inner-colo-sub red"><i class="fa fa-circle"></i> Numbers (0-9)</div>
                                                    <div class="form-inner-colo-sub"><i class="fa fa-circle"></i> Special characters (~*!@$#%_+.?:,)</div>
                                            </div> -->
                                        </div>
                                    </div>

                                    <div class="sign-radio-heding">Do you have a vehicle?</div>
                                    <div class="sign-up-radio">
                                        <div class="default-cate-radio-section">
                                            <div class="radio-btns">
                                                <div class="radio-btn yes-radio-section">
                                                    <input type="radio" id="f-option" value="YES" name="is_driver_vehicle" data-parsley-required-message="Please select vehicle option" data-parsley-required="false" data-parsley-errors-container="#err_do_you_have_vehicle">
                                                    <label for="f-option">Yes</label>
                                                    <div class="check"></div>
                                                </div>
                                                <div class="radio-btn no-radio-section">
                                                    <input type="radio" id="s-option" value="NO" name="is_driver_vehicle" data-parsley-required-message="Please select vehicle option" data-parsley-required="false" data-parsley-errors-container="#err_do_you_have_vehicle">
                                                    <label for="s-option">No</label>
                                                    <div class="check">
                                                        <div class="inside"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="clearfix"></div>
                                            <div class="error vehicle_check" id="err_do_you_have_vehicle"></div>
                                        </div>
                                    </div>
                                    
                                    <div class="clearfix"></div>

                                    <input type="hidden" name="user_type" value="DRIVER">
                                    <div class="check-box driver-registretion">
                                        <p>
                                            <input class="filled-in" id="terms_condition" onclick="showTerms(this);" type="checkbox" data-parsley-required-message="Please agree terms and conditions" data-parsley-required="false" data-parsley-errors-container="#err_terms_condition">
                                            <label for="terms_condition">I agree to the terms of use</label>
                                        </p>
                                        <div class="clearfix"></div>
                                        <div class="error" id="err_terms_condition"></div>
                                    </div>

                                </div>


                                
                                <div class="clearfix"></div>
                                
                                <div class="white-line-register"></div>
                                <div class="contact-bold-butto">
                                    <div id="btn_register_now" style="display:none" class="login-btn"> <button type="submit">Register Now</button></div>
                                    <div id="btn_next_page" class="login-btn"> <button type="button">Next</button></div>
                                </div>
                            </form>
                            
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

<!-- terms conditions popup start here -->

<script src="{{url('js/front/jquery-ui.js')}}" type="text/javascript"></script>

<script>

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

        if($('#frm_login').parsley().validate()){
            
            $('#new_password').attr('data-parsley-required', 'true');
            $('#confirm_password').attr('data-parsley-required', 'true');
            $('#terms_condition').attr('data-parsley-required', 'true');
            
            $('input[type=radio][name=is_driver_vehicle]').attr('data-parsley-required', 'true');
            
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
        $('input[type=radio][name=is_driver_vehicle]').attr('data-parsley-required', 'false');
        $('#div_part_one').slideDown();
        $('#div_part_two').slideUp();
        
        $('#back_btn_div').hide();
        $('#btn_next_page').show();
        $('#btn_register_now').hide();

        // $('#new_password').val('');
        // $('#confirm_password').val('');
        
        // $('#uppercase_characters').removeClass('red');
        // $('#uppercase_characters').removeClass('green');
        // $('#lowercase_characters').removeClass('red');
        // $('#lowercase_characters').removeClass('green');
        // $('#minimum_characters').removeClass('red');
        // $('#minimum_characters').removeClass('green');
        // $('#number_characters').removeClass('red');
        // $('#number_characters').removeClass('green');
        // $('#special_characters').removeClass('red');
        // $('#special_characters').removeClass('green');
        
        
    }

    $(".sign-radio-back-top").on("click", function() {
        $(".no-radio-section").addClass("active-radio");
        $(".yes-radio-section").addClass("unactive-radio");
    });
    $(".radio-btn").on("click", function() {
        $(".no-radio-section").removeClass("active-radio");
        $(".yes-radio-section").removeClass("unactive-radio");
    });
</script>

<script type="text/javascript">
    $(function() {
        $("#datepicker, #datepicker1").datepicker({
            changeMonth: true,
            changeYear: true,
            yearRange: '1950:n',
            autoclose: true,
            maxDate: "-21Y",
        });
    });

    function showTerms(ref) {
        if ($(ref).prop('checked')) {
            $('#terms-conditions-popup').modal('toggle');
        }
    }

    function AcceptTerms() 
    {
        $('#terms-conditions-popup').modal('toggle');
    }

    function check_is_usdot_required(ref){

        var is_usdot_required = $('option:selected', ref).attr('is_usdot_required');
        if(is_usdot_required!=undefined && is_usdot_required == '1')
        {
            console.log(is_usdot_required);
            $('#usdot_doc').attr('data-parsley-required', 'true');
            $('#usdot_doc_div').show();
        }
        else
        {
            $('#usdot_doc').attr('data-parsley-required', 'false');
            $('#usdot_doc_div').hide();
        }

    }
    function RejectTerms() 
    {
        $('#terms_condition').prop('checked',false);
        $('#terms_condition').attr('data-parsley-required', 'false');
        $('#terms-conditions-popup').modal('toggle');
    }

    // $('input[type=radio][name=is_driver_vehicle]').change(function() {

    //     if (this.value == 'YES') {
    //         $('#div_part_one').slideUp();
    //         $('#div_part_two').slideDown();

    //         $('#back_btn_div').show();

    //         $('#vehicle_type').attr('data-parsley-required', 'true');
    //         $('#vehicle_brand').attr('data-parsley-required', 'true');
    //         $('#vehicle_model').attr('data-parsley-required', 'true');
    //         $('#vehicle_number').attr('data-parsley-required', 'true');
    //         $('#vehicle_image').attr('data-parsley-required', 'true');
    //         $('#registration_doc').attr('data-parsley-required', 'true');
    //         $('#vehicle_insurance_doc').attr('data-parsley-required', 'true');
    //         $('#proof_of_inspection_doc').attr('data-parsley-required', 'true');
    //         $('#dmv_driving_record').attr('data-parsley-required', 'true');

    //     } else if (this.value == 'NO') {
    //         $('#div_part_one').slideDown();
    //         $('#div_part_two').slideUp();

    //         $('#back_btn_div').hide();

    //         $('#vehicle_type').attr('data-parsley-required', 'false');
    //         $('#vehicle_brand').attr('data-parsley-required', 'false');
    //         $('#vehicle_model').attr('data-parsley-required', 'false');
    //         $('#vehicle_number').attr('data-parsley-required', 'false');
    //         $('#vehicle_image').attr('data-parsley-required', 'false');
    //         $('#registration_doc').attr('data-parsley-required', 'false');
    //         $('#vehicle_insurance_doc').attr('data-parsley-required', 'false');
    //         $('#proof_of_inspection_doc').attr('data-parsley-required', 'false');
    //         $('#dmv_driving_record').attr('data-parsley-required', 'false');
    //         $('#usdot_doc').attr('data-parsley-required', 'false');

    //     }
    // });


</script>

<script src="https://maps.googleapis.com/maps/api/js?v=3&key={{ isset($google_map_api_key) ? $google_map_api_key : 'AIzaSyCccvQtzVx4aAt05YnfzJDSWEzPiVnNVsY' }}&libraries=places&callback=initAutocomplete" async defer>
</script>

<script type="text/javascript" language="javascript" src="{{url('/')}}/js/front/cleave.min.js"></script>
<script type="text/javascript" language="javascript" src="{{url('/')}}/js/front/cleave-phone.i18n.js"></script>

<script type="text/javascript">
    var country_code = '{{ isset($country_code) && $country_code!='' ? $country_code : 'US' }}';

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
                        // console.log("addressType-->",addressType,'val-->',val);
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

<script type="text/javascript">
    // $(document).ready(function() {
    //     var brand = document.getElementById('vehicle_image');
    //     brand.className = 'attachment_upload';
    //     brand.onchange = function() {
    //         document.getElementById('fakeUploadLogo').value = this.value.substring(12);
    //     };

    //     function readURL(input) {
    //         if (input.files && input.files[0]) {
    //             var reader = new FileReader();

    //             reader.onload = function(e) {
    //                 $('.img-preview').attr('src', e.target.result);

    //             };

    //             reader.readAsDataURL(input.files[0]);
    //         }
    //     }
    //     $("#vehicle_image").change(function() {
    //         readURL(this);
    //     });
    // });
</script>

<script type="text/javascript">
    // function selectCurrentFile(type) {
    //     $('#' + type).click();
    // };

    // $(document).ready(function() {

    //     $('#driving_license').change(function() {
    //         $('#sub_driving_license').val($(this).val());
    //     });

    //     $('#registration_doc').change(function() {
    //         $('#sub_registration_doc').val($(this).val());
    //     });

    //     $('#vehicle_insurance_doc').change(function() {
    //         $('#sub_vehicle_insurance_doc').val($(this).val());
    //     });

    //     $('#dmv_driving_record').change(function() {
    //         $('#sub_dmv_driving_record').val($(this).val());
    //     });

    //     $('#proof_of_inspection_doc').change(function() {
    //         $('#sub_proof_of_inspection_doc').val($(this).val());
    //     });
        
        

    //     $('#dmv_driving_record').change(function() {
    //         $('#sub_dmv_driving_record').val($(this).val());
    //     });

    //     $('#usdot_doc').change(function() {
    //         $('#sub_usdot_doc').val($(this).val());
    //     });
    // });


    //http: //192.168.1.65/quickpick/api/common_data/get_vehicle_model?vehicle_brand_id=1
    var vehicle_model_url = '{{url('/api/common_data/get_vehicle_model')}}';

    function loadVehicleModel(ref) {

        var vehicle_brand_id = $(ref).val();

        if (vehicle_brand_id && vehicle_brand_id != "" && vehicle_brand_id != 0) {

            $('select[id="vehicle_model"]').find('option').remove().end().append('<option value="">Select Vehicle Model</option>').val('');

            $.ajax({
                url: vehicle_model_url + '?vehicle_brand_id=' + vehicle_brand_id,
                type: 'GET',
                data: 'flag=true',
                dataType: 'json',
                beforeSend: function() {
                    $('select[id="vehicle_model"]').attr('readonly', 'readonly');
                },
                success: function(response) {
                    if (response.status == "success") {
                        $('select[id="vehicle_model"]').removeAttr('readonly');

                        if (typeof(response.data) == "object") {
                            var option = '<option value="">Select Vehicle Model</option>';
                            $(response.data).each(function(index, value) {
                                option += '<option value="' + value.id + '">' + value.name + '</option>';
                            });

                            $('select[id="vehicle_model"]').html(option);
                        }



                    }

                    return false;
                },
                error: function(res) {

                }
            });
        } else {
            var option = '<option value="">Select Vehicle Model</option>';
            $('select[id="vehicle_model"]').html(option);
        }
    }
</script>



@stop