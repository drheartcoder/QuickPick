@extends('front.layout.master')                

@section('main_content')

<?php 
        $user_path     = config('app.project.role_slug.driver_role_slug'); 
?>

 <div class="blank-div"></div>
     <div class="email-block">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6 col-md-6 col-lg-6">
                    <div class="headding-text-bredcr">
                       Messages
                    </div>
                </div>
                <div class="col-sm-6 col-md-6 col-lg-6">
                    <div class="bredcrum-right">
                        <a href="{{ $module_url_path }}" class="bredcrum-home"> Dashboard </a>
                        <span class="arrow-righ"><i class="fa fa-angle-right"></i></span>
                        Messages
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--dashboard page start-->
    <div class="main-wrapper">
        <div class="container-fluid">
            <div class="row">
                @include('front.driver.left_bar')
                <div class="col-sm-9 col-md-10 col-lg-10">
                    <div class="dash-white-main massage">
                                <div data-responsive-tabs class="verticalslide">
                                     <nav>
                                        <div class="search-member-block">
                                            <input type="text" name="Search" placeholder="Search" onkeyup="highlightSearch(this)"/>
                                        </div>
                                        <div class="users-block content-d">
                                            <ul id="driver-list">
                                            
                                            @if(isset($arr_chat_list) && sizeof($arr_chat_list)>0)
                                                @foreach($arr_chat_list as $key => $list)
                                                    
                                                    <li
                                                        data-id="{{$key}}" 
                                                        onclick="window.location.href='{{ url('/').'/'.$user_path.'/messages?to_user_id='.base64_encode($list['id']).'&is_chat_enable='.base64_encode($list['is_chat_enable'])}}'"
                                                        @if(isset($arr_to_user_details['id']) && $arr_to_user_details['id'] == $list['id'])
                                                          class="active" 
                                                        @endif>

                                                        <a href="javascript:void(0);">
                                                            <span class="travles-img">
                                                                @if(isset($list['profile_image']) && $list['profile_image']!='')
                                                                    <img src="{{$list['profile_image']}}" alt="avrt1" /> 
                                                                @else
                                                                    <img src="{{url('/uploads/default-profile.png')}}" alt="avrt1" /> 
                                                                @endif
                                                        </span> 
                                                        <span class="travles-name">{{ isset($list['full_name']) ? $list['full_name'] : '' }}</span></a>
                                                        @if(isset($list['messge_count']) && $list['messge_count']>0)
                                                            <span class="count-messa">{{ isset($list['messge_count']) ? $list['messge_count'] : '0' }}</span>
                                                        @endif
                                                    </li>

                                                @endforeach
                                            @endif
                                            <li id="search_no_records_found" style="display: none;">
                                                <a href="javascript:void(0);">
                                                    <span class="travles-name">Users not available</span>
                                                </a>
                                            </li>  

                                            </ul>
                                        </div>
                                    </nav>
                                    @if(isset($arr_to_user_details) && sizeof($arr_to_user_details)>0)
                                        <div class="chat-travels-name">
                                            @if(isset($arr_to_user_details['roles'][0]['slug']) && $arr_to_user_details['roles'][0]['slug'] == 'company')
                                                {{ isset($arr_to_user_details['company_name']) ? $arr_to_user_details['company_name'] : '' }}
                                            @else
                                                {{ isset($arr_to_user_details['first_name']) ? $arr_to_user_details['first_name'] : '' }} {{ isset($arr_to_user_details['last_name']) ? $arr_to_user_details['last_name'] : '' }}
                                            @endif
                                        </div>
                                        <div class="content">
                                            <section id="tabone">
                                                <div class="messages-section content-d append-chat-html">
                                                    <?php
                                                            $from_user_profile_image = (isset($arr_from_user_details['profile_image']) && $arr_from_user_details['profile_image']!='') ? $arr_from_user_details['profile_image'] : url('/uploads/default-profile.png');
                                                            $to_user_profile_image  = (isset($arr_to_user_details['profile_image']) && $arr_to_user_details['profile_image']!='') ? $arr_to_user_details['profile_image'] : url('/uploads/default-profile.png');
                                                            $arr_message_id = [];

                                                    ?>
                                                    @if(isset($arr_chat_details) && sizeof($arr_chat_details)>0)
                                                        @foreach($arr_chat_details as $key => $details) 
                                                            
                                                            <?php
                                                                if(isset($details['id']) && $details['id']!=''){
                                                                   $arr_message_id[] = strval($details['id']);
                                                                }
                                                            ?>

                                                            @if( (isset($details['from_user_id']) && isset($arr_from_user_details['id'])) && $arr_from_user_details['id'] == $details['from_user_id'])
                                                                <div class="left-message-block right-message-block">
                                                                    <div class="left-message-profile">
                                                                       <img src="{{$from_user_profile_image}}" alt="review-img2" />
                                                                    </div>
                                                                    <div class="left-message-content">
                                                                        <div class="actual-message">
                                                                            {{ isset($details['message']) ? $details['message'] : '' }}
                                                                        </div>
                                                                        {{-- <div class="message-time">
                                                                            {{ isset($details['date']) ? $details['date'] : '' }}
                                                                        </div> --}}
                                                                    </div>                                        
                                                                </div>
                                                            @else
                                                            
                                                                <div class="left-message-block">
                                                                    <div class="left-message-profile">
                                                                        <img src="{{$to_user_profile_image}}" alt="dash-profile-img" />
                                                                    </div>
                                                                    <div class="left-message-content">
                                                                        <div class="actual-message">
                                                                            {{ isset($details['message']) ? $details['message'] : '' }}
                                                                        </div>
                                                                        {{-- <div class="message-time">
                                                                            {{ isset($details['date']) ? $details['date'] : '' }}
                                                                        </div> --}}
                                                                    </div>                                        
                                                                </div>
                                                            @endif

                                                        @endforeach
                                                    @endif
                                                                                                
                                                </div>
                                            </section>
                                            @if(isset($is_chat_enable) && $is_chat_enable == 'YES')
                                                <div class="write-message-block">
                                                    <input type="text" name="message" id="message" placeholder="Enter Message" />                                        
                                                    <button class="send-message-btn" type="button" onclick="sendMessage()"><i class="fa fa-paper-plane"></i></button>
                                                </div>
                                            @endif
                                        </div>
                                    @endif

                                    <div class="clr"></div>
                                </div>
                            </div>
                    
                </div>

            </div>
        </div>
    </div>
 

    <input type="hidden" name="arr_message_id" id="arr_message_id" value="{{isset($arr_message_id) ? json_encode($arr_message_id) : json_encode([])}}">

    <script src="{{url('/js/front/responsivetabs.js')}}"></script>     
    <script>
        $(document).ready(function (){  
            $('[data-responsive-tabs]').responsivetabs({
                initialised : function ()
                {
                },

                change : function (newPanel)
                {
                }
            });
        });
    </script>     
    
    <script type="text/javascript">

        var curr_module_url_path = "{{ url('/').'/'.$user_path}}";

        // $('.append-chat-html').scrollTop($('.append-chat-html')[0].scrollHeight);

        @if(isset($arr_to_user_details) && sizeof($arr_to_user_details)>0)
            $('.append-chat-html').scrollTop($('.append-chat-html')[0].scrollHeight);
        @endif

        function highlightSearch(ref){
        
        var inputVal = $(ref).val();

        var ul = $('#driver-list');
        var count = 0;

        ul.find('li').each(function(index, row) {
            
            var li = $(row);
            var id = $(this).attr('data-id');

            var found = false;
            li.find('span.travles-name').each(function(index, row){
                var div = $(row);

                var regExp = new RegExp(inputVal, 'i');
                if (regExp.test($(div).text())) 
                {
                    $('#'+id).hide();
                    found = true;
                    return false;
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

    var from_user_profile_image = '{{ isset($from_user_profile_image) ? $from_user_profile_image : ''}}';
    var to_user_profile_image = '{{ isset($to_user_profile_image) ? $to_user_profile_image : ''}}';

    var from_user_id = '{{isset($arr_from_user_details['id']) ? $arr_from_user_details['id'] : '0'}}'; 
    var to_user_id = '{{isset($arr_to_user_details['id']) ? $arr_to_user_details['id'] : '0'}}'; 
    
    var arr_tmp_chat_message_id = $('#arr_message_id').val();

    var arr_chat_message_id = [];

    if(arr_tmp_chat_message_id!='')
    {
        arr_chat_message_id  = JSON.parse(arr_tmp_chat_message_id);
    }

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

    function load_current_chat_history() {
        $.ajax({
            url : curr_module_url_path+'/get_current_chat_messages',
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
                        
                        arr_chat_message_id.push(String(value.id));

                        if (value['from_user_id'] != undefined && value['from_user_id'] == from_user_id) {

                            chat_html += '<div class="left-message-block right-message-block">';
                            chat_html += '    <div class="left-message-profile">';
                            chat_html += '       <img src="'+from_user_profile_image+'" alt="review-img2" />';
                            chat_html += '    </div>';
                            chat_html += '    <div class="left-message-content">';
                            chat_html+= '        <div class="actual-message">'+value['message']+'</div>';
                            // chat_html+= '        <div class="message-time">'+value['date']+'</div>';
                            chat_html += '    </div>';
                            chat_html += '</div>';
                        }
                        else
                        {
                            chat_html+= '<div class="left-message-block">';
                            chat_html+= '    <div class="left-message-profile">';
                            chat_html+= '        <img src="'+to_user_profile_image+'" alt="dash-profile-img" />';
                            chat_html+= '    </div>';
                            chat_html+= '    <div class="left-message-content">';
                            chat_html+= '        <div class="actual-message">'+value['message']+'</div>';
                            // chat_html+= '        <div class="message-time">'+value['date']+'</div>';
                            chat_html+= '    </div>';
                            chat_html+= '</div>';
                        }
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
                    
                    if (value['from_user_id'] != undefined && value['from_user_id'] == from_user_id) 
                    {
                        chat_html += '<div class="left-message-block right-message-block">';
                        chat_html += '    <div class="left-message-profile">';
                        chat_html += '       <img src="'+from_user_profile_image+'" alt="review-img2" />';
                        chat_html += '    </div>';
                        chat_html += '    <div class="left-message-content">';
                        chat_html+= '        <div class="actual-message">'+value['message']+'</div>';
                        // chat_html+= '        <div class="message-time">'+value['date']+'</div>';
                        chat_html += '    </div>';
                        chat_html += '</div>';
                    }
                    else
                    {
                        chat_html+= '<div class="left-message-block">';
                        chat_html+= '    <div class="left-message-profile">';
                        chat_html+= '        <img src="'+to_user_profile_image+'" alt="dash-profile-img" />';
                        chat_html+= '    </div>';
                        chat_html+= '    <div class="left-message-content">';
                        chat_html+= '        <div class="actual-message">'+value['message']+'</div>';
                        // chat_html+= '        <div class="message-time">'+value['date']+'</div>';
                        chat_html+= '    </div>';
                        chat_html+= '</div>';
                    }
                }
            });

            $('.append-chat-html').html('');
            $('.append-chat-html').append(chat_html);
            $('.append-chat-html').scrollTop($('.append-chat-html')[0].scrollHeight);

        }
    }

    $('#message').keyup(function(e){
        if(e.keyCode == 13){
            sendMessage();
        }
    });

    function sendMessage(){
        
        var message = '';
        var message = $.trim($('#message').val());
        
        if(message!=''){

            ajax_send_messsage(message);
            
            var chat_html = '';

            chat_html += '<div class="left-message-block right-message-block">';
            chat_html += '    <div class="left-message-profile">';
            chat_html += '       <img src="'+from_user_profile_image+'" alt="review-img2" />';
            chat_html += '    </div>';
            chat_html += '    <div class="left-message-content">';
            chat_html += '        <div class="actual-message">'+message+'</div>';
            // chat_html += '        <div class="message-time">18 Aug 2018, 02:23 AM</div>';
            chat_html += '    </div>';
            chat_html += '</div>';

            $('.append-chat-html').append(chat_html);
            $('#message').val('');
            $('.append-chat-html').scrollTop($('.append-chat-html')[0].scrollHeight);
        }
    }
    
    var isRunning = null;

    function ajax_send_messsage(message) {
        
        var _token       = "{{csrf_token()}}";
        
        var obj_data = 
                        {
                            from_user_id : from_user_id,
                            to_user_id   : to_user_id,
                            user_type    : 'USER',
                            message      : message,
                            _token       : _token
                        };
        
        isRunning = $.ajax({
            url : curr_module_url_path+'/store_chat',
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
                    if(response.enc_id!=undefined && response.enc_id!=0){
                        arr_chat_message_id.push(String(response.enc_id));
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
