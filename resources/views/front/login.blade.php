@extends('front.layout.master')                

@section('main_content')

    <!--support section start-->
    <div class="support-section fot-about-us">
        <div class="container">
           <div class="row main-suppot-box">
            <div class="col-sm-6 col-md-6 col-lg-6 white-block">
                <div class="contact-info-about">

                <h2>Contact Information</h2>
                <h4>Behind Every Great Product Is a Great Support Team</h4>
                <h5>Experts provide consultation and on-site POS installation, account setup, hardware configuration, support and ongoing training for you and your staff.</h5>

                <div class="contact-info-block">
                    <ul>
                        <li>
                            <span class="con-img"></span>
                            <div class="con-info">
                                <div class="title">Address:</div>
                                <p>{{isset($arr_site_setting['site_address']) ? $arr_site_setting['site_address'] :''}}</p>

                            </div>
                        </li>
                        <li>
                            <span class="con-img phone"></span>
                            <div class="con-info">
                                <div class="title">Phone:</div>
                                <p>{{isset($arr_site_setting['site_contact_number']) ? $arr_site_setting['site_contact_number'] :''}}</p>
                            </div>
                        </li>
                        <li>
                            <span class="con-img email"></span>
                            <div class="con-info">
                                <div class="title">Email Address:</div>
                                <p>{{isset($arr_site_setting['site_email_address']) ? $arr_site_setting['site_email_address'] :''}}</p>
                                {{-- <p>info@ordertiq.com</p> --}}
                            </div>
                        </li>
                    </ul>
                </div>
                </div>

            </div>
            <div class="col-sm-6 col-md-6 col-lg-6  cont">
                <div class="get-in-touch-wrapper">
                    <h2>Get in Touch</h2>
                    <h4>For all General Enquiries</h4>
                    <h5>Drop in your details and we'll get back to you as soon as possible.</h5>
                    
                    <div id="success_div"></div>
                    <div id="error_div"></div>
                    <form id="validation-form" method="POST" url="{{$module_url_path.'/store'}}">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <input name="first_name" id="first_name" type="text" placeholder="First Name*" data-rule-required="true" class="input-box" />
                        </div>

                        <div class="form-group">
                            <input name="last_name" id="last_name" type="text" placeholder="Last Name*" data-rule-required="true" class="input-box" />
                        </div>

                        <div class="form-group">
                            <input name="email" id="email" type="text" placeholder="Email Address*" data-rule-required="true" data-rule-email="true" class="input-box" />
                        </div>

                        <div class="form-group">
                            <input name="phone" id="phone" type="text" placeholder="Contact Number*" data-rule-required="true" data-rule-digits="true" maxlength="15" minlength="6" class="input-box" />
                        </div>

                        <div class="form-group">
                            <input name="address" id="address" type="text" placeholder="Address*" data-rule-required="true"  class="input-box" />
                        </div>

                        <div class="form-group">
                            <textarea rows="3" class="text-area" name="subject" id="subject" data-rule-required="true" placeholder="Message"></textarea>
                        </div>
                        <button id="btn_submit" type="button" onclick="submitContactEnquiry();" class="red-btn">Send Now</button>
                    </form>

                </div>
            </div>
        </div>
    </div>
    </div>
    <!--support section end-->
        <?php
                $site_address = isset($arr_site_setting['site_address']) ? $arr_site_setting['site_address'] :'';
                $site_address = urlencode($site_address);
                // dd($site_address);
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

    <div class="car-rolling-section">
        <!--
        <div class="road-bck"> <img src="images/road-image.png" alt="road"/> </div>
        <div class="road-car"> <img src="images/cars-image.png" alt="car"/> </div>
-->
    </div>
<script type="text/javascript">
    var module_url_path = "{{url('/store_contact_enquiry')}}";
    function submitContactEnquiry() {
       if($('#validation-form').valid()){

            $.ajax({
                    url:module_url_path,
                    type:'POST',
                    data:$('#validation-form').serialize(),
                    beforeSend:function(){
                        $('#btn_submit').prop('disabled', true);
                        $('#success_div').html('');
                        $('#error_div').html('');
                    },
                    success:function(response)
                    {
                        if(response.status=="success")
                        {
                            $('#btn_submit').prop('disabled', false);
                            $( '#validation-form' ).each(function(){
                                this.reset();
                            });
                            var genrated_html = '<div class="alert alert-success">'+
                                                    '<strong>Success ! </strong>'+ response.msg+''+
                                                '</div>';

                            $('#success_div').html(genrated_html);
                            setTimeout(function(){
                                $('#success_div').html('');
                            },8000);

                        }
                        else
                        {
                            $('#btn_submit').prop('disabled', false);
                            var genrated_html = '<div class="alert alert-danger">'+
                                                    '<strong>Error ! </strong>'+ response.msg+''+
                                                '</div>';

                            $('#error_div').html(genrated_html);
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

@stop