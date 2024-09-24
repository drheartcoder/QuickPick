	@extends('admin.layout.master')                


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
				<a href="{{ url($admin_panel_slug.'/dashboard') }}">Dashboard</a>
			</li>
			<span class="divider">
				<i class="fa fa-angle-right"></i>
				<i class="fa fa-list-alt"></i>                
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

				<div class="clearfix" style="margin-top: 16px;"></div>
				<div style="margin-bottom: 10px;">

             <div class="col-sm-5 col-lg-3">
              <a class="column_filter btn btn-circle btn-to-success column_filter"  onclick="setFilter('daily')">Daily</a>|
              <a class="column_filter btn btn-circle btn-to-success column_filter"  onclick="setFilter('weekly')">Weekly</a>|
              <a class="column_filter btn btn-circle btn-to-success column_filter"  onclick="setFilter('monthly')">Monthly</a>
              <input type="hidden" name="q_date" id="q_date">
            </div>

					<div class="form-group" style="margin-top: 25px; display: inline-block; vertical-align: middle;">
						<div class="col-sm-7 col-lg-6 controls" >
							<input type="text" class="form-control" id ="from_date" name="from_date" placeholder="From" data-rule-required="true" />
						</div>
						<div class="col-sm-7 col-lg-6 controls" >
							<input type="text" class="form-control" id="to_date" name="to_date" placeholder="To" data-rule-required="true"/>
						</div>
					</div>
					<div style="display: inline-block; vertical-align: middle;" align="center">
						<a class="btn btn-circle btn-to-success search" onclick="filterData()">Search</a>
						<a class="btn btn-circle btn-to-success reset" href="{{$module_url_path}}">Reset</a>
                       {{--  <a class="btn btn-circle btn-to-success print" href="{{$module_url_path.'/generate_excel'}}">Print</a> --}}
						<a class="btn btn-circle btn-to-success print"  onclick="generateExcel()">Print</a>

					</div>

				</div>	
				
			     <div class="table-responsive" style="border:0">      
                  <input type="hidden" name="multi_action" value="" />
                    <table class="table table-advance"  id="table_module">
                      <thead>
                        <tr>  
                          <th><a class="sort-desc" href="#">Booking ID</a>
                              <input type="text" name="q_booking_unique_id" placeholder="Search" class="search-block-new-table column_filter" />
                          </th> 

                          <th><a class="sort-desc" href="#">Date </a>
                                <input type="text" id="q_booking_date" name="q_booking_date" placeholder="Search" class="search-block-new-table column_filter" onkeyup="javascript:return false;" onchange="filterData();"/>
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
                                <option value="CANCEL_BY_ADMIN">Cancel By Admin</option>
                              </select>
                          </th> 
                          <th style="width: 70px">Action</th>

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
              d['column_filter[from_date]']              = $("input[name='from_date']").val()
              d['column_filter[to_date]']                = $("input[name='to_date']").val()
              d['column_filter[vehicle_type]']          = $("select[name='vehicle_type']").val()
              d['column_filter[booking_unique_id]']      = $("input[name='q_booking_unique_id']").val()
              d['column_filter[booking_date]']           = $("input[name='q_booking_date']").val()
              d['column_filter[date_filter]']            = $("input[name='q_date']").val()
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

  $('#from_date').datepicker({ 
         dateFormat: "yy-mm-dd"
  });

 $('#to_date').datepicker({ 
         dateFormat: "yy-mm-dd"
  });

$(".vehicle_details").change(function(){
    filterData();
 });
 function getData()
 {
    var val1 =$(".select_date").val();
    if(val1=="daily")
    {
        $("#from_date").val("<?php echo date("Y-m-d"); ?>");
        $("#to_date").val("<?php echo date("Y-m-d"); ?>");
    }
    else if(val1=="weekly")
    {
        var from_date = '<?php echo date('Y-m-d', strtotime('-7 days')); ?>';
        var to_date =   '<?php echo date('Y-m-d'); ?>';
         
        $("#from_date").val(from_date);
        $("#to_date").val(to_date); 
    }
    else
    {
        var from_date = '<?php echo date('Y-m-d', strtotime('first day of this month')); ?>';
        var to_date =   '<?php echo date('Y-m-d', strtotime('last day of this month')); ?>';

        $("#from_date").val(from_date);
        $("#to_date").val(to_date);
    }
    filterData();
 }
 function setFilter(param)
 {
    $("#q_date").val(param);
    var val1 =param;

    if(val1=="daily")
    {
        $("#from_date").val("<?php echo date("Y-m-d"); ?>");
        $("#to_date").val("<?php echo date("Y-m-d"); ?>");
    }
    else if(val1=="weekly")
    {
        var from_date = '<?php echo date('Y-m-d', strtotime('-7 days')); ?>';
        var to_date =   '<?php echo date('Y-m-d'); ?>';
         
        $("#from_date").val(from_date);
        $("#to_date").val(to_date); 
    }
    else
    {
        var from_date = '<?php echo date('Y-m-d', strtotime('first day of this month')); ?>';
        var to_date =   '<?php echo date('Y-m-d', strtotime('last day of this month')); ?>';

        $("#from_date").val(from_date);
        $("#to_date").val(to_date);
    }
    filterData();
 }
 function generateExcel()
 {
    var from_date = $("#from_date").val(); 
    var to_date   = $("#to_date").val();
    var vehicle_type = $("#vehicle_type").val();



    var url = "{{$module_url_path.'/generate_excel?from_date='}}"+from_date+"&to_date="+to_date+"&vehicle_type="+vehicle_type;

    window.location.href=url;
 }
 </script>  

	@stop

