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
            <li class="active"><i class="fa fa-plus-square-o"></i> {{ $page_title or ''}}</li>
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

            <div class="col-md-10">
              <div id="ajax_op_status">  
              </div>
              <div class="alert alert-danger" style="display:none">
                  <button class="close" data-dismiss="alert">×</button>
                    <strong id="existence_error" ></strong>
              </div>
              
            </div>
            <div class="clearfix" ></div> 

            @if(isset($arr_load['data']) && sizeof($arr_load['data'])>0)
                {{-- ({{isset($arr_load['total'])?$arr_load['total']:'0'}})  --}}11
            @endif 


           {!! Form::open([ 'url' => $module_url_path.'/store',
                                 'method'=>'POST',
                                 'enctype' =>'multipart/form-data',   
                                 'class'=>'form-horizontal', 
                                 'id'=>'validation-form' 
                                ]) !!} 
            <div class="form-group">
                  <label class="col-sm-3 col-lg-2 control-label">Code Type <i style="color: red;">*</i></label>
                  <div class="col-sm-9 col-lg-4 controls" >
                      <select class="form-control" name="code_type" placeholder="Validity From" data-rule-required="true"  id="code_type"/>
                        <option value="1">Promo Code</option>
                        <option value="2">Promotional Code</option>
                       </select>
                      <span class="help-block">{{ $errors->first('code_type') }}</span>
                  </div>
            </div>

            <div class="form-group">
                  <label class="col-sm-3 col-lg-2 control-label">Promo Code<i style="color: red;">*</i></label>
                  <div class="col-sm-9 col-lg-4 controls" >
                      
                      <input type="text" class="form-control" name="code" id="code" placeholder="Code" data-rule-required="true"  value="" id="code"/>
                      <span class="help-block">{{ $errors->first('code') }}</span>
                  </div>
                  <div class="col-sm-9 col-lg-4 controls" >
                    <input type="button" class="btn btn btn-pink" value="Generate Code" id="generate_code" onclick="getCode()" />

                  </div>
            </div>
                                  
           {{ csrf_field() }}
            <div class="form-group">
                  <label class="col-sm-3 col-lg-2 control-label">Validity From <i style="color: red;">*</i></label>
                  <div class="col-sm-9 col-lg-4 controls" >
                      <input type="text" class="form-control" name="validity_from" placeholder="Validity From" data-rule-required="true" value="{{ old('validity_from') }}" id="validity_from"/>
                      <span class="help-block">{{ $errors->first('validity_from') }}</span>
                  </div>
            </div>
            <div class="form-group">
                  <label class="col-sm-3 col-lg-2 control-label">Validity To <i style="color: red;">*</i></label>
                  <div class="col-sm-9 col-lg-4 controls" >
                      <input type="text" class="form-control" name="validity_to" placeholder="Validity To" data-rule-required="true" value="{{ old('validity_to') }}" id="validity_to"/>
                      <span class="help-block">{{ $errors->first('validity_to') }}</span>
                  </div>
            </div>

            <div class="form-group">
                  <label class="col-sm-3 col-lg-2 control-label">Percentage<i style="color: red;">*</i></label>
                  <div class="col-sm-9 col-lg-4 controls" >
                      <input type="text" class="form-control" name="percentage" placeholder="Percentage" data-rule-required="true" data-rule-maxlength="5" data-rule-minlength= "1" onkeypress="return isNumberKey(event)" value="{{ old('percentage') }}" id="percentage"/>
                      <span class="help-block">{{ $errors->first('percentage') }}</span>
                  </div>
            </div>

            <div class="form-group">
                  <label class="col-sm-3 col-lg-2 control-label"> Max Amount<i style="color: red;">*</i></label>
                  <div class="col-sm-9 col-lg-4 controls" >
                      
                      <input type="text" class="form-control" name="max_amount" placeholder="Max Amount" data-rule-required="true"  value="{{ old('max_amount') }}" id="max_amount" onkeypress="return isNumberKey(event)"/>

                      <span class="help-block">{{ $errors->first('max_amount') }}</span>
                  </div>
            </div>

            <div class="form-group">
                  <label class="col-sm-3 col-lg-2 control-label"> Promo Code Usage Limit<i style="color: red;">*</i></label>
                  <div class="col-sm-9 col-lg-4 controls" >
                      
                      <input type="text" class="form-control" name="promo_code_usage_limit" placeholder="Promo Code Usage Limit" data-rule-required="true"  value="{{ old('promo_code_usage_limit') }}" id="promo_code_usage_limit" onkeypress="return isNumberKey(event)"/>

                      <span class="help-block">{{ $errors->first('promo_code_usage_limit') }}</span>
                  </div>
            </div>

            <div class="form-group">
              <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2">
               
                 <input type="button" class="btn btn btn-primary" name="save" value="Save" id="btn_submit"/> 
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

   $('#validation-form').valid();
   
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
    else
    {
      if($('#percentage').val()>100.00)
      {
        $('#percentage').next().html('Please enter valid percentage');
        return false;
        flag=1;
       
      }
    }
    if(!percentage_reg_exp.test($('#max_amount').val()))
    {
        $('#max_amount').next().html('Please enter valid amount');
        return false;
        flag=1;
        
    }
    if($("#code").val()!="")
    {
        var code = $("#code").val();
        var _token       = "<?php echo csrf_token(); ?>";
        $.ajax({
                  url  : "{{$module_url_path.'/code_existence'}}",
                  data : { 
                            'code' : code,
                            "_token": _token
                          },
                  type : 'post', 
                  async: false,
                  success:function(response){ 
                    if(response!="success")
                    {
                        flag = 1;
                        $(".alert-danger").css("display","block");
                        $("#existence_error").html("This Code is already in use.");

                    }
                }
          }); 
    }
    if(flag==0)
    {
        $('#validation-form').submit();
    }

 });
 function getCode()
 {
      var shuffledWord="";
      var word = "ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890";
      word = word.split('');
      while (word.length > 0) 
      {
         shuffledWord +=  word.splice(word.length * Math.random() << 0, 1);
      }
      code = shuffledWord.substr(0,6);
      $("#code").val(code);
      
 }

</script>     
  

@stop                    
