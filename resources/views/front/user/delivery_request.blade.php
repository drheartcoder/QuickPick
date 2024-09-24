 @extends('front.layout.master')                

    @section('main_content')

    <?php $user_path     = config('app.project.role_slug.user_role_slug'); ?>

    <div class="blank-div"></div>
    <div class="email-block">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6 col-md-6 col-lg-6">
                    <div class="headding-text-bredcr">
                       Delivery Request
                    </div>
                </div>
                <div class="col-sm-6 col-md-6 col-lg-6">
                    <div class="bredcrum-right">
                        <a href="{{ $module_url_path }}" class="bredcrum-home"> Dashboard </a>
                        <span class="arrow-righ"><i class="fa fa-angle-right"></i></span>
                        Delivery Request
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style type="text/css">
      /*.second_step_div{transform: translateY(15px);transition: 0.5s;opacity: 0;visibility: hidden;height: 0;overflow: hidden;}
      .edit-posted-bg-main.on-click-show-div{height: auto;transform: translateY(0px);transition: 0.5s;opacity: 1;visibility: visible;margin-top: 0;}
      .edit-posted-bg-main.on-click-hide-section{transform: translateY(-15px);transition: 0.5s;opacity: 0;visibility: hidden;height: 0;overflow: hidden;padding: 0;}*/
      .back-btn-section{float: left;}
      /*.first_step_div{height: auto;transform: translateY(0px);transition: 0.5s;opacity: 1;visibility: visible;margin-top: 0;}*/
      .success-class {color: #069611;font-size: 13px;}
    </style>

    <!--dashboard page start-->
    <div class="main-wrapper">
      <div class="container-fluid">
        <div class="row">
          @include('front.user.left_bar')
            <div class="middle-bar">

            @include('front.layout._operation_status')
            
            <div id="show_success_div"></div>
            <div id="show_load_post_error_div"></div>
            
            <form id="frm_packaging_details"  data-parsley-validate>
              {{ csrf_field() }}

              <div class="edit-posted-bg-main delevery-request" id="first_step_div">
                  <div class="delivery-request-main-wrapper" id="dvMap"></div>  
                  
                  <div class="delivery-request-map-inner" id="pickup_drop_div">
                    
                    <input type="hidden" name="request_type" value="BUSINESS">

                    <div class="form-group">
                        <span class="delive-reque-icon"></span>
                        <input type="text" id="pickup_location" name="pickup_location" placeholder="Address For Pickup" >
                    </div>

                    <div class="form-group">
                      <span class="delive-reque-icon"></span>
                        <input id="drop_location" name="drop_location" placeholder="Destination" >
                    </div>      
                  </div>

                  <div class="estimate-min">
                  <div class="estimate-cost est-left" id="div_estimate_time" style="display: none;"></div>
                  <div class="estimate-cost" id="div_estimate_cost" style="display: none;"></div> 
                  <div class="clearfix"></div> 
                </div>

                  <div class="delivery-reques-bottom-img-wrap content-d" id="third_step_div" style="display: none;">
                     <div class="min-close-box">
                  <button class="close-box" type="button" onclick="confirmClearDetails();"><span>X</span></button>
                </div>
                    <div class="delivery-loacation-block">
                      <div class="loacation-block" id="span_pickup_loacation"></div>
                                    
                      <div class="loacation-block" id="span_drop_loacation"></div>
                      <div class="clearfix"></div> 
                    </div>

                    <div class="delivery-reques-bottom-img-box" id="vehicle_type_div">
                        <div class="clearfix"></div>
                    </div>

                    <div class="clearfix"></div>
                      
                    <div class="delivery-by">
                      <h3>Delivery</h3>
                      <div class="driver-requ-button-wrapper">
                        <div class="radio-btns">
                          <div class="radio-btn">
                              <input type="radio" id="f-option" name="is_future_request" checked value="0"> 
                              <label for="f-option">Now</label>
                              <div class="check"></div>
                          </div>
                          <div class="radio-btn">
                              <input type="radio" id="s-option" name="is_future_request" value="1">
                              <label for="s-option">Future Booking</label>
                              <div class="check">
                                  <div class="inside"></div>
                              </div>
                          </div>
                        </div>
                        <div class="clearfix"></div>
                      </div>
                    </div>

                    <div class="driver-requ-date-wrapper" id="div_future_booking_details" style="display: none;">
                        <div class="co-sm-6 col-md-6 col-lg-6">
                            <div class="form-group">
                                <div class="transac-date-icon"><i class="fa fa-calendar"></i></div>
                                <input type="text" id="future_request_date" name="future_request_date" placeholder="Future Booking Date">
                                <div class="error" id="error_future_request_date"></div>
                            </div>
                        </div>
                        <div class="co-sm-6 col-md-6 col-lg-6">
                            <div class="form-group">
                                <div class="transac-date-icon clock"><i class="fa fa-clock-o"></i></div>
                                  <input type="text" class="timepicker-default" id="request_time" name="request_time" placeholder="Future Booking Time">
                                <div class="error" id="error_request_time"></div> 
                            </div>
                        </div>
                       <div class="clearfix"></div>
                    </div>

                  </div>

                  <input type="hidden" name="pickup_lat" id="pickup_lat">
                  <input type="hidden" name="pickup_lng" id="pickup_lng">

                  <input type="hidden" name="drop_lat" id="drop_lat">
                  <input type="hidden" name="drop_lng" id="drop_lng">

                  <div class="delivery-reques-form-btn">
                    <a href="javascript:void(0)" id="btn_first_step_div_next" onclick="validFirstStepDetails(this);" class="form-btn-delivery-req">Next</a>
                  </div>
                  <div class="map-form-btn" >
                  <div id="btn_back_div" style="display:none;" class="write-review-btn green margin-botto no-mar back-btn-section">
                      <button type="button" onclick="loadPreviousView();">Back</button>
                  </div>
                  <div id="btn_next_div" style="display:none;" class="write-review-btn green margin-botto no-mar">
                      <button type="button" onclick="validFormDetails(this);">Next</button>
                  </div>
                <div class="clearfix"></div>

              </div>

                  <div class="clearfix"></div>

              </div>
     
              <div class="edit-posted-bg-main delevery-req-form second_step_div" id="second_step_div" style="display: none">

                <div class="row">

                  <div class="col-sm-6 col-md-6 col-lg-6">
                    <div class="form-group">
                      <select name="package_type" id="package_type" data-parsley-required-message="Please select package type" data-parsley-required="true" data-parsley-errors-container="#err_package_type" onchange="checkCurrentpackageType(this)"> 
                        <option value="">Package Type</option>
                        <option value="BOX">Box</option>
                        <option value="CARTON">Carton</option>
                        <option value="CONTAINER">Container</option>
                        <option value="ENVELOPE">Envelope</option>
                        <option value="PALLET">Pallet</option>
                        <option value="OTHER">Other</option>
                      </select>
                      <div id="err_package_type" class="error"></div>
                    </div>
                  </div>
                  <div id="package_other_details">
                    <div class="col-sm-6 col-md-6 col-lg-6">
                      <div class="form-group">
                          <input type="text" id="weight" name="package_weight" placeholder="Weight (Pounds)" data-parsley-required-message="Please enter weight in pound" data-parsley-required="true" data-parsley-errors-container="#err_weight" onkeypress="return isNumberKey(event)"/>
                          <div id="err_weight" class="error"></div>
                      </div> 
                    </div>
            
                    <div class="col-sm-6 col-md-6 col-lg-6">
                      <div class="form-group">
                        <input type="text" id="length" name="package_length" placeholder="Length (ft)" data-parsley-required-message="Please enter Length in ft" data-parsley-required="true" data-parsley-errors-container="#err_length" onkeypress="return isNumberKey(event)"/>
                        <div id="err_length" class="error"></div>
                      </div> 
                    </div>
                    
                    <div class="col-sm-6 col-md-6 col-lg-6">
                      <div class="form-group">
                        <input type="text" id="width" name="package_breadth" placeholder="Width (ft)" data-parsley-required-message="Please enter width in ft" data-parsley-required="true" data-parsley-errors-container="#err_width" onkeypress="return isNumberKey(event)"/>
                        <div id="err_width" class="error"></div>
                      </div> 
                    </div>
                    
                    <div class="col-sm-6 col-md-6 col-lg-6">
                      <div class="form-group">
                        <input type="text" id="height" name="package_height" placeholder="Height (ft)" data-parsley-required-message="Please enter height in ft" data-parsley-required="true" data-parsley-errors-container="#err_height" onkeypress="return isNumberKey(event)"/>
                        <div id="err_height" class="error"></div>
                      </div> 
                    </div>
                  
                  </div>

                  <div class="col-sm-6 col-md-6 col-lg-6">
                    <div class="form-group">
                      <input type="text" id="quantity" name="package_quantity" placeholder="Quantity" data-parsley-required-message="Please enter quantity" data-parsley-required="true" data-parsley-errors-container="#err_quantity" onkeypress="return isNumberKey(event)"/>
                      <div id="err_quantity" class="error"></div>
                    </div> 
                  </div>
                  
                  <div class="col-sm-6 col-md-6 col-lg-6">
                    <div class="form-group">
                        <button type="button" class="promo-code-apply" onclick="applyPromoCode(this)">Apply!</button>
                        <input type="text" name="promo_code" id="promo_code" placeholder="Promo Code(Optional)">
                        <div id="err_promo_code" class="error"></div>
                        <div id="success_promo_code" class="success-class"></div>
                        <input type="hidden" name="promo_code_id" id="promo_code_id" value="0">
                    </div> 
                  </div>
                

                  <div class="col-sm-6 col-md-6 col-lg-6">
                      <div class="form-group">
                          @if(isset($arr_card_details) && count($arr_card_details)>0)
                        <select name="card_id" id="card_id" data-parsley-required-message="Please select card" data-parsley-required="true" data-parsley-errors-container="#err_card_id"> 
                          <option value="">Please select card</option>
                            @foreach($arr_card_details as $card)
                              <option value="{{ isset($card['id']) ? $card['id'] : '0' }}">{{ isset($card['masked_card_number']) ? $card['masked_card_number'] : '-' }}</option>
                            @endforeach
                        </select>
                        @else
                          {{-- <input type="hidden" name="card_id" id="card_id" value="0"> --}}
                          <select name="card_id" id="card_id" style="display: none;"> </select>
                          <div class="btns-wrapper" style="text-align: left;">
                              <button type="button" id="btn_open_add_card_modal" onclick="openAddCardModal(this)" class="green-btn chan-right">Add Card</button>
                          </div>
                          <div id="success_card_add" class="success-class"></div>
                        @endif
                        <div id="err_card_id" class="error"></div>
                      </div>
                  </div>               
                </div>

                <div class="delivery-reque-check-wrapper">
                  <div class="check-box login">
                    <p>
                        <input id="is_flammables_checked" class="filled-in" type="checkbox">
                        <label for="is_flammables_checked">Want to ship chemicals or flammables?</label>
                    </p>
                  </div>
                     
                  <div class="check-box login">
                      <p>
                          <input id="is_bonus_checkbox" class="filled-in" type="checkbox" >
                          <label for="is_bonus_checkbox">Use my bonus points (if any)</label>
                      </p>
                  </div>
                </div>

                <input type="hidden" name="is_bonus" id="is_bonus" value="NO">
                <input type="hidden" name="is_admin_assistant" id="is_admin_assistant" value="NO">
                
                <input type="hidden" name="action_type" id="action_type" value="search">
                <input type="hidden" name="vehicle_type_id" id="vehicle_type_id" value="0">
                
                {{-- <div class="write-review-btn green margin-botto no-mar back-btn-section">
                    <button type="button" onclick="loadPreviousView();">Back</button>
                </div>
                <div class="write-review-btn green margin-botto no-mar">
                    <button type="button" onclick="validFormDetails(this);">Next</button>
                </div>
                <div class="clearfix"></div> --}}
              </div> 

            </form> 

            </div>
            @include('front.user.right_bar')
        </div>
      </div>
    </div>

<!-- popup section start -->
<div class="mobile-popup-wrapper">
<div id="add-card-popup" class="modal fade" role="dialog" data-backdrop="static">
  <div class="modal-dialog" style="max-width: 700px;">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Add Credit or Debit card</h4>
      </div>
      <div class="modal-body">
          <div id="add_card_error_div"></div>
            <form id="payment-form">
              <div class="form-row">
                <div class="edit-posted-bg-main">
                    <div id="card-element" class="change-strip-eleme">
                    </div>
                    <div class="error" id="card-errors" role="alert"></div>
                </div>
              </div>

              <br>
              <div class="btns-wrapper change-pass">
                    <button type="submit" id="btn_add_card" class="green-btn chan-right">Add</button>
              </div>
            </form>
      </div>
    </div>

  </div>
</div>   
</div>
<!-- popup section end --> 

<!-- popup section start -->
<div class="mobile-popup-wrapper">
<div id="package-details-popup" class="modal fade" role="dialog" data-backdrop="static">
  <div class="modal-dialog" style="max-width: 700px;">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" onclick="closePackageDetailsModal(this)"></button>
        <h4 class="modal-title">Package Details</h4>
      </div>
      <div class="modal-body">
          <div class="edit-posted-bg-main my-job-details">
              <div class="first-names">
                  <span>Package Type : </span>
                  <div class="first-names-light" id="modal_package_type"></div>
                  <div class="clearfix"></div>
              </div>
              <div id="modal_other_package_details">
                <div class="first-names">
                    <span>Weight (Pounds) : </span>
                    <div class="first-names-light" id="modal_weight"></div>
                    <div class="clearfix"></div>
                </div>
                
                <div class="first-names">
                    <span>Length (ft) : </span>
                    <div class="first-names-light" id="modal_length"></div>
                    <div class="clearfix"></div>
                </div>
                
                <div class="first-names">
                    <span>Width (ft) : </span>
                    <div class="first-names-light" id="modal_width"></div>
                    <div class="clearfix"></div>
                </div>
                
                <div class="first-names">
                    <span>Height (ft) : </span>
                    <div class="first-names-light" id="modal_height"></div>
                    <div class="clearfix"></div>
                </div>

               </div>

              <div class="first-names">
                  <span>Quantity : </span>
                  <div class="first-names-light" id="modal_quantity"></div>
                  <div class="clearfix"></div>
              </div>

              <div class="first-names">
                  <span>Selected Card : </span>
                  <div class="first-names-light" id="modal_selected_card"></div>
                  <div class="clearfix"></div>
              </div>
              
              <br>

              <div class="btns-wrapper change-pass">
                    <button type="button" onclick="closePackageDetailsModal(this)" class="green-btn chan-right">Cancel</button>
                    <button type="button" onclick="validFormDetails(this)" class="green-btn chan-right">Confirm</button>
              </div>
          </div>
      </div>
    </div>

  </div>
</div>   
</div>
<!-- popup section end --> 
<?php
      $enc_user_login_id =  validate_user_login_id();
      $enc_user_login_id = base64_encode($enc_user_login_id);
?>

<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key={{ isset($google_map_api_key) ? $google_map_api_key : 'AIzaSyCccvQtzVx4aAt05YnfzJDSWEzPiVnNVsY' }}&libraries=places"></script>
<script src="{{ url('/') }}/js/admin/SlidingMarker.js"></script>
<link rel="stylesheet" type="text/css" href="{{ url('/') }}/css/admin/sweetalert.css" />
<script src="{{ url('/') }}/js/admin/sweetalert.min.js"></script>
<script type="text/javascript" src="{{ url('/js/admin') }}/sweetalert_msg.js"></script>


<link href="{{url('css/front/bootstrap-datepicker.min.css')}}" rel="stylesheet" type="text/css" />  
<link href="{{url('css/front/bootstrap-timepicker.min.css')}}" rel="stylesheet" type="text/css" />    
<script type="text/javascript" language="javascript" src="{{url('/')}}/js/front/bootstrap-datepicker.min.js"></script>
<script type="text/javascript" language="javascript" src="{{url('/')}}/js/front/bootstrap-timepicker.js"></script>

<!-- date picker js -->
<script>

  //<!--date and time picker js script-->  
  $(function() {
      $("#future_request_date").datepicker({
          todayHighlight: true,
          autoclose: true,
          startDate: new Date(),
      });
      $('.timepicker-default').timepicker();
  });
</script>
<!-- date picker js -->

@if(isset($arr_card_details) && count($arr_card_details) == 0)
  <script src="https://js.stripe.com/v3/"></script>
@endif

<script type="text/javascript">

    var card_count = "{{ isset($arr_card_details) ? count($arr_card_details) : 0 }}";

    if(card_count == 0)
    {
        var add_card_base_url = "{{url('/user/payment/store')}}";

        // Create a Stripe client.
        var stripe = Stripe('{{$publish_key}}');
        // var stripe = Stripe('pk_test_PZvF4AMC45leL5R9JYIthIxO');

        // Create an instance of Elements.
        var elements = stripe.elements();

        // Custom styling can be passed to options when creating an Element.
        // (Note that this demo uses a wider set of styles than the guide below.)
        var style = {
          base: {
            color: '#32325d',
            lineHeight: '18px',
            fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
            fontSmoothing: 'antialiased',
            fontSize: '16px',
            '::placeholder': {
              color: '#aab7c4'
            }
          },
          invalid: {
            color: '#fa755a',
            iconColor: '#fa755a'
          }
        };

        // Create an instance of the card Element.
        var card = elements.create('card', {style: style});

        // Add an instance of the card Element into the `card-element` <div>.
        card.mount('#card-element');

        // Handle real-time validation errors from the card Element.
        card.addEventListener('change', function(event) {
          var displayError = document.getElementById('card-errors');
          if (event.error) {
            displayError.textContent = event.error.message;
          } else {
            displayError.textContent = '';
          }
        });

        // Handle form submission.
        var form = document.getElementById('payment-form');
        form.addEventListener('submit', function(event) {
            
            event.preventDefault();
          
            $('#btn_add_card').html("<span><i class='fa fa-spinner fa-spin'></i> Processing...</span>");
            stripe.createToken(card).then(function(result) {
                if (result.error) {
                    $('#btn_add_card').html("<span>Add</span>");
                    // Inform the user if there was an error.
                    var errorElement = document.getElementById('card-errors');
                    errorElement.textContent = result.error.message;
                } else {
                    // Send the token to your server.
                    stripeTokenHandler(result.token);
                }
            });
        });

        function stripeTokenHandler(token)
        {    
            if(token!=undefined){

                var obj_data = new Object();

                obj_data._token       = "{{ csrf_token() }}";
                obj_data.stripe_token = token.id;

                $.ajax({
                    url:add_card_base_url,
                    type:'POST',
                    data:obj_data,
                    dataType:'json',
                    beforeSend:function(){
                      $('#add_card_error_div').html('');
                      $('#btn_add_card').html("<span><i class='fa fa-spinner fa-spin'></i> Processing.</span>");
                    },
                    success:function(response) {
                    if(response.status=="success")
                    {
                        if(response.data.card_id!=undefined){
                          $('#btn_add_card').html("<span>Add</span>");
                          
                          var drop_down_html = '<option value="" disabled="">Please select card</option><option value="'+response.data.card_id+'" selected="">'+response.data.masked_card_number+'</option>';
                          $('#card_id').html(drop_down_html).show();
                          $('#btn_open_add_card_modal').attr("disabled",true);
                          $('#btn_open_add_card_modal').hide();
                          // $('#success_card_add').html('Card added successfully.');
                          $('#add-card-popup').modal('toggle');
                        }
                    }
                    else{
                        
                        var html = '';
                        html+='<div class="alert alert-danger">';
                        html+=    '<button type="button" class="close" style="margin-top: 0px !important;padding: 0px !important;" data-dismiss="alert" aria-hidden="true">&times;</button>'+response.msg;
                        html+='</div>';
                        $('#add_card_error_div').html(html);
                        $('#btn_add_card').html("<span>Add</span>");
                    }
                    },error:function(res){
                        $('#btn_add_card').html("<span>Add</span>");
                    }    
                });
            }
        }
    }

    var BASE_URL = "{{url('/')}}";
    var ARR_MAPS_STYLE  = [];
    var STYLE_JSON_FILE = BASE_URL+'/assets/maps_style_2.json';
    
    var CURRENT_STEP = 1;

    function checkCurrentpackageType(ref){
      if($(ref).val() == 'PALLET'){
          $('#weight').attr('data-parsley-required','false');
          $('#length').attr('data-parsley-required','false');
          $('#width').attr('data-parsley-required','false');
          $('#height').attr('data-parsley-required','false');
          $('#package_other_details').hide();
          $('#second_step_div').addClass('pallet_div');
      }
      else{
          $('#weight').attr('data-parsley-required','true');
          $('#length').attr('data-parsley-required','true');
          $('#width').attr('data-parsley-required','true');
          $('#height').attr('data-parsley-required','true');
          $('#package_other_details').show();
          $('#second_step_div').removeClass('pallet_div');

      }
    }

    function isNumberKey(evt) {
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        
        if (charCode == 190 || charCode == 46 ) 
          return true;
        
        if (charCode > 31 && (charCode < 48 || charCode > 57 )) 
        return false;
        
        return true;
    }

    $( document ).ready(function() {
        $.ajax({
            url: STYLE_JSON_FILE,
            async: false,
            dataType: 'json',
            success: function (response) {
               ARR_MAPS_STYLE = response;
            }
        });

        setTimeout(function(){ 
            initSourceAutocomplete();
            initDestinationAutocomplete();
            LoadMap();
        }, 2000);
    });

    var map;
    var bounds;
    var source_marker;
    var destination_marker;

    var directionsService;
    var directionsDisplay;

    function LoadMap() {

        var mapOptions = {
            center: new google.maps.LatLng(0,0),
            zoom: 2,
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            scrollwheel: true,
            disableDefaultUI: true,
            styles : ARR_MAPS_STYLE
        };
        map = new google.maps.Map(document.getElementById("dvMap"), mapOptions);
        bounds = new google.maps.LatLngBounds();
    };

    var source_glob_autocomplete;
    var destination_glob_autocomplete;

    var glob_component_form = 
                {
                    street_number               : 'short_name',
                    route                       : 'long_name',
                    locality                    : 'long_name',
                    administrative_area_level_1 : 'long_name',
                    postal_code                 : 'short_name',
                    country                     : 'long_name',
                    postal_code                 : 'short_name',
                };

    var glob_options   = {};
  

    function initSourceAutocomplete() {
        source_glob_autocomplete = false;
        source_glob_autocomplete = initGoogleAutoComponent($('#pickup_location')[0],glob_options,source_glob_autocomplete,'source');
    }

    function initDestinationAutocomplete() {
        destination_glob_autocomplete = false;
        destination_glob_autocomplete = initGoogleAutoComponent($('#drop_location')[0],glob_options,destination_glob_autocomplete,'destination');
    }
    
    function initGoogleAutoComponent(elem,options,autocomplete_ref,type){
        
        if(type == 'source'){
            autocomplete_ref = new google.maps.places.Autocomplete(elem,options);
            autocomplete_ref = createPlaceChangeListener(autocomplete_ref,fillSourceAddress,'source');
            return autocomplete_ref;
        }

        if(type == 'destination'){
            autocomplete_ref = new google.maps.places.Autocomplete(elem,options);
            autocomplete_ref = createPlaceChangeListener(autocomplete_ref,fillDestinationAddress,'destination');
            return autocomplete_ref;
        }

    }
    
    function createPlaceChangeListener(autocomplete_ref,fillSourceAddress,type){
        if(type == 'source'){
            autocomplete_ref.addListener('place_changed', fillSourceAddress);
            return autocomplete_ref;
        }

        if(type == 'destination'){
            autocomplete_ref.addListener('place_changed', fillDestinationAddress);
            return autocomplete_ref;
        }
    }

    function fillSourceAddress() {
        
        $('#pickup_location').parent().removeClass('red-bor');
        $('#first_step_div').removeClass('first_step_div');
        bounds = false
        bounds = new google.maps.LatLngBounds();
     
        if(directionsDisplay!=undefined){
            directionsDisplay.setMap(null);
        }

        var place = source_glob_autocomplete.getPlace();
        $('#pickup_lat').val(place.geometry.location.lat());
        $('#pickup_lng').val(place.geometry.location.lng());
        SetSourceMarker();
    }

    function fillDestinationAddress() {
        
        $('#drop_location').parent().removeClass('red-bor');
        $('#first_step_div').removeClass('first_step_div');
        bounds = false
        bounds = new google.maps.LatLngBounds();
        
        if(directionsDisplay!=undefined){
            directionsDisplay.setMap(null);
        }

        var place = destination_glob_autocomplete.getPlace();
        $('#drop_lat').val(place.geometry.location.lat());
        $('#drop_lng').val(place.geometry.location.lng());
        SetDestinationMarker();
    }

    function SetSourceMarker() {
      
        if(source_marker!=undefined){
          source_marker.setMap(null);
        }

        var pickup_lat = $('#pickup_lat').val();
        var pickup_lng = $('#pickup_lng').val();

        var marker_icon = BASE_URL+'/node_assets/images/marker.png';
      
        var myLatlng = new google.maps.LatLng(pickup_lat,pickup_lng);

        source_marker = new SlidingMarker({
                                    position      : myLatlng,
                                    map           : map,
                                    icon          : marker_icon
                                });

        bounds.extend(myLatlng);
        map.fitBounds(bounds);
        map.setZoom(16);

        calculateAndDisplayRoute();
    };

    function SetDestinationMarker() {
      
        if(destination_marker!=undefined){
          destination_marker.setMap(null);
        }

        var drop_lat = $('#drop_lat').val();
        var drop_lng = $('#drop_lng').val();

        var marker_icon = BASE_URL+'/node_assets/images/marker.png';
      
        var myLatlng = new google.maps.LatLng(drop_lat,drop_lng);

        destination_marker = new SlidingMarker({
                                    position      : myLatlng,
                                    map           : map,
                                    icon          : marker_icon
                                });

        bounds.extend(myLatlng);
        map.fitBounds(bounds);
        
        calculateAndDisplayRoute();
    };

    function calculateAndDisplayRoute() {

        if(source_marker!=undefined && destination_marker!=undefined)
        {
            directionsService = new google.maps.DirectionsService;

            directionsDisplay = new google.maps.DirectionsRenderer({suppressMarkers: true});
            
            directionsDisplay.setMap(map);

            var pickup_lat = $('#pickup_lat').val();
            var pickup_lng = $('#pickup_lng').val();

            var drop_lat = $('#drop_lat').val();
            var drop_lng = $('#drop_lng').val();

            var origin_lat_lng      = pickup_lat+','+pickup_lng;
            var drop_lat_lng = drop_lat+','+drop_lng;
          
            directionsService.route({
                origin      : origin_lat_lng,
                destination : drop_lat_lng,
                travelMode  : 'DRIVING'
                }, function(response, status) {
                    if (status === 'OK') {
                        directionsDisplay.setDirections(response);
                    } else {
                        // alert('Unable to load route details!');
                        showAlert('Unable to load route details!');
                        //confirmOK();
                        console.log('Directions request failed due to ' + status);
                    }
            });

          $('#first_step_div').addClass('first_step_div');
        }
    }

    function validFirstStepDetails(ref)
    {
        $('#pickup_location').parent().removeClass('red-bor');
        $('#drop_location').parent().removeClass('red-bor');

        var flag = 0;
        if($('#pickup_location').val() == '')
        {
          $('#pickup_location').parent().addClass('red-bor');
          flag = 1;
        }
        if($('#drop_location').val() == '')
        {
          $('#drop_location').parent().addClass('red-bor');
          flag = 1;
        }
        if(flag == 1)
        {
          return false;
        }
        
        var pickup_lat = $('#pickup_lat').val();
        var pickup_lng = $('#pickup_lng').val();
        var drop_lat = $('#drop_lat').val();
        var drop_lng = $('#drop_lng').val();

        if(pickup_lat == ''  || pickup_lng == ''){
          swal('Warning','Please select valid pick up address.','warning');
          return;
        }
        if(drop_lat == ''  || drop_lng == ''){
          swal('Warning','Please select valid drop address.','warning');
          return;
        }

        $('#pickup_drop_div').hide();
        $(ref).hide();
        $( "#first_step_div" ).removeClass( "first_step_div");
        $("#second_step_div").show();

        CURRENT_STEP = 2;
        if(CURRENT_STEP>=2){
          $('#btn_back_div').show();
          $('#btn_next_div').show();
        }
    }
    
    function loadPreviousView(){

        if(CURRENT_STEP == 2){
          $( "#first_step_div" ).removeClass("first_step_div");
          $('#pickup_drop_div').show();
          $('#btn_first_step_div_next').show();
          $("#second_step_div").hide();
          $("#first_step_div" ).addClass("first_step_div");

          CURRENT_STEP = 1;
          $('#btn_back_div').hide();
          $('#btn_next_div').hide();
        }
        else if(CURRENT_STEP == 3){
          $('#action_type').val('search');
          $('#vehicle_type_div').html('');
          $('#div_estimate_time').html('').hide();
          $('#div_estimate_cost').html('').hide();
          $("#third_step_div").hide();
          $("#second_step_div").show();
          is_package_details_modal_show = false;
          CURRENT_STEP = 2;
        }
    }

    var USER_PANEL_URL = "{{url('/').'/'.$user_path}}";
    var LOAD_POST_URL = "{{url('/').'/'.$user_path.'/store_load_post_request'}}";
    
    var current_selected_vehicle_type_id = 0;
    var current_selected_drivers_count = 0;

    function confirmClearDetails(){

      swal({
              title: "Are you sure ?",
              text: 'You want to cancel,this trip.',
              type: "warning",
              showCancelButton: true,
              confirmButtonColor: "#DD6B55",
              confirmButtonText: "Yes",
              cancelButtonText: "No",
              closeOnConfirm: false,
              closeOnCancel: true
            },
            function(isConfirm)
            {
              if(isConfirm==true)
              {
                  window.location.href = USER_PANEL_URL+'/delivery_request';
              }
            });
    }
    
    function openAddCardModal(ref){
      $('#add-card-popup').modal('toggle');
    }
    function closePackageDetailsModal(ref){
      is_package_details_modal_show = false;
      $('#package-details-popup').modal('toggle');
    }
    
    var is_package_details_modal_show = false;

    function validFormDetails(ref){
        
        if($('#is_bonus_checkbox').prop("checked"))
        {
            $('#is_bonus').val('NO');
        }

        var is_valid_frm  = $('#frm_packaging_details').parsley().validate();
        if(is_valid_frm == false)
        {
            return false;
        }
        
        if(card_count == 0)
        {
          if($('#card_id').val() == null || $('#card_id').val() == ''){
            // swal('Error!',"Please add a card.",'error');
            $('#add-card-popup').modal('toggle');
            return false;   
          }
        }

        if($('#is_flammables_checked').prop("checked"))
        {
            swal('Error!',"Sorry we do not ship chemicals or flammables at this moment.!",'error');
            return;
        }
        
        // console.log($("#package-details-popup").data('bs.modal').isShown);

        if(CURRENT_STEP == 2 && is_package_details_modal_show == false)
        {
          $('#modal_package_type').html('');
          $('#modal_weight').html('');
          $('#modal_length').html('');
          $('#modal_width').html('');
          $('#modal_height').html('');
          $('#modal_quantity').html('');
          $('#modal_selected_card').html('');

          $('#modal_other_package_details').hide();
          if($('#package_type').val() != 'PALLET')
          {
            $('#modal_other_package_details').show();
          }
          
          $('#modal_package_type').html($('#package_type').val());
          $('#modal_weight').html($('#weight').val());
          $('#modal_length').html($('#length').val());
          $('#modal_width').html($('#width').val());
          $('#modal_height').html($('#height').val());
          $('#modal_quantity').html($('#quantity').val());
          $('#modal_selected_card').html($("#card_id option:selected").text());

          $('#package-details-popup').modal('toggle');
          is_package_details_modal_show = true;
          return;
        }
        else if(CURRENT_STEP == 2 && is_package_details_modal_show == true)
        {
          $('#package-details-popup').modal('toggle');
        }

        if(CURRENT_STEP == 3)
        {
          if($('input[type=radio][name=is_future_request]:checked').val() == '1'){

              var flag = 0;
              if($('#future_request_date').val() == '')
              {
                $('#error_future_request_date').html('Please enter future booking date.');
                flag = 1;
              }
              if($('#request_time').val() == '')
              {
                $('#error_request_time').html('Please enter future booking time.');
                flag = 1;
              }
              if(flag == 1)
              {
                return false;
              }
          }
        }

        $.ajax({
            url:LOAD_POST_URL,
            type:'POST',
            data : $('#frm_packaging_details').serialize(),
            beforeSend:function(){
              // $(ref).prop('disabled', true);
              // $(ref).html("<i class='fa fa-spinner fa-spin'></i>");
              showProcessingOverlay();
            },
            success:function(response)
            {
                hideProcessingOverlay();
                if(response.status=="success")
                { 
                    /*when current step is 2 then following function will do work*/
                    if(CURRENT_STEP == 2){
                        showStepTwoDetails(response);
                        return;
                    } 
                    else if(CURRENT_STEP == 3){
                      /*redirect user to next scrren to search and select drivers*/
                      var redirect_url = USER_PANEL_URL+'/my_booking?trip_type=PENDING';
                      window.location.href = redirect_url;
                      return;
                    }
                }
                if(response.status=="error")
                {
                    var error_html = '';
                    error_html += '<div class="alert alert-danger">';
                    error_html +=   '<button type="button" class="close" style="margin-top: 0px !important;padding: 0px !important;" data-dismiss="alert" aria-hidden="true">×</button>';
                    error_html +=      ''+response.msg+'';
                    error_html +=  '</div>';
                    $('#show_load_post_error_div').html(error_html);
                }
                return false;
            },
            error:function(response){
              hideProcessingOverlay();
            }
        });
    }

    function showStepTwoDetails(response){

        var pickup_location = $('#pickup_location').val();
        var drop_location   = $('#drop_location').val();

        $('#second_step_div').hide();

        $('#span_pickup_loacation').html('<span>Pickup Loacation :</span>'+pickup_location);
        $('#span_drop_loacation').html('<span> Drop Loacation:</span>'+drop_location);
        
        $('#action_type').val('book');

        $('#vehicle_type_div').html('');
        $('#show_load_post_error_div').html('');

        var vehicle_type_div_html  = '';
        var div_estimate_time_html = '';
        var div_estimate_cost_html = '';
        
        if(response.data.arr_driver_vehicle_type.length>0){
            var arr_driver_vehicle_type = response.data.arr_driver_vehicle_type;
            // console.log(arr_driver_vehicle_type);
            arr_driver_vehicle_type.forEach(function (value, index) {
              
              var active_class = '';
              var driver_count = value.driver_count;
              if(driver_count!=undefined && driver_count>0)
              {
                if(index == 0){
                    active_class = 'active';
                    current_selected_vehicle_type_id = value.id;
                    $('#vehicle_type_id').val(current_selected_vehicle_type_id);
                    current_selected_drivers_count   = driver_count;

                    div_estimate_time_html = '<h2>Estimate Time</h2><span>'+value.trip_duration+'</span>';
                    div_estimate_cost_html = '<h2>Estimate Cost</h2><span>$ '+value.calculated_farecharge+'</span>';
                }
                var vehicle_type_slug = 'sedan';
                if(value.vehicle_type_slug!=undefined) {
                  vehicle_type_slug = value.vehicle_type_slug;
                }
                
                var vehicle_type_id = value.id;

                var default_image = BASE_URL+'/images/sedan-img.png';
                var default_white_image = BASE_URL+'/images/sedan-img-white.png';
                
                switch (vehicle_type_slug) {

                  case "sedan":
                    default_image = BASE_URL+'/images/sedan-img.png';
                    default_white_image = BASE_URL+'/images/sedan-img-white.png';
                    break;
                
                  case "suv":
                    default_image = BASE_URL+'/images/suv-img.png';
                    default_white_image = BASE_URL+'/images/suv-img-white.png';
                    break;
                
                  case "pickup-truck":
                    default_image = BASE_URL+'/images/pickup-truck-img.png';
                    default_white_image = BASE_URL+'/images/pickup-truck-img-white.png';
                    break;
                
                  case "cargo-van":
                    default_image = BASE_URL+'/images/cargo-van-img.png';
                    default_white_image = BASE_URL+'/images/cargo-van-img-white.png';
                    break;
                
                  case "10-truck":
                    default_image = BASE_URL+'/images/10-truck-img.png';
                    default_white_image = BASE_URL+'/images/10-truck-img-white.png';
                    break;
                
                  case "26-truck":
                    default_image = BASE_URL+'/images/26-truck-img.png';
                    default_white_image = BASE_URL+'/images/26-truck-img-white.png';
                    break;
                
                  default:
                    default_image = BASE_URL+'/images/sedan-img.png';
                    default_white_image = BASE_URL+'/images/sedan-img-white.png';
                }

                vehicle_type_div_html += '<div class="delivery-reques-bottom-img-block '+active_class+' all_vehicle_type" ';
                vehicle_type_div_html += '         data-attr-id="'+vehicle_type_id+'" ';
                vehicle_type_div_html += '         data-attr-driver-count="'+driver_count+'" ';
                vehicle_type_div_html += '         data-attr-estimate-time="'+value.trip_duration+'" ';
                vehicle_type_div_html += '         data-attr-estimate-cost="'+value.calculated_farecharge+'" ';
                vehicle_type_div_html += '         onclick="visibleMarkerByVehicleType(this)"';
                vehicle_type_div_html += '         >';
                vehicle_type_div_html += '   <div class="delivery-img-circle">';
                vehicle_type_div_html += '        <img src="'+default_image+'" class="delivery-img-gry" />';
                vehicle_type_div_html += '        <img src="'+default_white_image+'" class="delivery-img-white" />';
                vehicle_type_div_html += '    </div>';
                vehicle_type_div_html += '   <div class="delivery-img-sub">'+value.vehicle_type+'</div>';
                vehicle_type_div_html += '</div>';
              }

            });
            
            showAllDriverMarkers(arr_driver_vehicle_type)
        } 

        if(vehicle_type_div_html!=''){
          $('#vehicle_type_div').html(vehicle_type_div_html);
          $('#div_estimate_time').html(div_estimate_time_html).show();
          $('#div_estimate_cost').html(div_estimate_cost_html).show();
          $('#third_step_div').show();

          CURRENT_STEP = 3;
        }
        else
        {
          var error_html = '';
          error_html += '<div class="alert alert-danger">';
          error_html +=   '<button type="button" class="close" style="margin-top: 0px !important;padding: 0px !important;" data-dismiss="alert" aria-hidden="true">×</button>';
          error_html +=      'Sorry for the inconvenience,currently drivers are not available.';
          error_html +=  '</div>';
          $('#show_load_post_error_div').html(error_html);
          $('#second_step_div').show();
          $('#action_type').val('search');
        }
    }

    $('input[type=radio][name=is_future_request]').change(function() {
        if(this.value == '0'){
          if($('#div_future_booking_details:visible').length == 1){
            $('#div_future_booking_details').slideUp();
            setTimeout(function(){
              $('#third_step_div').removeClass('future-booking');
            },300)
            return;
          }
        }
        else if(this.value == '1'){
          $('#error_future_request_date').html('');
          $('#error_request_time').html('');
          
          $('#third_step_div').addClass('future-booking');

          if($('#div_future_booking_details:visible').length == 0){
            $('#div_future_booking_details').slideDown();
            return;
          }
        }
    });

    var map_markers = [];

    function showAllDriverMarkers(arr_driver_vehicle_type){
      
      if(arr_driver_vehicle_type!=undefined && arr_driver_vehicle_type.length>0){

        arr_driver_vehicle_type.forEach(function (vt_value,vt_index) {
        
          if (vt_value.arr_driver_details!= undefined && vt_value.arr_driver_details.length>0) {
        
              vt_value.arr_driver_details.forEach(function (d_value, d_index) {
                  
                  // var marker_icon = BASE_URL+'/node_assets/images/blue.png';

                  var vehicle_type_slug = '';
                  if(vt_value.vehicle_type_slug!=undefined && vt_value.vehicle_type_slug!=''){
                      vehicle_type_slug = vt_value.vehicle_type_slug
                  }

                  var marker_icon = BASE_URL+'/node_assets/images/vehicle_type_images/sedan.png';

                  switch (vehicle_type_slug) { 
                      case 'sedan':
                          marker_icon = BASE_URL+'/node_assets/images/vehicle_type_images/sedan.png';
                          break;
                      case 'suv':
                          marker_icon = BASE_URL+'/node_assets/images/vehicle_type_images/suv.png';
                          break;
                      case 'pickup-truck':
                          marker_icon = BASE_URL+'/node_assets/images/vehicle_type_images/pickup-truck.png';
                          break;
                      case 'cargo-van':
                          marker_icon = BASE_URL+'/node_assets/images/vehicle_type_images/cargo-van.png';
                          break;
                      case '10-truck':
                          marker_icon = BASE_URL+'/node_assets/images/vehicle_type_images/10-truck.png';
                          break;
                      case '26-truck':
                          marker_icon = BASE_URL+'/node_assets/images/vehicle_type_images/26-truck.png';
                          break;
                      default:
                          marker_icon = BASE_URL+'/node_assets/images/vehicle_type_images/sedan.png';
                  }
                  
                  var markerImage = new google.maps.MarkerImage(
                                marker_icon,
                                new google.maps.Size(14,30), //size
                                null, //origin
                                null, //anchor
                                new google.maps.Size(14,30) //scale
                            );

                  var myLatlng = new google.maps.LatLng(d_value.current_latitude,d_value.current_longitude);

                  var marker = new SlidingMarker({
                                              position       : myLatlng,
                                              map            : map,
                                              icon           : markerImage,
                                              vehicle_type_id: vt_value.id,
                                              driver_id      : d_value.driver_id
                                          });

                  bounds.extend(myLatlng);
                  map.fitBounds(bounds);
                  map_markers.push(marker);
                  if(current_selected_vehicle_type_id == vt_value.id){
                    marker.setVisible(true);
                  }
                  else{
                    marker.setVisible(false);
                  }
              });
          }

        })
      }
    }

    function visibleMarkerByVehicleType(ref){
        var vehicle_type_id = $(ref).attr('data-attr-id');
        var drivers_count   = $(ref).attr('data-attr-driver-count');
        
        var estimate_time   = $(ref).attr('data-attr-estimate-time');
        var estimate_cost   = $(ref).attr('data-attr-estimate-cost');

        if(estimate_time!=undefined && estimate_cost!=undefined){
          $('#div_estimate_time').html('<h2>Estimate Time</h2><span>'+estimate_time+'</span>');
          $('#div_estimate_cost').html('<h2>Estimate Cost</h2><span>$ '+estimate_cost+'</span>');
        }
        if((vehicle_type_id!=undefined) && (drivers_count!=undefined) && (current_selected_vehicle_type_id!=undefined)){
          if(vehicle_type_id!=current_selected_vehicle_type_id){
              $( ".all_vehicle_type" ).each(function( index ) {
                  $( this ).removeClass('active');
              });
              $(ref).addClass('active');
              current_selected_vehicle_type_id = vehicle_type_id;
              $('#vehicle_type_id').val(vehicle_type_id);

              current_selected_drivers_count   = drivers_count;
              if (map_markers.length > 0) {
                  map_markers.forEach(function (value, index) {
                      var map_visible = false;
                      if(value.vehicle_type_id == current_selected_vehicle_type_id){   
                        map_visible = true;
                      }
                      value.setVisible(map_visible);
                  });
              }

          }
        }
    }

    var PROMO_CODE_URL = "{{url('/api/common_data/apply_promo_code')}}";
    var enc_user_login_id = "{{ isset($enc_user_login_id) ? $enc_user_login_id : '0' }}";
    
    function applyPromoCode(ref){
      $('#promo_code_id').val(0);
      $('#err_promo_code').html('');
      $('#success_promo_code').html('');
      if($('#promo_code').val()==''){
          $('#err_promo_code').html('Please enter promo code');
      }
      if($('#promo_code').val()!=''){
        var promo_code = $('#promo_code').val();
        $.ajax({
            url:PROMO_CODE_URL+'?promo_code='+promo_code+'&request_type=WEB&enc_id='+enc_user_login_id,
            type:'GET',
            beforeSend:function(){
              $(ref).prop('disabled', true);
              $(ref).html("<i class='fa fa-spinner fa-spin'></i>");
            },
            success:function(response)
            {
                if(response.status=="success")
                { 
                    if(response.data.id!=undefined){
                      $('#promo_code_id').val(response.data.id);
                    }
                    $('#success_promo_code').html(response.msg);
                    $(ref).prop('disabled', false);
                    $(ref).html("Apply!");
                }
                else
                {
                    $('#err_promo_code').html(response.msg);
                    $(ref).prop('disabled', false);
                    $(ref).html("Apply!");
                }
                return false;
            }
        });
      }
    }
</script>
@stop