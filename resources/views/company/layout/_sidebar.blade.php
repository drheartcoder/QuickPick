<?php 

     $admin_path     = config('app.project.company_panel_slug');
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
            
                    
                    <li class="<?php  if(Request::segment(2) == 'my_earning'){ echo 'active'; } ?>">
                        <a href="{{ url($company_panel_slug.'/my_earning') }}" >
                            <span class="icon-dash"> <i class="fa fa-usd faa-vertical animated-hover"></i></span>
                            <span class="mobile-nones">My Earning</span>
                        </a>
                    </li>   
                    
                    <li class="<?php  if(Request::segment(2) == 'stripe_account'){ echo 'active'; } ?>">
                        <a href="{{ url($company_panel_slug.'/stripe_account') }}" >
                            <span class="icon-dash"> <i class="fa fa-cc-stripe animated-hover"></i></span>
                            <span class="mobile-nones">Stripe Account</span>
                        </a>
                    </li>  

                    <li class="<?php  if(Request::segment(2) == 'deposit_money'){ echo 'active'; } ?>">
                        <a href="{{ url($company_panel_slug.'/deposit_money') }}" >
                            <span class="icon-dash"> <i class="fa fa-money faa-vertical animated-hover"></i></span>
                            <span class="mobile-nones">Deposited Money</span>
                        </a>
                    </li>                    

                    <li class="<?php  if(Request::segment(2) == 'company_commission'){ echo 'active'; } ?>">
                        <a href="{{ url($company_panel_slug.'/company_commission') }}" >
                            <span class="icon-dash"> <i class="fa fa-percent faa-vertical animated-hover"></i></span>
                            <span class="mobile-nones">Commission</span>
                        </a>
                    </li>             

                     <li class="<?php  if(Request::segment(2) == 'driver'){ echo 'active'; } ?>">
                            <a href="javascript:void(0)" class="dropdown-toggle">
                                <span class="icon-dash"> <i class="fa  fa-user faa-vertical animated-hover"></i></span>
                                <span class="text-center">Driver</span>
                                <b class="arrow fa fa-angle-right"></b>
                            </a>

                             <ul class="submenu">
                                <li style="display: block;" class="<?php  if(Request::segment(2) == 'driver' && Request::segment(3) == ''){ echo 'active'; } ?>"><a href="{{ url($company_panel_slug.'/driver')}}">Manage </a></li>
                                @if(array_key_exists('driver.create', $arr_current_user_access))
                                <li style="display: block;" class="<?php  if(Request::segment(2) == 'driver' && Request::segment(3) == 'create'){ echo 'active'; } ?>"><a href="{{ url($company_panel_slug.'/driver/create')}}">Create </a></li>   
                                @endif                         
                            </ul>
                        </li>
                  
                      <li class="<?php  if(Request::segment(2) == 'vehicle'){ echo 'active'; } ?>">
                            <a href="javascript:void(0)" class="dropdown-toggle">
                                <span class="icon-dash"> <i class="fa fa-motorcycle faa-vertical animated-hover"></i></span>
                                <span class="mobile-nones text-center">Vehicle</span>
                                <b class="arrow fa fa-angle-right"></b>
                            </a>

                             <ul class="submenu">
                                <li style="display: block;" class="<?php  if(Request::segment(2) == 'vehicle' && Request::segment(3) == ''){ echo 'active'; } ?>"><a href="{{ url($company_panel_slug.'/vehicle')}}">Manage </a></li>
                                @if(array_key_exists('vehicle.create', $arr_current_user_access))
                                <li style="display: block;" class="<?php  if(Request::segment(2) == 'vehicle' && Request::segment(3) == 'create'){ echo 'active'; } ?>"><a href="{{ url($company_panel_slug.'/vehicle/create')}}">Create </a></li>   
                                @endif                         
                            </ul>
                        </li>
                 
                        <li class="<?php  if(Request::segment(2) == 'driver_vehicle'){ echo 'active'; } ?>">
                            <a href="javascript:void(0)" class="dropdown-toggle">
                                <span class="icon-dash"> <i class="fa  fa-car faa-vertical animated-hover"></i></span>
                                <span class="text-center">Driver Vehicles</span>
                                <b class="arrow fa fa-angle-right"></b>
                            </a>

                             <ul class="submenu">
                                <li style="display: block;" class="<?php  if(Request::segment(2) == 'driver_vehicle' && Request::segment(3) == ''){ echo 'active'; } ?>"><a href="{{ url($company_panel_slug.'/driver_vehicle')}}">Manage </a></li>
                            </ul>
                        </li>
                 
                        <li class="<?php  if(Request::segment(2) == 'track_booking'){ echo 'active'; } ?>">
                            <a href="javascript:void(0)" class="dropdown-toggle">
                                <span class="icon-dash"> <i class="fa fa-street-view animated-hover"></i></span>
                                <span class="text-center">Ride Management</span>
                                <b class="arrow fa fa-angle-right"></b>
                            </a>

                             <ul class="submenu">
                                <li style="display: block;" class="<?php  if(Request::segment(2) == 'track_booking' && Request::segment(3) == ''){ echo 'active'; } ?>"><a href="{{ url($company_panel_slug.'/track_booking')}}">Todays Booking</a></li>
                                <li style="display: block;" class="<?php  if(Request::segment(2) == 'track_booking' && Request::segment(3) == 'history'){ echo 'active'; } ?>"><a href="{{ url($company_panel_slug.'/track_booking/booking_history')}}">Booking History</a></li>   
                            </ul>
                        </li>

                        <li class="<?php  if(Request::segment(2) == 'booking_summary'){ echo 'active'; } ?>">
                            <a href="{{ url($company_panel_slug.'/booking_summary')}}" class="dropdown-toggle">
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
                                <li style="display: block;" class="<?php  if(Request::segment(2) == 'report' && Request::segment(3) == ''){ echo 'active'; } ?>"><a href="{{ url($company_panel_slug.'/report/?report_type=driver')}}">Driver</a></li>
                                <li style="display: block;" class="<?php  if(Request::segment(2) == 'report' && Request::segment(3) == ''){ echo 'active'; } ?>"><a href="{{ url($company_panel_slug.'/report?report_type=booking')}}">Booking</a></li>
                                <!-- <li style="display: block;" class="<?php  if(Request::segment(2) == 'report' && Request::segment(3) == ''){ echo 'active'; } ?>"><a href="{{ url($company_panel_slug.'/report?report_type=rating')}}">Rating</a></li> -->
                            </ul>
                        </li>

                <!-- END Navlist -->

                <!-- BEGIN Sidebar Collapse Button -->
                <div id="sidebar-collapse" class="visible-lg">
                    <i class="fa fa-angle-double-left"></i>
                </div>
                <!-- END Sidebar Collapse Button -->
            </div>