<!DOCTYPE html>
<html>

<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta name="description" content="" />
<meta name="keywords" content="" />
<meta name="author" content="" />
<title>{{config('app.project.name')}} : {{ isset($page_title) ? $page_title : '' }}</title>
<!-- ======================================================================== -->
<link rel="icon" type="image/png" sizes="16x16" href="{{url('/images/favicon-48x48.png')}}">
<!-- Bootstrap CSS -->
<link href="{{url('css/front/bootstrap.css')}}" rel="stylesheet" type="text/css" />
{{-- <link href="{{url('css/front/bootstrap-modal.css')}}" rel="stylesheet" type="text/css" /> --}}
<link href="{{url('css/front/jquery-ui.css')}}" rel="stylesheet" type="text/css" />
<!--font-awesome-css-start-here-->
<!--<link href="{{url('css/front/font-awesome.min.css')}}" rel="stylesheet" type="text/css" />-->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
<!--Custom Css-->
<link href="{{url('css/front/quick-pick.css')}}" rel="stylesheet" type="text/css" />
<!--<link href="{{url('css/front/bootstrap-datepicker.min.css')}}" rel="stylesheet" type="text/css" />-->
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/css/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css" />
<link href="{{url('css/front/easy-responsive-tabs.css')}}" rel="stylesheet" type="text/css" />
<link href="{{url('css/front/jquery.mCustomScrollbar.css')}}" rel="stylesheet" type="text/css" />
<link href="{{url('css/front/bootstrap-datepicker.min.css')}}" rel="stylesheet" type="text/css" />
<!--Main JS-->

<link rel="stylesheet" type="text/css" href="{{ url('/') }}/css/admin/sweetalert.css" />
<script src="{{ url('/') }}/js/admin/sweetalert.min.js"></script>
<script type="text/javascript" src="{{ url('/js/admin') }}/sweetalert_msg.js"></script>

<script type="text/javascript" src="{{url('/js/front/jquery-1.11.3.min.js')}}"></script>
<!--common header footer script end-->
<script type="text/javascript" language="javascript" src="{{url('js/front/bootstrap.min.js')}}"></script>
<script type="text/javascript" language="javascript" src="{{url('/')}}/js/front/parsley.extend.js"></script>
<script type="text/javascript" language="javascript" src="{{url('/')}}/js/front/parsley.min.js"></script>    
<script type="text/javascript" language="javascript" src="{{url('/')}}/js/front/bootstrap-datepicker.min.js"></script>
<script type="text/javascript" language="javascript" src="{{url('/')}}/js/front/bootstrap-timepicker.js"></script>
<script type="text/javascript" language="javascript" src="{{url('/')}}/js/front/ajax_loader.js"></script>

<script src="{{url('js/front/jquery-ui.js')}}" type="text/javascript"></script>
<script src="{{url('js/front/jquery.mCustomScrollbar.concat.min.js')}}" type="text/javascript"></script>

