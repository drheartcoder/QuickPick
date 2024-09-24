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
                <i class="fa fa-user"></i>
                <a href="{{ $module_url_path }}">{{ $module_title or ''}}</a>
            </li>   
            <span class="divider">
                <i class="fa fa-angle-right"></i>
            </span>
            <li class="active"><i class="fa fa-edit"></i> {{ $page_title or ''}}</li>
        </ul>
    </div>
    <!-- END Breadcrumb -->



    <!-- BEGIN Main Content -->
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

          @include('company.layout._operation_status')  
           {!! Form::open([ 'url' => $module_url_path.'/update',
                                 'method'=>'POST',
                                 'enctype' =>'multipart/form-data',   
                                 'class'=>'form-horizontal', 
                                 'id'=>'validation-form' 
                                ]) !!} 

           {{ csrf_field() }}

           @if(isset($arr_data) && count($arr_data) > 0)   

           {!! Form::hidden('user_id',isset($arr_data['id']) ? $arr_data['id']: "")!!}

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
                  <label class="col-sm-3 col-lg-2 control-label">Email Address<i style="color: red;">*</i></label>
                  <div class="col-sm-9 col-lg-4 controls" >
                      
                      {!! Form::text('email',isset($arr_data['email']) ? $arr_data['email']: "",['class'=>'form-control','readonly', 'placeholder'=>'Enter Email Address' ,'maxlength'=>"255" ]) !!}  

                      <span class="help-block">{{ $errors->first('email') }}</span>
                  </div>
            </div>  

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
                  <input type="text" class="form-control" name="mobile_no" placeholder="Enter Mobile Number" data-rule-required="true" data-rule-digits="true" onkeypress="return isNumberKey(event)" {{-- data-rule-pattern="\+[1-9]{1,3}[0-9]{9,15}" --}} minlength="4" maxlength="12" value="{{ isset($arr_data['mobile_no']) ? $arr_data['mobile_no'] : ''}}" />
                  <span class="help-block">{{ $errors->first('mobile_no') }}</span>
                </div>
              </div>


            <div class="form-group">
              <label class="col-sm-3 col-lg-2 control-label">Profile Image</label>
              <div class="col-sm-9 col-lg-10 controls">
                 <div class="fileupload fileupload-new" data-provides="fileupload">
                   <div class="fileupload-new img-thumbnail" style="width: 200px; height: 150px;">
                      @if(isset($arr_data['profile_image']) && !empty($arr_data['profile_image']) && file_exists($user_profile_base_img_path.$arr_data['profile_image']))
                        <img src={{ $user_profile_public_img_path.$arr_data['profile_image']}} alt="" />
                      @else
                        <img src={{ url("uploads/default-profile.png")}} alt="" />
                      @endif
                  </div>
                    <div class="fileupload-preview fileupload-exists img-thumbnail" style="max-width: 200px; max-height: 150px; line-height: 20px;">
                      @if(isset($arr_data['profile_image']) && !empty($arr_data['profile_image']) && file_exists($user_profile_base_img_path.$arr_data['profile_image']))  
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


            <div class="form-group">
                <label class="col-sm-3 col-lg-2 control-label"> Driving License</label>
                <div class="col-sm-9 col-lg-10 controls"> {{-- value="{{isset($arr_data['driving_license']) ? $arr_data['driving_license'] : ''}}" --}}
                   <input type="file" name="driving_license" id="driving_license"  @if(isset($arr_data['driving_license']) && $arr_data['driving_license'] == '') data-rule-required="true" @endif />
                   <span class='help-block'>{{ $errors->first('driving_license') }}</span>  
                   {{isset($arr_data['driving_license']) ? $arr_data['driving_license'] : ''}}</br>   
                    <i class="red"> Allowed only jpg | jpeg | png | pdf </i>
                </div>
            </div>

            <input type="hidden" name="oldimage" value="{{isset($arr_data['profile_image']) ? $arr_data['profile_image'] :''}}">
            <input type="hidden" name="olddriving_license" value="{{isset($arr_data['driving_license']) ? $arr_data['driving_license'] :''}}">


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


<script>
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

@stop                    
