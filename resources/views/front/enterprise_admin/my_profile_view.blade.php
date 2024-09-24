<?php $user_path     = config('app.project.role_slug.enterprise_admin_role_slug'); ?>

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
                        @include('front.enterprise_admin.left_bar')
                        <div class="col-sm-9 col-md-10 col-lg-10">
                            <div class="edit-posted-bg-main my-profile">

                                <div class="clearfix"></div>
                                <div class="edit-btn-block my-pro">
                                    <a href="{{ url('/').'/'.$user_path.'/my_profile_edit'}}"> <i class="fa fa-pencil"></i> <span>Edit Profile</span> </a>
                                </div>
                                <div class="edit-img-block-main">
                                    <div class="edit-img-block">
                                    <?php 
                                        
                                        $profile_image_url = url('/uploads/default-profile.png');
                                        if(isset($arr_data['profile_image']) && $arr_data['profile_image']!='')
                                        {
                                            $profile_image_url = $arr_data['profile_image']; 
                                        }
                                    ?>

                                     <div class="edit-img-block">
                                        <img src="{{ isset($profile_image_url) ? $profile_image_url : url('/uploads/default-profile.png') }}" class="edit-pro-img" alt="" />
                                    </div>
                                    </div>

                                    <div class="first-names marg-top">
                                        <span>Enterprise Name : </span> <div class="first-names-light"> {{isset($arr_data['company_name']) ? $arr_data['company_name'] : '-'}} </div>
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
                                        <span>Address : </span> <div class="first-names-light"> {{isset($arr_data['address']) ? $arr_data['address'] : '-'}} </div>
                                        <div class="clearfix"></div>
                                    </div>
                                    
                                    <div class="first-names">
                                        <span>Postal Code : </span> <div class="first-names-light"> {{isset($arr_data['post_code']) ? $arr_data['post_code'] : '-'}} </div>
                                        <div class="clearfix"></div>
                                    </div>

                                    <div class="first-names">
                                        <span>Enterprise License : </span> 
                                        <div class="first-names-light">
                                            @if(isset($arr_data['enterprise_license']) && ($arr_data['enterprise_license']!=''))
                                                <?php 
                                                        $enterprise_license = $arr_data['enterprise_license'];

                                                        $document_download_url = url('/common/download_document');
                                                        $user_id = isset($arr_data['user_id']) ? base64_encode($arr_data['user_id']) : base64_encode(0);
                                                        $document_type = 'driving_license';
                                                        $document_download_url = $document_download_url.'?enterprise_id='.$user_id.'&document_type='.$document_type;
                                                        
                                                ?>
                                                 <a class="btn btn-default vehicle" href="{{ $document_download_url }}" title="Download Enterprise License" target="_blank"><i class="fa fa-download"></i></a>
                                                 <a class="btn btn-default vehicle" href="{{ $enterprise_license }}" title="View Enterprise License" target="_blank"><i class="fa fa-eye"></i></a>
                                            @else
                                                <a class="btn btn-default vehicle" href="javascript:void(0);" title="Download Enterprise License"><i class="fa fa-download"></i></a>
                                                <a class="btn btn-default vehicle" href="javascript:void(0);" title="View Enterprise License"><i class="fa fa-eye"></i></a>   
                                            @endif
                                            
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>


                            </div>



                        </div>


                    </div>
                </div>
            </div>



        </div>
    </div>
    </div>
@stop