<?php $user_path     = config('app.project.role_slug.driver_role_slug'); ?>
 @extends('front.layout.master')                

    @section('main_content')

    <div class="blank-div"></div>

    <div class="email-block">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6 col-md-6 col-lg-6">
                    <div class="headding-text-bredcr">
                        Vehicle Edit
                    </div>
                </div>
                <div class="col-sm-6 col-md-6 col-lg-6">
                    <div class="bredcrum-right">
                        <a href="{{ $module_url_path }}" class="bredcrum-home"> Dashboard </a>
                        <span class="arrow-righ"><i class="fa fa-angle-right"></i></span> Vehicle Edit
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
                        @include('front.driver.left_bar')
                        <div class="col-sm-9 col-md-10 col-lg-10">
                        @include('front.layout._operation_status')
                            <div class="edit-posted-bg-main">
                                <div class="edit-btn-block edit-sec">
                                    <a href="{{ url('/').'/'.$user_path.'/vehicle'}}">
                                        <span class="view-pro-spa-eye-main"><i class="fa fa-car"></i></span> <span class="view-pro-spa-eye"><i class="fa fa-eye"></i></span> 
                                        <span class="view-pro-spa">View Vehicle</span>
                                    </a>
                                </div>
                                
                                {{-- {{dump($arr_data)}} --}}

                                <form name="frm-edit-vehicle" method="post" action="{{url( config('app.project.role_slug.driver_role_slug'))}}/vehicle_update" data-parsley-validate enctype="multipart/form-data">
                                    {{ csrf_field() }}
                                    <div class="profile-img-block">
                                    </div>
                                    
                                    <div class="clearfix"></div>
                                    <div class="row">
                                        <div class="col-sm-6 col-md-6 col-lg-6">
                                            <div class="form-group marg-top">
                                            <input type="hidden" name="vehicle_id" value="{{ $arr_data['id'] or ''}}">
                                                <select id="vehicle_type_id" name="vehicle_type_id" 

                                                @if(isset($arr_data['is_individual_vehicle']) && $arr_data['is_individual_vehicle'] == '0')
                                                    disabled="" 
                                                @endif
                                                onchange="check_is_usdot_required(this);check_is_mcdoc_required(this)";
                                                >
                                                <option disabled="">--Select Vehicle Type--</option>
                                                @if(isset($arr_type) && sizeof($arr_type))
                                                    @foreach($arr_type as $key_brand => $vehicle_type)
                                                        <option 
                                                                value="{{ isset($vehicle_type['id']) ? $vehicle_type['id'] : 0 }}" 
                                                                @if(isset($arr_data['vehicle_type_id']) && $arr_data['vehicle_type_id'] == $vehicle_type['id'])
                                                                    selected="" 
                                                                @endif
                                                                is_usdot_required="{{ isset($vehicle_type['is_usdot_required']) ? $vehicle_type['is_usdot_required'] : '' }}"
                                                                is_mcdoc_required="{{ isset($vehicle_type['is_mcdoc_required']) ? $vehicle_type['is_mcdoc_required'] : '' }}"

                                                                >{{ isset($vehicle_type['vehicle_type']) ? $vehicle_type['vehicle_type'] : '' }}</option>
                                                    @endforeach
                                                @endif        
                                                </select>
                                                <div class="error" id="err_vehicle_name"></div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-md-6 col-lg-6">
                                            <div class="form-group marg-top">
                                                <select id="vehicle_brand_id" name="vehicle_brand_id" onchange="loadVehicleModel(this);"
                                                @if(isset($arr_data['is_individual_vehicle']) && $arr_data['is_individual_vehicle'] == '0')
                                                    disabled="" 
                                                @endif
                                                >
                                                <option>--Select Vehicle Brand--</option>
                                                @if(isset($arr_brand) && sizeof($arr_brand))
                                                    @foreach($arr_brand as $key => $result)
                                                        <option value="{{ $result['id'] or '' }}" @if($arr_data['vehicle_brand_id']==$result['id']) selected @endif>{{ $result['name'] or '' }}</option>
                                                    @endforeach
                                                @endif        
                                                </select>
                                                <div class="error" id="err_vehicle_name"></div>
                                            </div>
                                        </div>                                        
                                         <div class="col-sm-6 col-md-6 col-lg-6">
                                            <div class="form-group marg-top">
                                               <select id="vehicle_model" name="vehicle_model"
                                               @if(isset($arr_data['is_individual_vehicle']) && $arr_data['is_individual_vehicle'] == '0')
                                                    disabled="" 
                                                @endif
                                               >
                                                <option>Select Vehicle Model</option>
                                                </select>
                                                <div class="error" id="err_vehicle_model"></div>
                                            </div>
                                        </div>
                                         <div class="col-sm-6 col-md-6 col-lg-6">
                                            <div class="form-group marg-top">
                                                <input type="text" name="vehicle_number" 
                                                @if(isset($arr_data['is_individual_vehicle']) && $arr_data['is_individual_vehicle'] == '0')
                                                    disabled="" 
                                                @endif
                                                data-parsley-required-message="Please enter Vehicle number" data-parsley-required="true" data-parsley-errors-container="#err_vehicle_number" value="{{ $arr_data['vehicle_number'] or ''}}">
                                                <label>Vehicle Number</label>
                                                <div class="error" id="err_vehicle_number"></div>
                                            </div>
                                        </div>
                                    </div>
                              
                                    <div class="row">
                                        
                                        {{-- {{dd($arr_data)}} --}}

                                        <div class="col-sm-12 col-md-12 col-lg-12">
                                            <div class="form-group sign-upload profile">

                                                 @if(isset($arr_data['is_driving_license_verified']) && $arr_data['is_driving_license_verified']=='PENDING') 
                                                    <div class="pending-block posi"><i class="fa fa-exclamation"></i>&nbsp;Under Review</div>
                                                @elseif(isset($arr_data['is_driving_license_verified']) && $arr_data['is_driving_license_verified']=='APPROVED') 
                                                    <div class="pending-block posi green"><i class="fa fa-check"></i>&nbsp;Approved</div>
                                                @elseif(isset($arr_data['is_driving_license_verified']) && $arr_data['is_driving_license_verified']=='NOTAPPROVED') 
                                                    <div class="pending-block posi green">&nbsp;</div>
                                                @elseif(isset($arr_data['is_driving_license_verified']) && $arr_data['is_driving_license_verified']=='REJECTED') 
                                                    <div class="pending-block posi red"><i class="fa fa-times"></i>&nbsp;Rejected</div>
                                                @endif

                                                <div class="upload-block">
                                                    <input type="file" id="driving_license" name="driving_license" style="visibility:hidden; height: 0;">
                                                    <div class="input-group ">
                                                        <input type="text" class="form-control file-caption  kv-fileinput-caption" placeholder="Upload Your Driving License" readonly id="sub_driving_license" value="" />
                                                        <div class="btn btn-primary btn-file"><a class="file" onclick="selectCurrentFile('driving_license')"> <i class="fa fa-paperclip"></i></a></div>
                                                    </div>
                                                    {{ isset($arr_data['driving_license_orginal_path']) ? $arr_data['driving_license_orginal_path'] : ''}}
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-12 col-md-12 col-lg-12">
                                            <div class="form-group sign-upload profile">

                                                @if(isset($arr_data['is_vehicle_image_verified']) && $arr_data['is_vehicle_image_verified']=='PENDING') 
                                                    <div class="pending-block posi"><i class="fa fa-exclamation"></i>&nbsp;Under Review</div>
                                                @elseif(isset($arr_data['is_vehicle_image_verified']) && $arr_data['is_vehicle_image_verified']=='APPROVED') 
                                                    <div class="pending-block posi green"><i class="fa fa-check"></i>&nbsp;Approved</div>
                                                 @elseif(isset($arr_data['is_vehicle_image_verified']) && $arr_data['is_vehicle_image_verified']=='NOTAPPROVED') 
                                                    <div class="pending-block posi green">&nbsp;</div>   
                                                @elseif(isset($arr_data['is_vehicle_image_verified']) && $arr_data['is_vehicle_image_verified']=='REJECTED') 
                                                    <div class="pending-block posi red"><i class="fa fa-times"></i>&nbsp;Rejected</div>
                                                @endif

                                                <div class="upload-block">
                                                    <input type="file" id="vehicle_image" 

                                                    @if(isset($arr_data['is_individual_vehicle']) && $arr_data['is_individual_vehicle'] == '0')
                                                        disabled="" 
                                                    @endif
                                                     name="vehicle_image" style="visibility:hidden; height: 0;">
                                                    <div class="input-group ">
                                                        <input type="text" class="form-control file-caption  kv-fileinput-caption" placeholder="Upload Your Vehicle Image" readonly id="sub_vehicle_image" value="" />
                                                        <div class="btn btn-primary btn-file"><a class="file" onclick="selectCurrentFile('vehicle_image')"> <i class="fa fa-paperclip"></i></a></div>
                                                    </div>
                                                    {{ isset($arr_data['vehicle_image']) ? $arr_data['vehicle_image'] : ''}}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-md-12 col-lg-12">
                                            <div class="form-group sign-upload profile">

                                                @if(isset($arr_data['is_registration_doc_verified']) && $arr_data['is_registration_doc_verified']=='PENDING') 
                                                    <div class="pending-block posi"><i class="fa fa-exclamation"></i>&nbsp;Under Review</div>
                                                @elseif(isset($arr_data['is_registration_doc_verified']) && $arr_data['is_registration_doc_verified']=='APPROVED') 
                                                    <div class="pending-block posi green"><i class="fa fa-check"></i>&nbsp;Approved</div>
                                                 @elseif(isset($arr_data['is_registration_doc_verified']) && $arr_data['is_registration_doc_verified']=='NOTAPPROVED') 
                                                    <div class="pending-block posi green">&nbsp;</div>   
                                                @elseif(isset($arr_data['is_registration_doc_verified']) && $arr_data['is_registration_doc_verified']=='REJECTED') 
                                                    <div class="pending-block posi red"><i class="fa fa-times"></i>&nbsp;Rejected</div>
                                                @endif

                                                <div class="upload-block">

                                                    <input type="file" id="registration_doc" 
                                                    @if(isset($arr_data['is_individual_vehicle']) && $arr_data['is_individual_vehicle'] == '0')
                                                        disabled="" 
                                                    @endif
                                                    name="registration_doc" style="visibility:hidden; height: 0;">
                                                    <div class="input-group ">
                                                        <input type="text" class="form-control file-caption  kv-fileinput-caption" placeholder="Upload Your Registration Document" readonly id="sub_registration_doc" value="" />
                                                        <div class="btn btn-primary btn-file"><a class="file" onclick="selectCurrentFile('registration_doc')"> <i class="fa fa-paperclip"></i></a></div>
                                                    </div>
                                                    {{ isset($arr_data['registration_doc']) ? $arr_data['registration_doc'] : ''}}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-md-12 col-lg-12">
                                            <div class="form-group sign-upload profile">

                                                @if(isset($arr_data['is_insurance_doc_verified']) && $arr_data['is_insurance_doc_verified']=='PENDING') 
                                                    <div class="pending-block posi"><i class="fa fa-exclamation"></i>&nbsp;Under Review</div>
                                                @elseif(isset($arr_data['is_insurance_doc_verified']) && $arr_data['is_insurance_doc_verified']=='APPROVED') 
                                                    <div class="pending-block posi green"><i class="fa fa-check"></i>&nbsp;Approved</div>
                                                    @elseif(isset($arr_data['is_insurance_doc_verified']) && $arr_data['is_insurance_doc_verified']=='NOTAPPROVED') 
                                                    <div class="pending-block posi green">&nbsp;</div>
                                                @elseif(isset($arr_data['is_insurance_doc_verified']) && $arr_data['is_insurance_doc_verified']=='REJECTED') 
                                                    <div class="pending-block posi red"><i class="fa fa-times"></i>&nbsp;Rejected</div>
                                                @endif

                                                <div class="upload-block">
                                                    <input type="file" id="vehicle_insurance_doc" 
                                                    @if(isset($arr_data['is_individual_vehicle']) && $arr_data['is_individual_vehicle'] == '0')
                                                        disabled="" 
                                                    @endif
                                                    name="vehicle_insurance_doc" style="visibility:hidden; height: 0;">
                                                    <div class="input-group ">
                                                        <input type="text" class="form-control file-caption  kv-fileinput-caption" placeholder="Upload Your Insurance Document" readonly id="sub_vehicle_insurance_doc"  value=""/>
                                                        <div class="btn btn-primary btn-file"><a class="file" onclick="selectCurrentFile('vehicle_insurance_doc')"> <i class="fa fa-paperclip"></i></a></div>
                                                    </div>
                                                    {{ isset($arr_data['insurance_doc']) ? $arr_data['insurance_doc'] : ''}}
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-12 col-md-12 col-lg-12">
                                            <div class="form-group sign-upload profile">

                                                @if(isset($arr_data['is_proof_of_inspection_doc_verified']) && $arr_data['is_proof_of_inspection_doc_verified']=='PENDING') 
                                                    <div class="pending-block posi"><i class="fa fa-exclamation"></i>&nbsp;Under Review</div>
                                                @elseif(isset($arr_data['is_proof_of_inspection_doc_verified']) && $arr_data['is_proof_of_inspection_doc_verified']=='APPROVED') 
                                                    <div class="pending-block posi green"><i class="fa fa-check"></i>&nbsp;Approved</div>
                                                    @elseif(isset($arr_data['is_proof_of_inspection_doc_verified']) && $arr_data['is_proof_of_inspection_doc_verified']=='NOTAPPROVED') 
                                                    <div class="pending-block posi green">&nbsp;</div>
                                                @elseif(isset($arr_data['is_proof_of_inspection_doc_verified']) && $arr_data['is_proof_of_inspection_doc_verified']=='REJECTED') 
                                                    <div class="pending-block posi red"><i class="fa fa-times"></i>&nbsp;Rejected</div>
                                                @endif

                                                <div class="upload-block">
                                                    <input type="file" id="proof_of_inspection_doc" 
                                                    @if(isset($arr_data['is_individual_vehicle']) && $arr_data['is_individual_vehicle'] == '0')
                                                        disabled="" 
                                                    @endif
                                                    name="proof_of_inspection_doc" style="visibility:hidden; height: 0;">
                                                    <div class="input-group ">
                                                        <input type="text" class="form-control file-caption  kv-fileinput-caption" placeholder="Upload Your Proof of Inspection Doc" readonly id="sub_proof_of_inspection_doc"  value=""/>
                                                        <div class="btn btn-primary btn-file"><a class="file" onclick="selectCurrentFile('proof_of_inspection_doc')"> <i class="fa fa-paperclip"></i></a></div>
                                                    </div>
                                                    {{ isset($arr_data['proof_of_inspection_doc']) ? $arr_data['proof_of_inspection_doc'] : ''}}
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-12 col-md-12 col-lg-12">
                                            <div class="form-group sign-upload profile">

                                                @if(isset($arr_data['is_dmv_driving_record_verified']) && $arr_data['is_dmv_driving_record_verified']=='PENDING') 
                                                    <div class="pending-block posi"><i class="fa fa-exclamation"></i>&nbsp;Under Review</div>
                                                @elseif(isset($arr_data['is_dmv_driving_record_verified']) && $arr_data['is_dmv_driving_record_verified']=='APPROVED') 
                                                    <div class="pending-block posi green"><i class="fa fa-check"></i>&nbsp;Approved</div>
                                                    @elseif(isset($arr_data['is_dmv_driving_record_verified']) && $arr_data['is_dmv_driving_record_verified']=='NOTAPPROVED') 
                                                    <div class="pending-block posi green">&nbsp;</div>
                                                @elseif(isset($arr_data['is_dmv_driving_record_verified']) && $arr_data['is_dmv_driving_record_verified']=='REJECTED') 
                                                    <div class="pending-block posi red"><i class="fa fa-times"></i>&nbsp;Rejected</div>
                                                @endif

                                                <div class="upload-block">
                                                    <input type="file" id="dmv_driving_record" 
                                                    @if(isset($arr_data['is_individual_vehicle']) && $arr_data['is_individual_vehicle'] == '0')
                                                        disabled="" 
                                                    @endif
                                                    name="dmv_driving_record" style="visibility:hidden; height: 0;">
                                                    <div class="input-group ">
                                                        <input type="text" class="form-control file-caption  kv-fileinput-caption" placeholder="Upload Your DMV Driving Document" readonly id="sub_dmv_driving_record" value=""/>
                                                        <div class="btn btn-primary btn-file"><a class="file" onclick="selectCurrentFile('dmv_driving_record')"> <i class="fa fa-paperclip"></i></a></div>
                                                    </div>
                                                    {{ isset($arr_data['dmv_driving_record']) ? $arr_data['dmv_driving_record'] : ''}}
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-12 col-md-12 col-lg-12" id="usdot_doc_div"

                                        @if(isset($arr_data['is_usdot_required']))
                                            @if($arr_data['is_usdot_required'] == '0')
                                                style="display: none;"
                                            @endif
                                        @else
                                            style="display: none;"
                                        @endif

                                        >
                                            <div class="form-group sign-upload profile">

                                                @if(isset($arr_data['is_usdot_doc_verified']) && $arr_data['is_usdot_doc_verified']=='PENDING') 
                                                    <div class="pending-block posi"><i class="fa fa-exclamation"></i>&nbsp;Under Review</div>
                                                @elseif(isset($arr_data['is_usdot_doc_verified']) && $arr_data['is_usdot_doc_verified']=='APPROVED') 
                                                    <div class="pending-block posi green"><i class="fa fa-check"></i>&nbsp;Approved</div>
                                                    @elseif(isset($arr_data['is_usdot_doc_verified']) && $arr_data['is_usdot_doc_verified']=='NOTAPPROVED') 
                                                    <div class="pending-block posi green">&nbsp;</div>
                                                @elseif(isset($arr_data['is_usdot_doc_verified']) && $arr_data['is_usdot_doc_verified']=='REJECTED') 
                                                    <div class="pending-block posi red"><i class="fa fa-times"></i>&nbsp;Rejected</div>
                                                @endif

                                                <div class="upload-block">
                                                    <input type="file" id="usdot_doc" 
                                                    @if(isset($arr_data['is_individual_vehicle']) && $arr_data['is_individual_vehicle'] == '0')
                                                        disabled="" 
                                                    @endif
                                                    name="usdot_doc" style="visibility:hidden; height: 0;">
                                                    <div class="input-group ">
                                                        <input type="text" class="form-control file-caption  kv-fileinput-caption" placeholder="Upload Usdot Doc Document" readonly id="sub_usdot_doc" value=""/>
                                                        <div class="btn btn-primary btn-file"><a class="file" onclick="selectCurrentFile('usdot_doc')"> <i class="fa fa-paperclip"></i></a></div>
                                                    </div>
                                                    {{ isset($arr_data['usdot_doc']) ? $arr_data['usdot_doc'] : ''}}
                                                </div>
                                            </div>
                                        </div> 

                                        <div class="col-sm-12 col-md-12 col-lg-12" id="mc_doc_div"
                                           @if(isset($arr_data['is_mcdoc_required']))
                                                @if($arr_data['is_mcdoc_required'] == '0')
                                                    style="display: none;"
                                                @endif
                                            @else
                                                style="display: none;"
                                            @endif
                                          >
                                            <div class="form-group sign-upload profile">

                                                @if(isset($arr_data['is_mcdoc_doc_verified']) && $arr_data['is_mcdoc_doc_verified']=='PENDING') 
                                                    <div class="pending-block posi"><i class="fa fa-exclamation"></i>&nbsp;Under Review</div>
                                                @elseif(isset($arr_data['is_mcdoc_doc_verified']) && $arr_data['is_mcdoc_doc_verified']=='APPROVED') 
                                                    <div class="pending-block posi green"><i class="fa fa-check"></i>&nbsp;Approved</div>
                                                    @elseif(isset($arr_data['is_mcdoc_doc_verified']) && $arr_data['is_mcdoc_doc_verified']=='NOTAPPROVED') 
                                                    <div class="pending-block posi green">&nbsp;</div>
                                                @elseif(isset($arr_data['is_mcdoc_doc_verified']) && $arr_data['is_mcdoc_doc_verified']=='REJECTED') 
                                                    <div class="pending-block posi red"><i class="fa fa-times"></i>&nbsp;Rejected</div>
                                                @endif

                                                <div class="upload-block">
                                                    <input type="file" id="mc_doc" 
                                                    @if(isset($arr_data['is_individual_vehicle']) && $arr_data['is_individual_vehicle'] == '0')
                                                        disabled="" 
                                                    @endif
                                                    name="mc_doc" style="visibility:hidden; height: 0;">
                                                    <div class="input-group ">
                                                        <input type="text" class="form-control file-caption  kv-fileinput-caption" placeholder="Upload MC Doc Document" readonly id="sub_mc_doc" value=""/>
                                                        <div class="btn btn-primary btn-file"><a class="file" onclick="selectCurrentFile('mc_doc')"> <i class="fa fa-paperclip"></i></a></div>
                                                    </div>
                                                    {{ isset($arr_data['mc_doc']) ? $arr_data['mc_doc'] : ''}}
                                                </div>
                                            </div>
                                        </div> 

                                        <input type="hidden" name="is_individual_vehicle" value="{{ isset($arr_data['is_individual_vehicle']) ? $arr_data['is_individual_vehicle'] : '' }}">
                                        <div class="cancle-btn-block">
                                            <div class="write-review-btn green margin-botto no-mar">
                                                <button type="submit">Submit</button>
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
        <!--for datepicker and time picker -->
    <script src="{{url('js/front/jquery-ui.js')}}" type="text/javascript"></script>
    <!--date and time picker js script-->
    <script>
        $(function () {
            $("#datepicker").datepicker(); //datepicker
            $("#date-second").datepicker();
        });
    </script>

 <script type="text/javascript">


        function check_is_usdot_required(ref){

            var is_usdot_required = $('option:selected', ref).attr('is_usdot_required');
            console.log(is_usdot_required);

            if(is_usdot_required!=undefined && is_usdot_required == '1')
            {
                // $('#usdot_doc').attr('data-parsley-required', 'true');
                $('#is_usdot_doc_required').val('YES');
                $('#usdot_doc_div').show();
            }
            else
            {
                // $('#usdot_doc').attr('data-parsley-required', 'false');
                $('#is_usdot_doc_required').val('NO');
                $('#usdot_doc_div').hide();
            }

        }


        function check_is_mcdoc_required(ref){

            var is_mcdoc_required = $('option:selected', ref).attr('is_mcdoc_required');
           
            console.log(is_mcdoc_required);

            if(is_mcdoc_required!=undefined && is_mcdoc_required == '1')
            {
                // $('#mc_doc').attr('data-parsley-required', 'true');
                $('#is_mc_doc_required').val('YES');
                $('#mc_doc_div').show();
            }
            else
            {
                // $('#mc_doc').attr('data-parsley-required', 'false');
                $('#is_mc_doc_required').val('NO');
                $('#mc_doc_div').hide();
            }

        }


        function selectCurrentFile(type) {
            $('#' + type).click();
        };

        $(document).ready(function() {

            $('#driving_license').change(function() {
                $('#sub_driving_license').val($(this).val());
            });

            $('#vehicle_image').change(function() {
                $('#sub_vehicle_image').val($(this).val());
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

            // $('#dmv_driving_record').change(function() {
            //     $('#sub_dmv_driving_record').val($(this).val());
            // });

            $('#usdot_doc').change(function() {
                $('#sub_usdot_doc').val($(this).val());
            });

            $('#mc_doc').change(function() {
                $('#sub_mc_doc').val($(this).val());
            });

        });

        // function myFunction() {
        //     $('#pdffile').click();
        // };
        // function myFunction1() {
        //     $('#pdffile1').click();
        // };
        // function myFunction2() {
        //     $('#pdffile2').click();
        // };
        // function myFunction3() {
        //     $('#pdffile3').click();
        // };
        // function myFunction4() {
        //     $('#pdffile4').click();
        // };

        // function myFunction5() {
        //     $('#pdffile5').click();
        // };

        // function myFunction6() {
        //     $('#pdffile6').click();
        // };

        // $(document).ready(function() {
        //     // This is the simple bit of jquery to duplicate the hidden field to subfile
        //     $('#pdffile').change(function() {
        //         $('#subfile').val($(this).val());
        //     });

        //     // This bit of jquery will show the actual file input box
        //     $('#showHidden').click(function() {
        //         $('#pdffile').css('visibilty', 'visible');
        //     });

        //     // This is the simple bit of jquery to duplicate the hidden field to subfile
        //     $('#pdffile1').change(function() {
        //         $('#subfile1').val($(this).val());
        //     });

        //     // This bit of jquery will show the actual file input box
        //     $('#showHidden1').click(function() {
        //         $('#pdffile1').css('visibilty', 'visible');
        //     });

        //     // This is the simple bit of jquery to duplicate the hidden field to subfile
        //     $('#pdffile2').change(function() {
        //         $('#subfile2').val($(this).val());
        //     });

        //     // This bit of jquery will show the actual file input box
        //     $('#showHidden2').click(function() {
        //         $('#pdffile2').css('visibilty', 'visible');
        //     });

        //     // This is the simple bit of jquery to duplicate the hidden field to subfile
        //     $('#pdffile3').change(function() {
        //         $('#subfile3').val($(this).val());
        //     });

        //     // This bit of jquery will show the actual file input box
        //     $('#showHidden3').click(function() {
        //         $('#pdffile3').css('visibilty', 'visible');
        //     });

        //     // This is the simple bit of jquery to duplicate the hidden field to subfile
        //     $('#pdffile4').change(function() {
        //         $('#subfile4').val($(this).val());
        //     });

        //     // This bit of jquery will show the actual file input box
        //     $('#showHidden4').click(function() {
        //         $('#pdffile4').css('visibilty', 'visible');
        //     });


        //     // This is the simple bit of jquery to duplicate the hidden field to subfile
        //     $('#pdffile5').change(function() {
        //         $('#subfile5').val($(this).val());
        //     });

        //     // This bit of jquery will show the actual file input box
        //     $('#showHidden5').click(function() {
        //         $('#pdffile5').css('visibilty', 'visible');
        //     });

        // });
    </script>



{{--     <!--footer section end here-->
    <script type="text/javascript">
        $(document).ready(function() {
            var brand = document.getElementById('logo-id');
            brand.className = 'attachment_upload';
            brand.onchange = function() {
                document.getElementById('fakeUploadLogo').value = this.value.substring(12);
            };

            // Source: http://stackoverflow.com/a/4459419/6396981
            function readURL(input) {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();

                    reader.onload = function(e) {
                        $('.img-preview').attr('src', e.target.result);

                    };

                    reader.readAsDataURL(input.files[0]);
                }
            }
            $("#logo-id").change(function() {
                readURL(this);
            });



        });
    </script>
 --}}
    <script src="https://maps.googleapis.com/maps/api/js?v=3&key=AIzaSyCccvQtzVx4aAt05YnfzJDSWEzPiVnNVsY&libraries=places"></script>
<script src="{{ url('/') }}/js/front/jquery.geocomplete.js"></script>

<script type="text/javascript">

            $(function () 
             {  
                $("#address").geocomplete({
                    details: ".geo-details",
                    detailsAttribute: "data-geo"
                }).bind("geocode:result", function (event, result){ /* Retrun Lat Long*/                      
                    var searchAddressComponents = result.address_components,
                    searchPostalCode="";
                });
            });

        var vehicle_model_url = '{{url('/api/common_data/get_vehicle_model')}}';
    $(document).ready(function()
    {         

        var vehicle_brand_id     = {{ $arr_data['vehicle_brand_id'] }}
        var vehicle_model_id     = {{ $arr_data['vehicle_model_id'] }}
            //var vehicle_brand_id = $(ref).val();

            if(vehicle_brand_id && vehicle_brand_id!="" && vehicle_brand_id!=0){

               // $('select[id="vehicle_model"]').find('option').remove().end().append('<option value="">Select Vehicle Model</option>').val('');

                $.ajax({
                    url:vehicle_model_url+'?vehicle_brand_id='+vehicle_brand_id,
                    type:'GET',
                    data:'flag=true',
                    dataType:'json',
                    success:function(response)
                    {
                        if(response.status=="success")
                        {

                            if(typeof(response.data) == "object")
                            {
                               var option = '<option value="">Select Vehicle Model</option>'; 
                               $(response.data).each(function(index,value)
                               {    
                                    var is_selected = '';
                                    if(vehicle_model_id == value.id){
                                      is_selected = 'selected';
                                    }

                                    option+='<option value="'+value.id+'" '+is_selected+'>'+value.name+'</option>';
                               });

                               $('select[id="vehicle_model"]').html(option);
                            }

                            

                        }

                        return false;
                    },error:function(res){

                    }    
                });
            }
            else{
                var option = '<option value="">Select Vehicle Model</option>'; 
                $('select[id="vehicle_model"]').html(option);
            }

    });

    function loadVehicleModel(ref) {
        var vehicle_brand_id     = {{ $arr_data['vehicle_brand_id'] }}

            var vehicle_brand_id = $(ref).val();

            if(vehicle_brand_id && vehicle_brand_id!="" && vehicle_brand_id!=0){

                $('select[id="vehicle_model"]').find('option').remove().end().append('<option value="">Select Vehicle Model</option>').val('');

                $.ajax({
                    url:vehicle_model_url+'?vehicle_brand_id='+vehicle_brand_id,
                    type:'GET',
                    data:'flag=true',
                    dataType:'json',
                   beforeSend:function()
                    {
                        $('select[id="vehicle_model"]').attr('readonly','readonly');
                    },
                    success:function(response)
                    {
                        if(response.status=="success")
                        {
                            $('select[id="vehicle_model"]').removeAttr('readonly');

                            if(typeof(response.data) == "object")
                            {
                               var option = '<option value="">Select Vehicle Model</option>'; 
                               $(response.data).each(function(index,value)
                               {
                                    option+='<option value="'+value.id+'">'+value.name+'</option>';
                               });

                               $('select[id="vehicle_model"]').html(option);
                            }

                            

                        }

                        return false;
                    },error:function(res){

                    }    
                });
            }
            else{
                var option = '<option value="">Select Vehicle Model</option>'; 
                $('select[id="vehicle_model"]').html(option);
            }
        }
</script>
@stop