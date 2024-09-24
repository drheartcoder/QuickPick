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

            <div class="col-md-10">
              <div id="ajax_op_status">  
              </div>
              <div class="alert alert-danger" style="display:none">
                  <button class="close" data-dismiss="alert">×</button>
                    <strong id="existence_error" ></strong>
              </div>
              
            </div>
            <div class="clearfix" ></div> 

           {!! Form::open([ 'url' => $module_url_path.'/update',
                                 'method'=>'POST',
                                 'enctype' =>'multipart/form-data',   
                                 'class'=>'form-horizontal', 
                                 'id'=>'validation-form' 
                                ]) !!} 
            
            {{ csrf_field() }}
            
            <div class="form-group">
                  <label class="col-sm-3 col-lg-2 control-label">Banner Title<i style="color: red;">*</i></label>
                  <div class="col-sm-9 col-lg-4 controls" >
                      <input type="text" class="form-control" name="banner_title" id="banner_title" placeholder="Enter Banner Title" data-rule-required="true"  value="{{isset($arr_data['banner_title']) ? $arr_data['banner_title'] : ''}}" id="banner_title"/>
                      <span class="help-block">{{ $errors->first('banner_title') }}</span>
                  </div>
            </div>
            
            <div class="form-group">
              <label class="col-sm-3 col-lg-2 control-label">Banner Image<i class="red">*</i> </label>
              <div class="col-sm-9 col-lg-10 controls">
                 <div class="fileupload fileupload-new" data-provides="fileupload">
                    
                    <div class="fileupload-new img-thumbnail" style="width: 200px; height: 150px;">

                      <?php
                          $banner_image = url('/uploads/default-load-image.jpg');
                          if(isset($arr_data['banner_image']) && $arr_data['banner_image']!=''){
                              if(file_exists($banner_image_base_img_path.$arr_data['banner_image'])){
                                $banner_image =$banner_image_public_img_path.$arr_data['banner_image'];
                              }
                          }
                      ?>
                      <img src="{{ $banner_image }}">
                    </div>
                    
                    <div class="fileupload-preview fileupload-exists img-thumbnail" style="max-width: 200px; max-height: 150px; line-height: 20px;"></div>

                    <div>
                      <span class="btn btn-default btn-file" style="height:32px;">
                      <span class="fileupload-new">Select Image</span>
                      <span class="fileupload-exists">Change</span>
                      <input type="file" 
                             data-validation-allowing="jpg, png, gif" 
                             class="file-input news-image validate-image" name="banner_image" id="image" 
                              @if(isset($arr_data['banner_image']) && $arr_data['banner_image'] == '')
                                required=""
                              @endif

                             />
                      </span>
                      <a href="#" id="remove" class="btn btn-default fileupload-exists" data-dismiss="fileupload">Remove</a>
                    </div>

                    <span for="image" id="err-image" class="help-block">{{ $errors->first(' image') }}</span>
                    <i class="red"> {!! image_validate_note(300, 500) !!} </i>
                 </div>
              </div>
            </div>
            
            <input type="hidden" name="old_banner_image" value="{{ isset($arr_data['banner_image']) ? $arr_data['banner_image'] : '' }}">
            <input type="hidden" name="enc_id" value="{{ isset($enc_id) ? base64_encode($enc_id) : '' }}">

            <div class="form-group">
              <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2">
               
                <button type="submit"  id="btn_submit" class="btn btn-primary"> Update</button>
                &nbsp;
                <a class="btn" href="{{ $module_url_path }}">Back</a>
              </div>
            </div>
    
          {!! Form::close() !!}
      </div>
    </div>
  </div>
<script type="text/javascript">
    
    var BASE_URL = "{{url('/')}}";

    $(document).on("change",".validate-image", function()
    {            
        var file=this.files;
        validateVehicleTypeImage(this.files, 300, 500);
    });
    function validateVehicleTypeImage (files,height,width) 
    {
      var default_image_url = BASE_URL+'/uploads/default-profile.png';

      var image_height = height || "";
      var image_width = width || "";
      if (typeof files !== "undefined") 
      {
        for (var i=0, l=files.length; i<l; i++) 
        {
              var blnValid = false;
              var ext = files[0]['name'].substring(files[0]['name'].lastIndexOf('.') + 1);
              if(ext == "JPEG" || ext == "jpeg" || ext == "jpg" || ext == "JPG" || ext == "png" || ext == "PNG")
              {
                          blnValid = true;
              }
              
              if(blnValid == false) 
              {
                  showAlert("Sorry, " + files[0]['name'] + " is invalid, allowed extensions are: jpeg , jpg , png","error");
                  $(".fileupload-preview").html("");
                  
                  // var image_src = '<img src="'+default_image_url+'" style="max-height: 150px;">';

                  // $(".fileupload-preview fileupload-exists img-thumbnail").html(image_src);

                  $(".fileupload").attr('class',"fileupload fileupload-new");
                  $("#image").val('');
                  return false;
              }
              else
              {              
                
                    var reader = new FileReader();
                    reader.readAsDataURL(files[0]);
                    reader.onload = function (e) 
                    {
                            var image = new Image();
                            image.src = e.target.result;
                               
                            image.onload = function () 
                            {
                                var height = this.height;
                                var width = this.width;
                                

                                console.log("current height:"+height+"  validate height:"+image_height );

                                console.log("current width:"+width+" validate width:"+image_width);
                                
                                if (height > image_height || width > image_width ) 
                                {
                                    showAlert("Height and Width must be less than or equal to "+image_height+" X "+image_width+"." ,"error");
                                    $(".fileupload-preview").html("");
                                    $(".fileupload").attr('class',"fileupload fileupload-new");
                                    $("#image").val('');
                                    return false;
                                }
                                else
                                {
                                    
                                   return true;
                                }
                            };
         
                    }
                  
              }                
         
          }
        
      }
      else
      {
        showAlert("No support for the File API in this web browser" ,"error");
      } 
    }


</script>    
  

@stop                    
