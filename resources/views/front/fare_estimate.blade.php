@extends('front.layout.master') @section('main_content')
<div class="blank-div"></div>

<div class="fare-estimate-map-wrapper">
     <div class="fare-estimate-head head">fare <span>estimator</span></div>
    <div class="fare-estimate-map" id="dvMap"></div>

    <div class="fare-estimate-map-direction-block">
        
        <form id="frm_packaging_details"  data-parsley-validate>

        {{ csrf_field() }}

        <div class="fare-estimate-map-direction top">
            <div class="fare-estimate-head">fare <span>estimator</span></div>
            <div class="fare-estimate-sub"> Enter the following information </div>
             <div class="form-group">
                    <input type="text" id="pickup_location" name="pickup_location" class="input-box" placeholder="Pickup Location" data-parsley-required-message="Please enter pickup location" data-parsley-required="true" data-parsley-errors-container="#err_pickup_location" />
                    <div id="err_pickup_location" class="error"></div>
                    <!--<div class="map-direction-circle"></div>-->
                 <div class="frem-input-icon"></div>
             </div>
             <div class="form-group">
                    <input type="text" id="drop_location" name="drop_location" class="input-box" placeholder="Drop Location" data-parsley-required-message="Please enter drop location" data-parsley-required="true" data-parsley-errors-container="#err_drop_location"/>
                    <div id="err_drop_location" class="error"></div>
                    <!--<div class="map-direction-circle red"></div>-->
                    <div class="frem-input-icon"></div>
             </div>
              <div class="form-group package">
                        <select name="package_type" id="package_type" data-parsley-required-message="Please select package type" data-parsley-required="true" data-parsley-errors-container="#err_package_type" onchange="checkCurrentpackageType(this)"> 
                          <option value="">Package Type</option>
                          <option value="BOX">Box</option>
                          <option value="CARTON">Carton</option>
                          <option value="CONTAINER">Container</option>
                          <option value="ENVELOPE">Envelope</option>
                          <option value="PALLET">Pallet</option>
                          <option value="OTHER">Other</option>
                        </select>
                        <div id="err_package_type" class="error"></div>
                        <div class="frem-input-icon"></div>
                    </div>
                <div class="row">
                
                <div id="package_other_details">
                    <div class="col-sm-6 col-md-6 col-lg-6">
                        <div class="form-group weight">
                        <input type="text" class="input-box" id="weight" name="package_weight" placeholder="Weight (Pounds)" data-parsley-required-message="Please enter weight in pound" data-parsley-required="true" data-parsley-errors-container="#err_weight" onkeypress="return isNumberKey(event)"/>
                        <div id="err_weight" class="error"></div>
                        <div class="frem-input-icon"></div>
                        </div>
                    </div>
                    
                    <div class="col-sm-6 col-md-6 col-lg-6">
                        <div class="form-group hash">
                        <input type="text" class="input-box" id="length" name="package_length" placeholder="Length (ft)" data-parsley-required-message="Please enter Length in ft" data-parsley-required="true" data-parsley-errors-container="#err_length" onkeypress="return isNumberKey(event)"/>
                        <div id="err_length" class="error"></div>
                        <div class="frem-input-icon"></div>
                        </div>
                    </div>
                    
                    <div class="col-sm-6 col-md-6 col-lg-6">
                        <div class="form-group lenght">
                        <input type="text" class="input-box" id="width" name="package_breadth" placeholder="Width (ft)" data-parsley-required-message="Please enter width in ft" data-parsley-required="true" data-parsley-errors-container="#err_width" onkeypress="return isNumberKey(event)"/>
                        <div id="err_width" class="error"></div>
                        <div class="frem-input-icon"></div>
                        </div>
                    </div>

                    <div class="col-sm-6 col-md-6 col-lg-6">
                        <div class="form-group lenght">
                        <input type="text" class="input-box" id="height" name="package_height" placeholder="Height (ft)" data-parsley-required-message="Please enter height in ft" data-parsley-required="true" data-parsley-errors-container="#err_height" onkeypress="return isNumberKey(event)"/>
                        <div id="err_height" class="error"></div>
                        <div class="frem-input-icon"></div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-md-6 col-lg-6">
                    <div class="form-group lenght">
                    <input type="text" class="input-box" id="quantity" name="package_quantity" placeholder="Quantity" data-parsley-required-message="Please enter quantity" data-parsley-required="true" data-parsley-errors-container="#err_quantity" onkeypress="return isNumberKey(event)"/>
                    <div id="err_quantity" class="error"></div>
                    <div class="frem-input-icon"></div>
                    </div>
                </div>
            
                <div class="col-sm-6 col-md-6 col-lg-6">
                <div class="fare-estimate-map-button">
                    <button type="button" onclick="validFormDetails()" id="btn_go" class="red-btn">Go!</button>
                </div>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>

        <input type="hidden" name="pickup_lat" id="pickup_lat">
        <input type="hidden" name="pickup_lng" id="pickup_lng">

        <input type="hidden" name="drop_lat" id="drop_lat">
        <input type="hidden" name="drop_lng" id="drop_lng">

        <!--<div class="fare-estimate-map-direction" id="div_packaging_details" ></div>-->
        
        </form>
        
        <div class="fare-estimate-map-direction price" id="div_fare_estimate" style="display: none;margin-top:20px"> 
          <div class="fare-estimate-map-option">Your Options:</div>
            <div class="map-option-price-wrapper">
                <a href="javascript:void(0)">
                    <div class="map-option-price-left">Vehicle Type</div>
                    <div class="map-option-price-right" id="div_vehicle_type_name"></div>
                    <div class="clearfix"></div>
               </a>
            </div>
            
            {{-- <div class="map-option-price-wrapper">
                <a href="javascript:void(0)">
                    <div class="map-option-price-left">Per Mile Charge</div>
                    <div class="map-option-price-right" id="div_per_mile_charge"></div>
                    <div class="clearfix"></div>
               </a>
            </div> --}}

            <div class="map-option-price-wrapper">
                <a href="javascript:void(0)">
                    <div class="map-option-price-left">Distance (In Miles)</div>
                    <div class="map-option-price-right" id="div_distance_in_miles"></div>
                    <div class="clearfix"></div>
               </a>
            </div>

            <div class="map-option-price-wrapper bottom">
                <a href="javascript:void(0)">
                    <div class="map-option-price-left">Total Fare</div>
                    <div class="map-option-price-right" id="div_total_fare"></div>
                    <div class="clearfix"></div>
               </a>
            </div>

           <div class="fare-estimate-map-button second">
                <button type="button" onclick="confirmOK()" class="red-btn">ok</button>
            </div>
            <div class="clearfix"></div>
        </div>


        <div class="fare-estimate-map-direction price error" id="div_show_error" style="display: none">
            <div class="map-option-price-wrapper error" id="div_error_html">
            </div>
           <div class="clearfix"></div>
            <div class="estimate-map-direction-close"><a href="javascript:void(0)" onclick="hideErrorDiv()"><img src="{{url('images/noti-close-icon.png')}}" alt="noti-close-icon" /></a></div>
            <div class="clearfix"></div>
        </div>

    </div>
    
