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
                    
                    @include('front.layout._operation_status')

                    @if(isset($arr_data['data']) && sizeof($arr_data['data'])>0)

                        @foreach($arr_data['data'] as $key => $value)
                            
                            <?php
                                  
                                    //$redirect_url = url('/').'/'.$user_path.'/load_post_details?load_post_request_id='.base64_encode($value['id']).'&active_tab=request_list&type=request_list';
                            ?>

                            <div class="rating-white-block my-job booking-list">
                                <div class="row">
                                    <div class="col-sm-10 col-md-10 col-lg-10">
                                        <div class="review-profile-image">
                                            <img src="{{ isset($value['profile_image']) ? $value['profile_image'] : url('/uploads/default-profile.png') }}" alt="" />
                                        </div>
                                        <div class="review-content-block">
                                            <div class="review-send-head">
                                                
                                                <a href="javascript:void(0);"> {{isset($value['first_name']) ? $value['first_name'] : ''}} {{isset($value['last_name']) ? $value['last_name'] : ''}}</a>     

                                            </div>
                                            <div class="review-send-head-small-date"><i class="fa fa-clock-o"></i> {{ isset($value['time_ago']) ? $value['time_ago'] : '' }}</div>
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

                                        </div>
                                    </div>

                                    {{-- ->whereIn('request_status',['USER_REQUEST','TIMEOUT','REJECT_BY_USER','ACCEPT_BY_DRIVER','REJECT_BY_DRIVER']) --}}

                                    <div class="col-sm-2 col-md-2 col-lg-2  booking-list-but">
                                        @if(isset($value['request_status']) && ($value['request_status'] == 'ACCEPT_BY_DRIVER'))
                                            <div class="my-lob-completed" style="width: 120px">Accepted</div><div class="clearfix clr-m"></div>
                                        @else
                                            <div class="my-lob-completed" style="width: 120px">New Request</div><div class="clearfix clr-m"></div>
                                        @endif

                                        @if(isset($value['request_status']) && $value['request_status'] == 'USER_REQUEST')
                                            <a href="javascript:void(0);"><button type="button" data-id="{{ isset($value['id']) ? base64_encode($value['id']) : 0 }}" onclick="confirmAcceptTrip(this);" class="green-btn chan-left">Accept</button></a>
                                            <a href="javascript:void(0);"><button type="button" data-id="{{ isset($value['id']) ? base64_encode($value['id']) : 0 }}" onclick="confirmCancelRequest(this);" class="white-btn chan-left">Reject</button></a>
                                        @endif
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
<link rel="stylesheet" type="text/css" href="{{ url('/') }}/css/admin/sweetalert.css" />
<script src="{{ url('/') }}/js/admin/sweetalert.min.js"></script>
<script type="text/javascript" src="{{ url('/js/admin') }}/sweetalert_msg.js"></script>

<script type="text/javascript">
    var cancel_request_url = '{{ url('/').'/'.$user_path.'/cancel_pending_load_post'}}';
    var accept_request_url = '{{ url('/').'/'.$user_path.'/accept_pending_load_post'}}';

    function confirmAcceptTrip(ref)
    {
        var load_post_request_id = $(ref).attr('data-id');
        if(load_post_request_id!=undefined && load_post_request_id!=''){
            swal({
              title: "Are you sure ?",
              text: 'You want to cancel accept shipment request.',
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
                accept_request_url = accept_request_url+'?load_post_request_id='+load_post_request_id;
                window.location = accept_request_url;
              }
            });
        }
    }
    function confirmCancelRequest(ref) 
    {
        var load_post_request_id = $(ref).attr('data-id');
        if(load_post_request_id!=undefined && load_post_request_id!=''){
            swal({
              title: "Are you sure ?",
              text: 'You want to cancel reject shipment request.',
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
                cancel_request_url = cancel_request_url+'?load_post_request_id='+load_post_request_id;
                window.location = cancel_request_url;
              }
            });
        }
    }
</script>
@stop