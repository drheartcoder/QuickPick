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
           {!! Form::open([ 'url' => $module_url_path.'/send_email',
                                 'method'=>'POST',
                                 'enctype' =>'multipart/form-data',   
                                 'class'=>'form-horizontal', 
                                 'id'=>'validation-form' 
                                ]) !!} 

           {{ csrf_field() }}
            <div class="form-group">
                  <label class="col-sm-3 col-lg-2 control-label">To Email</label>
                  <div class="col-sm-9 col-lg-4 controls" >
                    <input type="text" name="email_to" value="{{$email_data['email']}}" class="form-control" readonly="readonly" >
                    <div class="help-block"></div>
                  </div>
            </div>
             <div class="form-group ">
                  <label class="col-sm-3 col-lg-2 control-label">Name</label>
                  <div class="col-sm-9 col-lg-4 controls" >
                    <input type="text" name="to_name" value="{{$email_data['first_name']}}&nbsp;{{$email_data['last_name']}}" class="form-control" readonly="readonly" >
                    <div class="help-block"> </div>
                  </div>
            </div>
    
            <div class="form-group">
                  <label class="col-sm-3 col-lg-2 control-label">Message <i style="color: red;">*</i></label>
                  <div class="col-sm-9 col-lg-8 controls" > 
                    <textarea class="form-control" name="email_body" id="email_body" data-rule-required="true">
                    </textarea>
                    <span class="help-block">{{ $errors->first('email_body') }}</span>
                  </div>
            </div>
            <div class="form-group">
              <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2">
               
                 <input type="button" class="btn btn btn-primary" name="save" onclick="saveTinyMceContent()" value="Send" id="btn_submit"/> 
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
$('#btn_submit').click(function(){

$('#validation-form').valid(); 

$('#validation-form').submit();
});

</script>     
  <script type="text/javascript">
    
   function saveTinyMceContent()
    {
      tinyMCE.triggerSave();
    }

    $(document).ready(function()
    {
      tinymce.init({
        selector: 'textarea',
        relative_urls: false,
        height:500,
        remove_script_host:false,
        convert_urls:false,
        menubar: false,
        /*plugins: [
          'advlist autolink lists link image charmap print preview anchor',
          'searchreplace visualblocks code fullscreen',
          'insertdatetime media table contextmenu paste code'
        ],
        toolbar: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image',*/
        plugins: [
          'advlist autolink lists link image charmap print preview anchor textcolor',
          'searchreplace visualblocks code fullscreen',
          'insertdatetime media table contextmenu paste code help wordcount'
        ],
        toolbar: 'insert | undo redo |  formatselect | bold italic backcolor  | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help',
        content_css: [
          '//fast.fonts.net/cssapi/e6dc9b99-64fe-4292-ad98-6974f93cd2a2.css',
          '//www.tinymce.com/css/codepen.min.css'
        ]
      });
    });
</script>
@stop                    
