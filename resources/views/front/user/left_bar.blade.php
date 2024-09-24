<?php 
    $user_path     = config('app.project.role_slug.user_role_slug'); 
?>

<div class="left-bar-min">
  <div class="left-bar">
    <ul>
      <li class="<?php  if(Request::segment(2) == 'dashboard'){ echo 'active'; } ?>">
        <a href="{{ url('/').'/'.$user_path.'/dashboard'}}">
              <span class="dash-img"></span>
              <span class="dash-title">Dashboard</span>
              <div class="clearfix"></div></a>
      </li>

            <li class="<?php  if((Request::segment(2) == 'delivery_request') || (Request::segment(2) == 'book_driver_request')){ echo 'active'; } ?>">
        <a href="{{ url('/').'/'.$user_path.'/delivery_request'}}">
              <span class="dash-img chg-img"></span>
              <span class="dash-title">Delivery Request</span>
              <div class="clearfix"></div></a>
      </li>

      <li class="<?php  if(Request::segment(2) == 'my_profile_view' || Request::segment(2) == 'my_profile_edit'){ echo 'active'; } ?>">
        <a href="{{ url('/').'/'.$user_path.'/my_profile_view'}}">
              <span class="dash-img pro-my-img"></span>
              <span class="dash-title">My Profile</span>
              <div class="clearfix"></div></a>
      </li>


      <li class="<?php  if((Request::segment(2) == 'my_booking') || (Request::segment(2) =='booking_details') || (Request::segment(2) =='pending_load_post')){ echo 'active'; } ?>">
        <a href="{{ url('/').'/'.$user_path.'/my_booking'}}">
              <span class="dash-img booking-my-img"></span>
              <span class="dash-title">My Bookings</span>
              <div class="clearfix"></div></a>
      </li>

      <li class="<?php  if((Request::segment(2) == 'track_driver') || (Request::segment(2) == 'track_trip')){ echo 'active'; } ?>">
        <a href="{{ url('/').'/'.$user_path.'/track_driver'}}">
              <span class="dash-img track-driver-img"></span>
              <span class="dash-title">Track Driver</span>
              <div class="clearfix"></div></a>
      </li>

      <li class="<?php  if(Request::segment(2) == 'payment'){ echo 'active'; } ?>">
        <a href="{{ url('/').'/'.$user_path.'/payment'}}">
              <span class="dash-img post-img"></span>
              <span class="dash-title">Payment</span>
              <div class="clearfix"></div></a>
      </li>
      @if(isset($arr_login_user_details['via_social']) && $arr_login_user_details['via_social'] == '0')
        <li class="<?php  if(Request::segment(2) == 'change_password'){ echo 'active'; } ?>">
          <a href="{{ url('/').'/'.$user_path.'/change_password'}}">
                <span class="dash-img change-pass-img"></span>
                <span class="dash-title">Change Password</span>
                <div class="clearfix"></div></a>
        </li>
      @endif

      <li class="<?php  if(Request::segment(2) == 'notification'){ echo 'active'; } ?>">
        <a href="{{ url('/').'/'.$user_path.'/notification'}}">
              <span class="dash-img noti-img"></span>
              <span class="dash-title">My Notifications</span>
              <div class="clearfix"></div></a>
      </li>

      <li class="<?php  if(Request::segment(2) == 'review_rating'){ echo 'active'; } ?>">
        <a href="{{ url('/').'/'.$user_path.'/review_rating'}}">
              <span class="dash-img subs-img"></span>
              <span class="dash-title">Review &amp; Ratings</span>
              <div class="clearfix"></div></a>
      </li>

      <li class="<?php  if(Request::segment(2) == 'messages'){ echo 'active'; } ?>">
        <a href="{{ url('/').'/'.$user_path.'/messages'}}">
              <span class="dash-img message-img"></span>
              <span class="dash-title">Chat With Driver</span>
              <div class="clearfix"></div></a>
      </li>

      <li>
          <a href="{{url('/logout')}}">
              <span class="dash-img log-out-img"></span>
              <span class="dash-title">Sign out</span>
              <div class="clearfix"></div></a>
      </li>


    </ul>
  </div>
</div>

<!--Left menu script start-->
<script type="text/javascript">
  function openmenus() {
    $('.left-side-inner-me').toggle();
  }
</script>
<!--Left menu script end-->