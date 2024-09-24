@extends('admin.layout.master')                
@section('main_content')
<!-- Scroll Start Here -->
<link href="{{ url('/') }}/css/admin/jquery.mCustomScrollbar.css" rel="stylesheet" type="text/css" />
<script src="{{ url('/') }}/js/admin/jquery.mCustomScrollbar.concat.min.js"></script>
<script type="text/javascript">
/*scrollbar start*/
(function($){
$(window).on("load",function(){
    $.mCustomScrollbar.defaults.scrollButtons.enable=true; //enable scrolling buttons by default
    $.mCustomScrollbar.defaults.axis="yx"; //enable 2 axis scrollbars by default
   $(".content-d").mCustomScrollbar({theme:"dark"});
});
})(jQuery);
</script>

<!-- Scroll End Here -->
    <!-- BEGIN Page Title -->
     <link rel="stylesheet" type="text/css" href="{{ url('/') }}/assets/data-tables/latest/dataTables.bootstrap.min.css">
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
                <i class="fa fa-users"></i>                
            </span> 
            <li class="active">{{ isset($module_title)?$module_title:"" }}</li>
        </ul>
      </div>
    <!-- END Breadcrumb -->

    <!-- BEGIN Main Content -->
    <div class="row">
      <div class="col-md-12">

          <div class="box {{ $theme_color }}">
            <div class="box-title">
              <h3>
                <i class="fa fa-list"></i>
                Today's Booking
            </h3>
            <div class="box-tool">
                <a data-action="collapse" href="#"></a>
                <a data-action="close" href="#"></a>
            </div>
        </div>
       
        <div class="box-content">
        <div class="button-sctions">
           <ul>
           <li>
                    <button onclick="change_default_type('SHOW_AVAILABLE_DRIVER');" 
                            role="button" 
                            class="btn btn-default setclass 
                            @if(isset($ride_status) && $ride_status == 'SHOW_AVAILABLE_DRIVER' )
                                active 
                            @endif
                            ">All Available Driver</button>
            </li>
            <li>
                    <button onclick="change_default_type('TO_BE_PICKED');" 
                            role="button" 
                            class="btn btn-default setclass 
                            @if(isset($ride_status) && $ride_status == 'TO_BE_PICKED' )
                                active 
                            @endif
                            ">To be Picked</button>
            </li>
            <li>
                    <button onclick="change_default_type('IN_TRANSIT');" 
                            role="button" 
                            class="btn btn-default setclass
                                @if(isset($ride_status) && $ride_status == 'IN_TRANSIT' )
                                active 
                                @endif
                            ">In Transit</button>
            </li>
            <li>
                    <button onclick="change_default_type('COMPLETED');" 
                            role="button" 
                            class="btn btn-default setclass
                                @if(isset($ride_status) && $ride_status == 'COMPLETED' )
                                active 
                                @endif
                            ">Completed</button>
            </li>

            {{--<li><button role="button" class="btn btn-default setclass">Available</button></li>
             <li><button role="button" class="btn btn-default setclass active">All</button></li> --}}
            </ul>
            <div class="clearfix"></div>
        </div>

        <div class="row">
            <div class="col-sm-12 col-md-4 col-lg-8 auto-height-main div-map-section-main">
                
                <div class="map-main-page-inner-tab">
                    <a href="javascript:void(0);" class="active">
                        @if(isset($ride_status) && $ride_status == 'SHOW_AVAILABLE_DRIVER' )
                            All Available Driver
                        @elseif(isset($ride_status) && $ride_status == 'TO_BE_PICKED' )
                            To be Picked
                        @elseif(isset($ride_status) && $ride_status == 'IN_TRANSIT' )
                            In Transit
                        @elseif(isset($ride_status) && $ride_status == 'COMPLETED' )
                            Completed
                        @else
                            -
                        @endif
                    </a>
                </div>
                <div class="clearfix"></div>

                <div  id="dvMap" style="width:100%; height:645px">
                
                </div>
            </div>
                    
            <div class="col-sm-12 col-md-4 col-lg-4">
                <div class="map-main-page-inner-tab">
                    <a href="javascript:void(0);" class="active" id="result">
                        Customer Shipment Post Request
                    </a>
                </div>
                <div class="clearfix"></div>
                <div class="chat-messagess" style="height:645px !important;">
                   <div class="clearfix"></div>
                   <div id="load_post_request_html"></div>
                    {{-- <ul class="content-txt1 content-d section-auto-height">
                    </ul> --}}
                </div>
            </div>
        </div>
        </div>
  </div>
