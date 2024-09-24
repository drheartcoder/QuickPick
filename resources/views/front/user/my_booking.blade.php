<?php $user_path     = config('app.project.role_slug.user_role_slug'); ?>
 @extends('front.layout.master')                

    @section('main_content')

    <div class="blank-div"></div>
     <div class="email-block">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6 col-md-6 col-lg-6">
                    <div class="headding-text-bredcr">
                       My Bookings
                    </div>
                </div>
                <div class="col-sm-6 col-md-6 col-lg-6">
                    <div class="bredcrum-right">
                        <a href="{{ $module_url_path }}" class="bredcrum-home"> Dashboard </a>
                        <span class="arrow-righ"><i class="fa fa-angle-right"></i></span>
                        My Bookings
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

                <div class="my-booking-dummy-tabb-block">
                  <a class="<?php if($trip_type == 'ONGOING'){ echo 'active'; } ?>" href="{{ url('/').'/'.$user_path.'/my_booking?trip_type=ONGOING'}}">Ongoing</a>

                  <a class="<?php if($trip_type == 'PENDING'){ echo 'active'; } ?>" href="{{ url('/').'/'.$user_path.'/my_booking?trip_type=PENDING'}}">Pending</a>

                  <a class="<?php if($trip_type == 'COMPLETED'){ echo 'active'; } ?>" href="{{ url('/').'/'.$user_path.'/my_booking?trip_type=COMPLETED'}}">Completed</a>
                  
                  <a class="<?php if($trip_type == 'CANCELED'){ echo 'active'; } ?>" href="{{ url('/').'/'.$user_path.'/my_booking?trip_type=CANCELED'}}">Cancelled</a>
                  <div class="clearfix"></div>
                </div>
                
                {{-- {{dd($arr_data['data'])}} --}}

                @if(isset($arr_data['data']) && sizeof($arr_data['data']))
                 @foreach($arr_data['data'] as $key=> $result)
                    
                    <?php
                            $canceled_url = 'javascript:void(0)';
                            if( isset($trip_type) && ($trip_type == 'CANCELED'))
                            {
                                if((isset($result['type']) && $result['type'] == 'normal_booking'))
                                {
                                    $canceled_url = url('/').'/'.$user_path.'/booking_details?booking_id='.base64_encode($result['id']);
                                }
                                else if((isset($result['type']) && $result['type'] == 'load_post'))
                                {
                                    $canceled_url = url('/').'/'.$user_path.'/pending_load_post?load_post_request_id='.base64_encode($result['id']);
                                }
                            }   
                            
                            $redirect_url = '';
                            if( isset($trip_type) && ($trip_type == 'COMPLETED'))
                                $redirect_url = url('/').'/'.$user_path.'/booking_details?booking_id='.base64_encode($result['id']);
                            elseif( isset($trip_type) && ($trip_type == 'ONGOING'))
                                $redirect_url = url('/').'/'.$user_path.'/track_trip?booking_id='.base64_encode($result['id']);
                            elseif( isset($trip_type) && ($trip_type == 'PENDING'))
                                $redirect_url = url('/').'/'.$user_path.'/pending_load_post?load_post_request_id='.base64_encode($result['id']);
                            elseif( isset($trip_type) && ($trip_type == 'CANCELED'))
                                $redirect_url =  isset($canceled_url) ? $canceled_url : 'javascript:void(0);';
                            else
                            $redirect_url = "javascript:void(0)"
                    ?>
                    <div class="rating-white-block my-job booking-list">
                        <span class="booking-box-click" onclick="window.location.href='{{$redirect_url}}'"> </span>

                        <div class="row">
                            <div class="col-sm-12 col-md-9 col-lg-9">
                                <div class="review-profile-image">
                                    <img src="{{ isset($result['profile_image']) ? $result['profile_image'] : url('/uploads/default-profile.png') }}" alt="" />
                                </div>
                                <div class="review-content-block">
                                    <div class="review-send-head">
                                        <a href="{{ $redirect_url }}"> {{isset($result['first_name']) ? $result['first_name'] : ''}} {{isset($result['last_name']) ? $result['last_name'] : ''}}</a>     
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
                                    @if( isset($trip_type) &&  $trip_type == 'COMPLETED')
                                        <div class="my-job-address-block">
                                            <div class="my-job-address-head">
                                                Price :
                                            </div>
                                            <div class="my-job-address-right">
                                                $ {{isset($result['total_charge']) ? number_format($result['total_charge'],2) : '0'}}
                                            </div>
                                            <div class="clearfix"></div>
                                        </div>
                                    @elseif( isset($trip_type) &&  $trip_type == 'CANCELED')
                                        @if((isset($result['type']) && $result['type'] == 'normal_booking'))
                                        
                                            @if(isset($result['booking_status']) && $result['booking_status'] != 'CANCEL_BY_ADMIN')

                                                <div class="my-job-address-block">
                                                    <div class="my-job-address-head">
                                                        Price :
                                                    </div>
                                                    <div class="my-job-address-right">
                                                        $ {{isset($result['total_charge']) ? number_format($result['total_charge'],2) : '0'}}
                                                    </div>
                                                    <div class="clearfix"></div>
                                                </div>
                                            @endif
                                        @endif
                                    @endif

                                </div>
                            </div>
                        <?php 
                        $color_class = '';
                        if($trip_type == 'ONGOING'){ $color_class=''; }
                        elseif($trip_type == 'PENDING'){ $color_class='pending'; }
                        elseif($trip_type == 'COMPLETED'){ $color_class='completed'; }
                        elseif($trip_type == 'CANCELED'){ $color_class='cancelled'; }

                         ?>
                            <div class="col-sm-12 col-md-3 col-lg-3 booking-list-but">
                                <div class="my-lob-completed {{ $color_class }}">{{ $trip_type or '' }}</div><div class="clearfix clr-m"></div>
                                @if(isset($trip_type) && $trip_type == 'ONGOING')
                                    
                                    @if(isset($result['booking_status']) && $result['booking_status'] == 'TO_BE_PICKED')
                                        <a href="javascript:void(0);"><button type="button" data-id="{{ isset($result['id']) ? base64_encode($result['id']) : 0 }}" onclick="confirmCancelOngoingTrip(this);" class="green-btn chan-left">Cancel Trip</button></a>
                                    @endif

                                @else
                                    @if(isset($result['request_status']) && ($result['request_status'] == 'NEW_REQUEST' || $result['request_status'] == 'USER_REQUEST'))
                                        @if(isset($result['request_status']) && $result['request_status'] == 'NEW_REQUEST')
                                            
                                            <?php
                                                    $select_driver_url = 'javascript:void(0);';
                                                    if(isset($result['id']) && $result['id']!=0)
                                                    {
                                                        $load_post_request_id = base64_encode($result['id']);
                                                        $select_driver_url = url('/').'/'.$user_path.'/book_driver_request?load_post_request_id='.$load_post_request_id;
                                                    }
                                            ?>

                                            <a href="{{ isset($select_driver_url) ? $select_driver_url : 'javascript:void(0);' }}"><button type="button" class="green-btn chan-left">Select Driver</button></a>
                                            <a href="javascript:void(0);"><button type="button" data-id="{{ isset($result['id']) ? base64_encode($result['id']) : 0 }}" onclick="confirmCancelTrip(this);" class="white-btn chan-left">Cancel Trip</button></a>
                                        @elseif(isset($result['request_status']) && $result['request_status'] == 'USER_REQUEST')
                                            <a href="javascript:void(0);"><button type="button" data-id="{{ isset($result['id']) ? base64_encode($result['id']) : 0 }}" onclick="confirmCancelTrip(this);" class="green-btn chan-left">Cancel Trip</button></a>
                                        @endif
                                    @else
                                        
                                        @if(isset($result['request_status']) && 
                                                    (
                                                        $result['request_status'] == 'ACCEPT_BY_DRIVER'||
                                                        $result['request_status'] == 'REJECT_BY_DRIVER'||
                                                        $result['request_status'] == 'REJECT_BY_USER'||
                                                        $result['request_status'] == 'TIMEOUT'
                                                    ))
                                            @if($result['request_status'] == 'ACCEPT_BY_DRIVER')
                                                <a href="javascript:void(0);"><button 
                                                                                        type="button" 
                                                                                        data-id="{{ isset($result['id']) ? base64_encode($result['id']) : 0 }}" 
                                                                                        data-driver-id="{{ isset($result['driver_id']) ? base64_encode($result['driver_id']) : 0 }}" 
                                                                                        onclick="confirmAcceptDriver(this);" class="green-btn chan-left">Accept Driver</button></a>   
                                                
                                                <a href="javascript:void(0);"><button type="button" 
                                                                                      data-id="{{ isset($result['id']) ? base64_encode($result['id']) : 0 }}" 
                                                                                      data-driver-id="{{ isset($result['driver_id']) ? base64_encode($result['driver_id']) : 0 }}" 
                                                                                      onclick="confirmRejectDriver(this);" class="white-btn chan-left">Reject Driver</button></a>

                                            @else
                                                <a href="javascript:void(0);"><button type="button" data-id="{{ isset($result['id']) ? base64_encode($result['id']) : 0 }}" onclick="confirmCancelTrip(this);" class="green-btn chan-left">Cancel Trip</button></a>
                                            @endif
                                        @endif
                                    @endif
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
<div id="accept-driver-popup" class="modal fade" role="dialog" data-backdrop="static">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal"><!--&times;--></button>
        <h4 class="modal-title">Receiver Details</h4>
      </div>
      <div id="show_error_div"></div>
      <div class="modal-body">
            <form id="frm_accept_driver_details" data-parsley-validate>
                {{ csrf_field() }}
                <input type="hidden" name="load_post_request_id" id="load_post_request_id" value="0">
                <input type="hidden" name="driver_id" id="driver_id" value="0">
                <div class="form-group marg-top">
                    <input type="text" id="po_no" name="po_no" placeholder="Enter PO Number"/>
                </div>
                
                <div class="form-group marg-top">
                    <input type="text" id="receiver_name" name="receiver_name" placeholder="Enter Receiver Name" data-parsley-required-message="Please enter receiver name" data-parsley-required="true" data-parsley-errors-container="#err_receiver_name"/>
                    <div id="err_receiver_name" class="error-red"></div>
                </div>

                <div class="form-group marg-top">
                    <input type="text" id="receiver_no" name="receiver_no" placeholder="Enter Receiver Number" data-parsley-required-message="Please enter receiver number" data-parsley-required="true" data-parsley-errors-container="#err_receiver_number" data-parsley-minlength="8" data-parsley-maxlength="12" onkeypress="return isNumberKey(event)"/>
                    <div id="err_receiver_number" class="error-red"></div>
                </div>
                
                <div class="form-group marg-top">
                    <input type="text" id="app_suite" name="app_suite" placeholder="Enter Apt/Suite/Unit"/>
                </div>                

                <div class="login-btn popup"><a onclick="checkValidReceiverDetails(this)" href="javascript:void(0)">Submit</a></div>
            </form>
      </div>
    </div>

  </div>
