
	@extends('admin.layout.master')                
	@section('main_content')
	<!-- BEGIN Page Title -->
	<link rel="stylesheet" type="text/css" href="{{ url('/') }}/assets/data-tables/latest/dataTables.bootstrap.min.css">
	<div class="page-title">
	</div>
	<!-- END Page Title -->

	<!-- BEGIN Breadcrumb -->
	<div id="breadcrumbs">
		<ul class="breadcrumb">
			<li>
				<i class="fa fa-home"></i>
				<a href="{{ url($admin_panel_slug.'/dashboard') }}">Dashboard</a>
			</li>
			<span class="divider">
				<i class="fa fa-angle-right"></i>
				<i class="fa fa-map"></i>                
			</span> 
			<li class="active"><a href="{{ url($module_url_path) }}">{{$module_title}}</a></li>
			<span class="divider">
				<i class="fa fa-angle-right"></i>
				<i class="fa fa-plus-square-o"></i>                
			</span> 
			<li class="active">Create</li>
		</ul>
	</div>
	<!-- END Breadcrumb -->
	<!-- BEGIN Main Content -->
	<div class="row">
		<div class="col-md-12">
			<div class="box {{ $theme_color }}">
				<div class="box-title">
					<h3>
						<i class="fa fa-list"></i>
						{{ isset($page_title)?$page_title:"" }}
					</h3>

					<div class="box-tool">
						<a data-action="collapse" href="#"></a>
						<a data-action="close" href="#"></a>
					</div>
				</div>

				<div class="box-content">


					<!-- <div class="button-sctions">
						<ul>
							<li><button role="button" class="btn btn-default setclass active" onclick="window.location.href='{{$module_url_path."/create"}}'">New</button></li>
							<li><button role="button" class="btn btn-default setclass" onclick="window.location.href='{{$module_url_path."/create_existing"}}'">Existing</button></li>
						</ul>
						<div class="clearfix"></div>
					</div> -->

					@include('admin.layout._operation_status')  

					{{--  <div class="alert alert-danger" style="display:none">
						<button class="close" data-dismiss="alert">×</button>
						<strong id="existence_error" ></strong>
					</div> 
					<div class="alert alert-success" style="display:none">
						<button class="close" data-dismiss="alert">×</button>
						<strong id="alert-success" ></strong>
					</div> 
					@include('admin.layout._operation_status')  --}}


					<div class="col-md-10">
						<div id="ajax_op_status"></div>
					</div>
					<div class="clearfix"></div>
					<div class="row">
						<div class="col-md-8">
							<div id="user_location_map" style="height:800px "></div>
							<div class="map"  >
							</div>
						</div>
						<div class="col-md-4">
							<!-- <div class="map-right-block second create-cals"> -->
								<div class="map-rig-inner-top"><h1>Assigned Zones </h1> </div>
								<div class="country-main" style="margin-top:30px">
									<div class="row"> 
									</div>
								</div>

								<div class="country-main" >
									<div class="row"> 
										<div class="col-md-4"> 
											<label class="control-label">Search Location</label>
										</div>
										<div class="col-md-8"> 
											<div class="form-group" >
												<div class="controls" >
													<input type="text" class="form-control" name="search_location" id="search_location" placeholder="Search Location"  />
												</div>
											</div>
										</div>
									</div>
								</div>

								<div class="country-main" >
									<div class="row"> 
										<div class="col-md-4"> 
											<label class="control-label">Zone name<i style="color: red;">*</i></label>
										</div>
										<div class="col-md-8"> 
											<div class="form-group" >
												<div class="controls" >
													<input type="text" class="form-control" name="zone_name" id="zone_name" placeholder="Zone Name"  />
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="clerfix"></div>
								<div class="cancel-button-block"><a class="cancel-button pull-right" href="javascript:void(0)" onclick="saveArea()">Save</a> </div>
								<div class="clerfix"></div> 
							<!-- </div> -->
						</div> 
					</div>
					</div>
					<br/>

