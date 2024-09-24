    @extends('admin.layout.master')                


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
                <a href="{{ url($admin_panel_slug.'/dashboard') }}">Dashboard</a>
            </li>
            <span class="divider">
                <i class="fa fa-angle-right"></i>
            </span>
            <li>
                <i class="fa fa-car"></i>
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

          @include('admin.layout._operation_status')  
           {!! Form::open([ 'url' => $module_url_path.'/store',
                                 'method'=>'POST',
                                 'enctype' =>'multipart/form-data',   
                                 'class'=>'form-horizontal', 
                                 'id'=>'validation-form' 
                                ]) !!} 

           {{ csrf_field() }}

            <div class="form-group">
                <label class="col-sm-3 col-lg-2 control-label"> Profile Image</label>
                <div class="col-sm-9 col-lg-10 controls">
                   <div class="fileupload fileupload-new" data-provides="fileupload">
                      <div class="fileupload-new img-thumbnail" style="width: 200px; height: 150px;">
                           <img src="{{url('/').'/uploads/default-profile.png' }}">
                      </div>
                      <div class="fileupload-preview fileupload-exists img-thumbnail" style="max-width: 200px; max-height: 150px; line-height: 20px;"></div>
                      <div>
                         <span class="btn btn-default btn-file"><span class="fileupload-new" >Select Image</span> 
                         <span class="fileupload-exists">Change</span>
                         
                         {!! Form::file('profile_image',['id'=>'image_proof','class'=>'file-input validate-image']) !!}

                         </span> 
                         <a href="#" class="btn btn-default fileupload-exists" data-dismiss="fileupload">Remove</a>
                         <span class='help-block'>{{ $errors->first('profile_image') }}</span>
                      </div>
                   </div>
                   <i class="red"> {!! image_validate_note(250,250) !!} </i>
                </div>
            </div>
            
            <div class="form-group" style="margin-top: 25px;">
                  <label class="col-sm-3 col-lg-2 control-label">First Name<i style="color: red;">*</i></label>
                  <div class="col-sm-9 col-lg-4 controls" >
                      <input type="text" class="form-control" name="first_name" maxlength="40" placeholder="Enter First Name" data-rule-required="true" value="{{ old('first_name') }}"  />
                      <span class="help-block">{{ $errors->first('first_name') }}</span>
                  </div>
            </div>

            <div class="form-group" style="">
                  <label class="col-sm-3 col-lg-2 control-label">Last Name<i style="color: red;">*</i></label>
                  <div class="col-sm-9 col-lg-4 controls" >
                      <input type="text" class="form-control" name="last_name" placeholder="Enter Last Name" maxlength="40" data-rule-required="true" value="{{ old('last_name') }}" />
                      <span class="help-block">{{ $errors->first('last_name') }}</span>
                  </div>
            </div>

            <div class="form-group" style="">
                  <label class="col-sm-3 col-lg-2 control-label">Address<i style="color: red;">*</i></label>
                  <div class="col-sm-9 col-lg-4 controls" >
                      <input type="text" class="form-control" name="address" id="autocomplete" data-rule-required="true"  placeholder="Enter Address" value="{{ old('address') }}" />
                      <div class="help-block">{{ $errors->first('address') }}</div>
                  </div>
            </div>

             <div class="geo-details">
                <div class="form-group" style="">
                    <label class="col-sm-3 col-lg-2 control-label">Post Code<i style="color: red;">*</i></label>
                      <div class="col-sm-9 col-lg-4 controls" >
                          <input type="text" class="form-control" name="post_code" id="postal_code" onkeypress="return isNumberKey(event)" placeholder="Enter Post Code" data-rule-digits="true" minlength="4" maxlength="12" data-rule-required="true"/>
                          <span class="help-block">{{ $errors->first('post_code') }}</span>
                      </div>
                </div>
                <input name="country_name" id="country" type="hidden" />
                <input name="state_name" id="administrative_area_level_1" type="hidden" />
                <input name="city_name" id="locality" type="hidden" />
                <input name="lat" id="lat" type="hidden" />
                <input name="long" id="lng" type="hidden" />
            </div>

            <div class="form-group" style="">
                  <label class="col-sm-3 col-lg-2 control-label">Email Address<i style="color: red;">*</i></label>
                  <div class="col-sm-9 col-lg-4 controls" >
                      <input type="text" class="form-control" name="email" placeholder="Email Address" data-rule-required="true" data-rule-email="true" value="{{ old('email') }}" />
                      <span class="help-block">{{ $errors->first('email') }}</span>
                  </div>
            </div>

            {{-- <div class="form-group" style="">
                  <label class="col-sm-3 col-lg-2 control-label">Company Name</label>
                  <div class="col-sm-9 col-lg-4 controls" >
                      <input type="text" class="form-control" name="company_name" placeholder="Enter Company Name" value="{{ old('company_name') }}" />

                      <div class="help-block">{{ $errors->first('company_name') }}</div>
                  </div>
            </div> --}}

            <div class="form-group" style="">
                  <label class="col-sm-3 col-lg-2 control-label">Mobile Number<i style="color: red;">*</i></label>
                  <div class="col-sm-9 col-lg-1 controls" >
                      <input type="text" class="form-control" id="country_code" name="country_code" placeholder="Code" readonly="" />
                      <span class="help-block">{{ $errors->first('country_code') }}</span>
                  </div>
                  <div class="col-sm-9 col-lg-3 controls" >
                      <input type="text" class="form-control" name="mobile_no" placeholder="Enter Mobile Number." data-rule-required="true" data-rule-digits="true" onkeypress="return isNumberKey(event)" {{-- data-rule-pattern="\+[1-9]{1,3}[0-9]{9,15}" --}} maxlength="16" minlength="8" value="{{ old('mobile_no') }}" />
                      <span class="help-block">{{ $errors->first('mobile_no') }}</span>
                  </div>
            </div>

            <div class="form-group" style="">
                  <label class="col-sm-3 col-lg-2 control-label">Password<i style="color: red;">*</i></label>
                  <div class="col-sm-9 col-lg-4 controls" >
                      <input type="password" class="form-control" name="password" id="password" data-rule-pattern="(^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[$@$!%*?&])[A-Za-z\d$@$!%*?&]{8,})" data-msg-pattern="Min. length should be 8 containing uppercase letter, lowercase letter, number and special character." placeholder="Enter Password" data-rule-required="true" value="{{ old('password') }}" minlength="8" />
                      <span class="help-block">{{ $errors->first('password') }}</span>
                  </div>
            </div>
            
            <div class="form-group" style="">
                  <label class="col-sm-3 col-lg-2 control-label">Confirm Password<i style="color: red;">*</i></label>
                  <div class="col-sm-9 col-lg-4 controls" >
                      <input type="password" class="form-control" name="confirm_password" placeholder="Enter Confirm Password" data-rule-required="true" data-rule-equalto="#password" value="{{ old('confirm_password') }}" minlength="6" />
                      <span class="help-block">{{ $errors->first('confirm_password') }}</span>
                  </div>
            </div>
            
           
            

            <div class="form-group">
                <label class="col-sm-3 col-lg-2 control-label"> Driving License</label>
                <div class="col-sm-9 col-lg-10 controls">
                   <input type="file" name="driving_license" id="driving_license" data-rule-required="true" onchange="check_file_format(this)"/>
                    <span class='help-block'>{{ $errors->first('driving_license') }}</span>  
                     <i class="red"> Allowed only jpg | jpeg | png | pdf </i>
                </div>
            </div>

            <br>

            <div class="form-group" style="">
              <label class="col-sm-3 col-lg-2 control-label">Own Vehicle <i style="color: red;">*</i></label>
              <div class="col-sm-5 col-lg-2 controls" >
                    <input type="radio" name="is_own_vehicle" id="yes_own_vehicle" value="1"/> YES
                <label for="filled-in-box"></label>
              </div>
              <div class="col-sm-5 col-lg-2 controls" >
                    <input type="radio" name="is_own_vehicle" checked="" id="no_own_vehicle" value="0"/> NO
                <label for="filled-in-box"></label>
              </div> 
              <span class="help-block">{{ $errors->first('is_own_vehicle') }}</span>
            </div><br>

            <div id="own_vehicle_details_div" style="display: none;">
            

              <div class="form-group" style="">
                  <label class="col-sm-3 col-lg-2 control-label">Vehicle Type<i style="color: red;">*</i></label>
                  <div class="col-sm-9 col-lg-4 controls" >
                      <select class="form-control" id="vehicle_type" data-rule-required="false" name="vehicle_type" onchange="check_is_usdot_required(this);check_is_mcdoc_required(this)">
                        <option value="">--Select Vehicle Type--</option>
                        @if(isset($arr_vehicle_types) && count($arr_vehicle_types)>0)
                          @foreach($arr_vehicle_types as $key => $value)
                            <option 
                            is_usdot_required="{{ isset($value['is_usdot_required']) ? $value['is_usdot_required'] : '' }}"
                            is_mcdoc_required="{{ isset($value['is_mcdoc_required']) ? $value['is_mcdoc_required'] : '' }}"
                            value="{{ $value['id'] }}">{{ $value['vehicle_type'] }}</option>
                          @endforeach
                        @endif
                      </select>
                      <span class="help-block">{{ $errors->first('vehicle_type') }}</span>
                  </div>
              </div>

              <div class="form-group" style="">
                  <label class="col-sm-3 col-lg-2 control-label">Vehicle Brand<i style="color: red;">*</i></label>
                  <div class="col-sm-9 col-lg-4 controls" >
                      <select class="form-control" id="vehicle_brand" data-rule-required="false" name="vehicle_brand" onchange="loadVehicleModel(this)">
                        <option value="">--Select Vehicle Brand--</option>
                        @if(isset($arr_vehicle_brands) && count($arr_vehicle_brands)>0)
                          @foreach($arr_vehicle_brands as $key => $value)
                            <option value="{{ $value['id'] }}">{{ $value['name'] }}</option>
                          @endforeach
                        @endif
                      </select>
                      <span class="help-block">{{ $errors->first('vehicle_brand') }}</span>
                  </div>
              </div>
              
              <div class="form-group" style="">
                  <label class="col-sm-3 col-lg-2 control-label">Vehicle Model<i style="color: red;">*</i></label>
                  <div class="col-sm-9 col-lg-4 controls" >
                      <select class="form-control" id="vehicle_model" data-rule-required="false" name="vehicle_model">
                         <option value="">--Select Vehicle Model--</option>
                      </select>
                      <span class="help-block">{{ $errors->first('vehicle_model') }}</span>
                  </div>
              </div>

              <div class="form-group" style="">
                    <label class="col-sm-3 col-lg-2 control-label">Vehicle License Plate Number<i style="color: red;">*</i></label>
                    <div class="col-sm-9 col-lg-4 controls" >
                        <input type="text" class="form-control" name="vehicle_number" id="vehicle_number" placeholder="Enter Vehicle License Plate Number" data-rule-required="false" value="{{ old('vehicle_number') }}" />
                        <span class="help-block">{{ $errors->first('vehicle_number') }}</span>
                    </div>
              </div>
              <br>
              <div class="form-group">
                  <label class="col-sm-3 col-lg-2 control-label"> Vehicle Image</label>
                  <div class="col-sm-9 col-lg-10 controls">
                     <input type="file" name="vehicle_image" id="vehicle_image" data-rule-required="false" onchange="check_file_format(this)" />
                      <span class='help-block'>{{ $errors->first('vehicle_image') }}</span>  
                       <i class="red"> Allowed only jpg | jpeg | png | pdf </i>
                  </div>
              </div>
              <br>
              <div class="form-group">
                  <label class="col-sm-3 col-lg-2 control-label"> Registration Document</label>
                  <div class="col-sm-9 col-lg-10 controls">
                     <input type="file" name="registration_doc" id="registration_doc" data-rule-required="false" onchange="check_file_format(this)"/>
                      <span class='help-block'>{{ $errors->first('registration_doc') }}</span>  
                       <i class="red"> Allowed only jpg | jpeg | png | pdf </i>
                  </div>
              </div>
              <br>
              <div class="form-group">
                  <label class="col-sm-3 col-lg-2 control-label"> Proof of Inspection Document</label>
                  <div class="col-sm-9 col-lg-10 controls">
                     <input type="file" name="proof_of_inspection" id="proof_of_inspection" data-rule-required="false" onchange="check_file_format(this)"/>
                      <span class='help-block'>{{ $errors->first('insurance_doc') }}</span>  
                       <i class="red"> Allowed only jpg | jpeg | png | pdf </i>
                  </div>
              </div>
              <br>
              <div class="form-group">
                  <label class="col-sm-3 col-lg-2 control-label"> Vehicle Insurance Document</label>
                  <div class="col-sm-9 col-lg-10 controls">
                     <input type="file" name="insurance_doc" id="insurance_doc" data-rule-required="false" onchange="check_file_format(this)"/>
                      <span class='help-block'>{{ $errors->first('insurance_doc') }}</span>  
                       <i class="red"> Allowed only jpg | jpeg | png | pdf </i>
                  </div>
              </div>
              <br>
              <div class="form-group">
                  <label class="col-sm-3 col-lg-2 control-label">DMV Driving Record Document</label>
                  <div class="col-sm-9 col-lg-10 controls">
                     <input type="file" name="driving_doc" id="driving_doc" data-rule-required="false" onchange="check_file_format(this)"/>
                      <span class='help-block'>{{ $errors->first('driving_doc') }}</span>  
                       <i class="red"> Allowed only jpg | jpeg | png | pdf </i>
                  </div>
              </div>
              <br>
              <div class="form-group" id="user_doc_div" style="display: none;">
                  <label class="col-sm-3 col-lg-2 control-label"> Usdot Document</label>
                  <div class="col-sm-9 col-lg-10 controls">
                     <input type="file" name="usdot_doc" id="usdot_doc" onchange="check_file_format(this)"/>
                      <span class='help-block'>{{ $errors->first('usdot_doc') }}</span>  
                       <i class="red"> Allowed only jpg | jpeg | png | pdf </i>
                  </div>
              </div>

              <br>
              <div class="form-group" id="mc_doc_div" style="display: none;">
                  <label class="col-sm-3 col-lg-2 control-label"> Mc Document</label>
                  <div class="col-sm-9 col-lg-10 controls">
                     <input type="file" name="mc_doc" id="mc_doc" onchange="check_file_format(this)"/>
                      <span class='help-block'>{{ $errors->first('mc_doc') }}</span>  
                       <i class="red"> Allowed only jpg | jpeg | png | pdf </i>
                  </div>
              </div>

            </div>
            <br>

            <div class="form-group" style="">
                  <label class="col-sm-3 col-lg-2 control-label">Make reset password mandatory for this Driver</label>
                  <div class="col-sm-9 col-lg-4 controls" >
                      <div class="check-box">
                            <input type="checkbox" class="filled-in" name="reset_password_mandatory" id="reset_password_mandatory" />
                            <label for="reset_password_mandatory"></label>
                        </div>
                  </div>
            </div>
            
            <div class="form-group">
              <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2">
               
                {!! Form::submit('Save',['class'=>'btn btn btn-primary','value'=>'true'])!!}
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
  
    $(document).on("change",".validate-image", function()
    {            
        var file=this.files;
        validateImage(this.files, 250, 250);
    });


