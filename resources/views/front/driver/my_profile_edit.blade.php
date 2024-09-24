<?php $user_path     = config('app.project.role_slug.driver_role_slug'); ?>
 @extends('front.layout.master')                

    @section('main_content')

    <div class="blank-div"></div>

    <div class="email-block">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6 col-md-6 col-lg-6">
                    <div class="headding-text-bredcr">
                        My Profile Edit
                    </div>
                </div>
                <div class="col-sm-6 col-md-6 col-lg-6">
                    <div class="bredcrum-right">
                        <a href="{{ $module_url_path }}" class="bredcrum-home"> Dashboard </a>
                        <span class="arrow-righ"><i class="fa fa-angle-right"></i></span> My Profile Edit
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="clearfix"></div>

    <div class="latest-courses-main small-heigh my-enroll ener">
        <div class="container-fluid">
            <div class="clearfix"></div>
            <div class="row">
                <div class="col-sm-12 col-md-12 col-lg-12">
                    <div class="row">
                        @include('front.driver.left_bar')
                        <div class="col-sm-9 col-md-10 col-lg-10">
                        @include('front.layout._operation_status')
                            <div class="edit-posted-bg-main">
                                <div class="edit-btn-block edit-sec">
                                    <a href="{{ url('/').'/'.$user_path.'/my_profile_view'}}">
                                        <span class="view-pro-spa-eye-main"><i class="fa fa-user-o"></i></span> <span class="view-pro-spa-eye"><i class="fa fa-eye"></i></span> 
                                        <span class="view-pro-spa">View Profile</span>
                                    </a>
                                </div>
                               @if( isset($arr_data['profile_image']) && ($arr_data['profile_image']!='') && file_exists($user_profile_base_img_path.$arr_data['profile_image']))
                               <?php $profileImageUrl = $user_profile_public_img_path.$arr_data['profile_image']; 
                               ?>
                               @else
                                    <?php $profileImageUrl = url('/uploads/default-profile.png'); ?> 
                                @endif

                                <form name="frm-edit-profile" method="post" action="{{url( config('app.project.role_slug.driver_role_slug'))}}/update_profile" data-parsley-validate enctype="multipart/form-data">
                                    {{ csrf_field() }}
                                    <div class="profile-img-block my-account-img">
                                        <div class="pro-img">
                                            <img src="{{ $profileImageUrl}}" class="img-preview" alt="" />
                                        </div>

                                        <div class="update-pic">
                                            <div class="hvr-rectangle-out view upload-btn-new"> <i class="fa fa-camera"></i> Browse Picture <input type="file"  id="logo-id" name="profile_picture" class="attachment_upload" /></div>
                                        </div>
                                    </div>
                                    <div class="clearfix"></div>
                                    <div class="row">
                                        <div class="col-sm-6 col-md-6 col-lg-6">
                                            <div class="form-group marg-top">
                                                <input type="text" name="first_name" data-parsley-required-message="Please enter First name" data-parsley-required="true" data-parsley-errors-container="#err_first_name" value="{{ $arr_data['first_name'] or ''}}">
                                                <label>First Name</label>
                                                <div class="error" id="err_first_name"></div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-md-6 col-lg-6">
                                            <div class="form-group">
                                                <input type="text" name="last_name" data-parsley-required-message="Please enter Last name" data-parsley-required="true" data-parsley-errors-container="#err_last_name" value="{{ $arr_data['last_name'] or ''}}">
                                                <label>Last Name</label>
                                                <div class="error" id="err_last_name"></div>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-md-12 col-lg-12">
                                            <div class="form-group">
                                                <input type="email" name="email" data-parsley-required-message="Please enter email" data-parsley-required="true" data-parsley-errors-container="#err_email" value="{{ $arr_data['email'] or ''}}">
                                                <label>Email</label>
                                                <div class="error" id="err_email"></div>
                                            </div>
                                        </div><div class="col-sm-12 col-md-12 col-lg-12">
                                            <div class="form-group">
                                                <button type="button" class="mobile-edit-btn" data-attr-mobile-no="{{ isset($arr_data['mobile_no']) ? $arr_data['mobile_no'] : '' }}" onclick="openMobileNumberModal(this)"><i class="fa fa-pencil"></i></button>
                                                <input type="text" readonly="" value="{{ $arr_data['mobile_no'] or ''}}">
                                                <label>Mobile Number</label>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-md-12 col-lg-12">
                                            <div class="form-group">
                                                <input type="text" id="address" name="address" data-parsley-required-message="Please enter Address" data-parsley-required="true" data-parsley-errors-container="#err_address" value="{{ $arr_data['address'] or ''}}">
                                                <label>Full Address</label>
                                                <div class="error" id="err_address"></div>
                                            </div>
                                        </div>
                                        {{-- <div class="col-sm-6 col-md-6 col-lg-6">
                                            <div class="form-group sign-upload profile">
                                                <div class="upload-block">
                                                    <input type="file" id="pdffile" name="driving_license" style="visibility:hidden; height: 0;">
                                                    <div class="input-group ">
                                                        <input type="text" class="form-control file-caption  kv-fileinput-caption" placeholder="Upload Your Driving License" readonly id="subfile" />
                                                        <div class="btn btn-primary btn-file"><a class="file" onclick="myFunction()"> <i class="fa fa-paperclip"></i></a></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div> --}}
                                        
                                        <div class="col-sm-12 col-md-12 col-lg-12">
                                            <div class="form-group date">
                                            <div class="form-date-icon"><a href="javascript:void(0)"><i class="fa fa-calendar"></i></a> </div>
                                            <input id="datepicker" type="text" name="dob" placeholder="Date of birth" value="<?php if($arr_data['dob']==null) { echo ''; } else { echo getDateFormat($arr_data['dob']); }?>">
                                            </div>
                                        </div>
                                        
                                        {{-- @if(isset($arr_data['driving_license']) && $arr_data['driving_license']!='')
                                            <div class="col-sm-12 col-md-12 col-lg-12">
                                                <a class="btn btn-default vehicle" href="{{ $arr_data['driving_license'] or '' }}" title="Download uploaded file" download><i class="fa fa-download"></i> &nbsp;Download</a>
                                            </div>
                                        @endif --}}
                                        <!--<div class="clearfix"></div>
                                        <div class="check-box check-block float-no pro-edit">
                                            <p>
                                                <input id="filled-in-box" class="filled-in" checked="checked" type="checkbox">
                                                <label for="filled-in-box">I agree Terms and Coditions</label>
                                            </p>

                                        </div>-->

                                        <div class="cancle-btn-block">
                                            <div class="write-review-btn green margin-botto no-mar">
                                                <button type="submit">Submit</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>



                        </div>


                    </div>
                </div>
            </div>



        </div>
    </div>