</div>   
</div>
 <!-- popup section end -->


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

<script type="text/javascript">
  
    var USER_PANEL_URL    = "{{url('/').'/'.$user_path}}";
    var cancel_trip_url   = '{{ url('/').'/'.$user_path.'/cancel_pending_load_post'}}';
    var accept_driver_url = '{{ url('/').'/'.$user_path.'/accept_load_post'}}';
    var reject_driver_url = '{{ url('/').'/'.$user_path.'/reject_load_post'}}';

    var CANCEL_ONGOING_TRIP_URL = '{{ url('/').'/'.$user_path.'/process_cancel_trip'}}';
    var USER_BASE_URL = "{{url('/').'/'.$user_path.'/'}}";

    function isNumberKey(evt) {
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        
        if (charCode == 190 || charCode == 46 ) 
          return true;
        
        if (charCode > 31 && (charCode < 48 || charCode > 57 )) 
        return false;
        
        return true;
    }
    
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

    function confirmAcceptDriver(ref)
    {
        var driver_id            = $(ref).attr('data-driver-id');
        var load_post_request_id = $(ref).attr('data-id');

        if((load_post_request_id!=undefined && driver_id!=undefined) && (load_post_request_id!='' && driver_id!='')){

            $('#load_post_request_id').val(load_post_request_id);
            $('#driver_id').val(driver_id);
            $('#accept-driver-popup').modal('toggle');
        }
    }
    function confirmRejectDriver(ref)
    {
        var driver_id            = $(ref).attr('data-driver-id');
        var load_post_request_id = $(ref).attr('data-id');

        if((load_post_request_id!=undefined && driver_id!=undefined) && (load_post_request_id!='' && driver_id!='')){

            swal({
              title: "Are you sure ?",
              text: 'You want to reject driver.',
              type: "warning",
              showCancelButton: true,
              confirmButtonColor: "#DD6B55",
              confirmButtonText: "Yes",
              cancelButtonText: "No",
              closeOnConfirm: false,
              closeOnCancel: true
            },
            function(isConfirm)
            {
              if(isConfirm==true)
              {
                reject_driver_url = reject_driver_url+'?load_post_request_id='+load_post_request_id+'&driver_id='+driver_id;
                window.location = reject_driver_url;
              }
            });
        }   
    }
    function checkValidReceiverDetails(ref)
    {
        var is_valid_frm  = $('#frm_accept_driver_details').parsley().validate();
        if(is_valid_frm == false)
        {
            return false;
        }

        $.ajax({
            url:accept_driver_url,
            type:'POST',
            data : $('#frm_accept_driver_details').serialize(),
            beforeSend:function(){
              $(ref).prop('disabled', true);
              $(ref).html("<i class='fa fa-spinner fa-spin'></i>");
              showProcessingOverlay();
            },
            success:function(response)
            {
                if(response.status=="success")
                { 
                    if(response.data.booking_master_id!=undefined && response.data.booking_master_id!=''){
                      var booking_id = response.data.booking_master_id;
                      var redirect_url = USER_PANEL_URL+'/track_trip?booking_id='+booking_id+'&redirect=track_driver';
                      window.location.href = redirect_url;
                    }
                }
                if(response.status=="error")
                {
                    hideProcessingOverlay();
                    $(ref).html("Submit");
                    $(ref).prop('disabled', false);
                    var error_html = '';
                    error_html += '<div class="alert alert-danger">';
                    error_html +=      ''+response.msg+'';
                    error_html +=  '</div>';
                    $('#show_error_div').html(error_html);
                }
                return false;
            }
        });
    }
    function confirmCancelTrip(ref) 
    {
        var load_post_request_id = $(ref).attr('data-id');
        if(load_post_request_id!=undefined && load_post_request_id!=''){
            swal({
              title: "Are you sure ?",
              text: 'You want to cancel current trip.',
              type: "warning",
              showCancelButton: true,
              confirmButtonColor: "#DD6B55",
              confirmButtonText: "Yes",
              cancelButtonText: "No",
              closeOnConfirm: false,
              closeOnCancel: true
            },
            function(isConfirm)
            {
              if(isConfirm==true)
              {
                cancel_trip_url = cancel_trip_url+'?load_post_request_id='+load_post_request_id;
                window.location = cancel_trip_url;
              }
            });
        }
    }
</script>
@stop