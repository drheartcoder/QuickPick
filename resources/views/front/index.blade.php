@extends('front.layout.master') 

@section('main_content')
<div class="maine-banner-main-responsive">
    <div class="maine-banner-show-responsive"></div>
<div class="maine-banner small-hieght-bnr index-wra">
	<div class="container home-top-banner">
		<div class="best-landing-page index-ban">
            
            <!-- <div class="row"> -->
				<div class="banner-left-box">
                      <!--  <div class="banner-text-block less">
                        <div class="fast-quick-txt">On-demand Delivery </div>
                        <div class="fast-quick-txt-sub">You Can Count On! </div>
                 </div> -->
					<div class="banner-text-block main">
						<!--<div class="fast-quick-txt">Transporting the supplies </div>
						<div class="fast-quick-txt-sub">you need in a <span>hurry</span></div>-->
				 		
						<div class="fast-quick-txt">On-demand Delivery </div>
						<div class="fast-quick-txt-sub">You Can Count On! <!--<span>On!</span--></div>
					</div>
					
					<div class="join-fleet-right-block driver-registre index">
                    <div class="join-fleet-head">Driver Qualifications</div>
                    <div class="join-fleet-sub">Sedan, SUV, Cargo Van, Pick Up Truck</div>

                    <div class="join-fleet-tick-block">
                        <div class="join-fleet-tick-img"><img src="{{ url('/')}}/images/join-fleet-tick.png" alt="join-fleet-tick" /> </div>
                        <div class="join-fleet-tick-text">Experience driving vehicles listed above</div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="join-fleet-tick-block">
                        <div class="join-fleet-tick-img"><img src="{{ url('/')}}/images/join-fleet-tick.png" alt="join-fleet-tick" /> </div>
                        <div class="join-fleet-tick-text">Experience driving in the Washington, D.C. Metro area</div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="join-fleet-tick-block">
                        <div class="join-fleet-tick-img"><img src="{{ url('/')}}/images/join-fleet-tick.png" alt="join-fleet-tick" /> </div>
                        <div class="join-fleet-tick-text">Availability to make on-demand deliveries</div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="join-fleet-tick-block">
                        <div class="join-fleet-tick-img"><img src="{{ url('/')}}/images/join-fleet-tick.png" alt="join-fleet-tick" /> </div>
                        <div class="join-fleet-tick-text">Friendly and courteous</div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="join-fleet-tick-block">
                        <div class="join-fleet-tick-img"><img src="{{ url('/')}}/images/join-fleet-tick.png" alt="join-fleet-tick" /> </div>
                        <div class="join-fleet-tick-text">Valid vehicle registration and inspection (if using your vehicle)</div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="join-fleet-tick-block">
                        <div class="join-fleet-tick-img"><img src="{{ url('/')}}/images/join-fleet-tick.png" alt="join-fleet-tick" /> </div>
                        <div class="join-fleet-tick-text">Valid vehicle insurance (if using your vehicle)</div>
                        <div class="clearfix"></div>
                    </div>
                  </div>
                 
                
					<div class="delivered-from-main-blo index">
						<div class="delivered-from-wrapper">
						  {{-- <div class="delivery-title">Sign Up With Quick Pick</div> --}}
                        <div class="delivered-top-img-left"><img src="{{url('/images/index-delivered-top-img.png')}}" alt="index-delivered-top-img" width="135px" height="48px"/> </div>
						<div class="delivered-top-img-right">
						<div class="delivery-title">Sign Up With Quick Pick</div>
						{{-- <div class="delivery-title-sub">Join Our Fleet</div> --}}
						</div>

                    <!--<div class="col-sm-12 col-md-5 col-lg-5">
                        {{-- <div class="delivery-title-righ active"><a href="javascript:void(0)">Step 1</a> </div>
                        <div class="delivery-title-righ"><a href="javascript:void(0)">Step 2</a> </div> --}}
                        <div class="clearfix"></div>
                       </div>-->                       
                         <div class="clearfix"></div>  
						</div>
						<div class="delivered-from">
							
                            <div id="success_div"></div>
							<div id="error_div"></div>
                            
                            <form id="frm_home_page" method="get" action="" data-parsley-validate>
                                <div class="row">
                                    <div class="col-sm-6 col-md-6 col-lg-6 ">
                                        <div class="form-group">
                                            <input type="text" name="first_name" placeholder="First Name" data-parsley-required-message="Please enter first name" data-parsley-required="true" data-parsley-errors-container="#err_first_name">
                                             <div id="err_first_name" class="error"></div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-md-6 col-lg-6 ">
                                        <div class="form-group">
                                            <input type="text" name="last_name" placeholder="Last Name" data-parsley-required-message="Please enter last name" data-parsley-required="true" data-parsley-errors-container="#err_last_name">
                                            <div id="err_last_name" class="error"></div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-md-6 col-lg-6 ">
                                        <div class="form-group">
                                            <input type="email" name="email" placeholder="Email" data-parsley-required-message="Please enter email address" data-parsley-required="true" data-parsley-errors-container="#err_email">
                                            <div id="err_email" class="error"></div>
                                        </div>
                                    </div>
                                    
                                    {{-- <div class="col-sm-6 col-md-6 col-lg-6 ">
                                        <div class="form-group white-bg">
                                        <select name="country_code" id="country_code" data-parsley-required-message="Please select country" data-parsley-required="true" data-parsley-errors-container="#err_country_code">
                                            <option value="">Select Country</option>
                                            @if(isset($arr_countries) && sizeof($arr_countries)>0)
                                                @foreach($arr_countries as $key => $country)
                                                    
                                                    <option selected="" value="{{ isset($country['phone_code']) ? $country['phone_code'] : '' }}">{{ isset($country['country_name']) ? $country['country_name'] : '' }}</option>
                                                        
                                                @endforeach
                                            @endif
                                        </select>
                                        <span class="bar"></span>
                                        <div class="error" id="err_country_code"></div>
                                    </div>
                                        
                                    </div> --}}

                                    <div class="col-sm-6 col-md-6 col-lg-6 ">
                                        <div class="form-group">
                                            <input type="text" name="country_name" readonly="" placeholder="Country Name" id="country_name" data-parsley-required-message="Please enter country" data-parsley-required="true" data-parsley-errors-container="#err_country_name" value="United States">
                                            <div id="err_country_name" class="error"></div>
                                        </div>
                                    </div>
                                    <input type="hidden" name="country_code" value="1">
                                    <div class="col-sm-12 col-md-12 col-lg-12 ">
                                        <div class="form-group">
                                            <input type="text" name="mobile_no" placeholder="Phone Number" id="mobile_no" data-parsley-required-message="Please enter mobile number" data-parsley-required="true" data-parsley-errors-container="#err_mobile_no">
                                            <div id="err_mobile_no" class="error"></div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-sm-12 col-md-12 col-lg-12 ">
                                        <div class="index-form-radio-block">
                                            <div class="sign-up-radio">
                                              <div class="default-cate-radio-section">
                                         <div class="radio-btns">
                                            <div class="radio-btn yes-radio-section">
                                                <input type="radio" id="f-option" value="driver" name="sign_up_type">
                                                <label for="f-option">Apply to Drive</label>
                                                <div class="check"></div>
                                            </div>
                                            <div class="radio-btn no-radio-section">
                                                <input type="radio" checked id="s-option" value="user" name="sign_up_type">
                                                <label for="s-option">Sign Up as Customer</label>
                                                <div class="check">
                                                    <div class="inside"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                                </div>
                                
                                <div class="clearfix"></div>
                                        </div>
                                    </div>
                             
                                    <div class="col-sm-12 col-md-12 col-lg-12 ">
                                        <div class="delivery-frm-btn index right">
                                        <button id="btn_submit" type="button" onclick="submitNeedDelivery();" class="red-btn">Submit Form</button>
                                        </div>
                                         <div class="clearfix"></div>
                                    </div>
                                    </div>
                            </form>
							{{-- <form id="validation-form">
								{{ csrf_field() }}
								<div class="row">
									<div class="col-sm-6 col-md-6 col-lg-6 ">
										<div class="form-group">
											<input type="text" name="first_name" data-rule-required="true" data-rule-maxlength="20" data-parsley-errors-container="#err_first_name">
											<label>First Name</label>
											<div id="err_first_name" class="error"></div>
										</div>
									</div>
									<div class="col-sm-6 col-md-6 col-lg-6 ">
										<div class="form-group">
											<input type="text" name="last_name" data-rule-required="true" data-rule-maxlength="20" data-parsley-errors-container="#err_last_name">
											<label>Last Name</label>
											<div id="err_last_name" class="error"></div>
										</div>
									</div>
									<div class="col-sm-6 col-md-6 col-lg-6 ">
										<div class="form-group">
											<input type="email" name="email" data-rule-required="true" data-parsley-errors-container="#err_email">
											<label>Email</label>
											<div id="err_email" class="error"></div>
										</div>
									</div>
									<div class="col-sm-6 col-md-6 col-lg-6 ">
										<div class="form-group">
											<input type="text" name="phone" id="mobile_no" data-rule-required="true" data-rule-minlength="7" data-rule-maxlength="14" data-parsley-errors-container="#err_phone">
											<label>Phone No.</label>
											<div id="err_phone" class="error"></div>
										</div>
									</div>
									<div class="col-sm-12 col-md-12 col-lg-12 ">
										<div class="delivered-from-head">Create Password</div>
										<div class="form-group">
											<input type="password" name="phone" id="mobile_no" data-rule-required="true" data-rule-minlength="7" data-rule-maxlength="14" data-parsley-errors-container="#err_phone">
											<label>Password</label>
											<div id="err_phone" class="error"></div>
										</div>
										<div class="delivered-from-head">DMV Driving History</div>
										<div class="delivered-from-sub">Please upload your driving record dated within the past 30 days</div>
									</div>
                                    <div class="col-sm-12 col-md-12 col-lg-12 ">
                                        <div class="delivery-frm-btn index">
                                        <button id="btn_submit" type="button" onclick="submitNeedDelivery();" class="red-btn">Submit Form</button>
                                        </div>
                                         <div class="clearfix"></div>
                                        <div class="delivered-from-wrapper-bottom">
                                           <div class="delivered-top-img-left"><img src="{{url('/images/index-delivered-bottom-img.png')}}" alt="index-delivered-top-img" /> </div>
                                            <div class="delivered-top-img-right index">
                                                <div class="delivery-title">Sign Up for Delivery</div>
                                            </div>
                                             <div class="clearfix"></div>
                                        </div>
									</div>
								</div>
							</form> --}}
						</div>
					</div>
                     <div class="clearfix"></div>
				</div>
			<!-- </div> -->
            <div class="clearfix"></div>
		</div>
	</div><!--Transporting the supplies -->
