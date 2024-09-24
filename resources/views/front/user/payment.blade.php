 @extends('front.layout.master')                

    @section('main_content')

    <?php $user_path     = config('app.project.role_slug.user_role_slug'); ?>

    <link rel="stylesheet" type="text/css" href="{{ url('/') }}/css/admin/sweetalert.css" />
    <script src="{{ url('/') }}/js/admin/sweetalert.min.js"></script>
    <script type="text/javascript" src="{{ url('/js/admin') }}/sweetalert_msg.js"></script>

    <div class="blank-div"></div>
    <div class="email-block">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6 col-md-6 col-lg-6">
                    <div class="headding-text-bredcr">
                       Payments
                    </div>
                </div>
                <div class="col-sm-6 col-md-6 col-lg-6">
                    <div class="bredcrum-right">
                        <a href="index.html" class="bredcrum-home"> Dashboard </a>
                        <span class="arrow-righ"><i class="fa fa-angle-right"></i></span>
                        Payments
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--dashboard page start-->
    <div class="main-wrapper">
        <div class="container-fluid">
            <div class="row">
               @include('front.user.left_bar')

                <div class="middle-bar">
                    
                    @include('front.layout._operation_status')
                    
                    <div class="btns-wrapper change-pass">
                       <a href="{{ url('/').'/'.$user_path.'/payment/add_card'}}">
                           <button type="button" class="white-btn chan-left">Add Card</button>
                       </a>
                    </div>
                    <br>

                    <div class="dash-white-main padding-less">
                        <div class="form-main-block">
                            
                            
                            @if(isset($arr_data['data']) && sizeof($arr_data['data'])>0)

                                @foreach($arr_data['data'] as $key => $value)
                                    
                                    <div class="notification-block">
                                        <div class="notification-icon">
                                            <img src="{{ url('/')}}/images/my-job-logo-img.png" alt="" />
                                        </div>
                                        <div class="notification-content">
                                            <div class="noti-head">
                                                {{isset($value['brand']) ? $value['brand'] : '-'}}
                                            </div>
                                            <div class="noti-head-content">
                                                {{isset($value['masked_card_number']) ? $value['masked_card_number'] : '-'}}
                                            </div>
                                        </div>
                                        <button type="button" class="close-noti" data-card-id="{{ isset($value['id']) ? base64_encode($value['id']) : '0' }}" onclick="deleteCardDetails(this)">
                                            <img src="{{ url('/')}}/images/noti-close-icon.png" alt="" class="gray-close-icon" />
                                            <img src="{{ url('/')}}/images/noti-close-icon-active.png" alt="" class="active-close-icon" />
                                        </button>
                                        <div class="clr"></div>
                                    </div>

                                @endforeach
                            @else

                                <div class="no-record-block">
                                    <span>Please add a card.</span> 
                                </div>

                            @endif

                        </div>
                    </div>

                    @if(isset($arr_pagination) && sizeof($arr_pagination)>0)
                        <div class="pagtna-main-wrapp">{!! $arr_pagination !!}</div>
                    @endif

                </div>
                @include('front.user.right_bar')
            </div>
        </div>
    </div>
<script type="text/javascript">

    var curr_module_url = "{{url('/user/payment')}}";

    function  deleteCardDetails(ref) {
        
        if($(ref).attr('data-card-id')!=undefined)
        {
            swal({
              title: "Are you sure ?",
              text: 'Do you really want to delete card ?',
              type: "warning",
              showCancelButton: true,
              confirmButtonColor: "#DD6B55",
              confirmButtonText: "Yes",
              cancelButtonText: "No",
              closeOnConfirm: false,
              closeOnCancel: true
            },
            function(isConfirm)
            {
                if(isConfirm==true)
                {
                    swal.close();   
                    var card_id = $(ref).attr('data-card-id');

                    window.location.href = curr_module_url+'/delete_card?card_id='+card_id
                }
            });
        }

    }
</script>
@stop