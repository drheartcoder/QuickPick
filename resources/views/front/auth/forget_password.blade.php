 @extends('front.layout.master')                

    @section('main_content')

    <div class="blank-div"></div>
 <div class="login-wrapper login-wrapper-main">
<!--        <div class="signup-back"><img src="images/login-banner.jpg" class="img-responsive" alt="" /></div>-->
        <div class="container">
            <div class="row">
                <div class="col-sm-6 col-md-6 col-lg-6 respo-padd-none">
                    <div class="login-banner-block">
                        <h1>Forget Your Password</h1>
                        {{-- <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed doeiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enimad minim veniam, quis nostrud exercitation ullamco laboris nisi utaliquip ex ea commodo consequat.</p> --}}
                        <h5 >Already have a account - <a href="{{url('/login')}}"> Login Now <i class="fa fa-long-arrow-right"></i></a></h5>
                    </div>
                </div>

                <div class="col-sm-6 col-md-6 col-lg-6 login-bg">
                    <div class="login-section">                        
                        <h1>Forget Password</h1>
                        {{-- <h5>Please login to your account</h5> --}}

                        <div class="login-tabs">                            

                            @include('front.layout._operation_status')  
                            <div class="login-form-block">                                
                                <form name="frm-login" method="post" action="{{url('/process_forget_password')}}" data-parsley-validate>

                                    {{ csrf_field() }}

                                    <div class="form-group login">
                                        <input type="text" class="input-box" placeholder="Mobile Number" id="mobile_no" name="mobile_no" data-parsley-required-message="Please enter mobile number" data-parsley-required="true" data-parsley-errors-container="#err_mobile_no"/>
                                        <span class="bar"></span>
                                        <div id="err_mobile_no" class="error"></div>
                                    </div>
                                    <div class="form-group sign-up">
                                        <select name="user_type" id="user_type" data-parsley-required-message="Please select role" data-parsley-required="true" data-parsley-errors-container="#err_user_type">
                                            <option value="">Select Role</option>
                                            <option value="USER">Customer</option>
                                            <option value="DRIVER">Driver</option>
                                            <option value="ENTERPRISE_ADMIN">Enterprise Admin</option>
                                        </select>
                                        <div class="dwon-arrow-icon"><i class="fa fa-angle-down"></i></div>
                                        <span class="bar"></span>
                                        <div class="error" id="err_user_type"></div>
                                    </div>

                                    <div class="clearfix"></div>
                                    <div class="login-btn">
                                        <button type="submit">Submit</button>
                                    </div>
                                
                                </form>
                                
                                <h5 class="dont-have-account-txt">Already have a account<a href="{{url('/login')}}"> Login Now</a></h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<script type="text/javascript" language="javascript" src="{{url('/')}}/js/front/cleave.min.js"></script>
<script type="text/javascript" language="javascript" src="{{url('/')}}/js/front/cleave-phone.i18n.js"></script>

<script type="text/javascript">
    var country_code = '{{ isset($country_code) && $country_code!='' ? $country_code : 'US' }}';
    
    var cleavePhone = new Cleave('#mobile_no', {
        phone: true,
        phoneRegionCode: 'US'
    });

</script>
@stop