</div>

<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key={{ isset($google_map_api_key) ? $google_map_api_key : 'AIzaSyCccvQtzVx4aAt05YnfzJDSWEzPiVnNVsY' }}&libraries=places"></script>
<script src="{{ url('/') }}/js/admin/SlidingMarker.js"></script>
<link rel="stylesheet" type="text/css" href="{{ url('/') }}/css/admin/sweetalert.css" />
<script src="{{ url('/') }}/js/admin/sweetalert.min.js"></script>
<script type="text/javascript" src="{{ url('/js/admin') }}/sweetalert_msg.js"></script>


<script type="text/javascript">

    function checkCurrentpackageType(ref)
    {
        if($(ref).val() == 'PALLET'){
            $('#weight').attr('data-parsley-required','false');
            $('#length').attr('data-parsley-required','false');
            $('#width').attr('data-parsley-required','false');
            $('#height').attr('data-parsley-required','false');
            $('#package_other_details').hide();
        }
        else{
            $('#weight').attr('data-parsley-required','true');
            $('#length').attr('data-parsley-required','true');
            $('#width').attr('data-parsley-required','true');
            $('#height').attr('data-parsley-required','true');
            $('#package_other_details').show();
        }
    }

    function isNumberKey(evt) 
    {
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        
        if (charCode == 190 || charCode == 46 ) 
          return true;
        
        if (charCode > 31 && (charCode < 48 || charCode > 57 )) 
        return false;
        
        return true;
    }

    

    var source_glob_autocomplete;
    var destination_glob_autocomplete;

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

    var glob_options   = {};
  

    function initSourceAutocomplete() {
        source_glob_autocomplete = false;
        source_glob_autocomplete = initGoogleAutoComponent($('#pickup_location')[0],glob_options,source_glob_autocomplete,'source');
    }

    function initDestinationAutocomplete() {
        destination_glob_autocomplete = false;
        destination_glob_autocomplete = initGoogleAutoComponent($('#drop_location')[0],glob_options,destination_glob_autocomplete,'destination');
    }
    function initGoogleAutoComponent(elem,options,autocomplete_ref,type){
        
        if(type == 'source'){
            autocomplete_ref = new google.maps.places.Autocomplete(elem,options);
            autocomplete_ref = createPlaceChangeListener(autocomplete_ref,fillSourceAddress,'source');
            return autocomplete_ref;
        }

        if(type == 'destination'){
            autocomplete_ref = new google.maps.places.Autocomplete(elem,options);
            autocomplete_ref = createPlaceChangeListener(autocomplete_ref,fillDestinationAddress,'destination');
            return autocomplete_ref;
        }

    }

    function createPlaceChangeListener(autocomplete_ref,fillSourceAddress,type){

        if(type == 'source'){
            autocomplete_ref.addListener('place_changed', fillSourceAddress);
            return autocomplete_ref;
        }

        if(type == 'destination'){
            autocomplete_ref.addListener('place_changed', fillDestinationAddress);
            return autocomplete_ref;
        }
    }

    function fillSourceAddress() {
        
        //directionsDisplay = false;
        bounds = false
        bounds = new google.maps.LatLngBounds();
        
        // directionsDisplay = new google.maps.DirectionsRenderer({suppressMarkers: true});
        // directionsDisplay.setMap(map);
        if(directionsDisplay!=undefined){
            directionsDisplay.setMap(null);
        }

        $('#div_fare_estimate').hide();
        var place = source_glob_autocomplete.getPlace();
        $('#pickup_lat').val(place.geometry.location.lat());
        $('#pickup_lng').val(place.geometry.location.lng());
        SetSourceMarker();

        // $('#drop_location').val('');
        // $('#drop_lat').val('');
        // $('#drop_lng').val('');

    }

    function fillDestinationAddress() {
        $('#div_fare_estimate').hide();        
        
        bounds = false
        bounds = new google.maps.LatLngBounds();
        
        // directionsDisplay = false;
        // directionsDisplay = new google.maps.DirectionsRenderer({suppressMarkers: true});
        if(directionsDisplay!=undefined){
            directionsDisplay.setMap(null);
        }

        var place = destination_glob_autocomplete.getPlace();
        $('#drop_lat').val(place.geometry.location.lat());
        $('#drop_lng').val(place.geometry.location.lng());
        SetDestinationMarker();
    }

    var BASE_URL = "{{url('/')}}";
    var ARR_MAPS_STYLE  = [];
    var STYLE_JSON_FILE = BASE_URL+'/assets/maps_style_2.json';

    $( document ).ready(function() {
        $.ajax({
            url: STYLE_JSON_FILE,
            async: false,
            dataType: 'json',
            success: function (response) {
               ARR_MAPS_STYLE = response;
            }
        });

        setTimeout(function(){ 
            initSourceAutocomplete();
            initDestinationAutocomplete();
            LoadMap();
        }, 2000);
    });

    var map;
    var bounds;
    var source_marker;
    var destination_marker;

    var directionsService;
    var directionsDisplay;

    function LoadMap() {

        var mapOptions = {
            center: new google.maps.LatLng(0,0),
            zoom: 2,
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            scrollwheel: true,
            disableDefaultUI: true,
            styles : ARR_MAPS_STYLE
        };
        map = new google.maps.Map(document.getElementById("dvMap"), mapOptions);
        bounds = new google.maps.LatLngBounds();
    };
    
    function SetSourceMarker() {
      
        if(source_marker!=undefined){
          source_marker.setMap(null);
        }

        var pickup_lat = $('#pickup_lat').val();
        var pickup_lng = $('#pickup_lng').val();

        var marker_icon = BASE_URL+'/node_assets/images/pointer.png';
      
        var myLatlng = new google.maps.LatLng(pickup_lat,pickup_lng);

        source_marker = new SlidingMarker({
                                    position      : myLatlng,
                                    map           : map,
                                    icon          : marker_icon
                                });

        bounds.extend(myLatlng);
        map.fitBounds(bounds);
        map.setZoom(16);

        calculateAndDisplayRoute();
    };

    function SetDestinationMarker() {
      
        if(destination_marker!=undefined){
          destination_marker.setMap(null);
        }

        var drop_lat = $('#drop_lat').val();
        var drop_lng = $('#drop_lng').val();

        var marker_icon = BASE_URL+'/node_assets/images/pointer.png';
      
        var myLatlng = new google.maps.LatLng(drop_lat,drop_lng);

        destination_marker = new SlidingMarker({
                                    position      : myLatlng,
                                    map           : map,
                                    icon          : marker_icon
                                });

        bounds.extend(myLatlng);
        map.fitBounds(bounds);
        
        calculateAndDisplayRoute();
    };

    function calculateAndDisplayRoute() {

        if(source_marker!=undefined && destination_marker!=undefined)
        {
            directionsService = new google.maps.DirectionsService;

            directionsDisplay = new google.maps.DirectionsRenderer({suppressMarkers: true});
            
            directionsDisplay.setMap(map);

            var pickup_lat = $('#pickup_lat').val();
            var pickup_lng = $('#pickup_lng').val();

            var drop_lat = $('#drop_lat').val();
            var drop_lng = $('#drop_lng').val();

            var origin_lat_lng      = pickup_lat+','+pickup_lng;
            var drop_lat_lng = drop_lat+','+drop_lng;
          
            directionsService.route({
                origin      : origin_lat_lng,
                destination : drop_lat_lng,
                travelMode  : 'DRIVING'
                }, function(response, status) {
                    if (status === 'OK') {
                        directionsDisplay.setDirections(response);
                    } else {
                        // alert('Unable to load route details!');
                        showAlert('Unable to load route details!');
                        //confirmOK();
                        console.log('Directions request failed due to ' + status);
                    }
            });
        }
    }

    function validFormDetails()
    {
        var is_valid_frm  = $('#frm_packaging_details').parsley().validate();

        if(is_valid_frm == false)
        {
            return false;
        }
        $.ajax({
            url:BASE_URL+'/ride/get_fair_estimate',
            type:'POST',
            data:$('#frm_packaging_details').serialize(),
            beforeSend:function(){
                $('#btn_go').prop('disabled', true);
                $('#btn_go').html("<i class='fa fa-spinner fa-spin'></i>");
                $('#div_fare_estimate').hide();
                $('#div_vehicle_type_name').html('');
                // $('#div_per_mile_charge').html('');
                $('#div_distance_in_miles').html('');
                $('#div_total_fare').html('');

                $('#div_error_html').html('');
                $('#div_show_error').hide();

            },
            success:function(response)
            {
                if(response.status=="success")
                {
                    $('#div_fare_estimate').show();

                    $('#btn_go').prop('disabled', false);
                    $('#btn_go').html("Go!");

                    /*$('#btn_submit').prop('disabled', false);
                    $('#btn_submit').html("Send Now");
                    $( '#validation-form' ).each(function(){
                        this.reset();
                    });
                    grecaptcha.reset();

                    var genrated_html = '<div class="alert alert-success">'+
                                            '<strong>Success ! </strong>'+ response.msg+''+
                                        '</div>';

                    $('#success_div').html(genrated_html);
                    setTimeout(function(){
                        $('#success_div').html('');
                    },8000);*/

                    $('#div_vehicle_type_name').html(response.data.vehicle_type);
                    // $('#div_per_mile_charge').html('$ '+response.data.per_miles_price);
                    $('#div_distance_in_miles').html(response.data.trip_distance);
                    $('#div_total_fare').html('$ '+response.data.calculated_farecharge);

                }
                else
                {
                    if($('#pickup_lat').val()!='')
                    {

                        $('#div_fare_estimate').hide();
                        $('#div_error_html').html(response.msg);
                        $('#div_show_error').show();
                    }
                    
                    $('#div_vehicle_type_name').html('');
                    // $('#div_per_mile_charge').html('');
                    $('#div_distance_in_miles').html('');
                    $('#div_total_fare').html('');

                    $('#btn_go').prop('disabled', false);
                    $('#btn_go').html("Go!");

                }
                return false;
            }
        });
    }

    function hideErrorDiv()
    {
        $('#div_error_html').html('');
        $('#div_show_error').hide();
    }

    function confirmOK()
    {
        $('#div_fare_estimate').hide();
        $('#div_error_html').html();
        $('#div_show_error').hide();
        
        $('#div_vehicle_type_name').html('');
        // $('#div_per_mile_charge').html('');
        $('#div_distance_in_miles').html('');
        $('#div_total_fare').html('');

        $('#btn_go').prop('disabled', false);
        $('#btn_go').html("Go");

        directionsDisplay.setMap(null);
        
        source_marker.setMap(null);
        destination_marker.setMap(null);

        // directionsDisplay.setMap(map);
        map.setZoom(2); 

        $('#frm_packaging_details').each(function(){
            this.reset();
        });
        

    }
</script>

@stop