

if (jQuery().validate) 
{
    var removeSuccessClass = function(e) 
    {
        $(e).closest('.form-group').removeClass('has-success');
    }

    function applyValidationToFrom(frm_ref)
    {
        $(frm_ref).validate({
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
}

/********** Newsletter Add Validation ***********/

$("#create_news_letter") .click(function()
{
    var news_title       = $("#news_title").val();
    var news_subject     = $("#news_subject").val();
    var news_description = tinyMCE.get('news_description').getContent();
    //var news_description=CKEDITOR.instances['news_description'].getData().replace(/<[^>]*>/gi, '').length;
    var flag = 1;
   
    $("#err_news_title").html('');
    $("#err_news_subject").html('');
    $("#err_news_description").html('');

    
    if($.trim(news_title)=='')
    {
      $("#err_news_title").html('Please enter title');
      flag = 0;
    }

    if($.trim(news_subject)=='')
    {
      $("#err_news_subject").html('Please enter subject');
      flag = 0;
    }

    if(news_description == '')
    {
      $("#err_news_description").html('Please enter description');
      flag = 0;
    }
    
    if(flag == 1)
    {
      return true;
    }
    else
    {
      return false;
    }
});


/********** Newsletter Update Validation ***********/

 $("#update_news_letter") .click(function()
 {

    var news_title       = $("#news_title").val();
    var news_subject     = $("#news_subject").val();   
    var news_description = tinyMCE.get('news_description').getContent();
    //var news_description=CKEDITOR.instances['news_description'].getData().replace(/<[^>]*>/gi, '').length;
    var flag = 1;

    $("#err_news_title").html('');
    $("#err_news_subject").html('');
    $("#err_news_description").html('');

    
    if($.trim(news_title)=='')
    {
      $("#err_news_title").html('Please enter title');
      flag = 0;
    }

    if($.trim(news_subject)=='')
    {
      $("#err_news_subject").html('Please enter subject');
      flag = 0;
    }

    if(news_description == '')
    {
      $("#err_news_description").html('Please enter description');
      flag = 0;
    }
    
    if(flag == 1)
    {
      return true;
    }
    else
    {
      return false;
    }
});

/****************** Multi selrt ********************/
 $('#selectall').click(function(event) {  /*on click */
      if(this.checked) { /* check select status*/
          $('.case').each(function() { /*loop through each checkbox*/
              this.checked = true;  /*select all checkboxes with class "case"*/               
          });
      }else{
          $('.case').each(function() { /*loop through each checkbox*/
              this.checked = false; /*deselect all checkboxes with class "case"*/                       
          });         
      }
  });