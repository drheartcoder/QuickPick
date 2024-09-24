 @extends('front.layout.master')                

    @section('main_content')

    <?php $user_path     = config('app.project.role_slug.driver_role_slug'); ?>

    <div class="blank-div"></div>
  <div class="email-block">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6 col-md-6 col-lg-6">
                    <div class="headding-text-bredcr">
                       My Jobs
                    </div>
                </div>
                <div class="col-sm-6 col-md-6 col-lg-6">
                    <div class="bredcrum-right">
                       <a href="{{ $module_url_path }}" class="bredcrum-home"> Dashboard </a>
                        <span class="arrow-righ"><i class="fa fa-angle-right"></i></span>
                        My Jobs
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--dashboard page start-->
    <div class="main-wrapper">
        <div class="container-fluid">
            <div class="row">
                @include('front.driver.left_bar')
                <div class="col-sm-9 col-md-10 col-lg-10">
                   <div class="my-booking-ste-wrapper">
                    <div class="my-booking-dummy-tabb-block second">

                      <a class="<?php  if( isset($trip_type) && $trip_type == 'COMPLETED'){ echo 'active'; } ?>" href="{{ url('/').'/'.$user_path.'/my_job?trip_type=COMPLETED'}}">Completed</a>
                      
                      <a class="<?php  if(isset($trip_type) && $trip_type == 'CANCELED'){ echo 'active'; } ?>" href="{{ url('/').'/'.$user_path.'/my_job?trip_type=CANCELED'}}">Cancelled</a>
                      <div class="clearfix"></div>
                    </div>
                    
                    @include('front.layout._operation_status')

                    @if(isset($arr_data['data']) && sizeof($arr_data['data'])>0)
                        
                        @foreach($arr_data['data'] as $key => $value)
                            
                            <?php
                                    $redirect_url = "javascript:void(0)";

                                    if( isset($trip_type) && $trip_type == 'COMPLETED')
                                    {
                                        $redirect_url = url('/').'/'.$user_path.'/job_details?booking_id='.base64_encode($value['id']);
                                    }
                                    else
                                    {
                                        if(isset($value['type']) && $value['type'] == 'normal_booking')
                                           $redirect_url = url('/').'/'.$user_path.'/job_details?booking_id='.base64_encode($value['id']);
                                        elseif(isset($value['type']) && $value['type'] == 'load_post')
                                           $redirect_url = url('/').'/'.$user_path.'/load_post_details?load_post_request_id='.base64_encode($value['id']).'&type=canceled';
                                    }
                                    
                            ?>
                            <div class="rating-white-block my-job booking-list">
                            <span class="booking-box-click" onclick="window.location.href='{{$redirect_url}}'"> </span>
                                <div class="row">
                                    <div class="col-sm-10 col-md-10 col-lg-10">
                                        <div class="review-profile-image">
                                            <img src="{{ isset($value['profile_image']) ? $value['profile_image'] : url('/uploads/default-profile.png') }}" alt="" />

                                        </div>
                                        <div class="review-content-block">
                                            <div class="review-send-head">
                                                  <a href="javascript:void(0)"> {{isset($value['first_name']) ? $value['first_name'] : ''}} {{isset($value['last_name']) ? $value['last_name'] : ''}}</a>
                                            </div>
                                            <div class="review-send-head-small-date"><i class="fa fa-calendar"></i> {{ isset($value['booking_date']) ? $value['booking_date'] : '' }}</div>
                                            <div class="my-job-address-block">
                                                <div class="my-job-address-head">
                                                    Pickup Location :
                                                </div>
                                                <div class="my-job-address-right">
                                                     {{isset($value['pickup_location']) ? $value['pickup_location'] : ''}}
                                                </div>
                                                <div class="clearfix"></div>
                                            </div>
                                            
                                            <div class="my-job-address-block">
                                                <div class="my-job-address-head">
                                                    Drop Location :
                                                </div>
                                                <div class="my-job-address-right">
                                                    {{isset($value['drop_location']) ? $value['drop_location'] : ''}}
                                                </div>
                                                <div class="clearfix"></div>
                                            </div>

                                            @if( isset($trip_type) &&  $trip_type == 'COMPLETED')
                                                <div class="my-job-address-block">
                                                    <div class="my-job-address-head">
                                                        Price :
                                                    </div>
                                                    <div class="my-job-address-right">
                                                        $ {{isset($value['total_charge']) ? number_format($value['total_charge'],2) : '0'}}
                                                    </div>
                                                    <div class="clearfix"></div>
                                                </div>
                                            @elseif( isset($trip_type) &&  $trip_type == 'CANCELED')
                                                @if((isset($value['type']) && $value['type'] == 'normal_booking'))
                                                    @if(isset($value['booking_status']) && ($value['booking_status'] != 'CANCEL_BY_ADMIN'))
                                                        <div class="my-job-address-block">
                                                            <div class="my-job-address-head">
                                                                Price :
                                                            </div>
                                                            <div class="my-job-address-right">
                                                                $ {{isset($value['total_charge']) ? number_format($value['total_charge'],2) : '0'}}
                                                            </div>
                                                            <div class="clearfix"></div>
                                                        </div>
                                                    @endif
                                                @endif
                                            @endif

                                        </div>
                                    </div>
                                    <div class="col-sm-2 col-md-2 col-lg-2 booking-list-but">
                                        @if(isset($value['booking_status']) && $value['booking_status'] == 'COMPLETED')
                                            <div class="my-lob-completed">Completed</div>
                                        @elseif(isset($value['booking_status']) && ($value['booking_status'] == 'CANCEL_BY_USER' || $value['booking_status'] == 'CANCEL_BY_DRIVER' || $value['booking_status'] == 'CANCEL_BY_ADMIN'))
                                            <div class="my-lob-completed cancelled">Canceled</div>
                                        @elseif(isset($value['booking_status']) && ($value['booking_status'] == 'REJECT_BY_DRIVER' || $value['booking_status'] == 'REJECT_BY_USER'))
                                            <div class="my-lob-completed cancelled">Canceled</div>
                                        @endif

                                        {{-- <a href="javascript:void(0);"><button type="button" class="green-btn chan-left">Book</button></a>
                                        <a href="javascript:void(0);"><button type="button" class="white-btn chan-left">Back</button></a> --}}

                                    </div>
                                </div>
                            </div>
                        @endforeach

                        @if(isset($arr_pagination) && sizeof($arr_pagination)>0)
                            <div class="pagtna-main-wrapp">{!! $arr_pagination !!}</div>
                        @endif
                    @else

                       <div class="no-record-block">
                         <span>No Jobs Available</span> 
                       </div>
                       
                    @endif
                   </div>
                </div>

            </div>
        </div>
    </div>
<!--dashboard page end-->

@stop