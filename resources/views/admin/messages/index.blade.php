@extends('admin.layout.master')


    @section('main_content')
<!-- Scroll Start Here -->
<link href="{{ url('/') }}/css/admin/jquery.mCustomScrollbar.css" rel="stylesheet" type="text/css" />
<script src="{{ url('/') }}/js/admin/jquery.mCustomScrollbar.concat.min.js"></script>
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
<style>
.msg-left-block {
    margin-bottom: 20px;
    position: relative;
}

    .min-imn-height{ height: 500px; overflow-y: auto;}
    .main-mesages {
 
    background-color: #f9f9f9;
    padding: 30px 20px;
    height: 700px; position: relative; 
}
    .text-type-msg {
    color: #3a3a3a;
    background: #f8fafc;
    padding: 7px 15px;
    border-radius: 0 5px 5px 5px;
    box-shadow: 0 2px 2px rgba(0, 0, 0, 0.33);
    display: inline-block;
    position: relative;
    margin-left: 24px;
}
    .date-time-block {
    font-size: 11px;
    color: #8a8a8a;
    margin-top: 3px;
}
    .arrow-message-block {
    position: absolute;
    left: -20px;
    top: 0;
}
    .msg-right-block .msg-profile-pic {
    right: 0px;
    left: auto;
}
    .arrow-green-message {
    position: absolute;
    right: -20px;
    top: 0;
}

