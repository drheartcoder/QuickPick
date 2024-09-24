@extends('company.layout.master')                
@section('main_content')

<style type="text/css">
  .ui-autocomplete
  {
    max-width: 26% !important;
  }
  .mass_min {
    background: #fcfcfc none repeat scroll 0 0;
    border: 1px dashed #d0d0d0;
    float: left;
    margin-bottom: 20px;
    margin-right: 21px;
    margin-top: 10px;
    padding: 5px;
  }
  .mass_addphoto {
    display: inline-block;
    margin: 0 10px;
    padding-top: 27px;
    text-align: center;
    vertical-align: top;
  }
  .mass_addphoto {
    text-align: center;
  }
  .upload_pic_btn {
    cursor: pointer;
    font-size: 14px;
    height: 100% !important;
    left: 0;
    margin: 0;
    opacity: 0;
    padding: 0;
    position: absolute;
    right: 0;
    top: 0;
  }
  .table.table-bordered.view .badge{float: right}
  .view-bitto-righ{float: right}

  .btn-verify {
    background-color: #57ce81;
    border-radius: 3px !important;
  }
  .btn-unverify {
    background-color: #da0000;
    border-radius: 3px !important;
  }
  

</style>

<!-- BEGIN Page Title -->
<div class="page-title">
  <div>
  </div>
</div>
<!-- END Page Title -->
<!-- BEGIN Breadcrumb -->
<div id="breadcrumbs">
  <ul class="breadcrumb">
    <li>
      <i class="fa fa-home">
      </i>
      <a href="{{ url($company_panel_slug.'/dashboard') }}">Dashboard
      </a>
    </li>
    <span class="divider">
      <i class="fa fa-angle-right">
      </i>
      <i class="fa fa-users faa-vertical animated-hover">
      </i>
      
      <a href="{{ url($module_url_path) }}" class="call_loader">{{ $module_title or ''}}
      </a>
    </span> 
    <span class="divider">
      <i class="fa fa-angle-right">
      </i>
      <i class="fa fa-eye">
      </i>
    </span> 
    <li class="active">   {{ $page_title or '' }}
    </li>
  </ul>
