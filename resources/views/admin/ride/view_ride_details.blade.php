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
      <i class="fa fa-users faa-vertical animated-hover">
      </i>
      
      <a href="{{ url($module_url_path) }}" class="call_loader">{{ $module_title or ''}}
      </a>
    </span>


    </li> &nbsp
               {{--  <i class="fa fa-angle-right"></i>
                <i class="fa fa-money"></i>                
            <a href="{{ $module_url_path.'/deposit_receipt/' }}" class="call_loader">{{ $module_title or ''}} {{ isset($module_title_receipt)?$module_title_receipt:"" }} </a>
            <li class="active"></li> --}}


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
              //Driver details
            $id                 = isset($arr_data['id']) ?$arr_data['id']:"";
            $driver_first_name  = isset($arr_data['driver_details']['first_name']) ?$arr_data['driver_details']['first_name']:"";
            $driver_last_name   = isset($arr_data['driver_details']['last_name']) ?$arr_data['driver_details']['last_name']:"";
            $driver_name        = $driver_first_name.' '.$driver_last_name;

            //Rider details
            $rider_first_name   = isset($arr_data['rider_details']['first_name']) ?$arr_data['rider_details']['first_name']:"";
            $rider_last_name    = isset($arr_data['rider_details']['last_name']) ?$arr_data['rider_details']['last_name']:"";
            $rider_name         = $rider_first_name.' '.$rider_last_name;

            //Vehicle details 
            $vehicle_name       = isset($arr_data['vehicle_details']['vehicle_name']) ?$arr_data['vehicle_details']['vehicle_name']:"";
            $vehicle_number     = isset($arr_data['vehicle_details']['vehicle_number']) ?$arr_data['vehicle_details']['vehicle_number']:"";
            $vehicle_model_name = isset($arr_data['vehicle_details']['vehicle_model_name']) ?$arr_data['vehicle_details']['vehicle_model_name']:"";

            //Ride payment Details
            $charge              = isset($arr_data['charge']) ? $arr_data['charge']:"";
            $payment_status     = isset($arr_data['payment_status']) ? $arr_data['payment_status']:"";
            $pick_up_location   = isset($arr_data['pick_up_location']) ? $arr_data['pick_up_location']:"";
            $drop_location   =    isset($arr_data['drop_location']) ? $arr_data['drop_location']:"";
            
        ?> 
        
        <div class="box">
          <div class="box-content studt-padding">
            <div class="row">
                <div class="col-md-8">
                <h3>{{$module_title or ''}} Details</h3>
                <br>
                    <table class="table table-bordered">
                      <tbody>
                            <tr>
                              <th style="width: 30%">Driver Name
                              </th>
                              <td>
                                {{$driver_name or '' }}
                              </td>
                            </tr> 

                            <tr>
                              <th style="width: 30%"> Rider Name
                              </th>
                              <td>
                                {{$rider_name or ''}}
                              </td>
                            </tr>
                            
                            <tr>
                              <th style="width: 30%"> Vehicle Name
                              </th>
                              <td>
                                {{$vehicle_name or ''}}
                              </td>
                            </tr>

                            <tr>
                              <th style="width: 30%"> Vehicle Model Name
                              </th>
                              <td>
                                {{$vehicle_model_name or ''}}
                              </td>
                            </tr>

                            <tr>
                              <th style="width: 30%"> Vehicle Number
                              </th>
                              <td>
                                {{$vehicle_number or ''}}
                              </td>
                            </tr>

                            <tr>
                              <th style="width: 30%"> Payment Status
                              </th>
                              <td>
                                {{$payment_status or ''}}
                              </td>
                            </tr>

                            <tr >
                              <th style="width: 30%"> Pick up location
                              </th>
                              <td >
                                {{$pick_up_location or ''}}
                              </td>
                            </tr>

                            <tr>
                              <th style="width: 30%"> Drop up location
                              </th>
                              <td>
                                {{$drop_location or ''}}
                              </td>
                            </tr>

                    </tbody>
                  </table>  
                  <center><a class="btn" href="{{ $module_url_path}}">Back</a>
                  </center>
                </div> 

              </div>

          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- END Main Content --> 
  @endsection
