<?php

namespace App\Http\Controllers\Company;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;

use App\Models\UserModel;
use App\Models\VehicleModel;
use App\Models\VehicleTypeModel;
use App\Models\BookingMasterModel;
use App\Models\LoadPostRequestModel;

use Sentinel;
use DB;
use Datatables;
use Excel;
use Flash;


class BookingSummaryController extends Controller
{
   public function __construct(
                 								UserModel $user,
                 								VehicleModel $vehicle,
                                VehicleTypeModel $vehicle_type,
                                BookingMasterModel $booking_type,
                                LoadPostRequestModel $load_post_request  
   							              )
   {
   		  $this->UserModel              = $user;
        $this->VehicleModel           = $vehicle;
        $this->VehicleTypeModel       = $vehicle_type;
        $this->BookingMasterModel     = $booking_type;
        $this->LoadPostRequestModel   = $load_post_request;

 		    $this->arr_view_data                = [];
        $this->module_title                 = "Booking Summary";
        $this->module_view_folder           = "company.booking_summary";
        $this->theme_color                  = theme_color();
        $this->admin_panel_slug             = config('app.project.company_panel_slug');
        $this->module_url_path              = url(config('app.project.company_panel_slug')."/booking_summary");		

        $this->user_profile_public_img_path = url('/').config('app.project.img_path.user_profile_images');
        $this->user_profile_base_img_path   = base_path().config('app.project.img_path.user_profile_images');

        // $user = Sentinel::check();
        $this->company_id = 0;

        $user = Sentinel::check();
        if($user){
            $this->company_id   = isset($user->id) ? $user->id :0;
        }

   }

   public function index(Request $request)
   {
      $filter = "";      

      $obj_normal_services = $this->VehicleTypeModel
                                     ->where('is_active',1)
                                     ->get();                                                         
      $arr_normal_services = [];                               
      if($obj_normal_services)
      {
        $arr_normal_services = $obj_normal_services->toArray();
      }    
      
      $page_title                                 = "Manage ".$this->module_title;
      $this->arr_view_data['arr_normal_services'] = $arr_normal_services;
      $this->arr_view_data['page_title']          = $page_title;
      $this->arr_view_data['module_title']        = $this->module_title;
      $this->arr_view_data['module_url_path']     = $this->module_url_path;
      $this->arr_view_data['theme_color']         = $this->theme_color;
      $this->arr_view_data['filter']              = $filter;

      return view($this->module_view_folder.'.index', $this->arr_view_data);
		
   }
   public function get_records(Request $request)
   {
      
        $arr_current_user_access =[];
        $arr_current_user_access = $request->user()->permissions;
        $obj_bookings        =  $this->get_booking_details($request);
       
        $json_result     = Datatables::of($obj_bookings);

        $json_result     = $json_result->blacklist(['id']);
        $current_context = $this;
        
        // 'TO_BE_PICKED','IN_TRANSIT','COMPLETED','CANCEL_BY_USER','CANCEL_BY_DRIVER'

        $json_result    = $json_result
                                    ->editColumn('build_action_btn',function($data) use ($current_context,$arr_current_user_access)
                                    {
                                        $view_href =  url(config('app.project.company_panel_slug')."/track_booking").'/view?enc_id='.base64_encode($data->id).'&status='.base64_encode($data->booking_status).'&curr_page=booking_history';
                                        $build_view_action = '<a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip" href="'.$view_href.'" title="View"><i class="fa fa-eye" ></i></a>';
                                        return  $build_view_action;
                                        
                                    })
                                    ->editColumn('driver_name',function($data) use ($current_context,$arr_current_user_access)
                                    {
                                        $driver_name       = isset($data->driver_name) ? $data->driver_name :'';
                                        $is_company_driver = isset($data->driver_name) ? $data->is_company_driver :'';
                                        
                                        $company_name = '';

                                        if($is_company_driver == '1'){
                                            $company_name      = isset($data->company_name) ? $data->company_name :'';
                                        }
                                        else if($is_company_driver == '0'){
                                            // $company_name      = isset($data->company_name) ? $data->company_name :'';
                                            $company_name      = config('app.project.name');
                                        }
                                        if($company_name!=''){
                                            return  $data->driver_name.' - '.$company_name;
                                        }
                                        return  $data->driver_name;
                                    })

                                    ->editColumn('vehicle_type',function($data) use ($current_context,$arr_current_user_access)
                                    {
                                        // return  $data->vehicle_name.' - '.$data->vehicle_type;
                                        return  $data->vehicle_type;
                                        
                                    })
                                    ->editColumn('booking_status',function($data) use ($current_context,$arr_current_user_access)
                                    {
                                        $booking_status = '';
                                        if($data->booking_status == 'TO_BE_PICKED')
                                        {
                                            $booking_status = '<span class="badge badge-info" style="width:100px">To be picked</span>';
                                        }
                                        else if($data->booking_status == 'IN_TRANSIT')
                                        {
                                            $booking_status = '<span class="badge badge-warning" style="width:100px">In Transit</span>';
                                        }
                                        else if($data->booking_status == 'COMPLETED')
                                        {
                                            $booking_status = '<span class="badge badge-success" style="width:100px">Completed</span>';
                                        }
                                        else if($data->booking_status == 'CANCEL_BY_USER')
                                        {
                                            $booking_status = '<span class="badge badge-important" style="width:110px">Cancel By User</span>';
                                        }
                                        else if($data->booking_status == 'CANCEL_BY_DRIVER')
                                        {
                                            $booking_status = '<span class="badge badge-important" style="width:115px">Cancel By Driver</span>';
                                        }
                                        return  $booking_status;
                                    })
                                    
                                ->make(true);

        $build_result = $json_result->getData();

      return response()->json($build_result);
    }    

