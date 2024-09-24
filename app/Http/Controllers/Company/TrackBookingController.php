<?php

namespace App\Http\Controllers\Company;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\UserModel;
use App\Models\VehicleModel;
use App\Models\VehicleTypeModel;
use App\Models\BookingMasterModel;
use App\Models\LoadPostRequestModel;

use DB;
use Sentinel;
use Datatables;

class TrackBookingController extends Controller
{
    public function __construct(UserModel $user_model,
                                VehicleModel $vehicle,
                                VehicleTypeModel $vehicle_type,
                                BookingMasterModel $booking_master,
                                LoadPostRequestModel $load_post_request)
    {
        $this->UserModel                    = $user_model;
        $this->VehicleModel                 = $vehicle;
        $this->VehicleTypeModel             = $vehicle_type;
        $this->BookingMasterModel           = $booking_master;
        $this->LoadPostRequestModel         = $load_post_request;
        $this->arr_view_data                = [];
        $this->module_title                 = "Track Booking";
        $this->module_view_folder           = "company.track_booking";
        $this->theme_color                  = theme_color();
        $this->admin_panel_slug             = config('app.project.company_panel_slug');
        $this->module_url_path              = url(config('app.project.company_panel_slug')."/track_booking");
        $this->user_profile_public_img_path = url('/').config('app.project.img_path.user_profile_images');
        $this->user_profile_base_img_path   = base_path().config('app.project.img_path.user_profile_images');

        $this->load_post_img_public_path    = url('/').config('app.project.img_path.load_post_img');
        $this->load_post_img_base_path      = base_path().config('app.project.img_path.load_post_img');

        $this->company_id = 0;

        $user = Sentinel::check();
        if($user){
            $this->company_id   = isset($user->id) ? $user->id :0;
        }
    }
    public function index(Request $request)
    {
        $todays_date = date('Y-m-d');
        
        $ride_status = 'SHOW_AVAILABLE_DRIVER';
        if($request->has('ride_status') && $request->input('ride_status')!='')
        {
            $ride_status = $request->input('ride_status');
        }

        if($ride_status == 'COMPLETED'){

            $arr_rides   = [];
            $todays_date = date('Y-m-d');
            $obj_rides   = $this->BookingMasterModel
                                    ->select('id','load_post_request_id','booking_unique_id','booking_status')
                                    // ->whereRaw('DATE(booking_date) = "'.date("Y-m-d",strtotime($todays_date)).'"')
                                    ->where('booking_status',$ride_status)
                                    ->whereHas('load_post_request_details',function($query){
                                        $query->whereHas('driver_details' , function ($query) {
                                            $query->where('company_id', $this->company_id);
                                            $query->where('is_company_driver','1');
                                        });
                                    })
                                    ->with(['load_post_request_details'=>function($query){
                                        $query->select('id','driver_id');
                                        $query->with(['driver_details'=>function($query){
                                            $query->where('company_id', $this->company_id);
                                            $query->where('is_company_driver','1');
                                            $query->select('id','first_name','last_name','email','mobile_no','profile_image');
                                            $query->with('driver_car_details.vehicle_details');

                                        }]);
                                    }])
                                    ->orderBy('id','DESC')
                                    ->get();
            
            if($obj_rides)
            {
                $arr_rides = $obj_rides->toArray();
            }

            if(isset($arr_rides) && sizeof($arr_rides)>0)
            {
                foreach ($arr_rides as $key => $value) 
                {
                    // dd($value);
                    $arr_rides[$key]['first_name'] = isset($value['load_post_request_details']['driver_details']['first_name']) ? $value['load_post_request_details']['driver_details']['first_name'] :'';
                    $arr_rides[$key]['last_name']  = isset($value['load_post_request_details']['driver_details']['last_name']) ? $value['load_post_request_details']['driver_details']['last_name'] :'';
                    $arr_rides[$key]['email']      = isset($value['load_post_request_details']['driver_details']['email']) ? $value['load_post_request_details']['driver_details']['email'] :'';
                    $arr_rides[$key]['mobile_no']  = isset($value['load_post_request_details']['driver_details']['mobile_no']) ? $value['load_post_request_details']['driver_details']['mobile_no'] :'';
                    
                    $vehicle_name       = (isset($value['load_post_request_details']['driver_details']['driver_car_details']['vehicle_details']['vehicle_name']) && $value['load_post_request_details']['driver_details']['driver_car_details']['vehicle_details']['vehicle_name']!='') ? $value['load_post_request_details']['driver_details']['driver_car_details']['vehicle_details']['vehicle_name'] :'';
                    $vehicle_type       = (isset($value['load_post_request_details']['driver_details']['driver_car_details']['vehicle_details']['vehicle_type_details']['vehicle_type']) && $value['load_post_request_details']['driver_details']['driver_car_details']['vehicle_details']['vehicle_type_details']['vehicle_type']!='') ? $value['load_post_request_details']['driver_details']['driver_car_details']['vehicle_details']['vehicle_type_details']['vehicle_type'] :'';
                    $vehicle_model_name = (isset($value['load_post_request_details']['driver_details']['driver_car_details']['vehicle_details']['vehicle_model_name']) && $value['load_post_request_details']['driver_details']['driver_car_details']['vehicle_details']['vehicle_model_name']!='') ? $value['load_post_request_details']['driver_details']['driver_car_details']['vehicle_details']['vehicle_model_name'] :'';
                    $vehicle_number     = (isset($value['load_post_request_details']['driver_details']['driver_car_details']['vehicle_details']['vehicle_number']) && $value['load_post_request_details']['driver_details']['driver_car_details']['vehicle_details']['vehicle_number']!='') ? $value['load_post_request_details']['driver_details']['driver_car_details']['vehicle_details']['vehicle_number'] :'';

                    $vehicle_full_name = '('.$vehicle_type.'-'.$vehicle_number.')';

                    $profile_image = url('/uploads/default-profile.png');
                    if(isset($value['load_post_request_details']['driver_details']['profile_image']) && $value['load_post_request_details']['driver_details']['profile_image']!=''){
                        if(file_exists($this->user_profile_base_img_path.$value['load_post_request_details']['driver_details']['profile_image']))
                        {
                           $profile_image = $this->user_profile_public_img_path.$value['load_post_request_details']['driver_details']['profile_image'];
                        }
                    }
                    $arr_rides[$key]['vehicle_full_name']  = $vehicle_full_name;
                    $arr_rides[$key]['profile_image']      = $profile_image;
                    unset($arr_rides[$key]['load_post_request_details']);
                }
            }
            $this->arr_view_data['arr_rides']                = $arr_rides;
        }

        $this->arr_view_data['page_title']                   = "Manage ".str_plural($this->module_title);
        $this->arr_view_data['module_title']                 = str_plural($this->module_title);
        $this->arr_view_data['module_url_path']              = $this->module_url_path;
        $this->arr_view_data['theme_color']                  = $this->theme_color;
        $this->arr_view_data['ride_status']                  = $ride_status;
        $this->arr_view_data['user_profile_public_img_path'] = $this->user_profile_public_img_path;
        $this->arr_view_data['user_profile_base_img_path']   = $this->user_profile_base_img_path;

        $view_name = $this->module_view_folder.'.show_available_driver_index';

        if($ride_status!='' && $ride_status == 'TO_BE_PICKED')
        {
            $view_name = $this->module_view_folder.'.to_be_picked_index';
        }
        else if($ride_status!='' && $ride_status == 'IN_TRANSIT')
        {
            $view_name = $this->module_view_folder.'.in_transit_index';
        }
        else if($ride_status!='' && $ride_status == 'COMPLETED')
        {
            $view_name = $this->module_view_folder.'.completed_index';
        }       
        return view($view_name,$this->arr_view_data);
    }

