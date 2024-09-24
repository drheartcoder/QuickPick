<?php $user_path     = config('app.project.role_slug.driver_role_slug'); ?>

 @extends('front.layout.master')                

    @section('main_content')

    <div class="blank-div"></div>

    <div class="email-block">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6 col-md-6 col-lg-6">
                    <div class="headding-text-bredcr">
                       View Profile
                    </div>
                </div>
                <div class="col-sm-6 col-md-6 col-lg-6">
                    <div class="bredcrum-right">
                        <a href="{{ $module_url_path }}" class="bredcrum-home"> Dashboard </a>
                        <span class="arrow-righ"><i class="fa fa-angle-right"></i></span>
                        View Profile
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
                        @include('front.driver.left_bar')
                        <div class="col-sm-9 col-md-10 col-lg-10">
                            <div class="edit-posted-bg-main my-profile">

                                <div class="clearfix"></div>
                                <div class="edit-btn-block my-pro">
                                    <a href="{{ url('/').'/'.$user_path.'/my_profile_edit'}}"> <i class="fa fa-pencil"></i> <span>Edit Profile</span> </a>
                                </div>
                                <div class="edit-img-block-main">
                                    <div class="edit-img-block">
                               @if(($arr_data['profile_image']!='') && file_exists($user_profile_base_img_path.$arr_data['profile_image']))
                               <?php $profileImageUrl = $user_profile_public_img_path.$arr_data['profile_image']; 
                               ?>
                               @else
                                    <?php $profileImageUrl = url('/uploads/default-profile.png'); ?> 
                                @endif

                                     <div class="edit-img-block">
                                        <img src="{{ $profileImageUrl }}" class="edit-pro-img" alt="" />
                                        {{-- <div class="circle-edit-blo">
                                            <a href="#"> <i class="fa fa-pencil"></i> </a>
                                        </div> --}}
                                    </div>
                                    </div>

                                    <div class="first-names marg-top">
                                        <span>First Name : </span> <div class="first-names-light"> {{isset($arr_data['first_name']) ? $arr_data['first_name'] : '-'}} </div>
                                        <div class="clearfix"></div>
                                    </div>
                                    <div class="first-names">
                                        <span>Last Name : </span> <div class="first-names-light"> {{isset($arr_data['last_name']) ? $arr_data['last_name'] : '-'}} </div>
                                        <div class="clearfix"></div>
                                    </div>

                                    <div class="first-names">
                                        <span>Date of Birth : </span> <div class="first-names-light"> {{isset($arr_data['dob']) ? date('d M Y',strtotime($arr_data['dob'])) : '-'}} </div>
                                        <div class="clearfix"></div>
                                    </div>

                                    <div class="first-names">
                                        <span>Email : </span> <div class="first-names-light"> {{isset($arr_data['email']) ? $arr_data['email'] : '-'}} </div>
                                        <div class="clearfix"></div>
                                    </div>
                                    
                                    <div class="first-names">
                                        <span>Mobile Number : </span> <div class="first-names-light"> {{isset($arr_data['country_code']) ? $arr_data['country_code'] : ''}} {{isset($arr_data['mobile_no']) ? $arr_data['mobile_no'] : ''}} </div>
                                        <div class="clearfix"></div>
                                    </div>

                                    <div class="first-names">
                                         <span>Gender : </span> <div class="first-names-light"> 
                                            @if(isset($arr_data['gender']) && $arr_data['gender'] == "M")
                                                Male
                                            @elseif(isset($arr_data['gender']) && $arr_data['gender'] == "F")
                                                Female
                                            @else
                                                -
                                            @endif
                                         </div>
                                        <div class="clearfix"></div>
                                    </div>
                                    
                                    <div class="first-names">
                                        <span>Full Address : </span> <div class="first-names-light"> {{isset($arr_data['address']) ? $arr_data['address'] : '-'}} </div>
                                        <div class="clearfix"></div>
                                    </div>
                                    
                                    {{-- <div class="first-names no-border">
                                        <span>Driving License : </span> 
                                        <div class="first-names-light">
                                        @if(isset($arr_data['driving_license']) && $arr_data['driving_license']!='')
                                            <div class="col-sm-12 col-md-12 col-lg-12">
                                                <a class="btn btn-default vehicle" href="{{ $arr_data['driving_license'] or '' }}" title="Download uploaded file" download><i class="fa fa-download"></i> &nbsp;Download</a>
                                            </div>
                                        @endif

                                        </div>
                                        <div class="clearfix"></div>
                                    </div> --}}


                            </div>



                        </div>


                    </div>
                </div>
            </div>



        </div>
    </div>
    </div>
@stop