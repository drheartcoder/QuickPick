@extends('company.layout.master')                
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
      <a href="{{ url($company_panel_slug.'/dashboard') }}">Dashboard
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
            $arr_data = filter_completed_trip_details($arr_bookings);
          
        ?>
          <div class="box-content studt-padding">
            <div class="row">
                <div class="col-md-12">
                  <h3>{{$page_title or ''}}</h3>
                  <br>
                    <table class="table table-bordered">
                      <tbody>
                            
                            <tr>
                              <th style="width: 20%">Booking ID
                              </th>
                              <td style="width: 30%">
                                
                                {{isset($arr_data['booking_unique_id']) ? $arr_data['booking_unique_id'] : '' }}
                              </td>
                              <th style="width: 20%">Booking Date
                              </th>
                              <td style="width: 30%">
                                {{isset($arr_data['booking_date']) ? $arr_data['booking_date'] : '' }}
                              </td>
                            </tr> 

                            <tr>
                              <th style="width: 20%">Ride Start Date
                              </th>
                              <td style="width: 30%">
                                {{isset($arr_data['start_datetime']) ? $arr_data['start_datetime'] : '' }}
                              </td>
                              <th style="width: 20%">Ride End Date
                              </th>
                              <td style="width: 30%">
                                {{isset($arr_data['end_datetime']) ? $arr_data['end_datetime'] : '' }}
                              </td>
                            </tr> 

                            <tr>
                              <th style="width: 20%">User Name
                              </th>
                              <td style="width: 30%">
                                {{isset($arr_data['user_name']) ? $arr_data['user_name'] : '' }}
                              </td>
                              <th style="width: 20%">Driver Name
                              </th>
                              <td style="width: 30%">
                                {{isset($arr_data['driver_name']) ? $arr_data['driver_name'] : '' }} <strong>({{isset($arr_data['company_name']) ? $arr_data['company_name'] : '' }})</strong>
                              </td>
                            </tr> 

                            <tr>
                              <th style="width: 20%">User Contact No.
                              </th>
                              <td style="width: 30%">
                                {{isset($arr_data['user_country_code']) ? $arr_data['user_country_code'] : '' }} {{isset($arr_data['user_contact_no']) ? $arr_data['user_contact_no'] : '' }}
                              </td>
                              <th style="width: 20%">Driver Contact No.
                              </th>
                              <td style="width: 30%">
                                {{isset($arr_data['driver_country_code']) ? $arr_data['driver_country_code'] : '' }} {{isset($arr_data['driver_contact_no']) ? $arr_data['driver_contact_no'] : '' }}
                              </td>
                            </tr> 

                            <tr>
                              <th style="width: 20%">User Email
                              </th>
                              <td style="width: 30%">
                                {{isset($arr_data['user_email']) ? $arr_data['user_email'] : '' }}
                              </td>
                              <th style="width: 20%">Driver Email
                              </th>
                              <td style="width: 30%">
                                {{isset($arr_data['driver_email']) ? $arr_data['driver_email'] : '' }}
                              </td>
                            </tr> 

                            <tr>
                              <th style="width: 20%">Vehicle Type
                              </th>
                              <td style="width: 30%">
                                {{isset($arr_data['vehicle_type']) ? $arr_data['vehicle_type'] : '' }}
                              </td>
                              <th style="width: 20%">Vehicle Number
                              </th>
                              <td style="width: 30%">
                                {{isset($arr_data['vehicle_number']) ? $arr_data['vehicle_number'] : '' }}
                              </td>
                            </tr> 

                            {{-- <tr>
                              <th style="width: 20%">Vehicle Number
                              </th>
                              <td style="width: 30%">
                                {{isset($arr_data['vehicle_number']) ? $arr_data['vehicle_number'] : '' }}
                              </td>
                              <th style="width: 20%">Vehicle Model
                              </th>
                              <td style="width: 30%">
                                {{isset($arr_data['vehicle_model']) ? $arr_data['vehicle_model'] : '' }}
                              </td>
                            </tr>  --}}

                            <tr>
                              <th style="width: 20%">Pick up location
                              </th>
                              <td style="width: 30%">
                                {{isset($arr_data['pickup_location']) ? $arr_data['pickup_location'] : '' }}
                              </td>
                              <th style="width: 20%">Drop up location
                              </th>
                              <td style="width: 30%">
                                {{isset($arr_data['drop_location']) ? $arr_data['drop_location'] : '' }}
                              </td>
                            </tr> 

                            <tr>
                              <th style="width: 20%">Total Traveled Distance
                              </th>
                              <td style="width: 30%">
                                {{ isset($arr_data['distance']) ? number_format($arr_data['distance'],2) : '0,0' }} <strong>Miles</strong>
                              </td>
                              <th style="width: 20%"> Vehicle Owner
                              </th>
                              <td style="width: 30%">
                                {{isset($arr_data['vehicle_owner']) ? $arr_data['vehicle_owner'] : '' }}
                              </td>
                            </tr> 

                            @if(isset($arr_data['booking_status']) && $arr_data['booking_status'] == 'CANCEL_BY_USER')

                                <tr>
                                    <th style="width: 20%">Cancellation Base Price
                                    </th>
                                    <td style="width: 30%">
                                      <i class="fa fa-usd"> </i> {{ isset($arr_data['cancellation_base_price']) ? number_format($arr_data['cancellation_base_price'],2) : '0,0' }} 
                                    </td>
                                    <th style="width: 20%">
                                    </th>
                                    <td style="width: 30%">
                                    </td>
                                  </tr> 

                            @elseif(isset($arr_data['booking_status']) && $arr_data['booking_status'] == 'COMPLETED')

                                @if(isset($arr_data['is_base_price_applied']) && $arr_data['is_base_price_applied'] == 'YES')
                                    <tr>
                                      <th style="width: 20%">Base Price Applied
                                      </th>
                                      <td style="width: 30%">
                                        <span class="badge badge-success" style="width:100px">Yes</span>
                                      </td>
                                      <th style="width: 20%">Base Price for Min Miles
                                      </th>
                                      <td style="width: 30%">
                                        {{isset($arr_data['min_miles']) ? number_format($arr_data['min_miles'],2) : '0.0'}} / {{isset($arr_data['base_price_min_miles']) ? number_format($arr_data['base_price_min_miles'],2) : '0.0' }}
                                      </td>
                                    </tr> 

                                @endif
                                
                                

                                @if(isset($arr_data['is_promo_code_applied']) && $arr_data['is_promo_code_applied'] == 'YES')
                                  <tr>
                                    <th style="width: 20%">Promo Code
                                    </th>
                                    <td style="width: 30%">
                                      {{isset($arr_data['promo_code']) ? $arr_data['promo_code'] : '' }}
                                    </td>
                                    <th style="width: 20%">Promo Percentage
                                    </th>
                                    <td style="width: 30%">
                                      {{ isset($arr_data['promo_percentage']) ? number_format($arr_data['promo_percentage'],2) : '0,0' }} <strong>%</strong>
                                    </td>
                                  </tr> 
                                  <tr>
                                    <th style="width: 20%">Promo Max Amount
                                    </th>
                                    <td style="width: 30%">
                                     <i class="fa fa-usd"> </i> {{ isset($arr_data['promo_max_amount']) ? number_format($arr_data['promo_max_amount'],2) : '0,0' }}
                                    </td>
                                    <th style="width: 20%">Promo Applied Amount
                                    </th>
                                    <td style="width: 30%">
                                     <i class="fa fa-usd"> </i> {{ isset($arr_data['applied_promo_code_charge']) ? number_format($arr_data['applied_promo_code_charge'],2) : '0,0' }}
                                    </td>
                                  </tr> 
                                @endif

                                @if(isset($arr_data['is_bonus_used']) && $arr_data['is_bonus_used'] == 'YES')
                                  
                                  <tr>
                                    <th style="width: 20%">Per User Referral Points Charge
                                    </th>
                                    <td style="width: 30%">
                                        {{isset($arr_data['admin_referral_points']) ? intval($arr_data['admin_referral_points']) : '' }} <strong>Points</strong>
                                    </td>

                                    <th style="width: 20%">Per USD Bonus point Charge
                                    </th>
                                    <td style="width: 30%">
                                      {{isset($arr_data['admin_referral_points_price_per_usd']) ? intval($arr_data['admin_referral_points_price_per_usd']) : '' }} <strong>Points</strong>
                                    </td>
                                  </tr> 
                                  
                                  <tr>
                                    <th style="width: 20%">Bonus Points Used
                                    </th>
                                    <td style="width: 30%">
                                      {{ isset($arr_data['user_bonus_points']) ? intval($arr_data['user_bonus_points']) : '0' }} <strong>Points</strong>
                                    </td>
                                    <th style="width: 20%">Bonus Points Amount Applied
                                    </th>
                                    <td style="width: 30%">
                                     <i class="fa fa-usd"> </i> {{ isset($arr_data['user_bonus_points_usd_amount']) ? number_format($arr_data['user_bonus_points_usd_amount'],2) : '0,0' }}
                                    </td>

                                  </tr> 
                                @endif

                                @if(isset($arr_data['is_individual_vehicle']) && $arr_data['is_individual_vehicle'] == '1')
                                    <tr>
                                      <th style="width: 20%">Commssion % from Individual Driver
                                      </th>
                                      <td style="width: 30%">
                                        <i class="fa fa-usd"> </i> {{ isset($arr_data['individual_driver_percentage']) ? number_format($arr_data['individual_driver_percentage'],2) : '0,0' }} 
                                      </td>
                                      <th style="width: 20%"></th>
                                      <td style="width: 30%"></td>
                                    </tr> 
                                @elseif(isset($arr_data['is_individual_vehicle']) && $arr_data['is_individual_vehicle'] == '0')
                                  @if(isset($arr_data['is_company_driver']) && $arr_data['is_company_driver'] == '1')

                                    <tr>
                                      <th style="width: 20%">Commssion % from company 
                                      </th>
                                      <td style="width: 30%">
                                       {{ isset($arr_data['admin_company_percentage']) ? number_format($arr_data['admin_company_percentage'],2) : '0,0' }} <strong>%</strong>

                                      </td>
                                      <th style="width: 20%">Company Commssion % to Company Driver
                                      </th>
                                      <td style="width: 30%">
                                       {{ isset($arr_data['company_driver_percentage']) ? number_format($arr_data['company_driver_percentage'],2) : '0,0' }} <strong>%</strong> 
                                      </td>
                                    </tr> 

                                  @elseif(isset($arr_data['is_company_driver']) && $arr_data['is_company_driver'] == '0')
                                  <tr>
                                    <th style="width: 20%">Commssion % to Admin Driver
                                    </th>
                                    <td style="width: 30%">
                                    {{ isset($arr_data['admin_driver_percentage']) ? number_format($arr_data['admin_driver_percentage'],2) : '0,0' }} <strong>%</strong> 

                                    </td>
                                    <th style="width: 20%">
                                    </th>
                                    <td style="width: 30%">
                                    </td>
                                  </tr> 
                                  @endif
                                @endif


                                  <tr>
                                    <th style="width: 20%">Total Amount
                                    </th>
                                    <td style="width: 30%">
                                      <i class="fa fa-usd"> </i> {{ isset($arr_data['total_amount']) ? number_format($arr_data['total_amount'],2) : '0,0' }} 
                                    </td>
                                    <th style="width: 20%">Total Discount Amount
                                    </th>
                                    <td style="width: 30%">
                                      <?php
                                              $applied_promo_code_charge    = isset($arr_data['applied_promo_code_charge']) ? $arr_data['applied_promo_code_charge'] : 0; 
                                              $user_bonus_points_usd_amount = isset($arr_data['user_bonus_points_usd_amount']) ? $arr_data['user_bonus_points_usd_amount'] : 0; 
                                              $total_discount_amount = ($applied_promo_code_charge + $user_bonus_points_usd_amount);
                                              $total_discount_amount = number_format($total_discount_amount,2);
                                      ?>
                                      <i class="fa fa-usd"> </i> {{ isset($total_discount_amount) ? $total_discount_amount : '0,0' }} 
                                    </td>
                                  </tr> 

                                  <tr>
                                    <th style="width: 20%">User Paid Amount
                                    </th>
                                    <td style="width: 30%">
                                      <i class="fa fa-usd"> </i> {{ isset($arr_data['total_charge']) ? number_format($arr_data['total_charge'],2) : '0,0' }} 
                                    </td>
                                    <th style="width: 20%">Payment Type
                                    </th>
                                    <td style="width: 30%">
                                      Stripe 
                                    </td>
                                  </tr> 
                                  
                                  @if(isset($arr_data['is_individual_vehicle']) && $arr_data['is_individual_vehicle'] == '1')
                                      
                                      <tr>
                                        <th style="width: 20%">Admin Commission Amount
                                          </th>
                                          <td style="width: 30%">
                                            <i class="fa fa-usd"> </i> {{ isset($arr_data['admin_earning_amount']) ? number_format($arr_data['admin_earning_amount'],2) : '0,0' }}  @if(isset($arr_data['is_admin_earning_less_than_promo_value']) && $arr_data['is_admin_earning_less_than_promo_value'] == true) Promo amount deduction @endif
                                          </td>
                                          <th style="width: 30%">Driver Earning Amount
                                          </th>
                                          <td>
                                            <i class="fa fa-usd"> </i> {{ isset($arr_data['driver_earning_amount']) ? number_format($arr_data['driver_earning_amount'],2) : '0,0' }}   
                                          </td> 
                                      </tr>

                                  @elseif(isset($arr_data['is_individual_vehicle']) && $arr_data['is_individual_vehicle'] == '0')
                                      
                                      @if(isset($arr_data['is_company_driver']) && $arr_data['is_company_driver'] == '1')
                                        <tr>
                                          <th style="width: 20%">Admin Commission Amount
                                            </th>
                                            <td style="width: 30%">
                                              <i class="fa fa-usd"> </i> {{ isset($arr_data['admin_earning_amount']) ? number_format($arr_data['admin_earning_amount'],2) : '0,0' }}  @if(isset($arr_data['is_admin_earning_less_than_promo_value']) && $arr_data['is_admin_earning_less_than_promo_value'] == true) Promo amount deduction @endif
                                            </td>
                                            <th style="width: 30%"><strong>({{isset($arr_data['company_name']) ? $arr_data['company_name'] : '' }})</strong> Company Earning Amount
                                            </th>
                                            <td>
                                              <i class="fa fa-usd"> </i> {{ isset($arr_data['driver_earning_amount']) ? number_format($arr_data['driver_earning_amount'],2) : '0,0' }}   
                                            </td>
                                        </tr>
                                      @elseif(isset($arr_data['is_company_driver']) && $arr_data['is_company_driver'] == '0')
                                        <tr>
                                          <th style="width: 20%">Admin Earning Amount
                                            </th>
                                            <td style="width: 30%">
                                              <i class="fa fa-usd"> </i> {{ isset($arr_data['admin_earning_amount']) ? number_format($arr_data['admin_earning_amount'],2) : '0,0' }}
                                            </td>
                                            <th style="width: 30%">Driver Commission Amount
                                            </th>
                                            <td>
                                              <i class="fa fa-usd"> </i> {{ isset($arr_data['driver_earning_amount']) ? number_format($arr_data['driver_earning_amount'],2) : '0,0' }}
                                            </td>
                                        </tr>
                                      @endif

                                  @endif

                            @endif 

                             <tr>
                              <th style="width: 20%">Ride Status
                              </th>
                              <td style="width: 30%">
                                @if(isset($arr_data['booking_status']) &&  $arr_data['booking_status'] == 'COMPLETED')
                                  <span class='badge badge-success' style="width:115px" >Ride Completed</span>
                                @elseif(isset($arr_data['booking_status']) &&  $arr_data['booking_status'] == 'CANCEL_BY_USER')
                                <span class='badge badge-important' style="width:115px">Canceled by User</span>
                                @elseif(isset($arr_data['booking_status']) &&  $arr_data['booking_status'] == 'CANCEL_BY_DRIVER')
                                <span class='badge badge-important' style="width:128px">Canceled by Driver</span>
                                @endif
                              </td>
                              <th style="width: 20%">Payment Status
                              </th>
                              <td style="width: 30%">
                                @if(isset($arr_data['payment_status']) &&  $arr_data['payment_status'] == 'SUCCESS')
                                  <span class='badge badge-success' style="width:150px" >Payment Completed</span>
                                @elseif(isset($arr_data['payment_status']) &&  $arr_data['payment_status'] == 'FAILED')
                                <span class='badge badge-important' style="width:150px">Payment Failed</span>
                                @elseif(isset($arr_data['payment_status']) &&  $arr_data['payment_status'] == 'PENDING')
                                <span class='badge badge-warning' style="width:150px">Payment Pending</span>
                                @endif
                              </td>
                            </tr> 
                            
                             <tr>
                              <th style="width: 20%">Start Trip Image
                              </th>
                              <td style="width: 30%">
                                @if(isset($arr_bookings['start_trip_image']) && $arr_bookings['start_trip_image']!='')
                                    @if(file_exists($load_post_img_base_path.$arr_bookings['start_trip_image']))
                                        <a target="_blank" href="{{$load_post_img_public_path.$arr_bookings['start_trip_image']}}"><img style="height: 120px" src="{{$load_post_img_public_path.$arr_bookings['start_trip_image']}}"></a>
                                    @else
                                      -
                                    @endif

                                @else
                                  -
                                @endif
                              </td>
                              <th style="width: 20%">End Trip Image
                              </th>
                              <td style="width: 30%">
                                 @if(isset($arr_bookings['end_trip_image']) && $arr_bookings['end_trip_image']!='')
                                    @if(file_exists($load_post_img_base_path.$arr_bookings['end_trip_image']))
                                        <a target="_blank" href="{{$load_post_img_public_path.$arr_bookings['end_trip_image']}}"><img style="height: 120px" src="{{$load_post_img_public_path.$arr_bookings['end_trip_image']}}"></a>
                                    @else
                                      -
                                    @endif

                                @else
                                  -
                                @endif
                              </td>
                            </tr> 
                            

                    </tbody>
                  </table>

                    <div id="dvMap" style="width: 1550px; height: 600px">
                    @if(isset($arr_data['map_image']) && $arr_data['map_image']!='')
                    <center><img style="width: 1200px; height: 500px" src="{{$arr_data['map_image']}}"></center>
                    @endif
                  </div>
                  <br> 
                  
                  @if(isset($previous_page) && $previous_page == 'booking_summary')
                    <center><a class="btn" href="{{ url('/admin/booking_summary') }}">Back</a></center>
                  @elseif(isset($previous_page) && $previous_page == 'booking_history')
                    <center><a class="btn" href="{{ url($module_url_path.'/booking_history') }}">Back</a></center>
                  @else
                    <center><a class="btn" href="{{ url($module_url_path) }}">Back</a></center>
                  @endif

                {{-- </div>  --}}

              </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  
  <script type="text/javascript" src="{{url('/node_apps/public')}}/socket.io.js"></script>
  <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key={{ isset($google_map_api_key) ? $google_map_api_key : 'AIzaSyCccvQtzVx4aAt05YnfzJDSWEzPiVnNVsY' }}"></script>
  <script src="https://code.jquery.com/jquery-1.11.1.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/marker-animate-unobtrusive/0.2.8/vendor/markerAnimate.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/marker-animate-unobtrusive/0.2.8/SlidingMarker.min.js"></script>

  <input type="hidden" id="arr_coordinates" name="arr_coordinates" value="{{isset($arr_data['coordinates']) ? $arr_data['coordinates'] :''}}">

  <script src="{{url('/node_assets')}}/jqueryeasing.js"></script>

  <script type="text/javascript">

    var BASE_URL          = '{{url('/')}}';

    var pickup_location = '{{isset($arr_data['$pickup_location']) ? $arr_data['$pickup_location'] :''}}';
    var pickup_lat      = '{{isset($arr_data['$pickup_lat']) ? $arr_data['$pickup_lat'] :''}}';
    var pickup_lng      = '{{isset($arr_data['$pickup_lng']) ? $arr_data['$pickup_lat'] :'' }}';
    var drop_location   = '{{isset($arr_data['$drop_location']) ? $arr_data['$drop_location'] :'' }}';
    var drop_lat        = '{{isset($arr_data['$drop_lat']) ? $arr_data['$drop_lat'] :'' }}';
    var drop_lng        = '{{isset($arr_data['$drop_lng']) ? $arr_data['$drop_lng'] :'' }}';

    var arr_tmp_coordinates = $('#arr_coordinates').val();
    var arr_coordinates = [];

    

    if(arr_tmp_coordinates!=''){
        arr_coordinates = jQuery.parseJSON(arr_tmp_coordinates);
    }
 
    $( document ).ready(function() {
        LoadMap();
    });

    var map;
    var bounds;
    
    function LoadMap() {
        var mapOptions = {
            center: new google.maps.LatLng(pickup_lat, pickup_lng),
            zoom: 10,
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            scrollwheel: false
        };
        map = new google.maps.Map(document.getElementById("dvMap"), mapOptions);
        bounds = new google.maps.LatLngBounds();
        SetMarker();
    };

    var marker;

    function SetMarker() {

        var sourceLatlng; 
        var destinationLatlng; 
        

        if(arr_coordinates.length>0){
            arr_coordinates.forEach(function(value,index){
                if(value!=undefined){
                  if(index == 0){
                    sourceLatlng = new google.maps.LatLng(parseFloat(value.lat), parseFloat(value.lng));
                    source_marker = new SlidingMarker({
                                      position: sourceLatlng,
                                      map: map,
                                      title : pickup_location,
                                      icon: BASE_URL+'/node_assets/images/pointer.png'
                                  });

                    bounds.extend(sourceLatlng);

                  }
                  if(index == (arr_coordinates.length - 1)){
                    destinationLatlng = new google.maps.LatLng(parseFloat(value.lat), parseFloat(value.lng));
                    destination_marker = new SlidingMarker({
                                      position: destinationLatlng,
                                      map: map,
                                      title : drop_location,
                                      icon: BASE_URL+'/node_assets/images/pointer.png'
                                  });
                    bounds.extend(destinationLatlng);

                  }
                }
            })
        }
        map.fitBounds(bounds);
        LoadMapRoute();
    };

    function LoadMapRoute(){

        var arr_coordinates_data = [];
        if(arr_coordinates.length>0){
            arr_coordinates.forEach(function(value,index){
                if(value!=undefined){
                    var obj_tmp = {"lat": parseFloat(value.lat), "lng": parseFloat(value.lng)};
                    arr_coordinates_data.push(obj_tmp);
                }
            })
        }
        map.fitBounds(bounds);
        var flightPath = new google.maps.Polyline({
          path: arr_coordinates_data,
          geodesic: true,
          strokeColor: '#3466c0',
          strokeOpacity: 3.0,
          strokeWeight: 6
        });

        flightPath.setMap(map);
    }
    </script>
@endsection