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
			<a href="{{ url($admin_panel_slug.'/dashboard') }}"> Dashboard </a>
		</li>
		<span class="divider">
			<i class="fa fa-angle-right"></i>
			<i class="fa {{$module_icon or ''}}"></i>
			<a href="{{ $module_url_path }}"> Manage {{ $module_title or ''}} </a>
		</span> 
		<span class="divider">
			<i class="fa fa-angle-right"></i>
			<i class="fa fa-edit"></i>
		</span>
		<li class="active"> {{ $page_title or ''}} </li>
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
					{{ $page_title or ''}}
				</h3>
				<div class="box-tool">
					<a data-action="collapse" href="#"></a>
					<a data-action="close" href="#"></a>
				</div>
			</div>

			<div class="box-content">
				
				@include('admin.layout._operation_status')



				<div class="tabbable">
					<form method="POST" id="validation-form" class="form-horizontal" 

					action="{{$module_url_path}}/update/{{base64_encode($arr_data['id'])}}" enctype="multipart/form-data">

					{{ csrf_field() }}              

					<ul  class="nav nav-tabs">
						@include('admin.layout._multi_lang_tab')
					</ul>

					<div  class="tab-content">

						@if(isset($arr_lang) && sizeof($arr_lang)>0)
						@foreach($arr_lang as $lang)

						<?php  
						/* Locale Variable */  
						$template_name = "";
						$template_subject = "";
						$template_html = "";
						
						$template_name = $arr_data['template_name'];

						if(isset($arr_data['translations'][$lang['locale']]))
						{ 
							$template_subject = $arr_data['translations'][$lang['locale']]['template_subject'];
						}

						if(isset($arr_data['translations'][$lang['locale']]))
						{
							$template_html = $arr_data['translations'][$lang['locale']]['template_html'];
						}?>
						<div class="tab-pane fade {{ $lang['locale']=='en'?'in active':'' }}"     id="{{ $lang['locale'] }}">

							@if($lang['locale'] == 'en')  

							<div class="form-group">
								<label class="col-sm-3 col-lg-2 control-label" for="email"> Email Name 
									<i class="red">*</i>
								</label>
								<div class="col-sm-6 col-lg-4 controls">   
									<input type="text" name="template_name" required=""  placeholder="Email Name" value="{{$template_name or ''}}"  class="form-control add-stundt"/>
									<span class='help-block'> {{ $errors->first('template_name') }} </span>  
								</div>
							</div>

							<div class="form-group">
								<label class="col-sm-3 col-lg-2 control-label" for="email"> From Name<i class="red">*</i></label>
								<div class="col-sm-6 col-lg-4 controls">
									{!! Form::text('template_from',$arr_data['template_from'],['class'=>'form-control','required'=>'true','data-rule-maxlength'=>'255', 'placeholder'=>'Email From']) !!}  
								<span class='help-block'> {{ $errors->first('template_from') }} </span>  
								</div>
							</div>

							<div class="form-group">
								<label class="col-sm-3 col-lg-2 control-label" for="email"> From Email<i class="red">*</i> </label>
								<div class="col-sm-6 col-lg-4 controls">       
									{!! Form::text('template_from_mail',$arr_data['template_from_mail'],['class'=>'form-control','required'=>'true','data-rule-maxlength'=>'255', 'placeholder'=>'Email From Email']) !!}  
								<span class='help-block'> {{ $errors->first('template_from_mail') }} </span>  
								</div>
							</div>

							@endif  

							<div class="form-group">
								<label class="col-sm-3 col-lg-2 control-label" for="email"> Email Subject 
									@if($lang['locale'] == 'en')<i class="red">*</i>@endif
								</label>
								<div class="col-sm-6 col-lg-4 controls">
									@if($lang['locale'] == 'en')       
									<input type="text" name="template_subject_{{$lang['locale']}}" required=""  placeholder="Email Subject" value="{{$template_subject}}"  class="form-control add-stundt"/>
									@else
									<input type="text" name="template_subject_{{$lang['locale']}}" placeholder="Email Subject" value="{{$template_subject}}"  class="form-control add-stundt"/>
									@endif
								<span class='help-block'> {{ $errors->first('template_subject_'.$lang['locale']) }} </span>  
								</div>
							</div>

							<div class="form-group">
								<label class="col-sm-3 col-lg-2 control-label" for="email"> Email Body 
									@if($lang['locale'] == 'en')<i class="red">*</i>@endif
								</label>
								<div class="col-sm-6 col-lg-8 controls">   
									@if($lang['locale'] == 'en')  
									<textarea name="template_html_{{$lang['locale']}}" class="form-control" required="" rows="10"  
									placeholder="Email Body">{{$template_html}}</textarea>
									@else
									<textarea name="template_html_{{$lang['locale']}}" class="form-control"  rows="10"  placeholder="Email Body">{{$template_html}}</textarea>
									@endif
								</div>
								<span class='help-block'> {{ $errors->first('template_html_'.$lang['locale']) }} </span>  

							</div>

							@if($lang['locale'] == 'en')
							<div class="form-group">
								<label class="col-sm-3 col-lg-2 control-label" for="email"> Variables: </label>
								<div class="col-sm-6 col-lg-7 controls">   
									@if(sizeof($arr_variables)>0)
									@foreach($arr_variables as $variable)
									<br> <label> {{ $variable }} </label> 
									@endforeach
									@endif 
								</div>
							</div>
							@endif

							<div class="form-group">
								<div class="col-sm-6 col-lg-1 col-lg-offset-2">
									@if($lang['locale'] == 'en')   
									<a class="btn btn btn-success" target="_blank" href="{{ url($module_url_path).'/view/'.base64_encode($arr_data['id']).'/en' }}"  title="Preview">
										<i class="fa fa-eye" ></i> Preview
									</a>
									@elseif($lang['locale'] == 'ar')
									<a class="btn btn btn-success" target="_blank" href="{{ url($module_url_path).'/view/'.base64_encode($arr_data['id']).'/ar' }}"  title="Preview">
										<i class="fa fa-eye" ></i> Preview
									</a>
									@endif   
								</div>
							</div>

						</div>     
						@endforeach
						@endif

					</div>
					<br>
					<div class="form-group">
						<div class="col-sm-6 col-lg-1 col-lg-offset-2">
							<input type="submit" id="save_btn" class="btn btn btn-primary"  onclick="saveTinyMceContent()" value="Update"/>
						</div>
					</div>

				</form>

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
			relative_urls: false,
			height:500,
			remove_script_host:false,
			convert_urls:false,
			plugins: [
			'advlist autolink lists link image charmap print preview anchor',
			'searchreplace visualblocks code fullscreen',
			'insertdatetime media table contextmenu paste code'
			],
			toolbar: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image',
			content_css: [
			'//fast.fonts.net/cssapi/e6dc9b99-64fe-4292-ad98-6974f93cd2a2.css',
			'//www.tinymce.com/css/codepen.min.css'
			]
		});
	});

</script>
@stop