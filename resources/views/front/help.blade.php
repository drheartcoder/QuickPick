@extends('front.layout.master')                

@section('main_content')
    <div class="blank-div"></div>
    
    <div class="email-block change">
    <div class="container">
        <div class="row">
            <div class="col-sm-12 col-md-12 col-lg-12">
                <div class="headding-text-bredcr">
                    Help
                </div>
            </div>
        </div>
    </div>
</div>

<!--support section start-->
<div class="help-content-block">
<div class="support-section fot-about-us help-main">
   <div class="container-fluid">
      <div class="row">
          <div class="col-md-3 col-lg-2 leftSidebar">
             <div class="theiaStickySidebar">
              <div class="help-container left-shidebaaar">
                 
                  <ul class="tab-links">
                      <li @if(isset($selected_tab) && $selected_tab == 'user') class="current" @endif ><a href="{{url('help?type=user')}}">For Users</a></li>
                      <li @if(isset($selected_tab) && $selected_tab == 'driver') class="current" @endif><a href="{{url('help?type=driver')}}">For Drivers</a></li>
                  </ul>
                 </div>
              </div>
          </div>
          <div class="col-md-9 col-lg-10 content-help">
             
                <div class="help-container-inner-righ">
                    
                    @if(isset($selected_tab) && $selected_tab == 'user')

                        <div  class="help-container tab-contents current">
                            <div class="row-help">
                                <div class="col-sm-6 col-md-6 col-lg-4">
                                    <div class="icon-help-page-box"><img src="{{ url('/')}}/images/help-icon-1-copy.png" alt="help-icon-1-copy." /></div>
                                    <div class="content-help-page-box">
                                        <h3>Account and Payment Options</h3>
                                        <ul>
                                            <li class="title">How do I sign up for QuickPick?</li>
                                            <li>If your business needs access to flexible same-day deliveries it’s easy to sign up as a QuickPick User.</li>
                                        </ul>
                                        <div class="morelinks">
                                            <a href="{{ url('help_details?slug=user-account-and-payment&selected_tab='.$selected_tab) }}">More <i class="fa fa-angle-right"></i><i class="fa fa-angle-right"></i></a>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-sm-6 col-md-6 col-lg-4">
                                    <div class="icon-help-page-box"><img src="{{ url('/')}}/images/help-icon-2.png" alt="help-icon-2" /></div>
                                    <div class="content-help-page-box">
                                        <h3>A Guide to Quick Pick</h3>
                                        <ul>
                                            <li class="title">What information will I need to create a User account?</li>
                                            <li>As a User, you will be able to place delivery orders with QuickPick.</li>
                                        </ul>
                                        <div class="morelinks">
                                            <a href="{{ url('help_details?slug=user-quick-pick-guide&selected_tab='.$selected_tab) }}">More <i class="fa fa-angle-right"></i><i class="fa fa-angle-right"></i></a>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-sm-6 col-md-6 col-lg-4">
                                    <div class="icon-help-page-box"><img src="{{ url('/')}}/images/help-icon-3.png" alt="help-icon-3" /></div>
                                    <div class="content-help-page-box">
                                        <h3>Signing Up</h3>
                                        <ul>
                                            <li class="title">What are the requirements to sign up?</li>
                                            <li>To sign up as a User,all we need is your basic contact information</li>
                                        </ul>
                                        <div class="morelinks">
                                            <a href="{{ url('help_details?slug=user-signing-up&selected_tab='.$selected_tab) }}">More <i class="fa fa-angle-right"></i><i class="fa fa-angle-right"></i></a>
                                        </div>
                                    </div>
                                </div>
    
                                <div class="col-sm-6 col-md-6 col-lg-4">
                                    <div class="icon-help-page-box"><img src="{{ url('/')}}/images/help-icon-5.png" alt="help-icon-5" /></div>
                                    <div class="content-help-page-box">
                                        <h3>Accessibility</h3>
                                        <ul>
                                            <li class="title">What if I need help using the app or the site?</li>
                                            <li>QuickPick recommends that people who</li>
                                            <li>have trouble interacting with our products use whatever assistance they need.</li>
                                         </ul>
                                        <div class="morelinks">
                                            <a href="{{ url('help_details?slug=user-accessibility&selected_tab='.$selected_tab) }}">More <i class="fa fa-angle-right"></i><i class="fa fa-angle-right"></i></a>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-6 col-md-6 col-lg-4">
                                    <div class="icon-help-page-box"><img src="{{ url('/')}}/images/help-icon-6.png" alt="help-icon-6" /></div>
                                    <div class="content-help-page-box">
                                        <h3>Frequently Asked Questions and Concerns</h3>
                                        <ul>
                                            <li class="title">What if my Driver is late/doesn’t show up?</li>

                                            <li>Drivers who know they will be late</li>
                                            <li>have a responsibility to update the app with the nature and expected length of their delay.</li>
                                         </ul>
                                        <div class="morelinks">
                                            <a href="{{ url('help_details?slug=user-faq&selected_tab='.$selected_tab) }}">More <i class="fa fa-angle-right"></i><i class="fa fa-angle-right"></i></a>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            
                        </div>
                    
                    @endif

                    @if(isset($selected_tab) && $selected_tab == 'driver')

                       <div class="help-container tab-contents current">
                          <div class="rows">
                                <div class="col-sm-6 col-md-6 col-lg-4">
                                    <div class="icon-help-page-box"><img src="{{ url('/')}}/images/help-icon-1-copy.png" alt="help-icon-1-copy" /></div>
                                    <div class="content-help-page-box">
                                        <h3>Account and Payment Options</h3>
                                        <ul>
                                            <li class="title">How much does QuickPick pay?</li>
                                            <li>We pride ourselves on offering a competitive per-delivery rate to Drivers</li>
                                        </ul>
                                        <div class="morelinks">
                                            <a href="{{ url('help_details?slug=driver-account-and-payment&selected_tab='.$selected_tab) }}">More <i class="fa fa-angle-right"></i><i class="fa fa-angle-right"></i></a>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-sm-6 col-md-6 col-lg-4">
                                    <div class="icon-help-page-box"><img src="{{ url('/')}}/images/help-icon-5.png" alt="help-icon-5" /></div>
                                    <div class="content-help-page-box">
                                        <h3>Accessibility</h3>
                                        <ul>
                                            <li class="title">I have a disability. Can I still deliver with QuickPick?</li>
                                            <li>Yes! In keeping with the ruling of the Fifth Circuit Court of Appeals in Flynn vs.</li>
                                         </ul>
                                        <div class="morelinks">
                                            <a href="{{ url('help_details?slug=driver-accessibility&selected_tab='.$selected_tab) }}">More <i class="fa fa-angle-right"></i><i class="fa fa-angle-right"></i></a>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-6 col-md-6 col-lg-4">
                                    <div class="icon-help-page-box"><img src="{{ url('/')}}/images/help-icon-2.png" alt="help-icon-3" /></div>
                                    <div class="content-help-page-box">
                                        <h3>A Guide to Quick Pick</h3>
                                        <ul>
                                            <li class="title">What exactly does QuickPick do?</li>
                                            <li>We contract with Drivers to serve the same-day needs of local businesses</li>
                                        </ul>
                                        <div class="morelinks">
                                            <a href="{{ url('help_details?slug=driver-quick-pick-guide&selected_tab='.$selected_tab) }}">More <i class="fa fa-angle-right"></i><i class="fa fa-angle-right"></i></a>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-sm-6 col-md-6 col-lg-4">
                                    <div class="icon-help-page-box"><img src="{{ url('/')}}/images/help-icon-6.png" alt="help-icon-6" /></div>
                                    <div class="content-help-page-box">
                                        <h3>Frequently Asked Questions and Concerns</h3>
                                        <ul>
                                            <li class="title">What types of loads will I ship?</li>

                                            <li>As a Driver, you will ship most types of ordinary supplies our Users might need.</li>
                                        </ul>
                                        <div class="morelinks">
                                            <a href="{{ url('help_details?slug=driver-faq&selected_tab='.$selected_tab) }}">More <i class="fa fa-angle-right"></i><i class="fa fa-angle-right"></i></a>
                                        </div>
                                    </div>
                                </div>
     
                            </div>
                            
                       </div>

                    @endif

              </div>                          
          </div>
      </div>
       
    </div>
