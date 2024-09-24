<?php 
    $user_path     = config('app.project.role_slug.user_role_slug'); 

    $arr_right_bar_details = get_right_bar_trip_list();
    
    $arr_pending_trips = $arr_ongoing_trips = [];

    if(isset($arr_right_bar_details['arr_pending_trips']) && sizeof($arr_right_bar_details['arr_pending_trips'])>0)
    {
        $arr_pending_trips = $arr_right_bar_details['arr_pending_trips'];
    }

    if(isset($arr_right_bar_details['arr_ongoing_trips']) && sizeof($arr_right_bar_details['arr_ongoing_trips'])>0)
    {
        $arr_ongoing_trips = $arr_right_bar_details['arr_ongoing_trips'];
    }
?>

<div class="right-bar-min">
  <div class="right-bar">
    
   <div class="right-bar-title">Pending</div>
    <div id="pending-scrollbar" class="content right-content-scrollbar">
      @if(isset($arr_pending_trips) && sizeof($arr_pending_trips)>0)
        @foreach($arr_pending_trips as $pending_key => $pending_value)
          <?php
                $enc_id = isset($pending_value['id']) ? $pending_value['id'] : '0';
                $redirect_url = url('/').'/'.$user_path.'/pending_load_post?load_post_request_id='.base64_encode($enc_id);
          ?>
          <div class="right-content-bar">
            <span class="booking-box-click" onclick="window.location.href='{{$redirect_url}}'"> </span>
            <h2>{{isset($pending_value['first_name']) ? $pending_value['first_name'] : ''}} {{isset($pending_value['last_name']) ? $pending_value['last_name'] : ''}}</h2>
            <span class="date-bar"> {{ isset($pending_value['booking_date']) ? $pending_value['booking_date'] : '' }}</span>
            <p><span>Pickup Loacation : </span>{{ isset($pending_value['pickup_location']) ? $pending_value['pickup_location'] : '' }}</p>
            <p><span>Drop Loacation: </span>{{ isset($pending_value['drop_location']) ? $pending_value['drop_location'] : '' }}</p>
            <div class="right-content-but">
              @if(isset($pending_value['request_status']) && ($pending_value['request_status'] == 'USER_REQUEST'))
                 <button type="button" data-id="{{ isset($pending_value['id']) ? base64_encode($pending_value['id']) : 0 }}" onclick="confirmRightBarCancelTrip(this);" class="green-btn chan-left">Cancel Trip</button>
              @else
                  
                  @if(isset($pending_value['request_status']) && 
                              (
                                  $pending_value['request_status'] == 'ACCEPT_BY_DRIVER'||
                                  $pending_value['request_status'] == 'REJECT_BY_DRIVER'||
                                  $pending_value['request_status'] == 'REJECT_BY_USER'||
                                  $pending_value['request_status'] == 'TIMEOUT'
                              ))
                      @if($pending_value['request_status'] == 'ACCEPT_BY_DRIVER')
                          <button 
                                type="button" 
                                data-id="{{ isset($pending_value['id']) ? base64_encode($pending_value['id']) : 0 }}" 
                                data-driver-id="{{ isset($pending_value['driver_id']) ? base64_encode($pending_value['driver_id']) : 0 }}" 
                                onclick="confirmRightBarAcceptDriver(this);" class="green-btn chan-left">Accept Driver</button>   
                          
                          <button type="button" 
                                data-id="{{ isset($pending_value['id']) ? base64_encode($pending_value['id']) : 0 }}" 
                                data-driver-id="{{ isset($pending_value['driver_id']) ? base64_encode($pending_value['driver_id']) : 0 }}" 
                                onclick="confirmRightBarRejectDriver(this);" class="white-btn chan-left">Reject Driver</button>

                      @else
                          <button type="button" data-id="{{ isset($pending_value['id']) ? base64_encode($pending_value['id']) : 0 }}" onclick="confirmRightBarCancelTrip(this);" class="green-btn chan-left">Cancel Trip</button>
                      @endif
                  @endif
              @endif
            </div>
            <div class="clear"></div>  
          </div>
        @endforeach
      @else
          <div class="right-content-bar">
            <h2>No Pending Trips Available</h2>
            <div class="clear"></div>  
          </div>
      @endif
    </div>

    <div class="right-bar-title">Ongoing</div>
      <div id="ongoing-scrollbar" class="content right-content-scrollbar">  
        @if(isset($arr_ongoing_trips) && sizeof($arr_ongoing_trips)>0)
          @foreach($arr_ongoing_trips as $ongoing_key => $ongoing_value)
            <?php
                  $enc_id = isset($ongoing_value['id']) ? $ongoing_value['id'] : '0';
                  $redirect_url = url('/').'/'.$user_path.'/track_trip?booking_id='.base64_encode($enc_id);
            ?>
            <div class="right-content-bar">
              <span class="booking-box-click" onclick="window.location.href='{{$redirect_url}}'"> </span>
              <h2>{{isset($ongoing_value['first_name']) ? $ongoing_value['first_name'] : ''}} {{isset($ongoing_value['last_name']) ? $ongoing_value['last_name'] : ''}}</h2>
              <span class="date-bar"> {{ isset($ongoing_value['booking_date']) ? $ongoing_value['booking_date'] : '' }}</span>
              <p><span>Pickup Loacation : </span>{{ isset($ongoing_value['pickup_location']) ? $ongoing_value['pickup_location'] : '' }}</p>
              <p><span>Drop Loacation: </span>{{ isset($ongoing_value['drop_location']) ? $ongoing_value['drop_location'] : '' }}</p>
              <div class="right-content-but">
                @if(isset($ongoing_value['booking_status']) && $ongoing_value['booking_status'] == 'TO_BE_PICKED')
                  <button type="button" data-id="{{ isset($ongoing_value['id']) ? base64_encode($ongoing_value['id']) : 0 }}" onclick="confirmRightBarCancelOngoingTrip(this);" class="green-btn chan-left">Cancel Trip</button>
                @endif

              </div>
              <div class="clear"></div>  
            </div>
          @endforeach
        @else
          <div class="right-content-bar">
              <h2>No Ongoing Trips Available</h2>
              <div class="clear"></div>  
            </div>
        @endif
      </div>
  </div>
