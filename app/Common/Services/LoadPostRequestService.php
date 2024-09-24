<?php
namespace App\Common\Services;

use App\Models\LoadPostRequestModel;
use App\Models\LoadPostRequestHistoryModel;
use App\Models\LoadPostRequestPackageDetailsModel;
use App\Models\BookingMasterModel;
use App\Models\BookingMasterCoordinateModel;

use App\Models\UserModel;
use App\Models\DepositMoneyModel;
use App\Models\PromoOfferAppliedDetailsModel;


use App\Common\Services\CommonDataService;
use App\Common\Services\ValidateAreaService;
use App\Common\Services\StripeService;
use App\Common\Services\NotificationsService;
use App\Common\Services\EmailService;


use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;

use Validator;
use Twilio\Rest\Client;

class LoadPostRequestService
{
         public function __construct(
                                    LoadPostRequestModel               $load_post_request,                 
                                    LoadPostRequestHistoryModel        $load_post_request_history,
                                    LoadPostRequestPackageDetailsModel $load_post_request_package_details,
                                    BookingMasterModel                 $booking_master,
                                    BookingMasterCoordinateModel       $booking_master_coordinate,
                                    UserModel                          $user,
                                    DepositMoneyModel                  $deposit_money,
                                    PromoOfferAppliedDetailsModel      $promo_offer_applied_details,
                                    CommonDataService                  $common_data_service,
                                    ValidateAreaService                $validatearea_service,
                                    StripeService                      $stripe_service,
                                    NotificationsService               $notifications_service,
                                    EmailService                       $email_service
                               )   
    {
        $this->LoadPostRequestModel               = $load_post_request;
        $this->LoadPostRequestHistoryModel        = $load_post_request_history;
        $this->LoadPostRequestPackageDetailsModel = $load_post_request_package_details;
        $this->BookingMasterModel                 = $booking_master;
        $this->BookingMasterCoordinateModel       = $booking_master_coordinate;
        $this->DepositMoneyModel                  = $deposit_money;
        $this->UserModel                          = $user;
        $this->PromoOfferAppliedDetailsModel      = $promo_offer_applied_details;
        $this->CommonDataService                  = $common_data_service;
        $this->ValidateAreaService                = $validatearea_service;
        $this->StripeService                      = $stripe_service;
        $this->NotificationsService               = $notifications_service;
        $this->EmailService                       = $email_service;
        $this->distance                           = 50;
        
        $this->per_page                           = 10;

        $this->DriverOneSignalApiKey = config('app.project.one_signal_credentials.driver_api_key');
        $this->DriverOneSignalAppId  = config('app.project.one_signal_credentials.driver_app_id');
        $this->UserOneSignalApiKey   = config('app.project.one_signal_credentials.user_api_key');
        $this->UserOneSignalAppId    = config('app.project.one_signal_credentials.user_app_id');

        $this->WebOneSignalApiKey   = config('app.project.one_signal_credentials.website_api_key');
        $this->WebOneSignalAppId    = config('app.project.one_signal_credentials.website_app_id');

        $this->load_post_img_public_path    = url('/').config('app.project.img_path.load_post_img');
        $this->load_post_img_base_path      = base_path().config('app.project.img_path.load_post_img');
        
        $this->user_profile_public_img_path = url('/').config('app.project.img_path.user_profile_images');
        $this->user_profile_base_img_path   = base_path().config('app.project.img_path.user_profile_images');

        $this->invoice_public_img_path = url('/').config('app.project.img_path.invoice');
        $this->invoice_base_img_path   = base_path().config('app.project.img_path.invoice');

        $this->trip_lat_lng_base_img_path   = base_path().config('app.project.img_path.trip_lat_lng'); 
        $this->trip_lat_lng_public_img_path = url('/').config('app.project.img_path.trip_lat_lng');

    }
    /*
    |
    | get fair estimation for front panel
    |
    */
    public function get_fair_estimate($request)
    {
        $pickup_location  = $request->input('pickup_location');
        $pickup_lat       = $request->input('pickup_lat');
        $pickup_lng       = $request->input('pickup_lng');
        $drop_location    = $request->input('drop_location');
        $drop_lat         = $request->input('drop_lat');
        $drop_lng         = $request->input('drop_lng');
        $package_type     = $request->input('package_type');
        $package_length   = $request->input('package_length');
        $package_breadth  = $request->input('package_breadth');
        $package_height   = $request->input('package_height');
        $package_weight   = $request->input('package_weight');
        $package_quantity = $request->input('package_quantity');

        $arr_validate_area_data = 
                                    [
                                            'pickup_lat' => $pickup_lat,
                                            'pickup_lng' => $pickup_lng,
                                            'drop_lat'   => $drop_lat,
                                            'drop_lng'   => $drop_lng
                                    ];
        
        $arr_valid_assigned_area = $this->ValidateAreaService->check_validate_assigned_area($arr_validate_area_data);
        
        if(isset($arr_valid_assigned_area['status']) && $arr_valid_assigned_area['status'] == 'error')
        {
            $arr_response['status'] = 'error';
            $arr_response['msg']    = isset($arr_valid_assigned_area['msg']) ? $arr_valid_assigned_area['msg'] :'';
            $arr_response['data']   = [];
            return $arr_response;
        }

        if(isset($arr_valid_assigned_area['status']) && $arr_valid_assigned_area['status'] == 'success')
        {
            $arr_valid_restricted_area = $this->ValidateAreaService->check_validate_restricted_area($arr_validate_area_data);
            
            if(isset($arr_valid_restricted_area['status']) && $arr_valid_restricted_area['status'] == 'error')
            {
                $arr_response['status'] = 'error';
                $arr_response['msg']    = isset($arr_valid_restricted_area['msg']) ? $arr_valid_restricted_area['msg'] :'';
                $arr_response['data']   = [];
                return $arr_response;
            }
        }

        $package_volume = (floatval($package_length) * floatval($package_breadth) * floatval($package_height))* intval($package_quantity);

        $package_weight = (floatval($package_weight) * intval($package_quantity));
        
        $arr_load_required_data = 
                                    [
                                        'package_type'     => $package_type,
                                        'package_quantity' => intval($package_quantity),
                                        'package_volume'   => $package_volume,
                                        'package_weight'   => $package_weight
                                    ];        
        /*check  for vehicle load capacity*/
        $is_vehicle_available = $this->CommonDataService->is_vehicle_available_against_load($arr_load_required_data);
        if($is_vehicle_available)
        {
            $arr_response['status'] = 'error';
            $arr_response['msg']    = 'Heavy shipment, no vehicle available.';
            $arr_response['data']   = [];
            return $arr_response;
        }
        /*check  for vehicle load capacity*/
        
        $arr_vehicle_type_details = $this->CommonDataService->get_vehicle_type_available_against_load($arr_load_required_data);
        
        if(isset($arr_vehicle_type_details) && sizeof($arr_vehicle_type_details)>0)
        {
            $origins      = $pickup_lat.",".$pickup_lng;
            $destinations = $drop_lat.",".$drop_lng;


            $arr_calculate_distance = $this->calculate_distance($origins,$destinations);

            $arr_vehicle_type_details['trip_distance'] = isset($arr_calculate_distance['distance']) ? $arr_calculate_distance['distance'] :'';
            $arr_vehicle_type_details['trip_duration'] = isset($arr_calculate_distance['duration']) ? $arr_calculate_distance['duration'] :'';

            $actual_distance = isset($arr_calculate_distance['actual_distance']) ? $arr_calculate_distance['actual_distance'] :0;
            $per_miles_price = isset($arr_vehicle_type_details['per_miles_price']) ? floatval($arr_vehicle_type_details['per_miles_price']) : 0;

            /*convert meter to miles*/
            if($actual_distance>0){
                $actual_distance = ($actual_distance/1609.344);
                $actual_distance = round($actual_distance,2);
            }   
            
            $calculated_farecharge = 0;

            $calculated_farecharge = ($actual_distance * $per_miles_price); 

            $arr_vehicle_type_details['calculated_farecharge'] = doubleval($calculated_farecharge);

            $arr_response['status'] = 'success';
            $arr_response['msg']    = 'vehicle details available successfully.';
            $arr_response['data']   = $arr_vehicle_type_details ;
            return $arr_response;
        }
        $arr_response['status'] = 'error';
        $arr_response['msg']    = 'No vehicles available for posted load.';
        $arr_response['data']   = $arr_vehicle_type_details;
        return $arr_response;
    }

    private function search_available_vehicle_with_driver($arr_data = [])
    {   
        if(isset($arr_data) && sizeof($arr_data)>0){

            $pickup_lat           = isset($arr_data['pickup_lat']) ? $arr_data['pickup_lat'] :0;
            $pickup_lng           = isset($arr_data['pickup_lng']) ? $arr_data['pickup_lng'] :0;
            $package_volume       = isset($arr_data['package_volume']) ? $arr_data['package_volume'] :0;
            $package_weight       = isset($arr_data['package_weight']) ? $arr_data['package_weight'] :0;
            $distance             = isset($arr_data['distance']) ? $arr_data['distance'] :0;
            
            $sql_query = '';
            $sql_query .= "Select ";
            $sql_query .= "users.id AS driver_id, ";
            $sql_query .= "CONCAT( users.first_name, ' ',users.last_name ) AS driver_name, ";
            $sql_query .= "CONCAT( users.country_code, '',users.mobile_no ) AS mobile_no, ";
            $sql_query .= "users.email AS email, ";
            $sql_query .= "VT.id AS vehicle_type_original_id, ";
            $sql_query .= "VT.vehicle_type AS vehicle_type_name, ";
            $sql_query .= "VT.starting_price, ";
            $sql_query .= "VT.per_miles_price, ";
            $sql_query .= "VT.per_minute_price, ";
            $sql_query .= "VT.minimum_price, ";
            $sql_query .= "VT.vehicle_min_volume AS vehicle_min_volume, ";
            $sql_query .= "VT.vehicle_max_volume AS vehicle_max_volume, ";
            $sql_query .= "V.id AS vehicle_id, ";
            $sql_query .= "V.vehicle_type_id, ";
            // $sql_query .= "V.vehicle_name AS vehicle_name, ";
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
            
            if(isset($obj_driver_details[0]) && sizeof($obj_driver_details[0])>0){
                /*
                |manually fliter data also remove driver which does not verify their indivital vechiles 
                |also remove drivers which are in restricted area.
                */
                /*foreach ($obj_driver_details as $key => $driver_details) 
                {
                    if($driver_details->is_individual_vehicle == '1')
                    {
                        // if($driver_details->fair_charge<=0 || $driver_details->is_verified == '0')
                        if($driver_details->is_verified == '0')
                        {
                            unset($obj_driver_details[$key]);
                        }

                    }
                }*/
                return json_decode(json_encode($obj_driver_details[0]), true);
            }
        }
        return [];
    }

    /*
    |
    | when user post new load then save load details in database and search nearby drivers with matching criteria
    |
    */
    public function store_load_post_request($request)
    {
        /*this api is used for multiple times against action type*/
        $arr_rules = $arr_response = [];
        $user_id     = validate_user_jwt_token();

        if ($user_id == 0) 
        {
            $arr_response['status'] = 'error';
            $arr_response['msg']    = 'Invalid token';
            $arr_response['data']    = [];
            return $arr_response;
        }
        
        $arr_rules['action_type']           = "required";
        // $arr_rules['request_type']          = "required";
        $arr_rules['pickup_location']       = "required";
        $arr_rules['pickup_lat']            = "required";
        $arr_rules['pickup_lng']            = "required";
        $arr_rules['drop_location']         = "required";
        $arr_rules['drop_lat']              = "required";
        $arr_rules['drop_lng']              = "required";
        // Package details
        $arr_rules['package_type']      = "required";

        if($request->input('package_type') != 'PALLET')
        {
            $arr_rules['package_length']    = "required";
            $arr_rules['package_breadth']   = "required";
            $arr_rules['package_height']    = "required";
            // $arr_rules['package_volume']    = "required";
            $arr_rules['package_weight']    = "required";
        }
        $arr_rules['package_quantity']  = "required";
        $arr_rules['is_bonus']          = "required";

        // Payment details
        $arr_rules['card_id']           = "required";

        if($request->input('action_type') == 'book'){
            $arr_rules['vehicle_type_id']     = "required";
            $arr_rules['is_future_request']   = "required";
        }

        $validator = Validator::make($request->all(),$arr_rules); 
        if($validator->fails())
        {
            $arr_response['status'] = 'error';
            $arr_response['msg']    = 'Please fill all the required field.';
            $arr_response['data']    = [];
            return $arr_response;
        }

        $action_type           = $request->input('action_type');
        $request_type          = $request->input('request_type');
        $pickup_location       = $request->input('pickup_location');
        $pickup_lat            = $request->input('pickup_lat');
        $pickup_lng            = $request->input('pickup_lng');
        $drop_location         = $request->input('drop_location');
        $drop_lat              = $request->input('drop_lat');
        $drop_lng              = $request->input('drop_lng');
        $promo_code_id         = $request->input('promo_code_id');

        // Package details
        $package_type       = $request->input('package_type');
        $package_length     = $request->input('package_length');
        $package_breadth    = $request->input('package_breadth');
        $package_height     = $request->input('package_height');
        $package_weight     = $request->input('package_weight');
        $package_quantity   = $request->input('package_quantity');
        $is_admin_assistant = $request->input('is_admin_assistant');

        /*strip get card id code not required at this moment*/
        $card_id = $request->input('card_id');

        $is_bonus = $request->input('is_bonus');

        $package_volume = (floatval($package_length) * floatval($package_breadth) * floatval($package_height))* intval($package_quantity);
        $package_weight = (floatval($package_weight) * intval($package_quantity));

        /*if user want to search drivers against vehicle then this api will be callled*/
        if($action_type == 'search')
        {
            /*check  for restricted area*/
            $arr_validate_area_data = 
                                        [
                                                'pickup_lat' => $pickup_lat,
                                                'pickup_lng' => $pickup_lng,
                                                'drop_lat'   => $drop_lat,
                                                'drop_lng'   => $drop_lng
                                        ];
            
            $arr_valid_assigned_area = $this->ValidateAreaService->check_validate_assigned_area($arr_validate_area_data);

            if(isset($arr_valid_assigned_area['status']) && $arr_valid_assigned_area['status'] == 'error')
            {
                $arr_response['status'] = 'error';
                $arr_response['msg']    = isset($arr_valid_assigned_area['msg']) ? $arr_valid_assigned_area['msg'] :'';
                $arr_response['data']   = [];
                return $arr_response;
            }

            if(isset($arr_valid_assigned_area['status']) && $arr_valid_assigned_area['status'] == 'success')
            {
                $arr_valid_restricted_area = $this->ValidateAreaService->check_validate_restricted_area($arr_validate_area_data);
                
                if(isset($arr_valid_restricted_area['status']) && $arr_valid_restricted_area['status'] == 'error')
                {
                    $arr_response['status'] = 'error';
                    $arr_response['msg']    = isset($arr_valid_restricted_area['msg']) ? $arr_valid_restricted_area['msg'] :'';
                    $arr_response['data']   = [];
                    return $arr_response;
                }
            }

            /*check  for vehicle load capacity*/
            $arr_load_required_data = 
                                        [
                                            'package_type'     => $package_type,
                                            'package_quantity' => intval($package_quantity),
                                            'package_volume'   => $package_volume,
                                            'package_weight'   => $package_weight
                                        ];        
            $is_vehicle_available = $this->CommonDataService->is_vehicle_available_against_load($arr_load_required_data);
            
            if($is_vehicle_available)
            {
                $arr_response['status'] = 'error';
                $arr_response['msg']    = 'Heavy shipment, no vehicle/driver available.';
                $arr_response['data']   = [];
                return $arr_response;
            }

            /*check  for validate card*/

            $arr_validate_card = [];
            $arr_validate_card['user_id']  = $user_id;
            $arr_validate_card['card_id']  = $card_id;

            $arr_validate = $this->StripeService->validate_card($arr_validate_card);
            
            if(isset($arr_validate['status']) && $arr_validate['status'] == 'error')
            {
                $arr_response['status'] = 'error';
                $arr_response['msg']    = isset($arr_validate['msg']) ? $arr_validate['msg'] :'';
                $arr_response['data']   = [];
                return $arr_response;
            }

            $str_except_driver_id = $this->get_user_driver_id($user_id);
            $arr_driver_search_data = 
                                    [
                                        'pickup_lat'           => $pickup_lat,
                                        'pickup_lng'           => $pickup_lng,
                                        'drop_lat'             => $drop_lat,
                                        'drop_lng'             => $drop_lng,
                                        'package_type'         => $package_type,
                                        'package_quantity'     => $package_quantity,
                                        'package_volume'       => $package_volume,
                                        'package_weight'       => $package_weight,
                                        'distance'             => $this->distance,
                                        'load_post_request_id' => 0,
                                        'str_except_driver_id' => strval($str_except_driver_id)
                                    ];

            $arr_driver_vehicle_type = $this->search_nearby_drivers($arr_driver_search_data);

            if(isset($arr_driver_vehicle_type) && count($arr_driver_vehicle_type)>0)
            {
                $arr_driver_vehicle_type = array_values($arr_driver_vehicle_type);
                
                $arr_tmp_data                            = [];
                $arr_tmp_data['load_post_request_id']    = 0;
                $arr_tmp_data['arr_driver_vehicle_type'] = $arr_driver_vehicle_type;

                $arr_response['status'] = 'success';
                $arr_response['msg']    = 'For update information regarding your delivery, click the menu button located at the top left corner of your screen and select “My Bookings”';
                $arr_response['data']   = $arr_tmp_data;
                return $arr_response;
            }
            else
            {
                $arr_response['status'] = 'error';
                $arr_response['msg']    = 'Currently no drivers available for your shipment request.';
                $arr_response['data']   = [];
                return $arr_response;
            }
        }
        else if($action_type == 'book')
        {
            $future_request_date  = '';
            $is_future_request    = $request->input('is_future_request');
            $request_time         = $request->input('request_time');
            $vehicle_type_id      = $request->input('vehicle_type_id');

            /*if booking request is not a future booking then not need to check drivers avalible currently*/
            if($is_future_request == '0')
            {
                $str_except_driver_id = $this->get_user_driver_id($user_id);
                $arr_driver_search_data = 
                                        [
                                            'pickup_lat'           => $pickup_lat,
                                            'pickup_lng'           => $pickup_lng,
                                            'vehicle_type_id'      => $vehicle_type_id,
                                            'distance'             => $this->distance,
                                            'load_post_request_id' => 0,
                                            'str_except_driver_id' => strval($str_except_driver_id)
                                        ];

                $arr_available_drivers = $this->search_and_send_notification_to_nearby_drivers($arr_driver_search_data);

                if(count($arr_available_drivers)<=0)
                {
                    $arr_response['status'] = 'error';
                    $arr_response['msg']    = 'Sorry for the inconvenience,currently drivers are not available.';
                    $arr_response['data']   = [];
                    return $arr_response;
                }
            }

            $db_is_bonus = 'NO';
            if(isset($is_bonus) && $is_bonus == 'YES'){
                $is_bonus_applicable = $this->CommonDataService->check_bonus_applicable($user_id);
                if(isset($is_bonus_applicable) && $is_bonus_applicable == 'YES')
                {
                    $db_is_bonus = 'YES';
                }
            }

            if($request->has('future_request_date') && $request->input('future_request_date')!=''){
                $future_request_date = date('Y-m-d',strtotime($request->input('future_request_date')));
                $future_request_date = $future_request_date.' '.date('H:i:s');
            }

            
            $arr_load_post_request = $arr_load_post_request_history = $arr_load_post_request_package_details = [];

            $arr_load_post_request['load_post_request_unique_id'] = $this->genrate_load_post_request_unique_number();
            $arr_load_post_request['card_id']                     = $card_id;
            $arr_load_post_request['user_id']                     = $user_id;
            $arr_load_post_request['request_type']                = $request_type;
            $arr_load_post_request['pickup_location']             = $pickup_location;
            $arr_load_post_request['pickup_lat']                  = $pickup_lat;
            $arr_load_post_request['pickup_lng']                  = $pickup_lng;
            $arr_load_post_request['drop_location']               = $drop_location;
            $arr_load_post_request['drop_lat']                    = $drop_lat;
            $arr_load_post_request['drop_lng']                    = $drop_lng;
            $arr_load_post_request['promo_code_id']               = $promo_code_id;
            $arr_load_post_request['is_bonus']                    = $db_is_bonus;
            $arr_load_post_request['request_status']              = 'USER_REQUEST';
            $arr_load_post_request['is_admin_assistant']          = $is_admin_assistant;
            $arr_load_post_request['is_future_request']           = $is_future_request;
            
            if($is_future_request == '1' && $future_request_date!='' && $request_time!='')
            {
                $arr_load_post_request['date']         = $future_request_date;
                $arr_load_post_request['request_time'] = $request_time;
            }
            else
            {
                $arr_load_post_request['date']         = date('Y-m-d h:i:s');
            }

            $arr_load_post_request['is_request_process']          = '0';

            $obj_load_post_request = $this->LoadPostRequestModel->create($arr_load_post_request);
            if($obj_load_post_request){
                
                /*if user applied promo code then maintain history*/
                if($promo_code_id != 0){
                    $this->PromoOfferAppliedDetailsModel->create([  
                                                        'promo_code_id'      => $promo_code_id,
                                                        'user_id'            => $user_id,
                                                        'promo_applied_date' => date('Y-m-d')
                                                    ]);
                }

                $load_post_request_id = isset($obj_load_post_request->id) ? $obj_load_post_request->id :0;

                $arr_load_post_request_history['load_post_request_id'] = $load_post_request_id;
                $arr_load_post_request_history['user_id']              = $user_id;
                $arr_load_post_request_history['driver_id']            = 0;
                $arr_load_post_request_history['status']               = 'USER_REQUEST';
                $arr_load_post_request_history['reason']               = '';

                $this->LoadPostRequestHistoryModel->create($arr_load_post_request_history);

                $arr_load_post_request_package_details['load_post_request_id']     = $load_post_request_id;
                $arr_load_post_request_package_details['selected_vehicle_type_id'] = $vehicle_type_id;
                $arr_load_post_request_package_details['package_type']             = $package_type;
                $arr_load_post_request_package_details['package_length']           = $package_length;
                $arr_load_post_request_package_details['package_breadth']          = $package_breadth;
                $arr_load_post_request_package_details['package_height']           = $package_height;
                $arr_load_post_request_package_details['package_volume']           = $package_volume;
                $arr_load_post_request_package_details['package_weight']           = $package_weight;
                $arr_load_post_request_package_details['package_quantity']         = $package_quantity;

                $this->LoadPostRequestPackageDetailsModel->create($arr_load_post_request_package_details);
                
                $msg = '';
                if($is_future_request == '0')
                {
                    //$msg = 'Shipment request successfully send to all nearby drivers,waiting for driver acceptance.';
                    $this->send_notification_to_drivers($arr_available_drivers,$load_post_request_id);
                }
                else if($is_future_request == '1')
                {
                    //$msg = 'Future Shipment request successfully processed, you will be notify soon.';
                }
                
                $arr_tmp_data                            = [];
                $arr_tmp_data['load_post_request_id']    = $load_post_request_id;
                // $arr_tmp_data['arr_fare_estimate']       = [];
                $arr_tmp_data['arr_driver_vehicle_type'] = [];

                $arr_response['status'] = 'success';
                $arr_response['msg']    = 'For update information regarding your delivery, click the menu button located at the top left corner of your screen and select “My Bookings”';
                $arr_response['data']   = $arr_tmp_data;
                return $arr_response;
            }
            else{
                
                $arr_response['status'] = 'error';
                $arr_response['msg']    = 'Problem occurred while storing shipment post details.';
                $arr_response['data']    = [];
                return $arr_response;
            }
           
        }
        
        $arr_response['status'] = 'error';
        $arr_response['msg']    = 'Something went wrong, Please try again.';
        $arr_response['data']    = [];
        return $arr_response;
    } 
    public function get_user_driver_id($user_id)
    {
        $obj_data = $this->UserModel
                                ->select('mobile_no')
                                ->where('id',$user_id)
                                ->first();
        $mobile_no = isset($obj_data->mobile_no) ? $obj_data->mobile_no : '';

        $obj_user_driver = $this->UserModel
                                ->select('id')
                                ->where('mobile_no',$mobile_no)
                                ->where('user_type','DRIVER')
                                ->first();
        
        if(isset($obj_user_driver->id))
        {
            return $obj_user_driver->id;
        }
        return 0;

    }
    public function get_driver_details($driver_id)
    {
        $arr_data = [];

        $obj_data = $this->UserModel
                                ->select('id','first_name','last_name','country_code','mobile_no')
                                ->with('driver_status_details')
                                ->where('id',$driver_id)
                                ->first();

        if($obj_data)
        {
            $arr_data = $obj_data->toArray();
        }
        
        if(isset($arr_data) && count($arr_data)>0)
        {
            $arr_data['lat'] = isset($arr_data['driver_status_details']['current_latitude'])  ?$arr_data['driver_status_details']['current_latitude'] : '';
            $arr_data['lng'] = isset($arr_data['driver_status_details']['current_longitude'])  ?$arr_data['driver_status_details']['current_longitude'] : '';
            unset($arr_data['driver_status_details']);
        }
        return $arr_data;
    }
    /*
    |
    | Common method for user and driver of load related.
    |
    */
    public function process_load_post_request($request,$client = null)
    {
        $login_user_id     = validate_user_jwt_token();
        if ($login_user_id == 0) 
        {
            $arr_response['status'] = 'error';
            $arr_response['msg']    = 'Invalid user token';
            $arr_response['data']    = [];
            return $arr_response;
        }
        
        $load_post_request_id = $request->input('load_post_request_id');
        $user_type            = $request->input('user_type');
        $request_status       = $request->input('request_status');
        $vehicle_type_id      = $request->input('vehicle_type_id');
        $is_admin_assign      = $request->input('is_admin_assign');
        
        $is_admin_assign = '0';
        if($request->has('is_admin_assign') && $request->input('is_admin_assign')!=''){
            $is_admin_assign = $request->input('is_admin_assign');
        }   

        if ($load_post_request_id == '' || $user_type == '' || $request_status == '') 
        {
            $arr_response['status'] = 'error';
            $arr_response['msg']    = 'Problem occurred, while processing shipment post request';
            $arr_response['data']    = [];
            return $arr_response;
        }
        
        if($request_status == 'USER_REQUEST' && $vehicle_type_id == '')
        {
            $arr_response['status'] = 'error';
            $arr_response['msg']    = 'Selected Vehicle identifier is missing,unable to process request,Please try again.';
            $arr_response['data']    = [];
            return $arr_response;
        }

        $arr_response = [];
        $arr_driver_status = ['ACCEPT_BY_DRIVER','REJECT_BY_DRIVER','TIMEOUT'];
        if($user_type == 'DRIVER' && in_array($request_status, $arr_driver_status) ){
            
            $arr_response = $this->process_request_by_driver($login_user_id,$load_post_request_id,$request_status,$is_admin_assign,$client);
            return $arr_response;
        }
        else if($user_type == 'USER'){
            if($request_status == 'USER_REQUEST'){
                
                $future_request_date  = '';
                $request_time         = $request->input('request_time');

                if($request->has('future_request_date') && $request->input('future_request_date')!=''){
                    $future_request_date = date('Y-m-d',strtotime($request->input('future_request_date')));
                    $future_request_date = $future_request_date.' '.date('H:i:s');
                }

                $arr_required_data = [  
                                        'login_user_id'        => $login_user_id,
                                        'vehicle_type_id'      => $vehicle_type_id,
                                        'load_post_request_id' => $load_post_request_id,
                                        'request_status'       => $request_status,
                                        'is_future_request'    => $request->input('is_future_request'),
                                        'future_request_date'  => $future_request_date,
                                        'request_time'         => $request_time,

                                     ];

                $arr_response = $this->process_to_book_driver_request_by_user($arr_required_data);
                return $arr_response;
            }
            else if($request_status == 'REJECT_BY_USER'){
                $arr_response = $this->process_reject_request_by_user($login_user_id,$load_post_request_id,$request_status);
                return $arr_response;
            }else if($request_status == 'ACCEPT_BY_USER') {   

                /*store booking master details in database and prooced further*/    
                $po_no         = $request->input('po_no');
                $receiver_name = $request->input('receiver_name');
                $receiver_no   = $request->input('receiver_no');
                $app_suite     = $request->input('app_suite');


                $arr_booking_data = 
                                    [
                                        'po_no'         => $po_no,
                                        'receiver_name' => $receiver_name,
                                        'receiver_no'   => $receiver_no,
                                        'app_suite'     => $app_suite
                                    ];

                $arr_response = $this->process_accept_request_by_user($login_user_id,$load_post_request_id,$request_status,$arr_booking_data);
                return $arr_response;
            }
            $arr_response['status'] = 'error';
            $arr_response['msg']    = 'Problem occurred, while processing shipment post request,Please try again.';
            $arr_response['data']    = [];
            return $arr_response;
        }
        $arr_response['status'] = 'error';
        $arr_response['msg']    = 'Problem occurred, while processing shipment post request';
        $arr_response['data']    = [];
        return $arr_response;
    }

