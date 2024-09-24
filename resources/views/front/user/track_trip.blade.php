 @extends('front.layout.master')                

    @section('main_content')

    <?php $user_path     = config('app.project.role_slug.user_role_slug'); ?>

    <style type="text/css">
        .sedan-new-class {width: 31px !important;height: 43px !important;}
        .sedan-new-class img {left: 0 !important;right: 0 !important;margin: 0 auto !important;}
        .cargo-van-new-class {width: 35px !important;height: 50px !important;}
        .cargo-van-new-class img {left: 0 !important;right: 0 !important;margin: 0 auto !important;}
        
    </style>

    <div class="blank-div"></div>
    <div class="email-block">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6 col-md-6 col-lg-6">
                    <div class="headding-text-bredcr">
                       Track Trip Details
                    </div>
                </div>
                <div class="col-sm-6 col-md-6 col-lg-6">
                    <div class="bredcrum-right">
                        <a href="{{ $module_url_path }}" class="bredcrum-home"> Dashboard </a>
                        <span class="arrow-righ"><i class="fa fa-angle-right"></i></span>
                        Track Trip Details
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!--dashboard page start-->
    <div class="main-wrapper">
        <div class="container-fluid">
            <div class="row">
                @include('front.user.left_bar')
                <div class="col-sm-9 col-md-8 col-lg-8">
                @include('front.layout._operation_status')
                    <div class="rating-white-block my-job">
                                <div class="row">
                                    <div class="col-sm-10 col-md-10 col-lg-10">
                                        <div class="review-profile-image">
                                            <img src="{{ isset($arr_trip_details['profile_image']) ? $arr_trip_details['profile_image'] : url('/uploads/default-profile.png') }}" alt="" />
                                        </div>
                                        
                                        <div class="review-content-block">
                                            <div class="review-send-head">
                                               {{isset($arr_trip_details['first_name']) ? $arr_trip_details['first_name'] : ''}} {{isset($arr_trip_details['last_name']) ? $arr_trip_details['last_name'] : ''}}
                                            </div>
                                            <div class="review-send-head-small-date"><i class="fa fa-calendar"></i> {{ isset($arr_trip_details['booking_date']) ? date('d M Y',strtotime($arr_trip_details['booking_date'])) : '' }} </div>

                                            @if(isset($arr_trip_details['booking_status']) && $arr_trip_details['booking_status'] == 'TO_BE_PICKED')
                                            <div class="my-job-address-block">
                                                <div class="my-job-address-head">
                                                    Pickup Location :
                                                </div>
                                                <div class="my-job-address-right">
                                                     {{isset($arr_trip_details['pickup_location']) ? $arr_trip_details['pickup_location'] : ''}}
                                                </div>
                                                <div class="clearfix"></div>
                                            </div>
                                            @endif
                                            @if(isset($arr_trip_details['booking_status']) && $arr_trip_details['booking_status'] == 'IN_TRANSIT')
                                            <div class="my-job-address-block">
                                                <div class="my-job-address-head">
                                                    Drop Location :
                                                </div>
                                                <div class="my-job-address-right">
                                                    {{isset($arr_trip_details['drop_location']) ? $arr_trip_details['drop_location'] : ''}}
                                                </div>
                                                <div class="clearfix"></div>
                                            </div>
                                            @endif
                                            <div class="my-job-address-block">
                                                <div class="my-job-address-head">
                                                    Contact No :
                                                </div>
                                                <div class="my-job-address-right">
                                                    {{isset($arr_trip_details['mobile_no']) ? $arr_trip_details['mobile_no'] : ''}}
                                                </div>
                                                <div class="clearfix"></div>
                                            </div>

                                            <div class="my-job-address-block">
                                                <div class="my-job-address-head">
                                                    Estimate Time :
                                                </div>
                                                <div class="my-job-address-right" id="div_estimate_time">
                                                    0 Min.
                                                </div>
                                                <div class="clearfix"></div>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="col-sm-4 col-md-2 col-lg-2">
                                        @if(isset($arr_trip_details['booking_status']) && $arr_trip_details['booking_status'] == 'TO_BE_PICKED')
                                            <div class="my-lob-completed" style="width: 100px;">To Be Picked</div>
                                        @elseif(isset($arr_trip_details['booking_status']) && $arr_trip_details['booking_status'] == 'IN_TRANSIT')
                                            <div class="my-lob-completed pending">In Transit</div>
                                        @endif
                                    </div>

                                </div>
                            </div>
                            
                    <div class="edit-posted-bg-main my-job-details">
                        <div class="my-job-details">Track Trip</div>
                        <div id="dvMap" style="height: 620px"></div>

                        <br>

                        <div class="btns-wrapper change-pass">
                        @if(isset($arr_trip_details['booking_status']) && $arr_trip_details['booking_status'] == 'TO_BE_PICKED')
                            <a href="javascript:void(0);"><button type="button" data-id="{{ isset($arr_trip_details['booking_id']) ? base64_encode($arr_trip_details['booking_id']) : 0 }}" onclick="confirmCancelTrip(this);" class="white-btn chan-left">Cancel Trip</button></a>
                        @endif
                        @if(isset($redirect_back) && $redirect_back == 'booking')
                           <a href="{{ url('/').'/'.$user_path.'/my_booking?trip_type=ONGOING'}}"><button type="button" class="white-btn chan-left">Back</button></a>
                        @elseif(isset($redirect_back) && $redirect_back == 'track_driver')
                           <a href="{{ url('/').'/'.$user_path.'/track_driver'}}"><button type="button" class="white-btn chan-left">Back</button></a>
                        @else
                            <a href="javascript:void(0);"><button type="button" class="white-btn chan-left">Back</button></a>
                        @endif
                        </div>
                    </div>
                            
                    
                </div>
                @include('front.user.right_bar')
            </div>
        </div>
    </div>

 <!-- popup section start -->