<style>
/*Portfolio Start Here*/
/*Flex Slider Start Here*/
#flexiselDemo1 {display: none;}
.nbs-flexisel-container{position:relative;max-width:100%;}
.nbs-flexisel-ul{position:relative;width:9999px;margin:0;padding:0;list-style-type:none;text-align:center}
.nbs-flexisel-inner{overflow:hidden;width:100%}
.nbs-flexisel-item{float:left;margin:0;padding:0;border-radius:0;cursor:pointer;position:relative;}
.nbs-flexisel-item img{border-radius:0}
.nbs-flexisel-item:last-child{border-right: 0px solid #1f1e1d;}

.nbs-flexisel-nav-left,.nbs-flexisel-nav-right{width:35px;height:60px;position:absolute;cursor:pointer;z-index:100;opacity:1; top: 0 !important; bottom: 0; margin: auto 0;}
.nbs-flexisel-nav-left{left:-29px;background:url(images/arrow-left.png) no-repeat;z-index: 9;top: 29px !important}
.nbs-flexisel-nav-left:hover{background:url(images/arrow-left-hover.png) no-repeat; }
.nbs-flexisel-nav-right{right:-29px;background:url(images/arrow-right.png) no-repeat;z-index: 9;top: 29px !important}
.nbs-flexisel-nav-right:hover{background:url(images/arrow-right-hover.png) no-repeat;}
/*Flex Slider End Here*/  

.ui-widget-content{z-index: 9999 !important;}


</style>
<div id="onesignal_notification_div"></div>
@if(Request::segment(1) == 'contact_us')
	<script src="https://www.google.com/recaptcha/api.js?onload=loadCaptcha&render=explicit" async defer></script>
@endif
{{-- <script src='https://www.google.com/recaptcha/api.js'></script> --}}


<!-- script of one signal  -->

<script src="https://cdn.onesignal.com/sdks/OneSignalSDK.js" async=""></script>

<?php
        
  $arr_onsignal_login_user_details = get_onsignal_login_user_details();

  $is_user_login = 'no';
  $current_page_slug = Request::segment(2);
  if(isset($arr_onsignal_login_user_details) && count($arr_onsignal_login_user_details)>0)
  {
    $is_user_login     = 'yes';
    $one_signal_app_id = isset($arr_onsignal_login_user_details['oneSignalAppId']) ? $arr_onsignal_login_user_details['oneSignalAppId'] : '';
    $active_user_id    = isset($arr_onsignal_login_user_details['user_id']) ? $arr_onsignal_login_user_details['user_id'] : '';
    $user_type         = isset($arr_onsignal_login_user_details['user_type']) ? $arr_onsignal_login_user_details['user_type'] : '';
  }
  else 
  {
      $one_signal_app_id =  isset($oneSignalAppId) ? $oneSignalAppId : '';
  }

?>

<script type="text/javascript">



  // Some features may not work properly becasue this browser does not support desktop notifications.
  
  // Your browser does not support desktop notification,due to that some features may not work properly.

  // Kindly Please allow desktop notification,so all features working fine.

  // Some features may not work properly if you don't allow the desktop notification access.
  var onsignal_image_url = "{{url('/images/notification-warning.png')}}";

  var is_user_login     = "{{ isset($is_user_login) ? $is_user_login : '' }}";
  var one_signal_app_id = "{{ isset($one_signal_app_id) ? $one_signal_app_id : '' }}";
  var active_user_id    = "{{ isset($active_user_id) ? $active_user_id : '' }}";
  var active_user_type  = "{{ isset($user_type) ? $user_type : '' }}";
  var current_page_slug = "{{ isset($current_page_slug) ? $current_page_slug : '' }}";

  var arr_user_refresh_pages     = ["my_booking","pending_load_post","booking_details","track_trip"];
  var arr_driver_refresh_pages   = ["my_job","request_list","track_trip","load_post_details","job_details"];

  var arr_user_notification_type   = ["ACCEPT_BY_DRIVER","REJECT_BY_DRIVER","TIMEOUT","TRIP_IN_TRANSIT","TRIP_COMPLETED","TRIP_CANCEL_BY_DRIVER","REJECT_BY_USER","ACCEPT_BY_USER","TRIP_CANCEL_BY_ADMIN"];
  
  var arr_driver_notification_type = ["USER_REQUEST","REJECT_BY_USER","ACCEPT_BY_USER","TRIP_CANCEL_BY_USER","ACCEPT_BY_DRIVER","REJECT_BY_DRIVER","TRIP_IN_TRANSIT","TRIP_IN_TRANSIT","TRIP_CANCEL_BY_ADMIN"];
  
  if(is_user_login == 'yes')
  {
      var OneSignal = window.OneSignal || [];

      OneSignal.push(function() {
        /*   browser support onsignal notificatins are not*/
        /*var isPushSupported = OneSignal.isPushNotificationsSupported();
        if (!isPushSupported) {
          swal('Warning','Some features may not work properly becasue this browser does not support desktop notifications.','warning');
          return;
        } */

      });

      OneSignal.push(function() {
        OneSignal.init({
          appId: one_signal_app_id,
          safari_web_id : 'web.quickPick',
          autoRegister: true,
          notifyButton: {
            enable: true,
          },
          notificationClickHandlerMatch:'exact',
          notificationClickHandlerAction:'focus',
        });
      });

      OneSignal.push(function() {
        
        OneSignal.isPushNotificationsEnabled(function(isEnabled) {
          console.log(isEnabled);
          if (!isEnabled){
            /*var notification_html = "<div class='show-notification'><span><img src='"+onsignal_image_url+"' alt='notification warning' />Some features may not work properly if you don't allow the desktop notifications access.</span></div>";
            $('#onesignal_notification_div').html(notification_html);
            $("body").addClass("notifications-fix");
            return;*/
          }
        });
        
        OneSignal.sendTags({
          'active_user_id': active_user_id
        }).then(function(tagsSent) {
          //console.log(tagsSent);   
        });

        OneSignal.on('subscriptionChange', function (isSubscribed) {
          if(!isSubscribed){
            var notification_html = "<div class='show-notification'><span><img src='"+onsignal_image_url+"' alt='notification warning' />Some features may not work properly if you don't allow the desktop notifications access.</span></div>";
            $('#onesignal_notification_div').html(notification_html);
            $("body").addClass("notifications-fix")
            return;
          }
          else{
            $('#onesignal_notification_div').html('');
            $("body").removeClass("notifications-fix")
          }
        });

        OneSignal.on('notificationPermissionChange', function(permissionChange) {
          var currentPermission = permissionChange.to;
          console.log('New permission state:', currentPermission);
          $('#onesignal_notification_div').html('');
          $("body").removeClass("notifications-fix");
        });

      });
      
      OneSignal.push(["getNotificationPermission", function(permission) {
        console.log("Site Notification Permission:", permission);
      }]);

      OneSignal.push(function() {
        
        OneSignal.on('notificationDisplay', function (event) {
          
          console.log('notificationDisplay-->',event);

          var notification_type = '';
          if(event.data.notification_type!=undefined && event.data.notification_type!=''){
            notification_type = event.data.notification_type;
          }

          if(active_user_type == 'user' && notification_type!=''){
            if(($.inArray( current_page_slug, arr_user_refresh_pages)!= -1) && ($.inArray( notification_type, arr_user_notification_type)!= -1)){
              if(event.content!=undefined && event.content!=''){
                swal('Success!',event.content,'success');
                setTimeout(function(){
                  location.reload();
                },4000);
              }else{
                location.reload();
              }
            }
          }

          if(active_user_type == 'driver' && notification_type!=''){
            if(($.inArray( current_page_slug, arr_driver_refresh_pages)!= -1) && ($.inArray( notification_type, arr_driver_notification_type)!= -1)){
              if(event.content!=undefined && event.content!=''){
                swal('Success!',event.content,'success');
                setTimeout(function(){
                  location.reload();
                },4000);
              }else{
                location.reload();
              }
            }
          }
          //console.warn('OneSignal notification displayed:', event,current_page_slug);
        });

        OneSignal.push(["addListenerForNotificationOpened", function(data) {
          console.log("Received NotificationOpened:");
          console.log(data);
        }]);
                
      });
  }
  else
  {
        var OneSignal = window.OneSignal || [];
        OneSignal.push(function() {
          OneSignal.init({
            appId: one_signal_app_id,
            safari_web_id : 'web.quickPick',
            autoRegister: false,
            notifyButton: {
              enable: true,
            },
          });
        });
        
        // OneSignal.push(["setSubscription", false]);  

        OneSignal.push(function() {
          OneSignal.getUserId(function(userId) {
            console.log("OneSignal User ID:", userId);
          });

          OneSignal.deleteTag(["active_user_id"]);
        });
        // OneSignal.push(["setSubscription", false]);
        // OneSignal.push(["getNotificationPermission", function(permission) {
        //   console.log("Site Notification Permission:", permission);
        // }]);


        // OneSignal.push(function() {
        //   OneSignal.on('notificationPermissionChange', function(permissionChange) {
        //     var currentPermission = permissionChange.to;
        //     console.log('New permission state:', currentPermission);
        //   });

        //   // OneSignal.on('customPromptClick', function(promptClickResult) {
        //   //   var promptClickResult = permissionChange.result;
        //   //   console.log('HTTP Pop-Up Prompt click result:', promptClickResult);
        //   // });
        // });

        // OneSignal.push(function() {

        //   var isPushSupported = OneSignal.isPushNotificationsSupported();
        //   if (!isPushSupported) {
        //     console.log('Push notifications are not supported');
        //   } 
          
        //   /* These examples are all valid */
        //   OneSignal.isPushNotificationsEnabled(function(isEnabled) {
        //     if (isEnabled)
        //       console.log("Push notifications are enabled!");
        //     else
        //       console.log("Push notifications are not enabled yet.");    
        //   });
        // });
  }
  
  // $(document).ready(function(){
  //   setTimeout(function(){
  //     console.clear();
  //   },2000);
  // });
</script>

<!-- Facebook Pixel Code -->
<script>
  !function(f,b,e,v,n,t,s)
  {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
  n.callMethod.apply(n,arguments):n.queue.push(arguments)};
  if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
  n.queue=[];t=b.createElement(e);t.async=!0;
  t.src=v;s=b.getElementsByTagName(e)[0];
  s.parentNode.insertBefore(t,s)}(window, document,'script',
  'https://connect.facebook.net/en_US/fbevents.js');
  fbq('init', '2059834794038674');
  fbq('track', 'PageView');
</script>
<noscript><img height="1" width="1" style="display:none"
  src="https://www.facebook.com/tr?id=2059834794038674&ev=PageView&noscript=1"
/></noscript>
<!-- End Facebook Pixel Code -->

<!-- conversion tracking code-->

<script>
  fbq('track', 'Lead');
</script>

</head>
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-123127766-1"></script>
<script>
window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);}
gtag('js', new Date());
gtag('config', 'UA-123127766-1');
</script>
<body >{{-- class="notifications-fix" --}}
<div id="main"></div>
<header class="inner-header">
   @include('front.layout.home-header')
</header>