    /*
    |
    | load post details for user as well as driver
    |
    */
    public function load_post_details($request)
    {
        $user_id     = validate_user_jwt_token();
        if ($user_id == 0) 
        {
            $arr_response['status'] = 'error';
            $arr_response['msg']    = 'Invalid token';
            $arr_response['data']    = [];
            return $arr_response;
        }
        
        $load_post_request_id = $request->input('load_post_request_id');
        $user_type            = $request->input('user_type');
        if($load_post_request_id == '' || $user_type == '')
        {
            $arr_response['status'] = 'error';
            $arr_response['msg']    = 'Invalid shipment post token,cannot process request.';
            $arr_response['data']    = [];
            return $arr_response;
        }
        /*need to check*/
        /*if($user_type == 'DRIVER')
        {
            $driver_status = 'BUSY';
            $this->CommonDataService->change_driver_status($user_id,$driver_status);
        }*/

        $obj_load_post_request = $this->LoadPostRequestModel
                                                ->with(['load_post_request_package_details','user_details','driver_details','driver_current_location_details','vehicle_details.vehicle_type_details'])
                                                ->where('id',$load_post_request_id)
                                                ->first();

        if(isset($obj_load_post_request) && $obj_load_post_request!=null)
        {
            $arr_load_post_request = $obj_load_post_request->toArray();
            

            if(isset($arr_load_post_request) && sizeof($arr_load_post_request)>0){

                $arr_result                         = [];
                $arr_result['first_name']           = '';
                $arr_result['last_name']            = '';
                $arr_result['email']                = '';
                $arr_result['mobile_no']            = '';
                $arr_result['profile_image']        = '';
                // $arr_result['fair_charge']       = $per_miles_price;
                $arr_result['load_post_request_id'] = isset($arr_load_post_request['id']) ? $arr_load_post_request['id'] : 0;
                $arr_result['user_id']              = isset($arr_load_post_request['user_id']) ? $arr_load_post_request['user_id'] : 0;
                $arr_result['driver_id']            = isset($arr_load_post_request['driver_id']) ? $arr_load_post_request['driver_id'] : 0;
                $arr_result['pickup_location']      = isset($arr_load_post_request['pickup_location']) ? $arr_load_post_request['pickup_location'] : '';
                $arr_result['pickup_lat']           = isset($arr_load_post_request['pickup_lat']) ? doubleval($arr_load_post_request['pickup_lat']) : floatval(0);
                $arr_result['pickup_lng']           = isset($arr_load_post_request['pickup_lng']) ? doubleval($arr_load_post_request['pickup_lng']) : floatval(0);
                $arr_result['drop_lat']             = isset($arr_load_post_request['drop_lat']) ? doubleval($arr_load_post_request['drop_lat']) : floatval(0);
                $arr_result['drop_lng']             = isset($arr_load_post_request['drop_lng']) ? doubleval($arr_load_post_request['drop_lng']) : floatval(0);
                $arr_result['drop_location']        = isset($arr_load_post_request['drop_location']) ? $arr_load_post_request['drop_location'] : '';
                $arr_result['is_future_request']    = isset($arr_load_post_request['is_future_request']) ? $arr_load_post_request['is_future_request'] :'0';
                $arr_result['is_request_process']   = isset($arr_load_post_request['is_request_process']) ? $arr_load_post_request['is_request_process'] :'0';
                $arr_result['request_time']         = isset($arr_load_post_request['request_time']) ? $arr_load_post_request['request_time'] :'';
                $arr_result['package_type']         = isset($arr_load_post_request['load_post_request_package_details']['package_type']) ? $arr_load_post_request['load_post_request_package_details']['package_type'] : '';
                $arr_result['package_length']       = isset($arr_load_post_request['load_post_request_package_details']['package_length']) ? $arr_load_post_request['load_post_request_package_details']['package_length'] : 0;
                $arr_result['package_breadth']      = isset($arr_load_post_request['load_post_request_package_details']['package_breadth']) ? $arr_load_post_request['load_post_request_package_details']['package_breadth'] : 0;
                $arr_result['package_height']       = isset($arr_load_post_request['load_post_request_package_details']['package_height']) ? $arr_load_post_request['load_post_request_package_details']['package_height'] : 0;
                $arr_result['package_weight']       = isset($arr_load_post_request['load_post_request_package_details']['package_weight']) ? $arr_load_post_request['load_post_request_package_details']['package_weight'] : 0;
                $arr_result['package_quantity']     = isset($arr_load_post_request['load_post_request_package_details']['package_quantity']) ? $arr_load_post_request['load_post_request_package_details']['package_quantity'] : 0;

                $driver_id                 = isset($arr_load_post_request['driver_id']) ? $arr_load_post_request['driver_id'] : 0;

                $arr_vehicle_type_details        = $this->CommonDataService->get_driver_vehicle_type_details($driver_id);
                
                $per_miles_price = isset($arr_vehicle_type_details['vehicle_details']['vehicle_type_details']['per_miles_price']) ? $arr_vehicle_type_details['vehicle_details']['vehicle_type_details']['per_miles_price'] :0;
                $arr_result['fair_charge']          = $per_miles_price;

                // $driver_id                 = isset($arr_load_post_request['driver_id']) ? $arr_load_post_request['driver_id'] : 0;
                // $driver_fare_charge        = $this->CommonDataService->get_driver_fair_charge($driver_id);
                // $arr_result['fair_charge'] = $driver_fare_charge;
                
                if($user_type == 'DRIVER')
                {
                    $arr_result['first_name']    = isset($arr_load_post_request['user_details']['first_name']) ? $arr_load_post_request['user_details']['first_name'] : '';
                    $arr_result['last_name']     = isset($arr_load_post_request['user_details']['last_name']) ? $arr_load_post_request['user_details']['last_name'] : '';
                    $arr_result['email']         = isset($arr_load_post_request['user_details']['email']) ? $arr_load_post_request['user_details']['email'] : '';
                    
                    $country_code   = isset($arr_load_post_request['user_details']['country_code']) ? $arr_load_post_request['user_details']['country_code'] : '';
                    $mobile_no      = isset($arr_load_post_request['user_details']['mobile_no']) ? $arr_load_post_request['user_details']['mobile_no'] : '';
                    $full_mobile_no = $country_code.''.$mobile_no;
                    $full_mobile_no = ($full_mobile_no!='')?$full_mobile_no :'';

                    $arr_result['mobile_no']     = $full_mobile_no;
                    
                    $profile_image = isset($arr_load_post_request['user_details']['profile_image']) ? $arr_load_post_request['user_details']['profile_image'] : '';

                    if($profile_image!='' && file_exists($this->user_profile_base_img_path.$profile_image))
                    {
                       $profile_image = $this->user_profile_public_img_path.$profile_image;
                    }
                    $arr_result['profile_image'] = $profile_image;
                }
                if($user_type == 'USER')
                {
                    $arr_result['first_name']    = isset($arr_load_post_request['driver_details']['first_name']) ? $arr_load_post_request['driver_details']['first_name'] : '';
                    $arr_result['last_name']     = isset($arr_load_post_request['driver_details']['last_name']) ? $arr_load_post_request['driver_details']['last_name'] : '';
                    $arr_result['email']         = isset($arr_load_post_request['driver_details']['email']) ? $arr_load_post_request['driver_details']['email'] : '';
                    
                    $country_code   = isset($arr_load_post_request['driver_details']['country_code']) ? $arr_load_post_request['driver_details']['country_code'] : '';
                    $mobile_no      = isset($arr_load_post_request['driver_details']['mobile_no']) ? $arr_load_post_request['driver_details']['mobile_no'] : '';
                    $full_mobile_no = $country_code.''.$mobile_no;
                    $full_mobile_no = ($full_mobile_no!='')?$full_mobile_no :'';

                    $arr_result['mobile_no']     = $full_mobile_no;

                    $profile_image = isset($arr_load_post_request['driver_details']['profile_image']) ? $arr_load_post_request['driver_details']['profile_image'] : '';

                    if($profile_image!='' && file_exists($this->user_profile_base_img_path.$profile_image))
                    {
                       $profile_image = $this->user_profile_public_img_path.$profile_image;
                    }
                    $arr_result['profile_image'] = $profile_image;

                    $driver_latitude  = isset($arr_load_post_request['driver_current_location_details']['current_latitude']) ? $arr_load_post_request['driver_current_location_details']['current_latitude'] : '';
                    $driver_longitude = isset($arr_load_post_request['driver_current_location_details']['current_longitude']) ? $arr_load_post_request['driver_current_location_details']['current_longitude'] : '';

                    $arr_result['driver_location'] = $this->get_address_from_google_maps($driver_latitude,$driver_longitude);
                    
                    $pickup_lat = isset($arr_load_post_request['pickup_lat']) ? doubleval($arr_load_post_request['pickup_lat']) : floatval(0);
                    $pickup_lng = isset($arr_load_post_request['pickup_lng']) ? doubleval($arr_load_post_request['pickup_lng']) : floatval(0);
                    $drop_lat   = isset($arr_load_post_request['drop_lat']) ? doubleval($arr_load_post_request['drop_lat']) : floatval(0);
                    $drop_lng   = isset($arr_load_post_request['drop_lng']) ? doubleval($arr_load_post_request['drop_lng']) : floatval(0);
                    
                    $origins      = $pickup_lat.",".$pickup_lng;
                    $destinations = $drop_lat.",".$drop_lng;

                    $arr_calculate_distance = $this->calculate_distance($origins,$destinations);
                    
                    $arr_result['trip_distance'] = isset($arr_calculate_distance['distance']) ? $arr_calculate_distance['distance'] :'';
                    $arr_result['trip_duration'] = isset($arr_calculate_distance['duration']) ? $arr_calculate_distance['duration'] :'';

                    $actual_distance = isset($arr_calculate_distance['actual_distance']) ? $arr_calculate_distance['actual_distance'] :0;
                    
                    /*convert meter to miles*/
                    if($actual_distance>0){
                        $actual_distance = ($actual_distance/1609.344);
                        $actual_distance = round($actual_distance,2);
                    }   
                    
                    $calculated_farecharge = 0;

                    $calculated_farecharge = ($actual_distance * $per_miles_price); 

                    $arr_result['calculated_farecharge'] = doubleval($calculated_farecharge);

                    $driver_origins      = $driver_latitude.",".$driver_longitude;
                    $driver_destinations = $pickup_lat.",".$pickup_lng;
                    
                    $arr_driver_distance = $this->calculate_distance($driver_origins,$driver_destinations);
                    
                    $arr_result['driver_distance'] = isset($arr_driver_distance['distance']) ? $arr_driver_distance['distance'] :'';
                    $arr_result['driver_duration'] = isset($arr_driver_distance['duration']) ? $arr_driver_distance['duration'] :'';
                }
                
                $arr_response['status'] = 'success';
                $arr_response['msg']    = 'Shipment post details found successfully.';
                $arr_response['data']   = $arr_result;
                return $arr_response;   

                
            }
            else
            {
                $arr_response['status'] = 'error';
                $arr_response['msg']    = 'Problem occurred, fetching shipment post details,Please try again.';
                $arr_response['data']   = [];
                return $arr_response;   
            }
        }
        $arr_response['status'] = 'error';
        $arr_response['msg']    = 'Problem occurred, fetching shipment post details,Please try again.';
        $arr_response['data']   = [];
        return $arr_response;
    }
    /*
    |
    | driver will see latetst pending load post requests.
    |
    */
    
    public function pending_load_post($request)
    {
        $driver_id     = validate_user_jwt_token();
        // dd($driver_id);

        if ($driver_id == 0) 
        {
            $arr_response['status'] = 'error';
            $arr_response['msg']    = 'Invalid driver token';
            $arr_response['data']    = [];
            return $arr_response;
        }
        
        $date = new \DateTime();
        
        $date->modify('-24 hours');
        $formatted_date = $date->format('Y-m-d H:i:s');
        
        $arr_vehicle_type_details = $this->CommonDataService->get_driver_vehicle_type_details($driver_id);
        
        $driver_mobile_no   = isset($arr_vehicle_type_details['driver_details']['mobile_no']) ? $arr_vehicle_type_details['driver_details']['mobile_no'] :'';

        $vehicle_type_id    = isset($arr_vehicle_type_details['vehicle_details']['vehicle_type_details']['id']) ? $arr_vehicle_type_details['vehicle_details']['vehicle_type_details']['id'] :0;

        // $vehicle_min_weight = isset($arr_vehicle_type_details['vehicle_details']['vehicle_type_details']['vehicle_min_weight']) ? $arr_vehicle_type_details['vehicle_details']['vehicle_type_details']['vehicle_min_weight'] :0;
        // $vehicle_max_weight = isset($arr_vehicle_type_details['vehicle_details']['vehicle_type_details']['vehicle_max_weight']) ? $arr_vehicle_type_details['vehicle_details']['vehicle_type_details']['vehicle_max_weight'] :0;
        // $vehicle_min_volume = isset($arr_vehicle_type_details['vehicle_details']['vehicle_type_details']['vehicle_min_volume']) ? $arr_vehicle_type_details['vehicle_details']['vehicle_type_details']['vehicle_min_volume'] :0;
        // $vehicle_max_volume = isset($arr_vehicle_type_details['vehicle_details']['vehicle_type_details']['vehicle_max_volume']) ? $arr_vehicle_type_details['vehicle_details']['vehicle_type_details']['vehicle_max_volume'] :0;

        $arr_pending_load_post = [];
        // dd($formatted_date);
        $arr_driver_current_lat_lng =  $this->CommonDataService->get_driver_current_lat_lng_details($driver_id);

        $driver_current_lat  = isset($arr_driver_current_lat_lng['current_latitude']) ? $arr_driver_current_lat_lng['current_latitude'] : 0;
        $driver_current_lng = isset($arr_driver_current_lat_lng['current_longitude']) ? $arr_driver_current_lat_lng['current_longitude'] : 0;

        $obj_pending_load_post  = $this->LoadPostRequestModel
                                                // ->select('id','user_id','driver_id','created_at','request_status','pickup_location','pickup_lat','pickup_lng','drop_location')
                                                ->select(\DB::raw('ROUND((
                                                                          6371 * ACOS(
                                                                            COS(RADIANS('.$driver_current_lat.')) * COS(RADIANS(pickup_lat)) * COS(
                                                                              RADIANS(pickup_lng) - RADIANS('.$driver_current_lng.')
                                                                            ) + SIN(RADIANS('.$driver_current_lat.')) * SIN(RADIANS(pickup_lat))
                                                                          )
                                                                        ),2) AS driver_distance
                                                                      '),'id','user_id','driver_id','created_at','request_status','pickup_location','pickup_lat','pickup_lng','drop_location','is_future_request','is_request_process')
                                                // $qry->having('driver_distance','<=',10);
                                                ->whereHas('load_post_request_history_details',function($query){
                                                        $query->whereIn('status',['USER_REQUEST','TIMEOUT','ACCEPT_BY_DRIVER','REJECT_BY_DRIVER']);
                                                        // $query->where('driver_id',$driver_id);
                                                })
                                                ->whereHas('load_post_request_package_details',function($query) use($vehicle_type_id) {
                                                        $query->where('selected_vehicle_type_id', $vehicle_type_id);
                                                        // use($vehicle_min_weight,$vehicle_max_weight,$vehicle_min_volume,$vehicle_max_volume)

                                                        // $query->where('package_volume', '>=', $vehicle_min_volume);
                                                        // $query->where('package_volume', '<=', $vehicle_max_volume);
                                                        // $query->where('package_weight', '>=', $vehicle_min_weight);
                                                        // $query->where('package_weight', '<=', $vehicle_max_weight);
                                                })
                                                ->with('load_post_request_package_details')
                                                ->with(['load_post_request_history_details'=>function($query){
                                                        $query->whereIn('status',['USER_REQUEST','TIMEOUT','ACCEPT_BY_DRIVER','REJECT_BY_DRIVER']);
                                                        // $query->where('driver_id',$driver_id);
                                                }])
                                                ->with(['user_details'=>function($query){
                                                    $query->select('id','first_name','last_name','profile_image','mobile_no');
                                                }])
                                                ->whereIn('request_status',['USER_REQUEST','TIMEOUT','REJECT_BY_USER','ACCEPT_BY_DRIVER','REJECT_BY_DRIVER'])
                                                ->where('created_at', '>',$formatted_date) /*latest 24 hours records will be shown */
                                                ->having('driver_distance','<=',$this->distance)
                                                ->where('is_admin_assistant','NO')
                                                ->orderBy('id','DESC')
                                                ->get();

        if($obj_pending_load_post)
        {
            $arr_tmp_pending_load_post = $obj_pending_load_post->toArray();
        }
        /*filter data based on cutom array*/
        if(isset($arr_tmp_pending_load_post) && sizeof($arr_tmp_pending_load_post)>0)
        {
            foreach ($arr_tmp_pending_load_post as $main_key => $main_value) 
            {
                $is_record_show = 'yes';
                if(isset($main_value['load_post_request_history_details']) && sizeof($main_value['load_post_request_history_details'])>0)
                {
                    foreach ($main_value['load_post_request_history_details'] as $sub_key => $sub_value) 
                    {
                        if(isset($sub_value['driver_id']) && ($sub_value['driver_id']===$driver_id))
                        {
                            if(isset($sub_value['status']) && $sub_value['status'] == 'REJECT_BY_DRIVER'){
                                $is_record_show = 'no';
                            }
                        }
                    }
                }

                /*if another driver accept the request then remove from list*/
                if(isset($main_value['request_status']) && $main_value['request_status'] == 'ACCEPT_BY_DRIVER'){
                    if( isset($main_value['driver_id']) && ($main_value['driver_id'] != $driver_id)){
                        unset($arr_tmp_pending_load_post[$main_key]);
                    }
                }

                // /*if another driver accept the request then remove from list*/
                // if(isset($main_value['request_status']) && $main_value['request_status'] == 'REJECT_BY_DRIVER'){
                //     if( isset($main_value['driver_id']) && ($main_value['driver_id'] == $driver_id)){
                //         unset($arr_tmp_pending_load_post[$main_key]);
                //     }
                // }

                if($is_record_show == 'no'){
                    unset($arr_tmp_pending_load_post[$main_key]);
                }
                if((isset($main_value['is_future_request']) && $main_value['is_future_request'] == '1') && (isset($main_value['is_request_process']) && $main_value['is_request_process'] == '0')){
                    unset($arr_tmp_pending_load_post[$main_key]);
                }
                if(isset($main_value['user_details']['mobile_no']) && $main_value['user_details']['mobile_no']!='')
                {
                    if($main_value['user_details']['mobile_no'] == $driver_mobile_no){
                        unset($arr_tmp_pending_load_post[$main_key]);
                    }
                }
                
            }
        }
        $arr_pending_load_post = [];

        $arr_pending_load_post = array_values($arr_tmp_pending_load_post);
        
        if(isset($arr_pending_load_post) && sizeof($arr_pending_load_post)>0){
            foreach ($arr_pending_load_post as $key => $value) 
            {
                $arr_pending_load_post[$key]['first_name'] = isset($value['user_details']['first_name']) ? $value['user_details']['first_name'] :'';
                $arr_pending_load_post[$key]['last_name']  = isset($value['user_details']['last_name']) ? $value['user_details']['last_name'] :'';
                $profile_image = '';
                if(isset($value['user_details']['profile_image']) && $value['user_details']['profile_image']!=''){
                    if(file_exists($this->user_profile_base_img_path.$value['user_details']['profile_image'])){
                        $profile_image = $this->user_profile_public_img_path.$value['user_details']['profile_image'];
                    }
                }
                $arr_pending_load_post[$key]['profile_image']  = $profile_image;
                
                $datetime1   = new \DateTime();
                $datetime2   = isset($value['created_at']) ? new \DateTime($value['created_at']) :'';

                $arr_pending_load_post[$key]['time_ago']  = get_time_difference($datetime1,$datetime2);

                unset($arr_pending_load_post[$key]['user_details']);
                unset($arr_pending_load_post[$key]['created_at']);
                unset($arr_pending_load_post[$key]['user_id']);
                unset($arr_pending_load_post[$key]['driver_id']);
                unset($arr_pending_load_post[$key]['driver_distance']);
                // unset($arr_pending_load_post[$key]['request_status']);
                unset($arr_pending_load_post[$key]['load_post_request_history_details']);
                unset($arr_pending_load_post[$key]['load_post_request_package_details']);
            }
            
            $arr_paginate_pending_load_post = [];

            $obj_paginate_pending_load_post =  $this->make_pagination_links($arr_pending_load_post,$this->per_page);
            if($obj_paginate_pending_load_post)
            {
                $arr_paginate_pending_load_post = $obj_paginate_pending_load_post->toArray();
            }
            
            if(isset($arr_paginate_pending_load_post['data']) && sizeof($arr_paginate_pending_load_post['data'])>0)
            {
                $arr_paginate_pending_load_post['data'] = array_values($arr_paginate_pending_load_post['data']);
                $arr_response['status'] = 'success';
                $arr_response['msg']    = 'pending request available.';
                $arr_response['data']    = $arr_paginate_pending_load_post;
                return $arr_response;
            }
            else
            {
                $arr_response['status'] = 'error';
                $arr_response['msg']    = 'No pending request available.';
                $arr_response['data']    = [];
                return $arr_response;
            }

        }
        $arr_response['status'] = 'error';
        $arr_response['msg']    = 'No pending request available.';
        $arr_response['data']    = [];
        return $arr_response;
    }

    public function make_pagination_links($items,$perPage)
    {
        $pageStart = \Request::get('page', 1);
        // Start displaying items from this number;
        $offSet = ($pageStart * $perPage) - $perPage; 

        // Get only the items you need using array_slice
        $itemsForCurrentPage = array_slice($items, $offSet, $perPage, true);

        return new LengthAwarePaginator($itemsForCurrentPage, count($items), $perPage,Paginator::resolveCurrentPage(), array('path' => Paginator::resolveCurrentPath()));
    } 
    
    /*
    |
    | user will see all ongoing trips
    |
    */
    public function ongoing_trips($request)
    {
        $user_id     = validate_user_jwt_token();
        if ($user_id == 0) 
        {
            $arr_response['status'] = 'error';
            $arr_response['msg']    = 'Invalid driver token';
            $arr_response['data']    = [];
            return $arr_response;
        }
        
        $arr_ongoing_trips = $this->get_filter_trips($user_id,'ONGOING',$request);

        if(isset($arr_ongoing_trips['data']) && sizeof($arr_ongoing_trips['data'])>0){
            foreach ($arr_ongoing_trips['data'] as $key => $value) 
            {
                $arr_ongoing_trips['data'][$key]['booking_status'] = isset($value['booking_status']) ? $value['booking_status'] : '';
                $arr_ongoing_trips['data'][$key]['booking_date']   = isset($value['booking_date']) ? date('d M Y',strtotime($value['booking_date'])) : '';
                $arr_ongoing_trips['data'][$key]['driver_id']      = isset($value['load_post_request_details']['driver_id']) ? intval($value['load_post_request_details']['driver_id']) :0;
                $arr_ongoing_trips['data'][$key]['first_name']     = isset($value['load_post_request_details']['driver_details']['first_name']) ? $value['load_post_request_details']['driver_details']['first_name'] :'';
                $arr_ongoing_trips['data'][$key]['last_name']      = isset($value['load_post_request_details']['driver_details']['last_name']) ? $value['load_post_request_details']['driver_details']['last_name'] :'';
                
                $profile_image = '';
                if(isset($value['load_post_request_details']['driver_details']['profile_image']) && $value['load_post_request_details']['driver_details']['profile_image']!=''){
                    if(file_exists($this->user_profile_base_img_path.$value['load_post_request_details']['driver_details']['profile_image'])){
                        $profile_image = $this->user_profile_public_img_path.$value['load_post_request_details']['driver_details']['profile_image'];
                    }
                }
                
                $arr_ongoing_trips['data'][$key]['profile_image']     = $profile_image;

                $country_code   = isset($value['load_post_request_details']['driver_details']['country_code']) ? $value['load_post_request_details']['driver_details']['country_code'] : '';
                $mobile_no      = isset($value['load_post_request_details']['driver_details']['mobile_no']) ? $value['load_post_request_details']['driver_details']['mobile_no'] : '';
                $full_mobile_no = $country_code.''.$mobile_no;
                $full_mobile_no = ($full_mobile_no!='')?$full_mobile_no :'';
                
                $arr_ongoing_trips['data'][$key]['mobile_no']         = $full_mobile_no;
                $arr_ongoing_trips['data'][$key]['pickup_location']   = isset($value['load_post_request_details']['pickup_location']) ? $value['load_post_request_details']['pickup_location'] :'';
                $arr_ongoing_trips['data'][$key]['drop_location']     = isset($value['load_post_request_details']['drop_location']) ? $value['load_post_request_details']['drop_location'] :'';
                $arr_ongoing_trips['data'][$key]['pickup_lat']        = isset($value['load_post_request_details']['pickup_lat']) ? doubleval($value['load_post_request_details']['pickup_lat']) :doubleval(0.0);
                $arr_ongoing_trips['data'][$key]['pickup_lng']        = isset($value['load_post_request_details']['pickup_lng']) ? doubleval($value['load_post_request_details']['pickup_lng']) :doubleval(0.0);
                $arr_ongoing_trips['data'][$key]['drop_lat']          = isset($value['load_post_request_details']['drop_lat']) ? doubleval($value['load_post_request_details']['drop_lat']) :doubleval(0.0);
                $arr_ongoing_trips['data'][$key]['drop_lng']          = isset($value['load_post_request_details']['drop_lng']) ? doubleval($value['load_post_request_details']['drop_lng']) :doubleval(0.0);
                $arr_ongoing_trips['data'][$key]['driver_lat']        = isset($value['load_post_request_details']['driver_current_location_details']['current_latitude']) ? doubleval($value['load_post_request_details']['driver_current_location_details']['current_latitude']) :doubleval(0.0);
                $arr_ongoing_trips['data'][$key]['driver_lng']        = isset($value['load_post_request_details']['driver_current_location_details']['current_longitude']) ? doubleval($value['load_post_request_details']['driver_current_location_details']['current_longitude']) :doubleval(0.0);

                $arr_ongoing_trips['data'][$key]['package_type']      = isset($value['load_post_request_details']['load_post_request_package_details']['package_type']) ? $value['load_post_request_details']['load_post_request_package_details']['package_type'] : '';
                $arr_ongoing_trips['data'][$key]['package_length']    = isset($value['load_post_request_details']['load_post_request_package_details']['package_length']) ? doubleval($value['load_post_request_details']['load_post_request_package_details']['package_length']) : doubleval(0.0);
                $arr_ongoing_trips['data'][$key]['package_breadth']   = isset($value['load_post_request_details']['load_post_request_package_details']['package_breadth']) ? doubleval($value['load_post_request_details']['load_post_request_package_details']['package_breadth']) : doubleval(0.0);
                $arr_ongoing_trips['data'][$key]['package_height']    = isset($value['load_post_request_details']['load_post_request_package_details']['package_height']) ? doubleval($value['load_post_request_details']['load_post_request_package_details']['package_height']) : doubleval(0.0);
                $arr_ongoing_trips['data'][$key]['package_weight']    = isset($value['load_post_request_details']['load_post_request_package_details']['package_weight']) ? doubleval($value['load_post_request_details']['load_post_request_package_details']['package_weight']) : doubleval(0.0);
                $arr_ongoing_trips['data'][$key]['package_quantity']  = isset($value['load_post_request_details']['load_post_request_package_details']['package_quantity']) ? intval($value['load_post_request_details']['load_post_request_package_details']['package_quantity']) : 0;

                $load_post_image = '';
                if(isset($value['load_post_request_details']['load_post_image']) && $value['load_post_request_details']['load_post_image']!=''){
                    if(file_exists($this->load_post_img_base_path.$value['load_post_request_details']['load_post_image'])){
                        $load_post_image = $this->load_post_img_public_path.$value['load_post_request_details']['load_post_image'];
                    }
                }
                $arr_ongoing_trips['data'][$key]['load_post_image']  = $load_post_image;

                unset($arr_ongoing_trips['data'][$key]['review_details']);
                unset($arr_ongoing_trips['data'][$key]['load_post_request_details']);
            }
            $arr_response['status'] = 'success';
            $arr_response['msg']    = 'ongoing trips available.';
            $arr_response['data']    = $arr_ongoing_trips;
            return $arr_response;

        }
        $arr_response['status'] = 'error';
        $arr_response['msg']    = 'No ongoing trips available.';
        $arr_response['data']    = [];
        return $arr_response;
    }
    
    /*
    |
    | user will see all pending trips
    |
    */
    public function pending_trips($request)
    {
        $user_id     = validate_user_jwt_token();
        if ($user_id == 0) 
        {
            $arr_response['status'] = 'error';
            $arr_response['msg']    = 'Invalid driver token';
            $arr_response['data']    = [];
            return $arr_response;
        }
        
        $arr_pending_trips = $this->get_filter_trips($user_id,'PENDING',$request);
        
        if(isset($arr_pending_trips['data']) && sizeof($arr_pending_trips['data'])>0)
        {
            foreach ($arr_pending_trips['data'] as $key => $value) 
            {
                // $booking_status = 'PENDING';
                // if(isset($value['request_status']) && $value['request_status'] == 'ACCEPT_BY_DRIVER'){
                //     $booking_status = 'ACCEPT_BY_DRIVER';
                // }
                $arr_pending_trips['data'][$key]['id']                   = 0;
                $arr_pending_trips['data'][$key]['load_post_request_id'] = isset($value['id']) ? $value['id'] :'';
                $arr_pending_trips['data'][$key]['booking_unique_id']    = '';
                $arr_pending_trips['data'][$key]['booking_status']       = isset($value['request_status']) ? $value['request_status'] : '';
                $arr_pending_trips['data'][$key]['booking_date']         = isset($value['date']) ? date('d M Y',strtotime($value['date'])) : '';
                $arr_pending_trips['data'][$key]['first_name']           = '';
                $arr_pending_trips['data'][$key]['last_name']            = '';
                $arr_pending_trips['data'][$key]['profile_image']        = '';
                $arr_pending_trips['data'][$key]['mobile_no']            = '';
                $arr_pending_trips['data'][$key]['pickup_location']      = isset($value['pickup_location']) ? $value['pickup_location'] :'';
                $arr_pending_trips['data'][$key]['drop_location']        = isset($value['drop_location']) ? $value['drop_location'] :'';
                $arr_pending_trips['data'][$key]['pickup_lat']           = isset($value['pickup_lat']) ? doubleval($value['pickup_lat']) :doubleval(0.0);
                $arr_pending_trips['data'][$key]['pickup_lng']           = isset($value['pickup_lng']) ? doubleval($value['pickup_lng']) :doubleval(0.0);
                $arr_pending_trips['data'][$key]['drop_lat']             = isset($value['drop_lat']) ? doubleval($value['drop_lat']) :doubleval(0.0);
                $arr_pending_trips['data'][$key]['drop_lng']             = isset($value['drop_lng']) ? doubleval($value['drop_lng']) :doubleval(0.0);
                $arr_pending_trips['data'][$key]['driver_lat']           = doubleval(0.0);
                $arr_pending_trips['data'][$key]['driver_lng']           = doubleval(0.0);
                $arr_pending_trips['data'][$key]['is_future_request']    = isset($value['is_future_request']) ? $value['is_future_request'] :'0';
                $arr_pending_trips['data'][$key]['is_request_process']   = isset($value['is_request_process']) ? $value['is_request_process'] :'0';
                $arr_pending_trips['data'][$key]['request_time']         = isset($value['request_time']) ? $value['request_time'] :'';
                $arr_pending_trips['data'][$key]['package_type']         = isset($value['load_post_request_package_details']['package_type']) ? $value['load_post_request_package_details']['package_type'] : '';
                $arr_pending_trips['data'][$key]['package_length']       = isset($value['load_post_request_package_details']['package_length']) ? doubleval($value['load_post_request_package_details']['package_length']) : doubleval(0.0);
                $arr_pending_trips['data'][$key]['package_breadth']      = isset($value['load_post_request_package_details']['package_breadth']) ? doubleval($value['load_post_request_package_details']['package_breadth']) : doubleval(0.0);
                $arr_pending_trips['data'][$key]['package_height']       = isset($value['load_post_request_package_details']['package_height']) ? doubleval($value['load_post_request_package_details']['package_height']) : doubleval(0.0);
                $arr_pending_trips['data'][$key]['package_weight']       = isset($value['load_post_request_package_details']['package_weight']) ? doubleval($value['load_post_request_package_details']['package_weight']) : doubleval(0.0);
                $arr_pending_trips['data'][$key]['package_quantity']     = isset($value['load_post_request_package_details']['package_quantity']) ? intval($value['load_post_request_package_details']['package_quantity']) : 0;

                $load_post_image = '';
                if(isset($value['load_post_image']) && $value['load_post_image']!=''){
                    if(file_exists($this->load_post_img_base_path.$value['load_post_image'])){
                        $load_post_image = $this->load_post_img_public_path.$value['load_post_image'];
                    }
                }
                $arr_pending_trips['data'][$key]['load_post_image']  = $load_post_image;

                unset($arr_pending_trips['data'][$key]['date']);
                unset($arr_pending_trips['data'][$key]['request_status']);
                unset($arr_pending_trips['data'][$key]['load_post_request_package_details']);
                

            }
            $arr_response['status'] = 'success';
            $arr_response['msg']    = 'Pending trips available.';
            $arr_response['data']    = $arr_pending_trips;
            return $arr_response;
        }
        
        $arr_response['status'] = 'error';
        $arr_response['msg']    = 'No pending trips available.';
        $arr_response['data']    = [];
        return $arr_response;
    }
    
