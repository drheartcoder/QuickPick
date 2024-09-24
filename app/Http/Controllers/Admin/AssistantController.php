<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\UserModel;
use App\Models\LoadPostRequestModel;
use App\Models\BookingMasterModel;


use App\Common\Services\CommonDataService;

use Flash;

class AssistantController extends Controller
{
    public function __construct(UserModel $user_model,
                                LoadPostRequestModel $load_post_request,
                                BookingMasterModel $booking_master,
                                CommonDataService $common_data_service)
    {
        $this->UserModel                    = $user_model;
        $this->LoadPostRequestModel         = $load_post_request;
        $this->BookingMasterModel           = $booking_master;
        $this->CommonDataService            = $common_data_service;
        $this->arr_view_data                = [];
        $this->module_title                 = "Admin Assistant";
        $this->module_view_folder           = "admin.assistant";
        $this->theme_color                  = theme_color();
        $this->admin_panel_slug             = config('app.project.admin_panel_slug');
        $this->module_url_path              = url(config('app.project.admin_panel_slug')."/assistant");

        $this->DriverOneSignalApiKey = config('app.project.one_signal_credentials.driver_api_key');
        $this->DriverOneSignalAppId  = config('app.project.one_signal_credentials.driver_app_id');
        $this->UserOneSignalApiKey   = config('app.project.one_signal_credentials.user_api_key');
        $this->UserOneSignalAppId    = config('app.project.one_signal_credentials.user_app_id');

    }
    
    public function index(Request $request)
    {
    	$arr_load_post_request = [];

    	$obj_load_post_request = $this->LoadPostRequestModel
                                                ->with(['user_details'])
                                                ->where('is_admin_assistant','YES')
                                                ->where('driver_id',0)
                                                // ->orderBy('id','DESC')
                                                ->get();
        if($obj_load_post_request)
        {
        	$arr_load_post_request = $obj_load_post_request->toArray();
        }
        $this->arr_view_data['page_title']                 ="Manage ".$this->module_title;
        $this->arr_view_data['module_title']               = $this->module_title;
        $this->arr_view_data['module_url_path']            = $this->module_url_path;
        $this->arr_view_data['theme_color']                = $this->theme_color;
        $this->arr_view_data['arr_load_post_request']      = $arr_load_post_request;

        return view($this->module_view_folder.'.index', $this->arr_view_data);
    }

