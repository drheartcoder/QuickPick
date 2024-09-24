    @extends('admin.layout.master')                


    @section('main_content')

    
    <style>
        .error{
          color: red;
        }
        label {
            font-size: 14px;
        }
        .popup-details
        {
          cursor:pointer;
          background-color: #ccc; 
          border-radius: 45px; 
          padding: 0px 8px; 
          color: #000; 
          border: 1px #000 !important;
        }  
        #vehicle_image .modal-dialog {
            width: 741px;
            margin: 200px auto;
        }
    </style>
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
                <a href="{{ url($admin_panel_slug.'/dashboard') }}">Dashboard</a>
            </li>
            <span class="divider">
                <i class="fa fa-angle-right"></i>
            </span>
            <li>
                <i class="fa fa-truck"></i>
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

          @include('admin.layout._operation_status')  
           {!! Form::open([ 'url' => $module_url_path.'/store',
                                 'method'=>'POST',
                                 'enctype' =>'multipart/form-data',   
                                 'class'=>'form-horizontal'
                                 
                                ]) !!} 

           {{ csrf_field() }}

            <div class="row">                         
                <h3 style="margin-left: 20px !important;">Vehicle Dimensions:
                    <a cla data-trigger="hover" data-placement="top" data-content="Where can I get this?" data-toggle="modal" data-target="#vehicle_image" class="show-popover popup-details"> <i style="color: #fff;" class="fa fa-question"></i> </a>
                </h3>               
            </div>

            <div class="row">  
              <div class="container">
                <div class="col-md-12" style="border:1px solid #ccc; padding: 10px;">
                  <div class="row">
                    <div class="col-lg-6">
                      
                      <div class="form-group" >
                            <label class="col-sm-3 col-lg-5 control-label"> Vehicle Type <i style="color: red;">*</i></label>
                            <div class="col-sm-9 col-lg-7 controls" >
                                <div class="form-group">
                                  <input type="text" class="form-control" name="vehicle_type" id="vehicle_type" placeholder="Enter vehicle type"  value="{{ old('vehicle_type') }}" />
                                </div>  
                                <span id="err_vehicle_type" class="help-block" style="margin-top: 1%; margin-left: 1.5%;">{{ $errors->first('vehicle_type') }}</span>
                            </div>                     
                      </div>

                      <div class="form-group" >
                            <label class="col-sm-3 col-lg-5 control-label"> Starting Price (USD) <i style="color: red;">*</i></label>
                            <div class="col-sm-9 col-lg-7 controls" >
                                <div class="form-group">
                                  <input type="text" class="form-control" name="starting_price" id="starting_price" placeholder="Enter Starting price"  value="{{ old('starting_price') }}" />
                                </div>  
                                <span id="err_starting_price" class="help-block" style="margin-top: 1%; margin-left: 1.5%;">{{ $errors->first('starting_price') }}</span>
                            </div>                     
                      </div>

                      <div class="form-group" >
                            <label class="col-sm-3 col-lg-5 control-label"> Per Miles Price (USD) <i style="color: red;">*</i></label>
                            <div class="col-sm-9 col-lg-7 controls" >
                                <div class="form-group">
                                  <input type="text" class="form-control" name="per_miles_price" id="per_miles_price" placeholder="Enter Per Miles price"  value="{{ old('per_miles_price') }}" />
                                </div>  
                                <span id="err_per_miles_price" class="help-block" style="margin-top: 1%; margin-left: 1.5%;">{{ $errors->first('per_miles_price') }}</span>
                            </div>                     
                      </div>

                      <div class="form-group" >
                            <label class="col-sm-3 col-lg-5 control-label"> Per Minute Price (USD) <i style="color: red;">*</i></label>
                            <div class="col-sm-9 col-lg-7 controls" >
                                <div class="form-group">
                                  <input type="text" class="form-control" name="per_minute_price" id="per_minute_price" placeholder="Enter Per Minute Price"  value="{{ old('per_minute_price') }}" />
                                </div>  
                                <span id="err_per_minute_price" class="help-block" style="margin-top: 1%; margin-left: 1.5%;">{{ $errors->first('per_minute_price') }}</span>
                            </div>                     
                      </div>

                      <div class="form-group" >
                            <label class="col-sm-3 col-lg-5 control-label"> Minimum price (USD) <i style="color: red;">*</i></label>
                            <div class="col-sm-9 col-lg-7 controls" >
                                <div class="form-group">
                                  <input type="text" class="form-control" name="minimum_price" id="minimum_price" placeholder="Enter Minimum price"  value="{{ old('minimum_price') }}" />
                                </div>  
                                <span id="err_minimum_price" class="help-block" style="margin-top: 1%; margin-left: 1.5%;">{{ $errors->first('minimum_price') }}</span>
                            </div>                     
                      </div>


                      <div class="form-group" >
                            <label class="col-sm-3 col-lg-5 control-label"> Cancellation Price (USD) <i style="color: red;">*</i></label>
                            <div class="col-sm-9 col-lg-7 controls" >
                                <div class="form-group">
                                  <input type="text" class="form-control" name="cancellation_base_price" id="cancellation_base_price" placeholder="Enter Cancellation price"  value="{{ old('cancellation_base_price') }}" />
                                </div>  
                                <span id="err_cancellation_base_price" class="help-block" style="margin-top: 1%; margin-left: 1.5%;">{{ $errors->first('cancellation_base_price') }}</span>
                            </div>                     
                      </div>

                      <div class="form-group" >
                            <label class="col-sm-3 col-lg-5 control-label"> No of Pallet <i style="color: red;">*</i></label>
                            <div class="col-sm-9 col-lg-7 controls" >
                                <div class="form-group">
                                  <input type="text" class="form-control" name="no_of_pallet" id="no_of_pallet" placeholder="Enter No of Pallet"  value="{{ old('no_of_pallet') }}" />
                                </div>  
                                <span id="err_no_of_pallet" class="help-block" style="margin-top: 1%; margin-left: 1.5%;">{{ $errors->first('no_of_pallet') }}</span>
                            </div>                     
                      </div>

                      <div class="form-group" >
                            <label class="col-sm-3 col-lg-5 control-label"> Is Usdot Document Required <i style="color: red;">*</i></label>
                            <div class="col-sm-9 col-lg-7 controls" >
                                <div class="form-group">
                                  <select class="form-control" name="is_usdot_required" id="is_usdot_required">
                                     <option value="">Select</option>     
                                     <option value="1">Yes</option>     
                                     <option value="0">No</option>     
                                  </select>

                                </div>  
                                <span id="err_is_usdot_required" class="help-block" style="margin-top: 1%; margin-left: 1.5%;">{{ $errors->first('is_usdot_required') }}</span>
                            </div>                     
                      </div>

                       <div class="form-group" >
                            <label class="col-sm-3 col-lg-5 control-label"> Is Mc Document Required <i style="color: red;">*</i></label>
                            <div class="col-sm-9 col-lg-7 controls" >
                                <div class="form-group">
                                  <select class="form-control" name="is_mcdoc_required" id="is_mcdoc_required">
                                     <option value="">Select</option>     
                                     <option value="1">Yes</option>     
                                     <option value="0">No</option>     
                                  </select>

                                </div>  
                                <span id="err_is_mcdoc_required" class="help-block" style="margin-top: 1%; margin-left: 1.5%;">{{ $errors->first('is_mcdoc_required') }}</span>
                            </div>                     
                      </div>

                    </div>

                  </div>
                </div>                    
              </div>   
            </div>    
            <br>

            <div class="row">  
              <div class="container">
                <div class="col-md-12" style="border:1px solid #ccc; padding: 10px;">
                  <div class="row">
                    <div class="col-lg-6">
                      <div class="form-group" >
                            <label class="col-sm-3 col-lg-5 control-label"> Min. Length <i style="color: red;">*</i></label>
                            <div class="col-sm-9 col-lg-7 controls" >
                                <div class="input-group">
                                  <input type="text" class="form-control" name="vehicle_min_length" id="vehicle_min_length" onblur="total_volume()" placeholder="Enter minimum length" maxlength="16" minlength="1" value="{{ old('vehicle_min_length') }}" />
                                  <span class="input-group-addon">ft</span>
                                </div>  
                                  <span id="err_vehicle_min_length" class="help-block error">{{ $errors->first('vehicle_min_length') }}</span>                           
                            </div>                     
                      </div>
                      <div class="form-group" >
                            <label class="col-sm-3 col-lg-5 control-label"> Min. Height <i style="color: red;">*</i></label>
                            <div class="col-sm-9 col-lg-7 controls" >
                              <div class="input-group">
                                <input type="text" class="form-control" name="vehicle_min_height" id="vehicle_min_height" onblur="total_volume()" placeholder="Enter minimum height" maxlength="16" minlength="1" value="{{ old('vehicle_min_height') }}" />
                                <span class="input-group-addon">ft</span>
                                </div>
                                <span id="err_vehicle_min_height" class="help-block error">{{ $errors->first('vehicle_min_height') }}</span>                            
                            </div>
                      </div>
                      <div class="form-group" >
                            <label class="col-sm-3 col-lg-5 control-label"> Min. Breadth <i style="color: red;">*</i></label>
                            <div class="col-sm-9 col-lg-7 controls" >
                              <div class="input-group">
                                <input type="text" class="form-control" name="vehicle_min_breadth" id="vehicle_min_breadth" onblur="total_volume()" placeholder="Enter minimum breadth" maxlength="16" minlength="1"  value="{{ old('vehicle_min_breadth') }}" />
                                <span class="input-group-addon">ft</span>
                                </div>
                                <span id="err_vehicle_min_breadth" class="help-block error">{{ $errors->first('vehicle_min_breadth') }}</span>
                            </div>
                      </div>
                      <div class="form-group" >
                            <label class="col-sm-3 col-lg-5 control-label"> Min. Volume <i style="color: red;">*</i></label>
                            <div class="col-sm-9 col-lg-7 controls" >
                              <div class="input-group">
                                  <input type="text" class="form-control" name="vehicle_min_volume" id="vehicle_min_volume" placeholder="Vehicle minimum volume" readonly="" />
                                  <span class="input-group-addon">ft<sup>3</sup></span>
                              </div>
                                  <span class="help-block error">{{ $errors->first('vehicle_min_volume') }}</span>                          
                            </div>
                      </div>
                      <div class="form-group" >
                            <label class="col-sm-3 col-lg-5 control-label"> Min. Weight Capacity <i style="color: red;">*</i></label>
                            <div class="col-sm-9 col-lg-7 controls" >
                                <div class="input-group">
                                  <input type="text" class="form-control" name="vehicle_min_weight" id="vehicle_min_weight" placeholder="Enter minimum weight capacity" value="{{ old('vehicle_min_weight') }}" />
                                  <span class="input-group-addon">lb</span>
                                </div>  
                                  <span id="err_vehicle_min_weight" class="help-block error">{{ $errors->first('vehicle_min_weight') }}</span>                                
                            </div>
                      </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group" >
                              <label class="col-sm-3 col-lg-5 control-label"> Max. Length <i style="color: red;">*</i></label>
                              <div class="col-sm-9 col-lg-7 controls" >
                                <div class="input-group">
                                  <input type="text" class="form-control" name="vehicle_max_length" id="vehicle_max_length" placeholder="Enter maximum length" onblur="total_volume()" value="{{ old('vehicle_max_length') }}" />
                                  <span class="input-group-addon">ft</span>
                                </div>    
                                  <span id="err_vehicle_max_length" class="help-block error">{{ $errors->first('vehicle_max_length') }}</span> 
                              </div>
                        </div>
                        
                        <div class="form-group" >
                              <label class="col-sm-3 col-lg-5 control-label"> Max. Height <i style="color: red;">*</i></label>
                              <div class="col-sm-9 col-lg-7 controls" >
                                <div class="input-group">
                                  <input type="text" class="form-control" name="vehicle_max_height" id="vehicle_max_height" placeholder="Enter maximum height" onblur="total_volume()" value="{{ old('vehicle_max_height') }}" />
                                  <span class="input-group-addon">ft</span>
                                </div>    
                                  <span id="err_vehicle_max_height" class="help-block error">{{ $errors->first('vehicle_max_height') }}</span>
                              </div>
                        </div>
                        
                        <div class="form-group" >
                              <label class="col-sm-3 col-lg-5 control-label"> Max. Breadth <i style="color: red;">*</i></label>
                              <div class="col-sm-9 col-lg-7 controls" >
                                <div class="input-group">
                                  <input type="text" class="form-control" name="vehicle_max_breadth" id="vehicle_max_breadth" placeholder="Enter maximum breadth" onblur="total_volume()" value="{{ old('vehicle_max_breadth') }}" />
                                  <span class="input-group-addon">ft</span>
                                </div>  
                                  <span id="err_vehicle_max_breadth" class="help-block error">{{ $errors->first('vehicle_max_breadth') }}</span>
                              </div>
                        </div>
                        <div class="form-group" >
                              <label class="col-sm-3 col-lg-5 control-label"> Max. Volume <i style="color: red;">*</i></label>
                              <div class="col-sm-9 col-lg-7 controls" >
                                <div class="input-group">
                                  <input type="text" class="form-control" name="vehicle_max_volume" id="vehicle_max_volume" placeholder="Vehicle maximum volume" readonly="true" />
                                  <span class="input-group-addon">ft<sup>3</sup></span>
                                </div>  
                                  <span class="help-block error"></span>
                              </div>
                        </div>
                        <div class="form-group" >
                              <label class="col-sm-3 col-lg-5 control-label"> Max. Weight Capacity <i style="color: red;">*</i></label>
                              <div class="col-sm-9 col-lg-7 controls" >
                                <div class="input-group">
                                  <input type="text" class="form-control" name="vehicle_max_weight" id="vehicle_max_weight" placeholder="Enter maximum weight capacity" value="{{ old('vehicle_max_weight') }}" />
                                  <span class="input-group-addon">lb</span>
                                </div>    
                                <span id="err_vehicle_max_weight" class="help-block error">{{ $errors->first('vehicle_max_weight') }}</span>
                              </div>
                        </div>
                    </div> 
                  </div>
                </div>  
                <div class="col-md-12" style="margin-top: 15px">
                  <div class="form-group" >
                    <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2">
                       
                        {!! Form::submit('Add',['class'=>'btn btn btn-primary','id'=>'add_vehicle_type','value'=>'true'])!!}
                        &nbsp;
                       <a class="btn" href="{{ $module_url_path }}">Back</a>
                   </div>
                  </div>
                </div>                       
              </div>   
            </div>    
          {!! Form::close() !!}
      </div>
    </div>
  </div>  