    public function cancel_pending_load_post($request)
    {
        $login_user_id     = validate_user_jwt_token();
        if ($login_user_id == 0) 
        {
            $arr_response['status'] = 'error';
            $arr_response['msg']    = 'Invalid driver token';
            $arr_response['data']    = [];
            return $arr_response;
        }
        
        $load_post_request_id = $request->input('load_post_request_id');

        if($login_user_id!='' && $load_post_request_id!='')
        {
            $user_id = $login_user_id;
            /*check for load post status if 'accept_by_user' or not*/
            $obj_load_post_request = $this->LoadPostRequestModel
                                                    ->where('id',$load_post_request_id)
                                                    ->first();
        
            if(isset($obj_load_post_request) && $obj_load_post_request!=null)
            {
                /*check if any driver accept load post request if yes then user not able to cancel the load*/
                if(isset($obj_load_post_request->request_status) && $obj_load_post_request->request_status == 'ACCEPT_BY_DRIVER')
                {
                    $arr_response['status'] = 'error';
                    $arr_response['msg']    = 'Sorry for inconvenience, driver accepted shipment request.';
                    $arr_response['data']   = [];
                    return $arr_response;
                }

                $obj_load_post_request->request_status = 'CANCEL_BY_USER';
                $status = $obj_load_post_request->save();
                
                if($status){
                    
                    $arr_load_post_request_history = [];
                    $arr_load_post_request_history['load_post_request_id'] = $load_post_request_id;
                    $arr_load_post_request_history['user_id']              = $user_id;
                    $arr_load_post_request_history['driver_id']            = 0;
                    $arr_load_post_request_history['status']               = 'CANCEL_BY_USER';
                    $arr_load_post_request_history['reason']               = '';

                    $this->LoadPostRequestHistoryModel->create($arr_load_post_request_history);
                    
                    $arr_response['status'] = 'success';
                    $arr_response['msg']    = 'You have cancelled pending shipment post request.';
                    $arr_response['data']   = [];
                    return $arr_response;

                }else{
                    $arr_response['status'] = 'error';
                    $arr_response['msg']    = 'Problem occurred, while processing shipment post request, Please try again.';
                    $arr_response['data']   = [];
                    return $arr_response;
                }
            }
            $arr_response['status'] = 'error';
            $arr_response['msg']    = 'Problem occurred, while processing request, Please try again.';
            $arr_response['data']   = [];
            return $arr_response;
        }
        $arr_response['status'] = 'error';
        $arr_response['msg']    = 'Problem occurred, while processing request, Please try again.';
        $arr_response['data']   = [];
        return $arr_response;   

    }
    /*
    |
    | user will see all completed trips
    |
    */
    public function completed_trips($request)
    {
        $user_id     = validate_user_jwt_token();
        if ($user_id == 0) 
        {
            $arr_response['status'] = 'error';
            $arr_response['msg']    = 'Invalid driver token';
            $arr_response['data']    = [];
            return $arr_response;
        }
        
        $user_type = $request->input('user_type','');
        if($user_type == ''){
            $user_type = 'USER';
        }
        
        $arr_completed_trips = $this->get_filter_trips($user_id,'COMPLETED',$request);
        
        if(isset($arr_completed_trips['data']) && sizeof($arr_completed_trips['data'])>0){
            foreach ($arr_completed_trips['data'] as $key => $value) 
            {
                $arr_completed_trips['data'][$key]['booking_status'] = isset($value['booking_status']) ? $value['booking_status'] : '';
                $arr_completed_trips['data'][$key]['booking_date']   = isset($value['booking_date']) ? date('d M Y',strtotime($value['booking_date'])) : '';
                
                if($user_type == 'USER')
                {
                    $arr_completed_trips['data'][$key]['first_name']     = isset($value['load_post_request_details']['driver_details']['first_name']) ? $value['load_post_request_details']['driver_details']['first_name'] :'';
                    $arr_completed_trips['data'][$key]['last_name']      = isset($value['load_post_request_details']['driver_details']['last_name']) ? $value['load_post_request_details']['driver_details']['last_name'] :'';
                    $profile_image = '';
                    if(isset($value['load_post_request_details']['driver_details']['profile_image']) && $value['load_post_request_details']['driver_details']['profile_image']!=''){
                        if(file_exists($this->user_profile_base_img_path.$value['load_post_request_details']['driver_details']['profile_image'])){
                            $profile_image = $this->user_profile_public_img_path.$value['load_post_request_details']['driver_details']['profile_image'];
                        }
                    }
                    
                    $arr_completed_trips['data'][$key]['profile_image']     = $profile_image;
                    
                    $country_code   = isset($value['load_post_request_details']['driver_details']['country_code']) ? $value['load_post_request_details']['driver_details']['country_code'] : '';
                    $mobile_no      = isset($value['load_post_request_details']['driver_details']['mobile_no']) ? $value['load_post_request_details']['driver_details']['mobile_no'] : '';
                    $full_mobile_no = $country_code.''.$mobile_no;
                    $full_mobile_no = ($full_mobile_no!='')?$full_mobile_no :'';

                    $arr_completed_trips['data'][$key]['mobile_no'] = $full_mobile_no;
                }

                if($user_type == 'DRIVER')
                {
                    $arr_completed_trips['data'][$key]['first_name']     = isset($value['load_post_request_details']['user_details']['first_name']) ? $value['load_post_request_details']['user_details']['first_name'] :'';
                    $arr_completed_trips['data'][$key]['last_name']      = isset($value['load_post_request_details']['user_details']['last_name']) ? $value['load_post_request_details']['user_details']['last_name'] :'';
                    $profile_image = '';
                    if(isset($value['load_post_request_details']['user_details']['profile_image']) && $value['load_post_request_details']['user_details']['profile_image']!=''){
                        if(file_exists($this->user_profile_base_img_path.$value['load_post_request_details']['user_details']['profile_image'])){
                            $profile_image = $this->user_profile_public_img_path.$value['load_post_request_details']['user_details']['profile_image'];
                        }
                    }
                    
                    $arr_completed_trips['data'][$key]['profile_image']     = $profile_image;
                    
                    $country_code   = isset($value['load_post_request_details']['user_details']['country_code']) ? $value['load_post_request_details']['user_details']['country_code'] : '';
                    $mobile_no      = isset($value['load_post_request_details']['user_details']['mobile_no']) ? $value['load_post_request_details']['user_details']['mobile_no'] : '';
                    $full_mobile_no = $country_code.''.$mobile_no;
                    $full_mobile_no = ($full_mobile_no!='')?$full_mobile_no :'';

                    $arr_completed_trips['data'][$key]['mobile_no'] = $full_mobile_no;
                }

                $arr_completed_trips['data'][$key]['pickup_location']   = isset($value['load_post_request_details']['pickup_location']) ? $value['load_post_request_details']['pickup_location'] :'';
                $arr_completed_trips['data'][$key]['drop_location']     = isset($value['load_post_request_details']['drop_location']) ? $value['load_post_request_details']['drop_location'] :'';
                $arr_completed_trips['data'][$key]['pickup_lat']        = isset($value['load_post_request_details']['pickup_lat']) ? doubleval($value['load_post_request_details']['pickup_lat']) :doubleval(0.0);
                $arr_completed_trips['data'][$key]['pickup_lng']        = isset($value['load_post_request_details']['pickup_lng']) ? doubleval($value['load_post_request_details']['pickup_lng']) :doubleval(0.0);
                $arr_completed_trips['data'][$key]['drop_lat']          = isset($value['load_post_request_details']['drop_lat']) ? doubleval($value['load_post_request_details']['drop_lat']) :doubleval(0.0);
                $arr_completed_trips['data'][$key]['drop_lng']          = isset($value['load_post_request_details']['drop_lng']) ? doubleval($value['load_post_request_details']['drop_lng']) :doubleval(0.0);
                $arr_completed_trips['data'][$key]['driver_lat']        = isset($value['load_post_request_details']['driver_current_location_details']['current_latitude']) ? doubleval($value['load_post_request_details']['driver_current_location_details']['current_latitude']) :doubleval(0.0);
                $arr_completed_trips['data'][$key]['driver_lng']        = isset($value['load_post_request_details']['driver_current_location_details']['current_longitude']) ? doubleval($value['load_post_request_details']['driver_current_location_details']['current_longitude']) :doubleval(0.0);
            
                $is_review_given = '0';
                if(isset($value['review_details']) && count($value['review_details'])>0){
                    $is_review_given = '1';
                }
                $arr_completed_trips['data'][$key]['is_review_given']  = $is_review_given;

                $arr_completed_trips['data'][$key]['package_type']      = isset($value['load_post_request_details']['load_post_request_package_details']['package_type']) ? $value['load_post_request_details']['load_post_request_package_details']['package_type'] : '';
                $arr_completed_trips['data'][$key]['package_length']    = isset($value['load_post_request_details']['load_post_request_package_details']['package_length']) ? doubleval($value['load_post_request_details']['load_post_request_package_details']['package_length']) : doubleval(0.0);
                $arr_completed_trips['data'][$key]['package_breadth']   = isset($value['load_post_request_details']['load_post_request_package_details']['package_breadth']) ? doubleval($value['load_post_request_details']['load_post_request_package_details']['package_breadth']) : doubleval(0.0);
                $arr_completed_trips['data'][$key]['package_height']    = isset($value['load_post_request_details']['load_post_request_package_details']['package_height']) ? doubleval($value['load_post_request_details']['load_post_request_package_details']['package_height']) : doubleval(0.0);
                $arr_completed_trips['data'][$key]['package_weight']    = isset($value['load_post_request_details']['load_post_request_package_details']['package_weight']) ? doubleval($value['load_post_request_details']['load_post_request_package_details']['package_weight']) : doubleval(0.0);
                $arr_completed_trips['data'][$key]['package_quantity']  = isset($value['load_post_request_details']['load_post_request_package_details']['package_quantity']) ? intval($value['load_post_request_details']['load_post_request_package_details']['package_quantity']) : 0;
                

                $load_post_image = '';
                if(isset($value['load_post_request_details']['load_post_image']) && $value['load_post_request_details']['load_post_image']!=''){
                    if(file_exists($this->load_post_img_base_path.$value['load_post_request_details']['load_post_image'])){
                        $load_post_image = $this->load_post_img_public_path.$value['load_post_request_details']['load_post_image'];
                    }
                }
                $arr_completed_trips['data'][$key]['load_post_image']  = $load_post_image;

                unset($arr_completed_trips['data'][$key]['review_details']);
                unset($arr_completed_trips['data'][$key]['load_post_request_details']);
            }
            $arr_response['status'] = 'success';
            $arr_response['msg']    = 'completed trips available.';
            $arr_response['data']    = $arr_completed_trips;
            return $arr_response;

        }
        $arr_response['status'] = 'error';
        $arr_response['msg']    = 'No completed trips available.';
        $arr_response['data']    = [];
        return $arr_response;
    }

    /*
    |
    | user will see all canceled trips 
    |
    */
    public function canceled_trips($request)
    {
        $user_id     = validate_user_jwt_token();
        if ($user_id == 0) 
        {
            $arr_response['status'] = 'error';
            $arr_response['msg']    = 'Invalid driver token';
            $arr_response['data']    = [];
            return $arr_response;
        }

        $user_type = $request->input('user_type','');
        if($user_type == ''){
            $user_type = 'USER';
        }
        $arr_canceled_trips = $this->get_filter_trips($user_id,'CANCELED',$request);
        
        if(isset($arr_canceled_trips['data']) && sizeof($arr_canceled_trips['data'])>0){
            foreach ($arr_canceled_trips['data'] as $key => $value) 
            {
                $record_type = isset($value['type']) ? $value['type'] : '';
                $arr_canceled_trips['data'][$key]['type']           = isset($value['type']) ? $value['type'] : '';
                $arr_canceled_trips['data'][$key]['booking_status'] = isset($value['booking_status']) ? $value['booking_status'] : '';
                $arr_canceled_trips['data'][$key]['booking_date']   = isset($value['booking_date']) ? date('d M Y',strtotime($value['booking_date'])) : '';
                
                if($user_type == 'USER')
                {
                    if($record_type == 'normal_booking')
                    {
                        $arr_canceled_trips['data'][$key]['first_name']     = isset($value['load_post_request_details']['driver_details']['first_name']) ? $value['load_post_request_details']['driver_details']['first_name'] :'';
                        $arr_canceled_trips['data'][$key]['last_name']      = isset($value['load_post_request_details']['driver_details']['last_name']) ? $value['load_post_request_details']['driver_details']['last_name'] :'';   

                        if(isset($value['booking_status']) && $value['booking_status'] == 'CANCEL_BY_ADMIN')
                        {
                            $arr_canceled_trips['data'][$key]['first_name']     = 'Canceled by '.config('app.project.name').' Admin';
                            $arr_canceled_trips['data'][$key]['last_name']      = '';
                        }
                        
                        $profile_image = '';
                        if(isset($value['load_post_request_details']['driver_details']['profile_image']) && $value['load_post_request_details']['driver_details']['profile_image']!=''){
                            if(file_exists($this->user_profile_base_img_path.$value['load_post_request_details']['driver_details']['profile_image'])){
                                $profile_image = $this->user_profile_public_img_path.$value['load_post_request_details']['driver_details']['profile_image'];
                            }
                        }
                        
                        $arr_canceled_trips['data'][$key]['profile_image']     = $profile_image;

                        $country_code   = isset($value['load_post_request_details']['driver_details']['country_code']) ? $value['load_post_request_details']['driver_details']['country_code'] : '';
                        $mobile_no      = isset($value['load_post_request_details']['driver_details']['mobile_no']) ? $value['load_post_request_details']['driver_details']['mobile_no'] : '';
                        
                        $full_mobile_no = $country_code.''.$mobile_no;
                        $full_mobile_no = ($full_mobile_no!='')?$full_mobile_no :'';

                        $arr_canceled_trips['data'][$key]['mobile_no'] = $full_mobile_no;
                    }
                    else if($record_type == 'load_post')
                    {
                        $arr_canceled_trips['data'][$key]['first_name']     = 'Canceled by you';
                        $arr_canceled_trips['data'][$key]['last_name']      = '';
                        $profile_image = '';
                        if(isset($value['driver_details']['profile_image']) && $value['driver_details']['profile_image']!=''){
                            if(file_exists($this->user_profile_base_img_path.$value['driver_details']['profile_image'])){
                                $profile_image = $this->user_profile_public_img_path.$value['driver_details']['profile_image'];
                            }
                        }
                        
                        $arr_canceled_trips['data'][$key]['profile_image']     = $profile_image;

                        $country_code   = isset($value['driver_details']['country_code']) ? $value['driver_details']['country_code'] : '';
                        $mobile_no      = isset($value['driver_details']['mobile_no']) ? $value['driver_details']['mobile_no'] : '';
                        
                        $full_mobile_no = $country_code.''.$mobile_no;
                        $full_mobile_no = ($full_mobile_no!='')?$full_mobile_no :'';

                        $arr_canceled_trips['data'][$key]['mobile_no'] = $full_mobile_no;
                    }
                }    

                if($user_type == 'DRIVER')
                {
                    if($record_type == 'normal_booking')
                    {
                        $arr_canceled_trips['data'][$key]['first_name']     = isset($value['load_post_request_details']['user_details']['first_name']) ? $value['load_post_request_details']['user_details']['first_name'] :'';
                        $arr_canceled_trips['data'][$key]['last_name']      = isset($value['load_post_request_details']['user_details']['last_name']) ? $value['load_post_request_details']['user_details']['last_name'] :'';
                        
                        if(isset($value['booking_status']) && $value['booking_status'] == 'CANCEL_BY_ADMIN')
                        {
                            $arr_canceled_trips['data'][$key]['first_name']     = 'Canceled by '.config('app.project.name').' Admin';
                            $arr_canceled_trips['data'][$key]['last_name']      = '';
                        }
                        
                        $profile_image = '';
                        if(isset($value['load_post_request_details']['user_details']['profile_image']) && $value['load_post_request_details']['user_details']['profile_image']!=''){
                            if(file_exists($this->user_profile_base_img_path.$value['load_post_request_details']['user_details']['profile_image'])){
                                $profile_image = $this->user_profile_public_img_path.$value['load_post_request_details']['user_details']['profile_image'];
                            }
                        }
                        
                        $arr_canceled_trips['data'][$key]['profile_image']     = $profile_image;
                        
                        $country_code   = isset($value['load_post_request_details']['user_details']['country_code']) ? $value['load_post_request_details']['user_details']['country_code'] : '';
                        $mobile_no      = isset($value['load_post_request_details']['user_details']['mobile_no']) ? $value['load_post_request_details']['user_details']['mobile_no'] : '';
                        $full_mobile_no = $country_code.''.$mobile_no;
                        $full_mobile_no = ($full_mobile_no!='')?$full_mobile_no :'';

                        $arr_canceled_trips['data'][$key]['mobile_no'] = $full_mobile_no;
                    }
                    else if($record_type == 'load_post')
                    {   
                        $arr_canceled_trips['data'][$key]['first_name']     = 'Canceled by you';
                        $arr_canceled_trips['data'][$key]['last_name']      = '';

                        if(isset($value['booking_status']) && $value['booking_status'] == 'REJECT_BY_USER'){
                            $arr_canceled_trips['data'][$key]['first_name']     = 'Canceled by Customer';
                            $arr_canceled_trips['data'][$key]['last_name']      = '';
                        }
                        else if(isset($value['booking_status']) && $value['booking_status'] == 'CANCEL_BY_USER'){
                            $arr_canceled_trips['data'][$key]['first_name']     = 'Canceled by Customer';
                            $arr_canceled_trips['data'][$key]['last_name']      = '';
                        }
                        else if(isset($value['booking_status']) && $value['booking_status'] == 'REJECT_BY_DRIVER'){
                            $arr_canceled_trips['data'][$key]['first_name']     = 'Declined by you';
                            $arr_canceled_trips['data'][$key]['last_name']      = '';
                        }

                        $profile_image = '';
                        if(isset($value['user_details']['profile_image']) && $value['user_details']['profile_image']!=''){
                            if(file_exists($this->user_profile_base_img_path.$value['user_details']['profile_image'])){
                                $profile_image = $this->user_profile_public_img_path.$value['user_details']['profile_image'];
                            }
                        }
                        
                        $arr_canceled_trips['data'][$key]['profile_image']     = $profile_image;
                        
                        $country_code   = isset($value['user_details']['country_code']) ? $value['user_details']['country_code'] : '';
                        $mobile_no      = isset($value['user_details']['mobile_no']) ? $value['user_details']['mobile_no'] : '';
                        $full_mobile_no = $country_code.''.$mobile_no;
                        $full_mobile_no = ($full_mobile_no!='')?$full_mobile_no :'';

                        $arr_canceled_trips['data'][$key]['mobile_no'] = $full_mobile_no;   
                    }


                    // $arr_canceled_trips['data'][$key]['mobile_no']         = isset($value['load_post_request_details']['user_details']['mobile_no']) ? $value['load_post_request_details']['user_details']['mobile_no'] :'';
                }    

                if($record_type == 'normal_booking')
                {
                    $arr_canceled_trips['data'][$key]['pickup_location']   = isset($value['load_post_request_details']['pickup_location']) ? $value['load_post_request_details']['pickup_location'] :'';
                    $arr_canceled_trips['data'][$key]['drop_location']     = isset($value['load_post_request_details']['drop_location']) ? $value['load_post_request_details']['drop_location'] :'';
                    $arr_canceled_trips['data'][$key]['pickup_lat']        = isset($value['load_post_request_details']['pickup_lat']) ? doubleval($value['load_post_request_details']['pickup_lat']) :doubleval(0.0);
                    $arr_canceled_trips['data'][$key]['pickup_lng']        = isset($value['load_post_request_details']['pickup_lng']) ? doubleval($value['load_post_request_details']['pickup_lng']) :doubleval(0.0);
                    $arr_canceled_trips['data'][$key]['drop_lat']          = isset($value['load_post_request_details']['drop_lat']) ? doubleval($value['load_post_request_details']['drop_lat']) :doubleval(0.0);
                    $arr_canceled_trips['data'][$key]['drop_lng']          = isset($value['load_post_request_details']['drop_lng']) ? doubleval($value['load_post_request_details']['drop_lng']) :doubleval(0.0);
                    
                    $arr_canceled_trips['data'][$key]['driver_lat']        = isset($value['load_post_request_details']['driver_current_location_details']['current_latitude']) ? doubleval($value['load_post_request_details']['driver_current_location_details']['current_latitude']) :doubleval(0.0);
                    $arr_canceled_trips['data'][$key]['driver_lng']        = isset($value['load_post_request_details']['driver_current_location_details']['current_longitude']) ? doubleval($value['load_post_request_details']['driver_current_location_details']['current_longitude']) :doubleval(0.0);

                    $arr_canceled_trips['data'][$key]['package_type']      = isset($value['load_post_request_details']['load_post_request_package_details']['package_type']) ? $value['load_post_request_details']['load_post_request_package_details']['package_type'] : '';
                    $arr_canceled_trips['data'][$key]['package_length']    = isset($value['load_post_request_details']['load_post_request_package_details']['package_length']) ? doubleval($value['load_post_request_details']['load_post_request_package_details']['package_length']) : doubleval(0.0);
                    $arr_canceled_trips['data'][$key]['package_breadth']   = isset($value['load_post_request_details']['load_post_request_package_details']['package_breadth']) ? doubleval($value['load_post_request_details']['load_post_request_package_details']['package_breadth']) : doubleval(0.0);
                    $arr_canceled_trips['data'][$key]['package_height']    = isset($value['load_post_request_details']['load_post_request_package_details']['package_height']) ? doubleval($value['load_post_request_details']['load_post_request_package_details']['package_height']) : doubleval(0.0);
                    $arr_canceled_trips['data'][$key]['package_weight']    = isset($value['load_post_request_details']['load_post_request_package_details']['package_weight']) ? doubleval($value['load_post_request_details']['load_post_request_package_details']['package_weight']) : doubleval(0.0);
                    $arr_canceled_trips['data'][$key]['package_quantity']  = isset($value['load_post_request_details']['load_post_request_package_details']['package_quantity']) ? intval($value['load_post_request_details']['load_post_request_package_details']['package_quantity']) : 0;

                    $load_post_image = '';
                    if(isset($value['load_post_request_details']['load_post_image']) && $value['load_post_request_details']['load_post_image']!=''){
                        if(file_exists($this->load_post_img_base_path.$value['load_post_request_details']['load_post_image'])){
                            $load_post_image = $this->load_post_img_public_path.$value['load_post_request_details']['load_post_image'];
                        }
                    }
                    $arr_canceled_trips['data'][$key]['load_post_image']  = $load_post_image;
                    unset($arr_canceled_trips['data'][$key]['review_details']);
                    unset($arr_canceled_trips['data'][$key]['load_post_request_details']);
                }
                else if($record_type == 'load_post')
                {
                    $arr_canceled_trips['data'][$key]['pickup_location']   = isset($value['pickup_location']) ? $value['pickup_location'] :'';
                    $arr_canceled_trips['data'][$key]['drop_location']     = isset($value['drop_location']) ? $value['drop_location'] :'';
                    $arr_canceled_trips['data'][$key]['pickup_lat']        = isset($value['pickup_lat']) ? doubleval($value['pickup_lat']) :doubleval(0.0);
                    $arr_canceled_trips['data'][$key]['pickup_lng']        = isset($value['pickup_lng']) ? doubleval($value['pickup_lng']) :doubleval(0.0);
                    $arr_canceled_trips['data'][$key]['drop_lat']          = isset($value['drop_lat']) ? doubleval($value['drop_lat']) :doubleval(0.0);
                    $arr_canceled_trips['data'][$key]['drop_lng']          = isset($value['drop_lng']) ? doubleval($value['drop_lng']) :doubleval(0.0);
                    
                    $arr_canceled_trips['data'][$key]['driver_lat']        = isset($value['driver_current_location_details']['current_latitude']) ? doubleval($value['driver_current_location_details']['current_latitude']) :doubleval(0.0);
                    $arr_canceled_trips['data'][$key]['driver_lng']        = isset($value['driver_current_location_details']['current_longitude']) ? doubleval($value['driver_current_location_details']['current_longitude']) :doubleval(0.0);

                    $arr_canceled_trips['data'][$key]['package_type']      = isset($value['load_post_request_package_details']['package_type']) ? $value['load_post_request_package_details']['package_type'] : '';
                    $arr_canceled_trips['data'][$key]['package_length']    = isset($value['load_post_request_package_details']['package_length']) ? doubleval($value['load_post_request_package_details']['package_length']) : doubleval(0.0);
                    $arr_canceled_trips['data'][$key]['package_breadth']   = isset($value['load_post_request_package_details']['package_breadth']) ? doubleval($value['load_post_request_package_details']['package_breadth']) : doubleval(0.0);
                    $arr_canceled_trips['data'][$key]['package_height']    = isset($value['load_post_request_package_details']['package_height']) ? doubleval($value['load_post_request_package_details']['package_height']) : doubleval(0.0);
                    $arr_canceled_trips['data'][$key]['package_weight']    = isset($value['load_post_request_package_details']['package_weight']) ? doubleval($value['load_post_request_package_details']['package_weight']) : doubleval(0.0);
                    $arr_canceled_trips['data'][$key]['package_quantity']  = isset($value['load_post_request_package_details']['package_quantity']) ? intval($value['load_post_request_package_details']['package_quantity']) : 0;

                    $load_post_image = '';
                    if(isset($value['load_post_image']) && $value['load_post_image']!=''){
                        if(file_exists($this->load_post_img_base_path.$value['load_post_image'])){
                            $load_post_image = $this->load_post_img_public_path.$value['load_post_image'];
                        }
                    }
                    $arr_canceled_trips['data'][$key]['load_post_image']  = $load_post_image;
                    unset($arr_canceled_trips['data'][$key]['load_post_request_package_details']);
                    unset($arr_canceled_trips['data'][$key]['user_details']);
                    unset($arr_canceled_trips['data'][$key]['driver_details']);
                }
            }
            $arr_response['status'] = 'success';
            $arr_response['msg']    = 'canceled trips available.';
            $arr_response['data']    = $arr_canceled_trips;
            return $arr_response;
        }
        $arr_response['status'] = 'error';
        $arr_response['msg']    = 'No canceled trips available.';
        $arr_response['data']    = [];
        return $arr_response;
    }

    /*
    |
    | user and driver will see trip details based on trip type.
    |
    */

    public function trip_details($request)
    {
        $user_id     = validate_user_jwt_token();
        if ($user_id == 0) 
        {
            $arr_response['status'] = 'error';
            $arr_response['msg']    = 'Invalid driver token';
            $arr_response['data']    = [];
            return $arr_response;
        }
        
        $user_type  = $request->input('user_type');
        $booking_id = $request->input('booking_id');
        $trip_type  = $request->input('trip_type');

        if ($booking_id == "") 
        {
            $arr_response['status'] = 'error';
            $arr_response['msg']    = 'Invalid booking ID';
            $arr_response['data']    = [];
            return $arr_response;
        }
        if ($trip_type == "") 
        {
            $arr_response['status'] = 'error';
            $arr_response['msg']    = 'Invalid request type';
            $arr_response['data']    = [];
            return $arr_response;
        }

        $arr_trip_details  = [];
        $obj_trip_details  = $this->BookingMasterModel
                                                ->select('id','load_post_request_id','booking_unique_id','booking_date','booking_status','po_no','receiver_name','receiver_no','app_suite')
                                                ->whereHas('load_post_request_details',function($query) {
                                                        $query->whereHas('driver_details',function($query){
                                                        });
                                                        $query->whereHas('user_details',function($query){
                                                        });
                                                })
                                                ->with(['load_post_request_details'=> function($query) use($user_id) {
                                                        $query->select('id','user_id','driver_id','pickup_location','drop_location','pickup_lat','pickup_lng','drop_lat','drop_lng','request_status');
                                                        $query->where('request_status','ACCEPT_BY_USER');
                                                        $query->with(['driver_details'=>function($query){
                                                                    $query->select('id','first_name','last_name','email','country_code','mobile_no','profile_image');
                                                                }]);
                                                        $query->with(['user_details'=>function($query){
                                                                    $query->select('id','first_name','last_name','email','country_code','mobile_no','profile_image');
                                                                }]);
                                                        $query->with(['load_post_request_package_details'=>function($query){
                                                                    // $query->select('id','first_name','last_name','email','mobile_no','profile_image');
                                                                }]);
                                                }])
                                                ->where('id',$booking_id)
                                                ->first();

        if($obj_trip_details)
        {
            $arr_trip_details = $obj_trip_details->toArray();
        }
        
        if(isset($arr_trip_details) && sizeof($arr_trip_details)>0)
        {
            $driver_fare_charge = 0;

            $arr_result                     = [];
            $arr_result['first_name']       = '';
            $arr_result['last_name']        = '';
            $arr_result['email']            = '';
            $arr_result['mobile_no']        = '';
            $arr_result['profile_image']    = '';
            $arr_result['booking_id']       = isset($arr_trip_details['id']) ? $arr_trip_details['id'] : 0 ;
            $arr_result['fair_charge']      = $driver_fare_charge;
            $arr_result['user_id']          = isset($arr_trip_details['load_post_request_details']['user_id']) ? strval($arr_trip_details['load_post_request_details']['user_id']) : '0';
            $arr_result['driver_id']        = isset($arr_trip_details['load_post_request_details']['driver_id']) ? strval($arr_trip_details['load_post_request_details']['driver_id']) : '0';
            $arr_result['pickup_location']  = isset($arr_trip_details['load_post_request_details']['pickup_location']) ? $arr_trip_details['load_post_request_details']['pickup_location'] :'';
            $arr_result['drop_location']    = isset($arr_trip_details['load_post_request_details']['drop_location']) ? $arr_trip_details['load_post_request_details']['drop_location'] :'';
            $arr_result['pickup_lat']       = isset($arr_trip_details['load_post_request_details']['pickup_lat']) ? doubleval($arr_trip_details['load_post_request_details']['pickup_lat']) :doubleval(0.0);
            $arr_result['pickup_lng']       = isset($arr_trip_details['load_post_request_details']['pickup_lng']) ? doubleval($arr_trip_details['load_post_request_details']['pickup_lng']) :doubleval(0.0);
            $arr_result['drop_lat']         = isset($arr_trip_details['load_post_request_details']['drop_lat']) ? doubleval($arr_trip_details['load_post_request_details']['drop_lat']) :doubleval(0.0);
            $arr_result['drop_lng']         = isset($arr_trip_details['load_post_request_details']['drop_lng']) ? doubleval($arr_trip_details['load_post_request_details']['drop_lng']) :doubleval(0.0);
            $arr_result['po_no']            = isset($arr_trip_details['po_no']) ? $arr_trip_details['po_no'] : '';
            $arr_result['app_suite']        = isset($arr_trip_details['app_suite']) ? $arr_trip_details['app_suite'] : '';
            $arr_result['receiver_name']    = isset($arr_trip_details['receiver_name']) ? $arr_trip_details['receiver_name'] : '';
            $arr_result['receiver_no']      = isset($arr_trip_details['receiver_no']) ? $arr_trip_details['receiver_no'] : '';
            $arr_result['package_type']     = isset($arr_trip_details['load_post_request_details']['load_post_request_package_details']['package_type']) ? $arr_trip_details['load_post_request_details']['load_post_request_package_details']['package_type'] : '';
            $arr_result['package_length']   = isset($arr_trip_details['load_post_request_details']['load_post_request_package_details']['package_length']) ? doubleval($arr_trip_details['load_post_request_details']['load_post_request_package_details']['package_length']) : doubleval(0.0);
            $arr_result['package_breadth']  = isset($arr_trip_details['load_post_request_details']['load_post_request_package_details']['package_breadth']) ? doubleval($arr_trip_details['load_post_request_details']['load_post_request_package_details']['package_breadth']) : doubleval(0.0);
            $arr_result['package_height']   = isset($arr_trip_details['load_post_request_details']['load_post_request_package_details']['package_height']) ? doubleval($arr_trip_details['load_post_request_details']['load_post_request_package_details']['package_height']) : doubleval(0.0);
            $arr_result['package_weight']   = isset($arr_trip_details['load_post_request_details']['load_post_request_package_details']['package_weight']) ? doubleval($arr_trip_details['load_post_request_details']['load_post_request_package_details']['package_weight']) : doubleval(0.0);
            $arr_result['package_quantity'] = isset($arr_trip_details['load_post_request_details']['load_post_request_package_details']['package_quantity']) ? intval($arr_trip_details['load_post_request_details']['load_post_request_package_details']['package_quantity']) : 0;

            if($user_type == 'DRIVER')
            {
                $arr_result['first_name']    = isset($arr_trip_details['load_post_request_details']['user_details']['first_name']) ? $arr_trip_details['load_post_request_details']['user_details']['first_name'] :'';
                $arr_result['last_name']     = isset($arr_trip_details['load_post_request_details']['user_details']['last_name']) ? $arr_trip_details['load_post_request_details']['user_details']['last_name'] :'';
                $arr_result['email']         = isset($arr_trip_details['load_post_request_details']['user_details']['email']) ? $arr_trip_details['load_post_request_details']['user_details']['email'] : '';
                
                $country_code   = isset($arr_trip_details['load_post_request_details']['user_details']['country_code']) ? $arr_trip_details['load_post_request_details']['user_details']['country_code'] : '';
                $mobile_no      = isset($arr_trip_details['load_post_request_details']['user_details']['mobile_no']) ? $arr_trip_details['load_post_request_details']['user_details']['mobile_no'] : '';
                $full_mobile_no = $country_code.''.$mobile_no;
                $full_mobile_no = ($full_mobile_no!='')?$full_mobile_no :'';

                $arr_result['mobile_no'] = $full_mobile_no;

                // $arr_result['mobile_no']     = isset($arr_trip_details['load_post_request_details']['user_details']['mobile_no']) ? $arr_trip_details['load_post_request_details']['user_details']['mobile_no'] : '';

                $profile_image = '';
                if(isset($arr_trip_details['load_post_request_details']['user_details']['profile_image']) && $arr_trip_details['load_post_request_details']['user_details']['profile_image']!=''){
                    if(file_exists($this->user_profile_base_img_path.$arr_trip_details['load_post_request_details']['user_details']['profile_image'])){
                        $profile_image = $this->user_profile_public_img_path.$arr_trip_details['load_post_request_details']['user_details']['profile_image'];
                    }
                }
                
                $arr_result['profile_image'] = $profile_image;

            }
            if($user_type == 'USER')
            {
                $arr_result['first_name']    = isset($arr_trip_details['load_post_request_details']['driver_details']['first_name']) ? $arr_trip_details['load_post_request_details']['driver_details']['first_name'] :'';
                $arr_result['last_name']     = isset($arr_trip_details['load_post_request_details']['driver_details']['last_name']) ? $arr_trip_details['load_post_request_details']['driver_details']['last_name'] :'';
                $arr_result['email']         = isset($arr_trip_details['load_post_request_details']['driver_details']['email']) ? $arr_trip_details['load_post_request_details']['driver_details']['email'] : '';

                $country_code   = isset($arr_trip_details['load_post_request_details']['driver_details']['country_code']) ? $arr_trip_details['load_post_request_details']['driver_details']['country_code'] : '';
                $mobile_no      = isset($arr_trip_details['load_post_request_details']['driver_details']['mobile_no']) ? $arr_trip_details['load_post_request_details']['driver_details']['mobile_no'] : '';
                $full_mobile_no = $country_code.''.$mobile_no;
                $full_mobile_no = ($full_mobile_no!='')?$full_mobile_no :'';

                $arr_result['mobile_no'] = $full_mobile_no;

                // $arr_result['mobile_no']     = isset($arr_trip_details['load_post_request_details']['driver_details']['mobile_no']) ? $arr_trip_details['load_post_request_details']['driver_details']['mobile_no'] : '';

                $profile_image = '';
                if(isset($arr_trip_details['load_post_request_details']['driver_details']['profile_image']) && $arr_trip_details['load_post_request_details']['driver_details']['profile_image']!=''){
                    if(file_exists($this->user_profile_base_img_path.$arr_trip_details['load_post_request_details']['driver_details']['profile_image'])){
                        $profile_image = $this->user_profile_public_img_path.$arr_trip_details['load_post_request_details']['driver_details']['profile_image'];
                    }
                }
                
                $arr_result['profile_image'] = $profile_image;
                
                $driver_id                 = isset($arr_trip_details['load_post_request_details']['driver_id']) ? $arr_trip_details['load_post_request_details']['driver_id'] : 0;
                $driver_fare_charge        = $this->CommonDataService->get_driver_fair_charge($driver_id);
                $arr_result['fair_charge'] = $driver_fare_charge;

            }
            $arr_response['status'] = 'success';
            $arr_response['msg']    = 'trip details found.';
            $arr_response['data']    = $arr_result;
            return $arr_response;
        }
        $arr_response['status'] = 'error';
        $arr_response['msg']    = 'No ongoing trip details available.';
        $arr_response['data']    = [];
        return $arr_response;
    }