    public function view($enc_id)
    {
        $id        = base64_decode($enc_id);
        
        $arr_load_post_request = [];

    	$obj_load_post_request = $this->LoadPostRequestModel
                                                ->with(['user_details','load_post_request_package_details'])
                                                ->where('is_admin_assistant','YES')
                                                ->where('driver_id',0)
                                                ->where('id',$id)
                                                ->first();
        if($obj_load_post_request)
        {
        	$arr_load_post_request = $obj_load_post_request->toArray();
        }
        
        if(sizeof($arr_load_post_request)<=0)
        {
        	Flash::error('Cannot proccess load post request,Pleae try again.');
        	return redirect($this->module_url_path);
        }

        $this->arr_view_data['page_title']            = "View ".str_singular($this->module_title);
        $this->arr_view_data['module_title']          = str_plural($this->module_title);
        $this->arr_view_data['module_url_path']       = $this->module_url_path;
        $this->arr_view_data['theme_color']           = $this->theme_color;
        $this->arr_view_data['arr_load_post_request'] = $arr_load_post_request;
        
        return view($this->module_view_folder.'.view',$this->arr_view_data);
    }
    public function search_nearby_drivers(Request $request)
    {
        $load_post_request_id = $request->input('load_post_request_id');
        $pickup_lat           = $request->input('pickup_lat');
        $pickup_lng           = $request->input('pickup_lng');
        $package_volume       = $request->input('package_volume');
        $package_weight       = $request->input('package_weight');
        $distance             = 20;
        
        $sql_query = '';
        $sql_query .= "Select ";
        $sql_query .= "users.id AS driver_id, ";
        $sql_query .= "CONCAT( users.first_name, ' ',users.last_name ) AS driver_name, ";
        $sql_query .= "CONCAT( users.country_code, '',users.mobile_no ) AS mobile_no, ";
        $sql_query .= "users.email AS email, ";
        $sql_query .= "VT.id AS vehicle_type_original_id, ";
        $sql_query .= "VT.vehicle_type AS vehicle_type_name, ";
        $sql_query .= "VT.vehicle_min_volume AS vehicle_min_volume, ";
        $sql_query .= "VT.vehicle_max_volume AS vehicle_max_volume, ";
        $sql_query .= "V.id AS vehicle_id, ";
        $sql_query .= "V.vehicle_type_id, ";
        $sql_query .= "V.vehicle_name AS vehicle_name, ";
        $sql_query .= "V.is_verified AS is_verified, ";
        $sql_query .= "driver_available_status.current_latitude as current_latitude, ";
        $sql_query .= "driver_available_status.current_longitude as current_longitude, ";
        $sql_query .= "driver_available_status.status as status, ";
        $sql_query .= "driver_car_relation.is_individual_vehicle as is_individual_vehicle, ";



        $sql_query .= "ROUND( 6371 * acos ( ";
        $sql_query .= " cos ( radians(".$pickup_lat.") ) ";
        $sql_query .= " * cos( radians( `current_latitude` ) ) ";
        $sql_query .= " * cos( radians( `current_longitude` ) - radians(".$pickup_lng.")) ";
        $sql_query .= " + sin ( radians(".$pickup_lat.") ) ";
        $sql_query .= " * sin( radians( `current_latitude` ) ) ";
        $sql_query .= ")) as distance ";

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
        $sql_query .= "WHERE ";

        $sql_query .=  "users.is_active = '1' AND ";
        $sql_query .=  "roles.slug = 'driver' AND ";
        $sql_query .=  "users.availability_status  = 'ONLINE' AND "; /* added by padmashri*/
        $sql_query .=  "driver_available_status.status = 'AVAILABLE' AND ";
        $sql_query .=  " ( driver_car_relation.is_car_assign = '1' OR driver_car_relation.is_individual_vehicle = '1' ) AND ";
        $sql_query .=  "VT.vehicle_min_volume <= ".$package_volume." AND ";
        $sql_query .=  "VT.vehicle_max_volume >= ".$package_volume." AND ";
        $sql_query .=  "VT. vehicle_min_weight <= ".$package_weight." AND ";
        $sql_query .=  "VT.vehicle_max_weight >= ".$package_weight." ";

        $sql_query .=  "HAVING distance <=".$distance;
       
        $obj_driver_details =  \DB::select($sql_query);

        if(isset($obj_driver_details) && sizeof($obj_driver_details)>0){
            
            /*
            |manually fliter data also remove driver which does not verify their indivital vechiles 
            |also remove drivers which are in restricted area.
            */
            foreach ($obj_driver_details as $key => $driver_details) 
            {
                if($driver_details->is_individual_vehicle == '1')
                {
                    if($driver_details->is_verified == '0')
                    {
                        unset($obj_driver_details[$key]);
                    }

                }
            }
            
            $arr_result['status']      = 'success';
            $arr_result['arr_drivers'] = $obj_driver_details;
            return response()->json($arr_result);    
        }
        $arr_result['status']      = 'error';
        $arr_result['arr_drivers'] = [];
        return response()->json($arr_result);    
    }
    public function assign_driver(Request $request)
    {
        $driver_id = base64_decode($request->input('driver_id'));
        $load_post_request_id = base64_decode($request->input('load_post_request_id'));
        $vehicle_id = base64_decode($request->input('vehicle_id'));
        
        $arr_load_post_request = [];

        $obj_load_post_request = $this->LoadPostRequestModel
                                                ->where('request_status','USER_REQUEST')
                                                ->where('id',$load_post_request_id)
                                                ->first();
        if($obj_load_post_request)
        {
            $driver_status = 'BUSY';
            $this->CommonDataService->change_driver_status($driver_id,$driver_status);

            $obj_load_post_request->request_status = 'ACCEPT_BY_USER';
            $obj_load_post_request->driver_id      = $driver_id;
            $obj_load_post_request->vehicle_id     = $vehicle_id;

            $status = $obj_load_post_request->save();
           
            if($status)
            {
                /*store booking master details in database and prooced further*/    
                $arr_response = $this->store_admin_booking_master_details($load_post_request_id);
                return $arr_response;
            }
            else
            {
                $arr_response['status'] = 'error';
                $arr_response['msg']    = 'Problem occurred, while processing load post request, Please try again.';
                $arr_response['data']   = [];
                return $arr_response;
            }

        }
        else
        {
            $arr_response['status'] = 'error';
            $arr_response['msg']    = 'Problem occurred, while processing load post request, Please try again.';
            $arr_response['data']   = [];
            return $arr_response;
        }

        $arr_response['status'] = 'error';
        $arr_response['msg']    = 'Problem occurred, while processing load post request, Please try again.';
        $arr_response['data']   = [];
        return $arr_response;
    }

