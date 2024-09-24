<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\UserModel;
use App\Models\RoleModel;
use App\Models\UserRoleModel;
use App\Models\VehicleModel;

use App\Models\VehicleTypeModel;
use App\Models\ReviewModel;
use App\Models\BookingMasterModel;
use App\Models\LoadPostRequestModel;


use Datatables;
use DB;
use Excel;
use Session;
use Flash;
class ReportController extends Controller
{
   	 public function __construct(
                                UserRoleModel             $user_role,
                                RoleModel                 $roles,
                                ReviewModel               $review,
    	                        UserModel                 $user_model,
                                VehicleTypeModel          $vehicle_type,
                                BookingMasterModel        $booking_master,
                                LoadPostRequestModel      $load_post_request,
                                VehicleModel              $vehicle
                                )
    {
    	$this->UserModel                    = $user_model;
        $this->ReviewModel                  = $review;
    	$this->UserRoleModel                = $user_role;
    	$this->BaseModel                    = $user_model;
    	$this->RoleModel 				    = $roles;
        $this->BookingMasterModel           = $booking_master;
        $this->LoadPostRequestModel         = $load_post_request;
        $this->VehicleModel                 = $vehicle;
    	$this->VehicleTypeModel             = $vehicle_type;

    	$this->arr_view_data                = [];
        $this->module_title                 = "Report";
    	$this->ride_module_title            = "Bookings Report";
        $this->rating_module_title          = "Rating";
        $this->user_module_title            = "Users Report";
        $this->driver_module_title          = "Drivers Report";
    	$this->module_view_folder           = "admin.report";
    	$this->theme_color                  = theme_color();
    	$this->admin_panel_slug             = config('app.project.admin_panel_slug');
    	$this->module_url_path              = url(config('app.project.admin_panel_slug')."/report");		

    	$this->user_profile_public_img_path = url('/').config('app.project.img_path.user_profile_images');
    	$this->user_profile_base_img_path   = base_path().config('app.project.img_path.user_profile_images');
    }

    public function index(Request $request)
    {	
	
        $report_type = 'user';
        if($request->has('report_type') && $request->input('report_type')!='')
        {
            $report_type = $request->input('report_type');
        }                      
        $this->arr_view_data['page_title']          = "Manage ".str_plural($this->module_title);
        $this->arr_view_data['rating_module_title'] = str_plural($this->rating_module_title);
        $this->arr_view_data['ride_module_title']   = $this->ride_module_title;
        $this->arr_view_data['user_module_title']   = $this->user_module_title;
        $this->arr_view_data['driver_module_title'] = $this->driver_module_title;
        $this->arr_view_data['user_type']           = "";
        $this->arr_view_data['module_url_path']     = $this->module_url_path;
        $this->arr_view_data['theme_color']         = $this->theme_color;

         $view_name = $this->module_view_folder.'.index';
        
        if($report_type == 'user')
        {
            $view_name = $this->module_view_folder.'.index';
        }
        else if($report_type == 'driver')
        {
            $view_name = $this->module_view_folder.'.driver_report';
        }
        else if($report_type == 'booking')
        {
            $view_name = $this->module_view_folder.'.booking_report';
        }
       
        return view($view_name, $this->arr_view_data);          	
    }

   /************ Get user list from database to build datatable *****************/
   public function get_user_records(Request $request)
   {	
        $obj_user                = $this->get_user_details($request);

        $current_context         = $this;

        $json_result             = Datatables::of($obj_user);

        $json_result             = $json_result->blacklist(['id']);

        $json_result             = $json_result->editColumn('gender',function($data) use ($current_context)
                                {      
                                    $gender ='';
                                    if ($data->gender=='M') {
                                       $gender = 'Male'; 
                                    }
                                    else
                                    {
                                        $gender = 'Female';
                                    }
                                    return $gender; 
                                })
                            ->make(true);

        $build_result = $json_result->getData();
            
        return response()->json($build_result);
    }