    /*
    |
    | track driver current location details
    |
    */
    public function track_driver($request)
    {
        $user_id     = validate_user_jwt_token();
        if ($user_id == 0) 
        {
            $arr_response['status'] = 'error';
            $arr_response['msg']    = 'Invalid driver token';
            $arr_response['data']    = [];
            return $arr_response;
        }
        
        $booking_id = $request->input('booking_id');

        if ($booking_id == "") 
        {
            $arr_response['status'] = 'error';
            $arr_response['msg']    = 'Invalid booking ID';
            $arr_response['data']    = [];
            return $arr_response;
        }

        $arr_ongoing_trips = [];
        $obj_ongoing_trips  = $this->BookingMasterModel
                                                ->select('id','load_post_request_id','booking_status')
                                                ->whereHas('load_post_request_details',function($query) use($user_id) {
                                                        $query->whereHas('driver_current_location_details',function($query){
                                                        });
                                                        $query->where('request_status','ACCEPT_BY_USER');
                                                        $query->where('user_id',$user_id);
                                                })
                                                ->with(['load_post_request_details'=> function($query) use($user_id) {
                                                        $query->select('id','driver_id');
                                                        $query->where('request_status','ACCEPT_BY_USER');
                                                        $query->where('user_id',$user_id);
                                                        $query->with(['driver_current_location_details'=>function($query){
                                                                    $query->select('id','driver_id','status','current_latitude','current_longitude');
                                                                }]);
                                                }])
                                                ->where('id',$booking_id)
                                                ->first();

        if($obj_ongoing_trips)
        {
            $arr_ongoing_trips = $obj_ongoing_trips->toArray();
        }
        if(isset($arr_ongoing_trips) && sizeof($arr_ongoing_trips)>0){
            
            $arr_ongoing_trips['booking_id']   = isset($arr_ongoing_trips['id']) ? $arr_ongoing_trips['id'] : 0 ;
            $arr_ongoing_trips['driver_lat']   = isset($arr_ongoing_trips['load_post_request_details']['driver_current_location_details']['current_latitude']) ? doubleval($arr_ongoing_trips['load_post_request_details']['driver_current_location_details']['current_latitude']) :doubleval(0.0);
            $arr_ongoing_trips['driver_lng']   = isset($arr_ongoing_trips['load_post_request_details']['driver_current_location_details']['current_longitude']) ? doubleval($arr_ongoing_trips['load_post_request_details']['driver_current_location_details']['current_longitude']) :doubleval(0.0);

            unset($arr_ongoing_trips['id']);
            unset($arr_ongoing_trips['load_post_request_id']);
            unset($arr_ongoing_trips['load_post_request_details']);
            
            $arr_response['status'] = 'success';
            $arr_response['msg']    = 'track driver details found.';
            $arr_response['data']    = $arr_ongoing_trips;
            return $arr_response;

        }
        $arr_response['status'] = 'error';
        $arr_response['msg']    = 'No track driver details available.';
        $arr_response['data']    = [];
        return $arr_response;
    }

    /*
    |
    | process ongoing trip status by user and driver
    |
    */
    public function process_trip_status($request,$client)
    {
        $login_user_id     = validate_user_jwt_token();
        if ($login_user_id == 0) 
        {
            $arr_response['status'] = 'error';
            $arr_response['msg']    = 'Invalid token';
            $arr_response['data']    = [];
            return $arr_response;
        }
        
        $booking_id        = $request->input('booking_id');
        $user_type         = $request->input('user_type');
        $booking_status    = $request->input('booking_status');
        $reason            = $request->input('reason');
        
        if ($booking_id == '' || $user_type == '' || $booking_status == '') 
        {
            $arr_response['status'] = 'error';
            $arr_response['msg']    = 'Invalid required details.';
            $arr_response['data']   = [];
            return $arr_response;
        }
        
        $arr_response = [];
        
        $arr_driver_status = ['IN_TRANSIT','COMPLETED','CANCEL_BY_DRIVER'];
        if($user_type == 'DRIVER' && in_array($booking_status, $arr_driver_status) ){
            
            if($booking_status == 'IN_TRANSIT'){

                $lat       = $request->input('lat');
                $lng       = $request->input('lng');
                $is_update = $request->input('is_update');

                $invoice_image = '';
                if($request->file('invoice_image')){
                    $invoice_image = $request->file('invoice_image'); 
                }
                $arr_tmp = 
                            [
                                'login_user_id'    => $login_user_id,
                                'booking_id'       => $booking_id,
                                'booking_status'   => $booking_status,
                                'start_trip_image' => $request->file('start_trip_image'),
                                'invoice_image'    => $invoice_image,
                                'lat'              => $lat,
                                'lng'              => $lng,
                                'client'           => $client, /*object of twillo client*/
                                'is_update'        => $is_update
                            ];

                $arr_response = $this->process_in_transit_trip_status_by_driver($arr_tmp);
                return $arr_response;
            }
            else if($booking_status == 'COMPLETED'){

                $distance            = $request->input('distance');
                $booking_coordinates = $request->input('booking_coordinates');
                $lat                 = $request->input('lat');
                $lng                 = $request->input('lng');
                $is_update           = $request->input('is_update');

                $arr_tmp = 
                            [
                                'login_user_id'       => $login_user_id,
                                'booking_id'          => $booking_id,
                                'booking_status'      => $booking_status,
                                'distance'            => $distance,
                                'booking_coordinates' => $booking_coordinates,
                                'end_trip_image' => $request->file('end_trip_image'),
                                'lat'              => $lat,
                                'lng'              => $lng,
                                'client'           => $client, /*object of twillo client*/
                                'is_update'        => $is_update
                            ];

                $arr_response = $this->process_completed_trip_status_by_driver($arr_tmp);
                return $arr_response;
            } 
            else if($booking_status == 'CANCEL_BY_DRIVER'){

                $arr_tmp = 
                            [
                                'login_user_id'  => $login_user_id,
                                'booking_id'     => $booking_id,
                                'booking_status' => $booking_status,
                                'reason'         => $reason,
                                'client'           => $client /*object of twillo client*/
                            ];

                $arr_response = $this->process_cancel_trip_status_by_driver($arr_tmp);
                return $arr_response;
            } 
            
            $arr_response['status'] = 'error';
            $arr_response['msg']    = 'Problem occurred, while processing update booking details request,Please try again.';
            $arr_response['data']    = [];
            return $arr_response;
        }
        else if($user_type == 'USER'){
            
            if($booking_status == 'CANCEL_BY_USER'){

                $arr_tmp = 
                            [
                                'login_user_id'  => $login_user_id,
                                'booking_id'     => $booking_id,
                                'booking_status' => $booking_status,
                                'reason'         => $reason
                            ];

                $arr_response = $this->process_cancel_trip_status_by_user($arr_tmp);
                return $arr_response;
            }
            $arr_response['status'] = 'error';
            $arr_response['msg']    = 'Problem occurred, while processing update booking details request,Please try again.';
            $arr_response['data']    = [];
            return $arr_response;
        }
        $arr_response['status'] = 'error';
        $arr_response['msg']    = 'Problem occurred, while processing update booking details request';
        $arr_response['data']    = [];
        return $arr_response;
    }

    /*
    |
    | process ongoing trip status by user and driver
    |
    */
    public function payment_receipt($request)
    {
        $login_user_id     = validate_user_jwt_token();
        if ($login_user_id == 0) 
        {
            $arr_response['status'] = 'error';
            $arr_response['msg']    = 'Invalid user token';
            $arr_response['data']    = [];
            return $arr_response;
        }
        
        $booking_id        = $request->input('booking_id');
        $user_type         = $request->input('user_type');
        
        if ($booking_id == '' || $user_type == '') 
        {
            $arr_response['status'] = 'error';
            $arr_response['msg']    = 'Invalid required details.';
            $arr_response['data']   = [];
            return $arr_response;
        }

        if($user_type == 'USER')
        {
            $arr_response = $this->process_user_payment_receipt($booking_id);
            return $arr_response;
        }
        else if($user_type == 'DRIVER')
        {
            $arr_response = $this->process_driver_payment_receipt($booking_id);
            return $arr_response;
        }
        
        $arr_response['status'] = 'error';
        $arr_response['msg']    = 'Something went wrong,Please try again.';
        $arr_response['data']   = [];
        return $arr_response;
    }

    private function process_user_payment_receipt($booking_id)
    {
        $arr_booking_master = $arr_response = $arr_data = [];
        
        $arr_response['status'] = 'error';
        $arr_response['msg']    = 'Payment receipt details not found.';
        $arr_response['data']   = [];

        $obj_booking_master = $this->BookingMasterModel
                                    ->with(['load_post_request_details'=>function($query){
                                        $query->with(['driver_details'=>function($query){
                                            $query->select('id','first_name','last_name','email','mobile_no','profile_image','is_company_driver','company_name');
                                            $query->with('driver_car_details.vehicle_details');

                                        }]);
                                        $query->with(['user_details'=>function($query){
                                            $query->select('id','first_name','last_name','email','mobile_no','profile_image');
                                        }]);
                                        $query->with('vehicle_details.vehicle_type_details');

                                    }])
                                    ->where('id',$booking_id)
                                    ->first();

        if($obj_booking_master)
        {
            $arr_booking_master = $obj_booking_master->toArray();
            if(isset($arr_booking_master) && sizeof($arr_booking_master)>0)
            {
                $arr_data = filter_completed_trip_details($arr_booking_master);
                
                if(isset($arr_data) && sizeof($arr_data)>0)
                {
                    $arr_result = [];
                    $arr_result['id']                 = isset($arr_data['booking_id']) ? $arr_data['booking_id'] : '';
                    $arr_result['booking_unique_id']  = isset($arr_data['booking_unique_id']) ? $arr_data['booking_unique_id'] : '';
                    $arr_result['po_no']              = isset($arr_data['po_no']) ? $arr_data['po_no'] : '';
                    $arr_result['receiver_name']      = isset($arr_data['receiver_name']) ? $arr_data['receiver_name'] : '';
                    $arr_result['receiver_no']        = isset($arr_data['receiver_no']) ? $arr_data['receiver_no'] : '';
                    $arr_result['app_suite']        = isset($arr_data['app_suite']) ? $arr_data['app_suite'] : '';
                    $arr_result['booking_date']       = isset($arr_data['booking_date']) ? $arr_data['booking_date'] : '';
                    $arr_result['distance']           = isset($arr_data['distance']) ? number_format($arr_data['distance'],2) : '0,0';
                    $arr_result['total_minutes_trip'] = isset($arr_data['total_minutes_trip']) ? $arr_data['total_minutes_trip'] : ''; 
                    
                    $fair_charge = number_format(0);

                    if(isset($arr_data['per_miles_price']) && $arr_data['per_miles_price'] != '')
                    {
                        $fair_charge = isset($arr_data['per_miles_price']) ? number_format($arr_data['per_miles_price'],2) : '0.0';
                    }
                    $arr_result['fair_charge']          = $fair_charge;

                    
                    $arr_result['total_amount']      = isset($arr_data['total_amount']) ? number_format($arr_data['total_amount'],2) : '0,0';
                    $arr_result['discount_amount']   = isset($arr_data['applied_promo_code_charge']) ? number_format($arr_data['applied_promo_code_charge'],2) : '0,0';
                    $arr_result['final_amount']      = isset($arr_data['total_charge']) ? number_format($arr_data['total_charge'],2) : '0,0';

                    $arr_result['is_bonus_used']      = isset($arr_data['is_bonus_used']) ? $arr_data['is_bonus_used'] : '';
                    $arr_result['points_used']        = isset($arr_data['user_bonus_points']) ? number_format($arr_data['user_bonus_points'],0) : '0';
                    $arr_result['points_usd_amount']  = isset($arr_data['user_bonus_points_usd_amount']) ? number_format($arr_data['user_bonus_points_usd_amount'],2) : '0';
                                        
                    $payment_status = 'UNPAID';
                    if(isset($arr_data['payment_status']) && $arr_data['payment_status'] == 'SUCCESS')
                    {
                        $payment_status = 'PAID';
                    }
                    $arr_result['payment_status']    = $payment_status;
                    
                    $arr_response['status'] = 'success';
                    $arr_response['msg']    = 'Payment receipt details found.';
                    $arr_response['data']   = $arr_result;
                }
            }
        }
        return $arr_response;
    }

    private function process_driver_payment_receipt($booking_id)
    {
        $arr_booking_master = $arr_response = $arr_data = [];
        
        $arr_response['status'] = 'error';
        $arr_response['msg']    = 'Payment receipt details not found.';
        $arr_response['data']   = [];

        $obj_booking_master = $this->BookingMasterModel
                                    ->with(['load_post_request_details'=>function($query){
                                        $query->with(['driver_details'=>function($query){
                                            $query->select('id','first_name','last_name','email','mobile_no','profile_image','is_company_driver','company_name');
                                            $query->with('driver_car_details.vehicle_details');

                                        }]);
                                        $query->with(['user_details'=>function($query){
                                            $query->select('id','first_name','last_name','email','mobile_no','profile_image');
                                        }]);
                                        $query->with('vehicle_details.vehicle_type_details');

                                    }])
                                    ->where('id',$booking_id)
                                    ->first();

        if($obj_booking_master)
        {
            $arr_booking_master = $obj_booking_master->toArray();
            if(isset($arr_booking_master) && sizeof($arr_booking_master)>0)
            {
                $arr_data = filter_completed_trip_details($arr_booking_master);
                if(isset($arr_data) && sizeof($arr_data)>0)
                {
                    $arr_result                          = [];
                    $arr_result['id']                    = isset($arr_data['booking_id']) ? $arr_data['booking_id'] : '';
                    $arr_result['booking_unique_id']     = isset($arr_data['booking_unique_id']) ? $arr_data['booking_unique_id'] : '';
                    $arr_result['po_no']                 = isset($arr_data['po_no']) ? $arr_data['po_no'] : '';
                    $arr_result['receiver_name']         = isset($arr_data['receiver_name']) ? $arr_data['receiver_name'] : '';
                    $arr_result['receiver_no']           = isset($arr_data['receiver_no']) ? $arr_data['receiver_no'] : '';
                    $arr_result['app_suite']             = isset($arr_data['app_suite']) ? $arr_data['app_suite'] : '';
                    $arr_result['booking_date']          = isset($arr_data['booking_date']) ? $arr_data['booking_date'] : '';
                    $arr_result['distance']              = isset($arr_data['distance']) ? number_format($arr_data['distance'],2) : '0.0';
                    $arr_result['total_minutes_trip']    = isset($arr_data['total_minutes_trip']) ? $arr_data['total_minutes_trip'] : ''; 
                    // $arr_result['is_individual_vehicle'] = isset($arr_data['is_individual_vehicle']) ? $arr_data['is_individual_vehicle'] : '0';
                    // $arr_result['is_company_driver']     = isset($arr_data['is_company_driver']) ? $arr_data['is_company_driver'] : '0';
                    
                    $arr_result['fair_charge']          = '0';
                    // $arr_result['admin_commission']            = '0';
                    // $arr_result['admin_per_kilometer_charge']  = '0';
                    // $arr_result['driver_per_kilometer_charge'] = '0';
                    $fair_charge = number_format(0);

                    if(isset($arr_data['per_miles_price']) && $arr_data['per_miles_price'] != '')
                    {
                        $fair_charge = isset($arr_data['per_miles_price']) ? number_format($arr_data['per_miles_price'],2) : '0.0';
                    }
                    $arr_result['fair_charge']          = $fair_charge;

                    $arr_result['fair_charge']       = $fair_charge;
                    $arr_result['total_amount']      = isset($arr_data['total_amount']) ? number_format($arr_data['total_amount'],2) : '0.0';
                    $arr_result['discount_amount']   = isset($arr_data['applied_promo_code_charge']) ? number_format($arr_data['applied_promo_code_charge'],2) : '0.0';
                    $arr_result['final_amount']      = isset($arr_data['total_charge']) ? number_format($arr_data['total_charge'],2) : '0.0';

                    $arr_result['is_bonus_used']      = isset($arr_data['is_bonus_used']) ? $arr_data['is_bonus_used'] : '';
                    $arr_result['points_used']        = isset($arr_data['user_bonus_points']) ? number_format($arr_data['user_bonus_points'],0) : '0';
                    $arr_result['points_usd_amount']  = isset($arr_data['user_bonus_points_usd_amount']) ? number_format($arr_data['user_bonus_points_usd_amount'],2) : '0';
                    
                    /*$arr_result['admin_commission_amount']   = '0.0';
                    $arr_result['admin_earning_amount']      = '0.0';
                    $arr_result['driver_earning_amount']     = '0.0';
                    $arr_result['driver_commission_amount']  = '0.0';
                    $arr_result['company_earning_amount']    = '0.0';

                    if(isset($arr_data['is_individual_vehicle']) && $arr_data['is_individual_vehicle'] == '1')
                    {
                        $arr_result['admin_commission_amount'] = isset($arr_data['admin_earning_amount']) ? number_format($arr_data['admin_earning_amount'],2) : '0.0';
                        $arr_result['driver_earning_amount'] = isset($arr_data['driver_earning_amount']) ? number_format($arr_data['driver_earning_amount'],2) : '0.0';
                    }
                    elseif(isset($arr_data['is_individual_vehicle']) && $arr_data['is_individual_vehicle'] == '0')
                    {
                        if(isset($arr_data['is_company_driver']) && $arr_data['is_company_driver'] == '1')
                        {
                            $arr_result['admin_commission_amount'] = isset($arr_data['admin_earning_amount']) ? number_format($arr_data['admin_earning_amount'],2) : '0.0';
                            $arr_result['driver_earning_amount']  = isset($arr_data['driver_earning_amount']) ? number_format($arr_data['driver_earning_amount'],2) : '0.0';
                            $arr_result['company_earning_amount']  = isset($arr_data['company_earning_amount']) ? number_format($arr_data['company_earning_amount'],2) : '0.0';

                        }
                        elseif(isset($arr_data['is_company_driver']) && $arr_data['is_company_driver'] == '0')
                        {
                            $arr_result['admin_earning_amount'] = isset($arr_data['admin_earning_amount']) ? number_format($arr_data['admin_earning_amount'],2) : '0.0';
                            $arr_result['driver_commission_amount'] = isset($arr_data['driver_earning_amount']) ? number_format($arr_data['driver_earning_amount'],2) : '0.0';
                        }
                    } */

                    $payment_status = 'UNPAID';
                    if(isset($arr_data['payment_status']) && $arr_data['payment_status'] == 'SUCCESS')
                    {
                        $payment_status = 'PAID';
                    }
                    $arr_result['payment_status']    = $payment_status;
                    
                    $arr_response['status'] = 'success';
                    $arr_response['msg']    = 'Payment receipt details found.';
                    $arr_response['data']   = $arr_result;
                }
            }
        }
        return $arr_response;
    }
    /*
    |******************************************************************
    |
    | class specific functions are listed below
    |
    |******************************************************************
    */

    /*
    |
    | When user post new load then finding nearby drivers which matching given critera
    |
    */ 
    private function search_nearby_drivers($arr_data = [])
    {   
        if(isset($arr_data) && sizeof($arr_data)>0){

            $pickup_lat       = isset($arr_data['pickup_lat']) ? $arr_data['pickup_lat'] :0;
            $pickup_lng       = isset($arr_data['pickup_lng']) ? $arr_data['pickup_lng'] :0;
            $drop_lat         = isset($arr_data['drop_lat']) ? $arr_data['drop_lat'] :0;
            $drop_lng         = isset($arr_data['drop_lng']) ? $arr_data['drop_lng'] :0;
            $package_type     = isset($arr_data['package_type']) ? $arr_data['package_type'] :'';
            $package_quantity = isset($arr_data['package_quantity']) ? $arr_data['package_quantity'] :0;
            $package_volume   = isset($arr_data['package_volume']) ? $arr_data['package_volume'] :0;
            $package_weight   = isset($arr_data['package_weight']) ? $arr_data['package_weight'] :0;
            
            $distance             = isset($arr_data['distance']) ? $arr_data['distance'] :0;
            $load_post_request_id = isset($arr_data['load_post_request_id']) ? $arr_data['load_post_request_id'] :0;
            $str_except_driver_id = isset($arr_data['str_except_driver_id']) ? $arr_data['str_except_driver_id'] :'';

            $origins      = $pickup_lat.",".$pickup_lng;
            $destinations = $drop_lat.",".$drop_lng;

            $arr_calculate_distance = $this->calculate_distance($origins,$destinations);

            $trip_distance   = isset($arr_calculate_distance['distance']) ? $arr_calculate_distance['distance'] :'';
            $trip_duration   = isset($arr_calculate_distance['duration']) ? $arr_calculate_distance['duration'] :'';
            $actual_distance = isset($arr_calculate_distance['actual_distance']) ? $arr_calculate_distance['actual_distance'] :0;

            /*convert meter to miles*/
            if($actual_distance>0){
                $actual_distance = ($actual_distance/1609.344);
                $actual_distance = round($actual_distance,2);
            }   

            $arr_vehicle_types_required_data = 
                                                [
                                                    'package_type'     => $package_type,
                                                    'package_quantity' => intval($package_quantity),
                                                    'package_volume'   => $package_volume,
                                                    'package_weight'   => $package_weight
                                                ];      

            $arr_vehicle_type  = $this->CommonDataService->get_all_next_vehicle_types_against_load($arr_vehicle_types_required_data);
            
            if(isset($arr_vehicle_type) && count($arr_vehicle_type)>0)
            {
                foreach ($arr_vehicle_type as $vt_key => $vt_value) 
                {
                    $arr_vehicle_type[$vt_key]['trip_distance'] = $trip_distance;
                    $arr_vehicle_type[$vt_key]['trip_duration'] = $trip_duration;

                    $actual_distance = isset($arr_calculate_distance['actual_distance']) ? $arr_calculate_distance['actual_distance'] :0;
                    $per_miles_price = isset($vt_value['per_miles_price']) ? floatval($vt_value['per_miles_price']) : 0;

                    /*convert meter to miles*/
                    if($actual_distance>0){
                        $actual_distance = ($actual_distance/1609.344);
                        $actual_distance = round($actual_distance,2);
                    }   
                    
                    $calculated_farecharge = 0;

                    $calculated_farecharge = ($actual_distance * $per_miles_price); 

                    $arr_vehicle_type[$vt_key]['calculated_farecharge'] = doubleval($calculated_farecharge);

                    unset($arr_vehicle_type[$vt_key]['vehicle_min_volume']);
                    unset($arr_vehicle_type[$vt_key]['vehicle_max_volume']);
                    unset($arr_vehicle_type[$vt_key]['vehicle_min_weight']);
                    unset($arr_vehicle_type[$vt_key]['vehicle_max_weight']);
                    
                    $vehicle_type_id = isset($vt_value['id'])  ? $vt_value['id'] : 0;

                    $sql_query = '';
                    $sql_query .= "Select ";
                    $sql_query .= "users.id AS driver_id, ";
                    $sql_query .= "CONCAT( users.first_name, ' ',users.last_name ) AS driver_name, ";
                    $sql_query .= "VT.id AS vehicle_type_original_id, ";
                    $sql_query .= "V.id AS vehicle_id, ";
                    $sql_query .= "V.vehicle_type_id, ";
                    $sql_query .= "V.is_verified AS is_verified, ";
                    $sql_query .= "driver_available_status.current_latitude, ";
                    $sql_query .= "driver_available_status.current_longitude, ";
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

                    if($str_except_driver_id!='')
                    {
                        $sql_query .=  "users.id NOT IN ( '" . $str_except_driver_id . "' ) AND ";
                    }

                    $sql_query .=  "users.is_active = '1' AND ";
                    $sql_query .=  "roles.slug = 'driver' AND ";
                    $sql_query .=  "users.availability_status  = 'ONLINE' AND "; /* added by padmashri*/
                    $sql_query .=  "users.stripe_account_id  != '' AND "; /* added by padmashri*/
                    $sql_query .=  "driver_available_status.status = 'AVAILABLE' AND ";
                    $sql_query .=  " ( driver_car_relation.is_car_assign = '1' OR driver_car_relation.is_individual_vehicle = '1' ) AND ";
                    $sql_query .=  "VT.id = ".$vehicle_type_id." ";

                    $sql_query .=  "HAVING distance <=".$distance;
                   
                    $obj_driver_details =  \DB::select($sql_query);
                    
                    $arr_driver_details = json_decode(json_encode($obj_driver_details), true);
                    
                    if(isset($arr_driver_details) && sizeof($arr_driver_details)>0)
                    {
                        /*
                        |manually fliter data also remove driver which does not verify their indivital vechiles 
                        |also remove drivers which are in restricted area.
                        */
                        foreach ($arr_driver_details as $key => $driver_details) 
                        {
                            if( isset($driver_details['is_individual_vehicle']) && $driver_details['is_individual_vehicle'] == '1')
                            {
                                if(isset($driver_details['is_verified']) && $driver_details['is_verified'] == '0')
                                {
                                    unset($arr_driver_details[$key]);
                                }
                            }
                            unset($arr_driver_details[$key]['vehicle_type_original_id']);
                            unset($arr_driver_details[$key]['vehicle_id']);
                            unset($arr_driver_details[$key]['vehicle_type_id']);
                            unset($arr_driver_details[$key]['is_verified']);
                            unset($arr_driver_details[$key]['status']);
                            unset($arr_driver_details[$key]['is_individual_vehicle']);
                            // unset($arr_driver_details[$key]['distance']);

                        }
                    }
                    
                    if(count($arr_driver_details)>0)
                    {
                        $arr_driver_details = array_values($arr_driver_details);
                        $arr_vehicle_type[$vt_key]['driver_count']       = count($arr_driver_details);
                        $arr_vehicle_type[$vt_key]['arr_driver_details'] = $arr_driver_details;
                    }
                    else
                    {
                        /*if driver count is 0 then remove the vehicle type category details form the main array*/
                        unset($arr_vehicle_type[$vt_key]);
                    }
                }
            }

            return $arr_vehicle_type;
        }      
    }
    /*
    |
    | When user post new load then finding nearby drivers which matching given critera
    |
    */ 
    private function search_and_send_notification_to_nearby_drivers($arr_data = [])
    {   
        if(isset($arr_data) && sizeof($arr_data)>0){

            $pickup_lat           = isset($arr_data['pickup_lat']) ? $arr_data['pickup_lat'] :0;
            $pickup_lng           = isset($arr_data['pickup_lng']) ? $arr_data['pickup_lng'] :0;
            $distance             = isset($arr_data['distance']) ? $arr_data['distance'] :0;
            $vehicle_type_id      = isset($arr_data['vehicle_type_id']) ? $arr_data['vehicle_type_id'] :0;
            $load_post_request_id = isset($arr_data['load_post_request_id']) ? $arr_data['load_post_request_id'] :0;
            $str_except_driver_id = isset($arr_data['str_except_driver_id']) ? $arr_data['str_except_driver_id'] :'';

            $sql_query = '';
            $sql_query .= "Select ";
            $sql_query .= "users.id AS driver_id, ";
            $sql_query .= "CONCAT( users.first_name, ' ',users.last_name ) AS driver_name, ";
            $sql_query .= "VT.id AS vehicle_type_original_id, ";
            $sql_query .= "V.id AS vehicle_id, ";
            $sql_query .= "V.vehicle_type_id, ";
            $sql_query .= "V.is_verified AS is_verified, ";
            $sql_query .= "driver_available_status.current_latitude, ";
            $sql_query .= "driver_available_status.current_longitude, ";
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

            if($str_except_driver_id!='')
            {
                $sql_query .=  "users.id NOT IN ( '" . $str_except_driver_id . "' ) AND ";
            }

            $sql_query .=  "users.is_active = '1' AND ";
            $sql_query .=  "roles.slug = 'driver' AND ";
            $sql_query .=  "users.availability_status  = 'ONLINE' AND "; /* added by padmashri*/
            $sql_query .=  "users.stripe_account_id  != '' AND "; /* added by padmashri*/
            $sql_query .=  "driver_available_status.status = 'AVAILABLE' AND ";
            $sql_query .=  " ( driver_car_relation.is_car_assign = '1' OR driver_car_relation.is_individual_vehicle = '1' ) AND ";
            $sql_query .=  "VT.id = ".$vehicle_type_id." ";

            $sql_query .=  "HAVING distance <=".$distance;
           
            $obj_driver_details =  \DB::select($sql_query);
            
            if(isset($obj_driver_details) && sizeof($obj_driver_details)>0)
            {
                /*
                |manually fliter data also remove driver which does not verify their indivital vechiles 
                |also remove drivers which are in restricted area.
                */
                foreach ($obj_driver_details as $key => $driver_details) 
                {
                    if( isset($driver_details->is_individual_vehicle) && $driver_details->is_individual_vehicle == '1')
                    {
                        if(isset($driver_details->is_verified) && $driver_details->is_verified == '0')
                        {
                            unset($obj_driver_details[$key]);
                        }
                    }
                    unset($obj_driver_details[$key]->vehicle_type_original_id);
                    unset($obj_driver_details[$key]->vehicle_id);
                    unset($obj_driver_details[$key]->vehicle_type_id);
                    unset($obj_driver_details[$key]->is_verified);
                    unset($obj_driver_details[$key]->status);
                    unset($obj_driver_details[$key]->is_individual_vehicle);
                    unset($obj_driver_details[$key]->distance);
                }
                return json_decode(json_encode($obj_driver_details), true);
            }
        }  
        return [];    
    }
    /*
    |
    | private class function to parase driver data and send filter notification data
    |
    */
    private function send_notification_to_drivers($arr_driver_details,$load_post_request_id)
    {
        if(isset($arr_driver_details) && sizeof($arr_driver_details)>0){
            foreach ($arr_driver_details as $key => $driver_details) {
                if(isset($driver_details)){
                    
                    $enc_user_id = isset($driver_details['driver_id']) ? $driver_details['driver_id'] :0;
                    
                    $arr_notification_data = 
                                            [
                                                'title'             => 'Trip requested by customer',
                                                'notification_type' => 'USER_REQUEST',
                                                'record_id'         => $load_post_request_id,
                                                'is_admin_assign'   => '0',
                                                'enc_user_id'       => $enc_user_id,
                                                'user_type'         => 'DRIVER',

                                            ];

                    $this->send_on_signal_notification($arr_notification_data);

                    $arr_notification_data = 
                                            [
                                                'title'             => 'Trip requested by customer',
                                                'notification_type' => 'USER_REQUEST',
                                                'record_id'         => $load_post_request_id,
                                                'is_admin_assign'   => '0',
                                                'enc_user_id'       => $enc_user_id,
                                                'user_type'         => 'WEB',

                                            ];

                    $this->send_on_signal_notification($arr_notification_data);

                }
            }
        }   
        return true;
    }

