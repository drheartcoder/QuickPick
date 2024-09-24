 @extends('front.layout.master')                

    @section('main_content')

    <?php $user_path     = config('app.project.role_slug.driver_role_slug'); ?>
    
    <div class="blank-div"></div>
    <div class="email-block">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6 col-md-6 col-lg-6">
                    <div class="headding-text-bredcr">
                       My Earnings
                    </div>
                </div>
                <div class="col-sm-6 col-md-6 col-lg-6">
                    <div class="bredcrum-right">
                        <a href="{{ $module_url_path }}" class="bredcrum-home"> Dashboard </a>
                        <span class="arrow-righ"><i class="fa fa-angle-right"></i></span>
                        My Earnings
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
                     <div class="row">
                         
                         <div class="col-sm-12 col-md-4 col-lg-4">
                            <div class="rating-white-block earning">
                               <div class="earnint-amount-titel">Total Amount</div>
                               <div class="earnint-amount">$ {{ isset($arr_earning_data['driver_total_amount']) ? $arr_earning_data['driver_total_amount'] : '0' }}</div>
                             </div>
                         </div>

                         <div class="col-sm-12 col-md-4 col-lg-4">
                            <div class="rating-white-block earning">
                               <div class="earnint-amount-titel">Paid Amount</div>
                                <div class="earnint-amount">$ {{ isset($arr_earning_data['driver_paid_amount']) ? $arr_earning_data['driver_paid_amount'] : '0' }}</div>
                             </div>
                         </div>
                         
                         <div class="col-sm-12 col-md-4 col-lg-4">
                            <div class="rating-white-block earning">
                               <div class="earnint-amount-titel">Unpaid Amount</div>
                               <div class="earnint-amount">$ {{ isset($arr_earning_data['driver_unpaid_amount']) ? number_format($arr_earning_data['driver_unpaid_amount'],2) : '0' }}</div>
                             </div>
                         </div>
                         
                     </div>
                    
                    @if(isset($arr_data['data']) && sizeof($arr_data['data'])>0)

                        @foreach($arr_data['data'] as $key => $value)

                            <div class="rating-white-block my-job">
                                <div class="row">
                                    <div class="col-sm-10 col-md-10 col-lg-10">
                                        <div class="review-profile-image">
                                            <img src="{{ url('/')}}/images/my-job-logo-img.png" alt="" />
                                        </div>
                                        <div class="review-content-block">
                                            <div class="review-send-head">
                                                {{isset($value['transaction_id']) ? $value['transaction_id'] : ''}} ({{isset($value['date']) ? $value['date'] : ''}})
                                            </div>
                                            <div class="my-job-address-block">
                                                <div class="my-job-address-head">
                                                    Amount :
                                                </div>
                                                <div class="my-job-address-right">
                                                     {{ isset($value['amount_paid']) ? number_format($value['amount_paid'],2) : '0' }}
                                                </div>
                                                <div class="clearfix"></div>
                                            </div>
                                           
                                            <div class="my-job-address-block">
                                                <div class="my-job-address-head">
                                                    Note :
                                                </div>
                                                <div class="my-job-address-right">  
                                                    {{isset($value['note']) ? $value['note'] : ''}}
                                                </div>
                                                <div class="clearfix"></div>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="col-sm-2 col-md-2 col-lg-2">
                                        @if(isset($value['status']) && $value['status'] == 'SUCCESS')
                                            <div class="my-lob-completed">Success</div>
                                        @elseif(isset($value['status']) && $value['status'] == 'FAILED')
                                        <div class="my-lob-completed cancelled">Failed</div>
                                        @elseif(isset($value['status']) && $value['status'] == 'PENDING')
                                        <div class="my-lob-completed pending">Failed</div>
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
                         <span>No Earnings Available</span> 
                       </div>

                    @endif
                </div>

            </div>
        </div>
    </div>

@stop