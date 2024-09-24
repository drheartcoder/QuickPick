<?php 

     $admin_path     = config('app.project.admin_panel_slug');
     //dump($arr_current_user_access);
?>

            <div id="sidebar" class="navbar-collapse collapse">
                <!-- BEGIN Navlist -->
                <ul class="nav nav-list">
                   
                    <li class="logo-side">
                       <a href="{{ url('/').'/'.$admin_path.'/dashboard'}}"> <img src="{{url('/')}}/images/admin-logo.png" alt="" /></a>
                    </li>
                    
                    
                    <li class="<?php  if(Request::segment(2) == 'dashboard'){ echo 'active'; } ?>">
                        <a href="{{ url('/').'/'.$admin_path.'/dashboard'}}">
                            <span class="icon-dash"><i class="fa fa-dashboard faa-vertical animated-hover"></i></span>
                            <span class="mobile-nones">Dashboard</span>
                        </a>
                    </li>
            
                    <li class="<?php  if(Request::segment(2) == 'site_settings'){ echo 'active'; } ?>">
                        <a href="{{ url($admin_panel_slug.'/site_settings') }}" >
                            <span class="icon-dash"><i class="fa  fa-wrench faa-vertical animated-hover"></i></span>
                            <span class="mobile-nones">Site Settings</span>
                        </a>
                    </li>
                    
                    <li class="<?php  if(Request::segment(2) == 'my_earning'){ echo 'active'; } ?>">
                        <a href="{{ url($admin_panel_slug.'/my_earning') }}" >
                            <span class="icon-dash"> <i class="fa fa-usd faa-vertical animated-hover"></i></span>
                            <span class="mobile-nones">My Earning</span>
                        </a>
                    </li>

                    <li class="<?php  if(Request::segment(2) == 'assigned_area'){ echo 'active'; } ?>">
                            <a href="{{ url($admin_panel_slug.'/assigned_area')}}" >
                                <span class="icon-dash"> <i class="fa fa-map faa-vertical animated-hover"></i></span>
                                <span class="text-center">Assigned Area</span>
                            </a>
                    </li>
                    
                    <li class="<?php  if(Request::segment(2) == 'restricted_area'){ echo 'active'; } ?>">
                            <a href="{{ url($admin_panel_slug.'/restricted_area')}}" >
                                <span class="icon-dash"> <i class="fa fa-location-arrow animated-hover"></i></span>
                                <span class="text-center">Restricted Area</span>
                            </a>
                    </li>

                    <li class="<?php  if(Request::segment(2) == 'admin_commission'){ echo 'active'; } ?>">
                        <a href="{{ url($admin_panel_slug.'/admin_commission') }}" >
                            <span class="icon-dash"> <i class="fa fa-percent faa-vertical animated-hover"></i></span>
                            <span class="mobile-nones">Commission</span>
                        </a>
                    </li>
                   
                    <li class="<?php  if(Request::segment(2) == 'admin_bonus'){ echo 'active'; } ?>">
                        <a href="{{ url($admin_panel_slug.'/admin_bonus') }}" >
                            <span class="icon-dash"> <i class="fa fa-trophy faa-vertical animated-hover"></i></span>
                            <span class="mobile-nones">Bonus Points</span>
                        </a>
                    </li>
                   
                    <!--
                    <li class="<?php  if(Request::segment(2) == 'review_tag'){ echo 'active'; } ?>">
                        <a href="{{ url($admin_panel_slug.'/review_tag') }}" >
                            <span class="icon-dash"> <i class="fa fa-star faa-vertical animated-hover"></i></span>
                            <span class="mobile-nones">Review Tags</span>
                        </a>
                    </li>
                    -->
                    
                    <li class="<?php  if(Request::segment(2) == 'promo_offer'){ echo 'active'; } ?>">
                        <a href="javascript:void(0)" class="dropdown-toggle">
                            <span class="icon-dash"> <i class="fa  fa-gift faa-vertical animated-hover"></i></span>
                            <span class="text-center">Promo Offer</span>
                            <b class="arrow fa fa-angle-right"></b>
                        </a>

                         <ul class="submenu">
                            <li style="display: block;" class="<?php  if(Request::segment(2) == 'promo_offer' && Request::segment(3) == ''){ echo 'active'; } ?>"><a href="{{ url($admin_panel_slug.'/promo_offer')}}">Manage </a></li>

                            <li style="display: block;" class="<?php  if(Request::segment(2) == 'promo_offer' && Request::segment(3) == 'create'){ echo 'active'; } ?>"><a href="{{ url($admin_panel_slug.'/promo_offer/create')}}">Create </a></li>   
                                                 
                        </ul>
                    </li>

                    <li class="<?php  if(Request::segment(2) == 'promotional_offer'){ echo 'active'; } ?>">
                        <a href="javascript:void(0)" class="dropdown-toggle">
                            <span class="icon-dash"> <i class="fa  fa-gift faa-vertical animated-hover"></i></span>
                            <span class="text-center">Promotional Offer</span>
                            <b class="arrow fa fa-angle-right"></b>
                        </a>

                         <ul class="submenu">
                            <li style="display: block;" class="<?php  if(Request::segment(2) == 'promotional_offer' && Request::segment(3) == ''){ echo 'active'; } ?>"><a href="{{ url($admin_panel_slug.'/promotional_offer')}}">Manage </a></li>

                            <li style="display: block;" class="<?php  if(Request::segment(2) == 'promotional_offer' && Request::segment(3) == 'create'){ echo 'active'; } ?>"><a href="{{ url($admin_panel_slug.'/promotional_offer/create')}}">Create </a></li>   
                                                 
                        </ul>
                    </li>

                    <li class="<?php  if(Request::segment(2) == 'users'){ echo 'active'; } ?>">
                        <a href="javascript:void(0)" class="dropdown-toggle">
                            <span class="icon-dash"> <i class="fa fa-users faa-vertical animated-hover"></i></span>
                            <span class="text-center">Users</span>
                            <b class="arrow fa fa-angle-right"></b>
                        </a>

                         <ul class="submenu">
                            <li style="display: block;" class="<?php  if(Request::segment(2) == 'users' && Request::segment(3) == ''){ echo 'active'; } ?>"><a href="{{ url($admin_panel_slug.'/users')}}">Manage </a></li>
                           
                            <li style="display: block;" class="<?php  if(Request::segment(2) == 'users' && Request::segment(3) == 'create'){ echo 'active'; } ?>"><a href="{{ url($admin_panel_slug.'/users/create')}}">Create </a></li>   
                                                   
                        </ul>
                    </li>

                    <li class="<?php  if(Request::segment(2) == 'company'){ echo 'active'; } ?>">
                        <a href="javascript:void(0)" class="dropdown-toggle">
                            <span class="icon-dash"><i class="fa fa-building-o faa-vertical animated-hover"></i></span>
                            <span class="text-center">Company</span>
                            <b class="arrow fa fa-angle-right"></b>
                        </a>

                         <ul class="submenu">
                            <li style="display: block;" class="<?php  if(Request::segment(2) == 'company' && Request::segment(3) == ''){ echo 'active'; } ?>"><a href="{{ url($admin_panel_slug.'/company')}}">Manage </a></li>
                            
                            <li style="display: block;" class="<?php  if(Request::segment(2) == 'company' && Request::segment(3) == 'create'){ echo 'active'; } ?>"><a href="{{ url($admin_panel_slug.'/company/create')}}">Create </a></li>   
                                                    
                        </ul>
                    </li>

                    <li class="<?php  if(Request::segment(2) == 'driver'){ echo 'active'; } ?>">
                        <a href="javascript:void(0)" class="dropdown-toggle">
                            <span class="icon-dash"> <i class="fa fa-car faa-vertical animated-hover"></i></span>
                            <span class="text-center">Drivers</span>
                            <b class="arrow fa fa-angle-right"></b>
                        </a>

                         <ul class="submenu">
                            <li style="display: block;" class="<?php  if(Request::segment(2) == 'driver' && Request::segment(3) == ''){ echo 'active'; } ?>"><a href="{{ url($admin_panel_slug.'/driver')}}">Manage </a></li>
                           
                            <li style="display: block;" class="<?php  if(Request::segment(2) == 'driver' && Request::segment(3) == 'create'){ echo 'active'; } ?>"><a href="{{ url($admin_panel_slug.'/driver/create')}}">Create </a></li>   
                                                   
                        </ul>
                    </li>

                     <!---- <li class="<?php  if(Request::segment(2) == 'package_type'){ echo 'active'; } ?>">
                        <a href="javascript:void(0)" class="dropdown-toggle">
                            <span class="icon-dash"> <i class="fa fa-tasks faa-vertical animated-hover"></i></span>
                            <span class="mobile-nones text-center">Package Type</span>
                            <b class="arrow fa fa-angle-right"></b>
                        </a>

                         <ul class="submenu">
                            <li style="display: block;" class="<?php  if(Request::segment(2) == 'package_type' && Request::segment(3) == ''){ echo 'active'; } ?>"><a href="{{ url($admin_panel_slug.'/package_type')}}">Manage </a></li>
                           
                            <li style="display: block;" class="<?php  if(Request::segment(2) == 'package_type' && Request::segment(3) == 'create'){ echo 'active'; } ?>"><a href="{{ url($admin_panel_slug.'/package_type/create')}}">Create </a></li>   
                                                   
                        </ul>
                    </li> -->   


                    <li class="<?php  if(Request::segment(2) == 'vehicle_type'){ echo 'active'; } ?>">
                        <a href="javascript:void(0)" class="dropdown-toggle">
                            <span class="icon-dash"> <i class="fa fa-bus faa-vertical animated-hover"></i></span>
                            <span class="mobile-nones text-center">Vehicle Type</span>
                            <b class="arrow fa fa-angle-right"></b>
                        </a>

                         <ul class="submenu">
                            <li style="display: block;" class="<?php  if(Request::segment(2) == 'vehicle_type' && Request::segment(3) == ''){ echo 'active'; } ?>"><a href="{{ url($admin_panel_slug.'/vehicle_type')}}">Manage </a></li>
                           
                            <li style="display: block;" class="<?php  if(Request::segment(2) == 'vehicle_type' && Request::segment(3) == 'create'){ echo 'active'; } ?>"><a href="{{ url($admin_panel_slug.'/vehicle_type/create')}}">Create </a></li>   
                                                   
                        </ul>
                    </li>

                    <li class="<?php  if(Request::segment(2) == 'vehicle'){ echo 'active'; } ?>">
                        <a href="javascript:void(0)" class="dropdown-toggle">
                            <span class="icon-dash"> <i class="fa fa-truck faa-vertical animated-hover"></i></span>
                            <span class="mobile-nones text-center">Vehicle</span>
                            <b class="arrow fa fa-angle-right"></b>
                        </a>

                         <ul class="submenu">
                            <li style="display: block;" class="<?php  if(Request::segment(2) == 'vehicle' && Request::segment(3) == ''){ echo 'active'; } ?>"><a href="{{ url($admin_panel_slug.'/vehicle')}}">Manage </a></li>
                          
                            <li style="display: block;" class="<?php  if(Request::segment(2) == 'vehicle' && Request::segment(3) == 'create'){ echo 'active'; } ?>"><a href="{{ url($admin_panel_slug.'/vehicle/create')}}">Create </a></li>   
                                                    
                        </ul>
                    </li>

                    <li class="<?php  if(Request::segment(2) == 'driver_vehicle' && Request::segment(3) == ''){ echo 'active'; } ?>">
                        <a href="{{ url($admin_panel_slug.'/driver_vehicle')}}" class="dropdown-toggle">
                            <span class="icon-dash"><i class="fa fa-car faa-vertical animated-hover"></i></span>
                            <span class="text-center"> Driver Vehicle</span>
                        </a>
                    </li>

                    <li class="<?php  if(Request::segment(2) == 'track_booking' || Request::segment(2) == 'request_list'  || Request::segment(2) == 'future_booking' ){ echo 'active'; } ?>">
                        <a href="javascript:void(0)" class="dropdown-toggle">
                            <span class="icon-dash"> <i class="fa fa-street-view animated-hover"></i></span>
                            <span class="text-center">Ride Management</span>
                            <b class="arrow fa fa-angle-right"></b>
                        </a>

                         <ul class="submenu">
                            <li style="display: block;" class="<?php  if(Request::segment(2) == 'track_booking' && Request::segment(3) == ''){ echo 'active'; } ?>"><a href="{{ url($admin_panel_slug.'/track_booking')}}">Todays Booking</a></li>


                            <li style="display: block;" class="<?php  if(Request::segment(2) 
                            == 'request_list' ){ echo 'active'; } ?>"><a href="{{ url($admin_panel_slug.'/request_list')}}">Request List</a></li>   

                            <li style="display: block;" class="<?php  if(Request::segment(2) 
                            == 'request_list' ){ echo 'active'; } ?>"><a href="{{ url($admin_panel_slug.'/future_booking')}}">Future Booking List</a></li>   


                            <li style="display: block;" class="<?php  if(Request::segment(2) == 'track_booking' && Request::segment(3) == 'history'){ echo 'active'; } ?>"><a href="{{ url($admin_panel_slug.'/track_booking/booking_history')}}">Booking History</a></li>   

                        </ul>
                    </li>


                    <!--<li class="<?php  if(Request::segment(2) == 'assistant'){ echo 'active'; } ?>">
                        <a href="{{ url($admin_panel_slug.'/assistant')}}" class="dropdown-toggle">
                            <span class="icon-dash"> <i class="fa fa-microphone"></i></span>
                            <span class="text-center">Admin Assistant</span>
                        </a>
                    </li>-->

                    <li class="<?php  if(Request::segment(2) == 'booking_summary'){ echo 'active'; } ?>">
                        <a href="{{ url($admin_panel_slug.'/booking_summary')}}" class="dropdown-toggle">
                            <span class="icon-dash"> <i class="fa fa-list-alt"></i></span>
                            <span class="text-center">Booking Summary</span>
                        </a>
                    </li>

                    <li class="<?php  if(Request::segment(2) == 'report'){ echo 'active'; } ?>">
                            <a href="javascript:void(0)" class="dropdown-toggle">
                                <span class = "icon-dash"> <i class="fa  fa-newspaper-o faa-vertical animated-hover"></i></span>
                                <span class="text-center">Report</span>
                                <b class="arrow fa fa-angle-right"></b>
                            </a>

                             <ul class="submenu">
                                <li style="display: block;" class="<?php  if(Request::segment(2) == 'report' && Request::segment(3) == ''){ echo 'active'; } ?>"><a href="{{ url($admin_panel_slug.'/report/?report_type=user')}}">User</a></li>
                                <li style="display: block;" class="<?php  if(Request::segment(2) == 'report' && Request::segment(3) == ''){ echo 'active'; } ?>"><a href="{{ url($admin_panel_slug.'/report/?report_type=driver')}}">Driver</a></li>
                                <li style="display: block;" class="<?php  if(Request::segment(2) == 'report' && Request::segment(3) == ''){ echo 'active'; } ?>"><a href="{{ url($admin_panel_slug.'/report?report_type=booking')}}">Booking</a></li>
                                <!-- <li style="display: block;" class="<?php  if(Request::segment(2) == 'report' && Request::segment(3) == ''){ echo 'active'; } ?>"><a href="{{ url($admin_panel_slug.'/report?report_type=rating')}}">Rating</a></li> -->
                            </ul>
                        </li>

                    <li class="<?php  if(Request::segment(2) == 'static_pages'){ echo 'active'; } ?>">
                        <a href="javascript:void(0)" class="dropdown-toggle">
                            <span class="icon-dash"> <i class="fa  fa-sitemap faa-vertical animated-hover"></i></span>
                            <span>CMS</span>
                            <b class="arrow fa fa-angle-right"></b>
                        </a>

                         <ul class="submenu">
                            <li style="display: block;" class="<?php  if(Request::segment(2) == 'static_pages' && Request::segment(3) == ''){ echo 'active'; } ?>"><a href="{{ url($admin_panel_slug.'/static_pages')}}">Manage </a></li>
                            
                            <li style="display: block;" class="<?php  if(Request::segment(2) == 'static_pages' && Request::segment(3) == 'create'){ echo 'active'; } ?>"><a href="{{ url($admin_panel_slug.'/static_pages/create')}}">Create </a></li>   
                                               
                        </ul>
                    </li>

                    <li class="<?php  if(Request::segment(2) == 'faq'){ echo 'active'; } ?>">
                            <a href="javascript:void(0)" class="dropdown-toggle">
                                <span class="icon-dash"> <i class="fa fa-question-circle faa-vertical animated-hover"></i></span>
                                <span>FAQ's</span>
                                <b class="arrow fa fa-angle-right"></b>
                            </a>

                            <ul class="submenu">
                                <li style="display: block;" class="<?php  if(Request::segment(2) == 'faq' && Request::segment(3) == ''){ echo 'active'; } ?>"><a href="{{ url($admin_panel_slug.'/faq')}}">Manage </a></li>
                                @if(array_key_exists('faq.create', $arr_current_user_access))
                                <li style="display: block;" class="<?php  if(Request::segment(2) == 'faq' && Request::segment(3) == 'create'){ echo 'active'; } ?>"><a href="{{ url($admin_panel_slug.'/faq/create')}}">Create </a></li>   
                                @endif                         
                            </ul>
                        </li>

                    <li class="<?php  if(Request::segment(2) == 'email_template'){ echo 'active'; } ?>">
                        <a href="{{ url($admin_panel_slug.'/email_template')}}" class="dropdown-toggle">
                            <span class="icon-dash"><i class="fa fa-envelope faa-vertical animated-hover"></i></span>
                            <span class="mobile-nones">Email Templates</span>
                        </a>
                    </li>

                    <li class="<?php  if(Request::segment(2) == 'subscriber'){ echo 'active'; } ?>">
                        <a href="{{ url($admin_panel_slug.'/subscriber')}}" class="dropdown-toggle">
                            <span class="icon-dash"><i class="fa fa-envelope-o faa-vertical animated-hover"></i></span>
                            <span class="mobile-nones">Subscribers</span>
                        </a>
                    </li>
                    
                    <li class="<?php  if(Request::segment(2) == 'contact_enquiry'){ echo 'active'; } ?>">
                        <a href="{{ url($admin_panel_slug.'/contact_enquiry')}}" class="dropdown-toggle" >
                            <span class="icon-dash"><i class="fa fa-phone faa-vertical animated-hover"></i></span>
                            <span class="text-center">Contact Inquiry</span>
                        </a>
                    </li>
                     

                    <!-- <li class="<?php  if(Request::segment(2) == 'need_delivery'){ echo 'active'; } ?>">
                        <a href="{{ url($admin_panel_slug.'/need_delivery')}}" class="dropdown-toggle" >
                            <span class="icon-dash"><i class="fa fa-phone faa-vertical animated-hover"></i></span>
                            <span class="text-center">Need Delivery</span>
                        </a>
                    </li>  -->

                <!-- BEGIN Sidebar Collapse Button -->
                <div id="sidebar-collapse" class="visible-lg">
                    <i class="fa fa-angle-double-left"></i>
                </div>
                <!-- END Sidebar Collapse Button -->
            </div>