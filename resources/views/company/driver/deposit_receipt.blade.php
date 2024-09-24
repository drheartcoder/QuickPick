  @extends('company.layout.master')                


  @section('main_content')
  <style type="text/css">
  .table.table-new{border: 1px solid #E6E6E6;margin-bottom: 20px;}
  .table.table-new th{font-size: 16px;background-color:#f1f1f1; }
  .titles-h1.h2dashord{ margin-left: 10px;margin-bottom: 30px; }

  th.rows5 {width: 12% !important;}
  th.rows4 {width: 17% !important;}
  th.rows3 {width: 39% !important;}
  th.rows2 {width: 11% !important;}
  th.rows1 {width: 8% !important;}
</style>

<!-- BEGIN Page Title -->
<link rel="stylesheet" type="text/css" href="{{ url('/') }}/assets/data-tables/latest/dataTables.bootstrap.min.css">
<div class="page-title">
  <div>

  </div>
</div>

<div id="breadcrumbs">
  <ul class="breadcrumb">
    <li>
      <i class="fa fa-home"></i>
      <a href="{{ url($company_panel_slug.'/dashboard') }}">Dashboard</a>
    </li> &nbsp;
    <li class="active">
      <i class="fa fa-angle-right"></i>
      <i class="fa fa-car"></i>
      <a href="{{$module_url_path}}">
        {{ isset($module_title_deposit)?$module_title_deposit:"" }}
      </a>
    </li> &nbsp;
    <i class="fa fa-angle-right"></i>
    <i class="fa fa-bus"></i>
    <li class="active">{{ isset($module_title)?$module_title:"" }}</li>
  </ul>
</div>
<div class="row">
  <div class="col-md-12">
    <div class="box {{ $theme_color }}">
      <div class="box-title">
        <h3>
          <i class="fa fa-list"></i>
          {{ isset($page_title)?$page_title:"" }}
        </h3>
        <div class="box-tool">
          <a data-action="collapse" href="#"></a>
          <a data-action="close" href="#"></a>
        </div>
      </div>
      <div class="box-content">
        @include('admin.layout._operation_status')                
      
        <?php  
          $first_name = isset($arr_driver['first_name']) ? $arr_driver['first_name'] : '';
          $last_name  = isset($arr_driver['last_name']) ? $arr_driver['last_name'] : '';
          $full_name  = $first_name.' '.$last_name; 
        ?>

        <div class="col-md-8 col-md-8 col-lg-8">
          <h2> Driver Name : &nbsp;{{$full_name}}</h2>
        </div>

        <div class="col-md-4">
          <div class="btn-toolbar pull-right clearfix">
            <div class="btn-group"> 
              <a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip refrash-btns" 
                title="Refresh" 
                href="{{ $module_url_path.'/deposit_receipt/'.$driver_id }}"
                style="text-decoration:none;">
                <i class="fa fa-repeat"></i>
              </a> 
            </div>
            <br>
          </div>
        </div>

        <hr class="margin-0">
        <div class="col-md-12">
              <div class="driver-inline">
                         <div  class="jpgs-avrd">
                            <img src="{{url('/')}}/images/admin/money-bill-of-1_318-41743.jpg" height="130" width="110" alt="" />
                         </div><br>
                         <div class="titles-h1">
                            <strong>{!! config('app.project.currency') !!}</strong>&nbsp;{{isset($arr_driver_balance_information['driver_total_amount']) ? number_format($arr_driver_balance_information['driver_total_amount'],2): 00.00}}
                            <p><strong>Driver Total Earning</strong></p>
                         </div>
                         <div class="clearfix"></div>
                      </div>

                        <div class="driver-inline">
                          <div  class="jpgs-avrd">
                            <img src="{{url('/')}}/images/admin/money-bill-of-1_318-41743.jpg" height="130" width="110" alt="" />
                          </div><br>
                          <div class="titles-h1">
                            <strong>{!! config('app.project.currency') !!}</strong>&nbsp;{{isset($arr_driver_balance_information['driver_paid_amount']) ? number_format($arr_driver_balance_information['driver_paid_amount'],2): 00.00}}
                            <p><strong>Driver Paid Amount</strong></p>
                          </div>
                          <div class="clearfix"></div>
                        </div>

                        <div class="driver-inline">
                          <div  class="jpgs-avrd">
                            <img src="{{url('/')}}/images/admin/money-bill-of-1_318-41743.jpg" height="130" width="110" alt="" />
                          </div><br>
                          <div class="titles-h1">
                            <strong>{!! config('app.project.currency') !!}</strong>&nbsp;{{isset($arr_driver_balance_information['driver_unpaid_amount']) ? number_format($arr_driver_balance_information['driver_unpaid_amount'],2): 00.00}}
                            <p><strong>Driver Unpaid Amount</strong></p>
                          </div>
                          <div class="clearfix"></div>
                        </div>

        </div>
        <hr class="margin-0">
        <div class="col-md-12">
          <h3>Payment History</h3>
          <div class="table-responsive" style="border:1px solid #ccc; padding: 10px;">      
            <input type="hidden" name="multi_action" value="" />
            <table class="table table-advance"  id="table_module" >
              <thead>
                <tr>
                  {{-- <th class="rows1"> Sr.No. </th>  --}}
                  <th class="rows4"> Transactions ID</th>
                  <th class="rows2"> Amount </th>
                  <th class="rows3"> Note </th>
                  <th class="rows4"> Date </th>
                  <th class="rows5"> Receipt </th>
                  <th class="rows6"> Status</th> 
                </tr>
              </thead>
              <tbody>
                @if(isset($arr_company_deposit_money) && sizeof($arr_company_deposit_money)>0)
                @foreach($arr_company_deposit_money as $key => $value)              
                  <tr>
                    {{-- <td> {{ $key + 1 }} )</td> --}}
                    <td> {{ (isset($value['transaction_id']) && ($value['transaction_id'] != '') ) ? $value['transaction_id']:'-' }}</td>
                    <td>{!! config('app.project.currency') !!} {{ isset($value['amount_paid']) ? number_format($value['amount_paid'],2) : 0 }}</td>
                    
                    <td> {{ (isset($value['note']) && ($value['note'] != '') ) ? str_limit($value['note'],200) :'-' }}</td>
                    <td>
                      {{ isset($value['date']) ? date('d M Y', strtotime($value['date'])) : '-' }}
                    </td>
                    <td>
                      @if(isset($value['receipt_image']) && file_exists($receipt_image_base_path.$value['receipt_image']))
                        <a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip btn-delets" href="{{$receipt_image_public_path.$value['receipt_image']}}" download><i class="fa fa-download" aria-hidden="true"></i></a>
                      @else
                        <a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip btn-delets" href="javascript:void(0);"><i class="fa fa-download" aria-hidden="true"></i></a>
                      @endif
                    </td>
                    <td>
                      @if($value['status'] == 'APPROVED')
                        <label class="label label-success">Approved</label>
                      @elseif($value['status'] == 'PENDING')
                        <label class="label label-warning">Pending</label>
                      @elseif($value['status'] == 'REJECTED')
                        <label class="label label-important">Rejected</label>
                      @else
                        <label class="label label-warning">-</label>
                      @endif
                    </td>
                  </tr>
                @endforeach
                @endif
              </tbody>
            </table>  
          </div>
        </div>

        
        <hr class="margin-0">
        <div class="col-md-12" >
          <h3>Add Payment</h3>
          {!! Form::open([ 'url' => $module_url_path.'/make_payment',
          'method'=>'POST',
          'enctype' =>'multipart/form-data',   
          'class'=>'form-horizontal', 
          'id'=>'validation-form',
          'style' => "border:1px solid #ccc; padding: 10px;",
          'onsubmit'=>'return addLoader();'
          ]) !!} 

          {{ csrf_field() }}

          <input type="hidden" name="driver_id" id="driver_id" value="{{isset($driver_id) ? $driver_id :'0'}}">
          
          <div class="form-group" style="">
            <label class="col-sm-3 col-lg-2 control-label">Amount Pay<i style="color: red;">*</i></label>
            <div class="col-sm-9 col-lg-4 controls" >                      
              <input type="text" class="form-control" name="amount_paid" placeholder="Enter Amount to be paid" onkeypress="return isNumberKey(event)" data-rule-required="true" data-rule-digits="true" maxlength="15" minlength="1" value="" />
              <span class="help-block">{{ $errors->first('amount_paid') }}</span>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-3 col-lg-2 control-label"> Pay Receipt <i style="color: red;">*</i></label>
            <div class="col-sm-9 col-lg-10 controls">
              <input type="file" name="receipt_image" id="receipt_image" data-rule-required="true"/>
              <i class="red"> Allowed only jpg | jpeg | png | pdf </i>
              <span id="error-file_upload" class='help-block'>{{ $errors->first('receipt_image') }}</span>  
              
            </div>
          </div>

          <div class="form-group" style="">
            <label class="col-sm-3 col-lg-2 control-label">Note</label>
            <div class="col-sm-9 col-lg-4 controls" >                      
              <textarea id="note" name="note" class="form-control is-maxlength" placeholder="Note" maxlength="1000"></textarea>
              <span class="maxlength-feedback">Enter Note Max.1000 characters</span>
              <div class="help-block">{{ $errors->first('amount_paid') }}</div>
            </div>
          </div>          
          <div class="form-group">
            <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2">
              <button type="submit"  id="proceed" class="btn btn-primary"> Make Payment</button>
            </div>
          </div>      
          {!! Form::close() !!}
        </div>
        
        <div class="clearfix"></div>    
      </div>
    </div>
  </div>

  <script type="text/javascript">

    $(document).ready(function() {
      $('#table_module').DataTable();
    });

    $(document).on("change","#receipt_name", function()
    {            
      var file=this.files;

      validateFileCustom(this.files);
    });
    function validateFileCustom(files) 
    {

      if (typeof files !== "undefined") 
      {
        for (var i=0, l=files.length; i<l; i++) 
        {
          var blnValid = false;
          var ext = files[0]['name'].substring(files[0]['name'].lastIndexOf('.') + 1);
          if(ext == "JPEG" || ext == "jpeg" || ext == "jpg" || ext == "JPG" || ext == "png" || ext == "PNG" || ext == "gif" || ext == "pdf" || ext == "docx" )
          {
            blnValid = true;
          }

          if(blnValid ==false) 
          {
            showAlert("Please select valid file","error");
            $("#receipt_name").html("");
            $(".fileupload").attr('class',"fileupload fileupload-new");
            $("#receipt_name").val('');
            return false;
          }   
        }
      }
      else
      {
        showAlert("No support for the File API in this web browser" ,"error");
      } 
    }

    function addLoader(){   
          $('#validation-form').submit(function(event) {

              if($('.has-error').length > 0){
                 event.preventDefault();
              }else{
                  $("#proceed").html("<b><i class='fa fa-spinner fa-spin'></i></b> Processing...");
                  $("#proceed").attr('disabled', true);
              }
          });
      }

  </script>
  @stop