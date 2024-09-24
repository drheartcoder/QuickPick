    @extends('company.layout.master')                


    @section('main_content')

    
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
                <i class="fa fa-home"></i>
                <a href="{{ url($company_panel_slug.'/dashboard') }}">Dashboard</a>
            </li>
            <span class="divider">
                <i class="fa fa-angle-right"></i>
            </span>
            <li>
                <i class="fa fa-motorcycle"></i>
                <a href="{{ $module_url_path }}">{{ $module_title or ''}}</a>
            </li>   
            <span class="divider">
                <i class="fa fa-angle-right"></i>
            </span>
            <li class="active"><i class="fa fa-plus-square-o"></i> {{ $page_title or ''}}</li>
        </ul>
    </div>
    <!-- END Breadcrumb -->


    <!-- BEGIN Main Content -->
    <div class="row">
      <div class="col-md-12">
          <div class="box {{ $theme_color }}">
            <div class="box-title">
              <h3>
                <i class="fa fa-plus-square-o"></i>
                {{ isset($page_title)?$page_title:"" }}
              </h3>
              <div class="box-tool">
                <a data-action="collapse" href="#"></a>
                <a data-action="close" href="#"></a>
              </div>
            </div>
            <div class="box-content">

          @include('company.layout._operation_status')  
           {!! Form::open([ 'url' => $module_url_path.'/update',
                                 'method'=>'POST',
                                 'enctype' =>'multipart/form-data',   
                                 'class'=>'form-horizontal', 
                                 'id'=>'validation-form' 
                                ]) !!} 

           {{ csrf_field() }}

            <div class="form-group" style="">
                <label class="col-sm-3 col-lg-2 control-label">Vehicle Type<i style="color: red;">*</i></label>
                 <input type="hidden" class="form-control" name="enc_id" value="{{$enc_id}}"/>
                <div class="col-sm-9 col-lg-4 controls" >
                    <select class="form-control" id="vehicle_type" data-rule-required="true" name="vehicle_type" onchange="check_is_usdot_required(this)">
                      <option value="">--Select Vehicle Type--</option>
                      @if(isset($arr_vehicle_types) && count($arr_vehicle_types)>0)
                        @foreach($arr_vehicle_types as $key => $value)
                          <option 
                                  @if(isset($arr_data['vehicle_type_id']) && $arr_data['vehicle_type_id'] == $value['id'])
                                    selected="" 
                                  @endif
                          is_usdot_required="{{ isset($value['is_usdot_required']) ? $value['is_usdot_required'] : '' }}"
                           is_mcdoc_required="{{ isset($value['is_mcdoc_required']) ? $value['is_mcdoc_required'] : '' }}"
                          value="{{ $value['id'] }}">{{ $value['vehicle_type'] }}</option>
                        @endforeach
                      @endif
                    </select>
                    <span class="help-block">{{ $errors->first('vehicle_type') }}</span>
                </div>
            </div>


            @if(isset($arr_data['driver_car_details']['driver_details']) && !empty($arr_data['driver_car_details']['driver_details']))
            <div class="form-group" style="">
                <label class="col-sm-3 col-lg-2 control-label">Previous Driver</label>
                <div class="col-sm-9 col-lg-4 controls" >
                    <input class="form-control" type="hidden" name="old_driver_id" value="{{$arr_data['driver_car_details']['driver_id']}}" readonly="readonly" >
                    <input class="form-control" type="text" name="old_driver_name" value="{{$arr_data['driver_car_details']['driver_details']['first_name'].' '.$arr_data['driver_car_details']['driver_details']['last_name']}}" readonly="readonly" >
                    <span class="help-block">{{ $errors->first('old_driver_id') }}</span>
                </div>
            </div>
            @endif
           

            <div class="form-group" style="">
                <label class="col-sm-3 col-lg-2 control-label">Select Driver<i style="color: red;">*</i></label>
                <div class="col-sm-9 col-lg-4 controls" >
                    <select class="form-control" id="driver_name" name="driver_id">
                      <option value="">--Select Driver--</option>
                    
                      @if(isset($arr_drivers) && count($arr_drivers)>0)
                        @foreach($arr_drivers as $key => $value)
                          <option value="{{ $value['id'] }}"  >{{ $value['first_name'].' '.$value['last_name'] }}</option>
                        @endforeach
                      @endif




                    </select>
                    <span class="help-block">{{ $errors->first('driver_name') }}</span>
                </div>
            </div>

            <div class="form-group" style="">
                <label class="col-sm-3 col-lg-2 control-label">Vehicle Brand<i style="color: red;">*</i></label>
                <div class="col-sm-9 col-lg-4 controls" >
                    <select class="form-control" id="vehicle_brand" data-rule-required="true" name="vehicle_brand" onchange="loadVehicleModel(this)">
                      <option value="">--Select Vehicle Brand--</option>
                      @if(isset($arr_vehicle_brands) && count($arr_vehicle_brands)>0)
                        @foreach($arr_vehicle_brands as $key => $value)
                          <option value="{{ $value['id'] }}" @if($arr_data['vehicle_brand_id']==$value['id']) selected @endif>{{ $value['name'] }}</option>
                        @endforeach
                      @endif
                    </select>
                    <span class="help-block">{{ $errors->first('vehicle_brand') }}</span>
                </div>
            </div>
            <div class="form-group" style="">
                <label class="col-sm-3 col-lg-2 control-label">Vehicle Model<i style="color: red;">*</i></label>
                <div class="col-sm-9 col-lg-4 controls" >
                    <select class="form-control" id="vehicle_model" data-rule-required="true" name="vehicle_model">
                       <option value="">--Select Vehicle Model--</option>
                      </select>
                    <span class="help-block">{{ $errors->first('vehicle_model') }}</span>
                </div>
            </div>

            <div class="form-group" style="">
                  <label class="col-sm-3 col-lg-2 control-label">Vehicle License Plate Number<i style="color: red;">*</i></label>
                  <div class="col-sm-9 col-lg-4 controls" >
                      <input type="text" class="form-control" name="vehicle_number" placeholder="Enter Vehicle License Plate Number" data-rule-required="true" maxlength="15" minlength="3"  value="{{ isset($arr_data['vehicle_number']) ? $arr_data['vehicle_number'] :'' }}" />
                      <span class="help-block">{{ $errors->first('vehicle_number') }}</span>
                  </div>
            </div>
           

            <div class="form-group">
                <label class="col-sm-3 col-lg-2 control-label"> Vehicle Image</label>
                <div class="col-sm-9 col-lg-10 controls">
                   <input type="file" name="vehicle_image" id="vehicle_image" onchange="check_file_format(this)"/>
                    <span class='help-block'>{{ $errors->first('vehicle_image') }}</span>  
                    {{isset($arr_data['vehicle_image']) ? $arr_data['vehicle_image'] :''}}</br>   
                     <i class="red"> Allowed only jpg | jpeg | png | pdf </i>
                </div>
          </div>

          <div class="form-group">
                <label class="col-sm-3 col-lg-2 control-label"> Registration Document</label>
                <div class="col-sm-9 col-lg-10 controls">
                   <input type="file" name="registration_doc" id="registration_doc" onchange="check_file_format(this)"/>
                    <span class='help-block'>{{ $errors->first('registration_doc') }}</span>  
                    {{isset($arr_data['registration_doc']) ? $arr_data['registration_doc'] :''}}</br>   
                     <i class="red"> Allowed only jpg | jpeg | png | pdf </i>
                </div>
          </div>

          <div class="form-group">
                <label class="col-sm-3 col-lg-2 control-label"> Proof of Inspection Document</label>
                <div class="col-sm-9 col-lg-10 controls">
                   <input type="file" name="proof_of_inspection" id="proof_of_inspection" onchange="check_file_format(this)"/>
                    <span class='help-block'>{{ $errors->first('proof_of_inspection') }}</span>  
                    {{isset($arr_data['proof_of_inspection_doc']) ? $arr_data['proof_of_inspection_doc'] :''}}</br>   
                     <i class="red"> Allowed only jpg | jpeg | png | pdf </i>
                </div>
          </div><br>

          <div class="form-group">
                <label class="col-sm-3 col-lg-2 control-label"> Vehicle Insurance Document</label>
                <div class="col-sm-9 col-lg-10 controls">
                   <input type="file" name="insurance_doc" id="insurance_doc" onchange="check_file_format(this)"/>
                    <span class='help-block'>{{ $errors->first('insurance_doc') }}</span>  
                    {{isset($arr_data['insurance_doc']) ? $arr_data['insurance_doc'] :''}}</br>   
                     <i class="red"> Allowed only jpg | jpeg | png | pdf </i>
                </div>
          </div><br>

          <div class="form-group">
                <label class="col-sm-3 col-lg-2 control-label"> DMV Driving Record Document</label>
                <div class="col-sm-9 col-lg-10 controls">
                   <input type="file" name="driving_doc" id="driving_doc" onchange="check_file_format(this)"/>
                    <span class='help-block'>{{ $errors->first('driving_doc') }}</span>  
                    {{isset($arr_data['dmv_driving_record']) ? $arr_data['dmv_driving_record'] :''}}</br>   
                     <i class="red"> Allowed only jpg | jpeg | png | pdf </i>
                </div>
          </div>

          <div class="form-group" id="user_doc_div">
                <label class="col-sm-3 col-lg-2 control-label"> Usdot Document</label>
                <div class="col-sm-9 col-lg-10 controls">
                   <input type="file" name="user_doc" id="user_doc" onchange="check_file_format(this)"/>
                    <span class='help-block'>{{ $errors->first('usdot_doc') }}</span>  
                    {{isset($arr_data['usdot_doc']) ? $arr_data['usdot_doc'] :''}}</br>   
                     <i class="red"> Allowed only jpg | jpeg | png | pdf </i>
                </div>
          </div>

          <div class="form-group" id="mc_doc_div">
                <label class="col-sm-3 col-lg-2 control-label"> Mc Document</label>
                <div class="col-sm-9 col-lg-10 controls">
                   <input type="file" name="mc_doc" id="mc_doc" onchange="check_file_format(this)"/>
                    <span class='help-block'>{{ $errors->first('mc_doc') }}</span>  
                    {{isset($arr_data['mc_doc']) ? $arr_data['mc_doc'] :''}}</br>   
                     <i class="red"> Allowed only jpg | jpeg | png | pdf </i>
                </div>
          </div>


            <div class="form-group">
              <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2">
               
                {!! Form::submit('Update',['class'=>'btn btn btn-primary','value'=>'true'])!!}
                &nbsp;
                <a class="btn" href="{{ $module_url_path }}">Back</a>
              </div>
            </div>
    
          {!! Form::close() !!}
      </div>
    </div>
  </div>
  
  <!-- END Main Content -->
