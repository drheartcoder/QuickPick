<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title>{{ isset($page_title)?$page_title:"" }} - {{ config('app.project.name') }}</title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <!-- Place favicon.ico and apple-touch-icon.png in the root directory -->

        <!--base css styles-->
        <link rel="stylesheet" href="{{ url('/') }}/assets/bootstrap/css/bootstrap.min.css">
        <link rel="stylesheet" href="{{ url('/') }}/assets/font-awesome/css/font-awesome.min.css">

        <!--page specific css styles-->
        <link rel="stylesheet" type="text/css" href="{{ url('/') }}/assets/bootstrap-fileupload/bootstrap-fileupload.css" />

        <!--flaty css styles-->
        <link rel="stylesheet" href="{{ url('/') }}/css/admin/flaty.css">
        <link rel="stylesheet" href="{{ url('/') }}/css/admin/flaty-responsive.css">
        
        <link rel="stylesheet" href="{{ url('/') }}/assets/jquery-ui/jquery-ui.min.css">
        <link rel="stylesheet" type="text/css" href="{{ url('/') }}/css/admin/sweetalert.css" />

        <link rel="stylesheet" type="text/css" href="{{ url('/') }}/assets/bootstrap-switch/static/stylesheets/bootstrap-switch.css" />
        <link rel="stylesheet" type="text/css" href="{{ url('/') }}/assets/bootstrap-wysihtml5/bootstrap-wysihtml5.css" />
        
        <link rel="stylesheet" type="text/css" href="{{ url('/') }}/css/admin/select2.min.css" />
       
       <!-- Auto load email address -->
        <link rel="stylesheet" type="text/css" href="{{ url('/') }}/assets/chosen-bootstrap/chosen.min.css" />
                
        <!--basic scripts-->
        <script src="{{ url('/') }}/js/admin/sweetalert.min.js"></script>
        <script src="{{ url('/') }}/assets/base64.js"></script>

        <!-- This is custom js for sweetalert messages -->
        <script type="text/javascript" src="{{ url('/js/admin') }}/sweetalert_msg.js"></script>
        <!-- Ends -->
                                
        <script>window.jQuery || document.write('<script src="{{ url('/') }}/assets/jquery/jquery-2.1.4.min.js"><\/script>')</script>
        
        <script src="{{ url('/') }}/assets/jquery-ui/jquery-ui.min.js"></script>
        <script src="{{ url('/') }}/js/admin/select2.min.js"></script>
        
        <!-- Geolocations script-->
       {{--  <script  src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBLKCmJEIrOrzdRklZHYer8q9qx2XLJ4Vs&sensor=false&libraries=places"></script> --}}
        
        <script src="{{ url('/') }}/assets/jquery.geocomplete.min.js"></script>

        <link rel="stylesheet" type="text/css" href="{{ url('/') }}/assets/jquery-tags-input/jquery.tagsinput.css" />
    
        <link rel="stylesheet" type="text/css" href="{{ url('/') }}/assets/bootstrap-duallistbox/duallistbox/bootstrap-duallistbox.css" />
        <link rel="stylesheet" type="text/css" href="{{ url('/') }}/assets/dropzone/downloads/css/dropzone.css" />
        <link rel="stylesheet" type="text/css" href="{{ url('/') }}/assets/bootstrap-colorpicker/css/colorpicker.css" />

        <link rel="stylesheet" type="text/css" href="{{ url('/') }}/assets/chosen-bootstrap/chosen.min.css" />
        <link rel="stylesheet" type="text/css" href="{{ url('/') }}/assets/font-awesome/css/font-awesome-animation.min.css" />

        <link rel="stylesheet" type="text/css" href="{{ url('/') }}/css/admin/jquery.multiselect.css" />

         <link href="{{ url('/') }}/assets/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <link href="{{ url('/') }}/assets/font-awesome.css" rel="stylesheet" type="text/css" />
       
        <script src="{{ url('/') }}/js/admin/image_validation.js"></script>
        <script src="{{ url('/') }}/js/admin/jquery.multiselect.js"></script>
        <script src="{{ url('/') }}/js/admin/ajax_loader.js"></script>
        <?php  
            $admin_type = ": Admin";
            
            $login_user_id   = 0;
            $login_user_role = '';
            $user = Sentinel::check();
            
            if($user)
            {
                $login_user_id = isset($user->id) ? $user->id :0;

                if($user->inRole(config('app.project.role_slug.admin_role_slug')))
                {
                    $admin_type = ": Admin";
                    $login_user_role = 'ADMIN';
                }
                else if($user->inRole(config('app.project.role_slug.subadmin_role_slug')))
                {
                    $admin_type = ": Sub-Admin";
                    $login_user_role = 'SUB_ADMIN';
                }
                else if($user->inRole(config('app.project.role_slug.company_role_slug')))
                {
                    $admin_type = ": Company";
                    $login_user_role = 'COMPANY';
                }

            }

            $locations_url_path = url('/common');
        ?>
        <script type="text/javascript">
            var site_admin_url = '{{ url("/") }}';
            var locations_url_path = '{{$locations_url_path}}';
        </script>
     <script>
         function isNumberKey(evt) {
            var charCode = (evt.which) ? evt.which : evt.keyCode;
            
            if (charCode == 190 || charCode == 46 ) 
              return true;
            
            if (charCode > 31 && (charCode < 48 || charCode > 57 )) 
            return false;
            
            return true;
          }
     </script>   
    </head>

    <body class="{{ theme_body_color() }}">
    <?php
            $admin_path = config('app.project.admin_panel_slug');?>
        <!-- BEGIN Theme Setting -->
        
        <!-- END Theme Setting -->

        <!-- BEGIN Navbar -->
        <div id="navbar" class="navbar {{ theme_navbar_color() }}">
            <button type="button" class="navbar-toggle navbar-btn collapsed" data-toggle="collapse" data-target="#sidebar">
                <span class="fa fa-bars"></span>
            </button>
            <a class="navbar-brand" href="#">
                <small>
                    <i class="fa fa-desktop"></i>
                    <?php  
                        $admin_type = ": Admin";
                        $user = Sentinel::check();
                        if($user)
                        {
                            if($user->inRole(config('app.project.role_slug.admin_role_slug')))
                            {
                                $admin_type = ": Admin";
                            }
                            else if($user->inRole(config('app.project.role_slug.subadmin_role_slug')))
                            {
                                $admin_type = ": Sub-Admin";
                            }
                        }
                    ?>   
                    {{ config('app.project.name') }} {{ $admin_type or '' }}
                </small>
            </a>

            {{-- {{dd($arr_notifications)}} --}}
            <!-- BEGIN Navbar Buttons -->
            <ul class="nav flaty-nav pull-right">
               

                <li class="hidden-xs">
                    <a href="{{ url('/').'/'.$admin_path }}/messages">
                        <i class="fa fa-envelope"></i>
                        <span class="badge badge-warning" id="message_count">0</span>
                    </a>
                </li>

                <!-- BEGIN Button Tasks -->
                <li class="hidden-xs">
                    <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                        <i class="fa fa-tasks"></i>
                        <span class="badge badge-warning" id="notification_count">0</span>
                    </a>
                    
                    <!-- BEGIN Tasks Dropdown -->
                    <div class="dropdown-navbar dropdown-menu notifi-drop">
                    <ul class=" create-scrolls">
                        <li class="nav-header">
                            Notifications
                        </li>

                        {{-- <li class="more">
                            <a href="#">See tasks with details</a>
                        </li> --}}
                    </ul>

                        <div class="notification-all">
                                <a href="{{ url('/').'/'.$admin_path }}/notification">
                                   See All Notifications
                                </a>
                         </div>
                         </div>
                    <!-- END Tasks Dropdown -->
                </li>
                <!-- END Button Tasks -->

                <!-- BEGIN Button Notifications -->
               
                <!-- END Button Messages -->

                <!-- BEGIN Button User -->
                <li class="user-profile">
                    <a data-toggle="dropdown" href="#" class="user-menu dropdown-toggle">

                    <?php
                        $obj_data  = Sentinel::check();
                        if($obj_data)
                        {
                           $arr_data = $obj_data->toArray();    
                        }

                       ?>


                    <?php 
                        $profile_img = isset($arr_data['profile_image'])  ? $arr_data['profile_image'] : "";
                    ?>                

                    <img class="nav-user-photo" src="{{ get_resized_image($profile_img,config('app.project.img_path.user_profile_images'),119,148) }}" alt="">
                        <span class="hhh" id="user_info">
                          Welcome {{$arr_data['first_name'] or ''}}
                        </span>
                        <i class="fa fa-caret-down"></i>
                    </a>

                    <!-- BEGIN User Dropdown -->
                    <ul class="dropdown-menu dropdown-navbar" id="user_menu">
                        <li>
                            <a href="{{ url('/').'/'.$admin_path }}/change_password" >
                                <i class="fa fa-key"></i>
                                Change Password
                            </a>    
                        </li> 

                        <li>
                            <a href="{{ url('/').'/'.$admin_path }}/profile" >
                                <i class="fa fa-user"></i>
                                Edit Profile
                            </a>    
                        </li> 

                        <li>
                             <a href="{{ url('/').'/'.$admin_path }}/logout "> 
                                <i class="fa fa-power-off"></i>
                                Logout
                            </a>
                        </li>
                    </ul>
                    <!-- BEGIN User Dropdown -->
                </li>
                <!-- END Button User -->
            </ul>
            <!-- END Navbar Buttons -->
        </div>
        <!-- END Navbar -->
        
        <!-- BEGIN Container -->
        <div class="container {{ theme_sidebar_color() }}" id="main-container">
    <script type="text/javascript">
    
    var request_segment   = "{{Request::segment(2)}}";
    var login_user_id   = "{{isset($login_user_id) ? $login_user_id : 0}}";
    var login_user_role = "{{isset($login_user_role) ? $login_user_role : ''}}";

    $( document ).ready(function() {
        check_new_notifications();
        start_checking();
    });

    function start_checking(){    
        setInterval(function() {
            check_new_notifications();
        },60000);
    }

    function check_new_notifications(){
      
        $.ajax({
                url:locations_url_path+'/get_new_notification?user_id='+btoa(login_user_id)+'&user_role='+login_user_role,
                type:'GET',
                data:'flag=true',
                dataType:'json',
                success:function(response)
                {
                    if(response.status == 'success'){
                        build_notification_html(response.arr_notifications);
                        $('#message_count').html(response.unread_message_count);
                    }   
                    else{
                        build_notification_html();
                        $('#message_count').html('0');
                    }
                }     
        });
    }
        function change_notification_status(ref) {
            var data_id = $(ref).attr('data-id');
            var data_view_url = $(ref).attr('data-view-url');
       
            if(data_id!=0 && data_id!='' && data_view_url!='' && data_view_url!=undefined){
                $.ajax({
                        url:locations_url_path+'/change_notification_status?notification_id='+btoa(data_id),
                        type:'GET',
                        data:'flag=true',
                        dataType:'json',
                        success:function(response)
                        {
                            window.location.replace(site_admin_url+data_view_url);  // site_admin_url+
                        }     
                });
            }
            
        }

        function build_notification_html(arr_notification)
        {
            $('#notification_count').html(arr_notification.length);

            var notification_html = '';
            var dashboard_notification_html = '';

            if(arr_notification.length>0 && arr_notification!=undefined)
            {
                $( ".nav-header" ).nextAll().html( "" );
                
                $.each(arr_notification,function(index,value){

                    var notification_type = value['notification_type'] ? value['notification_type'] :'-';
                    var title             = value['title'] ? value['title'] :'-';

                    var full_title = notification_type+' : '+title;

                    notification_html += '<li>';
                    notification_html += '<a href="javascript:void(0);" data-view-url="'+value.view_url+'" data-id="'+value.id+'" onclick="change_notification_status(this);">'+full_title;
                    notification_html += '</a>';
                    notification_html += '</li>';

                    if(request_segment == 'dashboard'){

                        dashboard_notification_html +=  '<div class="list-group">';
                        dashboard_notification_html +=  '<a href="javascript:void(0);" data-view-url="'+value.view_url+'" data-id="'+value.id+'" onclick="change_notification_status(this);" class="list-group-item">';
                        dashboard_notification_html +=  '<span class="chat-icnsin"><i class="fa fa-comment-o"></i></span> '+full_title;
                        dashboard_notification_html +=  '</a>';
                        dashboard_notification_html += '</div>';
                    }

                });
                $(notification_html).insertAfter( ".nav-header" );
                if(request_segment == 'dashboard'){
                    $('#dashbord_notification_div').html('');
                    $('#dashbord_notification_div').html(dashboard_notification_html);
                }



            }
            else
            {
                $( ".nav-header" ).nextAll().html( "" );
                notification_html =  '<li>'+
                                        '<a href="javascript:void(0);">'+
                                            '<div class="clearfix">'+
                                                '<span class="pull-left">No new notification available.</span>'+
                                            '</div>'+
                                        '</a>'+
                                    '</li>';
                $(notification_html).insertAfter( ".nav-header" );
                
                if(request_segment == 'dashboard'){
                    
                    $('#dashbord_notification_div').html('');
                    
                    var msg = 'No new notification available.';

                    dashboard_notification_html +=  '<div class="list-group">';
                    dashboard_notification_html +=  '<a href="javascript:void(0);" class="list-group-item">';
                    dashboard_notification_html +=  '<span class="chat-icnsin"><i class="fa fa-comment-o"></i></span>'+msg;
                    dashboard_notification_html +=  '</a>';
                    dashboard_notification_html += '</div>';

                    $('#dashbord_notification_div').html(dashboard_notification_html);
                }

            }
        }
    </script>    
