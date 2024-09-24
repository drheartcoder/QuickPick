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
         <i class="fa fa-home">
         </i>
         <a href="{{ url($admin_panel_slug.'/dashboard') }}">Dashboard
         </a>
      </li>
      <span class="divider">
      <i class="fa fa-angle-right">
      </i>
      <i class="fa {{$module_icon}}">
      </i>
      </span> 
      <li class="active">  {{ isset($page_title)?$page_title:"" }}
      </li>
   </ul>
</div>
<!-- END Breadcrumb -->
<!-- BEGIN Main Content -->
<div class="row">
<div class="col-md-12">
   <div class="box {{ $theme_color }}">
      <div class="box-title">
         <h3>
            <i class="fa {{$module_icon}}">
            </i>{{ isset($page_title)?$page_title:"" }} 
         </h3>
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

         <input type="hidden" name="enc_id" value="{{isset($arr_data['id']) ? base64_encode($arr_data['id']) : 0}}">
         <div class="form-group">
            <label class="col-sm-3 col-lg-2 control-label">Profile Image  <i class="red">*</i> </label>
            <div class="col-sm-9 col-lg-10 controls">
               <div class="fileupload fileupload-new" data-provides="fileupload">
                  <div class="fileupload-new img-thumbnail" style="width: 200px; height: 150px;">
                     @if(isset($arr_data['profile_image']) && !empty($arr_data['profile_image']))
                     <img src="{{$profile_image_public_img_path.$arr_data['profile_image'] }}">
                     @else
                     <img src="{{url('/').'/uploads/default.png' }}">
                     @endif
                  </div>
                  <div class="fileupload-preview fileupload-exists img-thumbnail" style="max-width: 200px; max-height: 150px; line-height: 20px;"></div>
                  <div>
                     <span class="btn btn-default btn-file" style="height:32px;">
                     <span class="fileupload-new">Select Image</span>
                     <span class="fileupload-exists">Change</span>
                     <input type="file"  data-validation-allowing="jpg, png, gif" class="file-input news-image validate-image" name="image" id="image"  /><br>
                     <input type="hidden" class="file-input " name="oldimage" id="oldimage"  
                        value="{{ $arr_data['profile_image'] }}"/>
                     </span>
                     <a href="#" id="remove" class="btn btn-default fileupload-exists" data-dismiss="fileupload">Remove</a>
                  </div>
                  <i class="red"> {!! image_validate_note(250,250) !!} </i>
                  <span for="image" id="err-image" class="help-block">{{ $errors->first(' image') }}</span>
               </div>
            </div>
            <div class="clearfix"></div>
            <div class="col-sm-6 col-lg-5 control-label help-block-red" style="color:#b94a48;" id="err_logo"></div>
            <br/>
            <div class="col-sm-6 col-lg-5 control-label help-block-green" style="color:#468847;" id="success_logo"></div>
         </div>
         <div class="form-group">
            <label class="col-sm-3 col-lg-2 control-label">First Name
            <i class="red">*
            </i>
            </label>
            <div class="col-sm-9 col-lg-4 controls">
               {!! Form::text('first_name',$arr_data['first_name'],['class'=>'form-control','data-rule-required'=>'true','data-rule-lettersonly'=>'true','data-rule-maxlength'=>'255', 'placeholder'=>'First Name']) !!}
               <span class='help-block'>{{ $errors->first('first_name') }}
               </span>
            </div>
         </div>
         <div class="form-group">
            <label class="col-sm-3 col-lg-2 control-label">Last Name
            <i class="red">*
            </i>
            </label>
            <div class="col-sm-9 col-lg-4 controls">
               {!! Form::text('last_name',$arr_data['last_name'],['class'=>'form-control','data-rule-required'=>'true','data-rule-lettersonly'=>'true','data-rule-maxlength'=>'255', 'placeholder'=>'Last Name']) !!}
               <span class='help-block'>{{ $errors->first('last_name') }}
               </span>
            </div>
         </div>
         
         {{-- <div class="form-group">
            <label class="col-sm-3 col-lg-2 control-label">Email
            <i class="red">*
            </i>
            </label>
            <div class="col-sm-9 col-lg-4 controls">
               {!! Form::text('email',$arr_data['email'],['class'=>'form-control', 'data-rule-required'=>'true','data-rule-email'=>'true','data-rule-maxlength'=>'255','placeholder'=>'Email']) !!}
               <span class='help-block'>{{ $errors->first('email') }}
               </span>
            </div>
         </div> --}}

         <div class="form-group">
            <label class="col-sm-3 col-lg-2 control-label">Mobile Number
            <i class="red">*
            </i>
            </label>
            <div class="col-sm-9 col-lg-4 controls">
               {!! Form::text('phone',$arr_data['mobile_no'],['class'=>'form-control', 'data-rule-required'=>'true','data-rule-number'=>'true','data-rule-maxlength'=>'16','data-rule-minlength'=>'7','placeholder'=>'Mobile Number']) !!}
               <span class='help-block'>{{ $errors->first('phone') }}
               </span>
            </div>
         </div>

         <div class="form-group">
            <label class="col-sm-3 col-lg-2 control-label">Address
            <i class="red">*
            </i>
            </label>
            <div class="col-sm-9 col-lg-4 controls">
               {!! Form::textarea('address',$arr_data['address'],['class'=>'form-control', 'data-rule-required'=>'true','placeholder'=>'Address','rows'=>'4','cols'=>'50']) !!}
               <span class='help-block'>{{ $errors->first('address') }}
               </span>
            </div>
         </div>

         {{-- 
         <div class="form-group">
            <label class="col-sm-3 col-lg-2 control-label">Fax
            <i class="red">*
            </i>
            </label>
            <div class="col-sm-9 col-lg-4 controls">
               {!! Form::text('fax',$arr_data['fax'],['class'=>'form-control', 'data-rule-required'=>'true','data-rule-maxlength'=>'100','placeholder'=>'Fax']) !!}
               <span class='help-block'>{{ $errors->first('fax') }}
               </span>
            </div>
         </div>
         <div class="form-group">
            <label class="col-sm-3 col-lg-2 control-label">Address
            <i class="red">*
            </i>
            </label>
            <div class="col-sm-9 col-lg-4 controls">
               {!! Form::textarea('address',$arr_data['address'],['class'=>'form-control', 'data-rule-required'=>'true','placeholder'=>'Address','rows'=>'4','cols'=>'50']) !!}
               <span class='help-block'>{{ $errors->first('address') }}
               </span>
            </div>
         </div>
         --}}
         {{-- 
         <div class="form-group">
            <label class="col-sm-3 col-lg-2 control-label">Old Password
            </label>
            <div class="col-sm-9 col-lg-4 controls">
               {!! Form::password('old_password',['class'=>'form-control','data-rule-maxlength'=>'255', 'placeholder'=>'Old Password']) !!}
               <span class='help-block'>{{ $errors->first('old_password') }}
               </span>
            </div>
         </div>
         <div class="form-group">
            <label class="col-sm-3 col-lg-2 control-label">New Password
            </label>
            <div class="col-sm-9 col-lg-4 controls">
               {!! Form::password('new_password',['class'=>'form-control',
               'data-rule-maxlength'=>'40',
               'id'=>'new_password',
               'data-rule-minlength'=>'6',
               'data-rule-pattern'=>'^(?=.*[a-zA-Z])(?=.*\d)(?=.*[!@#$%^&*()_+])[A-Za-z\d][A-Za-z\d!@#$%^&*()_+]{6,40}$', 
               'placeholder'=>'New Password'
               ]) !!}
               <span class='help-block'>{{ $errors->first('new_password') }}
               </span>
            </div>
         </div>
         <div class="form-group">
            <label class="col-sm-3 col-lg-2 control-label">Confirm Password
            </label>
            <div class="col-sm-9 col-lg-4 controls">
               {!! Form::password('new_password_confirmation',['class'=>'form-control','data-rule-maxlength'=>'40','data-rule-equalto'=>'#new_password', 'placeholder'=>'Confirm Password']) !!}
               <span class='help-block'>{{ $errors->first('confirm_password') }}
               </span>
            </div>
         </div>
         --}}    
         <div class="form-group">
            <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2">
               {!! Form::submit('Update',['class'=>'btn btn btn-primary','value'=>'true'])!!}
               <a href="{{ url($admin_panel_slug.'/dashboard') }}" class="btn">Back</a> 
            </div>
         </div>
         {!! Form::close() !!}
      </div>
   </div>
</div>
{{--  <script type="text/javascript" src="{{ url('')}}/front-assets/js/custom_jquery.validate.js"></script> --}}
<script type="text/javascript">

   $(document).on("change",".validate-image", function()
    {            
        var file=this.files;
        validateImage(this.files, 250,250);
    });
   
  
   
   
</script>
<!-- END Main Content --> 
@endsection

