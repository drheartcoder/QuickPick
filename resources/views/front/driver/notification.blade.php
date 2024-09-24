 @extends('front.layout.master')                

    @section('main_content')

    <div class="blank-div"></div>
    <div class="email-block">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6 col-md-6 col-lg-6">
                    <div class="headding-text-bredcr">
                       Notifications
                    </div>
                </div>
                <div class="col-sm-6 col-md-6 col-lg-6">
                    <div class="bredcrum-right">
                       <a href="{{ $module_url_path }}" class="bredcrum-home"> Dashboard </a>
                        <span class="arrow-righ"><i class="fa fa-angle-right"></i></span>
                        Notifications
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
                    <div class="dash-white-main padding-less">
                        <div class="form-main-block">
                            
                            @if(isset($arr_data['data']) && sizeof($arr_data['data'])>0)

                                @foreach($arr_data['data'] as $key => $value)
                                    
                                    <div class="notification-block">
                                        <div class="notification-icon">
                                            <img src="{{ url('/')}}/images/my-job-logo-img.png" alt="" />
                                        </div>
                                        <div class="notification-content">
                                            <div class="noti-head">
                                                {{isset($value['notification_type']) ? $value['notification_type'] : '-'}}
                                            </div>
                                            <div class="noti-time-text"><span><i class="fa fa-clock-o"></i></span> {{isset($value['created_at']) ? $value['created_at'] : '-'}}</div>
                                            <div class="noti-head-content">
                                                {{isset($value['title']) ? $value['title'] : '-'}}
                                            </div>
                                        </div>
                                        {{-- <button type="button" class="close-noti">
                                            <img src="{{ url('/')}}/images/noti-close-icon.png" alt="" class="gray-close-icon" />
                                            <img src="{{ url('/')}}/images/noti-close-icon-active.png" alt="" class="active-close-icon" />
                                        </button> --}}
                                        <div class="clr"></div>
                                    </div>

                                @endforeach
                            @else

                                <div class="no-record-block">
                                 <span>No Notifications</span> 
                               </div>

                            @endif

                        </div>
                    </div>

                    @if(isset($arr_pagination) && sizeof($arr_pagination)>0)
                        <div class="pagtna-main-wrapp">{!! $arr_pagination !!}</div>
                    @endif

                </div>
            </div>
        </div>
    </div>
   

@stop