<!-- Modal -->
<div id="vehicle_image" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-body">
      <button type="button" class="close" data-dismiss="modal">&times;</button>       
      <img src={{ url("uploads/Truck_front.png")}} alt=""/>
      <img src={{ url("uploads/Truck_side_view.png")}} alt=""/>  
      </div>
    </div>
  </div>
</div>
  
  
  <!-- END Main Content -->

  <script>
      $(document).ready(function(){   
          $('.form-horizontal input').on('input',function(){
            if($(this).val() != '' ){
              $(this).parent().next().html('');
            }
          }); 

          $('#is_usdot_required').on('change',function(){
            if($(this).val() != '' ){
              $(this).parent().next().html('');
            }
          });
          $('#is_mcdoc_required').on('change',function(){
            if($(this).val() != '' ){
              $(this).parent().next().html('');
            }
          });
      });

      function total_volume()
      {
          var vehicle_min_length  = $('#vehicle_min_length').val();
          var vehicle_min_height  = $('#vehicle_min_height').val();
          var vehicle_min_breadth = $('#vehicle_min_breadth').val();
          var vehicle_max_length  = $('#vehicle_max_length').val();
          var vehicle_max_height  = $('#vehicle_max_height').val();
          var vehicle_max_breadth = $('#vehicle_max_breadth').val();
          var total_min_volume = vehicle_min_length * vehicle_min_height * vehicle_min_breadth;
          var total_max_volume = vehicle_max_length * vehicle_max_height * vehicle_max_breadth;       
           
          document.getElementById("vehicle_min_volume").value = total_min_volume.toFixed(2);
          document.getElementById("vehicle_max_volume").value = total_max_volume.toFixed(2);    
      }

      $('#add_vehicle_type').on('click',function(){
          
          var vehicle_type        = $('#vehicle_type').val();
  

          var starting_price          = $('#starting_price').val();
          var per_miles_price         = $('#per_miles_price').val();
          var per_minute_price        = $('#per_minute_price').val();
          var minimum_price           = $('#minimum_price').val();
          var cancellation_base_price = $('#cancellation_base_price').val();
          var no_of_pallet = $('#no_of_pallet').val();

          var vehicle_min_length  = $('#vehicle_min_length').val();
          var vehicle_min_height  = $('#vehicle_min_height').val();
          var vehicle_min_breadth = $('#vehicle_min_breadth').val();
          var vehicle_min_weight  = $('#vehicle_min_weight').val();
          var vehicle_max_length  = $('#vehicle_max_length').val();
          var vehicle_max_height  = $('#vehicle_max_height').val();
          var vehicle_max_breadth = $('#vehicle_max_breadth').val();
          var vehicle_max_weight  = $('#vehicle_max_weight').val();
          var is_usdot_required   = $('#is_usdot_required').val();
          var is_mcdoc_required  = $('#is_mcdoc_required').val();

         
          var pattern1 = /^[A-z ]+$/;
          var pattern2 = /^[A-z ]+$/;
          var filter = /^[0-9-+]+$/; 
          var filter1 = /^[0-9-+]+$/;
          var filter2 = /^[0-9]\d*(\.\d{0,10})?$/; 
          var filter3 = /^[1-9]\d*(\.\d{0,2})?$/; 
          var flag = 0;


            
          if($.trim(vehicle_type) == "")
          {
              $('#err_vehicle_type').html('Please enter vehicle type');              
              flag = 1;               
          }
          // else
          // {
          //     if (!pattern1.test(vehicle_type))
          //     {
          //        $('#err_vehicle_type').html('Please enter only alphabate');                 
          //        flag = 1;
          //     }
          // }

          if($.trim(starting_price) == "")
          {
              $('#err_starting_price').html('Please enter Starting Price');              
              flag = 1;               
          }
          else
          {
              /*if (!filter3.test(starting_price)) 
              {
                  $('#err_starting_price').html('Please entered number allowed upto 2 decimal points');
                  flag = 1;               
              }                          
              else*/ if(starting_price < 0)
              {
                $('#err_starting_price').html('Please enter length greater than 0');
                  flag = 1;
              }
          }

          if($.trim(per_miles_price) == "")
          {
              $('#err_per_miles_price').html('Please enter per miles price');              
              flag = 1;               
          }
          else
          {
              /*if (!filter3.test(per_miles_price)) 
              {
                  $('#err_per_miles_price').html('Please entered number allowed upto 2 decimal points');
                  flag = 1;               
              }                          
              else*/ if(per_miles_price < 0)
              {
                $('#err_per_miles_price').html('Please enter length greater than 0');
                  flag = 1;
              }
          }

          if($.trim(per_minute_price) == "")
          {
              $('#err_per_minute_price').html('Please enter per minute price');              
              flag = 1;               
          }
          else
          {
              /*if (!filter3.test(per_minute_price)) 
              {
                  $('#err_per_minute_price').html('Please entered number allowed upto 2 decimal points');
                  flag = 1;               
              }                          
              else*/ if(per_minute_price < 0)
              {
                $('#err_per_minute_price').html('Please enter length greater than 0');
                  flag = 1;
              }
          }

          if($.trim(minimum_price) == "")
          {
              $('#err_minimum_price').html('Please enter minimum price');              
              flag = 1;               
          }
          else
          {
              /*if (!filter3.test(minimum_price)) 
              {
                  $('#err_minimum_price').html('Please entered number allowed upto 2 decimal points');
                  flag = 1;               
              }                          
              else*/ if(minimum_price < 0)
              {
                $('#err_minimum_price').html('Please enter length greater than 0');
                  flag = 1;
              }
          }

          if($.trim(cancellation_base_price) == "")
          {
              $('#err_cancellation_base_price').html('Please enter cancellation base price');              
              flag = 1;               
          }
          else
          {
              /*if (!filter3.test(cancellation_base_price)) 
              {
                  $('#err_cancellation_base_price').html('Please entered number allowed upto 2 decimal points');
                  flag = 1;               
              }                          
              else*/ if(cancellation_base_price < 0)
              {
                $('#err_cancellation_base_price').html('Please enter length greater than 0');
                  flag = 1;
              }
          }

          if($.trim(no_of_pallet) == "")
          {
              $('#err_no_of_pallet').html('Please enter number of pallet');              
              flag = 1;               
          }
          else
          {
              if (!filter.test(no_of_pallet)) 
              {
                  $('#err_no_of_pallet').html('Please entered valid no of pallet');
                  flag = 1;               
              }                          
              else if(no_of_pallet < 0)
              {
                $('#err_no_of_pallet').html('Please enter length greater than 0');
                  flag = 1;
              }
          }
          
          if($.trim(is_usdot_required) == "")
          {
              $('#err_is_usdot_required').html('Please select is usdot document required type');            
              flag = 1;
          }
          

          if($.trim(is_mcdoc_required) == "")
          {
              $('#err_is_mcdoc_required').html('Please select is mc document required type');            
              flag = 1;
          }

          if($.trim(vehicle_min_length) == "")
          {
              $('#err_vehicle_min_length').html('Please enter length');            
              flag = 1;
          }
          else
          {
              if (!filter2.test(vehicle_min_length)) 
              {
                  $('#err_vehicle_min_length').html('Please entered number allowed upto 10 decimal points');
                  flag = 1;               
              }                          
              /*else if(vehicle_min_length <= 0)
              {
                $('#err_vehicle_min_length').html('Please enter length greater than 0');
                  flag = 1;
              }*/
          } 

          if($.trim(vehicle_max_length) == "")
          {
              $('#err_vehicle_max_length').html('Please enter length');
              flag = 1;             
          }
          else
          {
              console.log(vehicle_max_length,vehicle_min_length);

              if (!filter2.test(vehicle_max_length)) 
              {
                  $('#err_vehicle_max_length').html('Please entered number allowed upto 10 decimal points');
                  flag = 1;               
              }
              /*else if(vehicle_max_length <= 0)
              {
                $('#err_vehicle_max_length').html('Please enter length greater than 0 or not 0');
                  flag = 1;
              }*/
              else if(parseFloat(vehicle_max_length) <= parseFloat(vehicle_min_length))
              {
                $('#err_vehicle_max_length').html('Max. length should be greater than min. length');
                  flag = 1;
              }
          } 
          if($.trim(vehicle_min_height) == "")
          {
              $('#err_vehicle_min_height').html('Please enter height');
              flag = 1;
             
          }
          else
          {
              if (!filter2.test(vehicle_min_height)) 
              {
                  $('#err_vehicle_min_height').html('Please entered number allowed upto 10 decimal points');
                  flag = 1;               
              }
              /*else if(vehicle_min_height <= 0)
              {
                $('#err_vehicle_min_height').html('Please enter Height greater than 0');
                  flag = 1;
              }*/
          } 

          if($.trim(vehicle_max_height) == "")
          {
              $('#err_vehicle_max_height').html('Please enter height');
              flag = 1;             
          }
          else
          {
              if (!filter2.test(vehicle_max_height)) 
              {
                  $('#err_vehicle_max_height').html('Please entered number allowed upto 10 decimal points');
                  flag = 1;               
              }
              /*else if(vehicle_max_height <= 0)
              {
                $('#err_vehicle_max_height').html('Please enter height greater than 0');
                  flag = 1;
              }*/
              else if(parseFloat(vehicle_max_height) <= parseFloat(vehicle_min_height))
              {
                $('#err_vehicle_max_height').html('Max. height should be greater than min. height');
                  flag = 1;
              }
          } 

          if($.trim(vehicle_min_breadth) == "")
          {
              $('#err_vehicle_min_breadth').html('Please enter breadth');
              flag = 1;             
          }
          else
          {
              if (!filter2.test(vehicle_min_breadth)) 
              {
                  $('#err_vehicle_min_breadth').html('Please entered number allowed upto 10 decimal points');
                  flag = 1;               
              }
              /*else if(vehicle_min_breadth <= 0)
              {
                $('#err_vehicle_min_breadth').html('Please enter breadth greater than 0');
                  flag = 1;
              }              */
          } 

          if($.trim(vehicle_max_breadth) == "")
          {
              $('#err_vehicle_max_breadth').html('Please enter breadth');
              flag = 1;             
          }
          else
          {
              if (!filter2.test(vehicle_max_breadth)) 
              {
                  $('#err_vehicle_max_breadth').html('Please entered number allowed upto 10 decimal points');
                  flag = 1;               
              }
              /*else if(vehicle_max_breadth <= 0 )
              {
                $('#err_vehicle_max_breadth').html('Please enter Breadth greater than 0');
                  flag = 1;
              }*/
              else if(parseFloat(vehicle_max_breadth) <= parseFloat(vehicle_min_breadth))
              {
                $('#err_vehicle_max_breadth').html('Max. breadth should be greater than min. breadth');
                  flag = 1;
              }
          } 

          if($.trim(vehicle_min_weight) == "")
          {            
              $('#err_vehicle_min_weight').html('Please enter vehicle capacity weight');            
              flag = 1;             
          }
          else
          {
              if (!filter2.test(vehicle_min_weight)) 
              {
                  $('#err_vehicle_min_weight').html('Please entered number allowed upto 10 decimal points');
                  flag = 1;               
              }
              /*else if(vehicle_min_weight <= 0 )
              {
                $('#err_vehicle_min_weight').html('Please enter weight greater than 0');
                  flag = 1;
              }*/
          } 

          if($.trim(vehicle_max_weight) == "")
          {            
              $('#err_vehicle_max_weight').html('Please enter vehicle capacity weight');            
              flag = 1;             
          }
          else
          {
              if (!filter2.test(vehicle_max_weight)) 
              {
                  $('#err_vehicle_max_weight').html('Please entered number allowed upto 10 decimal points');
                  flag = 1;               
              }
             /* else if(vehicle_max_weight <= 0 )
              {
                $('#err_vehicle_max_weight').html('Please enter weight greater than 0');
                  flag = 1;
              }*/
              else if(parseFloat(vehicle_max_weight) <= parseFloat(vehicle_min_weight))
              {
                $('#err_vehicle_max_weight').html('Max. weight should be greater than min. weight');
                  flag = 1;
              }
          } 

          if(flag == 1)
          {
            return false;    
          }
          else
          {
            return true;
          }
    });
  </script>
  <script>
    $(document).ready(function(){
        $('[data-toggle="tooltip"]').tooltip();   
    });
</script>

@stop                    
