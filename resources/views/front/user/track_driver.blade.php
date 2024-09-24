<?php $user_path     = config('app.project.role_slug.user_role_slug'); ?>
 @extends('front.layout.master')                

    @section('main_content')

    <div class="blank-div"></div>
     <div class="email-block">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6 col-md-6 col-lg-6">
                    <div class="headding-text-bredcr">
                       Track Driver
                    </div>
                </div>
                <div class="col-sm-6 col-md-6 col-lg-6">
                    <div class="bredcrum-right">
                        <a href="{{ $module_url_path }}" class="bredcrum-home"> Dashboard </a>
                        <span class="arrow-righ"><i class="fa fa-angle-right"></i></span>
                        Track Driver
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--dashboard page start-->
    <div class="main-wrapper">
        <div class="container-fluid">
            <div class="row">
                @include('front.user.left_bar')

                <div class="middle-bar">
                <div class="my-booking-ste-wrapper">
                @include('front.layout._operation_status')

                @if(isset($arr_data['data']) && sizeof($arr_data['data']))
                 @foreach($arr_data['data'] as $key=>$result)
                    
                    <?php
                        $booking_id = isset($result['id']) ? base64_encode($result['id']) : 0;
                        $current_url = url('/').'/'.$user_path.'/track_trip?booking_id='.$booking_id.'&redirect=track_driver';
                    ?>

                    <div class="rating-white-block my-job booking-list">
                        
                        <span class="booking-box-click" onclick="window.location.href='{{$current_url}}'"> </span>

                        <div class="row">
                            <div class="col-sm-10 col-md-10 col-lg-10">
                                <div class="review-profile-image">
                                    <img src="{{ isset($result['profile_image']) ? $result['profile_image'] : url('/uploads/default-profile.png') }}" alt="" />
                                </div>
                                <div class="review-content-block">
                                    <div class="review-send-head">
                                        <a href="javascript:void(0);"> {{isset($result['first_name']) ? $result['first_name'] : ''}} {{isset($result['last_name']) ? $result['last_name'] : ''}}
                                        </a>     
                                    </div>
                                    <div class="review-send-head-small-date"><i class="fa fa-calendar"></i> {{ isset($result['booking_date']) ? $result['booking_date'] : '' }}</div>
                                    <div class="my-job-address-block">
                                        <div class="my-job-address-head">
                                            Pickup Loacation :
                                        </div>
                                        <div class="my-job-address-right">
                                             {{ $result['pickup_location'] or ''}}
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                                    
                                    <div class="my-job-address-block">
                                        <div class="my-job-address-head">
                                            Drop Loacation:
                                        </div>
                                        <div class="my-job-address-right">
                                           {{ $result['drop_location'] or ''}}
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                                   
                                </div>
                            </div>

                            <div class="col-sm-2 col-md-2 col-lg-2 booking-list-but">
                                <div class="my-lob-completed ">ONGOING</div>

                                @if(isset($result['booking_status']) && $result['booking_status'] == 'TO_BE_PICKED')
                                    <a href="javascript:void(0);"><button type="button" data-id="{{ isset($booking_id) ? $booking_id : '0' }}" onclick="confirmCancelOngoingTrip(this);" class="green-btn chan-left">Cancel Trip</button></a>
                                @endif

                            </div>

                        </div>
                    </div>
                 @endforeach
                    <div class="pagination-block notifica">
                        @if(isset($arr_data['arr_pagination']) && sizeof($arr_data['arr_pagination']))
                            <div class="pagtna-main-wrapp">{!! $arr_data['arr_pagination'] !!}</div>
                        @endif 
                    </div>
                    @else 

                    <div class="no-record-block">
                        <span>No Bookings Available</span> 
                    </div>

                @endif


                  </div>
                </div>
                @include('front.user.right_bar')
            </div>
        </div>
    </div>

  <!-- popup section start -->
<div class="mobile-popup-wrapper">
<div id="cancellation-reason-popup" class="modal fade" role="dialog" data-backdrop="static">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal"><!--&times;--></button>
        <h4 class="modal-title">Cancellation Reason</h4>
      </div>
      <div class="modal-body">
            <div id="div_error_show"></div>
            <div class="form-group marg-top">
            <input type="hidden" name="enc_booking_id" id="enc_booking_id" value="0">
            <textarea id="reason" name="reason" placeholder="Enter Cancellation Reason"></textarea>
                <div id="err_cancellation_reason" class="error-red"></div>
            </div>
            <div class="login-btn popup"><a onclick="checkValidCancellationReason()" href="javascript:void(0)">Submit</a></div>
      </div>
    </div>

  </div>
</div>   
</div>
 <!-- popup section end --> 
 
<link rel="stylesheet" type="text/css" href="{{ url('/') }}/css/admin/sweetalert.css" />
<script src="{{ url('/') }}/js/admin/sweetalert.min.js"></script>
<script type="text/javascript" src="{{ url('/js/admin') }}/sweetalert_msg.js"></script>

<script type="text/javascript">
  
    var CANCEL_ONGOING_TRIP_URL = '{{ url('/').'/'.$user_path.'/process_cancel_trip'}}';
    var USER_BASE_URL = "{{url('/').'/'.$user_path.'/'}}";

    function confirmCancelOngoingTrip(ref){
        var booking_id = $(ref).attr('data-id');
        if(booking_id!=undefined && booking_id!=''){
            $('#enc_booking_id').val(booking_id);
            $('#cancellation-reason-popup').modal('toggle');
        }
    }
    function checkValidCancellationReason()
    {
        $('#err_cancellation_reason').html('');
        $('#div_error_show').html('');

        if($('#reason').val() == '')
        {
            $('#err_cancellation_reason').html('Please enter cancellation reason');
            return false; 
        }  
        var obj_data = new Object();

        obj_data._token     = "{{ csrf_token() }}";
        obj_data.reason     = $('#reason').val();
        obj_data.booking_id = $('#enc_booking_id').val();

        $.ajax({
            url:CANCEL_ONGOING_TRIP_URL,
            type:'POST',
            data:obj_data,
            dataType:'json',
            beforeSend:function(){
                showProcessingOverlay();
            },
            success:function(response)
            {
                hideProcessingOverlay();
                if(response.status=="success")
                {
                    $('#cancellation-reason-popup').modal('toggle');
                    swal("Success!", response.msg, "success");

                    setTimeout(function(){ 
                        window.location.href = USER_BASE_URL+'my_booking?trip_type=CANCELED';
                    }, 3000);

                    return false;
                }
                else if(response.status=="error")
                {
                    swal("Error!", response.msg, "error");
                    return false;
                }
                return false;
            },error:function(res){
                hideProcessingOverlay();
            }    
        });

        return false; 
    }
</script>
@stop