     public function booking_history()
    {
        $this->arr_view_data['page_title']      = "Manage ".str_plural($this->module_title)." History";
        $this->arr_view_data['module_title']    = str_plural($this->module_title);
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['theme_color']     = $this->theme_color;

        return view($this->module_view_folder.'.booking_history', $this->arr_view_data);   
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
                                        return  $data->vehicle_type;
                                        // return  $data->vehicle_name.' - '.$data->vehicle_type;
                                        
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
        return $obj_user;
    }     
    
    public function view(Request $request)
    {
        $enc_id        = base64_decode($request->input('enc_id'));
        $status        = base64_decode($request->input('status'));

        $previous_page = $request->input('curr_page');
        
        $arr_bookings     = [];
        
        $obj_bookings   = $this->BookingMasterModel
                                    ->with(['load_post_request_details'=>function($query){
                                        $query->with(['driver_details'=>function($query){
                                            $query->select('id','company_id','first_name','last_name','email','mobile_no','country_code','profile_image','is_company_driver','company_name');
                                            $query->with('driver_car_details.vehicle_details','company_details');

                                        }]);
                                        $query->with(['user_details'=>function($query){
                                            $query->select('id','first_name','last_name','email','mobile_no','country_code','profile_image');
                                        }]);
                                        $query->with('vehicle_details.vehicle_type_details');

                                    },'booking_master_coordinate_details'])
                                    ->where('id',$enc_id)
                                    ->first();

        if($obj_bookings)
        {
            $arr_bookings = $obj_bookings->toArray();
        }    
        $this->arr_view_data['page_title']       = "View ".str_singular($this->module_title);
        $this->arr_view_data['module_title']     = str_plural($this->module_title);
        $this->arr_view_data['module_url_path']  = $this->module_url_path;
        $this->arr_view_data['theme_color']      = $this->theme_color;
        $this->arr_view_data['status']           = $status;
        $this->arr_view_data['arr_bookings']     = $arr_bookings;
        $this->arr_view_data['previous_page']    = $previous_page;

        $this->arr_view_data['load_post_img_public_path'] = $this->load_post_img_public_path;
        $this->arr_view_data['load_post_img_base_path']   = $this->load_post_img_base_path;

        $view_name = $this->module_view_folder.'.completed_ride_view';
        if($status!='' && $status == 'TO_BE_PICKED')
        {   
            $this->arr_view_data['page_title']       = "To Be Picked Booking";
            $view_name = $this->module_view_folder.'.to_be_picked_ride_view';
        }
        else if($status!='' && $status == 'IN_TRANSIT')
        {
            $this->arr_view_data['page_title']       = "In Transit Booking";
            $view_name = $this->module_view_folder.'.in_transit_ride_view';
        }
        else if($status!='' && $status == 'COMPLETED')
        {
            $this->arr_view_data['page_title']       = "Completed Booking";
            $view_name = $this->module_view_folder.'.completed_ride_view';
        }
        else if($status!='' && ($status == 'CANCEL_BY_USER' || $status == 'CANCEL_BY_DRIVER'))
        {
            $this->arr_view_data['page_title']       = "Cancel Booking Details";
            $view_name = $this->module_view_folder.'.cancel_ride_view';
        }
        return view($view_name,$this->arr_view_data);
    }

