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
                <i class="fa fa-microphone"></i>                
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

           <div class="table-responsive" style="border:0">      
              <input type="hidden" name="multi_action" value="" />
                <table class="table table-advance table-arrows"  id="table_module">
                  <thead>
                    <tr>  
                        <th><a class="sort-desc" href="#">ID </a></th> 
                        <th><a class="sort-desc" href="#">UNIQUE ID </a></th> 
                        <th><a class="sort-desc" href="#">Date </a></th> 
                        <th><a class="sort-desc" href="#">User Name </a></th> 
                        <th><a class="sort-desc" href="#">Pickup Location </a></th> 
                        <th><a class="sort-desc" href="#">Drop Location </a></th> 
                        <th><a class="sort-desc" href="#">Status</a></th> 
                        <th width="150px">Action</th>
                        
                    </tr>
                    </thead>
                    <tbody>
								@if(isset($arr_load_post_request) && sizeof($arr_load_post_request)>0)
								@foreach($arr_load_post_request as $key => $value)              
									<tr>
                  
                    <td>{{ isset($value['id']) ? $value['id']  : '' }}</td>
                    <td>{{ (isset($value['load_post_request_unique_id']) && ($value['load_post_request_unique_id'] != '') ) ? $value['load_post_request_unique_id']:'-' }}</td>
										<td> {{ isset($value['date']) ? date('d M Y h:i:s', strtotime($value['date'])) : '-' }} </td>
										<td>{{ isset($value['user_details']['first_name']) ? $value['user_details']['first_name'] : '' }} {{ isset($value['user_details']['last_name']) ? $value['user_details']['last_name'] : '' }}</td>
										
										<td>{{ isset($value['pickup_location']) ? $value['pickup_location'] : '' }}</td>
										<td>{{ isset($value['drop_location']) ? $value['drop_location'] : '' }}</td>
										
										<td><span class="badge badge-info" style="width:100%">{{isset($value['request_status']) ? ucfirst(strtolower(str_replace('_', ' ', $value['request_status']))) : ''}}</span></td>
										<td><a href="{{$module_url_path}}/view/{{base64_encode($value['id'])}}" class="btn btn-primary btn-bordered" title="Assign Driver">Assign Driver</a></td>
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
</div>
<script type="text/javascript">
$(document).ready(function() {
	$('#table_module').DataTable({
    "order": [[ 0, "desc" ]]
  });
});
</script>
@stop