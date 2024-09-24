 @extends('front.layout.master')                

    @section('main_content')

    <div class="blank-div"></div>
 <div class="login-wrapper login-wrapper-main">
<!--        <div class="signup-back"><img src="images/login-banner.jpg" class="img-responsive" alt="" /></div>-->
        <div class="container">
            <div class="row">
                <div class="col-sm-6 col-md-6 col-lg-6 respo-padd-none">
                    <div class="login-banner-block">
                        {{-- <h1>Login To Your Account</h1>
                        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed doeiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enimad minim veniam, quis nostrud exercitation ullamco laboris nisi utaliquip ex ea commodo consequat.</p>
                        <h5>Don't have an Account - <a href="user-signup.html">Register Now <i class="fa fa-long-arrow-right"></i></a></h5> --}}
                    </div>
                </div>

                <div class="col-sm-6 col-md-6 col-lg-6 login-bg">
                    <div class="login-section">                        
                        <h1>Verify OTP</h1>

                        <div class="login-tabs">                            
                            @include('front.layout._operation_status')  

                            <div class="login-form-block">                                
                                <form name="frm-login" method="post" action="{{url('/process_verify_otp')}}" data-parsley-validate>
                                
                                    {{ csrf_field() }}

                                    <div class="form-group login">
                                        <input type="password" class="input-box" placeholder="Enter OTP" id="otp" name="otp" data-parsley-required-message="Please enter OTP" data-parsley-required="true" data-parsley-errors-container="#err_otp"/>
                                        <span class="bar"></span>
                                        <div id="err_otp" class="error"></div>
                                    </div>
                                    <input type="hidden" name="mobile_no" value="{{ isset($mobile_no) ? $mobile_no : ''}}">
                                    <input type="hidden" name="user_type" value="{{ isset($user_type) ? $user_type : ''}}">
                                    <input type="hidden" name="redirect_to" value="{{ isset($redirect_to) ? $redirect_to : ''}}">
                                    <div class="clearfix"></div>
                                    <div class="login-btn">
                                        <button type="submit">Verify</button>
                                    </div>
                                   {{--  <div class="or-block">
                                        <span>&nbsp;</span>
                                        <p>Or</p>
                                    </div>
                                    <div class="social-btns">
                                        <a href="javascript:void(0);" class="fb"><i class="fa fa-facebook"></i> Sign Up Via Facebook</a>
                                        <a href="javascript:void(0);" class="google-p"><i class="fa fa-google-plus"></i> Sign Up Via Google+</a>
                                    </div> --}}
                                </form>
                                {{-- <div class="clearfix"></div>       --}}
                                <?php
                                        $resend_otp_url = 'javascript:void(0)';
                                        
                                        if(isset($mobile_no) && isset($user_type))
                                        {
                                            $resend_otp_url = url('/resend_otp?mobile_no='.$mobile_no.'&user_type='.$user_type);
                                        }
                                ?>
                                <h5 class="dont-have-account-txt">Don't received ? <a href="{{$resend_otp_url}}">Resend OTP</a></h5>                          
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@stop
