    @extends('company.layout.master')                


    @section('main_content')

    <!-- BEGIN Page Title -->
     <link rel="stylesheet" type="text/css" href="{{ url('/') }}/assets/data-tables/latest/dataTables.bootstrap.min.css">
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
                <i class="fa fa-users"></i>                
            </span> 
            <li class="active">{{ isset($module_title)?$module_title:"" }}</li>
        </ul>
      </div>
    <!-- END Breadcrumb -->

    <!-- BEGIN Main Content -->
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
          
          {!! Form::open([ 'url' => $module_url_path.'/multi_action',
                                 'method'=>'POST',
                                 'enctype' =>'multipart/form-data',   
                                 'class'=>'form-horizontal', 
                                 'id'=>'frm_manage' 
                                ]) !!}

            {{ csrf_field() }}

            <div class="col-md-10">
            <div id="ajax_op_status">
                
            </div>
            <div class="alert alert-danger" id="no_select" style="display:none;"></div>
            <div class="alert alert-warning" id="warning_msg" style="display:none;"></div>
          </div>
          <div class="btn-toolbar pull-right clearfix">
            
          <div class="btn-group"> 
                <a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip" 
                   title="Refresh" 
                   href="{{ $module_url_path }}/booking_history"
                   style="text-decoration:none;">
                   <i class="fa fa-repeat"></i>
                </a> 
              </div>
          </div>
            
          <div class="clearfix"></div>

          <div class="table-responsive" style="border:0">      
              <input type="hidden" name="multi_action" value="" />
                <table class="table table-advance"  id="table_module">
                  <thead>
                    <tr>  
                      <th><a class="sort-desc" href="#">Booking ID</a>
                          <input type="text" name="q_booking_unique_id" placeholder="Search" class="search-block-new-table column_filter" />
                      </th> 

                      <th><a class="sort-desc" href="#">Date </a>
                            <input type="text" id="q_date" name="q_date" placeholder="Search" class="search-block-new-table column_filter" onkeyup="javascript:return false;" onchange="filterData();"/>
                      </th> 

                      <th><a class="sort-desc" href="#">User Name</a>
                          <input type="text" name="q_user_name" placeholder="Search" class="search-block-new-table column_filter" />
                      </th> 

                      <th><a class="sort-desc" href="#">Driver Name</a>
                          <input type="text" name="q_driver_name" placeholder="Search" class="search-block-new-table column_filter" />
                      </th> 
                      
                      <th><a class="sort-desc" href="#">Vehicle Type</a> </th>

                      <th><a class="sort-desc" href="#">Pick Location</a>
                      </th> 

                      <th><a class="sort-desc" href="#">Drop Location</a>
                      </th> 

                      <th><a class="sort-desc" href="#">Booking Status</a>
                          <select class="search-block-new-table column_filter" name="q_booking_status" onchange="filterData();">
                            <option value="">Select</option>
                            <option value="TO_BE_PICKED">To be picked</option>
                            <option value="IN_TRANSIT">In Transit</option>
                            <option value="COMPLETED">Completed</option>
                            <option value="CANCEL_BY_USER">Cancel By User</option>
                            <option value="CANCEL_BY_DRIVER">Cancel By Driver</option>
                          </select>
                      </th> 
                      <th>Action</th>

                    </tr>
                  </thead>
               </table>
          </div>

          <div> </div>
         
          {!! Form::close() !!}
      </div>
  </div>
</div>

<script type="text/javascript">
      
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
          'url':'{{ $module_url_path.'/get_records'}}',
          'data': function(d)
            {
              d['column_filter[booking_unique_id]']      = $("input[name='q_booking_unique_id']").val()
              d['column_filter[booking_date]']           = $("input[name='q_date']").val()
              d['column_filter[user_name]']              = $("input[name='q_user_name']").val()
              d['column_filter[driver_name]']            = $("input[name='q_driver_name']").val()
              d['column_filter[booking_status]']         = $("select[name='q_booking_status']").val()
            }
          },
          columns: [
          {data: 'booking_unique_id', "orderable": true, "searchable":true},
          {data: 'booking_date', "orderable": false, "searchable":false},
          {data: 'user_name', "orderable": true, "searchable":true},
          {data: 'driver_name', "orderable": true, "searchable":true},
          {data: 'vehicle_type', "orderable":false, " searchable":false},
          {data: 'pickup_location', "orderable": true, "searchable":false},
          {data: 'drop_location', "orderable": true, "searchable":false},
          {
            render : function(data, type, row, meta) 
            {
              return row.booking_status;
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

 </script>  
@stop