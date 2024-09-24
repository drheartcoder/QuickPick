<?php $user_path     = config('app.project.role_slug.enterprise_admin_role_slug'); ?>
 @extends('front.layout.master')                

    @section('main_content')

    <div class="blank-div"></div>

    <div class="email-block">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6 col-md-6 col-lg-6">
                    <div class="headding-text-bredcr">
                        Edit Enterprise User
                    </div>
                </div>
                <div class="col-sm-6 col-md-6 col-lg-6">
                    <div class="bredcrum-right">
                        <a href="{{ $module_url_path }}" class="bredcrum-home"> Dashboard </a>
                        <span class="arrow-righ"><i class="fa fa-angle-right"></i></span> Edit Enterprise User
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
                                
                                {{-- {{dd($arr_data)}} --}}

                                <form name="frm_update_user" id="frm_update_user" method="post" action="{{url( config('app.project.role_slug.enterprise_admin_role_slug'))}}/update_enterprise_user" data-parsley-validate>
                                    {{ csrf_field() }}
                                    
                                    <div class="row">

                                        <div id="div_part_one">
                                        <input type="hidden" name="enc_id" value="{{ isset($arr_data['id']) ? base64_encode($arr_data['id']) : ''}}">
                                            <div class="col-sm-6 col-md-6 col-lg-6">
                                                <div class="form-group marg-top">
                                                    <input type="text" name="first_name" data-parsley-required-message="Please enter first name" data-parsley-required="true" data-parsley-errors-container="#err_first_name" value="{{ isset($arr_data['first_name']) ? $arr_data['first_name'] : ''}}">
                                                    <label>First Name</label>
                                                    <div class="error" id="err_first_name"></div>
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-md-6 col-lg-6">
                                                <div class="form-group">
                                                    <input type="text" name="last_name" data-parsley-required-message="Please enter last name" data-parsley-required="true" data-parsley-errors-container="#err_last_name" value="{{ isset($arr_data['last_name']) ? $arr_data['last_name'] : ''}}">
                                                    <label>Last Name</label>
                                                    <div class="error" id="err_last_name"></div>
                                                </div>
                                            </div>
                                            <div class="col-sm-12 col-md-12 col-lg-12">
                                                <div class="form-group">
                                                    <input type="email" name="email" data-parsley-required-message="Please enter email address" data-parsley-required="true" data-parsley-errors-container="#err_email" value="{{ isset($arr_data['email']) ? $arr_data['email'] : ''}}">
                                                    <label>Email</label>
                                                    <div class="error" id="err_email"></div>
                                                </div>
                                            </div>

                                            <div class="col-sm-12 col-md-12 col-lg-12">
                                                <div class="form-group">
                                                    <input type="text" id="address" name="address" placeholder="" data-parsley-required-message="Please enter address" data-parsley-required="true" data-parsley-errors-container="#err_address" autocomplete="off" value="{{ isset($arr_data['address']) ? $arr_data['address'] : ''}}">
                                                    <label>Full Address</label>
                                                    <div class="error" id="err_address"></div>
                                                </div>
                                            </div>

                                            <div class="col-sm-3 col-md-3 col-lg-3">
                                                <div class="form-group marg-top">
                                                    <input type="text" name="country_code" id="country_code" data-parsley-required-message="Please enter country code" data-parsley-required="true" data-parsley-errors-container="#err_country_code" onkeypress="return isNumberKey(event);" value="{{ isset($arr_data['country_code']) ? $arr_data['country_code'] : ''}}">
                                                    <label>Country Code</label>
                                                    <div class="error" id="err_country_code"></div>
                                                </div>
                                            </div>
                                            <div class="col-sm-9 col-md-9 col-lg-9">
                                                <div class="form-group">
                                                    <input type="text" name="mobile_no" id="mobile_no" data-parsley-required-message="Please enter mobile number" data-parsley-required="true" data-parsley-errors-container="#err_mobile_no" value="{{ isset($arr_data['mobile_no']) ? $arr_data['mobile_no'] : ''}}">
                                                    <label>Mobile Number</label>
                                                    <div class="error" id="err_mobile_no"></div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-sm-6 col-md-6 col-lg-6">
                                                <div class="form-group">
                                                    <input type="text" name="post_code" id="postal_code" data-parsley-required-message="Please enter zip code" data-parsley-required="true" data-parsley-errors-container="#err_postal_code" value="{{ isset($arr_data['post_code']) ? $arr_data['post_code'] : ''}}">
                                                    <label>Zip Code</label>
                                                    <div class="error" id="err_postal_code"></div>

                                                    <input name="country_name" id="country" type="hidden" value="{{ isset($arr_data['country_name']) ? $arr_data['country_name'] : ''}}"/>
                                                    <input name="state_name" id="administrative_area_level_1" type="hidden" value="{{ isset($arr_data['state_name']) ? $arr_data['state_name'] : ''}}" />
                                                    <input name="city_name" id="locality" type="hidden"  value="{{ isset($arr_data['city_name']) ? $arr_data['city_name'] : ''}}"/>
                                                    <input name="lat" id="lat" type="hidden"  value="{{ isset($arr_data['latitude']) ? $arr_data['latitude'] : ''}}"/>
                                                    <input name="long" id="lng" type="hidden" value="{{ isset($arr_data['longitude']) ? $arr_data['longitude'] : ''}}"/>

                                                </div>
                                            </div>

                                            <div class="col-sm-6 col-md-6 col-lg-6">
                                                <div class="form-group date">
                                                    <div class="form-date-icon">
                                                        <a href="javascript:void(0)"><i class="fa fa-calendar"></i></a> 
                                                    </div>
                                                    <input id="datepicker" type="text" data-parsley-required-message="Please enter date of birth" name="dob" placeholder="Date of birth" data-parsley-required="true" data-parsley-errors-container="#err_dob" value="{{ isset($arr_data['dob']) ? date('m/d/Y',strtotime($arr_data['dob'])) : ''}}">
                                                    <div class="error" id="err_dob"></div>
                                                </div>
                                            </div>
                                            
                                        </div>

                                        <div class="cancle-btn-block">

                                            <div id="btn_register_now" class="write-review-btn green margin-botto no-mar"> 
                                                <button type="button" onclick="validFormDetails(this)">Update</button>
                                            </div>
                                            <div  class="write-review-btn green margin-botto no-mar"> 
                                                <button type="button" onclick="location.href = '{{ url( config('app.project.role_slug.enterprise_admin_role_slug').'/manage_users') }}';">Back</button>
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
        if($('#frm_update_user').parsley().validate())
        {
            $(ref).prop('disabled', true);
            $(ref).html("<span><i class='fa fa-spinner fa-spin'></i> Processing...</span>");
            $('#frm_update_user').submit();
        }
        return false;
    }

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