@extends('admin.layout.master') @section('main_content')

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

<script src="{{ url('/') }}/js/admin/jquery.rotapie.js"></script>

<!-- BEGIN Page Title -->
<div class="page-title">
    <div>
        <h1><i class="fa fa-dashboard"></i> Dashboard</h1>

    </div>
</div>
<!-- END Page Title -->
                        
<!-- BEGIN Breadcrumb -->
<div id="breadcrumbs">
    <ul class="breadcrumb">
        <li class="active"><i class="fa fa-home"></i> Home</li>

    </ul>
</div>
<!-- END Breadcrumb -->

<!-- BEGIN Tiles -->
<div class="row">
    <div class="col-md-12">

        <div class="clearfix"></div>
        <div class="row">
            
            <div class="col-sm-12 col-md-6 col-lg-12">
               <div class="dashboard-statistics">
                    <div class="panel-heading-dash">Site Statistics</div>
                   <div class="panel-bodys dashbords">
                       <div class="row">
                           <div class="col-sm-12 col-md-6 col-lg-3">
                               <a class="ttl-express bg-aqua" href="{{url('/admin/users')}}">
                                 <span class="left-icns-tp"><i class="fa fa-users"></i></span>
                                 <span class="info-box-contentts">
                                     <span class="info-box-textt">Users</span>
                                     <span class="info-box-numberr">{{isset($arr_dashboard_counts['users_count']) ? $arr_dashboard_counts['users_count'] : 0}}</span>
                                 </span>
                               </a>
                           </div>
                            <div class="col-sm-12 col-md-6 col-lg-3">
                               <a class="ttl-express tile-green" href="{{url('/admin/driver')}}">
                                 <span class="left-icns-tp"><i class="fa fa-users"></i></span>
                                 <span class="info-box-contentts">
                                     <span class="info-box-textt">Drivers</span>
                                     <span class="info-box-numberr">{{isset($arr_dashboard_counts['drivers_count']) ? $arr_dashboard_counts['drivers_count'] : 0}}</span>
                                 </span>
                               </a>
                           </div>

                          <div class="col-sm-12 col-md-6 col-lg-3">
                               <a class="ttl-express tile-green" href="{{url('/admin/company')}}">
                                 <span class="left-icns-tp"><i class="fa fa-users"></i></span>
                                 <span class="info-box-contentts">
                                     <span class="info-box-textt">Companies</span>
                                     <span class="info-box-numberr">{{isset($arr_dashboard_counts['company_count']) ? $arr_dashboard_counts['company_count'] : 0}}</span>
                                 </span>
                               </a>
                          </div>

                           

                           <div class="col-sm-12 col-md-6 col-lg-3">
                               <a class="ttl-express tile-yellow" href="{{url('/admin/vehicle')}}">
                                 <span class="left-icns-tp"><i class="fa fa-car"></i></span>
                                 <span class="info-box-contentts">

                                     <span class="info-box-textt">Verified Vehicle</span>
                                     <span class="info-box-numberr">{{isset($arr_dashboard_counts['verified_vehicle_count']) ? $arr_dashboard_counts['verified_vehicle_count'] : 0}}</span>
                                 </span>
                               </a>
                           </div>

                           <div class="col-sm-12 col-md-6 col-lg-3">
                               <a class="ttl-express tile-yellow" href="{{url('/admin/driver_vehicle')}}">
                                 <span class="left-icns-tp"><i class="fa fa-car"></i></span>
                                 <span class="info-box-contentts">
                                     <span class="info-box-textt">Unverified Vehicle</span>
                                     <span class="info-box-numberr">{{isset($arr_dashboard_counts['unverified_vehicle_count']) ? $arr_dashboard_counts['unverified_vehicle_count'] : 0}}</span>
                                 </span>
                               </a>
                           </div>

                           <div class="col-sm-12 col-md-6 col-lg-3">
                               <a class="ttl-express tile-magenta" href="{{url('/admin/booking_summary')}}">
                                 <span class="left-icns-tp"><i class="fa fa-newspaper-o"></i></span>
                                 <span class="info-box-contentts">
                                     <span class="info-box-textt">Total Bookings</span>
                                     <span class="info-box-numberr">{{isset($arr_dashboard_counts['booking_count']) ? $arr_dashboard_counts['booking_count'] : 0}}</span>
                                 </span>
                               </a>
                           </div>

                            <div class="col-sm-12 col-md-6 col-lg-3">
                               <a class="ttl-express tile-yellow" href="{{url('/admin/contact_enquiry')}}">
                                 <span class="left-icns-tp"><i class="fa fa-phone"></i></span>
                                 <span class="info-box-contentts">
                                     <span class="info-box-textt">Contact Enquiries</span>
                                     <span class="info-box-numberr">{{isset($arr_dashboard_counts['contact_enquiry_count']) ? $arr_dashboard_counts['contact_enquiry_count'] : 0}}</span>
                                 </span>
                               </a>
                          </div>

                            <div class="col-sm-12 col-md-6 col-lg-3">
                               <a class="ttl-express bg-aqua" href="{{url('/admin/email_template')}}">
                                 <span class="left-icns-tp"><i class="fa fa-envelope"></i></span>
                                 <span class="info-box-contentts">
                                     <span class="info-box-textt">Email Template</span>
                                     <span class="info-box-numberr">{{isset($arr_dashboard_counts['email_template_count']) ? $arr_dashboard_counts['email_template_count'] : 0}}</span>
                                 </span>
                               </a>
                           </div>

                           

                           

                       </div>
                   </div>
               </div>
            </div>
           
            <div class="col-sm-12 col-md-6 col-lg-6">
                <div class="dashboard-statistics">
                    <div class="panel-heading-dash line-lft">No of Users/Drivers/Companies Registered </div>
                    <div class="panel-bodys">
                        <div id="users_drivers_chart_div" style="height: 400px; width: 100%;"></div>
                    </div>
                </div>
            </div>
            
            <div class="col-sm-12 col-md-6 col-lg-6">
                <div class="dashboard-statistics">
                    <div class="panel-heading-dash line-lft">Latest Week Ride Details</div>
                    <div class="panel-bodys">
                        <div id="chartContainer1" style="height: 400px; width: 100%;"></div>
                    </div>
                </div>
            </div>

            {{-- <div class="col-sm-12 col-md-6 col-lg-12">
                <div class="dashboard-statistics">
                    <div class="panel-heading-dash line-lft">Ride Earning Details</div>
                    <div class="panel-bodys">
                        <div id="chartContainer3" style="height: 400px; width: 100%;"></div>
                    </div>
                </div>
            </div> --}}


            <div class="col-sm-12 col-md-12 col-lg-12">
                <div class="chat-panel panel panel-info">
                    <div class="panel-heading">
                        <div class="panel-title-box">
                            Notifications Panel
                            <div class="clearfix"></div>
                        </div>
                    </div>

                    <div class="panel-heading padding-15 content-txt1 content-d" style="background:none;" id="dashbord_notification_div">

                    </div>

                </div>
            </div>
        </div>
        </div>

        <input type="hidden" id="arr_encoded_user_drivers_details" value="{{ isset($arr_encoded_user_drivers_details) ? $arr_encoded_user_drivers_details : '' }}">
        <input type="hidden" id="arr_encoded_ride_details" value="{{ isset($arr_encoded_ride_details) ? $arr_encoded_ride_details : '' }}">
        
        <script src="{{ url('/') }}/js/admin/charts_loader.js"></script>

            <script type="text/javascript">
            google.charts.load('current', {'packages':['corechart','bar']});
            google.charts.setOnLoadCallback(drawUsersDriversChart);
            google.charts.setOnLoadCallback(drawRideChart);

            function drawUsersDriversChart() {
              
              var arr_user_drivers_details = [];
              var arr_encoded_user_drivers_details = $('#arr_encoded_user_drivers_details').val();
              arr_user_drivers_details = JSON.parse(arr_encoded_user_drivers_details);
            console.log(arr_user_drivers_details);
              var data = new google.visualization.arrayToDataTable(arr_user_drivers_details);
              
              var options = {
                  width: 750,
                  chart: {
                      title: 'No of Users/Drivers/Companies Registered'
                      // subtitle: 'Users on the left, Drivers on the right'
                  },
                  bars: 'vertical', // Required for Material Bar Charts.
                  axes: {
                    x: {
                        distance: {label: 'parsecs'}, // Bottom x-axis.
                        brightness: {side: 'top', label: 'apparent magnitude'} // Top x-axis.
                      }
                  }
              };

              var chart = new google.charts.Bar(document.getElementById('users_drivers_chart_div'));
              chart.draw(data, options);
            }

            function drawRideChart() {
              
              var arr_ride_details = [];
              var arr_encoded_ride_details = $('#arr_encoded_ride_details').val();
              arr_ride_details = JSON.parse(arr_encoded_ride_details);
           
              var data = new google.visualization.arrayToDataTable(arr_ride_details);
              var options = {
                  title: 'Ride Details',
                  width: 750,
                  chart: { title: 'Ride Details',
                           subtitle: 'Latest Week Ride Details' },
                  bars: 'vertical',
                  bar: { groupWidth: "90%" }
              };

              var chart = new google.charts.Bar(document.getElementById('chartContainer1'));
              chart.draw(data, options);
            }

        
          </script>

@stop