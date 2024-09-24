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
      <i class="fa fa-users faa-vertical animated-hover">
      </i>
      
      <a href="{{ url($module_url_path) }}" class="call_loader">{{ $module_title or ''}}
      </a>
    </span>


    </li> &nbsp
                <i class="fa fa-angle-right"></i>
                <i class="fa fa-money"></i>                
            <a href="{{ $module_url_path.'/deposit_receipt/'.$driver_id }}" class="call_loader">{{ $module_title or ''}} {{ isset($module_title_receipt)?$module_title_receipt:"" }} </a>
            <li class="active"></li>


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
            $driver_id          = isset($arr_data['driver_details']['id']) ?$arr_data['driver_details']['id']:"";
            $first_name         = isset($arr_data['driver_details']['first_name']) ?$arr_data['driver_details']['first_name']:"";
            $last_name          = isset($arr_data['driver_details']['last_name']) ?$arr_data['driver_details']['last_name']:"";
            $name               = $first_name.' '.$last_name;
            $contact_number     = isset($arr_data['driver_details']['mobile_no']) ?$arr_data['driver_details']['mobile_no']:"";
            $email              = isset($arr_data['driver_details']['email']) ?$arr_data['driver_details']['email']:"";

            //Receipt Details
            $amount             = isset($arr_data['amount']) ? $arr_data['amount']:"";
            $receipt_image_path = isset($receipt_image_path) ? $receipt_image_path:"";
            $receipt_image      = isset($arr_data['receipt_image']) ? $arr_data['receipt_image']:"";
        ?>
        
        <div class="box">
          <div class="box-content studt-padding">
            <div class="row">
                <div class="col-md-8">
                <h3>{{$module_title or ''}} Deposit Receipt Details</h3>
                <br>
                    <table class="table table-bordered">
                      <tbody>
                            <tr>
                              <th style="width: 30%">Driver Name
                              </th>
                              <td>
                                {{$name or '' }}
                              </td>
                            </tr> 

                            <tr>
                              <th style="width: 30%"> Email
                              </th>
                              <td>
                                {{$email or ''}}
                              </td>
                            </tr>
                            
                            <tr>
                              <th style="width: 30%">Mobile Number
                              </th>
                              <td>
                                {{$contact_number or ''}}
                              </td>
                            </tr>

                            <tr>
                              <th style="width: 30%">Receipt Amount
                              </th>
                              <td>
                                {{$amount or ''}}
                              </td>
                            </tr>
                             <tr>
                              <th style="width: 30%">Receipt Image
                              </th>
                              <td>
                                <img src="{{$receipt_image_path.$receipt_image}}" height="100" width="150" />
                              </td>
                            </tr>


                    </tbody>
                  </table>  
                  <center><a class="btn" href="{{ $module_url_path.'/deposit_receipt/'.base64_encode($driver_id) }}">Back</a>
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
