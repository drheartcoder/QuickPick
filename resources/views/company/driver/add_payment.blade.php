    @extends('company.layout.master')                


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
                <a href="{{ url($company_panel_slug.'/dashboard') }}">Dashboard</a>
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

           {{--  {{ dd($user_id) }} --}}

          @include('company.layout._operation_status')  
           {!! Form::open([ 'url' => $module_url_path.'/make_payment/'.base64_encode($user_id),
                                 'method'=>'POST',
                                 'enctype' =>'multipart/form-data',   
                                 'class'=>'form-horizontal', 
                                 'id'=>'validation-form' 
                                ]) !!} 

           {{ csrf_field() }}

            <div class="form-group" style="">
                  <label class="col-sm-3 col-lg-2 control-label">Total Earning Amount<i style="color: red;">*</i></label>
                  <div class="col-sm-9 col-lg-4 controls" >
                      <input type="text" class="form-control" name="total_amount" readonly="true" value="{{ $arr_earning['total_earning'] }}" />
                      <span class="help-block">{{ $errors->first('total_earning') }}</span>
                  </div>
            </div>
            <div class="form-group" style="">
                  <label class="col-sm-3 col-lg-2 control-label">Total Paid Amount<i style="color: red;">*</i></label>
                  <div class="col-sm-9 col-lg-4 controls" >
                      <input type="text" class="form-control" name="paid_amount_total" readonly="true" value="{{ $arr_total_amount_paid['0']['total_amount_paid'] }}" />
                      <span class="help-block">{{ $errors->first('total_amount_paid') }}</span>
                  </div>
            </div>

            {{-- <input type="hidden" name=""> --}}

            <div class="form-group" style="">
                  <label class="col-sm-3 col-lg-2 control-label">Amount Pay<i style="color: red;">*</i></label>
                  <div class="col-sm-9 col-lg-4 controls" >                      
                      <input type="text" class="form-control" name="amount_paid" placeholder="Enter Amount to be paid" onkeypress="return isNumberKey(event)" data-rule-required="true" data-rule-digits="true" maxlength="15" minlength="1" value="{{ old('amount_paid') }}" />

                      <span class="help-block">{{ $errors->first('amount_paid') }}</span>
                  </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 col-lg-2 control-label"> Pay Receipt </label>
                <div class="col-sm-9 col-lg-10 controls">
                   <input type="file" name="receipt_image" id="receipt_image"/>
                    <span class='help-block'>{{ $errors->first('receipt_image') }}</span>  
                     <i class="red"> Allowed only jpg | jpeg | png | pdf </i>
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

@stop                    