    private function get_booking_details($request)
    {     
        $company_id = $this->company_id;
       
        $booking_details                    = $this->BookingMasterModel->getTable();
        $prefixed_booking_details           = DB::getTablePrefix().$this->BookingMasterModel->getTable();

        $load_post_request_details          = $this->LoadPostRequestModel->getTable();
        $prefixed_load_post_request_details = DB::getTablePrefix().$this->LoadPostRequestModel->getTable();
        
        $user_details                       = $this->UserModel->getTable();
        $prefixed_user_details              = DB::getTablePrefix().$this->UserModel->getTable();

        $vehicle_details                    = $this->VehicleModel->getTable();
        $prefixed_vehicle_details           = DB::getTablePrefix().$this->VehicleModel->getTable();

        $vehicle_type_details               = $this->VehicleTypeModel->getTable();
        $prefixed_type_vehicle_details      = DB::getTablePrefix().$this->VehicleTypeModel->getTable();


        $obj_user = DB::table($booking_details)
                                ->select(DB::raw(   

                                                    $prefixed_booking_details.".id ,".
                                                    $prefixed_booking_details.".booking_unique_id as booking_unique_id,".
                                                   
                                                   "DATE_FORMAT(".$prefixed_booking_details.".booking_date,'%d %b %Y') as booking_date,".
                                                    
                                                    "CONCAT(".$prefixed_user_details.".first_name,' ',"
                                                          .$prefixed_user_details.".last_name) as driver_name,".
                                                    
                                                    "CONCAT(user.first_name,' ',user.last_name) as user_name,".

                                                    $prefixed_user_details.".company_id as company_id,".
                                                    $prefixed_user_details.".is_company_driver as is_company_driver,".

                                                    $prefixed_booking_details.".booking_status as booking_status,".
                                                    // $vehicle_details.".vehicle_name as vehicle_name,".
                                                    $vehicle_type_details.".vehicle_type as vehicle_type,".
                                                    $vehicle_type_details.".id as vehicle_type_id,".
                                                    $prefixed_load_post_request_details.".vehicle_id as vehicle_id,".
                                                    $prefixed_load_post_request_details.".pickup_location as pickup_location,".
                                                    $prefixed_load_post_request_details.".drop_location as drop_location"

                                                ))

                                ->join($prefixed_load_post_request_details,$prefixed_load_post_request_details.'.id','=',$booking_details.'.load_post_request_id')
                                ->join($prefixed_user_details,$user_details.'.id','=',$prefixed_load_post_request_details.'.driver_id')                               
                                ->leftjoin("users AS user", "user.id", '=', $prefixed_load_post_request_details.'.user_id')
                                ->leftjoin("users AS company", "company.id", '=', $user_details.'.company_id')
                                ->join($prefixed_vehicle_details,$vehicle_details.'.id','=',$prefixed_load_post_request_details.'.vehicle_id')
                                ->join($prefixed_type_vehicle_details,$vehicle_type_details.'.id','=',$prefixed_vehicle_details.'.vehicle_type_id')

                                ->where($user_details.'.company_id',$this->company_id)
                                ->where($user_details.'.is_company_driver','1')
                                ->orderBy($booking_details.'.created_at','DESC');

        /* ---------------- Filtering Logic ----------------------------------*/                    

        $arr_search_column = $request->input('column_filter');
        //dd($arr_search_column);
       
       if(isset($arr_search_column['date_filter']) && $arr_search_column['date_filter']!="")
       { 
             $filter = $arr_search_column['date_filter'];
              
            if($filter == "daily")
            {
               $obj_user = $obj_user ->whereDate($prefixed_booking_details.'.booking_date','=',date("Y-m-d")); 
            } 
            else if($filter == "weekly")
            {
              $from_date = date('Y-m-d', strtotime('-7 days'));
              $to_date   = date('Y-m-d');
              $obj_user = $obj_user ->whereDate($prefixed_booking_details.'.booking_date','>=',$from_date)
                                   ->whereDate($prefixed_booking_details.'.booking_date','<=',$to_date);                   
            }
            else if($filter == "monthly")
            {
              $obj_user = $obj_user ->whereMonth($prefixed_booking_details.'.booking_date','=',date("m"));  
            }
        }
       if(isset($arr_search_column['from_date']) && $arr_search_column['from_date']!="" && isset($arr_search_column['to_date']) && $arr_search_column['to_date']!="")
       {
           $from_date =  $arr_search_column['from_date'];
           $to_date   =  $arr_search_column['to_date'];

           $obj_user = $obj_user ->whereDate($prefixed_booking_details.'.booking_date','>=',$from_date)
                                 ->whereDate($prefixed_booking_details.'.booking_date','<=',$to_date); 
           //dd($obj_user->get());

        }

        if(isset($arr_search_column['vehicle_type']) && $arr_search_column['vehicle_type']!="")
        {
          if($arr_search_column['vehicle_type'] != 'all')
          {
             $obj_user = $obj_user->where('vehicle_type_id',$arr_search_column['vehicle_type']);
          }  
        }
      
        if(isset($arr_search_column['booking_unique_id']) && $arr_search_column['booking_unique_id']!="")
        {
            $search_term      = $arr_search_column['booking_unique_id'];
            $obj_user        = $obj_user->where('booking_unique_id','LIKE', '%'.$search_term.'%');
        }

        if(isset($arr_search_column['booking_date']) && $arr_search_column['booking_date']!="")
        {
            $search_term     = $arr_search_column['booking_date'];
            $obj_user        = $obj_user->having('booking_date','LIKE', '%'.$search_term.'%');
        }

        if(isset($arr_search_column['user_name']) && $arr_search_column['user_name']!="")
        {
            $search_term      = $arr_search_column['user_name'];
            $obj_user = $obj_user->having('user_name','LIKE', '%'.$search_term.'%');
        }

        if(isset($arr_search_column['driver_name']) && $arr_search_column['driver_name']!="")
        {
            $search_term      = $arr_search_column['driver_name'];
            $obj_user = $obj_user->having('driver_name','LIKE', '%'.$search_term.'%');
        }
        if(isset($arr_search_column['booking_status']) && $arr_search_column['booking_status']!="")
        {
            $ride_status      = $arr_search_column['booking_status'];
            $obj_user        = $obj_user->where($booking_details.'.booking_status', $ride_status);
        }

        //dd($obj_user->get());
        return $obj_user;
    }   

