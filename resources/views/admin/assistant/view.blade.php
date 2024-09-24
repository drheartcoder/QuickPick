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
      <i class="fa fa-microphone faa-vertical animated-hover">
      </i>
      
      <a href="{{ url($module_url_path) }}" class="call_loader">{{ $module_title or ''}}
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
       
          <div class="box-content studt-padding">
            <div class="row">
                <div class="col-md-12">
                  <h3>{{$page_title or ''}}</h3>
                  <br>
                    <table class="table table-bordered">
                      <tbody>
                      
                            <tr>
                              <th style="width: 20%">ID
                              </th>
                              <td style="width: 30%">
                                
                                {{isset($arr_load_post_request['load_post_request_unique_id']) ? $arr_load_post_request['load_post_request_unique_id'] : '' }}
                              </td>
                              <th style="width: 20%">Date
                              </th>
                              <td style="width: 30%">
                                {{isset($arr_load_post_request['date']) ? $arr_load_post_request['date'] : '' }}
                              </td>
                            </tr> 

                            <tr>
                              <th style="width: 20%">User Name
                              </th>
                              <td style="width: 30%">
                                {{isset($arr_load_post_request['user_details']['first_name']) ? $arr_load_post_request['user_details']['first_name'] : '' }} {{isset($arr_load_post_request['user_details']['last_name']) ? $arr_load_post_request['user_details']['last_name'] : '' }}
                              </td>
                              <th style="width: 20%">User Contact No.
                              </th>
                              <td style="width: 30%">
                                {{isset($arr_load_post_request['user_details']['country_code']) ? $arr_load_post_request['user_details']['country_code'] : '' }} {{isset($arr_load_post_request['user_details']['mobile_no']) ? $arr_load_post_request['user_details']['mobile_no'] : '' }}
                              </td>
                            </tr> 

                            <tr>
                              <th style="width: 20%">Pick up location
                              </th>
                              <td style="width: 30%">
                                {{isset($arr_load_post_request['pickup_location']) ? $arr_load_post_request['pickup_location'] : '' }}
                              </td>
                              <th style="width: 20%">Drop up location
                              </th>
                              <td style="width: 30%">
                                {{isset($arr_load_post_request['drop_location']) ? $arr_load_post_request['drop_location'] : '' }}
                              </td>
                            </tr> 

                             <tr>
                              <th style="width: 20%">Status
                              </th>
                              <td style="width: 30%">
                                  <span class="badge badge-info" style="width:130px">{{isset($arr_load_post_request['request_status']) ? ucfirst(strtolower(str_replace('_', ' ', $arr_load_post_request['request_status']))) : ''}}</span>
                              </td>
                            </tr> 
                    </tbody>
                  </table>

                  <center><div id="dvMap" style="width: 1400px; height: 600px"></div></center>
                  <br> 

                  <center><a class="btn" href="{{ url($module_url_path) }}">Back</a></center>
  
              </div>
          </div>
        </div>
      </div>
    </div>
  </div>

