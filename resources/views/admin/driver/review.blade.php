@extends('admin.layout.master')                
@section('main_content')
<!-- BEGIN Page Title -->
<link rel="stylesheet" type="text/css" href="{{ url('/') }}/assets/data-tables/latest/dataTables.bootstrap.min.css">
<div class="page-title">
  <div>
  </div>
</div>

<?php

    $first_name      = isset($arr_user['first_name']) && ($arr_user['first_name']!= '') ? $arr_user['first_name']:"N/A";

    $last_name       = isset($arr_user['last_name']) && ($arr_user['last_name']!= '') ? $arr_user['last_name']:"";

  ?>

<div id="breadcrumbs">
  <ul class="breadcrumb">
    <li>
      <i class="fa fa-home"></i>
      <a href="{{ url($admin_panel_slug.'/dashboard') }}"> Dashboard </a>
    </li>

    <span class="divider">
      <i class="fa fa-angle-right"></i>
      <i class="fa fa-car"></i>   
    </span>
    <li>
      <a href="{{ $module_url_path }}"> Driver </a>
    </li>
    <span class="divider">
      <i class="fa fa-angle-right"></i>
      <i class="fa fa-star"></i>   
    </span> 
    <li class="active"> Reviews </li> 
   
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
          {{ $page_title or ''}}
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
{{-- 
        <div class="col-md-10">
          <div id="ajax_op_status"></div>
          <div class="alert alert-danger" id="no_select" style="display:none;"></div>
          <div class="alert alert-warning" id="warning_msg" style="display:none;"></div>
        </div>
        <div class="btn-toolbar pull-right clearfix">
          <div class="btn-group"> 
            <a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip refrash-btns" 
               title="Refresh" 
               href="{{ $module_url_path.'/review/'.base64_encode($user_id) }}"
               style="text-decoration:none;">
              <i class="fa fa-repeat"></i>
            </a> 
          </div>
        </div>
        <br/>
        <br/> --}}
        
        <div class="col-md-10 col-md-10 col-lg-10">
            <div class="alert alert-danger" id="no_select" style="display:none;"></div>
            <div class="alert alert-warning" id="warning_msg" style="display:none;"></div>
            <h2> Driver Name : &nbsp;{{ $first_name }} {{ $last_name }}   </h2>
          </div>
          <div class="col-md-2 col-md-2 col-lg-2">
            <div class="btn-toolbar pull-right ">
              <div class="btn-group"> 
                <a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip refrash-btns"  
                title="Refresh" 
                href="{{ $module_url_path.'/review/'.base64_encode($user_id) }}"
                style="text-decoration:none;">
                <i class="fa fa-repeat"></i>
              </a> 
            </div>
          </div>
        </div>
        <div class="clearfix"></div>


        <div class="table-responsive" style="border:0">
          <table class="table table-advance"  id="table_module" >
            <thead>
            
              <tr>
                <th>   Users Name </th> 
                <th>   Rating     </th> 
                <th>   Message    </th>
                <th>   Review Badge Name </th> 
                <th>   Action     </th>
              </tr>
            </thead>
            <tbody>
              @if(isset($arr_data) && sizeof($arr_data)>0)
              @foreach($arr_data as $value)
              <tr>
              
                
                {{-- <td>  
                    @if(isset($value['rating_tag_details']['review_image']) && !empty($value['rating_tag_details']['review_image']))
                      <img style="height: 100px; width: 100px;" src={{ $review_tag_public_path.$value['rating_tag_details']['review_image']}} alt="" /> 
                    @else
                       <img style="height: 100px; width: 100px;" src={{ url("uploads/default-review.png")}} alt="" />
                    @endif 
                </td> --}}
                <td> {{ $value['from_user_details']['first_name'] or ''}} {{ $value['from_user_details']['last_name'] or '' }} </td>

                <td>
                <?php
                  $rating = isset($value['rating'])?intval($value['rating']):0;
                ?>
                @for($i=1;$i<=5;$i++)
                    @if($i<=$rating)
                      <span title="{{$i}}" style="color:orange" class="fa fa-star"></span>
                    @else
                      <span title="{{$i}}" class="fa fa-star"></span>
                    @endif
                @endfor
                </td> 

                <td> @if(isset($value['rating_msg']) && !empty($value['rating_msg']))
                        @if(strlen($value['rating_msg']) >50 )
                          {{ substr($value['rating_msg'],0,50) }}..
                        @else
                          {{ $value['rating_msg'] }}        
                        @endif  
                     @endif  
                </td> 
                <td>  {{$value['rating_tag_details']['tag_name'] or ''}}  </td>
                <td>
                  <a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip call_loader btn-delets" href="{{ $module_url_path.'/review/view/'.base64_encode($value['id'])}}" title="" data-original-title="Edit">
                          <i class="fa fa-eye"></i>
                  </a>
                </td>    
              </tr>
              @endforeach
              @endif
            </tbody>
          </table>
        </div>
        {!! Form::close() !!}
        <div align ="center"><a  class="btn  btn-to-success" href="{{$module_url_path}}" title="" data-original-title="Back">
            Back
        </a></div>
      </div>
    </div>
  </div>

<!-- END Main Content -->

<script type="text/javascript">

  $(document).ready(function() {
        $('#table_module').DataTable();
  });

</script>

@stop