    /************ Build query to generate rider list and send result to get_rider_records function *****************/
    private function get_user_details(Request $request)
    {     
        
        $arr_search_column        = $request->input('column_filter');
        $user_type                = 'rider';

        $user_details             = $this->BaseModel->getTable();
        $prefixed_user_details    = DB::getTablePrefix().$this->BaseModel->getTable();

        $user_role_table          = $this->UserRoleModel->getTable();
        $prefixed_user_role_table = DB::getTablePrefix().$this->UserRoleModel->getTable();

        $role_table               = $this->RoleModel->getTable();
        $prefixed_role_table      = DB::getTablePrefix().$this->RoleModel->getTable();

    	
       	$obj_user = DB::table($user_details)
                                ->select(DB::raw($prefixed_user_details.".id as id,".
                                                 $prefixed_user_details.".email as email, ".
                                                 $prefixed_user_details.".mobile_no as contact_number, ".
                                                 $prefixed_user_details.".address as address, ".
                                                 "DATE_FORMAT(".$prefixed_user_details.".created_at,'%d %b %Y') as register_date,".
                                                 $prefixed_user_details.".gender as gender, ".
                                                 "CONCAT(".$prefixed_user_details.".first_name,' ',"
                                                          .$prefixed_user_details.".last_name) as user_name"
                                                 ))
                                ->join($user_role_table,$user_details.'.id','=',$user_role_table.'.user_id')
                                ->join($role_table, function ($join) use($role_table,$user_role_table) {
                                    $join->on($role_table.'.id', ' = ',$user_role_table.'.role_id')
                                         ->where('slug','=','user');
                                })
                                ->whereNull($user_details.'.deleted_at')
                                ->orderBy($user_details.'.created_at','DESC');

        $arr_search_column = $request->input('column_filter');
                            
        if(isset($arr_search_column['from_date']) && $arr_search_column['from_date']!="" && isset($arr_search_column['to_date']) && $arr_search_column['to_date']!="")
        {
           $from_date =  $arr_search_column['from_date'];
           $to_date   =  $arr_search_column['to_date'];

           $obj_user = $obj_user ->whereDate($prefixed_user_details.'.created_at','>=',$from_date)
                                 ->whereDate($prefixed_user_details.'.created_at','<=',$to_date); 
        }
        if(isset($arr_search_column['date_filter']) && $arr_search_column['date_filter']!="")
        { 
            $filter = $arr_search_column['date_filter'];

            if($filter == "daily")
            {
               $obj_user = $obj_user ->whereDate($prefixed_user_details.'.created_at','=',date("Y-m-d"));
            } 
            else if($filter == "weekly")
            {
              $from_date = date('Y-m-d', strtotime('-7 days'));
              $to_date   = date('Y-m-d');
              $obj_user = $obj_user->whereDate($prefixed_user_details.'.created_at','>=',$from_date)
                                   ->whereDate($prefixed_user_details.'.created_at','<=',$to_date);
            }
            else if($filter == "monthly")
            {
              $obj_user = $obj_user ->whereMonth($prefixed_user_details.'.created_at','=',date("m"));
            }
        }                 
        return $obj_user;
    }   

    /************ Get drivers list from database to build datatable *****************/
    public function get_driver_records(Request $request)
    {
        $arr_current_user_access =[];
     
        $obj_user        = $this->get_driver_details($request);

        $current_context = $this;

        $json_result     = Datatables::of($obj_user);

        $json_result     = $json_result->blacklist(['id']);

        $json_result     = $json_result->editColumn('gender',function($data) use ($current_context)
                            {       
                                $gender ='';
                                if ($data->gender=='M') {
                                   $gender = 'Male'; 
                                }
                                else
                                {
                                    $gender = 'Female';
                                }
                                return $gender; 
                            })    
                            ->editColumn('build_action_btn',function($data) use ($current_context)
                            {                                      
                               // $view_href =  /*$this->module_url_path.'/view/'.base64_encode($data->id)*/"";
                                /*$build_view_action = '<a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip btn-delets" href="'.$view_href.'" title="View"><i class="fa fa-eye" ></i></a>';
                                
                                return  $build_view_action;*/
                            })
                            ->make(true);

        $build_result = $json_result->getData();
        return response()->json($build_result);
    }
     