<div class="mobile-popup-wrapper">
<div id="cancellation-reason-popup" class="modal fade" role="dialog" data-backdrop="static">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal"><!--&times;--></button>
        <h4 class="modal-title">Cancellation Reason</h4>
      </div>
      <div class="modal-body">
            <div id="div_error_show"></div>
            <div class="form-group marg-top">
            <textarea id="reason" name="reason" placeholder="Enter Cancellation Reason"></textarea>
                <div id="err_cancellation_reason" class="error-red"></div>
            </div>
            <div class="login-btn popup"><a onclick="checkValidCancellationReason()" href="javascript:void(0)">Submit</a></div>
      </div>
    </div>

  </div>
</div>   
</div>
 <!-- popup section end -->       

<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key={{ isset($google_map_api_key) ? $google_map_api_key : 'AIzaSyCccvQtzVx4aAt05YnfzJDSWEzPiVnNVsY' }}&libraries=places"></script>
<script src="{{ url('/') }}/js/admin/SlidingMarker.js"></script>
<script src="{{ url('/') }}/js/admin/markerAnimate.js"></script>
<script src="{{url('/node_assets')}}/jqueryeasing.js"></script>

<link rel="stylesheet" type="text/css" href="{{ url('/') }}/css/admin/sweetalert.css" />
<script src="{{ url('/') }}/js/admin/sweetalert.min.js"></script>
<script type="text/javascript" src="{{ url('/js/admin') }}/sweetalert_msg.js"></script>

