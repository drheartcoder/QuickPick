    @extends('admin.layout.master')                


    @section('main_content')

    
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
            </span>
            <li>
                <i class="fa fa-car"></i>
                <a href="{{ $module_url_path }}">{{ $module_title or ''}}</a>
            </li>   
            <span class="divider">
                <i class="fa fa-angle-right"></i>
            </span>
            <li class="active"><i class="fa fa-edit"></i> {{ $page_title or ''}}</li>
        </ul>
    </div>
    <!-- END Breadcrumb -->



    <!-- BEGIN Main Content -->
    <div class="row">
      <div class="col-md-12">
          <div class="box {{ $theme_color }}">
            <div class="box-title">
              <h3>
                <i class="fa fa-edit"></i>
                {{ isset($page_title)?$page_title:"" }}
              </h3>
              <div class="box-tool">
                <a data-action="collapse" href="#"></a>
                <a data-action="close" href="#"></a>
              </div>
            </div>
            <div class="box-content">

          @include('admin.layout._operation_status')  
           {!! Form::open([ 'url' => $module_url_path.'/update',
                                 'method'=>'POST',
                                 'enctype' =>'multipart/form-data',   
                                 'class'=>'form-horizontal', 
                                 'id'=>'validation-form' 
                                ]) !!} 

           {{ csrf_field() }}
 
            <div class="form-group" style="margin-top: 25px;">
                  <label class="col-sm-3 col-lg-2 control-label">Package Type<i style="color: red;">*</i></label>
                  <div class="col-sm-9 col-lg-4 controls" >
                      <input type="text" class="form-control" name="package_type" maxlength="40" placeholder="Enter Package Type" data-rule-required="true" value="{{ isset($arr_data['name']) ? $arr_data['name'] : '' }}"  />
                      <span class="help-block">{{ $errors->first('package_type') }}</span>
                  </div>
            </div>
            
             <div class="form-group" style="">
              <label class="col-sm-3 col-lg-2 control-label">Is Special Package Type <i style="color: red;">*</i></label>
              <div class="col-sm-5 col-lg-2 controls" >
                    <input type="radio" 
                           name="is_special_type" 
                           @if(isset($arr_data['is_special_type']) && $arr_data['is_special_type'] == '1') 
                            checked="" 
                           @endif
                           id="yes_is_special_type" 
                           value="1"/> YES
                <label for="filled-in-box"></label>
              </div>
              <div class="col-sm-5 col-lg-2 controls" >
                    <input type="radio" 
                           name="is_special_type" 
                           @if(isset($arr_data['is_special_type']) && $arr_data['is_special_type'] == '0') 
                            checked="" 
                           @endif
                           id="no_is_special_type" value="0"/> NO
                <label for="filled-in-box"></label>
              </div> 
              <span class="help-block">{{ $errors->first('is_special_type') }}</span>
            </div><br>
            <input type="hidden" name="enc_id" value="{{ isset($enc_id) ? $enc_id : 0 }}">
            <div class="form-group">
              <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2">
               
                {!! Form::submit('Update',['class'=>'btn btn btn-primary','value'=>'true'])!!}
                &nbsp;
                <a class="btn" href="{{ $module_url_path }}">Back</a>
              </div>
            </div>
    
          {!! Form::close() !!}
      </div>
    </div>
  </div>
  
  <!-- END Main Content -->


@stop                    
