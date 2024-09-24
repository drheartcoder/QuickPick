@extends('admin.layout.master')
@section('main_content')
<link rel="stylesheet" type="text/css" href="{{ url('/assets/data-tables/latest/') }}/dataTables.bootstrap.min.css">

    <!-- BEGIN Page Title -->
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
                <i class="fa fa-sitemap"></i>               
            </span> 
            <li class="active">{{ $module_title or ''}}</li>    
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
                                 'class'=>'form-horizontal', 
                                 'id'=>'frm_manage' 
                                ]) !!}     

          

            <div class="col-md-10">
            

            <div id="ajax_op_status">
            </div>
            <div class="alert alert-danger" id="no_select" style="display:none;"></div>
            <div class="alert alert-warning" id="warning_msg" style="display:none;"></div>
          </div>
          <div class="btn-toolbar pull-right clearfix">

          
          <div class="btn-group">
          <a href="{{ $module_url_path.'/create'}}" class="btn btn-primary btn-add-new-records"  title="Add {{ $module_title }}">Add {{ $module_title }}</a> 
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

          </div>
          <br/><br/>

          <div class="clearfix"></div>
          <div class="table-responsive" style="border:0">
            <input type="hidden" name="multi_action" value="" />
            <table class="table table-advance"  id="table_module" >
              <thead>
                <tr>
                  
                  <th style="width:18px"> <div class="check-box">
                            <input type="checkbox" class="filled-in" name="selectall" id="selectall" />
                            <label for="selectall"></label>
                        </div></th>
                  
                  <th>Page Name</th>
                  <th>Page Slug</th>
                  <th>Status</th>
                  
                    <th width="150px">Action</th> 
                  
                </tr>
              </thead>
              <tbody>

               @foreach($arr_static_page as $page)
        
            <tr>
            
              <td> 
                
                  <div class="check-box"><input type="checkbox" class="filled-in case" name="checked_record[]"  id="mult_change_{{ base64_encode($page['id']) }}" value="{{ base64_encode($page['id']) }}" /><label for="mult_change_{{ base64_encode($page['id']) }}"></label></div> 
                
              </td>
            
                <td> {{ isset($page['page_title']) ? $page['page_title'] :'-'}} </td> 
                <td> {{ isset($page['page_slug']) ? $page['page_slug'] :'-'}} </td>  

                   <td>
                      
                        @if($page['is_active']==1)
                          <a href="{{ $module_url_path.'/deactivate/'.base64_encode($page['id']) }}" class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip" onclick="return confirm_action(this,event,'Do you really want to deactivate this record ?')" title="Deactivate" ><i class="fa fa-unlock"></i></a>
                        @else
                          <a href="{{ $module_url_path.'/activate/'.base64_encode($page['id']) }}" class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip btn-dangers" onclick="return confirm_action(this,event,'Do you really want to activate this record ?')" title="Activate" ><i class="fa fa-lock"></i></a>
                        @endif
                      
                  
                   </td>
                
                <td> 
                  

                    <a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip btn-delets" href="{{ $module_url_path.'/edit/'.base64_encode($page['id']) }}"  title="Edit">
                      <i class="fa fa-edit" ></i>
                    </a>  
              
                  

                  
                  <a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip btn-delets"  href="{{ $module_url_path.'/delete/'.base64_encode($page['id']) }}"  onclick="return confirm_action(this,event,'Do you really want to delete this record ?')" title="Delete">
                    <i class="fa fa-trash" ></i>
                  </a>  
                  
              </td> 
              
              </tr>
                                 
                  @endforeach
                </tbody>
            </table>
          </div>
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


