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
           {{-- {{dd($ride_status)}} --}}
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
            <div class="col-sm-12 col-md-5 col-lg-5">
                <div class="map-main-page-inner-tab">
                    <a href="javascript:void(0);" class="active" onclick="track_all_vehicles()">
                        @if(isset($ride_status) && $ride_status == 'TO_BE_PICKED' )
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
                
                <div class="searchboxs" id="search_drivers" style="display: none">
                    <input name="search_term" type="text" placeholder="Search driver" onkeyup="highlightSearch(this)" />
                    <div class="map-icnss"><i class="fa fa-search"></i></div>
                </div>
                
                <div class="chat-messagess">
                   <div class="clearfix"></div>
                    <ul class="content-txt1 content-d" id="driver-list">
                        <li id="no_records_found">
                           <div class="avatar-outr">
                           <div class="mp-txs"></div>
                             </div>
                            <div class="avatar-content">
                                <center><div class="avtar-name">No Driver details available.</div></center>
                            </div>
                            <div class="clearfix"></div>
                        </li>              

                        <li id="search_no_records_found" style="display: none;">
                           <div class="avatar-outr">
                           <div class="mp-txs"></div>
                             </div>
                            <div class="avatar-content">
                                <center><div class="avtar-name">No Driver details available.</div></center>
                            </div>
                            <div class="clearfix"></div>
                        </li>              

                    </ul>
                </div>
            </div>
            <div class="col-sm-12 col-md-7 col-lg-7">
                <div  id="dvMap" style="width:100%; height:533px">
                
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

    function highlightSearch(ref){
        
        var inputVal = $(ref).val();

        var ul = $('#driver-list');
        var count = 0;

        ul.find('li').each(function(index, row) {
            
            var li = $(row);
            var id = $(this).attr('data-id');

            var found = false;
            li.find('div.avatar-content').each(function(index, row){
                var div = $(row);
                var user_type = $(div).attr('data-user-type');
                if(user_type == "driver")
                {
                    var regExp = new RegExp(inputVal, 'i');
                    if (regExp.test($(div).text())) 
                    {
                        $('#'+id).hide();
                        found = true;
                        return false;
                    } 
                }
                else{
                    $('#'+id).show();
                }
            });  
            if (found == true)
            {
              $(li).show();
                count = count  + 1;
            } 
            else 
            {
              $(li).hide();
            }
        });
        if (count == 0) {
          $('#search_no_records_found').show();
        }
        else {
          $('#search_no_records_found').hide();
        }
    }