    /*
    |
    | private class function to send notification to user and driver
    |
    */
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
                                "is_admin_assign"   => isset($arr_notification_data['is_admin_assign']) ? $arr_notification_data['is_admin_assign'] :'0',
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
    /*
    |
    | When driver recive notification on new job post, and driver click on circle or timeout that time this metho called. 
    |
    */
    private function process_request_by_driver($login_user_id,$load_post_request_id,$request_status,$is_admin_assign,$client)
    {
        if($login_user_id!='' && $load_post_request_id!='' && $request_status!='')
        {
            $driver_id = $login_user_id;
            /*check for load post status if 'accept_by_user' or not*/
            $obj_load_post_request = $this->LoadPostRequestModel/*->with('load_post_request_history_details')*/
                                                    ->with(['load_post_request_history_details'=>function($query) use($driver_id){
                                                        $query->where(function($query){
                                                            $query->where('status','=','REJECT_BY_DRIVER');
                                                            // $query->orwhere('status','=','TIMEOUT');
                                                        });
                                                        // $query->where('status','=','REJECT_BY_DRIVER');
                                                        $query->where('driver_id',$driver_id);

                                                    }])
                                                    ->with(['user_details'=>function($query){
                                                        $query->select('id','first_name','last_name','country_code','mobile_no');
                                                    }])
                                                    ->where('id',$load_post_request_id)
                                                    // ->whereIn('request_status',['USER_REQUEST','REJECT_BY_USER'])
                                                    ->first();
            
            if(isset($obj_load_post_request) && $obj_load_post_request!=null)
            {
                if(isset($obj_load_post_request->request_status) && $obj_load_post_request->request_status!=''){
                    
                    /*check if user already rejected load post request*/
                    if($obj_load_post_request->request_status == 'CANCEL_BY_USER')
                    {
                        $arr_response['status'] = 'error';
                        $arr_response['msg']    = 'Sorry for inconvenience, customer canceled the trip.';
                        $arr_response['data']   = [];
                        return $arr_response;
                    }

                    /*check if trip has been already accepted by another driver*/
                    if($obj_load_post_request->request_status == 'ACCEPT_BY_DRIVER')
                    {
                        $arr_response['status'] = 'error';
                        $arr_response['msg']    = 'Sorry for inconvenience, shipment request has been already accepted by another driver.';
                        $arr_response['data']   = [];
                        return $arr_response;
                    }

                    $arr_required_status = ['USER_REQUEST','REJECT_BY_USER','REJECT_BY_DRIVER','TIMEOUT'];
                    
                    if(in_array($obj_load_post_request->request_status, $arr_required_status)){
                        /*requested driver already rejected request so cannot process further need to check*/
                        if(isset($obj_load_post_request->load_post_request_history_details) && sizeof($obj_load_post_request->load_post_request_history_details)>0){
                            $arr_response['status'] = 'error';
                            $arr_response['msg']    = 'You have already rejected this request, cannot process further';
                            $arr_response['data']   = [];
                            return $arr_response;
                        }
                        
                        $enc_user_id = isset($obj_load_post_request->user_id) ? $obj_load_post_request->user_id :0;

                        if($request_status == 'ACCEPT_BY_DRIVER')
                        {
                            $is_previous_load_post_accepted_status_pending =  $this->check_user_previous_load_post_accepted_status($enc_user_id);
                            
                            if($is_previous_load_post_accepted_status_pending>0)
                            {
                                $arr_response['status'] = 'error';
                                $arr_response['msg']    = 'Currently,Customer is busy,Please try again after some time.';
                                $arr_response['data']   = [];
                                return $arr_response;
                            }
                            $obj_load_post_request->driver_id      = $driver_id;
                            $obj_load_post_request->request_status = 'ACCEPT_BY_DRIVER';
                            
                            $driver_status = 'BUSY';
                            $this->CommonDataService->change_driver_status($driver_id,$driver_status);

                            // $this->search_drivers_to_make_available($load_post_request_id,$driver_id); 

                            /*if single driver accept then make all another drivers available*/
                        }
                        else
                        {
                            $obj_load_post_request->driver_id = 0;
                            $obj_load_post_request->request_status = $request_status;

                            $driver_status = 'AVAILABLE';
                            $this->CommonDataService->change_driver_status($driver_id,$driver_status);
                        }

                        
                        $status = $obj_load_post_request->save();
                        if($status){
                            $arr_load_post_request_history = [];
                            $arr_load_post_request_history['load_post_request_id'] = $load_post_request_id;
                            $arr_load_post_request_history['user_id']              = 0;
                            $arr_load_post_request_history['driver_id']            = $driver_id;
                            $arr_load_post_request_history['status']               = $request_status;
                            $arr_load_post_request_history['is_admin_assign']      = $is_admin_assign;
                            $arr_load_post_request_history['reason']               = '';

                            $this->LoadPostRequestHistoryModel->create($arr_load_post_request_history);
                         
                            if($request_status == 'ACCEPT_BY_DRIVER')
                            {
                                //send twilio sms eta notifications
                                if(isset($client)){
                                    //send sms to user
                                    $user_country_code   = isset($obj_load_post_request->user_details->country_code) ? $obj_load_post_request->user_details->country_code :'';
                                    $user_mobile_no      = isset($obj_load_post_request->user_details->mobile_no) ? $obj_load_post_request->user_details->mobile_no :'';
                                    $user_full_mobile_no = $user_country_code.''.$user_mobile_no;

                                    if($user_full_mobile_no!=''){
                                        
                                        $arr_driver_details = $this->get_driver_details($login_user_id);
                                        $driver_first_name  = isset($arr_driver_details['first_name']) ? $arr_driver_details['first_name'] :'';
                                        $driver_last_name   = isset($arr_driver_details['last_name']) ? $arr_driver_details['last_name'] :'';
                                        $driver_full_name   = $driver_first_name.' '.$driver_last_name;

                                        $pickup_lat  = isset($obj_load_post_request->pickup_lat) ? $obj_load_post_request->pickup_lat : '';
                                        $pickup_lng  = isset($obj_load_post_request->pickup_lng) ? $obj_load_post_request->pickup_lng : '';

                                        $driver_latitude  = isset($arr_driver_details['lat']) ? $arr_driver_details['lat'] : '';
                                        $driver_longitude = isset($arr_driver_details['lng']) ? $arr_driver_details['lng'] : '';

                                        $driver_origins      = $driver_latitude.",".$driver_longitude;
                                        $driver_destinations = $pickup_lat.",".$pickup_lng;
                                        
                                        $arr_driver_distance = $this->calculate_distance($driver_origins,$driver_destinations);
                                        $driver_duration = isset($arr_driver_distance['duration']) ? $arr_driver_distance['duration'] :'';

                                        $messageBody         = 'Your trip has been accepted by '.$driver_full_name.' and has an eta of '.$driver_duration.' to the pickup location.';

                                        $this->sendEtaNotificationsMessage(
                                                                $client,
                                                                $user_full_mobile_no,
                                                                $messageBody,
                                                                ''
                                                            );
                                    }                 
                                }

                                /*Send on signal notification to user that driver accepted your request*/
                                $arr_notification_data = 
                                                        [
                                                            'title'             => 'New shipment post request accepted by driver',
                                                            'record_id'         => $load_post_request_id,
                                                            'enc_user_id'       => $enc_user_id,
                                                            'notification_type' => 'ACCEPT_BY_DRIVER',
                                                            'user_type'         => 'USER',
                                                        ];

                                $this->send_on_signal_notification($arr_notification_data);

                                /*Send on signal notification to user that driver accepted your request*/
                                $arr_notification_data = 
                                                        [
                                                            'title'             => 'New shipment post request accepted by driver',
                                                            'record_id'         => $load_post_request_id,
                                                            'enc_user_id'       => $enc_user_id,
                                                            'notification_type' => 'ACCEPT_BY_DRIVER',
                                                            'user_type'         => 'WEB',
                                                        ];

                                $this->send_on_signal_notification($arr_notification_data);


                                /*Send on signal notification to driver which is loged in the web panel*/
                                $arr_notification_data = 
                                                        [
                                                            'title'             => 'New shipment post request accepted by you',
                                                            'record_id'         => $load_post_request_id,
                                                            'enc_user_id'       => $driver_id,
                                                            'notification_type' => 'ACCEPT_BY_DRIVER',
                                                            'user_type'         => 'WEB',
                                                        ];

                                $this->send_on_signal_notification($arr_notification_data);

                            }
                            if($request_status == 'REJECT_BY_DRIVER')
                            {
                                /*Send on signal notification to driver which is loged in the web panel*/
                                $arr_notification_data = 
                                                        [
                                                            'title'             => 'New shipment post request rejected by you',
                                                            'record_id'         => $load_post_request_id,
                                                            'enc_user_id'       => $driver_id,
                                                            'notification_type' => 'REJECT_BY_DRIVER',
                                                            'user_type'         => 'WEB',
                                                        ];

                                $this->send_on_signal_notification($arr_notification_data);
                            }

                            // $arr_driver_details = $this->CommonDataService->get_driver_details($driver_id);
                            $arr_driver_details = [];
                            $response_msg = '';
                            if($request_status == 'ACCEPT_BY_DRIVER'){
                                $arr_driver_details['load_post_request_id'] = $load_post_request_id;
                                $response_msg = 'You have accepted the trip. Once the customer confirms, you may begin.';
                            }
                            else if($request_status == 'REJECT_BY_DRIVER'){
                                $response_msg = 'You have rejected shipment post request.';
                            }
                            else if($request_status == 'TIMEOUT'){
                                $response_msg = 'Shipment Request is timeout, you can check that shipment in request list if it is not accepted by another driver.';
                            }
                            $arr_response['status'] = 'success';
                            $arr_response['msg']    = $response_msg;
                            $arr_response['data']   = $arr_driver_details;
                            return $arr_response;

                        }else{
                            $arr_response['status'] = 'error';
                            $arr_response['msg']    = 'Problem occurred, while processing shipment post request';
                            $arr_response['data']   = [];
                            return $arr_response;
                        }
                    }
                    else{
                        
                        $request_status    = isset($obj_load_post_request->request_status) ? $obj_load_post_request->request_status :'';
                        $request_driver_id = isset($obj_load_post_request->driver_id) ? $obj_load_post_request->driver_id :'';
                        
                        $message = '';

                        if($request_status == 'ACCEPT_BY_USER'){
                            $message = 'Shipment post request is already In-Progress cannot process further.';
                        }
                        
                        if($request_driver_id == $driver_id){
                            
                            if($request_status == 'ACCEPT_BY_DRIVER'){
                            $message = 'Shipment post request already accepted by you,cannot process this trip again.';
                            } 
                            if($request_status == 'REJECT_BY_DRIVER'){
                                $message = 'Trip No Longer Avalible! you have already rejected this trip request.';
                            }
                        }
                        else{

                            if($request_status == 'ACCEPT_BY_DRIVER'){
                            $message = 'Trip No Longer Avalible! Another driver has accepted this trip request.';
                            } 
                            if($request_status == 'REJECT_BY_DRIVER'){
                                $message = 'Trip No Longer Avalible! you have already rejected this trip request.';
                            }   
                        }
                        $arr_response['status'] = 'error';
                        $arr_response['msg']    = $message;
                        $arr_response['data']   = [];
                        return $arr_response;
                    }
                }else{
                    $arr_response['status'] = 'error';
                    $arr_response['msg']    = 'Problem occurred, while processing shipment post request';
                    $arr_response['data']   = [];
                    return $arr_response;   
                }
            }
            $arr_response['status'] = 'error';
            $arr_response['msg']    = 'Problem occurred, while processing shipment post request';
            $arr_response['data']   = [];
            return $arr_response;
        }
        $arr_response['status'] = 'error';
        $arr_response['msg']    = 'Problem occurred, while processing load post request';
        $arr_response['data']   = [];
        return $arr_response;
    }
    
    private function check_user_previous_load_post_accepted_status($user_id)
    {
        $date = new \DateTime();        
        $date->modify('-1 hours');
        $formatted_date = $date->format('Y-m-d H:i:s');
        
        $obj_pending_previous_load_post_count = $this->LoadPostRequestModel
                                                    ->where('user_id',$user_id)
                                                    ->where('date', '>',$formatted_date) /*latest 1 hours records will be shown */
                                                    ->where('request_status','ACCEPT_BY_DRIVER')
                                                    ->count();

        return $obj_pending_previous_load_post_count;

    }
    /*
    |
    | When request is accepted by driver and then user reject drivers acceted request then this method will call.
    |
    */
    private function process_accept_request_by_user($login_user_id,$load_post_request_id,$request_status,$arr_booking_data)
    {
        if($login_user_id!='' && $load_post_request_id!='' && $request_status!='')
        {
            $user_id = $login_user_id;
            /*check for load post status if 'accept_by_user' or not*/
            $obj_load_post_request = $this->LoadPostRequestModel
                                                    ->where('id',$load_post_request_id)
                                                    ->first();
        
            if(isset($obj_load_post_request) && $obj_load_post_request!=null)
            {
                if(isset($obj_load_post_request->request_status) && $obj_load_post_request->request_status!='ACCEPT_BY_DRIVER')
                {
                    $arr_response['status'] = 'not_valid';
                    $arr_response['msg']    = 'Sorry for inconvenience, this request is already processed.';
                    $arr_response['data']   = [];
                    return $arr_response;
                }
                
                $driver_id = isset($obj_load_post_request->driver_id) ? $obj_load_post_request->driver_id :0;
                $driver_vehicle_id = $this->CommonDataService->get_driver_vehicle_id($driver_id);
                
                $card_id = isset($obj_load_post_request->card_id) ? $obj_load_post_request->card_id :0;
                $total_cent_charge = (round(1) * 100);

                $arr_card_details = [
                                      "card_id"           => $card_id,
                                      "user_id"           => $login_user_id,
                                      'total_charge'      => $total_cent_charge
                                    ];
                

                $arr_stripe_response = $this->StripeService->charge_customer_initial_payment($arr_card_details);
                
                /*initial payment charge success*/

                if(isset($arr_stripe_response['status']) && $arr_stripe_response['status'] == 'success')
                {
                    $charge_id = isset($arr_stripe_response['charge_id']) ? $arr_stripe_response['charge_id'] : '';

                    $arr_stripe_refund_response = $this->StripeService->refund_customer_initial_payment($charge_id);
                    
                    /*refund initial payment success*/
                    if(isset($arr_stripe_refund_response['status']) && $arr_stripe_refund_response['status'] == 'success')
                    {
                        $obj_load_post_request->request_status = 'ACCEPT_BY_USER';
                        $obj_load_post_request->vehicle_id     = $driver_vehicle_id;
                        $status = $obj_load_post_request->save();

                        if($status){
                            $arr_load_post_request_history['load_post_request_id'] = $load_post_request_id;
                            $arr_load_post_request_history['user_id']              = $user_id;
                            $arr_load_post_request_history['driver_id']            = 0;
                            $arr_load_post_request_history['status']               = 'ACCEPT_BY_USER';
                            $arr_load_post_request_history['reason']               = '';

                            $this->LoadPostRequestHistoryModel->create($arr_load_post_request_history);

                            $arr_response = $this->store_booking_master_details($load_post_request_id,$arr_booking_data);
                            return $arr_response;

                        }else{
                            $arr_response['status'] = 'error';
                            $arr_response['msg']    = 'Problem occurred, while processing shipment post request, Please try again.';
                            $arr_response['data']   = [];
                            return $arr_response;
                        }
                    }
                    else
                    {
                        $obj_load_post_request->driver_id      = 0;
                        $obj_load_post_request->request_status = 'CANCEL_BY_USER';
                        $status = $obj_load_post_request->save();

                        $driver_status = 'AVAILABLE';
                        $this->CommonDataService->change_driver_status($driver_id,$driver_status);

                        $arr_load_post_request_history = [];
                        $arr_load_post_request_history['load_post_request_id'] = $load_post_request_id;
                        $arr_load_post_request_history['user_id']              = $user_id;
                        $arr_load_post_request_history['driver_id']            = $driver_id;
                        $arr_load_post_request_history['status']               = 'REJECT_BY_USER';
                        $arr_load_post_request_history['reason']               = '';

                        $this->LoadPostRequestHistoryModel->create($arr_load_post_request_history);
                        
                        /*if user reject driver request then the particular driver will not able to accept request again. so make entry in database REJECT_BY_DRIVER*/
                        $arr_load_post_request_history = [];
                        $arr_load_post_request_history['load_post_request_id'] = $load_post_request_id;
                        $arr_load_post_request_history['user_id']              = 0;
                        $arr_load_post_request_history['driver_id']            = $driver_id;
                        $arr_load_post_request_history['status']               = 'REJECT_BY_DRIVER';
                        $arr_load_post_request_history['reason']               = '';

                        $this->LoadPostRequestHistoryModel->create($arr_load_post_request_history);

                        /*Send on signal notification to user that driver accepted your request*/
                        $arr_notification_data = 
                                                [
                                                    'title'             => 'Customer cancel shipment post request.',
                                                    'record_id'         => $load_post_request_id,
                                                    'enc_user_id'       => $driver_id,
                                                    'notification_type' => 'REJECT_BY_USER',
                                                    'user_type'         => 'DRIVER',
                                                ];

                        $this->send_on_signal_notification($arr_notification_data);

                        /*Send on signal notification to user that driver accepted your request*/
                        $arr_notification_data = 
                                                [
                                                    'title'             => 'Customer cancel shipment post request.',
                                                    'record_id'         => $load_post_request_id,
                                                    'enc_user_id'       => $driver_id,
                                                    'notification_type' => 'REJECT_BY_USER',
                                                    'user_type'         => 'WEB',
                                                ];

                        $this->send_on_signal_notification($arr_notification_data);

                        /*Send on signal notification to user website user*/
                        $arr_notification_data = 
                                                [
                                                    'title'             => 'Customer cancel shipment post request.',
                                                    'record_id'         => $load_post_request_id,
                                                    'enc_user_id'       => $login_user_id,
                                                    'notification_type' => 'REJECT_BY_USER',
                                                    'user_type'         => 'WEB',
                                                ];

                        $this->send_on_signal_notification($arr_notification_data);
                        
                        $arr_response['status'] = 'not_valid';
                        $arr_response['msg']    = isset($arr_stripe_refund_response['msg']) ?$arr_stripe_refund_response['msg'] : '';
                        $arr_response['data']   = [];
                        return $arr_response;       
                    }
                }
                else
                {
                    $obj_load_post_request->driver_id      = 0;
                    $obj_load_post_request->request_status = 'CANCEL_BY_USER';
                    $status = $obj_load_post_request->save();

                    $driver_status = 'AVAILABLE';
                    $this->CommonDataService->change_driver_status($driver_id,$driver_status);

                    $arr_load_post_request_history = [];
                    $arr_load_post_request_history['load_post_request_id'] = $load_post_request_id;
                    $arr_load_post_request_history['user_id']              = $user_id;
                    $arr_load_post_request_history['driver_id']            = $driver_id;
                    $arr_load_post_request_history['status']               = 'REJECT_BY_USER';
                    $arr_load_post_request_history['reason']               = '';

                    $this->LoadPostRequestHistoryModel->create($arr_load_post_request_history);
                    
                    /*if user reject driver request then the particular driver will not able to accept request again. so make entry in database REJECT_BY_DRIVER*/
                    $arr_load_post_request_history = [];
                    $arr_load_post_request_history['load_post_request_id'] = $load_post_request_id;
                    $arr_load_post_request_history['user_id']              = 0;
                    $arr_load_post_request_history['driver_id']            = $driver_id;
                    $arr_load_post_request_history['status']               = 'REJECT_BY_DRIVER';
                    $arr_load_post_request_history['reason']               = '';

                    $this->LoadPostRequestHistoryModel->create($arr_load_post_request_history);

                    /*Send on signal notification to user that driver accepted your request*/
                    $arr_notification_data = 
                                            [
                                                'title'             => 'Customer cancel shipment post request.',
                                                'record_id'         => $load_post_request_id,
                                                'enc_user_id'       => $driver_id,
                                                'notification_type' => 'REJECT_BY_USER',
                                                'user_type'         => 'DRIVER',
                                            ];

                    $this->send_on_signal_notification($arr_notification_data);

                    /*Send on signal notification to driver website user*/
                    $arr_notification_data = 
                                            [
                                                'title'             => 'Customer cancel shipment post request.',
                                                'record_id'         => $load_post_request_id,
                                                'enc_user_id'       => $driver_id,
                                                'notification_type' => 'REJECT_BY_USER',
                                                'user_type'         => 'WEB',
                                            ];

                    $this->send_on_signal_notification($arr_notification_data);

                    /*Send on signal notification to user website user*/
                    $arr_notification_data = 
                                            [
                                                'title'             => 'Customer cancel shipment post request.',
                                                'record_id'         => $load_post_request_id,
                                                'enc_user_id'       => $login_user_id,
                                                'notification_type' => 'REJECT_BY_USER',
                                                'user_type'         => 'WEB',
                                            ];

                    $this->send_on_signal_notification($arr_notification_data);

                    $arr_response['status'] = 'not_valid';
                    $arr_response['msg']    = isset($arr_stripe_response['msg']) ? $arr_stripe_response['msg'] : '';
                    $arr_response['data']   = [];
                    return $arr_response;
                }

            }
            $arr_response['status'] = 'error';
            $arr_response['msg']    = 'Problem occurred, while processing request, Please try again.';
            $arr_response['data']   = [];
            return $arr_response;
        }
        $arr_response['status'] = 'error';
        $arr_response['msg']    = 'Problem occurred, while processing request, Please try again.';
        $arr_response['data']   = [];
        return $arr_response;   
    }

    /*
    |
    | When user click on specific driver then this function is called
    |
    */ 
    public function process_to_book_driver_request_by_user($arr_required_data)
    {
        $login_user_id        = isset($arr_required_data['login_user_id']) ? $arr_required_data['login_user_id'] : '';
        $vehicle_type_id      = isset($arr_required_data['vehicle_type_id']) ? $arr_required_data['vehicle_type_id'] : '';
        $load_post_request_id = isset($arr_required_data['load_post_request_id']) ? $arr_required_data['load_post_request_id'] : '';
        $request_status       = isset($arr_required_data['request_status']) ? $arr_required_data['request_status'] : '';
        $is_future_request    = isset($arr_required_data['is_future_request']) ? $arr_required_data['is_future_request'] : '';
        $future_request_date  = isset($arr_required_data['future_request_date']) ? $arr_required_data['future_request_date'] : '';
        $request_time         = isset($arr_required_data['request_time']) ? $arr_required_data['request_time'] : '';
        
        $arr_available_drivers = [];

        if($login_user_id!='' && $vehicle_type_id!='' && $load_post_request_id!='' && $request_status!='')
        {
            $user_id = $login_user_id;
            /*check for load post status if 'accept_by_user' or not*/
            $obj_load_post_request = $this->LoadPostRequestModel
                                                    ->where('id',$load_post_request_id)
                                                    ->first();
        
            if(isset($obj_load_post_request) && $obj_load_post_request!=null)
            {
                if(isset($obj_load_post_request->request_status) && $obj_load_post_request->request_status == 'ACCEPT_BY_USER'){
                    $arr_response['status'] = 'error';
                    $arr_response['msg']    = 'You have already accepted this request, cannot book driver,Please try again.';
                    $arr_response['data']   = [];
                    return $arr_response;
                }

                if(isset($obj_load_post_request->request_status) && $obj_load_post_request->request_status == 'ACCEPT_BY_DRIVER'){
                    $arr_response['status'] = 'error';
                    $arr_response['msg']    = 'Driver already accepted this request, cannot book driver,Please try again.';
                    $arr_response['data']   = [];
                    return $arr_response;
                }

                $arr_required_status = ['NEW_REQUEST','REJECT_BY_DRIVER','TIMEOUT'];

                if(isset($obj_load_post_request->request_status) && (!in_array($obj_load_post_request->request_status, $arr_required_status))){
                    $arr_response['status'] = 'error';
                    $arr_response['msg']    = 'Sorry for the inconvenience,this shipment request is already processed.';
                    $arr_response['data']   = [];
                    return $arr_response;
                }
                
                /*if booking request is not a future booking then not need to check drivers avalible currently*/
                if($is_future_request == '0')
                {
                    $pickup_lat = isset($obj_load_post_request->pickup_lat) ? $obj_load_post_request->pickup_lat : 0;
                    $pickup_lng = isset($obj_load_post_request->pickup_lng) ? $obj_load_post_request->pickup_lng : 0;
                    $str_except_driver_id = $this->get_user_driver_id($user_id);
                    $arr_driver_search_data = 
                                            [
                                                'pickup_lat'           => $pickup_lat,
                                                'pickup_lng'           => $pickup_lng,
                                                'vehicle_type_id'      => $vehicle_type_id,
                                                'distance'             => $this->distance,
                                                'load_post_request_id' => $load_post_request_id,
                                                'str_except_driver_id' => strval($str_except_driver_id)
                                            ];

                    $arr_available_drivers = $this->search_and_send_notification_to_nearby_drivers($arr_driver_search_data);

                    if(count($arr_available_drivers)<=0)
                    {
                        $arr_response['status'] = 'error';
                        $arr_response['msg']    = 'Sorry for the inconvenience,currently drivers are not available.';
                        $arr_response['data']   = [];
                        return $arr_response;
                    }
                }

                $obj_load_post_request->request_status    = 'USER_REQUEST';
                $obj_load_post_request->is_future_request = $is_future_request;
                
                if($is_future_request == '1' && $future_request_date!='' && $request_time!='')
                {
                    $obj_load_post_request->date = $future_request_date;
                    $obj_load_post_request->request_time = $request_time;
                }
                $status = $obj_load_post_request->save();
                if($status){

                    $arr_load_post_request_history = [];
                    $arr_load_post_request_history['load_post_request_id'] = $load_post_request_id;
                    $arr_load_post_request_history['user_id']              = $user_id;
                    $arr_load_post_request_history['driver_id']            = 0;
                    $arr_load_post_request_history['status']               = 'USER_REQUEST';
                    $arr_load_post_request_history['reason']               = '';

                    $this->LoadPostRequestHistoryModel->create($arr_load_post_request_history);
                    
                    $msg = '';
                    if($is_future_request == '0')
                    {
                        $msg = 'Shipment request successfully send to all nearby drivers,waiting for driver acceptance.';
                        $this->send_notification_to_drivers($arr_available_drivers,$load_post_request_id);
                    }
                    else if($is_future_request == '1')
                    {
                        $msg = 'Future Shipment request successfully processed, you will be notify soon.';
                    }

                    $arr_response['status'] = 'success';
                    $arr_response['msg']    = $msg;
                    $arr_response['data']   = [];
                    return $arr_response;
                        

                }else{
                    $arr_response['status'] = 'error';
                    $arr_response['msg']    = 'Problem occurred, while processing request, Please try again.';
                    $arr_response['data']   = [];
                    return $arr_response;
                }


                
            }
            $arr_response['status'] = 'error';
            $arr_response['msg']    = 'Problem occurred, while processing request, Please try again.';
            $arr_response['data']   = [];
            return $arr_response;
        }
        $arr_response['status'] = 'error';
        $arr_response['msg']    = 'Problem occurred, while book driver, Please try again.';
        $arr_response['data']   = [];
        return $arr_response;   
    }
    /*
    |
    | When user click on specific driver then this function is called
    |
    */ 
    public function load_post_all_driver_details($request)
    {
        $user_id     = validate_user_jwt_token();
        
        if ($user_id == 0) 
        {
            $arr_response['status'] = 'error';
            $arr_response['msg']    = 'Invalid driver token';
            $arr_response['data']    = [];
            return $arr_response;
        }

        $load_post_request_id = $request->input('load_post_request_id');
        
        if($load_post_request_id == '')
        {
            $arr_response['status'] = 'error';
            $arr_response['msg']    = 'Invalid load post token,cannot process request.';
            $arr_response['data']    = [];
            return $arr_response;
        }
        
        $obj_load_post_request = $this->LoadPostRequestModel
                                                ->with(['load_post_request_package_details','user_details','driver_details','driver_current_location_details'])
                                                ->where('id',$load_post_request_id)
                                                ->first();

        if(isset($obj_load_post_request) && $obj_load_post_request!=null)
        {
            $arr_load_post_request = $obj_load_post_request->toArray();

            if(isset($arr_load_post_request) && sizeof($arr_load_post_request)>0){

                $str_except_driver_id = $this->get_user_driver_id($user_id);

                $arr_driver_search_data = 
                                    [
                                        'pickup_lat'           => isset($arr_load_post_request['pickup_lat']) ? doubleval($arr_load_post_request['pickup_lat']) : floatval(0),
                                        'pickup_lng'           => isset($arr_load_post_request['pickup_lng']) ? doubleval($arr_load_post_request['pickup_lng']) : floatval(0),
                                        'package_type'         => isset($arr_load_post_request['load_post_request_package_details']['package_type']) ? $arr_load_post_request['load_post_request_package_details']['package_type'] : '',
                                        'package_quantity'     => isset($arr_load_post_request['load_post_request_package_details']['package_quantity']) ? intval($arr_load_post_request['load_post_request_package_details']['package_quantity']) : 0,
                                        'package_volume'       => isset($arr_load_post_request['load_post_request_package_details']['package_volume']) ? $arr_load_post_request['load_post_request_package_details']['package_volume'] : 0,
                                        'package_weight'       => isset($arr_load_post_request['load_post_request_package_details']['package_weight']) ? $arr_load_post_request['load_post_request_package_details']['package_weight'] : 0,
                                        'distance'             => $this->distance,
                                        'load_post_request_id' => $load_post_request_id,
                                        'str_except_driver_id' => strval($str_except_driver_id)
                                    ];

                $arr_driver_vehicle_type = $this->search_nearby_drivers($arr_driver_search_data);
                $arr_result = [];
                $arr_result['load_post_request_id'] = intval($load_post_request_id);
                $arr_result['arr_driver_vehicle_type'] = $arr_driver_vehicle_type;

                $arr_response['status'] = 'success';
                $arr_response['msg']    = 'Driver details found successfully.';
                $arr_response['data']   = $arr_result;
                return $arr_response;   

                
            }
            else
            {
                $arr_response['status'] = 'error';
                $arr_response['msg']    = 'Problem occurred, fetching driver details,Please try again.';
                $arr_response['data']   = [];
                return $arr_response;   
            }
        }
        $arr_response['status'] = 'error';
        $arr_response['msg']    = 'Problem occurred, fetching driver details,Please try again.';
        $arr_response['data']   = [];
        return $arr_response;

    }
    
    /*
    |
    | When request is accepted by driver and then user reject drivers acceted request then this method will call.
    |
    */   
    private function process_reject_request_by_user($login_user_id,$load_post_request_id,$request_status)
    {
        if($login_user_id!='' && $load_post_request_id!='' && $request_status!='')
        {
            $user_id = $login_user_id;
            /*check for load post status if 'accept_by_user' or not*/
            $obj_load_post_request = $this->LoadPostRequestModel
                                                    ->where('id',$load_post_request_id)
                                                    ->first();
        
            if(isset($obj_load_post_request) && $obj_load_post_request!=null)
            {
                $enc_user_id = isset($obj_load_post_request->driver_id) ? $obj_load_post_request->driver_id :0;

                if(isset($obj_load_post_request->request_status) && $obj_load_post_request->request_status == 'ACCEPT_BY_USER'){
                    $arr_response['status'] = 'error';
                    $arr_response['msg']    = 'You have already accepted this request, cannot reject shipment request,Please try again.';
                    $arr_response['data']   = [];
                    return $arr_response;
                }
                $obj_load_post_request->driver_id      = 0;
                $obj_load_post_request->request_status = 'REJECT_BY_USER';
                $status = $obj_load_post_request->save();
                
                if($status){
                    
                    $driver_status = 'AVAILABLE';
                    $this->CommonDataService->change_driver_status($enc_user_id,$driver_status);

                    $arr_load_post_request_history = [];
                    $arr_load_post_request_history['load_post_request_id'] = $load_post_request_id;
                    $arr_load_post_request_history['user_id']              = $user_id;
                    $arr_load_post_request_history['driver_id']            = $enc_user_id;
                    $arr_load_post_request_history['status']               = 'REJECT_BY_USER';
                    $arr_load_post_request_history['reason']               = '';

                    $this->LoadPostRequestHistoryModel->create($arr_load_post_request_history);
                    
                    /*if user reject driver request then the particular driver will not able to accept request again. so make entry in database REJECT_BY_DRIVER*/
                    $arr_load_post_request_history = [];
                    $arr_load_post_request_history['load_post_request_id'] = $load_post_request_id;
                    $arr_load_post_request_history['user_id']              = 0;
                    $arr_load_post_request_history['driver_id']            = $enc_user_id;
                    $arr_load_post_request_history['status']               = 'REJECT_BY_DRIVER';
                    $arr_load_post_request_history['reason']               = '';

                    $this->LoadPostRequestHistoryModel->create($arr_load_post_request_history);

                    /*Send on signal notification to user that driver accepted your request*/
                    $arr_notification_data = 
                                            [
                                                'title'             => 'Customer rejected shipment post request.',
                                                'record_id'         => $load_post_request_id,
                                                'enc_user_id'       => $enc_user_id,
                                                'notification_type' => 'REJECT_BY_USER',
                                                'user_type'         => 'DRIVER',
                                            ];

                    $this->send_on_signal_notification($arr_notification_data);

                    /*Send on signal notification to user that user rejected your request*/
                    $arr_notification_data = 
                                            [
                                                'title'             => 'Customer rejected shipment post request.',
                                                'record_id'         => $load_post_request_id,
                                                'enc_user_id'       => $login_user_id,
                                                'notification_type' => 'REJECT_BY_USER',
                                                'user_type'         => 'WEB',
                                            ];

                    $this->send_on_signal_notification($arr_notification_data);

                    /*Send on signal notification to driver that he reject driver*/
                    $arr_notification_data = 
                                            [
                                                'title'             => 'You have rejected driver.',
                                                'record_id'         => $load_post_request_id,
                                                'enc_user_id'       => $enc_user_id,
                                                'notification_type' => 'REJECT_BY_USER',
                                                'user_type'         => 'WEB',
                                            ];

                    $this->send_on_signal_notification($arr_notification_data);

                    $arr_response['status'] = 'success';
                    $arr_response['msg']    = 'You have rejected driver.';
                    $arr_response['data']   = [];
                    return $arr_response;

                }else{
                    $arr_response['status'] = 'error';
                    $arr_response['msg']    = 'Problem occurred, while processing shipment post request, Please try again.';
                    $arr_response['data']   = [];
                    return $arr_response;
                }
            }
            $arr_response['status'] = 'error';
            $arr_response['msg']    = 'Problem occurred, while processing request, Please try again.';
            $arr_response['data']   = [];
            return $arr_response;
        }
        $arr_response['status'] = 'error';
        $arr_response['msg']    = 'Problem occurred, while processing request, Please try again.';
        $arr_response['data']   = [];
        return $arr_response;   
    }
    