</div>

<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key={{ isset($google_map_api_key) ? $google_map_api_key : 'AIzaSyCccvQtzVx4aAt05YnfzJDSWEzPiVnNVsY' }}"></script>
<script src="https://code.jquery.com/jquery-1.11.1.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/marker-animate-unobtrusive/0.2.8/vendor/markerAnimate.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/marker-animate-unobtrusive/0.2.8/SlidingMarker.min.js"></script>
<script src="{{url('/node_assets')}}/jqueryeasing.js"></script>

<script type="text/javascript">
    var ride_status = '{{$ride_status}}';
    var module_url_path = '{{$module_url_path}}';

    function change_default_type(type){
        if(module_url_path!=undefined && ride_status!=undefined && type!=undefined){
            // if(type!=ride_status){
                var url = module_url_path+'?ride_status='+type;
                window.location.href = url;
            // }
        }
    }

</script>
<?php
        //$slice = str_before('This is my name', 'my name');
        //dd($slice);

?>
<script>
    $(document).ready(function(){       
        if($("#dvMap").height() != "undefined")
        {$(".section-auto-height").css("height", $("#dvMap").height());}
    });   
</script>
<script type="text/javascript">
    
    //var Base64={_keyStr:"ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",encode:function(e){var t="";var n,r,i,s,o,u,a;var f=0;e=Base64._utf8_encode(e);while(f<e.length){n=e.charCodeAt(f++);r=e.charCodeAt(f++);i=e.charCodeAt(f++);s=n>>2;o=(n&3)<<4|r>>4;u=(r&15)<<2|i>>6;a=i&63;if(isNaN(r)){u=a=64}else if(isNaN(i)){a=64}t=t+this._keyStr.charAt(s)+this._keyStr.charAt(o)+this._keyStr.charAt(u)+this._keyStr.charAt(a)}return t},decode:function(e){var t="";var n,r,i;var s,o,u,a;var f=0;e=e.replace(/[^A-Za-z0-9+/=]/g,"");while(f<e.length){s=this._keyStr.indexOf(e.charAt(f++));o=this._keyStr.indexOf(e.charAt(f++));u=this._keyStr.indexOf(e.charAt(f++));a=this._keyStr.indexOf(e.charAt(f++));n=s<<2|o>>4;r=(o&15)<<4|u>>2;i=(u&3)<<6|a;t=t+String.fromCharCode(n);if(u!=64){t=t+String.fromCharCode(r)}if(a!=64){t=t+String.fromCharCode(i)}}t=Base64._utf8_decode(t);return t},_utf8_encode:function(e){e=e.replace(/rn/g,"n");var t="";for(var n=0;n<e.length;n++){var r=e.charCodeAt(n);if(r<128){t+=String.fromCharCode(r)}else if(r>127&&r<2048){t+=String.fromCharCode(r>>6|192);t+=String.fromCharCode(r&63|128)}else{t+=String.fromCharCode(r>>12|224);t+=String.fromCharCode(r>>6&63|128);t+=String.fromCharCode(r&63|128)}}return t},_utf8_decode:function(e){var t="";var n=0;var r=c1=c2=0;while(n<e.length){r=e.charCodeAt(n);if(r<128){t+=String.fromCharCode(r);n++}else if(r>191&&r<224){c2=e.charCodeAt(n+1);t+=String.fromCharCode((r&31)<<6|c2&63);n+=2}else{c2=e.charCodeAt(n+1);c3=e.charCodeAt(n+2);t+=String.fromCharCode((r&15)<<12|(c2&63)<<6|c3&63);n+=3}}return t}}


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
        }, 6000);
    });

    // var selected_driver_id   = 0;
    // var load_post_request_id = '{{isset($arr_load_post_request_details['id']) ? $arr_load_post_request_details['id'] :0}}';
    // var pickup_location      = '{{isset($arr_load_post_request_details['pickup_location']) ? $arr_load_post_request_details['pickup_location'] :''}}';
    // var pickup_lat           = '{{isset($arr_load_post_request_details['pickup_lat']) ? $arr_load_post_request_details['pickup_lat'] : ''}}';
    // var pickup_lng           = '{{isset($arr_load_post_request_details['pickup_lng']) ? $arr_load_post_request_details['pickup_lng'] : ''}}';
    // var drop_lat           = '{{isset($arr_load_post_request_details['drop_lat']) ? $arr_load_post_request_details['drop_lat'] : ''}}';
    // var drop_lng           = '{{isset($arr_load_post_request_details['drop_lng']) ? $arr_load_post_request_details['drop_lng'] : ''}}';

    var selected_driver_id   = 0;
    var load_post_request_id = 0;
    var pickup_location      = '';
    var drop_location        = '';
    var pickup_lat           = 0;
    var pickup_lng           = 0;
    var drop_lat             = 0;
    var drop_lng             = 0;

    var arr_remove_driver_ids = [];

    function get_all_available_driver() {
        $.ajax({
                url:MODULE_URL_PATH+'/available_driver',
                type:'GET',
                data:{'flag':true,'pickup_lat':pickup_lat,'pickup_lng':pickup_lng,'load_post_request_id':load_post_request_id,'selected_driver_id':selected_driver_id},
                dataType:'json',
                success:function(response)
                {
                    //response = $.parseJSON(Base64.decode(response));

                    $('#load_post_request_html').html('');
                    $('#load_post_request_html').html(response.str_html_load_post_request);

                    if(response.status == 'success'){

                        show_on_map_available_driver(response.available_driver,response.arr_driver_id,response.arr_load_post_request_history_details);
                    }
                    else
                    {
                        if (markers.length > 0) 
                        {
                            markers = [];
                        }
                        if (arr_remove_driver_ids.length > 0) 
                        {
                            arr_remove_driver_ids = [];
                        }
                        
                        if (map_markers.length > 0) 
                        {
                            map_markers.forEach(function (value, index) {
                                value.setMap(null);
                            });
                            
                            map.setZoom(2);

                            map_markers = [];
                        }
                    }
                    
                }     
        });
    }

    function show_on_map_available_driver(obj_available_driver,arr_driver_id,arr_load_post_request_history_details){
       
        if(obj_available_driver!=undefined && obj_available_driver.length>0){
            
            $.each(obj_available_driver,function(obj_index,obj_value){
                    
                    /*remove onject from marker array*/
                    if (markers.length > 0 && arr_driver_id.length>0) {
                        markers.forEach(function (value, index) {
                            if($.inArray( value['driver_id'], arr_driver_id)==-1){
                                markers.splice(index, 1);
                            }
                        });
                    }
                    /*remove marker from the map*/
                    if (map_markers.length > 0 && arr_driver_id.length>0) {
                        map_markers.forEach(function (value, index) {
                            if($.inArray( value['driver_id'], arr_driver_id)==-1){
                                value.setMap(null);
                                map_markers.splice(index, 1);
                            }
                        });
                    }

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
                                        if (inner_value != undefined) {
                                            if (inner_value['driver_id'] != undefined) {
                                                if (inner_value['driver_id'] == obj_value.driver_id) {
                                                    
                                                    var myLatlng = new google.maps.LatLng(obj_value.current_latitude,obj_value.current_longitude);
                                                    inner_value.setDuration('1000');
                                                    inner_value.setEasing('easeInOutQuint');
                                                    inner_value.setPosition(myLatlng);
                                                    
                                                    /*change marker image dynamic*/                                            

                                                    var vehicle_type_slug = '';
                                                    if(obj_value.vehicle_type_slug!=undefined && obj_value.vehicle_type_slug!=''){
                                                        vehicle_type_slug = obj_value.vehicle_type_slug
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
                                                    // var marker_icon = BASE_URL+'/node_assets/images/light-grey.png';
                                                    // if(obj_value.booking_status!=null && (obj_value.booking_status == 'TO_BE_PICKED' || obj_value.booking_status == 'IN_TRANSIT')){
                                                    //     marker_icon = BASE_URL+'/node_assets/images/blue.png';
                                                    // }
                                                    // else if(obj_value.availability_status!=undefined && obj_value.availability_status == 'OFFLINE'){
                                                    //     marker_icon = BASE_URL+'/node_assets/images/light-grey.png';
                                                    // }
                                                    // else if(obj_value.status!=undefined && obj_value.status == 'AVAILABLE' && obj_value.availability_status == 'ONLINE'){
                                                    //     marker_icon = BASE_URL+'/node_assets/images/green.png';
                                                    // }

                                                    // if(arr_load_post_request_history_details.length>0){
                                                    //     arr_load_post_request_history_details.forEach(function(tmp_value,tmp_index){
                                                    //         if(tmp_value.driver_id == obj_value.driver_id)
                                                    //         {
                                                    //             if(tmp_value.status == 'REJECT_BY_DRIVER'){
                                                    //                 marker_icon = BASE_URL+'/node_assets/images/red.png';
                                                    //             }
                                                    //         }
                                                    //     });
                                                    // }

                                                    var markerImage = new google.maps.MarkerImage(
                                                        marker_icon,
                                                        new google.maps.Size(14,30), //size
                                                        null, //origin
                                                        null, //anchor
                                                        new google.maps.Size(14,30) //scale
                                                    );
                                                    inner_value.setIcon(markerImage);
                                                }
                                            }
                                        }
                                    });
                                }
                            }
                        }
                    });
                    
                    if(create_new_map == "YES") {
                        SetMarker(obj_value,arr_load_post_request_history_details);
                    }
            })
        }
        else
        {
            // if (markers.length > 0) {
            //     markers.forEach(function (value, index) {
            //         markers[index].setMap(null);
            //     });
            // }
            // markers = [];
            // map_markers = [];
        }
    }

    var map;
    var bounds;
    var directionsService;
    var directionsDisplay;
    
    var pickup_marker;
    var drop_marker;

    function LoadMap() {

        var map_center = new google.maps.LatLng(0,0);
        var map_zoom = 2;
        if(pickup_lat!='' && pickup_lng!='')
        {
            map_center = new google.maps.LatLng(pickup_lat,pickup_lng);
            map_zoom = 10;
        }

        var mapOptions = {
            center: map_center,
            zoom: map_zoom,
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            scrollwheel: true,
            disableDefaultUI: true,
        };
        map = new google.maps.Map(document.getElementById("dvMap"), mapOptions);
        bounds = new google.maps.LatLngBounds();

        // if(pickup_lat!='' && pickup_lng!='')
        // {
        //     var pickupMarkerImage = new google.maps.MarkerImage(
        //                             BASE_URL+'/node_assets/images/pointer.png',
        //                             new google.maps.Size(36,36), //size
        //                             null, //origin
        //                             null, //anchor
        //                             new google.maps.Size(36,36) //scale
        //                         );

        //     var pickupLatlng = new google.maps.LatLng(pickup_lat, pickup_lng);
        //     pickup_marker    = new SlidingMarker({
        //                                   position: pickupLatlng,
        //                                   map: map,
        //                                   title : pickup_location,
        //                                   icon: pickupMarkerImage
        //                               });
            
        //     bounds.extend(pickupLatlng);
        // }

        // if(drop_lat!='' && drop_lng!='')
        // {
        //     var dropMarkerImage = new google.maps.MarkerImage(
        //                             BASE_URL+'/node_assets/images/pointer.png',
        //                             new google.maps.Size(36,36), //size
        //                             null, //origin
        //                             null, //anchor
        //                             new google.maps.Size(36,36) //scale
        //                         );

        //     var dropLatlng = new google.maps.LatLng(drop_lat, drop_lng);
        //     pickup_marker    = new SlidingMarker({
        //                                   position: dropLatlng,
        //                                   map: map,
        //                                   title : pickup_location,
        //                                   icon: dropMarkerImage
        //                               });
            
        //     bounds.extend(dropLatlng);
        // }

        // if((pickup_lat!='' && pickup_lng!='')&& (drop_lat!='' && drop_lng!=''))
        // {
        //     directionsService = new google.maps.DirectionsService;
        //     directionsDisplay = new google.maps.DirectionsRenderer({suppressMarkers: true});
        //     directionsDisplay.setMap(map);
        //     calculateAndDisplayRoute(directionsService, directionsDisplay);
        // }
    };

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

    function SetMarker(data,arr_load_post_request_history_details) {
        
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
        // if(data.booking_status!=null && (data.booking_status == 'TO_BE_PICKED' || data.booking_status == 'IN_TRANSIT')){
        //     marker_icon = BASE_URL+'/node_assets/images/blue.png';
        // }
        // else if(data.availability_status!=undefined && data.availability_status == 'OFFLINE'){
        //     marker_icon = BASE_URL+'/node_assets/images/light-grey.png';
        // }
        // else if(data.status!=undefined && data.status == 'AVAILABLE' && data.availability_status == 'ONLINE'){
        //     marker_icon = BASE_URL+'/node_assets/images/green.png';
        // }   

        var is_request_timeout = 'no';
        if(arr_load_post_request_history_details.length>0){
            arr_load_post_request_history_details.forEach(function(tmp_value,tmp_index){
                if(tmp_value.driver_id == data.driver_id)
                {
                    // if(tmp_value.status == 'REJECT_BY_DRIVER'){
                    //     marker_icon = BASE_URL+'/node_assets/images/red.png';
                    //     is_request_timeout = 'yes';
                    // }
                }
            });
        }

        var markerImage = new google.maps.MarkerImage(
                                marker_icon,
                                new google.maps.Size(14,30), //size
                                null, //origin
                                null, //anchor
                                new google.maps.Size(14,30) //scale
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
     
        google.maps.event.addListener(marker, 'click', function() {

            hideAllInfoWindows(map);

            var company_name = '';
            if(data.is_company_driver!=undefined && data.is_company_driver == '1'){
                company_name = data.company_name ?  data.company_name : '';
            }
            else if(data.is_company_driver!=undefined && data.is_company_driver == '0'){
                // company_name = data.company_name ?  data.company_name : '';
                company_name = 'QuickPick';
            }

            var html = '';

            html +=  '<div class="modal-content map-driver-details-popup">';
            html +=     '<div class="modal-header"><h5 class="modal-title" id="exampleModalLabel">Driver Details</h5></div>';
            html +=        '<div class="modal-body">';
            html +=           '<div class="review-detais">';
            html +=                 '<div class="boldtxts">Name : </div>';
            html +=                 '<div class="rightview-txt">'+data.driver_name+'</div>';
            html +=            '</div>';
            html +=            '<div class="review-detais">';
            html +=                '<div class="boldtxts">Email : </div>';
            html +=                    '<div class="rightview-txt">'+data.email+'</div>';
            html +=                 '</div>';
            html +=                 '<div class="review-detais">';
            html +=                        '<div class="boldtxts">Mobile : </div>';
            html +=                        '<div class="rightview-txt">'+data.mobile_no+'</div>';
            html +=                 '</div>';
            html +=                 '<div class="review-detais">';
            html +=                        '<div class="boldtxts">Company : </div>';
            html +=                        '<div class="rightview-txt">'+company_name+'</div>';
            html +=                  '</div>';
            html +=                  '<div class="review-detais">';
            html +=                        '<div class="boldtxts">Vehicle Details : </div>';
            html +=                        '<div class="rightview-txt">'+ data.vehicle_type_name +' - '+ data.vehicle_number +'</div>';
            html +=                   '</div>';
            html +=                '<div class="modal-footer">';

            if(is_request_timeout == 'no'){
                html +=                    '<button type="button" data-driver-id='+this.driver_id+' class="btn btn-primary" onclick="assignRequestToDriver(this)">Assign</button>';
            }
            
            html +=                    '<button type="button" data-driver-id='+this.driver_id+' class="btn btn-secondary" onclick="closeInfoWindow(this)">Close</button>';
            html +=                '</div>';
            html +=            '</div>';

              infowindow.setContent(html);

            infowindow.open(map, this);
        });
    };

    function assignRequestToDriver(ref){
        var driver_id = $(ref).attr('data-driver-id');

        if(driver_id!=undefined && markers.length > 0){
            markers.forEach(function(value,index) {
                if(value!=undefined && value.driver_id == driver_id){
                    var arr_driver_obj = markers[index];
                    if(arr_driver_obj!=undefined)
                    {
                        if(load_post_request_id==0){
                            swal('Error!','Please select Shipment load Request from the list','error');
                            return;
                        }
                        if(arr_driver_obj.booking_status!=null && (arr_driver_obj.booking_status == 'TO_BE_PICKED' || arr_driver_obj.booking_status == 'IN_TRANSIT')){
                            swal('Error!','Currenly driver has ongoing trip,cannot assign Shipment load request.','error');
                            return;
                        }

                        swal({
                              title: "Are you sure ?",
                              text: 'You want to assign shipment load request to current driver.',
                              type: "warning",
                              showCancelButton: true,
                              confirmButtonColor: "#DD6B55",
                              confirmButtonText: "Yes",
                              cancelButtonText: "No",
                              closeOnConfirm: true,
                              closeOnCancel: true
                            },
                            function(isConfirm){
                                if(isConfirm) {
                                    processAssignRequestToDriver(arr_driver_obj);
                                }
                                return false;
                            }); 
                        return false;
                    }
                    return false;
                }
            });
        }
    }
    
    function processAssignRequestToDriver(arr_driver_obj) {
        if(arr_driver_obj!=undefined){

            selected_driver_id = arr_driver_obj.driver_id;

            $.ajax({
                    url:MODULE_URL_PATH+'/assign_request_to_driver',
                    type:'GET',
                    data:{'driver_id':arr_driver_obj.driver_id,'load_post_request_id':load_post_request_id,'mobile_no':arr_driver_obj.mobile_no},
                    dataType:'json',
                    beforeSend:function(){
                        hideAllInfoWindows(map);
                        showProcessingOverlay();
                    },
                    success:function(response)
                    {
                        if(response.status == 'success'){
                            
                            hideProcessingOverlay();

                            swal("Success!", response.msg, "success");
                            setTimeout(function(){ 
                                var url = module_url_path+'?ride_status='+ride_status;
                                window.location.href = url;

                            }, 3000);

                            return false;
                        }
                        else  
                        {
                            hideProcessingOverlay();
                            swal('Error!',response.msg,'error');
                            return false;
                        } 
                    }     
            });
            hideProcessingOverlay();
            return false;

        }
        return false;
    }

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

    function load_request_details(ref){
        var str_obj = $(ref).attr('data-attr-obj');
        if(str_obj!='')
        {
            var obj_data = $.parseJSON(str_obj)
            if(obj_data!=undefined && obj_data.id!=undefined){

                load_post_request_id = obj_data.id;
                pickup_location      = obj_data.pickup_location;
                drop_location        = obj_data.drop_location;
                pickup_lat           = obj_data.pickup_lat;
                pickup_lng           = obj_data.pickup_lng;
                drop_lat             = obj_data.drop_lat;
                drop_lng             = obj_data.drop_lng;

                

                if((pickup_lat!='' && pickup_lng!='') && drop_lat!='' && drop_lng!='')
                {
                    if(pickup_marker!=undefined){
                        pickup_marker.setMap(null);
                    }
                    if(drop_marker!=undefined){
                        drop_marker.setMap(null);
                    }
                    var pickupMarkerImage = new google.maps.MarkerImage(
                                            BASE_URL+'/node_assets/images/pointer.png',
                                            new google.maps.Size(36,36), //size
                                            null, //origin
                                            null, //anchor
                                            new google.maps.Size(36,36) //scale
                                        );

                    pickupLatlng = new google.maps.LatLng(pickup_lat, pickup_lng);
                    pickup_marker    = new SlidingMarker({
                                                  position: pickupLatlng,
                                                  map: map,
                                                  title : pickup_location,
                                                  icon: pickupMarkerImage
                                              });
                    
                    bounds.extend(pickupLatlng);
                
                    var dropMarkerImage = new google.maps.MarkerImage(
                                            BASE_URL+'/node_assets/images/pointer.png',
                                            new google.maps.Size(36,36), //size
                                            null, //origin
                                            null, //anchor
                                            new google.maps.Size(36,36) //scale
                                        );

                    dropLatlng = new google.maps.LatLng(drop_lat, drop_lng);
                    drop_marker    = new SlidingMarker({
                                                  position: dropLatlng,
                                                  map: map,
                                                  title : pickup_location,
                                                  icon: dropMarkerImage
                                              });
                    
                    bounds.extend(dropLatlng);
                
                    if (directionsDisplay != null) {
                        directionsDisplay.setMap(null);
                        directionsDisplay = null;
                    }


                    directionsService = new google.maps.DirectionsService;
                    directionsDisplay = new google.maps.DirectionsRenderer({suppressMarkers: true});
                    directionsDisplay.setMap(map);
                    calculateAndDisplayRoute(directionsService, directionsDisplay);
                    
                    map.fitBounds(bounds);
                    // map.setZoom(5);
                }
                get_all_available_driver();
            }
        }
        //jQuery.parseJSON

        console.log($.parseJSON(str_obj));
    }
    function cancel_request(ref)
    {
        var id         = $(ref).attr('data-id');
        if(id!="")
        {   
            var msg = msg || false;
            swal({
            title: "Are you sure to cancel the request?",
            text: msg,
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes",
            cancelButtonText: "No",
            closeOnConfirm: true,
            closeOnCancel: true
          },
          function(isConfirm)
          {
            if(isConfirm==true)
            {
                    
                var csrf_token = "{{ csrf_token() }}";
                var url ="{{ $module_url_path.'/cancel_request' }}"; 
                $.ajax({

                  url:url,
                  type:"POST",
                  data:{
                    '_token' : csrf_token,
                    'id' : id
                  },
                  success:function(response){

                      if(response.status == "success")
                      {
                        swal('Success!','Shipment request canceled successfully','success');
                        window.location.reload();
                      }
                      else if(response.status=='error_problem')
                      {
                        swal('Error!',"Something went wrong,Please try again later",'error'); 
                      }else  if(response.status=='error_not_id'){
                        swal('Error!',"Shipment identifier is missing,Please try again",'error'); 
                      }
                  }

                });
            }
          });
        }
        else
        {
          swal("Records not found"); 
        }
    }
</script>
@stop