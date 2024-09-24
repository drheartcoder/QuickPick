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
                        
                        <form name="frm-login" method="post" action="{{url('/update_admin_driver_previous_vehicle_details')}}" data-parsley-validate enctype="multipart/form-data">
                                {{ csrf_field() }}
                                <div id="div_part_two" >
                                    <div class="row">
                                        <div class="col-sm-12 col-md-6 col-lg-6">
                                            <div class="form-group sign-up">
                                                <select disabled="">
                                                <option value="">Select Vehicle Type</option>
                                                @if(isset($arr_vehicle_type) && sizeof($arr_vehicle_type)>0)
                                                    @foreach($arr_vehicle_type as $key => $vehicle_type)
                                                        <option 
                                                        			value="{{ isset($vehicle_type['id']) ? $vehicle_type['id'] : 0 }}" 
                                                        			@if(isset($arr_vehicle_details['vehicle_type_id']) && $arr_vehicle_details['vehicle_type_id'] == $vehicle_type['id'])
                                                        				selected="" 
                                                        			@endif
                                                        			is_usdot_required="{{ isset($vehicle_type['is_usdot_required']) ? $vehicle_type['is_usdot_required'] : '' }}">{{ isset($vehicle_type['vehicle_type']) ? $vehicle_type['vehicle_type'] : '' }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                                <div class="dwon-arrow-icon"><i class="fa fa-angle-down"></i></div>
                                                <span class="bar"></span>
                                                <div class="error" id="err_vehicle_type"></div>
                                            </div>
                                        </div>

                                        <div class="col-sm-12 col-md-6 col-lg-6">
                                            <div class="form-group sign-up">
                                                <select disabled="">
                                                <option value="">Select Vehicle Brand</option>
                                                @if(isset($arr_vehicle_brand) && sizeof($arr_vehicle_brand)>0)
                                                    @foreach($arr_vehicle_brand as $key => $vehicle_brand)
                                                        <option 
                                                        			value="{{ isset($vehicle_brand['id']) ? $vehicle_brand['id'] : 0 }}" 
                                                        			@if(isset($arr_vehicle_details['vehicle_brand_id']) && $arr_vehicle_details['vehicle_brand_id'] == $vehicle_brand['id'])
                                                        				selected="" 
                                                        			@endif
                                                        			>{{ isset($vehicle_brand['name']) ? $vehicle_brand['name'] : '' }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                                <div class="dwon-arrow-icon"><i class="fa fa-angle-down"></i></div>
                                                <span class="bar"></span>
                                                <div class="error" id="err_vehicle_brand"></div>
                                            </div>
                                        </div>

                                        <div class="col-sm-12 col-md-6 col-lg-6">
                                            <div class="form-group sign-up">
                                                <select disabled="">
                                                <option value="">Select Vehicle Model</option>
                                                @if(isset($arr_vehicle_model) && sizeof($arr_vehicle_model)>0)
                                                    @foreach($arr_vehicle_model as $key => $vehicle_model)
                                                        <option 
                                                        			value="{{ isset($vehicle_model['id']) ? $vehicle_model['id'] : 0 }}" 
                                                        			
                                                        			@if(isset($arr_vehicle_details['vehicle_model_id']) && $arr_vehicle_details['vehicle_model_id'] == $vehicle_model['id'])
                                                        				selected="" 
                                                        			@endif

                                                        			>{{ isset($vehicle_model['name']) ? $vehicle_model['name'] : '' }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                                <div class="dwon-arrow-icon"><i class="fa fa-angle-down"></i></div>
                                                <span class="bar"></span>
                                                <div class="error" id="err_vehicle_model"></div>
                                            </div>
                                        </div>

                                        <div class="col-sm-12 col-md-6 col-lg-6">
                                            <div class="form-group login">
                                                <input type="text" class="input-box" value="{{ isset($arr_vehicle_details['vehicle_number']) ? $arr_vehicle_details['vehicle_number'] : '' }}" disabled="" />
                                                <span class="bar"></span>
                                                <div class="error" id="err_vehicle_number"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group sign-upload">
                                        {{-- @if(isset($arr_vehicle_details['is_driving_license_verified']) && $arr_vehicle_details['is_driving_license_verified']=='PENDING') 
                                            <div class="pending-block posi"><i class="fa fa-exclamation"></i></div>
                                        @elseif(isset($arr_vehicle_details['is_driving_license_verified']) && $arr_vehicle_details['is_driving_license_verified']=='APPROVED') 
                                            <div class="pending-block posi green"><i class="fa fa-check"></i></div>
                                        @elseif(isset($arr_vehicle_details['is_driving_license_verified']) && $arr_vehicle_details['is_driving_license_verified']=='REJECTED') 
                                            <div class="pending-block posi red"><i class="fa fa-times"></i></div>
                                        @endif --}}
                                        
                                        @if(isset($arr_vehicle_details['is_driving_license_verified']) && $arr_vehicle_details['is_driving_license_verified']=='PENDING') 
                                            <div class="pending-block posi"><i class="fa fa-exclamation"></i>&nbsp;Under Review</div>
                                        @elseif(isset($arr_vehicle_details['is_driving_license_verified']) && $arr_vehicle_details['is_driving_license_verified']=='APPROVED') 
                                            <div class="pending-block posi green"><i class="fa fa-check"></i>&nbsp;Approved</div>
                                        @elseif(isset($arr_vehicle_details['is_driving_license_verified']) && $arr_vehicle_details['is_driving_license_verified']=='REJECTED') 
                                            <div class="pending-block posi red"><i class="fa fa-times"></i>&nbsp;Rejected</div>
                                        @endif

	                                    <div class="upload-block">
	                                        <input type="file" id="driving_license" style="visibility:hidden; height: 0;" name="driving_license" 
                                                @if(isset($arr_vehicle_details['driving_license']) && $arr_vehicle_details['driving_license']!='') 
                                                    data-parsley-required="false" 
                                                @else  
                                                    data-parsley-required="true" 
                                                @endif 
                                                data-parsley-required-message="Please upload driver’s license" data-parsley-errors-container="#err_driving_license">
	                                        <div class="form-group">
	                                            <input type="text" class="file-caption  kv-fileinput-caption" placeholder="Upload Your Driver’s License (pdf/img)" readonly id="sub_driving_license" />
	                                            <div id="err_first_name" class="error"></div>
	                                            <div class="btn btn-primary btn-file"><a class="file" onclick="selectCurrentFile('driving_license')"> <i class="fa fa-paperclip"></i></a></div>
	                                        </div>
	                                    </div>
	                                    <div class="error" id="err_driving_license"></div>
	                                </div>

                                </div>
                                <input type="hidden" name="vehicle_id" value="{{ isset($arr_vehicle_details['id']) ? base64_encode($arr_vehicle_details['id']) : 0 }}">
                                <input type="hidden" name="driver_id" value="{{ isset($driver_id) ? base64_encode($driver_id) : 0 }}">
                                <input type="hidden" name="is_usdot_doc_required" id="is_usdot_doc_required" value="NO">

                                <div class="clearfix"></div>
                                
                                <div class="white-line-register"></div>
                                <div class="contact-bold-butto">
                                    <button type="submit" class="red-btn">Update Vehicle Details</button>
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

<script src="{{url('js/front/jquery-ui.js')}}" type="text/javascript"></script>

<script>
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
    
    function check_is_usdot_required(ref){

        var is_usdot_required = $('option:selected', ref).attr('is_usdot_required');
        if(is_usdot_required!=undefined && is_usdot_required == '1')
        {
            $('#usdot_doc').attr('data-parsley-required', 'true');
            $('#is_usdot_doc_required').val('YES');
            $('#usdot_doc_div').show();
        }
        else
        {
            $('#usdot_doc').attr('data-parsley-required', 'true');
            $('#is_usdot_doc_required').val('NO');
            $('#usdot_doc_div').hide();
        }

    }

</script>


<script type="text/javascript">
    $(document).ready(function() {
        var brand = document.getElementById('vehicle_image');
        brand.className = 'attachment_upload';
        brand.onchange = function() {
            document.getElementById('fakeUploadLogo').value = this.value.substring(12);
        };

        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function(e) {
                    $('.img-preview').attr('src', e.target.result);

                };

                reader.readAsDataURL(input.files[0]);
            }
        }
        $("#vehicle_image").change(function() {
            readURL(this);
        });
    });
</script>

<script type="text/javascript">
    function selectCurrentFile(type) {
        $('#' + type).click();
    };

    $(document).ready(function() {

        $('#driving_license').change(function() {
            $('#sub_driving_license').val($(this).val());
        });

        $('#registration_doc').change(function() {
            $('#sub_registration_doc').val($(this).val());
        });

        $('#vehicle_insurance_doc').change(function() {
            $('#sub_vehicle_insurance_doc').val($(this).val());
        });

        $('#dmv_driving_record').change(function() {
            $('#sub_dmv_driving_record').val($(this).val());
        });

        $('#proof_of_inspection_doc').change(function() {
            $('#sub_proof_of_inspection_doc').val($(this).val());
        });

        $('#dmv_driving_record').change(function() {
            $('#sub_dmv_driving_record').val($(this).val());
        });

        $('#usdot_doc').change(function() {
            $('#sub_usdot_doc').val($(this).val());
        });
    });


    // http: //192.168.1.65/quickpick/api/common_data/get_vehicle_model?vehicle_brand_id=1
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
