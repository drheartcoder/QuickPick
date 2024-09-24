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
                <i class="fa fa-street-view"></i>                
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
                    <a href="javascript:void(0);" class="active">
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
                @if(isset($arr_rides) && sizeof($arr_rides)>0)
                <div class="searchboxs">
                    <input name="search_term" type="text" placeholder="Search driver" onkeyup="highlightSearch(this)" />
                    <div class="map-icnss"><i class="fa fa-search"></i></div>
                </div>
                @endif
                <div class="chat-messagess">
                   <div class="clearfix"></div>
                    <ul class="content-txt1 content-d" id="driver-list">
                        @if(isset($arr_rides) && sizeof($arr_rides)>0)
                            @foreach($arr_rides as $key => $ride)

                                <?php

                                        $profile_image = url('/uploads/default-profile.png');
                                        if(isset($ride['driver_details']['profile_image']) && $ride['driver_details']['profile_image']!='')
                                        {
                                            if(file_exists($user_profile_base_img_path.$ride['driver_details']['profile_image']))
                                            {
                                                $profile_image = $user_profile_public_img_path.$ride['driver_details']['profile_image'];
                                            }
                                        }
                                        
                                        $first_name = (isset($ride['driver_details']['first_name']) && $ride['driver_details']['first_name']!='') ? $ride['driver_details']['first_name'] :'';
                                        $last_name  = (isset($ride['driver_details']['last_name']) && $ride['driver_details']['last_name']!='') ? $ride['driver_details']['last_name'] :'';
                                        $full_name  = $first_name.' '.$last_name;
                                        $full_name  = ($full_name!=' ')?$full_name:'-';

                                        $email       = (isset($ride['driver_details']['email']) && $ride['driver_details']['email']!='') ? $ride['driver_details']['email'] :'';
                                        $mobile_no  = (isset($ride['driver_details']['mobile_no']) && $ride['driver_details']['mobile_no']!='') ? $ride['driver_details']['mobile_no'] :'';
                                        
                                        $vehicle_name       = (isset($ride['vehicle_details']['vehicle_name']) && $ride['vehicle_details']['vehicle_name']!='') ? $ride['vehicle_details']['vehicle_name'] :'';
                                        $vehicle_type       = (isset($ride['vehicle_details']['vehicle_type_details']['vehicle_type']) && $ride['vehicle_details']['vehicle_type_details']['vehicle_type']!='') ? $ride['vehicle_details']['vehicle_type_details']['vehicle_type'] :'';
                                        $vehicle_model_name = (isset($ride['vehicle_details']['vehicle_model_name']) && $ride['vehicle_details']['vehicle_model_name']!='') ? $ride['vehicle_details']['vehicle_model_name'] :'';
                                        $vehicle_number     = (isset($ride['vehicle_details']['vehicle_number']) && $ride['vehicle_details']['vehicle_number']!='') ? $ride['vehicle_details']['vehicle_number'] :'';

                                        $vehicle_full_name = '('.$vehicle_name.'-'.$vehicle_model_name.' ('.$vehicle_type.')-'.$vehicle_number.')';


                                ?>

                                <li data-id="{{$ride['id']}}">
                                   <div class="avatar-outr"> <div class="avatar-img"><img src="{{$profile_image}}" alt="" /></div>
                                        <div class="mp-txs-online"></div>
                                    </div>

                                    <div class="avatar-content pull-left" data-user-type="driver">
                                        <div class="avtar-name">{{$full_name}}</div>
                                        <div class="avtar-ps">{{$vehicle_full_name}}</div>
                                        <p>+ {{$mobile_no}} / {{$email}}</p>
                                    </div>
                                    <div class="avatar-ivew">
                                        <a target="_blank" class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip" href="{{$module_url_path}}/view/{{base64_encode($ride['id'])}}" title="View Details"><i class="fa fa-eye"></i></a>
                                    </div>
                                    <div class="clearfix"></div>
                                </li>
                            @endforeach
                        @else
                            <li>
                               <div class="avatar-outr">
                               <div class="mp-txs"></div>
                                 </div>
                                <div class="avatar-content">
                                    <center><div class="avtar-name">No Driver details available.</div></center>
                                </div>
                                <div class="clearfix"></div>
                            </li>
                        @endif

                        <li id="no_records_found" style="display: none;">
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
                <div class="map-div">
                {{-- <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d130883.73129326482!2d73.90304115063152!3d19.93900283877269!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3bddeae4e0245423%3A0xeb6a128eb0f552ae!2sWebwing+Technologies+(+Website+Design+%26+Mobile+App+%26+Website+Development+Company)!5e0!3m2!1sen!2sin!4v1487651509958" width="100%" height="535px" display="block" frameborder="0" style="border:0" allowfullscreen></iframe> --}}
                <div id="map"></div>
                </div>
            </div>
        </div>
        </div>
  </div>
</div>

<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAMxj8U1CuCcFhEUupXEID8XUaW6Ay6rZc&callback=initMap"> </script>


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
          $('#no_records_found').show();
        }
        else {
          $('#no_records_found').hide();
        }
    }
