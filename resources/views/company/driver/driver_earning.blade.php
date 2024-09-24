@extends('company.layout.master')                
@section('main_content')

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
    </li>
    <li class="active">
      <i class="fa fa-angle-right"></i>
      <i class="fa fa-car"></i>
      <a href="{{$module_url_path}}"> {{ $module_title }}</a>
    </li>
    <span class="divider">
      <i class="fa fa-angle-right"></i>
      <i class="fa fa-usd"></i>                
    </span> 
    <li class="active">{{ isset($module_title_earning)?$module_title_earning:"" }}</li>
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

      <div class="modal fade view-modals" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <div id="ride_details_html"></div>
              
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <div id="genrated_html"></div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
              {{--  <button type="button" class="btn btn-primary">Print</button> --}}
            </div>
          </div>
        </div>
      </div>

      <div class="box-content">
        @include('admin.layout._operation_status')
        
        <?php  

          $is_company_driver = isset($arr_driver['is_company_driver']) ? $arr_driver['is_company_driver'] : '';
          $first_name        = isset($arr_driver['first_name']) ? $arr_driver['first_name'] : '';
          $last_name         = isset($arr_driver['last_name']) ? $arr_driver['last_name'] : '';
          $full_name         = $first_name.' '.$last_name;

          $company_name = '-';

          if($is_company_driver == '1')
          {
            $company_name = isset($arr_driver['company_details']['company_name']) ? $arr_driver['company_details']['company_name'] : '';
            $company_name = $company_name.' Driver';
          }
          elseif($is_company_driver == '0')
          {
            $company_name = config('app.project.name').' Driver';
          }
        ?>

        <div class="col-md-12">
          <div class="inpt-homes-filtr">
            <div class="row">

              <div class="col-md-8 col-md-8 col-lg-8">
              <h2> Driver Name : &nbsp;{{$full_name}} ({{$company_name}})</h2>
              </div>

              <div class="col-md-4">
                <div class="btn-toolbar pull-right clearfix">
                  <div class="btn-group"> 
                    <a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip refrash-btns" 
                      title="Refresh" 
                      href="{{ $module_url_path.'/driver_earning/'.$driver_id }}"
                      style="text-decoration:none;">
                      <i class="fa fa-repeat"></i>
                    </a> 
                  </div>
                  <br>
                </div>
              </div>

            <div class="col-md-12 col-md-12 col-lg-12">
            
            
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


              <div class="table-responsive" style="overflow-y: hidden;">      
                <table class="table table-advance table-arrows"  id="table_module">
                  <thead>
                    
                    <tr>  
                                        <th><a class="sort-desc" href="#">Booking ID</a>
                                            <input type="text" name="q_booking_unique_id" placeholder="Search" class="search-block-new-table column_filter" />
                                        </th> 

                                        <th><a class="sort-desc" href="#">Date </a>
                                              <input type="text" id="q_date" name="q_date" placeholder="Search" class="search-block-new-table column_filter" onkeyup="javascript:return false;" onchange="filterData();"/>
                                        </th> 
                                        
                                        <th><a class="sort-desc" href="#">Distance In Miles</a></th>

                                        <th><a class="sort-desc" href="#">Fare Change</a></th>
                                        

                                        <th><a class="sort-desc" href="#">Total Amt</a></th> 
                                        <th><a class="sort-desc" href="#">Discount Amt / <br>Bonus Amt</a></th> 
                                        <th><a class="sort-desc" href="#">User Paid Amt</a></th> 
                                        <th><a class="sort-desc" href="#">Company Amt</a></th>
                                        <th><a class="sort-desc" href="#">Driver Amt</a></th>
                                        <th><a class="sort-desc" href="#">Admin Amt</a></th>

                                        <th><a class="sort-desc" href="#">Payment Status</a>
                                            <select class="search-block-new-table column_filter" name="q_payment_status" onchange="filterData();">
                                              <option value="">Select</option>
                                              <option value="PENDING">Pending</option>
                                              <option value="SUCCESS">Success</option>
                                              <option value="FAILED">Failed</option>
                                            </select>
                                        </th> 
                                        <th>Action</th>

                                      </tr>
                  </thead>
                </table>
              </div>                       
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
      var module_url_path = '{{$module_url_path}}';
      var driver_id = '{{$driver_id}}';

      var ajax_url = module_url_path+'/get_earning_records?driver_id='+driver_id;


      $('#q_date').datepicker({ 
             // dateFormat: "yy-mm-dd"
             dateFormat: "dd M yy"
      });

      /*Script to show table data*/
      var table_module = false;
      $(document).ready(function()
      {
        table_module = $('#table_module').DataTable({
          processing: true,
          serverSide: true,
          autoWidth: false,
          bFilter: false,
          ajax: {
          'url':ajax_url,
          'data': function(d)
            {
              d['column_filter[booking_unique_id]']      = $("input[name='q_booking_unique_id']").val()
              d['column_filter[booking_date]']           = $("input[name='q_date']").val()
              d['column_filter[payment_status]']         = $("select[name='q_payment_status']").val()
            }
          },
          columns: [
          {data: 'booking_unique_id', "orderable": true, "searchable":true},
          {data: 'booking_date', "orderable": false, "searchable":false},
          {data: 'distance', "orderable":false, " searchable":false},
          {data: 'fare_charge', "orderable":false, " searchable":false},
          {data: 'total_amount', "orderable": true, "searchable":false},
          {data: 'applied_promo_code_charge', "orderable": true, "searchable":false},
          {data: 'total_charge', "orderable": true, "searchable":false},
          {data: 'company_amount', "orderable": false, "searchable":false},
          {data: 'driver_amount', "orderable": false, "searchable":false},
          {data: 'admin_amount', "orderable": false, "searchable":false},
          {
            render : function(data, type, row, meta) 
            {
              return row.payment_status;
            },
            "orderable": false, "searchable":false
          },
          {
            render : function(data, type, row, meta) 
            {
              return row.build_action_btn;
            },
            "orderable": false, "searchable":false
          }

          ]
        });

        $('input.column_filter').on( 'keyup click', function () 
        {
            filterData();
        });

        $('#table_module').on('draw.dt',function(event)
        {
          var oTable = $('#table_module').dataTable();
          var recordLength = oTable.fnGetData().length;
          $('#record_count').html(recordLength);
        });
      });

    function filterData(){
      table_module.draw();
    }

  function earning_details(ref)
  {
    var id         = $(ref).attr('data-id');
    if(id!="")
    { 
      var url ="{{ $module_url_path.'/earning_info' }}?id="+id; 
      $.ajax({

        url:url,
        type:"GET",
        success:function(response){

          if(response.status == "success")
          {
            $('#genrated_html').html(response); 
            $('#ride_details_html').html(response); 
            $('#exampleModal').modal('show');
            $('#genrated_html').append(response.generated_html);
            $('#ride_details_html').append(response.ride_details_html);
          }
          else
          {
            swal("Details not found"); 
          }
        }
      });
    }
    else
    {
      swal("Records not found"); 
    }
  }
</script>
@stop