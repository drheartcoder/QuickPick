    @extends('company.layout.master')                


    @section('main_content')
    <!-- BEGIN Page Title -->

    <link rel="stylesheet" type="text/css" href="{{ url('/') }}/assets/data-tables/latest/dataTables.bootstrap.min.css">
    <div class="page-title">
        <div>

        </div>
    </div>
    <!-- END Page Title -->
    {{-- dd($arr_auth_user) --}}

    <!-- BEGIN Breadcrumb -->
    <div id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="fa fa-home"></i>
                <a href="{{ url($company_panel_slug.'/dashboard') }}">Dashboard</a>
            </li>
            <span class="divider">
                <i class="fa fa-angle-right"></i>
                <i class="fa fa-car"></i>           
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
               <a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip refrash-btns" 
                   title="Refresh" 
                   href="javascript:void(0)"
                   onclick="javascript:location.reload();" 
                   style="text-decoration:none;">
                   <i class="fa fa-repeat"></i>
                </a> 
            </div>
          </div>
          <br/><br/>
          <div class="clearfix"></div>
          <div class="table-responsive" style="border:0">

            <input type="hidden" name="multi_action" value="" />

            <table class="table table-advance"  id="table_module" >
              <thead>
                <tr>
                  <th>Driver Name</th> 
                  <th>Email/Mobile No</th>
                  <th>Vehicle Type</th>
                  <th>Vehicle Brand</th>
                  <th>Vehicle Model</th>
                  <th>Vehicle Number</th>
                  <th>Vehicle Details</th>
                  <th width="150px">Action</th>
                </tr>
              </thead>
              <tbody>
          
                @if(isset($arr_data) && sizeof($arr_data)>0)
                  @foreach($arr_data as $data)
                  
                  {{-- {{dd($data)}} --}}

                  <tr>
                    <td> 
                          @if(isset($data['driver_details']) && sizeof($data['driver_details'])>0)
                            {{ isset($data['driver_details']['first_name']) ? $data['driver_details']['first_name'] : '' }} {{ isset($data['driver_details']['last_name']) ? $data['driver_details']['last_name'] : '' }}
                          @else
                            -
                          @endif
                    </td> 
                    
                    <td> 
                          @if(isset($data['driver_details']) && sizeof($data['driver_details'])>0)
                            {{ isset($data['driver_details']['email']) ? $data['driver_details']['email'] : '' }} <br> 
                            {{ isset($data['driver_details']['country_code']) ? $data['driver_details']['country_code'] : '' }}
                            {{ isset($data['driver_details']['mobile_no']) ? $data['driver_details']['mobile_no'] : '' }}
                          @else
                            -
                          @endif
                    </td> 

                    <td> 
                          @if(isset($data['vehicle_details']) && sizeof($data['vehicle_details'])>0)
                            @if(isset($data['vehicle_details']['vehicle_type_details']) && sizeof($data['vehicle_details']['vehicle_type_details'])>0) 
                                {{ isset($data['vehicle_details']['vehicle_type_details']['vehicle_type']) ? $data['vehicle_details']['vehicle_type_details']['vehicle_type'] : '' }}
                            @endif
                          @else
                            -
                          @endif
                    </td> 

                    <td> 
                          @if(isset($data['vehicle_details']) && sizeof($data['vehicle_details'])>0)
                            {{ isset($data['vehicle_details']['vehicle_model_details']['name']) ? $data['vehicle_details']['vehicle_model_details']['name'] : '' }}  
                          @else
                            -
                          @endif
                    </td>

                    <td> 
                          @if(isset($data['vehicle_details']) && sizeof($data['vehicle_details'])>0)
                            {{ isset($data['vehicle_details']['vehicle_brand_details']['name']) ? $data['vehicle_details']['vehicle_brand_details']['name'] : '' }}  
                          @else
                            -
                          @endif
                    </td>

                    <td> 
                          @if(isset($data['vehicle_details']) && sizeof($data['vehicle_details'])>0)
                            {{ isset($data['vehicle_details']['vehicle_number']) ? $data['vehicle_details']['vehicle_number'] : '' }}  
                          @else
                            -
                          @endif
                    </td>

                    <td>
                    <?php 
                        $vehicle_view_url  = 'javascript:void(0);';
                        if(isset($data['vehicle_id']) && $data['vehicle_id']!=0){
                          $vehicle_view_url  = url('/company/vehicle/view').'/'.base64_encode($data['vehicle_id']);
                        }
                    ?>
                    
                    <a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip btn-delets" href="{{ $vehicle_view_url }}" title="View Vehicle Details">
                                  <i class="fa fa-eye" ></i>
                                </a> 


                  </td>

                    <td style="width: 100px;"> 
                        @if($data['is_car_assign']==0)
                            <a href="javascript:void(0);" data-id="{{base64_encode($data['id'])}}" onclick="openAssignCarModal(this);" class="btn btn-success"  title="Assign vehicle" >Assign</a>
                          @else
                            <a href="javascript:void(0);" data-id="{{base64_encode($data['id'])}}" onclick="openAssignCarModal(this);" class="btn btn-primary btn-bordered"  title="Remove assigned Car" >Update</a>
                            <a href="{{ $module_url_path.'/remove_car/'.base64_encode($data['id']) }}" class="btn btn-danger" onclick="return confirm_action(this,event,'Do you really want to remove car for this driver ?')"  title="Remove Vehicle" >Remove</a>
                        @endif
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

