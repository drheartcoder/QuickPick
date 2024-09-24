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
                              <th style="width: 20%">Vehicle Name
                              </th>
                              <td style="width: 30%">
                                {{isset($arr_data['vehicle_name']) ? $arr_data['vehicle_name'] : '' }}
                              </td>
                            </tr> 

                            <tr>
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
                            </tr> 

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
                          @if(isset($arr_data['booking_status']) && $arr_data['booking_status']!='CANCEL_BY_ADMIN')

                              <tr>
                                
                                <th style="width: 20%">Payment Type
                                </th>
                                <td style="width: 30%">
                                 
                                    @if(isset($arr_data['payment_type']) &&  $arr_data['payment_type'] == 'STRIPE')
                                      <span class='badge badge-success' style="width:115px" >Stripe</span>
                                    @else
                                    -
                                    @endif

                                </td>

                                <th style="width: 20%">Total Amount
                                </th>
                                <td style="width: 30%">
                                  <i class="fa fa-usd"> </i> {{ isset($arr_data['total_amount']) ? number_format($arr_data['total_amount'],2) : '0,0' }} 
                                </td>

                              </tr> 

                              <tr>
                                <th style="width: 20%">User Paid Amount
                                </th>
                                <td style="width: 30%">
                                  <i class="fa fa-usd"> </i> {{ isset($arr_data['total_charge']) ? number_format($arr_data['total_charge'],2) : '0,0' }} 
                                </td>
                                 <th style="width: 20%">Admin Earning Amount
                                  </th>
                                  <td style="width: 30%">
                                    <i class="fa fa-usd"> </i> {{ isset($arr_data['admin_earning_amount']) ? number_format($arr_data['admin_earning_amount'],2) : '0,0' }}  @if(isset($arr_data['is_admin_earning_less_than_promo_value']) && $arr_data['is_admin_earning_less_than_promo_value'] == true) Promo amount deduction @endif
                                  </td>

                              </tr> 
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
                                @elseif(isset($arr_data['booking_status']) &&  $arr_data['booking_status'] == 'CANCEL_BY_ADMIN')
                                <span class='badge badge-important' style="width:128px">Canceled by Admin</span>
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
                          

                    </tbody>
                  </table>
  
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
@endsection