 @extends('front.layout.master')                

    @section('main_content')

    <?php $user_path     = config('app.project.role_slug.driver_role_slug'); ?>

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
                @include('front.driver.left_bar')
                <div class="col-sm-9 col-md-10 col-lg-10">
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
                                            <div class="review-send-head-small-date"><i class="fa fa-calendar"></i> {{ isset($arr_load_post_details['booking_date']) ? $arr_load_post_details['booking_date'] : '' }}</div>
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
                                    
                                    @if(isset($arr_load_post_details['request_status']) && $arr_load_post_details['request_status'] == 'USER_REQUEST')
                                        <div class="col-sm-2 col-md-2 col-lg-2">
                                            <div class="my-lob-completed" style="width: 120px">New Request</div>
                                        </div>
                                    @elseif(isset($arr_load_post_details['request_status']) && $arr_load_post_details['request_status'] == 'ACCEPT_BY_DRIVER')
                                        <div class="col-sm-2 col-md-2 col-lg-2">
                                            <div class="my-lob-completed" style="width: 120px">Accepted</div>
                                        </div>
                                    @elseif(isset($arr_load_post_details['request_status']) && $arr_load_post_details['request_status'] == 'REJECT_BY_DRIVER' || $arr_load_post_details['request_status'] == 'REJECT_BY_USER')
                                        <div class="col-sm-2 col-md-2 col-lg-2">
                                            <div class="my-lob-completed cancelled" style="width: 120px">Canceled</div>
                                        </div>
                                    @endif

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

                            @if(isset($arr_load_post_details['request_status']) && $arr_load_post_details['request_status'] == 'USER_REQUEST')
                                <a href="javascript:void(0);"><button type="button" data-id="{{ isset($arr_load_post_details['load_post_request_id']) ? base64_encode($arr_load_post_details['load_post_request_id']) : 0 }}" onclick="confirmAcceptTrip(this);" class="white-btn chan-left">Accept Request</button></a>
                                <a href="javascript:void(0);"><button type="button" data-id="{{ isset($arr_load_post_details['load_post_request_id']) ? base64_encode($arr_load_post_details['load_post_request_id']) : 0 }}" onclick="confirmCancelRequest(this);" class="white-btn chan-left">Reject Request</button></a>
                                <a href="{{ url('/').'/'.$user_path.'/my_job?trip_type=PENDING'}}"><button type="button" class="white-btn chan-left">Back</button></a>
                            @elseif(isset($arr_load_post_details['request_status']) && $arr_load_post_details['request_status'] == 'ACCEPT_BY_DRIVER')
                                <a href="{{ url('/').'/'.$user_path.'/my_job?trip_type=PENDING'}}"><button type="button" class="white-btn chan-left">Back</button></a>
                            @elseif(isset($arr_load_post_details['request_status']) && ($arr_load_post_details['request_status'] == 'REJECT_BY_DRIVER' || $arr_load_post_details['request_status'] == 'REJECT_BY_USER'))
                                <a href="{{ url('/').'/'.$user_path.'/my_job?trip_type=CANCELED'}}"><button type="button" class="white-btn chan-left">Back</button></a>
                            @endif

                        </div>


                    </div>
                            
                    
                </div>

            </div>
        </div>
    </div>
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