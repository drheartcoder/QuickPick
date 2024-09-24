
/*admin_module_url_path is defined in the footer*/
function loadStates(ref) {
    var selected_country = $(ref).val();
    
    if(selected_country && selected_country!="" && selected_country!=0){

	 	$('select[id="state"]').find('option').remove().end().append('<option value="">--Select State--</option>').val('');
     	$('select[id="city"]').find('option').remove().end().append('<option value="">--Select City--</option>').val('');

        $.ajax({
              	url:locations_url_path+'/get_states?country_id='+btoa(selected_country),
              	type:'GET',
              	data:'flag=true',
              	dataType:'json',
              	beforeSend:function()
              	{
                  $('select[id="state"]').attr('readonly','readonly');
              	},
              	success:function(response)
              	{
                  	if(response.status=="success")
                  	{
                      	$('select[id="state"]').removeAttr('readonly');

                      	if(typeof(response.arr_state) == "object")
                      	{
                         	var option = '<option value="">--Select State--</option>'; 
                         	$(response.arr_state).each(function(index,state){
                              option+='<option value="'+state.id+'">'+state.state_name+'</option>';
                         	});

                         	$('select[id="state"]').html(option);
                      	}
                  	}
                  return false;
              	}	  
        });
     }
}

function loadCities(ref) {

    var selected_state = $(ref).val();

    if(selected_state && selected_state!="" && selected_state!=0)
    {

     $('select[id="city"]').find('option').remove().end().append('<option value="">--Select City--</option>').val('');

          $.ajax({
              url:locations_url_path+'/get_cities?state_id='+btoa(selected_state),
              type:'GET',
              data:'flag=true',
              dataType:'json',
             beforeSend:function()
              {
                  $('select[id="city"]').attr('readonly','readonly');
              },
              success:function(response)
              {
                  if(response.status=="success")
                  {
                      $('select[id="city"]').removeAttr('readonly');

                      if(typeof(response.arr_cities) == "object")
                      {
                         var option = '<option value="">--Select City--</option>'; 
                         $(response.arr_cities).each(function(index,city)
                         {
                              option+='<option value="'+city.id+'">'+city.city_name+'</option>';
                         });

                         $('select[id="city"]').html(option);
                      }

                  }
                  return false;
              }    
        });
     }
}


