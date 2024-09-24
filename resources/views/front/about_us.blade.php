

<!-- <div class="show-notification">
    <span>
        <img src="{{ url('/')}}/images/notification-warning.png" alt="notification warning" />
        "Some features may not work properly if you don't allow the notification access."
    </span>
    
</div>
 -->

@extends('front.layout.master')  

@section('main_content')

{{-- {!! isset($page_details->page_desc) ? $page_details->page_desc : '' !!} --}}

<div class="blank-div"></div>

<div class="email-block change about">
    <div class="container">
        <div class="row">
            <div class="col-sm-12 col-md-12 col-lg-12">
                <div class="headding-text-bredcr">
                    About <span>Us</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="about-content-block">
      <div class="container">
         <div class="about-content-short-wrappper">  
            <div class="about-head-left-block">
                <div class="about-head-left">Our <span>History</span></div>
            </div>
            <div class="about-conten-right-block">
                <div class="about-content-phara">QuickPick, was founded in 2017 in Lorton, Virginia. The company specializes in on demand delivery of construction material in Northern Virginia, Washington D.C., and Maryland. </div>
                <div class="about-content-phara">We have been in the construction business for a combination of almost 40 years and understand how the wrong or missing material on the jobsite can cost you valuable man hours and cost you precious budget dollars. Or worse, you are the last stop on your supplier's delivery manifest which means you receive material at the end of the day and pay your crew to wait on the jobsite.</div>
                <div class="about-content-phara no-mar">Hence QuickPick was born to get you the material you need on site in an on-demand fashion. Typically, our drivers deliver your materials in the Metro DC in 1-5 hours saving you time and money.</div>
            
            </div>
          </div>
      </div>
</div>

<div class="about-content-block our-vision">
      <div class="container">
         <div class="about-content-short-wrappper">  
            <div class="about-head-left-block">
                <div class="about-head-left">Our <span>Vision</span></div>
            </div>
            <div class="about-conten-right-block">
                <div class="about-content-phara no-mar">Our vision is to provide our customers with a premier delivery service that saves you time and money.</div>
            </div>
          </div>
      </div>
</div>

<div class="about-content-block our-mission">
      <div class="container">
         <div class="about-content-short-wrappper">  
            <div class="about-head-left-block">
                <div class="about-head-left">Our <span>Mission</span></div>
            </div>
            <div class="about-conten-right-block">
                <div class="about-content-phara no-mar">Our mission: on time, efficient deliveries for anyone needing to get material fast!</div>
            </div>
          </div>
      </div>
</div>


<div class="about-help-bottom">
<div class="container">
<div class="help-bottom-blue-wrapper">
        <div class="row">
                            <div class="col-sm-12 col-md-4 col-lg-4">
                                <div class="get-help-txt">
                                    <div class="cin-helps-hml"><img src="{{ url('/')}}/images/phone-icn-smart.png" alt="phone-icn-smart" /></div>
                                        <div class="cin-helps-text">
                                            <div class="txt-appp">Get help in the app</div>
                                            <p>Just head to "HELP" in the Quick Pick app navigation.</p>
                                        </div>
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-4 col-lg-4">
                                <div class="get-help-txt right">
                                    <div class="cin-helps-hml"><i class="fa fa-twitter"></i></div>
                                        <div class="cin-helps-text">
                                            <div class="txt-appp">Follow us on Twitter</div>
                                            <p>Get the latest status on the app, or tweet to us with any questions you have.</p>
                                            <div class="morelinks">
                                        <a href="JavaScript:Void(0);">Follow @Quickpick_Support <!--<i class="fa fa-angle-right"></i>--></a>
                                    </div>
                                        </div>
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-4 col-lg-4">
                                <div class="get-help-txt right">
                                    <div class="cin-helps-hml"><img src="{{ url('/')}}/images/floow-us-truck.png" alt="floow-us-truck" /></div>
                                        <div class="cin-helps-text">
                                            <div class="txt-appp">JOIN OUR TEAM</div>
                                            <p>We are looking for Drivers who knows the Washington, D.C area</p>
                                        </div>
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        </div>
    </div>
</div>
</div>
@stop