 @extends('front.layout.master')                

    @section('main_content')

    <div class="blank-div"></div>
 <div class="login-wrapper login-wrapper-main">
<!--        <div class="signup-back"><img src="images/login-banner.jpg" class="img-responsive" alt="" /></div>-->
        <div class="container">
            <div class="row">
                 <div class="col-sm-6 col-md-6 col-lg-6 respo-padd-none">
                    <div class="login-banner-block">
                        <h1>Reset Your Password</h1>
                        {{-- <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed doeiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enimad minim veniam, quis nostrud exercitation ullamco laboris nisi utaliquip ex ea commodo consequat.</p> --}}
                        <h5 >Already have a account - <a href="{{url('/login')}}"> Login Now <i class="fa fa-long-arrow-right"></i></a></h5>
                    </div>
                </div>
                <div class="col-sm-6 col-md-6 col-lg-6 login-bg">
                    <div class="login-section">                        
                        <h1>Reset Password</h1>
                        
                        <h5>Reset your account password</h5>

                        <div class="login-tabs">                            
                            @include('front.layout._operation_status')  
                            <div class="login-form-block">                                
                                <form name="frm-login" method="post" action="{{url('/process_reset_password')}}" data-parsley-validate>

                                    {{ csrf_field() }}

                                    <div class="form-group login">
                                        <div class="eye-icon-fro">
                                            <span class="eye-icon-clo" onclick="showPassword(this,1)"><i class="fa fa-eye"></i></span>
                                        </div>
                                        {{-- <input type="password" class="input-box" placeholder="Enter New Password" name="password" id="new_password" data-parsley-required="true" data-parsley-errors-container="#err_password" data-parsley-pattern="((?=.*\d)(?=.*[a-z A-Z])(?=.*[!@#$%]).{6,})" data-parsley-pattern-message="Password must be 6 characters in length and contain atleast one special character one alphabet and digit."  data-parsley-minlength="6" /> --}}
                                        <input type="password" class="input-box" placeholder="Enter New Password" name="password" id="new_password" data-parsley-required-message="Please enter new password" data-parsley-required="false" data-parsley-errors-container="#err_password" data-parsley-minlength="8" data-parsley-pattern="((?=.*\d)(?=.*[a-z A-Z])(?=.*[!@#$%]).{8,})"/>
                                        <span class="bar"></span>
                                        <div class="error" id="err_password"></div>
                                    </div>

                                    <div id="password_message_div" class="form-inner-info-colo-wrapp">
                                        <div class="form-inner-colo-head">Your password must have:</div>
                                        <div id="minimum_characters" class="form-inner-colo-sub"><i class="fa fa-circle"></i> Minimum 8 characters</div>
                                        <div id="uppercase_characters" class="form-inner-colo-sub"><i class="fa fa-circle"></i> Uppercase characters (A-Z)</div>
                                        <div id="lowercase_characters" class="form-inner-colo-sub"><i class="fa fa-circle"></i> Lowercase characters (a-z)</div>
                                        <div id="number_characters" class="form-inner-colo-sub "><i class="fa fa-circle"></i> Numbers (0-9)</div>
                                        <div id="special_characters" class="form-inner-colo-sub"><i class="fa fa-circle"></i> Special characters (~*!@$#%_+.?:,)</div>
                                    </div>

                                    <div class="form-group login">
                                        <div class="eye-icon-fro">
                                            <span class="eye-icon-clo" onclick="showPassword(this,2)"><i class="fa fa-eye"></i></span>
                                        </div>
                                        <input type="password" class="input-box" placeholder="Enter Confirm Password" name="confirm_password" id="confirm_password" data-parsley-required="true" data-parsley-errors-container="#err_confirm_password" data-parsley-pattern="((?=.*\d)(?=.*[a-z A-Z])(?=.*[!@#$%]).{6,})" data-parsley-pattern-message="Password must be 6 characters in length and contain atleast one special character one alphabet and digit."  data-parsley-minlength="6" data-parsley-equalto="#new_password" />
                                        <span class="bar"></span>
                                        <div class="error" id="err_confirm_password"></div>
                                    </div>
                                    
                                    <input type="hidden" name="user_id" value="{{isset($user_id) ? base64_encode($user_id) : ''}}">
                                    <div class="clearfix"></div>
                                    <div class="login-btn">
                                        <button type="submit">Reset Password</button>
                                    </div>
                                </form>
                                {{-- <div class="clearfix"></div>       --}}
                                {{-- <h5 class="dont-have-account-txt"><a href="{{url('/login')}}">Back to Login!</a></h5>                           --}}
                                <h5 class="dont-have-account-txt">Already have a account<a href="{{url('/login')}}"> Login Now</a></h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<script type="text/javascript">
    
    function showPassword(ref,type) {
        if(type == 1){
            if($('#new_password').attr('type') == 'password'){
                $('#new_password').attr('type','text');
                $(ref).addClass('span-enable');
            }
            else if($('#new_password').attr('type') == 'text'){
                $('#new_password').attr('type','password');
                $(ref).removeClass('span-enable');
            }
        }
        if(type == 2){
            if($('#confirm_password').attr('type') == 'password'){
                $('#confirm_password').attr('type','text');
                $(ref).addClass('span-enable');
            }
            else if($('#confirm_password').attr('type') == 'text'){
                $('#confirm_password').attr('type','password');
                $(ref).removeClass('span-enable');
            }
        }
    }

    $('#new_password').keyup(function(){

    var new_password = $('#new_password').val();

    if(new_password!='')
    {
        var uppercase_characters = /[A-Z]/g;
        if(new_password.match(uppercase_characters)) {  
            $('#uppercase_characters').removeClass('red');
            $('#uppercase_characters').addClass('green');
        } else {
            $('#uppercase_characters').removeClass('green');
            $('#uppercase_characters').addClass('red');
        }
        
        var lowercase_characters = /[a-z]/g;
        if(new_password.match(lowercase_characters)) {  
            $('#lowercase_characters').removeClass('red');
            $('#lowercase_characters').addClass('green');
        } else {
            $('#lowercase_characters').removeClass('green');
            $('#lowercase_characters').addClass('red');
        }

        // var minimum_characters = /[a-z]/g;
        if(new_password.length >= 8) {  
            $('#minimum_characters').removeClass('red');
            $('#minimum_characters').addClass('green');
        } else {
            $('#minimum_characters').removeClass('green');
            $('#minimum_characters').addClass('red');
        }

        var number_characters = /[0-9]/g;
        if(new_password.match(number_characters)) {  
            $('#number_characters').removeClass('red');
            $('#number_characters').addClass('green');
        } else {
            $('#number_characters').removeClass('green');
            $('#number_characters').addClass('red');
        }

        var special_characters = /[!@#$%^&*()]/g;

        if(new_password.match(special_characters)) {  
            $('#special_characters').removeClass('red');
            $('#special_characters').addClass('green');
        } else {
            $('#special_characters').removeClass('green');
            $('#special_characters').addClass('red');
        }
    } 
});

</script>
@stop
