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
    
    <i class="fa fa-angle-right"></i>
    <i class="fa fa-money"></i>
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
      
        <div class="col-md-8 col-md-8 col-lg-8">
        </div>
        
        <div class="col-md-4">
          <div class="btn-toolbar pull-right clearfix">
            <div class="btn-group"> 
              <a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip refrash-btns" 
                title="Refresh" 
                href="{{ $module_url_path }}"
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
                           <strong>{!! config('app.project.currency') !!}</strong>&nbsp;{{isset($arr_company_balance_information['company_total_amount']) ? number_format($arr_company_balance_information['company_total_amount'],2): 00.00}}
                            <p><strong>Total Earning</strong></p>
                          </div>
                          <div class="clearfix"></div>
                        </div>

                        <div class="driver-inline">
                          <div  class="jpgs-avrd">
                            <img src="{{url('/')}}/images/admin/money-bill-of-1_318-41743.jpg" height="130" width="110" alt="" />
                          </div><br>
                          <div class="titles-h1">
                            <strong>{!! config('app.project.currency') !!}</strong>&nbsp;{{isset($arr_company_balance_information['company_paid_amount']) ? number_format($arr_company_balance_information['company_paid_amount'],2): 00.00}}
                            <p><strong>Paid Amount (From Admin)</strong></p>
                          </div>
                          <div class="clearfix"></div>
                        </div>

                        <div class="driver-inline">
                          <div  class="jpgs-avrd">
                            <img src="{{url('/')}}/images/admin/money-bill-of-1_318-41743.jpg" height="130" width="110" alt="" />
                          </div><br>
                          <div class="titles-h1">
                            <strong>{!! config('app.project.currency') !!}</strong>&nbsp;{{isset($arr_company_balance_information['company_unpaid_amount']) ? number_format($arr_company_balance_information['company_unpaid_amount'],2): 00.00}}
                            <p><strong>Unpaid Amount (From Admin)</strong></p>
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
                  <th> Transactions ID</th>
                  <th> Booking ID</th>
                  <th> Driver/Company Name</th>
                  <th> Amount </th>
                  <th> Note </th>
                  <th> Date </th>
                  <th> Status</th> 
                </tr>
              </thead>
              <tbody>
                @if(isset($arr_deposit_money) && sizeof($arr_deposit_money)>0)
                @foreach($arr_deposit_money as $key => $value)              

                  <tr>

                    <td> {{ (isset($value['transaction_id']) && ($value['transaction_id'] != '') ) ? $value['transaction_id']:'-' }}</td>
                    
                    <td> {{ (isset($value['booking_master_details']['booking_unique_id']) && ($value['booking_master_details']['booking_unique_id'] != '') ) ? $value['booking_master_details']['booking_unique_id']:'-' }}</td>
                    
                    <td> 
                        @if(isset($value['to_user_type']) && $value['to_user_type'] == 'COMPANY')
                          {{ isset($value['to_user_details']['company_name']) ? $value['to_user_details']['company_name'] : '' }} (Company)
                        @elseif(isset($value['to_user_type']) && $value['to_user_type'] == 'COMPANY_DRIVER')
                          {{ isset($value['to_user_details']['first_name']) ? $value['to_user_details']['first_name'] : '' }} {{ isset($value['to_user_details']['last_name']) ? $value['to_user_details']['last_name'] : '' }} (Company Driver)
                        @else
                          - 
                        @endif

                    </td>

                    <td>{!! config('app.project.currency') !!} {{ isset($value['amount_paid']) ? number_format($value['amount_paid'],2) : 0 }}</td>
                    
                    <td> {{ (isset($value['note']) && ($value['note'] != '') ) ? str_limit($value['note'],200) :'-' }}</td>
                    <td>
                      {{ isset($value['date']) ? date('d M Y', strtotime($value['date'])) : '-' }}
                    </td>
                    
                    <td>
                      @if($value['status'] == 'SUCCESS')
                        <span style="width: 100px" class="badge badge-success">Success</span>
                      @elseif($value['status'] == 'FAILED')
                        <span style="width: 100px" class="badge badge-important">Failed</span>
                      @elseif($value['status'] == 'PENDING')
                        <span style="width: 100px" class="badge badge-warning">Pending</span>
                      @else
                        <span style="width: 100px" class="badge badge-warning">-</span>
                      @endif
                    </td>
                  </tr>
                @endforeach
                @endif
              </tbody>
            </table>  
          </div>
        </div>

  
        <div class="clearfix"></div>    
      </div>
    </div>
  </div>

  <script type="text/javascript">

    $(document).ready(function() {
      $('#table_module').DataTable();
    });

  </script>
  @stop