    /*
    |
    | When user accept the driver acceptance then actual booking details entry will be insert in booking table and further process countinue.
    |
    */  
    private function store_booking_master_details($load_post_request_id,$arr_booking_data)
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

                $is_individual_vehicle = $this->CommonDataService->check_is_individual_vehicle_from_driver_car_relation($driver_id);

                $arr_is_individual_vehicle = $this->CommonDataService->check_is_individual_vehicle($vehicle_id);

                $company_id                = isset($arr_is_individual_vehicle['company_id']) ? $arr_is_individual_vehicle['company_id'] :'0';
                //$is_individual_vehicle     = isset($arr_is_individual_vehicle['is_individual_vehicle']) ? $arr_is_individual_vehicle['is_individual_vehicle'] :'0';
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
                
                /*if($is_individual_vehicle == '1')
                {
                    if($promo_percentage>$individual_driver_percentage){
                        $promo_percentage = $individual_driver_percentage;
                    }
                }
                else
                {
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
                }*/
                
                $arr_booking_master                                        = [];
                $arr_booking_master['load_post_request_id']                = $load_post_request_id;
                $arr_booking_master['booking_unique_id']                   = $this->genrate_booking_unique_number();
                $arr_booking_master['booking_date']                        = date('Y-m-d');
                $arr_booking_master['start_datetime']                      = date('Y-m-d H:i:s');
                $arr_booking_master['end_datetime']                        = null;
                $arr_booking_master['card_id']                             = $card_id;
                $arr_booking_master['po_no']                               = isset($arr_booking_data['po_no']) ? $arr_booking_data['po_no'] : '';
                $arr_booking_master['receiver_name']                       = isset($arr_booking_data['receiver_name']) ? $arr_booking_data['receiver_name'] : '';
                $arr_booking_master['receiver_no']                         = isset($arr_booking_data['receiver_no']) ? $arr_booking_data['receiver_no'] : '';
                $arr_booking_master['app_suite']                           = isset($arr_booking_data['app_suite']) ? $arr_booking_data['app_suite'] : '';
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
                    
                    // /*if user applied promo code then maintain history*/
                    // if($is_promo_code_applied == 'YES'){
                    //     $this->PromoOfferAppliedDetailsModel->create([  
                    //                                         'promo_code_id'      => $promo_code_id,
                    //                                         'user_id'            => $user_id,
                    //                                         'promo_applied_date' => date('Y-m-d')
                    //                                     ]);
                    // }


                    $booking_master_id = isset($obj_booking_master->id) ? $obj_booking_master->id :0;

                    /*Send on signal notification to user that driver accepted your request*/
                    $arr_notification_data = 
                                            [
                                                'title'             => 'Customer approved your request.',
                                                'record_id'         => $booking_master_id,
                                                'enc_user_id'       => $driver_id,
                                                'notification_type' => 'ACCEPT_BY_USER',
                                                'user_type'         => 'DRIVER',
                                            ];

                    $this->send_on_signal_notification($arr_notification_data);

                    /*Send on signal notification to user that driver accepted your request*/
                    $arr_notification_data = 
                                            [
                                                'title'             => 'Customer approved your request.',
                                                'record_id'         => $booking_master_id,
                                                'enc_user_id'       => $driver_id,
                                                'notification_type' => 'ACCEPT_BY_USER',
                                                'user_type'         => 'WEB',
                                            ];

                    $this->send_on_signal_notification($arr_notification_data);

                    /*Send on signal notification to User that he accept driver*/
                    $arr_notification_data = 
                                            [
                                                'title'             => 'Booking details successfully saved, now you can track ongoing ride details.',
                                                'record_id'         => $load_post_request_id,
                                                'enc_user_id'       => $user_id,
                                                'notification_type' => 'ACCEPT_BY_USER',
                                                'user_type'         => 'WEB',
                                            ];

                    $this->send_on_signal_notification($arr_notification_data);

                    $arr_tmp_data = 
                                    [
                                        'booking_master_id' => $booking_master_id,
                                        'driver_id'         => strval($driver_id)
                                    ];
                    
                    $arr_response['status'] = 'success';
                    $arr_response['msg']    = 'Booking details successfully saved, now you can track ongoing ride details.';
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

    /*
    |
    | When driver change the trip status to in_transit then this method is called
    |
    */  
    private function process_in_transit_trip_status_by_driver($arr_data)
    {
        $login_user_id  = isset($arr_data['login_user_id']) ? $arr_data['login_user_id'] :'';
        $booking_id     = isset($arr_data['booking_id']) ? $arr_data['booking_id'] :'';
        $booking_status = isset($arr_data['booking_status']) ? $arr_data['booking_status'] :'';
        $lat            = isset($arr_data['lat']) ? $arr_data['lat'] :'';
        $lng            = isset($arr_data['lng']) ? $arr_data['lng'] :'';
        $client         = isset($arr_data['client']) ? $arr_data['client'] :null;
        $is_update      = isset($arr_data['is_update']) ? $arr_data['is_update'] :'NO';

        if($login_user_id!='' && $booking_id!='' && $booking_status!='')
        {
            $user_id = $login_user_id;
            
            /*check for load post status if 'accept_by_user' or not*/
            
            $obj_booking_master = $this->BookingMasterModel
                                                ->whereHas('load_post_request_details',function($query){
                                                })
                                                ->with(['load_post_request_details'=>function($query){
                                                    $query->with(['user_details'=>function($query){
                                                        $query->select('id','country_code','mobile_no');
                                                    }]);
                                                }])
                                                ->where('id',$booking_id)
                                                ->first();

            if(isset($obj_booking_master) && $obj_booking_master!=null)
            {
                $enc_user_id = isset($obj_booking_master->load_post_request_details->user_id) ? $obj_booking_master->load_post_request_details->user_id :0;

                if(isset($obj_booking_master->booking_status) && $obj_booking_master->booking_status == 'IN_TRANSIT'){
                    $arr_response['status'] = 'error';
                    $arr_response['msg']    = 'You have already started trip, cannot start trip again.';
                    $arr_response['data']   = [];
                    return $arr_response;
                }
                
                $start_trip_image = '';

                if(isset($arr_data['start_trip_image']) && $arr_data['start_trip_image']!=null)
                {
                    $file_name = $arr_data['start_trip_image'];
                    $file_extension = strtolower($arr_data['start_trip_image']->getClientOriginalExtension());
                    if(in_array($file_extension,['png','jpg','jpeg','heif']))
                    {
                        $file_name = time().uniqid().'.'.$file_extension;
                        $isUpload = $arr_data['start_trip_image']->move($this->load_post_img_base_path , $file_name);
                        $start_trip_image = $file_name;
                    }
                    else
                    {
                        $arr_response['status'] = 'error';
                        $arr_response['msg']    = 'Please upload valid file.';
                        $arr_response['data']   = [];
                        return $arr_response;        
                    }
                }
                $invoice_image = '';
                if(isset($arr_data['invoice_image']) && $arr_data['invoice_image']!=null)
                {
                    $file_name = $arr_data['invoice_image'];
                    $file_extension = strtolower($arr_data['invoice_image']->getClientOriginalExtension());
                    if(in_array($file_extension,['png','jpg','jpeg','heif']))
                    {
                        $file_name = time().uniqid().'.'.$file_extension;
                        $isUpload = $arr_data['invoice_image']->move($this->load_post_img_base_path , $file_name);
                        $invoice_image = $file_name;
                    }
                    else
                    {
                        $arr_response['status'] = 'error';
                        $arr_response['msg']    = 'Please upload valid file.';
                        $arr_response['data']   = [];
                        return $arr_response;        
                    }
                }

                $obj_booking_master->start_trip_image = $start_trip_image;
                $obj_booking_master->invoice_image    = $invoice_image;
                $obj_booking_master->booking_status   = 'IN_TRANSIT';
                $status = $obj_booking_master->save();
                if($status){

                    // if(isset($is_update) && $is_update == 'YES')
                    // {
                    //     if($lat!=0 && $lng!=0){
                    //         $arr_lat_lng_data = [];
                    //         $arr_tmp_data = [
                    //                             'lat' => floatval($lat),
                    //                             'lng' => floatval($lng)
                    //                         ];

                    //         /*create a new json file to store lat lng of trip*/
                    //         $trip_file_name = 'trip_lat_lng_'.$booking_id.'.json';
                    //         $trip_file_path = $this->trip_lat_lng_base_img_path.'/'.$trip_file_name;

                    //         if(file_exists($trip_file_path) == false)
                    //         {
                    //             array_push($arr_lat_lng_data, $arr_tmp_data);         
                    //             /* Create new json file and write content in file */
                    //             $fp = fopen($trip_file_path, 'w');
                    //             fwrite($fp,json_encode($arr_lat_lng_data));
                    //             fclose($fp);
                    //         }
                    //     }
                    // }

                    /*Send on signal notification from user to specific driver*/
                    $arr_notification_data = 
                                            [
                                                'title'             => 'Driver started current trip.',
                                                'record_id'         => $booking_id,
                                                'enc_user_id'       => $enc_user_id,
                                                'notification_type' => 'TRIP_IN_TRANSIT',
                                                'user_type'         => 'USER',
                                            ];

                    $this->send_on_signal_notification($arr_notification_data);

                    /*Send on signal notification from user to specific driver*/
                    $arr_notification_data = 
                                            [
                                                'title'             => 'Driver started current trip.',
                                                'record_id'         => $booking_id,
                                                'enc_user_id'       => $enc_user_id,
                                                'notification_type' => 'TRIP_IN_TRANSIT',
                                                'user_type'         => 'WEB',
                                            ];

                    $this->send_on_signal_notification($arr_notification_data);


                    /*Send on signal notification from driver to driver web panel*/
                    $arr_notification_data = 
                                            [
                                                'title'             => 'You have started current trip.',
                                                'record_id'         => $booking_id,
                                                'enc_user_id'       => $login_user_id,
                                                'notification_type' => 'TRIP_IN_TRANSIT',
                                                'user_type'         => 'WEB',
                                            ];

                    $this->send_on_signal_notification($arr_notification_data);

                    $arr_response['status'] = 'success';
                    $arr_response['msg']    = 'You have started current trip.';
                    $arr_response['data']   = [];
                    return $arr_response;

                }else{
                    $arr_response['status'] = 'error';
                    $arr_response['msg']    = 'Problem occurred, while update booking request details, Please try again.';
                    $arr_response['data']   = [];
                    return $arr_response;
                }
            }
            $arr_response['status'] = 'error';
            $arr_response['msg']    = 'Problem occurred, while update booking request details, Please try again.';
            $arr_response['data']   = [];
            return $arr_response;
        }
        $arr_response['status'] = 'error';
        $arr_response['msg']    = 'Problem occurred, while update booking request details, Please try again.';
        $arr_response['data']   = [];
        return $arr_response;   
    }