</script>


<script src="https://maps.googleapis.com/maps/api/js?v=3&key={{ isset($google_map_api_key) ? $google_map_api_key : 'AIzaSyCccvQtzVx4aAt05YnfzJDSWEzPiVnNVsY' }}&libraries=places&callback=initAutocomplete"
        async defer>
</script>

<script>  
  var BASE_URL = "{{url('/')}}";

  var COUNTRY_CODES_JSON_FILE  = BASE_URL+'/assets/country_codes.json';
  
  var glob_autocomplete;
  var glob_component_form = 
                {
                    street_number: 'short_name',
                    route: 'long_name',
                    locality: 'long_name',
                    administrative_area_level_1: 'long_name',
                    postal_code: 'short_name',
                    country : 'long_name',
                    postal_code : 'short_name',
                };

  var glob_options = {};
  glob_options.types = ['address'];

  function initAutocomplete(country_code) 
  {
    glob_autocomplete = false;
    glob_autocomplete = initGoogleAutoComponent($('#autocomplete')[0],glob_options,glob_autocomplete);
  }


  function initGoogleAutoComponent(elem,options,autocomplete_ref)
  {
    autocomplete_ref = new google.maps.places.Autocomplete(elem,options);
    autocomplete_ref = createPlaceChangeListener(autocomplete_ref,fillInAddress);

    return autocomplete_ref;
  }
  

  function createPlaceChangeListener(autocomplete_ref,fillInAddress)
  {
    autocomplete_ref.addListener('place_changed', fillInAddress);
    return autocomplete_ref;
  }

  function destroyPlaceChangeListener(autocomplete_ref)
  {
    google.maps.event.clearInstanceListeners(autocomplete_ref);
  }

  function fillInAddress() 
  {
    $('#country_code').val('');

    var place = glob_autocomplete.getPlace();
    
    $('#lat').val(place.geometry.location.lat());
    $('#lng').val(place.geometry.location.lng());
    
    for (var component in glob_component_form) 
    {
        $("#"+component).val("");
        $("#"+component).attr('disabled',false);
    }
    
    if(place.address_components.length > 0 )
    {
      $.each(place.address_components,function(index,elem){

          var addressType = elem.types[0];
          
          if(addressType!=undefined){

            if(addressType == 'country')
            {
                set_country_code_value(elem.short_name);
            }
            if(glob_component_form[addressType]!=undefined){
              var val = elem[glob_component_form[addressType]];
              $("#"+addressType).val(val) ;  
              console.log("addressType-->",addressType,'val-->',val);
            }
          }
      });  
    }
  }

  function set_country_code_value(country_code)
  {
      $('#country_code').val('');
      $.getJSON( COUNTRY_CODES_JSON_FILE, function( data ) {
        if(data!=undefined){
          $.each(data, function( index, value ) {
              if ((value.code!=undefined) && (value.code === country_code)) {
                if(value.dial_code!=undefined){
                    $('#country_code').val(value.dial_code);
                }
              }
          });
        }
      });
  }
