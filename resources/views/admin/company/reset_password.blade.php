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
           {!! Form::open([ 'url' => $module_url_path.'/process_reset_password',
                                 'method'=>'POST',
                                 'enctype' =>'multipart/form-data',   
                                 'class'=>'form-horizontal', 
                                 'id'=>'validation-form' 
                                ]) !!} 

           {{ csrf_field() }}
            <input type="hidden" name="enc_id" value="{{ isset($arr_data['id']) ? base64_encode($arr_data['id']) : '0' }}">
            <div class="form-group" style="">
                  <label class="col-sm-3 col-lg-2 control-label">Password<i style="color: red;">*</i></label>
                  <div class="col-sm-9 col-lg-4 controls" >
                      <input type="text" class="form-control" name="password" id="password" data-rule-pattern="(^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[$@$!%*?&])[A-Za-z\d$@$!%*?&]{8,})" data-msg-pattern="Min. length should be 8 containing uppercase letter, lowercase letter, number and special character." placeholder="Enter Password" data-rule-required="true" value="{{ old('password') }}" minlength="8" />
                      <span class="help-block">{{ $errors->first('password') }}</span>
                  </div>
                  <div class="col-sm-9 col-lg-4 controls" >
                    <input type="button" class="btn btn btn-pink" value="Generate Password" id="generate_password" onclick="generatePassword()" />
                  </div>
            </div>
            
            <div class="form-group" style="">
                  <label class="col-sm-3 col-lg-2 control-label">Confirm Password<i style="color: red;">*</i></label>
                  <div class="col-sm-9 col-lg-4 controls" >
                      <input type="text" class="form-control" id="confirm_password" name="confirm_password" placeholder="Enter Confirm Password" data-rule-required="true" data-rule-equalto="#password" value="{{ old('confirm_password') }}" minlength="6" />
                      <span class="help-block">{{ $errors->first('confirm_password') }}</span>
                  </div>
            </div>
            
            <div class="form-group">
              <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2">
               
                {!! Form::submit('Save',['class'=>'btn btn btn-primary','value'=>'true'])!!}
                &nbsp;
                <a class="btn" href="{{ $module_url_path }}">Back</a>
              </div>
            </div>
    
          {!! Form::close() !!}
      </div>
    </div>
  </div>
  
  <!-- END Main Content -->

<script type="text/javascript">
  function  generatePassword() 
  {
      var length = 8;
      var chars = "abcdefghijklmnopqrstuvwxyz?=.*)(?=.*[$@$!%*?&]ABCDEFGHIJKLMNOP1234567890";
      var pass = "";
      for (var x = 0; x < length; x++) {
          var i = Math.floor(Math.random() * chars.length);
          pass += chars.charAt(i);
      }
      
      if(pass!='')
      {
        $('#password').val(pass);
        $('#confirm_password').val(pass);
      }      
  }
</script>
@stop                    
