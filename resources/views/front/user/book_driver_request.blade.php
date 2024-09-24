 @extends('front.layout.master')                

    @section('main_content')

    <?php $user_path     = config('app.project.role_slug.user_role_slug'); ?>

    <div class="blank-div"></div>
    <div class="email-block">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6 col-md-6 col-lg-6">
                    <div class="headding-text-bredcr">
                       My Job Details
                    </div>
                </div>
                <div class="col-sm-6 col-md-6 col-lg-6">
                    <div class="bredcrum-right">
                        <a href="{{ $module_url_path }}" class="bredcrum-home"> Dashboard </a>
                        <span class="arrow-righ"><i class="fa fa-angle-right"></i></span>
                        My Job Details
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style type="text/css">
      .back-btn-section{float: left;}
      .success-class {color: #069611;font-size: 13px;}
    </style>

    <!--dashboard page start-->
    <div class="main-wrapper">
      <div class="container-fluid">
        <div class="row">
          @include('front.user.left_bar')
            <div class="middle-bar">

            @include('front.layout._operation_status')
            
            <div id="show_success_div"></div>
            <div id="show_error_div"></div>
          
              <div class="edit-posted-bg-main delevery-request first_step_div" id="first_step_div"  >
                  <div class="delivery-request-main-wrapper" style="height: 460px" id="dvMap"></div>  
                  <div class="delivery-request-map-inner">
                    
                    
                    <div class="form-group">
                        <span class="delive-reque-icon"></span>
                        <input type="text" id="pickup_location" name="pickup_location" placeholder="Address For Pickup" >
                    </div>

                    <div class="form-group">
                      <span class="delive-reque-icon"></span>
                        <input id="drop_location" name="drop_location" placeholder="Destination" >
                    </div>      
                  </div>
                  <div class="min-close-box">
                  <button class="close-box" type="button" onclick="confirmClearDetails();"><span>X</span></button>
                </div>

                <div class="estimate-cost est-left">
                    <h2>Estimate Time</h2>
                    <span>12:30</span>
                </div>

                <div class="estimate-cost">
                    <h2>Estimate Cost</h2>
                    <span>$300</span>
                </div>  

                  <div class="delivery-reques-bottom-img-wrap content-d">
                    
                    <div class="delivery-loacation-block">
                      <div class="loacation-block">
                          <span>Pickup Loacation :</span>
                          Nashik, Maharashtra, India
                      </div>
                                    
                      <div class="loacation-block">
                         <span> Drop Loacation:</span>
                             Nashik Road, Nashik, Maharashtra, India
                      </div>
                      <div class="clearfix"></div> 
                    </div>


                      <div class="delivery-reques-bottom-img-box">
                      <?php
                              $current_selected_vehicle_type_id = 0;
                              $current_selected_drivers_count = 0;
                      ?>
                      @if(isset($arr_load_post_details['arr_driver_vehicle_type']) && sizeof($arr_load_post_details['arr_driver_vehicle_type'])>0)
                        @foreach($arr_load_post_details['arr_driver_vehicle_type'] as $key => $vehicle_type)
                          <?php
                                $active_clas = '';
                                if($key == 0){
                                  $active_clas = 'active';
                                  $current_selected_vehicle_type_id = isset($vehicle_type['id']) ? $vehicle_type['id'] : 0;
                                  $current_selected_drivers_count = isset($vehicle_type['driver_count']) ? $vehicle_type['driver_count'] : 0;
                                }

                                $driver_count_label = 'Driver';

                                $driver_count = isset($vehicle_type['driver_count']) ? $vehicle_type['driver_count'] : 0;

                                if($driver_count>1)
                                {
                                  $driver_count_label = 'Drivers';
                                }
                                $vehicle_type_slug = isset($vehicle_type['vehicle_type_slug']) ? $vehicle_type['vehicle_type_slug'] : '';

                                switch ($vehicle_type_slug) {

                                    case "sedan":
                                      $default_image = url('images/sedan-img.png');
                                      $default_white_image = url('images/sedan-img-white.png');
                                      break;
                                  
                                    case "suv":
                                      $default_image = url('images/suv-img.png');
                                      $default_white_image = url('images/suv-img-white.png');
                                      break;
                                  
                                    case "pickup-truck":
                                      $default_image = url('images/pickup-truck-img.png');
                                      $default_white_image = url('images/pickup-truck-img-white.png');
                                      break;
                                  
                                    case "cargo-van":
                                      $default_image = url('images/cargo-van-img.png');
                                      $default_white_image = url('images/cargo-van-img-white.png');
                                      break;
                                  
                                    case "10-truck":
                                      $default_image = url('images/10-truck-img.png');
                                      $default_white_image = url('images/10-truck-img-white.png');
                                      break;
                                  
                                    case "26-truck":
                                      $default_image = url('images/26-truck-img.png');
                                      $default_white_image = url('images/26-truck-img-white.png');
                                      break;
                                  
                                    default:
                                      $default_image = url('images/sedan-img.png');
                                      $default_white_image = url('images/sedan-img-white.png');
                                }

                          ?>

                          <div class="delivery-reques-bottom-img-block {{ isset($active_clas) ? $active_clas : '' }} all_vehicle_type" 
                                data-attr-id="{{ isset($vehicle_type['id']) ? $vehicle_type['id'] : 0 }}" 
                                data-attr-driver-count="{{ isset($driver_count) ? $driver_count : 0 }}" 
                                onclick="visibleMarkerByVehicleType(this)"
                                >
                            {{-- <div class="delivery-img-head">{{ isset($driver_count) ? $driver_count : 0 }} {{isset($driver_count_label) ? $driver_count_label : ''}}</div> --}}
                            <div class="delivery-img-circle">
                                 <img src="{{ isset($default_image) ? $default_image : url('images/sedan-img.png')}}" class="delivery-img-gry" />
                                 <img src="{{ isset($default_white_image) ? $default_white_image : url('images/sedan-img-white.png')}}" class="delivery-img-white" />
                             </div>
                            <div class="delivery-img-sub">{{ isset($vehicle_type['vehicle_type']) ? $vehicle_type['vehicle_type'] : '' }}</div>
                          </div>
                        
                        @endforeach
                        <input type="hidden" name="arr_driver_vehicle_type" id="arr_driver_vehicle_type" value="{{ json_encode($arr_load_post_details['arr_driver_vehicle_type']) }}">
                      @else
                        <style type="text/css">
                          .no-jobs-available-section{background: #f4f4f4;border: #f1f1f1;border-radius: 3px;margin: 20px auto;width: 90%;}
                        </style>

                        <div class="no-record-block no-jobs-available-section">
                          <span>No Drivers Available</span> 
                        </div>
                        <input type="hidden" name="arr_driver_vehicle_type" id="arr_driver_vehicle_type" value="{{ json_encode([]) }}">
                      @endif

                      <div class="clearfix"></div>
                    </div>
                     <div class="clearfix"></div>
                 
                     <div class="delivery-by">
                    <h3>Delivery</h3>
                  <div class="driver-requ-button-wrapper">
                    <button type="button" class="green-btn">Now</button>
                    <button type="button" class="green-btn righ">Future Booking</button>
                    <div class="clearfix"></div>
                  </div>
                  </div>

                  </div>

  

                @if(isset($arr_load_post_details['arr_driver_vehicle_type']) && sizeof($arr_load_post_details['arr_driver_vehicle_type'])>0)
                  <div class="clearfix"></div>
                  <div class="driver-requ-date-wrapper" id="div_future_booking_details" style="display: none;">
                      <div class="row">
                          <div class="co-sm-6 col-md-6 col-lg-6">
                              <div class="form-group">
                                  <div class="transac-date-icon"><i class="fa fa-calendar"></i></div>
                                  <input type="text" id="future_request_date" name="future_request_date" placeholder="Future Booking Date">
                                  <div class="error" id="error_future_request_date"></div>
                              </div>
                          </div>
                          <div class="co-sm-6 col-md-6 col-lg-6">
                              <div class="form-group">
                                  <div class="transac-date-icon clock"><i class="fa fa-clock-o"></i></div>
                                    <input type="text" class="timepicker-default" id="request_time" name="request_time" placeholder="Future Booking Time">
                                  <div class="error" id="error_request_time"></div> 
                              </div>
                          </div>
                      </div>
                  </div>
                  <input type="hidden" name="is_future_request" value="0">

                 

                  <div class="driver-requ-button-wrapper">
                    <button type="button" class="driver-requ-butto" onclick="sendNotificationtoBookDriver('normal');">Book now</button>
                    <button type="button" class="driver-requ-butto righ" onclick="sendNotificationtoBookDriver('future');">Future Booking</button>
                    <div class="clearfix"></div>
                  </div>
                @endif
              </div>
              
            </div>
            @include('front.user.right_bar')
        </div>
      </div>
    </div>
    
    
   
    
<link href="{{url('css/front/bootstrap-datepicker.min.css')}}" rel="stylesheet" type="text/css" />  
<link href="{{url('css/front/bootstrap-timepicker.min.css')}}" rel="stylesheet" type="text/css" />    
<script type="text/javascript" language="javascript" src="{{url('/')}}/js/front/bootstrap-datepicker.min.js"></script>
<script type="text/javascript" language="javascript" src="{{url('/')}}/js/front/bootstrap-timepicker.js"></script>

   
<!-- date picker js -->
<script>
  //<!--date and time picker js script-->  
  $(function() {
      $("#future_request_date").datepicker({
          todayHighlight: true,
          autoclose: true,
          startDate: new Date(),
      });
      $('.timepicker-default').timepicker();
  });
</script>
<!-- date picker js -->
    
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key={{ isset($google_map_api_key) ? $google_map_api_key : 'AIzaSyCccvQtzVx4aAt05YnfzJDSWEzPiVnNVsY' }}&libraries=places"></script>
<script src="{{ url('/') }}/js/admin/SlidingMarker.js"></script>
<link rel="stylesheet" type="text/css" href="{{ url('/') }}/css/admin/sweetalert.css" />
<script src="{{ url('/') }}/js/admin/sweetalert.min.js"></script>
<script type="text/javascript" src="{{ url('/js/admin') }}/sweetalert_msg.js"></script>

<script type="text/javascript">

    var load_post_request_id = '{{isset($arr_load_post_details['load_post_request_id']) ? $arr_load_post_details['load_post_request_id'] :0}}';
    var pickup_location      = '{{isset($arr_load_post_details['pickup_location']) ? $arr_load_post_details['pickup_location'] :''}}';
    var pickup_lat           = '{{isset($arr_load_post_details['pickup_lat']) ? $arr_load_post_details['pickup_lat'] :0}}';
    var pickup_lng           = '{{isset($arr_load_post_details['pickup_lng']) ? $arr_load_post_details['pickup_lng'] :0}}'
    var drop_location        = '{{isset($arr_load_post_details['drop_location']) ? $arr_load_post_details['drop_location'] :''}}';
    var drop_lat             = '{{isset($arr_load_post_details['drop_lat']) ? $arr_load_post_details['drop_lat'] :0}}';
    var drop_lng             = '{{isset($arr_load_post_details['drop_lng']) ? $arr_load_post_details['drop_lng'] :0}}';

    var current_selected_vehicle_type_id = "{{ isset($current_selected_vehicle_type_id) ? $current_selected_vehicle_type_id : 0 }}"
    var current_selected_drivers_count = "{{ isset($current_selected_drivers_count) ? $current_selected_drivers_count : 0 }}"

    var BASE_URL = "{{url('/')}}";
    var ARR_MAPS_STYLE  = [];
    var STYLE_JSON_FILE = BASE_URL+'/assets/maps_style_2.json';

    function isNumberKey(evt) {
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        
        if (charCode == 190 || charCode == 46 ) 
          return true;
        
        if (charCode > 31 && (charCode < 48 || charCode > 57 )) 
        return false;
        
        return true;
    }

    $( document ).ready(function() {
        $.ajax({
            url: STYLE_JSON_FILE,
            async: false,
            dataType: 'json',
            success: function (response) {
               ARR_MAPS_STYLE = response;
            }
        });

        $('#pickup_location').val(pickup_location);
        $('#drop_location').val(drop_location);

        $('#pickup_location').attr('disabled','true');
        $('#drop_location').attr('disabled','true');
        
        var str_arr_driver_vehicle_type = $('#arr_driver_vehicle_type').val();
        var arr_driver_vehicle_type = [];

        if(str_arr_driver_vehicle_type!=''){
            arr_driver_vehicle_type = JSON.parse(str_arr_driver_vehicle_type);
        } 
        setTimeout(function(){ 
            LoadMap();
            showAllDriverMarkers(arr_driver_vehicle_type);
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

      SetSourceMarker();
      SetDestinationMarker();

    };

    function SetSourceMarker() {
      
        if(source_marker!=undefined){
          source_marker.setMap(null);
        }

        var marker_icon = BASE_URL+'/node_assets/images/marker.png';
      
        var myLatlng = new google.maps.LatLng(pickup_lat,pickup_lng);

        source_marker = new SlidingMarker({
                                    position      : myLatlng,
                                    map           : map,
                                    icon          : marker_icon
                                });

        bounds.extend(myLatlng);
        map.fitBounds(bounds);
        map.setZoom(16);

    };

    function SetDestinationMarker() {
      
        if(destination_marker!=undefined){
          destination_marker.setMap(null);
        }

        var marker_icon = BASE_URL+'/node_assets/images/marker.png';
      
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

    var map_markers = [];

    function showAllDriverMarkers(arr_driver_vehicle_type){
      
      if(arr_driver_vehicle_type!=undefined && arr_driver_vehicle_type.length>0){

        arr_driver_vehicle_type.forEach(function (vt_value,vt_index) {
        
          if (vt_value.arr_driver_details!= undefined && vt_value.arr_driver_details.length>0) {
        
              vt_value.arr_driver_details.forEach(function (d_value, d_index) {
                  
                  var marker_icon = BASE_URL+'/node_assets/images/blue.png';

                  var markerImage = new google.maps.MarkerImage(
                                marker_icon,
                                new google.maps.Size(16,32), //size
                                null, //origin
                                null, //anchor
                                new google.maps.Size(16,32) //scale
                            );

                  var myLatlng = new google.maps.LatLng(d_value.current_latitude,d_value.current_longitude);

                  var marker = new SlidingMarker({
                                              position       : myLatlng,
                                              map            : map,
                                              icon           : markerImage,
                                              vehicle_type_id: vt_value.id,
                                              driver_id      : d_value.driver_id
                                          });

                  bounds.extend(myLatlng);
                  map.fitBounds(bounds);
                  map_markers.push(marker);
                  if(current_selected_vehicle_type_id == vt_value.id){
                    marker.setVisible(true);
                  }
                  else{
                    marker.setVisible(false);
                  }
              });
          }

        })
      }
    }

    function visibleMarkerByVehicleType(ref){
        var vehicle_type_id = $(ref).attr('data-attr-id');
        var drivers_count   = $(ref).attr('data-attr-driver-count');

        if((vehicle_type_id!=undefined) && (drivers_count!=undefined) && (current_selected_vehicle_type_id!=undefined)){
          if(vehicle_type_id!=current_selected_vehicle_type_id){
              $( ".all_vehicle_type" ).each(function( index ) {
                  $( this ).removeClass('active');
              });
              $(ref).addClass('active');
              current_selected_vehicle_type_id = vehicle_type_id;
              current_selected_drivers_count   = drivers_count;
              if (map_markers.length > 0) {
                  map_markers.forEach(function (value, index) {
                      var map_visible = false;
                      if(value.vehicle_type_id == current_selected_vehicle_type_id){   
                        map_visible = true;
                      }
                      value.setVisible(map_visible);
                  });
              }

          }
        }
    }

    function sendNotificationtoBookDriver(type){

        if(current_selected_drivers_count!=undefined && current_selected_drivers_count == 0)
        {
          swal('Drivers not available for selected vehicle type.');
          return;
        }

        if(type!=undefined){
          if(type == 'normal'){

              if($('#div_future_booking_details:visible').length == 1){
                $('#div_future_booking_details').slideUp();
                return;
              }

              swal({
                title: "Are you sure ?",
                text: 'You want to Send request to all available drivers',
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes",
                cancelButtonText: "No",
                closeOnConfirm: false,
                closeOnCancel: true
              },
              function(isConfirm)
              {
                if(isConfirm==true)
                {
                    var obj_data = 
                                  {
                                    _token               : "{{ csrf_token() }}",
                                    vehicle_type_id      : current_selected_vehicle_type_id,
                                    load_post_request_id : load_post_request_id,
                                    request_status       : 'USER_REQUEST',
                                    is_future_request    : '0'
                                  };
                    sendBookRequestToDriver(obj_data);
                }
              });
          }
          else if(type == 'future'){
            
            $('#error_future_request_date').html('');
            $('#error_request_time').html('');
            
            if($('#div_future_booking_details:visible').length == 0){
              $('#div_future_booking_details').slideDown();
              return;
            }

            var flag = 0;
            if($('#future_request_date').val() == '')
            {
              $('#error_future_request_date').html('Please enter future booking date.');
              flag = 1;
            }
            if($('#request_time').val() == '')
            {
              $('#error_request_time').html('Please enter future booking time.');
              flag = 1;
            }
            if(flag == 1)
            {
              return false;
            }

            var obj_data = {
                              _token               : "{{ csrf_token() }}",
                              vehicle_type_id      : current_selected_vehicle_type_id,
                              load_post_request_id : load_post_request_id,
                              request_status       : 'USER_REQUEST',
                              is_future_request    : '1',
                              future_request_date  : $('#future_request_date').val(),
                              request_time         : $('#request_time').val()
                          };
            
            sendBookRequestToDriver(obj_data);
            
            

          }
        }
    }


    var USER_PANEL_URL = "{{url('/').'/'.$user_path}}";
    var PROCESS_TO_BOOK_DRIVER_URL = "{{url('/').'/'.$user_path.'/process_to_book_driver'}}";

    function confirmClearDetails(){

      swal({
              title: "Are you sure ?",
              text: 'You want to cancel,this booking is not yet completed, you can still select the driver from the my bookings section',
              type: "warning",
              showCancelButton: true,
              confirmButtonColor: "#DD6B55",
              confirmButtonText: "Yes",
              cancelButtonText: "No",
              closeOnConfirm: false,
              closeOnCancel: true
            },
            function(isConfirm)
            {
              if(isConfirm==true)
              {
                  window.location.href = USER_PANEL_URL+'/delivery_request';
              }
            });
    }

    
    function sendBookRequestToDriver(obj_data){
        
        $.ajax({
            url:PROCESS_TO_BOOK_DRIVER_URL,
            type:'POST',
            data : obj_data,
            beforeSend:function(){
              swal.close();
              showProcessingOverlay();
            },
            success:function(response)
            {
                if(response.status=="success")
                { 
                    /*redirect user to next scrren to search and select drivers*/
                    var redirect_url = USER_PANEL_URL+'/my_booking?trip_type=PENDING';
                    window.location.href = redirect_url;
                }
                if(response.status=="error")
                {
                    hideProcessingOverlay();
                    var error_html = '';
                    error_html += '<div class="alert alert-danger">';
                    error_html +=   '<button type="button" class="close" style="margin-top: 0px !important;padding: 0px !important;" data-dismiss="alert" aria-hidden="true">×</button>';
                    error_html +=      ''+response.msg+'';
                    error_html +=  '</div>';
                    $('#show_error_div').html(error_html);
                }
                return false;
            }
        });
    }

</script>

<script type="text/javascript">
  /*$(document).ready(function(){
    if($(".left-bar").height() != "undefined"){
      $(".edit-posted-bg-main").css("height", $(".left-bar").height()); 
    }
  });*/
</script>

@stop