</script>

<script>
      var map;
      function initMap() {
        map = new google.maps.Map(document.getElementById('map'), {
          zoom: 16,
          center: new google.maps.LatLng(-33.91722, 151.23064),
          mapTypeId: 'roadmap'
        });

        var iconBase = 'https://maps.google.com/mapfiles/kml/shapes/';
        var icons = {
          parking: {
            icon: iconBase + 'parking_lot_maps.png'
          },
          library: {
            icon: iconBase + 'library_maps.png'
          },
          info: {
            icon: iconBase + 'info-i_maps.png'
          }
        };

        var features = [
          {
            position: new google.maps.LatLng(-33.91721, 151.22630),
            type: 'info'
          }, {
            position: new google.maps.LatLng(-33.91539, 151.22820),
            type: 'info'
          }, {
            position: new google.maps.LatLng(-33.91747, 151.22912),
            type: 'info'
          }, {
            position: new google.maps.LatLng(-33.91910, 151.22907),
            type: 'info'
          }, {
            position: new google.maps.LatLng(-33.91725, 151.23011),
            type: 'info'
          }, {
            position: new google.maps.LatLng(-33.91872, 151.23089),
            type: 'info'
          }, {
            position: new google.maps.LatLng(-33.91784, 151.23094),
            type: 'info'
          }, {
            position: new google.maps.LatLng(-33.91682, 151.23149),
            type: 'info'
          }, {
            position: new google.maps.LatLng(-33.91790, 151.23463),
            type: 'info'
          }, {
            position: new google.maps.LatLng(-33.91666, 151.23468),
            type: 'info'
          }, {
            position: new google.maps.LatLng(-33.916988, 151.233640),
            type: 'info'
          }, {
            position: new google.maps.LatLng(-33.91662347903106, 151.22879464019775),
            type: 'parking'
          }, {
            position: new google.maps.LatLng(-33.916365282092855, 151.22937399734496),
            type: 'parking'
          }, {
            position: new google.maps.LatLng(-33.91665018901448, 151.2282474695587),
            type: 'parking'
          }, {
            position: new google.maps.LatLng(-33.919543720969806, 151.23112279762267),
            type: 'parking'
          }, {
            position: new google.maps.LatLng(-33.91608037421864, 151.23288232673644),
            type: 'parking'
          }, {
            position: new google.maps.LatLng(-33.91851096391805, 151.2344058214569),
            type: 'parking'
          }, {
            position: new google.maps.LatLng(-33.91818154739766, 151.2346203981781),
            type: 'parking'
          }, {
            position: new google.maps.LatLng(-33.91727341958453, 151.23348314155578),
            type: 'library'
          }
        ];

        // Create markers.
        features.forEach(function(feature) {
          var marker = new google.maps.Marker({
            position: feature.position,
            icon: icons[feature.type].icon,
            map: map
          });
        });
      }
    </script>

<script>
/*mapboxgl.accessToken = 'undefined';

var map = new mapboxgl.Map({
    container: 'map',
    style: 'mapbox://styles/mapbox/streets-v9'
});

map.on('load', function () {

    var width = 64; // The image will be 64 pixels square
    var bytesPerPixel = 4; // Each pixel is represented by 4 bytes: red, green, blue, and alpha.
    var data = new Uint8Array(width * width * bytesPerPixel);

    for (var x = 0; x < width; x++) {
        for (var y = 0; y < width; y++) {
            var offset = (y * width + x) * bytesPerPixel;
            data[offset + 0] = y / width * 255; // red
            data[offset + 1] = x / width * 255; // green
            data[offset + 2] = 128;             // blue
            data[offset + 3] = 255;             // alpha
        }
    }

    map.addImage('gradient', {width: width, height: width, data: data});

    map.addLayer({
        "id": "points",
        "type": "symbol",
        "source": {
            "type": "geojson",
            "data": {
                "type": "FeatureCollection",
                "features": [{
                    "type": "Feature",
                    "geometry": {
                        "type": "Point",
                        "coordinates": [0, 0]
                    }
                }]
            }
        },
        "layout": {
            "icon-image": "gradient"
        }
    });
});*/

</script>

@stop