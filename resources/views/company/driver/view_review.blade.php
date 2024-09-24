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


<?php

    $first_name      = isset($arr_review['to_user_details']['first_name']) && ($arr_review['to_user_details']['first_name']!= '') ? $arr_review['to_user_details']['first_name']:"N/A";

    $last_name      = isset($arr_review['to_user_details']['last_name']) && ($arr_review['to_user_details']['last_name']!= '') ? $arr_review['to_user_details']['last_name']:"";
    
  ?>

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
      <i class="fa fa-user faa-vertical animated-hover">
      </i>
      
      <a href="{{url($company_panel_slug.'/driver')}}" class="call_loader">Drivers
      </a>
    </span> 
    <span class="divider">
      <i class="fa fa-angle-right">
      </i>
      <i class="fa fa-star faa-vertical animated-hover">
      </i>
      
      <a href="{{$module_url_path.'/'.base64_encode($arr_review['to_user_id'])}}" class="call_loader">{{ $module_title or ''}}
      </a>
    </span> 
    <span class="divider">
      <i class="fa fa-angle-right">
      </i>
      <i class="fa fa-eye">
      </i>
    </span> 
    <li class="active">  View
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
        <div class="box">
          <div class="box-content studt-padding">
            <div class="row">
                <div class="col-md-8">
                <h3>Review</h3>
                <br>
                    <table class="table table-bordered">
                      <tbody>
                            <tr>
                              <th style="width: 30%">User Name
                              </th>
                              <td>
                                {{$arr_review['from_user_details']['first_name'] or ''}} {{$arr_review['from_user_details']['last_name'] or ''}}
                              </td>
                            </tr> 
                            <tr>
                              <th style="width: 30%">Driver Name
                              </th>
                              <td>
                                {{ $first_name }} {{ $last_name }}
                              </td>
                            </tr> 

                            <tr>
                              <th style="width: 30%">Rating 
                              </th>
                              <td>
                                <?php
                                  $rating = isset($arr_review['rating'])?intval($arr_review['rating']):0;
                                ?>
                                @for($i=1;$i<=5;$i++)
                                    @if($i<=$rating)
                                      <span title="{{$i}}" style="color:orange" class="fa fa-star"></span>
                                    @else
                                      <span title="{{$i}}" class="fa fa-star"></span>
                                    @endif
                                @endfor
                              </td>
                            </tr>
                            
                            <tr>
                              <th style="width: 30%">Message
                              </th>
                              <td>
                                {{$arr_review['rating_msg'] or ''}}
                              </td>
                            </tr>
                            <tr>
                              <th style="width: 30%">Review Badge Image
                              </th>
                              <td>
                                @if(isset($arr_review['rating_tag_details']['review_image']) && !empty($arr_review['rating_tag_details']['review_image']))
                                  <img style="height: 100px; width: 100px;" src={{ $review_tag_public_path.$arr_review['rating_tag_details']['review_image']}} alt="" />
                                @else
                                   <img style="height: 100px; width: 100px;" src={{ url("uploads/default-review.png")}} alt="" />
                                @endif 
                                <br/> <div style="margin-left: 24px; margin-top: 7px;">{{$arr_review['rating_tag_details']['tag_name'] or ''}} </div>
                              </td>
                            </tr>
                    </tbody>
                  </table>
                  @if(isset($arr_review['to_user_id']))
                    <div align="center"><a class="btn" href="{{$module_url_path.'/'.base64_encode($arr_review['to_user_id'])}}">Back</a></div>
                  @endif  
                </div> 
              </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- END Main Content --> 
  @endsection
