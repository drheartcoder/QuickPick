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
                God's View
            </h3>
            <div class="box-tool">
                <a data-action="collapse" href="#"></a>
                <a data-action="close" href="#"></a>
            </div>
        </div>
        <div class="box-content">
        <div class="button-sctions">
           <ul>
            <li><button role="button" class="btn btn-default setclass">Enroute to Pickup</button></li>
            <li><button role="button" class="btn btn-default setclass">Reached Pickup</button></li>
            <li><button role="button" class="btn btn-default setclass">Journey Started</button></li>
            <li><button role="button" class="btn btn-default setclass">Available</button></li>
            <li><button role="button" class="btn btn-default setclass active">All</button></li>
            </ul>
            <div class="clearfix"></div>
        </div>
        <div class="row">
            <div class="col-sm-12 col-md-5 col-lg-5">
                <div class="map-main-page-inner-tab">
                    <a href="javascript:void(0);" class="active">Available</a>
                </div>
                <div class="clearfix"></div>
                <div class="searchboxs">
                    <input name="" type="text" placeholder="Search driver" />
                    <div class="map-icnss"><i class="fa fa-search"></i></div>
                </div>
                <div class="chat-messagess">
                   <div class="clearfix"></div>
                    <ul class="content-txt1 content-d">
                        <li>
                           <div class="avatar-outr"> <div class="avatar-img"><img src="../../images\admin\client-img-1.jpg" alt="" /></div>
                           <div class="mp-txs-online"></div>
                             </div>
                            <div class="avatar-content">
                                <div class="avtar-name">Michelle J. Bennett</div>
                                <p>+41 0698 5854</p>
                            </div>
                            <div class="clearfix"></div>
                        </li>
                        <li>
                            <div class="avatar-outr"><div class="avatar-img"><img src="../../images\admin\client-img-2.jpg" alt="" /></div><div class="mp-txs-offline"></div></div>
                            <div class="avatar-content">
                                <div class="avtar-name">Colleen C. Schippers</div>
                                <p>+41 0698 5854</p>
                            </div>
                            <div class="clearfix"></div>
                        </li>
                        <li>
                            <div class="avatar-outr"> <div class="avatar-img"><img src="../../images\admin\client-img-3.jpg" alt="" /></div><div class="mp-txs-online"></div></div>
                            <div class="avatar-content">
                                <div class="avtar-name">Connie G. Shedd</div>
                                <p>+41 0698 5854</p>
                            </div>
                            <div class="clearfix"></div>
                        </li>
                        <li>
                            <div class="avatar-outr"><div class="avatar-img"><img src="../../images\admin\client-staff.jpg" alt="" /></div><div class="mp-txs-online"></div></div>
                            <div class="avatar-content">
                                <div class="avtar-name">Michelle J. Bennett</div>
                                <p>+41 0698 5854</p>
                            </div>
                            <div class="clearfix"></div>
                        </li>
                        <li>
                            <div class="avatar-outr"><div class="avatar-img"><img src="../../images\admin\client-staff2.jpg" alt="" /></div><div class="mp-txs-online"></div></div>
                            <div class="avatar-content">
                                <div class="avtar-name">Allen A. Romero</div>
                                <p>+41 0698 5854</p>
                            </div>
                            <div class="clearfix"></div>
                        </li>
                        <li>
                            <div class="avatar-outr"><div class="avatar-img"><img src="../../images\admin\client-img-1.jpg" alt="" /></div><div class="mp-txs-online"></div></div>
                            <div class="avatar-content">
                                <div class="avtar-name">Michelle J. Bennett</div>
                                <p>+41 0698 5854</p>
                            </div>
                            <div class="clearfix"></div>
                        </li>
                        <li>
                            <div class="avatar-outr"> <div class="avatar-img"><img src="../../images\admin\client-img-2.jpg" alt="" /></div><div class="mp-txs-offline"></div></div>
                            <div class="avatar-content">
                                <div class="avtar-name">Colleen C. Schippers</div>
                                <p>+41 0698 5854</p>
                            </div>
                            <div class="clearfix"></div>
                        </li>
                        <li>
                            <div class="avatar-outr"> <div class="avatar-img"><img src="../../images\admin\client-img-3.jpg" alt="" /></div><div class="mp-txs-online"></div></div>
                            <div class="avatar-content">
                                <div class="avtar-name">Connie G. Shedd</div>
                                <p>+41 0698 5854</p>
                            </div>
                            <div class="clearfix"></div>
                        </li>
                        <li>
                            <div class="avatar-outr">  <div class="avatar-img"><img src="../../images\admin\client-staff.jpg" alt="" /></div><div class="mp-txs-online"></div></div>
                            <div class="avatar-content">
                                <div class="avtar-name">Michelle J. Bennett</div>
                                <p>+41 0698 5854</p>
                            </div>
                            <div class="clearfix"></div>
                        </li>
                        <li>
                            <div class="avatar-outr"><div class="avatar-img"><img src="../../images\admin\client-staff2.jpg" alt="" /></div><div class="mp-txs-online"></div></div>
                            <div class="avatar-content">
                                <div class="avtar-name">Allen A. Romero</div>
                                <p>+41 0698 5854</p>
                            </div>
                            <div class="clearfix"></div>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="col-sm-12 col-md-7 col-lg-7">
                <div class="map-div">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d130883.73129326482!2d73.90304115063152!3d19.93900283877269!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3bddeae4e0245423%3A0xeb6a128eb0f552ae!2sWebwing+Technologies+(+Website+Design+%26+Mobile+App+%26+Website+Development+Company)!5e0!3m2!1sen!2sin!4v1487651509958" width="100%" height="535px" display="block" frameborder="0" style="border:0" allowfullscreen></iframe>
                </div>
            </div>
        </div>
        </div>
  </div>
</div>
<script>
mapboxgl.accessToken = 'undefined';

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
});

</script>

@stop