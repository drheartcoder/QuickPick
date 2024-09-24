	@extends('admin.layout.master')                

	@section('main_content')

	<link rel="stylesheet" type="text/css" href="{{ url('/') }}/assets/data-tables/latest/dataTables.bootstrap.min.css">
	<div class="page-title">
		<div>

		</div>
	</div>
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
				<i class="fa fa-map-marker"></i>                
			</span> 
			<li class="active">View</li>
		</ul>
	</div>

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

					
					<div class="button-sctions">
						<ul>
							<li><button role="button" class="btn btn-default setclass" onclick="window.location.href='{{$module_url_path."/create"}}'">New</button></li>
							<li><button role="button" class="btn btn-default setclass active" onclick="window.location.href='{{$module_url_path."/create_existing"}}'">Existing</button></li>
						</ul>
						<div class="clearfix"></div>
					</div>

					@include('admin.layout._operation_status')  
					<div class="col-md-10">
						<div id="ajax_op_status">  </div>
						<div class="alert alert-danger" id="error_msg" style="display:none;"></div>
						<div class="alert alert-success" id="success_msg" style="display:none;"></div>
					</div>
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
					</div>

					<div class="clearfix"></div>
					<div class="row">
						<div class="col-md-8 col-md-8 col-md-8">
							<div id="user_location_map" style="height:800px "></div>
							<div class="map"  >
							</div>
							<input type="hidden" name="lat" value="" id="rest_lat" />
							<input type="hidden" name="lon" value="" id="rest_long"/>
							<input type="hidden" name="lon" value="" id="edit_id"/>
						</div>
						<div class="col-md-4"> 
							<div class="map-right-block second create-cals">

								<div class="map-rig-inner-top"><h1> Assigned Zones </h1> </div>
								<div class="map-rig-inner-cunten" >
									<div class="controls left-pul">
										<input type="text" class="form-control" name="serach" id="search" placeholder="search">
										<span><i class="fa fa-search"></i></span>
									</div>
								</div>          
								<div class="ul-list-create">
									@if(isset($arr_restricted_areas) && !empty($arr_restricted_areas))
									@foreach($arr_restricted_areas as $key=>$restricted_area) 
									<div class="map-rig-inner-cunten zone_name active"  >
										<h1 id="names_{{$restricted_area['id']}}"><a class="btn btn-circle show-tooltip" title="Edit selected" onclick="editArea({{$key+1}},'{{$restricted_area["name"]}}','{{$restricted_area["co-ordinates"]}}')"> {{$restricted_area['name']}}</a></h1> 

										<div class="btn-toolbar pull-right">
											<div class="btn-group">
												<a class="btn btn-circle show-tooltip" title="Edit selected" onclick="editArea({{$key+1}},'{{$restricted_area["name"]}}','{{$restricted_area["co-ordinates"]}}')"><i class="fa fa-edit"></i></a>

											</div>   
										</div>
										<div class="clearfix"></div> 
									</div> 
									@endforeach
									@endif 
								</div>
								<div class="map-rig-inner-cunten" id="hide_row" style="display:none">
									No Records Found
								</div>       
								<div class="country-main" style="margin-top:30px">
									<div class="row"> 
										<div class="col-md-8"> 

										</div>

									</div>
								</div>

								<div class="country-main" style="margin-top:30px">
									<div class="row"> 
										<div class="col-md-4"> 

										</div>
										<div class="col-md-8"> 
											<div class="form-group" >

											</div>

										</div>

									</div>
								</div>

								<div class="country-main" style="margin-top:30px">
									<div class="row"> 
										<div class="col-md-4"> 
											<label class="control-label">Zone name<i style="color: red;">*</i></label>
										</div>
										<div class="col-md-8"> 
											<div class="form-group" >

												<div class="controls" >
													<input type="text" class="form-control" name="zone_name" id="zone_name" placeholder="Zone Name" />  <!-- readonly -->

												</div>
											</div>

										</div>

									</div>
								</div>
								<div class="clerfix"></div> 
								<div class="cancel-button-block"><a class="cancel-button pull-right" href="javascript:void(0)" onclick="saveArea()">Save</a> </div>


								<div class="clerfix"></div> 
							</div>
						</div> 

					</div>
				</div>
				<br/>
				<div class="clearfix"></div>

				<input type="hidden" id="original_coords"/> 

				<input type="hidden" name="stored_lat" value="19.997454" id="stored_lat">
				<input type="hidden" name="stored_long" value="73.789803" id="stored_long"> 


				@if(isset($arr_restricted_areas) && !empty($arr_restricted_areas))
				@foreach($arr_restricted_areas as $key => $restricted_area) 
				<input type="hidden" id="coords_{{$key}}" value="{{$restricted_area['co-ordinates']}}" data-area="{{$restricted_area['id']}}"/>
				@endforeach
				@endif

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

		var map;
		var geocoder;
		var marker;
		var infowindow;
		var drawingManager;

		var locality_lat = false;
		var locality_lon = false;

		var stored_lat  = $('#stored_lat').val();
		var stored_long = $('#stored_long').val();
		var latlng      = new google.maps.LatLng(stored_lat, stored_long);
		
   var co_ordinates; // co_ordinates of circle in miles. 
   var site_url       = '{{ url('/') }}';
   var poly           = '';
   var poly_cords     = [];
   var poly_cords_str = '';
   var stored_area    = [];
   var polygon        = '';
   var polygon_map    = '';
   var color_ary      = [];
   border_color_ary =[];

   function initMap() 
   { 
	   // Create the map.
	   map = new google.maps.Map(document.getElementById('user_location_map'), {
	   	zoom: 14,
	   	center: latlng,

	   	/*mapTypeId: google.maps.MapTypeId.HYBRID*/
	   });
	   
		  //set marker
		  geocoder = new google.maps.Geocoder();
		  marker = new google.maps.Marker({
		  	position  : latlng,
		  	map       : map,
		  	draggable : false,

		  });
		  
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
		
		color_ary = ["rgb(255, 16, 16)", 'rgb(0, 0, 255)' ,"rgb(50, 205, 50)","rgb(255, 0, 115)","rgb(4, 117, 2)", "rgb(255, 0, 196)","rgb(94, 97, 11)",
		'rgb(191, 0, 255)', 'rgb(96, 2, 140)','rgb(106, 0, 255)','rgb(247, 129, 12)','rgb(55, 0, 255)','rgb(0, 128, 255)','rgb(0, 255, 111)',
		"rgb(255, 153, 0)","rgb(140, 2, 99)","rgb(2, 112, 140)","rgb(2,140,89)","rgb(113,117,2)","rgb(117,75,2)","rgb(219,169,1)", "rgb(134,180,4)","rgb(88,88,88)","rgb(140,2,13)","rgb(96,32,64)"];

		 /* border_color_ary = ["rgba(255, 16, 16,0.8)", 'rgba(0, 0, 255,0.8)' ,"rgba(50, 205, 50,0.8)","rgba(255, 0, 115,0.8)","rgba(4, 117, 2,0.8)", 
							  "rgba(255, 0, 196,0.8)","rgba(94, 97, 11,0.8)",'rgba(191, 0, 255,0.8)', 'rgba(96, 2, 140,0.8)','rgba(106, 0, 255,0.8)',
							  'rgba(247, 129, 12,0.8)','rgba(55, 0, 255,0.8)','rgba(0, 128, 255,0.8)','rgba(0, 255, 111,0.8)',"rgba(255, 153, 0,0.8)",
							  "rgba(140, 2, 99,0.8)","rgba(2, 112, 140,0.8)","rgba(2,140,89,0.8)","rgba(113,117,2,0.8)","rgba(117,75,2,0.8)",
							  "rgba(219,169,1,0.8)", "rgba(134,180,4,0.8)","rgba(88,88,88,0.8)","rgba(140,2,13,0.8)","rgba(96,32,64,0.8)"];     */         


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
	   
	   <?php  
	   
	   if(isset($arr_restricted_areas) && !empty($arr_restricted_areas)) 
	   {
	   	foreach($arr_restricted_areas as $key=>$value)
	   	{
	   		?>         

				//console.log(<?php echo $value['co-ordinates']  ?>);
				var name = "{{$value['name']}}" ;
				var stores_co_ordinates  =  new Array(<?php echo $value['co-ordinates']; ?>);
				
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
					fillOpacity   : 0.45,

				});
				attachPolygonInfoWindow( stored_area[cnt], '<strong>'+name+'</strong>');
				makeEditable(stored_area[cnt],'{{$value["co-ordinates"]}}','{{$value["name"]}}',cnt);

				
				stored_area[cnt].setMap(map);
				cnt++;
				color_cnt++;
				console.log(color);

			   /* google.maps.event.addListener(stored_area[cnt],"mouseover",function(){
				  this.setOptions({name: "title"});
				});*/

				$("#names_{{$value['id']}} ").before("<div style='background-color:"+color+";width:20px;height:20px;opacity:0.45;border-color:"+color+";border-opacity'></div>");

				/*var marker = new google.maps.Marker({
				  position: stores_co_ordinates[0],
				  draggable: false,
				  map: map,
				  title: "{{$value['name']}}",
				});*/
				
				
				<?php               
			}
		} 
		?>  
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
			polygon.infoWindow.open(map);
		});
		google.maps.event.addListener(polygon, 'mouseout', function() {
			/*this.setOptions({fillOpacity:0.35});*/
			polygon.infoWindow.close();
		});
		
	}
	function makeEditable(polygon,coords,name,id)
	{

		google.maps.event.addListener(polygon,'click', function(e) {
			editArea(id+1,name,coords);
		});

	}
	function saveArea()
	{
		var _token  = "<?php echo csrf_token(); ?>";
		var edit_id = $("#edit_id").val();
		var coords  = $("#coords_"+edit_id).val();
		var zone_name    = $("#zone_name").val(); 

		if(zone_name=="")
		{
			swal('Please enter zone name');
			return false;
		}
		if(coords != '')
		{
			$.ajax({
				url  : site_url+'/admin/assigned_area/store',
				data : { 
					'co_ordinates' : coords,
					'zone_name'    : zone_name,
					"_token"       : _token
				},
				type : 'post', 
				async: false,
				/*beforeSend:function(){
					showProcessingOverlay();
				}, */
				success:function(response){ 
					if(response=="success")
					{
					   /*$(".3col option[value='"+user_id+"']").remove();
					   $(".alert-success").css('display','block');
					   $("#alert-success").html('This zone already exists. Please try another name or zone.');
					   poly_cords_str="";
					   initMap();*/

					   setTimeout(function(){ swal("Zone assigned successfully"); }, 6000);
					   window.location.reload();
					   
					}
					else if(response == "already_exist")
					{
						$(".alert-danger").css('display','block');
						$("#existence_error").html('This zone already exists. Please try another name or zone.');
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
	function disablePolygon()
	{

		for(d =0; d < {{count($arr_restricted_areas)}}; d++)
		{
			stored_area[d].set('draggable', false);
			stored_area[d].set('editable', false);
		}
	}
	function editArea(id,name,co_ordinates)
	{

		$('#edit_id').val(id-1);
		$('#zone_name').val(name);
		$('#original_coords').val(co_ordinates);
		var id= id-1; 
		
		disablePolygon();

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
			
			$("#coords_"+id).val(edit_cords_str); 
			if(co_ordinates!=edit_cords_str)
			{
				$('#zone_name').val("");
				$('#zone_name').removeAttr("readonly");
			}      
			else
			{
				$('#zone_name').val(name);
				$('#zone_name').attr("readonly","readonly");
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
			
			$("#coords_"+id).val(edit_cords_str); 
			if(co_ordinates!=edit_cords_str)
			{
				$('#zone_name').val("");
				$('#zone_name').removeAttr("readonly");
			}      
			else
			{
				$('#zone_name').val(name);
				$('#zone_name').attr("readonly","readonly");
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

			$("#coords_"+id).val(edit_cords_str); 
			if(co_ordinates!=edit_cords_str)
			{
				$('#zone_name').val(name);
				$('#zone_name').removeAttr("readonly");
			}      
			else
			{
				$('#zone_name').val(name);
				// $('#zone_name').attr("readonly","readonly");
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
	$("#search").keyup(function(){

		var search_text = $("#search").val(); 
		searchTable(search_text); 

	});
	function searchTable(inputVal) {
		var count =0;
		var ul = $('div.zone_name');
		ul.each(function(index, row1) {

			var li = $(row1).text();
			var name = li.trim();
			var found = false;

			var regExp = new RegExp(inputVal, 'i');
			if(regExp.test(name)) 
			{
				$(row1).show();
				count = count  + 1;
			} 
			else 
			{
				$(row1).hide();
			}
		});
		if (count == 0) {
			$('#hide_row').show();
		}
		else {
			$('#hide_row').hide();
		}
	}
</script>
@stop