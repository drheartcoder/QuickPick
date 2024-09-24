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


        <div class="modal fade view-modals" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Shipment Request Preview</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
              <div id="genrated_html"></div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>

              </div>

            </div>
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
                <a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip btn-dangers" 
                   title="Multiple Cancel Request" 
                   href="javascript:void(0);" 
                   onclick="javascript : return check_multi_action('frm_manage','cancel_request');"  
                   style="text-decoration:none;">
                    <i class="fa fa-times"></i>
                </a> 
            </div>

          <div class="btn-group"> 
                <a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip" 
                   title="Refresh" 
                   href="{{ $module_url_path }}"
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
                       
                      <th style="width: 3%; vertical-align: initial;">
                        <div class="check-box">
                            <input type="checkbox" class="filled-in" name="selectall" id="selectall" />
                            <label for="selectall"></label>
                        </div></th>
                      <th width="10%">Request ID 
                          <input type="text" name="q_request_id" placeholder="Search" class="search-block-new-table column_filter" />
                      </th> 
                      <th width="10%">Date
                          <input type="text" name="q_date" placeholder="Search" class="search-block-new-table column_filter" />
                      </th> 
                      <th width="7%">Time
                          <input type="text" name="q_time" placeholder="Search" class="search-block-new-table column_filter" />
                      </th> 
                      <th width="10%">User Name</a>
                          <input type="text" name="q_user_name" placeholder="Search" class="search-block-new-table column_filter" />
                      </th> 
                      <th width="20%">Pick Location</th> 
                      <th width="20%">Drop Location</th> 
                      <th width="10%">Booking Status</th> 
                      <th width="10%">Action</th>

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
          'url':'{{ $module_url_path.'/get_records_request_list'}}',
          'data': function(d)
            {
              d['column_filter[request_id]']             = $("input[name='q_request_id']").val()
              d['column_filter[date]']                   = $("input[name='q_date']").val()
              d['column_filter[time]']                   = $("input[name='q_time']").val()
              d['column_filter[user_name]']              = $("input[name='q_user_name']").val()
              d['column_filter[booking_status]']         = $("select[name='q_booking_status']").val()
            }
          },
          columns: [
         {
            render : function(data, type, row, meta) 
            {
             return '<div class="check-box"><input type="checkbox" class="filled-in case" name="checked_record[]"  id="mult_change_'+row.enc_id+'" value="'+row.enc_id+'" /><label for="mult_change_'+row.enc_id+'"></label></div>';
            },
            "orderable": false,
            "searchable":false
          },

          {data: 'request_id', "orderable": true, "searchable":true},
          {data: 'date', "orderable": true, "searchable":true},
          {data: 'time', "orderable": true, "searchable":true},
          {data: 'user_name', "orderable": true, "searchable":true},
       /*   {data: 'vehicle_type', "orderable":false, " searchable":false},*/
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


<script type="text/javascript">

function booking_details(ref)
{
    var id         = $(ref).attr('data-id');

    if(id!="")
    { 
        var url ="{{ $module_url_path.'/booking_info' }}?id="+id; 

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


function cancel_request(ref)
{
    var id         = $(ref).attr('data-id');
    if(id!="")
    {
         var msg = msg || false;

               
                   swal({
                    title: "Are you sure to cancel the request?",
                    text: msg,
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes",
                    cancelButtonText: "No",
                    closeOnConfirm: true,
                    closeOnCancel: true
                  },
                  function(isConfirm)
                  {
                    if(isConfirm==true)
                    {
                            
                        var csrf_token = "{{ csrf_token() }}";
                        var url ="{{ $module_url_path.'/cancel_request' }}"; 
                        $.ajax({

                          url:url,
                          type:"POST",
                          data:{
                            '_token' : csrf_token,
                            'id' : id
                          },
                          success:function(response){

                              if(response.status == "success")
                              {
                                swal('Request canceled successfully');
                                window.location.reload();
                              }
                              else if(response.status=='error_problem')
                              {
                                swal("Something went wrong,Please try again later"); 
                              }else  if(response.status=='error_not_id'){

                                swal("Details not found"); 
                              }
                          }

                        });


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