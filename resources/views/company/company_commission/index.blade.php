@extends('company.layout.master')    

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
                <a href="{{ url($company_panel_slug.'/dashboard') }}">Dashboard</a>
            </li>
            <span class="divider">
                <i class="fa fa-angle-right"></i>
                <i class="fa fa-percent"></i>
            </span> 
            <li class="active">  {{ isset($page_title)?$page_title:"" }}</li>
        </ul>
    </div>
    <!-- END Breadcrumb -->

    <!-- BEGIN Main Content -->
    
    
    <div class="row">
        <div class="col-md-12">
            <div class="box  {{ $theme_color }}">
                <div class="box-title">
                    <h3><i class="fa fa-percent"></i> {{ isset($page_title)?$page_title:"" }}</h3>
                    <div class="box-tool">
                    </div>
                </div>
                <div class="box-content">
                    @include('company.layout._operation_status')
                    
                    {!! Form::open([ 'url' => $module_url_path.'/update',
                                 'method'=>'POST',   
                                 'class'=>'form-horizontal', 
                                 'id'=>'validation-form' ,
                                 'enctype'=>'multipart/form-data'
                                ]) !!}


                    <div class="form-group"> <label class="col-sm-3 col-lg-4 control-label"></label></div>
                       
                       <input type="hidden" name="enc_id" value="{{isset($arr_data['id'])?base64_encode($arr_data['id']):0}}">
                       

                        <div class="form-group">
                            <label class="col-sm-3 col-lg-2 control-label">Commssion % to drivers<i class="red">*</i></label>
                            <div class="col-sm-9 col-lg-4 controls">
                                
                                <div class="input-group">
                                    <input type="text" class="form-control" name="driver_percentage" id="driver_percentage"
                                    placeholder="Commssion % to drivers" value="{{isset($arr_data['driver_percentage'])?$arr_data['driver_percentage']:'' }}"
                                    pattern="^(?!0+$)\d{1,2}(\.\d{0,2})?$" data-native-error="Please enter valid input" required=''
                                     onkeypress="return isNumberKey(event)"
                                     maxlength=5 >
                                    <span class="input-group-addon" id="perc-addon" ><i class="fa fa-percent"></i></span>
                                </div>
                                <span class='help-block'>{{ $errors->first('driver_percentage') }}</span>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2">
                                {!! Form::submit('Update',['class'=>'btn btn btn-primary','value'=>'true'])!!}
                            </div>
                       </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    
    <!-- END Main Content --> 
@endsection