<div class="clearfix"></div>

{{--  <div id="user_location_map" style="height:800px "></div> --}}

<input type="hidden" name="lat" value="" id="rest_lat" />
<input type="hidden" name="lon" value="" id="rest_long"/> 
<input type="hidden" name="stored_lat" value="19.997454" id="stored_lat">
<input type="hidden" name="stored_long" value="73.789803" id="stored_long">

<div class="right_sidebar">
	<a id="btn-scrollup" class="btn btn-circle btn-lg" href="#"><i class="fa fa-chevron-up"></i></a>    
	<div id="wrapper">
	</div>
</div>
</div>
</div>


<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key={{ isset($google_map_api_key) ? $google_map_api_key : 'AIzaSyCccvQtzVx4aAt05YnfzJDSWEzPiVnNVsY' }}&libraries=places,geometry,drawing,visualization"></script>
<script>
	
	var glob_autocomplete;

	var glob_component_form = 
	                {
	                    street_number: 'short_name',
	                    route: 'long_name',
	                    locality: 'long_name',
	                    administrative_area_level_1: 'long_name',
	                    postal_code: 'short_name',
	                    country : 'long_name',
	                    postal_code : 'short_name',
	                };

	var glob_options   = {};
	  //glob_options.types = ['address'];

  	function initAutocomplete() {
      	glob_autocomplete = false;
      	glob_autocomplete = initGoogleAutoComponent($('#search_location')[0],glob_options,glob_autocomplete);
  	}


  	function initGoogleAutoComponent(elem,options,autocomplete_ref){
    	autocomplete_ref = new google.maps.places.Autocomplete(elem,options);
    	autocomplete_ref = createPlaceChangeListener(autocomplete_ref,fillInAddress);
    	return autocomplete_ref;
  	}

  	function createPlaceChangeListener(autocomplete_ref,fillInAddress){
    	autocomplete_ref.addListener('place_changed', fillInAddress);
    	return autocomplete_ref;
  	}

	function fillInAddress() {

		var place = glob_autocomplete.getPlace();
    	
    	$('#rest_lat').val(place.geometry.location.lat());
      	$('#rest_long').val(place.geometry.location.lng());
      
      	SetMarker()
  	}

  	function SetMarker(data) {
      
      	if(marker!=undefined){
          marker.setMap(null);
      	}
      
      	var rest_lat = $('#rest_lat').val();
      	var rest_long = $('#rest_long').val();

      	var myLatlng = new google.maps.LatLng(rest_lat,rest_long);

      	var bounds = new google.maps.LatLngBounds();

      	marker = new google.maps.Marker({
		  	position  : myLatlng,
		  	map       : map,
		  	scrollwheel: true,
		  	draggable : false

		});
	    
	    bounds.extend(myLatlng);
    	map.fitBounds(bounds);
    	map.setZoom(10);

    };

	var module_url_path = "{{$module_url_path}}";
	var country = false;
	var city = false;

	var map;
	var geocoder;
	var marker;
	var infowindow;
	var drawingManager;

	var locality_lat = false;
	var locality_lon = false;

	var stored_lat  = $('#stored_lat').val();
	var stored_long = $('#stored_long').val();

	var latlng      = new google.maps.LatLng(0, 0);

   var co_ordinates; // co_ordinates of circle in miles. 
   var site_url       = '{{ url('/') }}';
   var poly           = '';
   var poly_cords     = [];
   var poly_cords_str = '';
   var stored_area    = [];
   var polygon        = '';
   var polygon_map    = '';
   var color_ary      = [];

   function initMap() 
   { 
	   // Create the map.
	   map = new google.maps.Map(document.getElementById('user_location_map'), {
	   		zoom: 2,
	   		center: latlng,
	   		scrollwheel: true,
	   		disableDefaultUI: true,
	   		/*mapTypeId: google.maps.MapTypeId.HYBRID*/
	   	});
	   
		//set marker
		geocoder = new google.maps.Geocoder();
		
		// marker = new google.maps.Marker({
		//   	position  : latlng,
		//   	map       : map,
		//   	scrollwheel: true,
		//   	draggable : false

		// });
		  
		// Construct the polygon 
		poly = new google.maps.Polygon({               
			draggable     : true,
			editable      : true,
			strokeColor   : '#A3a3a3',
			strokeOpacity : 0.8,
			strokeWeight  : 2,
			fillColor     : '#A3a3a3',
			fillOpacity   : 0.35
		}); 

		

		// Add a listener for the click event
		map.addListener('click', addLatLng);
		
		color_ary = ["#FF1010", '#0000FF',"#32CD32","#FF0073","#047502", "#FF00C4","#5E610B","#BF00FF", "#60028C","#6A00FF","#F78181","#3700FF","#0080FF","#00FF6F","#FF9900",
		"#8C0263","#02708C","#028C59","#717502","#754B02","#DBA901",
		"#86B404","#585858","#8C020D","#DF3A01"];
	}

	function addLatLng(event) 
	{
		var path = poly.getPath(); 
		path.push(event.latLng);
		var timeStamp = new Date().getTime();  
		
	   // Add a new marker at the new plotted point on the polyline.
	   var marker = new google.maps.Marker({
	   	position: event.latLng,
	   	title: '#' + path.getLength(),
	   	map: map,  
	   });


	   google.maps.event.addListener(poly, "dragend", getPolygonCoords);
	   google.maps.event.addListener(poly.getPath(), "insert_at", getPolygonCoords);
	   google.maps.event.addListener(poly.getPath(), "remove_at", getPolygonCoords);
	   google.maps.event.addListener(poly.getPath(), "set_at", getPolygonCoords);
	   poly.setMap(map);  
	}

	function getPolygonCoords() 
	{
		var len = poly.getPath().getLength();
		var htmlStr = ''; lat_long = '';
		poly_cords = [];

		for (var i = 0; i < len; i++) 
		{
			lat_long = poly.getPath().getAt(i).toUrlValue(5);  
			htmlStr = 'new google.maps.LatLng ('+lat_long+')'; 
			poly_cords.push(htmlStr);
		} 

		poly_cords_str  =    poly_cords.join(', ');
	}

	function saveArea()
	{
		var _token       = "<?php echo csrf_token(); ?>";
		var co_ordinates = poly_cords_str;
		var zone_name    = $("#zone_name").val();

		if(zone_name=="")
		{
	 		swal('Please enter zone name');
	 		return false;
		}
		
		if(poly_cords_str != '')
		{
		 	$.ajax({
		 		url  : site_url+'/admin/assigned_area/store',
		 		data : { 
		 			'co_ordinates' : co_ordinates,
		 			'zone_name'    : zone_name,
		 			"_token"       : _token
		 		},
		 		type : 'post', 
		 		async: false,
				 		
		 		success:function(response){ 
		 			if(response=="success")
		 			{
						   /*$(".3col option[value='"+user_id+"']").remove();
						   $(".alert-success").css('display','block');
						   $("#alert-success").html('This zone already exists. Please try another name or zone.');
						   poly_cords_str="";
						   initMap();*/
						   swal({
					            title: "Success", 
					            text: "Area added successfully!", 
					            type: "success"
					        },function() {
					            location.reload();
					        });
						   // setTimeout(function(){ swal("Zone assigned successfully"); }, 500);
						   // window.location.reload();	   
					}
					else if(response == "already_exist")
					{
						swal('Already Exist');
						/*$(".alert-danger").css('display','block');
						$("#existence_error").html('This zone already exists. Please try another name or zone.');*/
					}
					else
					{
						$(".alert-danger").css('display','block');
						$("#existence_error").html('This zone already exists. Please try another name or zone.');
					}
				}
			});
		}
		else
		{
		 	swal('Please select area on map');
		} 
	}
$(window).load(function()
{
	initAutocomplete();
	initMap();
});

</script>
@stop