</div>
<!-- END Breadcrumb -->
<!-- BEGIN Main Content -->
<div class="row">
  <div class="col-md-12">
    <div class="box ">
      <div class="box-title">
        <h3>
          <i class="fa fa-eye">
          </i> {{ $page_title or '' }} 
        </h3>
        <div class="box-tool">
        </div>
      </div>
      <div class="box-content">
        <?php

          $driving_license = $vehicle_image = $registration_doc = $proof_of_inspection_doc = $insurance_doc = $dmv_driving_record = $usdot_doc = $mc_doc='';

          if(isset($arr_data['driver_car_details']['driver_details']['driving_license']) && $arr_data['driver_car_details']['driver_details']['driving_license']!=''){
              if(file_exists($driving_license_base_path.$arr_data['driver_car_details']['driver_details']['driving_license'])){
                $driving_license = $driving_license_public_path.$arr_data['driver_car_details']['driver_details']['driving_license'];
              }
          }
          if(isset($arr_data['vehicle_image']) && $arr_data['vehicle_image']!=''){
              if(file_exists($vehicle_doc_base_path.$arr_data['vehicle_image'])){
                $vehicle_image = $vehicle_doc_public_path.$arr_data['vehicle_image'];
              }
          }
          if(isset($arr_data['registration_doc']) && $arr_data['registration_doc']!=''){
              if(file_exists($vehicle_doc_base_path.$arr_data['registration_doc'])){
                $registration_doc = $vehicle_doc_public_path.$arr_data['registration_doc'];
              }
          }
          
          if(isset($arr_data['proof_of_inspection_doc']) && $arr_data['proof_of_inspection_doc']!=''){
              if(file_exists($vehicle_doc_base_path.$arr_data['proof_of_inspection_doc'])){
                $proof_of_inspection_doc = $vehicle_doc_public_path.$arr_data['proof_of_inspection_doc'];
              }
          }

          if(isset($arr_data['insurance_doc']) && $arr_data['insurance_doc']!=''){
              if(file_exists($vehicle_doc_base_path.$arr_data['insurance_doc'])){
                $insurance_doc = $vehicle_doc_public_path.$arr_data['insurance_doc'];
              }
          }
          if(isset($arr_data['dmv_driving_record']) && $arr_data['dmv_driving_record']!=''){
              if(file_exists($vehicle_doc_base_path.$arr_data['dmv_driving_record'])){
                $dmv_driving_record = $vehicle_doc_public_path.$arr_data['dmv_driving_record'];
              }
          }
          if(isset($arr_data['usdot_doc']) && $arr_data['usdot_doc']!=''){
              if(file_exists($vehicle_doc_base_path.$arr_data['usdot_doc'])){
                $usdot_doc = $vehicle_doc_public_path.$arr_data['usdot_doc'];
              }
          }
          if(isset($arr_data['mc_doc']) && $arr_data['mc_doc']!=''){
              if(file_exists($vehicle_doc_base_path.$arr_data['mc_doc'])){
                $mc_doc = $vehicle_doc_public_path.$arr_data['mc_doc'];
              }
          }

          $vehicle_id         = isset($arr_data['id']) ? $arr_data['id'] : 0;
          $driver_id         = isset($arr_data['driver_car_details']['driver_id']) ? $arr_data['driver_car_details']['driver_id'] : 0;

          $is_driving_license_verified = 'APPROVED';

          if(isset($arr_data['driver_car_details']['driver_id']) && $arr_data['driver_car_details']['driver_id']!=0)
          {
            $is_driving_license_verified         = isset($arr_data['driver_car_details']['driver_details']['is_driving_license_verified']) ? $arr_data['driver_car_details']['driver_details']['is_driving_license_verified'] : '';
          }

          $is_vehicle_image_verified           = isset($arr_data['is_vehicle_image_verified']) ? $arr_data['is_vehicle_image_verified'] : '';
          $is_registration_doc_verified        = isset($arr_data['is_registration_doc_verified']) ? $arr_data['is_registration_doc_verified'] : '';
          $is_insurance_doc_verified           = isset($arr_data['is_insurance_doc_verified']) ? $arr_data['is_insurance_doc_verified'] : '';
          $is_proof_of_inspection_doc_verified = isset($arr_data['is_proof_of_inspection_doc_verified']) ? $arr_data['is_proof_of_inspection_doc_verified'] : '';
          $is_dmv_driving_record_verified      = isset($arr_data['is_dmv_driving_record_verified']) ? $arr_data['is_dmv_driving_record_verified'] : '';
          
          $is_usdot_doc_verified = 'APPROVED';

          if(isset($arr_data['vehicle_type_details']['is_usdot_required']) && $arr_data['vehicle_type_details']['is_usdot_required'] == '1')
          {
              $is_usdot_doc_verified               = isset($arr_data['is_usdot_doc_verified']) ? $arr_data['is_usdot_doc_verified'] : '';
          }
          
          $is_mc_doc_verified = 'APPROVED';

          if(isset($arr_data['vehicle_type_details']['is_mcdoc_required']) && $arr_data['vehicle_type_details']['is_mcdoc_required'] == '1')
          {
              $is_mc_doc_verified               = isset($arr_data['is_mcdoc_doc_verified']) ? $arr_data['is_mcdoc_doc_verified'] : '';
          }


          $is_all_document_verified = 'NO';

        if( 
            $is_driving_license_verified          == 'APPROVED' && 
            $is_vehicle_image_verified            == 'APPROVED' &&
            $is_registration_doc_verified         == 'APPROVED' &&
            $is_insurance_doc_verified            == 'APPROVED' &&
            $is_proof_of_inspection_doc_verified  == 'APPROVED' &&
            $is_dmv_driving_record_verified       == 'APPROVED' &&
            $is_usdot_doc_verified                == 'APPROVED' &&
            $is_mc_doc_verified                   == 'APPROVED' 
           )
        {
            $is_all_document_verified = 'YES';
        }

        ?>
    
        <div class="box">
          <div class="box-content studt-padding">
            <div class="row">
                <div class="col-md-6">
                <h3>Vehicle Information</h3>
                <br>
                    <table class="table table-bordered view">
                      <tbody>

                            <tr>
                              <th style="width: 30%">Vehicle Type
                              </th>
                              <td>
                                {{ isset($arr_data['vehicle_type_details']['vehicle_type'])  ? $arr_data['vehicle_type_details']['vehicle_type']  : '' }}
                              </td>
                            </tr> 

                            <tr>
                              <th style="width: 30%">Vehicle Brand 
                              </th>
                              <td>
                                {{ isset($arr_data['vehicle_brand_details']['name'])  ? $arr_data['vehicle_brand_details']['name']  : '' }}
                              </td>
                            </tr>
                            
                            <tr>
                              <th style="width: 30%">Vehicle Model
                              </th>
                              <td>
                                {{ isset($arr_data['vehicle_model_details']['name'])  ? $arr_data['vehicle_model_details']['name']  : '' }}
                              </td>
                            </tr>
                            <tr>
                              <th style="width: 30%">Vehicle License Plate Number
                              </th>
                              <td>
                                {{ isset($arr_data['vehicle_number'])  ? $arr_data['vehicle_number']  : '' }}
                              </td>
                            </tr>
                          @if(isset($arr_data['driver_car_details']['driver_id']) && $arr_data['driver_car_details']['driver_id']!=0)
                          
                            <tr>
                              <th style="width: 30%">Assigned Driver Name
                              </th>
                              <td>
                                {{ isset($arr_data['driver_car_details']['driver_details']['first_name'])  ? $arr_data['driver_car_details']['driver_details']['first_name']  : '' }} {{ isset($arr_data['driver_car_details']['driver_details']['first_name'])  ? $arr_data['driver_car_details']['driver_details']['first_name']  : '' }}
                              </td>
                            </tr>


                            <tr>
                              <th style="width: 30%">Driving License
                              </th>
                              
                              <td>
                                @if($driving_license!='')
                                  <a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip btn-delets" title="View" target="_blank" href="{{$driving_license}}"><i class="fa fa-eye"></i></a>
                                  <a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip btn-delets" download="" title="Download" target="_blank" href="{{$driving_license}}"><i class="fa fa-download"></i></a>
                                @else
                                  <a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip btn-delets" title="View" href="javascript:void(0);"><i class="fa fa-eye"></i></a>
                                  <a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip btn-delets" title="Download" href="javascript:void(0);"><i class="fa fa-download"></i></a>
                                @endif

                                @if($is_driving_license_verified=='PENDING')
                                  <div class="view-bitto-righ">
                                  <a href="{{ $module_url_path.'/document_status?driver_id='.base64_encode($driver_id).'&vehicle_id='.base64_encode($vehicle_id).'&type=driving_license&status=approved' }}" class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip" onclick="return confirm_action(this,event,'Do you really want to approved Driving License ?')" title="Approve Driving License" ><i class="fa fa-check"></i></a>
                                  <a href="{{ $module_url_path.'/document_status?driver_id='.base64_encode($driver_id).'&vehicle_id='.base64_encode($vehicle_id).'&type=driving_license&status=rejected' }}" class="btn btn-circle btn-to-danger btn-bordered btn-fill show-tooltip btn-dangers" onclick="return confirm_action(this,event,'Do you really want to reject Driving License ?')" title="Reject Driving License" ><i class="fa fa-times"></i></a>
                                  </div>
                                 @elseif($is_driving_license_verified=='APPROVED')
                                  <span class="badge badge-success" style="width:100px">Approved</span>
                                @elseif($is_driving_license_verified=='REJECTED')
                                  <span class="badge badge-important" style="width:100px">Rejected</span>
                                @endif
                              </td>

                            </tr>
                          @endif

                            <tr>
                              <th style="width: 30%">Vehicle Image
                              </th>
                              <td>
                                @if($vehicle_image!='')
                                  <a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip btn-delets" title="View" target="_blank" href="{{$vehicle_image}}"><i class="fa fa-eye"></i></a>
                                  <a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip btn-delets" download="" title="Download" target="_blank" href="{{$vehicle_image}}"><i class="fa fa-download"></i></a>
                                @else
                                  <a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip btn-delets" title="View" href="javascript:void(0);"><i class="fa fa-eye"></i></a>
                                  <a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip btn-delets" title="Download" href="javascript:void(0);"><i class="fa fa-download"></i></a>
                                @endif

                                @if($is_vehicle_image_verified=='PENDING')
                                 <div class="view-bitto-righ">
                                  <a href="{{ $module_url_path.'/document_status?driver_id='.base64_encode($driver_id).'&vehicle_id='.base64_encode($vehicle_id).'&type=vehicle_image&status=approved' }}" class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip" onclick="return confirm_action(this,event,'Do you really want to approved Driving License ?')" title="Approve Driving License" ><i class="fa fa-check"></i></a>
                                  <a href="{{ $module_url_path.'/document_status?driver_id='.base64_encode($driver_id).'&vehicle_id='.base64_encode($vehicle_id).'&type=vehicle_image&status=rejected' }}" class="btn btn-circle btn-to-danger btn-bordered btn-fill show-tooltip btn-dangers" onclick="return confirm_action(this,event,'Do you really want to reject Driving License ?')" title="Reject Driving License" ><i class="fa fa-times"></i></a>
                                  </div>
                                 @elseif($is_vehicle_image_verified=='APPROVED')
                                  <span class="badge badge-success" style="width:100px">Approved</span>
                                @elseif($is_vehicle_image_verified=='REJECTED')
                                  <span class="badge badge-important" style="width:100px">Rejected</span>
                                @endif
                              </td>
                            </tr>

                            <tr>
                              <th style="width: 30%">Registration Document
                              </th>
                              <td>
                                @if($registration_doc!='')
                                  <a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip btn-delets" title="View" target="_blank" href="{{$registration_doc}}"><i class="fa fa-eye"></i></a>
                                  <a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip btn-delets" download="" title="Download" target="_blank" href="{{$registration_doc}}"><i class="fa fa-download"></i></a>
                                @else
                                  <a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip btn-delets" title="View" href="javascript:void(0);"><i class="fa fa-eye"></i></a>
                                  <a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip btn-delets" title="Download" href="javascript:void(0);"><i class="fa fa-download"></i></a>
                                @endif

                                @if($is_registration_doc_verified=='PENDING')
                                 <div class="view-bitto-righ">
                                  <a href="{{ $module_url_path.'/document_status?driver_id='.base64_encode($driver_id).'&vehicle_id='.base64_encode($vehicle_id).'&type=registration_doc&status=approved' }}" class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip" onclick="return confirm_action(this,event,'Do you really want to approved Driving License ?')" title="Approve Driving License" ><i class="fa fa-check"></i></a>
                                  <a href="{{ $module_url_path.'/document_status?driver_id='.base64_encode($driver_id).'&vehicle_id='.base64_encode($vehicle_id).'&type=registration_doc&status=rejected' }}" class="btn btn-circle btn-to-danger btn-bordered btn-fill show-tooltip btn-dangers" onclick="return confirm_action(this,event,'Do you really want to reject Driving License ?')" title="Reject Driving License" ><i class="fa fa-times"></i></a>
                                  </div>
                                 @elseif($is_registration_doc_verified=='APPROVED')
                                  <span class="badge badge-success" style="width:100px">Approved</span>
                                @elseif($is_registration_doc_verified=='REJECTED')
                                  <span class="badge badge-important" style="width:100px">Rejected</span>
                                @endif
                              </td>
                            </tr>

                            <tr>
                              <th style="width: 30%">Proof of Inspection Document
                              </th>
                              <td>
                                @if($proof_of_inspection_doc!='')
                                  <a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip btn-delets" title="View" target="_blank" href="{{$proof_of_inspection_doc}}"><i class="fa fa-eye"></i></a>
                                  <a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip btn-delets" download="" title="Download" target="_blank" href="{{$proof_of_inspection_doc}}"><i class="fa fa-download"></i></a>
                                @else
                                  <a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip btn-delets" title="View" href="javascript:void(0);"><i class="fa fa-eye"></i></a>
                                  <a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip btn-delets" title="Download" href="javascript:void(0);"><i class="fa fa-download"></i></a>
                                @endif

                                @if($is_proof_of_inspection_doc_verified=='PENDING')
                                 <div class="view-bitto-righ">
                                  <a href="{{ $module_url_path.'/document_status?driver_id='.base64_encode($driver_id).'&vehicle_id='.base64_encode($vehicle_id).'&type=proof_of_inspection_doc&status=approved' }}" class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip" onclick="return confirm_action(this,event,'Do you really want to approved Driving License ?')" title="Approve Driving License" ><i class="fa fa-check"></i></a>
                                  <a href="{{ $module_url_path.'/document_status?driver_id='.base64_encode($driver_id).'&vehicle_id='.base64_encode($vehicle_id).'&type=proof_of_inspection_doc&status=rejected' }}" class="btn btn-circle btn-to-danger btn-bordered btn-fill show-tooltip btn-dangers" onclick="return confirm_action(this,event,'Do you really want to reject Driving License ?')" title="Reject Driving License" ><i class="fa fa-times"></i></a>
                                  </div>
                                 @elseif($is_proof_of_inspection_doc_verified=='APPROVED')
                                  <span class="badge badge-success" style="width:100px">Approved</span>
                                @elseif($is_proof_of_inspection_doc_verified=='REJECTED')
                                  <span class="badge badge-important" style="width:100px">Rejected</span>
                                @endif
                              </td>
                            </tr>


                            <tr>
                              <th style="width: 30%">Insurance Document
                              </th>
                              <td>
                                @if($insurance_doc!='')
                                  <a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip btn-delets" title="View" target="_blank" href="{{$insurance_doc}}"><i class="fa fa-eye"></i></a>
                                  <a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip btn-delets" download="" title="Download" target="_blank" href="{{$insurance_doc}}"><i class="fa fa-download"></i></a>
                                @else
                                  <a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip btn-delets" title="View" href="javascript:void(0);"><i class="fa fa-eye"></i></a>
                                  <a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip btn-delets" title="Download" href="javascript:void(0);"><i class="fa fa-download"></i></a>
                                @endif

                                @if($is_insurance_doc_verified=='PENDING')
                                 <div class="view-bitto-righ">
                                  <a href="{{ $module_url_path.'/document_status?driver_id='.base64_encode($driver_id).'&vehicle_id='.base64_encode($vehicle_id).'&type=insurance_doc&status=approved' }}" class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip" onclick="return confirm_action(this,event,'Do you really want to approved Driving License ?')" title="Approve Driving License" ><i class="fa fa-check"></i></a>
                                  <a href="{{ $module_url_path.'/document_status?driver_id='.base64_encode($driver_id).'&vehicle_id='.base64_encode($vehicle_id).'&type=insurance_doc&status=rejected' }}" class="btn btn-circle btn-to-danger btn-bordered btn-fill show-tooltip btn-dangers" onclick="return confirm_action(this,event,'Do you really want to reject Driving License ?')" title="Reject Driving License" ><i class="fa fa-times"></i></a>
                                  </div>
                                 @elseif($is_insurance_doc_verified=='APPROVED')
                                  <span class="badge badge-success" style="width:100px">Approved</span>
                                @elseif($is_insurance_doc_verified=='REJECTED')
                                  <span class="badge badge-important" style="width:100px">Rejected</span>
                                @endif
                              </td>
                            </tr>

                            <tr>
                              <th style="width: 30%">DMV Driving Record Document
                              </th>
                              <td>
                                @if($dmv_driving_record!='')
                                  <a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip btn-delets" title="View" target="_blank" href="{{$dmv_driving_record}}"><i class="fa fa-eye"></i></a>
                                  <a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip btn-delets" download="" title="Download" target="_blank" href="{{$dmv_driving_record}}"><i class="fa fa-download"></i></a>
                                @else
                                  <a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip btn-delets" title="View" href="javascript:void(0);"><i class="fa fa-eye"></i></a>
                                  <a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip btn-delets" title="Download" href="javascript:void(0);"><i class="fa fa-download"></i></a>
                                @endif

                                @if($is_dmv_driving_record_verified=='PENDING')
                                 <div class="view-bitto-righ">
                                  <a href="{{ $module_url_path.'/document_status?driver_id='.base64_encode($driver_id).'&vehicle_id='.base64_encode($vehicle_id).'&type=dmv_driving_record&status=approved' }}" class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip" onclick="return confirm_action(this,event,'Do you really want to approved Driving License ?')" title="Approve Driving License" ><i class="fa fa-check"></i></a>
                                  <a href="{{ $module_url_path.'/document_status?driver_id='.base64_encode($driver_id).'&vehicle_id='.base64_encode($vehicle_id).'&type=dmv_driving_record&status=rejected' }}" class="btn btn-circle btn-to-danger btn-bordered btn-fill show-tooltip btn-dangers" onclick="return confirm_action(this,event,'Do you really want to reject Driving License ?')" title="Reject Driving License" ><i class="fa fa-times"></i></a>
                                  </div>
                                 @elseif($is_dmv_driving_record_verified=='APPROVED')
                                  <span class="badge badge-success" style="width:100px">Approved</span>
                                @elseif($is_dmv_driving_record_verified=='REJECTED')
                                  <span class="badge badge-important" style="width:100px">Rejected</span>
                                @endif
                              </td>
                            </tr>

                          @if(isset($arr_data['vehicle_type_details']['is_usdot_required']) && $arr_data['vehicle_type_details']['is_usdot_required'] == '1')
                            <tr>
                              <th style="width: 30%">USDOT Document
                              </th>
                              <td>
                                @if($usdot_doc!='')
                                  <a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip btn-delets" title="View" target="_blank" href="{{$usdot_doc}}"><i class="fa fa-eye"></i></a>
                                  <a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip btn-delets" download="" title="Download" target="_blank" href="{{$usdot_doc}}"><i class="fa fa-download"></i></a>
                                @else
                                  <a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip btn-delets" title="View" href="javascript:void(0);"><i class="fa fa-eye"></i></a>
                                  <a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip btn-delets" title="Download" href="javascript:void(0);"><i class="fa fa-download"></i></a>
                                @endif

                                @if($is_usdot_doc_verified=='PENDING')
                                 <div class="view-bitto-righ">
                                  <a href="{{ $module_url_path.'/document_status?driver_id='.base64_encode($driver_id).'&vehicle_id='.base64_encode($vehicle_id).'&type=usdot_doc&status=approved' }}" class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip" onclick="return confirm_action(this,event,'Do you really want to approved Driving License ?')" title="Approve Driving License" ><i class="fa fa-check"></i></a>
                                  <a href="{{ $module_url_path.'/document_status?driver_id='.base64_encode($driver_id).'&vehicle_id='.base64_encode($vehicle_id).'&type=usdot_doc&status=rejected' }}" class="btn btn-circle btn-to-danger btn-bordered btn-fill show-tooltip btn-dangers" onclick="return confirm_action(this,event,'Do you really want to reject Driving License ?')" title="Reject Driving License" ><i class="fa fa-times"></i></a>
                                 </div>
                                 @elseif($is_usdot_doc_verified=='APPROVED')
                                  <span class="badge badge-success" style="width:100px">Approved</span>
                                @elseif($is_usdot_doc_verified=='REJECTED')
                                  <span class="badge badge-important" style="width:100px">Rejected</span>
                                @endif
                              </td>
                            </tr>
                          @endif

                           @if(isset($arr_data['vehicle_type_details']['is_mcdoc_required']) && $arr_data['vehicle_type_details']['is_mcdoc_required'] == '1')
                            <tr>
                              <th style="width: 30%">MC Document
                              </th>
                              <td>
                                @if($mc_doc!='')
                                  <a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip btn-delets" title="View" target="_blank" href="{{$mc_doc}}"><i class="fa fa-eye"></i></a>
                                  <a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip btn-delets" download="" title="Download" target="_blank" href="{{$mc_doc}}"><i class="fa fa-download"></i></a>
                                @else
                                  <a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip btn-delets" title="View" href="javascript:void(0);"><i class="fa fa-eye"></i></a>
                                  <a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip btn-delets" title="Download" href="javascript:void(0);"><i class="fa fa-download"></i></a>
                                @endif
                               
                                @if($is_mc_doc_verified=='PENDING')
                                 <div class="view-bitto-righ">
                                  <a href="{{ $module_url_path.'/document_status?driver_id='.base64_encode($driver_id).'&vehicle_id='.base64_encode($vehicle_id).'&type=mc_doc&status=approved' }}" class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip" onclick="return confirm_action(this,event,'Do you really want to approved Driving License ?')" title="Approve Driving License" ><i class="fa fa-check"></i></a>
                                  <a href="{{ $module_url_path.'/document_status?driver_id='.base64_encode($driver_id).'&vehicle_id='.base64_encode($vehicle_id).'&type=mc_doc&status=rejected' }}" class="btn btn-circle btn-to-danger btn-bordered btn-fill show-tooltip btn-dangers" onclick="return confirm_action(this,event,'Do you really want to reject Driving License ?')" title="Reject Driving License" ><i class="fa fa-times"></i></a>
                                 </div>
                                 @elseif($is_mc_doc_verified=='APPROVED')
                                  <span class="badge badge-success" style="width:100px">Approved</span>
                                @elseif($is_mc_doc_verified=='REJECTED')
                                  <span class="badge badge-important" style="width:100px">Rejected</span>
                                @endif
                              </td>
                            </tr>
                          @endif
                          

                            <tr>
                              <th style="width: 30%"> Vehicle Status
                              </th>
                              <td>

                                @if(isset($is_all_document_verified) && $is_all_document_verified == 'YES')
                                   <center><div >
                                    @if( isset($arr_data['is_verified']) && $arr_data['is_verified']==1)
                                    <a href="{{ $module_url_path.'/unverify_vehicle/'.base64_encode($arr_data['id']) }}" class="btn btn-unverify" onclick="return confirm_action(this,event,'Do you really want to Unverify this vehicle ?')" title="Unerify Vehicle" >Unverify Vehicle</a>
                                    @else
                                      <a href="{{ $module_url_path.'/verify_vehicle/'.base64_encode($arr_data['id']) }}" class="btn btn-verify" onclick="return confirm_action(this,event,'Do you really want to Verify this vehicle ?')" title="Verify Vehicle" >Verify Vehicle</a>
                                    @endif

                                   </div>
                                </center>
                                @else 
                                  <center><div >
                                  @if( isset($arr_data['is_verified']) && $arr_data['is_verified']==1)
                                    <a href="javascript:void(0);" class="btn btn-unverify" onclick="return showAlert('Please approve all the vehicle documents first.')" title="Unerify Vehicle" >UnVerify Vehicle</a>
                                  @else
                                    <a href="javascript:void(0);" class="btn btn-verify" onclick="return showAlert('Please approve all the vehicle documents first.')" title="Verify Vehicle" >Verify Vehicle</a>
                                  @endif
                                  </div>
                                </center>
                                @endif

                                {{-- @if($is_usdot_doc_verified=='PENDING')
                                 @elseif($is_usdot_doc_verified=='APPROVED')
                                  <span class="badge badge-success" style="width:100px">Approved</span>
                                @elseif($is_usdot_doc_verified=='REJECTED')
                                  <span class="badge badge-important" style="width:100px">Rejected</span>
                                @endif --}}
                              </td>
                            </tr>

                            @if(isset($arr_data['driver_car_details']['driver_details']) && count($arr_data['driver_car_details']['driver_details'])>0)
                              <tr>
                                <th style="width: 30%">Driver Approve/Unapprove Status
                                </th>
                                <td>
                                  <center><div>
                                  <?php
                                        $driver_url = url(config('app.project.admin_panel_slug')."/driver");
                                  ?>
                                  @if(isset($arr_data['driver_car_details']['driver_details']['account_status']) && $arr_data['driver_car_details']['driver_details']['account_status'] == 'unapproved')
                                    <a class="btn btn-verify" title="Click to Approve" href="{{ $driver_url.'/approve/'.base64_encode($driver_id) }}" onclick="return confirm_action(this,event,'Do you really want to Approve this record ?')" >Verify Driver</a>
                                  @elseif(isset($arr_data['driver_car_details']['driver_details']['account_status']) && $arr_data['driver_car_details']['driver_details']['account_status'] == 'approved')
                                    <a class="btn btn-unverify" title="Approved" href="{{ $driver_url.'/unapprove/'.base64_encode($driver_id) }}" onclick="return confirm_action(this,event,'Do you really want to unapprove this record ?')" >Unverify Driver</a>     
                                  @endif
                                  </div></center>
                                </td>
                              </tr>
                            @endif

                            {{-- if($data->account_status != null && $data->account_status == "unapproved")
                            {   
                              $build_status_check = '<a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip btn-dangers" title="Click to approve" href="'.$this->module_url_path.'/approve/'.base64_encode($data->id).'" 
                              onclick="return confirm_action(this,event,\'Do you really want to Approve this record ?\')" ><i class="fa fa-close"></i></a>';
                            }
                            elseif($data->account_status != null && $data->account_status == "approved")
                            {
                              $build_status_check = '<a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip" title="Approved" href="'.$this->module_url_path.'/unapprove/'.base64_encode($data->id).'" onclick="return confirm_action(this,event,\'Do you really want to unapprove this record ?\')" ><i class="fa fa-check"></i></a>';
                            } --}}

        
                    </tbody>
                  </table>  
                   <center><a class="btn btn-primary" href="{{ url($module_url_path) }}">Back</a></center>
                </div> 
              </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- END Main Content --> 
  @endsection
