@extends('front.layout.master')                

@section('main_content')
<div class="blank-div"></div>
    <!--register section start-->
        <div class="login-wrapper signup-wrapper">
        <!--        <div class="signup-back sign-back2"><img src="images/login-banner.jpg" class="img-responsive" alt="" /></div>-->
        <div class="container">
            <div class="row">
                <div class="col-sm-6 col-md-6 col-lg-6 respo-padd-none">
                    <div class="login-banner-block">
                        <!--                        <div class="white-logo"><img src="images/logo.png" class="img-responsive" alt="" /></div>-->
                        <h1>Create an Your Account</h1>
                        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed doeiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enimad minim veniam, quis nostrud exercitation ullamco laboris nisi utaliquip ex ea commodo consequat.</p>
                        <h5>Already have a Account - <a href="{{url('/login')}}">Login Now <i class="fa fa-long-arrow-right"></i></a></h5>
                    </div>
                </div>
                <div class="col-sm-6 col-md-6 col-lg-6 login-bg">
                    <div class="login-section login2 change">
                        <h1>Sign Up</h1>
                        <h5>It's free and always will be.</h5>

                        <div class="login-tabs">
                            <div class="login-form-block">
                             <form name="frm-signup" method="post" action="{{url('/process_signup')}}" data-parsley-validate>    
                             {{ csrf_field() }}                        
                                    <div class="form-group login">
                                        <input type="text" class="input-box" placeholder="First Name"  id="first_name" name="first_name" data-parsley-required-message="Please enter mobile number" data-parsley-required="true" data-parsley-errors-container="#err_first_name"/>
                                        <span class="bar"></span>
                                        <div id="err_first_name" class="error"></div>
                                    </div>
                                    <div class="form-group login">
                                        <input type="text" class="input-box" placeholder="Last Name" />
                                        <span class="bar"></span>
                                        <!--                                        <div class="error">This field is required</div>-->
                                    </div>
                                    <div class="form-group login">
                                        <input type="text" class="input-box" placeholder="Email Address" />
                                        <span class="bar"></span>
                                        <div id="err_mobile_no" class="error"></div>
                                    </div>
                                    <div class="form-group login">
                                        <input type="text" class="input-box" placeholder="Mobile Number" />
                                        <span class="bar"></span>
                                        <div id="err_mobile_no" class="error"></div>
                                    </div>

                                    <div class="form-group sign-up">
                                        <select>
                                            <option>Male</option>
                                            <option>Female</option>
                                        </select>
                                        <div class="dwon-arrow-icon"><i class="fa fa-angle-down"></i></div>
                                        <span class="bar"></span>
                                        <div id="err_mobile_no" class="error"></div>
                                    </div>

                                    <div class="form-group login">
                                        <input type="text" class="input-box" placeholder="Full Address" />
                                        <span class="bar"></span>
                                        <div id="err_mobile_no" class="error"></div>
                                    </div>

                                    <div class="form-group sign-upload">
                                        <div class="upload-block">
                                            <input type="file" id="pdffile" style="visibility:hidden; height: 0;" name="file">
                                            <div class="input-group ">
                                                <input type="text" class="form-control file-caption  kv-fileinput-caption" placeholder="Upload Your Driving License" readonly id="subfile" />
                                                <div class="btn btn-primary btn-file"><a class="file" onclick="myFunction()"> <i class="fa fa-paperclip"></i></a></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <input type="password" class="input-box" placeholder="Password" />
                                        <span class="bar"></span>
                                        <div id="err_mobile_no" class="error"></div>
                                    </div>
                                    <div class="form-group">
                                        <input type="password" class="input-box" placeholder="Confirm Password" />
                                        <span class="bar"></span>
                                        <div id="err_mobile_no" class="error"></div>
                                    </div>
                                    <div class="check-box">
                                        <p>
                                            <input id="filled-in-box" class="filled-in" checked="checked" type="checkbox">
                                            <label for="filled-in-box">I agree to the terms of use and privacy</label>
                                        </p>
                                    </div>
                                    <div class="clearfix"></div>
                                    {{-- <div class="login-btn"><a href="javascript:void(0);">Sign Up Now</a></div> --}}
                                    <div class="login-btn"><button type="submit">Sign Up Now</button></div>

                                    <!--<div class="login-btn"><button>Sign Up Now</button></div>-->
                                    <div class="or-block">
                                        <span>&nbsp;</span>
                                        <p>Or</p>
                                    </div>
                                    <div class="social-btns">
                                        <a href="#" class="fb"><i class="fa fa-facebook"></i> Sign Up Via Facebook</a>
                                        <a href="#" class="google-p"><i class="fa fa-google-plus"></i> Sign Up Via Google+</a>
                                    </div>
                                </form>
                                <div class="clearfix"></div>
                                <h5 class="dont-have-account-txt">Already have a account<a href="login.html"> Login Now</a></h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
 <script type="text/javascript">
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

    <!--register section end-->
@stop