</div>

<!-- popup section start -->
<div class="mobile-popup-wrapper">
<div id="right-bar-accept-driver-popup" class="modal fade" role="dialog" data-backdrop="static">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal"><!--&times;--></button>
        <h4 class="modal-title">Receiver Details</h4>
      </div>
      <div id="right_bar_show_error_div"></div>
      <div class="modal-body">
            <form id="frm_right_bar_accept_driver_details" data-parsley-validate>
                {{ csrf_field() }}
                <input type="hidden" name="right_bar_load_post_request_id" id="right_bar_load_post_request_id" value="0">
                <input type="hidden" name="right_bar_driver_id" id="right_bar_driver_id" value="0">
                <div class="form-group marg-top">
                    <input type="text" id="right_bar_po_no" name="right_bar_po_no" placeholder="Enter PO Number"/>
                </div>
                
                <div class="form-group marg-top">
                    <input type="text" id="right_bar_receiver_name" name="right_bar_receiver_name" placeholder="Enter Receiver Name" data-parsley-required-message="Please enter receiver name" data-parsley-required="true" data-parsley-errors-container="#err_right_bar_receiver_name"/>
                    <div id="err_right_bar_receiver_name" class="error-red"></div>
                </div>

                <div class="form-group marg-top">
                    <input type="text" id="right_bar_receiver_no" name="right_bar_receiver_no" placeholder="Enter Receiver Number" data-parsley-required-message="Please enter receiver number" data-parsley-required="true" data-parsley-errors-container="#err_right_bar_receiver_number" data-parsley-minlength="8" data-parsley-maxlength="12" onkeypress="return isNumberKey(event)"/>
                    <div id="err_right_bar_receiver_number" class="error-red"></div>
                </div>
                
                <div class="form-group marg-top">
                    <input type="text" id="right_bar_app_suite" name="right_bar_app_suite" placeholder="Enter Apt/Suite/Unit"/>
                </div>                

                <div class="login-btn popup"><a onclick="checkRightBarValidReceiverDetails(this)" href="javascript:void(0)">Submit</a></div>
            </form>
      </div>
    </div>

  </div>
</div>   
</div>
 <!-- popup section end -->


  <!-- popup section start -->
<div class="mobile-popup-wrapper">
<div id="right-bar-cancellation-reason-popup" class="modal fade" role="dialog" data-backdrop="static">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal"><!--&times;--></button>
        <h4 class="modal-title">Cancellation Reason</h4>
      </div>
      <div class="modal-body">
            <div id="div_right_bar_error_show"></div>
            <div class="form-group marg-top">
            <input type="hidden" name="enc_right_bar_booking_id" id="enc_right_bar_booking_id" value="0">
            <textarea id="right_bar_reason" name="right_bar_reason" placeholder="Enter Cancellation Reason"></textarea>
                <div id="err_right_bar_cancellation_reason" class="error-red"></div>
            </div>
            <div class="login-btn popup"><a onclick="checkRightBarValidCancellationReason()" href="javascript:void(0)">Submit</a></div>
      </div>
    </div>

  </div>
</div>   
</div>
<!-- popup section end --> 

<!--Left menu script start-->
<script type="text/javascript">
  function openmenus() {
    $('.left-side-inner-me').toggle();
  }
</script>
<!--Left menu script end-->

<script>
    (function($){
      $(window).load(function(){
        $("#pending-scrollbar").mCustomScrollbar({
          scrollButtons:{
            enable:true
          }
        });

        $("#ongoing-scrollbar").mCustomScrollbar({
          scrollButtons:{
            enable:true
          }
        });

      });
    })(jQuery);
