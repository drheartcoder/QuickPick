 <?php  
      
      $is_valid_user_login = false;

      $user_login = $driver_login = $enterprise_admin_login = false;
      
      $login_user_id = 0;

      $obj_user = Sentinel::check(); 

      if($obj_user){
        $login_user_id = isset($obj_user->id) ? $obj_user->id : 0;
        $user_type     = '';
        
        if($obj_user->inRole(config('app.project.role_slug.user_role_slug')) || $obj_user->inRole(config('app.project.role_slug.driver_role_slug')) || $obj_user->inRole(config('app.project.role_slug.enterprise_admin_role_slug')))
        {
          $is_valid_user_login = true;
        }
        if($obj_user->inRole(config('app.project.role_slug.user_role_slug')))
        {
          $user_login = true;
          $user_type  =  'user';
        }
        
        if($obj_user->inRole(config('app.project.role_slug.driver_role_slug')))
        {
          $driver_login = true;
          $user_type    = 'driver';
        }

        if($obj_user->inRole(config('app.project.role_slug.enterprise_admin_role_slug')))
        {
          $enterprise_admin_login = true;
          $user_type    = 'enterprise_admin';
        }
        
      }

    ?>

<!--<div class="main-banner-block">-->
<div class="header header-home">
<!--            <a href="javascript:void(0);" class="heads-link-none"><span class="menu-icon" onclick="return openNav();"> &#9776;</span></a>-->
            <div class="logo-block">
                <a href="{{url('/')}}">
                    <!--<img src="{{url('images/logo.png')}}" alt="" class="main-logo" />-->
                     <img src="{{url('images/logo.png')}}" alt="Quick-Pick" class="main-logo-inner" width="500px" height="127px"/>
                </a>
            </div>
            
            
            <a href="javascript:void(0)" class="menu-icon-line responsivemenus hidden-md hidden-lg" id="flipdashboard2"><i class="fa fa-bars"></i></a>
            <div class="hm-qucik-close mobile-show-section"> <button type="button" id="closeBtn" class="close paneldashboardclose" ></button> </div>
            <!--Menu Start-->
            <div class="menu-default">
                <ul>
                    @if(!$is_valid_user_login)
                      @if((!$user_login) || (!$driver_login))
                        <li><a href="{{url('/login')}}"><i class="fa fa-user"></i> Login</a></li>
                      @endif
                    @endif
                    <li><a href="{{url('/contact_us')}}"><i class="fa fa-envelope"></i> Contact Us</a></li>
                </ul>
            </div>
            
            <div id="mySidenav" class="sidenav">
            <div>
                <a href="{{url('/')}}" class="closebtn" onclick="closeNav()">&times;</a>
                <div class="banner-img-block" onclick="window.location.href='{{url('/')}}';">
                   <img src="{{url('images/logo.png')}}" alt="Logo" />
                   <!-- <div class="img-responsive-logo"></div>-->
                </div>

                <ul class="min-menu">
                      @if($is_valid_user_login)
                          @if($user_login) 
                            <li><a href="{{url('/user/dashboard')}}">Dashboard</a></li>
                          @elseif($driver_login) 
                            <li><a href="{{url('/driver/dashboard')}}">Dashboard</a></li>
                          @elseif($enterprise_admin_login) 
                            <li><a href="{{url('/enterprise_admin/dashboard')}}">Dashboard</a></li>
                          @endif
                     @else
                      <li><a href="{{url('/login')}}">Delivery</a></li>
                      <li><a href="{{url('/join_our_fleet')}}">Drive</a></li>
                     @endif
                      
                      <li><a href="{{url('/how_it_works')}}">How It Works</a></li>
                      <li><a href="{{url('/about_us')}}">About Us</a></li>
                     <!-- <li class="header-drive-signup"><a href="{{url('/join_our_fleet')}}">We are looking for drivers</a></li> -->
                </ul>
                
                <div class="clearfix"></div>
            </div>
         </div>
          
          @if($is_valid_user_login)
            @if((!$driver_login))
              <div class="header-drive-signup"><a href="{{url('/join_our_fleet')}}">We are looking for drivers</a></div>
            @endif
          @endif


            <div class="clearfix"></div>
              <div id="paneldashboard" class="paneldashboard">
                <div class="hm-qucik-close mobile-hide-section"> <button type="button" id="closeBtn" class="close paneldashboardclose" ></button> </div>
                <div class="ride-links-section">
                     <div class="container">
                         <div class="header-drop-search">
                             <input type="text" placeholder="Search" id="search_link" />
                             <div class="header-drop-search-icon"><i class="fa fa-search"></i></div>
                         </div>
                         
                          <div class="row">                      
                          <div class="col-sm-12 col-md-4 col-lg-4">
                              <div class="txt-contents-home drop">
                                  <div class="txt-contents-home-head">Drivers</div>
                                  <a class="mobile-view" href="{{url('/join_our_fleet')}}">Drive</a>
                                 <a href="{{url('how_it_works')}}">How it Works</a>
                                 <a href="{{url('our_fleet')}}">Our Fleet</a>
                                </div>
                          </div>
                          <div class="col-sm-12 col-md-4 col-lg-4">
                              <div class="txt-contents-home dro">
                                  <div class="txt-contents-home-head">Customers</div>
                                  <a class="mobile-view" href="{{url('/login')}}">Delivery</a>
                                 <a href="{{url('fare_estimate')}}">Fare Estimator</a>
                                 <a href="{{url('how_it_works')}}">How it Works</a>
                                 {{-- <a href="{{url('policy')}}">Privacy Policy</a> --}}
                                </div>
                          </div>
                          
                          <div class="col-sm-12 col-md-4 col-lg-4">
                              <div class="txt-contents-home dro">
                                 <div class="txt-contents-home-head">Quickpick General</div>
                                 <a href="{{url('about_us')}}"> About Us </a>
                                 <a href="{{url('help')}}">Help</a>
                                 <a href="{{url('/contact_us')}}">Contact Us</a>
                              </div>
                          </div>
                          
                          
                          <div class="clearfix"></div>
                          <div class="bottom-section-menu">
                             <div class="header-dro-secial-head">Stay Connected</div>
                                 <div class="header-dro-secial-block-main">
                                 <div class="row">   
                                  <div class="col-sm-4 col-md-3 col-lg-3">
                                      <div class="header-dro-secial-block-inner">
                                         <div class="header-dro-secial-img-left">
                                           <a target="_black" href="{{ (isset($arr_site_settings['fb_url']) && $arr_site_settings['fb_url']!='') ? $arr_site_settings['fb_url'] : 'javascript:void(0)' }}"><img src="{{url('images/header-drop-social-icon-1.jpg')}}" alt="header-drop-social-icon-1" /></a>
                                         </div>
                                         <div class="header-dro-secial-text-right">
                                            <h1>Like Us On</h1>
                                            <h3>Facebook</h3>
                                         </div>
                                         <div class="clearfix"></div>
                                      </div>
                                  </div>
                                  <div class="col-sm-4 col-md-3 col-lg-3">
                                      <div class="header-dro-secial-block-inner">
                                         <div class="header-dro-secial-img-left">
                                           <a target="_black" href="{{ (isset($arr_site_settings['instagram_url']) && $arr_site_settings['instagram_url']!='') ? $arr_site_settings['instagram_url'] : 'javascript:void(0)' }}"><img src="{{url('images/header-drop-social-icon-2.jpg')}}" alt="header-drop-social-icon-2" /></a>
                                         </div>
                                         <div class="header-dro-secial-text-right">
                                            <h1>Follow On</h1>
                                            <h3>Instagram</h3>
                                         </div>
                                         <div class="clearfix"></div>
                                      </div>
                                  </div>
                                  <div class="col-sm-4 col-md-3 col-lg-3">
                                      <div class="header-dro-secial-block-inner">
                                         <div class="header-dro-secial-img-left">
                                           <a target="_black" href="{{ (isset($arr_site_settings['linked_in_url']) && $arr_site_settings['linked_in_url']!='') ? $arr_site_settings['linked_in_url'] : 'javascript:void(0)' }}"><img src="{{url('images/header-drop-social-icon-3.jpg')}}" alt="header-drop-social-icon-3" /></a>
                                         </div>
                                         <div class="header-dro-secial-text-right">
                                            <h1>Keep Up On</h1>
                                            <h3>Linkedin</h3>
                                         </div>
                                         <div class="clearfix"></div>
                                      </div>
                                  </div>
                                  <div class="col-sm-4 col-md-3 col-lg-3">
                                      <div class="header-dro-secial-block-inner">
                                         <div class="header-dro-secial-img-left">
                                           <a target="_black" href="{{ (isset($arr_site_settings['twitter_url']) && $arr_site_settings['twitter_url']!='') ? $arr_site_settings['twitter_url'] : 'javascript:void(0)' }}"><img src="{{url('images/header-drop-social-icon-4.jpg')}}" alt="header-drop-social-icon-4" /></a>
                                         </div>
                                         <div class="header-dro-secial-text-right">
                                            <h1>Catch Us On</h1>
                                            <h3>Twitter</h3>
                                         </div>
                                         <div class="clearfix"></div>
                                      </div>
                                  </div>
                              </div>
                              </div>
                          </div>
                          
                          <div class="clearfix"></div>
                        </div>
                     </div>
                  </div>
        </div>
