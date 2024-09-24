@extends('company.layout.master')                
@section('main_content')

<style type="text/css">
  .ui-autocomplete
  {
    max-width: 26% !important;
  }
  .mass_min {
    background: #fcfcfc none repeat scroll 0 0;
    border: 1px dashed #d0d0d0;
    float: left;
    margin-bottom: 20px;
    margin-right: 21px;
    margin-top: 10px;
    padding: 5px;
  }
  .mass_addphoto {
    display: inline-block;
    margin: 0 10px;
    padding-top: 27px;
    text-align: center;
    vertical-align: top;
  }
  .mass_addphoto {
    text-align: center;
  }
  .upload_pic_btn {
    cursor: pointer;
    font-size: 14px;
    height: 100% !important;
    left: 0;
    margin: 0;
    opacity: 0;
    padding: 0;
    position: absolute;
    right: 0;
    top: 0;
  }
</style>

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
      <i class="fa fa-home">
      </i>
      <a href="{{ url($company_panel_slug.'/dashboard') }}">Dashboardnn
      </a>
    </li>
    <span class="divider">
      <i class="fa fa-angle-right">
      </i>
      <i class="fa fa-users faa-vertical animated-hover">
      </i>
      
      <a href="{{ url($module_url_path) }}/booking_history" class="call_loader">{{ $module_title or ''}}
      </a>
    </span> 
    <span class="divider">
      <i class="fa fa-angle-right">
      </i>
      <i class="fa fa-eye">
      </i>
    </span> 
    <li class="active">   {{ $page_title or '' }}
    </li>
  </ul>