</script>     
  
<script type="text/javascript">
  
   var vehicle_model_url = '{{url('/api/common_data/get_vehicle_model')}}';

   $('input[type=radio][name=is_own_vehicle]').change(function() {

        if (this.value == '1') {
            
            $('#own_vehicle_details_div').show();
            $('#vehicle_type').data('rule-required', true);
            $('#vehicle_brand').data('rule-required', true);
            $('#vehicle_model').data('rule-required', true);
            $('#vehicle_number').data('rule-required', true);

        } else if (this.value == '0') {
            
            $('#own_vehicle_details_div').hide();
            $('#vehicle_type').data('rule-required', false);
            $('#vehicle_brand').data('rule-required', false);
            $('#vehicle_model').data('rule-required', false);
            $('#vehicle_number').data('rule-required', false);
        }
    });

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
            $('#usdot_doc').data('rule-required',true);
            $('#is_usdot_doc_required').val('YES');
            $('#user_doc_div').show();
        }
        else
        {
            $('#usdot_doc').data('rule-required',false);
            $('#is_usdot_doc_required').val('NO');
            $('#user_doc_div').hide();
        }

    }

    function check_is_mcdoc_required(ref){

        var is_mcdoc_required = $('option:selected', ref).attr('is_mcdoc_required');

        if(is_mcdoc_required!=undefined && is_mcdoc_required == '1')
        {
            $('#mc_doc').data('rule-required',true);
            $('#is_mcdoc_required').val('YES');
            $('#mc_doc_div').show();
        }
        else
        {
            $('#mc_doc').data('rule-required',false);
            $('#is_mcdoc_required').val('NO');
            $('#mc_doc_div').hide();
        }

    }

  

function loadVehicleModel(ref) {

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
