 @extends('front.layout.master')                

    @section('main_content')

    <div class="blank-div"></div>
   <div class="email-block">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6 col-md-6 col-lg-6">
                    <div class="headding-text-bredcr">
                       Dashboard
                    </div>
                </div>
                <div class="col-sm-6 col-md-6 col-lg-6">
                    <div class="bredcrum-right">
                        <a href="javascript:void(0);" class="bredcrum-home"> Dashboard </a>
                        {{-- <span class="arrow-righ"><i class="fa fa-angle-right"></i></span>
                        Dashboard --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
   <!--dashboard page start-->

   <!--dashboard page start-->
   <div class="main-wrapper">
       <div class="container-fluid">
           <div class="row">
               @include('front.enterprise_admin.left_bar')
               <div class="col-sm-9 col-md-10 col-lg-10">
                  @include('front.layout._operation_status')
                   <div class="dash-right-block">
                       <div class="top-title">
                           <h2>Hi {{ $user_name }}, Welcome to {{ isset($arr_site_settings['site_name']) ? $arr_site_settings['site_name'] : ''}}!</h2>
                       </div>
                       {{-- <div class="row">
                            <div class="col-sm-6 col-md-6 col-lg-6" onclick="location.href = '{{url('/driver/my_job?trip_type=COMPLETED')}}';">
                              <div class="dash-menu-block-wrapper red">
                                   <div class="dash-menu-block">
                                       <div class="img-block">
                                          <span> <img src="{{ url('/')}}/images/dash-menu-icon3.png" class="img-responsive" alt=""/></span>
                                       </div>
                                       <div class="dash-pro-data">
                                           <h1>{{ get_trip('COMPLETED','DRIVER')}}</h1>
                                           <h5>Completed Trips</h5>
                                       </div>
                                   </div>
                               </div>
                           </div>
                           <div class="col-sm-6 col-md-6 col-lg-6" onclick="location.href = '{{url('/driver/my_job?trip_type=CANCELED')}}';">
                              <div class="dash-menu-block-wrapper red">
                                   <div class="dash-menu-block">
                                       <div class="img-block">
                                          <span> <img src="{{ url('/')}}/images/dash-menu-icon6.png" class="img-responsive" alt=""/></span>
                                       </div>
                                       <div class="dash-pro-data">
                                           <h1>{{ get_trip('CANCELED','DRIVER')}}</h1>
                                           <h5>Cancelled Trips</h5>
                                       </div>
                                   </div>
                               </div>
                           </div>
                           <div class="col-sm-6 col-md-6 col-lg-6" onclick="location.href = '{{url('/driver/review_rating')}}';">
                              <div class="dash-menu-block-wrapper sky-blue">
                                   <div class="dash-menu-block">
                                       <div class="img-block">
                                          <span> <img src="{{ url('/')}}/images/dash-menu-icon5.png" class="img-responsive" alt=""/></span>
                                       </div>
                                       <div class="dash-pro-data">
                                           <h1>{{ get_review_ratings()}}</h1>
                                           <h5>Review and Ratings</h5>
                                       </div>
                                   </div>
                               </div>
                           </div>
                           <div class="col-sm-6 col-md-6 col-lg-6" onclick="location.href = '{{url('/driver/notification')}}';">
                              <div class="dash-menu-block-wrapper green">
                                   <div class="dash-menu-block">
                                       <div class="img-block">
                                          <span> <img src="{{ url('/')}}/images/dash-menu-icon4.png" class="img-responsive" alt=""/></span>
                                       </div>
                                       <div class="dash-pro-data">
                                           <h1>{{ get_review_ratings()}}</h1>
                                           <h5>Notifications</h5>
                                       </div>
                                   </div>
                               </div>
                           </div>
                       </div> --}}
                       <div class="need-help hidden-xs hidden-sm inner-blue">
                           <div class="row">
                               <div class="col-sm-8 col-md-8 col-lg-9">
                                   <h4>Need {{ isset($arr_site_settings['site_name']) ? $arr_site_settings['site_name'] : ''}} Help?</h4>
                                   <p>We would be more than happy to help you. Our team advisor are 24/7 at your service to help you.</p>
                               </div>
                               <div class="col-sm-4 col-md-4 col-lg-3">
                                   <a href="{{ url('/contact_us') }}" class="get-in-touch">Get In Touch</a>
                               </div>
                           </div>
                       </div>
                   </div>
               </div>
           </div>
           <div class="need-help hidden-md hidden-lg inner-blue">
                <div class="row">
                    <div class="col-sm-8 col-md-8 col-lg-9">
                        <h4>Need {{ isset($arr_site_settings['site_name']) ? $arr_site_settings['site_name'] : ''}} Help?</h4>
                        <p>We would be more than happy to help you. Our team advisor are 24/7 at your service to help you.</p>
                    </div>
                    <div class="col-sm-4 col-md-4 col-lg-3">
                        <a href="{{ url('/contact_us') }}" class="get-in-touch">Get In Touch</a>
                    </div>
               </div>
        </div>
       </div>
   </div>
   <!--dashboard page end-->

@stop