</div>
</div>
<div class="clearfix"></div>
<div class="fill-blue-section">
	<div class="easy-text-box">
		<div class="easy-title">IT’S EASY!</div>
		<div class="easy-sml-txt">Just Follow These 4 Steps:</div>
	</div>
	<div class="it-say-main-wrapper">
		<div class="container-fluid">
			<div class="row">
				<div class="col-sm-6 col-md-3 col-lg-3">
					<div class="blue-box-fill">
						<div class="fill-out-img"><img src="{{ url('/')}}/images/step-1-icon.png" alt="Quick-Pick" width="180px" height="118px"/> </div>
						<div class="dispatch-truck-bx">
							<div class="number-count poss-left">1</div>
							<div class="fill-out-title">Download and sign up for our app using your Android or Apple device</div>
							
                        </div>
                        <div class="clearfix"></div>
                        <div class="dispatch-down-butt">
                               <a target="blank" href="https://itunes.apple.com/us/app/quickpick-on-demand-delivery/id1434510525?ls=1&mt=8" class="dispatch-dow-android">
                                <img src="{{url('images/app-store-icon.jpg')}}" alt="index-app-store-icon" />
                                <!-- <span class="app-store-left"><img src="{{url('images/index-app-store-icon.png')}}" alt="index-app-store-icon" /></span>
                                <span class="app-store-right">
                                    <h3>Available on the</h3>
                                    <h1>App Store</h1>
                                </span> -->
                                <span class="clearfix"></span>
                               </a> 
                               <a target="blank" href="https://play.google.com/store/apps/details?id=com.app.quickpick" class="dispatch-dow-apple">
                                 <img src="{{url('images/google-play-icon.jpg')}}" alt="index-app-store-icon" />
                            <!--     <span class="app-store-left"><img src="{{url('images/index-google-play-icon.png')}}" alt="index-google-play-icon" /></span>
                                <span class="app-store-right">
                                    <h3>Get it on</h3>
                                    <h1>Google Play</h1>
                                </span> -->
                                <span class="clearfix"></span>
                               </a>
                            </div>
				    </div>
				</div>
				<div class="col-sm-6 col-md-3 col-lg-3">
					<div class="blue-box-fill color-2">
						<div class="fill-out-img"><img src="{{ url('/')}}/images/step-2-icon.png" alt="Quick-Pick" width="180px" height="118px"/> </div>
						<div class="dispatch-truck-bx">
							<div class="number-count">2</div>
							<div class="fill-out-title">Enter your package information, pickup location and destination using the app</div>
                        </div>
					</div>
				</div>
				<div class="col-sm-6 col-md-3 col-lg-3">
					<div class="blue-box-fill color-3">
						<div class="fill-out-img"><img src="{{ url('/')}}/images/step-3-icon.png" alt="Quick-Pick" width="180px" height="118px"/> </div>
						<div class="dispatch-truck-bx">
							<div class="number-count">3</div>
							<div class="fill-out-title">Our driver will accept your request and pickup the package</div>
						</div>
					</div>
				</div>
				<div class="col-sm-6 col-md-3 col-lg-3">
					<div class="blue-box-fill color-4">
						<div class="fill-out-img"><img src="{{ url('/')}}/images/step-4-icon.png" alt="Quick-Pick" width="180px" height="118px"/> </div>
						<div class="dispatch-truck-bx">
							<div class="number-count">4</div>
							<div class="fill-out-title">Upon arrival at the destination, you will be notified that your package has arrived </div>
						</div>
					</div>
				</div>
			</div>
			    
		</div>
	</div>