    public function available_driver(Request $request)
    {
        $arr_response                     = [];
        $arr_response['status']           = 'error';
        $arr_response['available_driver'] = [];

        $type = 'AVAILABLE'; 
        $obj_available_driver = $this->search_available_drivers($type);
        if(isset($obj_available_driver) && sizeof($obj_available_driver)>0){
            $arr_response['status']           = 'success';
            $arr_response['available_driver'] = $obj_available_driver;
        }
        return response()->json($arr_response);
    }

    public function track_current_booking(Request $request)
    {
        $arr_response                     = [];
        $arr_response['status']           = 'error';
        $arr_response['current_booking'] = [];

        $obj_current_booking = $this->get_current_booking_tracking_details($request);

        if(isset($obj_current_booking) && sizeof($obj_current_booking)>0){
            $arr_response['status']           = 'success';
            $arr_response['current_booking'] = $obj_current_booking;
        }
        return response()->json($arr_response);
    }

    private function search_available_drivers($type)
    {

        $date = new \DateTime();
        
        $date->modify('-1 min');
        $formatted_date = $date->format('Y-m-d H:i:s');

        $sql_query = '';
        $sql_query .= "Select ";
        $sql_query .= "users.id AS driver_id, ";
        $sql_query .= "CONCAT( users.first_name, ' ',users.last_name ) AS driver_name, ";
        $sql_query .= "users.mobile_no AS mobile_no, ";
        $sql_query .= "users.email AS email, ";
        $sql_query .= "users.company_id AS company_id, ";
        $sql_query .= "company.company_name AS company_name, ";
        $sql_query .= "users.is_company_driver AS is_company_driver, ";

        $sql_query .= "V.id AS vehicle_id, ";
        $sql_query .= "VT.id AS vehicle_type_original_id, ";
        // $sql_query .= "V.vehicle_name AS vehicle_name, ";
        $sql_query .= "VT.vehicle_type AS vehicle_type_name, ";
        $sql_query .= "VT.vehicle_type_slug AS vehicle_type_slug, ";
        $sql_query .= "driver_available_status.current_latitude as current_latitude, ";
        $sql_query .= "driver_available_status.current_longitude as current_longitude, ";
        $sql_query .= "driver_available_status.status as status, ";
        $sql_query .= "driver_available_status.updated_at as last_location_updated_time ,";
        $sql_query .= "load_post_request.id as load_post_request_id ,";
        $sql_query .= "load_post_request.driver_id as load_post_request_driver_id ,";
        $sql_query .= "booking_master.id as booking_id ,";
        $sql_query .= "booking_master.booking_status as booking_status ,";
        $sql_query .= "booking_master.load_post_request_id as booking_load_post_request_id ";

        $sql_query .= "FROM ";
        $sql_query .=   "users "; 
        $sql_query .=  "JOIN ";
        $sql_query .=   "role_users ON role_users.user_id = users.id ";
        $sql_query .=  "JOIN ";
        $sql_query .=    "roles ON roles.id = role_users.role_id ";
        $sql_query .=  "JOIN ";
        $sql_query .=     "driver_car_relation ON driver_car_relation.driver_id = users.id ";
        $sql_query .=  "JOIN ";
        $sql_query .=     "driver_available_status ON driver_available_status.driver_id = users.id ";
        $sql_query .=  "JOIN ";
        $sql_query .=      "vehicle as V ON V.id = driver_car_relation.vehicle_id ";
        $sql_query .=  "JOIN ";
        $sql_query .=       "vehicle_type as VT ON VT.id = V.vehicle_type_id ";
        $sql_query .=  "JOIN ";
        $sql_query .=      "users as company ON company.id = users.company_id ";

        $sql_query .=  "LEFT JOIN ";
        $sql_query .=      "load_post_request ON load_post_request.driver_id = users.id ";

        $sql_query .=  "LEFT JOIN ";
        $sql_query .=      "booking_master ON booking_master.load_post_request_id = load_post_request.id ";

        $sql_query .= "WHERE ";

        $sql_query .=  "users.company_id = ".$this->company_id." AND ";
        $sql_query .=  "users.is_company_driver = '1' AND ";
        $sql_query .=  "users.is_active = '1' AND ";
        $sql_query .=  "roles.slug = 'driver' AND ";
        // $sql_query .=  "driver_available_status.status = 'AVAILABLE' AND ";
        $sql_query .=  " ( driver_car_relation.is_car_assign = '1' OR driver_car_relation.is_individual_vehicle = '1' ) AND ";
        
        // $sql_query .=  "V.is_verified = '1'  ";
        
        $sql_query .=  "V.is_verified = '1' AND ";
        $sql_query .=  "driver_available_status.updated_at >= "."'".$formatted_date."'"."  ";
        
        $sql_query .=  "GROUP BY users.id ";

        $obj_driver_details =  \DB::select($sql_query);
        
        return $obj_driver_details;
    }
    
