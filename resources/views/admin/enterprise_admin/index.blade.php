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
                <i class="fa fa-building-o"></i>                
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
            <a href="{{ $module_url_path.'/create'}}" class="btn btn-primary btn-add-new-records">Add New {{ str_singular($module_title) }}</a> 
          </div>


            <div class="btn-group">  
                <a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip " 
                    title="Multiple Active/Unblock" 
                    href="javascript:void(0);" 
                    onclick="javascript : return check_multi_action('frm_manage','activate');" 
                    style="text-decoration:none;">

                    <i class="fa fa-unlock"></i>
                </a> 
                <a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip btn-dangers" 
                   title="Multiple Deactive/Block" 
                   href="javascript:void(0);" 
                   onclick="javascript : return check_multi_action('frm_manage','deactivate');"  
                   style="text-decoration:none;">
                    <i class="fa fa-lock"></i>
                </a> 

                 <div class="btn-group">  
                  <a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip btn-delets" 
                     title="Multiple Delete" 
                     href="javascript:void(0);" 
                     onclick="javascript : return check_multi_action('frm_manage','delete');"  
                     style="text-decoration:none;">
                     <i class="fa fa-trash-o"></i>
                  </a>
                </div>
                
             </div>

          {{-- @if(array_key_exists('company.delete', $arr_current_user_access))    
              <div class="btn-group">  
                <a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip btn-delets" 
                   title="Multiple Delete" 
                   href="javascript:void(0);" 
                   onclick="javascript : return check_multi_action('frm_manage','delete');"  
                   style="text-decoration:none;">
                   <i class="fa fa-trash-o"></i>
                </a>
              </div>
          @endif    --}}
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
          <br/>
          <div class="clearfix"></div>

           <div class="table-responsive" style="border:0">      
              <input type="hidden" name="multi_action" value="" />
                <table class="table table-advance table-arrows"  id="table_module">
                  <thead>
                    <tr>  
                                                  
                        <th style="width: 18px; vertical-align: initial;"><div class="check-box">
                            <input type="checkbox" class="filled-in" name="selectall" id="selectall" />
                            <label for="selectall"></label>
                        </div></th>
                      

                        <th><a class="sort-desc" href="#">Enterprise Name </a>
                            <input type="text" name="q_name" placeholder="Search" class="search-block-new-table column_filter" />
                        </th> 

                        <th><a class="sort-desc" href="#">Email Address </a>
                            <input type="text" name="q_email" placeholder="Search" class="search-block-new-table column_filter" />
                        </th> 

                        <th><a class="sort-desc" href="#">Mobile Number </a>
                            <input type="text" name="q_contact_number" placeholder="Search" class="search-block-new-table column_filter" />
                        </th> 
                        
                        <th>Status</th>
                        
                        
                        
                        <th width="150px">Action</th>
                        
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
          'url':'{{ $module_url_path.'/get_records?role='}}{{$role or ''}}',
          'data': function(d)
            {
              d['column_filter[q_name]']          = $("input[name='q_name']").val()
              d['column_filter[q_email]']         = $("input[name='q_email']").val()
              d['column_filter[q_contact_number]']  = $("input[name='q_contact_number']").val()
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
          {data: 'user_name', "orderable": false, "searchable":false},
          {data: 'email', "orderable": false, "searchable":false},
          {data: 'contact_number', "orderable": false, "searchable":false},
          {
            render : function(data, type, row, meta) 
            {
              return row.build_status_btn;
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
 </script> 

<!-- END Main Content -->
<script type="text/javascript">
  function show_details(url)
  {  
      window.location.href = url;
  }
 function filterData()
  {
    table_module.draw();
  }
</script>
@stop