<div class="clearfix"></div>
<!--</div>-->
<script type="text/javascript">
var base_url = '{{url('/')}}';

  $( function() {
    var arr_links = 
                    [
                      {
                        label: "Home",
                        link: base_url
                      },
                      {
                        label: "Login",
                        link: base_url+'/login'
                      },
                      {
                        label: "Sign Up as Customer",
                        link: base_url+'/register'
                      },
                      {
                        label: "Sign Up as Driver",
                        link: base_url+'/join_our_fleet'
                      },
                      {
                        label: "How it Works",
                        link: base_url+'/how_it_works'
                      },
                      {
                        label: "Contact Us",
                        link: base_url+'/contact_us'
                      },
                      {
                        label: "About Us",
                        link: base_url+'/about_us'
                      },
                      {
                        label: "Help",
                        link: base_url+'/help'
                      },
                      {
                        label: "Privacy Policy",
                        link: base_url+'/policy'
                      },
                      {
                        label: "Terms & Conditions",
                        link: base_url+'/terms_and_conditions'
                      },
                      {
                        label: "Drive",
                        link: base_url+'/join_our_fleet'
                      },
                      {
                        label: "Our Fleet",
                        link: base_url+'/our_fleet'
                      },
                      {
                        label: "Fare Estimator",
                        link: base_url+'/fare_estimate'
                      },
                      
                    ];

    $( "#search_link" ).autocomplete({
      source: arr_links,
      search:function(event,ui){
      },
      open:function(event,ui){
      },
      
      select:function(event,ui){
        if(ui.item.link!=false)
        {
          if(ui.item.link!=undefined && ui.item.link!=''){
            window.location.href = ui.item.link;
          }
        }
      }
    });
  } );