    /************ Build query to generate driver list and send result to get_driver_records function *****************/   
    private function get_driver_details(Request $request)
    {     
        $role                     = 'driver';

        $user_details             = $this->BaseModel->getTable();
        $prefixed_user_details    = DB::getTablePrefix().$this->BaseModel->getTable();

        $user_role_table          = $this->UserRoleModel->getTable();
        $prefixed_user_role_table = DB::getTablePrefix().$this->UserRoleModel->getTable();

        $role_table               = $this->RoleModel->getTable();
        $prefixed_role_table      = DB::getTablePrefix().$this->RoleModel->getTable();
        
        $obj_user = DB::table($user_details)
                                ->select(DB::raw($prefixed_user_details.".id as id,".
                                                 $prefixed_user_details.".email as email, ".
                                               //  $prefixed_user_details.".mobile_no as contact_number, ".
                                                 "CONCAT(".$prefixed_user_details.".country_code,' ',"
                                                          .$prefixed_user_details.".mobile_no) as contact_number,".
                                                 $prefixed_user_details.".address as address, ".
                                                 "DATE_FORMAT(".$prefixed_user_details.".created_at,'%d %b %Y') as register_date,".
                                                 $prefixed_user_details.".gender as gender, ".
                                                 "CONCAT(".$prefixed_user_details.".first_name,' ',"
                                                          .$prefixed_user_details.".last_name) as user_name"
                                                ))
                                ->join($user_role_table,$user_details.'.id','=',$user_role_table.'.user_id')
                                ->join($role_table, function ($join) use($role_table,$user_role_table,$role) {
                                    $join->on($role_table.'.id', ' = ',$user_role_table.'.role_id')
                                         ->where('slug','=',$role);
                                })
                                ->whereNull($user_details.'.deleted_at')
                                ->orderBy($user_details.'.created_at','DESC');

        $arr_search_column = $request->input('column_filter');
        if(isset($arr_search_column['from_date']) && $arr_search_column['from_date']!="" && isset($arr_search_column['to_date']) && $arr_search_column['to_date']!="")
        {
           $from_date =  $arr_search_column['from_date'];
           $to_date   =  $arr_search_column['to_date'];

           $obj_user = $obj_user ->whereDate($prefixed_user_details.'.created_at','>=',$from_date)
                                 ->whereDate($prefixed_user_details.'.created_at','<=',$to_date); 
        }
        if(isset($arr_search_column['date_filter']) && $arr_search_column['date_filter']!="")
        { 
            $filter = $arr_search_column['date_filter'];

            if($filter == "daily")
            {
               $obj_user = $obj_user ->whereDate($prefixed_user_details.'.created_at','=',date("Y-m-d"));
            } 
            else if($filter == "weekly")
            {
              $from_date = date('Y-m-d', strtotime('-7 days'));
              $to_date   = date('Y-m-d');
              $obj_user = $obj_user->whereDate($prefixed_user_details.'.created_at','>=',$from_date)
                                   ->whereDate($prefixed_user_details.'.created_at','<=',$to_date);
            }
            else if($filter == "monthly")
            {
              $obj_user = $obj_user ->whereMonth($prefixed_user_details.'.created_at','=',date("m"));
            }
        }                        
        return $obj_user;
    }  

