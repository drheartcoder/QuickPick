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
            <div class="col-sm-12 col-md-5 col-lg-12">
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
                                    // dd($ride);

                                        $booking_unique_id = (isset($ride['booking_unique_id']) && $ride['booking_unique_id']!='') ? $ride['booking_unique_id'] :'';
                                        $profile_image     = (isset($ride['profile_image']) && $ride['profile_image']!='') ? $ride['profile_image'] :'';
                                        $first_name        = (isset($ride['first_name']) && $ride['first_name']!='') ? $ride['first_name'] :'';
                                        $last_name         = (isset($ride['last_name']) && $ride['last_name']!='') ? $ride['last_name'] :'';
                                        $full_name         = $first_name.' '.$last_name;
                                        $full_name         = ($full_name!=' ')?$full_name:'-';
                                        $email             = (isset($ride['email']) && $ride['email']!='') ? $ride['email'] :'';
                                        $mobile_no         = (isset($ride['mobile_no']) && $ride['mobile_no']!='') ? $ride['mobile_no'] :'';
                                        $vehicle_full_name = (isset($ride['vehicle_full_name']) && $ride['vehicle_full_name']!='') ? $ride['vehicle_full_name'] :'';


                                ?>

                                <li data-id="{{$ride['id']}}">
                                   <div class="avatar-outr"> <div class="avatar-img"><img src="{{$profile_image}}" alt="" /></div>
                                        <div class="mp-txs-online"></div>
                                    </div>

                                    <div class="avatar-content pull-left" data-user-type="driver">
                                        <div class="avtar-name">{{$full_name}} </div>
                                        <div class="avtar-ps">{{$vehicle_full_name}} ({{$booking_unique_id}})</div>
                                        <p>+ {{$mobile_no}} / {{$email}}</p>
                                        <p>Booking Unique Id : ({{$booking_unique_id}})</p>
                                    </div>
                                    <div class="avatar-ivew">
                                        <a target="_blank" class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip" href="{{$module_url_path}}/view?enc_id={{base64_encode($ride['id'])}}&status={{base64_encode($ride_status)}}&curr_page=track_booking" title="View completed ride Details"><i class="fa fa-eye"></i></a>
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
           
        </div>
        </div>
  </div>
</div>


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

@stop