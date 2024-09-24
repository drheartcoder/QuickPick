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
                <i class="fa fa-users"></i>
                <a href="{{ $module_url_path }}">{{ $module_title or ''}}</a>
            </li>   
            <span class="divider">
                <i class="fa fa-angle-right"></i>
            </span>
            <?php
                
                $enc_user_id = isset($arr_data['id']) ? base64_encode($arr_data['id']) : 0;
                $edit_page_url = 'javascript:void(0);';
                
                if($enc_user_id!='' || $enc_user_id!=0)
                {
                    $edit_page_url = $module_url_path.'/edit/'.$enc_user_id;
                }
            ?>
            <li>
                <i class="fa fa-edit"></i>
                <a href="{{ $edit_page_url }}">Edit User Details</a>
            </li>   
            <span class="divider">
                <i class="fa fa-angle-right"></i>
            </span>

            <li class="active"><i class="fa fa-unlock"></i> Reset User Password</li>
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
            
            <div class="form-group" style="">
                  <label class="col-sm-3 col-lg-2 control-label">Make reset password mandatory for this customer</label>
                  <div class="col-sm-9 col-lg-4 controls" >
                      <div class="check-box">
                            <input type="checkbox" class="filled-in" name="reset_password_mandatory" id="reset_password_mandatory" />
                            <label for="reset_password_mandatory"></label>
                        </div>
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
      var generate_password = password.generate();
      $('#password').val(generate_password);
      $('#confirm_password').val(generate_password);
  }
  
  var password = {
    // Add another object to the rules array here to add rules.
    // They are executed from top to bottom, with callbacks in between if defined.
    rules: [

        //Take a combination of 12 letters and numbers, both lower and upper case.
        {
            characters: 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890',
            max: 9
        },
        //Take 4 special characters, use the callback to shuffle the resulting 16 character string
        {
            characters: '$@$!%*?&',
            max: 3,
            callback: function (s) {
                var a = s.split(""),
                    n = a.length;

                for (var i = n - 1; i > 0; i--) {
                    var j = Math.floor(Math.random() * (i + 1));
                    var tmp = a[i];
                    a[i] = a[j];
                    a[j] = tmp;
                }
                return a.join("");
            }
        }
    ],
    generate: function () {
        var g = '';

        $.each(password.rules, function (k, v) {
            var m = v.max;
            for (var i = 1; i <= m; i++) {
                g = g + v.characters[Math.floor(Math.random() * (v.characters.length))];
            }
            if (v.callback) {
                g = v.callback(g);
            }
        });
        return g;
    }
}


</script>
@stop                    