    private function get_current_booking_tracking_details($request)
    {
        $status = $request->input('status');
        $enc_id = $request->input('enc_id');

        $sql_query = '';
        $sql_query .= "Select ";
        $sql_query .= "booking_master.id AS booking_master_id, ";
        $sql_query .= "booking_master.load_post_request_id AS booking_load_post_request_id, ";
        $sql_query .= "booking_master.booking_unique_id AS booking_unique_id, ";
        $sql_query .= "booking_master.booking_status AS booking_status, ";
        $sql_query .= "load_post_request.id AS load_post_request_id, ";
        $sql_query .= "load_post_request.driver_id AS driver_id, ";

        $sql_query .= "CONCAT( users.first_name, ' ',users.last_name ) AS driver_name, ";
        $sql_query .= "users.mobile_no AS mobile_no, ";
        $sql_query .= "users.email AS email, ";
        $sql_query .= "users.company_id AS company_id, ";
        $sql_query .= "company.company_name AS company_name, ";
        $sql_query .= "users.is_company_driver AS is_company_driver, ";

        $sql_query .= "V.id AS vehicle_id, ";
        $sql_query .= "VT.id AS vehicle_type_original_id, ";
        // $sql_query .= "V.vehicle_name AS vehicle_name, ";
        $sql_query .= "VT.vehicle_type AS vehicle_type_name, ";
        $sql_query .= "VT.vehicle_type_slug AS vehicle_type_slug, ";
        $sql_query .= "driver_available_status.current_latitude as current_latitude, ";
        $sql_query .= "driver_available_status.current_longitude as current_longitude, ";
        $sql_query .= "driver_available_status.status as status ";

        $sql_query .= "FROM ";
        $sql_query .=   "booking_master "; 

        $sql_query .=  "JOIN ";
        $sql_query .=   "load_post_request ON load_post_request.id = booking_master.load_post_request_id ";

        $sql_query .=  "JOIN ";
        $sql_query .=   "users ON users.id = load_post_request.driver_id ";

        $sql_query .=  "JOIN ";
        $sql_query .=     "driver_car_relation ON driver_car_relation.driver_id = load_post_request.driver_id ";
        $sql_query .=  "JOIN ";
        $sql_query .=      "vehicle as V ON V.id = driver_car_relation.vehicle_id ";
        $sql_query .=  "JOIN ";
        $sql_query .=       "vehicle_type as VT ON VT.id = V.vehicle_type_id ";
        $sql_query .=  "JOIN ";
        $sql_query .=     "driver_available_status ON driver_available_status.driver_id = load_post_request.driver_id ";
        
        $sql_query .=  "JOIN ";
        $sql_query .=      "users as company ON company.id = users.company_id ";

        $sql_query .= "WHERE ";
        
        if($enc_id == null){
            $sql_query .=  "booking_master.booking_status IN ('TO_BE_PICKED', 'IN_TRANSIT') AND ";
        }

        if($enc_id !=null && $enc_id!=''){

            $sql_query .=  "booking_master.id = ".$enc_id." AND ";
        }

        $sql_query .=  "users.is_active = '1' AND ";
        $sql_query .=  "users.company_id = ".$this->company_id." AND ";
        $sql_query .=  "users.is_company_driver = '1' AND ";
        $sql_query .=  " ( driver_car_relation.is_car_assign = '1' OR driver_car_relation.is_individual_vehicle = '1' )";
       
        $obj_driver_details =  \DB::select($sql_query);
        
        if($enc_id !=null && $enc_id!='' && $enc_id!=0){

            return isset($obj_driver_details[0]) ? $obj_driver_details[0] :[];
        }

        return $obj_driver_details;
    }
}
