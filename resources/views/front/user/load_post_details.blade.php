 @extends('front.layout.master')                

    @section('main_content')

    <?php $user_path     = config('app.project.role_slug.user_role_slug'); ?>

    <div class="blank-div"></div>
    <div class="email-block">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6 col-md-6 col-lg-6">
                    <div class="headding-text-bredcr">
                       My Job Details
                    </div>
                </div>
                <div class="col-sm-6 col-md-6 col-lg-6">
                    <div class="bredcrum-right">
                        <a href="{{ $module_url_path }}" class="bredcrum-home"> Dashboard </a>
                        <span class="arrow-righ"><i class="fa fa-angle-right"></i></span>
                        My Job Details
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
                <div class="col-sm-9 col-md-8 col-lg-8">
                @include('front.layout._operation_status')
                    <div class="rating-white-block my-job">
                                <div class="row">
                                    <div class="col-sm-10 col-md-10 col-lg-10">
                                        <div class="review-profile-image">
                                            <img src="{{ isset($arr_load_post_details['profile_image']) ? $arr_load_post_details['profile_image'] : url('/uploads/default-profile.png') }}" alt="" />
                                        </div>
                                        <div class="review-content-block">
                                            <div class="review-send-head">
                                               {{isset($arr_load_post_details['first_name']) ? $arr_load_post_details['first_name'] : ''}} {{isset($arr_load_post_details['last_name']) ? $arr_load_post_details['last_name'] : ''}}
                                            </div>
                                            <div class="review-send-head-small-date">
                                                    <i class="fa fa-calendar"></i> {{ isset($arr_load_post_details['booking_date']) ? $arr_load_post_details['booking_date'] : '' }}
                                                    @if(isset($arr_load_post_details['is_future_request']) && $arr_load_post_details['is_future_request'] == '1')
                                                        <i class="fa fa-clock-o"></i> {{ isset($arr_load_post_details['request_time']) ? $arr_load_post_details['request_time'] : '' }}
                                                    @endif
                                            </div>
                                            <div class="my-job-address-block">
                                                <div class="my-job-address-head">
                                                    Pickup Location :
                                                </div>
                                                <div class="my-job-address-right">
                                                     {{isset($arr_load_post_details['pickup_location']) ? $arr_load_post_details['pickup_location'] : ''}}
                                                </div>
                                                <div class="clearfix"></div>
                                            </div>
                                            
                                            <div class="my-job-address-block">
                                                <div class="my-job-address-head">
                                                    Drop Location :
                                                </div>
                                                <div class="my-job-address-right">
                                                    {{isset($arr_load_post_details['drop_location']) ? $arr_load_post_details['drop_location'] : ''}}
                                                </div>
                                                <div class="clearfix"></div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-sm-2 col-md-2 col-lg-2">
                                        @if(isset($arr_load_post_details['request_status']) && 
                                            (
                                                $arr_load_post_details['request_status'] == 'NEW_REQUEST' || 
                                                $arr_load_post_details['request_status'] == 'USER_REQUEST'||
                                                $arr_load_post_details['request_status'] == 'ACCEPT_BY_DRIVER'||
                                                $arr_load_post_details['request_status'] == 'REJECT_BY_DRIVER'||
                                                $arr_load_post_details['request_status'] == 'REJECT_BY_USER'||
                                                $arr_load_post_details['request_status'] == 'TIMEOUT'
                                            ))
                                            <div class="my-lob-completed pending">PENDING</div>
                                        @elseif(isset($arr_load_post_details['request_status']) && 
                                            (
                                                $arr_load_post_details['request_status'] == 'CANCEL_BY_ADMIN' || 
                                                $arr_load_post_details['request_status'] == 'CANCEL_BY_USER' 
                                            ))
                                            <div class="my-lob-completed cancelled">CANCELED</div>
                                        @endif
                                    </div>

                                </div>
                            </div>
                            

                    <div class="edit-posted-bg-main my-job-details">
                        <div class="my-job-details">Package Details</div>
                        <div class="first-names">
                            <span>Package Type : </span>
                            <div class="first-names-light">{{isset($arr_load_post_details['package_type']) ? $arr_load_post_details['package_type'] : ''}}</div>
                            <div class="clearfix"></div>
                        </div>
                        @if(isset($arr_load_post_details['package_type']) && $arr_load_post_details['package_type']!="PALLET")
                            <div class="first-names">
                                <span>Weight (Pounds) : </span>
                                <div class="first-names-light">{{isset($arr_load_post_details['package_weight']) ? $arr_load_post_details['package_weight'] : '0'}}</div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="first-names">
                                <span>Length (ft) : </span>
                                <div class="first-names-light">{{isset($arr_load_post_details['package_length']) ? $arr_load_post_details['package_length'] : '0'}}</div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="first-names">
                                <span>Width (ft) : </span>
                                <div class="first-names-light">{{isset($arr_load_post_details['package_breadth']) ? $arr_load_post_details['package_breadth'] : '0'}}</div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="first-names">
                                <span>Height (ft) : </span>
                                <div class="first-names-light">{{isset($arr_load_post_details['package_height']) ? $arr_load_post_details['package_height'] : ''}}</div>
                                <div class="clearfix"></div>
                            </div>
                        @endif
                        <div class="first-names">
                            <span>Quantity : </span>
                            <div class="first-names-light">{{isset($arr_load_post_details['package_quantity']) ? $arr_load_post_details['package_quantity'] : ''}}</div>
                            <div class="clearfix"></div>
                        </div>

                        <br>
                        <div class="btns-wrapper change-pass">
                        

                            @if(isset($arr_load_post_details['request_status']) && ($arr_load_post_details['request_status'] == 'NEW_REQUEST' || $arr_load_post_details['request_status'] == 'USER_REQUEST'))
                                @if(isset($arr_load_post_details['request_status']) && $arr_load_post_details['request_status'] == 'NEW_REQUEST')
                                    

                                    <?php
                                            $select_driver_url = 'javascript:void(0);';
                                            if(isset($arr_load_post_details['load_post_request_id']) && $arr_load_post_details['load_post_request_id']!=0)
                                            {
                                                $load_post_request_id = base64_encode($arr_load_post_details['load_post_request_id']);
                                                $select_driver_url = url('/').'/'.$user_path.'/book_driver_request?load_post_request_id='.$load_post_request_id;
                                            }
                                    ?>

                                    <a href="{{ isset($select_driver_url) ? $select_driver_url : 'javascript:void(0);' }}"><button type="button" class="white-btn chan-left">Select Driver</button></a>
                                @endif
                                <a href="javascript:void(0);"><button type="button" data-id="{{ isset($arr_load_post_details['load_post_request_id']) ? base64_encode($arr_load_post_details['load_post_request_id']) : 0 }}" onclick="confirmCancelTrip(this);" class="white-btn chan-left">Cancel Trip</button></a>
                                <a href="{{ url('/').'/'.$user_path.'/my_booking?trip_type=PENDING'}}"><button type="button" class="white-btn chan-left">Back</button></a>
                            @else
                                @if(isset($arr_load_post_details['request_status']) && 
                                            (
                                                $arr_load_post_details['request_status'] == 'ACCEPT_BY_DRIVER'||
                                                $arr_load_post_details['request_status'] == 'REJECT_BY_DRIVER'||
                                                $arr_load_post_details['request_status'] == 'REJECT_BY_USER'||
                                                $arr_load_post_details['request_status'] == 'TIMEOUT'
                                            ))
                                    @if($arr_load_post_details['request_status'] == 'ACCEPT_BY_DRIVER')
                                        <a href="javascript:void(0);"><button 
                                                                                type="button" 
                                                                                data-id="{{ isset($arr_load_post_details['load_post_request_id']) ? base64_encode($arr_load_post_details['load_post_request_id']) : 0 }}" 
                                                                                data-driver-id="{{ isset($arr_load_post_details['driver_id']) ? base64_encode($arr_load_post_details['driver_id']) : 0 }}" 
                                                                                onclick="confirmAcceptDriver(this);" class="white-btn chan-left">Accept Driver</button></a>   
                                        
                                        <a href="javascript:void(0);"><button type="button" 
                                                                              data-id="{{ isset($arr_load_post_details['load_post_request_id']) ? base64_encode($arr_load_post_details['load_post_request_id']) : 0 }}" 
                                                                              data-driver-id="{{ isset($arr_load_post_details['driver_id']) ? base64_encode($arr_load_post_details['driver_id']) : 0 }}" 
                                                                              onclick="confirmRejectDriver(this);" class="white-btn chan-left">Reject Driver</button></a>

                                    @else
                                        <a href="javascript:void(0);"><button type="button" data-id="{{ isset($result['load_post_request_id']) ? base64_encode($result['load_post_request_id']) : 0 }}" onclick="confirmCancelTrip(this);" class="green-btn chan-left">Cancel Trip</button></a>
                                    @endif
                                    <a href="{{ url('/').'/'.$user_path.'/my_booking?trip_type=PENDING'}}"><button type="button" class="white-btn chan-left">Back</button></a>
                                @elseif(isset($arr_load_post_details['request_status']) && 
                                            (
                                                $arr_load_post_details['request_status'] == 'CANCEL_BY_ADMIN'||
                                                $arr_load_post_details['request_status'] == 'CANCEL_BY_USER'
                                            ))
                                    <a href="{{ url('/').'/'.$user_path.'/my_booking?trip_type=CANCELED'}}"><button type="button" class="white-btn chan-left">Back</button></a>
                                @else
                                    <a href="javascript:void(0);"><button type="button" class="white-btn chan-left">Back</button></a>
                                @endif
                            @endif
                            
                        </div>


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

<link rel="stylesheet" type="text/css" href="{{ url('/') }}/css/admin/sweetalert.css" />
<script src="{{ url('/') }}/js/admin/sweetalert.min.js"></script>
<script type="text/javascript" src="{{ url('/js/admin') }}/sweetalert_msg.js"></script>

<script type="text/javascript">
  
    var USER_PANEL_URL    = "{{url('/').'/'.$user_path}}";
    var cancel_trip_url   = '{{ url('/').'/'.$user_path.'/cancel_pending_load_post'}}';
    var accept_driver_url = '{{ url('/').'/'.$user_path.'/accept_load_post'}}';
    var reject_driver_url = '{{ url('/').'/'.$user_path.'/reject_load_post'}}';

    function isNumberKey(evt) {
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        
        if (charCode == 190 || charCode == 46 ) 
          return true;
        
        if (charCode > 31 && (charCode < 48 || charCode > 57 )) 
        return false;
        
        return true;
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