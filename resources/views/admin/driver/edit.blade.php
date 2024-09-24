	@extends('admin.layout.master')                

	@section('main_content')
	<div class="page-title"><div></div></div>
	
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
			<li class="active"><i class="fa fa-edit"></i> {{ $page_title or ''}}</li>
		</ul>
	</div>

	<div class="row">
		<div class="col-md-12">
			<div class="box {{ $theme_color }}">
				<div class="box-title">
					<h3>
						<i class="fa fa-edit"></i>
						{{ isset($page_title)?$page_title:"" }}
					</h3>
					<div class="box-tool">
						<a data-action="collapse" href="#"></a>
						<a data-action="close" href="#"></a>
					</div>
				</div>
				<div class="box-content">

					@include('admin.layout._operation_status')  
					{!! Form::open([ 'url' => $module_url_path.'/update',
					'method'=>'POST',
					'enctype' =>'multipart/form-data',   
					'class'=>'form-horizontal', 
					'id'=>'validation-form' 
					]) !!} 

					{{ csrf_field() }}
					
					{{-- {{dump($arr_data)}} --}}

					@if(isset($arr_data) && count($arr_data) > 0)   

					{!! Form::hidden('user_id',isset($arr_data['id']) ? $arr_data['id']: "")!!}

					<div class="form-group">
							<label class="col-sm-3 col-lg-2 control-label">Profile Image</label>
							<div class="col-sm-9 col-lg-10 controls">
								<div class="fileupload fileupload-new" data-provides="fileupload">
									<div class="fileupload-new img-thumbnail" style="width: 150px; height: 150px;">
										@if(isset($arr_data['profile_image']) && !empty($arr_data['profile_image']))
										<img src={{ $user_profile_public_img_path.$arr_data['profile_image']}} alt="" />
										@else
										<img src={{ url("uploads/default-profile.png")}} alt="" />
										@endif
									</div>
									<div class="fileupload-preview fileupload-exists img-thumbnail" style="max-width: 200px; max-height: 150px; line-height: 20px;">
										@if(isset($arr_data['profile_image']) && !empty($arr_data['profile_image']))  
										<img src={{ $user_profile_public_img_path.$arr_data['profile_image']}} alt="" />
										@else
										<img src={{ url("uploads/default-profile.png")}} alt="" />
										@endif   
									</div>
									<div>
										<span class="btn btn-default btn-file"><span class="fileupload-new" >Select Image</span> 
										<span class="fileupload-exists">Change</span>

										{!! Form::file('profile_image',['id'=>'profile_image','class'=>'file-input validate-image']) !!}

									</span> 
									<a href="#" class="btn btn-default fileupload-exists" data-dismiss="fileupload">Remove</a>
									<span>
									</span> 
								</div>
							</div>
							<i class="red"> {!! image_validate_note(250,250) !!} </i>
							<span class='help-block'><b>{{ $errors->first('profile_image') }}</b></span>  
						</div>
					</div>

					<div class="form-group" style="margin-top: 25px;">
						<label class="col-sm-3 col-lg-2 control-label">Firstname<i style="color: red;">*</i></label>
						<div class="col-sm-9 col-lg-4 controls" >

							{!! Form::text('first_name',isset($arr_data['first_name']) ? $arr_data['first_name']: "",['class'=>'form-control','data-rule-required'=>'true', 'placeholder'=>'Enter Firstname' , 'maxlength'=>"40" ]) !!}  

							<span class="help-block">{{ $errors->first('first_name') }}</span>
						</div>
					</div>

					<div class="form-group" style="">
						<label class="col-sm-3 col-lg-2 control-label">Last Name<i style="color: red;">*</i></label>
						<div class="col-sm-9 col-lg-4 controls" >

							{!! Form::text('last_name',isset($arr_data['last_name']) ? $arr_data['last_name']: "",['class'=>'form-control','data-rule-required'=>'true', 'placeholder'=>'Enter Last Name' , 'maxlength'=>"40"]) !!}  

							<span class="help-block">{{ $errors->first('last_name') }}</span>
						</div>
					</div>

					<div class="form-group" style="">
						<label class="col-sm-3 col-lg-2 control-label">Email Address<i style="color: red;"></i></label>
						<div class="col-sm-9 col-lg-4 controls" >

							{!! Form::text('email',isset($arr_data['email']) ? $arr_data['email']: "",['class'=>'form-control', 'placeholder'=>'Enter Email Address' ,'maxlength'=>"255" ]) !!}  

							<span class="help-block">{{ $errors->first('email') }}</span>
						</div>
					</div> 

					{{--  <div class="form-group" style="">
						<label class="col-sm-3 col-lg-2 control-label">Company Name</label>
						<div class="col-sm-9 col-lg-4 controls" >
							<input type="text" class="form-control" name="company_name" placeholder="Enter Company Name" value="{{ isset($arr_data['company_name']) ? $arr_data['company_name'] : ''}}" />

							<div class="help-block">{{ $errors->first('company_name') }}</div>
						</div>
					</div> --}}

					<div class="form-group" style="">
						<label class="col-sm-3 col-lg-2 control-label">Address</label>
						<div class="col-sm-9 col-lg-4 controls" >

							{!! Form::text('address',isset($arr_data['address']) ? $arr_data['address']: "",['class'=>'form-control', 'placeholder'=>'Enter Address', 'id'=>"autocomplete"]) !!}

							<div class="help-block">{{ $errors->first('address') }}</div>
						</div>
					</div>
					
					<div class="geo-details">
						<div class="form-group" style="">
							<label class="col-sm-3 col-lg-2 control-label">Post Code<i style="color: red;">*</i></label>
							<div class="col-sm-9 col-lg-4 controls" >
								<input type="text" class="form-control" id="postal_code" name="post_code" onkeypress="return isNumberKey(event)" placeholder="Enter Post Code" data-rule-required="true" data-rule-digits="true" minlength="4" maxlength="12" value="{{ isset($arr_data['post_code']) ? $arr_data['post_code'] : ''}}" />
								<span class="help-block">{{ $errors->first('post_code') }}</span>
							</div>
						</div>

						<input name="country_name" id="country" type="hidden" value="{{ isset($arr_data['country_name']) ? $arr_data['country_name'] : ''}}" />
						<input name="state_name" id="administrative_area_level_1" type="hidden" value="{{ isset($arr_data['state_name']) ? $arr_data['state_name'] : ''}}" />
						<input name="city_name" id="locality" type="hidden" value="{{ isset($arr_data['city_name']) ? $arr_data['city_name'] : ''}}" />
						<input name="lat" id="lat" type="hidden" value="{{ isset($arr_data['latitude']) ? $arr_data['latitude'] : ''}}" />
						<input name="long" id="lng" type="hidden" value="{{ isset($arr_data['longitude']) ? $arr_data['longitude'] : ''}}" />
					</div>

					<div class="form-group" style="">
						<label class="col-sm-3 col-lg-2 control-label">Mobile Number<i style="color: red;">*</i></label>
						<div class="col-sm-9 col-lg-1 controls" >
	                      	<input type="text" class="form-control" id="country_code" name="country_code" placeholder="Code" readonly="" value="{{ isset($arr_data['country_code']) ? $arr_data['country_code'] : ''}}" />
	                      	<span class="help-block">{{ $errors->first('country_code') }}</span>
	                  	</div>

						<div class="col-sm-9 col-lg-3 controls" >
							<input type="text" class="form-control" name="mobile_no" placeholder="Enter Mobile Number" data-rule-required="true" data-rule-digits="true" onkeypress="return isNumberKey(event)" {{-- data-rule-pattern="\+[1-9]{1,3}[0-9]{9,15}" --}} minlength="8" maxlength="16" value="{{ isset($arr_data['mobile_no']) ? $arr_data['mobile_no'] : ''}}" />
							<span class="help-block">{{ $errors->first('mobile_no') }}</span>
						</div>
					</div>



					{{--  <div class="form-group" style="">
						<label class="col-sm-3 col-lg-2 control-label">Post Code<i style="color: red;">*</i></label>
						<div class="col-sm-9 col-lg-4 controls" >
							<input type="text" class="form-control" name="post_code" placeholder="Enter Post Code" data-rule-required="true" value="{{ isset($arr_data['post_code']) ? $arr_data['post_code'] : ''}}" />
							<span class="help-block">{{ $errors->first('post_code') }}</span>
						</div>
					</div> --}}

				<div class="form-group">
					<label class="col-sm-3 col-lg-2 control-label"> Driving License</label>
					<div class="col-sm-9 col-lg-10 controls">
						<input type="file" name="driving_license" id="driving_license" @if(isset($arr_data['driving_license']) && $arr_data['driving_license'] == '') data-rule-required="true" @endif/>
						<span class='help-block'>{{ $errors->first('driving_license') }}</span> 
						@if(isset($arr_data['driving_license']) && $arr_data['driving_license']!='')
							{{isset($arr_data['driving_license']) ? $arr_data['driving_license'] :''}}</br> 
						@endif
						<i class="red"> Allowed only jpg | jpeg | png | pdf <br> </i>
					</div>
				</div>

				<input type="hidden" name="oldimage" value="{{isset($arr_data['profile_image']) ? $arr_data['profile_image'] :''}}">
				<input type="hidden" name="olddriving_license" value="{{isset($arr_data['driving_license']) ? $arr_data['driving_license'] :''}}">

		           	<input type="hidden" name="is_individual_vehicle" id="is_individual_vehicle" value="{{ isset($arr_data['driver_car_details']['is_individual_vehicle']) ? $arr_data['driver_car_details']['is_individual_vehicle'] : '' }}">
		           	
				@if(isset($arr_data['driver_car_details']['is_individual_vehicle']) && $arr_data['driver_car_details']['is_individual_vehicle'] == '1')
					<?php

						$arr_vehicle_details = [];
						if(isset($arr_data['driver_car_details']['vehicle_details']) && count($arr_data['driver_car_details']['vehicle_details'])>0)
						{
							$arr_vehicle_details = $arr_data['driver_car_details']['vehicle_details'];
						}

					?>
					
		           	<input type="hidden" class="form-control" name="vehicle_id" value="{{ isset($arr_data['driver_car_details']['vehicle_id']) ? $arr_data['driver_car_details']['vehicle_id'] : '0' }}"/>
					
					<div class="form-group" style="">
		                <label class="col-sm-3 col-lg-2 control-label">Vehicle Type<i style="color: red;">*</i></label>
		                <div class="col-sm-9 col-lg-4 controls" >
		                    <select class="form-control" id="vehicle_type" data-rule-required="true" name="vehicle_type" onchange="check_is_usdot_required(this);check_is_mcdoc_required(this)">
		                      <option value="">--Select Vehicle Type--</option>
		                      @if(isset($arr_vehicle_types) && count($arr_vehicle_types)>0)
		                        @foreach($arr_vehicle_types as $key => $value)
		                          <option 
		                                  @if(isset($arr_vehicle_details['vehicle_type_id']) && $arr_vehicle_details['vehicle_type_id'] == $value['id'])
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

		            <div class="form-group" style="">
		                <label class="col-sm-3 col-lg-2 control-label">Vehicle Brand<i style="color: red;">*</i></label>
		                <div class="col-sm-9 col-lg-4 controls" >
		                    <select class="form-control" id="vehicle_brand" data-rule-required="true" name="vehicle_brand" onchange="loadVehicleModel(this)">
		                      <option value="">--Select Vehicle Brand--</option>
		                      @if(isset($arr_vehicle_brands) && count($arr_vehicle_brands)>0)
		                        @foreach($arr_vehicle_brands as $key => $value)
		                          <option value="{{ $value['id'] }}" @if(isset($arr_vehicle_details['vehicle_brand_id']) && $arr_vehicle_details['vehicle_brand_id']==$value['id']) selected @endif>{{ $value['name'] }}</option>
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
		                      <input type="text" class="form-control" name="vehicle_number" placeholder="Enter Vehicle License Plate Number" data-rule-required="true" value="{{ isset($arr_vehicle_details['vehicle_number']) ? $arr_vehicle_details['vehicle_number'] :'' }}" />
		                      <span class="help-block">{{ $errors->first('vehicle_number') }}</span>
		                  </div>
		            </div>

		          	<div class="form-group">
		                <label class="col-sm-3 col-lg-2 control-label"> Vehicle Image</label>
		                <div class="col-sm-9 col-lg-10 controls">
		                   <input type="file" name="vehicle_image" id="vehicle_image" onchange="check_file_format(this)"/>
		                    <span class='help-block'>{{ $errors->first('vehicle_image') }}</span>  
		                    {{isset($arr_vehicle_details['vehicle_image']) ? $arr_vehicle_details['vehicle_image'] :''}}
		                     <i class="red"> Allowed only jpg | jpeg | png | pdf </i>
		                </div>
		          	</div>

		          	<div class="form-group">
		                <label class="col-sm-3 col-lg-2 control-label"> Registration Document</label>
		                <div class="col-sm-9 col-lg-10 controls">
		                   <input type="file" name="registration_doc" id="registration_doc" onchange="check_file_format(this)"/>
		                    <span class='help-block'>{{ $errors->first('registration_doc') }}</span>  
		                    {{isset($arr_vehicle_details['registration_doc']) ? $arr_vehicle_details['registration_doc'] :''}}   
		                     <i class="red"> Allowed only jpg | jpeg | png | pdf </i>
		                </div>
		          	</div>

		          	<div class="form-group">
		                <label class="col-sm-3 col-lg-2 control-label"> Vehicle Insurance Document</label>
		                <div class="col-sm-9 col-lg-10 controls">
		                   <input type="file" name="insurance_doc" id="insurance_doc" onchange="check_file_format(this)"/>
		                    <span class='help-block'>{{ $errors->first('insurance_doc') }}</span>  
		                    {{isset($arr_vehicle_details['insurance_doc']) ? $arr_vehicle_details['insurance_doc'] :''}}  
		                     <i class="red"> Allowed only jpg | jpeg | png | pdf </i>
		                </div>
		          	</div>

		          	<div class="form-group">
		                <label class="col-sm-3 col-lg-2 control-label"> Proof of Inspection Document</label>
		                <div class="col-sm-9 col-lg-10 controls">
		                   <input type="file" name="proof_of_inspection" id="proof_of_inspection" onchange="check_file_format(this)"/>
		                    <span class='help-block'>{{ $errors->first('proof_of_inspection') }}</span>  
		                    {{isset($arr_vehicle_details['proof_of_inspection_doc']) ? $arr_vehicle_details['proof_of_inspection_doc'] :''}}
		                     <i class="red"> Allowed only jpg | jpeg | png | pdf </i>
		                </div>
		          	</div>
		          
		          	<div class="form-group">
		                <label class="col-sm-3 col-lg-2 control-label"> DMV Driving Record Document</label>
		                <div class="col-sm-9 col-lg-10 controls">
		                   <input type="file" name="driving_doc" id="driving_doc" onchange="check_file_format(this)"/>
		                    <span class='help-block'>{{ $errors->first('driving_doc') }}</span>  
		                    {{isset($arr_vehicle_details['dmv_driving_record']) ? $arr_vehicle_details['dmv_driving_record'] :''}}
		                     <i class="red"> Allowed only jpg | jpeg | png | pdf </i>
		                </div>
		          	</div>
		          	
		          	<div class="form-group" id="user_doc_div" 
		          		@if(isset($arr_vehicle_details['vehicle_type_details']['is_usdot_required']))
		          			@if($arr_vehicle_details['vehicle_type_details']['is_usdot_required'] == '0')
							style="display: none;" 
		          			@endif
		          		@else
		          			style="display: none;" 
		          		@endif
		          	>
		                <label class="col-sm-3 col-lg-2 control-label"> Usdot Document</label>
		                <div class="col-sm-9 col-lg-10 controls">
		                   <input type="file" name="usdot_doc" id="usdot_doc" onchange="check_file_format(this)"/>
		                    <span class='help-block'>{{ $errors->first('usdot_doc') }}</span>  
		                    {{isset($arr_vehicle_details['usdot_doc']) ? $arr_vehicle_details['usdot_doc'] :''}}
		                     <i class="red"> Allowed only jpg | jpeg | png | pdf </i>
		                </div>
		          	</div>

		          	<div class="form-group" id="mc_doc_div"
		          		@if(isset($arr_vehicle_details['vehicle_type_details']['is_mcdoc_required']))
		          			@if($arr_vehicle_details['vehicle_type_details']['is_mcdoc_required'] == '0')
							style="display: none;" 
		          			@endif
		          		@else
		          			style="display: none;" 
		          		@endif
		          		>
		                <label class="col-sm-3 col-lg-2 control-label"> Mc Document</label>
		                <div class="col-sm-9 col-lg-10 controls">
		                   <input type="file" name="mc_doc" id="mc_doc" onchange="check_file_format(this)"/>
		                    <span class='help-block'>{{ $errors->first('mc_doc') }}</span>  
		                    {{isset($arr_vehicle_details['mc_doc']) ? $arr_vehicle_details['mc_doc'] :''}}
		                     <i class="red"> Allowed only jpg | jpeg | png | pdf </i>
		                </div>
		          	</div>

				@endif
				<br>
				<div class="form-group">
					<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2">

						{!! Form::submit('Update',['class'=>'btn btn btn-primary','value'=>'true'])!!}
						&nbsp;
						@if(isset($arr_data['id']))
			              <a class="btn btn btn-primary" href="{{ $module_url_path.'/reset_password/'.base64_encode($arr_data['id']) }}">Reset Password</a>
			            @endif
			            &nbsp;
						<a class="btn" href="{{ $module_url_path }}">Back</a>
					</div>
				</div>

				@else 
				<div class="form-group">
					<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2">
						<h3><strong>No Record found..</strong></h3>     
					</div>
				</div>
				@endif

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
            $('#usdot_doc').attr('data-rule-required', 'true');
            $('#is_usdot_doc_required').val('YES');
            $('#user_doc_div').show();
        }
        else
        {
            $('#usdot_doc').attr('data-rule-required', 'false');
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

  var vehicle_model_url = '{{url('/api/common_data/get_vehicle_model')}}';

  var vehicle_brand_id = '{{isset($arr_vehicle_details['vehicle_brand_id']) ? $arr_vehicle_details['vehicle_brand_id'] : 0 }}';
  var vehicle_model_id = '{{isset($arr_vehicle_details['vehicle_model_id']) ? $arr_vehicle_details['vehicle_model_id'] : 0 }}';

  $(document).ready(function(){         
        
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

function loadVehicleModel(ref) 
{
    var vehicle_brand_id     = {{ isset($arr_vehicle_details['vehicle_brand_id']) ? $arr_vehicle_details['vehicle_brand_id'] : '0' }}

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