</script>
<script type="text/javascript">

    var BASE_URL        = "{{url('/')}}";
    var MODULE_URL_PATH = '{{$module_url_path}}';

    var markers            = [];
    var map_markers        = [];
    var arr_driver_details = [];

    $( document ).ready(function() {
        setTimeout(function(){ 
            LoadMap();
        }, 2000);

        setInterval(function(){ 
        // setTimeout(function(){ 
            track_available_vehicles();
        }, 5000);
    });

    function track_available_vehicles() {
        $.ajax({
                url:MODULE_URL_PATH+'/track_current_booking?status=TO_BE_PICKED',
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
        
        if(obj_available_driver!=undefined && obj_available_driver.length>0){
            
            $.each(obj_available_driver,function(obj_index,obj_value){
                
                if(obj_value.booking_status!=undefined && obj_value.booking_status == 'TO_BE_PICKED'){

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

                    /*make driver html*/

                    var html_present   = 'NO';                    
                    if (arr_driver_details.length > 0) {
                        arr_driver_details.forEach(function (value, index) {
                            if (value != undefined) {
                                if (value['driver_id'] != undefined) {
                                    if (value['driver_id'] == obj_value.driver_id) {
                                        arr_driver_details.splice(index, 1);
                                        html_present    = 'YES';
                                    }
                                }
                            }
                        });
                    }
                    obj_value['html_present']   = html_present;
                    arr_driver_details.push(obj_value);

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
                                                if (inner_value['driver_id'] == obj_value.driver_id && obj_value.booking_status == 'TO_BE_PICKED') {
                                                    
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
                    
                    // console.log("create_new_map-->",create_new_map);   

                    if(create_new_map == "YES") {
                        SetMarker(obj_value);
                    }

                    var create_new_html = "YES";
                    arr_driver_details.forEach(function (value, index) {
                        if (value != undefined) {
                            if (value['driver_id'] != undefined) {
                                if ((value['driver_id'] == obj_value.driver_id) && (value['html_present'] == 'YES') ) {
                                    create_new_html = "NO";
                                }
                            }
                        }
                    });

                    if(create_new_html == "YES") {
                        genrate_driver_html();
                    }
                }
                else 
                {
                    // /*remove busy drivers*/

                    if (markers.length > 0) {
                        markers.forEach(function (value, index) {
                            if (value != undefined) {
                                if (value['driver_id'] != undefined) {
                                    if (value['driver_id'] == obj_value.driver_id && (obj_value.booking_status == 'IN_TRANSIT' || obj_value.booking_status == 'COMPLETED')) {
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
                                    if (value['driver_id'] == obj_value.driver_id && (obj_value.booking_status == 'IN_TRANSIT' || obj_value.booking_status == 'COMPLETED')) {
                                        value.setMap(null);
                                        map_markers.splice(index, 1);
                                    }
                                }
                            }
                        });
                    }

                    if(arr_driver_details.length>0){
                        arr_driver_details.forEach(function (value, index) {
                            if (value != undefined) {
                                if (value.driver_id != undefined) {
                                  if (value['driver_id'] == obj_value.driver_id && (obj_value.booking_status == 'IN_TRANSIT' || obj_value.booking_status == 'COMPLETED')) {
                                    arr_driver_details.splice(index, 1);
                                  }
                                }
                              }
                        });
                    }
                    genrate_driver_html();
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
            scrollwheel: true
        };
        map = new google.maps.Map(document.getElementById("dvMap"), mapOptions);
        bounds = new google.maps.LatLngBounds();
    };

    function SetMarker(data) {

        if(data.booking_status!=undefined && data.booking_status == 'TO_BE_PICKED'){

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

            map.setOptions({ maxZoom: 16 });
            
            google.maps.event.addListener(marker, 'click', function() {

                var company_name = '';
                if(data.is_company_driver!=undefined && data.is_company_driver == '1'){
                    company_name = data.company_name ?  data.company_name : '';
                }
                else if(data.is_company_driver!=undefined && data.is_company_driver == '0'){
                    // company_name = data.company_name ?  data.company_name : '';
                    company_name = 'QuickPick';
                }


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

    function genrate_driver_html(){
        
        var generated_html = '';

        if(arr_driver_details.length>0){
            arr_driver_details.forEach(function (value, index) {
                
                var view_url = 'javascript:void(0);'
                
                if(value.booking_status == 'TO_BE_PICKED'){
                    var booking_master_id     = btoa(parseInt(value.booking_master_id));
                    var booking_status       = btoa(value.booking_status);
                    view_url = BASE_URL+'/company/track_booking/view?enc_id='+booking_master_id+'&status='+booking_status+'&curr_page=track_booking';
                }
                generated_html +=   '<li data-id="'+index+'">'+
                                       '<div class="avatar-outr"> <div class="avatar-img"><img src="'+BASE_URL+'/uploads/default-profile.png" alt="" /></div>'+
                                            '<div class="mp-txs-online"></div>'+
                                        '</div>'+
                                        '<div class="avatar-content pull-left" data-user-type="driver">'+
                                            '<div class="avtar-name">'+value.driver_name+'</div>'+
                                            '<div class="avtar-ps">'+value.vehicle_type_name +' - '+ value.vehicle_number+'</div>'+ 
                                            '<p>+ '+value.mobile_no+' / '+value.email+'</p>'+
                                        '</div>'+
                                        '<div class="avatar-ivew">'+
                                           '<a target="_blank" class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip" href="'+view_url+'" title="View Details"><i class="fa fa-eye"></i></a>'+
                                        '</div>'+
                                        '<div class="clearfix"></div>'+
                                    '</li>';
            });
            
            generated_html +=   '<li id="search_no_records_found" style="display: none;">'+
                                   '<div class="avatar-outr">'+
                                   '<div class="mp-txs"></div></div>'+
                                    '<div class="avatar-content">'+
                                        '<center><div class="avtar-name">No Driver details available.</div></center>'+
                                    '</div>'+
                                    '<div class="clearfix"></div>'+
                                '</li>';

            $('#search_drivers').show();
        }
        else{
            
            generated_html =   '<li>'+
                                   '<div class="avatar-outr">'+
                                   '<div class="mp-txs"></div></div>'+
                                    '<div class="avatar-content">'+
                                        '<center><div class="avtar-name">No Driver details available.</div></center>'+
                                    '</div>'+
                                    '<div class="clearfix"></div>'+
                                '</li>'+
                                '<li id="search_no_records_found" style="display: none;">'+
                                   '<div class="avatar-outr">'+
                                   '<div class="mp-txs"></div></div>'+
                                    '<div class="avatar-content">'+
                                        '<center><div class="avtar-name">No Driver details available.</div></center>'+
                                    '</div>'+
                                    '<div class="clearfix"></div>'+
                                '</li>';
        }   

        $('#driver-list').html('');
        $('#driver-list').html(generated_html);

        return true;
    }

</script>
@stop