<?php $user_path     = config('app.project.role_slug.driver_role_slug'); ?>
 @extends('front.layout.master')                

    @section('main_content')

    <div class="blank-div"></div>
     <div class="email-block">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6 col-md-6 col-lg-6">
                    <div class="headding-text-bredcr">
                       Manage Vehicle
                    </div>
                </div>
                <div class="col-sm-6 col-md-6 col-lg-6">
                    <div class="bredcrum-right">
                      <a href="{{ $module_url_path }}" class="bredcrum-home"> Dashboard </a>
                        <span class="arrow-righ"><i class="fa fa-angle-right"></i></span>
                        Manage Vehicle
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--dashboard page start-->
    <div class="latest-courses-main small-heigh my-enroll ener">
        <div class="container-fluid">
            <div class="clearfix"></div>
            <div class="row">
                <div class="col-sm-12 col-md-12 col-lg-12">
                    <div class="row">
                        @include('front.driver.left_bar')
                        <div class="col-sm-9 col-md-10 col-lg-10">
                        @include('front.layout._operation_status')

                            @if( isset($arr_data) && sizeof($arr_data)>0)

                            <div class="edit-posted-bg-main my-profile vehicle">
                                
                                <div class="edit-btn-block my-pro">
                                    <a href="{{ url('/').'/'.$user_path.'/vehicle_edit'}}"> <i class="fa fa-pencil"></i> <span>Edit Vehicle</span> </a>
                                </div>  

                                {{-- @if(isset($arr_data['is_individual_vehicle']) && $arr_data['is_individual_vehicle']=='1')
                                @endif     --}}

                                <div class="clearfix"></div>
                                <div class="edit-img-block-main">
                                    <div class="first-names marg-top">
                                        <span>Vehicle Brand : </span> <div class="first-names-light"> {{isset($arr_data['vehicle_brand_details']['name']) ? $arr_data['vehicle_brand_details']['name'] : '-'}} </div>
                                        <div class="clearfix"></div>
                                    </div>
                                    <div class="first-names">
                                        <span>Vehicle Model : </span> <div class="first-names-light"> {{isset($arr_data['vehicle_model_details']['name']) ? $arr_data['vehicle_model_details']['name'] : '-'}} </div>
                                        <div class="clearfix"></div>
                                    </div>
                                    <div class="first-names">
                                        <span>Vehicle Number : </span> <div class="first-names-light"> {{isset($arr_data['vehicle_number']) ? $arr_data['vehicle_number'] : '-'}} </div>
                                        <div class="clearfix"></div>
                                    </div>

                                    <div class="first-names">
                                        <span>Vehicle Type : </span> <div class="first-names-light"> {{isset($arr_data['vehicle_type']) ? $arr_data['vehicle_type'] : '-'}} </div>
                                        <div class="clearfix"></div>
                                    </div>

                                    <div class="first-names">
                                        <span>Driving License : </span> 
                                        <div class="first-names-light">
                                            @if(isset($arr_data['driving_license']) && ($arr_data['driving_license']!=''))
                                                <?php 
                                                        $driving_license = $arr_data['driving_license'];

                                                        $document_download_url = url('/common/download_document');
                                                        $driver_id = isset($arr_data['driver_id']) ? base64_encode($arr_data['driver_id']) : base64_encode(0);
                                                        $document_type = 'driving_license';
                                                        $document_download_url = $document_download_url.'?driver_id='.$driver_id.'&document_type='.$document_type;
                                                        
                                                ?>
                                                 <a class="btn btn-default vehicle" href="{{ $document_download_url }}" title="Download Driving License" target="_blank"><i class="fa fa-download"></i></a>
                                                 <a class="btn btn-default vehicle" href="{{ $driving_license }}" title="View Driving License" target="_blank"><i class="fa fa-eye"></i></a>

                                                {{-- <a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip btn-delets" title="View" target="_blank" href="{{$driving_license}}"><i class="fa fa-eye"></i></a>
                                                <a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip btn-delets" download="" title="Download" target="_blank" href="{{$driving_license}}"><i class="fa fa-download"></i></a> --}}

                                            @else
                                                <a class="btn btn-default vehicle" href="javascript:void(0);" title="Download Driving License"><i class="fa fa-download"></i></a>
                                                <a class="btn btn-default vehicle" href="javascript:void(0);" title="View Driving License"><i class="fa fa-eye"></i></a>   
                                            @endif
                                            
                                            @if(isset($arr_data['is_driving_license_verified']) && $arr_data['is_driving_license_verified'] == 'PENDING')
                                                <div class="pending-block"><i class="fa fa-exclamation"></i>&nbsp; Under review</div>
                                            @elseif(isset($arr_data['is_driving_license_verified']) && $arr_data['is_driving_license_verified'] == 'APPROVED')
                                                <div class="pending-block green"><i class="fa fa-check"></i>&nbsp; Approved</div>
                                            @elseif(isset($arr_data['is_driving_license_verified']) && $arr_data['is_driving_license_verified'] == 'REJECTED')
                                                <div class="pending-block red"><i class="fa fa-times"></i>&nbsp; Rejected</div>
                                            @endif


                                        </div>
                                        <div class="clearfix"></div>
                                    </div>

                                    <div class="first-names">
                                        <span>Vehicle Image : </span> 
                                        <div class="first-names-light">
                                            @if(isset($arr_data['vehicle_image_path']) && ($arr_data['vehicle_image_path']!=''))
                                                <?php $vehicle_image = $arr_data['vehicle_image_path'];?>
                                                 <a class="btn btn-default vehicle" href="{{ $vehicle_image }}" title="Download uploaded file" download><i class="fa fa-download"></i></a>
                                                 <a class="btn btn-default vehicle" href="{{ $driving_license }}" title="View Driving License" target="_blank"><i class="fa fa-eye"></i></a>
                                            @else
                                                <a class="btn btn-default vehicle" href="javascript:void(0);" title="Download uploaded file"><i class="fa fa-download"></i></a>
                                                <a class="btn btn-default vehicle" href="javascript:void(0);" title="View Driving License"><i class="fa fa-eye"></i></a>   
                                            @endif

                                            @if(isset($arr_data['is_vehicle_image_verified']) && $arr_data['is_vehicle_image_verified'] == 'PENDING')
                                                <div class="pending-block"><i class="fa fa-exclamation"></i>&nbsp; Under review</div>
                                            @elseif(isset($arr_data['is_vehicle_image_verified']) && $arr_data['is_vehicle_image_verified'] == 'APPROVED')
                                                <div class="pending-block green"><i class="fa fa-check"></i>&nbsp; Approved</div>
                                            @elseif(isset($arr_data['is_vehicle_image_verified']) && $arr_data['is_vehicle_image_verified'] == 'REJECTED')
                                                <div class="pending-block red"><i class="fa fa-times"></i>&nbsp; Rejected</div>
                                            @endif


                                        </div>
                                        <div class="clearfix"></div>
                                    </div>

                                    <div class="first-names">
                                        <span>Registration Document : </span> 
                                        <div class="first-names-light">
                                        @if(isset($arr_data['registration_doc_path']) && ($arr_data['registration_doc_path']!=''))
                                            <?php $registration_doc = $arr_data['registration_doc_path'];?> 
                                                <a class="btn btn-default vehicle" href="{{ $registration_doc }}" title="Download Registration Document" download><i class="fa fa-download"></i></a>
                                                <a class="btn btn-default vehicle" href="{{ $registration_doc }}" title="View Registration Document" target="_blank"><i class="fa fa-eye"></i></a>
                                         @else
                                            <a class="btn btn-default vehicle" href="javascript:void(0);" title="Download Registration Document"><i class="fa fa-download"></i></a>
                                            <a class="btn btn-default vehicle" href="javascript:void(0);" title="View Registration Document"><i class="fa fa-eye"></i></a>   
                                        @endif
                                        
                                        @if(isset($arr_data['is_registration_doc_verified']) && $arr_data['is_registration_doc_verified'] == 'PENDING')
                                            <div class="pending-block"><i class="fa fa-exclamation"></i>&nbsp; Under review</div>
                                        @elseif(isset($arr_data['is_registration_doc_verified']) && $arr_data['is_registration_doc_verified'] == 'APPROVED')
                                            <div class="pending-block green"><i class="fa fa-check"></i>&nbsp; Approved</div>
                                        @elseif(isset($arr_data['is_registration_doc_verified']) && $arr_data['is_registration_doc_verified'] == 'REJECTED')
                                            <div class="pending-block red"><i class="fa fa-times"></i>&nbsp; Rejected</div>
                                        @endif

                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                                    <div class="first-names">
                                        <span> Insurance Document: </span> 
                                        <div class="first-names-light">
                                        @if( isset($arr_data['vehicle_insurance_doc_path']) && ($arr_data['vehicle_insurance_doc_path']!=''))
                                            <?php $vehicle_insurance_doc = $arr_data['vehicle_insurance_doc_path'];?> 
                                            <a class="btn btn-default vehicle" href="{{ $vehicle_insurance_doc }}" title="Download Vehicle Insurance Document" download><i class="fa fa-download"></i></a>
                                            <a class="btn btn-default vehicle" href="{{ $vehicle_insurance_doc }}" title="View Vehicle Insurance Document" target="_blank"><i class="fa fa-eye"></i></a>
                                        @else
                                            <a class="btn btn-default vehicle" href="javascript:void(0);" title="Download Vehicle Insurance Document"><i class="fa fa-download"></i></a>
                                            <a class="btn btn-default vehicle" href="javascript:void(0);" title="View Vehicle Insurance Document"><i class="fa fa-eye"></i></a>   
                                        @endif

                                        @if(isset($arr_data['is_insurance_doc_verified']) && $arr_data['is_insurance_doc_verified'] == 'PENDING')
                                            <div class="pending-block"><i class="fa fa-exclamation"></i>&nbsp; Under review</div>
                                        @elseif(isset($arr_data['is_insurance_doc_verified']) && $arr_data['is_insurance_doc_verified'] == 'APPROVED')
                                            <div class="pending-block green"><i class="fa fa-check"></i>&nbsp; Approved</div>
                                        @elseif(isset($arr_data['is_insurance_doc_verified']) && $arr_data['is_insurance_doc_verified'] == 'REJECTED')
                                            <div class="pending-block red"><i class="fa fa-times"></i>&nbsp; Rejected</div>
                                        @endif

                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                                    
                                    <div class="first-names">
                                        <span>Proof of Inspection : </span> 
                                        <div class="first-names-light">
                                        @if( isset($arr_data['proof_of_inspection_doc_path']) && ($arr_data['proof_of_inspection_doc_path']!=''))
                                            <?php $proof_of_inspection_doc_path = $arr_data['proof_of_inspection_doc_path'];?> 
                                            <a class="btn btn-default vehicle" href="{{ $proof_of_inspection_doc_path }}" title="Download Proof Of Inspection Document" download><i class="fa fa-download"></i></a>
                                            <a class="btn btn-default vehicle" href="{{ $proof_of_inspection_doc_path }}" title="View Proof Of Inspection Document" target="_blank"><i class="fa fa-eye"></i></a>
                                        @else
                                            <a class="btn btn-default vehicle" href="javascript:void(0);" title="Download Proof Of Inspection Document"><i class="fa fa-download"></i></a>
                                            <a class="btn btn-default vehicle" href="javascript:void(0);" title="View Proof Of Inspection Document"><i class="fa fa-eye"></i></a>   
                                        @endif

                                        @if(isset($arr_data['is_proof_of_inspection_doc_verified']) && $arr_data['is_proof_of_inspection_doc_verified'] == 'PENDING')
                                            <div class="pending-block"><i class="fa fa-exclamation"></i>&nbsp; Under review</div>
                                        @elseif(isset($arr_data['is_proof_of_inspection_doc_verified']) && $arr_data['is_proof_of_inspection_doc_verified'] == 'APPROVED')
                                            <div class="pending-block green"><i class="fa fa-check"></i>&nbsp; Approved</div>
                                        @elseif(isset($arr_data['is_proof_of_inspection_doc_verified']) && $arr_data['is_proof_of_inspection_doc_verified'] == 'REJECTED')
                                            <div class="pending-block red"><i class="fa fa-times"></i>&nbsp; Rejected</div>
                                        @endif

                                        </div>
                                        <div class="clearfix"></div>
                                    </div>

                                    <div class="first-names">
                                        <span>Dmv Driving Record Doc : </span> 
                                        <div class="first-names-light">
                                        @if(isset($arr_data['dmv_driving_record_path']) && ($arr_data['dmv_driving_record_path']!=''))
                                             <?php $dmv_driving_record = $arr_data['dmv_driving_record_path'];?> 
                                             <a class="btn btn-default vehicle" href="{{ $dmv_driving_record }}" title="Download DMV Driving Record" download><i class="fa fa-download"></i></a>
                                             <a class="btn btn-default vehicle" href="{{ $dmv_driving_record }}" title="View DMV Driving Record" target="_blank"><i class="fa fa-eye"></i></a>
                                        @else
                                            <a class="btn btn-default vehicle" href="javascript:void(0);" title="Download DMV Driving Record"><i class="fa fa-download"></i></a>
                                            <a class="btn btn-default vehicle" href="javascript:void(0);" title="View DMV Driving Record"><i class="fa fa-eye"></i></a>   
                                        @endif

                                        @if(isset($arr_data['is_dmv_driving_record_verified']) && $arr_data['is_dmv_driving_record_verified'] == 'PENDING')
                                            <div class="pending-block"><i class="fa fa-exclamation"></i>&nbsp; Under review</div>
                                        @elseif(isset($arr_data['is_dmv_driving_record_verified']) && $arr_data['is_dmv_driving_record_verified'] == 'APPROVED')
                                            <div class="pending-block green"><i class="fa fa-check"></i>&nbsp; Approved</div>
                                        @elseif(isset($arr_data['is_dmv_driving_record_verified']) && $arr_data['is_dmv_driving_record_verified'] == 'REJECTED')
                                            <div class="pending-block red"><i class="fa fa-times"></i>&nbsp; Rejected</div>
                                        @endif

                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                                    <div class="first-names"
                                    @if(isset($arr_data['is_usdot_required']))
                                        @if($arr_data['is_usdot_required'] == '0')
                                            style="display: none;"
                                        @endif
                                    @else
                                        style="display: none;"
                                    @endif

                                    >

                                        <span>Usdot Doc Document: </span> 
                                        <div class="first-names-light">
                                        @if( isset($arr_data['usdot_doc_path']) && ($arr_data['usdot_doc_path']!=''))
                                           <?php $usdot_doc = $arr_data['usdot_doc_path'];?> 
                                           <a class="btn btn-default vehicle" href="{{ $usdot_doc }}" title="Download USDOT Document" download><i class="fa fa-download"></i></a>
                                           <a class="btn btn-default vehicle" href="{{ $usdot_doc }}" title="View DMV USDOT Document" target="_blank"><i class="fa fa-eye"></i></a>
                                        @else
                                            <a class="btn btn-default vehicle" href="javascript:void(0);" title="Download USDOT Document"><i class="fa fa-download"></i></a>
                                            <a class="btn btn-default vehicle" href="javascript:void(0);" title="View USDOT Document"><i class="fa fa-eye"></i></a>   
                                        @endif

                                        @if(isset($arr_data['is_usdot_doc_verified']) && $arr_data['is_usdot_doc_verified'] == 'PENDING')
                                            <div class="pending-block"><i class="fa fa-exclamation"></i>&nbsp; Under review</div>
                                        @elseif(isset($arr_data['is_usdot_doc_verified']) && $arr_data['is_usdot_doc_verified'] == 'APPROVED')
                                            <div class="pending-block green"><i class="fa fa-check"></i>&nbsp; Approved</div>
                                        @elseif(isset($arr_data['is_usdot_doc_verified']) && $arr_data['is_usdot_doc_verified'] == 'REJECTED')
                                            <div class="pending-block red"><i class="fa fa-times"></i>&nbsp; Rejected</div>
                                        @endif

                                        </div>
                                        <div class="clearfix"></div>
                                    </div>


                                    <div class="first-names"
                                        @if(isset($arr_data['is_mcdoc_required']))
                                            @if($arr_data['is_mcdoc_required'] == '0')
                                                style="display: none;"
                                            @endif
                                        @else
                                            style="display: none;"
                                        @endif
                                    
                                    >
                                        <span>Mc Document: </span> 
                                        <div class="first-names-light">
                                        @if( isset($arr_data['mcdoc_doc_path']) && ($arr_data['mcdoc_doc_path']!=''))
                                           <?php $mc_doc = $arr_data['mcdoc_doc_path'];?> 
                                           <a class="btn btn-default vehicle" href="{{ $mc_doc }}" title="Download MC Document" download><i class="fa fa-download"></i></a>
                                           <a class="btn btn-default vehicle" href="{{ $mc_doc }}" title="View DMV MC Document" target="_blank"><i class="fa fa-eye"></i></a>
                                        @else
                                            <a class="btn btn-default vehicle" href="javascript:void(0);" title="Download MC Document"><i class="fa fa-download"></i></a>
                                            <a class="btn btn-default vehicle" href="javascript:void(0);" title="View MC Document"><i class="fa fa-eye"></i></a>   
                                        @endif

                                        @if(isset($arr_data['is_mcdoc_doc_verified']) && $arr_data['is_mcdoc_doc_verified'] == 'PENDING')
                                            <div class="pending-block"><i class="fa fa-exclamation"></i>&nbsp; Under review</div>
                                        @elseif(isset($arr_data['is_mcdoc_doc_verified']) && $arr_data['is_mcdoc_doc_verified'] == 'APPROVED')
                                            <div class="pending-block green"><i class="fa fa-check"></i>&nbsp; Approved</div>
                                        @elseif(isset($arr_data['is_mcdoc_doc_verified']) && $arr_data['is_mcdoc_doc_verified'] == 'REJECTED')
                                            <div class="pending-block red"><i class="fa fa-times"></i>&nbsp; Rejected</div>
                                        @endif

                                        </div>
                                        <div class="clearfix"></div>
                                    </div>

                                    {{-- <div class="first-names">
                                        <span>Vehicle : </span> 
                                        <div class="first-names-light">
                                            <div class="pending-block"><i class="fa fa-exclamation"></i> Pending</div>
                                            <div class="pending-block green"><i class="fa fa-check"></i> Completed</div>
                                            <div class="pending-block red"><i class="fa fa-times"></i> Cancelled</div>
                                            <div class="clearfix"></div>
                                        </div>
                                    </div> --}}

                            </div>
                            @else
                            <div class="edit-posted-bg-main my-profile">
                                <center>{{ isset($msg) ? $msg : 'records not avalible' }}</center>
                            </div>
                            @endif


                        </div>


                    </div>
                </div>
            </div>



        </div>
    </div>
    </div>
@stop