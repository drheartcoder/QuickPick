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
                <i class="fa fa-wrench"></i>
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
                    <h3><i class="fa fa-wrench"></i> {{ isset($page_title)?$page_title:"" }}</h3>
                    <div class="box-tool">
                    </div>
                </div>
                <div class="box-content">
                    @include('admin.layout._operation_status')
                    
                    {!! Form::open([ 'url' => $module_url_path.'/update',
                                 'method'=>'POST',   
                                 'class'=>'form-horizontal', 
                                 'id'=>'validation-form' ,
                                 'enctype'=>'multipart/form-data'
                                ]) !!}

                    
                    <div class="form-group"> <label class="col-sm-3 col-lg-4 control-label"></label></div>
                       
                       <input type="hidden" name="enc_id" value="{{isset($arr_data['id'])?base64_encode($arr_data['id']):0}}">
                       

                        <div class="form-group">
                            <label class="col-sm-3 col-lg-2 control-label">Referrel Bonus Points<i class="red">*</i></label>
                            <div class="col-sm-9 col-lg-4 controls">
                                <input name="referral_points" class="form-control" data-rule-required="true" placeholder="Enter Referrel Bonus Points" data-rule-maxlength="5" data-rule-minlength= "1" onkeypress="return isNumberKey(event)" id="referral_points" type="text" value="{{ isset($arr_data['referral_points']) ? $arr_data['referral_points'] : ''}}" 
                                
                                />

                                <span class='help-block'>{{ $errors->first('referral_points') }}</span>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="col-sm-3 col-lg-2 control-label">Points Per USD <i class="red">*</i></label>
                            <div class="col-sm-9 col-lg-4 controls">
                                <input name="referral_points_price" class="form-control" placeholder="Enter Points Per USD" data-rule-required="true" data-rule-maxlength="5" data-rule-minlength= "1" onkeypress="return isNumberKey(event)" id="referral_points_price" type="text" value="{{ isset($arr_data['referral_points_price']) ? $arr_data['referral_points_price'] : ''}}" />

                                <span class='help-block'>{{ $errors->first('referral_points_price') }}</span>
                                {{-- <i class="red"> Please Enter Price Per Bonus Points </i> --}}
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