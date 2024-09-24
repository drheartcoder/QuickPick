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
			<li class="active">{{ isset($module_title)?$module_title:"" }}</li>
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

					@include('admin.layout._operation_status')  

					{!! Form::open([ 'url' => $module_url_path.'/multi_action',
					'method'=>'POST',
					'enctype' =>'multipart/form-data',   
					'class'=>'form-horizontal', 
					'id'=>'frm_manage' 
					]) !!}

					{{ csrf_field() }}

					<div class="col-md-10">


						<div id="ajax_op_status">

						</div>
						<div class="alert alert-danger" id="no_select" style="display:none;"></div>
						<div class="alert alert-warning" id="warning_msg" style="display:none;"></div>
					</div>
					<div class="btn-toolbar pull-right clearfix">


						<div class="btn-group">
							<a href="{{ $module_url_path.'/create'}}" class="btn btn-primary btn-add-new-records"  title="Add {{ $module_title }}">Add Area</a> 
						</div>         
						
						<div class="btn-group">
							<a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip" 
							title="Multiple Active/Unblock" 
							href="javascript:void(0);" 
							onclick="javascript : return check_multi_action('frm_manage','activate');" 
							style="text-decoration:none;">
							<i class="fa fa-unlock"></i>
						</a> 
					</div>

					<div class="btn-group">
						<a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip btn-dangers" 
						title="Multiple Deactive/Block" 
						href="javascript:void(0);" 
						onclick="javascript : return check_multi_action('frm_manage','deactivate');"  
						style="text-decoration:none;">
						<i class="fa fa-lock"></i>
					</a> 
				</div>
			

				
				<div class="btn-group">    
					<a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip btn-delets" 
					title="Multiple Delete" 
					href="javascript:void(0);" 
					onclick="javascript : return check_multi_action('frm_manage','delete');"  
					style="text-decoration:none;">
					<i class="fa fa-trash-o"></i>
				</a>
			</div>  


			<div class="btn-group"> 
				<a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip refrash-btns" 
				title="Refresh" 
				href="{{ $module_url_path }}"
				style="text-decoration:none;">
				<i class="fa fa-repeat"></i>
			</a> 
		</div>
		<br>

	</div>
	<br/>
	<div class="clearfix"></div>

	
		<input type="hidden" name="multi_action" value="" />
		<table class="table table-advance"  id="table_module" >
			<thead>
				<tr>
					
					<th style="width: 18px; vertical-align: initial;"><div class="check-box">
						<input type="checkbox" class="filled-in" name="selectall" id="selectall" />
						<label for="selectall"></label>
					</div></th>
				
					<th> Area Name  </th>
					
					<th>   Status       </th> 
					
					<th width="150px">   Action </th>
				</tr>
			</thead>
			<tbody>

				@if(isset($arr_data) && sizeof($arr_data)>0)
				@foreach($arr_data as $value)
				<tr>
					
					<td>
						<div class="check-box"><input type="checkbox" class="filled-in case" name="checked_record[]"  id="mult_change_{{ base64_encode($value['id']) }}" value="{{ base64_encode($value['id']) }}" /><label for="mult_change_{{ base64_encode($value['id']) }}"></label></div> 
					</td>
				

					<td>{{$value['name']}}</td>

					
					<td>
						@if($value['is_active']==1)
						<a href="{{ $module_url_path.'/deactivate/'.base64_encode($value['id']) }}" class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip" onclick="return confirm_action(this,event,'Do you really want to deactivate this record ?')" title="Deactivate" ><i class="fa fa-unlock"></i></a>
						@else
						<a href="{{ $module_url_path.'/activate/'.base64_encode($value['id']) }}" class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip btn-dangers" onclick="return confirm_action(this,event,'Do you really want to activate this record ?')" title="Activate" ><i class="fa fa-lock"></i></a>
						@endif
					</td> 
					

					
					<td>
						
							<a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip call_loader btn-delets" href="{{ $module_url_path.'/view_map/'.base64_encode($value['id'])}}" title="" data-original-title="Edit">
							<i class="fa fa-edit"></i>
							
						</a> 
						
						<a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip btn-delets" href="{{ $module_url_path.'/delete/'.base64_encode($value['id']) }}"  onclick="return confirm_action(this,event,'Do you really want to delete this record ?')" title="Delete">
							<i class="fa fa-trash" ></i>
						</a>
						
					</td> 


				</tr>
				@endforeach
				@endif

			</tbody>
		</table>  
	</div>

	<div> </div>

	{!! Form::close() !!}
</div>
</div>

<script type="text/javascript">
	var site_url       = '{{ url('/') }}';
	$(document).ready(function() {
		$('#table_module').DataTable();
	});

	function loadUsers()
	{

		var user_type = $("#user_type").val();
		window.location.href = site_url+'/admin/assigned_area?user_type='+user_type;
	}
</script>
@stop