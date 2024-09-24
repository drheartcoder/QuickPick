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
                        
                        {{-- {{dd($arr_vehicle_details)}} --}}

                        @if(isset($arr_vehicle_details) && sizeof($arr_vehicle_details)>0)
                            <form name="frm-login" method="post" action="{{url('/update_driver_previous_vehicle_details')}}" data-parsley-validate enctype="multipart/form-data">
                        @else
                            <form name="frm-login" method="post" action="{{url('/update_driver_vehicle_details')}}" data-parsley-validate enctype="multipart/form-data">
                        @endif
                                {{ csrf_field() }}
								
                                {{-- {{dump($arr_vehicle_type)}} --}}

                                <div id="div_part_two" >
                                    <div class="row">
                                        <div class="col-sm-12 col-md-6 col-lg-6">
                                            <div class="form-group sign-up">
                                                <select name="vehicle_type_id" id="vehicle_type" data-parsley-required-message="Please select vehicle type" data-parsley-required="true" data-parsley-errors-container="#err_vehicle_type" onchange="check_is_usdot_required(this);check_is_mcdoc_required(this)">
                                                <option value="">Select Vehicle Type</option>
                                                @if(isset($arr_vehicle_type) && sizeof($arr_vehicle_type)>0)
                                                    @foreach($arr_vehicle_type as $key => $vehicle_type)
                                                        <option 
                                                        			value="{{ isset($vehicle_type['id']) ? $vehicle_type['id'] : 0 }}" 
                                                        			@if(isset($arr_vehicle_details['vehicle_type_id']) && $arr_vehicle_details['vehicle_type_id'] == $vehicle_type['id'])
                                                        				selected="" 
                                                        			@endif
                                                                    is_usdot_required="{{ isset($vehicle_type['is_usdot_required']) ? $vehicle_type['is_usdot_required'] : '' }}"
                                                        			is_mcdoc_required="{{ isset($vehicle_type['is_mcdoc_required']) ? $vehicle_type['is_mcdoc_required'] : '' }}"

                                                                    >{{ isset($vehicle_type['vehicle_type']) ? $vehicle_type['vehicle_type'] : '' }}</option>
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
                                                <select name="vehicle_brand_id" id="vehicle_brand" data-parsley-required-message="Please select vehicle brand" data-parsley-required="true" data-parsley-errors-container="#err_vehicle_brand" onchange="loadVehicleModel(this)">
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
                                                <select name="vehicle_model_id" id="vehicle_model" data-parsley-required-message="Please select vehicle model" data-parsley-required="true" data-parsley-errors-container="#err_vehicle_model">
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
                                                <input type="text" class="input-box" placeholder="Vehicle License Plate No" id="vehicle_number" name="vehicle_number" value="{{ isset($arr_vehicle_details['vehicle_number']) ? $arr_vehicle_details['vehicle_number'] : '' }}" data-parsley-required-message="Please enter vehicle number" data-parsley-required="true" data-parsley-errors-container="#err_vehicle_number" />
                                                <span class="bar"></span>
                                                <div class="error" id="err_vehicle_number"></div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="sign-radio-heding" style="position: relative;">Vehicle Image (img)
                                    
                                        @if(isset($arr_vehicle_details['is_vehicle_image_verified']) && $arr_vehicle_details['is_vehicle_image_verified']=='PENDING') 
                                            <div class="pending-block posi" style="right: -115px; top: -3px;"><i class="fa fa-exclamation"></i>&nbsp;Under Review</div>
                                        @elseif(isset($arr_vehicle_details['is_vehicle_image_verified']) && $arr_vehicle_details['is_vehicle_image_verified']=='APPROVED') 
                                            <div class="pending-block posi green" style="right: -115px; top: -3px;"><i class="fa fa-check"></i>&nbsp;Approved</div>
                                        @elseif(isset($arr_vehicle_details['is_vehicle_image_verified']) && $arr_vehicle_details['is_vehicle_image_verified']=='REJECTED') 
                                            <div class="pending-block posi red" style="right: -115px; top: -3px;"><i class="fa fa-times"></i>&nbsp;Rejected</div>
                                        @endif

                                    </div>

                                    <div class="sign-up-uploa-img">
                                        <div class="profile-img-block my-account-img">
                                            <div class="pro-img">
                                            	@if(isset($arr_vehicle_details['vehicle_image_path']) && $arr_vehicle_details['vehicle_image_path']!='')
	                                                <img src="{{ $arr_vehicle_details['vehicle_image_path'] }}" class="img-preview" alt="" />
                                            	@else
	                                                <img src="images/signup-upload-img.jpg" class="img-preview" alt="" />
                                            	@endif
                                            </div>
                                            <div class="update-pic">
                                                {{-- <div class="hvr-rectangle-out view upload-btn-new">
                                                    <input id="vehicle_image" 
                                                            @if(isset($arr_vehicle_details['vehicle_image']) && $arr_vehicle_details['vehicle_image']!='') 
                                                                data-parsley-required="false" 
                                                            @else  
                                                                data-parsley-required="true" 
                                                            @endif 
                                                            data-parsley-required-message="Please enter vehicle image" data-parsley-errors-container="#err_vehicle_image" name="vehicle_image" type="file" class="attachment_upload" /></div> --}}

                                                  <div class="hvr-rectangle-out view upload-btn-new">
                                                    <input id="vehicle_image" name="vehicle_image" type="file" class="attachment_upload" /></div>          

                                                <div class="clearfix"></div>
                                                <div class="error sign-error" id="err_vehicle_image"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group sign-upload">
                                        @if(isset($arr_vehicle_details['is_driving_license_verified']) && $arr_vehicle_details['is_driving_license_verified']=='PENDING') 
                                            <div class="pending-block posi"><i class="fa fa-exclamation"></i>&nbsp;Under Review</div>
                                        @elseif(isset($arr_vehicle_details['is_driving_license_verified']) && $arr_vehicle_details['is_driving_license_verified']=='APPROVED') 
                                            <div class="pending-block posi green"><i class="fa fa-check"></i>&nbsp;Approved</div>
                                        @elseif(isset($arr_vehicle_details['is_driving_license_verified']) && $arr_vehicle_details['is_driving_license_verified']=='NOTAPPROVED') 
                                            <div class="pending-block posi green">&nbsp;</div>
                                        @elseif(isset($arr_vehicle_details['is_driving_license_verified']) && $arr_vehicle_details['is_driving_license_verified']=='REJECTED') 
                                            <div class="pending-block posi red"><i class="fa fa-times"></i>&nbsp;Rejected</div>
                                        @endif
	                                    {{-- <div class="upload-block">
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
	                                    </div> --}}

	                                    <div class="upload-block">
	                                        <input type="file" id="driving_license" style="visibility:hidden; height: 0;" name="driving_license">
	                                        <div class="form-group">
	                                            <input type="text" class="file-caption  kv-fileinput-caption" placeholder="Upload Your Driver’s License (pdf/img)" readonly id="sub_driving_license" />
	                                            <div id="err_first_name" class="error"></div>
	                                            <div class="btn btn-primary btn-file"><a class="file" onclick="selectCurrentFile('driving_license')"> <i class="fa fa-paperclip"></i></a></div>
                                                @if(isset($arr_vehicle_details['driving_license_orginal_name']) && $arr_vehicle_details['driving_license_orginal_name']!='') {{$arr_vehicle_details['driving_license_orginal_name']}} @endif
                                            </div>
	                                    </div>

	                                    <div class="error" id="err_driving_license"></div>
	                                </div>
                                    
                                            
                                            
                                    <div class="form-group sign-upload">
                                        @if(isset($arr_vehicle_details['is_registration_doc_verified']) && $arr_vehicle_details['is_registration_doc_verified']=='PENDING') 
                                            <div class="pending-block posi"><i class="fa fa-exclamation"></i>&nbsp;Under Review</div>
                                        @elseif(isset($arr_vehicle_details['is_registration_doc_verified']) && $arr_vehicle_details['is_registration_doc_verified']=='APPROVED') 
                                            <div class="pending-block posi green"><i class="fa fa-check"></i>&nbsp;Approved</div>
                                         @elseif(isset($arr_vehicle_details['is_registration_doc_verified']) && $arr_vehicle_details['is_registration_doc_verified']=='NOTAPPROVED') 
                                            <div class="pending-block posi green">&nbsp;</div>   
                                        @elseif(isset($arr_vehicle_details['is_registration_doc_verified']) && $arr_vehicle_details['is_registration_doc_verified']=='REJECTED') 
                                            <div class="pending-block posi red"><i class="fa fa-times"></i>&nbsp;Rejected</div>
                                        @endif
                                        <div class="upload-block">
                                            <input type="file" id="registration_doc" 
                                                    style="visibility:hidden; height: 0;" 
                                                    name="registration_doc" 
                                                    {{-- @if(isset($arr_vehicle_details['registration_doc']) && $arr_vehicle_details['registration_doc']!='') 
                                                        data-parsley-required="false" 
                                                    @else  
                                                        data-parsley-required="true" 
                                                    @endif 
                                                    data-parsley-required-message="Please upload registration document" data-parsley-errors-container="#err_registration_doc" --}}>
                                            <div class="input-group ">
                                                <input type="text" class="form-control file-caption  kv-fileinput-caption" placeholder="Vehicle Registration Document (pdf/img)" readonly id="sub_registration_doc" />
                                                <div class="btn btn-primary btn-file"><a class="file" onclick="selectCurrentFile('registration_doc')"> <i class="fa fa-paperclip"></i></a></div>
                                                @if(isset($arr_vehicle_details['registration_doc']) && $arr_vehicle_details['registration_doc']!='') {{$arr_vehicle_details['registration_doc']}} @endif
                                            </div>
                                        </div>
                                        <div class="error" id="err_registration_doc"></div>
                                    </div>

                                    <div class="form-group sign-upload">
                                       {{-- <div class="pending-block posi green"><i class="fa fa-check"></i>&nbsp;Aproved</div> --}}
                                        @if(isset($arr_vehicle_details['is_insurance_doc_verified']) && $arr_vehicle_details['is_insurance_doc_verified']=='PENDING') 
                                            <div class="pending-block posi"><i class="fa fa-exclamation"></i>&nbsp;Under Review</div>
                                        @elseif(isset($arr_vehicle_details['is_insurance_doc_verified']) && $arr_vehicle_details['is_insurance_doc_verified']=='APPROVED') 
                                            <div class="pending-block posi green"><i class="fa fa-check"></i>&nbsp;Aproved</div>
                                            @elseif(isset($arr_vehicle_details['is_insurance_doc_verified']) && $arr_vehicle_details['is_insurance_doc_verified']=='NOTAPPROVED') 
                                            <div class="pending-block posi green">&nbsp;</div>
                                        @elseif(isset($arr_vehicle_details['is_insurance_doc_verified']) && $arr_vehicle_details['is_insurance_doc_verified']=='REJECTED') 
                                            <div class="pending-block posi red"><i class="fa fa-times"></i>&nbsp;Rejected</div>
                                        @endif
                                        <div class="upload-block">
                                            <input type="file" id="vehicle_insurance_doc" style="visibility:hidden; height: 0;" name="vehicle_insurance_doc" 
                                                {{-- @if(isset($arr_vehicle_details['insurance_doc']) && $arr_vehicle_details['insurance_doc']!='') 
                                                    data-parsley-required="false" 
                                                @else  
                                                    data-parsley-required="true" 
                                                @endif 
                                             data-parsley-required-message="Please upload vehicle insurance document" data-parsley-errors-container="#err_vehicle_insurance_doc" --}}>
                                            <div class="input-group ">
                                                <input type="text" class="form-control file-caption  kv-fileinput-caption" placeholder="Vehicle Insurance Document (pdf/img)" readonly id="sub_vehicle_insurance_doc" />
                                                <div class="btn btn-primary btn-file"><a class="file" onclick="selectCurrentFile('vehicle_insurance_doc')"> <i class="fa fa-paperclip"></i></a></div>
                                                @if(isset($arr_vehicle_details['insurance_doc']) && $arr_vehicle_details['insurance_doc']!='') {{$arr_vehicle_details['insurance_doc']}} @endif
                                            </div>
                                        </div>
                                        <div class="error" id="err_vehicle_insurance_doc"></div>
                                    </div>

                                    <div class="form-group sign-upload">
                                       {{-- <div class="pending-block posi red"><i class="fa fa-times">&nbsp;Approved<</i></div> --}}
                                        @if(isset($arr_vehicle_details['is_proof_of_inspection_doc_verified']) && $arr_vehicle_details['is_proof_of_inspection_doc_verified']=='PENDING') 
                                            <div class="pending-block posi"><i class="fa fa-exclamation"></i>&nbsp;Under Review</div>
                                        @elseif(isset($arr_vehicle_details['is_proof_of_inspection_doc_verified']) && $arr_vehicle_details['is_proof_of_inspection_doc_verified']=='APPROVED') 
                                            <div class="pending-block posi green"><i class="fa fa-check"></i>&nbsp;Approved</div>
                                            @elseif(isset($arr_vehicle_details['is_proof_of_inspection_doc_verified']) && $arr_vehicle_details['is_proof_of_inspection_doc_verified']=='NOTAPPROVED') 
                                            <div class="pending-block posi green">&nbsp;</div>
                                        @elseif(isset($arr_vehicle_details['is_proof_of_inspection_doc_verified']) && $arr_vehicle_details['is_proof_of_inspection_doc_verified']=='REJECTED') 
                                            <div class="pending-block posi red"><i class="fa fa-times"></i>&nbsp;Rejected</div>
                                        @endif
                                        <div class="upload-block">
                                            <input type="file" 
                                                    id="proof_of_inspection_doc" 
                                                    style="visibility:hidden; height: 0;" 
                                                    name="proof_of_inspection_doc" 
                                                    {{-- @if(isset($arr_vehicle_details['proof_of_inspection_doc']) && $arr_vehicle_details['proof_of_inspection_doc']!='') 
                                                        data-parsley-required="false" 
                                                    @else  
                                                        data-parsley-required="true" 
                                                    @endif 
                                                    data-parsley-required-message="Please upload proof of inspection document" data-parsley-errors-container="#err_proof_of_inspection_doc" --}}>
                                            <div class="input-group ">
                                                <input type="text" class="form-control file-caption  kv-fileinput-caption" placeholder="Proof of Inspection (pdf/img)" readonly id="sub_proof_of_inspection_doc" />
                                                <div class="btn btn-primary btn-file"><a class="file" onclick="selectCurrentFile('proof_of_inspection_doc')"> <i class="fa fa-paperclip"></i></a></div>
                                                @if(isset($arr_vehicle_details['proof_of_inspection_doc']) && $arr_vehicle_details['proof_of_inspection_doc']!='') {{$arr_vehicle_details['proof_of_inspection_doc']}} @endif
                                            </div>
                                        </div>
                                        <div class="error" id="err_proof_of_inspection_doc"></div>
                                    </div>


                                    <div class="form-group sign-upload">
                                        @if(isset($arr_vehicle_details['is_dmv_driving_record_verified']) && $arr_vehicle_details['is_dmv_driving_record_verified']=='PENDING') 
                                            <div class="pending-block posi"><i class="fa fa-exclamation"></i>&nbsp;Under Review</div>
                                        @elseif(isset($arr_vehicle_details['is_dmv_driving_record_verified']) && $arr_vehicle_details['is_dmv_driving_record_verified']=='APPROVED') 
                                            <div class="pending-block posi green"><i class="fa fa-check"></i>&nbsp;Approved</div>
                                            @elseif(isset($arr_vehicle_details['is_dmv_driving_record_verified']) && $arr_vehicle_details['is_dmv_driving_record_verified']=='NOTAPPROVED') 
                                            <div class="pending-block posi green">&nbsp;</div>
                                        @elseif(isset($arr_vehicle_details['is_dmv_driving_record_verified']) && $arr_vehicle_details['is_dmv_driving_record_verified']=='REJECTED') 
                                            <div class="pending-block posi red"><i class="fa fa-times"></i>&nbsp;Rejected</div>
                                        @endif
                                        <div class="upload-block">
                                            <input type="file" id="dmv_driving_record" style="visibility:hidden; height: 0;" name="dmv_driving_record" 
                                                    {{-- @if(isset($arr_vehicle_details['dmv_driving_record']) && $arr_vehicle_details['dmv_driving_record']!='') 
                                                        data-parsley-required="false" 
                                                    @else  
                                                        data-parsley-required="true" 
                                                    @endif 
                                                    data-parsley-required-message="Please upload dmv driving record document" data-parsley-errors-container="#err_dmv_driving_record" --}}>
                                            <div class="input-group ">
                                                <input type="text" class="form-control file-caption  kv-fileinput-caption" placeholder="DMV Driving Record(pdf/img)" readonly id="sub_dmv_driving_record" />
                                                <div class="btn btn-primary btn-file"><a class="file" onclick="selectCurrentFile('dmv_driving_record')"> <i class="fa fa-paperclip"></i></a></div>
                                                @if(isset($arr_vehicle_details['dmv_driving_record']) && $arr_vehicle_details['dmv_driving_record']!='') {{$arr_vehicle_details['dmv_driving_record']}} @endif
                                            </div>
                                        </div>
                                        <div class="error" id="err_dmv_driving_record"></div>
                                    </div>
                                    
                                    <div class="form-group sign-upload" id="usdot_doc_div" 
    
                                    @if(isset($arr_vehicle_details['is_usdot_required']))
                                        @if($arr_vehicle_details['is_usdot_required'] == '0')
                                            style="display: none;"
                                        @endif
                                    @else
                                        style="display: none;"
                                    @endif
                                    > 

                                        @if(isset($arr_vehicle_details['is_usdot_doc_verified']) && $arr_vehicle_details['is_usdot_doc_verified']=='PENDING') 
                                            <div class="pending-block posi"><i class="fa fa-exclamation"></i>&nbsp;Under Review</div>
                                        @elseif(isset($arr_vehicle_details['is_usdot_doc_verified']) && $arr_vehicle_details['is_usdot_doc_verified']=='APPROVED') 
                                            <div class="pending-block posi green"><i class="fa fa-check"></i>&nbsp;Approved</div>
                                            @elseif(isset($arr_vehicle_details['is_usdot_doc_verified']) && $arr_vehicle_details['is_usdot_doc_verified']=='NOTAPPROVED') 
                                            <div class="pending-block posi green">&nbsp;</div>
                                        @elseif(isset($arr_vehicle_details['is_usdot_doc_verified']) && $arr_vehicle_details['is_usdot_doc_verified']=='REJECTED') 
                                            <div class="pending-block posi red"><i class="fa fa-times"></i>&nbsp;Rejected</div>
                                        @endif
                                        <div class="upload-block">
                                            <input type="file" id="usdot_doc" style="visibility:hidden; height: 0;" name="usdot_doc" 
                                                    @if(isset($arr_vehicle_details['usdot_doc']))
                                                        @if($arr_vehicle_details['usdot_doc']!='') 
                                                            data-parsley-required="false" 
                                                        @else  
                                                            data-parsley-required="false" 
                                                        @endif 
                                                    @else
                                                        data-parsley-required="false" 
                                                    @endif
                                                    data-parsley-required-message="Please upload usdot document" data-parsley-errors-container="#err_usdot_doc">
                                            <div class="input-group ">
                                                <input type="text" class="form-control file-caption  kv-fileinput-caption" placeholder="USDOT document(pdf/img)" readonly id="sub_usdot_doc" />
                                                <div class="btn btn-primary btn-file"><a class="file" onclick="selectCurrentFile('usdot_doc')"> <i class="fa fa-paperclip"></i></a></div>
                                                @if(isset($arr_vehicle_details['usdot_doc']) && $arr_vehicle_details['usdot_doc']!='') {{$arr_vehicle_details['usdot_doc']}} @endif
                                            </div>
                                        </div>


                                        <div class="error" id="err_usdot_doc"></div>
                                    </div>

                                    <!--MC document-->
                                    
                                 <div class="form-group sign-upload" id="mc_doc_div" 
    
                                    @if(isset($arr_vehicle_details['is_mcdoc_required']))
                                        @if($arr_vehicle_details['is_mcdoc_required'] == '0')
                                            style="display: none;"
                                        @endif
                                    @else
                                        style="display: none;"
                                    @endif
                                    > 

                                        @if(isset($arr_vehicle_details['is_mcdoc_doc_verified']) && $arr_vehicle_details['is_mcdoc_doc_verified']=='PENDING') 
                                            <div class="pending-block posi"><i class="fa fa-exclamation"></i>&nbsp;Under Review</div>
                                        @elseif(isset($arr_vehicle_details['is_mcdoc_doc_verified']) && $arr_vehicle_details['is_mcdoc_doc_verified']=='APPROVED') 
                                            <div class="pending-block posi green"><i class="fa fa-check"></i>&nbsp;Approved</div>
                                            @elseif(isset($arr_vehicle_details['is_mcdoc_doc_verified']) && $arr_vehicle_details['is_mcdoc_doc_verified']=='NOTAPPROVED') 
                                            <div class="pending-block posi green">&nbsp;</div>
                                        @elseif(isset($arr_vehicle_details['is_mcdoc_doc_verified']) && $arr_vehicle_details['is_mcdoc_doc_verified']=='REJECTED') 
                                            <div class="pending-block posi red"><i class="fa fa-times"></i>&nbsp;Rejected</div>
                                        @endif
                                        <div class="upload-block">
                                            <input type="file" id="mc_doc" style="visibility:hidden; height: 0;" name="mc_doc" 
                                                    @if(isset($arr_vehicle_details['mc_doc']))
                                                        @if($arr_vehicle_details['mc_doc']!='') 
                                                            data-parsley-required="false" 
                                                        @else  
                                                            data-parsley-required="false" 
                                                        @endif 
                                                    @else
                                                        data-parsley-required="false" 
                                                    @endif
                                                    data-parsley-required-message="Please upload mc document" data-parsley-errors-container="#err_mc_doc">
                                            <div class="input-group ">
                                                <input type="text" class="form-control file-caption  kv-fileinput-caption" placeholder="MC document(pdf/img)" readonly id="sub_mc_doc" />
                                                <div class="btn btn-primary btn-file"><a class="file" onclick="selectCurrentFile('mc_doc')"> <i class="fa fa-paperclip"></i></a></div>
                                                @if(isset($arr_vehicle_details['mc_doc']) && $arr_vehicle_details['mc_doc']!='') {{$arr_vehicle_details['mc_doc']}} @endif
                                            </div>
                                        </div>

                                        
                                        <div class="error" id="err_mc_doc"></div>
                                    </div>

                                </div>
                                <input type="hidden" name="vehicle_id" value="{{ isset($arr_vehicle_details['id']) ? base64_encode($arr_vehicle_details['id']) : 0 }}">
                                <input type="hidden" name="driver_id" value="{{ isset($driver_id) ? base64_encode($driver_id) : 0 }}">
                                <input type="hidden" name="is_usdot_doc_required" id="is_usdot_doc_required" value="NO">
                                <input type="hidden" name="is_mc_doc_required" id="is_mc_doc_required" value="NO">

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
            $('#usdot_doc').attr('data-parsley-required', 'false');
            $('#is_usdot_doc_required').val('NO');
            $('#usdot_doc_div').hide();
        }

    }


    function check_is_mcdoc_required(ref){

        var is_mcdoc_required = $('option:selected', ref).attr('is_mcdoc_required');
       
        console.log(is_mcdoc_required);

        if(is_mcdoc_required!=undefined && is_mcdoc_required == '1')
        {
            $('#mc_doc').attr('data-parsley-required', 'true');
            $('#is_mc_doc_required').val('YES');
            $('#mc_doc_div').show();
        }
        else
        {
            $('#mc_doc').attr('data-parsley-required', 'false');
            $('#is_mc_doc_required').val('NO');
            $('#mc_doc_div').hide();
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

        $('#mc_doc').change(function() {
            $('#sub_mc_doc').val($(this).val());
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