<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?key=AIzaSyCccvQtzVx4aAt05YnfzJDSWEzPiVnNVsY"></script>
<script src="https://code.jquery.com/jquery-1.11.1.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/marker-animate-unobtrusive/0.2.8/vendor/markerAnimate.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/marker-animate-unobtrusive/0.2.8/SlidingMarker.min.js"></script>
<script src="{{url('/node_assets')}}/jqueryeasing.js"></script>
<script type="text/javascript">

    var load_post_request_id = "{{ isset($arr_load_post_request['id']) ? $arr_load_post_request['id'] :0 }}";
    var pickup_lat           = "{{ isset($arr_load_post_request['pickup_lat']) ? $arr_load_post_request['pickup_lat'] :0 }}";
    var pickup_lng           = "{{ isset($arr_load_post_request['pickup_lng']) ? $arr_load_post_request['pickup_lng'] :0 }}";
    var package_volume       = "{{ isset($arr_load_post_request['load_post_request_package_details']['package_volume']) ? $arr_load_post_request['load_post_request_package_details']['package_volume'] :0 }}";
    var package_weight       = "{{ isset($arr_load_post_request['load_post_request_package_details']['package_weight']) ? $arr_load_post_request['load_post_request_package_details']['package_weight'] :0 }}";



    var pickup_location = '{{isset($arr_load_post_request['pickup_location']) ? $arr_load_post_request['pickup_location'] :''}}';
    var drop_location   = '{{isset($arr_load_post_request['drop_location']) ? $arr_load_post_request['drop_location'] :''}}';
    var drop_lat        = '{{isset($arr_load_post_request['drop_lat']) ? $arr_load_post_request['drop_lat'] :0}}';
    var drop_lng        = '{{isset($arr_load_post_request['drop_lng']) ? $arr_load_post_request['drop_lng'] :0}}';

    var BASE_URL        = "{{url('/')}}";
    var MODULE_URL_PATH = '{{$module_url_path}}';
    
    var markers            = [];
    var map_markers        = [];
    
    $( document ).ready(function() {
        setTimeout(function(){ 
            LoadMap();
        }, 2000);

        setInterval(function(){ 
            get_all_available_driver();
        }, 5000);
    });

    function get_all_available_driver() {
        
        var obj_data = new Object();
        
        obj_data.load_post_request_id = load_post_request_id;
        obj_data.pickup_lat           = pickup_lat;
        obj_data.pickup_lng           = pickup_lng;
        obj_data.package_volume       = package_volume;
        obj_data.package_weight       = package_weight;
        
        $.ajax({
                url:MODULE_URL_PATH+'/search_nearby_drivers',
                type:'GET',
                data:obj_data,
                dataType:'json',
                success:function(response)
                {
                    if(response.status == 'success'){
                        show_on_map_available_driver(response.arr_drivers);
                    }   
                }     
        });
    }
    function show_on_map_available_driver(obj_available_driver){
        
        if(obj_available_driver!=undefined && obj_available_driver.length>0){
            
            $.each(obj_available_driver,function(obj_index,obj_value){
                
                var marker_present = 'NO';

                    if (markers.length > 0) {
                        markers.forEach(function (value, index) {
                            if (value != undefined) {
                                if (value['driver_id'] != undefined) {
                                    if (value['driver_id'] == obj_value.driver_id) {
                                        markers.splice(index, 1);
                                        marker_present = 'YES';
                                    }
                                }
                            }
                        });
                    }

                    obj_value['marker_present'] = marker_present;
                    markers.push(obj_value);

                    // Check if the marker is exist on the map.
                    var create_new_map = "YES";
                    markers.forEach(function (value, index) {
                        if (value != undefined) {
                            if (value['driver_id'] != undefined) {
                                if ((value['driver_id'] == obj_value.driver_id) && (value['marker_present'] == 'YES') ) {
                                    create_new_map = "NO";
                                    map_markers.forEach(function (inner_value, inner_index) {
                                    // console.log("map_markers ---> ",inner_value, inner_index,obj_value);
                                        if (inner_value != undefined) {
                                            if (inner_value['driver_id'] != undefined) {
                                                if (inner_value['driver_id'] == obj_value.driver_id && obj_value.status == 'AVAILABLE') {
                                                    
                                                    var myLatlng = new google.maps.LatLng(obj_value.current_latitude,obj_value.current_longitude);
                                                    inner_value.setDuration('1000');
                                                    inner_value.setEasing('easeInOutQuint');
                                                    inner_value.setPosition(myLatlng);
                                                }
                                            }
                                        }
                                    });
                                }
                            }
                        }
                    });

                    if(create_new_map == "YES") {
                        SetDriverMarker(obj_value);
                    }

                    /*remove busy drivers*/

                    if (markers.length > 0) {
                        markers.forEach(function (value, index) {
                            if (value != undefined) {
                                if (value['driver_id'] != undefined) {
                                    if (value['driver_id'] == obj_value.driver_id && obj_value.status == 'BUSY') {
                                        markers.splice(index, 1);
                                    }
                                }
                            }
                        });
                    }
                    
                    if(map_markers.length>0){
                        map_markers.forEach(function (value, index) {
                            if (value != undefined) {
                                if (value['driver_id'] != undefined) {
                                    if (value['driver_id'] == obj_value.driver_id && obj_value.status == 'BUSY') {
                                        value.setMap(null);
                                        map_markers.splice(index, 1);
                                    }
                                }
                            }
                        });
                    }

            })
        }
    }

    var map;
    var bounds;
    var directionsService;
    var directionsDisplay;

    function LoadMap() {

        var mapOptions = {
            center: new google.maps.LatLng(0,0),
            zoom: 2,
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            scrollwheel: true
        };
        map = new google.maps.Map(document.getElementById("dvMap"), mapOptions);
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

    function SetDriverMarker(data) {

        if(data.status!=undefined && data.status == 'AVAILABLE'){

            var marker_icon = BASE_URL+'/node_assets/images/truck.png';

            var markerImage = new google.maps.MarkerImage(
                                    marker_icon,
                                    new google.maps.Size(20,36), //size
                                    null, //origin
                                    null, //anchor
                                    new google.maps.Size(20,36) //scale
                                );

            var marker;
            var myLatlng = new google.maps.LatLng(data.current_latitude,data.current_longitude);

            var infowindow = new google.maps.InfoWindow({
                content: " "
            });

            marker = new SlidingMarker({
                                          position      : myLatlng,
                                          map           : map,
                                          icon          : markerImage,
                                          driver_id     : data.driver_id,
                                          driver_name   : data.driver_name,
                                          vehicle_id    : data.vehicle_id,
                                          marker_status : data.marker_status,
                                          infowindow    : infowindow
                                      });

            bounds.extend(myLatlng);
            map.fitBounds(bounds);
            map_markers.push(marker);

            // map.setOptions({ maxZoom: 16 });
            
            // let zoom1 = 22 - zoom
        
            // let level = 36 * zoom1/100
            // self.carMarker.icon = UIImage.init(data: UIImagePNGRepresentation(img)!, scale:CGFloat(self.zoom_pix))//CGFloat((zoom - 60)/10))

            google.maps.event.addListener(marker, 'click', function() {

                hideAllInfoWindows(map);

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
                                        '<div class="boldtxts">Vehicle Name : </div>'+
                                        '<div class="rightview-txt">'+ data.vehicle_type_name +' - '+ data.vehicle_name +'</div>'+
                                        '<div class="clearfix"></div>'+
                                    '</div>'+
                            '<div class="modal-footer">'+
                            '<button type="button" data-driver-id='+this.driver_id+' class="btn btn-secondary" onclick="closeInfoWindow(this)">Close</button>'+
                            '<button data-driver-id='+this.driver_id+' data-vehicle-id='+this.vehicle_id+' type="button" onclick="bookDriver(this)" class="btn btn-primary">Assign Driver</button>'+
                            '</div>'+
                        '</div>';

                  infowindow.setContent(html);

                infowindow.open(map, this);
            });
        }
    };

    function hideAllInfoWindows(map) {
        if (map_markers.length > 0) {
            map_markers.forEach(function(marker) {
                marker.infowindow.close(map, marker);
            });
        }
    }

    function closeInfoWindow(ref){
        var driver_id = $(ref).attr('data-driver-id');
        if(driver_id!=undefined && map_markers.length > 0){
            map_markers.forEach(function(marker) {
                if(marker!=undefined && marker.driver_id == driver_id){
                    marker.infowindow.close(map, marker);
                }
            });
        }
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

    var timeout;
    function bookDriver(ref){
      var driver_id = $(ref).attr('data-driver-id');
      var vehicle_id = $(ref).attr('data-vehicle-id');

      if(driver_id!=undefined && vehicle_id!=undefined){
          
          swal({
                title: "Are you sure you want to assign driver ?",
                text: "",
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
                  swal.close();
                  if(isConfirm==true){
                      
                      if(driver_id!=undefined && map_markers.length > 0){
                          map_markers.forEach(function(marker) {
                              if(marker!=undefined && marker.driver_id == driver_id){
                                  marker.infowindow.close(map, marker);
                              }
                          });
                      }
                      showProcessingOverlay();
                      $.ajax({
                            url:MODULE_URL_PATH+'/assign_driver?driver_id='+btoa(driver_id)+'&load_post_request_id='+btoa(load_post_request_id)+'&vehicle_id='+btoa(vehicle_id),
                            type:'GET',
                            dataType:'json',
                            beforeSend:function() {
                              showProcessingOverlay();
                            },
                            success:function(response)
                            {
                                hideProcessingOverlay();
                                if(response.status == 'success'){
                                  swal(response.msg);  
                                  setTimeout(function(){ 
                                    if(response.data.view_url!=undefined && response.data.view_url!='')
                                    {
                                      window.location.href = response.data.view_url;
                                    }
                                  }, 3000);
                                }   
                                else{
                                    swal(response.msg);  
                                    return;

                                }
                            }     
                    });
                  }
              });
      }
    }

</script>
@endsection