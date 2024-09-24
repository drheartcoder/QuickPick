<div class="footer-main-new">
    <div class="container">
        <div class="row">

            <div class="col-sm-4 col-md-4 col-lg-4">
                <div class="footer-logo">
                <a href="{{url('/')}}"><img src="{{ url('/')}}/images/logo.png" alt="logo"/></a></div>
                <div class="footer-info-pick">DOT Number: 3189973 <br>Freight Broker Operating Authority: MC134964</div>
              <div class="hiring-we-section">
              <div class="copy-right-txt main"><i class="fa fa-copyright"></i> Copyright {{date('Y')}} <span>{{config('app.project.name')}}</span>, Inc. All rights reserved. <span class="footer-terms-link"><a href="{{url('/terms_and_conditions')}}" class="terms-footer">Terms &amp; Conditions</a>&nbsp;&nbsp;&nbsp;<a href="{{url('/policy')}}" class="terms-footer">Privacy Policy</a></span></div>
            </div>
              </div>
 

            <div class="col-sm-4 col-md-4 col-lg-4 abc">
               <div class="hiring-we-section footer-col-head">
                <div class="hiring-title">Quick Links</div>
                <div class="reponsive-footer-menu">
                <div class="quick-links">
                    <ul>
                       <li><a href="{{url('/how_it_works')}}">How It Works</a></li>
                        <li><a href="{{url('/our_fleet')}}">Our Fleet</a></li>
                        @if(!\Sentinel::check())
                            <li><a href="{{url('/join_our_fleet')}}">Drive</a></li>
                        @endif
                       <li><a href="{{url('/about_us')}}">About Us</a></li>
                    </ul>
                 </div>
                <div class="clerfix"></div>
                 <div class="join-fleet-btn footer contact">
                    <a href="{{url('/contact_us')}}">Contact Us</a>
                </div>
                </div>
             </div>
            </div>
            
             <div class="col-sm-4 col-md-4 col-lg-4 abc">
              <div class="hiring-we-section footer-col-head ">
                <div class="hiring-title">We are looking for drivers</div>
                <div class="reponsive-footer-menu">
                <div class="hiring-sml-txt">If you are a seasoned truck driver who knows the Washington, D.C. area, click here to join the QuickPick team.</div>
                    <div class="join-fleet-btn footer">
                        <a href="{{url('/join_our_fleet')}}">Drive</a>
                    </div>
                </div>
             </div>
           </div>
            
            
        </div>
    </div>
    <div class="copy-right-footer">
        <div class="container">
        </div>
    </div>

</div>



<script type="text/javascript">
    $(document).ready(function () {
        $('input, textarea').each(function () {

            $(this).on('focus', function () {
                $(this).parent('.form-group').addClass('active');
            });

            $('label').on('click', function () {
                $(this).parent('.form-group').addClass('active');
            });

            $(this).on('blur', function () {
                if ($(this).val().length == 0) {
                    $(this).parent('.form-group').removeClass('active');
                }
            });

            if ($(this).val() != '') $(this).parent('.form-group').addClass('active');

        });
    });
</script>
<script type="text/javascript">
    // setTimeout(function(){
    //     location.reload();  
    // },1)
</script>

<!--<script type="text/javascript">
    $(function() {
        $(".hiring-we-section").on("click", function() {
            $(this).toggleClass("active");
            $(this).find(".reponsive-footer-menu").slideToggle("slow");
            $(this).parent(".abc").siblings().find(".reponsive-footer-menu").slideUp();
            $(this).parent(".abc").siblings().children().removeClass("active")
        })
    });
</script>-->