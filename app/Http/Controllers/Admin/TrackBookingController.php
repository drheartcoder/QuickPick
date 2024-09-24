<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\UserModel;
use App\Models\VehicleModel;
use App\Models\VehicleTypeModel;
use App\Models\BookingMasterModel;
use App\Models\LoadPostRequestModel;
use App\Models\LoadPostRequestHistoryModel;
use App\Common\Services\CommonDataService;
use App\Common\Services\NotificationsService;

use Datatables;
use DB;
use Flash;
use Validator;
use Twilio\Rest\Client;

class TrackBookingController extends Controller
{
    public function __construct(UserModel $user_model,
                                VehicleModel $vehicle,
                                VehicleTypeModel $vehicle_type,
                                BookingMasterModel $booking_master,
                                LoadPostRequestModel $load_post_request,
                                LoadPostRequestHistoryModel $load_post_request_history,
                                CommonDataService $common_data_service,
                                NotificationsService $notifications_service)
    {
        $this->UserModel                    = $user_model;
        $this->VehicleModel                 = $vehicle;
        $this->VehicleTypeModel             = $vehicle_type;
        $this->BookingMasterModel           = $booking_master;
        $this->LoadPostRequestModel         = $load_post_request;
        $this->LoadPostRequestHistoryModel  = $load_post_request_history;
        $this->CommonDataService            = $common_data_service;
        $this->NotificationsService         = $notifications_service;
        $this->arr_view_data                = [];
        $this->module_title                 = "Track Booking";
        $this->module_view_folder           = "admin.track_booking";
        $this->theme_color                  = theme_color();
        $this->admin_panel_slug             = config('app.project.admin_panel_slug');
        $this->module_url_path              = url(config('app.project.admin_panel_slug')."/track_booking");
        $this->user_profile_public_img_path = url('/').config('app.project.img_path.user_profile_images');
        $this->user_profile_base_img_path   = base_path().config('app.project.img_path.user_profile_images');

        $this->load_post_img_public_path    = url('/').config('app.project.img_path.load_post_img');
        $this->load_post_img_base_path      = base_path().config('app.project.img_path.load_post_img');

        $this->distance                           = 20;

        $this->DriverOneSignalApiKey = config('app.project.one_signal_credentials.driver_api_key');
        $this->DriverOneSignalAppId  = config('app.project.one_signal_credentials.driver_app_id');
        
        $this->UserOneSignalApiKey   = config('app.project.one_signal_credentials.user_api_key');
        $this->UserOneSignalAppId    = config('app.project.one_signal_credentials.user_app_id');

        $this->WebOneSignalApiKey   = config('app.project.one_signal_credentials.website_api_key');
        $this->WebOneSignalAppId    = config('app.project.one_signal_credentials.website_app_id');

    }
    
