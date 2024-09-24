@extends('company.layout.master') @section('main_content')

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
@include('admin.layout._operation_status')                
<!-- BEGIN Tiles -->
<div class="row">
    <div class="col-md-12">
        <div class="clearfix"></div>
        <div class="row">
            <div class="col-sm-12 col-md-12 col-lg-12">
               <div class="dashboard-statistics">
                    <div class="panel-heading-dash">Site Statistics</div>
                   <div class="panel-bodys dashbords">
                       <div class="row">
                          
                            <div class="col-sm-2 col-md-3 col-lg-3">
                               <a class="ttl-express tile-green" href="{{url('/company/driver')}}">
                                 <span class="left-icns-tp"><i class="fa  fa-user"></i></span>
                                 <span class="info-box-contentts">
                                     <span class="info-box-textt">Drivers</span>
                                     <span class="info-box-numberr">{{$driver_count}}</span>
                                 </span>
                               </a>
                           </div>
                            <div class="col-sm-2 col-md-3 col-lg-3">
                               <a class="ttl-express tile-magenta" href="{{url('/company/vehicle')}}">
                                 <span class="left-icns-tp"><i class="fa fa-motorcycle"></i></span>
                                 <span class="info-box-contentts">
                                     <span class="info-box-textt">Vehicles</span>
                                     <span class="info-box-numberr">{{$vehicle_count}}</span>
                                 </span>
                               </a>
                           </div>
                            <div class="col-sm-2 col-md-3 col-lg-3">
                               <a class="ttl-express bg-aqua" href="{{url('/company/booking_summary')}}">
                                 <span class="left-icns-tp"><i class="fa fa-list"></i></span>
                                 <span class="info-box-contentts">Bookings
                                     <span class="info-box-textt"></span>
                                     <span class="info-box-numberr">{{$booking_count}}</span>
                                 </span>
                               </a>
                           </div>

                            <div class="col-sm-2 col-md-3 col-lg-3">
                               <a class="ttl-express bg-aqua" href="{{url('/company/track_booking/booking_history')}}">
                                 <span class="left-icns-tp"><i class="fa fa-street-view"></i></span>
                                 <span class="info-box-contentts">Bookings History
                                     <span class="info-box-textt"></span>
                                     <span class="info-box-numberr">{{$booking_count}}</span>
                                 </span>
                               </a>
                           </div>
                         </div>
                   </div>
               </div>
            </div>
            <div class="col-sm-12 col-md-6 col-lg-6">
                <div class="dashboard-statistics">
                    <div class="panel-heading-dash line-lft">No of Drivers Registered </div>
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
            <div class="col-sm-12 col-md-12 col-lg-12">
                <div class="chat-panel panel panel-danger">
                    <div class="panel-heading">
                        <div class="panel-title-box">
                            Notifications Alerts Panel
                            <div class="clearfix"></div>
                        </div>
                    </div>
                    <div class="panel-heading padding-15 content-txt1 content-d" style="background:none;" id="dashbord_notification_div">
                        
                    </div>
                </div>
            </div>
        </div>
        
        
        <div class="modal fade view-modals" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Transaction Preview</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
              <div class="review-detais">
                  <div class="boldtxts">ID</div> 
                  <div class="rightview-txt">7</div>
                  <div class="clearfix"></div>
              </div>
              <div class="review-detais">
                  <div class="boldtxts">Order Id</div> 
                  <div class="rightview-txt">Event- ABCXDF596DJF3</div>
                  <div class="clearfix"></div>
              </div>
              <div class="review-detais">
                  <div class="boldtxts">Date</div> 
                  <div class="rightview-txt">11 Dec 2018</div>
                  <div class="clearfix"></div>
              </div>
              <div class="review-detais">
                  <div class="boldtxts">Time</div> 
                  <div class="rightview-txt">11:56 AM</div>
                  <div class="clearfix"></div>
              </div>
                  <div class="review-detais">
                  <div class="boldtxts">Event Type</div> 
                  <div class="rightview-txt">Paid</div>
                  <div class="clearfix"></div>
              </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Print</button>
              </div>
            </div>
          </div>
        </div> 
        </div>
      <input type="hidden" id="arr_encoded_user_drivers_details"  value="{{ isset($arr_encoded_user_drivers_details) ? $arr_encoded_user_drivers_details : '' }}">


      <input type="hidden" id="arr_encoded_ride_details" value="{{ isset($arr_encoded_ride_details) ? $arr_encoded_ride_details : '' }}">
        
        
        <script src="http://192.168.1.92/quickpick/js/admin/charts_loader.js"></script>

            <script type="text/javascript">
            google.charts.load('current', {'packages':['corechart','bar']});
            google.charts.setOnLoadCallback(drawUsersDriversChart);
            google.charts.setOnLoadCallback(drawRideChart);

            function drawUsersDriversChart() {
              
              var arr_user_drivers_details = [];
              var arr_encoded_user_drivers_details = $('#arr_encoded_user_drivers_details').val();
              arr_user_drivers_details = JSON.parse(arr_encoded_user_drivers_details);
            
              var data = new google.visualization.arrayToDataTable(arr_user_drivers_details);
            
              var options = {
                  width: 750,
                  chart: {
                      title: 'No of Drivers Registered',

                      // subtitle: 'Users on the left, Drivers on the right'
                  },
                  colors: ['green'],
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
         
 <script>
        $(function(){
            $('#pie').rotapie({
                slices: [
                    {color: '#33414e', percentage: 10 },
                    {color: '#fea223', percentage: 30 },
                    {color: '#1caf9a', percentage: 60 }
                ],
                sliceIndex: 0,
                deltaAngle: 0.2,
                minRadius: 100,
                maxRadius: 110,
                minInnerRadius: 55,
                maxInnerRadius: 65,
                innerColor: '#fff',
                minFontSize: 30,
                maxFontSize: 40,
                fontYOffset: 0,
                fontFamily: 'Open Sans',
                fontWeight: 'bold', 
                decimalPoint: '.',
                clickable: true 
               
            });
          
        });
    </script>
    <script type="text/javascript">
      
  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-36251023-1']);
  _gaq.push(['_setDomainName', 'jqueryscript.net']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>

        @stop