    /************ Get ride list from database to build datatable *****************/
    public function get_booking_records(Request $request)
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
                                        $view_href =  url(config('app.project.admin_panel_slug')."/track_booking").'/view?enc_id='.base64_encode($data->id).'&status='.base64_encode($data->booking_status).'&curr_page=booking_history';
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
                                        else if($data->booking_status == 'CANCEL_BY_ADMIN')
                                        {
                                            $booking_status = '<span class="badge badge-important" style="width:115px">Cancel By Driver</span>';
                                        }
                                        return  $booking_status;
                                    })
                                    
                                ->make(true);

        $build_result = $json_result->getData();

      return response()->json($build_result);
    }    

    /************ Build query to generate ride list and send result to get_ride_records function *****************/ 
    private function get_booking_details($request)
    {     
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
                                                          .$prefixed_user_details.".last_name) as user_name,".
                                                    
                                                    "CONCAT(user.first_name,' ',user.last_name) as driver_name,".

                                                    $prefixed_user_details.".company_id as company_id,".
                                                    $prefixed_user_details.".company_name as company_name,".
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
                                ->join($prefixed_user_details,$user_details.'.id','=',$prefixed_load_post_request_details.'.user_id')
                                
                                ->join($prefixed_vehicle_details,$vehicle_details.'.id','=',$prefixed_load_post_request_details.'.vehicle_id')
                                ->join($prefixed_type_vehicle_details,$vehicle_type_details.'.id','=',$prefixed_vehicle_details.'.vehicle_type_id')

                                ->join('users as user','user.id','=',$prefixed_load_post_request_details.'.driver_id')
                                
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
 
   // generate excel sheet of rider report    
   public function generate_user_excel(Request $request)
   {        
      $tbl_name = "users"; 
      $obj_bookings = $this->UserModel
                   ->whereHas('roles',function($query){
                                                $query->where('slug','=','user');
                                              });
                    if($request->has('from_date') && $request->input('from_date')!='')
                    {
                      $obj_bookings = $obj_bookings->whereDate('created_at','>=',$request->input('from_date')) ; 
                    }
                    if($request->has('to_date') && $request->input('to_date')!="")
                    {
                      $obj_bookings = $obj_bookings->whereDate('created_at','<=',$request->input('to_date')) ;
                    }
                    $obj_bookings = $obj_bookings->get();
                    
      $arr_bookings = [];
      $arr_data     = [];
      
    if($obj_bookings)
    {
         $arr_bookings = $obj_bookings->toArray();
        if (!empty($arr_bookings)) 
        {     
              if(isset($arr_bookings) && sizeof($arr_bookings)>0)
              {
                  foreach($arr_bookings as $key=>$booking)
                  {                                
                   
                    $rider_first_name = isset($booking['first_name']) ? $booking['first_name'] :'';
                        
                    $rider_last_name  = isset($booking['last_name']) ? $booking['last_name'] :'';
                        
                    $register_date    = isset($booking['created_at']) ? date('d M y',strtotime($booking['created_at'])) :'-';
                                            
                    $email            = isset($booking['email']) ? $booking['email'] :'-';

                    $mobile_no        = isset($booking['mobile_no']) ? $booking['mobile_no'] :'-';

                    $address          = isset($booking['address']) ? $booking['address'] :'-';

                    $gender           =  isset($booking['gender']) &&  $booking['gender']=='M' ? 'Male' :'-';                             

                    $arr_temp_data = [];
                    
                    $arr_temp_data['rider_name']       = $rider_first_name.'-'.$rider_last_name;
                    $arr_temp_data['created_at']       = $register_date ;
                    $arr_temp_data['email']            = $email ;
                    $arr_temp_data['mobile_no']        = $mobile_no ;
                    $arr_temp_data['address']          = $address ;
                    $arr_temp_data['gender']           = $gender ;

                    array_push($arr_data, $arr_temp_data);
                    
                  }
              }
        
          $tbl_columns = [];

          $tbl_columns['0']  = 'User Name';
          $tbl_columns['1']  = 'Register Date';
          $tbl_columns['2']  = 'Email';
          $tbl_columns['3']  = 'Mobile no.';
          $tbl_columns['4']  = 'Address';
          $tbl_columns['5']  = 'Gender';
               
          Excel::create($tbl_name.'-'.date('Ymd').uniqid(), function($excel) use($tbl_name,$tbl_columns,$arr_data) 
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
        Flash::error(str_singular($this->user_module_title).' Record not given'); 
      } 

    }
            return redirect()->back();

   }

   // generate excel sheet of driver report   
   public function generate_driver_excel(Request $request)
   {        
      $tbl_name = "users"; 
      $obj_bookings = $this->UserModel
                   ->whereHas('roles',function($query){
                                                $query->where('slug','driver');
                                              });
                    if($request->has('from_date') && $request->input('from_date')!='')
                    {
                      $obj_bookings = $obj_bookings->whereDate('created_at','>=',$request->input('from_date')) ; 
                    }
                    if($request->has('to_date') && $request->input('to_date')!="")
                    {
                      $obj_bookings = $obj_bookings->whereDate('created_at','<=',$request->input('to_date')) ;
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
                        $driver_first_name = isset($booking['first_name']) ? $booking['first_name'] :'';
                        
                        $driver_last_name  = isset($booking['last_name']) ? $booking['last_name'] :'';
                        
                        $register_date     = isset($booking['created_at']) ? date('d M y',strtotime($booking['created_at'])) :'-';
                                        
                        $email             = isset($booking['email']) ? $booking['email'] :'-';
         
                        $country_code      = isset($booking['country_code']) ? $booking['country_code'] :'-';

                        $mobile_no         = isset($booking['mobile_no']) ? $booking['mobile_no'] :'-';

                        $address           = isset($booking['address']) ? $booking['address'] :'-';

                        $gender            = isset($booking['gender']) &&  $booking['gender']=='M' ? 'Male' :'-';                           

                        $arr_temp_data = [];
                        
                        $arr_temp_data['driver_name']      = $driver_first_name.'-'.$driver_last_name;
                        $arr_temp_data['created_at']       = $register_date;
                        $arr_temp_data['email']            = $email;
                        $arr_temp_data['mobile_no']        = $country_code.' '.$mobile_no;
                        $arr_temp_data['address']          = $address;
                        $arr_temp_data['gender']           = $gender;

                        array_push($arr_data, $arr_temp_data);
                    
                   }
                }
              
                  $tbl_columns = [];

                  $tbl_columns['0']  = 'Driver Name';
                  $tbl_columns['1']  = 'Register Date';
                  $tbl_columns['2']  = 'Email';
                  $tbl_columns['3']  = 'Mobile no.';
                  $tbl_columns['4']  = 'Address';
                  $tbl_columns['5']  = 'Gender';
                    
                  Excel::create('drivers'.'-'.date('Ymd').uniqid(), function($excel) use($tbl_name,$tbl_columns,$arr_data) 
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
            Flash::error(str_singular($this->driver_module_title).' Record not given'); 

          }   
        }
        return redirect()->back();
   }
   
   public function generate_excel(Request $request)
   {
    // dd($request->all());
      $tbl_name = "Bookings"; 
      $obj_bookings =   $this->BookingMasterModel
                                    ->with(['load_post_request_details'=>function($query){
                                        $query->with(['driver_details'=>function($query){
                                            $query->select('id','first_name','last_name','email','mobile_no','profile_image','is_company_driver','company_name');
                                            $query->with('driver_car_details.vehicle_details');

                                        }]);
                                        $query->with(['user_details'=>function($query){
                                            $query->select('id','first_name','last_name','email','mobile_no','profile_image');
                                        }]);
                                        $query->with('vehicle_details.vehicle_type_details');

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
            if(isset($arr_bookings) && sizeof($arr_bookings)>0)
            {
                foreach($arr_bookings as $key=>$booking)
                {
                        $arr_tmp = [];
                        
                        $arr_tmp =  filter_completed_trip_details($booking);
                        //dd($arr_tmp);
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
                        

                        $arr_temp_data['driver_fare_charge']          = '0.0';
                        $arr_temp_data['admin_commission']            = '0.0';
                        $arr_temp_data['admin_per_kilometer_charge']  = '0.0';
                        $arr_temp_data['driver_per_kilometer_charge'] = '0.0';

                        if(isset($arr_tmp['is_individual_vehicle']) && $arr_tmp['is_individual_vehicle'] == '1')
                        {
                            $arr_temp_data['driver_fare_charge']          = isset($arr_tmp['driver_fare_charge']) ? number_format($arr_tmp['driver_fare_charge'],2) : '0.0';
                            $arr_temp_data['admin_commission']            = isset($arr_tmp['admin_commission']) ? number_format($arr_tmp['admin_commission'],2) : '0.0';
                        }
                        elseif(isset($arr_tmp['is_individual_vehicle']) && $arr_tmp['is_individual_vehicle'] == '0')
                        {
                            $arr_temp_data['admin_per_kilometer_charge']  = isset($arr_tmp['admin_per_kilometer_charge']) ? number_format($arr_tmp['admin_per_kilometer_charge'],2) : '0.0';
                            $arr_temp_data['driver_per_kilometer_charge'] = isset($arr_tmp['driver_per_kilometer_charge']) ? number_format($arr_tmp['driver_per_kilometer_charge'],2) : '0.0';
                        }
                        $arr_temp_data['total_amount']                 = isset($arr_tmp['total_amount']) ? number_format($arr_tmp['total_amount'],2) : '0.0';
                        $arr_temp_data['applied_promo_code_charge']    = isset($arr_tmp['applied_promo_code_charge']) ? number_format($arr_tmp['applied_promo_code_charge'],2) : '0.0';
                        $arr_temp_data['total_charge']                 = isset($arr_tmp['total_charge']) ? number_format($arr_tmp['total_charge'],2) : '0.0';
                        $arr_temp_data['admin_earning_amount']         = isset($arr_tmp['admin_earning_amount']) ? number_format($arr_tmp['admin_earning_amount'],2) : '0.0';
                        $arr_temp_data['driver_earning_amount']        = isset($arr_tmp['driver_earning_amount']) ? number_format($arr_tmp['driver_earning_amount'],2) : '0.0';
                        $arr_temp_data['payment_status']               = isset($arr_tmp['payment_status']) ? ucfirst($arr_tmp['payment_status']) :'';
                        $arr_temp_data['payment_type']                 = isset($arr_tmp['payment_type']) ? ucfirst($arr_tmp['payment_type']) :'';
                        $arr_temp_data['booking_status']               = isset($arr_tmp['booking_status']) ? ucfirst(strtolower(str_replace('_',' ', $arr_tmp['booking_status']))) :'';
                        //dd($arr_temp_data);
                        array_push($arr_data, $arr_temp_data);
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
                  $tbl_columns['14'] = 'Driver Fare Charge';
                  $tbl_columns['15'] = 'Admin Commission';
                  $tbl_columns['16'] = 'Admin Per Km Charge';
                  $tbl_columns['17'] = 'Driver Per Km Charge';
                  $tbl_columns['18'] = 'Total Amount';
                  $tbl_columns['19'] = 'Promo Code Charge';
                  $tbl_columns['20'] = 'Total Charge';
                  $tbl_columns['21'] = 'Admin Earning/Commission Amount';
                  $tbl_columns['22'] = 'Driver Earning/Commission Amount';
                  $tbl_columns['23'] = 'Payment Status';
                  $tbl_columns['24'] = 'Payment Type';
                  $tbl_columns['25'] = 'Booking Status';
                  
                  Excel::create($tbl_name.'-'.date('Ymd').uniqid(), function($excel) use($tbl_name,$tbl_columns,$arr_data) 
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
                Flash::error(str_singular($this->user_module_title).' Record not found'); 
            } 
      
    }
    return redirect()->back();
  }  
}
