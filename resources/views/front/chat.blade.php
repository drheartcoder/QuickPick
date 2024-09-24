@extends('front.layout.master')                

@section('main_content')

    <div class="blank-div"></div>
     <div class="email-block">
        <div class="container">
            <div class="row">
                <div class="col-sm-12 col-md-12 col-lg-12">
                    <div class="headding-text-bredcr">
                       Message
                    </div>
                </div>
                <div class="col-sm-6 col-md-6 col-lg-6">
                    <div class="bredcrum-right">
                        <a href="" class="bredcrum-home"> Dashboard </a>
                        <span class="arrow-righ"><i class="fa fa-angle-right"></i></span>
                        Message
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--dashboard page start-->
    <div class="main-wrapper">
        <div class="container">
            <div class="row">
                @include('front.user.left_bar')
                <div class="col-sm-9 col-md-9 col-lg-9">
                    <div class="dash-white-main massage">
                                <div data-responsive-tabs class="verticalslide">
                                     <nav>
                                        <div class="search-member-block">
                                            <input type="text" name="Search" placeholder="Search" />
                                            <button class="message-search-btn" type="submit"> </button>
                                        </div>
                                        <div class="users-block content-d">
                                            <ul>
                                                <li class="active">
                                                    <a href="#tabone"><span class="travles-img"><img src="{{url('images/avrt1.png')}}" alt="avrt1" /> </span> <span class="travles-name">Hays Travel Ltd.</span></a>
                                                </li>
                                                <li>
                                                    <a href="#tabtwo"><span class="travles-img"><img src="{{url('images/avrt1.png')}}" alt="avrt1" /></span> <span class="travles-name">Azumano Travels</span></a>
                                                </li>
                                                <li>
                                                    <a href="#tabthree"><span class="travles-img"><img src="{{url('images/avrt1.png')}}" alt="avrt1" /></span> <span class="travles-name">BCD Travel</span></a>
                                                </li>	
                                                <li>
                                                    <a href="#tabfour"><span class="travles-img"><img src="{{url('images/avrt1.png')}}" alt="avrt1" /></span>  <span class="travles-name">Hays Travel Ltd.</span></a>
                                                </li>
                                                <li>
                                                    <a href="#tabfive"><span class="travles-img"><img src="{{url('images/avrt1.png')}}" alt="avrt1" /></span> <span class="travles-name">Azumano Travels</span></a>
                                                </li>
                                                <li>
                                                    <a href="#tabsix"><span class="travles-img"><img src="{{url('images/avrt1.png')}}" alt="avrt1" /></span>  <span class="travles-name">BCD Travel</span></a>
                                                </li>
                                              <li>
                                                  <a href="#tabseven"><span class="travles-img"><img src="{{url('images/avrt1.png')}}" alt="avrt1" /></span> <span class="travles-name">BCD Travel</span></a>
                                                </li>                                                
                                            </ul>
                                        </div>
                                    </nav>
                                    <div class="chat-travels-name">
                                        Thomas Edword
                                    </div>
                                    <div class="content">
                                        <section id="tabone">
                                            <div class="messages-section content-d">
                                                <div class="left-message-block">
                                                    <div class="left-message-profile">
                                                        <img src="{{url('images/dash-profile-img.jpg')}}" alt="dash-profile-img" />
                                                    </div>
                                                    <div class="left-message-content">
                                                        <div class="actual-message">
                                                            Nanti  kita technical meeting lomb..
                                                        </div>
                                                        <div class="message-time">
                                                            03 Jan, 12:30 am
                                                        </div>
                                                    </div>                                        
                                                </div>
                                                <div class="left-message-block right-message-block">
                                                    <div class="left-message-profile">
                                                       <img src="{{url('images/review-img2.png')}}" alt="review-img2" />
                                                    </div>
                                                    <div class="left-message-content">
                                                        <div class="actual-message">
                                                            Semua satu team ITone yang berangkat ke ja?
                                                        </div>
                                                        <div class="message-time">
                                                            03 Jan, 12:30 am
                                                        </div>
                                                    </div>                                        
                                                </div>
                                                <div class="left-message-block">
                                                    <div class="left-message-profile">
                                                        <img src="{{url('images/dash-profile-img.jpg')}}" alt="dash-profile-img" />
                                                    </div>
                                                    <div class="left-message-content">
                                                        <div class="actual-message">
                                                            Nanti  kita technical meeting lomb..
                                                        </div>
                                                        <div class="message-time">
                                                            03 Jan, 12:30 am
                                                        </div>
                                                    </div>                                        
                                                </div>
                                                <div class="left-message-block right-message-block">
                                                    <div class="left-message-profile">
                                                       <img src="{{url('images/review-img2.png')}}" alt="review-img2" />
                                                    </div>
                                                    <div class="left-message-content">
                                                         <div class="actual-message">
                                                            Semua satu team ITone yang berangkat ke ja?
                                                        </div>
                                                        <div class="message-time">
                                                            03 Jan, 12:30 am
                                                        </div>
                                                    </div>                                        
                                                </div>   
                                                <div class="left-message-block">
                                                    <div class="left-message-profile">
                                                        <img src="{{url('images/dash-profile-img.jpg')}}" alt="dash-profile-img" />
                                                    </div>
                                                    <div class="left-message-content">
                                                        <div class="actual-message">
                                                            Nanti  kita technical meeting lomb..
                                                        </div>
                                                        <div class="message-time">
                                                            03 Jan, 12:30 am
                                                        </div>
                                                    </div>                                        
                                                </div>
                                                <div class="left-message-block right-message-block">
                                                    <div class="left-message-profile">
                                                        <img src="{{url('images/review-img2.png')}}" alt="review-img2" />
                                                    </div>
                                                    <div class="left-message-content">
                                                        <div class="actual-message">
                                                            Semua satu team ITone yang berangkat ke ja?
                                                        </div>
                                                        <div class="message-time">
                                                            03 Jan, 12:30 am
                                                        </div>
                                                    </div>                                        
                                                </div>   
                                                <div class="left-message-block">
                                                    <div class="left-message-profile">
                                                        <img src="{{url('images/dash-profile-img.jpg')}}" alt="dash-profile-img" />
                                                    </div>
                                                    <div class="left-message-content">
                                                        <div class="actual-message">
                                                            Nanti  kita technical meeting lomb..
                                                        </div>
                                                        <div class="message-time">
                                                            03 Jan, 12:30 am
                                                        </div>
                                                    </div>                                        
                                                </div>
                                                <div class="left-message-block right-message-block">
                                                    <div class="left-message-profile">
                                                        <img src="{{url('images/review-img2.png')}}" alt="review-img2" />
                                                    </div>
                                                    <div class="left-message-content">
                                                        <div class="actual-message">
                                                            Semua satu team ITone yang berangkat ke ja?
                                                        </div>
                                                        <div class="message-time">
                                                            03 Jan, 12:30 am
                                                        </div>
                                                    </div>                                        
                                                </div>                                                   
                                            </div>
                                            <div class="write-message-block">
                                                <input type="text" name="write a replay" placeholder="Write a Reply..." />                                        
                                                <button class="send-message-btn" type="submit"><i class="fa fa-paper-plane"></i></button>
                                            </div>
                                        </section>
                                        <section id="tabtwo">
                                           <div class="messages-section content-d">
                                                <div class="left-message-block">
                                                    <div class="left-message-profile">
                                                        <img src="images/user-profile-img.jpg" alt="" />
                                                    </div>
                                                    <div class="left-message-content">
                                                        <div class="actual-message">
                                                            Nanti  kita technical meeting lomb..
                                                        </div>
                                                        <div class="message-time">
                                                            03 Jan, 12:30 am
                                                        </div>
                                                    </div>                                        
                                                </div>
                                                <div class="left-message-block right-message-block">
                                                    <div class="left-message-profile">
                                                        <img src="images/user-profile-img.jpg" alt="" />
                                                    </div>
                                                    <div class="left-message-content">
                                                        <div class="actual-message">
                                                            Semua satu team ITone yang berangkat ke ja?
                                                        </div>
                                                        <div class="message-time">
                                                            03 Jan, 12:30 am
                                                        </div>
                                                    </div>                                        
                                                </div>
                                                <div class="left-message-block">
                                                    <div class="left-message-profile">
                                                        <img src="images/user-profile-img.jpg" alt="" />
                                                    </div>
                                                    <div class="left-message-content">
                                                        <div class="actual-message">
                                                            Nanti  kita technical meeting lomb..
                                                        </div>
                                                        <div class="message-time">
                                                            03 Jan, 12:30 am
                                                        </div>
                                                    </div>                                        
                                                </div>
                                                <div class="left-message-block right-message-block">
                                                    <div class="left-message-profile">
                                                        <img src="images/user-profile-img.jpg" alt="" />
                                                    </div>
                                                    <div class="left-message-content">
                                                         <div class="actual-message">
                                                            Semua satu team ITone yang berangkat ke ja?
                                                        </div>
                                                        <div class="message-time">
                                                            03 Jan, 12:30 am
                                                        </div>
                                                    </div>                                        
                                                </div>   
                                                <div class="left-message-block">
                                                    <div class="left-message-profile">
                                                        <img src="images/user-profile-img.jpg" alt="" />
                                                    </div>
                                                    <div class="left-message-content">
                                                        <div class="actual-message">
                                                            Nanti  kita technical meeting lomb..
                                                        </div>
                                                        <div class="message-time">
                                                            03 Jan, 12:30 am
                                                        </div>
                                                    </div>                                        
                                                </div>
                                             <div class="write-message-block">
                                                <input type="text" name="write a replay" placeholder="Write a Reply..." />                                        
                                                <button class="send-message-btn" type="submit"><i class="fa fa-paper-plane"></i></button>
                                            </div>
                                            </section>
                                        <section id="tabthree">
                                            <div class="messages-section content-d">
                                                <div class="left-message-block">
                                                    <div class="left-message-profile">
                                                        <img src="images/user-profile-img.jpg" alt="" />
                                                    </div>
                                                    <div class="left-message-content">
                                                        <div class="actual-message">
                                                            Nanti  kita technical meeting lomb..
                                                        </div>
                                                        <div class="message-time">
                                                            03 Jan, 12:30 am
                                                        </div>
                                                    </div>                                        
                                                </div>
                                                <div class="left-message-block right-message-block">
                                                    <div class="left-message-profile">
                                                        <img src="images/user-profile-img.jpg" alt="" />
                                                    </div>
                                                    <div class="left-message-content">
                                                        <div class="actual-message">
                                                            Semua satu team ITone yang berangkat ke ja?
                                                        </div>
                                                        <div class="message-time">
                                                            03 Jan, 12:30 am
                                                        </div>
                                                    </div>                                        
                                                </div>
                                                <div class="write-message-block">
                                                <input type="text" name="write a replay" placeholder="Write a Reply..." />                                        
                                                <button class="send-message-btn" type="submit"><i class="fa fa-paper-plane"></i></button>
                                            </div>
                                            </section>
                                            
                                        <section id="tabfour">
                                          <div class="messages-section content-d">
                                                <div class="left-message-block">
                                                    <div class="left-message-profile">
                                                        <img src="images/user-profile-img.jpg" alt="" />
                                                    </div>
                                                    <div class="left-message-content">
                                                        <div class="actual-message">
                                                            Nanti  kita technical meeting lomb..
                                                        </div>
                                                        <div class="message-time">
                                                            03 Jan, 12:30 am
                                                        </div>
                                                    </div>                                        
                                                </div>
                                                <div class="left-message-block right-message-block">
                                                    <div class="left-message-profile">
                                                        <img src="images/user-profile-img.jpg" alt="" />
                                                    </div>
                                                    <div class="left-message-content">
                                                        <div class="actual-message">
                                                            Semua satu team ITone yang berangkat ke ja?
                                                        </div>
                                                        <div class="message-time">
                                                            03 Jan, 12:30 am
                                                        </div>
                                                    </div>                                        
                                                </div>
                                                <div class="left-message-block">
                                                    <div class="left-message-profile">
                                                        <img src="images/user-profile-img.jpg" alt="" />
                                                    </div>
                                                    <div class="left-message-content">
                                                        <div class="actual-message">
                                                            Nanti  kita technical meeting lomb..
                                                        </div>
                                                        <div class="message-time">
                                                            03 Jan, 12:30 am
                                                        </div>
                                                    </div>                                        
                                                </div>
                                                <div class="left-message-block right-message-block">
                                                    <div class="left-message-profile">
                                                        <img src="images/user-profile-img.jpg" alt="" />
                                                    </div>
                                                    <div class="left-message-content">
                                                         <div class="actual-message">
                                                            Semua satu team ITone yang berangkat ke ja?
                                                        </div>
                                                        <div class="message-time">
                                                            03 Jan, 12:30 am
                                                        </div>
                                                    </div>                                        
                                                </div>   
                                                <div class="left-message-block">
                                                    <div class="left-message-profile">
                                                        <img src="images/user-profile-img.jpg" alt="" />
                                                    </div>
                                                    <div class="left-message-content">
                                                        <div class="actual-message">
                                                            Nanti  kita technical meeting lomb..
                                                        </div>
                                                        <div class="message-time">
                                                            03 Jan, 12:30 am
                                                        </div>
                                                    </div>                                        
                                                </div>
                                             <div class="write-message-block">
                                                <input type="text" name="write a replay" placeholder="Write a Reply..." />                                        
                                                <button class="send-message-btn" type="submit"><i class="fa fa-paper-plane"></i></button>
                                            </div>
                                            </section>	
                                        <section id="tabfive">
                                            <h3>Section 5</h3>
                                            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Tenetur deleniti, odio quibusdam laboriosam cupiditate quo repellendus iure optio ea maiores tempore voluptatibus omnis temporibus nemo, a natus repudiandae nulla excepturi.</p>
                                        </section>	
                                        <section id="tabsix">
                                            <h3>Section 6</h3>
                                            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Tenetur deleniti, odio quibusdam laboriosam cupiditate quo repellendus iure optio ea maiores tempore voluptatibus omnis temporibus nemo, a natus repudiandae nulla excepturi.</p>
                                        </section>	
                                        <section id="tabseven">
                                            <h3>Section 7</h3>
                                            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Tenetur deleniti, odio quibusdam laboriosam cupiditate quo repellendus iure optio ea maiores tempore voluptatibus omnis temporibus nemo, a natus repudiandae nulla excepturi.</p>
                                        </section>	
                                     </div>
                                    <div class="clr"></div>
                                </div>
                            </div>
                    
                </div>

            </div>
        </div>
    </div>
    
    <!-- custom scrollbars plugin -->
    <link href="{{url('/css/front/jquery.mCustomScrollbar.css')}}" rel="stylesheet" />
    
     <!-- custom scrollbar plugin -->
    <script type="text/javascript" src="{{url('/js/front/jquery.mCustomScrollbar.concat.min.js')}}"></script>
    <script type="text/javascript">
        /*scrollbar start*/
        (function($){
            $(window).on("load",function(){
            $.mCustomScrollbar.defaults.scrollButtons.enable=true; //enable scrolling buttons by default
            $.mCustomScrollbar.defaults.axis="yx"; //enable 2 axis scrollbars by default
                $(".content-d").mCustomScrollbar({theme:"dark"});
            });
        })(jQuery);
    </script>
    
    <!--tabbing js start here -->
    <script src="{{url('/js/front/responsivetabs.js')}}"></script> 
    
    <script>
        $(document).ready(function ()
        {	
            $(document).on('responsive-tabs.initialised', function (event, el)
            {
                console.log(el);
            });

            $(document).on('responsive-tabs.change', function (event, el, newPanel)
            {
                console.log(el);
                console.log(newPanel);
            });

            $('[data-responsive-tabs]').responsivetabs(
            {
                initialised : function ()
                {
                    console.log(this);
                },

                change : function (newPanel)
                {
                    console.log(newPanel);
                }
            });


        });
    </script>     


@stop
