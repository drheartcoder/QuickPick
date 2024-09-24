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

    @if(isset($role) && $role != 'photographer')
    <script type="text/javascript">
        window.history.pushState("", "", "{{$module_url_path}}" );
        // above code sets url as we want . only url segments , parameters can be changed. but not domain name.
    </script>
    @endif

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
          {{--  @if(array_key_exists('users.update', $arr_current_user_access))    --}}  
                <a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip" 
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
                
                <a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip review-stars" 
                       title="Send Notifications" 
                   href="javascript:void(0);" 
                   onclick="javascript : return send_multiple_notification();"  
                   style="text-decoration:none;">
                    <i class="fa fa-bullhorn" ></i>
                </a> 
                
           {{-- @endif--}}  
             </div>
              
              <div class="btn-group">  
                <a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip btn-delets" 
                   title="Multiple Delete" 
                   href="javascript:void(0);" 
                   onclick="javascript : return check_multi_action('frm_manage','delete');"  
                   style="text-decoration:none;">
                   <i class="fa fa-trash-o"></i>
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
              <br>
          

          </div>
          <br/>
          <div class="clearfix"></div>

           <div class="table-responsive" style="border:0">      
              <input type="hidden" name="multi_action" value="" />
                <table class="table table-advance"  id="table_module">
                  <thead>
                    <tr>  
                       <th style="width: 18px; vertical-align: initial;">
                        <div class="check-box">
                            <input type="checkbox" class="filled-in" name="selectall" id="selectall" />
                            <label for="selectall"></label>
                        </div></th>

                        <th><a class="sort-desc" href="#">Name </a>
                            <input type="text" name="q_name" placeholder="Search" class="search-block-new-table column_filter" />
                        </th> 

                        <th><a class="sort-desc" href="#">Email </a>
                            <input type="text" name="q_email" placeholder="Search" class="search-block-new-table column_filter" />
                        </th> 

                        <th><a class="sort-desc" href="#">Contact Number </a>
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

 <div class="modal fade view-modals indext-modals" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Send Push Notification </h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
        {!! Form::open([ 'url' => url('/common/send_notification'),
                                 'method'=>'POST',
                                 'id'=>'notification_frm' 
                                ]) !!} 

           {{ csrf_field() }}
        
        <div class="review-detais">
          <div class="boldtxts" id="header_title"></div>
          <div class="clearfix"></div>
        </div>

        <div class="review-detais">
        
        <div class="form-group" style="">
            <div class=" controls">
              <select class="form-control" id="code_type" name="code_type" onchange="changeCodeType(this);">
                <option value="">Select Code Type</option>
                <option value="1">Promo Code</option>
                <option value="2">Promotional Code</option>
              </select>
          </div>
        </div>

         <div class="form-group" style="display: none;" id="div_code">
            <div class=" controls">
              <select class="form-control" id="code" name="code" onchange="loadCodeDetails(this);">
                <option value="">Select Code</option>
              </select>
          </div>
        </div>

        <div class="form-group" id="div_message" style="display: none;">
            <div class=" controls" >
                <textarea data-rule-required="true" rows="5" data-rule-maxlength="500" class="form-control" placeholder="Enter message" name="message" id="message"></textarea>
                <label id="message_error" for="message" class="error" style="color:#a94442"></label>
          </div>
        </div>
          {{-- <div class="clearfix"></div> --}}
        </div>


        <input type="hidden" name="enc_user_id" id="enc_user_id">
        <input type="hidden" name="enc_user_type" id="enc_user_type">
        <input type="hidden" name="notification_type" id="notification_type">

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="button" id="btn_send_notification" style="display: none;" onclick="send_notification()" class="btn btn-primary" >Send Notification</button>
        </div>
        {{-- </form> --}}
        
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
  var curr_url = "{{$module_url_path.'/get_promo_codes'}}";
  function changeCodeType(ref)
  {
      $('#div_code').hide();
      $('#div_message').hide();
      $('#btn_send_notification').hide();

      var code_type = $(ref).val();

      if(code_type && code_type!="" && code_type!=0){

          $('select[id="code"]').find('option').remove().end().append('<option value="">Select Code</option>').val('');

          $.ajax({
              url:curr_url+'?code_type='+code_type,
              type:'GET',
              data:'flag=true',
              dataType:'json',
             beforeSend:function()
              {
                  $('select[id="code"]').attr('readonly','readonly');
              },
              success:function(response)
              {
                  if(response.status=="success")
                  {
                      // $('#div_message').show();
                      $('#div_code').show();
                      $('select[id="code"]').removeAttr('readonly');

                      if(typeof(response.data) == "object")
                      {
                         var option = '<option value="">Select Code</option>'; 
                         $(response.data).each(function(index,value)
                         {
                              option+='<option value="'+value.code+'-'+value.percentage+'">Code : '+value.code+' with Discount of : '+value.percentage+'%</option>';
                         });

                         $('select[id="code"]').html(option);
                      }

                      

                  }

                  return false;
              },error:function(res){

              }    
          });
      }
      else{
          var option = '<option value="">Select Code</option>'; 
          $('select[id="code"]').html(option);
      }
  }
  function loadCodeDetails(ref){
      
      if($(ref).val()!=undefined && $(ref).val()!=''){
          var tmp_type = $(ref).val().split('-');
          var promo_code = tmp_type[0];
          var promo_percentage = tmp_type[1];

          if(promo_code!=undefined && promo_percentage!=undefined){

              var message = 'Use this promo code ('+promo_code+') and get amazing discount of '+promo_percentage+'% on your next delivery request..!';
              $('#message').val(message);
              $('#div_message').show();
              $('#btn_send_notification').show();

          }
          else{
              $('#message').val('');
              $('#div_message').hide();
              $('#btn_send_notification').hide();
          }
      } 
      else{
          $('#message').val('');
          $('#div_message').hide();
          $('#btn_send_notification').hide();
      }
  }
  function open_notitication_modal(ref)
  {
      $('#enc_id').val('');
      $('#header_title').html('');

      var enc_id      = $(ref).attr('data-user-id');

      var user_name   = $(ref).attr('data-user-name');

      var header_title = 'User Name : '+user_name;

      $('#notification_type').val('single');
      $('#enc_user_id').val(enc_id);
      $('#enc_user_type').val('USER');

      $('#header_title').html(header_title);

      $('#exampleModal').modal('show');
  }
  
  function send_multiple_notification()
  {
      var len = $('input[name="checked_record[]"]:checked').length;
      

      if(len<=0)
      {
          swal("Oops..","Please select the record to perform this Action.");
          return false;
      }
      
      var arr_checked_record =  $('input[name="checked_record[]"]:checked').map(function(){
                                  return $(this).val();
                                }).get(); // <----

      if(arr_checked_record.length<=0)
      {
          swal("Oops..","Please select the record to perform this Action.");
          return false;
      } 
      $('#enc_id').val('');
      $('#header_title').html('');

      var header_title = 'Send Notification to Users';

      $('#notification_type').val('multiple');
      $('#enc_user_id').val(arr_checked_record.toString());
      
      $('#enc_user_type').val('USER');

      $('#header_title').html(header_title);


      $('#exampleModal').modal('show');

  }

  function send_notification()
  {
    if($('#message').val() == "")
    {
       $('#message_error').html('Please enter notification message.');
      return false;
    }
    $('#notification_frm').submit();
    return true;
  }

</script>
@stop