    private function store_admin_booking_master_details($load_post_request_id)
    {
        if($load_post_request_id!=''){

            $arr_load_post_request = $this->get_load_post_request_details($load_post_request_id);
            
            if(sizeof($arr_load_post_request)>0)
            {
                $card_id                 = isset($arr_load_post_request['card_id']) ? $arr_load_post_request['card_id'] :0;
                $vehicle_id              = isset($arr_load_post_request['vehicle_id']) ? $arr_load_post_request['vehicle_id'] :0;
                $user_id                 = isset($arr_load_post_request['user_id']) ? $arr_load_post_request['user_id'] :0;
                $driver_id               = isset($arr_load_post_request['driver_id']) ? $arr_load_post_request['driver_id'] :0;
                $promo_code_id           = isset($arr_load_post_request['promo_code_id']) ? $arr_load_post_request['promo_code_id'] :0;
                $is_bonus                = isset($arr_load_post_request['is_bonus']) ? $arr_load_post_request['is_bonus'] :'';
                
                $starting_price          = isset($arr_load_post_request['vehicle_details']['vehicle_type_details']['starting_price']) ? $arr_load_post_request['vehicle_details']['vehicle_type_details']['starting_price'] : 0 ;
                $per_miles_price         = isset($arr_load_post_request['vehicle_details']['vehicle_type_details']['per_miles_price']) ? $arr_load_post_request['vehicle_details']['vehicle_type_details']['per_miles_price'] : 0 ;
                $per_minute_price        = isset($arr_load_post_request['vehicle_details']['vehicle_type_details']['per_minute_price']) ? $arr_load_post_request['vehicle_details']['vehicle_type_details']['per_minute_price'] : 0 ;
                $minimum_price           = isset($arr_load_post_request['vehicle_details']['vehicle_type_details']['minimum_price']) ? $arr_load_post_request['vehicle_details']['vehicle_type_details']['minimum_price'] : 0 ;
                $cancellation_base_price = isset($arr_load_post_request['vehicle_details']['vehicle_type_details']['cancellation_base_price']) ? $arr_load_post_request['vehicle_details']['vehicle_type_details']['cancellation_base_price'] : 0 ;

                $arr_admin_referral_points_details = [];
                
                $user_bonus_points = $user_bonus_points_usd_amount = 0;

                $admin_referral_points = $admin_referral_points_price_per_usd = 0;
                
                if($is_bonus == 'YES')
                {
                    $user_bonus_points             = $this->CommonDataService->get_user_bonus_points($user_id);
                }
                
                if($user_bonus_points>0)
                {
                    $arr_admin_referral_points_details = $this->CommonDataService->get_admin_referral_points_details();
                    
                    $admin_referral_points               = isset($arr_admin_referral_points_details['referral_points']) ? intval($arr_admin_referral_points_details['referral_points']) : 0;
                    $admin_referral_points_price_per_usd = isset($arr_admin_referral_points_details['referral_points_price']) ? intval($arr_admin_referral_points_details['referral_points_price']) :0;
                }
                // dd($user_bonus_points_usd_amount);
                if($user_bonus_points>0 && $admin_referral_points_price_per_usd>0)
                {
                    $user_bonus_points_usd_amount = ($user_bonus_points/$admin_referral_points_price_per_usd);
                }
                
                $admin_driver_percentage = $admin_company_percentage = $individual_driver_percentage = $company_driver_percentage = 0;

                $is_individual_vehicle = $is_company_driver = '0';

                $arr_is_individual_vehicle = $this->CommonDataService->check_is_individual_vehicle($vehicle_id);

                $company_id                = isset($arr_is_individual_vehicle['company_id']) ? $arr_is_individual_vehicle['company_id'] :'0';
                $is_individual_vehicle     = isset($arr_is_individual_vehicle['is_individual_vehicle']) ? $arr_is_individual_vehicle['is_individual_vehicle'] :'0';
                $is_company_driver         = isset($arr_is_individual_vehicle['is_company_vehicle']) ? $arr_is_individual_vehicle['is_company_vehicle'] :'0';
                
                $arr_admin_commission_per     = $this->CommonDataService->get_admin_commission_percentage();
                $admin_driver_percentage      = isset($arr_admin_commission_per['admin_driver_percentage']) ? doubleval($arr_admin_commission_per['admin_driver_percentage']) :0;
                $admin_company_percentage     = isset($arr_admin_commission_per['company_percentage']) ? doubleval($arr_admin_commission_per['company_percentage']) :0;
                $individual_driver_percentage = isset($arr_admin_commission_per['individual_driver_percentage']) ? doubleval($arr_admin_commission_per['individual_driver_percentage']) :0;

                if($is_individual_vehicle == '1')
                {
                    $admin_driver_percentage = 0;    
                }
                
                if($is_individual_vehicle == '0'){
                    $individual_driver_percentage = 0;
                }

                if($is_company_driver == '0')
                {   
                    $admin_company_percentage = 0;
                }
                else if($is_company_driver == '1')
                {   
                    $individual_driver_percentage = 0;
                    $admin_driver_percentage      = 0;
                    $arr_company_commission_per   = $this->CommonDataService->get_company_commission_percentage($company_id);
                    $company_driver_percentage    = isset($arr_company_commission_per['company_driver_percentage']) ? doubleval($arr_company_commission_per['company_driver_percentage']) :0;
                }
                
                $arr_promo_code_details = $this->CommonDataService->get_promo_code_details($promo_code_id);

                $is_promo_code_applied = 'NO';
                if(isset($arr_promo_code_details) && sizeof($arr_promo_code_details)>0){
                    $is_promo_code_applied = 'YES';
                }
                
                $promo_percentage = isset($arr_promo_code_details['percentage']) ? $arr_promo_code_details['percentage'] :0;
                
                if($is_individual_vehicle == '1')
                {
                    if($promo_percentage>$individual_driver_percentage){
                        $promo_percentage = $individual_driver_percentage;
                    }
                }
                
                if($is_company_driver == '0')
                {   
                    if($promo_percentage>$admin_driver_percentage){
                        $promo_percentage = $admin_driver_percentage;
                    }
                }
                else if($is_company_driver == '1')
                {   
                    if($promo_percentage>$admin_company_percentage){
                        $promo_percentage = $admin_company_percentage;
                    }
                }

                $arr_booking_master                                        = [];
                $arr_booking_master['load_post_request_id']                = $load_post_request_id;
                $arr_booking_master['booking_unique_id']                   = $this->genrate_booking_unique_number();
                $arr_booking_master['booking_date']                        = date('Y-m-d');
                $arr_booking_master['start_datetime']                      = date('Y-m-d H:i:s');
                $arr_booking_master['end_datetime']                        = null;
                $arr_booking_master['card_id']                             = $card_id;
                $arr_booking_master['is_promo_code_applied']               = $is_promo_code_applied;
                $arr_booking_master['promo_code']                          = isset($arr_promo_code_details['code']) ? $arr_promo_code_details['code'] :'';
                $arr_booking_master['promo_percentage']                    = $promo_percentage;
                $arr_booking_master['promo_max_amount']                    = isset($arr_promo_code_details['max_amount']) ? $arr_promo_code_details['max_amount'] :0;
                $arr_booking_master['applied_promo_code_charge']           = 0;
                $arr_booking_master['is_company_driver']                   = $is_company_driver;
                $arr_booking_master['is_individual_vehicle']               = $is_individual_vehicle;
                $arr_booking_master['starting_price']                      = $starting_price;
                $arr_booking_master['per_miles_price']                     = $per_miles_price;
                $arr_booking_master['per_minute_price']                    = $per_minute_price;
                $arr_booking_master['minimum_price']                       = $minimum_price;
                $arr_booking_master['cancellation_base_price']             = $cancellation_base_price;
                $arr_booking_master['admin_driver_percentage']             = $admin_driver_percentage;
                $arr_booking_master['admin_company_percentage']            = $admin_company_percentage;
                $arr_booking_master['individual_driver_percentage']        = $individual_driver_percentage;
                $arr_booking_master['company_driver_percentage']           = $company_driver_percentage;
                $arr_booking_master['is_bonus_used']                       = $is_bonus;
                $arr_booking_master['admin_referral_points']               = $admin_referral_points;
                $arr_booking_master['admin_referral_points_price_per_usd'] = $admin_referral_points_price_per_usd;
                $arr_booking_master['user_bonus_points']                   = $user_bonus_points;
                $arr_booking_master['user_bonus_points_usd_amount']        = $user_bonus_points_usd_amount;
                $arr_booking_master['distance']                            = 0;
                $arr_booking_master['total_charge']                        = 0;
                $arr_booking_master['total_amount']                        = 0;
                $arr_booking_master['admin_amount']                        = 0;
                $arr_booking_master['company_amount']                      = 0;
                $arr_booking_master['admin_driver_amount']                 = 0;
                $arr_booking_master['company_driver_amount']               = 0;
                $arr_booking_master['individual_driver_amount']            = 0;
                $arr_booking_master['payment_type']                        = 'STRIPE';
                $arr_booking_master['admin_payment_status']                = 'UNPAID';
                $arr_booking_master['payment_status']                      = 'PENDING';
                $arr_booking_master['booking_status']                      = 'TO_BE_PICKED';
                
                $obj_booking_master = $this->BookingMasterModel->create($arr_booking_master);
                if($obj_booking_master){
                    
                    $booking_master_id = isset($obj_booking_master->id) ? $obj_booking_master->id :0;

                     /*Send on signal notification to user that driver accepted your request*/
                    $arr_notification_data = 
                                            [
                                                'title'             => config('app.project.name').' Admin assign you for current ride.',
                                                'record_id'         => $booking_master_id,
                                                'enc_user_id'       => $driver_id,
                                                'notification_type' => 'ACCEPT_BY_USER',
                                                'user_type'         => 'DRIVER',
                                            ];

                    $this->send_on_signal_notification($arr_notification_data);


                     /*Send on signal notification to user that admin assign driver to load post request*/
                    $arr_notification_data = 
                                            [
                                                'title'             => config('app.project.name').' Admin assign driver for your load.',
                                                'record_id'         => $booking_master_id,
                                                'enc_user_id'       => $user_id,
                                                'notification_type' => 'ADMIN_ASSIGN',
                                                'user_type'         => 'USER',
                                            ];

                    $this->send_on_signal_notification($arr_notification_data);

                    $ride_status = 'TO_BE_PICKED';

                    $view_url = url(config('app.project.admin_panel_slug')."/track_booking/view?enc_id=".base64_encode($booking_master_id).'&status='.base64_encode($ride_status).'&curr_page=booking_history');
                    $arr_tmp_data = 
                                    [
                                        'booking_master_id' => $booking_master_id,
                                        'driver_id'         => strval($driver_id),
                                        'view_url'          => $view_url
                                    ];
                    
                    $arr_response['status'] = 'success';
                    $arr_response['msg']    = 'Request successfully assign to driver, now you can track ongoing ride details.';
                    $arr_response['data']   = $arr_tmp_data;
                    return $arr_response;                  

                }
                else{
                    $arr_response['status'] = 'error';
                    $arr_response['msg']    = 'Problem occurred, while processing booking details, Please try again.';
                    $arr_response['data']   = [];
                    return $arr_response;                  
                }

            }
            else
            {
                $arr_response['status'] = 'error';
                $arr_response['msg']    = 'Problem occurred, while processing booking details, Please try again.';
                $arr_response['data']   = [];
                return $arr_response;           
            }
            
        }
        $arr_response['status'] = 'error';
        $arr_response['msg']    = 'Problem occurred, while processing booking details, Please try again.';
        $arr_response['data']   = [];
        return $arr_response;   
    }

    private function get_load_post_request_details($load_post_request_id)
    {
        $arr_load_post_request = [];
        $obj_load_post_request = $this->LoadPostRequestModel
                                                ->with('load_post_request_package_details','user_details','driver_details','vehicle_details.vehicle_type_details')
                                                ->where('id',$load_post_request_id)
                                                ->first();
        if($obj_load_post_request){
            $arr_load_post_request = $obj_load_post_request->toArray();
        }
        return $arr_load_post_request;
    }
    
    private function genrate_booking_unique_number()
    {
        $secure = TRUE;    
        $bytes = openssl_random_pseudo_bytes(6, $secure);
        $order_ref_num = "QPB-".bin2hex($bytes);

        return strtoupper($order_ref_num);   
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


            $custom_data =  [
                                "app_id"            => $OneSignalAppId ,
                                "record_id"         => isset($arr_notification_data['record_id']) ? intval($arr_notification_data['record_id']) :0,
                                "notification_type" => isset($arr_notification_data['notification_type']) ? $arr_notification_data['notification_type'] :''
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
}