</div>

<div class="schedule-delivery-section">
	<div class="container-fluid">
		<div class="col-sm-12 col-md-offset-5 col-md-7 col-lg-offset-6 col-lg-6 ">
			<div class="schedule-delivery-title">Schedule Your Delivery</div>
			<div class="schedule-delivery-txt">Don’t stall your project waiting for supplies. We deliver materials for all trades across the Washington, D.C. Metro area in just a few hours or less.</div>
            <div class="schedul-btn">
                {{-- <button onclick="location.href = '{{url('/coming_soon')}}';" type="button" class="red-btn">Schedule</button> --}}
				<button onclick="showSchedulePopup(this);" type="button" class="red-btn">Schedule</button>

			</div>
		</div>
	</div>
</div>


  <!-- popup section start -->
<div class="mobile-popup-wrapper">
    <div id="schedule-popup" class="modal fade" role="dialog" data-backdrop="static">
        <div class="modal-dialog">
            <form id="frm_subscriber" data-parsley-validate>
                {{ csrf_field() }}
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"></button>
                        <div class="header-content-section">
                            <img src="{{ url('/')}}/images/logo-footer.png" alt="join-fleet-tick" />
                            <div class="schdule-txt-section">
                                SCHEDULE YOUR DELIVERY
                            </div>
                            <div class="coming-soon-txt">
                                We're Coming Soon...
                            </div>
                        </div>          
                    </div>
                    <div class="modal-body">
                        <div id="div_right_bar_error_show"></div>
                        <div class="subscrib-txt">
                            Subscribe to be Notified!
                        </div>
                        <div id="success_subscriber_div"></div>
                        <div id="error_subscriber_div"></div>
                        <div class="form-group marg-top subscrib-input-section">
                            {{-- <input type="text" id="subscriber_email" name="subscriber_email" placeholder="Enter Your Email Id"/> --}}
                            <input type="email" id="subscriber_email" name="subscriber_email" placeholder="Enter your email address" data-parsley-required-message="Please enter email address" data-parsley-required="true" data-parsley-errors-container="#err_subscriber_email">
                            <div id="err_subscriber_email" class="error"></div>
                            <div class="login-btn popup"><a href="javascript:void(0)" onclick="checkValidSubscriberDetails(this)">Submit</a></div>
                        </div>        
                    </div>
                </div>
            </form>
        </div>
    </div>   