<!-- popup section start -->
<div class="mobile-popup-wrapper">
<div id="mobile-popup" class="modal fade" role="dialog" data-backdrop="static">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal"><!--&times;--></button>
        <h4 class="modal-title">Edit Mobile Number</h4>
      </div>
      <div class="modal-body">
            <div id="div_error_show"></div>
            <div class="form-group marg-top">
                <input type="text" id="mobile_no" placeholder="Enter mobile number" value="mobile_no">
                {{-- <label>Enter mobile number</label> --}}
                <div id="err_mobile_no" class="error-red"></div>
            </div>
            
            <div class="login-btn popup"><a onclick="updateMobileNumber()" href="javascript:void(0)">Change</a></div>
      </div>
    </div>

  </div>
</div>   
</div>
 <!-- popup section end -->       

      
<!-- OTP popup section start -->
<div class="mobile-popup-wrapper">
<div id="otp-popup" class="modal fade" role="dialog" data-backdrop="static">
  <div class="modal-dialog">

 
    <div class="modal-content">
      <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal"></button>
        <h4 class="modal-title">Verify your OTP</h4>
      </div>
      <div class="modal-body">
            <div id="div_otp_error_show"></div>
            <div class="form-group marg-top">
                <input type="text" id="otp" name="otp">
                <label>Enter your OTP</label>
                <div id="err_otp" class="error-red"></div>
            </div>
            
            <div class="login-btn popup"><a onclick="updateMobileNumberWithOTP()" href="javascript:void(0)">Verify</a></div>
      </div>
    </div>

  </div>
</div>   
</div>
 <!-- OTP popup section end -->        

<script type="text/javascript" language="javascript" src="{{url('/')}}/js/front/cleave.min.js"></script>
<script type="text/javascript" language="javascript" src="{{url('/')}}/js/front/cleave-phone.i18n.js"></script>

<script src="{{url('js/front/jquery-ui.js')}}" type="text/javascript"></script>

<script type="text/javascript">
    var country_code = '{{ isset($country_code) && $country_code!='' ? $country_code : 'US' }}';
    
    var cleavePhone = new Cleave('#mobile_no', {
        phone: true,
        phoneRegionCode: 'US'
    });

