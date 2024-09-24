@extends('front.layout.master')  

<style>
    .contact-img-main-wrapper{position: relative}
    .contact-img-main{position: relative;padding: 50px 0 64px;background-repeat: no-repeat;background-position: top center;background-image:  url({{url('/images/contact-us-bg.jpg')}});}
    .contact-img-main:before{position: absolute;content: "";top: 0;left: 0;width: 100%;height: 100%;background-color: rgba(0,0,0,0.7)}
    .contact-info-about{position: relative}
    .header-home #flipdashboard2 {margin: 30px 0 0 30px !important}
    
    
    /*------------main replace css ----------------*/
    .contact-img-main-wrapper .contact-info-about{background-color: transparent;padding: 0;margin-left: 87px;}
    .contact-img-main-wrapper .get-in-touch-wrapper{position: absolute;top: 0;right: 100px;padding: 50px 30px;background-color: #5dc3e9;width: 100%;max-width: 555px;}
    .contact-img-main-wrapper .get-in-touch-main-head{font-size: 54px;color: #fff;font-family: 'robotolight';position: relative;text-transform: uppercase}
    .contact-img-main-wrapper .get-in-touch-main-head span{font-family: 'robotobold';font-weight: 600;color: #232425}
    .get-in-touch-main-head.main span{color: #5dc3e9}
    .contact-img-main-wrapper .contact-info-about h4{color: #fff;font-size: 21px;margin: 0 0 2px;line-height: 30px}
    .contact-img-main-wrapper .contact-info-about h5{color: #fff;font-size: 18px;margin: 0;width: 100%;max-width: 640px;line-height: 20px;}
    .contact-img-main-wrapper .con-info .title{font-size: 18px;font-family: 'robotobold';margin: 0 0 0;color: #5dc3e9;font-weight: 600;}
    .contact-img-main-wrapper .con-info p{font-size: 16px;color: #ffff}
    .get-in-touch-line{height: 1px;width: 100%;background: #fff;margin: 10px 0 20px;position: relative}
    .contact-img-main-wrapper .form-group.cotact input{background: #fff}
    .contact-img-main-wrapper .form-group .text-area{background: #fff !important;height: 145px}
    .captcha-text{font-size: 16px;text-align: center;color: #fff;position: relative;font-family: 'robotolight';margin: -6px 0 15px}
    .captcha-img{text-align: center}
    .captcha-img a{position: relative;margin: 20px 0 44px;display: block}
    .contact-img-main-wrapper .red-btn{height: 40px;width: 100%;background-color: #232425;border-radius: 0;color: #5dc3e9;font-family: 'robotobold';line-height: 40px;
     border: 1px solid #232425;}
    .contact-img-main-wrapper .red-btn:hover{color: #fff;border: 1px solid #fff}
    .contact-img-main-wrapper .get-in-touch-wrapper::before{background-color: #5dc3e9}
    .get-in-touch-for-all-text{color: #232425;font-family: 'robotolight';font-size: 24px;position: relative}
    .get-in-touch-drop-in-your{color: #fff;font-family: 'robotomedium';font-size: 17px;position: relative;line-height: 20px;margin: 0px 0 21px}
    .contact-img-main-wrapper .contact-info-block .con-img{width: 39px}
    .contact-img-main-wrapper .con-info{margin-left: 53px}

    /*------------main replace css ----------------*/
    
    @media all and (max-width:1400px){
      .contact-img-main-wrapper .contact-info-about{margin-left: 13px} 
      .contact-img-main-wrapper .contact-info-about h5{max-width: 470px}
      .contact-img-main-wrapper .get-in-touch-wrapper{right: 30px}
      .header-home #flipdashboard2 {margin: 17px 0 0 30px !important}
    }
    @media all and (max-width:1199px){
      .contact-img-main-wrapper .contact-info-about h4{line-height: 23px;max-width: 300px;margin-bottom: 8px;}
      .contact-img-main-wrapper .contact-info-about h5 {max-width: 340px}
      .contact-img-main-wrapper .contact-info-block {margin: 32px 0 0}
      .contact-img-main-wrapper .con-info {margin-left: 53px;width: 100%;max-width: 300px;    position: relative;
    z-index: 9;}
      .contact-img-main-wrapper .contact-info-block li {margin: 0 0 26px}
      .contact-img-main-wrapper .contact-img-main{padding: 50px 0 39px}
    }
    @media all and (max-width:991px){
      .contact-img-main-wrapper .get-in-touch-wrapper{position: relative;margin: 35px auto 30px;right: 0;left: 0}
      .contact-info-about h4{max-width: 1000%}
      .contact-img-main-wrapper .contact-info-about h5 {max-width: 90%}
      .contact-img-main-wrapper .contact-img-main {padding: 50px 0 25px}
      .header-home #flipdashboard2 {margin: 14px 0 0 30px !important}
    }
    @media all and (max-width:767px){
      .contact-img-main-wrapper .get-in-touch-wrapper {position: relative;margin: 5px 0 0;right: 0;left: 0;max-width: 100%;padding: 30px 20px 80px} 
      /*.contact-img-main-wrapper .contact-img-main{padding: 27px 0 25px;height: 260px;background-size: cover;background-color: rgba(0,0,0,0.2);background-image:  url({{url('/images/contact-bnner-responsive-bg.jpg')}});}*/
      .contact-img-main-wrapper .contact-info-about {margin-left: 0;overflow: hidden}
      .contact-img-main-wrapper .contact-info-about h5 {max-width: 100%;font-family: 'robotolight';font-size: 15px}
      .contact-img-main-wrapper .get-in-touch-main-head {font-size: 47px;margin-bottom: 19px}
      .contact-img-main-wrapper .get-in-touch-main-head.two {font-size: 40px}
      .contact-img-main-wrapper .contact-img-main {padding: 30px 0 50px}
    /*.contact-img-main-wrapper .contact-info-block{margin: 32px 0 0;background: #000;padding: 30px;}*/
    }
</style>                            
{{-- <script src='https://www.google.com/recaptcha/api.js'></script> --}}
@section('main_content')

<?php
    $site_address = isset($arr_site_setting['site_address']) ? $arr_site_setting['site_address'] :'';
    $site_contact_number = isset($arr_site_setting['site_contact_number']) ? $arr_site_setting['site_contact_number'] :'';
    $site_email_address = isset($arr_site_setting['site_email_address']) ? $arr_site_setting['site_email_address'] :'';
    ?>
<div class="blank-div"></div>
    <!--<div class="email-block">
        <div class="container">
            <div class="row">
                <div class="col-sm-6 col-md-6 col-lg-6">
                    <div class="headding-text-bredcr">
                        Contact Us
                    </div>
                </div>
                <div class="col-sm-6 col-md-6 col-lg-6">
                    <div class="bredcrum-right">
                        <a href="{{ url('/')}}" class="bredcrum-home"> Home </a>
                        <span class="arrow-righ"><i class="fa fa-angle-right"></i></span>Contact Us
                    </div>
                </div>
            </div>
        </div>
    </div>-->

<div class="contact-img-main-wrapper">  
    <div class="contact-img-main">
        <div class="container-fluid">
           <div class="contact-info-about">

                <div class="get-in-touch-main-head main">Contact <span>Us</span></div>
                <h4>Behind Every Great Product Is a Great Support Team</h4>
                <h5>Experts provide consultation and on-site POS installation, account setup,hardware configuration, support and ongoing training for you and your staff.</h5>

                <div class="contact-info-block">
                    <ul>
                        <li>
                            <span class="con-img"></span>
                            <div class="con-info main">
                                <div class="title">Address:</div>
                                <div class="conta-link-call">{{ $site_address }}</div>
                            </div>
                            <div class="clearfix"></div>
                        </li>
                        <li>
                            <span class="con-img phone"></span>
                            <div class="con-info main">
                                <div class="title">Phone:</div>
                                <div class="conta-link-call"><a href="javascript:void(0)">{{ $site_contact_number }}</a></div>
                            </div>
                            <div class="clearfix"></div>
                        </li>
                        <li>
                            <span class="con-img email"></span>
                            <div class="con-info main">
                                <div class="title">Email:</div>
                                <div class="conta-link-call">{{ $site_email_address }}</div>
                                {{-- <div class="conta-link-call">info@ordertiq.com</div> --}}
                            </div>
                            <div class="clearfix"></div>
                        </li>
                    </ul>
                </div>
                </div>

    </div>
    </div>
    <!--support section end-->

              <?php 
               $site_address = urlencode($site_address); 
               $api_key = 'AIzaSyCccvQtzVx4aAt05YnfzJDSWEzPiVnNVsY';
                $map_url = "https://www.google.com/maps/embed/v1/place?q=".$site_address."&amp;key=".$api_key."";

                // $map_url = "https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d130883.73129326482!2d73.90304115063152!3d19.93900283877269!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3bddeae4e0245423%3A0xeb6a128eb0f552ae!2sWebwing+Technologies+(+Website+Design+%26+Mobile+App+%26+Website+Development+Company)!5e0!3m2!1sen!2sin!4v1487651509958"

        ?>
        
        
       
      <div class="map-cont">
        <div class="featuresInfo">
            <div class="map-setion">
                <div class="loc-map">
                    {{-- <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d130883.73129326482!2d73.90304115063152!3d19.93900283877269!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3bddeae4e0245423%3A0xeb6a128eb0f552ae!2sWebwing+Technologies+(+Website+Design+%26+Mobile+App+%26+Website+Development+Company)!5e0!3m2!1sen!2sin!4v1487651509958" width="100%" height="445px" display="block" frameborder="0" style="border:0" allowfullscreen></iframe> --}}
                    <iframe src="{{$map_url}}" width="100%" height="445px" display="block" frameborder="0" style="border:0" allowfullscreen></iframe>
                </div>
            </div>
        </div>
    </div>
    <div class="get-in-touch-wrapper">
                    <div class="get-in-touch-main-head two">Get in <span>Touch</span></div>
                    <div class="get-in-touch-line"></div>
                    <div class="get-in-touch-for-all-text">For All General Inquiries</div>
                    <div class="get-in-touch-drop-in-your">Drop in your details and we'll get back to you as soon as possible.</div>
                    <div id="success_div"></div>
                    <div id="error_div"></div>
                    <form id="validation-form">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <input name="first_name" type="text" placeholder="First Name" data-rule-required="true" data-rule-maxlength="20"/>
                        </div>
                        <div class="form-group">
                            <input name="last_name" type="text" placeholder="Last Name" data-rule-required="true" data-rule-maxlength="20"/>

                        </div>
                        <div class="form-group">
                            <input name="email" type="email" placeholder="Email" data-rule-required="true" />

                        </div>
                        <div class="form-group">
                            <input name="address" id="address" type="text" placeholder="Address" data-rule-required="true" data-rule-maxlength="100"/>
                        </div>
                        
                        <div class="form-group">
                            <input name="phone" type="text" id="mobile_no" placeholder="Phone" data-rule-required="true" data-rule-minlength="7" data-rule-maxlength="14"/>
                        </div>

                        {{-- <div class="row">
                            <div class="col-sm-6 col-md-6 col-lg-6">
                                <div class="form-group">
                                    <input name="state" type="text" placeholder="State*" data-rule-required="true" />
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-6 col-lg-6">
                                <div class="form-group">
                                    <input name="city" type="text" placeholder="City*" data-rule-required="true" />

                                </div>
                            </div>
                        </div> --}}
                        <div class="form-group">
                            <textarea rows="3" class="text-area" name="subject" placeholder="Subject" data-rule-required="true" ></textarea>
                        </div>
                        
                        <div class="form-group">
                           {{-- <div class="captcha-text">* Asterisk Indicates Requiered Field</div> --}}
                            {{-- <div class="captcha-text">* Asterisk Indicates Requiered Field</div> --}}
                            {{-- <div class="captcha-img"><a href="javascript:void(0)"><img src="{{url('/')}}/images/capcta-img.png" alt="capcta-img" /></a></div> --}}
                            <div id="captcha_container"></div>
                            <div id="captcha_error_div" style="color: #a94442"></div>
                            {{-- <div class="g-recaptcha captcha-img" data-sitekey="6LdUvGkUAAAAAFZ8iw0kvJp_0ujleIqLfaTvf9pz"></div> --}}
                        {{-- <div class="g-recaptcha" data-sitekey="6LdUvGkUAAAAAFZ8iw0kvJp_0ujleIqLfaTvf9pz"></div> --}}
                        </div>
                        
                        
                        <button id="btn_submit" type="button" onclick="submitContactEnquiry();" class="red-btn contact">Send Now</button>
                        {{-- <button type="submit" class="">Send Now</button> --}}
                    </form>
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

    <script type='text/javascript'>
    var captchaContainer = null;
    var loadCaptcha = function() {
      captchaContainer = grecaptcha.render('captcha_container', {
        'sitekey' : '6LdUvGkUAAAAAFZ8iw0kvJp_0ujleIqLfaTvf9pz',
        'callback' : function(response) {
          console.log(response);
          $('#captcha_error_div').html('');
        }
      });
    };
    </script>

<script type="text/javascript">
    var module_url_path = "{{url('/store_contact_enquiry')}}";
    function submitContactEnquiry() {
       if($('#validation-form').valid()){

            $('#captcha_error_div').html('');

            // if(grecaptcha.getResponse() == ''){
            //     $('#captcha_error_div').html('Please resolve the captcha and submit');
            //     return;
            // }

            $.ajax({
                    url:module_url_path,
                    type:'POST',
                    data:$('#validation-form').serialize(),
                    beforeSend:function(){
                        $('#btn_submit').prop('disabled', true);
                        $('#btn_submit').html("<span><i class='fa fa-spinner fa-spin'></i> Processing...</span>");
                        $('#success_div').html('');
                        $('#error_div').html('');
                    },
                    success:function(response)
                    {
                        if(response.status=="success")
                        {
                            $('#btn_submit').prop('disabled', false);
                            $('#btn_submit').html("Send Now");
                            $( '#validation-form' ).each(function(){
                                this.reset();
                            });
                            grecaptcha.reset();

                            var genrated_html = '<div class="alert alert-success">'+
                                                    '<strong>Success ! </strong>'+ response.msg+''+
                                                '</div>';

                            $('#success_div').html(genrated_html);
                            $('.get-in-touch-wrapper').scrollTop($('.get-in-touch-wrapper')[0].scrollHeight);
                            setTimeout(function(){
                                $('#success_div').html('');
                            },8000);

                        }
                        else
                        {
                            $('#btn_submit').prop('disabled', false);
                            $('#btn_submit').html("Send Now");
                            var genrated_html = '<div class="alert alert-danger">'+
                                                    '<strong>Error ! </strong>'+ response.msg+''+
                                                '</div>';

                            $('#error_div').html(genrated_html);
                            $('#error_div').scrollTop($('#error_div')[0].scrollHeight);
                            setTimeout(function(){
                                $('#error_div').html('');
                            },8000);
                        }
                        return false;
                    }
            });
       }
    }
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