</div>

<!-- popup section end -->
<div id="footer"></div>
<script type="text/javascript" src="{{ url('/') }}/js/front/bootstrap.min.js"></script>
<script type="text/javascript" language="javascript" src="{{url('/')}}/js/front/cleave.min.js"></script>
<script type="text/javascript" language="javascript" src="{{url('/')}}/js/front/cleave-phone.i18n.js"></script>
<script type="text/javascript">
	var country_code = '{{ isset($country_code) && $country_code!='' ? $country_code : 'US' }}';

	var cleavePhone = new Cleave('#mobile_no', {
		phone: true,
		phoneRegionCode: 'US'
	});
</script>

<script type="text/javascript">
    function showSchedulePopup(ref){
        $('#schedule-popup').modal('toggle');
    }
  
</script>

<script type="text/javascript">
	var loading_img = "{{url('/images/front/spinner-gif.gif')}}";
    var loading_img_div_html = '<img style="height: 100px; width: 100px;align-self: center;" src="'+loading_img+'">';
    
    var module_url_path = "{{url('/need_delivery')}}";

    var store_subscriber_url = "{{url('/store_subscriber')}}";

    var user_register_url   = "{{url('/register')}}";
    var driver_register_url = "{{url('/join_our_fleet')}}";

    function submitNeedDelivery() {
        
        if($('#frm_home_page').parsley().validate()){
            if($('input[name=sign_up_type]:checked').val()!=undefined){
                if($('input[name=sign_up_type]:checked').val() == 'user'){
                    $("#frm_home_page").attr('action',user_register_url);
                    $("#frm_home_page").submit();
                }
                else if($('input[name=sign_up_type]:checked').val() == 'driver'){
                    $("#frm_home_page").attr('action',driver_register_url);
                    $("#frm_home_page").submit();
                }
            }
            return false;

        }

		/*if ($('#validation-form').valid()) {

			$.ajax({
				url: module_url_path,
				type: 'POST',
				data: $('#validation-form').serialize(),
				datatype: 'json',
				beforeSend: function() {

					$('#btn_submit').prop('disabled', true);
					$('#btn_submit').html("<span><i class='fa fa-spinner fa-spin'></i> Processing...</span>");

					// $('#btn_submit').hide();
					$('#success_div').html('');
					$('#error_div').html('');
				},
				success: function(response) {
					if (response.status == "success") {
						$('#btn_submit').prop('disabled', false);

						$('#btn_submit').html("Submit Form");

						$('#validation-form').each(function() {
							this.reset();
						});
						var genrated_html = '<div class="alert alert-success">' +
							'<strong>Success ! </strong>' + response.msg + '' +
							'</div>';

						$('#success_div').html(genrated_html);
						setTimeout(function() {
							window.location.reload();
						}, 4000);

					} else {
						$('#btn_submit').prop('disabled', false);
						$('#btn_submit').html("Submit Form");
						var genrated_html = '<div class="alert alert-danger">' +
							'<strong>Error ! </strong>' + response.msg + '' +
							'</div>';

						$('#error_div').html(genrated_html);
						setTimeout(function() {
							$('#error_div').html('');
						}, 8000);
					}
					return false;
				}
			});
		}*/
	}

    function checkValidSubscriberDetails(ref){
        if($('#frm_subscriber').parsley().validate()){

            $.ajax({
                url: store_subscriber_url,
                type: 'POST',
                data: $('#frm_subscriber').serialize(),
                datatype: 'json',
                beforeSend: function() {
                    $(ref).prop('disabled', true);
                    $(ref).html("<span><i class='fa fa-spinner fa-spin'></i></span>");
                    $('#success_subscriber_div').html('');
                    $('#error_subscriber_div').html('');
                },
                success: function(response) {
                    if (response.status == "success") {
                        $(ref).prop('disabled', false);

                        $(ref).html("SUBMIT");

                        $('#frm_subscriber').each(function() {
                            this.reset();
                        });
                        var genrated_html = '<div class="alert alert-success">' +
                            '<strong>Success ! </strong>' + response.msg + '' +
                            '</div>';

                        $('#success_subscriber_div').html(genrated_html);
                        setTimeout(function() {
                            // window.location.reload();
                            $('#schedule-popup').modal('toggle');
                            $('#success_subscriber_div').html('');

                        }, 5000);

                    } else {
                        $(ref).prop('disabled', false);
                        $(ref).html("SUBMIT");
                        var genrated_html = '<div class="alert alert-danger">' +
                            '<strong>Error ! </strong>' + response.msg + '' +
                            '</div>';

                        $('#error_subscriber_div').html(genrated_html);
                        setTimeout(function() {
                            $('#error_subscriber_div').html('');
                        }, 8000);
                    }
                    return false;
                }
            });
            console.log($('#subscriber_email').val());

            // if($('input[name=sign_up_type]:checked').val()!=undefined){
            //     if($('input[name=sign_up_type]:checked').val() == 'user'){
            //         $("#frm_home_page").attr('action',user_register_url);
            //         $("#frm_home_page").submit();
            //     }
            //     else if($('input[name=sign_up_type]:checked').val() == 'driver'){
            //         $("#frm_home_page").attr('action',driver_register_url);
            //         $("#frm_home_page").submit();
            //     }
            // }
            return false;

        }
    }
</script>
<!-- Review slider End -->
@stop