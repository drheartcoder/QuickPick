    @extends('company.layout.master')                


    @section('main_content')
    <!-- BEGIN Page Title -->

    <link rel="stylesheet" type="text/css" href="{{ url('/') }}/assets/data-tables/latest/dataTables.bootstrap.min.css">
    <div class="page-title">
        <div>

        </div>
    </div>
    <!-- END Page Title -->
  
    <!-- BEGIN Breadcrumb -->
    <div id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="fa fa-home"></i>
                <a href="{{ url($company_panel_slug.'/dashboard') }}">Dashboard</a>
            </li>
            <span class="divider">
                <i class="fa fa-angle-right"></i>
                <i class="fa fa-motorcycle"></i>           
            </span> 
             <li class="active">{{ $module_title or ''}}</li>
           
        </ul>
      </div>
    <!-- END Breadcrumb -->

    <!-- BEGIN Main Content -->
    <div class="row">
      <div class="col-md-12">

          <div class="box">
            <div class="box-title">
              <h3>
                <i class="fa fa-list"></i>
                {{ isset($page_title)?$page_title:"" }}
            </h3>
            <div class="box-tool">
                <a data-action="collapse" href="#"></a>
                <a data-action="close" href="#"></a>
            </div>
        </div>
        <div class="box-content">
        
          @include('company.layout._operation_status')  
          

          {!! Form::open([ 'url' => $module_url_path.'/multi_action',
                                 'method'=>'POST',
                                 'enctype' =>'multipart/form-data',   
                                 'class'=>'form-horizontal', 
                                 'id'=>'frm_manage' 
                                ]) !!} 

            {{ csrf_field() }}

            <div class="col-md-10">
            

            <div id="ajax_op_status">
                
            </div>
            <div class="alert alert-danger" id="no_select" style="display:none;"></div>
            <div class="alert alert-warning" id="warning_msg" style="display:none;"></div>
          </div>
          <div class="btn-toolbar pull-right clearfix">

          
          <div class="btn-group">
                  
           <a href="{{ $module_url_path.'/create' }}" class="btn btn-primary btn-add-new-records" title="Add {{ str_singular($module_title) }}">Add {{ str_singular($module_title) }}</a> 
          </div>
  
      
            <div class="btn-group"> 
                  
                <a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip btn-dangers" 
                   title="Lock" 
                   href="javascript:void(0)"
                   onclick="javascript : return check_multi_action('frm_manage','deactivate');" 
                   style="text-decoration:none;">
                   <i class="fa fa-lock"></i>
                </a> 

                <a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip" 
                   title="Unlock" 
                   href="javascript:void(0)"
                   onclick="javascript : return check_multi_action('frm_manage','activate');" 
                   style="text-decoration:none;">
                   <i class="fa fa-unlock"></i>
                </a> 
          
                <a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip btn-delets" 
                   title="Multiple Delete" 
                   href="javascript:void(0);" 
                   onclick="javascript : return check_multi_action('frm_manage','delete');"  
                   style="text-decoration:none;">
                   <i class="fa fa-trash-o"></i>
                </a>
          
                <a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip refrash-btns" 
                   title="Refresh" 
                   href="javascript:void(0)"
                   onclick="javascript:location.reload();" 
                   style="text-decoration:none;">
                   <i class="fa fa-repeat"></i>
                </a> 
            </div>
          </div>
          <br/>
          <div class="clearfix"></div>
          <div class="table-responsive" style="border:0">

            <input type="hidden" name="multi_action" value="" />

            <table class="table table-advance"  id="table_module" >
              <thead>
                <tr>
                    <th style="width:18px"> 
                    <div class="check-box">
                            <input type="checkbox" class="filled-in" name="selectall" id="selectall" />
                            <label for="selectall"></label>
                    </div></th>
                  <th>Vehicle Type</th>
                  <th>Vehicle Brand</th>
                  <th>Vehicle Model</th>
                  <th>Vehicle Number</th>             
                  <th>Driver Name</th>
                  
                  <th>Vehicle Image</th>
                  <th>Registration Doc</th>
                  <th>Proof Of Inspection Doc</th>
                  <th>Insurance Doc</th>
                  <th>Dmv Driving Record</th>
                  <th>Usdot Doc</th>
                  <th>McDoc Document</th>

                   <th>Status</th> 
                   <th width="150px">Action</th>
                </tr>
              </thead>
              <tbody>
          
                @if(isset($arr_vehicle) && sizeof($arr_vehicle)>0)
                  @foreach($arr_vehicle as $data)
                  <tr>               
                    <td>
                    <div class="check-box"><input type="checkbox" class="filled-in case" name="checked_record[]"  id="mult_change_{{ base64_encode($data['id']) }}" value="{{ base64_encode($data['id']) }}" /><label for="mult_change_{{ base64_encode($data['id']) }}"></label></div> 
                    </td>
                   
                    <td> {{ isset($data['vehicle_type_details']['vehicle_type']) ? $data['vehicle_type_details']['vehicle_type'] : '-' }} </td> 

                     <td > {{ isset($data['vehicle_brand_details']['name']) ? $data['vehicle_brand_details']['name'] : '-' }} </td> 
                    <td > {{ isset($data['vehicle_model_details']['name']) ? $data['vehicle_model_details']['name'] : '-' }} </td> 
                    
                    <td> {{ isset($data['vehicle_number']) ? $data['vehicle_number'] : '-' }} </td> 

                    <td> {{ isset($data['car_driver_details']['0']['first_name']) ? $data['car_driver_details']['0']['first_name'] : '-' }} {{ isset($data['car_driver_details']['0']['last_name']) ? $data['car_driver_details']['0']['last_name'] : '-' }} </td>
                    
                    <?php
                            
                            $vehicle_image = $registration_doc = $proof_of_inspection_doc = $insurance_doc = $dmv_driving_record = $usdot_doc = $mc_doc = '';

                            if(isset($data['vehicle_image']) && $data['vehicle_image']!=''){
                                if(file_exists($vehicle_doc_base_path.$data['vehicle_image'])){
                                  $vehicle_image = $vehicle_doc_public_path.$data['vehicle_image'];
                                }
                            }
                            if(isset($data['registration_doc']) && $data['registration_doc']!=''){
                                if(file_exists($vehicle_doc_base_path.$data['registration_doc'])){
                                  $registration_doc = $vehicle_doc_public_path.$data['registration_doc'];
                                }
                            }

                            if(isset($data['proof_of_inspection_doc']) && $data['proof_of_inspection_doc']!=''){
                                if(file_exists($vehicle_doc_base_path.$data['proof_of_inspection_doc'])){
                                  $proof_of_inspection_doc = $vehicle_doc_public_path.$data['proof_of_inspection_doc'];
                                }
                            }

                            if(isset($data['insurance_doc']) && $data['insurance_doc']!=''){
                                if(file_exists($vehicle_doc_base_path.$data['insurance_doc'])){
                                  $insurance_doc = $vehicle_doc_public_path.$data['insurance_doc'];
                                }
                            }
                            if(isset($data['dmv_driving_record']) && $data['dmv_driving_record']!=''){
                                if(file_exists($vehicle_doc_base_path.$data['dmv_driving_record'])){
                                  $dmv_driving_record = $vehicle_doc_public_path.$data['dmv_driving_record'];
                                }
                            }
                            if(isset($data['usdot_doc']) && $data['usdot_doc']!=''){
                                if(file_exists($vehicle_doc_base_path.$data['usdot_doc'])){
                                  $usdot_doc = $vehicle_doc_public_path.$data['usdot_doc'];
                                }
                            }
                            if(isset($data['mc_doc']) && $data['mc_doc']!=''){
                                if(file_exists($vehicle_doc_base_path.$data['mc_doc'])){
                                  $mc_doc = $vehicle_doc_public_path.$data['mc_doc'];
                                }
                            }
                        ?>
                    <td>
                      @if($vehicle_image!='')
                        <a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip btn-delets" title="View" target="_blank" href="{{$vehicle_image}}"><i class="fa fa-eye"></i></a>
                        <a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip btn-delets" download="" title="Download" target="_blank" href="{{$vehicle_image}}"><i class="fa fa-download"></i></a>
                      @else
                        <a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip btn-delets" title="View" href="javascript:void(0);"><i class="fa fa-eye"></i></a>
                        <a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip btn-delets" title="Download" href="javascript:void(0);"><i class="fa fa-download"></i></a>
                      @endif
                    </td>           
                    <td>
                      @if($registration_doc!='')
                        <a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip btn-delets" title="View" target="_blank" href="{{$registration_doc}}"><i class="fa fa-eye"></i></a>
                        <a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip btn-delets" download="" title="Download" target="_blank" href="{{$registration_doc}}"><i class="fa fa-download"></i></a>
                      @else
                        <a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip btn-delets" title="View" href="javascript:void(0);"><i class="fa fa-eye"></i></a>
                        <a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip btn-delets" title="Download" href="javascript:void(0);"><i class="fa fa-download"></i></a>
                      @endif
                    </td>  
                    
                    <td>
                      @if($proof_of_inspection_doc!='')
                        <a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip btn-delets" title="View" target="_blank" href="{{$proof_of_inspection_doc}}"><i class="fa fa-eye"></i></a>
                        <a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip btn-delets" download="" title="Download" target="_blank" href="{{$proof_of_inspection_doc}}"><i class="fa fa-download"></i></a>
                      @else
                        <a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip btn-delets" title="View" href="javascript:void(0);"><i class="fa fa-eye"></i></a>
                        <a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip btn-delets" title="Download" href="javascript:void(0);"><i class="fa fa-download"></i></a>
                      @endif
                    </td>    

                    <td>
                      @if($insurance_doc!='')
                        <a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip btn-delets" title="View" target="_blank" href="{{$insurance_doc}}"><i class="fa fa-eye"></i></a>
                        <a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip btn-delets" download="" title="Download" target="_blank" href="{{$insurance_doc}}"><i class="fa fa-download"></i></a>
                      @else
                        <a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip btn-delets" title="View" href="javascript:void(0);"><i class="fa fa-eye"></i></a>
                        <a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip btn-delets" title="Download" href="javascript:void(0);"><i class="fa fa-download"></i></a>
                      @endif
                    </td>           
                    <td>
                      @if($dmv_driving_record!='')
                        <a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip btn-delets" title="View" target="_blank" href="{{$dmv_driving_record}}"><i class="fa fa-eye"></i></a>
                        <a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip btn-delets" download="" title="Download" target="_blank" href="{{$dmv_driving_record}}"><i class="fa fa-download"></i></a>
                      @else
                        <a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip btn-delets" title="View" href="javascript:void(0);"><i class="fa fa-eye"></i></a>
                        <a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip btn-delets" title="Download" href="javascript:void(0);"><i class="fa fa-download"></i></a>
                      @endif
                    </td>           
                    <td>
                      @if($usdot_doc!='')
                        <a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip btn-delets" title="View" target="_blank" href="{{$usdot_doc}}"><i class="fa fa-eye"></i></a>
                        <a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip btn-delets" download="" title="Download" target="_blank" href="{{$usdot_doc}}"><i class="fa fa-download"></i></a>
                      @else
                        <a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip btn-delets" title="View" href="javascript:void(0);"><i class="fa fa-eye"></i></a>
                        <a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip btn-delets" title="Download" href="javascript:void(0);"><i class="fa fa-download"></i></a>
                      @endif
                    </td>  
                     <td>
                      @if($mc_doc!='')
                        <a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip btn-delets" title="View" target="_blank" href="{{$mc_doc}}"><i class="fa fa-eye"></i></a>
                        <a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip btn-delets" download="" title="Download" target="_blank" href="{{$mc_doc}}"><i class="fa fa-download"></i></a>
                      @else
                        <a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip btn-delets" title="View" href="javascript:void(0);"><i class="fa fa-eye"></i></a>
                        <a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip btn-delets" title="Download" href="javascript:void(0);"><i class="fa fa-download"></i></a>
                      @endif
                    </td>           

                    
                    <td>
                        @if($data['is_active']==1)
                        <a href="{{ $module_url_path.'/deactivate/'.base64_encode($data['id']) }}" class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip" onclick="return confirm_action(this,event,'Do you really want to deactivate this record ?')" title="Deactivate" ><i class="fa fa-unlock"></i></a>
                        @else
                        <a href="{{ $module_url_path.'/activate/'.base64_encode($data['id']) }}" class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip btn-dangers" onclick="return confirm_action(this,event,'Do you really want to activate this record ?')" title="Activate" ><i class="fa fa-lock"></i></a>
                        @endif
                    </td>
                    <td style="width: 100px;"> 
                        
                        <a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip btn-delets" href="{{ $module_url_path.'/view/'.base64_encode($data['id']) }}" title="View">
                          <i class="fa fa-eye" ></i>
                        </a>  

                        <a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip btn-delets" href="{{ $module_url_path.'/edit/'.base64_encode($data['id']) }}" title="Edit">
                          <i class="fa fa-edit" ></i>
                         </a>  
                        <a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip btn-delets" href="{{ $module_url_path.'/delete/'.base64_encode($data['id']) }}"  
                           onclick="return confirm_action(this,event,'Do you really want to delete this record ?')" 
                           title="Delete">
                          <i class="fa fa-trash" ></i>  
                        </a>   
                    </td>                              
                  </tr>
                  @endforeach
                @endif
                 
              </tbody>
            </table>
          </div>
        <div> </div>
         
          {!! Form::close() !!} 
      </div>
  </div>
</div>
<script type="text/javascript">
    
    $(document).ready(function() {
        $('#table_module').DataTable();
    });
</script>
<!-- END Main Content -->

@stop                    


