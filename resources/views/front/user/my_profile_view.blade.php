<?php $user_path     = config('app.project.role_slug.user_role_slug'); ?>

 @extends('front.layout.master')                

    @section('main_content')

    <div class="blank-div"></div>

    <div class="email-block">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6 col-md-6 col-lg-6">
                    <div class="headding-text-bredcr">
                       My Profile
                    </div>
                </div>
                <div class="col-sm-6 col-md-6 col-lg-6">
                    <div class="bredcrum-right">
                        <a href="{{ $module_url_path }}" class="bredcrum-home"> Dashboard </a>
                        <span class="arrow-righ"><i class="fa fa-angle-right"></i></span>
                        My Profile
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="clr"></div>

    <div class="clearfix"></div>
    <div class="latest-courses-main small-heigh my-enroll ener">
        <div class="container-fluid">
            <div class="clearfix"></div>
            <div class="row">
                <div class="col-sm-12 col-md-12 col-lg-12">
                    <div class="row">
                        @include('front.user.left_bar')
                        <div class="middle-bar">
                            <div class="edit-posted-bg-main my-profile">

                                <div class="clearfix"></div>
                                <div class="edit-btn-block my-pro">
                                    <a href="{{ url('/').'/'.$user_path.'/my_profile_edit'}}"> <i class="fa fa-pencil"></i> <span>Edit Profile</span> </a>
                                </div>
                               @if(($arr_data['profile_image']!='') && file_exists($user_profile_base_img_path.$arr_data['profile_image']))
                               <?php $profileImageUrl = $user_profile_public_img_path.$arr_data['profile_image']; 
                               ?>
                               @else
                                    <?php $profileImageUrl = url('/uploads/default-profile.png'); ?> 
                                @endif


                                <div class="edit-img-block-main">
                                    <div class="edit-img-block">
                                        <img src="{{ $profileImageUrl }}" class="edit-pro-img" alt="" />
                                        {{-- <div class="circle-edit-blo">
                                            <a href="#"> <i class="fa fa-pencil"></i> </a>
                                        </div> --}}
                                    </div>

                                    <div class="first-names marg-top">
                                        <span>First Name:</span> <div class="first-names-light"> {{ $arr_data['first_name'] or ''}} </div>
                                        <div class="clearfix"></div>
                                    </div>
                                    <div class="first-names">
                                        <span>Last Name:</span> <div class="first-names-light"> {{ $arr_data['last_name'] or ''}}  </div>
                                        <div class="clearfix"></div>
                                    </div>
                                    <div class="first-names">
                                        <span>Email:</span> <div class="first-names-light"> {{ $arr_data['email'] or ''}}  </div>
                                        <div class="clearfix"></div>
                                    </div>
                                    <div class="first-names">
                                        <span>Mobile Number:</span> <div class="first-names-light"> {{ $arr_data['mobile_no'] or ''}}  </div>
                                        <div class="clearfix"></div>
                                    </div>
                                     <div class="first-names">
                                         <span>Gender:</span> <div class="first-names-light">
                                        @if(isset($arr_data))
                                            @if($arr_data['gender']=='M') Male @else Female @endif
                                        @endif
                                         </div>
                                        <div class="clearfix"></div>
                                    </div>
                                    <div class="first-names no-border">
                                        <span>Full Address:</span> <div class="first-names-light"> {{ $arr_data['address'] or ''}}  </div>
                                        <div class="clearfix">
                                    </div>
                                </div>

                            </div>



                        </div>


                    </div>
                    @include('front.user.right_bar')
                </div>
            </div>



        </div>
    </div>
    </div>
@stop