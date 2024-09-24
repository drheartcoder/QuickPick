@extends('company.layout.master')                
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
                <a href="{{ url($company_panel_slug.'/dashboard') }}">Dashboard</a>
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
            <div class="col-sm-12 col-md-7 col-lg-12">
                
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
            if(type!=ride_status){
                var url = module_url_path+'?ride_status='+type;
                window.location.href = url;
            }
        }
    }

</script>
<script type="text/javascript">

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
        $.ajax({
                url:MODULE_URL_PATH+'/available_driver',
                type:'GET',
                data:'flag=true',
                dataType:'json',
                success:function(response)
                {
                    if(response.status == 'success'){
                        show_on_map_available_driver(response.available_driver);
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
                        SetMarker(obj_value);
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
    function LoadMap() {

        var mapOptions = {
            center: new google.maps.LatLng(0,0),
            zoom: 2,
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            scrollwheel: true,
            disableDefaultUI: true,
        };
        map = new google.maps.Map(document.getElementById("dvMap"), mapOptions);
        bounds = new google.maps.LatLngBounds();
    };

    function SetMarker(data) {

        if(data.status!=undefined){

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

            //var marker_icon = BASE_URL+'/node_assets/images/truck.png';

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

            // map.setOptions({ maxZoom: 16 });
            
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
                            '<button type="button" data-driver-id='+this.driver_id+' class="btn btn-secondary" onclick="closeInfoWindow(this)">Close</button>'+
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
</script>
@stop