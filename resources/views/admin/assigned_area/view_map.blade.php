	@extends('admin.layout.master')                
	@section('main_content')

	<!-- BEGIN Page Title -->
	<link rel="stylesheet" type="text/css" href="{{ url('/') }}/assets/data-tables/latest/dataTables.bootstrap.min.css">
	<div class="page-title">
		<div>

		</div>
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
			<li class="active"><a href="{{ url($module_url_path) }}">Restricted Area</a></li>
			<span class="divider">
				<i class="fa fa-angle-right"></i>
				<i class="fa fa-eye"></i>                
			</span> 
			<li class="active">View</li>
		</ul>
	</div>
	<!-- END Breadcrumb -->

	<!-- BEGIN Main Content -->
	<div class="row">
		<div class="col-sm-12 col-md-12 col-jg-12">
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
					@include('admin.layout._operation_status')  
					<div class="col-md-10">
						<div id="ajax_op_status">  </div>
						<div class="alert alert-danger" id="error_msg" style="display:none;"></div>
						<div class="alert alert-success" id="success_msg" style="display:none;"></div>
					</div>
					@if(isset($arr_users) && count($arr_users)>0)  
					<div class="form-group" >
						<div class="col-sm-9 col-lg-4 controls" >  
							<select name="basic[]" multiple="multiple" class="form-control select_user 3col active" id="select_user">
								@foreach($arr_users as $key => $value)
								<option value="{{ $value['id'] }}">{{ $value['first_name'] or ''}} {{ $value['last_name'] or '' }} </option>                   
								@endforeach
							</select>
						</div>
					</div>
					@endif  
					<div class="btn-toolbar pull-right clearfix">
					</div>

					<div class="btn-group">
						<div class="form-group area_input" style="display:none;">
							<div class="col-sm-9 col-lg-4 controls" >
								<input type="text" class="form-control" name="restricted_area" placeholder="Restricted Area Name" id="restricted_area" style="width:180px" />
							</div>
						</div>
					</div>
					<div class="btn-group">
						<a class="btn btn-primary btn-cancel" style="display:none;">Cancel</a> 
					</div> 
					<div class="btn-group">
						<a class="btn btn-primary btn-save" onclick="saveArea()" style="display:none;">Save</a> 
						<div class="clearfix"></div>


					</div>
					<div class="row">
						<div class="col-md-8 col-md-8 col-md-8">
							<div id="user_location_map" style="height:800px "></div>
							<div class="map"  >
							</div>
							<input type="hidden" name="lat" value="" id="rest_lat" />
							<input type="hidden" name="lon" value="" id="rest_long"/>
							<input type="hidden" name="lon" value="" id="edit_id"/>
						</div>
						<div class="col-md-4 col-md-4 col-md-4"> 
							<div class="map-right-block">
								<div class="map-rig-inner-top"><h1> Zone Name </h1> </div>
								<br>
								<div class="form-group" >
									<label class="control-label">Zone Name<i style="color: red;">*</i></label>
									<div class="controls" >
										<input type="text" class="form-control" name="zone_name" placeholder="Enter Zone Name" id="zone_name" value ="{{$arr_restricted_areas['name']}}" required /> <!-- readonly -->
									</div>
								</div>
								<div class="cancel-button-block"><a class="cancel-button" onclick="updateArea()">Update</a> </div>

							</div>
						</div> 
					</div>
				</div>
				<br/>
				<div class="clearfix"></div>

				<input type="hidden" id="original_coords"/> 
				<input type="hidden" name="stored_lat" value="19.997454" id="stored_lat">
				<input type="hidden" name="stored_long" value="73.789803" id="stored_long">  

				<input type="hidden" id="coords" value="{{$arr_restricted_areas['co-ordinates']}}" data-area="{{$arr_restricted_areas['id']}}"/>

				<div class="right_sidebar">
					<a id="btn-scrollup" class="btn btn-circle btn-lg" href="#"><i class="fa fa-chevron-up"></i></a>    
					<div id="wrapper">
					</div>
				</div>
			</div>
		</div>

		<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key={{ isset($google_map_api_key) ? $google_map_api_key : 'AIzaSyCccvQtzVx4aAt05YnfzJDSWEzPiVnNVsY' }}&libraries=places,geometry,drawing,visualization"></script>
	<script>
		var module_url_path = "{{$module_url_path}}";
		var country = false;
		var city = false;

		//var map;
		var geocoder;
		var marker;
		var infowindow;
		var drawingManager;

		var locality_lat = false;
		var locality_lon = false;

		var stored_lat  = $('#stored_lat').val();
		var stored_long = $('#stored_long').val();
		
		// console.log(stored_lat+' , '+stored_long);

		var latlng      = new google.maps.LatLng(parseFloat(stored_lat), parseFloat(stored_long));
		
		var bounds = new google.maps.LatLngBounds();
		
		// console.log(latlng);

		// var lat_lng_str = "lat: "+stored_lat+", lng: "+stored_long;
		var co_ordinates; // co_ordinates of circle in miles. 
		var site_url       = '{{ url('/') }}';
		var poly           = '';
		var poly_cords     = [];
		var poly_cords_str = '';
		var stored_area    = [];
		var polygon        = '';
		var polygon_map    = '';
		var color_ary      = [];
		var polygonCoords = [];

   		function initMap() 
   		{
   			// console.log(latlng+'==='+lat_lng_str);
	   		// Create the map.
	   		// map = new google.maps.Map(document.getElementById('user_location_map'),
	   		// 							{
						// 			   		zoom: 15,
						// 			   		// center: latlng
						// 			   		// center: {lat: stored_lat, lng: stored_long},
						// 			   		// center: {lat: 40.741895, lng: -73.989308}, //latlng,
						// 			   		// center: {lat: stored_lat, lng: stored_long},
						// 			   		/*mapTypeId: google.maps.MapTypeId.HYBRID*/
						// 			   	}
	   		// 						);

	   		// var myLatLng = {lat: -25.363, lng: 131.044};

			var map = new google.maps.Map(document.getElementById('user_location_map'), {
						zoom: 10,
						//center: myLatLng,
						scrollwheel: true,
					});

	   		// placeMarker(latlng);
			// map.setCenter(latlng);
			// map.panTo(latlng);
	   		
			//set marker
			/*geocoder = new google.maps.Geocoder();
			marker = new google.maps.Marker({
			  	position  : latlng,
		  		map       : map,
		  		draggable : false,
		  	});*/

		  	// map.panTo(marker.getPosition());
   			
		  
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
		//map.addListener('click', addLatLng);
		
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
	
	function drawPolygon()
	{

	   // var co_ordinate_size      =  $("#co_ordinate_size").val();
	   var cnt = 0 ;
	   var color_cnt = 0;
	   var name = "{{$arr_restricted_areas['name']}}" ; 
	   var stores_co_ordinates  =  new Array(<?php echo $arr_restricted_areas['co-ordinates']; ?>);

	   
	   	var myLatLng = {lat: -25.363, lng: 131.044};

		var map = new google.maps.Map(document.getElementById('user_location_map'), {
					zoom: 10,
					scrollwheel: true,
					//center: myLatLng
				});
	   if(!(color_ary[color_cnt]))
	   {
	   	color_cnt = 0 ;             

	   }
	   var color  =  color_ary[color_cnt];                
	   stored_area[cnt]  = new google.maps.Polygon({
							   	paths         : stores_co_ordinates,
							   	draggable     : false,
							   	editable      : false,
							   	strokeColor   : color,
							   	strokeOpacity : 0.8,
							   	strokeWeight  : 2,
							   	fillColor     : color,
							   	fillOpacity   : 0.45
							   });
	   attachPolygonInfoWindow( stored_area[cnt], '<strong>'+name+'</strong>');
	   makeEditable(stored_area[cnt],"{{$arr_restricted_areas['co-ordinates']}}","{{$arr_restricted_areas['name']}}",cnt);

	   stored_area[cnt].setMap(map);
	   cnt++;
	   color_cnt++;

	   	/*var center_lat = myPolygon.my_getBounds().getCenter().lat();
		var center_lng = myPolygon.my_getBounds().getCenter().lng();*/
	   		
		// console.log(stores_co_ordinates.length);
		for (i = 0; i < stores_co_ordinates.length; i++) {
		   bounds.extend(stores_co_ordinates[i]);
		}

		// The Center of the polygon
		var bound_lat_lng = bounds.getCenter();
		
		//console.log('here1'+bound_lat_lng);

		// marker.setMap(null);

		var marker = new google.maps.Marker({
					    	position: bound_lat_lng,
					    	map: map,	
					    	title: '{{isset($arr_restricted_areas['zone_name']) ? $arr_restricted_areas['zone_name']  : '' }}'
					  });

		//map.setCenter(latlng);
		// map.panTo(bound_lat_lng);
		map.fitBounds(bounds);
		/*marker = new google.maps.Marker({
		  position: bound_lat_lng, 
		  map: map
		});*/

	   	/*google.maps.Polygon.prototype.getBoundingBox = function() {
		  var bounds = new google.maps.LatLngBounds();


		  this.getPath().forEach(function(element,index) {
		    bounds.extend(element)
		  });

		  return(bounds);
		};

		map.marker({ position: polygon.getBoundingBox().getCenter(), map:map});*/
	}


	function attachPolygonInfoWindow(polygon, html)
	{
		polygon.infoWindow = new google.maps.InfoWindow({
			content: html,
		});
		google.maps.event.addListener(polygon, 'mouseover', function(e) {
			var latLng = e.latLng;
			/*this.setOptions({fillOpacity:0.1});*/
			polygon.infoWindow.setPosition(latLng);
			// polygon.infoWindow.open(map);
		});
		google.maps.event.addListener(polygon, 'mouseout', function() {
			/*this.setOptions({fillOpacity:0.35});*/
			// polygon.infoWindow.close();
		});

	}
	function makeEditable(polygon,coords,name,id)
	{

		google.maps.event.addListener(polygon,'click', function(e) {
			editArea(id+1,name,coords);
		});

	}

	function disablePolygon()
	{
		stored_area[0].set('draggable', false);
		stored_area[0].set('editable', false);
	}

	function editArea(id,name,co_ordinates)
	{
		$("#zone_label").css("display","none");
		$("#edit_icon").css("display","none");
		$("#zone_name").css("display","block");

		$('#zone_name').val(name);
		$('#original_coords').val(co_ordinates);

		var id=0;
		/* disablePolygon();*/

		stored_area[id].set('draggable', true);
		stored_area[id].set('editable', true);

		google.maps.event.addListener(stored_area[id], "dragend", function(event)
		{ 
			var len = stored_area[id].getPath().getLength();
			var htmlStr = ''; lat_long = ''; edit_cords_str  = '';
			edit_poly_cords = [];

			for (var i = 0; i < len; i++) 
			{
				lat_long = stored_area[id].getPath().getAt(i).toUrlValue(5);  
				htmlStr = 'new google.maps.LatLng ('+lat_long+')'; 
				edit_poly_cords.push(htmlStr);
			} 

			edit_cords_str  =    edit_poly_cords.join(', ');    

			$("#coords").val(edit_cords_str); 
			if(co_ordinates!=edit_cords_str)
			{
				$('#zone_name').val(name);
				// $('#zone_name').removeAttr("readonly");
			}      
			else
			{
				$('#zone_name').val(name);
				// $('#zone_name').attr("readonly","readonly");
			}      

		});
		google.maps.event.addListener(stored_area[id], "mouseup", function(event)
		{
			var len = stored_area[id].getPath().getLength();
			var htmlStr = ''; lat_long = ''; edit_cords_str  = '';
			edit_poly_cords = [];
			
			for (var i = 0; i < len; i++) 
			{
				lat_long = stored_area[id].getPath().getAt(i).toUrlValue(5);  
				htmlStr = 'new google.maps.LatLng ('+lat_long+')'; 
				edit_poly_cords.push(htmlStr);
			} 

			edit_cords_str  =    edit_poly_cords.join(', ');  

			$("#coords").val(edit_cords_str); 
			if(co_ordinates!=edit_cords_str)
			{
				$('#zone_name').val(name);
			// $('#zone_name').removeAttr("readonly");
		}      
		else
		{
			$('#zone_name').val(name);
			 // $('#zone_name').attr("readonly","readonly");
			}  

		});
		google.maps.event.addListener(stored_area[id], "mousemove", function(event)
		{
			var len = stored_area[id].getPath().getLength();
			var htmlStr = ''; lat_long = ''; edit_cords_str  = '';
			edit_poly_cords = [];

			for (var i = 0; i < len; i++) 
			{
				lat_long = stored_area[id].getPath().getAt(i).toUrlValue(5);  
				htmlStr = 'new google.maps.LatLng ('+lat_long+')'; 
				edit_poly_cords.push(htmlStr);
			} 

			edit_cords_str  =    edit_poly_cords.join(', ');  

			$("#coords").val(edit_cords_str); 
			if(co_ordinates!=edit_cords_str)
			{
				$('#zone_name').val(name);
				$('#zone_name').removeAttr("readonly");
			}      
			else
			{
				$('#zone_name').val(name);
				$('#zone_name').attr("readonly","readonly");
			}   

		});

	}
	function updateArea()
	{
		var _token  = "<?php echo csrf_token(); ?>";
		var coords  = $("#coords").val();
		var name    = $("#zone_name").val();
		if(name=="")
		{
			swal('Please enter zone name');
			// $("#zone_name").focus();
			return false;
		}
		if(coords == "")
		{
			swal('Please select area on map');
			return false;
		}
		$.ajax({
			url  : site_url+'/admin/assigned_area/update',
			data : { 
				'co_ordinates' : coords,
				'id'      : "{{$arr_restricted_areas['id']}}",
				'name'    : name,
				"_token"       : _token
			},
			type : 'post', 
			async: false,
			beforeSend:function(){
				  // showProcessingOverlay();
				},
				success:function(response)
				{ 
					if(response=="success")
					{
						swal({
							title: "Success", 
							text: "Area updated successfully!", 
							type: "success"
						},function() {
							location.reload();
						});
						// window.location.reload();

						$("#success_msg").val('Assigned area details updated successfully');  
					  // setTimeout(function(){ 

					  // }, 3000);

					}
					else
					{
						$("#error_msg").val('Deleted successfully');
					}
				}
			});
	}


	$(window).load(function()
	{
		initMap();
		drawPolygon();
	});

</script>
<script type="text/javascript">
	$(function () {
		$('select[multiple].active.3col').multiselect({
			columns: 1,
			placeholder: 'Select States',
			search: true,
			searchOptions: {
				'default': 'Search States'
			},
			selectAll: true
		});
	});

</script>
@stop