    /*
    |
    | When driver change the trip status to completed then this method is called
    |
    */
    private function process_completed_trip_status_by_driver($arr_data)
    {   
        //dd($arr_data);

        $login_user_id       = isset($arr_data['login_user_id']) ? $arr_data['login_user_id'] :'';
        $booking_id          = isset($arr_data['booking_id']) ? $arr_data['booking_id'] :'';
        $booking_status      = isset($arr_data['booking_status']) ? $arr_data['booking_status'] :'';
        $lat                 = isset($arr_data['lat']) ? $arr_data['lat'] :'';
        $lng                 = isset($arr_data['lng']) ? $arr_data['lng'] :'';
        $client              = isset($arr_data['client']) ? $arr_data['client'] :null;
        $is_update           = isset($arr_data['is_update']) ? $arr_data['is_update'] :'NO';

        $request_distance    = isset($arr_data['distance']) ? doubleval($arr_data['distance']) :0.0;
        $booking_coordinates = isset($arr_data['booking_coordinates']) ? $arr_data['booking_coordinates'] :'';

        $distance = 0;
        if($is_update == 'NO'){
            $distance = $request_distance;
        }   

        if($login_user_id!='' && $booking_id!='' && $booking_status!='')
        {
            /*check for load post status if 'accept_by_user' or not*/
            
            $obj_booking_master = $this->BookingMasterModel
                                                ->whereHas('load_post_request_details',function($query){
                                                })
                                                ->with('booking_master_coordinate_details')
                                                ->with(['load_post_request_details'=>function($query){
                                                    $query->with(['user_details'=>function($query){
                                                        $query->select('id','country_code','mobile_no');
                                                    }]);
                                                }])
                                                ->where('id',$booking_id)
                                                ->first();

            // dd($obj_booking_master->booking_master_coordinate_details);

            if(isset($obj_booking_master) && $obj_booking_master!=null)
            {
                $enc_user_id = isset($obj_booking_master->load_post_request_details->user_id) ? $obj_booking_master->load_post_request_details->user_id :0;
                $driver_id   = isset($obj_booking_master->load_post_request_details->driver_id) ? $obj_booking_master->load_post_request_details->driver_id :0;

                if(isset($obj_booking_master->booking_status) && $obj_booking_master->booking_status == 'COMPLETED'){
                    $arr_response['status'] = 'not_valid';
                    $arr_response['msg']    = 'You have already completed trip, cannot complete trip again.';
                    $arr_response['data']   = [];
                    return $arr_response;
                }
                
                //dd($obj_booking_master->booking_master_coordinate_details->total_distance_in_km);

                /*changes done for new update*/
                if(isset($is_update) && $is_update == 'YES')
                {
                    $total_distance_in_km = isset($obj_booking_master->booking_master_coordinate_details->total_distance_in_km) ? $obj_booking_master->booking_master_coordinate_details->total_distance_in_km :0;
                    $total_distance_in_miles = 0;
                    /*convert distance to miles*/
                    if($total_distance_in_km>0){
                        $total_distance_in_miles = $total_distance_in_km / 1.609344;
                        $total_distance_in_miles = round($total_distance_in_miles,2);
                    }

                    $distance = $total_distance_in_miles;
                    // /*read and write the josn file contents */
                    // $arr_lat_lng_json_data = '';
                    // $arr_lat_lng_data = [];
                    // $distance = 0;
                    // /*create a new json file to store lat lng of trip*/
                    // $trip_file_name = 'trip_lat_lng_'.$booking_id.'.json';
                    // $trip_file_path = $this->trip_lat_lng_base_img_path.'/'.$trip_file_name;
                    
                    // if($lat!=0 && $lng!=0){
                    //     $arr_tmp_data = [
                    //                         'lat' => floatval($lat),
                    //                         'lng' => floatval($lng)
                    //                     ];

                    //     if(file_exists($trip_file_path)){
                    //         $trip_file_contents = file_get_contents($trip_file_path);
                    //         $arr_lat_lng_data = json_decode($trip_file_contents,true);
                    //         if(isset($arr_lat_lng_data) && count($arr_lat_lng_data)>0){
                    //             /*if file is not empty then push data old file*/
                    //             array_push($arr_lat_lng_data,$arr_tmp_data);
                    //             $arr_lat_lng_json_data = json_encode($arr_lat_lng_data);
                    //             file_put_contents($trip_file_path, $arr_lat_lng_json_data);

                    //             /*find th distance from the lat lng*/
                    //             if(isset($arr_lat_lng_data) && count($arr_lat_lng_data))
                    //             {
                    //                 foreach ($arr_lat_lng_data as $key => $value) 
                    //                 {   
                    //                     if( (isset($value['lat']) && isset($value['lng'])) && (isset($arr_lat_lng_data[$key+1]['lat']) && isset($arr_lat_lng_data[$key+1]['lng']))){
                    //                         $latitudeFrom  =  floatval($value['lat']);
                    //                         $longitudeFrom =  floatval($value['lng']);

                    //                         $latitudeTo  = $arr_lat_lng_data[$key+1]['lat'];
                    //                         $longitudeTo = $arr_lat_lng_data[$key+1]['lng'];
                                            
                    //                         $tmp_distance = $this->findDistance($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo);
                    //                         $distance = $distance + $tmp_distance;
                    //                     }
                    //                 }
                    //             }
                    //         }
                    //         else{
                    //             /*if file is empty then update data to file*/
                    //             $arr_lat_lng_data = [];
                    //             array_push($arr_lat_lng_data, $arr_tmp_data);
                                
                    //             $arr_lat_lng_json_data = json_encode($arr_lat_lng_data);
                    //             file_put_contents($trip_file_path, $arr_lat_lng_json_data);

                    //             /*find th distance from the lat lng*/
                    //             if(isset($arr_lat_lng_data) && count($arr_lat_lng_data))
                    //             {
                    //                 foreach ($arr_lat_lng_data as $key => $value) 
                    //                 {   
                    //                     if( (isset($value['lat']) && isset($value['lng'])) && (isset($arr_lat_lng_data[$key+1]['lat']) && isset($arr_lat_lng_data[$key+1]['lng']))){
                    //                         $latitudeFrom  =  floatval($value['lat']);
                    //                         $longitudeFrom =  floatval($value['lng']);

                    //                         $latitudeTo  = $arr_lat_lng_data[$key+1]['lat'];
                    //                         $longitudeTo = $arr_lat_lng_data[$key+1]['lng'];
                                            
                    //                         $tmp_distance = $this->findDistance($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo);
                    //                         $distance = $distance + $tmp_distance;
                    //                     }
                    //                 }
                    //             }
                    //         }
                    //     }                    
                    // }
                    
                    // $distance = round($distance,2);
                }

                // $meters * 0.000621371

                $booking_unique_id            = isset($obj_booking_master->booking_unique_id) ? $obj_booking_master->booking_unique_id :'';
                $card_id                      = isset($obj_booking_master->card_id) ? $obj_booking_master->card_id :0;
                $is_promo_code_applied        = isset($obj_booking_master->is_promo_code_applied) ? $obj_booking_master->is_promo_code_applied :0;
                $promo_percentage             = isset($obj_booking_master->promo_percentage) ? $obj_booking_master->promo_percentage :0;
                $promo_max_amount             = isset($obj_booking_master->promo_max_amount) ? $obj_booking_master->promo_max_amount :0;
                $is_individual_vehicle        = isset($obj_booking_master->is_individual_vehicle) ? $obj_booking_master->is_individual_vehicle :'0';
                $is_company_driver            = isset($obj_booking_master->is_company_driver) ? $obj_booking_master->is_company_driver :'0';
                $user_bonus_points            = isset($obj_booking_master->user_bonus_points) ? $obj_booking_master->user_bonus_points :0;
                $user_bonus_points_usd_amount = isset($obj_booking_master->user_bonus_points_usd_amount) ? $obj_booking_master->user_bonus_points_usd_amount :0;
                
                $starting_price          = isset($obj_booking_master->starting_price) ? floatval($obj_booking_master->starting_price) :0;
                $per_miles_price         = isset($obj_booking_master->per_miles_price) ? floatval($obj_booking_master->per_miles_price) :0;
                $per_minute_price        = isset($obj_booking_master->per_minute_price) ? floatval($obj_booking_master->per_minute_price) :0;
                $minimum_price           = isset($obj_booking_master->minimum_price) ? floatval($obj_booking_master->minimum_price) :0;
                $cancellation_base_price = isset($obj_booking_master->cancellation_base_price) ? floatval($obj_booking_master->cancellation_base_price) :0;

                $admin_driver_percentage      = isset($obj_booking_master->admin_driver_percentage) ? floatval($obj_booking_master->admin_driver_percentage) :0;
                $admin_company_percentage     = isset($obj_booking_master->admin_company_percentage) ? floatval($obj_booking_master->admin_company_percentage) :0;
                $individual_driver_percentage = isset($obj_booking_master->individual_driver_percentage) ? floatval($obj_booking_master->individual_driver_percentage) :0;
                $company_driver_percentage    = isset($obj_booking_master->company_driver_percentage) ? floatval($obj_booking_master->company_driver_percentage) :0;
                
                $start_datetime = isset($obj_booking_master->start_datetime) ? date('Y-m-d H:i:s',strtotime($obj_booking_master->start_datetime)) :date('Y-m-d H:i:s');
                $end_datetime   = date('Y-m-d H:i:s');

                $interval  = abs(strtotime($end_datetime) - strtotime($start_datetime));
                $total_minutes_trip   = round($interval / 60);

                $applied_promo_code_charge = $total_amount = $total_charge =  0;

                if($distance<=1){
                    $distance = 1;
                }

                $total_per_miles_price = $total_per_minute_price = 0;

                $total_per_miles_price = ($distance * $per_miles_price);

                if($total_minutes_trip>0){
                    $total_per_minute_price = ($total_minutes_trip * $per_minute_price);
                }

                $total_amount = $starting_price + $total_per_miles_price + $total_per_minute_price;
                
                if($total_amount<$minimum_price)
                {
                    $total_amount = $minimum_price;
                }
                
                $total_amount = round($total_amount);

                $admin_amount = $company_amount = $admin_driver_amount = $company_driver_amount = $individual_driver_amount = 0;

                if($is_individual_vehicle == '1')
                {
                    // calculate amount of admin earning from the admin commission. 
                    if($individual_driver_percentage!=0 && $total_amount!=0)
                    {
                      $admin_amount = ((floatval($total_amount) * floatval($individual_driver_percentage)) / 100);

                      $admin_amount = floatval(round($admin_amount,2));
                      
                      /*minus promo amount from admin commission amount*/
                      if($admin_amount>0 && $admin_amount>$applied_promo_code_charge)
                      {
                        $admin_amount = floatval($admin_amount) - floatval($applied_promo_code_charge);
                        $admin_amount = floatval(round($admin_amount,2));
                      }
                      
                      /*also minnus bonus point amount from admin commission value */
                      if($user_bonus_points_usd_amount>0 && $admin_amount>$user_bonus_points_usd_amount){
                        $admin_amount = floatval($admin_amount) - floatval($user_bonus_points_usd_amount);
                        $admin_amount = floatval(round($admin_amount,2));
                      }
                    }
                    
                    //minus admin earning amount from the total amount for total driver earning. 
                    if($total_amount>0 && $total_amount>=$admin_amount)
                    {
                        $individual_driver_amount = ($total_amount - $admin_amount);
                        $individual_driver_amount = floatval($individual_driver_amount);
                    }
                }
                else if($is_individual_vehicle == '0')
                {    
                    //company drivers not having their own vehicles if driver for company then appy company commission amount
                    if($is_company_driver == '1')
                    {
                        if($admin_company_percentage!=0 && $total_amount!=0){
                          
                          $admin_amount = ((floatval($total_amount) * floatval($admin_company_percentage)) / 100);
                          $admin_amount = floatval(round($admin_amount,2));

                          /*minus promo amount from admin commission amount*/
                          if($applied_promo_code_charge>0 && $applied_promo_code_charge<$admin_amount){
                            $admin_amount = floatval($admin_amount) - floatval($applied_promo_code_charge);
                            $admin_amount = floatval(round($admin_amount,2));
                          }
                        
                          /*also minnus bonus point amount from admin commission value */
                          if($user_bonus_points_usd_amount>0 && $user_bonus_points_usd_amount<$admin_amount){
                            $admin_amount = floatval($admin_amount) - floatval($user_bonus_points_usd_amount);
                            $admin_amount = floatval(round($admin_amount,2));
                          }
                        }

                        //minus admin earning amount from the total amount for total company earning. 
                        if($total_amount>0 && $total_amount>=$admin_amount){
                            $company_amount = ($total_amount - $admin_amount);
                            $company_amount = floatval($company_amount);
                        }

                        //calculate company driver amount by company_driver_percentage
                        $company_driver_amount = ((floatval($company_amount) * floatval($company_driver_percentage)) / 100);
                        $company_driver_amount = floatval(round($company_driver_amount,2));
                        
                        //minus company driver earning from then total amount of company earning so final calculate final company amount
                        if($company_amount>0 && $company_amount>=$company_driver_amount){
                            $company_amount = ($company_amount - $company_driver_amount);
                            $company_amount = floatval($company_amount);
                        }
                    }
                    if($is_company_driver == '0')
                    {
                        $admin_driver_amount = ((floatval($total_amount) * floatval($admin_driver_percentage)) / 100);
                        $admin_driver_amount = floatval(round($admin_driver_amount,2));
                        
                        //minus driver earning from then total amount without promo code
                        if($total_amount>0 && $total_amount>=$admin_driver_amount){
                            $admin_amount = ($total_amount - $admin_driver_amount);
                            $admin_amount = floatval(round($admin_amount,2));
                        }
                    }
                }

                //if use apply promo code then deduct promo amount from the final amount
                if(($is_promo_code_applied == 'YES') && ($promo_percentage!=0 && $total_amount!=0))
                {
                    $applied_promo_code_charge = ((doubleval($total_amount) * doubleval($promo_percentage)) / 100);
                    $applied_promo_code_charge = doubleval(number_format($applied_promo_code_charge,2)); // 2 demial points after value.
                    if($promo_max_amount!=0){
                        if($applied_promo_code_charge > $promo_max_amount){
                          $applied_promo_code_charge = doubleval(number_format($promo_max_amount,1)); // 2 demial points after value.
                        }
                    }
                }
                // check for applied_promo_code_charge which must be less than total amount.
                if($total_amount>$applied_promo_code_charge){
                    $total_charge = $total_amount - $applied_promo_code_charge;
                }

                // check for user_bonus_points_usd_amount which must be less than total amount.
                
                if(($user_bonus_points_usd_amount>0) && ($total_amount>$user_bonus_points_usd_amount)){
                    $total_charge = $total_amount - $user_bonus_points_usd_amount;
                }
                
                $total_cent_charge = (round($total_charge) * 100);
                
                $arr_card_details = [
                                      "card_id"           => $card_id,
                                      "user_id"           => $enc_user_id,
                                      'total_charge'      => $total_cent_charge,
                                      "booking_id"        => $booking_id,
                                      "booking_unique_id" => $booking_unique_id,
                                    ];
                
                if($total_cent_charge>0)
                {
                   $arr_stripe_response = $this->StripeService->charge_customer($arr_card_details);
                }
                $end_trip_image = '';

                if(isset($arr_data['end_trip_image']) && $arr_data['end_trip_image']!=null)
                {
                    $file_name = $arr_data['end_trip_image'];
                    $file_extension = strtolower($arr_data['end_trip_image']->getClientOriginalExtension());
                    if(in_array($file_extension,['png','jpg','jpeg','heif']))
                    {
                        $file_name = time().uniqid().'.'.$file_extension;
                        $isUpload = $arr_data['end_trip_image']->move($this->load_post_img_base_path , $file_name);
                        $end_trip_image = $file_name;
                    }
                    else
                    {
                        $arr_response['status'] = 'error';
                        $arr_response['msg']    = 'Please upload valid file.';
                        $arr_response['data']   = [];
                        return $arr_response;        
                    }
                }

                $obj_booking_master->end_trip_image = $end_trip_image;

                $obj_booking_master->transaction_unique_id        = $this->genrate_transaction_unique_number();
                $obj_booking_master->end_datetime                 = $end_datetime;
                $obj_booking_master->total_minutes_trip           = $total_minutes_trip;
                $obj_booking_master->applied_promo_code_charge    = $applied_promo_code_charge;
                $obj_booking_master->user_bonus_points_usd_amount = $user_bonus_points_usd_amount;
                $obj_booking_master->distance                     = $distance;
                $obj_booking_master->total_charge                 = $total_charge;
                $obj_booking_master->total_amount                 = $total_amount;
                $obj_booking_master->admin_amount                 = $admin_amount;
                $obj_booking_master->company_amount               = $company_amount;
                $obj_booking_master->admin_driver_amount          = $admin_driver_amount;
                $obj_booking_master->company_driver_amount        = $company_driver_amount;
                $obj_booking_master->individual_driver_amount     = $individual_driver_amount;
                $obj_booking_master->admin_payment_status         = 'UNPAID';
                $obj_booking_master->payment_type                 = 'STRIPE';
                $obj_booking_master->booking_status               = 'COMPLETED';

                if($total_cent_charge>0)
                {

                    if(isset($arr_stripe_response['status']) && $arr_stripe_response['status'] == 'success')
                    {
                        $obj_booking_master->payment_status    = 'SUCCESS';
                        $obj_booking_master->payment_response  = isset($arr_stripe_response['payment_data']) ? $arr_stripe_response['payment_data'] :'';
                    }
                    elseif(isset($arr_stripe_response['status']) && $arr_stripe_response['status'] == 'error')
                    {
                    
                        $obj_booking_master->payment_status    = 'FAILED';
                        $obj_booking_master->payment_response  = isset($arr_stripe_response['payment_data']) ? $arr_stripe_response['payment_data'] :'';
                    }
                    else
                    {
                        $obj_booking_master->payment_status    = 'PENDING';
                        $obj_booking_master->payment_response  = '';
                    }
                }
                else
                {
                    $obj_booking_master->payment_status    = 'SUCCESS';
                    $obj_booking_master->payment_type      = '';
                    $obj_booking_master->payment_response  = 'Amount is 0';

                }

                $status = $obj_booking_master->save();
                
                if($status){

                    if($total_cent_charge>0)
                    {
                        /*split payment against driver and company*/
                        $this->split_trip_payment_to_drivers_and_company($booking_id);
                    }

                    /*after payment completed deduct user bonus points*/
                    $this->CommonDataService->deduct_user_bonus_points($enc_user_id,$user_bonus_points);

                    // if(isset($is_update) && $is_update == 'YES')
                    // {
                    //     /*insert booking_coordinates details in relational table*/
                    //     if(isset($arr_lat_lng_json_data) && $arr_lat_lng_json_data!=''){
                            
                    //         /*delete the genrated file*/
                    //         if(isset($trip_file_path) && file_exists($trip_file_path)){
                    //             @unlink($trip_file_path);
                    //         }

                    //         $this->BookingMasterCoordinateModel->create([
                    //                                                         'booking_master_id' => $booking_id,
                    //                                                         'coordinates'       => $booking_coordinates                  
                    //                                                     ]);
                    //     }
                    // }
                    // else
                    // {
                    //     $this->BookingMasterCoordinateModel->create([
                    //                                                         'booking_master_id' => $booking_id,
                    //                                                         'coordinates'       => $booking_coordinates 
                    //                                                     ]);
                    // }
                    

                    $notification_title = '';
                    if(isset($arr_stripe_response['status']) && $arr_stripe_response['status'] == 'success')
                    {   
                        $notification_title = 'Payment successfully done for Quick-Pick Trip - '.$booking_unique_id;
                    }
                    else
                    {
                        $notification_title = 'Payment failed for Quick-Pick Trip - '.$booking_unique_id.' contact with admin.';
                    }
                    
                    /*Send on signal notification from to user*/
                    // $arr_notification_data = 
                    //                         [
                    //                             'title'             => $notification_title,
                    //                             'record_id'         => $booking_id,
                    //                             'enc_user_id'       => $enc_user_id,
                    //                             'notification_type' => 'TRIP_PAYMENT',
                    //                             'user_type'         => 'USER',
                    //                         ];
                                            
                    //$this->send_on_signal_notification($arr_notification_data);

                    /*send notification to admin*/
                    $arr_notification_data = $this->built_notification_data($booking_id); 
                    $this->NotificationsService->store_notification($arr_notification_data);

                    /*send notification to user*/
                    $arr_notification_data = $this->built_user_notification_data($booking_id); 
                    $this->NotificationsService->store_notification($arr_notification_data);

                    $driver_status = 'AVAILABLE';
                    $this->CommonDataService->change_driver_status($driver_id,$driver_status);


                    //send twilio sms eta notifications to user
                    if(isset($client)){
                        $user_country_code   = isset($obj_booking_master->load_post_request_details->user_details->country_code) ? $obj_booking_master->load_post_request_details->user_details->country_code :'';
                        $user_mobile_no      = isset($obj_booking_master->load_post_request_details->user_details->mobile_no) ? $obj_booking_master->load_post_request_details->user_details->mobile_no :'';
                        $user_full_mobile_no = $user_country_code.''.$user_mobile_no;
                        
                        $msg_total_charge = isset($total_charge) ? number_format($total_charge,2) : '0';

                        $messageBody         = 'Your trip has completed, your trip amount is $'.$msg_total_charge;
                       
                        // $user_full_mobile_no = '+919021106380';
                        if($user_full_mobile_no!=''){
                            $this->sendEtaNotificationsMessage(
                                                    $client,
                                                    $user_full_mobile_no,
                                                    $messageBody,
                                                    ''
                                                );
                        }
                    }

                    /*Send on signal notification from user to specific driver*/
                    $arr_notification_data = 
                                            [
                                                'title'             => 'Driver completed current trip.',
                                                'record_id'         => $booking_id,
                                                'enc_user_id'       => $enc_user_id,
                                                'notification_type' => 'TRIP_COMPLETED',
                                                'user_type'         => 'USER',
                                            ];

                    $this->send_on_signal_notification($arr_notification_data);
                    
                    /*Send on signal notification from user to specific driver*/
                    $arr_notification_data = 
                                            [
                                                'title'             => 'Driver completed current trip.',
                                                'record_id'         => $booking_id,
                                                'enc_user_id'       => $enc_user_id,
                                                'notification_type' => 'TRIP_COMPLETED',
                                                'user_type'         => 'WEB',
                                            ];

                    $this->send_on_signal_notification($arr_notification_data);

                    /*Send on signal notification from driver to driver web panel*/
                    $arr_notification_data = 
                                            [
                                                'title'             => 'You have completed current trip.',
                                                'record_id'         => $booking_id,
                                                'enc_user_id'       => $login_user_id,
                                                'notification_type' => 'TRIP_IN_TRANSIT',
                                                'user_type'         => 'WEB',
                                            ];

                    $this->send_on_signal_notification($arr_notification_data);

                    try
                    {
                        require_once('tcpdf-master/tcpdf.php');
                        $obj_tcpdf = new \TCPDF;

                        $obj_tcpdf->SetTitle('QuickPick-Trip-Invoice');
                        $obj_tcpdf->AddPage(); 

                        $arr_tmp_data = $this->get_booking_details($booking_id);
                        $arr_trip_data = filter_completed_trip_details($arr_tmp_data,'genrate_pdf');

                        $html ="";
                        $view ="";
                        $view = view('invoice.trip_invoice')->with(['arr_trip_data'=>$arr_trip_data,'tcpdf'=>$obj_tcpdf]);
                        $html = $view->render(); 

                        $obj_tcpdf->writeHTML($html, true, false, true, false, '');
                        $FileName = 'TRIP_INVOICE_'.$booking_id.'.pdf'; 
                        $obj_tcpdf->output($this->invoice_base_img_path.$FileName,'F'); 

                        /*send invoice email to user*/
                        $arr_user_invoice_email_data = $this->built_user_invoice_email_data($booking_id); 
                        $this->EmailService->send_mail_with_attachments($arr_user_invoice_email_data);
                        
                        /*send invoice email to driver*/
                        $arr_driver_invoice_email_data = $this->built_driver_invoice_email_data($booking_id); 
                        $this->EmailService->send_mail_with_attachments($arr_driver_invoice_email_data);

                        $arr_response['status'] = 'success';
                        $arr_response['msg']    = 'You have complete current trip.';
                        $arr_response['data']   = [];
                        return $arr_response;
                    }
                    catch (\Exception $e)
                    {
                        $arr_response['status'] = 'success';
                        $arr_response['msg']    = 'You have complete current trip.';
                        $arr_response['data']   = [];
                        return $arr_response;
                    }
                }
                else
                {
                    $arr_response['status'] = 'error';
                    $arr_response['msg']    = 'Problem occurred, while update booking request details, Please try again.';
                    $arr_response['data']   = [];
                    return $arr_response;
                }
            }
            $arr_response['status'] = 'error';
            $arr_response['msg']    = 'Problem occurred, while update booking request details, Please try again.';
            $arr_response['data']   = [];
            return $arr_response;
        }
        $arr_response['status'] = 'error';
        $arr_response['msg']    = 'Problem occurred, while update booking request details, Please try again.';
        $arr_response['data']   = [];
        return $arr_response;   
    }
    private function findDistance($lat1, $lon1, $lat2, $lon2, $unit = 'M') {
        if (($lat1 == $lat2) && ($lon1 == $lon2)) {
            return 0;
        }
        else {
            $theta = $lon1 - $lon2;
            $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
            $dist = acos($dist);
            $dist = rad2deg($dist);
            $miles = $dist * 60 * 1.1515;
            $unit = strtoupper($unit);

            if ($unit == "K") {
                return ($miles * 1.609344);
            } else if ($unit == "N") {
                return ($miles * 0.8684);
            } else {
                return $miles;
            }
        }
    }
    private function split_trip_payment_to_drivers_and_company($booking_id)
    {
        $arr_booking_master = $this->get_booking_details($booking_id);
        
        $booking_id               = isset($arr_booking_master['id']) ? $arr_booking_master['id']:'';
        $enc_driver_id            = isset($arr_booking_master['load_post_request_details']['driver_id']) ? $arr_booking_master['load_post_request_details']['driver_id']:'';
        $booking_unique_id        = isset($arr_booking_master['booking_unique_id']) ? $arr_booking_master['booking_unique_id']:'';
        
        $driver_first_name       = isset($arr_booking_master['load_post_request_details']['driver_details']['first_name']) ? $arr_booking_master['load_post_request_details']['driver_details']['first_name']:'';
        $driver_last_name        = isset($arr_booking_master['load_post_request_details']['driver_details']['last_name']) ? $arr_booking_master['load_post_request_details']['driver_details']['last_name']:'';
        $driver_email            = isset($arr_booking_master['load_post_request_details']['driver_details']['email']) ? $arr_booking_master['load_post_request_details']['driver_details']['email']:'';
        $driver_mobile_no        = isset($arr_booking_master['load_post_request_details']['driver_details']['mobile_no']) ? $arr_booking_master['load_post_request_details']['driver_details']['mobile_no']:'';
        $driver_stripe_account_id = isset($arr_booking_master['load_post_request_details']['driver_details']['stripe_account_id']) ? $arr_booking_master['load_post_request_details']['driver_details']['stripe_account_id']:'';

        $company_id                = isset($arr_booking_master['load_post_request_details']['driver_details']['company_id']) ? $arr_booking_master['load_post_request_details']['driver_details']['company_id']:'';
        $company_name              = isset($arr_booking_master['load_post_request_details']['driver_details']['company_details']['company_name']) ? $arr_booking_master['load_post_request_details']['driver_details']['company_details']['company_name']:'';
        $company_email             = isset($arr_booking_master['load_post_request_details']['driver_details']['company_details']['email']) ? $arr_booking_master['load_post_request_details']['driver_details']['company_details']['email']:'';
        $company_mobile_no         = isset($arr_booking_master['load_post_request_details']['driver_details']['company_details']['mobile_no']) ? $arr_booking_master['load_post_request_details']['driver_details']['company_details']['mobile_no']:'';
        $company_stripe_account_id = isset($arr_booking_master['load_post_request_details']['driver_details']['company_details']['stripe_account_id']) ? $arr_booking_master['load_post_request_details']['driver_details']['company_details']['stripe_account_id']:'';

        $company_amount           = isset($arr_booking_master['company_amount']) ? floatval($arr_booking_master['company_amount']):0;
        $admin_driver_amount      = isset($arr_booking_master['admin_driver_amount']) ? floatval($arr_booking_master['admin_driver_amount']):0;
        $company_driver_amount    = isset($arr_booking_master['company_driver_amount']) ? floatval($arr_booking_master['company_driver_amount']):0;
        $individual_driver_amount = isset($arr_booking_master['individual_driver_amount']) ? floatval($arr_booking_master['individual_driver_amount']):0;

        $is_company_driver        = isset($arr_booking_master['is_company_driver']) ? $arr_booking_master['is_company_driver']:0;
        $is_individual_vehicle    = isset($arr_booking_master['is_individual_vehicle']) ? $arr_booking_master['is_individual_vehicle']:0;
        
        $driver_earning_amount = $company_earning_amount = 0;

        if($is_individual_vehicle == '1')
        {
            $driver_earning_amount = $individual_driver_amount;
        }
        else if($is_individual_vehicle == '0')
        {    
            if($is_company_driver == '1')
            {
                $driver_earning_amount  = $company_driver_amount; 
                $company_earning_amount = $company_amount;
            }
            else if($is_company_driver == '0')
            {
                $driver_earning_amount = $admin_driver_amount;
            }
        }
        
        if($is_company_driver == '1')
        {
            $total_company_cent_charge = (round($company_earning_amount) * 100);
            
            $admin_deposit_money_status = 'PENDING';
            $payment_note = '';

            $arr_company_payment_details = [
                                                  'company_id'                => $company_id,
                                                  'company_name'              => $company_name,
                                                  'company_email'             => $company_email,
                                                  'company_mobile_no'         => $company_mobile_no,
                                                  'booking_id'                => $booking_id,
                                                  'booking_unique_id'         => $booking_unique_id,
                                                  'company_stripe_account_id' => $company_stripe_account_id,
                                                  'company_earning_amount'    => $company_earning_amount,
                                                  'total_company_cent_charge' => $total_company_cent_charge
                                                ];

            if($total_company_cent_charge>0)
            {
                $arr_company_stripe_response = $this->StripeService->make_company_payment($arr_company_payment_details);
                
                if(isset($arr_company_stripe_response['status']) && $arr_company_stripe_response['status'] == 'success') 
                {
                    $admin_deposit_money_status = 'SUCCESS';  
                    $payment_note = isset($arr_company_stripe_response['msg']) ? $arr_company_stripe_response['msg'] : '';  
                }
                else if(isset($arr_company_stripe_response['status']) && $arr_company_stripe_response['status'] == 'error')
                {
                    $admin_deposit_money_status = 'FAILED';    
                    $payment_note = isset($arr_company_stripe_response['msg']) ? $arr_company_stripe_response['msg'] : '';  
                } 
            }
            else
            {
                $admin_deposit_money_status = 'SUCCESS';    
                $payment_note = 'Company earning amount is 0.';  
            }

            $arr_company_deposit_money                      = [];
            $arr_company_deposit_money['booking_master_id'] = $booking_id;
            $arr_company_deposit_money['from_user_id']      = $this->CommonDataService->get_admin_id();
            $arr_company_deposit_money['from_user_type']    = 'ADMIN';
            $arr_company_deposit_money['to_user_type']      = 'COMPANY';
            $arr_company_deposit_money['to_user_id']        = $company_id;
            $arr_company_deposit_money['transaction_id']    = $this->genrate_transaction_unique_number();
            $arr_company_deposit_money['amount_paid']       = $company_earning_amount;
            $arr_company_deposit_money['note']              = $payment_note;
            $arr_company_deposit_money['status']            = $admin_deposit_money_status;
            $arr_company_deposit_money['date']              = date('Y-m-d');
            $arr_company_deposit_money['payment_data']      = isset($arr_company_stripe_response['payment_data']) ? $arr_company_stripe_response['payment_data'] : '';

            $obj_company_deposit_money = $this->DepositMoneyModel->create($arr_company_deposit_money);

            if($obj_company_deposit_money)
            {
                $arr_data_details = array_merge($arr_company_deposit_money,$arr_company_payment_details);     
                
                /*build admin notification data*/
                $arr_admin_notification_data = $this->built_payment_notification_data($arr_data_details,'ADMIN_COMPANY');                 
                $this->NotificationsService->store_notification($arr_admin_notification_data);
                
                /*build company notification data*/

                $arr_company_notification_data = $this->built_payment_notification_data($arr_data_details,'COMPANY'); 
                $this->NotificationsService->store_notification($arr_company_notification_data);
            }
        }
        
        $total_driver_cent_charge = (round($driver_earning_amount) * 100);

        $driver_deposit_money_status = 'PENDING';
        $driver_note = '';

        $arr_payment_details = [
                                          'driver_id'                => $enc_driver_id,
                                          'driver_first_name'        => $driver_first_name,
                                          'driver_last_name'         => $driver_last_name,
                                          'driver_email'             => $driver_email,
                                          'driver_mobile_no'         => $driver_mobile_no,
                                          'is_company_driver'        => $is_company_driver,
                                          'company_name'             => $company_name,
                                          'booking_id'               => $booking_id,
                                          'booking_unique_id'        => $booking_unique_id,
                                          'driver_stripe_account_id' => $driver_stripe_account_id,
                                          'driver_earning_amount'    => $driver_earning_amount,
                                          'total_driver_cent_charge' => $total_driver_cent_charge
                                        ];
                                        
        if($total_driver_cent_charge>0)
        {
            $arr_stripe_response = $this->StripeService->make_driver_payment($arr_payment_details);
            
            if(isset($arr_stripe_response['status']) && $arr_stripe_response['status'] == 'success') 
            {
                $driver_deposit_money_status = 'SUCCESS';    
                $driver_note = isset($arr_stripe_response['msg']) ? $arr_stripe_response['msg'] : '';
            }
            else if(isset($arr_stripe_response['status']) && $arr_stripe_response['status'] == 'error')
            {
                $driver_deposit_money_status = 'FAILED';    
                $driver_note = isset($arr_stripe_response['msg']) ? $arr_stripe_response['msg'] : '';
            } 
        }
        else
        {
            $driver_deposit_money_status = 'SUCCESS';  
            $driver_note = 'Driver earning amount is 0.';    
        }

        $arr_driver_deposit_money                      = [];
        $arr_driver_deposit_money['booking_master_id'] = $booking_id;

        if($is_company_driver == '0')
        {
            $arr_driver_deposit_money['from_user_id']      = $this->CommonDataService->get_admin_id();
            $arr_driver_deposit_money['from_user_type']    = 'ADMIN';
            $arr_driver_deposit_money['to_user_type']      = 'DRIVER';
        }
        elseif($is_company_driver == '1')
        {
            $arr_driver_deposit_money['from_user_id']      = $company_id;
            $arr_driver_deposit_money['from_user_type']    = 'COMPANY';
            $arr_driver_deposit_money['to_user_type']      = 'COMPANY_DRIVER';
        }
        $arr_driver_deposit_money['to_user_id']        = $enc_driver_id;
        $arr_driver_deposit_money['transaction_id']    = $this->genrate_transaction_unique_number();
        $arr_driver_deposit_money['amount_paid']       = $driver_earning_amount;
        $arr_driver_deposit_money['note']              = $driver_note;
        $arr_driver_deposit_money['status']            = $driver_deposit_money_status;
        $arr_driver_deposit_money['date']              = date('Y-m-d');
        $arr_driver_deposit_money['payment_data']      = isset($arr_stripe_response['payment_data']) ? $arr_stripe_response['payment_data'] : '';

        $obj_deposit_money = $this->DepositMoneyModel->create($arr_driver_deposit_money);

        if($obj_deposit_money)
        {
            $arr_data_details = array_merge($arr_driver_deposit_money,$arr_payment_details);     
            
            /*build admin notification data*/            
            $arr_admin_notification_data = $this->built_payment_notification_data($arr_data_details,'ADMIN_DRIVER'); 
            $this->NotificationsService->store_notification($arr_admin_notification_data);
            
            /*build driver notification data*/
            $arr_driver_notification_data = $this->built_payment_notification_data($arr_data_details,'DRIVER'); 
            $this->NotificationsService->store_notification($arr_driver_notification_data);

            //send one signal notification to driver
            $title = '';
            if($is_company_driver == '0')
            {
                if(isset($arr_driver_deposit_money['status']) && $arr_driver_deposit_money['status'] == 'SUCCESS')
                {   
                    $title = 'Payment for trip #'.$booking_unique_id.' is successfully received to your account with transaction id #'.$arr_driver_deposit_money['transaction_id'];
                }
                else if(isset($arr_driver_deposit_money['status']) && $arr_driver_deposit_money['status'] == 'FAILED')
                {
                    $title = 'Payment for trip #'.$booking_unique_id.' is failed  with the transaction id #'.$arr_driver_deposit_money['transaction_id'].' Please contact '.config('app.project.name').' Admin';
                }
                else if(isset($arr_driver_deposit_money['status']) && $arr_driver_deposit_money['status'] == 'PENDING')
                {
                    $title = 'Payment for trip #'.$booking_unique_id.' is pending with the transaction id #'.$arr_driver_deposit_money['transaction_id'].' Please contact '.config('app.project.name').' Admin';
                }   
            }
            else if($is_company_driver == '1')
            {
                if(isset($arr_driver_deposit_money['status']) && $arr_driver_deposit_money['status'] == 'SUCCESS')
                {   
                    $title = 'Payment for trip #'.$booking_unique_id.' is successfully received to your account with transaction id #'.$arr_driver_deposit_money['transaction_id'];
                }
                else if(isset($arr_driver_deposit_money['status']) && $arr_driver_deposit_money['status'] == 'FAILED')
                {
                    $title = 'Payment for trip #'.$booking_unique_id.' is failed  with the transaction id #'.$arr_driver_deposit_money['transaction_id'].' Please contact '.$company_name.' Admin';
                }
                else if(isset($arr_driver_deposit_money['status']) && $arr_driver_deposit_money['status'] == 'PENDING')
                {
                    $title = 'Payment for trip #'.$booking_unique_id.' is pending with the transaction id #'.$arr_driver_deposit_money['transaction_id'].' Please contact '.$company_name.' Admin';
                }   
            }

            $arr_notification_data = 
                                        [
                                            'title'             => $title,
                                            'notification_type' => 'DEPOSIT_MONEY',
                                            'enc_user_id'       => $enc_driver_id,
                                            'user_type'         => 'DRIVER',

                                        ];
            $this->NotificationsService->send_on_signal_notification($arr_notification_data);
            return true;
        }
        return true;

    }
    private function built_payment_notification_data($arr_data,$type)
    {
        $arr_notification = [];
        if(isset($arr_data) && sizeof($arr_data)>0)
        {
            if(isset($type) && $type == 'ADMIN_DRIVER')
            {
                $id                = isset($arr_data['driver_id']) ? $arr_data['driver_id'] :'';
                $transaction_id    = isset($arr_data['transaction_id']) ? $arr_data['transaction_id'] :'';
                $booking_unique_id = isset($arr_data['booking_unique_id']) ? $arr_data['booking_unique_id'] :'';
                
                $is_company_driver = isset($arr_data['is_company_driver']) ? $arr_data['is_company_driver'] :'';
                $company_name      = isset($arr_data['company_name']) ? $arr_data['company_name'] :'';

                $first_name = isset($arr_data['driver_first_name']) ? $arr_data['driver_first_name'] :'';
                $last_name  = isset($arr_data['driver_last_name']) ? $arr_data['driver_last_name'] :'';

                $full_name  = $first_name.' '.$last_name;
                $full_name  = ($full_name!=' ') ? $full_name : '-';

                $notification_title = $notification_type = '';

                if($is_company_driver == '0')
                {
                    $notification_type = 'Driver Online Payment';
                    if(isset($arr_data['status']) && $arr_data['status'] == 'SUCCESS')
                    {   
                        $notification_title = 'Payment for trip #'.$booking_unique_id.' is successfully sent to - '.$full_name.' account with transaction id #'.$transaction_id;
                    }
                    else if(isset($arr_data['status']) && $arr_data['status'] == 'FAILED')
                    {
                        $notification_title = 'Payment for trip #'.$booking_unique_id.' is failed while sending to - '.$full_name.' account with transaction id #'.$transaction_id;
                    }
                    else if(isset($arr_data['status']) && $arr_data['status'] == 'PENDING')
                    {
                        $notification_title = 'Payment for trip #'.$booking_unique_id.' is pending to - '.$full_name.' account with transaction id #'.$transaction_id;
                    }
                }
                else if($is_company_driver == '1')
                {
                    $notification_type = '('.$company_name.') Driver Online Payment';

                    $sent_to_name = $full_name.' ('.$company_name.' Driver)';

                    if(isset($arr_data['status']) && $arr_data['status'] == 'SUCCESS')
                    {   
                        $notification_title = 'Payment for trip #'.$booking_unique_id.' is successfully sent to - '.$sent_to_name.' account with transaction id #'.$transaction_id;
                    }
                    else if(isset($arr_data['status']) && $arr_data['status'] == 'FAILED')
                    {
                        $notification_title = 'Payment for trip #'.$booking_unique_id.' is failed while sending to - '.$sent_to_name.' account with transaction id #'.$transaction_id;
                    }
                    else if(isset($arr_data['status']) && $arr_data['status'] == 'PENDING')
                    {
                        $notification_title = 'Payment for trip #'.$booking_unique_id.' is pending to - '.$sent_to_name.' account with transaction id #'.$transaction_id;
                    }
                }

                $arr_notification['user_id']           = $this->CommonDataService->get_admin_id();
                $arr_notification['is_read']           = 0;
                $arr_notification['is_show']           = 0;
                $arr_notification['user_type']         = 'ADMIN';
                $arr_notification['notification_type'] = $notification_type;
                $arr_notification['title']             = $notification_title;
                
                $arr_notification['view_url']          = '/'.config('app.project.admin_panel_slug').'/driver/deposit_receipt/'.base64_encode($id); 
            }
            else if(isset($type) && $type == 'ADMIN_COMPANY')
            {
                $id                = isset($arr_data['company_id']) ? $arr_data['company_id'] :'';
                $transaction_id    = isset($arr_data['transaction_id']) ? $arr_data['transaction_id'] :'';
                $booking_unique_id = isset($arr_data['booking_unique_id']) ? $arr_data['booking_unique_id'] :'';
                
                $company_name      = isset($arr_data['company_name']) ? $arr_data['company_name'] :'';

                $notification_title = $notification_type = '';

                $notification_type = $company_name.' Company Online Payment';
                if(isset($arr_data['status']) && $arr_data['status'] == 'SUCCESS')
                {   
                    $notification_title = 'Payment for trip #'.$booking_unique_id.' is successfully sent to - ('.$company_name.') Company account with transaction id #'.$transaction_id;
                }
                else if(isset($arr_data['status']) && $arr_data['status'] == 'FAILED')
                {
                    $notification_title = 'Payment for trip #'.$booking_unique_id.' is failed while sending to - ('.$company_name.') Company account with transaction id #'.$transaction_id;
                }
                else if(isset($arr_data['status']) && $arr_data['status'] == 'PENDING')
                {
                    $notification_title = 'Payment for trip #'.$booking_unique_id.' is pending to - ('.$company_name.') Company account with transaction id #'.$transaction_id;
                }

                $arr_notification['user_id']           = $this->CommonDataService->get_admin_id();
                $arr_notification['is_read']           = 0;
                $arr_notification['is_show']           = 0;
                $arr_notification['user_type']         = 'ADMIN';
                $arr_notification['notification_type'] = $notification_type;
                $arr_notification['title']             = $notification_title;
                
                $arr_notification['view_url']          = '/'.config('app.project.admin_panel_slug').'/company/deposit_receipt/'.base64_encode($id); 
            }
            else if(isset($type) && $type == 'DRIVER')
            {
                $id                    = isset($arr_data['driver_id']) ? $arr_data['driver_id'] :'';
                $transaction_id        = isset($arr_data['transaction_id']) ? $arr_data['transaction_id'] :'';
                $booking_unique_id     = isset($arr_data['booking_unique_id']) ? $arr_data['booking_unique_id'] :'';
                $driver_earning_amount = isset($arr_data['driver_earning_amount']) ? $arr_data['driver_earning_amount']: '-';

                $is_company_driver = isset($arr_data['is_company_driver']) ? $arr_data['is_company_driver'] :'';
                $company_name      = isset($arr_data['company_name']) ? $arr_data['company_name'] :'';

                $notification_title = $notification_type = '';

                if($is_company_driver == '0')
                {
                    $notification_type = 'Driver Online Payment';
                    if(isset($arr_data['status']) && $arr_data['status'] == 'SUCCESS')
                    {   
                        $notification_title = 'Payment for trip #'.$booking_unique_id.' is successfully received to your account with transaction id #'.$transaction_id;
                    }
                    else if(isset($arr_data['status']) && $arr_data['status'] == 'FAILED')
                    {
                        $notification_title = 'Payment for trip #'.$booking_unique_id.' is failed  with the transaction id #'.$transaction_id.' Please contact '.config('app.project.name').' Admin';
                    }
                    else if(isset($arr_data['status']) && $arr_data['status'] == 'PENDING')
                    {
                        $notification_title = 'Payment for trip #'.$booking_unique_id.' is pending with the transaction id #'.$transaction_id.' Please contact '.config('app.project.name').' Admin';
                    }
                }
                else if($is_company_driver == '1')
                {
                    $notification_type = '('.$company_name.') Driver Online Payment';

                    if(isset($arr_data['status']) && $arr_data['status'] == 'SUCCESS')
                    {   
                        $notification_title = 'Payment for trip #'.$booking_unique_id.' is successfully received to your account with transaction id #'.$transaction_id;
                    }
                    else if(isset($arr_data['status']) && $arr_data['status'] == 'FAILED')
                    {
                        $notification_title = 'Payment for trip #'.$booking_unique_id.' is failed  with the transaction id #'.$transaction_id.' Please contact '.$company_name.' Admin';
                    }
                    else if(isset($arr_data['status']) && $arr_data['status'] == 'PENDING')
                    {
                        $notification_title = 'Payment for trip #'.$booking_unique_id.' is pending with the transaction id #'.$transaction_id.' Please contact '.$company_name.' Admin';
                    }
                }

                $arr_notification['user_id']           = $id;
                $arr_notification['is_read']           = 0;
                $arr_notification['is_show']           = 0;
                $arr_notification['user_type']         = 'DRIVER';
                $arr_notification['notification_type'] = $notification_type;
                $arr_notification['title']             = $notification_title;
                $arr_notification['view_url']          = '';
            }
            else if(isset($type) && $type == 'COMPANY')
            {
                $id                    = isset($arr_data['company_id']) ? $arr_data['company_id'] :'';
                $transaction_id        = isset($arr_data['transaction_id']) ? $arr_data['transaction_id'] :'';
                $booking_unique_id     = isset($arr_data['booking_unique_id']) ? $arr_data['booking_unique_id'] :'';
                
                $notification_title = $notification_type = '';

                $notification_type = 'Company Online Payment';
                if(isset($arr_data['status']) && $arr_data['status'] == 'SUCCESS')
                {   
                    $notification_title = 'Payment for trip #'.$booking_unique_id.' is successfully received to your account with transaction id #'.$transaction_id;
                }
                else if(isset($arr_data['status']) && $arr_data['status'] == 'FAILED')
                {
                    $notification_title = 'Payment for trip #'.$booking_unique_id.' is failed  with the transaction id #'.$transaction_id.' Please contact '.config('app.project.name').' Admin';
                }
                else if(isset($arr_data['status']) && $arr_data['status'] == 'PENDING')
                {
                    $notification_title = 'Payment for trip #'.$booking_unique_id.' is pending with the transaction id #'.$transaction_id.' Please contact '.config('app.project.name').' Admin';
                }

                $arr_notification['user_id']           = $id;
                $arr_notification['is_read']           = 0;
                $arr_notification['is_show']           = 0;
                $arr_notification['user_type']         = 'COMPANY';
                $arr_notification['notification_type'] = $notification_type;
                $arr_notification['title']             = $notification_title;
                $arr_notification['view_url']          = '/'.config('app.project.company_panel_slug').'/deposit_money';
            }
        }
        return $arr_notification;
    }
    /*
    |
    | When driver change the trip status to cancel then this method is called
    |
    */
    private function process_cancel_trip_status_by_driver($arr_data)
    {
        $login_user_id  = isset($arr_data['login_user_id']) ? $arr_data['login_user_id'] :'';
        $booking_id     = isset($arr_data['booking_id']) ? $arr_data['booking_id'] :'';
        $booking_status = isset($arr_data['booking_status']) ? $arr_data['booking_status'] :'';
        $reason         = isset($arr_data['reason']) ? $arr_data['reason'] :'';
        $client         = isset($arr_data['client']) ? $arr_data['client'] :null;

        if($login_user_id!='' && $booking_id!='' && $booking_status!='')
        {
            /*check for load post status if 'accept_by_user' or not*/
            
            $obj_booking_master = $this->BookingMasterModel
                                                ->whereHas('load_post_request_details',function($query){
                                                })
                                                ->with(['load_post_request_details'=>function($query){
                                                    $query->with(['user_details'=>function($query){
                                                        $query->select('id','first_name','last_name','country_code','mobile_no');
                                                    }]);
                                                    $query->with(['driver_details'=>function($query){
                                                        $query->select('id','first_name','last_name','country_code','mobile_no');
                                                    }]);
                                                }]) 
                                                ->where('id',$booking_id)
                                                ->first();

            if(isset($obj_booking_master) && $obj_booking_master!=null)
            {
                $enc_user_id = isset($obj_booking_master->load_post_request_details->user_id) ? $obj_booking_master->load_post_request_details->user_id :0;

                if(isset($obj_booking_master->booking_status) && $obj_booking_master->booking_status == 'CANCEL_BY_DRIVER'){
                    $arr_response['status'] = 'error';
                    $arr_response['msg']    = 'You have already cancel trip, cannot cancel trip again.';
                    $arr_response['data']   = [];
                    return $arr_response;
                }

                $obj_booking_master->booking_status = 'CANCEL_BY_DRIVER';
                $obj_booking_master->reason         = $reason;
                $status = $obj_booking_master->save();
                if($status){

                    //send twilio sms eta notifications to user
                    if(isset($client)){
                        $user_country_code   = isset($obj_booking_master->load_post_request_details->user_details->country_code) ? $obj_booking_master->load_post_request_details->user_details->country_code :'';
                        $user_mobile_no      = isset($obj_booking_master->load_post_request_details->user_details->mobile_no) ? $obj_booking_master->load_post_request_details->user_details->mobile_no :'';
                        $user_full_mobile_no = $user_country_code.''.$user_mobile_no;

                        if($user_full_mobile_no!=''){

                            $driver_first_name  = isset($obj_booking_master->load_post_request_details->driver_details->first_name) ? $obj_booking_master->load_post_request_details->driver_details->first_name :'';
                            $driver_last_name   = isset($obj_booking_master->load_post_request_details->driver_details->last_name) ? $obj_booking_master->load_post_request_details->driver_details->last_name :'';
                            $driver_full_name   = $driver_first_name.' '.$driver_last_name;
                            
                            $messageBody         = $driver_full_name.' has cancelled the current trip, sorry for inconvenience.';

                            $this->sendEtaNotificationsMessage(
                                                    $client,
                                                    $user_full_mobile_no,
                                                    $messageBody,
                                                    ''
                                                );
                        }
                    }
                    /*Send on signal notification from user to specific driver*/
                    $arr_notification_data = 
                                            [
                                                'title'             => 'Driver cancel booking request.',
                                                'record_id'         => $booking_id,
                                                'enc_user_id'       => $enc_user_id,
                                                'notification_type' => 'TRIP_CANCEL_BY_DRIVER',
                                                'user_type'         => 'USER',
                                            ];

                    $this->send_on_signal_notification($arr_notification_data);

                    /*Send on signal notification from user to specific driver*/
                    $arr_notification_data = 
                                            [
                                                'title'             => 'Driver cancel booking request.',
                                                'record_id'         => $booking_id,
                                                'enc_user_id'       => $enc_user_id,
                                                'notification_type' => 'TRIP_CANCEL_BY_DRIVER',
                                                'user_type'         => 'WEB',
                                            ];

                    $this->send_on_signal_notification($arr_notification_data);

                    $driver_status = 'AVAILABLE';
                    $this->CommonDataService->change_driver_status($login_user_id,$driver_status);

                    $arr_response['status'] = 'success';
                    $arr_response['msg']    = 'You have cancel current trip.';
                    $arr_response['data']   = [];
                    return $arr_response;

                }else{
                    $arr_response['status'] = 'error';
                    $arr_response['msg']    = 'Problem occurred, while update booking request details, Please try again.';
                    $arr_response['data']   = [];
                    return $arr_response;
                }
            }
            $arr_response['status'] = 'error';
            $arr_response['msg']    = 'Problem occurred, while update booking request details, Please try again.';
            $arr_response['data']   = [];
            return $arr_response;
        }
        $arr_response['status'] = 'error';
        $arr_response['msg']    = 'Problem occurred, while update booking request details, Please try again.';
        $arr_response['data']   = [];
        return $arr_response;   
    }

