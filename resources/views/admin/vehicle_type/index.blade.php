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
                <i class="fa fa-truck"></i>           
            </span> 
             <li class="active">{{ $module_title or ''}}</li>
           
        </ul>
      </div>
    <!-- END Breadcrumb -->

    <!-- BEGIN Main Content -->
    <div class="row">
      <div class="col-md-12">

          <div class="box">
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
          
           <a href="{{ $module_url_path.'/create' }}" class="btn btn-primary btn-add-new-records" title="Add {{ str_singular($module_title) }}">Add {{ str_singular($module_title) }}</a> 
          
          </div>
  
      
            <div class="btn-group"> 
            
                  
                <a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip btn-dangers" 
                   title="Lock" 
                   href="javascript:void(0)"
                   onclick="javascript : return check_multi_action('frm_manage','deactivate');" 
                   style="text-decoration:none;">
                   <i class="fa fa-lock"></i>
                </a> 

                <a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip" 
                   title="Unlock" 
                   href="javascript:void(0)"
                   onclick="javascript : return check_multi_action('frm_manage','activate');" 
                   style="text-decoration:none;">
                   <i class="fa fa-unlock"></i>
                </a> 
            
                <a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip btn-delets" 
                   title="Multiple Delete" 
                   href="javascript:void(0);" 
                   onclick="javascript : return check_multi_action('frm_manage','delete');"  
                   style="text-decoration:none;">
                   <i class="fa fa-trash-o"></i>
                </a>
            
                <a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip refrash-btns" 
                   title="Refresh" 
                   href="javascript:void(0)"
                   onclick="javascript:location.reload();" 
                   style="text-decoration:none;">
                   <i class="fa fa-repeat"></i>
                </a> 
            </div>
          </div>
          <br/>
          <div class="clearfix"></div>
          <div class="table-responsive" style="border:0">

            <input type="hidden" name="multi_action" value="" />

            <table class="table table-advance"  id="table_module" >
              <thead>
                <tr>

                  
                    <th style="width:18px"> 

                    <div class="check-box">
                            <input type="checkbox" class="filled-in" name="selectall" id="selectall" />
                            <label for="selectall"></label>
                    </div></th>
                  
                  <th>Vehicle Type</th> 
                  <th>Starting Price</th> 
                  <th>Per Miles Price</th> 
                  <th>Per Minute Price</th> 
                  <th>Minimum Price</th> 
                  <th>Cancellation Price</th> 
                  <th>No of Pallet</th> 
                  <th>Min. Dimension<br>(L x B x H) ft</th>
                  <th>Max. Dimension<br>(L x B x H) ft</th>
                  <th>Min. Weight<br>lb</th>
                  <th>Max. Weight<br>lb</th>
                  <th>Min. Volume<br>ft<sup>3</sup></th>
                  <th>Max. Volume<br>ft<sup>3</sup></th>
                  
                   <th>Status</th> 
                  
                  <th style="width: 80px;"">Action</th>
                  
                </tr>
              </thead>
              <tbody>
          
                @if(isset($arr_vehicle_type) && sizeof($arr_vehicle_type)>0)
                  @foreach($arr_vehicle_type as $data)
                  
                  <tr>
                    
                    <td>
                    <div class="check-box"><input type="checkbox" class="filled-in case" name="checked_record[]"  id="mult_change_{{ base64_encode($data['id']) }}" value="{{ base64_encode($data['id']) }}" /><label for="mult_change_{{ base64_encode($data['id']) }}"></label></div> 
                      
                    </td>
                    

                    <td > {{ isset($data['vehicle_type']) ? $data['vehicle_type'] : 'N/A' }} </td> 
                    
                    <td ><i class="fa fa-usd"></i> {{ isset($data['starting_price']) ? number_format($data['starting_price'],2) : '0' }} </td> 
                    <td ><i class="fa fa-usd"></i> {{ isset($data['per_miles_price']) ? number_format($data['per_miles_price'],2) : '0' }} </td> 
                    <td ><i class="fa fa-usd"></i> {{ isset($data['per_minute_price']) ? number_format($data['per_minute_price'],2) : '0' }} </td> 
                    <td ><i class="fa fa-usd"></i> {{ isset($data['minimum_price']) ? number_format($data['minimum_price'],2) : '0' }} </td> 
                    <td ><i class="fa fa-usd"></i> {{ isset($data['cancellation_base_price']) ? number_format($data['cancellation_base_price'],2) : '0' }} </td> 

                    <td > {{ isset($data['no_of_pallet']) ? $data['no_of_pallet'] : '0' }}</td> 

                    <td > {{ isset($data['vehicle_min_length']) ? $data['vehicle_min_length'] : '' }} x {{ isset($data['vehicle_min_height']) ? $data['vehicle_min_height'] : '' }} x {{ isset($data['vehicle_min_breadth']) ? $data['vehicle_min_breadth'] : '' }} </td> 
                    <td > {{ isset($data['vehicle_max_length']) ? $data['vehicle_max_length'] : '' }} x {{ isset($data['vehicle_max_height']) ? $data['vehicle_max_height'] : '' }} x {{ isset($data['vehicle_max_breadth']) ? $data['vehicle_max_breadth'] : '' }}  </td> 
                    <td > {{ isset($data['vehicle_min_weight']) ? number_format($data['vehicle_min_weight'],2) : '0' }} </td>                    
                    <td > {{ isset($data['vehicle_max_weight']) ? number_format($data['vehicle_max_weight'],2) : '0' }} </td> 

                    <td > {{ isset($data['vehicle_min_volume']) ? number_format($data['vehicle_min_volume'],2) : '0' }} </td>                    
                    <td > {{ isset($data['vehicle_max_volume']) ? number_format($data['vehicle_max_volume'],2) : '0' }} </td> 

                                  
                      <td>
                        
                          @if($data['is_active']==1)
                          <a href="{{ $module_url_path.'/deactivate/'.base64_encode($data['id']) }}" class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip" onclick="return confirm_action(this,event,'Do you really want to deactivate this record ?')" title="Deactivate" ><i class="fa fa-unlock"></i></a>
                          @else
                          <a href="{{ $module_url_path.'/activate/'.base64_encode($data['id']) }}" class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip btn-dangers" onclick="return confirm_action(this,event,'Do you really want to activate this record ?')" title="Activate" ><i class="fa fa-lock"></i></a>
                          @endif
                        
                     </td>
                  
                     
                      <td style="width: 100px;"> 
                       
                        
                         <a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip btn-delets" href="{{ $module_url_path.'/edit/'.base64_encode($data['id']) }}" title="Edit">
                          <i class="fa fa-edit" ></i>
                         </a>  
                        
                        <a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip btn-delets" href="{{ $module_url_path.'/delete/'.base64_encode($data['id']) }}"  
                           onclick="return confirm_action(this,event,'Do you really want to delete this record ?')" 
                           title="Delete">
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
</div>
<script type="text/javascript">
    
     $(document).ready(function() {
    $('#table_module').DataTable( {
      aoColumnDefs: [
    {
     bSortable: false,
     aTargets: [0],
    }
]
});
  });


    
    </script>
<!-- END Main Content -->

@stop                    


