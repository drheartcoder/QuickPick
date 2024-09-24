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
                <i class="fa fa-users"></i>                
            </span> 
            
            <li class="active">
              <a href="{{isset($module_url_path) ?  $module_url_path : '' }}">
                {{ isset($main_module_title)?$main_module_title:"" }}
                </a>
            </li>
            
            <span class="divider">
                <i class="fa fa-angle-right"></i>
                <i class="fa fa-money"></i>                
            </span> 

            <li class="active">{{ isset($module_title)?$module_title:"" }}</li>
        </ul>
      </div>
    <!-- END Breadcrumb -->

    <!-- BEGIN Main Content -->
    <div class="row">
      <div class="col-md-12">

          <div class="box {{ $theme_color }} ">
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

            
                <?php 
                $first_name = isset($arr_user['first_name']) ? $arr_user['first_name']:'';
                $last_name = isset($arr_user['last_name']) ? $arr_user['last_name']:'';
                
                $full_name = $first_name.' '.$last_name;
                  ?>
            <div class="col-md-10">
            <div id="ajax_op_status">
                
            </div>
            <div class="alert alert-danger" id="no_select" style="display:none;"></div>
            <div class="alert alert-warning" id="warning_msg" style="display:none;"></div>
            <div class="form-group" style="">
                  <label class="col-sm-6 col-lg-3 control-label"><h3><strong>Driver Name : {{$full_name or ''}}</strong></h3></label>
            </div>
          </div>

          <div class="btn-toolbar pull-right clearfix">

            <div class="btn-group"> 
            <a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip refrash-btns"  
               title="Refresh" 
               href="{{ $module_url_path.'/fair_charge_request/'.$enc_id}}"
               style="text-decoration:none;">
               <i class="fa fa-repeat"></i>
            </a> 
            </div>
              <br>
         
          </div>

          <br/> <br/>
          <div class="clearfix"></div>

          <div class="table-responsive" style="border:0">      
              <input type="hidden" name="multi_action" value="" />
              <table class="table table-advance"  id="table_module" >
              <thead>
                <tr>
                
                <th style="width: 10%">Sr.No.</th>  
                <th style="width: 20%">Fair charge</th>
                <th style="width: 25%">Status</th>
                 <th style="width: 20%">Request Date</th> 
                <th>Action</th>
              </tr>
              </thead>
              <tbody>

              @if(isset($arr_data) && sizeof($arr_data)>0)
              @foreach($arr_data as $key => $value)

                <tr>
                <td>
                {{ $key + 1}} )
                </td>
                 <td>
                  {{ $value['fair_charge'] or 0 }}
                </td>
                      
                <td>
                @if(isset($value['status']))
                   @if($value['status'] == 'REQUEST') 
                    <span style="width: 50%" class="badge badge-info" style="width:100px">New request</span>
                   @elseif($value['status'] == 'APPROVE') 
                     <span style="width: 50%" class="badge badge-success" style="width:100px">Approved</span>
                   @elseif($value['status'] == 'REJECT') 
                    <span style="width: 50%" class="badge badge-important" style="width:100px">Rejected</span>
                   @else
                  @endif 
                @endif
                </td> 
                <?php  $date = date('d M Y',strtotime($value['created_at'])); ?>
                <td> {{ $date or 0 }} </td>
                <td>
                      
                      @if(isset($value['is_editable']) && $value['is_editable'] == 1)
                        @if($value['status'] == 'REQUEST')
                        
                          <a href="{{ $module_url_path.'/change_request_status/'.base64_encode($value['id']).'/'.base64_encode('approve') }}" class="btn btn btn-success" onclick="return confirm_action(this,event,'Do you really want to approve fair charge request ?')" title="Approve Request" >Approve Request</a>

                          <a href="{{ $module_url_path.'/change_request_status/'.base64_encode($value['id']).'/'.base64_encode('reject') }}" class="btn btn btn-danger" onclick="return confirm_action(this,event,'Do you really want to reject fair charge request ?')" title="Reject Request" >Reject Request</a>

                        @elseif($value['status'] == 'APPROVE')
                          <span style="width: 50%" class="badge badge-success">Request Approved </span>
                        @elseif($value['status'] == 'REJECT')
                          <span style="width: 50%" class="badge badge-important">Request Rejected </span>
                        @else
                        -
                        @endif
                      @elseif(isset($value['is_editable']) && $value['is_editable'] == 0)
                          @if($value['status'] == 'REQUEST')
                            <span style="width: 50%" class="badge badge-warning">No Action Perform </span>
                          @elseif($value['status'] == 'APPROVE')
                            <span style="width: 50%" class="badge badge-success">Request Approved </span>
                          @elseif($value['status'] == 'REJECT')
                            <span style="width: 50%" class="badge badge-important">Request Rejected </span>
                          @else
                          -
                          @endif

                      @endif
                </td>    
              </tr>
              @endforeach
              @endif

                </tbody>
            </table>  
          </div>

          <div>  </div>
         
          {!! Form::close() !!}
      </div>
  </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $('#table_module').DataTable();
    });
</script>
@stop