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
                <i class="fa fa-gift"></i>
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
                <i class="fa fa-plus-square-o"></i>
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
          <div class="form-group">
                  <label class="col-sm-3 col-lg-2 control-label">Code Type <i style="color: red;">*</i></label>
                  <div class="col-sm-9 col-lg-4 controls" >
                      <select class="form-control" name="code_type" placeholder="Validity From" data-rule-required="true"  id="code_type"/>
                        <option value="1" @if($arr_data['code_type']=='1') selected @endif>Promo Code</option>
                        <option value="2" @if($arr_data['code_type']=='2') selected @endif>Promotional Code</option>
                       </select>
                      <span class="help-block">{{ $errors->first('code_type') }}</span>
                  </div>
            </div> 
            
            <div class="form-group">
                  <label class="col-sm-3 col-lg-2 control-label"> Promo Code<i style="color: red;">*</i></label>
                  <div class="col-sm-9 col-lg-4 controls" >
                      
                      <input type="text" class="form-control" name="code" placeholder="Code" data-rule-required="true"  value="@if(isset($arr_data['code'])){{$arr_data['code']}}@endif" id="code"/>

                      <span class="help-block">{{ $errors->first('code') }}</span>
                  </div>
            </div>

           <div class="form-group">
                  <label class="col-sm-3 col-lg-2 control-label">Validity From <i style="color: red;">*</i></label>
                  <div class="col-sm-9 col-lg-4 controls" >
                      <input type="text" class="form-control" name="validity_from" placeholder="Validity From" data-rule-required="true" value="@if(isset($arr_data['validity_from'])){{$arr_data['validity_from']}}@endif" id="validity_from"/>
                      <span class="help-block">{{ $errors->first('validity_from') }}</span>
                  </div>
            </div>
            <div class="form-group">
                  <label class="col-sm-3 col-lg-2 control-label">Validity To <i style="color: red;">*</i></label>
                  <div class="col-sm-9 col-lg-4 controls" >
                      <input type="text" class="form-control" name="validity_to" placeholder="Validity To" data-rule-required="true" value="@if(isset($arr_data['validity_to'])){{$arr_data['validity_to']}}@endif" id="validity_to"/>
                      <span class="help-block">{{ $errors->first('validity_to') }}</span>
                  </div>
            </div>

            <div class="form-group">
                  <label class="col-sm-3 col-lg-2 control-label">Percentage<i style="color: red;">*</i></label>
                  <div class="col-sm-9 col-lg-4 controls" >
                      <input type="text" class="form-control" name="percentage" placeholder="Percentage" data-rule-required="true" value="@if(isset($arr_data['percentage'])){{$arr_data['percentage']}}@endif" id="percentage" onkeypress="return isNumberKey(event)"/>
                      <span class="help-block">{{ $errors->first('percentage') }}</span>
                  </div>
            </div>

            <div class="form-group">
                  <label class="col-sm-3 col-lg-2 control-label"> Max Amount<i style="color: red;">*</i></label>
                  <div class="col-sm-9 col-lg-4 controls" >
                      
                      <input type="text" class="form-control" name="max_amount" placeholder="Max Amount" data-rule-required="true"  value="@if(isset($arr_data['max_amount'])){{$arr_data['max_amount']}}@endif" id="max_amount" onkeypress="return isNumberKey(event)">

                      <span class="help-block">{{ $errors->first('max_amount') }}</span>
                  </div>
            </div>
            

            <div class="form-group">
                  <label class="col-sm-3 col-lg-2 control-label"> Promo Code Usage Limit<i style="color: red;">*</i></label>
                  <div class="col-sm-9 col-lg-4 controls" >
                      
                      <input type="text" class="form-control" name="promo_code_usage_limit" placeholder="Promo Code Usage Limit" data-rule-required="true"  value="{{ isset($arr_data['promo_code_usage_limit']) ? $arr_data['promo_code_usage_limit'] : '' }}" id="promo_code_usage_limit" onkeypress="return isNumberKey(event)"/>

                      <span class="help-block">{{ $errors->first('promo_code_usage_limit') }}</span>
                  </div>
            </div>

            <input type="hidden" name="promo_id" value="{{$promo_id}}" /> 
            <div class="form-group">
              <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2">
               
                 <input type="button" class="btn btn btn-primary" name="save" value="Update" id="btn_submit"/> 
                &nbsp;
                <a class="btn" href="{{ $module_url_path }}">Back</a>
              </div>
            </div>
    
          {!! Form::close() !!}
      </div>
    </div>
  </div>
  
  <!-- END Main Content -->

<script>  
 
 
 $('#validity_from').datepicker({ 
         dateFormat: "yy-mm-dd"
  });
 $('#validity_to').datepicker({ 
         dateFormat: "yy-mm-dd"
  });
 $('#btn_submit').click(function(){
    var flag=0;
    var percentage_reg_exp = /^[0-9]+(?:\.[0-9]+)?$/;
    if($('#validity_from').val() !='')
    {
       if($('#validity_from').val() > $('#validity_to').val())
       {
          $('#validity_from').next().html('Please Enter valid dates');
          flag=1;
       }
    }
    if(!percentage_reg_exp.test($('#percentage').val()))
    {
        $('#percentage').next().html('Please enter valid percentage');
        return false;
        flag=1;
    }
    if($('#percentage').val()>100.00)
    {
        $('#percentage').next().html('Please enter valid percentage');

        return false;
        flag=1; 
       
    }
    if(!percentage_reg_exp.test($('#max_amount').val()))
    {
        $('#max_amount').next().html('Please enter valid amount');
        return false;
        flag=1;
        
    }
    
    if(flag==0)
    {
        $('#validation-form').submit();
    }

 })
</script>     
  

@stop                    
