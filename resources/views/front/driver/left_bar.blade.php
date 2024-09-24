<?php 
    $user_path     = config('app.project.role_slug.driver_role_slug'); 

    $active_tab = isset($active_tab) ? $active_tab : '';

    $is_ongoing_trip = get_trip('ONGOING','DRIVER');
?>

<div class="col-sm-3 col-md-2 col-lg-2">
  <div class="left-bar">
    <ul>
      <li class="<?php  if(Request::segment(2) == 'dashboard'){ echo 'active'; } ?>">
        <a href="{{ url('/').'/'.$user_path.'/dashboard'}}">
              <span class="dash-img"></span>
              <span class="dash-title">Dashboard</span>
              <div class="clearfix"></div></a>
      </li>

      <li class="<?php  if(Request::segment(2) == 'my_profile_view' || Request::segment(2) == 'my_profile_edit'){ echo 'active'; } ?>">
        <a href="{{ url('/').'/'.$user_path.'/my_profile_view'}}">
              <span class="dash-img pro-my-img"></span>
              <span class="dash-title">My Profile</span>
              <div class="clearfix"></div></a>
      </li>

      <li class="<?php  if(Request::segment(2) == 'vehicle' || Request::segment(2) == 'vehicle_edit'){ echo 'active'; } ?>">
        <a href="{{ url('/').'/'.$user_path.'/vehicle'}}">
              <span class="dash-img chg-img"></span>
              <span class="dash-title">Manage Vehicle</span>
              <div class="clearfix"></div></a>
      </li>

      <li class="<?php  if(Request::segment(2) == 'my_job' || Request::segment(2) == 'job_details' ||  $active_tab == 'my_job'){ echo 'active'; } ?>">
        <a href="{{ url('/').'/'.$user_path.'/my_job'}}">
              <span class="dash-img enrol-img"></span>
              <span class="dash-title">My Jobs</span>
              <div class="clearfix"></div></a>
      </li>

    @if(isset($is_ongoing_trip) && $is_ongoing_trip == 0)
      <li class="<?php  if(Request::segment(2) == 'request_list' ||  $active_tab == 'request_list'){ echo 'active'; } ?>">
        <a href="{{ url('/').'/'.$user_path.'/request_list'}}">
              <span class="dash-img booking-my-img"></span>
              <span class="dash-title">Request List</span>
              <div class="clearfix"></div></a>
      </li>
    @endif

      <li class="<?php  if((Request::segment(2) == 'track_trip')){ echo 'active'; } ?>">
        <a href="{{ url('/').'/'.$user_path.'/track_trip'}}">
              <span class="dash-img tra-tip-img"></span>
              <span class="dash-title">Track Trip</span>
              <div class="clearfix"></div></a>
      </li>

      <li class="<?php  if(Request::segment(2) == 'my_earning'){ echo 'active'; } ?>">
        <a href="{{ url('/').'/'.$user_path.'/my_earning'}}">
              <span class="dash-img post-img"></span>
              <span class="dash-title">My Earnings</span>
              <div class="clearfix"></div></a>
      </li>

      <li class="<?php  if(Request::segment(2) == 'change_password'){ echo 'active'; } ?>">
        <a href="{{ url('/').'/'.$user_path.'/change_password'}}">
              <span class="dash-img change-pass-img"></span>
              <span class="dash-title">Change Password</span>
              <div class="clearfix"></div></a>
      </li>

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
              <span class="dash-title">Chat With Customer</span>
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