.text-type-msg.right-name span{ color: #3c5f00; float: left;}
.text-type-msg p{margin-left: 80px;}

.text-type-msg span{display: block; float: left; font-weight: 600; color: #117d76;}
    .map-icnss{display: block; cursor: pointer;}
    .searchboxs.newsearchboxs{position: absolute;bottom: 10px; width: 94%;}
    .msg-right-block .msg-profile-pic{right: 0px; left: auto}
.msg-right-block .text-type-msg{float: right; background: #92c44f; color: #ffffff; border-radius: 5px 0 5px 5px; text-align: right; margin: 0 24px 0 0; position: relative;}
.msg-right-block .text-type-msg:after{content: ''; width: 100%; height: 100%;
background: -moz-linear-gradient(top, rgba(125,185,232,0) 1%, rgba(255,255,255,0.96) 96%, rgba(255,255,255,1) 100%); /* FF3.6-15 */
background: -webkit-linear-gradient(top, rgba(125,185,232,0) 1%,rgba(255,255,255,0.96) 96%,rgba(255,255,255,1) 100%); /* Chrome10-25,Safari5.1-6 */
background: linear-gradient(to bottom, rgba(125,185,232,0) 1%,rgba(255,255,255,0.96) 96%,rgba(255,255,255,1) 100%); /* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#007db9e8', endColorstr='#ffffff',GradientType=0 ); /* IE6-9 */position: absolute; top: 0;left: 0;    opacity: 0.2;
 }
</style>
<!-- Scroll End Here -->
    <!-- BEGIN Page Title -->
     <link rel="stylesheet" type="text/css" href="{{ url('/') }}/assets/data-tables/latest/dataTables.bootstrap.min.css">
    <div class="page-title">
        <div>

        </div>
    </div>
    <!-- END Page Title -->

    <!-- BEGIN Breadcrumb -->
    <div id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="fa fa-home"></i>
                <a href="{{ url($admin_panel_slug.'/dashboard') }}">Dashboard </a>
            </li>
            
           @if(isset($prev_module_name) && $prev_module_name!='') 
                <span class="divider">
                    <i class="fa fa-angle-right"></i>
                    <i class="fa fa-users"></i>
                </span>
                <li>
                        <a href="{{ isset($prev_module_url) ? $prev_module_url : '' }}">{{ isset($prev_module_name)?$prev_module_name:"" }}</a>
                </li>
            @endif

            <span class="divider">
                <i class="fa fa-angle-right"></i>
                <i class="fa fa-comments"></i>
            </span>
            <li class="active">{{ isset($module_title)?$module_title:"" }}</li>
        </ul>
      </div>
    <!-- END Breadcrumb -->

    <!-- BEGIN Main Content -->
    <div class="row">
      <div class="col-md-12">

          <div class="box {{ $theme_color }}">
            <div class="box-title">
              <h3>
                <i class="fa fa-list"></i>
                {{ isset($module_title)?$module_title:"" }}
            </h3>
            <div class="box-tool">
                <a data-action="collapse" href="#"></a>
                <a data-action="close" href="#"></a>
            </div>
        </div>
        </div>
       
        <div class="box-content message">

            <div class="button-sctions">
                <div class="clearfix"></div>
            </div>
        
            <div class="row">
                
                <div class="col-sm-12 col-md-5 col-lg-4">
                    <div class="map-main-page-inner-tab">
                        <a href="javascript:void(0);" class="active">Chat List</a>
                        <select class="form-control" onchange="loadCurrentTypeUser(this);">
                            <option value="">Select User Type</option>
                            <option value="user" @if(isset($user_role) && $user_role == 'user') selected="" @endif >Users</option>
                            <option value="driver" @if(isset($user_role) && $user_role == 'driver') selected="" @endif>Drivers</option>
                            <option value="company" @if(isset($user_role) && $user_role == 'company') selected="" @endif>Companies</option>

                        </select>
                        <br>
                    </div>
                    <div class="clearfix"></div>
                    
                    @if(isset($arr_data) && sizeof($arr_data)>0)
                        <div class="searchboxs" id="search_drivers" >
                            <input name="search_term" type="text" placeholder="Search" onkeyup="highlightSearch(this)" />
                            <div class="map-icnss"><i class="fa fa-search"></i></div>
                        </div>
                    @endif
                
                    <div class="chat-messagess">
                       <div class="clearfix"></div>
                        <ul class="content-txt1 content-d" id="driver-list">
                            
                            @if(isset($arr_data) && sizeof($arr_data)>0)
                                @foreach($arr_data as $key => $value)
                                    
                                    <li 
                                        @if(isset($to_user_id) && $to_user_id == $value['id'])
                                            class="active" 
                                        @endif
                                        data-id="{{$key}}" 
                                        data-user-role="{{ isset($user_role) ? $user_role : '' }}" 
                                        data-user-id="{{ isset($value['id']) ? base64_encode($value['id']) : '' }}" onclick="startCurrentChat(this)">
                                        <div class="avatar-outr"> 
                                            <?php
                                                $profile_img = url('/uploads/default-profile.png');
                                                if(isset($value['profile_image']) && $value['profile_image']!='' ) 
                                                {
                                                    if(file_exists($user_profile_base_img_path.$value['profile_image'])){
                                                        $profile_img = $user_profile_public_img_path.$value['profile_image'];
                                                    }   
                                                }
                                            ?>
                                            <div class="avatar-img"><img src="{{$profile_img}}" alt="" /><!--<div class="avatar-img-acti"></div>--></div>
                                        </div>
                                        <div class="avatar-content pull-left" data-user-type="driver">
                                            <div class="avtar-name">
                                                @if(isset($user_role) && $user_role == 'company')
                                                    {{ isset($value['company_name']) ? $value['company_name'] : '' }}
                                                @else
                                                    {{ isset($value['first_name']) ? $value['first_name'] : '' }} {{ isset($value['last_name']) ? $value['last_name'] : '' }}
                                                @endif
                                            </div>
                                        </div>
                                        <div class="clearfix"></div>
                                        @if(isset($value['message_count']) && $value['message_count']>0)
                                            <span class="chat-coun">{{ $value['message_count'] }}</span>
                                        @endif
                                    </li>
                                @endforeach
                            @else
                                <li>
                                   <div class="avatar-outr">
                                   <div class="mp-txs"></div>
                                     </div>
                                    <div class="avatar-content">
                                        <center><div class="avtar-name">Users not available.</div></center>
                                    </div>
                                    <div class="clearfix"></div>
                                </li>    
                            @endif
                            
                            <li id="search_no_records_found" style="display: none;">
                               <div class="avatar-outr">
                               <div class="mp-txs"></div>
                                 </div>
                                <div class="avatar-content">
                                    <center><div class="avtar-name">Users not available.</div></center>
                                </div>
                                <div class="clearfix"></div>
                            </li>    

                        </ul>
                    </div>
                </div>

                <div class="col-sm-12 col-md-5 col-lg-8">
                    
                    <div class="map-main-page-inner-tab">
                        <a href="javascript:void(0);" class="active">
                                Chat with {{isset($to_user_name) ? $to_user_name :''}} 
                        </a>
                    </div>
                
                    <div class="clearfix"></div>
                    <?php

                            $message_arrow_1 = url('/images/message-arrow.png');
                            $message_arrow_2 = url('/images/message-arrow-green.png');
                            $arr_message_id = [];
                    ?>
                    <div class="main-mesages">
                       
                        <div class="min-imn-height chat-messages append-chat-html">
                            
                            @if(isset($arr_previous_chat) && sizeof($arr_previous_chat)>0)
                                @foreach($arr_previous_chat as $key=>$value)
                                    <?php
                                        if(isset($value['id']) && $value['id']!=''){
                                        $arr_message_id[] = strval($value['id']);
                                        }
                                    ?>
                                    @if (isset($value['from_user_id']) && $value['from_user_id'] == $from_user_id)
                                        <div class="msg-left-block msg-right-block">
                                           <div class="text-type-msg right-name">
                                             <p>{{isset($value['message']) ? $value['message'] :''}}</p>
                                           </div>
                                            <div class="msg-left-chat-admin-img"><img src="{{ isset($from_user_profile_image) ? $from_user_profile_image : '' }}" alt="" /></div>
                                           <div class="clearfix"></div>
                                        </div>
                                    @else

                                        <div class="msg-left-block">
                                          <div class="text-type-msg">
                                              <p>{{isset($value['message']) ? $value['message'] :''}}</p>
                                          </div>
                                          <div class="msg-left-chat-admin-img"><img src="{{isset($to_user_profile_image) ? $to_user_profile_image : ''}}" alt="" /></div>
                                          <div class="clearfix"></div>
                                        </div>
                                    @endif        
                                @endforeach
                            @endif

                        </div>

                        <div class="clearfix"></div>

                        <div class="searchboxs newsearchboxs" >
                            <input name="message" id="message" type="text" placeholder="Enter message" />
                            <a class="map-icnss" onclick="sendMessage()"><i class="fa fa-paper-plane"></i></a>
                        </div>
                         
                    
                    </div>
                    
                </div>

            </div>
        </div>
  </div>
  <input type="hidden" name="arr_message_id" id="arr_message_id" value="{{isset($arr_message_id) ? json_encode($arr_message_id) : json_encode([])}}">
<script type="text/javascript">

    $('.append-chat-html').scrollTop($('.append-chat-html')[0].scrollHeight);

    var arr_tmp_chat_message_id = $('#arr_message_id').val();
    
    var arr_chat_message_id = [];

    if(arr_tmp_chat_message_id!='')
    {
        arr_chat_message_id  = JSON.parse(arr_tmp_chat_message_id);
    }

    var module_url_path = '{{$module_url_path}}';

    function loadCurrentTypeUser(ref){
        var user_role = $(ref).val();

        if(module_url_path!=undefined && user_role!=undefined && user_role!=''){
            var url = module_url_path+'?user_role='+user_role;
            window.location.href = url;
        }
    }

    function startCurrentChat(ref) {

        var user_id   = $(ref).attr('data-user-id');
        var user_role = $(ref).attr('data-user-role');
        
        if(module_url_path!=undefined && user_role!=undefined && user_role!='' && user_id!=undefined && user_id!=''){
            var url = module_url_path+'?user_role='+user_role+'&user_id='+user_id;
            window.location.href = url;
        }
    }

    function highlightSearch(ref){
        
        var inputVal = $(ref).val();

        var ul = $('#driver-list');
        var count = 0;

        ul.find('li').each(function(index, row) {
            
            var li = $(row);
            var id = $(this).attr('data-id');

            var found = false;
            li.find('div.avatar-content').each(function(index, row){
                var div = $(row);
                var user_type = $(div).attr('data-user-type');
                if(user_type == "driver")
                {
                    var regExp = new RegExp(inputVal, 'i');
                    if (regExp.test($(div).text())) 
                    {
                        $('#'+id).hide();
                        found = true;
                        return false;
                    } 
                }
                else{
                    $('#'+id).show();
                }
            });  
            if (found == true)
            {
              $(li).show();
                count = count  + 1;
            } 
            else 
            {
              $(li).hide();
            }
        });
        if (count == 0) {
          $('#search_no_records_found').show();
        }
        else {
          $('#search_no_records_found').hide();
        }
    }


    /*
    |from user id = admin who recived messages from
    |in admin chat admin will be from user and admin whom chatting is to user_id
    */
    var from_user_id = '{{isset($from_user_id) ? $from_user_id : ''}}'; //i.e. admin id
    var to_user_id   = '{{isset($to_user_id) ? $to_user_id : ''}}'; //i.e. admin id

    var from_user_name = "{{isset($from_user_name) ? $from_user_name :''}}";
    var to_user_name   = "{{isset($to_user_name) ? $to_user_name :''}}";

    var from_user_profile_image = "{{ isset($from_user_profile_image) ? $from_user_profile_image : '' }}";
    var to_user_profile_image   = "{{ isset($to_user_profile_image) ? $to_user_profile_image : '' }}";

    var user_role = '{{ isset($user_role) ? strtoupper($user_role) : '' }}';

    $( document ).ready(function() {
        
        setTimeout(function(){
            start_auto_load_chat_history();
        }, 2000);
    });

    function start_auto_load_chat_history() {

        setInterval(function() {
            load_current_chat_history() 
        }, 5000);
    }

    function load_current_chat_history() 
    {
        $.ajax({
            url : module_url_path+'/get_current_chat_messages',
            type : "GET",
            dataType: 'JSON',
            data : {from_user_id:from_user_id,to_user_id:to_user_id},
            success:function(response){
                if(response.status == 'success'){
                    build_chat_html(response.data);
                }
            },
            error:function(response){
            }
        });

    }

    function  build_chat_html(response) {
        
        if(arr_chat_message_id.length>0){

            response.forEach(function (value, index) {
                if (value != undefined && value.id!=undefined) {
                    
                    if($.inArray(String(value.id), arr_chat_message_id) == -1){
                        var chat_html = '';
                        
                        if (value['from_user_id'] != undefined && value['from_user_id'] == from_user_id) {

                            chat_html +='<div class="msg-left-block msg-right-block">';
                            chat_html +=    '<div class="text-type-msg right-name">';
                            chat_html +=       '<p>' + value.message+'</p>';
                            chat_html +=    '</div>';
                            chat_html +=    '<div class="msg-left-chat-admin-img"><img src="'+from_user_profile_image+'" alt=""></div>';
                            chat_html +=    '<div class="clearfix"></div>';
                            chat_html +='</div>';
                        }
                        else{
                            chat_html += '<div class="msg-left-block">';
                            chat_html +=    '<div class="text-type-msg">';
                            chat_html +=        '<p>'+ value.message+'</p>';
                            chat_html +=    '</div>';
                            chat_html +=    '<div class="msg-left-chat-admin-img"><img src="'+to_user_profile_image+'" alt=""></div>';
                            chat_html +=    '<div class="clearfix"></div>';
                            chat_html += '</div>';
                        }
                        arr_chat_message_id.push(String(value.id));
                        $('.append-chat-html').append(chat_html);
                        $('.append-chat-html').scrollTop($('.append-chat-html')[0].scrollHeight);
                    }
                }
            });
        }
        else
        {
            var chat_html = '';
            response.forEach(function (value, index) {
                if (value != undefined && value.id!=undefined) {
                    
                    arr_chat_message_id.push(String(value.id));
                    
                    if (value['from_user_id'] != undefined && value['from_user_id'] == from_user_id) {
                            
                        chat_html +='<div class="msg-left-block msg-right-block">';
                        chat_html +=    '<div class="text-type-msg right-name">';
                        chat_html +=       '<p>' + value.message+'</p>';
                        chat_html +=     '</div>';
                        chat_html +=     '<div class="msg-left-chat-admin-img"><img src="'+from_user_profile_image+'" alt=""></div>';
                        chat_html +=    '<div class="clearfix"></div>';
                        chat_html +='</div>';
                    }
                    else{
                        
                        chat_html += '<div class="msg-left-block">';
                        chat_html +=    '<div class="text-type-msg">';
                        chat_html +=        '<p>'+ value.message+'</p>';
                        chat_html +=    '</div>';
                        chat_html +=    '<div class="msg-left-chat-admin-img"><img src="'+to_user_profile_image+'" alt=""></div>';
                        chat_html +=    '<div class="clearfix"></div>';
                        chat_html += '</div>';
                    }
                }
            });

            $('.append-chat-html').html('');
            $('.append-chat-html').append(chat_html);
            $('.append-chat-html').scrollTop($('.append-chat-html')[0].scrollHeight);

        }
        
    }

    function sendMessage(){
        
        var message = '';
        var tmp_message = $('#message').val();
        message = $.trim(tmp_message);
        
        if(message!=''){

            ajax_send_messsage(message);

            var chat_html = '';

            chat_html +='<div class="msg-left-block msg-right-block">';
            chat_html +=    '<div class="text-type-msg right-name">';
            chat_html +=       '<p>' + message+'</p>';
            chat_html +=    '</div>';
            chat_html +=    '<div class="msg-left-chat-admin-img"><img src="'+from_user_profile_image+'" alt=""></div>';
            chat_html +=    '<div class="clearfix"></div>';
            chat_html +='</div>';

            $('.append-chat-html').append(chat_html);
            $('#message').val('');
            $('.append-chat-html').scrollTop($('.append-chat-html')[0].scrollHeight);
        }
    }
    
    $('#message').keyup(function(e){
        if(e.keyCode == 13)
        {
            sendMessage();
        }
    });

    var isRunning = null;

    function ajax_send_messsage(message) {
        
        var _token       = "{{csrf_token()}}";
        
        var obj_data = 
                        {
                            from_user_id : from_user_id,
                            to_user_id   : to_user_id,
                            user_role    : user_role,
                            message      : message,
                            _token       : _token
                        };
        
        isRunning = $.ajax({
            url : module_url_path+'/store_chat',
            type : "POST",
            dataType: 'JSON',
            data : obj_data,
            beforeSend:function(response){
                
                $('#message').attr("disabled");
                if(isRunning != null){
                    return false;
                }
            },
            success:function(response){
                isRunning = null;
                $('#message').removeAttr("disabled");
                if(response.status == 'success')
                {
                    if(response.id!=undefined && response.id!=0){
                        arr_chat_message_id.push(String(response.id));
                    }
                }
            },
            error:function(response){
              isRunning = null;  
              $('#message').removeAttr("disabled");
            }
        });
    }
</script>
@stop