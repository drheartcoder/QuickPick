@extends('company.layout.master')                
@section('main_content')

<style type="text/css">
	.table.table-new{border: 1px solid #E6E6E6;margin-bottom: 20px;}
	.table.table-new th{font-size: 16px;background-color:#f1f1f1; }
	.titles-h1.h2dashord{ margin-left: 10px;margin-bottom: 30px; }
</style>

<!-- BEGIN Page Title -->
<link rel="stylesheet" type="text/css" href="{{ url('/') }}/assets/data-tables/latest/dataTables.bootstrap.min.css">
<div class="page-title">
	<div>
	</div>
</div>

<div id="breadcrumbs">
	<ul class="breadcrumb">
		<li>
			<i class="fa fa-home"></i>
			<a href="{{ url($company_panel_slug.'/dashboard') }}">Dashboard</a>
			<i class="fa fa-angle-right"></i>
		</li>
		<li class="active">
			<i class="fa fa-bell"></i>
			{{ isset($module_title) ? $module_title:"" }}    
		</li>
	</ul>
</div>

{{-- {{ dd(url()) }} --}}

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
				@include('company.layout._operation_status')			
					<div class="col-md-12">
						<div class="table-responsive">      
							<table class="table xtable-advance"  id="table_module" >
								<thead>
									<tr>
										<th> Sr. No.</th> 
										<th> Notifications Type </th>
										<th> Title </th>
										<th> Date </th>
										<th> Actions </th>
									</tr>
								</thead>	
								<tbody>
									@if(isset($arr_notification) && sizeof($arr_notification)>0)
									@foreach($arr_notification as $key => $value)        
									<tr <?php if($value['is_read']==1){ ?> class="active" <?php } ?>>
										<td> {{ $key+1 }} </td>
										<td> {{ (isset($value['notification_type']) && ($value['notification_type'] != '') ) ? $value['notification_type']:'N/A' }}</td>
										<td> {{ (isset($value['title']) && ($value['title'] != '') ) ? $value['title']:'N/A' }}	 </td>
										<td> {{ (isset($value['created_at']) && ($value['created_at'] != '0000-00-00 00:00:00')) ? date('d M Y h:i A', strtotime($value['created_at'])) : 'N/A' }} </td>
										<td> 
											<?php
                                            	$json_notification = isset($value) ? json_encode($value) :''; ?>
											<a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip call_loader" onclick="change_notification_status({{$json_notification}});" href="{{ url($value['view_url']) }}">              	<i class="fa fa-eye"></i>
            								</a>
            							</td>												
									</tr>
									@endforeach
									@endif

								</tbody>  
							</table>  
						</div>
	                        
	                </div>
					<div class="clearfix"></div>
				</div>
				<hr class="margin-0">
				<div class="clearfix"></div>
			</div>
		</div>
	</div>

<script type="text/javascript">
$(document).ready(function() {
   $('#table_module').DataTable();
});
</script>


<script type="text/javascript">
        function change_notification_status(ref) {
            if(ref!=undefined){
                if(ref.id!=0 && ref.view_url!=''){
                    $.ajax({
                            url:locations_url_path+'/change_notification_status?notification_id='+btoa(ref.id),
                            type:'GET',
                            data:'flag=true',
                            dataType:'json',
                            success:function(response)
                            {
                                // window.location();
                                window.location.replace(site_admin_url+ref.view_url);
                            }     
                    });
                }
            }
        }
    </script>    
@stop