</div>
<!-- END Breadcrumb -->
<!-- BEGIN Main Content -->
<div class="row">
  <div class="col-md-12">
    <div class="box ">
      <div class="box-title">
        <h3>
          <i class="fa fa-eye">
          </i> {{ $page_title or '' }} 
        </h3>
        <div class="box-tool">
        </div>
      </div>
      <div class="box-content">
        <?php
        
            $arr_data = filter_completed_trip_details($arr_bookings);
           
        ?>
        <div class="box">
          <div class="box-content studt-padding">
            <div class="row">
                <div class="col-md-12">
                  <h3>{{$page_title or ''}}</h3>
                  <br>
                    <table class="table table-bordered">
                      <tbody>
                            
                            <tr>
                              <th style="width: 20%">Booking ID
                              </th>
                              <td style="width: 30%">
                                
                                {{isset($arr_data['booking_unique_id']) ? $arr_data['booking_unique_id'] : '' }}
                              </td>
                              <th style="width: 20%">Booking Date
                              </th>
                              <td style="width: 30%">
                                {{isset($arr_data['booking_date']) ? $arr_data['booking_date'] : '' }}
                              </td>
                            </tr> 

                            <tr>
                              <th style="width: 20%">Ride Start Date
                              </th>
                              <td style="width: 30%">
                                {{isset($arr_data['start_datetime']) ? $arr_data['start_datetime'] : '' }}
                              </td>
                              <th style="width: 20%">Ride End Date
                              </th>
                              <td style="width: 30%">
                                {{isset($arr_data['end_datetime']) ? $arr_data['end_datetime'] : '' }}
                              </td>
                            </tr> 

                            <tr>
                              <th style="width: 20%">User Name
                              </th>
                              <td style="width: 30%">
                                {{isset($arr_data['user_name']) ? $arr_data['user_name'] : '' }}
                              </td>
                              <th style="width: 20%">Driver Name
                              </th>
                              <td style="width: 30%">
                                {{isset($arr_data['driver_name']) ? $arr_data['driver_name'] : '' }} <strong>({{isset($arr_data['company_name']) ? $arr_data['company_name'] : '' }})</strong>
                              </td>
                            </tr> 

                            <tr>
                              <th style="width: 20%">User Contact No.
                              </th>
                              <td style="width: 30%">
                                {{isset($arr_data['user_country_code']) ? $arr_data['user_country_code'] : '' }} {{isset($arr_data['user_contact_no']) ? $arr_data['user_contact_no'] : '' }}
                              </td>
                              <th style="width: 20%">Driver Contact No.
                              </th>
                              <td style="width: 30%">
                                {{isset($arr_data['driver_country_code']) ? $arr_data['driver_country_code'] : '' }}  {{isset($arr_data['driver_contact_no']) ? $arr_data['driver_contact_no'] : '' }}
                              </td>
                            </tr> 

                            <tr>
                              <th style="width: 20%">User Email
                              </th>
                              <td style="width: 30%">
                                {{isset($arr_data['user_email']) ? $arr_data['user_email'] : '' }}
                              </td>
                              <th style="width: 20%">Driver Email
                              </th>
                              <td style="width: 30%">
                                {{isset($arr_data['driver_email']) ? $arr_data['driver_email'] : '' }}
                              </td>
                            </tr> 

                            <tr>
                              <th style="width: 20%">Vehicle Type
                              </th>
                              <td style="width: 30%">
                                {{isset($arr_data['vehicle_type']) ? $arr_data['vehicle_type'] : '' }}
                              </td>
                              <th style="width: 20%">Vehicle Number
                              </th>
                              <td style="width: 30%">
                                {{isset($arr_data['vehicle_number']) ? $arr_data['vehicle_number'] : '' }}
                              </td>
                            </tr> 

                           {{--  <tr>
                              <th style="width: 20%">Vehicle Number
                              </th>
                              <td style="width: 30%">
                                {{isset($arr_data['vehicle_number']) ? $arr_data['vehicle_number'] : '' }}
                              </td>
                              <th style="width: 20%">Vehicle Model
                              </th>
                              <td style="width: 30%">
                                {{isset($arr_data['vehicle_model']) ? $arr_data['vehicle_model'] : '' }}
                              </td>
                            </tr>  --}}

                            <tr>
                              <th style="width: 20%">Pick up location
                              </th>
                              <td style="width: 30%">
                                {{isset($arr_data['pickup_location']) ? $arr_data['pickup_location'] : '' }}
                              </td>
                              <th style="width: 20%">Drop up location
                              </th>
                              <td style="width: 30%">
                                {{isset($arr_data['drop_location']) ? $arr_data['drop_location'] : '' }}
                              </td>
                            </tr> 
                         
                             <tr>
                              <th style="width: 20%">Ride Status
                              </th>
                              <td style="width: 30%">
                                @if(isset($arr_data['booking_status']) &&  $arr_data['booking_status'] == 'TO_BE_PICKED')
                                  <span class='badge badge-info' style="width:115px">To Be Picked</span>
                                @endif
                              </td>
                            </tr> 

                            

                    </tbody>
                  </table>

                  <div id="dvMap" style="width: 1550px; height: 600px"></div>
                  <br> 
                  
                  @if(isset($previous_page) && $previous_page == 'booking_summary')
                    <center><a class="btn" href="{{ url('/admin/booking_summary') }}">Back</a></center>
                  @elseif(isset($previous_page) && $previous_page == 'booking_history')
                    <center><a class="btn" href="{{ url($module_url_path.'/booking_history') }}">Back</a></center>
                  @else
                    <center><a class="btn" href="{{ url($module_url_path) }}">Back</a></center>
                  @endif

                {{-- </div>  --}}

              </div>
          </div>
        </div>
        </div>
      </div>
    </div>
  </div>

  <!-- END Main Content --> 
  <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key={{ isset($google_map_api_key) ? $google_map_api_key : 'AIzaSyCccvQtzVx4aAt05YnfzJDSWEzPiVnNVsY' }}"></script>
  <script src="https://code.jquery.com/jquery-1.11.1.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/marker-animate-unobtrusive/0.2.8/vendor/markerAnimate.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/marker-animate-unobtrusive/0.2.8/SlidingMarker.min.js"></script>
  
  <script src="{{url('/node_assets')}}/jqueryeasing.js"></script>

  <script type="text/javascript">

    var BASE_URL        = '{{url('/')}}';
    var MODULE_URL_PATH = '{{$module_url_path}}';
  
    var booking_id  = '{{isset($arr_data['booking_id']) ? $arr_data['booking_id'] :0}}';

    var pickup_location = '{{isset($arr_data['pickup_location']) ? $arr_data['pickup_location'] :''}}';
    var pickup_lat      = '{{isset($arr_data['pickup_lat']) ? $arr_data['pickup_lat'] :0}}';
    var pickup_lng      = '{{isset($arr_data['pickup_lng']) ? $arr_data['pickup_lng'] :0}}'
    var drop_location   = '{{isset($arr_data['drop_location']) ? $arr_data['drop_location'] :''}}';
    var drop_lat        = '{{isset($arr_data['drop_lat']) ? $arr_data['drop_lat'] :0}}';
    var drop_lng        = '{{isset($arr_data['drop_lng']) ? $arr_data['drop_lng'] :0}}';

    $( document ).ready(function() {
        setTimeout(function(){ 
            LoadMap()
        }, 2000);

        setInterval(function(){ 
        // setTimeout(function(){ 
            track_available_vehicles();
        }, 5000);
    });

    function track_available_vehicles() {
        $.ajax({
                url:MODULE_URL_PATH+'/track_current_booking?status=TO_BE_PICKED&enc_id='+booking_id,
                type:'GET',
                dataType:'json',
                success:function(response)
                {
                    if(response.status == 'success'){
                        show_on_map_available_driver(response.current_booking);
                    }   
                }     
        });
    }
    function show_on_map_available_driver(obj_available_driver){
      
        if(obj_available_driver.booking_status == 'IN_TRANSIT'){
          setTimeout(function(){ 
            var booking_master_id     = btoa(parseInt(obj_available_driver.booking_master_id));
            var booking_status = btoa(obj_available_driver.booking_status);
            view_url = BASE_URL+'/company/track_booking/view?enc_id='+booking_master_id+'&status='+booking_status+'&curr_page=track_booking';
            window.location.href = view_url;
          }, 2000);
        }
        if(marker!=undefined && marker!=''){
          if (marker.driver_id != undefined) {
              if ((marker.driver_id == obj_available_driver.driver_id)) {

                  var myLatlng = new google.maps.LatLng(obj_available_driver.current_latitude,obj_available_driver.current_longitude);
                  marker.setDuration('1000');
                  marker.setEasing('easeInOutQuint');
                  marker.setPosition(myLatlng);
                  map.panTo(myLatlng);
                  // bounds.extend(myLatlng);
                  // map.fitBounds(bounds);
              }
          }          
        }
        else{
          SetRideMarker(obj_available_driver);
        }

    }

    var map;
    var bounds;
    var marker;
    var directionsService;
    var directionsDisplay;

    function LoadMap() {
        var mapOptions = {
            center: new google.maps.LatLng(pickup_lat, pickup_lng),
            zoom: 10,
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            scrollwheel: true
        };
        map    = new google.maps.Map(document.getElementById("dvMap"), mapOptions);
        bounds = new google.maps.LatLngBounds();
        
        directionsService = new google.maps.DirectionsService;
        directionsDisplay = new google.maps.DirectionsRenderer({suppressMarkers: true});
        directionsDisplay.setMap(map);

        SetMarker();
    };

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
        
        bounds.extend(pickupLatlng);
        bounds.extend(dropLatlng);
        map.fitBounds(bounds);
        calculateAndDisplayRoute(directionsService, directionsDisplay);
        map.setOptions({ maxZoom: 16 });

    };

    function SetRideMarker(data) {

        var marker_icon = BASE_URL+'/node_assets/images/truck.png';

        var markerImage = new google.maps.MarkerImage(
                                    marker_icon,
                                    new google.maps.Size(20,36), //size
                                    null, //origin
                                    null, //anchor
                                    new google.maps.Size(20,36) //scale
                                );

        var myLatlng = new google.maps.LatLng(data.current_latitude,data.current_longitude);
        var infowindow = new google.maps.InfoWindow({
                                    content: " "
                            });

        marker = new SlidingMarker({
                                        position: myLatlng,
                                        map: map,
                                        icon: markerImage,
                                        driver_id: data.driver_id,
                                        infowindow    : infowindow
                                    });
        
        google.maps.event.addListener(marker, 'click', function() {

                var company_name = '';
                if(data.is_company_driver!=undefined && data.is_company_driver == '1'){
                    company_name = data.company_name ?  data.company_name : '';
                }
                else if(data.is_company_driver!=undefined && data.is_company_driver == '0'){
                    // company_name = data.company_name ?  data.company_name : '';
                    company_name = 'QuickPick';
                }
                
                hideAllInfoWindows();

                var html = '<div class="modal-content">'+
                            '<div class="modal-header">'+
                            '<h5 class="modal-title" id="exampleModalLabel">Driver Details</h5>'+
                            '</div>'+
                                '<div class="modal-body">'+
                                '<div class="review-detais">'+
                                        '<div class="boldtxts">Driver Name : </div>'+
                                        '<div class="rightview-txt">'+data.driver_name+'</div>'+
                                        '<div class="clearfix"></div>'+
                                    '</div>'+
                                    '<div class="review-detais">'+
                                        '<div class="boldtxts">Driver Email : </div>'+
                                        '<div class="rightview-txt">'+data.email+'</div>'+
                                        '<div class="clearfix"></div>'+
                                    '</div>'+
                                    '<div class="review-detais">'+
                                        '<div class="boldtxts">Driver Mobile : </div>'+
                                        '<div class="rightview-txt">'+data.mobile_no+'</div>'+
                                        '<div class="clearfix"></div>'+
                                    '</div>'+
                                    '<div class="review-detais">'+
                                        '<div class="boldtxts">Driver Company : </div>'+
                                        '<div class="rightview-txt">'+company_name+'</div>'+
                                        '<div class="clearfix"></div>'+
                                    '</div>'+
                                    '<div class="review-detais">'+
                                        '<div class="boldtxts">Vehicle Name : </div>'+
                                        '<div class="rightview-txt">'+ data.vehicle_type_name +' - '+ data.vehicle_number +'</div>'+
                                        '<div class="clearfix"></div>'+
                                    '</div>'+
                            '<div class="modal-footer">'+
                            '<button type="button" class="btn btn-secondary" onclick="hideAllInfoWindows()">Close</button>'+
                            '</div>'+
                        '</div>';

                  infowindow.setContent(html);

                infowindow.open(map, this);
        });

    }
    
    function hideAllInfoWindows() {
        marker.infowindow.close(map, marker);
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

    </script>
@endsection