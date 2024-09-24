@extends('admin.layout.master')                
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
      <a href="{{ url($admin_panel_slug.'/dashboard') }}">Dashboard
      </a>
    </li>
    <span class="divider">
      <i class="fa fa-angle-right">
      </i>
      <i class="fa fa-ticket faa-vertical animated-hover">
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
            
            $driver_id   = isset($arr_bookings['driver_id']) ? $arr_bookings['driver_id'] :0;
            $vehicle_id  = isset($arr_bookings['vehicle_id']) ? $arr_bookings['vehicle_id'] :0;

            $rider_first_name    = isset($arr_bookings['rider_details']['first_name']) ? $arr_bookings['rider_details']['first_name'] :"";
            $rider_last_name     = isset($arr_bookings['rider_details']['last_name'])  ? $arr_bookings['rider_details']['last_name']  :"";

            $rider_name = $rider_first_name." ".$rider_last_name;

            $driver_first_name    = isset($arr_bookings['driver_details']['first_name']) ? $arr_bookings['driver_details']['first_name'] :"";
            $driver_last_name     = isset($arr_bookings['driver_details']['last_name'])  ? $arr_bookings['driver_details']['last_name']  :"";

            $driver_name = $driver_first_name." ".$driver_last_name;

            $pickup_location = isset($arr_bookings['pick_up_location']) ? $arr_bookings['pick_up_location'] :"";

            $dropup_location = isset($arr_bookings['drop_location'])    ? $arr_bookings['drop_location'] :""; 

            $vehicle_name = isset($arr_bookings['vehicle_details']['vehicle_name']) ? $arr_bookings['vehicle_details']['vehicle_name'] :"";

            $vehicle_number = isset($arr_bookings['vehicle_details']['vehicle_number']) ? $arr_bookings['vehicle_details']['vehicle_number'] :"";


            $vehicle_model = isset($arr_bookings['vehicle_details']['vehicle_model_name']) ? $arr_bookings['vehicle_details']['vehicle_model_name'] :"";

            $vehicle_type = isset($arr_bookings['vehicle_details']['vehicle_type_details']['vehicle_type']) ? $arr_bookings['vehicle_details']['vehicle_type_details']['vehicle_type'] :"";

        ?>
          <div class="box-content studt-padding">
            <div class="row">
                <div class="col-md-8">
                  <h3>{{$module_title or ''}}</h3>
                  <br>
                    <table class="table table-bordered">
                      <tbody>
                            <tr>
                              <th style="width: 30%">Rider Name
                              </th>
                              <td>
                                {{$rider_name or ''}}
                              </td>
                            </tr> 

                            <tr>
                              <th style="width: 30%">Driver Name 
                              </th>
                              <td>
                                {{$driver_name or ''}}
                              </td>
                            </tr>
                            
                            <tr>
                              <th style="width: 30%">Vehicle Type
                              </th>
                              <td>
                                {{$vehicle_type or ''}}
                              </td>
                            </tr>

                            <tr>
                              <th style="width: 30%">Vehicle Name
                              </th>
                              <td>
                                {{$vehicle_name or ''}}
                              </td>
                            </tr>       

                            <tr>
                              <th style="width: 30%">Vehicle Number
                              </th>
                              <td>
                                {{$vehicle_number or ''}}
                              </td>
                            </tr>

                            <tr>
                              <th style="width: 30%">Vehicle Model
                              </th>
                              <td>
                                {{$vehicle_model or ''}}
                              </td>
                            </tr>

                            <tr>
                              <th style="width: 30%">Pick up location
                              </th>
                              <td>
                                {{ $pickup_location or '' }}
                              </td>
                            </tr>

                            <tr>
                              <th style="width: 30%">Drop up location
                              </th>
                              <td>
                                {{ $dropup_location or '' }}
                              </td>
                            </tr>

                    </tbody>
                  </table>

                  <hr/>

                  <p>
                      <button onclick="startTrip();" >Start Trip</button>
                  </p>

                  <p>
                      <button onclick="stopTrip();" >Stop Trip</button>
                  </p>

                  <p>
                      <button onclick="bookDriver();" >Book Driver</button>
                  </p>

                  <p>
                      <button onclick="accept_request();" >Accept Request by Driver</button>
                  </p>

                  
                  <div id="dvMap" style="width: 1550px; height: 600px"></div>
                   <br> 
                  <center><a class="btn" href="{{ url($module_url_path) }}">Back</a></center>
                </div> 

              </div>

          </div>

          

        </div>
      </div>
    </div>
  </div>
  <script type="text/javascript" src="{{url('/node_apps/public')}}/socket.io.js"></script>
  <script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?key=AIzaSyCccvQtzVx4aAt05YnfzJDSWEzPiVnNVsY"></script>
  {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/2.0.4/socket.io.js"></script> --}}
  {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/2.0.4/socket.io.js"></script> --}}
  <script src="https://code.jquery.com/jquery-1.11.1.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/marker-animate-unobtrusive/0.2.8/vendor/markerAnimate.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/marker-animate-unobtrusive/0.2.8/SlidingMarker.min.js"></script>
  
  <script src="{{url('/assets/node_assets')}}/jqueryeasing.js"></script>

  {{-- <script type="text/javascript" src="{{url('/node_apps/node_modules')}}/socket.io/lib/socket.js"></script> --}}

  <script type="text/javascript">
  
    var BASE_URL          = '{{url('/')}}';
    
    var NODE_SERVER_URL   = '{{env('NODE_SERVER_URL','localhost:8080')}}';
    var NODE_SERVER_PORT  = '{{env('NODE_SERVER_PORT','8080')}}';

    var vehicle_track_port           = NODE_SERVER_PORT;
    var vehicle_track_socket_connect = io(NODE_SERVER_URL);

    var driver_id  = '{{$driver_id}}';
    var vehicle_id = '{{$vehicle_id}}';

    

    $( document ).ready(function() {

        LoadMap();

        /*var obj_data = {
                          id         : vehicle_track_socket_connect.id,
                          vehicle_id : vehicle_id,
                          driver_id  : driver_id
                        };*/
        var obj_data = {
                          id         : vehicle_track_socket_connect.id,
                          vehicle_id : 27,
                          driver_id  : 47
                        };
        vehicle_track_socket_connect.emit('send_vehicle_info_to_track',obj_data);
        // startTrip();
    });
    
    var markers = [{
        "lat": '0',
        "lng": '0',
    }];

    var map;
    var marker;
    
    function LoadMap() {
        var mapOptions = {
            center: new google.maps.LatLng(markers[0].lat, markers[0].lng),
            zoom: 13,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        };
        map = new google.maps.Map(document.getElementById("dvMap"), mapOptions);
        SetMarker(markers);
    };

    function SetMarker(markers) {
        //Remove previous Marker.
        if (marker != null) {
            marker.setMap(null);
        }

         // var infoWindow = new google.maps.InfoWindow();
        // infoWindow.setContent("<div style = 'width:200px;min-height:40px'>" + data.description + "</div>");

        //Set Marker on Map.
        //Create and open InfoWindow.
        // var contentString = "<div style = 'width:200px;min-height:40px'>" + data.description + "</div>";

        /*var infoWindow = new google.maps.InfoWindow({
            content: contentString
        });*/
        
        var data = markers[0];
        var myLatlng = new google.maps.LatLng(data.lat, data.lng);

        marker = new SlidingMarker({
                    position: myLatlng,
                    map: map,
                    title: '',
                    icon: BASE_URL+'/assets/node_assets/images/car-icon.png'
                });

         

        //Set Marker on Map.
        //Create and open InfoWindow.
        // var contentString = "<div style = 'width:200px;min-height:40px'>" + data.description + "</div>";

        /*var infoWindow = new google.maps.InfoWindow({
            content: contentString
        });*/

        /*marker = new google.maps.Marker({
            position: myLatlng,
            map: map,
            //title: data.title,
            icon: '/public/images/car-icon.png'
        });*/       
        // infoWindow.open(map, marker);
    };


    vehicle_track_socket_connect.on('get_vehicle_info', function(response) {

        if(response != undefined) {
            if(response.lat != undefined && response.lat != undefined && response.lng != undefined ){
                
                var myLatlng = new google.maps.LatLng(response.lat,response.lng);
                marker.setDuration('1000');
                marker.setEasing('easeInOutQuint');
                marker.setPosition(myLatlng);
                map.panTo(myLatlng);

                var contentString = "<div style = 'width:200px;min-height:40px'> Driver Name : "+response.driver_name+"<br>Fair Charge: "+response.fair_charge+"</div>";
                var infowindow = new google.maps.InfoWindow({
                  content: contentString
                });

                marker.addListener('click', function() {
                  infowindow.open(map, marker);
                });
                marker.addListener('close', function() {
                  infowindow.close(map, marker);
                });
            }
        }
    });

    // Sending the lat lng dynamically.
    function callPosition() {
        lat += 0.001;
        lng += 0.001;

        mlat += 0.001;
        mlng += 0.001;

        ride_id = 1;
        mride_id = 2;
        
        var location_description = 'Nashik, lat-> ' + lat + ' , lng -> '+ lng;
        var obj_location = { 'vehicle_id': vehicle_id, 'lat': lat ,'lng' : lng ,'location_description' : location_description ,'ride_id' : ride_id };
        
        // vehicle_track_socket_connect.emit('show_on_map',obj_location);
        
        /*var mlocation_description = 'Mumbai, India. lat-> ' + mlat + ' , lng -> '+ mlng;
        var mobj_location = { 'vehicle_id': mvehicle_id, 'lat': mlat ,'lng' : mlng ,'location_description' : mlocation_description , 'ride_id': mride_id };
        vehicle_track_socket_connect.emit('show_on_map',mobj_location);          */
    }

    function changeMarkerPosition() {
       /*
        var lat = 19.9975;
        var lng = 73.7898;
        setInterval(function () {
            lat += 0.003;
            lng += 0.003;
            var markers = [{
                "title": 'Nashik',
                "lat": String(lat),
                "lng": String(lng),
                "description": 'Nashik, India. lat-> ' + lat + ' , lng -> '+ lng 
            }];
            SetMarker(markers);
        },1000);
        */
    }

    var lat = 19.9975;
    var lng = 73.7898;

    var mlat = 19.0760;
    var mlng = 72.8777;

    // var vehicle_id  = 3;
    var mvehicle_id = 4;
    var timeout;


    function startTrip() {
        timeout = setInterval(function () {
            // callPosition();
        },3000);
    }
    
    function stopTrip() {
        clearInterval(timeout);
        vehicle_track_socket_connect.emit('stop_vehicle_tracking', { vehicle_id: vehicle_id });
    }

    function bookDriver(){
        
        var obj_request_to_book = {
                                      socket_id            : vehicle_track_socket_connect.id,
                                      rider_id             : 27,
                                      driver_id            : 47,
                                      vehicle_id           : 47,
                                      promo_code_id        : 1,
                                      source_location      : 'nashik road',
                                      destination_location : 'mumbai naka',
                                      source_lat           : 19.991085220448543,
                                      source_lng           : 73.78286048031401,
                                      destination_lat      : 73.78286048031401,
                                      destination_lng      : 73.78286048031401,
                                      request_status       : 'RIDER_REQUEST'
                                    };
        
        vehicle_track_socket_connect.emit('request_to_book_driver',obj_request_to_book);

    }
    
    /*'ACCEPT_BY_DRIVER','REJECT_BY_DRIVER','ACCEPT_BY_RIDER','REJECT_BY_RIDER','TIME_OUT','RIDER_REQUEST'*/

    function accept_request()
    {
      var obj_process_request = {
                                            socket_id            : vehicle_track_socket_connect.id,
                                            request_id           : 1,
                                            request_status       : 'ACCEPT_BY_DRIVER'
                                          };

                
      vehicle_track_socket_connect.emit('process_request_by_driver',obj_process_request);
    }

    vehicle_track_socket_connect.on('send_receive_request_to_driver', function(response) {
        console.log(response);

/*        if(response != undefined) {
            if(response.status != undefined && response.request_id != undefined && response.status == 'success' ){
                
                var obj_process_request = {
                                            socket_id            : vehicle_track_socket_connect.id,
                                            request_id           : response.request_id,
                                            request_status       : 'ACCEPT_BY_DRIVER'
                                          };

                
                vehicle_track_socket_connect.emit('process_request_by_driver',obj_process_request);
            }
        }*/
    });

    /*vehicle_track_socket_connect.on('process_request_result_by_driver', function(response) {
        
        if(response != undefined) {
            if(response.status != undefined && response.request_id != undefined && response.status == 'success' ){
                
                var obj_process_request = {
                                            socket_id            : vehicle_track_socket_connect.id,
                                            request_id           : response.request_id,
                                            request_status       : 'ACCEPT_BY_DRIVER'
                                          };

                
                vehicle_track_socket_connect.emit('process_request_by_driver',obj_process_request);
            }
        }
    });*/


</script>

  <!-- END Main Content --> 
  @endsection
