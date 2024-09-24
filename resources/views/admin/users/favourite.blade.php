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
                <i class="fa fa-users"></i>
                <a href="{{ $module_url_path }}">{{ $module_title or ''}}</a>
      </span>

      <span class="divider">
      <i class="fa fa-angle-right"></i>
      <i class="fa fa-heart"></i>      
      </span>      
      <li class="active">{{ $page_title or ''}}</li>
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

            <form name="frm_manage" id="frm_manage" method="POST" class="form-horizontal" action="{{$module_url_path}}/multi_action">
               {{ csrf_field() }}
               
               <div class="btn-toolbar pull-right clearfix">
                  {{-- <div class="btn-group">
                     <a href="{{ $module_url_path.'/create'}}" class="btn btn-primary btn-add-new-records"  title="Add CMS">Add skill</a>                      
                  </div> --}}                   
                  <div class="btn-group"> 
                     <a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip" 
                        title="Refresh" 
                        href="{{ $module_url_path }}"
                        style="text-decoration:none;">
                     <i class="fa fa-repeat"></i>
                     </a> 
                  </div>
               </div>
               <br/>
               <br/>
               <div class="clearfix"></div>
               <div class="table-responsive" style="border:0">
                  <input type="hidden" name="multi_action" value="" />
                  <table class="table table-advance"  id="table1" >
                     <thead>
                        <tr>
                           <th>Username</th>
                           <th>Package</th>
                           <th>Status</th>
                        </tr>
                     </thead>
                     <tbody>
                      @if(isset($arr_favourite) && sizeof($arr_favourite)>0)
                      @foreach($arr_favourite as $result)
                          <tr>
                             <td>{{$arr_data['first_name']." ".$arr_data['last_name']}}</td>
                             <td>{{$result['package_details']['title']}}</td>
                             
                             <td> 
                                <a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip" href="{{$package_url_path.'/view/'.base64_encode($result['package_id'])}}"  title="View Package">
                                <i class="fa fa-eye" ></i>
                                </a>  
                                &nbsp;  
                             </td>
                          </tr>
                       @endforeach   
                      @endif    
                     </tbody>
                  </table>
               </div>
            </form>
         </div>
      </div>
   </div>
</div>
<!-- END Main Content -->
<script type="text/javascript" src="{{ url('/assets/data-tables/latest') }}/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="{{ url('/assets/data-tables/latest') }}/dataTables.bootstrap.min.js"></script>

@stop