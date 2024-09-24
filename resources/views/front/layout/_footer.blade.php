  
    
  @include('front.layout.footer')    
    
    
    
    
    <!-- Review slider -->
    <script type="text/javascript" src="{{url('js/front/swiper.min.js')}}"></script>
    <script type="text/javascript" src="{{ url('/') }}/assets/jquery-validation/dist/jquery.validate.min.js"></script>
    <!-- Initialize Swiper -->
    <script>
    var swiper = new Swiper('.swiper-container', {
      slidesPerView: 3,
      spaceBetween: 20,
        centeredSlides: true,
      // init: false,
      pagination: {
        el: '.swiper-pagination',
        clickable: true,
      },
        autoplay: {
        delay: 3000,
        },
      breakpoints: {
        1024: {
          slidesPerView: 2,
          spaceBetween: 20,
        },
        768: {
          slidesPerView: 2,
          spaceBetween: 20,
        },
        640: {
          slidesPerView: 1,
          spaceBetween: 10,
        },
        320: {
          slidesPerView: 1,
          spaceBetween: 10,
        }
      }
    });
  </script>
    <!-- Review slider End -->

<script type="text/javascript">
  if (jQuery().validate) {
        var removeSuccessClass = function(e) {
            $(e).closest('.form-group').removeClass('has-success');
        }
        var $validator = $('#validation-form').validate({
            errorElement: 'span', //default input error message container
            errorClass: 'help-block', // default input error message class
            errorPlacement: function(error, element) {
                if(element.parent('.input-group').length) {
                    error.insertAfter(element.parent());
                } else if (element.next('.chosen-container').length) {
                    error.insertAfter(element.next('.chosen-container'));
                } else {
                    error.insertAfter(element);
                }
            },
            focusInvalid: false, // do not focus the last invalid input
            ignore: "",

            invalidHandler: function (event, validator) { //display error alert on form submit              
                var el = $(validator.errorList[0].element);
                if ($(el).hasClass('chosen')) {
                    $(el).trigger('chosen:activate');
                } else {
                    $(el).focus();
                }
            },

            highlight: function (element) { // hightlight error inputs
                $(element).closest('.form-group').removeClass('has-success').addClass('has-error'); // set error class to the control group
            },

            unhighlight: function (element) { // revert the change dony by hightlight
                $(element).closest('.form-group').removeClass('has-error'); // set error class to the control group
                setTimeout(function(){removeSuccessClass(element);}, 3000);
            },

            success: function (label) {
                label.closest('.form-group').removeClass('has-error').addClass('has-success'); // set success class to the control group
            }
        });
    }
</script>
{{-- @if(isset($arr_login_user_details['user_type']) && ($arr_login_user_details['user_type'] == 'user' || $arr_login_user_details['user_type'] == 'driver'))
@endif --}}

  {{-- @include('common.user_and_driver_applozic_chat') --}}
</body>

</html>