</script>

<script type="text/javascript">

  var RIGHT_BAR_USER_PANEL_URL          = "{{url('/').'/'.$user_path}}";
  var RIGHT_BAR_CANCEL_TRIP_URL         = '{{ url('/').'/'.$user_path.'/cancel_pending_load_post'}}';
  var RIGHT_BAR_ACCEPT_DRIVER_URL       = '{{ url('/').'/'.$user_path.'/accept_load_post'}}';
  var RIGHT_BAR_REJECT_DRIVER_URL       = '{{ url('/').'/'.$user_path.'/reject_load_post'}}';
  var RIGHT_BAR_CANCEL_ONGOING_TRIP_URL = '{{ url('/').'/'.$user_path.'/process_cancel_trip'}}';

  function confirmRightBarCancelOngoingTrip(ref){
      var booking_id = $(ref).attr('data-id');
      if(booking_id!=undefined && booking_id!=''){
          $('#enc_right_bar_booking_id').val(booking_id);
          $('#right-bar-cancellation-reason-popup').modal('toggle');
      }
  }

  function checkRightBarValidCancellationReason()
  {
      $('#err_right_bar_cancellation_reason').html('');
      $('#div_right_bar_error_show').html('');

      if($('#right_bar_reason').val() == '')
      {
          $('#err_right_bar_cancellation_reason').html('Please enter cancellation reason');
          return false; 
      }  
      var obj_data = new Object();

      obj_data._token     = "{{ csrf_token() }}";
      obj_data.reason     = $('#right_bar_reason').val();
      obj_data.booking_id = $('#enc_right_bar_booking_id').val();

      $.ajax({
          url:RIGHT_BAR_CANCEL_ONGOING_TRIP_URL,
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
                  $('#right-bar-cancellation-reason-popup').modal('toggle');
                  swal("Success!", response.msg, "success");

                  setTimeout(function(){ 
                    location.reload();
                      //window.location.href = RIGHT_BAR_USER_PANEL_URL+'my_booking?trip_type=CANCELED';
                  }, 2000);

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

  function confirmRightBarCancelTrip(ref) 
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
              RIGHT_BAR_CANCEL_TRIP_URL = RIGHT_BAR_CANCEL_TRIP_URL+'?load_post_request_id='+load_post_request_id;
              window.location = RIGHT_BAR_CANCEL_TRIP_URL;
            }
          });
      }
  }

  function confirmRightBarAcceptDriver(ref)
  {
      var driver_id            = $(ref).attr('data-driver-id');
      var load_post_request_id = $(ref).attr('data-id');

      if((load_post_request_id!=undefined && driver_id!=undefined) && (load_post_request_id!='' && driver_id!='')){

          $('#right_bar_load_post_request_id').val(load_post_request_id);
          $('#right_bar_driver_id').val(driver_id);
          $('#right-bar-accept-driver-popup').modal('toggle');
      }
  }
 
  function confirmRightBarRejectDriver(ref)
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
              RIGHT_BAR_REJECT_DRIVER_URL = RIGHT_BAR_REJECT_DRIVER_URL+'?load_post_request_id='+load_post_request_id+'&driver_id='+driver_id;
              window.location = RIGHT_BAR_REJECT_DRIVER_URL;
            }
          });
      }   
  }

  function checkRightBarValidReceiverDetails(ref)
  {
      var is_valid_frm  = $('#frm_right_bar_accept_driver_details').parsley().validate();
      if(is_valid_frm == false)
      {
          return false;
      }

      var obj_data = new Object();
      obj_data._token               = "{{ csrf_token() }}";
      obj_data.load_post_request_id = $('#right_bar_load_post_request_id').val();
      obj_data.driver_id            = $('#right_bar_driver_id').val();
      obj_data.po_no                = $('#right_bar_po_no').val();
      obj_data.receiver_name        = $('#right_bar_receiver_name').val();
      obj_data.receiver_no          = $('#right_bar_receiver_no').val();
      obj_data.app_suite            = $('#right_bar_app_suite').val();

      $.ajax({
          url:RIGHT_BAR_ACCEPT_DRIVER_URL,
          type:'POST',
          data : obj_data,
          beforeSend:function(){
            $(ref).prop('disabled', true);
            $(ref).html("<i class='fa fa-spinner fa-spin'></i>");
            showProcessingOverlay();
          },
          success:function(response)
          {
              if(response.status=="success")
              { 
                  if(response.data.booking_master_id!=undefined && response.data.booking_master_id!='' && response.data.booking_master_id!=0){
                    var booking_id = response.data.booking_master_id;
                    var redirect_url = RIGHT_BAR_USER_PANEL_URL+'/track_trip?booking_id='+booking_id+'&redirect=track_driver';
                    window.location.href = redirect_url;
                  }
                  else {
                    location.reload();
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
                  $('#right_bar_show_error_div').html(error_html);
              }
              return false;
          }
      });
  }
</script>