    public function index(Request $request)
    {
        $todays_date = date('Y-m-d');
        
        $ride_status = 'SHOW_AVAILABLE_DRIVER';
        if($request->has('ride_status') && $request->input('ride_status')!='')
        {
            $ride_status = $request->input('ride_status');
        }

        if($ride_status == 'SHOW_AVAILABLE_DRIVER')
        {   
            $date = new \DateTime();
        
            $date->modify('-24 hours');
            $formatted_date = $date->format('Y-m-d H:i:s');

            $enc_load_id = '';

            $arr_load_post_request = $arr_load_post_request_details = [];
            if($request->has('enc_load_id') && $request->input('enc_load_id')!='')
            {
                $enc_load_id = base64_decode($request->input('enc_load_id'));
                $obj_load_post_request_details = $this->LoadPostRequestModel
                                                            ->select("id","load_post_request_unique_id","date","pickup_location","drop_location","pickup_lat","pickup_lng","drop_lat","drop_lng","request_status")
                                                            ->with('load_post_request_package_details','load_post_request_history_details')
                                                            // ->where('request_status','USER_REQUEST')
                                                            ->where('id',$enc_load_id)
                                                            ->first();

                if($obj_load_post_request_details){
                    $arr_load_post_request_details = $obj_load_post_request_details->toArray();
                }
                $package_type     = isset($arr_load_post_request_details['load_post_request_package_details']['package_type']) ? $arr_load_post_request_details['load_post_request_package_details']['package_type'] : '';
                $package_length   = isset($arr_load_post_request_details['load_post_request_package_details']['package_length']) ? $arr_load_post_request_details['load_post_request_package_details']['package_length'] : 0;
                $package_breadth  = isset($arr_load_post_request_details['load_post_request_package_details']['package_breadth']) ? $arr_load_post_request_details['load_post_request_package_details']['package_breadth'] : 0;
                $package_height   = isset($arr_load_post_request_details['load_post_request_package_details']['package_height']) ? $arr_load_post_request_details['load_post_request_package_details']['package_height'] : 0;
                $package_weight   = isset($arr_load_post_request_details['load_post_request_package_details']['package_weight']) ? $arr_load_post_request_details['load_post_request_package_details']['package_weight'] : 0;
                $package_quantity = isset($arr_load_post_request_details['load_post_request_package_details']['package_quantity']) ? $arr_load_post_request_details['load_post_request_package_details']['package_quantity'] : 0;
        
                $package_volume = (floatval($package_length) * floatval($package_breadth) * floatval($package_height))* intval($package_quantity);
                $package_weight = (floatval($package_weight) * intval($package_quantity));

                $arr_load_required_data = 
                                            [
                                                'package_type'     => $package_type,
                                                'package_quantity' => intval($package_quantity),
                                                'package_volume'   => $package_volume,
                                                'package_weight'   => $package_weight
                                            ];        

                $arr_vehicle_type_details = $this->CommonDataService->get_vehicle_type_available_against_load($arr_load_required_data);
                
                $arr_load_post_request_details['arr_vehicle_type_details'] = $arr_vehicle_type_details;
                unset($arr_load_post_request_details['load_post_request_package_details']);
                
                $this->arr_view_data['arr_load_post_request_details'] = $arr_load_post_request_details;
            }
            $obj_load_post_request = $this->LoadPostRequestModel
                                                ->select("id","load_post_request_unique_id","date","pickup_location","drop_location","pickup_lat","pickup_lng","drop_lat","drop_lng","request_status")
                                                ->whereIn('request_status',['TIMEOUT'])
                                                ->orderBy('id','DESC')
                                                ->where('created_at', '>',$formatted_date); /*latest 24 hours records will be shown */
            if($enc_load_id!='')
            {
                $obj_load_post_request = $obj_load_post_request->where('id','!=',$enc_load_id);
            }
            $obj_load_post_request = $obj_load_post_request->get();

            if($obj_load_post_request){
                $arr_load_post_request = $obj_load_post_request->toArray();
            }
            $this->arr_view_data['arr_load_post_request'] = $arr_load_post_request;
        }

        if($ride_status == 'COMPLETED'){

            $arr_rides   = [];
            $todays_date = date('Y-m-d');
            $obj_rides   = $this->BookingMasterModel
                                    ->select('id','load_post_request_id','booking_unique_id','booking_status')
                                    ->whereRaw('DATE(booking_date) = "'.date("Y-m-d",strtotime($todays_date)).'"')
                                    ->where('booking_status',$ride_status)
                                    ->whereHas('load_post_request_details',function($query){
                                        $query->whereHas('driver_details',function($query){
                                        });
                                    })
                                    ->with(['load_post_request_details'=>function($query){
                                        $query->select('id','driver_id');
                                        $query->with(['driver_details'=>function($query){
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
                                        $view_href =  url(config('app.project.admin_panel_slug')."/track_booking").'/view?enc_id='.base64_encode($data->id).'&status='.base64_encode($data->booking_status).'&curr_page=booking_history';
                                        $build_view_action = '<a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip" href="'.$view_href.'" title="View"><i class="fa fa-eye" ></i></a>';
                                        
                                        $booking_status = isset($data->booking_status) ? $data->booking_status : '';
                                        
                                        $cancel_href = $build_cancel_action = '';

                                        if($booking_status == 'TO_BE_PICKED' || $booking_status == 'IN_TRANSIT')
                                        {
                                            $cancel_href =  $this->module_url_path.'/cancel_trip_request/'.base64_encode($data->id);
                                            $build_cancel_action = '<a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip btn-dangers" href="'.$cancel_href.'" title="Cancel Trip" onclick="return confirm_action(this,event,\'Do you really want to cancel this  trip request?\')"><i class="fa fa-times" ></i></a>';

                                        }
                                        
                                        $build_send_invoice_action = '';

                                        if($booking_status == 'COMPLETED')
                                        {
                                            $base_url = url('/common/send_invoice_email');
                                            
                                            $booking_id = isset($data->id)?$data->id:0;

                                            $base_url = $base_url.'?booking_id='.$booking_id.'&type=BOTH&request_type=WEB';

                                            $build_send_invoice_action = '<a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip btn-delets" href="'.$base_url.'" title="Send Invoice Email"><i class="fa fa-envelope-o" ></i></a>';

                                        }

                                        return  $build_view_action.' '.$build_cancel_action.' '.$build_send_invoice_action;
                                        
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
                                            $booking_status = '<span class="badge badge-important" style="width:115px">Cancel By Admin</span>';
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
                                                          .$prefixed_user_details.".last_name) as user_name,".
                                                    
                                                    "CONCAT(user.first_name,' ',user.last_name) as driver_name,".

                                                    $prefixed_user_details.".company_id as company_id,".
                                                   // $prefixed_user_details.".company_name as company_name,".
                                                    $prefixed_user_details.".is_company_driver as is_company_driver,".
                                                     "company.company_name as company_name,".

                                                    $prefixed_booking_details.".booking_status as booking_status,".
                                                    // $vehicle_details.".vehicle_name as vehicle_name,".
                                                    $vehicle_type_details.".vehicle_type as vehicle_type,".
                                                    $prefixed_load_post_request_details.".vehicle_id as vehicle_id,".
                                                    $prefixed_load_post_request_details.".pickup_location as pickup_location,".
                                                    $prefixed_load_post_request_details.".drop_location as drop_location"

                                                ))

                                ->join($prefixed_load_post_request_details,$prefixed_load_post_request_details.'.id','=',$booking_details.'.load_post_request_id')
                                ->join($prefixed_user_details,$user_details.'.id','=',$prefixed_load_post_request_details.'.user_id')
                                ->leftjoin("users AS company", "company.id", '=', $user_details.'.company_id')
                                ->join($prefixed_vehicle_details,$vehicle_details.'.id','=',$prefixed_load_post_request_details.'.vehicle_id')
                                ->join($prefixed_type_vehicle_details,$vehicle_type_details.'.id','=',$prefixed_vehicle_details.'.vehicle_type_id')

                                ->join('users as user','user.id','=',$prefixed_load_post_request_details.'.driver_id')
                                
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
                                            $query->select('id','first_name','last_name','email','mobile_no','country_code','profile_image','is_company_driver','company_name');
                                            $query->with('driver_car_details.vehicle_details');

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
           
        $this->arr_view_data['page_title']                = "View ".str_singular($this->module_title);
        $this->arr_view_data['module_title']              = str_plural($this->module_title);
        $this->arr_view_data['module_url_path']           = $this->module_url_path;
        $this->arr_view_data['theme_color']               = $this->theme_color;
        $this->arr_view_data['status']                    = $status;
        $this->arr_view_data['arr_bookings']              = $arr_bookings;
        $this->arr_view_data['previous_page']             = $previous_page;
        $this->arr_view_data['load_post_img_public_path'] = $this->load_post_img_public_path;
        $this->arr_view_data['load_post_img_base_path']   = $this->load_post_img_base_path;

        $view_name = $this->module_view_folder.'.completed_ride_view';
        if($status!='' && $status == 'TO_BE_PICKED')
        {   
            $this->arr_view_data['page_title']       = "To Be Picked Booking Details";
            $view_name = $this->module_view_folder.'.to_be_picked_ride_view';
        }
        else if($status!='' && $status == 'IN_TRANSIT')
        {
            $this->arr_view_data['page_title']       = "In Transit Booking Details";
            $view_name = $this->module_view_folder.'.in_transit_ride_view';
        }
        else if($status!='' && $status == 'COMPLETED')
        {
            $this->arr_view_data['page_title']       = "Completed Booking Details";
            $view_name = $this->module_view_folder.'.completed_ride_view';
        }
        else if($status!='' && ($status == 'CANCEL_BY_USER' || $status == 'CANCEL_BY_DRIVER' || $status == 'CANCEL_BY_ADMIN'))
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
        $arr_response['arr_driver_id'] = [];

        $type = 'AVAILABLE'; 
        
        $arr_driver_id = [];

        $obj_available_driver = $this->search_available_drivers($type,$request);
        if(isset($obj_available_driver) && sizeof($obj_available_driver)>0){
            foreach ($obj_available_driver as $key => $value) 
            {
                if(isset($value->driver_id))
                {
                    $arr_driver_id[$key] = isset($value->driver_id) ? $value->driver_id : 0;
                }
            }
            $arr_response['status']           = 'success';
            $arr_response['available_driver'] = $obj_available_driver;
            $arr_response['arr_driver_id']    = $arr_driver_id;
        }

        $load_post_request_id = $request->input('load_post_request_id');
        $selected_driver_id   = $request->input('selected_driver_id');
        
        $str_html_load_post_request = $this->get_all_timeout_request_list($load_post_request_id,$selected_driver_id);

        $arr_load_post_request_history_details = [];

        $load_post_request_status = '';
        if($load_post_request_id!=0){

            $obj_load_post_request = $this->LoadPostRequestModel
                                                    ->with(['load_post_request_history_details'=>function($query)use($selected_driver_id){
                                                        //$query->where('driver_id',$selected_driver_id);
                                                        $query->whereIn('status',['USER_REQUEST','REJECT_BY_DRIVER','TIMEOUT']);
                                                        $query->where('is_admin_assign','1');
                                                        $query->orderBy('id','DESC');
                                                    }])
                                                    ->where('id',$load_post_request_id)
                                                    ->first();
            
            $load_post_request_status = isset($obj_load_post_request->request_status) ? $obj_load_post_request->request_status : '';

            if($obj_load_post_request)
            {
                $arr_tmp = $obj_load_post_request->toArray();
                if(isset($arr_tmp['load_post_request_history_details']) && count($arr_tmp['load_post_request_history_details'])>0)
                {
                    $arr_load_post_request_history_details = $arr_tmp['load_post_request_history_details'];
                }
            }
        }
        
        // dd($arr_load_post_request_history_details);

        $arr_response['load_post_request_status']              = $load_post_request_status;
        $arr_response['arr_load_post_request_history_details'] = $arr_load_post_request_history_details;
        $arr_response['str_html_load_post_request'] = $str_html_load_post_request;

        // return response()->json(base64_encode(json_encode($arr_response)));
        return response()->json($arr_response);
    }
    private function get_all_timeout_request_list($load_post_request_id,$selected_driver_id)
    {
        $date = new \DateTime();
        
        $date->modify('-1 min');
        $formatted_date = $date->format('Y-m-d H:i:s');
        
        //dump($formatted_date);

        $enc_load_id = '';

        $arr_load_post_request = $arr_load_post_request_details = [];
        if($load_post_request_id!=0)
        {
            $obj_load_post_request_details = $this->LoadPostRequestModel
                                                        ->select("id","load_post_request_unique_id","user_id","date","pickup_location","drop_location","pickup_lat","pickup_lng","drop_lat","drop_lng","request_status","is_future_request","is_request_process")
                                                        ->with('load_post_request_package_details','load_post_request_history_details')
                                                        ->with(['user_details'=>function($query){
                                                            $query->select('id','first_name','last_name','email','country_code','mobile_no');
                                                        }])
                                                        ->where('id',$load_post_request_id)
                                                        ->first();

            if($obj_load_post_request_details){
                $arr_load_post_request_details = $obj_load_post_request_details->toArray();
            }
            
            $package_type     = isset($arr_load_post_request_details['load_post_request_package_details']['package_type']) ? $arr_load_post_request_details['load_post_request_package_details']['package_type'] : '';
            $package_length   = isset($arr_load_post_request_details['load_post_request_package_details']['package_length']) ? $arr_load_post_request_details['load_post_request_package_details']['package_length'] : 0;
            $package_breadth  = isset($arr_load_post_request_details['load_post_request_package_details']['package_breadth']) ? $arr_load_post_request_details['load_post_request_package_details']['package_breadth'] : 0;
            $package_height   = isset($arr_load_post_request_details['load_post_request_package_details']['package_height']) ? $arr_load_post_request_details['load_post_request_package_details']['package_height'] : 0;
            $package_weight   = isset($arr_load_post_request_details['load_post_request_package_details']['package_weight']) ? $arr_load_post_request_details['load_post_request_package_details']['package_weight'] : 0;
            $package_quantity = isset($arr_load_post_request_details['load_post_request_package_details']['package_quantity']) ? $arr_load_post_request_details['load_post_request_package_details']['package_quantity'] : 0;
    
            $package_volume = (floatval($package_length) * floatval($package_breadth) * floatval($package_height))* intval($package_quantity);
            $package_weight = (floatval($package_weight) * intval($package_quantity));

            $arr_load_required_data = 
                                    [
                                        'package_type'     => $package_type,
                                        'package_quantity' => intval($package_quantity),
                                        'package_volume'   => $package_volume,
                                        'package_weight'   => $package_weight
                                    ];        

            $arr_vehicle_type_details = $this->CommonDataService->get_vehicle_type_available_against_load($arr_load_required_data);
            
            $arr_load_post_request_details['arr_vehicle_type_details'] = $arr_vehicle_type_details;
            unset($arr_load_post_request_details['load_post_request_package_details']);
        }

        $obj_load_post_request = $this->LoadPostRequestModel
                                                ->select("id","load_post_request_unique_id","user_id","date","pickup_location","drop_location","pickup_lat","pickup_lng","drop_lat","drop_lng","request_status","is_future_request","is_request_process","is_admin_assign","created_at")
                                                ->whereIn('request_status',['USER_REQUEST','REJECT_BY_DRIVER','TIMEOUT'])
                                                ->where(function($query){
                                                    $query->where('is_future_request','0');
                                                    $query->orWhere(function($inner_query){
                                                        $inner_query->where('is_future_request','1');
                                                        $inner_query->where('is_request_process','1');
                                                    });

                                                })
                                                ->whereHas('user_details',function($query){

                                                })
                                                ->with(['user_details'=>function($query){
                                                    $query->select('id','first_name','last_name','email','country_code','mobile_no');
                                                }])

                                                ->orderBy('id','DESC');
                                                // ->where('created_at', '>',$formatted_date); /*latest 24 hours records will be shown */
        if($load_post_request_id!=0)
        {
            $obj_load_post_request = $obj_load_post_request->where('id','!=',$load_post_request_id);
        }
        $obj_load_post_request = $obj_load_post_request->get();

        if($obj_load_post_request){
            $arr_load_post_request = $obj_load_post_request->toArray();
        }
        $load_post_html  = '';
        $load_post_html .= '<ul class="content-txt1 content-d section-auto-height">';
        
        if(isset($arr_load_post_request_details) && sizeof($arr_load_post_request_details)>0)
        {
            $date = new \DateTime();
            $date->modify('-1 min');
            $current_date = $date->format('Y-m-d H:i:s');

            $created_at        = isset($arr_load_post_request_details['created_at']) ? $arr_load_post_request_details['created_at'] : '';
            $request_status    = isset($arr_load_post_request_details['request_status']) ? $arr_load_post_request_details['request_status'] : '';
            $is_future_request = isset($arr_load_post_request_details['is_future_request']) ? $arr_load_post_request_details['is_future_request'] : '';
            $is_admin_assign   = isset($load_post_request['is_admin_assign']) ? $load_post_request['is_admin_assign'] : '';
            $request_status_html = '';

            if(strtotime($current_date)>strtotime($created_at)){
                if($request_status == 'REJECT_BY_DRIVER' && $is_admin_assign == '1'){
                    $request_status_html = '<span class="badge badge-important" style="width:150px">Rejected by Driver</span>';
                }
                else{
                    $request_status_html = '<span class="badge badge-success" style="width:200px">Drivers not responding</span>';
                }
            }
            else
            {
                if($request_status == 'USER_REQUEST' && $is_future_request == '0'){
                    $request_status_html = '<span class="badge badge-success" style="width:150px">New Request</span>';
                }
                else if($request_status == 'USER_REQUEST' && $is_future_request == '1'){
                    $request_status_html = '<span class="badge badge-success" style="width:150px">Future Request</span>';
                }
                else if($request_status == 'TIMEOUT'){
                    $request_status_html = '<span class="badge badge-warning" style="width:150px">Timeout</span>';
                }
                else if($request_status == 'REJECT_BY_DRIVER'){
                    $request_status_html = '<span class="badge badge-important" style="width:150px">Rejected by Driver</span>';
                }
            }

            $load_post_request_unique_id = isset($arr_load_post_request_details['load_post_request_unique_id']) ? $arr_load_post_request_details['load_post_request_unique_id'] : '';
            $pickup_location             = isset($arr_load_post_request_details['pickup_location']) ? $arr_load_post_request_details['pickup_location'] : '';
            $drop_location               = isset($arr_load_post_request_details['drop_location']) ? $arr_load_post_request_details['drop_location'] : '';
            $vehicle_type_name           = isset($arr_load_post_request_details['arr_vehicle_type_details']['vehicle_type']) ? $arr_load_post_request_details['arr_vehicle_type_details']['vehicle_type'] : '-';
            $date                        = isset($arr_load_post_request_details['date']) ? date('d M Y',strtotime($arr_load_post_request_details['date'])) : '';

            $first_name    = isset($arr_load_post_request_details['user_details']['first_name']) ? $arr_load_post_request_details['user_details']['first_name'] : '';
            $last_name     = isset($arr_load_post_request_details['user_details']['last_name']) ? $arr_load_post_request_details['user_details']['last_name'] : '';
            $email         = isset($arr_load_post_request_details['user_details']['email']) ? $arr_load_post_request_details['user_details']['email'] : '';
            
            $country_code   = isset($arr_load_post_request_details['user_details']['country_code']) ? $arr_load_post_request_details['user_details']['country_code'] : '';
            $mobile_no      = isset($arr_load_post_request_details['user_details']['mobile_no']) ? $arr_load_post_request_details['user_details']['mobile_no'] : '';
            
            $full_name = $first_name.' '.$last_name;
            $full_name = ($full_name!=' ')?$full_name :'-';

            $full_mobile_no = $country_code.''.$mobile_no;
            $full_mobile_no = ($full_mobile_no!='')?$full_mobile_no :'';

            $data_id = isset($arr_load_post_request_details['id']) ? $arr_load_post_request_details['id'] : 0;

            $load_post_html.= '<li style="background-color: #DCDCDC">';
            $load_post_html.= '    <div class="avatar-content pull-left">';
            $load_post_html.= '        <div class="avtar-name">'.$load_post_request_unique_id.' - '.$request_status_html.'</div>';
            $load_post_html.= '        <div class="avtar-ps"><strong>Customer Name :</strong> '.$full_name.' </div>';
            $load_post_html.= '        <div class="avtar-ps"><strong>Contact No :</strong> '.$full_mobile_no.' </div>';
            $load_post_html.= '        <div class="avtar-ps"><strong>Pickup Location :</strong> '.$pickup_location.' </div>';
            $load_post_html.= '        <div class="avtar-ps"><strong>Drop Location :</strong> '.$drop_location.'</div>';
            $load_post_html.= '        <div class="avtar-ps"><strong>Required Vehicle Type :</strong>'.$vehicle_type_name.'</div>';
            $load_post_html.= '        <p>'.$date.'</p>';
            $load_post_html.= '    </div>';
            $load_post_html.= '    <div class="avatar-ivew">';
            $load_post_html.= '        <a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip btn-dangers" href="javascript:void(0);"  data-id="'.$data_id.'"  onclick="cancel_request(this)" title="Cancel Request"><i class="fa fa-times" ></i></a>';
            $load_post_html.= '    </div>';
            $load_post_html.= '    <div class="clearfix"></div>';
            $load_post_html.= '</li>';
        }
        
        if(isset($arr_load_post_request) && sizeof($arr_load_post_request)>0)
        {
            foreach($arr_load_post_request as $key=>$load_post_request)
            {
                $date = new \DateTime();
                $date->modify('-1 min');
                $current_date = $date->format('Y-m-d H:i:s');

                $created_at        = isset($load_post_request['created_at']) ? $load_post_request['created_at'] : '';
                $request_status    = isset($load_post_request['request_status']) ? $load_post_request['request_status'] : '';
                $is_future_request = isset($load_post_request['is_future_request']) ? $load_post_request['is_future_request'] : '';
                $is_admin_assign   = isset($load_post_request['is_admin_assign']) ? $load_post_request['is_admin_assign'] : '';
                
                $request_status_html = '';

                if(strtotime($current_date)>strtotime($created_at)){
                    if($request_status == 'REJECT_BY_DRIVER' && $is_admin_assign == '1'){
                        $request_status_html = '<span class="badge badge-important" style="width:150px">Rejected by Driver</span>';
                    }
                    else{
                        $request_status_html = '<span class="badge badge-success" style="width:200px">Drivers not responding</span>';
                    }
                }
                else
                {
                    if($request_status == 'USER_REQUEST' && $is_future_request == '0'){
                        $request_status_html = '<span class="badge badge-success" style="width:150px">New Request</span>';
                    }
                    else if($request_status == 'USER_REQUEST' && $is_future_request == '1'){
                        $request_status_html = '<span class="badge badge-success" style="width:150px">Future Request</span>';
                    }
                    else if($request_status == 'TIMEOUT'){
                        $request_status_html = '<span class="badge badge-warning" style="width:150px">Timeout</span>';
                    }
                    else if($request_status == 'REJECT_BY_DRIVER'){
                        $request_status_html = '<span class="badge badge-important" style="width:150px">Rejected by Driver</span>';
                    }
                }
                
                $ride_status = 'SHOW_AVAILABLE_DRIVER';

                $enc_load_id = isset($load_post_request['id']) ? base64_encode($load_post_request['id']) : 0;

                $current_url = $this->module_url_path.'?ride_status='.$ride_status.'&enc_load_id='.$enc_load_id;

                $load_post_request_unique_id = isset($load_post_request['load_post_request_unique_id']) ? $load_post_request['load_post_request_unique_id'] : '';
                $pickup_location             = isset($load_post_request['pickup_location']) ? $load_post_request['pickup_location'] : '';
                $drop_location               = isset($load_post_request['drop_location']) ? $load_post_request['drop_location'] : '';
                $vehicle_type_name           = isset($load_post_request['arr_vehicle_type_details']['vehicle_type']) ? $load_post_request['arr_vehicle_type_details']['vehicle_type'] : '-';
                $date                        = isset($load_post_request['date']) ? date('d M Y',strtotime($load_post_request['date'])) : '';

                $first_name    = isset($load_post_request['user_details']['first_name']) ? $load_post_request['user_details']['first_name'] : '';
                $last_name     = isset($load_post_request['user_details']['last_name']) ? $load_post_request['user_details']['last_name'] : '';
                $email         = isset($load_post_request['user_details']['email']) ? $load_post_request['user_details']['email'] : '';
                
                $country_code   = isset($load_post_request['user_details']['country_code']) ? $load_post_request['user_details']['country_code'] : '';
                $mobile_no      = isset($load_post_request['user_details']['mobile_no']) ? $load_post_request['user_details']['mobile_no'] : '';
                
                $full_name = $first_name.' '.$last_name;
                $full_name = ($full_name!=' ')?$full_name :'-';

                $full_mobile_no = $country_code.''.$mobile_no;
                $full_mobile_no = ($full_mobile_no!='')?$full_mobile_no :'';

                $data_id = isset($load_post_request['id']) ? $load_post_request['id'] : 0;

                $str_load_post_request = isset($load_post_request) ? htmlspecialchars(json_encode($load_post_request), ENT_QUOTES, 'UTF-8') : '';
           
                $load_post_html.= '<li>';
                $load_post_html.= '    <div class="avatar-content pull-left">';
                $load_post_html.= '        <a href="javascript:void(0)" data-attr-obj="'.$str_load_post_request.'" onclick="load_request_details(this)">';
                $load_post_html.= '        <div class="avtar-name">'.$load_post_request_unique_id.' - '.$request_status_html.'</div>';
                $load_post_html.= '        <div class="avtar-ps"><strong>Customer Name :</strong> '.$full_name.' </div>';
                $load_post_html.= '        <div class="avtar-ps"><strong>Contact No :</strong> '.$full_mobile_no.' </div>';
                $load_post_html.= '        <div class="avtar-ps"><strong>Pickup Location :</strong> '.$pickup_location.' </div>';
                $load_post_html.= '        <div class="avtar-ps"><strong>Drop Location :</strong> '.$drop_location.' </div>';
                $load_post_html.= '        <p>'.$date.'</p>';                
                $load_post_html.= '        </a>';
                $load_post_html.= '    </div>';
                $load_post_html.= '    <div class="avatar-ivew">';
                $load_post_html.= '        <a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip btn-dangers" href="javascript:void(0);"  data-id="'.$data_id.'"  onclick="cancel_request(this)" title="Cancel Request"><i class="fa fa-times" ></i></a>';
                $load_post_html.= '    </div>';
                $load_post_html.= '    <div class="clearfix"></div>';
                $load_post_html.= '</li>';
            }
            
        }
        else
        {
            if(isset($arr_load_post_request_details['load_post_request_unique_id']) == false)
            {
                $load_post_html.= '<li>';
                $load_post_html.= '   <div class="avatar-outr">';
                $load_post_html.= '   <div class="mp-txs"></div>';
                $load_post_html.= '     </div>';
                $load_post_html.= '    <div class="avatar-content">';
                $load_post_html.= '        <center><div class="avtar-name">Customer Shipment Post Request Not Available.</div></center>';
                $load_post_html.= '    </div>';
                $load_post_html.= '    <div class="clearfix"></div>';
                $load_post_html.= '</li> ';
            }
        }
        $load_post_html .= '</ul>';
        
        return $load_post_html;

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

    private function search_available_drivers($type,$request)
    {
        $date = new \DateTime();
        
        $date->modify('-1 min');
        $formatted_date = $date->format('Y-m-d H:i:s');
        $pickup_lat = $pickup_lng  = '';
        if($request->has('pickup_lat') && $request->input('pickup_lat')!=0)
        {
            $pickup_lat = $request->input('pickup_lat');
        }
        if($request->has('pickup_lng') && $request->input('pickup_lng')!=0)
        {
            $pickup_lng = $request->input('pickup_lng');
        }

        $sql_query = '';
        $sql_query .= "Select ";
        $sql_query .= "users.id AS driver_id, ";
        $sql_query .= "CONCAT( users.first_name, ' ',users.last_name ) AS driver_name, ";
        $sql_query .= "users.mobile_no AS mobile_no, ";
        $sql_query .= "users.email AS email, ";
        $sql_query .= "company.company_name AS company_name, ";
        $sql_query .= "users.company_name AS company_name, ";
        $sql_query .= "users.availability_status AS availability_status, ";
        $sql_query .= "users.is_company_driver AS is_company_driver, ";

        $sql_query .= "V.id AS vehicle_id, ";
        $sql_query .= "VT.id AS vehicle_type_original_id, ";
        $sql_query .= "V.vehicle_number AS vehicle_number, ";
        $sql_query .= "VT.vehicle_type AS vehicle_type_name, ";
        $sql_query .= "VT.vehicle_type_slug AS vehicle_type_slug, ";
        $sql_query .= "driver_available_status.current_latitude as current_latitude, ";
        $sql_query .= "driver_available_status.current_longitude as current_longitude, ";
        $sql_query .= "driver_available_status.status as status ,";
        $sql_query .= "driver_available_status.updated_at as last_location_updated_time ,";
        $sql_query .= "load_post_request.id as load_post_request_id ,";
        $sql_query .= "load_post_request.driver_id as load_post_request_driver_id ,";
        $sql_query .= "booking_master.id as booking_id ,";
        $sql_query .= "booking_master.booking_status as booking_status ,";

        // if($pickup_lat!='' && $pickup_lng!='')
        // {
        //     $sql_query .= "ROUND( 6371 * acos ( ";
        //     $sql_query .= " cos ( radians(".$pickup_lat.") ) ";
        //     $sql_query .= " * cos( radians( `current_latitude` ) ) ";
        //     $sql_query .= " * cos( radians( `current_longitude` ) - radians(".$pickup_lng.")) ";
        //     $sql_query .= " + sin ( radians(".$pickup_lat.") ) ";
        //     $sql_query .= " * sin( radians( `current_latitude` ) ) ";
        //     $sql_query .= ")) as distance, ";
        // }

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
        $sql_query .=  "LEFT JOIN ";
        $sql_query .=      "users as company ON company.id = users.company_id ";

        $sql_query .=  "LEFT JOIN ";
        $sql_query .=      "load_post_request ON load_post_request.driver_id = users.id ";

        $sql_query .=  "LEFT JOIN ";
        $sql_query .=      "booking_master ON booking_master.load_post_request_id = load_post_request.id ";

        $sql_query .= "WHERE ";

        $sql_query .=  "users.is_active = '1' AND ";
        $sql_query .=  "roles.slug = 'driver' AND ";
        $sql_query .=  "users.deleted_at IS NULL AND ";
        $sql_query .=  "users.stripe_account_id  != '' AND ";
        $sql_query .=  " ( driver_car_relation.is_car_assign = '1' OR driver_car_relation.is_individual_vehicle = '1' ) AND ";
        
        // $sql_query .=  "V.is_verified = '1'  ";

        $sql_query .=  "V.is_verified = '1' AND ";
        $sql_query .=  "driver_available_status.updated_at >= "."'".$formatted_date."'"."  ";
        
        $sql_query .=  "GROUP BY users.id ";

        // if($pickup_lat!='' && $pickup_lng!='')
        // {
        //     $sql_query .=  "HAVING distance <=".$this->distance." ";
        // }
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
        $sql_query .= "users.company_name AS company_name, ";
        $sql_query .= "users.is_company_driver AS is_company_driver, ";

        $sql_query .= "V.id AS vehicle_id, ";
        $sql_query .= "VT.id AS vehicle_type_original_id, ";
        $sql_query .= "V.vehicle_number AS vehicle_number, ";
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
            
        $sql_query .= "WHERE ";
        
        if($enc_id == null){
            $sql_query .=  "booking_master.booking_status IN ('TO_BE_PICKED', 'IN_TRANSIT') AND ";
        }

        if($enc_id !=null && $enc_id!=''){

            $sql_query .=  "booking_master.id = ".$enc_id." AND ";
        }

        $sql_query .=  "users.is_active = '1' AND ";
        
        $sql_query .=  " ( driver_car_relation.is_car_assign = '1' OR driver_car_relation.is_individual_vehicle = '1' )";
        
        $obj_driver_details =  \DB::select($sql_query);
        
        if($enc_id !=null && $enc_id!='' && $enc_id!=0){

            return isset($obj_driver_details[0]) ? $obj_driver_details[0] :[];
        }

        return $obj_driver_details;
    }

    public function assign_request_to_driver(Request $request)
    {
        $driver_id            = $request->input('driver_id');
        $load_post_request_id = $request->input('load_post_request_id');
        $mobile_no            = $request->input('mobile_no');
        
        if($driver_id == '')
        {
            $arr_response['status'] = 'error';
            $arr_response['msg']    = 'Driver indentifier is missing,unable to process request,Please try again.';
            $arr_response['data']   = [];
            return response()->json($arr_response);
        }

        if($load_post_request_id == '')
        {
            $arr_response['status'] = 'error';
            $arr_response['msg']    = 'Shipment load request indentifier is missing,unable to process request,Please try again.';
            $arr_response['data']   = [];
            return response()->json($arr_response);
        }

        $arr_load_post_request = [];

        $obj_load_post_request = $this->LoadPostRequestModel
                                                ->with(['user_details','load_post_request_history_details'=>function($query)use($driver_id){
                                                    $query->where('driver_id',$driver_id);
                                                    $query->whereIn('status',['REJECT_BY_DRIVER','TIMEOUT']);
                                                }])
                                                ->where('id',$load_post_request_id)
                                                ->first();
        if($obj_load_post_request)
        {
            if(isset($obj_load_post_request->load_post_request_history_details) && count($obj_load_post_request->load_post_request_history_details)>0)
            {
                $arr_response['status'] = 'error';
                $arr_response['msg']    = 'Sorry for the inconvenience,request already rejected by driver.';
                $arr_response['data']   = [];
                return response()->json($arr_response);               
            }
            if($mobile_no!='' && isset($obj_load_post_request->user_details->mobile_no) && $obj_load_post_request->user_details->mobile_no !='')
            {
                if($obj_load_post_request->user_details->mobile_no == $mobile_no)
                {
                    $arr_response['status'] = 'error';
                    $arr_response['msg']    = 'Shipment load request cannot be assign to same driver,which post shipment details ';
                    $arr_response['data']   = [];
                    return response()->json($arr_response);
                }
            }

            if(isset($obj_load_post_request->request_status) && $obj_load_post_request->request_status == 'ACCEPT_BY_USER')
            {
                $arr_response['status'] = 'error';
                $arr_response['msg']    = 'Sorry for the inconvenience,request already accepted by user unable to process further.';
                $arr_response['data']   = [];
                return response()->json($arr_response);               
            }

            if(isset($obj_load_post_request->request_status) && $obj_load_post_request->request_status == 'ACCEPT_BY_DRIVER')
            {
                $arr_response['status'] = 'error';
                $arr_response['msg']    = 'Sorry for the inconvenience,request already accepted unable to process further.';
                $arr_response['data']   = [];
                return response()->json($arr_response);               
            }

            $enc_user_id = isset($obj_load_post_request->user_id) ? $obj_load_post_request->user_id : 0;

            $obj_load_post_request->driver_id       = $driver_id;
            $obj_load_post_request->is_admin_assign = '1';
            $obj_load_post_request->created_at      = date('Y-m-d h:i:s');
            $status = $obj_load_post_request->save();
            if($status){
                

                $arr_notification_data = 
                                            [
                                                'title'             => 'Trip requested by customer',
                                                'notification_type' => 'USER_REQUEST',
                                                'record_id'         => $load_post_request_id,
                                                'is_admin_assign'   => '1',
                                                'enc_user_id'       => $driver_id,
                                                'user_type'         => 'DRIVER',

                                            ];

                $this->send_on_signal_notification($arr_notification_data);

                $arr_notification_data = 
                                            [
                                                'title'             => 'Trip requested by customer',
                                                'notification_type' => 'USER_REQUEST',
                                                'record_id'         => $load_post_request_id,
                                                'is_admin_assign'   => '1',
                                                'enc_user_id'       => $driver_id,
                                                'user_type'         => 'WEB',

                                            ];

                $this->send_on_signal_notification($arr_notification_data);

                // $arr_notification_data = 
                //                         [
                //                             'title'             => 'Your driver is not responding to your shipment request,sorry for the inconvenience, '.config('app.project.name').' Admin replaced the driver for you.',
                //                             'record_id'         => $load_post_request_id,
                //                             'driver_id'         => $driver_id,
                //                             'enc_user_id'       => $enc_user_id,
                //                             'notification_type' => 'ADMIN_ASSIGN',
                //                             'user_type'         => 'USER',
                //                         ];

                // $this->send_on_signal_notification($arr_notification_data);

                $arr_response['status'] = 'success';
                $arr_response['msg']    = 'Shipment Request has been successfully sent to driver ,waiting for driver acceptance.';
                $arr_response['data']   = [];
                return response()->json($arr_response);
            }
            else
            {
                $arr_response['status'] = 'error';
                $arr_response['msg']    = 'Problem occurred, while processing shipment post request';
                $arr_response['data']   = [];
                return response()->json($arr_response);
            }
        }
        $arr_response['status'] = 'error';
        $arr_response['msg']    = 'Sorry for inconvenience, shipment load request details not available,Please try again.';
        $arr_response['data']   = [];
        return response()->json($arr_response);
    }
    
    private function send_on_signal_notification($arr_notification_data)
    {
        if(isset($arr_notification_data)){
            
            $user_type = isset($arr_notification_data['user_type']) ? $arr_notification_data['user_type'] :'';
            
            if($user_type == '')
            {
                return FALSE;
            }

            $OneSignalAppId = $OneSignalApiKey = '';
            
            if($user_type == 'USER')
            {
                $OneSignalAppId  = $this->UserOneSignalAppId;
                $OneSignalApiKey = $this->UserOneSignalApiKey;
            }

            if($user_type == 'DRIVER')
            {
                $OneSignalAppId  = $this->DriverOneSignalAppId;
                $OneSignalApiKey = $this->DriverOneSignalApiKey;
            }

            if($user_type == 'WEB')
            {
                $OneSignalAppId  = $this->WebOneSignalAppId;
                $OneSignalApiKey = $this->WebOneSignalApiKey;
            }


            $custom_data =  [
                                "app_id"            => $OneSignalAppId ,
                                "record_id"         => isset($arr_notification_data['record_id']) ? intval($arr_notification_data['record_id']) :0,
                                "driver_id"         => isset($arr_notification_data['driver_id']) ? intval($arr_notification_data['driver_id']) :0,
                                "notification_type" => isset($arr_notification_data['notification_type']) ? $arr_notification_data['notification_type'] :'',
                            ];

            $title       = isset($arr_notification_data['title']) ? $arr_notification_data['title'] :'';
            $enc_user_id = isset($arr_notification_data['enc_user_id']) ? $arr_notification_data['enc_user_id'] :0;
          
            $filters     = array();
            $filters = array(array("field" => "tag", "key" => "active_user_id", "relation" => "=", "value" => $enc_user_id));

            if ($OneSignalAppId!='' && $OneSignalApiKey!='')
            {
                $fields = array(    
                                    'app_id'            => $OneSignalAppId,
                                    'headings'          => array("en" => 'Quick-Pick'),
                                    'filters'           => $filters,
                                    'data'              => $custom_data,
                                    'contents'          => array("en" => $title),
                                    'content_available' => true,
                                    'ios_badgeType'     => 'Increase',
                                    'ios_badgeCount'    => '1',
                                    'priority'          => 10,
                                );
                
                $fields = json_encode($fields);

                $ch = curl_init();

                curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8','Authorization: Basic '.$OneSignalApiKey));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                curl_setopt($ch, CURLOPT_HEADER, FALSE);
                curl_setopt($ch, CURLOPT_POST, TRUE);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
                $response = curl_exec($ch);
           
                curl_close($ch);
                return true;
            }
        }
        return true;
    }


    /*
    |
    | Users request
    |
    */
    public function request_list()
    {

        $this->module_url_path    = url('/').'/admin/request_list';
        $this->arr_view_data['page_title']      = "Manage Request List";
        $this->arr_view_data['module_title']    = "Manage Request List";
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['theme_color']     = $this->theme_color;
        return view($this->module_view_folder.'.request_list', $this->arr_view_data); 
    }


   
    public function get_records_request_list(Request $request)
    {
        $arr_current_user_access =[];
        $arr_current_user_access = $request->user()->permissions;
        $obj_bookings        =  $this->get_request_list($request);
        

        $json_result     = Datatables::of($obj_bookings);

        $json_result     = $json_result->blacklist(['id']);
        $current_context = $this;
        
        $json_result    = $json_result
                                    ->editColumn('enc_id',function($data) use ($current_context)
                                    {
                                        return base64_encode($data->id);
                                    })
                                    ->editColumn('request_id',function($data) use ($current_context)
                                    {
                                        return $data->request_id;
                                    })
                                    ->editColumn('build_action_btn',function($data) use ($current_context,$arr_current_user_access)
                                    {
                                        $build_view_action = '<a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip" href="#"  data-id="'.$data->id.'" onclick="booking_details(this)"  title="View"><i class="fa fa-eye" ></i></a>';
                                        $cancel_request = '<a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip btn-dangers" href="javascript:void(0);"  data-id="'.$data->id.'"  onclick="cancel_request(this)"    title="Cancel Request"><i class="fa fa-times" ></i></a>';
                                        return  $build_view_action.' '.$cancel_request;
                                        
                                    })
                                    ->editColumn('booking_status',function($data) use ($current_context,$arr_current_user_access)
                                    {
                                        $booking_status = '<span class="badge badge-success" style="width:200px">Drivers not responding</span>';
                                        if($data->booking_status == 'USER_REQUEST')
                                        {
                                            $booking_status = '<span class="badge badge-warning" style="width:200px">Waiting for Driver Acceptance </span>';
                                        }
                                        return  $booking_status;
                                    })
                                    
                                ->make(true);

        $build_result = $json_result->getData();

        return response()->json($build_result);
    }    

    private function get_request_list($request)
    {     
        $load_post_request_details          = $this->LoadPostRequestModel->getTable();
        $prefixed_load_post_request_details = DB::getTablePrefix().$this->LoadPostRequestModel->getTable();
        
        $user_details                       = $this->UserModel->getTable();
        $prefixed_user_details              = DB::getTablePrefix().$this->UserModel->getTable();

        $obj_user = DB::table($load_post_request_details)
                                ->select(DB::raw(  
                                                    $prefixed_load_post_request_details.".id as id,".
                                                    $prefixed_load_post_request_details.".user_id as user_id,".
                                                    "CONCAT(".$prefixed_user_details.".first_name,' ',"
                                                          .$prefixed_user_details.".last_name) as user_name,".
                                                    $prefixed_load_post_request_details.".load_post_request_unique_id as request_id,".  
                                                    "DATE_FORMAT(".$prefixed_load_post_request_details.".date,'%d %b %Y') as date,".
                                                    "TIME_FORMAT(".$prefixed_load_post_request_details.".date,'%h:%i:%s') as time,". 
                                                    $prefixed_load_post_request_details.".pickup_location as pickup_location,".
                                                    $prefixed_load_post_request_details.".drop_location as drop_location,".
                                                    $prefixed_load_post_request_details.".request_status as booking_status"

                                                ))

                                ->join($prefixed_user_details,$user_details.'.id','=',$prefixed_load_post_request_details.'.user_id')
                                ->whereIn($prefixed_load_post_request_details.'.request_status',['USER_REQUEST','REJECT_BY_DRIVER','TIMEOUT'])
                                // ->where($prefixed_load_post_request_details.'.is_future_request','=','0')
                                ->where(function($query) use($prefixed_load_post_request_details){
                                    $query->where($prefixed_load_post_request_details.'.is_future_request','0');
                                    $query->orWhere(function($inner_query) use($prefixed_load_post_request_details){
                                        $inner_query->where($prefixed_load_post_request_details.'.is_future_request','1');
                                        $inner_query->where($prefixed_load_post_request_details.'.is_request_process','1');
                                    });
                                })

                                ->orderBy($prefixed_load_post_request_details.'.created_at','DESC');

        /* ---------------- Filtering Logic ----------------------------------*/                    
       
        $arr_search_column = $request->input('column_filter');

        if(isset($arr_search_column['user_name']) && $arr_search_column['user_name']!="")
        {
            $search_term      = $arr_search_column['user_name'];
            $obj_user = $obj_user->having('user_name','LIKE', '%'.$search_term.'%');
        }

        if(isset($arr_search_column['date']) && $arr_search_column['date']!="")
        {
            $search_term     = $arr_search_column['date'];
            $obj_user        = $obj_user->having('date','LIKE', '%'.$search_term.'%');
        }

        if(isset($arr_search_column['time']) && $arr_search_column['time']!="")
        {
            $search_term     = $arr_search_column['time'];
            $obj_user        = $obj_user->having('time','LIKE', '%'.$search_term.'%');
        }

        if(isset($arr_search_column['request_id']) && $arr_search_column['request_id']!="")
        {
            $search_term      = $arr_search_column['q_request_id'];
            $obj_user = $obj_user->having('request_id','LIKE', '%'.$search_term.'%');
        }
         
        return $obj_user;
    }  

    public function booking_info(Request $request){
        $id          = $request->input('id');
        $type        = $request->input('type');

        $build_html = '';
        $obj_load_post_request = $this->LoadPostRequestModel
                                                ->with(['load_post_request_package_details','user_details','driver_details','driver_current_location_details'])
                                                ->where('id',$id)
                                                ->first();
        if(!empty($obj_load_post_request)){
            $arr_load_post_request = $obj_load_post_request->toArray();


            if(isset($arr_load_post_request) && sizeof($arr_load_post_request)>0){
                 
            $build_html .=  '<div class="review-detais">';
            $build_html .=  '<div class="boldtxts">Request ID : </div>';
            $build_html .=  '<div class="rightview-txt">';
            $build_html .=  isset($arr_load_post_request['load_post_request_unique_id']) ? $arr_load_post_request['load_post_request_unique_id'] : 0;
            $build_html .=  '</div>';
            $build_html .=  '<div class="clearfix"></div>';
            $build_html .=  '</div>';

            $build_html .=  '<div class="review-detais">';
            $build_html .=  '<div class="boldtxts">Request Date : </div>';
            $build_html .=  '<div class="rightview-txt">';
            $build_html .=  isset($arr_load_post_request['date']) ? date('d M Y',strtotime($arr_load_post_request['date'])) : '';
            $build_html .=  '</div>';
            $build_html .=  '<div class="clearfix"></div>';
            $build_html .=  '</div>';

            $build_html .=  '<div class="review-detais">';
            $build_html .=  '<div class="boldtxts">Request Time : </div>';
            $build_html .=  '<div class="rightview-txt">';
            $build_html .=  isset($arr_load_post_request['request_time'])&&$arr_load_post_request['request_time']!=''?date('h:i:s a',strtotime($arr_load_post_request['request_time'])) : '';
            $build_html .=  '</div>';
            $build_html .=  '<div class="clearfix"></div>';
            $build_html .=  '</div>';

            $first_name = isset($arr_load_post_request['user_details']['first_name']) ? $arr_load_post_request['user_details']['first_name'] : '';

            $last_name = isset($arr_load_post_request['user_details']['last_name']) ? $arr_load_post_request['user_details']['last_name'] : '';

            $build_html .=  '<div class="review-detais">';
            $build_html .=  '<div class="boldtxts">User Name : </div>';
            $build_html .=  '<div class="rightview-txt">';
            $build_html .=  $first_name.' '.$last_name;
            $build_html .= '</div>';
            $build_html .=  '<div class="clearfix"></div>';
            $build_html .=  '</div>';


            $build_html .=  '<div class="review-detais">';
            $build_html .=  '<div class="boldtxts">User Contact No / Email : </div>';
            $build_html .=  '<div class="rightview-txt">';
            $build_html .=  isset($arr_load_post_request['user_details']['country_code']) ? $arr_load_post_request['user_details']['country_code'] : '';

            $build_html .=  isset($arr_load_post_request['user_details']['mobile_no']) ? $arr_load_post_request['user_details']['mobile_no'] : '';
            
            if(isset($arr_load_post_request['user_details']['email']) && $arr_load_post_request['user_details']['email']!='')
            {
                $build_html .=  ' / ';
                $build_html .=  isset($arr_load_post_request['user_details']['email']) ? $arr_load_post_request['user_details']['email'] : '';
            }
            
            $build_html .= '</div>';
            $build_html .=  '<div class="clearfix"></div>';
            $build_html .=  '</div>';
 
            
            $build_html .= '</div>';
            $build_html .=  '<div class="clearfix"></div>';
            $build_html .=  '</div>';

            $build_html .=  '<div class="review-detais">';
            $build_html .=  '<div class="boldtxts">Pick Up Location : </div>';
            $build_html .=  '<div class="rightview-txt">';
            $build_html .=  isset($arr_load_post_request['pickup_location']) ? $arr_load_post_request['pickup_location'] : '';
            $build_html .= '</div>';
            $build_html .=  '<div class="clearfix"></div>';
            $build_html .=  '</div>';

            $build_html .=  '<div class="review-detais">';
            $build_html .=  '<div class="boldtxts">Drop Up Location : </div>';
            $build_html .=  '<div class="rightview-txt">';
            $build_html .=  isset($arr_load_post_request['drop_location']) ? $arr_load_post_request['drop_location'] : '';
            $build_html .= '</div>';
            $build_html .=  '<div class="clearfix"></div>';
            $build_html .=  '</div>';


            $build_html .=  '<div class="review-detais">';
            $build_html .=  '<div class="boldtxts">Package Type : </div>';
            $build_html .=  '<div class="rightview-txt">';
            $build_html .=  isset($arr_load_post_request['load_post_request_package_details']['package_type']) ? $arr_load_post_request['load_post_request_package_details']['package_type'] : '';;
            $build_html .= '</div>';
            $build_html .=  '<div class="clearfix"></div>';
            $build_html .=  '</div>';


            $build_html .=  '<div class="review-detais">';
            $build_html .=  '<div class="boldtxts">Package Length : </div>';
            $build_html .=  '<div class="rightview-txt">';
            $build_html .=  isset($arr_load_post_request['load_post_request_package_details']['package_length']) ? $arr_load_post_request['load_post_request_package_details']['package_length'] : 0;
            $build_html .= '</div>';
            $build_html .=  '<div class="clearfix"></div>';
            $build_html .=  '</div>';


            $build_html .=  '<div class="review-detais">';
            $build_html .=  '<div class="boldtxts">Package Breadth : </div>';
            $build_html .=  '<div class="rightview-txt">';
            $build_html .=   isset($arr_load_post_request['load_post_request_package_details']['package_breadth']) ? $arr_load_post_request['load_post_request_package_details']['package_breadth'] : 0;
            $build_html .= '</div>';
            $build_html .=  '<div class="clearfix"></div>';
            $build_html .=  '</div>';


            $build_html .=  '<div class="review-detais">';
            $build_html .=  '<div class="boldtxts">Package Height : </div>';
            $build_html .=  '<div class="rightview-txt">';
            $build_html .=  isset($arr_load_post_request['load_post_request_package_details']['package_height']) ? $arr_load_post_request['load_post_request_package_details']['package_height'] : 0;
            $build_html .= '</div>';
            $build_html .=  '<div class="clearfix"></div>';
            $build_html .=  '</div>';


            $build_html .=  '<div class="review-detais">';
            $build_html .=  '<div class="boldtxts">Package Weight : </div>';
            $build_html .=  '<div class="rightview-txt">';
            $build_html .=  isset($arr_load_post_request['load_post_request_package_details']['package_weight']) ? $arr_load_post_request['load_post_request_package_details']['package_weight'] : 0;
            $build_html .= '</div>';
            $build_html .=  '<div class="clearfix"></div>';
            $build_html .=  '</div>'; 

            $build_html .=  '<div class="review-detais">';
            $build_html .=  '<div class="boldtxts">Package Quantity : </div>';
            $build_html .=  '<div class="rightview-txt">';
            $build_html .=  isset($arr_load_post_request['load_post_request_package_details']['package_quantity']) ? $arr_load_post_request['load_post_request_package_details']['package_quantity'] : 0;
            $build_html .= '</div>';
            $build_html .=  '<div class="clearfix"></div>';
            $build_html .=  '</div>';

           

            $build_html .=  '<div class="review-detais">';
            $build_html .=  '<div class="boldtxts">Cancel Request: </div>';
            $build_html .=  '<div class="rightview-txt">';
            $build_html .=   '<div class="btn-group future-booking"><a class="btn btn-primary btn-add-new-records btn-dangers" href="javascript:void(0);"  data-id="'.$arr_load_post_request['id'].'"  onclick="cancel_request(this)"    title="Cancel Request">Cancel Request</a></div>';
            $build_html .= '</div>';
            $build_html .=  '<div class="clearfix"></div>';
            $build_html .=  '</div>';
             





            $arr_response = [];

                $arr_response['status']         = 'error';
                $arr_response['generated_html'] = '';
                
                if($build_html!='')
                {
                    $arr_response['status']         = 'success';
                    $arr_response['generated_html'] = $build_html;
                }

                return response()->json($arr_response); 

            }
        }

        $arr_response = [];
        $arr_response['status']         = 'error';
        $arr_response['generated_html'] = '';
        return response()->json($arr_response); 
    }


    public function cancel_request(Request $request){
        $id = $request->input('id');
        if($id!=0){

            $res =   $this->cancel_booking_request($id);
             if($res){
                $arr_response = [];
                $arr_response['status']         = 'success';
             }else{
                $arr_response = [];
                $arr_response['status']         = 'error_problem';
             }
            return response()->json($arr_response); 
        }
        $arr_response = [];
        $arr_response['status']         = 'error_not_id';
        $arr_response['generated_html'] = '';
        return response()->json($arr_response); 
    }


    public function cancel_booking_request($id)
    {

        $obj_load_post_request = $this->LoadPostRequestModel
                                                ->with(['user_details','driver_details'])
                                                ->where('id',$id)
                                                ->first();
        if(!empty($obj_load_post_request))
        {
            $arr_data   =  $obj_load_post_request->toArray();
            
            $arr_update = [];
            $arr_update['request_status'] = 'CANCEL_BY_ADMIN';

            $res  = LoadPostRequestModel::where('id',$id)->update($arr_update);

            $arr_insert = [];
            $arr_insert['load_post_request_id'] = $id;
            $arr_insert['user_id']              = $arr_data['user_id'];
            $arr_insert['status']               = 'CANCEL_BY_ADMIN';
            $res2 = LoadPostRequestHistoryModel::create($arr_insert);
            if($res || $res2)
            {
                $arr_notification_data = 
                                                [
                                                    'title'             => config('app.project.name').' Admin canceled your booking request.',
                                                    'notification_type' => 'CANCEL_BY_ADMIN',
                                                    'enc_user_id'       => $arr_data['user_id'],
                                                    'user_type'         => 'USER',

                                                ];
                $this->NotificationsService->send_on_signal_notification($arr_notification_data);


                return 1;
            }
        }
        else
        {
            return 0;
        }
    }

    public function cancel_trip_request($enc_id,Client $client)
    {
        $enc_id = base64_decode($enc_id);
        if($enc_id == ''){
            Flash::error('Booking identifier is missing,unable to cancel trip.');
            return redirect()->back();
        }
        
        $obj_booking_master = $this->BookingMasterModel
                                            ->select('id','load_post_request_id','booking_status')
                                            ->with(['load_post_request_details'=>function($query){
                                                $query->select('id','user_id','driver_id');
                                                $query->with(['user_details'=>function($query){
                                                                $query->select('id','first_name','last_name','country_code','mobile_no');
                                                            },'driver_details'=>function($query){
                                                                $query->select('id','first_name','last_name','country_code','mobile_no');
                                                        }]);
                                            }])
                                            ->where('id',$enc_id)
                                            ->first();
        // dd($obj_booking_master->toArray());

        if($obj_booking_master)
        {
            $arr_required_status = ['TO_BE_PICKED','IN_TRANSIT'];
                    
            if( isset($obj_load_post_request->request_status) && (!in_array($obj_load_post_request->request_status, $arr_required_status)))
            {
                Flash::error('Booking details are not valid, unable to cancel trip.');
                return redirect()->back();        
            }
            
            $obj_booking_master->booking_status = 'CANCEL_BY_ADMIN';
            $status = $obj_booking_master->save();
            if($status){

                //send twilio sms eta notifications
                if(isset($client)){
                    
                    $messageBody         = 'Your trip has been canceled by '.config('app.project.name').' Admin.';

                    //send sms to user
                    $user_country_code   = isset($obj_booking_master->load_post_request_details->user_details->country_code) ? $obj_booking_master->load_post_request_details->user_details->country_code :'';
                    $user_mobile_no      = isset($obj_booking_master->load_post_request_details->user_details->mobile_no) ? $obj_booking_master->load_post_request_details->user_details->mobile_no :'';
                    $user_full_mobile_no = $user_country_code.''.$user_mobile_no;
                    
                    if($user_full_mobile_no!=''){
                        $this->sendEtaNotificationsMessage(
                                                $client,
                                                $user_full_mobile_no,
                                                $messageBody,
                                                ''
                                            );
                    }
                    
                    //send sms to driver
                    $driver_country_code   = isset($obj_booking_master->load_post_request_details->driver_details->country_code) ? $obj_booking_master->load_post_request_details->driver_details->country_code :'';
                    $driver_mobile_no      = isset($obj_booking_master->load_post_request_details->driver_details->mobile_no) ? $obj_booking_master->load_post_request_details->driver_details->mobile_no :'';
                    $driver_full_mobile_no = $driver_country_code.''.$driver_mobile_no;
                    if($driver_full_mobile_no!=''){
                        $this->sendEtaNotificationsMessage(
                                                $client,
                                                $driver_full_mobile_no,
                                                $messageBody,
                                                ''
                                            );
                    }                   
                }


                $enc_user_id   = isset($obj_booking_master->load_post_request_details->user_id) ? $obj_booking_master->load_post_request_details->user_id : 0;
                $enc_driver_id = isset($obj_booking_master->load_post_request_details->driver_id) ? $obj_booking_master->load_post_request_details->driver_id : 0;
                
                $arr_user_notification_data = 
                                                [
                                                    'title'             => config('app.project.name').' Admin canceled your booking request.',
                                                    'notification_type' => 'TRIP_CANCEL_BY_ADMIN',
                                                    'enc_user_id'       => $enc_user_id,
                                                    'user_type'         => 'USER',

                                                ];
                $this->NotificationsService->send_on_signal_notification($arr_user_notification_data);

                $arr_driver_notification_data = 
                                                [
                                                    'title'             => config('app.project.name').' Admin canceled your booking request.',
                                                    'notification_type' => 'TRIP_CANCEL_BY_ADMIN',
                                                    'enc_user_id'       => $enc_driver_id,
                                                    'user_type'         => 'DRIVER',

                                                ];

                $this->NotificationsService->send_on_signal_notification($arr_driver_notification_data);
                
                //web user
                $arr_user_notification_data = 
                                                [
                                                    'title'             => config('app.project.name').' Admin canceled your booking request.',
                                                    'notification_type' => 'TRIP_CANCEL_BY_ADMIN',
                                                    'enc_user_id'       => $enc_user_id,
                                                    'user_type'         => 'WEB',

                                                ];
                $this->NotificationsService->send_on_signal_notification($arr_user_notification_data);

                //web driver
                $arr_driver_notification_data = 
                                                [
                                                    'title'             => config('app.project.name').' Admin canceled your booking request.',
                                                    'notification_type' => 'TRIP_CANCEL_BY_ADMIN',
                                                    'enc_user_id'       => $enc_driver_id,
                                                    'user_type'         => 'WEB',

                                                ];

                $this->NotificationsService->send_on_signal_notification($arr_driver_notification_data);

                $driver_status = 'AVAILABLE';
                $this->CommonDataService->change_driver_status($enc_driver_id,$driver_status);

                Flash::success('Ongoing Trip canceled successfully.');
                return redirect()->back();        

            }
            else
            {
                Flash::error('Problem occurred while changing trip status,Please try again');
                return redirect()->back();        
            }



        }
        Flash::error('Booking details not found,unable to cancel trip,Please try again.');
        return redirect()->back();

    }
    
    private function sendEtaNotificationsMessage($client, $to, $messageBody, $callbackUrl)
    {

        $twilioNumber = config('app.project.twilio_credentials.from_user_mobile');
        try {
            $client->messages->create(
                $to, // Text any number
                [
                    'from' => $twilioNumber, // From a Twilio number in your account
                    'body' => $messageBody,
                    'statusCallback' => $callbackUrl
                ]
            );
            return true;
        } catch (\Exception $e) {
            return true;
        }
        return true;
    }

    public function multi_action(Request $request)
    {
        $arr_rules = array();
        $arr_rules['multi_action'] = "required";
        $arr_rules['checked_record'] = "required";

        $validator = Validator::make($request->all(),$arr_rules);

        if($validator->fails())
        {
            Flash::error('Please Select '.$this->module_title.' To Perform Multi Actions');
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $multi_action   = $request->input('multi_action');
        $checked_record = $request->input('checked_record');

        /* Check if array is supplied*/
        if(is_array($checked_record) && sizeof($checked_record)<=0)
        {
            Flash::error('Problem Occurred, While Doing Multi Action');
            return redirect()->back();
        }

        foreach ($checked_record as $key => $record_id) 
        {   
            if($multi_action=="cancel_request")
            {
               $this->cancel_booking_request(base64_decode($record_id));    
               Flash::success('User Request Canceled Successfully'); 
            } 
        }
        
        return redirect()->back();
    }


    /*
    |
    | Users Future Booking Request
    |
    */
    public function future_booking()
    {

        $this->module_url_path    = url('/').'/admin/future_booking';
        $this->arr_view_data['page_title']      = "Manage Future Booking Request List";
        $this->arr_view_data['module_title']    = "Manage Future Booking Request List";
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['theme_color']     = $this->theme_color;
        return view($this->module_view_folder.'.future_request_list', $this->arr_view_data); 
    }


   
    public function get_records_future_list(Request $request)
    {
        $arr_current_user_access =[];
        $arr_current_user_access = $request->user()->permissions;
        $obj_bookings        =  $this->get_future_request_list($request);
        

        $json_result     = Datatables::of($obj_bookings);

        $json_result     = $json_result->blacklist(['id']);
        $current_context = $this;
        
        $json_result    = $json_result
                                    ->editColumn('enc_id',function($data) use ($current_context)
                                    {
                                        return base64_encode($data->id);
                                    })
                                   /* ->editColumn('time',function($data) use ($current_context)
                                    {   
                                        $time = isset($data->time)&&$data->time!=''?date('h:i:s a',strtotime($data->time)):'';
                                        return $time; 
                                    })*/
                                    ->editColumn('build_action_btn',function($data) use ($current_context,$arr_current_user_access)
                                    {
                                         
                                        $build_view_action = '<a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip" href="#"  data-id="'.$data->id.'" onclick="booking_details(this)"  title="View"><i class="fa fa-eye" ></i></a>';


                                         $cancel_request = '<a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip btn-dangers" href="javascript:void(0);"  data-id="'.$data->id.'"  onclick="cancel_request(this)"    title="Cancel Request"><i class="fa fa-times" ></i></a>';
                                         return  $build_view_action." ".$cancel_request;
                                        
                                    })
                                    ->editColumn('booking_status',function($data) use ($current_context,$arr_current_user_access)
                                    {
                                        $booking_status = '';
                                        if($data->booking_status == 'USER_REQUEST')
                                        {
                                            $booking_status = '<span class="badge badge-success" style="width:115px">User Requested </span>';
                                        }
                                        return  $booking_status;
                                    })
                                    
                                ->make(true);

        $build_result = $json_result->getData();

        return response()->json($build_result);
    }    

    private function get_future_request_list($request)
    {     
        

        $load_post_request_details          = $this->LoadPostRequestModel->getTable();
        $prefixed_load_post_request_details = DB::getTablePrefix().$this->LoadPostRequestModel->getTable();
        
        $user_details                       = $this->UserModel->getTable();
        $prefixed_user_details              = DB::getTablePrefix().$this->UserModel->getTable();

        $obj_user = DB::table($load_post_request_details)
                                ->select(DB::raw(  "CONCAT(".$prefixed_user_details.".first_name,' ',"
                                                          .$prefixed_user_details.".last_name) as user_name,".
                                                    $prefixed_load_post_request_details.".load_post_request_unique_id as request_id,".  
                                                    "DATE_FORMAT(".$prefixed_load_post_request_details.".date,'%d %b %Y') as date,".  
                                                    "TIME_FORMAT(".$prefixed_load_post_request_details.".request_time ,'%h:%i:%s ') as time,". 
                                                   /*  $prefixed_load_post_request_details.".request_time  as time,". */
                                                    $prefixed_load_post_request_details.".id as id,".
                                                    $prefixed_load_post_request_details.".pickup_location as pickup_location,".
                                                    $prefixed_load_post_request_details.".drop_location as drop_location,".
                                                    $prefixed_load_post_request_details.".request_status as booking_status"

                                                ))

                                 ->join($prefixed_user_details,$user_details.'.id','=',$prefixed_load_post_request_details.'.user_id')
                                ->where($prefixed_load_post_request_details.'.request_status','=','USER_REQUEST')
                                ->where($prefixed_load_post_request_details.'.is_future_request','=','1')
                                ->where($prefixed_load_post_request_details.'.is_request_process','=','0')
                                ->orderBy($prefixed_load_post_request_details.'.created_at','DESC');

        /* ---------------- Filtering Logic ----------------------------------*/                    
       
        $arr_search_column = $request->input('column_filter');
        
        if(isset($arr_search_column['user_name']) && $arr_search_column['user_name']!="")
        {
            $search_term      = $arr_search_column['user_name'];
            $obj_user = $obj_user->having('user_name','LIKE', '%'.$search_term.'%');
        }

        if(isset($arr_search_column['date']) && $arr_search_column['date']!="")
        {
            $search_term     = $arr_search_column['date'];
            $obj_user        = $obj_user->having('date','LIKE', '%'.$search_term.'%');
        }

        if(isset($arr_search_column['time']) && $arr_search_column['time']!="")
        {
            $search_term     = $arr_search_column['time'];
            $obj_user        = $obj_user->having('time','LIKE', '%'.$search_term.'%');
        }

        if(isset($arr_search_column['request_id']) && $arr_search_column['request_id']!="")
        {
            $search_term      = $arr_search_column['request_id'];
            $obj_user = $obj_user->having('request_id','LIKE', '%'.$search_term.'%');
        }
         
        return $obj_user;
    }  

}