<style>
    #assign-car .control-label{margin-top: 5px;}
    #assign-car .modal-header{padding: 8px 15px;}
</style>


<div id="assign-car" class="modal fade">
  <div class="modal-dialog">
    <form id="validation-form" action="{{$module_url_path}}/assign_car" method="POST">
      {{ csrf_field() }}
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h3 id="myModalLabel2">Assign Vehicle</h3>
            </div>
            <div class="modal-body">
                <input type="hidden" name="driver_car_id" id="driver_car_id">
                <div class="form-group" style="">
                    <label class="col-sm-3 col-lg-2 control-label">Vehicle<i style="color: red;">*</i></label>
                    <div class="col-sm-9 col-lg-10 controls" >
                        <select class="form-control"  name="vehicle_id" data-rule-required="true" id="vehicle_id">
                          <option value="">--Select Vehicle--</option>
                        </select>
                    </div>
                    <div class="clearfix"></div>
                </div>
                
                {{-- <div class="form-group" style="">
                    <label class="col-sm-3 col-lg-2 control-label">Vehicle<i style="color: red;">*</i></label>
                    <div class="col-sm-9 col-lg-10 controls" >
                        <input class="form-control input-sm" placeholder="" aria-controls="table_module" type="text">
                    </div>
                    <div class="clearfix"></div>
                </div> --}}
            </div>
            <div class="modal-footer">
                <button class="btn" data-dismiss="modal" aria-hidden="true">Cancel</button>
                <button class="btn btn-primary" type="submit">Submit</button>
            </div>
        </div>
      </form>
  </div>
</div>

<script type="text/javascript">
    
    var module_url_path = '{{$module_url_path}}';

    $(document).ready(function() {
        $('#table_module').DataTable();
    });

    function openAssignCarModal(ref){
        var enc_id = $(ref).attr('data-id');
        if(enc_id!=undefined){
          $.ajax({
                url:module_url_path+'/get_cars?enc_id='+enc_id,
                type:'GET',
                data:'flag=true',
                dataType:'json',
                beforeSend:function(){
                  $('select[id="vehicle_id"]').attr('readonly','readonly');
                },
                success:function(response)
                {
                    if(response.status=="success")
                    {
                        $('select[id="vehicle_id"]').removeAttr('readonly');

                        if(typeof(response.arr_vehicle) == "object")
                        {
                          var option = '<option value="">--Select Vehicle--</option>'; 
                          $(response.arr_vehicle).each(function(index,vehicle){
                              option+='<option value="'+vehicle.id+'">'+vehicle.vehicle_full_name+'</option>';
                          });
                          $('select[id="vehicle_id"]').html(option);
                        }
                        
                        $('#driver_car_id').val(enc_id);
                        $('#assign-car').modal('show');

                    }
                    else
                    {
                        swal("Oops..","All vehicles are assigned to each driver.");
                    }
                  return false;
                }   
        });
        }
    }

</script>
<!-- END Main Content -->

@stop                    


