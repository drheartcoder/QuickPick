<?php $user_path     = config('app.project.role_slug.user_role_slug'); ?>
 @extends('front.layout.master')                

    @section('main_content')

    <div class="blank-div"></div>
     <div class="email-block">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6 col-md-6 col-lg-6">
                    <div class="headding-text-bredcr">
                       Add Card
                    </div>
                </div>
                <div class="col-sm-6 col-md-6 col-lg-6">
                    <div class="bredcrum-right">
                        <a href="{{ $module_url_path }}" class="bredcrum-home"> Dashboard </a>
                        <span class="arrow-righ"><i class="fa fa-angle-right"></i></span>
                        Add Card
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--dashboard page start-->
    <div class="main-wrapper">
        <div class="container-fluid">
            <div class="row">
                @include('front.user.left_bar')
                <div class="middle-bar">
                    <div class="form-wrapper">
                        <div class="row">
                            
                            <script src="https://js.stripe.com/v3/"></script>
                            
                            <div id="error_div"></div>

                            <form id="payment-form">
                              
                              <div class="form-row">
                                <label for="card-element">
                                  Add Credit or Debit card
                                </label>
                                <div class="edit-posted-bg-main">
                                    <div id="card-element" class="change-strip-eleme">
                                      <!-- A Stripe Element will be inserted here. -->
                                    </div>
                                    <div class="error" id="card-errors" role="alert"></div>
                                </div>

                                <!-- Used to display form errors. -->
                                
                              </div>

                              <br>
                              <div class="btns-wrapper change-pass">
                                    <button type="submit" id="btn_add_card" class="green-btn chan-right">Add</button>
                                </div>
                            </form>

                            {{-- <form name="frm-edit-profile" method="post" action="{{url( config('app.project.role_slug.user_role_slug'))}}/payment/store" data-parsley-validate enctype="multipart/form-data">
                                    {{ csrf_field() }}
                                   <div class="col-sm-6 col-md-6 col-lg-6">
                                        <div class="form-group">
                                            <input type="text" name="card_number" data-parsley-required-message="Please enter card number" data-parsley-required="true" data-parsley-pattern="^[0-9]*$" data-parsley-pattern-message="Please enter number" data-parsley-minlength="16" data-parsley-maxlength="16" data-parsley-minlength-message="Please enter valid card number"
                                            data-parsley-maxlength-message="Please enter valid card number" data-parsley-errors-container="#err_card_number"> 
                                            <label>Card Number</label>
                                            <div class="error" id="err_card_number"></div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-md-6 col-lg-6">
                                        <div class="form-group">
                                            <input type="text" name="cvv_no" data-parsley-required-message="Please enter CVV number" data-parsley-required="true" data-parsley-pattern="^[0-9]*$" data-parsley-pattern-message="Please enter number" data-parsley-maxlength="3" data-parsley-maxlength-message="Please enter valid card number"
                                            data-parsley-minlength="3" data-parsley-minlength-message="Please enter valid card number" data-parsley-errors-container="#err_cvv_no"> 
                                            <label>CVV Number</label>
                                            <div class="error" id="err_cvv_no"></div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-md-6 col-lg-6">
                                            <div class="form-group date">
                                            <div class="form-date-icon"><a href="javascript:void(0)"><i class="fa fa-calendar"></i></a> </div>
                                            <input id="date-second" type="text" name="expiry_date" placeholder="Expiry Date" data-parsley-required-message="Please enter expiry date" data-parsley-required="true" data-parsley-errors-container="#err_expiry_date">
                                            <div class="error" id="err_expiry_date"></div>
                                            </div>
                                        </div>
                                    <div class="col-sm-6 col-md-6 col-lg-6">
                                        <div class="form-group">
                                            <input type="text" name="card_name" data-parsley-required-message="Please enter card name" data-parsley-required="true" data-parsley-maxlength="100" data-parsley-maxlength-message="Please enter short name" data-parsley-errors-container="#err_card_name"> 
                                            <label>Name of Card</label>
                                            <div class="error" id="err_card_name"></div>
                                        </div>
                                    </div>
                                    <div class="btns-wrapper change-pass">
                                        <button type="submit" class="green-btn chan-right">Add</button>
                                    </div>
                                </form> --}}
                            
                        </div>
                    </div>
                </div>
                @include('front.user.right_bar')
            </div>
        </div>
    </div>

<script type="text/javascript">

var base_url = "{{url('/user/payment/store')}}";
var curr_module_url = "{{url('/user/payment')}}";

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
            url:base_url,
            type:'POST',
            data:obj_data,
            dataType:'json',
            beforeSend:function(){
              $('#error_div').html('');
              $('#btn_add_card').html("<span><i class='fa fa-spinner fa-spin'></i> Processing.</span>");
            },
            success:function(response) {
            if(response.status=="success")
            {
                // $('#btn_add_card').html("<span>Add</span>");
                window.location.href = curr_module_url;
            }
            else{
                
                var html = '';

                html+='<div class="alert alert-danger">';
                html+=    '<button type="button" class="close" style="margin-top: 0px !important;padding: 0px !important;" data-dismiss="alert" aria-hidden="true">&times;</button>'+response.msg;
                html+='</div>';
                $('#error_div').html(html);
                $('#btn_add_card').html("<span>Add</span>");
            }
            },error:function(res){
                $('#btn_add_card').html("<span>Add</span>");
            }    
        });
    }
}

</script>

@stop