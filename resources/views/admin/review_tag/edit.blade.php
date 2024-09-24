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
           {!! Form::open([ 'url' => $module_url_path.'/update/'.$enc_id,
                                 'method'=>'POST',
                                 'enctype' =>'multipart/form-data',   
                                 'class'=>'form-horizontal', 
                                 'id'=>'validation-form' 
                                ]) !!} 

           {{ csrf_field() }}

           @if(isset($arr_review_tag) && count($arr_review_tag) > 0)   


            <div class="form-group" style="">
                  <label class="col-sm-3 col-lg-2 control-label">Review Tag<i style="color: red;">*</i></label>
                  <div class="col-sm-9 col-lg-4 controls" >

                      {!! Form::text('tag_name',isset($arr_review_tag['tag_name']) ? $arr_review_tag['tag_name']: "",['class'=>'form-control','data-rule-required'=>'true', 'placeholder'=>'Enter Review Tag' , 'maxlength'=>"40"]) !!}  

                      <span class="help-block">{{ $errors->first('tag_name') }}</span>
                  </div>
            </div>

            <div class="form-group">
              <label class="col-sm-3 col-lg-2 control-label">Review Image<i style="color: red;">*</i> </label>
              <div class="col-sm-9 col-lg-10 controls">
                 <div class="fileupload fileupload-new" data-provides="fileupload">
                   <div class="fileupload-new img-thumbnail" style="width: 200px; height: 150px;">
                      @if(isset($arr_review_tag['review_image']) && !empty($arr_review_tag['review_image']))
                        <img src={{ $review_tag_public_path.$arr_review_tag['review_image']}} alt="" /> 
                      @else
                         <img src={{ url("uploads/default-review.png")}} alt="" />
                      @endif 
                  </div>
                    <div class="fileupload-preview fileupload-exists img-thumbnail" style="max-width: 200px; max-height: 150px; line-height: 20px;">
                         @if(isset($arr_review_tag['review_image']) && !empty($arr_review_tag['review_image']))
                      <img src={{ $review_tag_public_path.$arr_review_tag['review_image']}} alt="" /> 
                    @else
                         <img src={{ url("uploads/default-review.png")}} alt="" />
                      @endif   
                    </div>
                    <div>
                       <span class="btn btn-default btn-file"><span class="fileupload-new" >Select Image</span> 
                       <span class="fileupload-exists">Change</span>
                       
                       {!! Form::file('review_image',['id'=>'review_image','class'=>'file-input validate-image','data-rule-required'=>'']) !!}

                       </span> 
                       <a href="#" class="btn btn-default fileupload-exists" data-dismiss="fileupload">Remove</a>
                       <span>
                       </span> 
                    </div>
                 </div>
                  <i class="red"> {!! image_validate_note(250,250) !!} </i>
                  <span class='help-block'><b>{{ $errors->first('review_image') }}</b></span>  
              </div>
            </div>
            <input type="hidden" name="oldimage" value="{{isset($arr_review_tag['review_image']) ? $arr_review_tag['review_image'] :''}}">
            
            <div class="form-group">
              <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2">
               
                {!! Form::submit('Update',['class'=>'btn btn btn-primary','value'=>'true'])!!}
                &nbsp;
                <a class="btn" href="{{ $module_url_path }}">Back</a>
              </div>
            </div>

            @else 
              <div class="form-group">
                <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2">
                  <h3><strong>No Record found..</strong></h3>     
                </div>
              </div>
            @endif
    
          {!! Form::close() !!}
      </div>
    </div>
  </div>
  
  <!-- END Main Content -->

<script type="text/javascript">  

   $(document).on("change",".validate-image", function()
    {            
        var file=this.files;
        validateImage(this.files, 250, 250);
    });

</script>

@stop                    
