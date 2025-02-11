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
                <i class="fa fa-question-circle"></i>
                <a href="{{ $module_url_path }}">{{ $module_title or ''}}</a>
            </span> 
            <span class="divider">
                <i class="fa fa-angle-right"></i>
                  <i class="fa fa-plus"></i>
            </span>
            <li class="active">{{ $page_title or ''}}</li>
        </ul>
    </div>
    <!-- END Breadcrumb -->


    <!-- BEGIN Main Content -->
    <div class="row">
      <div class="col-md-12">

          <div class="box">
            <div class="box-title">
              <h3>
                <i class="fa fa-plus"></i>
                {{ isset($page_title)?$page_title:"" }}
            </h3>
            <div class="box-tool">
                <a data-action="collapse" href="#"></a>
                <a data-action="close" href="#"></a>
            </div>
        </div>
        <div class="box-content">

            @include('admin.layout._operation_status')  

			<div class="tabbable">

                {!! Form::open([ 'url' => $module_url_path.'/store',
                                 'method'=>'POST',   
                                 'class'=>'form-horizontal', 
                                 'id'=>'validation-form' 
                                ]) !!} 

                <ul  class="nav nav-tabs">
                    @include('admin.layout._multi_lang_tab')
                </ul>

                <div  class="tab-content">

                	@if(isset($arr_lang) && sizeof($arr_lang)>0)
                        @foreach($arr_lang as $lang)

                            <div class="tab-pane fade {{ $lang['locale']=='en'?'in active':'' }}" 
                            	 id="{{ $lang['locale'] }}">

    							<div class="form-group">
                                      <label class="col-sm-3 col-lg-2 control-label" for="state"> Question @if($lang['locale'] == 'en') 
                                          <i class="red">*</i>
                                       @endif
                                       </label>
                                      <div class="col-sm-6 col-lg-8 controls">

                                        @if($lang['locale'] == 'en')        
                                            {!! Form::text('question_'.$lang['locale'],old('question_'.$lang['locale']),['class'=>'form-control','data-rule-required'=>'true','data-rule-maxlength'=>'500', 'placeholder'=>'Question']) !!}
                                        @else
                                            {!! Form::text('question_'.$lang['locale'],old('question_'.$lang['locale'])) !!}
                                        @endif    
                                      </div>
                                      <span class='help-block'>{{ $errors->first('question_'.$lang['locale']) }}</span>  
                                </div>

                                <div class="form-group">
                                      <label class="col-sm-3 col-lg-2 control-label" for="state"> Answer 
                                            @if($lang['locale'] == 'en') 
                                              <i class="red">*</i>
                                           @endif
                                      </label>
                                      <div class="col-sm-6 col-lg-8 controls">

                                        @if($lang['locale'] == 'en')        
                                            {!! Form::textarea('answer_'.$lang['locale'],old('answer_'.$lang['locale']),['class'=>'form-control','data-rule-required'=>'true', 'placeholder'=>'Answer']) !!}
                                        @else
                                            {!! Form::textarea('answer_'.$lang['locale'],old('answer_'.$lang['locale'])) !!}
                                        @endif    
                                      </div>
                                      <span class='help-block'>{{ $errors->first('answer_'.$lang['locale']) }}</span>  
                                </div>

    	                    </div>

	                    @endforeach
	                @endif

                </div>
                <br>
                <div class="form-group">
                      <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2">
                      {!! Form::submit('Save',['class'=>'btn btn btn-primary','value'=>'true'])!!}
                    </div>
                </div>
                
                {!! Form::close() !!}
                
            </div>
    
</div>
</div>
</div>

<!-- END Main Content -->
<script type="text/javascript">
    function saveTinyMceContent()
    {
      tinyMCE.triggerSave();
    }
    $(document).ready(function()
                      {
      tinymce.init({
        selector: 'textarea',
        height:350,
        plugins: [
          'advlist autolink lists link image charmap print preview anchor',
          'searchreplace visualblocks code fullscreen',
          'insertdatetime media table contextmenu paste code'
        ],
        valid_elements : '*[*]',
        toolbar: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image ',
        content_css: [
          '//fast.fonts.net/cssapi/e6dc9b99-64fe-4292-ad98-6974f93cd2a2.css',
          '//www.tinymce.com/css/codepen.min.css'
        ]
      }
                  );
    }
                     );
  </script>




     


@stop                    
