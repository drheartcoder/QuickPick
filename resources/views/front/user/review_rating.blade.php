 @extends('front.layout.master')                

    @section('main_content')

    <div class="blank-div"></div>
    <div class="email-block">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6 col-md-6 col-lg-6">
                    <div class="headding-text-bredcr">
                       Review And Ratings
                    </div>
                </div>
                <div class="col-sm-6 col-md-6 col-lg-6">
                    <div class="bredcrum-right">
                        <a href="{{ $module_url_path }}" class="bredcrum-home"> Dashboard </a>
                        <span class="arrow-righ"><i class="fa fa-angle-right"></i></span>
                        Review And Ratings
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
                    
                    @if(isset($arr_data['data']) && sizeof($arr_data['data'])>0)

                        @foreach($arr_data['data'] as $key => $value)

                            <div class="rating-white-block">
                                <div class="review-profile-image">
                                    <img src="{{ isset($value['profile_image']) ? $value['profile_image'] : url('/uploads/default-profile.png') }}" alt="" />
                                </div>
                                <div class="review-content-block">
                                    {{-- <div class="review-send-head">
                                        {{ isset($value['tag_name']) ? $value['tag_name'] : '' }}
                                    </div> --}}
                                    <div class="rating-review-stars">
                                        {{-- <span class="start-rate-count-blue">{{ isset($value['rating']) ? number_format($value['rating'],1) : '0.0' }}</span> --}}
                                        <span class="stars-block star-listing">
                                            <span>
                                                <?php
                                                        $rating = isset($value['rating'])?intval($value['rating']):0; 
                                                ?>

                                                @for($i=1;$i<=5;$i++)
                                                    @if($i<=$rating)
                                                       <i class="fa fa-star star-acti"></i> 
                                                    @else
                                                        <i class="fa fa-star star"></i> 
                                                    @endif
                                                @endfor 
                                            </span>
                                        </span>
                                        {{-- <div class="time-text"> {{ isset($value['rating_date']) ? $value['rating_date'] : '' }} </div> --}}
                                    </div>
                                    <div class="review-rating-message">
                                        {{ isset($value['rating_msg']) ? $value['rating_msg'] : '' }}
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                            
                        @endforeach

                        @if(isset($arr_pagination) && sizeof($arr_pagination)>0)
                            <div class="pagtna-main-wrapp">{!! $arr_pagination !!}</div>
                        @endif
                    @else

                        <div class="no-record-block">
                            <span>You can receive the reviews from drivers in this section.</span> 
                        </div>

                    @endif

                </div>
                @include('front.user.right_bar')
            </div>
        </div>
    </div>
 
@stop