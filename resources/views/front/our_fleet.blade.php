@extends('front.layout.master')                
 
@section('main_content')

<div class="blank-div"></div>

     <div class="maine-banner fleet-banner">
    <div class="container">
      <div class="fleet-section">
        <div class="fleet-title">Our Fleet</div>
        <div class="fleet-sml-txt">Experienced drivers. Quality vehicles. Rapid delivery.</div>
      </div>
    </div>
  </div>

  <div class="clearfix"></div>
    
    {{-- {!! $page_details->page_desc !!} --}}

    <div class="quick-fleed-section tab responsive">
    <div class="container">
      <!-- Tab Main Section Start -->
      <div class="tabbing_area">
        <div id="horizontalTab" class="quick-fleed-tabb">
          <ul class="resp-tabs-list">
            <li>Sedan</li>
            <li>SUV</li>
            <li>Cargo Van</li>
            <li>Pickup Truck</li>
            <li>10-26 Box Truck</li>
          </ul>
          <div class="clearfix"></div>
          <div class="resp-tabs-container">
            <!--tab-1 start-->
            <div>
              <div class="fleed-main-tab-section">
                <div class="col-sm-6 col-md-6 col-lg-6">
                  <div class="fleed-sedan-img top-mar"> <img src="images/Fleet-Sedan-1.jpg" alt="fllet" /> </div>
                </div>

                <div class="col-sm-6 col-md-6 col-lg-6">
                  <div class="fleed-sedan-box">
                    <div class="fleed-truck-title">Sedan</div>
                    <div class="fleed-specifications-box">
                      <div class="specifications-title">Specifications</div>
                      <div class="specifications-size"><span>Size :</span>Approximately 13 ft. to 16 ft. long</div>
                      <div class="specifications-size"><span>Side Doors :</span>Yes</div>
                      <div class="specifications-size price"><span>Price :</span>Coming Soon</div>
                      
                      <div class="specifications-sml-txt">*Dimensions of vehicles may vary slightly</div>   
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!--tab-2 start-->
            <div>
              <div class="fleed-main-tab-section">
                <div class="col-sm-6 col-md-6 col-lg-6">
                  <div class="fleed-sedan-img"> <img src="images/Fleet-SUV.jpg" alt="fllet" /> </div>
                </div>

                <div class="col-sm-6 col-md-6 col-lg-6">
                  <div class="fleed-sedan-box">
                    <div class="fleed-truck-title">SUV</div>
                    <div class="fleed-specifications-box">
                      <div class="specifications-title">Specifications</div>
                                            <div class="specifications-size"><span>Size :</span>Approximately 12 ft<sup>3</sup> – 30 ft<sup>3</sup></div>
                      <div class="specifications-size"><span>Side Doors :</span>Yes</div>
                      <div class="specifications-size"><span>Back Doors :</span>Yes</div>
                      <div class="specifications-size price"><span>Price :</span>Coming Soon</div>
                      
                      <div class="specifications-sml-txt">*Dimensions of vehicles may vary slightly</div>   
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!--tab-3 start-->

            <div>
                             <div class="fleed-main-tab-section">
                <div class="col-sm-6 col-md-6 col-lg-6">
                  <div class="fleed-sedan-img"> <img src="images/Fleet-Van.jpg" alt="fllet" /> </div>
                </div>

                <div class="col-sm-6 col-md-6 col-lg-6">
                  <div class="fleed-sedan-box">
                    <div class="fleed-truck-title">Cargo Van</div>
                    <div class="fleed-specifications-box">
                      <div class="specifications-title">Specifications</div>
                      <div class="specifications-size"><span>Size :</span>Approximately 76 ft<sup>3</sup> – 250 ft<sup>3</sup></div>
                      <div class="specifications-size"><span>Side Doors :</span>Yes</div>
                      <div class="specifications-size"><span>Back Doors :</span>Yes</div>
                      <div class="specifications-size price"><span>Price :</span>Coming Soon</div>
                      
                      <div class="specifications-sml-txt">*Dimensions of vehicles may vary slightly</div>   
                    </div>
                  </div>
                </div>
              </div>

            </div>

            <!--tab-4 start-->
            <div>
                                    <div class="fleed-main-tab-section">
                <div class="col-sm-6 col-md-6 col-lg-6">
                  <div class="fleed-sedan-img"> <img src="images/Truck.jpg" alt="fllet" /> </div>
                </div>

                <div class="col-sm-6 col-md-6 col-lg-6">
                  <div class="fleed-sedan-box">
                    <div class="fleed-truck-title">Pickup Truck</div>
                    <div class="fleed-specifications-box">
                      <div class="specifications-title">Specifications</div>
                                            <div class="specifications-size"><span>Size :</span>Approximately 5ft – 7ft truck bed, 31 ft<sup>3</sup> – 75 ft<sup>3</sup></div>
                      <div class="specifications-size"><span>Side Doors :</span>Yes</div>
                      <div class="specifications-size price"><span>Price :</span>Coming Soon</div>
                      
                      <div class="specifications-sml-txt">*Dimensions of vehicles may vary slightly</div>   
                    </div>
                  </div>
                </div>
              </div>

            </div>
            
            <!--tab-5 start-->
            <div>
                         <div class="fleed-main-tab-section">
                                    
                            <div class="fleet-slider-main">
                                <div id="myCarousel" class="carousel slide" data-ride="carousel">
                                    <!-- Wrapper for slides -->
                                    <div class="carousel-inner">
                                        <div class="item active">
                                            <div class="fleet-slider-img"> <img src="images/Fleet-10-15ft.jpg" alt="fllet" /> </div>
                                        </div>

                                        <div class="item">
                                            <div class="fleet-slider-img"> <img src="images/Fleet-10-15ft.jpg" alt="fllet" /> </div>
                                        </div>

                                        <div class="item">
                                            <div class="fleet-slider-img"> <img src="images/Fleet-10-15ft.jpg" alt="fllet" /> </div>
                                        </div>
                                    </div>

                                    <!-- Left and right controls -->
                                    <a class="left carousel-control" href="#myCarousel" data-slide="prev">
                                <span class="glyphicon"><img src="images/fleet-slider-left-btn.png" alt="fleet-slider-left" /></span>
                                <span class="sr-only"></span>
                              </a>
                                    <a class="right carousel-control" href="#myCarousel" data-slide="next">
                                <span class="glyphicon"><img src="images/fleet-slider-right-btn.png" alt="fleet-slider-right" /></span>
                                <span class="sr-only"></span>
                              </a>
                                </div>
                            </div>    
                                    
                                    
                
                
                                 <div class="row">
                <div class="col-sm-6 col-md-4 col-lg-3">
                  <div class="fleed-sedan-box border-bottom respon-our">
                    <div class="fleed-truck-title">10′ Box Truck</div>
                    <div class="fleed-specifications-box">
                      <div class="specifications-title">Specifications</div>
                      <div class="specifications-size"><span>Inside dimensions: </span>9’11” x 6’4″ x 6’2″ (LxWxH)</div>
                      <div class="specifications-size"><span>Deck height :</span>2’5″ Length: 9’11”</div>
                      <div class="specifications-size"><span>Door opening :</span>5’11” x 5’7″ (WxH)</div>
                      <div class="specifications-size price"><span>Price :</span>Coming Soon</div>
                      
                      <div class="specifications-sml-txt">*Dimensions of vehicles may vary slightly</div>   
                    </div>
                  </div>
                </div>
                
                <div class="col-sm-6 col-md-4 col-lg-3">
                  <div class="fleed-sedan-box border-bottom respon-our">
                    <div class="fleed-truck-title">15′ Box Truck</div>
                    <div class="fleed-specifications-box">
                      <div class="specifications-title">Specifications</div>
                      <div class="specifications-size"><span>Inside dimensions: </span>15′ x 7’8″ x 7’2″ (LxWxH)</div>
                      <div class="specifications-size"><span>Deck height :</span>2’9″ Length: 12’5″</div>
                      <div class="specifications-size"><span>Door opening :</span>7’3″ x 6’5″ (WxH)</div>
                      <div class="specifications-size price"><span>Price :</span>Coming Soon</div>
                      
                      <!--<div class="specifications-sml-txt">*Dimensions of vehicles may vary slightly</div> --> 
                    </div>
                  </div>
                </div>
                
                <div class="col-sm-6 col-md-4 col-lg-3">
                  <div class="fleed-sedan-box border-bottom respon-our">
                    <div class="fleed-truck-title">20′ Box Truck</div>
                    <div class="fleed-specifications-box">
                      <div class="specifications-title">Specifications</div>
                      <div class="specifications-size"><span>Inside dimensions: </span>19’6″ x 7’8″ x 7’2″ (LxWxH)</div>
                      <div class="specifications-size"><span>Deck height :</span>2’11” Length: 16’8″</div>
                      <div class="specifications-size"><span>Door opening :</span>7’3.75″ x 6’5″ (WxH)</div>
                      <div class="specifications-size price"><span>Price :</span>Coming Soon</div>
                      
                      <!--<div class="specifications-sml-txt">*Dimensions of vehicles may vary slightly</div>   -->
                    </div>
                  </div>
                </div>
                
                <div class="col-sm-6 col-md-4 col-lg-3">
                  <div class="fleed-sedan-box respon-our">
                    <div class="fleed-truck-title">26′ Box Truck</div>
                    <div class="fleed-specifications-box">
                      <div class="specifications-title">Specifications</div>
                      <div class="specifications-size"><span>Inside dimensions: </span>26’5″ x 7’8″ x 8’3″ (LxWxH)</div>
                      <div class="specifications-size"><span>Deck height :</span>2′ 9″ Length 23’5″</div>
                      <div class="specifications-size"><span>Door opening :</span>7’3″ x 6’10” (WxH)</div>
                      <div class="specifications-size price"><span>Price :</span>Coming Soon</div>
                      
                      <!--<div class="specifications-sml-txt">*Dimensions of vehicles may vary slightly</div>-->    
                    </div>
                  </div>
                </div>
                </div>
              </div>

            </div>
          </div>
        </div>
      </div>
      <!-- Tab Main Section Start -->


    </div>
  </div>
 

  <div class="interested-joining-section our-fleet">
    <div class="container">
      <div class="row">
        <div class="col-sm-6 col-md-6 col-lg-6">
          <div class="interested-joining-title">Need something delivered?</div>
          <div class="interested-joining-sml-txt">We work quickly to meet your needs so you don’t have to! <a href="{{url('/contact_us')}}">Contact us to get started.</a></div>
          <div class="schedul-btn interested-joining one">
            {{-- <button type="submit" class="red-btn">Get Started</button> --}}

              <button  @if($is_login==1) onclick="location.href = 'javascript:void(0)';"   @else  onclick="location.href = '{{url('/login')}}';"  @endif  type="button" class="red-btn">Get Started</button> 
          </div>
        </div>

        <div class="col-sm-6 col-md-6 col-lg-6">
          <div class="interested-joining-title">
            Interested in joining the team?</div>
          <div class="interested-joining-sml-txt">We’re looking for drivers to deliver around the Washington Washington, D.C. Metro area. <a href="{{url('/join_our_fleet')}}">Join our fleet for more information.</a></div>
          <div class="schedul-btn interested-joining ">
            <button onclick="location.href = '{{url('/join_our_fleet')}}';"  type="button" class="red-btn">Apply Now</button>
          </div>
        </div>

      </div>
    </div>
  </div>



    <!--tabing-css-js-start-here-->
      <script src="{{ url('/')}}/js/front/easyResponsiveTabs.js" type="text/javascript"></script>
      <script>
         //<!--tab js script-->  
         $('#horizontalTab').easyResponsiveTabs({
           type: 'default',   
           width: 'auto', 
           fit: true, 
           closed: 'accordion', 
           activate: function(event) { 
               var $tab = $(this);
               var $info = $('#tabInfo');
               var $name = $('span', $info);
         
               $name.text($tab.text());
         
               $info.show();
           }
         });
     </script>
@stop

    
