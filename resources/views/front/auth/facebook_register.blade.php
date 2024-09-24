@extends('front.layout.master')                

@section('main_content')
    
    <div class="blank-div"></div>
    
    <!--register section start-->
        <div class="login-wrapper signup-wrapper">
        <!--        <div class="signup-back sign-back2"><img src="images/login-banner.jpg" class="img-responsive" alt="" /></div>-->
        <div class="container">
            <div class="row">
                <div class="col-sm-6 col-md-6 col-lg-6 respo-padd-none">
                    <div class="login-banner-block">
                        <!--                        <div class="white-logo"><img src="images/logo.png" class="img-responsive" alt="" /></div>-->
                        <h1>Create an Your Account</h1>
                        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed doeiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enimad minim veniam, quis nostrud exercitation ullamco laboris nisi utaliquip ex ea commodo consequat.</p>
                        <h5>Already have a Account - <a href="{{url('/login')}}">Login Now <i class="fa fa-long-arrow-right"></i></a></h5>
                    </div>
                </div>
                <div class="col-sm-6 col-md-6 col-lg-6 login-bg">
                    <div class="login-section login2 change">
                        <h1>Facebook Sign Up</h1>
                        <h5>It's free and always will be.</h5>

                        <div class="login-tabs">

                        @include('front.layout._operation_status')  

                            <div class="login-form-block">
                                <form name="frm-login" method="post" action="{{url('/process_facebook_register')}}" data-parsley-validate>

                                    {{ csrf_field() }}

                                    <div class="form-group login">
                                        <input type="text" class="input-box" value="{{isset($arr_data['email']) ? $arr_data['email'] : ''}}" disabled="" />
                                        <span class="bar"></span>
                                        <div class="error" id="err_email"></div>
                                    </div>

                                    <div class="form-group login">
                                        <input type="text" class="input-box" placeholder="Mobile Number" id="mobile_no" name="mobile_no" data-parsley-required-message="Please enter mobile number" data-parsley-required="true" data-parsley-errors-container="#err_mobile_no"/>
                                        <span class="bar"></span>
                                        <div id="err_mobile_no" class="error"></div>
                                    </div>

                                    <div class="form-group login">
                                        <input type="text" class="input-box" placeholder="Full Address" id="address" name="address" data-parsley-required-message="Please enter full address" data-parsley-required="true" data-parsley-errors-container="#err_address"/>
                                        <span class="bar"></span>
                                        <div class="error" id="err_address"></div>
                                    </div>

                                    <div class="form-group login">
                                        <input type="text" class="input-box" placeholder="Enter Post Code" id="postal_code" name="post_code" data-parsley-required-message="Please enter post code" data-parsley-required="true" data-parsley-errors-container="#err_post_code"/>
                                        <span class="bar"></span>
                                        <div class="error" id="err_post_code"></div>
                                        <input type="hidden" id="country_code" name="country_code" />
                                        <input name="country_name" id="country" type="hidden" />
                                        <input name="state_name" id="administrative_area_level_1" type="hidden" />
                                        <input name="city_name" id="locality" type="hidden" />
                                        <input name="lat" id="lat" type="hidden" />
                                        <input name="long" id="lng" type="hidden" />
                                    </div>

                                    <input type="hidden" name="email" value="{{isset($arr_data['email']) ? $arr_data['email'] : ''}}" />
                                    <input type="hidden" name="first_name" value="{{isset($arr_data['first_name']) ? $arr_data['first_name'] : ''}}" />
                                    <input type="hidden" name="last_name" value="{{isset($arr_data['last_name']) ? $arr_data['last_name'] : ''}}" />

                                    <input type="hidden" name="user_type" value="USER">
                                   
                                    <div class="clearfix"></div>
                                    <div class="login-btn"> <button type="submit">Register Now</button></div>

                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--register section end-->
    <script src="https://maps.googleapis.com/maps/api/js?v=3&key={{ isset($google_map_api_key) ? $google_map_api_key : 'AIzaSyCccvQtzVx4aAt05YnfzJDSWEzPiVnNVsY' }}&libraries=places&callback=initAutocomplete"
        async defer>
    </script>

<script type="text/javascript" language="javascript" src="{{url('/')}}/js/front/cleave.min.js"></script>
<script type="text/javascript" language="javascript" src="{{url('/')}}/js/front/cleave-phone.i18n.js"></script>

<script type="text/javascript">
    var country_code = '{{ isset($country_code) && $country_code!='' ? $country_code : 'US' }}';
    
    var cleavePhone = new Cleave('#mobile_no', {
        phone: true,
        phoneRegionCode: 'US'
    });

</script>

<script type="text/javascript">
 
  var BASE_URL = "{{url('/')}}";

  var COUNTRY_CODES_JSON_FILE  = BASE_URL+'/assets/country_codes.json';
  
  var glob_autocomplete;
  var glob_component_form = 
                {
                    street_number: 'short_name',
                    route: 'long_name',
                    locality: 'long_name',
                    administrative_area_level_1: 'long_name',
                    country : 'long_name',
                    postal_code: 'short_name',
                };

  var glob_options = {};
  glob_options.types = ['address'];

  function initAutocomplete(country_code) 
  {
    glob_autocomplete = false;
    glob_autocomplete = initGoogleAutoComponent($('#address')[0],glob_options,glob_autocomplete);
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
              // console.log("addressType-->",addressType,'val-->',val);
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
</script>
@stop