</script>

       
        <script>
        //<!--date and time picker js script-->  
        $(function () {
            $("#datepicker, #datepicker1").datepicker({
                changeMonth: true,
                changeYear: true,
                yearRange: '1950:n',
                autoclose: true,
                maxDate: "-21Y",
            });

        });
    </script>

 <script type="text/javascript">

        var curr_module_url = '{{url('/driver')}}';

        function openMobileNumberModal(ref){
            var mobile_no = $(ref).attr('data-attr-mobile-no');
            if(mobile_no == undefined)
            {
                mobile_no = '';
            }
            $('#mobile_no').val(mobile_no);
            $('#mobile-popup').modal('toggle');
        }
        function updateMobileNumber()
        {
            $('#err_mobile_no').html('');
            $('#div_error_show').html('');

            if($('#mobile_no').val() == '')
            {
                $('#err_mobile_no').html('Please enter Mobile number');
                return false; 
            }  
            var obj_data = new Object();

            obj_data._token        = "{{ csrf_token() }}";
            obj_data.mobile_no     = $('#mobile_no').val();

            $.ajax({
                url:curr_module_url+'/verify_mobile_number',
                type:'POST',
                data:obj_data,
                dataType:'json',
                beforeSend:function()
                {
                    
                },
                success:function(response)
                {
                    if(response.status=="success")
                    {
                        var html = '';
                        html+= '<div class="alert alert-success">';
                        html+= ''+response.msg;
                        html+= '</div>';

                        $('#div_otp_error_show').html(html);
                        $('#mobile-popup').modal('toggle');
                        $('#otp-popup').modal('toggle');
                        return false;
                    }
                    else if(response.status=="error")
                    {
                        var html = '';
                        html+= '<div class="alert alert-danger">';
                        html+= ''+response.msg;
                        html+= '</div>';

                        $('#div_error_show').html(html);
                        return false;
                    }
                    else
                    {
                        var html = '';
                        html+= '<div class="alert alert-danger">';
                        html+= 'Soemthing went wrong,unable to update mobile number,Please try again.';
                        html+= '</div>';

                        $('#div_error_show').html(html);
                        return false;
                    }

                    return false;
                },error:function(res){

                }    
            });

            return false; 
        }

        function updateMobileNumberWithOTP()
        {
            $('#err_otp').html('');
            $('#div_otp_error_show').html('');

            /*if($('#mobile_no').val() == '')
            {
                $('#err_mobile_no').html('Please enter Mobile number');
                return false; 
            }  */

            if($('#otp').val() == '')
            {
                $('#err_otp').html('Please enter otp');
                return false; 
            }  

            var obj_data = new Object();

            obj_data._token    = "{{ csrf_token() }}";
            obj_data.mobile_no = $('#mobile_no').val();
            obj_data.otp       = $('#otp').val();

            $.ajax({
                url:curr_module_url+'/update_mobile_no',
                type:'POST',
                data:obj_data,
                dataType:'json',
                beforeSend:function()
                {
                    
                },
                success:function(response)
                {
                    if(response.status=="success")
                    {
                        var html = '';
                        html+= '<div class="alert alert-success">';
                        html+= ''+response.msg;
                        html+= '</div>';

                        $('#div_otp_error_show').html(html);
                        
                        setTimeout(function(){ window.location.reload(); }, 3000);

                        return false;
                    }
                    else if(response.status=="error")
                    {
                        var html = '';
                        html+= '<div class="alert alert-danger">';
                        html+= ''+response.msg;
                        html+= '</div>';

                        $('#div_otp_error_show').html(html);
                        return false;
                    }
                    else
                    {
                        var html = '';
                        html+= '<div class="alert alert-danger">';
                        html+= 'Soemthing went wrong,unable to update mobile number,Please try again.';
                        html+= '</div>';

                        $('#div_otp_error_show').html(html);
                        return false;
                    }

                    return false;
                },error:function(res){

                }    
            });

            return false; 
        }

        function myFunction() {
            $('#pdffile').click();
        };
        $(document).ready(function() {
            // This is the simple bit of jquery to duplicate the hidden field to subfile
            $('#pdffile').change(function() {
                $('#subfile').val($(this).val());
            });

            // This bit of jquery will show the actual file input box
            $('#showHidden').click(function() {
                $('#pdffile').css('visibilty', 'visible');
            });

            // This is the simple bit of jquery to duplicate the hidden field to subfile
            $('#pdffile1').change(function() {
                $('#subfile1').val($(this).val());
            });

            // This bit of jquery will show the actual file input box
            $('#showHidden1').click(function() {
                $('#pdffile1').css('visibilty', 'visible');
            });
        });
    </script>



    <!--footer section end here-->
    <script type="text/javascript">
        $(document).ready(function() {
            var brand = document.getElementById('logo-id');
            brand.className = 'attachment_upload';
            brand.onchange = function() {
                document.getElementById('fakeUploadLogo').value = this.value.substring(12);
            };

            // Source: http://stackoverflow.com/a/4459419/6396981
            function readURL(input) {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();

                    reader.onload = function(e) {
                        $('.img-preview').attr('src', e.target.result);

                    };

                    reader.readAsDataURL(input.files[0]);
                }
            }
            $("#logo-id").change(function() {
                readURL(this);
            });



        });
    </script>

    <script src="https://maps.googleapis.com/maps/api/js?v=3&key={{ isset($google_map_api_key) ? $google_map_api_key : 'AIzaSyCccvQtzVx4aAt05YnfzJDSWEzPiVnNVsY' }}&libraries=places"></script>
<script src="{{ url('/') }}/js/front/jquery.geocomplete.js"></script>

<script type="text/javascript">

            $(function () 
             {  
                $("#address").geocomplete({
                    details: ".geo-details",
                    detailsAttribute: "data-geo"
                }).bind("geocode:result", function (event, result){ /* Retrun Lat Long*/                      
                    var searchAddressComponents = result.address_components,
                    searchPostalCode="";
                });
            });

</script>
@stop