</div>
</div>

<div class="help-bottom-blue-wrapper">
        <div class="row">
            <div class="col-sm-12 col-md-6 col-lg-6">
                <div class="get-help-txt">
                    <div class="cin-helps-hml"><img src="{{ url('/')}}/images/phone-icn-smart.png" alt="" /></div>
                        <div class="cin-helps-text">
                            <div class="txt-appp">Get help in the app</div>
                            <p>Just head to "HELP" in the Quick Pick app navigation.</p>
                        </div>
                    <div class="clearfix"></div>
                </div>
            </div>
            <div class="col-sm-12 col-md-6 col-lg-6">
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
        </div>
    </div> 

 <script type="text/javascript" src="{{ url('/')}}/js/front/theia-sticky-sidebar.js"></script>
 <script type="text/javascript">
            // $(document).ready(function() {
            //     $('.leftSidebar, .content-help')
            //         .theiaStickySidebar({
            //             additionalMarginTop: 100
            //         });
            // });
        </script>
 
 
 <script>
// $(document).ready(function(){
    
//     $('ul.tab-links li').click(function(){
//         var tab_id = $(this).attr('data-tab');

//         $('ul.tab-links li').removeClass('current');
//         $('.tab-contents').removeClass('current');

//         $(this).addClass('current');
//         $("#"+tab_id).addClass('current');
//     })

// })    
</script>
 
@stop