</script>
<!--Sticky Menu-->
<script type="text/javascript">
    $(document).ready(function() {
        var stickyNavTop = $('.header').offset().top = 20;
        var stickyNav = function() {
            var scrollTop = $(window).scrollTop();

            if (scrollTop > stickyNavTop) {
                $('.header').addClass('sticky');
            } else {
                $('.header').removeClass('sticky');
            }
        };

        stickyNav();

        $(window).scroll(function() {
            stickyNav();
        });
    })
</script>
<!--Sticky Menu-->

<!-- Min Top Menu Start Here  -->
<script type="text/javascript">
    var doc_width = $(window).width();
    if (doc_width < 1180) {
        function openNav() {
            document.getElementById("mySidenav").style.width = "250px";
            $("body").css({
                "margin-left": "250px",
                "overflow-x": "hidden",
                "transition": "margin-left .5s",
                "position": "fixed"
            });
            $("#main").addClass("overlay");
        }

        function closeNav() {
            document.getElementById("mySidenav").style.width = "0";
            $("body").css({
                "margin-left": "0px",
                "transition": "margin-left .5s",
                "position": "relative"
            });
            $("#main").removeClass("overlay");
        }
    }
   
</script>
<!-- Min Top Menu Start End  -->

<!-- Hide Show Menu Icon Start -->
<script> 
    $(document).ready(function(){
        $("#flipdashboard, #flipdashboard2").click(function(){
            $("#paneldashboard").slideToggle("slow");
            $("body").addClass("scroll-remove");
        });
      $('.paneldashboardclose').click(function() {
          $("#paneldashboard").slideToggle("hide");
          $("body").removeClass("scroll-remove");
      }); 
    });
</script>
<!-- Hide Show Menu Icon End -->