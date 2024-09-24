 @extends('front.layout.master')                

    @section('main_content')

    <div class="blank-div"></div>
    <div class="email-block">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6 col-md-6 col-lg-6">
                    <div class="headding-text-bredcr">
                       Change Password
                    </div>
                </div>
                <div class="col-sm-6 col-md-6 col-lg-6">
                    <div class="bredcrum-right">
                        <a href="{{ $module_url_path }}" class="bredcrum-home"> Dashboard </a>
                        <span class="arrow-righ"><i class="fa fa-angle-right"></i></span>
                        Change Password
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
                @include('front.layout._operation_status')
                    <div class="edit-posted-bg-main">
                        <div class="row">
                            <div class="col-sm-12 col-md-12 col-lg-9">
                                <form name="frm-change-password" method="post" action="{{url( config('app.project.role_slug.driver_role_slug'))}}/update_password" data-parsley-validate>
                                 {{ csrf_field() }}
                                    <div class="form-group">
                                        <div class="eye-icon-fro right-space">
                                            <span class="eye-icon-clo" onclick="showPassword(this,0)"><i class="fa fa-eye"></i></span>
                                        </div>
                                        <input type="password" placeholder="Old Password" name="old_password" id="old_password" data-parsley-required-message="Please enter old password" data-parsley-required="true" data-parsley-errors-container="#err_old_password">
                                        <div class="error" id="err_old_password"></div>
                                    </div>

                                    <div class="form-group">
                                        <div class="eye-icon-fro right-space">
                                            <span class="eye-icon-clo" onclick="showPassword(this,1)"><i class="fa fa-eye"></i></span>
                                        </div>
                                        <input type="password" placeholder="New Password" name="new_password" id="new_password" data-parsley-required-message="Please enter new password" data-parsley-required="true" data-parsley-errors-container="#err_new_password" data-parsley-minlength-message="Please enter 8 characters" data-parsley-minlength="8" data-parsley-pattern="((?=.*\d)(?=.*[a-z A-Z])(?=.*[!@#$%]).{8,})">
                                        <div class="error" id="err_new_password"></div>
                                    </div>

                                    <div id="password_message_div" class="form-inner-info-colo-wrapp">
                                        <div class="form-inner-colo-head">Your password must have:</div>
                                        <div id="minimum_characters" class="form-inner-colo-sub"><i class="fa fa-circle"></i> Minimum 8 characters</div>
                                        <div id="uppercase_characters" class="form-inner-colo-sub"><i class="fa fa-circle"></i> Uppercase characters (A-Z)</div>
                                        <div id="lowercase_characters" class="form-inner-colo-sub"><i class="fa fa-circle"></i> Lowercase characters (a-z)</div>
                                        <div id="number_characters" class="form-inner-colo-sub "><i class="fa fa-circle"></i> Numbers (0-9)</div>
                                        <div id="special_characters" class="form-inner-colo-sub"><i class="fa fa-circle"></i> Special characters (~*!@$#%_+.?:,)</div>
                                    </div>

                                    <div class="form-group">
                                        <div class="eye-icon-fro right-space">
                                            <span class="eye-icon-clo" onclick="showPassword(this,2)"><i class="fa fa-eye"></i></span>
                                        </div>
                                        <input type="password" placeholder="Confirm New Password" name="confirm_password" id="confirm_password" data-parsley-required-message="Please enter confirm password" data-parsley-required="true" data-parsley-errors-container="#err_confirm_password"  data-parsley-equalto="#new_password" data-parsley-minlength="8">
                                        <div class="error" id="err_confirm_password"></div>
                                    </div>

                                    <div class="btns-wrapper change-pass">
                                        <a href="{{ $module_url_path }}"><button type="button" class="white-btn chan-left">Cancel</button></a>
                                        <button type="submit" class="green-btn chan-right">Submit</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <script type="text/javascript">
            function showPassword(ref,type) {
                if(type == 0){
                    if($('#old_password').attr('type') == 'password'){
                        $('#old_password').attr('type','text');
                        $(ref).addClass('span-enable');
                    }
                    else if($('#old_password').attr('type') == 'text'){
                        $('#old_password').attr('type','password');
                        $(ref).removeClass('span-enable');
                    }
                }
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

            if(new_password == '')
            {
                $('#uppercase_characters').removeClass('red');
                $('#uppercase_characters').removeClass('green');

                $('#lowercase_characters').removeClass('red');
                $('#lowercase_characters').removeClass('green');

                $('#minimum_characters').removeClass('red');
                $('#minimum_characters').removeClass('green');

                $('#number_characters').removeClass('red');
                $('#number_characters').removeClass('green');

                $('#special_characters').removeClass('red');
                $('#special_characters').removeClass('green');
            }

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