<script type="text/javascript">
    
    var booking_id      = '{{isset($arr_trip_details['booking_id']) ? base64_encode($arr_trip_details['booking_id']) :0}}';
    var pickup_location = '{{isset($arr_trip_details['pickup_location']) ? $arr_trip_details['pickup_location'] :''}}';
    var pickup_lat      = '{{isset($arr_trip_details['pickup_lat']) ? $arr_trip_details['pickup_lat'] :0}}';
    var pickup_lng      = '{{isset($arr_trip_details['pickup_lng']) ? $arr_trip_details['pickup_lng'] :0}}'
    var drop_location   = '{{isset($arr_trip_details['drop_location']) ? $arr_trip_details['drop_location'] :''}}';
    var drop_lat        = '{{isset($arr_trip_details['drop_lat']) ? $arr_trip_details['drop_lat'] :0}}';
    var drop_lng        = '{{isset($arr_trip_details['drop_lng']) ? $arr_trip_details['drop_lng'] :0}}';


    var BASE_URL = "{{url('/')}}";
    var ARR_MAPS_STYLE  = [];
    var STYLE_JSON_FILE = BASE_URL+'/assets/maps_style_2.json';

    var USER_BASE_URL = "{{url('/').'/'.$user_path.'/'}}";

    var TRACKING_BASE_URL = "{{url('/').'/'.$user_path.'/track_live_trip'}}";

    TRACKING_BASE_URL = TRACKING_BASE_URL+'?booking_id='+booking_id;

    var CANCEL_TRIP_URL = '{{ url('/').'/'.$user_path.'/process_cancel_trip'}}';
    
    function confirmCancelTrip(ref) 
    {
        if(booking_id!=undefined && booking_id!=''){
            $('#cancellation-reason-popup').modal('toggle');
        }
    }

    function checkValidCancellationReason()
    {
        $('#err_cancellation_reason').html('');
        $('#div_error_show').html('');

        if($('#reason').val() == '')
        {
            $('#err_cancellation_reason').html('Please enter cancellation reason');
            return false; 
        }  
        var obj_data = new Object();

        obj_data._token     = "{{ csrf_token() }}";
        obj_data.reason     = $('#reason').val();
        obj_data.booking_id = booking_id;
        
        $.ajax({
            url:CANCEL_TRIP_URL,
            type:'POST',
            data:obj_data,
            dataType:'json',
            beforeSend:function(){
                showProcessingOverlay();
            },
            success:function(response)
            {
                hideProcessingOverlay();
                if(response.status=="success")
                {
                    $('#cancellation-reason-popup').modal('toggle');
                    swal("Success!", response.msg, "success");

                    setTimeout(function(){ 
                        window.location.href = USER_BASE_URL+'my_booking?trip_type=CANCELED';
                    }, 3000);

                    return false;
                }
                else if(response.status=="error")
                {
                    swal("Error!", response.msg, "error");
                    return false;
                }
                return false;
            },error:function(res){
                hideProcessingOverlay();
            }    
        });

        return false; 
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

        setTimeout(function(){ 
            LoadMap();
        }, 2000);

        setInterval(function(){ 
            track_live_trip();
        }, 5000);

    });

    var map;
    var bounds;
    var marker;
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

        directionsService = new google.maps.DirectionsService;
        directionsDisplay = new google.maps.DirectionsRenderer({suppressMarkers: true});
        directionsDisplay.setMap(map);

        SetMarker();
    };

    function track_live_trip() {
        $.ajax({
                url:TRACKING_BASE_URL,
                type:'GET',
                dataType:'json',
                success:function(response)
                {
                    if(response.status == 'success'){

                        if(response.data.driver_duration!=undefined){
                            $('#div_estimate_time').html(response.data.driver_duration);
                        }
                        show_on_map_available_driver(response.data);
                    }   
                }     
        });
    }

    function SetMarker() {

        var pickupMarkerImage = new google.maps.MarkerImage(
                                    BASE_URL+'/node_assets/images/pointer.png',
                                    new google.maps.Size(36,36), //size
                                    null, //origin
                                    null, //anchor
                                    new google.maps.Size(36,36) //scale
                                );

        var pickupLatlng = new google.maps.LatLng(pickup_lat, pickup_lng);
        pickup_marker    = new SlidingMarker({
                                      position: pickupLatlng,
                                      map: map,
                                      title : pickup_location,
                                      icon: pickupMarkerImage
                                  });
        
        var dropMarkerImage = new google.maps.MarkerImage(
                                    BASE_URL+'/node_assets/images/pointer.png',
                                    new google.maps.Size(36,36), //size
                                    null, //origin
                                    null, //anchor
                                    new google.maps.Size(36,36) //scale
                                );

        var dropLatlng = new google.maps.LatLng(drop_lat, drop_lng);
        destination_marker = new SlidingMarker({
                                      position: dropLatlng,
                                      map: map,
                                      title : drop_location,
                                      icon: dropMarkerImage
                                  });

        calculateAndDisplayRoute(directionsService, directionsDisplay);
    };

    function show_on_map_available_driver(obj_available_driver){
        
        if(obj_available_driver.booking_status == 'COMPLETED'){
          setTimeout(function(){ 
            window.location.href = USER_BASE_URL+'my_booking?trip_type=COMPLETED';
          }, 2000);
        }
        if(marker!=undefined && marker!=''){
            if (marker.driver_id != undefined) {
                if ((marker.driver_id == obj_available_driver.driver_id)) {
                    
                    var prev_lat = marker.getPosition().lat();
                    var prev_lng = marker.getPosition().lng();
                  
                    var curr_lat = parseFloat(obj_available_driver.driver_lat);
                    var curr_lng = parseFloat(obj_available_driver.driver_lng);

                    var bearing = getBearing(prev_lat,prev_lng,curr_lat,curr_lng);
                    bearing = parseInt(bearing);

                    var myLatlng = new google.maps.LatLng(obj_available_driver.driver_lat,obj_available_driver.driver_lng);
                    marker.setDuration('6000');
                    marker.setEasing('linear');
                    marker.setPosition(myLatlng);

                    map.panTo(myLatlng);

                    var vehicle_type_slug = 'sedan';
                    if(marker.vehicle_type_slug!=undefined && marker.vehicle_type_slug!=''){
                        vehicle_type_slug = marker.vehicle_type_slug
                    }

                    $('#markerLayer img').parent().last().addClass(vehicle_type_slug+'-new-class');

                    $('#markerLayer img').last().css({
                        'transform':'rotate('+bearing+'deg)'
                    });
                }
            }          
        }
        else{
          SetRideMarker(obj_available_driver);
        }

    }

    function radians(n) {
      return n * (Math.PI / 180);
    }
    
    function degrees(n) {
      return n * (180 / Math.PI);
    }

    function getBearing(startLat,startLong,endLat,endLong){
      startLat = radians(startLat);
      startLong = radians(startLong);
      endLat = radians(endLat);
      endLong = radians(endLong);

      var dLong = endLong - startLong;

      var dPhi = Math.log(Math.tan(endLat/2.0+Math.PI/4.0)/Math.tan(startLat/2.0+Math.PI/4.0));
      if (Math.abs(dLong) > Math.PI){
        if (dLong > 0.0)
           dLong = -(2.0 * Math.PI - dLong);
        else
           dLong = (2.0 * Math.PI + dLong);
      }

      return (degrees(Math.atan2(dLong, dPhi)) + 360.0) % 360.0;
    }

    function calculateAndDisplayRoute(directionsService, directionsDisplay) {

      var origin_lat_lng      = pickup_lat+','+pickup_lng;
      var destination_lat_lng = drop_lat+','+drop_lng;
      
      directionsService.route({
        origin      : origin_lat_lng,
        destination : destination_lat_lng,
        travelMode  : 'DRIVING'
      }, function(response, status) {
        if (status === 'OK') {
          directionsDisplay.setDirections(response);
        } else {
          console.log('Directions request failed due to ' + status);

        }
      });
    }

    function SetRideMarker(data) {

        // var marker_icon = BASE_URL+'/node_assets/images/blue.png';

        // vehicle_type_slug

        var vehicle_type_slug = '';
        if(data.vehicle_type_slug!=undefined && data.vehicle_type_slug!=''){
            vehicle_type_slug = data.vehicle_type_slug
        }

        var marker_icon = BASE_URL+'/node_assets/images/vehicle_type_images/sedan.png';

        switch (vehicle_type_slug) { 
            case 'sedan':
                marker_icon = BASE_URL+'/node_assets/images/vehicle_type_images/sedan.png';
                break;
            case 'suv':
                marker_icon = BASE_URL+'/node_assets/images/vehicle_type_images/suv.png';
                break;
            case 'pickup-truck':
                marker_icon = BASE_URL+'/node_assets/images/vehicle_type_images/pickup-truck.png';
                break;
            case 'cargo-van':
                marker_icon = BASE_URL+'/node_assets/images/vehicle_type_images/cargo-van.png';
                break;
            case '10-truck':
                marker_icon = BASE_URL+'/node_assets/images/vehicle_type_images/10-truck.png';
                break;
            case '26-truck':
                marker_icon = BASE_URL+'/node_assets/images/vehicle_type_images/26-truck.png';
                break;
            default:
              marker_icon = BASE_URL+'/node_assets/images/vehicle_type_images/sedan.png';
        }

        var markerImage = new google.maps.MarkerImage(
                                    marker_icon,
                                    new google.maps.Size(14,30), //size
                                    null, //origin
                                    null, //anchor
                                    new google.maps.Size(14,30) //scale
                                );

        var myLatlng = new google.maps.LatLng(data.driver_lat,data.driver_lng);

        marker = new SlidingMarker({
                                        position: myLatlng,
                                        map: map,
                                        icon: markerImage,
                                        driver_id: data.driver_id,
                                        vehicle_type_slug : vehicle_type_slug
                                    });

        var overlay = new google.maps.OverlayView();
        overlay.draw = function() {
            this.getPanes().markerLayer.id = 'markerLayer'
        };
        overlay.setMap(map);

    }

</script>
@stop