    /*
    |
    | When request is accepted by driver and then user cancel trip then this method will call.
    |
    */ 
    private function process_cancel_trip_status_by_user($arr_data)
    {
        $login_user_id  = isset($arr_data['login_user_id']) ? $arr_data['login_user_id'] :'';
        $booking_id     = isset($arr_data['booking_id']) ? $arr_data['booking_id'] :'';
        $booking_status = isset($arr_data['booking_status']) ? $arr_data['booking_status'] :'';
        $reason         = isset($arr_data['reason']) ? $arr_data['reason'] :'';
        
        if($login_user_id!='' && $booking_id!='' && $booking_status!='')
        {
            $user_id = $login_user_id;
            
            /*check for load post status if 'accept_by_user' or not*/
            
            $obj_booking_master = $this->BookingMasterModel
                                                ->whereHas('load_post_request_details',function($query){
                                                })
                                                ->with('load_post_request_details')
                                                ->where('id',$booking_id)
                                                ->first();

            if(isset($obj_booking_master) && $obj_booking_master!=null)
            {
                $enc_user_id             = isset($obj_booking_master->load_post_request_details->driver_id) ? $obj_booking_master->load_post_request_details->driver_id :0;

                $booking_unique_id       = isset($obj_booking_master->booking_unique_id) ? $obj_booking_master->booking_unique_id :'';
                $card_id                 = isset($obj_booking_master->card_id) ? $obj_booking_master->card_id :0;
                $cancellation_base_price = isset($obj_booking_master->cancellation_base_price) ? $obj_booking_master->cancellation_base_price :0;

                $total_cent_charge = (floatval($cancellation_base_price) * 100);

                if(isset($obj_booking_master->booking_status) && $obj_booking_master->booking_status == 'CANCEL_BY_USER'){
                    $arr_response['status'] = 'error';
                    $arr_response['msg']    = 'You have already cancel trip, cannot cancel trip again.';
                    $arr_response['data']   = [];
                    return $arr_response;
                }

                $arr_card_details = [
                                      "card_id"           => $card_id,
                                      "user_id"           => $login_user_id,
                                      'total_charge'      => round($total_cent_charge),
                                      "booking_id"        => $booking_id,
                                      "booking_unique_id" => $booking_unique_id,
                                    ];

                $arr_stripe_response = [];

                if($total_cent_charge>0){

                    $arr_stripe_response = $this->StripeService->charge_customer($arr_card_details);
                }

                
                $obj_booking_master->total_charge         = $cancellation_base_price;
                $obj_booking_master->total_amount         = $cancellation_base_price;
                $obj_booking_master->admin_payment_status = 'UNPAID';
                $obj_booking_master->payment_type         = 'STRIPE';
                $obj_booking_master->booking_status       = 'CANCEL_BY_USER';
                $obj_booking_master->reason               = $reason;

                if(isset($arr_stripe_response['status']) && $arr_stripe_response['status'] == 'success')
                {
                    $obj_booking_master->payment_status    = 'SUCCESS';
                    $obj_booking_master->payment_response  = isset($arr_stripe_response['payment_data']) ? $arr_stripe_response['payment_data'] :'';
                }
                elseif(isset($arr_stripe_response['status']) && $arr_stripe_response['status'] == 'error')
                {
                
                    $obj_booking_master->payment_status    = 'FAILED';
                    $obj_booking_master->payment_response  = isset($arr_stripe_response['payment_data']) ? $arr_stripe_response['payment_data'] :'';
                }
                else
                {
                    $obj_booking_master->payment_status    = 'PENDING';
                    $obj_booking_master->payment_response  = '';
                }

                if($total_cent_charge<=0)
                {
                    $obj_booking_master->payment_status    = 'SUCCESS';
                    $obj_booking_master->payment_type      = '';
                    $obj_booking_master->payment_response  = 'O amount cannot process.';   
                }

                $status = $obj_booking_master->save();
                if($status){

                    /*Send on signal notification from user to specific driver*/
                    $arr_notification_data = 
                                            [
                                                'title'             => 'Customer cancel booking request.',
                                                'record_id'         => $booking_id,
                                                'enc_user_id'       => $enc_user_id,
                                                'notification_type' => 'TRIP_CANCEL_BY_USER',
                                                'user_type'         => 'DRIVER',
                                            ];

                    $this->send_on_signal_notification($arr_notification_data);

                    /*Send on signal notification from user to specific driver*/
                    $arr_notification_data = 
                                            [
                                                'title'             => 'Customer cancel booking request.',
                                                'record_id'         => $booking_id,
                                                'enc_user_id'       => $enc_user_id,
                                                'notification_type' => 'TRIP_CANCEL_BY_USER',
                                                'user_type'         => 'WEB',
                                            ];

                    $this->send_on_signal_notification($arr_notification_data);

                    /*Send on signal notification to User that he accept driver*/
                    $arr_notification_data = 
                                            [
                                                'title'             => 'You have cancel current trip',
                                                'record_id'         => $booking_id,
                                                'enc_user_id'       => $user_id,
                                                'notification_type' => 'TRIP_CANCEL_BY_USER',
                                                'user_type'         => 'WEB',
                                            ];

                    $this->send_on_signal_notification($arr_notification_data);
                    
                    $driver_status = 'AVAILABLE';
                    $this->CommonDataService->change_driver_status($enc_user_id,$driver_status);
                    
                    /*send notification to admin*/
                    $arr_notification_data = $this->built_notification_data($booking_id); 
                    $this->NotificationsService->store_notification($arr_notification_data);

                    $arr_response['status'] = 'success';
                    $arr_response['msg']    = 'You have cancel current trip.';
                    $arr_response['data']   = [];
                    return $arr_response;

                }else{
                    $arr_response['status'] = 'error';
                    $arr_response['msg']    = 'Problem occurred, while update booking request details, Please try again.';
                    $arr_response['data']   = [];
                    return $arr_response;
                }
            }
            $arr_response['status'] = 'error';
            $arr_response['msg']    = 'Problem occurred, while update booking request details, Please try again.';
            $arr_response['data']   = [];
            return $arr_response;
        }
        $arr_response['status'] = 'error';
        $arr_response['msg']    = 'Problem occurred, while update booking request details, Please try again.';
        $arr_response['data']   = [];
        return $arr_response;   
    }

    /*
    |
    | common function to filter trip listing details accourding to status
    |
    */
    private function get_filter_trips($enc_user_id,$trip_type,$request)
    {
        $user_type = $request->input('user_type','');
        if($user_type == ''){
            $user_type = 'USER';
        }
        
        $arr_trips = [];
        $arr_trip_status = ['ONGOING','PENDING','COMPLETED','CANCELED'];
       
        if(!in_array($trip_type, $arr_trip_status)){
            
            $arr_response['status'] = 'error';
            $arr_response['msg']    = 'Invalid request type';
            $arr_response['data']    = [];
            return $arr_response;

        }   

        if($trip_type == 'PENDING')
        {
            $obj_trips  = $this->LoadPostRequestModel
                                        ->with('load_post_request_package_details')
                                        ->select("id","date",'request_time',"pickup_location","drop_location","pickup_lat","pickup_lng","drop_lat","drop_lng","load_post_image","request_status","is_future_request","is_request_process")
                                        ->whereIn('request_status',['USER_REQUEST','TIMEOUT','ACCEPT_BY_DRIVER','REJECT_BY_DRIVER','REJECT_BY_USER'])
                                        ->where('user_id',$enc_user_id)
                                        ->orderBy('id','DESC')
                                        ->paginate($this->per_page);

            if($obj_trips)
            {
                $arr_trips = $obj_trips->toArray();
            }
            return $arr_trips;                           
        }

        $arr_cancel_load_post = [];
        if($trip_type == 'CANCELED')
        {
            $obj_cancel_load_post  = $this->LoadPostRequestModel
                                        ->with(['load_post_request_package_details',
                                                'driver_details'=>function($query){
                                                        $query->select('id','first_name','last_name','email','country_code','mobile_no','profile_image');
                                                    },
                                                'user_details'=>function($query){
                                                        $query->select('id','first_name','last_name','email','country_code','mobile_no','profile_image');
                                                    },
                                                'driver_current_location_details'=>function($query){
                                                        $query->select('id','driver_id','status','current_latitude','current_longitude');
                                                    }
                                                ])
                                            ->select("id","user_id","driver_id","date as booking_date","pickup_location","drop_location","pickup_lat","pickup_lng","drop_lat","drop_lng","load_post_image","request_status as booking_status");

            if($user_type == 'USER')
            {
                $obj_cancel_load_post = $obj_cancel_load_post->where('user_id',$enc_user_id);
                $obj_cancel_load_post = $obj_cancel_load_post->where('request_status','CANCEL_BY_USER');
            }
            if($user_type == 'DRIVER')
            {
                // dd($enc_user_id);
                $obj_cancel_load_post = $obj_cancel_load_post
                                                ->whereHas('load_post_request_history_details',function($query) use ($enc_user_id) {
                                                    $query->where('driver_id',$enc_user_id);
                                                    $query->whereIn('status',['REJECT_BY_DRIVER','REJECT_BY_USER']);
                                                }); 
                                                // ->with('load_post_request_history_details');

                // $obj_cancel_load_post = $obj_cancel_load_post->where('request_status','CANCEL_BY_DRIVER');
                

            }
            $obj_cancel_load_post = $obj_cancel_load_post
                                        ->orderBy('id','DESC')
                                        ->get();

            if($obj_cancel_load_post)
            {
                $arr_cancel_load_post = $obj_cancel_load_post->toArray();
            }
            
            // dd($arr_cancel_load_post);

            if(isset($arr_cancel_load_post) && count($arr_cancel_load_post)>0)
            {
                foreach ($arr_cancel_load_post as $load_post_key => $load_post_value) 
                {
                    $arr_cancel_load_post[$load_post_key]['type'] = 'load_post';
                }
            }
        }
        $obj_trips  = $this->BookingMasterModel
                                    ->select('id','load_post_request_id','booking_unique_id','booking_date','booking_status','total_charge')
                                    ->whereHas('load_post_request_details',function($query) use($enc_user_id,$user_type) {
                                            $query->whereHas('driver_details',function($query){
                                            });
                                            $query->whereHas('user_details',function($query){
                                            });
                                            $query->whereHas('driver_current_location_details',function($query){
                                            });
                                            $query->where('request_status','ACCEPT_BY_USER');
                                            if($user_type == 'USER')
                                            {
                                                $query->where('user_id',$enc_user_id);
                                            }
                                            if($user_type == 'DRIVER')
                                            {
                                                $query->where('driver_id',$enc_user_id);
                                            }
                                    })
                                    ->with(['load_post_request_details'=> function($query) use($enc_user_id,$user_type) {
                                            $query->select('id','user_id','driver_id','pickup_location','drop_location','pickup_lat','pickup_lng','drop_lat','drop_lng','request_status','load_post_image');
                                            $query->where('request_status','ACCEPT_BY_USER');
                                            //$query->where('user_id',$enc_user_id);
                                            
                                            if($user_type == 'USER')
                                            {
                                                $query->where('user_id',$enc_user_id);
                                            }
                                            if($user_type == 'DRIVER')
                                            {
                                                $query->where('driver_id',$enc_user_id);
                                            }

                                            $query->with(['driver_details'=>function($query){
                                                        $query->select('id','first_name','last_name','email','country_code','mobile_no','profile_image');
                                                    }]);
                                            $query->with(['user_details'=>function($query){
                                                        $query->select('id','first_name','last_name','email','country_code','mobile_no','profile_image');
                                                    }]);
                                            $query->with(['driver_current_location_details'=>function($query){
                                                        $query->select('id','driver_id','status','current_latitude','current_longitude');
                                                    },'load_post_request_package_details']);
                                    },'review_details'=>function($query) use($enc_user_id,$user_type){
                                        $query->where('from_user_id',$enc_user_id);
                                    }]);

        if($trip_type == 'ONGOING')
        {
            $obj_trips = $obj_trips->whereIn('booking_status',['TO_BE_PICKED','IN_TRANSIT']);
        }
        else if($trip_type == 'COMPLETED')
        {
            $obj_trips = $obj_trips->where('booking_status','COMPLETED');
        }
        else if($trip_type == 'CANCELED')
        {
            $obj_trips = $obj_trips->whereIn('booking_status',['CANCEL_BY_USER','CANCEL_BY_DRIVER','CANCEL_BY_ADMIN']);
            $obj_trips = $obj_trips->orderBy('id','DESC');
            $obj_trips = $obj_trips->get();
            
            $arr_tmp_trips = [];
            if($obj_trips)
            {
                $arr_tmp_trips = $obj_trips->toArray();
                if(isset($arr_tmp_trips) && count($arr_tmp_trips)>0)
                {
                    foreach ($arr_tmp_trips as $tmp_key => $tmp_value) 
                    {
                        $arr_tmp_trips[$tmp_key]['type'] = 'normal_booking';
                    }
                }
            }
            $arr_all_cancel_trips = array_merge($arr_cancel_load_post,$arr_tmp_trips);
            
            $obj_all_cancel_trips = $this->make_pagination_links($arr_all_cancel_trips,$this->per_page);

            if($obj_all_cancel_trips)
            {
                $arr_trips = $obj_all_cancel_trips->toArray();
                $arr_trips['data'] = array_values($arr_trips['data']);

            }
            return $arr_trips;

        }
        else
        {
            /*if any of status not found then  return back with empty array */
            return [];
        }
        $obj_trips = $obj_trips->orderBy('id','DESC');
        $obj_trips = $obj_trips->paginate($this->per_page);

        if($obj_trips)
        {
            $arr_trips = $obj_trips->toArray();
        }
        return $arr_trips;
    }
    
    /*
    |
    | private class function to make driver avalible
    |
    */

    private function search_drivers_to_make_available($load_post_request_id,$driver_id)
    {
        $arr_load_post_request = $this->get_load_post_request_details($load_post_request_id);

        $pickup_lat           = isset($arr_load_post_request['pickup_lat']) ? floatval($arr_load_post_request['pickup_lat']) :0;
        $pickup_lng           = isset($arr_load_post_request['pickup_lng']) ? floatval($arr_load_post_request['pickup_lng']) :0;
        $package_volume       = isset($arr_load_post_request['load_post_request_package_details']['package_volume']) ? $arr_load_post_request['load_post_request_package_details']['package_volume'] :0;
        $distance             = $this->distance;
        $load_post_request_id = $load_post_request_id;

        $sql_query = '';
        $sql_query .= "Select ";
        $sql_query .= "users.id AS driver_id, ";
        $sql_query .= "driver_available_status.status as status, ";

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

        $sql_query .=  "users.id NOT IN ( '" . $driver_id . "' ) AND ";
        $sql_query .=  "users.is_active = '1' AND ";
        $sql_query .=  "roles.slug = 'driver' AND ";
        $sql_query .=  "driver_available_status.status = 'BUSY' AND ";
        $sql_query .=  " ( driver_car_relation.is_car_assign = '1' OR driver_car_relation.is_individual_vehicle = '1' ) AND ";
        $sql_query .=  "VT.vehicle_min_volume <= ".$package_volume." AND ";
        $sql_query .=  "VT.vehicle_max_volume >= ".$package_volume." ";
        $sql_query .=  "HAVING distance <=".$distance;
        
        $obj_driver_details =  \DB::select($sql_query);
        
        if(isset($obj_driver_details) && sizeof($obj_driver_details)>0)
        {
            foreach ($obj_driver_details as $key => $driver_details) 
            {
                if(isset($driver_details))
                {
                    $driver_status = 'AVAILABLE';
                    $enc_user_id = isset($driver_details->driver_id) ? $driver_details->driver_id :0;
                    $this->CommonDataService->change_driver_status($enc_user_id,$driver_status);
                }
            }
        }
        return true;
    }
    
    /*
    |
    | private class function to get load post record details
    |
    */

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
    
    /*
    |
    | private class function to genrate unique load post request number
    |
    */

    private function genrate_load_post_request_unique_number()
    {
        $secure = TRUE;    
        $bytes = openssl_random_pseudo_bytes(6, $secure);
        $order_ref_num = "LPR-".bin2hex($bytes);

        return strtoupper($order_ref_num);
    }

    /*
    |
    | private class function to genrate unique booking number
    |
    */
    
    private function genrate_booking_unique_number()
    {
        $secure = TRUE;    
        $bytes = openssl_random_pseudo_bytes(6, $secure);
        $order_ref_num = "QPB-".bin2hex($bytes);

        return strtoupper($order_ref_num);   
    }
    
    /*
    |
    | get address from latitide and longitude;
    |
    */

    private function genrate_transaction_unique_number()
    {
        $secure = TRUE;    
        $bytes = openssl_random_pseudo_bytes(6, $secure);
        $order_ref_num = "TRAN-".bin2hex($bytes);

        return strtoupper($order_ref_num);   
    }

    private function get_address_from_google_maps($lat,$lng)
    {
        $address = '';

        $url = 'https://maps.googleapis.com/maps/api/geocode/json?latlng='.trim($lat).','.trim($lng).'&key=AIzaSyCTScU19j-YU1Gt5xrFWlo4dwHoFF1wl-s';
        $json = @file_get_contents($url);
        
        $data = json_decode($json);
        if(isset($data->results[0]->formatted_address) && $data->results[0]->formatted_address!=''){
            $address = $data->results[0]->formatted_address;
        }

        return $address;

    }

    private function calculate_distance($origins,$destinations)
    {
        $arr_result = [];
        
        $arr_result['distance']        = '';
        $arr_result['actual_distance'] = 0;
        $arr_result['duration']        = '';
        $arr_result['actual_duration'] = 0;

        $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=".$origins."&destinations=".$destinations."&mode=driving&key=AIzaSyCTScU19j-YU1Gt5xrFWlo4dwHoFF1wl-s";
   
        $json = @file_get_contents($url);
        $data = json_decode($json);

        if(isset($data->status) && $data->status == 'OK')
        {
            if(isset($data->rows[0]) && sizeof($data->rows[0])>0)
            {
                if((isset($data->rows[0]->elements[0]) && sizeof($data->rows[0]->elements[0])>0) && $data->rows[0]->elements[0]->status == 'OK')
                {
                    $actual_distance = isset($data->rows[0]->elements[0]->distance->value) ? $data->rows[0]->elements[0]->distance->value :0;
                    $actual_distance_im_miles = 0;
                    
                    if($actual_distance>0){
                        $actual_distance_im_miles = ($actual_distance/1609.344);
                        $actual_distance_im_miles = round($actual_distance_im_miles,2);
                    }   
                    $actual_distance_im_miles = $actual_distance_im_miles.' Miles';

                    $arr_result['distance']        = $actual_distance_im_miles;
                    $arr_result['actual_distance'] = isset($data->rows[0]->elements[0]->distance->value) ? $data->rows[0]->elements[0]->distance->value :0;
                    $arr_result['duration']        = isset($data->rows[0]->elements[0]->duration->text) ? $data->rows[0]->elements[0]->duration->text :'';
                    $arr_result['actual_duration'] = isset($data->rows[0]->elements[0]->duration->value) ? $data->rows[0]->elements[0]->duration->value :0;
                }
            }
        }
        return $arr_result;
    }

    private function built_notification_data($booking_id)
    {
        $arr_booking_details = $this->get_booking_details($booking_id);

        $booking_unique_id  = isset($arr_booking_details['booking_unique_id']) ? $arr_booking_details['booking_unique_id'] :'';
        $payment_status     = isset($arr_booking_details['payment_status']) ? $arr_booking_details['payment_status'] :'';
        $booking_status     = isset($arr_booking_details['booking_status']) ? $arr_booking_details['booking_status'] :'';

        $notification_type = $title = '';

        if($payment_status == 'SUCCESS')
        {
            $notification_type = 'Trip Booking Payment';
            $title = 'Payment for Trip #'.$booking_unique_id.' successfully done';
        }

        if($payment_status == 'PENDING')
        {
            $notification_type = 'Trip Booking Payment';
            $title = 'Payment for Trip #'.$booking_unique_id.' is pending, Please contact with the Customer.';
        }

        if($payment_status == 'FAILED')
        {
            $notification_type = 'Trip Booking Payment';
            $title = 'Payment for Trip #'.$booking_unique_id.' failed, Please contact with the Customer.';
        }

        $view_url = '/'.config('app.project.admin_panel_slug').'/track_booking/view?enc_id='.base64_encode($booking_id).'&status='.base64_encode($booking_status).'&curr_page=booking_history';

        $arr_notification = [];
        $arr_notification['user_id']           = $this->CommonDataService->get_admin_id();
        $arr_notification['is_read']           = 0;
        $arr_notification['is_show']           = 0;
        $arr_notification['user_type']         = 'ADMIN';
        $arr_notification['notification_type'] = $notification_type;
        $arr_notification['title']             = $title;
        $arr_notification['view_url']          = $view_url;
        
        return $arr_notification;
    }

    private function built_user_invoice_email_data($booking_id)
    {
        $arr_booking_details = $this->get_booking_details($booking_id);
        
        $arr_data = filter_completed_trip_details($arr_booking_details);
        
        $distance = isset($arr_data['distance']) ? number_format($arr_data['distance'],2) :'0';
        $distance = $distance.' Miles';
        
        $time_taken = isset($arr_data['total_minutes_trip']) ? number_format($arr_data['total_minutes_trip'],2) : '0';
        $time_taken = $time_taken.' Min.';

        $discount_amount = isset($arr_data['applied_promo_code_charge']) ? number_format($arr_data['applied_promo_code_charge'],2) : '0';
        $discount_amount = $discount_amount.' USD';

        $total_amount = isset($arr_data['total_charge']) ? number_format($arr_data['total_charge'],2) : '0';
        $total_amount = $total_amount.' USD';

        $invoice_attachment = '';
        $invoice_attachment_name = 'TRIP_INVOICE_'.$booking_id.'.pdf';
        
        if(file_exists($this->invoice_base_img_path.$invoice_attachment_name))
        {
            $invoice_attachment = $this->invoice_base_img_path.$invoice_attachment_name;
        }

        $arr_built_content = [

            'FULL_NAME'            => isset($arr_data['user_name']) ? $arr_data['user_name'] : '',
            'BOOKING_ID'           => isset($arr_data['booking_unique_id']) ? $arr_data['booking_unique_id'] : '',
            'PICKUP_LOCATION'      => isset($arr_data['pickup_location']) ? $arr_data['pickup_location'] : '',
            'DROP_LOCATION'        => isset($arr_data['drop_location']) ? $arr_data['drop_location'] :'',
            'DISTANCE'             => $distance,
            'TIME_TAKEN'           => $time_taken,
            'DISCOUNT_AMOUNT'      => $discount_amount,
            'TOTAL_AMOUNT'         => $total_amount,
            'PROJECT_NAME'         => config('app.project.name')];

            $arr_mail_data                      = [];
            $arr_mail_data['email_template_id'] = '19';
            $arr_mail_data['arr_built_content'] = $arr_built_content;
            $arr_mail_data['attachment']        = $invoice_attachment;
            $arr_mail_data['user']              = ['email'=>isset($arr_data['user_email']) ? $arr_data['user_email'] : ''];

        return $arr_mail_data;
    }

    private function built_driver_invoice_email_data($booking_id)
    {
        $arr_booking_details = $this->get_booking_details($booking_id);
        
        $arr_data = filter_completed_trip_details($arr_booking_details);

        $distance = isset($arr_data['distance']) ? number_format($arr_data['distance'],2) :'0';
        $distance = $distance.' Miles';
        
        $time_taken = isset($arr_data['total_minutes_trip']) ? number_format($arr_data['total_minutes_trip'],2) : '0';
        $time_taken = $time_taken.' Min.';

        $discount_amount = isset($arr_data['applied_promo_code_charge']) ? number_format($arr_data['applied_promo_code_charge'],2) : '0';
        $discount_amount = $discount_amount.' USD';

        $total_amount = isset($arr_data['total_charge']) ? number_format($arr_data['total_charge'],2) : '0';
        $total_amount = $total_amount.' USD';

        $invoice_attachment = '';
        $invoice_attachment_name = 'TRIP_INVOICE_'.$booking_id.'.pdf';
        
        if(file_exists($this->invoice_base_img_path.$invoice_attachment_name))
        {
            $invoice_attachment = $this->invoice_base_img_path.$invoice_attachment_name;
        }

        $arr_built_content = [

            'FULL_NAME'            => isset($arr_data['driver_name']) ? $arr_data['driver_name'] : '',
            'BOOKING_ID'           => isset($arr_data['booking_unique_id']) ? $arr_data['booking_unique_id'] : '',
            'PICKUP_LOCATION'      => isset($arr_data['pickup_location']) ? $arr_data['pickup_location'] : '',
            'DROP_LOCATION'        => isset($arr_data['drop_location']) ? $arr_data['drop_location'] :'',
            'DISTANCE'             => $distance,
            'TIME_TAKEN'           => $time_taken,
            'DISCOUNT_AMOUNT'      => $discount_amount,
            'TOTAL_AMOUNT'         => $total_amount,
            'PROJECT_NAME'         => config('app.project.name')];

            $arr_mail_data                      = [];
            $arr_mail_data['email_template_id'] = '20';
            $arr_mail_data['arr_built_content'] = $arr_built_content;
            $arr_mail_data['attachment']        = $invoice_attachment;
            $arr_mail_data['user']              = ['email'=>isset($arr_data['driver_email']) ? $arr_data['driver_email'] : ''];

        return $arr_mail_data;
    }

    private function built_user_notification_data($booking_id)
    {
        $arr_booking_details = $this->get_booking_details($booking_id);

        $user_id           = isset($arr_booking_details['load_post_request_details']['user_id']) ? $arr_booking_details['load_post_request_details']['user_id'] :'';
        $booking_unique_id = isset($arr_booking_details['booking_unique_id']) ? $arr_booking_details['booking_unique_id'] :'';
        $payment_status    = isset($arr_booking_details['payment_status']) ? $arr_booking_details['payment_status'] :'';
        $booking_status    = isset($arr_booking_details['booking_status']) ? $arr_booking_details['booking_status'] :'';

        $notification_type = $title = '';

        if($payment_status == 'SUCCESS')
        {
            $notification_type = 'Trip Booking Payment';
            $title = 'Payment for Trip #'.$booking_unique_id.' successfully done';
        }

        if($payment_status == 'PENDING')
        {
            $notification_type = 'Trip Booking Payment';
            $title = 'Payment for Trip #'.$booking_unique_id.' is pending, Please contact with the QuickPick customer support.';
        }

        if($payment_status == 'FAILED')
        {
            $notification_type = 'Trip Booking Payment';
            $title = 'Payment for Trip #'.$booking_unique_id.' failed, Please contact with the QuickPick customer support.';
        }

        // $view_url = '/'.config('app.project.admin_panel_slug').'/track_booking/view?enc_id='.base64_encode($booking_id).'&status='.base64_encode($booking_status).'&curr_page=booking_history';
        $view_url = '';

        $arr_notification = [];
        $arr_notification['user_id']           = $user_id;
        $arr_notification['is_read']           = 0;
        $arr_notification['is_show']           = 0;
        $arr_notification['user_type']         = 'RIDER';
        $arr_notification['notification_type'] = $notification_type;
        $arr_notification['title']             = $title;
        $arr_notification['view_url']          = $view_url;
        
        return $arr_notification;
    }
    

    private function built_load_post_notification_data($load_post_request_id)
    {
        $arr_load_post_request_details = $this->get_load_post_request_details($load_post_request_id);
        
        $load_post_request_unique_id  = isset($arr_load_post_request_details['load_post_request_unique_id']) ? $arr_load_post_request_details['load_post_request_unique_id'] :'';
        
        $first_name = isset($arr_load_post_request_details['user_details']['first_name']) ? $arr_load_post_request_details['user_details']['first_name'] : '';
        $last_name  = isset($arr_load_post_request_details['user_details']['last_name']) ? $arr_load_post_request_details['user_details']['last_name'] : '';
        $full_name  = $first_name.' '.$last_name;
        $full_name  = ($full_name!=' ') ? $full_name : '-'; 

        $notification_type = 'Driver Assistant Request';
        $title = 'Driver Assistant Request for #'.$load_post_request_unique_id.' received from '.$full_name;

        $view_url = '/'.config('app.project.admin_panel_slug').'/assistant/view/'.base64_encode($load_post_request_id);

        $arr_notification = [];
        $arr_notification['user_id']           = $this->CommonDataService->get_admin_id();
        $arr_notification['is_read']           = 0;
        $arr_notification['is_show']           = 0;
        $arr_notification['user_type']         = 'ADMIN';
        $arr_notification['notification_type'] = $notification_type;
        $arr_notification['title']             = $title;
        $arr_notification['view_url']          = $view_url;
        
        return $arr_notification;
    }

    private function get_booking_details($booking_id)
    {
        $arr_booking_master = [];
        $obj_booking_master = $this->BookingMasterModel
                                            // ->with(['load_post_request_details'=> function($query){
                                            //         $query->select('id','user_id','driver_id','pickup_location','drop_location','pickup_lat','pickup_lng','drop_lat','drop_lng','request_status','load_post_image');
                                            //         $query->with(['driver_details'=>function($query){
                                            //                     $query->select('id','first_name','last_name','email','mobile_no','profile_image','stripe_account_id','company_id','is_company_driver');
                                            //                     $query->with('company_details');
                                            //                 }]);
                                            //         $query->with(['user_details'=>function($query){
                                            //                     $query->select('id','first_name','last_name','email','mobile_no','profile_image');
                                            //                 }]);
                                            // }])
                                            ->with(['load_post_request_details'=> function($query){
                                                    $query->select('id','user_id','driver_id','vehicle_id','pickup_location','drop_location','pickup_lat','pickup_lng','drop_lat','drop_lng','request_status','load_post_image');
                                                    $query->with(['driver_details'=>function($query){
                                                                $query->select('id','first_name','last_name','email','mobile_no','profile_image','stripe_account_id','company_id','is_company_driver');
                                                                $query->with('company_details');
                                                            }]);
                                                    $query->with(['user_details'=>function($query){
                                                                $query->select('id','first_name','last_name','email','mobile_no','profile_image');
                                                            }]);
                                                    $query->with(['vehicle_details'=>function($query){
                                                                $query->with('vehicle_type_details');
                                                            }]);
                                                    $query->with(['load_post_request_package_details'=>function($query){
                                                            }]);
                                                    
                                            }])
                                            ->where('id',$booking_id)
                                            ->first();
        if($obj_booking_master)
        {
            $arr_booking_master = $obj_booking_master->toArray();
        }
        return $arr_booking_master;
      
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
    // function GetDrivingDistance($lat1, $lat2, $long1, $long2)
    // {
          //  $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=".$origins."&destinations=".$destinations."&mode=driving&key=AIzaSyCTScU19j-YU1Gt5xrFWlo4dwHoFF1wl-s";
    //     $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=".$lat1.",".$long1."&destinations=".$lat2.",".$long2."&mode=driving&&key=AIzaSyCTScU19j-YU1Gt5xrFWlo4dwHoFF1wl-s";

    //     //$url = 'https://maps.googleapis.com/maps/api/distancematrix/json?units=imperial&origins=".$origin."&destinations=".$destination."&key=AIzaSyCccvQtzVx4aAt05YnfzJDSWEzPiVnNVsY"';
        
    //     https://maps.googleapis.com/maps/api/distancematrix/json?origins=\(destination)&destinations=\(origin)&mode=driving&key=AIzaSyCTScU19j-YU1Gt5xrFWlo4dwHoFF1wl-s"

    //     $ch = curl_init();
    //     curl_setopt($ch, CURLOPT_URL, $url);
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    //     curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
    //     curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    //     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    //     $response = curl_exec($ch);
    //     curl_close($ch);
    //     $response_a = json_decode($response, true);
        
    //     dd($response_a);

    //     $dist = $response_a['rows'][0]['elements'][0]['distance']['text'];
    //     $time = $response_a['rows'][0]['elements'][0]['duration']['text'];

    //     return array('distance' => $dist, 'time' => $time);
    // }

    // private function calculate_total_distance($arr_booking_master_coordinate)
    // {

    //     $total_distance = 0;
    //     $earthRadius = 6371000;

    //     if(isset($arr_booking_master_coordinate) && sizeof($arr_booking_master_coordinate)>0)
    //     {
    //         for ($i=0; $i < count($arr_booking_master_coordinate) ; $i++) 
    //         { 
    //             $next_index = $i + 1;
                
    //             $latFrom = isset($arr_booking_master_coordinate[$i]['lat']) ? $arr_booking_master_coordinate[$i]['lat'] :0;
    //             $lonFrom = isset($arr_booking_master_coordinate[$i]['lng']) ? $arr_booking_master_coordinate[$i]['lng'] :0;
    //             $latTo   = isset($arr_booking_master_coordinate[$next_index]['lat']) ? $arr_booking_master_coordinate[$next_index]['lat'] :0;
    //             $lonTo   = isset($arr_booking_master_coordinate[$next_index]['lng']) ? $arr_booking_master_coordinate[$next_index]['lng'] :0;

    //             $theta = $latFrom - $lonTo;
    //             $dist = sin(deg2rad($latFrom)) * sin(deg2rad($lonFrom)) +  cos(deg2rad($latFrom)) * cos(deg2rad($latTo)) * cos(deg2rad($theta));
    //             $dist = acos($dist);
    //             $dist = rad2deg($dist);
    //             $miles = $dist * 60 * 1.1515;
    //             $total_distance = $total_distance + ($miles * 1.609344);

    //             /*$latFrom = deg2rad($latFrom);
    //             $lonFrom = deg2rad($lonFrom);
    //             $latTo   = deg2rad($latTo);
    //             $lonTo   = deg2rad($lonTo);
    
    //             $latDelta = $latTo - $latFrom;
    //             $lonDelta = $lonTo - $lonFrom;

    //             $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) + cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
    //             $total_distance = $total_distance + ($angle * $earthRadius);*/
    //         }
    //     }
        
    //     dd($total_distance);

    //     return $total_distance;
    // }

}