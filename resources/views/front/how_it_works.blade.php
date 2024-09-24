@extends('front.layout.master') @section('main_content')

<style>
    .how-it-top-conten.top-bord {
        position: relative;
        padding-bottom: 42px
    }

    .how-it-top-conten.top-bord:before {
        position: absolute;
        height: 100%;
        content: "";
        right: 0;
        left: 0;
        bottom: 0;
        margin: 0 auto;
        width: 46%;
        border-bottom: 2px solid #5dc3e9
    }

    .how-ti-work-slider {
        background-repeat: no-repeat;
        position: relative;
        padding: 35px 300px 40px;
        background-position: top center;
        background-image: url({{url('/images/how-it-work-slider-bg.jpg')}});
        height: 400px;
    }
    .how-ti-work-slider .carousel-inner{max-width: 1200px;margin: 0 auto;}
    .how-ti-work-slider .carousel{height: 320px;}

    /*.how-ti-work-slider:before{position: absolute;content: "";width: 100%;height: 100%;top: 0;left: 0;background-color: rgba(112,208,243,0.5)}*/

    .how-ti-work-sli-img-block {
        display: table-cell;
        vertical-align: middle;
        padding-right: 60px
    }

    /*.how-ti-work-sli-img-block img{height: 425px}*/

    .how-ti-work-sli-text-block {
        display: table-cell;
        vertical-align: middle
    }

    .how-ti-work-sli-head-black {
        color: #374247;
        font-size: 36px;
        line-height: 36px;
        text-transform: uppercase
    }

    .how-ti-work-sli-head-white {
        color: #fff;
        font-size: 36px;
        line-height: 36px;
        font-family: 'robotolight';
        text-transform: uppercase;
        margin-bottom: 40px
    }

    .how-ti-work-sli-phara {
        color: #fff;
        font-size: 18px;
        line-height: 27px;
        font-family: 'robotolight';
        width: 100%;
        max-width: 538px;
    }

    /*.how-ti-work-slider .carousel-control.right{background-image: none}
    .how-ti-work-slider .carousel-control.left{background-image: none}*/

    .how-ti-work-slider .carousel-indicators li {
        width: 14px;
        height: 14px;
        background-color: #fff;
        border: 1px solid #fff;
        margin: 0 4px
    }

    .how-ti-work-slider .carousel-indicators li.active {
        width: 14px;
        height: 14px;
        background-color: #343434;
        border: 1px solid #343434;
    }

    .how-ti-work-slider .carousel-indicators {
        bottom: -26px
    }

    .how-ti-work-slider .carousel-control.right {
        background-repeat: no-repeat;
        height: 54px;
        width: 27px;
        top: 121px;
        right: -180px;
        filter: invert(40);
        background-image: url({{url('/images/how-it-work-slide-arro-right.png')}});
    }

    .how-ti-work-slider .carousel-control.left {
        background-repeat: no-repeat;
        height: 54px;
        width: 27px;
        top: 121px;
        left: -180px;
        filter: invert(40);
        background-image: url({{url('/images/how-it-work-slide-arro-left.png')}});
    }

    .how-it-top-conten-wrappe {
        padding: 50px 15px 30px
    }

    .how-user-tab-head {
        position: relative;
        width: 62%;
        display: block;
        float: right;
    }

    .resp-tab-active .how-user-tab-head:before {
        position: absolute;
        content: "";
        bottom: -26px;
        right: 0;
        left: 0;
        margin: 0 auto;
        width: 0;
        height: 0;
        border-left: 19px solid transparent;
        border-right: 19px solid transparent;
        border-bottom: 19px solid #fff
    }

    .how-user-tab-head.driver {
        float: left
    }
    .how-ti-work-sli-second{
        display: none;
    }
    .how-ti-work-sli-left-count-wrapp{
        display: none;
    }
    .how-ti-work-slider .carousel-control.right {
            top: 0;
            bottom: 0;
            margin: auto;
        opacity: 1
        }
        .how-ti-work-slider .carousel-control.left {
            top: 0;
            bottom: 0;
            margin: auto;
            opacity: 1
        }
    .safe-secure-block.how-it {
           margin-top: -77px;
     }
    .safe-secure-block.how-it .resp-tabs-list li:first-child{border-right: 3px solid #5dc3e9}
    .email-block {
    padding: 100px 0 174px;background-position: center center;
    }
    .safe-secure-block.how-it .resp-tabs-list li{font-size: 21px;font-family: 'robotobold';font-weight: 600;padding: 21px 0 26px;
    position: relative;background-color: rgba(217,218,219,0.4);}
    
    /*------ this main css for replace ------*/
    .how-it-top-sub {
      margin: 28px auto 0;
      line-height: 31px;
      font-size: 18px;        
     }
    .how-it-two-section{position: relative;padding-bottom: 42px;margin-top: 58px;}
    .how-it-two-section:before{position: absolute;height: 100%;content: "";right: 0;left: 0;bottom: 0;margin: 0 auto;width: 46%;border-bottom: 2px solid #5dc3e9;}
    .how-it-top-conten {
      font-size: 19px;
      margin: 43px auto -27px;
      font-family: 'robotomedium';
      line-height: 31px;
      max-width: 870px;
     }
    .how-it-top-conten-wrappe {
       padding: 50px 15px 117px;
    }
    /*------ this main css for replace ------*/

    .resp-tab-active:before {
        display: none
    }

    @media all and (max-width:1400px) {
        .how-ti-work-slider {
            padding: 35px 80px 40px;
            background-size: cover
        }
        .how-ti-work-slider .carousel-control.left {
            left: -50px
        }
        .how-ti-work-slider .carousel-control.right {
            right: -50px
        }
    }
    
    
   @media all and (max-width:1199px) {
        .how-user-tab-head {
            width: 100%;
            float: none
        }
        .how-user-tab-head.driver {
            float: none
        }
    }

    @media all and (max-width:991px) {
        .how-ti-work-sli-img-block {
            display: block; }
        /*.how-ti-work-sli-text-block {
            display: block;
            text-align: center;
            margin-top: 30px
        }*/
        .how-ti-work-sli-head-white {
            font-size: 25px;
            margin-bottom: 11px
        }
        .how-ti-work-sli-head-black {
            font-size: 27px
        }
        .how-ti-work-sli-phara {
            max-width: 100%;
            margin-bottom: 30px
        }
        .how-ti-work-slider .carousel-indicators li {
            width: 13px;
            height: 13px;
            margin: 0 3px
        }
        .how-ti-work-slider .carousel-indicators li.active {
            width: 13px;
            height: 13px
        }
        .how-ti-work-slider .carousel-indicators {
            bottom: -18px
        }
        
        .how-ti-work-sli-second{
        display: block;
        margin-bottom: 60px;
        }
        .how-ti-work-sli-first{
         display: none;   
        }
        .how-ti-work-sli-left-count-wrapp{
        display: block;
        float: left;
        font-size: 77px;
        color: #374247;
        font-family: 'robotobold';
        line-height: 68px;
        } 
        .how-ti-work-sli-rihgt-text-wrapp{
           display: block;
           margin-left: 43px;
        }
        .how-ti-work-slider .carousel-indicators {
          bottom: -5px;
        }
        .resp-tabs-container {
           border-top: none;
         }
        .how-it-top-conten-wrappe {
            padding: 10px 15px 6px;
         }
        .safe-secure-block.how-it .resp-tabs-list li{padding: 13px 0 20px !important}
        .resp-tab-active .how-user-tab-head::before{bottom: -20px}
        .safe-secure-block.how-it {margin-top: -63px}
        .how-ti-work-sli-second img {height:140px;}
  
    }

    @media all and (max-width:768px) {
        /*How it work tab Start Here*/
        .how-it ul.resp-tabs-list {
            display: block;
        }
        .how-it h2.resp-accordion {
            display: none;
        }
        .how-it .tabbing_area {
            margin-top: 0px;
        }
        .how-it .resp-tabs-list li {
            padding: 13px 0 20px !important;
        }
        /* .how-it h2.resp-accordion{background-color: #a8a9a9;float: left;width: 50%; text-align: center; padding: 13px 0 20px;text-transform: uppercase;
    font-family: 'robotomedium';color: #fff;font-size: 17px; }
   .how-it h2.resp-tab-active{background-color: #51a1bf !important;}
   .how-it .resp-tabs-container{margin-bottom: 50px;}*/
        /*How it work tab End Here*/
    }

    @media all and (max-width:767px) {

        .resp-tab-active .how-user-tab-head:before {
            display: none
        }
        .how-it-top-conten-wrappe {
            padding: 0 15px 30px
        }
        /*.safe-secure-block.how-it {
            padding: 0 15px
        }*/
        .how-ti-work-slider{height: 470px;
            padding: 35px 30px 40px;
        }
        .how-ti-work-sli-left-count-wrapp{
            font-size: 119px;
            line-height: 105px;
        }
        .how-ti-work-sli-rihgt-text-wrapp{
            margin-left: 72px;
        }
        .how-ti-work-slider .carousel-control.left {
            left: -25px;filter: invert(0);
        }
        .how-ti-work-slider .carousel-control.right {
            right: -25px;filter: invert(0);
        }
        .resp-tab-active {
            color: #fff !important;
        }
        .resp-tab-active .how-user-tab-head::before{
            display: block
        }
         /*Bredcrum css*/
        .email-block {padding: 53px 0 118px;background-position: center center;background-size: cover;}
        .safe-secure-block.how-it .resp-tabs-list li:first-child{border-right: none}
        .safe-secure-block.how-it .how-it-top-conten-wrappe {border-bottom: none;}
        .safe-secure-block.how-it .tabbing_area {margin-bottom: 10px}
/*        .how-it-top-conten.top-bord::before{width: 70%}*/
        .how-ti-work-slider .carousel-indicators { bottom: -110px;}
        
        .headding-text-bredcr{font-size: 44px;line-height: 47px;}
        /*Bredcrum css*/
    }
    
    .how-it-work-img{float: left;}
    .how-it-new-section .how-it-top-conten{font-family: robotolight;}
    .quickpick-img-contnet{text-align: right;margin-left: 220px;}
    .quickpick-img-contnet span{font-family: 'robotomedium';}
    .how-it-two-section .how-it-work-img{float: right;}
    .how-it-two-section .quickpick-img-contnet{margin: 0 150px 0 0;text-align: left;}
    .how-it-clock-section .quickpick-img-contnet{margin-left: 120px;}
    
</style>

<div class="blank-div"></div>

<div class="email-block change">
    <div class="container">
        <div class="row">
            <div class="col-sm-12 col-md-12 col-lg-12">
                <div class="headding-text-bredcr">
                    How it Works
                </div>
            </div>
            <!--<div class="col-sm-6 col-md-6 col-lg-6">
                <div class="bredcrum-right">
                    <a href="{{ url('/')}}" class="bredcrum-home"> Home </a>
                    <span class="arrow-righ"><i class="fa fa-angle-right"></i></span> How it Work
                </div>
            </div>-->
        </div>
    </div>
</div>

{{-- {!! isset($page_details->page_desc) ? $page_details->page_desc : '' !!} --}}


<div class="safe-secure-block how-it">
    <!--<div class="container">-->
    <!-- Tab Main Section Start -->
    <div class="tabbing_area">
        <div id="horizontalTab">
            <div class="how-it-resp-tabs-name">
                <ul class="resp-tabs-list">
                    <li><span class="how-user-tab-head">Customer</span></li>
                    <li><span class="how-user-tab-head driver">Driver</span></li>
                </ul>
            </div>
            <div class="clearfix"></div>
            <div class="clearfix"></div>
            <div class="resp-tabs-container">
                <!--tab-1 start-->

                {{-- QuickPick drivers deliver to job sites all across Northern Virginia, D.C. and Maryland. We connect with you to figure out what supplies you&rsquo;re missing. Then, our team will pick up and drop off whatever tools, parts, or materials you need. --}}

                {{-- You placed your orders, but your supplies didn&rsquo;t show up on time. In the D.C. metro area, there wasn&rsquo;t an affordable way to quickly and painlessly fix this dilemma. Until now. --}}

                <div>
                    <div class="how-it-top-conten-wrappe how-it-new-section">
                        <div class="how-it-top-conten top-bord">
                            <div class="how-it-work-img">
                                <img src="{{url('/')}}/images/how-it-works-truck-img.png" alt="" />
                            </div>
                            <div class="quickpick-img-contnet">
                                We work fast, so you can stay on schedule. We deliver whatever you need, wherever you need it! <!-- <span>QuickPick will deliver within 1-5 hours.</span> -->
                            </div>  
                            <div class="clearfix"></div>                          
                        </div>
                        <div class="how-it-top-sub how-it-two-section">
                            <div class="how-it-work-img">
                                <img src="{{url('/')}}/images/how-it-works-map-img.png" alt="" />
                            </div>
                            <div class="quickpick-img-contnet">
                                QuickPick drivers delivers to locations all across Northern Virginia, D.C. and Maryland. We connect with you pick up and drop off whatever you need.
                            </div>
                        </div>
                        <div class="how-it-top-sub how-it-clock-section">
                            <div class="how-it-work-img">
                                <img src="{{url('/')}}/images/how-it-works-clock-img.png" alt="" />
                            </div>
                            <div class="quickpick-img-contnet">
                                We understand the problem. You placed your orders, but your supplies didn't show up on time. In the D.C. metro area, there wasn't an affordable way to quickly and painlessly fix this dilemma. Until now.
                            </div>
                        </div>
                    </div>
                    <!--<div class="how-it-work-main-block">
                            <div class="container">
                                <div class="border-line">
                                    <div class="white-gredient-bg-blo">
                                        <div class="row">
                                            <div class="col-md-6 col-lg-6 col-sm-12 circle-move hidden-sm">
                                                <div class="white-gredi-img-block"></div>
                                                <span class="white-gredi-circle">01</span></div>
                                            <div class="col-md-6 col-lg-6 col-sm-12">
                                                <div class="how-it-box">
                                                    <div class="how-it-img"><img src="images/how-1.png" alt="how" /></div>
                                                    <div class="white-gredi-text-block">
                                                        <h1>Give us a call, or send us your info online</h1>
                                                        <p class="white-gredi-sub">If you&rsquo;re a first-time customer, we&rsquo;ll help you set up a QuickPick account. That way, your next order will be processed in no time.</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="white-gredient-bg-blo">
                                        <div class="row">
                                            <div class="col-md-6 col-lg-6 col-sm-12 float-righ-white hidden-sm">
                                                <div class="white-gredi-img-block"></div>
                                            </div>
                                            <div class="col-md-6 col-lg-6 col-sm-12 circle-move">
                                                <div class="how-it-box right-box">
                                                    <div class="how-it-img"><img src="images/how-2.png" alt="how" /></div>
                                                    <div class="white-gredi-text-block for-left-mar">
                                                        <h1>QuickPick drivers get to work</h1>
                                                        <p class="white-gredi-sub">We find the right truck, based on the supplies you need. Our fleet of vehicles includes pickup trucks, box trucks, and flatbed trucks.</p>
                                                    </div>
                                                </div>
                                                <div class="white-gredi-circle">
                                                    <div class="circle-digict">02</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="white-gredient-bg-blo">
                                        <div class="row">
                                            <div class="col-md-6 col-lg-6 col-sm-12 circle-move hidden-sm">
                                                <div class="white-gredi-img-block"></div>
                                                <span class="white-gredi-circle">03</span></div>
                                            <div class="col-md-6 col-lg-6 col-sm-12">
                                                <div class="how-it-box">
                                                    <div class="how-it-img"><img src="images/how-3.png" alt="how" /></div>
                                                    <div class="white-gredi-text-block">
                                                        <h1>Confirm the details of your order</h1>
                                                        <p class="white-gredi-sub">Let us know what we&rsquo;re picking up, its approximate size, where you need it delivered, and what time you want it there.</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="white-gredient-bg-blo">
                                        <div class="row">
                                            <div class="col-md-6 col-lg-6 col-sm-12 float-righ-white hidden-sm">
                                                <div class="white-gredi-img-block">
                                                                                            <img src="images/How-it-Works-1.png" alt="" /></div>
                                            </div>
                                            <div class="col-md-6 col-lg-6 col-sm-12 circle-move">
                                                <div class="how-it-box right-box">
                                                    <div class="how-it-img"><img src="images/how-4.png" alt="how" /></div>
                                                    <div class="white-gredi-text-block for-left-mar">
                                                        <h1>Your delivery arrives on time</h1>
                                                        <p class="white-gredi-sub">Our certified drivers know how to navigate the D.C. area, so even with the heaviest traffic, you won&rsquo;t have to wait long for your materials.</p>
                                                    </div>
                                                </div>
                                                <div class="white-gredi-circle">
                                                    <div class="circle-digict">04</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="clr"></div>
                                </div>
                            </div>
                        </div>-->
                    <div class="how-ti-work-slider">
                        <!--<div class="container">-->
                        <div id="myCarousel" class="carousel slide" data-ride="carousel">
                            <!-- Indicators -->
                            <ol class="carousel-indicators">
                                <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
                                <li data-target="#myCarousel" data-slide-to="1"></li>
                                <li data-target="#myCarousel" data-slide-to="2"></li>
                                <li data-target="#myCarousel" data-slide-to="3"></li>
                                <li data-target="#myCarousel" data-slide-to="4"></li>
                            </ol>

                            <!-- Wrapper for slides -->
                            <div class="carousel-inner">
                                <div class="item active">
                                    <div class="how-ti-work-sli-img-block">
                                        <div class="how-ti-work-sli-first"><img src="{{url('/images/how-it-works-slider-img-1.png')}}" alt="how-it-work-slider-inner-img-1" /></div>
                                    </div>
                                    <div class="how-ti-work-sli-text-block">
                                        <div class="how-ti-work-sli-righ-left-wrapper">
                                            <div class="how-ti-work-sli-left-count-wrapp">1</div>
                                            <div class="how-ti-work-sli-rihgt-text-wrapp">
                                                <span class="how-ti-work-sli-head-white">Sign Up or</span>
                                                <span class="how-ti-work-sli-head-black">Give us a call</span>
                                            </div>
                                            <div class="clearfix"></div>
                                        </div>
                                        <div class="how-ti-work-sli-phara" style="margin-top: 30px;">Set up a QuickPick account. That way, your next order will be processed in no time</div>
                                       <div class="how-ti-work-sli-second"><img src="{{url('/images/how-it-works-slider-img-m-1.png')}}" alt="how-it-work-slider-inner-img-second" /></div>
                                    </div>
                                </div>

                                <div class="item">
                                    <div class="how-ti-work-sli-img-block">
                                        <div class="how-ti-work-sli-first"> <img src="{{url('/images/how-it-works-slider-img-2.png')}}" alt="how-it-work-slider-inner-img-1" /></div>
                                    </div>
                                    <div class="how-ti-work-sli-text-block">
                                        <div class="how-ti-work-sli-righ-left-wrapper">
                                            <div class="how-ti-work-sli-left-count-wrapp">2</div>
                                            <div class="how-ti-work-sli-rihgt-text-wrapp">
                                                <span class="how-ti-work-sli-head-black">CONFIRM DETAILS</span>
                                                <span class="how-ti-work-sli-head-white">OF YOUR ORDER</span>
                                            </div>
                                            <div class="clearfix"></div>
                                        </div>
                                        <div class="how-ti-work-sli-phara" style="margin-top: 30px;">Let us know what we are MOVING, its size, pickup and drop-off locations. </div>
                                        <div class="how-ti-work-sli-second"><img src="{{url('/images/how-it-works-slider-img-m-2.png')}}" alt="how-it-work-slider-inner-img-second" /></div>
                                    </div>
                                </div>

                                <div class="item">
                                    <div class="how-ti-work-sli-img-block">
                                        <div class="how-ti-work-sli-first"> <img src="{{url('/images/how-it-works-slider-img-3.png')}}" alt="how-it-work-slider-inner-img-1" /></div>
                                    </div>
                                    <div class="how-ti-work-sli-text-block">
                                        <div class="how-ti-work-sli-righ-left-wrapper">
                                            <div class="how-ti-work-sli-left-count-wrapp">3</div>
                                            <div class="how-ti-work-sli-rihgt-text-wrapp">
                                                <span class="how-ti-work-sli-head-black">DRIVER</span>
                                                <span class="how-ti-work-sli-head-white"> GETS TO WORK</span>
                                            </div>
                                            <div class="clearfix"></div>
                                        </div>
                                        <div class="how-ti-work-sli-phara" style="margin-top: 30px;">We will offer the right vehicle based on your supplies. Our fleet includes pickup trucks, box. Trucks, flatbed trucks, among others. </div>
                                        <div class="how-ti-work-sli-second"><img src="{{url('/images/how-it-works-slider-img-m-3.png')}}" alt="how-it-work-slider-inner-img-second" /></div>
                                    </div>
                                </div>
                                <div class="item">
                                    <div class="how-ti-work-sli-img-block">
                                        <div class="how-ti-work-sli-first"> <img src="{{url('/images/how-it-works-slider-img-4.png')}}" alt="how-it-work-slider-inner-img-1" /></div>
                                    </div>
                                    <div class="how-ti-work-sli-text-block">
                                        <div class="how-ti-work-sli-left-count-wrapp">4</div>
                                        <div class="how-ti-work-sli-rihgt-text-wrapp">
                                            <span class="how-ti-work-sli-head-black">ORDER PICK UP</span>                        
                                        </div>
                                        <div class="clearfix"></div>
                                        <div class="how-ti-work-sli-phara" style="margin-top: 30px;">The confirmed driver will arrive at the pickup location. </div>
                                        <div class="how-ti-work-sli-second"><img src="{{url('/images/how-it-works-slider-img-m-4.png')}}" alt="how-it-work-slider-inner-img-second" /></div>
                                    </div>
                                </div>
                                <div class="item">
                                    <div class="how-ti-work-sli-img-block">
                                        <div class="how-ti-work-sli-first"> <img src="{{url('/images/how-it-works-slider-img-5.png')}}" alt="how-it-work-slider-inner-img-1" /></div>
                                    </div>
                                    <div class="how-ti-work-sli-text-block">
                                        <div class="how-ti-work-sli-left-count-wrapp">5</div>
                                        <div class="how-ti-work-sli-rihgt-text-wrapp">
                                            <span class="how-ti-work-sli-head-black">ORDER</span>           
                                            <span class="how-ti-work-sli-head-white"> DROP-OFF</span>
                                        </div>
                                        <div class="clearfix"></div>
                                        <div class="how-ti-work-sli-phara" style="margin-top: 30px;">Your order will arrive at the destined location. *Curbside delivery only </div>
                                        <div class="how-ti-work-sli-second"><img src="{{url('/images/how-it-works-slider-img-m-5.png')}}" alt="how-it-work-slider-inner-img-second" /></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Left and right controls -->
                            <a class="left carousel-control" href="#myCarousel" data-slide="prev">
                        <span class="glyphicon"></span>
                        <span class="sr-only">Previous</span>
                      </a>
                            <a class="right carousel-control" href="#myCarousel" data-slide="next">
                        <span class="glyphicon"></span>
                        <span class="sr-only">Next</span>
                      </a>
                        </div>
                        <!--</div>-->
                    </div>
                </div>
                <!--tab-2 start-->
                <div>
                    <div class="how-ti-work-slider" style="margin-top: 20px;">
                        <!--<div class="container">-->
                        <div id="myCarousel-two" class="carousel slide" data-ride="carousel">
                            <!-- Indicators -->
                            <ol class="carousel-indicators">
                                <li data-target="#myCarousel-two" data-slide-to="0" class="active"></li>
                                <li data-target="#myCarousel-two" data-slide-to="1"></li>
                                <li data-target="#myCarousel-two" data-slide-to="2"></li>
                                <li data-target="#myCarousel-two" data-slide-to="3"></li>
                                <li data-target="#myCarousel-two" data-slide-to="4"></li>
                            </ol>

                            <!-- Wrapper for slides -->
                            <div class="carousel-inner">
                                <div class="item active">
                                    <div class="how-ti-work-sli-img-block">
                                        <div class="how-ti-work-sli-first"><img src="{{url('/images/how-it-works-slider-img-driver-1.png')}}" alt="how-it-work-slider-inner-img-1" /></div>
                                    </div>
                                    <div class="how-ti-work-sli-text-block">
                                        <div class="how-ti-work-sli-righ-left-wrapper">
                                            <div class="how-ti-work-sli-left-count-wrapp">1</div>
                                            <div class="how-ti-work-sli-rihgt-text-wrapp">
                                                <span class="how-ti-work-sli-head-white">Apply </span>
                                                <span class="how-ti-work-sli-head-black">Online</span>
                                            </div>
                                            <div class="clearfix"></div>
                                        </div>
                                        <div class="how-ti-work-sli-phara" style="margin-top: 30px;">Fill the form and upload the requested documents. </div>
                                       <div class="how-ti-work-sli-second"><img src="{{url('/images/how-it-works-slider-img-driver-m-1.png')}}" alt="how-it-work-slider-inner-img-second" /></div>
                                    </div>
                                </div>

                                <div class="item">
                                    <div class="how-ti-work-sli-img-block">
                                        <div class="how-ti-work-sli-first"> <img src="{{url('/images/how-it-works-slider-img-driver-2.png')}}" alt="how-it-work-slider-inner-img-1" /></div>
                                    </div>
                                    <div class="how-ti-work-sli-text-block">
                                        <div class="how-ti-work-sli-righ-left-wrapper">
                                            <div class="how-ti-work-sli-left-count-wrapp">2</div>
                                            <div class="how-ti-work-sli-rihgt-text-wrapp">
                                                <span class="how-ti-work-sli-head-white">RECEIVE</span>
                                                <span class="how-ti-work-sli-head-black"> CONFIRMATION </span>                                                
                                            </div>
                                            <div class="clearfix"></div>
                                        </div>
                                        <div class="how-ti-work-sli-phara" style="margin-top: 30px;">QuickPick team will get in touch with you to confirm or request additional information. </div>
                                        <div class="how-ti-work-sli-second"><img src="{{url('/images/how-it-works-slider-img-driver-m-2.png')}}" alt="how-it-work-slider-inner-img-second" /></div>
                                    </div>
                                </div>

                                <div class="item">
                                    <div class="how-ti-work-sli-img-block">
                                        <div class="how-ti-work-sli-first"> <img src="{{url('/images/how-it-works-slider-img-driver-3.png')}}" alt="how-it-work-slider-inner-img-1" /></div>
                                    </div>
                                    <div class="how-ti-work-sli-text-block">
                                        <div class="how-ti-work-sli-righ-left-wrapper">
                                            <div class="how-ti-work-sli-left-count-wrapp">3</div>
                                            <div class="how-ti-work-sli-rihgt-text-wrapp">
                                                <span class="how-ti-work-sli-head-black">HIT THE ROAD</span>                                                
                                            </div>
                                            <div class="clearfix"></div>
                                        </div>
                                        <div class="how-ti-work-sli-phara" style="margin-top: 30px;">Make sure you have the app, be signed in and allow notifications on your phone.</div>
                                        <div class="how-ti-work-sli-second"><img src="{{url('/images/how-it-works-slider-img-driver-m-3.png')}}" alt="how-it-work-slider-inner-img-second" /></div>
                                    </div>
                                </div>
                                <div class="item">
                                    <div class="how-ti-work-sli-img-block">
                                        <div class="how-ti-work-sli-first"> <img src="{{url('/images/how-it-works-slider-img-driver-4.png')}}" alt="how-it-work-slider-inner-img-1" /></div>
                                    </div>
                                    <div class="how-ti-work-sli-text-block">
                                        <div class="how-ti-work-sli-left-count-wrapp">4</div>
                                        <div class="how-ti-work-sli-rihgt-text-wrapp">
                                            <span class="how-ti-work-sli-head-black">ACCEPT  </span>  
                                            <span class="how-ti-work-sli-head-white">DELIVERY REQUEST</span>                      
                                        </div>
                                        <div class="clearfix"></div>
                                        <div class="how-ti-work-sli-phara" style="margin-top: 30px;">Pickup and drop-off the delivery. Have QuickPick app online all the time. 
</div>
                                        <div class="how-ti-work-sli-second"><img src="{{url('/images/how-it-works-slider-img-driver-m-4.png')}}" alt="how-it-work-slider-inner-img-second" /></div>
                                    </div>
                                </div>
                                <div class="item">
                                    <div class="how-ti-work-sli-img-block">
                                        <div class="how-ti-work-sli-first"> <img src="{{url('/images/how-it-works-slider-img-driver-5.png')}}" alt="how-it-work-slider-inner-img-1" /></div>
                                    </div>
                                    <div class="how-ti-work-sli-text-block">
                                        <div class="how-ti-work-sli-left-count-wrapp">5</div>
                                        <div class="how-ti-work-sli-rihgt-text-wrapp">
                                            <span class="how-ti-work-sli-head-black">GET PAID </span>                                                       
                                        </div>
                                        <div class="clearfix"></div>
                                        <div class="how-ti-work-sli-phara" style="margin-top: 30px;">You will be paid for your trip via stripe</div>
                                        <div class="how-ti-work-sli-second"><img src="{{url('/images/how-it-works-slider-img-driver-m-5.png')}}" alt="how-it-work-slider-inner-img-second" /></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Left and right controls -->
                            <a class="left carousel-control" href="#myCarousel-two" data-slide="prev">
                        <span class="glyphicon"></span>
                        <span class="sr-only">Previous</span>
                      </a>
                            <a class="right carousel-control" href="#myCarousel-two" data-slide="next">
                        <span class="glyphicon"></span>
                        <span class="sr-only">Next</span>
                      </a>
                        </div>
                        <!--</div>-->
                    </div>                    
                </div>
            </div>
        </div>
    </div>
    <!--</div>-->
</div>

<script>
    // Activate Carousel
//$("#myCarousel-two").carousel();
</script>

<!--tabing-css-js-start-here-->
<script src="{{ url('/')}}/js/front/easyResponsiveTabs.js" type="text/javascript"></script>
<script>
    //<!--tab js script-->  
    $('#horizontalTab').easyResponsiveTabs({
        type: 'default',
        width: 'auto',
        fit: true,
        closed: 'accordion',
        activate: function(event) {
            var $tab = $(this);
            var $info = $('#tabInfo');
            var $name = $('span', $info);

            $name.text($tab.text());

            $info.show();
        }
    });
</script>
@stop