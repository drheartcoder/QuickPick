 @extends('front.layout.master')                

    @section('main_content')

    <?php $driver_path     = config('app.project.role_slug.driver_role_slug'); ?>

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
                                            <img src="{{ isset($arr_trip_details['profile_image']) ? $arr_trip_details['profile_image'] : url('/uploads/default-profile.png') }}" alt="" />
                                        </div>
                                        <div class="review-content-block">
                                            <div class="review-send-head">
                                               {{isset($arr_trip_details['first_name']) ? $arr_trip_details['first_name'] : ''}} {{isset($arr_trip_details['last_name']) ? $arr_trip_details['last_name'] : ''}}
                                            </div>
                                            <div class="review-send-head-small-date"><i class="fa fa-calendar"></i> {{ isset($arr_trip_details['booking_date']) ? $arr_trip_details['booking_date'] : '' }}</div>
                                            <div class="my-job-address-block">
                                                <div class="my-job-address-head">
                                                    Pick :
                                                </div>
                                                <div class="my-job-address-right">
                                                     {{isset($arr_trip_details['pickup_location']) ? $arr_trip_details['pickup_location'] : ''}}
                                                </div>
                                                <div class="clearfix"></div>
                                            </div>
                                            
                                            <div class="my-job-address-block">
                                                <div class="my-job-address-head">
                                                    Drop :
                                                </div>
                                                <div class="my-job-address-right">
                                                    {{isset($arr_trip_details['drop_location']) ? $arr_trip_details['drop_location'] : ''}}
                                                </div>
                                                <div class="clearfix"></div>
                                            </div>
                                            @if(isset($arr_trip_details['booking_status']) && ($arr_trip_details['booking_status'] != 'CANCEL_BY_ADMIN'))
                                                <div class="my-job-address-block">
                                                    <div class="my-job-address-head">
                                                        Price :
                                                    </div>
                                                    <div class="my-job-address-right">
                                                        $ {{isset($arr_trip_details['total_charge']) ? number_format($arr_trip_details['total_charge'],2) : '0'}}
                                                    </div>
                                                    <div class="clearfix"></div>
                                                </div>
                                            @endif

                                        </div>
                                    </div>
                                    <div class="col-sm-2 col-md-2 col-lg-2">
                                        @if(isset($arr_trip_details['booking_status']) && $arr_trip_details['booking_status'] == 'COMPLETED')
                                            <div class="my-lob-completed">Completed</div>
                                        @elseif(isset($arr_trip_details['booking_status']) && ($arr_trip_details['booking_status'] == 'CANCEL_BY_USER' || $arr_trip_details['booking_status'] == 'CANCEL_BY_DRIVER' || $arr_trip_details['booking_status'] == 'CANCEL_BY_ADMIN'))
                                        <div class="my-lob-completed cancelled">Canceled</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                    @if(isset($arr_trip_details['booking_status']) && $arr_trip_details['booking_status'] == 'COMPLETED')
                        <div class="edit-posted-bg-main my-job-details bord-none">
                    @else
                        <div class="edit-posted-bg-main my-job-details">
                    @endif
                    
                        @if(isset($arr_trip_details['booking_status']) && $arr_trip_details['booking_status'] == 'COMPLETED')
                            <div class="row">
                                <div class="col-sm-12 col-md-6 col-lg-6">
                                    <div class="my-job-details">Package Details</div>
                                    <div class="first-names">
                                        <span>Type : </span>
                                        <div class="first-names-light">{{isset($arr_trip_details['package_type']) ? $arr_trip_details['package_type'] : ''}}</div>
                                        <div class="clearfix"></div>
                                    </div>
                                    @if(isset($arr_trip_details['package_type']) && $arr_trip_details['package_type']!="PALLET")
                                        <div class="first-names">
                                        <span>Weight (Pounds) : </span>
                                            <div class="first-names-light">{{isset($arr_trip_details['package_weight']) ? $arr_trip_details['package_weight'] : '0'}}</div>
                                            <div class="clearfix"></div>
                                        </div>
                                        <div class="first-names">
                                        <span>Length (ft) : </span>
                                            <div class="first-names-light">{{isset($arr_trip_details['package_length']) ? $arr_trip_details['package_length'] : '0'}}</div>
                                            <div class="clearfix"></div>
                                        </div>
                                        <div class="first-names">
                                        <span>Width (ft) : </span>
                                            <div class="first-names-light">{{isset($arr_trip_details['package_weight']) ? $arr_trip_details['package_weight'] : '0'}}</div>
                                            <div class="clearfix"></div>
                                        </div>
                                        <div class="first-names">
                                        <span>Height (ft) : </span>
                                            <div class="first-names-light">{{isset($arr_trip_details['package_height']) ? $arr_trip_details['package_height'] : ''}}</div>
                                            <div class="clearfix"></div>
                                        </div>
                                    @endif
                                    <div class="first-names">
                                    <span>Quantity : </span>
                                        <div class="first-names-light">{{isset($arr_trip_details['package_quantity']) ? $arr_trip_details['package_quantity'] : ''}}</div>
                                        <div class="clearfix"></div>
                                    </div>
                                </div>
                                
                                <div class="col-sm-12 col-md-6 col-lg-6">
                                  <div class="receipt-main-block">
                                   <div class="my-job-details">Trip Receipt</div>
                                   
                                    <div class="my-job-detai-disco"><i class="fa fa-check-circle"></i> $ {{isset($arr_trip_details['discount_amount']) ? $arr_trip_details['discount_amount'] : ''}} Discount applied</div>
                                   
                                    <div class="first-names align-lef">
                                        <span>Distance : </span>
                                        <div class="first-names-light">{{isset($arr_trip_details['distance']) ? $arr_trip_details['distance'] : ''}} Miles.</div>
                                        <div class="clearfix"></div>
                                    </div>
                                    <div class="first-names align-lef">
                                        <span>Time Taken : </span>
                                        <div class="first-names-light">{{isset($arr_trip_details['total_minutes_trip']) ? $arr_trip_details['total_minutes_trip'] : ''}} min</div>
                                        <div class="clearfix"></div>
                                    </div>
                                    <div class="first-names align-lef">
                                        <span>Total : </span>
                                        <div class="first-names-light">$ {{isset($arr_trip_details['final_amount']) ? $arr_trip_details['final_amount'] : ''}}</div>
                                        <div class="clearfix"></div>
                                    </div>
                                    <div class="first-names align-lef">
                                        <span>PO# : </span>
                                        <div class="first-names-light">{{isset($arr_trip_details['po_no']) ? $arr_trip_details['po_no'] : ''}}</div>
                                        <div class="clearfix"></div>
                                    </div>
                                       <div class="first-names align-lef">
                                        <span>Receiver Name : </span>
                                        <div class="first-names-light">{{isset($arr_trip_details['receiver_name']) ? $arr_trip_details['receiver_name'] : ''}}</div>
                                        <div class="clearfix"></div>
                                    </div>
                                       <div class="first-names align-lef">
                                        <span>Receiver Mob. No. : </span>
                                        <div class="first-names-light">{{isset($arr_trip_details['receiver_no']) ? $arr_trip_details['receiver_no'] : ''}}</div>
                                        <div class="clearfix"></div>
                                    </div>
                                    <div class="first-names align-lef">
                                        <span>Apt/Suite/Unit : </span>
                                        <div class="first-names-light">{{isset($arr_trip_details['app_suite']) ? $arr_trip_details['app_suite'] : ''}}</div>
                                        <div class="clearfix"></div>
                                    </div>
                                        {{-- <a href="javascript:void(0);"><button type="button" class="white-btn chan-left">Ok</button></a> --}}
                                        <div class="clearfix"></div>
                                         </div>
                                </div>
                            </div>
                        @else

                            <div class="my-job-details">Package Details</div>
                            <div class="first-names">
                                <span>Package Type : </span>
                                <div class="first-names-light">{{isset($arr_trip_details['package_type']) ? $arr_trip_details['package_type'] : ''}}</div>
                                <div class="clearfix"></div>
                            </div>
                            @if(isset($arr_trip_details['package_type']) && $arr_trip_details['package_type']!="PALLET")

                                <div class="first-names">
                                    <span>Weight (Pounds) : </span>
                                    <div class="first-names-light">{{isset($arr_trip_details['package_weight']) ? $arr_trip_details['package_weight'] : '0'}}</div>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="first-names">
                                    <span>Length (ft) : </span>
                                    <div class="first-names-light">{{isset($arr_trip_details['package_length']) ? $arr_trip_details['package_length'] : '0'}}</div>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="first-names">
                                    <span>Width (ft) : </span>
                                    <div class="first-names-light">{{isset($arr_trip_details['package_breadth']) ? $arr_trip_details['package_breadth'] : '0'}}</div>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="first-names">
                                    <span>Height (ft) : </span>
                                    <div class="first-names-light">{{isset($arr_trip_details['package_height']) ? $arr_trip_details['package_height'] : ''}}</div>
                                    <div class="clearfix"></div>
                                </div>
                            @endif
                            <div class="first-names">
                                <span>Quantity : </span>
                                <div class="first-names-light">{{isset($arr_trip_details['package_quantity']) ? $arr_trip_details['package_quantity'] : ''}}</div>
                                <div class="clearfix"></div>
                            </div>

                        @endif
                        
                        
                        <br>
                        <div class="btns-wrapper change-pass">

                        @if((isset($arr_trip_details['booking_status']) && $arr_trip_details['booking_status'] == 'COMPLETED') &&
                            (isset($arr_trip_details['download_receipt'])  && $arr_trip_details['download_receipt']!='') &&
                            (isset($arr_trip_details['booking_id'])  && $arr_trip_details['booking_id']!='')) 
                            <a href="{{url('/').'/'.$driver_path.'/download_invoice?booking_id='.base64_encode($arr_trip_details['booking_id'])}}"><button type="button" class="white-btn chan-left"><i class="fa fa-download"></i> Invoice</button></a>
                        @endif

                        @if(isset($arr_trip_details['booking_status']) && $arr_trip_details['booking_status'] == 'COMPLETED')
                           <a href="{{ url('/').'/'.$driver_path.'/my_job?trip_type=COMPLETED'}}"><button type="button" class="white-btn chan-left">Back</button></a>
                        @elseif(isset($arr_trip_details['booking_status']) && ($arr_trip_details['booking_status'] == 'CANCEL_BY_USER' || $arr_trip_details['booking_status'] == 'CANCEL_BY_DRIVER' || $arr_trip_details['booking_status'] == 'CANCEL_BY_ADMIN'))
                            <a href="{{ url('/').'/'.$driver_path.'/my_job?trip_type=CANCELED'}}"><button type="button" class="white-btn chan-left">Back</button></a>
                        @else
                            <a href="javascript:void(0);"><button type="button" class="white-btn chan-left">Back</button></a>
                        @endif
                        </div>
                    </div>
                        
                   {{--  <div class="edit-posted-bg-main my-job-details">
                        <div class="my-job-details">Details</div>
                        <div class="first-names">
                            <span>Type : </span>
                            <div class="first-names-light">{{isset($arr_trip_details['package_type']) ? $arr_trip_details['package_type'] : ''}}</div>
                            <div class="clearfix"></div>
                        </div>
                        <div class="first-names">
                            <span>Weight (Pounds) : </span>
                            <div class="first-names-light">{{isset($arr_trip_details['package_weight']) ? $arr_trip_details['package_weight'] : '0'}}</div>
                            <div class="clearfix"></div>
                        </div>
                        <div class="first-names">
                            <span>Length (ft) : </span>
                            <div class="first-names-light">{{isset($arr_trip_details['package_length']) ? $arr_trip_details['package_length'] : '0'}}</div>
                            <div class="clearfix"></div>
                        </div>
                        <div class="first-names">
                            <span>Width (ft) : </span>
                            <div class="first-names-light">{{isset($arr_trip_details['package_weight']) ? $arr_trip_details['package_weight'] : '0'}}</div>
                            <div class="clearfix"></div>
                        </div>
                        <div class="first-names">
                            <span>Height (ft) : </span>
                            <div class="first-names-light">{{isset($arr_trip_details['package_height']) ? $arr_trip_details['package_height'] : ''}}</div>
                            <div class="clearfix"></div>
                        </div>
                        <div class="first-names">
                            <span>Quantity : </span>
                            <div class="first-names-light">{{isset($arr_trip_details['package_quantity']) ? $arr_trip_details['package_quantity'] : ''}}</div>
                            <div class="clearfix"></div>
                        </div>

                        <br>
                        <div class="btns-wrapper change-pass">
                        @if(isset($arr_trip_details['booking_status']) && $arr_trip_details['booking_status'] == 'COMPLETED')
                           <a href="{{ url('/').'/'.$driver_path.'/my_job?trip_type=COMPLETED'}}"><button type="button" class="white-btn chan-left">Back</button></a>
                        @elseif(isset($arr_trip_details['booking_status']) && ($arr_trip_details['booking_status'] == 'CANCEL_BY_USER' || $arr_trip_details['booking_status'] == 'CANCEL_BY_DRIVER'))
                            <a href="{{ url('/').'/'.$driver_path.'/my_job?trip_type=CANCELED'}}"><button type="button" class="white-btn chan-left">Back</button></a>
                        @else
                            <a href="javascript:void(0);"><button type="button" class="white-btn chan-left">Back</button></a>
                        @endif
                        </div>
                    </div> --}}
                            
                    
                </div>

            </div>
        </div>
    </div>

@stop