<script type="text/javascript">
  
  function check_file_format(ref)
  {
      var ext = $(ref).val().split('.').pop().toLowerCase();
      if($.inArray(ext, ['jpg','jpeg','png','pdf']) == -1) {
          $(ref).val('');
      }
  }

  function check_is_usdot_required(ref){

        var is_usdot_required = $('option:selected', ref).attr('is_usdot_required');
        
        console.log(is_usdot_required);

        if(is_usdot_required!=undefined && is_usdot_required == '1')
        {
           // $('#usdot_doc').attr('data-rule-required', 'true');
           // $('#is_usdot_doc_required').val('YES');
            $('#user_doc_div').show();
        }
        else
        {
          //  $('#usdot_doc').attr('data-rule-required', 'false');
          //  $('#is_usdot_doc_required').val('NO');
            $('#user_doc_div').hide();
        }

    }

    function check_is_mcdoc_required(ref){

        var is_mcdoc_required = $('option:selected', ref).attr('is_mcdoc_required');
        
        console.log(is_mcdoc_required);

        if(is_mcdoc_required!=undefined && is_mcdoc_required == '1')
        {
          //  $('#mc_doc').attr('data-rule-required', 'true');
         //   $('#is_mcdoc_required').val('YES');
            $('#mc_doc_div').show();
        }
        else
        {
         //   $('#mc_doc').attr('data-rule-required', 'false');
         //   $('#is_mcdoc_required').val('NO');
            $('#mc_doc_div').hide();
        }

    }

  var vehicle_model_url = '{{url('/api/common_data/get_vehicle_model')}}';

  var vehicle_brand_id = '{{isset($arr_data['vehicle_brand_id']) ? $arr_data['vehicle_brand_id'] : 0 }}';
  var vehicle_model_id = '{{isset($arr_data['vehicle_model_id']) ? $arr_data['vehicle_model_id'] : 0 }}';

 $(document).ready(function()
    {         

        
            //var vehicle_brand_id = $(ref).val();

            if(vehicle_brand_id && vehicle_brand_id!="" && vehicle_brand_id!=0){

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
                                    option+='<option '+is_selected+' value="'+value.id+'">'+value.name+'</option>';
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
        
 $('#validation-form').submit(function()
 {
  var admin_per_kilometer_charge  =  parseFloat($("#admin_per_kilometer_charge").val());
  var driver_per_kilometer_charge = parseFloat($("#driver_per_kilometer_charge").val());
  if (admin_per_kilometer_charge!='' && driver_per_kilometer_charge!='' ) 
  {
    if(driver_per_kilometer_charge >= admin_per_kilometer_charge)
    {
       $('#errordriver').html("Driver Per miles charge must be less than admin per miles charge");
       return false;
    } 
  }
});
</script> 
@stop                    