    public function generate_excel(Request $request)
    {
        $vehicle_type = '';
        if($request->has('vehicle_type') && $request->input('vehicle_type')!="")
        {
          $vehicle_type = $request->input('vehicle_type');
        }
    
        $tbl_name = "Bookings"; 
        $obj_bookings =   $this->BookingMasterModel
                                ->whereHas('load_post_request_details',function($query) use($vehicle_type) {
                                    $query->whereHas('driver_details',function($query){
                                        $query->where('company_id',$this->company_id);
                                        $query->where('is_company_driver','1');
                                    });
                                    $query->whereHas('vehicle_details',function($query3) use($vehicle_type){
                                                $query3->whereHas('vehicle_type_details',function($query2) use($vehicle_type){
                                                    if($vehicle_type!='all')
                                                    {
                                                      $query2->where('id',$vehicle_type);
                                                    }
                                                });
                                              });
                                })   
                                ->with(['load_post_request_details'=>function($query) use($vehicle_type){
                                    $query->with(['driver_details'=>function($query){
                                        $query->select('id','company_id','first_name','last_name','email','mobile_no','profile_image','is_company_driver','company_name');
                                        $query->with('driver_car_details');
                                        $query->where('company_id',$this->company_id);
                                        $query->where('is_company_driver','1');

                                    }]);
                                    $query->with(['user_details'=>function($query){
                                        $query->select('id','first_name','last_name','email','mobile_no','profile_image');
                                      
                                    }]);
                                    $query->with(['vehicle_details'=>function($query3) use($vehicle_type){
                                                  $query3->with(['vehicle_type_details'=>function($query2) use($vehicle_type){
                                                      if($vehicle_type!='all')
                                                      {
                                                         $query2->where('id',$vehicle_type);
                                                      }
                                                  }]);
                                              }]);

                                }]);
                                      
      if($request->has('from_date') && $request->input('from_date')!='')
      {
        $obj_bookings = $obj_bookings->whereDate('booking_date','>=',$request->input('from_date')) ; 
      }
      if($request->has('to_date') && $request->input('to_date')!="")
      {
        $obj_bookings = $obj_bookings->whereDate('booking_date','<=',$request->input('to_date')) ;
      }    

      $obj_bookings = $obj_bookings->get();
    
      $arr_bookings = [];
      $arr_data     = [];

      if($obj_bookings)
      {
         $arr_bookings = $obj_bookings->toArray();
        if(!empty($arr_bookings)) 
        {  
          if(isset($arr_bookings) && sizeof($arr_bookings)>0)
          {
              foreach($arr_bookings as $key=>$booking)
              {
                $arr_tmp = [];
                
                $arr_tmp =  filter_completed_trip_details($booking);
                
                $arr_temp_data = [];

                $arr_temp_data['booking_unique_id']         = isset($arr_tmp['booking_unique_id']) ? $arr_tmp['booking_unique_id'] :'-';
                $arr_temp_data['booking_date']              = isset($arr_tmp['booking_date']) ? date('d M y',strtotime($arr_tmp['booking_date'])) :'-';
                $arr_temp_data['user_name']                 = isset($arr_tmp['user_name']) ? $arr_tmp['user_name'] :'-';
                $arr_temp_data['driver_name']               = isset($arr_tmp['driver_name']) ? $arr_tmp['driver_name'] :'-';
                $arr_temp_data['vehicle_type']              = isset($arr_tmp['vehicle_type']) ? $arr_tmp['vehicle_type'] :'-';
                // $arr_temp_data['vehicle_name']              = isset($arr_tmp['vehicle_name']) ? $arr_tmp['vehicle_name'] :'-';
                $arr_temp_data['vehicle_number']            = isset($arr_tmp['vehicle_number']) ? $arr_tmp['vehicle_number'] :'-';
                $arr_temp_data['start_date']                = isset($arr_tmp['start_datetime']) ? $arr_tmp['start_datetime'] :'-';
                $arr_temp_data['end_date']                  = isset($arr_tmp['end_datetime']) ? $arr_tmp['end_datetime'] :'-';
                $arr_temp_data['pickup_location']           = isset($arr_tmp['pickup_location']) ? $arr_tmp['pickup_location'] :'-';
                $arr_temp_data['drop_location']             = isset($arr_tmp['drop_location']) ? $arr_tmp['drop_location'] :'-';

                $user_contact_no                            = isset($arr_tmp['user_contact_no']) ? $arr_tmp['user_contact_no'] :'-';
                $user_email                                 = isset($arr_tmp['user_email']) ? $arr_tmp['user_email'] :'-';

                $driver_contact_no                          = isset($arr_tmp['driver_contact_no']) ? $arr_tmp['driver_contact_no'] :'-';
                $driver_email                               = isset($arr_tmp['driver_email']) ? $arr_tmp['driver_email'] :'-';

                $arr_temp_data['user_contact_no']           = $user_contact_no.' / '.$user_email;
                                                            
                $arr_temp_data['driver_contact_no']         = $driver_contact_no.' / '.$driver_email;

                $arr_temp_data['distance']                  = isset($arr_tmp['distance']) ? $arr_tmp['distance'] :'-';
                $arr_temp_data['vehicle_owner']             = isset($arr_tmp['vehicle_owner']) ? $arr_tmp['vehicle_owner'] :'-';
                

                // $arr_temp_data['driver_fare_charge']          = '0.0';
                // $arr_temp_data['admin_commission']            = '0.0';
                // $arr_temp_data['admin_per_kilometer_charge']  = '0.0';
                // $arr_temp_data['driver_per_kilometer_charge'] = '0.0';

                // if(isset($arr_tmp['is_individual_vehicle']) && $arr_tmp['is_individual_vehicle'] == '1')
                // {
                //     $arr_temp_data['driver_fare_charge']          = isset($arr_tmp['driver_fare_charge']) ? number_format($arr_tmp['driver_fare_charge'],2) : '0.0';
                //     $arr_temp_data['admin_commission']            = isset($arr_tmp['admin_commission']) ? number_format($arr_tmp['admin_commission'],2) : '0.0';
                // }
                // elseif(isset($arr_tmp['is_individual_vehicle']) && $arr_tmp['is_individual_vehicle'] == '0')
                // {
                //     $arr_temp_data['admin_per_kilometer_charge']  = isset($arr_tmp['admin_per_kilometer_charge']) ? number_format($arr_tmp['admin_per_kilometer_charge'],2) : '0.0';
                //     $arr_temp_data['driver_per_kilometer_charge'] = isset($arr_tmp['driver_per_kilometer_charge']) ? number_format($arr_tmp['driver_per_kilometer_charge'],2) : '0.0';
                // }
                $arr_temp_data['total_amount']                 = isset($arr_tmp['total_amount']) ? number_format($arr_tmp['total_amount'],2) : '0.0';
                $arr_temp_data['applied_promo_code_charge']    = isset($arr_tmp['applied_promo_code_charge']) ? number_format($arr_tmp['applied_promo_code_charge'],2) : '0.0';
                $arr_temp_data['total_charge']                 = isset($arr_tmp['total_charge']) ? number_format($arr_tmp['total_charge'],2) : '0.0';
                $arr_temp_data['admin_earning_amount']         = isset($arr_tmp['admin_earning_amount']) ? number_format($arr_tmp['admin_earning_amount'],2) : '0.0';
                $arr_temp_data['driver_earning_amount']        = isset($arr_tmp['driver_earning_amount']) ? number_format($arr_tmp['driver_earning_amount'],2) : '0.0';
                $arr_temp_data['payment_status']               = isset($arr_tmp['payment_status']) ? ucfirst($arr_tmp['payment_status']) :'';
                $arr_temp_data['payment_type']                 = isset($arr_tmp['payment_type']) ? ucfirst($arr_tmp['payment_type']) :'';
                array_push($arr_data, $arr_temp_data);
              }
          }
          
          $tbl_columns       = [];

          $tbl_columns['0']  = 'Booking ID';
          $tbl_columns['1']  = 'Booking Date';
          $tbl_columns['2']  = 'User Name';
          $tbl_columns['3']  = 'Driver Name';
          $tbl_columns['4']  = 'Vehicle Type';
          // $tbl_columns['5']  = 'Vehicle Name';
          $tbl_columns['5']  = 'Vehicle Number';
          $tbl_columns['6']  = 'Trip Start Date Time';
          $tbl_columns['7']  = 'Trip End Date Time';
          $tbl_columns['8']  = 'Pick Up Location';
          $tbl_columns['9'] = 'Drop Up Location';
          $tbl_columns['10'] = 'User Contact No / Email';
          $tbl_columns['11'] = 'Driver Contact No / Email';
          $tbl_columns['12'] = 'Distance';
          $tbl_columns['13'] = 'Vehicle Owner';
          // $tbl_columns['14'] = 'Driver Fare Charge';
          // $tbl_columns['15'] = 'Admin Commission';
          // $tbl_columns['16'] = 'Admin Per Km Charge';
          // $tbl_columns['17'] = 'Driver Per Km Charge';
          $tbl_columns['14'] = 'Total Amount';
          $tbl_columns['15'] = 'Promo Code Charge';
          $tbl_columns['16'] = 'Total Charge';
          $tbl_columns['17'] = 'Admin Earning/Commission Amount';
          $tbl_columns['18'] = 'Driver Earning/Commission Amount';
          $tbl_columns['19'] = 'Payment Status';
          $tbl_columns['20'] = 'Payment Type';
          
          \Excel::create($tbl_name.'-'.date('Ymd').uniqid(), function($excel) use($tbl_name,$tbl_columns,$arr_data) 
          {
              $excel->sheet('sheet1', function($sheet) use ($arr_data,$tbl_columns) {
                    $sheet->cell('A1', function($cell) {
                      $cell->setValue('Generated on :'.date("d-m-Y H:i:s"));
                      $cell->setAlignment('center');
                     });
                    $sheet->row(2,$tbl_columns); 
                    $sheet->rows($arr_data);
                  });
          })->export('csv');   
      }
      else
      {
         Flash::error(' Record not given');
      }   
    }
        return redirect()->back();
   }
}
