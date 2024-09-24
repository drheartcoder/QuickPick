 @extends('front.layout.master')                

    @section('main_content')

    <div class="blank-div"></div>
<div class="login-wrapper-change-new">
 <div class="login-wrapper login-wrapper-main">
<!--        <div class="signup-back"><img src="images/login-banner.jpg" class="img-responsive" alt="" /></div>-->
        <div class="container-fluid">
           <div class="login-bannefluid-inner">
            <div class="row">
                <div class="col-sm-6 col-md-6 col-lg-7 respo-padd-none">
                    <div class="login-banner-block">
<!--                        <div class="white-logo"><img src="images/logo.png" class="img-responsive" alt="" /></div>-->
                        <h1>Don't have an Account?</h1>
                        {{-- <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed doeiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enimad minim veniam, quis nostrud exercitation ullamco laboris nisi utaliquip ex ea commodo consequat.</p> --}}
                        
                        <div class="login-banner-block-change-button">
                            <div class="change-button-login first"><a href="{{url('/register')}}">Sign Up as Customer!</a></div>
                            <div class="change-button-login"><a href="{{url('/join_our_fleet')}}">Sign Up as Driver!</a></div>
                            {{-- <div class="change-button-login"><a href="{{url('/enterprise_admin')}}">Sign Up as enterprise Admin!</a></div> --}}
                        </div>
                       <!-- <h5>Don't have an Account - <a href="{{url('/register')}}">Sign Up as User Now <i class="fa fa-long-arrow-right"></i></a></h5>
                        <h5>Don't have an Account - <a href="{{url('/join_our_fleet')}}">Sign Up as Driver Now <i class="fa fa-long-arrow-right"></i></a></h5>-->
                    </div>
                </div>
                <div class="col-sm-6 col-md-6 col-lg-5 login-bg">
                   <div class="login-section-top-black"> 
                       <div class="login-section-top-black-head">Log In</div>
                       <div class="login-section-top-black-phara">Please log in to your account</div>
                    </div>
                    <div class="login-section">                        
                       <!-- <h1>Log In</h1>
                        <h5>Please login to your account</h5>-->

                        <div class="login-tabs">
                        
                            @include('front.layout._operation_status')  

                            <div class="login-form-block">                                
                                <form name="frm-login" method="post" autocomplete="off" action="{{url('/process_login')}}" data-parsley-validate>

                                    {{ csrf_field() }}

                                    <div class="form-group">
                                        <input type="text" class="input-box" placeholder="Mobile Number" id="mobile_no" name="mobile_no" data-parsley-required-message="Please enter mobile number" data-parsley-required="true" data-parsley-errors-container="#err_mobile_no"/>
                                        <span class="bar"></span>
                                        <div id="err_mobile_no" class="error"></div>
                                    </div>

                                    <div class="form-group">
                                        <div class="eye-icon-fro right-space">
                                            <span class="eye-icon-clo" onclick="showPassword(this)"><i class="fa fa-eye"></i></span>
                                        </div>
                                        <input type="password" class="input-box" placeholder="Password" id="password" name="password" data-parsley-required-message="Please enter Password" data-parsley-required="true" data-parsley-errors-container="#err_password"/>
                                        <span class="bar"></span>
                                        <div class="error" id="err_password"></div>
                                    </div>

                                    <div class="form-group">
                                        <select name="user_type" id="user_type" data-parsley-required-message="Please select role" data-parsley-required="true" data-parsley-errors-container="#err_user_type">
                                            <option value="">Select Role</option>
                                            <option value="USER">Customer</option>
                                            <option value="DRIVER">Driver</option>
                                            <option value="ENTERPRISE_ADMIN">Enterprise Admin</option>
                                        </select>
                                        <span class="bar"></span>
                                        <div class="error" id="err_user_type"></div>
                                    </div>

                                    <div class="check-box login">
                                        <p>
                                            <input id="filled-in-box" class="filled-in" type="checkbox">
                                            <label for="filled-in-box">Remember me</label>
                                        </p>
                                        <a href="{{url('/forget_password')}}" class="forget-pw">Forgot Password?</a>
                                        <div class="clearfix"></div>
                                    </div>
                                    <div class="clearfix"></div>
                                    <div class="login-btn">
                                        {{-- <a href="user-dashboard.html">Log In Now</a> --}}
                                        <button type="submit">Log In Now</button>
                                    </div>
                                    <!--<div class="login-btn"><button>Log In Now</button></div>-->
                                    <div class="or-block">
                                        <!--<span>&nbsp;</span>-->
                                        {{-- <p></p> --}}
                                    </div>
                                    {{-- <div class="social-btns">
                                        <a href="{{url('/redirect_to_facebook')}}" class="fb"><i class="fa fa-facebook"></i> Sign Up As Customer Via Facebook</a>
                                    </div> --}}
                                </form>
                                <div class="clearfix"></div>      
                                <!--<h5 class="dont-have-account-txt">Dont have an Account? <a href="{{url('/register')}}">Sign Up as User!</a></h5>

                                <div class="clearfix"></div>      
                                <h5 class="dont-have-account-txt second">Dont have an Account? <a href="{{url('/join_our_fleet')}}">Sign Up as Driver!</a></h5>-->

                            </div>
                        </div>
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

    function showPassword(ref) {
        if($('#password').attr('type') == 'password'){
            $('#password').attr('type','text');
            $(ref).addClass('span-enable');
        }
        else if($('#password').attr('type') == 'text'){
            $('#password').attr('type','password');
            $(ref).removeClass('span-enable');
        }
    }

    var country_code = '{{ isset($country_code) && $country_code!='' ? $country_code : 'US' }}';
    
    var cleavePhone = new Cleave('#mobile_no', {
        phone: true,
        phoneRegionCode: 'US'
    });

</script>
@stop
