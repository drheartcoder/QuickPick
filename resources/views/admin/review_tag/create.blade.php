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
                <i class="fa fa-star"></i>
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
           {!! Form::open([ 'url' => $module_url_path.'/store',
                                 'method'=>'POST',
                                 'enctype' =>'multipart/form-data',   
                                 'class'=>'form-horizontal', 
                                 'id'=>'validation-form' 
                                ]) !!} 

           {{ csrf_field() }}

            <div class="form-group" style="">
                  <label class="col-sm-3 col-lg-2 control-label">Review Tag<i style="color: red;">*</i></label>
                  <div class="col-sm-9 col-lg-4 controls" >
                      <input type="text" class="form-control" name="tag_name" id="tag_name" placeholder="Enter Review Tag" maxlength="40" value="{{ old('tag_name') }}" />
                      <span id="err_tag_name" class="help-block">{{ $errors->first('tag_name') }}</span>                      
                  </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 col-lg-2 control-label"> Review Image <i style="color: red;">*</i></label>
                <div class="col-sm-9 col-lg-10 controls">
                   <div class="fileupload fileupload-new" data-provides="fileupload">
                      <div class="fileupload-new img-thumbnail" style="width: 200px; height: 150px;">
                          
                      </div>
                      <div class="fileupload-preview fileupload-exists img-thumbnail" style="max-width: 200px; max-height: 150px; line-height: 20px;"></div>
                      <div>
                         <span class="btn btn-default btn-file">
                             <span class="fileupload-new" >Select Image</span> 
                             <span class="fileupload-exists">Change</span>                             
                           {{--   {!! Form::file('review_image',['id'=>'image_proof','class'=>'file-input validate-image', 'data-rule-required'=>'true']) !!} --}}

                             <input id="review_tag_image" class="file-input validate-image" name="review_image" type="file">

                         </span> 
                         <a href="#" class="btn btn-default fileupload-exists" data-dismiss="fileupload">Remove</a>                         
                      </div>
                   </div>

                   <i class="red"> {!! image_validate_note(250,250) !!} 

                    <div class="error" id="err_fileUpload"></div>
                   </i>
                   

                </div>
            </div>
            
            <div class="form-group">
              <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2">
               
                {!! Form::submit('Save',['class'=>'btn btn btn-primary', 'id'=>'submit_forms','value'=>'true'])!!}
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

   $('#submit_forms').on('click',function(){
      var tag_name       = $('#tag_name').val();
      var fileUpload     = $('#review_tag_image').val();;
      var flag = 0;

      //alert(tag_name, fileUpload);
      var pattern = /^[A-z ]+$/;

        if($.trim(tag_name) == "")
        {
            $('#tag_name').val('');
            $('#err_tag_name').html('Please Enter Tag Name');
            $('#tag_name').focus();
            flag = 1;
           //// return false;
        } else{
            if (!pattern.test(tag_name)) 
            {
                $('#err_tag_name').html('Please Enter Only Alphabate');
                flag = 1;
             //   return false;
            }
        }
       
        if($.trim(fileUpload) == "")
        {
           $('#fileuploads').val('');
           $('#err_fileUpload').html('Please select Review Image');
           $('#fileuploads').focus();
           //return false;
           flag = 1;
        }else {
            var iSize = $("#fileuploads")[0].files[0].size;
            if (!fileUpload.match(/(?:jpg|png|jpeg)$/)){
            $('#err_fileUpload').html('Please Select only jpg | jpeg | png Image');
            $('#fileuploads').focus();
            flag = 1;               
            }
        }

        if(flag == 1)
        {
              return false;    
        }
        else
        {
              return true;
        }


   }); 

   $(document).on("change",".validate-image", function()
    {            
        var file=this.files;
        validateImage(this.files, 250, 250);
    });

  /* $(document).on("change",".validate-image", function()
    {            
        var files = this.files;
        var fileUpload = this.files;
        var flag = 0;
        // validateImage(this.files, 250, 250);

        if($.trim(fileUpload) == "")
        {
           $('#fileuploads').val('');
           $('#err_fileUpload').html('Please select Review Image');
           $('#fileuploads').focus();
           //return false;
           flag = 1;
        }else {
            var iSize = $("#fileuploads")[0].files[0].size;
            if (!fileUpload.match(/(?:jpg|png|jpeg)$/)){
            $('#err_fileUpload').html('Please Select only jpg | jpeg | png Image');
            $('#fileuploads').focus();
            flag = 1;               
            }
        }

    });
*/

   
</script>

@stop                    
