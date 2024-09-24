<?php

namespace App\Common\Services\Web;

use App\Models\UserModel;
use App\Models\LoadPostRequestModel;
use App\Models\LoadPostRequestHistoryModel;
use App\Models\LoadPostRequestPackageDetailsModel;
use App\Models\BookingMasterModel;
use App\Models\BookingMasterCoordinateModel;
use App\Models\DepositMoneyModel;
use App\Models\DriverStatusModel;
use App\Models\PromoOfferAppliedDetailsModel;



use App\Common\Services\CommonDataService;
use App\Common\Services\ValidateAreaService;
use App\Common\Services\StripeService;
use App\Common\Services\NotificationsService;


use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;

use Validator;


class UserService
{
         public function __construct(
                                    UserModel                          $user,
                                    LoadPostRequestModel               $load_post_request,                 
                                    LoadPostRequestHistoryModel        $load_post_request_history,
                                    LoadPostRequestPackageDetailsModel $load_post_request_package_details,
                                    BookingMasterModel                 $booking_master,
                                    BookingMasterCoordinateModel       $booking_master_coordinate,
                                    DepositMoneyModel                  $deposit_money,
                                    PromoOfferAppliedDetailsModel      $promo_offer_applied_details,
                                    CommonDataService                  $common_data_service,
                                    ValidateAreaService                $validatearea_service,
                                    StripeService                      $stripe_service,
                                    NotificationsService               $notifications_service
                               )   
    {
        $this->UserModel                          = $user;
        $this->LoadPostRequestModel               = $load_post_request;
        $this->LoadPostRequestHistoryModel        = $load_post_request_history;
        $this->LoadPostRequestPackageDetailsModel = $load_post_request_package_details;
        $this->BookingMasterModel                 = $booking_master;
        $this->BookingMasterCoordinateModel       = $booking_master_coordinate;
        $this->DepositMoneyModel                  = $deposit_money;
        $this->PromoOfferAppliedDetailsModel      = $promo_offer_applied_details;
        $this->CommonDataService                  = $common_data_service;
        $this->ValidateAreaService                = $validatearea_service;
        $this->StripeService                      = $stripe_service;
        $this->NotificationsService               = $notifications_service;
        $this->distance                           = 50;
        
        $this->per_page                           = 5;

        $this->load_post_img_public_path    = url('/').config('app.project.img_path.load_post_img');
        $this->load_post_img_base_path      = base_path().config('app.project.img_path.load_post_img');
        
        $this->user_profile_public_img_path = url('/').config('app.project.img_path.user_profile_images');
        $this->user_profile_base_img_path   = base_path().config('app.project.img_path.user_profile_images');

        $this->invoice_public_img_path = url('/').config('app.project.img_path.invoice');
        $this->invoice_base_img_path   = base_path().config('app.project.img_path.invoice');
    }
    public function load_post_details($request)
    {
        $user_id = validate_user_login_id();
        if ($user_id == 0) 
        {
            $arr_response['status'] = 'error';
            $arr_response['msg']    = 'Invalid user token';
            $arr_response['data']    = [];
            return $arr_response;
        }
        
        $load_post_request_id = $request->input('load_post_request_id');
        $load_post_request_id = base64_decode($load_post_request_id);
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

                $request_status = isset($arr_load_post_request['request_status']) ? $arr_load_post_request['request_status'] : '';
                $is_future_request = isset($arr_load_post_request['is_future_request']) ? $arr_load_post_request['is_future_request'] : '0';
                $is_request_process = isset($arr_load_post_request['is_request_process']) ? $arr_load_post_request['is_request_process'] : '0';
                
                $first_name = '';
                if($request_status == 'NEW_REQUEST'){
                    $first_name = 'Please Select Driver';
                }
                else if($request_status == 'USER_REQUEST' && $is_future_request == '0'){
                    $first_name = 'Waiting for Driver to Accept';
                }
                else if($request_status == 'USER_REQUEST' && $is_future_request == '1'){
                    if($is_request_process == '0'){
                        $first_name = 'Future Booking Request';
                    }
                    else if($is_request_process == '1'){
                        $first_name = 'Waiting for Driver to Accept';
                    }
                }
                else if($request_status == 'ACCEPT_BY_DRIVER'){
                    $first_name = 'Please respond to driver';
                }
                else if($request_status == 'REJECT_BY_DRIVER'){
                    $first_name = 'Waiting for Driver to Accept';
                }
                else if($request_status == 'REJECT_BY_USER'){
                    $first_name = 'Waiting for Driver to Accept';
                }
                else if($request_status == 'TIMEOUT'){
                    $first_name = 'Waiting for Driver to Accept';
                }else if($request_status == 'CANCEL_BY_USER'){
                    $first_name = 'Canceled by you';
                }else if($request_status == 'CANCEL_BY_ADMIN'){
                    $first_name = 'Canceled by '.config('app.project.name').' Admin';
                }

                $profile_image = url('/uploads/listing-default-logo.png');

                $driver_fare_charge = 0;

                $arr_result                         = [];
                $arr_result['first_name']           = $first_name;
                $arr_result['last_name']            = '';
                $arr_result['email']                = '';
                $arr_result['mobile_no']            = '';
                $arr_result['profile_image']        = $profile_image;
                $arr_result['fair_charge']          = $driver_fare_charge;
                $arr_result['load_post_request_id'] = isset($arr_load_post_request['id']) ? $arr_load_post_request['id'] : 0;
                $arr_result['user_id']              = isset($arr_load_post_request['user_id']) ? $arr_load_post_request['user_id'] : 0;
                $arr_result['driver_id']            = isset($arr_load_post_request['driver_id']) ? $arr_load_post_request['driver_id'] : 0;
                $arr_result['pickup_location']      = isset($arr_load_post_request['pickup_location']) ? $arr_load_post_request['pickup_location'] : '';
                $arr_result['pickup_lat']           = isset($arr_load_post_request['pickup_lat']) ? doubleval($arr_load_post_request['pickup_lat']) : floatval(0);
                $arr_result['pickup_lng']           = isset($arr_load_post_request['pickup_lng']) ? doubleval($arr_load_post_request['pickup_lng']) : floatval(0);
                $arr_result['drop_lat']             = isset($arr_load_post_request['drop_lat']) ? doubleval($arr_load_post_request['drop_lat']) : floatval(0);
                $arr_result['drop_lng']             = isset($arr_load_post_request['drop_lng']) ? doubleval($arr_load_post_request['drop_lng']) : floatval(0);
                $arr_result['drop_location']        = isset($arr_load_post_request['drop_location']) ? $arr_load_post_request['drop_location'] : '';
                $arr_result['booking_date']         = isset($arr_load_post_request['date']) ? date('d M Y',strtotime($arr_load_post_request['date'])) : '';
                $arr_result['request_time']         = isset($arr_load_post_request['request_time']) ? date('H:i:s',strtotime($arr_load_post_request['request_time'])) : '';
                $arr_result['is_future_request']    = isset($arr_load_post_request['is_future_request']) ? $arr_load_post_request['is_future_request'] : '';
                $arr_result['request_status']       = isset($arr_load_post_request['request_status']) ? $arr_load_post_request['request_status'] : '';
                $arr_result['package_type']         = isset($arr_load_post_request['load_post_request_package_details']['package_type']) ? $arr_load_post_request['load_post_request_package_details']['package_type'] : '';
                $arr_result['package_length']       = isset($arr_load_post_request['load_post_request_package_details']['package_length']) ? $arr_load_post_request['load_post_request_package_details']['package_length'] : 0;
                $arr_result['package_breadth']      = isset($arr_load_post_request['load_post_request_package_details']['package_breadth']) ? $arr_load_post_request['load_post_request_package_details']['package_breadth'] : 0;
                $arr_result['package_height']       = isset($arr_load_post_request['load_post_request_package_details']['package_height']) ? $arr_load_post_request['load_post_request_package_details']['package_height'] : 0;
                $arr_result['package_weight']       = isset($arr_load_post_request['load_post_request_package_details']['package_weight']) ? $arr_load_post_request['load_post_request_package_details']['package_weight'] : 0;
                $arr_result['package_quantity']     = isset($arr_load_post_request['load_post_request_package_details']['package_quantity']) ? $arr_load_post_request['load_post_request_package_details']['package_quantity'] : 0;

                $driver_id = isset($arr_load_post_request['driver_id']) ? $arr_load_post_request['driver_id'] : 0;

                $arr_trip_status = ['NEW_REQUEST','USER_REQUEST','ACCEPT_BY_DRIVER'];
                
                if($driver_id!=0 && !in_array($request_status, $arr_trip_status))
                {
                    $driver_fare_charge        = $this->CommonDataService->get_driver_fair_charge($driver_id);
                    $arr_result['fair_charge'] = $driver_fare_charge;

                    $arr_result['first_name']    = isset($arr_load_post_request['driver_details']['first_name']) ? $arr_load_post_request['driver_details']['first_name'] : '';
                    $arr_result['last_name']     = isset($arr_load_post_request['driver_details']['last_name']) ? $arr_load_post_request['driver_details']['last_name'] : '';
                    $arr_result['email']         = isset($arr_load_post_request['driver_details']['email']) ? $arr_load_post_request['driver_details']['email'] : '';
                    
                    $country_code   = isset($arr_load_post_request['driver_details']['country_code']) ? $arr_load_post_request['driver_details']['country_code'] : '';
                    $mobile_no      = isset($arr_load_post_request['driver_details']['mobile_no']) ? $arr_load_post_request['driver_details']['mobile_no'] : '';
                    $full_mobile_no = $country_code.''.$mobile_no;
                    $full_mobile_no = ($full_mobile_no!='')?$full_mobile_no :'';

                    $arr_result['mobile_no']     = $full_mobile_no;

                    $profile_image = url('/uploads/default-profile.png');

                    $tmp_profile_image = isset($arr_load_post_request['driver_details']['profile_image']) ? $arr_load_post_request['driver_details']['profile_image'] : '';

                    if($tmp_profile_image!='' && file_exists($this->user_profile_base_img_path.$tmp_profile_image))
                    {
                       $profile_image = $this->user_profile_public_img_path.$tmp_profile_image;
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
                    
                    /*convert meter to kilomiter*/
                    if($actual_distance>0){
                        $actual_distance = ($actual_distance/1000);
                        $actual_distance = round($actual_distance,2);
                    }   
                    
                    $calculated_farecharge = 0;

                    $calculated_farecharge = ($actual_distance * $driver_fare_charge); 

                    $arr_result['calculated_farecharge'] = doubleval($calculated_farecharge);

                    $driver_origins      = $driver_latitude.",".$driver_longitude;
                    $driver_destinations = $pickup_lat.",".$pickup_lng;
                    
                    $arr_driver_distance = $this->calculate_distance($driver_origins,$driver_destinations);
                    
                    $arr_result['driver_distance'] = isset($arr_driver_distance['distance']) ? $arr_driver_distance['distance'] :'';
                    $arr_result['driver_duration'] = isset($arr_driver_distance['duration']) ? $arr_driver_distance['duration'] :'';
                }

                
                $arr_response['status'] = 'success';
                $arr_response['msg']    = 'Load post details found successfully.';
                $arr_response['data']   = $arr_result;
                return $arr_response;   

                
            }
            else
            {
                $arr_response['status'] = 'error';
                $arr_response['msg']    = 'Problem occurred, fetching load post details,Please try again.';
                $arr_response['data']   = [];
                return $arr_response;   
            }
        }
        $arr_response['status'] = 'error';
        $arr_response['msg']    = 'Problem occurred, fetching load post details,Please try again.';
        $arr_response['data']   = [];
        return $arr_response;
    }
    /*
    |
    | when user post new load then save load details in database and search nearby drivers with matching criteria
    |
    */
    public function store_load_post_request($request)
    {
        /*send notification to admin*/
        $arr_rules = $arr_response = [];
        $user_id     = validate_user_login_id();

        if ($user_id == 0) 
        {
            $arr_response['status'] = 'error';
            $arr_response['msg']    = 'Invalid user token';
            $arr_response['data']    = [];
            return $arr_response;
        }
        

        $arr_rules['request_type']          = "required";
        $arr_rules['pickup_location']       = "required";
        $arr_rules['pickup_lat']            = "required";
        $arr_rules['pickup_lng']            = "required";
        $arr_rules['drop_location']         = "required";
        $arr_rules['drop_lat']              = "required";
        $arr_rules['drop_lng']              = "required";
        // Package details
        $arr_rules['action_type']           = "required";
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
        $is_admin_assistant = "NO";

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
            $arr_driver_vehicle_type = array_values($arr_driver_vehicle_type);
            
            
            $arr_tmp_data                            = [];
            $arr_tmp_data['load_post_request_id']    = 0;
            $arr_tmp_data['arr_driver_vehicle_type'] = $arr_driver_vehicle_type;

            $arr_response['status'] = 'success';
            $arr_response['msg']    = 'Drivers with vehicle type get successfully.';
            $arr_response['data']   = $arr_tmp_data;
            return $arr_response;
        }
        else if($action_type == 'book')
        {
            $request_time = $future_request_date  = '';

            $is_future_request    = $request->input('is_future_request');
            $vehicle_type_id      = $request->input('vehicle_type_id');

            if($request->has('future_request_date') && $request->input('future_request_date')!=''){
                $future_request_date = date('Y-m-d',strtotime($request->input('future_request_date')));
                $future_request_date = $future_request_date.' '.date('H:i:s');
            }

            if($request->has('request_time') && $request->input('request_time')!=''){
                $request_time = date('H:i:s',strtotime($request->input('request_time')));
            }
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
                $arr_tmp_data['arr_driver_vehicle_type'] = [];

                $arr_response['status'] = 'success';
                $arr_response['msg']    = 'For update information regarding your delivery, click the menu button at your screen and select “My Bookings”';
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

    public function load_post_book_driver_details($request)
    {
        $user_id = validate_user_login_id();
        if ($user_id == 0) 
        {
            $arr_response['status'] = 'error';
            $arr_response['msg']    = 'Invalid user token';
            $arr_response['data']    = [];
            return $arr_response;
        }
        
        $load_post_request_id = $request->input('load_post_request_id');
        $load_post_request_id = base64_decode($load_post_request_id);
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

                $request_status = isset($arr_load_post_request['request_status']) ? $arr_load_post_request['request_status'] : '';
                $first_name = '';
                if($request_status == 'NEW_REQUEST'){
                    $first_name = 'Please Select Driver';
                }
                else if($request_status == 'USER_REQUEST'){
                    $first_name = 'Waiting for Driver to Accept';
                }

                $profile_image = url('/uploads/listing-default-logo.png');

                $driver_fare_charge = 0;

                $arr_result                         = [];
                $arr_result['first_name']           = $first_name;
                $arr_result['last_name']            = '';
                $arr_result['email']                = '';
                $arr_result['mobile_no']            = '';
                $arr_result['profile_image']        = $profile_image;
                $arr_result['fair_charge']          = $driver_fare_charge;
                $arr_result['load_post_request_id'] = isset($arr_load_post_request['id']) ? $arr_load_post_request['id'] : 0;
                $arr_result['user_id']              = isset($arr_load_post_request['user_id']) ? $arr_load_post_request['user_id'] : 0;
                $arr_result['driver_id']            = isset($arr_load_post_request['driver_id']) ? $arr_load_post_request['driver_id'] : 0;
                $arr_result['pickup_location']      = isset($arr_load_post_request['pickup_location']) ? $arr_load_post_request['pickup_location'] : '';
                $arr_result['pickup_lat']           = isset($arr_load_post_request['pickup_lat']) ? doubleval($arr_load_post_request['pickup_lat']) : floatval(0);
                $arr_result['pickup_lng']           = isset($arr_load_post_request['pickup_lng']) ? doubleval($arr_load_post_request['pickup_lng']) : floatval(0);
                $arr_result['drop_lat']             = isset($arr_load_post_request['drop_lat']) ? doubleval($arr_load_post_request['drop_lat']) : floatval(0);
                $arr_result['drop_lng']             = isset($arr_load_post_request['drop_lng']) ? doubleval($arr_load_post_request['drop_lng']) : floatval(0);
                $arr_result['drop_location']        = isset($arr_load_post_request['drop_location']) ? $arr_load_post_request['drop_location'] : '';
                $arr_result['booking_date']         = isset($arr_load_post_request['date']) ? date('d M Y',strtotime($arr_load_post_request['date'])) : '';
                $arr_result['request_status']       = isset($arr_load_post_request['request_status']) ? $arr_load_post_request['request_status'] : '';
                $arr_result['package_type']         = isset($arr_load_post_request['load_post_request_package_details']['package_type']) ? $arr_load_post_request['load_post_request_package_details']['package_type'] : '';
                $arr_result['package_length']       = isset($arr_load_post_request['load_post_request_package_details']['package_length']) ? $arr_load_post_request['load_post_request_package_details']['package_length'] : 0;
                $arr_result['package_breadth']      = isset($arr_load_post_request['load_post_request_package_details']['package_breadth']) ? $arr_load_post_request['load_post_request_package_details']['package_breadth'] : 0;
                $arr_result['package_height']       = isset($arr_load_post_request['load_post_request_package_details']['package_height']) ? $arr_load_post_request['load_post_request_package_details']['package_height'] : 0;
                $arr_result['package_weight']       = isset($arr_load_post_request['load_post_request_package_details']['package_weight']) ? $arr_load_post_request['load_post_request_package_details']['package_weight'] : 0;
                $arr_result['package_quantity']     = isset($arr_load_post_request['load_post_request_package_details']['package_quantity']) ? $arr_load_post_request['load_post_request_package_details']['package_quantity'] : 0;
                $arr_result['package_volume']       = isset($arr_load_post_request['load_post_request_package_details']['package_volume']) ? $arr_load_post_request['load_post_request_package_details']['package_volume'] : 0;
                
                $arr_trip_status = ['NEW_REQUEST','USER_REQUEST'];
                
                $str_except_driver_id = $this->get_user_driver_id($user_id);

                $arr_driver_search_data = 
                                    [
                                        'pickup_lat'           => isset($arr_load_post_request['pickup_lat']) ? doubleval($arr_load_post_request['pickup_lat']) : floatval(0),
                                        'pickup_lng'           => isset($arr_load_post_request['pickup_lng']) ? doubleval($arr_load_post_request['pickup_lng']) : floatval(0),
                                        'package_volume'       => isset($arr_load_post_request['load_post_request_package_details']['package_volume']) ? $arr_load_post_request['load_post_request_package_details']['package_volume'] : 0,
                                        'package_weight'       => isset($arr_load_post_request['load_post_request_package_details']['package_weight']) ? $arr_load_post_request['load_post_request_package_details']['package_weight'] : 0,
                                        'package_type'         => isset($arr_load_post_request['load_post_request_package_details']['package_type']) ? $arr_load_post_request['load_post_request_package_details']['package_type'] : '',
                                        'package_quantity'     => isset($arr_load_post_request['load_post_request_package_details']['package_quantity']) ? intval($arr_load_post_request['load_post_request_package_details']['package_quantity']) : 0,
                                        'distance'             => $this->distance,
                                        'load_post_request_id' => $load_post_request_id,
                                        'str_except_driver_id' => strval($str_except_driver_id)
                                    ];

                $arr_driver_vehicle_type = $this->search_nearby_drivers($arr_driver_search_data);
                
                // dd($arr_driver_vehicle_type);

                $arr_result['arr_driver_vehicle_type'] = $arr_driver_vehicle_type;

                $arr_response['status'] = 'success';
                $arr_response['msg']    = 'Load post details found successfully.';
                $arr_response['data']   = $arr_result;
                return $arr_response;   

                
            }
            else
            {
                $arr_response['status'] = 'error';
                $arr_response['msg']    = 'Problem occurred, fetching load post details,Please try again.';
                $arr_response['data']   = [];
                return $arr_response;   
            }
        }
        $arr_response['status'] = 'error';
        $arr_response['msg']    = 'Problem occurred, fetching load post details,Please try again.';
        $arr_response['data']   = [];
        return $arr_response;
    }
    public function process_to_book_driver($request)
    {
        $user_id = validate_user_login_id();
        if ($user_id == 0) 
        {
            $arr_response['status'] = 'error';
            $arr_response['msg']    = 'Invalid user token';
            $arr_response['data']    = [];
            return $arr_response;
        }
        $load_post_request_id = $request->input('load_post_request_id');
        $request_status       = $request->input('request_status');
        $vehicle_type_id      = $request->input('vehicle_type_id');
        $is_future_request    = $request->input('is_future_request');
        
        $future_request_date  = '';
        $request_time         = $request->input('request_time');

        if($request->has('future_request_date') && $request->input('future_request_date')!=''){
            $future_request_date = date('Y-m-d',strtotime($request->input('future_request_date')));
            $future_request_date = $future_request_date.' '.date('H:i:s');
        }

        if($request->has('request_time') && $request->input('request_time')!=''){
            $request_time = date('H:i:s',strtotime($request->input('request_time')));
        }

        if ($load_post_request_id == '' || $request_status == '') 
        {
            $arr_response['status'] = 'error';
            $arr_response['msg']    = 'Problem occurred, while processing book driver request';
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

        $arr_available_drivers = [];

        if($user_id!='' && $vehicle_type_id!='' && $load_post_request_id!='' && $request_status!='')
        {
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
                $obj_load_post_request->created_at        = date('Y-m-d H:i:s');

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
    public function accept_load_post_by_user($request)
    {
        $login_user_id     = validate_user_login_id();
        if ($login_user_id == 0) 
        {
            $arr_response['status'] = 'error';
            $arr_response['msg']    = 'Invalid driver token';
            $arr_response['data']    = [];
            return $arr_response;
        }

        $load_post_request_id = base64_decode($request->input('load_post_request_id'));
        if ($load_post_request_id == '') 
        {
            $arr_response['status'] = 'error';
            $arr_response['msg']    = 'Shipment identifier is missing unable to process request.';
            $arr_response['data']   = [];
            return $arr_response;
        }
        
        $po_no         = $request->input('po_no');
        $receiver_name = $request->input('receiver_name');
        $receiver_no   = $request->input('receiver_no');
        $app_suite     = $request->input('app_suite');
        
        $request_status       = 'ACCEPT_BY_USER';

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

                            $arr_booking_data = 
                                                    [
                                                        'po_no'         => $po_no,
                                                        'receiver_name' => $receiver_name,
                                                        'receiver_no'   => $receiver_no,
                                                        'app_suite'     => $app_suite
                                                    ];

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

                        $this->NotificationsService->send_on_signal_notification($arr_notification_data);

                        /*Send on signal notification to user that driver accepted your request*/
                        $arr_notification_data = 
                                                [
                                                    'title'             => 'Customer cancel shipment post request.',
                                                    'record_id'         => $load_post_request_id,
                                                    'enc_user_id'       => $driver_id,
                                                    'notification_type' => 'REJECT_BY_USER',
                                                    'user_type'         => 'WEB',
                                                ];

                        $this->NotificationsService->send_on_signal_notification($arr_notification_data);

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

                    $this->NotificationsService->send_on_signal_notification($arr_notification_data);

                    /*Send on signal notification to user that driver accepted your request*/
                    $arr_notification_data = 
                                            [
                                                'title'             => 'Customer cancel shipment post request.',
                                                'record_id'         => $load_post_request_id,
                                                'enc_user_id'       => $driver_id,
                                                'notification_type' => 'REJECT_BY_USER',
                                                'user_type'         => 'WEB',
                                            ];

                    $this->NotificationsService->send_on_signal_notification($arr_notification_data);

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
    public function reject_load_post($request)
    {
        $login_user_id     = validate_user_login_id();
        if ($login_user_id == 0) 
        {
            $arr_response['status'] = 'error';
            $arr_response['msg']    = 'Invalid driver token';
            $arr_response['data']    = [];
            return $arr_response;
        }
        
        $load_post_request_id = base64_decode($request->input('load_post_request_id'));
        
        $request_status = 'REJECT_BY_USER';

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

                    $this->NotificationsService->send_on_signal_notification($arr_notification_data);

                    /*Send on signal notification to user that driver accepted your request*/
                    $arr_notification_data = 
                                            [
                                                'title'             => 'Customer rejected shipment post request.',
                                                'record_id'         => $load_post_request_id,
                                                'enc_user_id'       => $enc_user_id,
                                                'notification_type' => 'REJECT_BY_USER',
                                                'user_type'         => 'WEB',
                                            ];

                    $this->NotificationsService->send_on_signal_notification($arr_notification_data);

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
    public function cancel_pending_load_post($request)
    {
        $login_user_id     = validate_user_login_id();
        if ($login_user_id == 0) 
        {
            $arr_response['status'] = 'error';
            $arr_response['msg']    = 'Invalid driver token';
            $arr_response['data']    = [];
            return $arr_response;
        }
        
        $load_post_request_id = base64_decode($request->input('load_post_request_id'));

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
    | user will see all ongoing trips
    |
    */
    public function ongoing_trips()
    {
        $user_id = validate_user_login_id();
        if ($user_id == 0) 
        {
            $arr_response['status'] = 'error';
            $arr_response['msg']    = 'Invalid driver token';
            $arr_response['data']    = [];
            return $arr_response;
        }
        
        $arr_ongoing_trips = $this->get_filter_trips($user_id,'ONGOING');

        if(isset($arr_ongoing_trips['data']) && sizeof($arr_ongoing_trips['data'])>0){
            foreach ($arr_ongoing_trips['data'] as $key => $value) 
            {
                $arr_ongoing_trips['data'][$key]['booking_status'] = isset($value['booking_status']) ? $value['booking_status'] : '';
                $arr_ongoing_trips['data'][$key]['booking_date']   = isset($value['booking_date']) ? date('d M Y',strtotime($value['booking_date'])) : '';
                $arr_ongoing_trips['data'][$key]['first_name']     = isset($value['load_post_request_details']['driver_details']['first_name']) ? $value['load_post_request_details']['driver_details']['first_name'] :'';
                $arr_ongoing_trips['data'][$key]['last_name']      = isset($value['load_post_request_details']['driver_details']['last_name']) ? $value['load_post_request_details']['driver_details']['last_name'] :'';
                $profile_image = url('/uploads/default-profile.png');
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
    public function pending_trips()
    {
        $user_id     = validate_user_login_id();
        if ($user_id == 0) 
        {
            $arr_response['status'] = 'error';
            $arr_response['msg']    = 'Invalid driver token';
            $arr_response['data']    = [];
            return $arr_response;
        }
        
        $arr_pending_trips = $this->get_filter_trips($user_id,'PENDING');

        if(isset($arr_pending_trips['data']) && sizeof($arr_pending_trips['data'])>0)
        {
            foreach ($arr_pending_trips['data'] as $key => $value) 
            {
                $booking_status = isset($value['request_status']) ? $value['request_status'] : '';
                $is_future_request = isset($value['is_future_request']) ? $value['is_future_request'] : '0';
                $is_request_process = isset($value['is_request_process']) ? $value['is_request_process'] : '0';

                $first_name = '';
                if($booking_status == 'NEW_REQUEST'){
                    $first_name = 'Please Select Driver';
                }
                else if($booking_status == 'USER_REQUEST' && $is_future_request == '0'){
                    $first_name = 'Waiting for Driver to Accept';
                }
                else if($booking_status == 'USER_REQUEST' && $is_future_request == '1'){
                    if($is_request_process == '0'){
                        $first_name = 'Future Booking Request';
                    }
                    else if($is_request_process == '1'){
                        $first_name = 'Waiting for Driver to Accept';
                    }
                }
                else if($booking_status == 'ACCEPT_BY_DRIVER'){
                    $first_name = 'Please respond to driver';
                }
                else if($booking_status == 'REJECT_BY_DRIVER'){
                    $first_name = 'Waiting for Driver to Accept';
                }
                else if($booking_status == 'REJECT_BY_USER'){
                    $first_name = 'Waiting for Driver to Accept';
                }
                else if($booking_status == 'TIMEOUT'){
                    $first_name = 'Waiting for Driver to Accept';
                }

                $profile_image = url('/uploads/listing-default-logo.png');
                $arr_pending_trips['data'][$key]['id']                   = isset($value['id']) ? $value['id'] :0;
                $arr_pending_trips['data'][$key]['load_post_request_id'] = isset($value['id']) ? $value['id'] :'';
                $arr_pending_trips['data'][$key]['booking_unique_id']    = '';
                $arr_pending_trips['data'][$key]['booking_status']       = $booking_status;
                $arr_pending_trips['data'][$key]['booking_date']         = isset($value['date']) ? date('d M Y',strtotime($value['date'])) : '';
                $arr_pending_trips['data'][$key]['first_name']           = $first_name;
                $arr_pending_trips['data'][$key]['last_name']            = '';
                $arr_pending_trips['data'][$key]['profile_image']        = $profile_image;
                $arr_pending_trips['data'][$key]['mobile_no']            = '';
                $arr_pending_trips['data'][$key]['pickup_location']      = isset($value['pickup_location']) ? $value['pickup_location'] :'';
                $arr_pending_trips['data'][$key]['drop_location']        = isset($value['drop_location']) ? $value['drop_location'] :'';
                $arr_pending_trips['data'][$key]['pickup_lat']           = isset($value['pickup_lat']) ? doubleval($value['pickup_lat']) :doubleval(0.0);
                $arr_pending_trips['data'][$key]['pickup_lng']           = isset($value['pickup_lng']) ? doubleval($value['pickup_lng']) :doubleval(0.0);
                $arr_pending_trips['data'][$key]['drop_lat']             = isset($value['drop_lat']) ? doubleval($value['drop_lat']) :doubleval(0.0);
                $arr_pending_trips['data'][$key]['drop_lng']             = isset($value['drop_lng']) ? doubleval($value['drop_lng']) :doubleval(0.0);
                $arr_pending_trips['data'][$key]['driver_lat']           = doubleval(0.0);
                $arr_pending_trips['data'][$key]['driver_lng']           = doubleval(0.0);

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
    
    /*
    |
    | user will see all completed trips
    |
    */
    public function completed_trips()
    {
        $user_id     = validate_user_login_id();
        if ($user_id == 0) 
        {
            $arr_response['status'] = 'error';
            $arr_response['msg']    = 'Invalid driver token';
            $arr_response['data']    = [];
            return $arr_response;
        }
        
        /*$user_type = $request->input('user_type','');
        if($user_type == ''){*/
            $user_type = 'USER';
        //}
        
        $arr_completed_trips = $this->get_filter_trips($user_id,'COMPLETED');

        if(isset($arr_completed_trips['data']) && sizeof($arr_completed_trips['data'])>0){
            foreach ($arr_completed_trips['data'] as $key => $value) 
            {
                $arr_completed_trips['data'][$key]['booking_status'] = isset($value['booking_status']) ? $value['booking_status'] : '';
                $arr_completed_trips['data'][$key]['booking_date']   = isset($value['booking_date']) ? date('d M Y',strtotime($value['booking_date'])) : '';
                
                if($user_type == 'USER')
                {
                    $arr_completed_trips['data'][$key]['first_name']     = isset($value['load_post_request_details']['driver_details']['first_name']) ? $value['load_post_request_details']['driver_details']['first_name'] :'';
                    $arr_completed_trips['data'][$key]['last_name']      = isset($value['load_post_request_details']['driver_details']['last_name']) ? $value['load_post_request_details']['driver_details']['last_name'] :'';
                    $profile_image = url('/uploads/default-profile.png');
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
                    $profile_image = url('/uploads/default-profile.png');
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
    public function canceled_trips()
    {
        $user_id     = validate_user_login_id();
        if ($user_id == 0) 
        {
            $arr_response['status'] = 'error';
            $arr_response['msg']    = 'Invalid driver token';
            $arr_response['data']    = [];
            return $arr_response;
        }

        
        $user_type = 'USER';
        
        $arr_canceled_trips = $this->get_filter_trips($user_id,'CANCELED');

        if(isset($arr_canceled_trips['data']) && sizeof($arr_canceled_trips['data'])>0){
            foreach ($arr_canceled_trips['data'] as $key => $value) 
            {
                $record_type = isset($value['type']) ? $value['type'] : '';
                $arr_canceled_trips['data'][$key]['type']           = isset($value['type']) ? $value['type'] : '';
                $arr_canceled_trips['data'][$key]['booking_status'] = isset($value['booking_status']) ? $value['booking_status'] : '';
                $arr_canceled_trips['data'][$key]['booking_date']   = isset($value['booking_date']) ? date('d M Y',strtotime($value['booking_date'])) : '';
               
                if($record_type == 'normal_booking')
                {
                    $arr_canceled_trips['data'][$key]['first_name']     = isset($value['load_post_request_details']['driver_details']['first_name']) ? $value['load_post_request_details']['driver_details']['first_name'] :'';
                    $arr_canceled_trips['data'][$key]['last_name']      = isset($value['load_post_request_details']['driver_details']['last_name']) ? $value['load_post_request_details']['driver_details']['last_name'] :'';
                    
                    $profile_image = url('/uploads/default-profile.png');
                    if(isset($value['load_post_request_details']['driver_details']['profile_image']) && $value['load_post_request_details']['driver_details']['profile_image']!=''){
                        if(file_exists($this->user_profile_base_img_path.$value['load_post_request_details']['driver_details']['profile_image'])){
                            $profile_image = $this->user_profile_public_img_path.$value['load_post_request_details']['driver_details']['profile_image'];
                        }
                    }
                    
                    if(isset($value['booking_status']) && $value['booking_status'] == 'CANCEL_BY_ADMIN')
                    {
                        $arr_canceled_trips['data'][$key]['first_name']     = 'Canceled by '.config('app.project.name').' Admin';
                        $arr_canceled_trips['data'][$key]['last_name']      = '';
                        $profile_image = url('/uploads/listing-default-logo.png');
                        
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
                    $profile_image = url('/uploads/default-profile.png');
                    $arr_canceled_trips['data'][$key]['first_name']     = 'Canceled by you';
                    $arr_canceled_trips['data'][$key]['last_name']      = '';

                    if(isset($value['booking_status']) && $value['booking_status'] == 'CANCEL_BY_ADMIN')
                    {
                        $arr_canceled_trips['data'][$key]['first_name']     = 'Canceled by '.config('app.project.name').' Admin';
                        $arr_canceled_trips['data'][$key]['last_name']      = '';                        
                        $profile_image = url('/uploads/listing-default-logo.png');
                    }

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
        $user_id     = validate_user_login_id();
        if ($user_id == 0) 
        {
            $arr_response['status'] = 'error';
            $arr_response['msg']    = 'Invalid driver token';
            $arr_response['data']    = [];
            return $arr_response;
        }
        
        $user_type  = 'USER';
        $booking_id = base64_decode($request->input('booking_id'));
         
        if ($booking_id == "") 
        {
            $arr_response['status'] = 'error';
            $arr_response['msg']    = 'Invalid booking ID';
            $arr_response['data']    = [];
            return $arr_response;
        }
        
        $arr_trip_details  = [];
        $obj_trip_details  = $this->BookingMasterModel
                                                // ->select('id','load_post_request_id','booking_unique_id','booking_date','booking_status','total_charge')
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
          // dd($arr_trip_details);
        }
        
        if(isset($arr_trip_details) && sizeof($arr_trip_details)>0)
        {
            $driver_fare_charge = 0;

            $arr_result                      = [];
            $arr_result['first_name']        = '';
            $arr_result['last_name']         = '';
            $arr_result['email']             = '';
            $arr_result['mobile_no']         = '';
            $arr_result['profile_image']     = '';
            $arr_result['booking_id']        = isset($arr_trip_details['id']) ? $arr_trip_details['id'] : 0 ;
            $arr_result['booking_unique_id'] = isset($arr_trip_details['booking_unique_id']) ? $arr_trip_details['booking_unique_id'] : '';
            $arr_result['booking_status']    = isset($arr_trip_details['booking_status']) ? $arr_trip_details['booking_status'] : '';
            $arr_result['booking_date']      = isset($arr_trip_details['booking_date']) ? $arr_trip_details['booking_date'] : '';
            $arr_result['total_charge']      = isset($arr_trip_details['total_charge']) ? $arr_trip_details['total_charge'] : 0;
            $arr_result['user_id']           = isset($arr_trip_details['load_post_request_details']['user_id']) ? strval($arr_trip_details['load_post_request_details']['user_id']) : '0';
            $arr_result['driver_id']         = isset($arr_trip_details['load_post_request_details']['driver_id']) ? strval($arr_trip_details['load_post_request_details']['driver_id']) : '0';
            $arr_result['pickup_location']   = isset($arr_trip_details['load_post_request_details']['pickup_location']) ? $arr_trip_details['load_post_request_details']['pickup_location'] :'';
            $arr_result['drop_location']     = isset($arr_trip_details['load_post_request_details']['drop_location']) ? $arr_trip_details['load_post_request_details']['drop_location'] :'';
            $arr_result['pickup_lat']        = isset($arr_trip_details['load_post_request_details']['pickup_lat']) ? doubleval($arr_trip_details['load_post_request_details']['pickup_lat']) :doubleval(0.0);
            $arr_result['pickup_lng']        = isset($arr_trip_details['load_post_request_details']['pickup_lng']) ? doubleval($arr_trip_details['load_post_request_details']['pickup_lng']) :doubleval(0.0);
            $arr_result['drop_lat']          = isset($arr_trip_details['load_post_request_details']['drop_lat']) ? doubleval($arr_trip_details['load_post_request_details']['drop_lat']) :doubleval(0.0);
            $arr_result['drop_lng']          = isset($arr_trip_details['load_post_request_details']['drop_lng']) ? doubleval($arr_trip_details['load_post_request_details']['drop_lng']) :doubleval(0.0);
           
            $arr_result['package_type']     = isset($arr_trip_details['load_post_request_details']['load_post_request_package_details']['package_type']) ? $arr_trip_details['load_post_request_details']['load_post_request_package_details']['package_type'] : '';
            $arr_result['package_length']   = isset($arr_trip_details['load_post_request_details']['load_post_request_package_details']['package_length']) ? doubleval($arr_trip_details['load_post_request_details']['load_post_request_package_details']['package_length']) : doubleval(0.0);
            $arr_result['package_breadth']  = isset($arr_trip_details['load_post_request_details']['load_post_request_package_details']['package_breadth']) ? doubleval($arr_trip_details['load_post_request_details']['load_post_request_package_details']['package_breadth']) : doubleval(0.0);
            $arr_result['package_height']   = isset($arr_trip_details['load_post_request_details']['load_post_request_package_details']['package_height']) ? doubleval($arr_trip_details['load_post_request_details']['load_post_request_package_details']['package_height']) : doubleval(0.0);
            $arr_result['package_weight']   = isset($arr_trip_details['load_post_request_details']['load_post_request_package_details']['package_weight']) ? doubleval($arr_trip_details['load_post_request_details']['load_post_request_package_details']['package_weight']) : doubleval(0.0);
            $arr_result['package_quantity'] = isset($arr_trip_details['load_post_request_details']['load_post_request_package_details']['package_quantity']) ? intval($arr_trip_details['load_post_request_details']['load_post_request_package_details']['package_quantity']) : 0;

            /*if($user_type == 'DRIVER')
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

                $profile_image = url('/uploads/default-profile.png');
                if(isset($arr_trip_details['load_post_request_details']['user_details']['profile_image']) && $arr_trip_details['load_post_request_details']['user_details']['profile_image']!=''){
                    if(file_exists($this->user_profile_base_img_path.$arr_trip_details['load_post_request_details']['user_details']['profile_image'])){
                        $profile_image = $this->user_profile_public_img_path.$arr_trip_details['load_post_request_details']['user_details']['profile_image'];
                    }
                }
                
                $arr_result['profile_image'] = $profile_image;

            }*/
            $profile_image = '';
            $download_receipt = '';
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

                $profile_image = url('/uploads/default-profile.png');
                if(isset($arr_trip_details['load_post_request_details']['driver_details']['profile_image']) && $arr_trip_details['load_post_request_details']['driver_details']['profile_image']!=''){
                    if(file_exists($this->user_profile_base_img_path.$arr_trip_details['load_post_request_details']['driver_details']['profile_image'])){
                        $profile_image = $this->user_profile_public_img_path.$arr_trip_details['load_post_request_details']['driver_details']['profile_image'];
                    }
                }

                if(isset($arr_trip_details['booking_status']) && $arr_trip_details['booking_status'] == 'CANCEL_BY_ADMIN')
                {
                    $arr_result['first_name']     = 'Canceled by '.config('app.project.name').' Admin';
                    $arr_result['last_name']      = '';
                    $profile_image = url('/uploads/listing-default-logo.png');
                    
                }

                if($arr_trip_details['booking_status'] == 'COMPLETED'){
                    $receiptName = "TRIP_INVOICE_".$booking_id.".pdf";
                    if(file_exists($this->invoice_base_img_path.$receiptName)){
                        $download_receipt = $receiptName;
                    }
                } 
                $arr_result['download_receipt'] = $download_receipt;
                $arr_result['profile_image'] = $profile_image;
                
                $arr_data = filter_completed_trip_details($arr_trip_details);
                
                $arr_result['po_no']              = isset($arr_data['po_no']) ? $arr_data['po_no'] : '';
                $arr_result['receiver_name']      = isset($arr_data['receiver_name']) ? $arr_data['receiver_name'] : '';
                $arr_result['receiver_no']        = isset($arr_data['receiver_no']) ? $arr_data['receiver_no'] : '';
                $arr_result['app_suite']          = isset($arr_data['app_suite']) ? $arr_data['app_suite'] : '';
                $arr_result['distance']           = isset($arr_data['distance']) ? number_format($arr_data['distance'],2) : '0,0';
                $arr_result['total_minutes_trip'] = isset($arr_data['total_minutes_trip']) ? $arr_data['total_minutes_trip'] : ''; 
                $arr_result['total_amount']      = isset($arr_data['total_amount']) ? number_format($arr_data['total_amount'],2) : '0,0';
                $arr_result['discount_amount']   = isset($arr_data['applied_promo_code_charge']) ? number_format($arr_data['applied_promo_code_charge'],2) : '0,0';
                $arr_result['final_amount']      = isset($arr_data['total_charge']) ? number_format($arr_data['total_charge'],2) : '0,0';
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

    public function track_live_trip($request)
    {
        $user_id     = validate_user_login_id();

        if ($user_id == 0) 
        {
            $arr_response['status'] = 'error';
            $arr_response['msg']    = 'Invalid driver token';
            $arr_response['data']    = [];
            return $arr_response;
        }
        
        $booking_id = base64_decode($request->input('booking_id'));
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
                                                        $query->select('id','driver_id','vehicle_id','pickup_lat','pickup_lng','drop_lat','drop_lng');
                                                        $query->where('request_status','ACCEPT_BY_USER');
                                                        $query->where('user_id',$user_id);
                                                        $query->with(['driver_current_location_details'=>function($query){
                                                                    $query->select('id','driver_id','status','current_latitude','current_longitude');
                                                                },
                                                                'vehicle_details'=>function($query){
                                                                    $query->select('id','vehicle_type_id');
                                                                    $query->with(['vehicle_type_details'=>function($query){
                                                                        $query->select('id','vehicle_type','vehicle_type_slug');
                                                                    }]);
                                                                }]);
                                                }])
                                                ->where('id',$booking_id)
                                                ->first();

        if($obj_ongoing_trips)
        {
            $arr_ongoing_trips = $obj_ongoing_trips->toArray();
        }
        
        if(isset($arr_ongoing_trips) && sizeof($arr_ongoing_trips)>0){
            
            $arr_ongoing_trips['booking_id']        = isset($arr_ongoing_trips['id']) ? $arr_ongoing_trips['id'] : 0 ;
            $arr_ongoing_trips['driver_id']         = isset($arr_ongoing_trips['load_post_request_details']['driver_id']) ? doubleval($arr_ongoing_trips['load_post_request_details']['driver_id']) :0;
            $arr_ongoing_trips['driver_lat']        = isset($arr_ongoing_trips['load_post_request_details']['driver_current_location_details']['current_latitude']) ? doubleval($arr_ongoing_trips['load_post_request_details']['driver_current_location_details']['current_latitude']) :doubleval(0.0);
            $arr_ongoing_trips['driver_lng']        = isset($arr_ongoing_trips['load_post_request_details']['driver_current_location_details']['current_longitude']) ? doubleval($arr_ongoing_trips['load_post_request_details']['driver_current_location_details']['current_longitude']) :doubleval(0.0);
            $arr_ongoing_trips['vehicle_type_slug'] = isset($arr_ongoing_trips['load_post_request_details']['vehicle_details']['vehicle_type_details']['vehicle_type_slug']) ? $arr_ongoing_trips['load_post_request_details']['vehicle_details']['vehicle_type_details']['vehicle_type_slug'] :'';

            $arr_ongoing_trips['driver_distance'] = '';
            $arr_ongoing_trips['driver_duration'] = '';
            
            $driver_latitude  = isset($arr_ongoing_trips['load_post_request_details']['driver_current_location_details']['current_latitude']) ? $arr_ongoing_trips['load_post_request_details']['driver_current_location_details']['current_latitude'] : '';
            $driver_longitude = isset($arr_ongoing_trips['load_post_request_details']['driver_current_location_details']['current_longitude']) ? $arr_ongoing_trips['load_post_request_details']['driver_current_location_details']['current_longitude'] : '';
            $pickup_lat       = isset($arr_ongoing_trips['load_post_request_details']['pickup_lat']) ? doubleval($arr_ongoing_trips['load_post_request_details']['pickup_lat']) : floatval(0);
            $pickup_lng       = isset($arr_ongoing_trips['load_post_request_details']['pickup_lng']) ? doubleval($arr_ongoing_trips['load_post_request_details']['pickup_lng']) : floatval(0);
            $drop_lat         = isset($arr_ongoing_trips['load_post_request_details']['drop_lat']) ? doubleval($arr_ongoing_trips['load_post_request_details']['drop_lat']) : floatval(0);
            $drop_lng         = isset($arr_ongoing_trips['load_post_request_details']['drop_lng']) ? doubleval($arr_ongoing_trips['load_post_request_details']['drop_lng']) : floatval(0);
            $booking_status   = isset($arr_ongoing_trips['booking_status']) ? $arr_ongoing_trips['booking_status'] : '';

            if($booking_status == 'TO_BE_PICKED')
            {
                $driver_origins      = $driver_latitude.",".$driver_longitude;
                $driver_destinations = $pickup_lat.",".$pickup_lng;
                $arr_driver_distance = $this->calculate_distance($driver_origins,$driver_destinations);
                $arr_ongoing_trips['driver_distance'] = isset($arr_driver_distance['distance']) ? $arr_driver_distance['distance'] :'';
                $arr_ongoing_trips['driver_duration'] = isset($arr_driver_distance['duration']) ? $arr_driver_distance['duration'] :'';
            }
            else if($booking_status == 'IN_TRANSIT')
            {
                $driver_origins      = $driver_latitude.",".$driver_longitude;
                $driver_destinations = $drop_lat.",".$drop_lng;
                $arr_driver_distance = $this->calculate_distance($driver_origins,$driver_destinations);
                $arr_ongoing_trips['driver_distance'] = isset($arr_driver_distance['distance']) ? $arr_driver_distance['distance'] :'';
                $arr_ongoing_trips['driver_duration'] = isset($arr_driver_distance['duration']) ? $arr_driver_distance['duration'] :'';
            }
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

    public function process_cancel_trip_status_by_user($request)
    {
        $login_user_id     = validate_user_login_id();
        if ($login_user_id == 0) 
        {
            $arr_response['status'] = 'error';
            $arr_response['msg']    = 'Invalid token';
            $arr_response['data']    = [];
            return $arr_response;
        }
        
        $booking_status = 'CANCEL_BY_USER';
        $booking_id        = base64_decode($request->input('booking_id'));
        $reason            = $request->input('reason');
        

        if ($booking_id == '' || $booking_status == '') 
        {
            $arr_response['status'] = 'error';
            $arr_response['msg']    = 'Invalid required details.';
            $arr_response['data']   = [];
            return $arr_response;
        }

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

                    $this->NotificationsService->send_on_signal_notification($arr_notification_data);

                    /*Send on signal notification from user to specific driver*/
                    $arr_notification_data = 
                                            [
                                                'title'             => 'Customer cancel booking request.',
                                                'record_id'         => $booking_id,
                                                'enc_user_id'       => $enc_user_id,
                                                'notification_type' => 'TRIP_CANCEL_BY_USER',
                                                'user_type'         => 'WEB',
                                            ];

                    $this->NotificationsService->send_on_signal_notification($arr_notification_data);

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

                    $this->NotificationsService->send_on_signal_notification($arr_notification_data);

                    /*Send on signal notification to user that driver accepted your request*/
                    $arr_notification_data = 
                                            [
                                                'title'             => 'Customer approved your request.',
                                                'record_id'         => $booking_master_id,
                                                'enc_user_id'       => $driver_id,
                                                'notification_type' => 'ACCEPT_BY_USER',
                                                'user_type'         => 'WEB',
                                            ];

                    $this->NotificationsService->send_on_signal_notification($arr_notification_data);

                    $arr_tmp_data = 
                                    [
                                        'booking_master_id' => base64_encode($booking_master_id),
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
    | common function to filter trip listing details accourding to status
    |
    */
    private function get_filter_trips($enc_user_id,$trip_type)
    {
        $user_type = 'USER';
        
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
                                        ->select("id","date","pickup_location","drop_location","pickup_lat","pickup_lng","drop_lat","drop_lng","request_status","is_future_request","is_request_process")
                                        ->whereIn('request_status',['USER_REQUEST','TIMEOUT','ACCEPT_BY_DRIVER','REJECT_BY_DRIVER','REJECT_BY_USER'])
                                        ->where('user_id',$enc_user_id)
                                        ->orderBy('id','DESC')
                                        ->paginate($this->per_page);

            if($obj_trips)
            {
                $arr_trips = $obj_trips->toArray();
                $arr_pagination = $obj_trips->appends(['trip_type'=>$trip_type])->links();
                $arr_trips = $obj_trips->toArray();
                $arr_trips['arr_pagination'] = $arr_pagination;
                
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
                                            ->select("id","user_id","driver_id","date as booking_date","pickup_location","drop_location","pickup_lat","pickup_lng","drop_lat","drop_lng","load_post_image","request_status as booking_status","updated_at");

            if($user_type == 'USER')
            {
                $obj_cancel_load_post = $obj_cancel_load_post->where('user_id',$enc_user_id);
                $obj_cancel_load_post = $obj_cancel_load_post->whereIn('request_status',['CANCEL_BY_USER','CANCEL_BY_ADMIN']);
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
                                    ->select('id','load_post_request_id','booking_unique_id','booking_date','booking_status','total_charge','updated_at')
                                    ->whereHas('load_post_request_details',function($query) use($enc_user_id,$user_type) {
                                            // $query->whereHas('driver_details',function($query){
                                            // });
                                            // $query->whereHas('user_details',function($query){
                                            // });
                                            // $query->whereHas('driver_current_location_details',function($query){
                                            // });
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
            
            if(count($arr_all_cancel_trips)>0){

                usort($arr_all_cancel_trips, function($a, $b){
                      $t1 = strtotime($a['updated_at']);
                      $t2 = strtotime($b['updated_at']);
                      return $t1 < $t2;
                });
            }
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

        $arr_pagination = [];


        if($obj_trips)
        {
            $arr_trips = $obj_trips->toArray();
            $arr_pagination = $obj_trips->appends(['trip_type'=>$trip_type])->links();
            $arr_trips['arr_pagination'] = $arr_pagination;
        }
        return $arr_trips;
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
            $title = 'Payment for Trip #'.$booking_unique_id.' is pending, kindly contact with the user.';
        }

        if($payment_status == 'FAILED')
        {
            $notification_type = 'Trip Booking Payment';
            $title = 'Payment for Trip #'.$booking_unique_id.' failed, kindly contact with the user.';
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
    
    public function make_pagination_links($items,$perPage)
    {
        $pageStart = \Request::get('page', 1);
        // Start displaying items from this number;
        $offSet = ($pageStart * $perPage) - $perPage; 

        // Get only the items you need using array_slice
        $itemsForCurrentPage = array_slice($items, $offSet, $perPage, true);

        return new LengthAwarePaginator($itemsForCurrentPage, count($items), $perPage,Paginator::resolveCurrentPage(), array('path' => Paginator::resolveCurrentPath()));
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

    /*
    |
    | When user post new load then finding nearby drivers which matching given critera
    |
    */ 
    private function search_nearby_drivers($arr_data = [])
    {   
        if(isset($arr_data) && sizeof($arr_data)>0){

            $pickup_lat           = isset($arr_data['pickup_lat']) ? $arr_data['pickup_lat'] :0;
            $pickup_lng           = isset($arr_data['pickup_lng']) ? $arr_data['pickup_lng'] :0;
            $drop_lat             = isset($arr_data['drop_lat']) ? $arr_data['drop_lat'] :0;
            $drop_lng             = isset($arr_data['drop_lng']) ? $arr_data['drop_lng'] :0;
            $package_type         = isset($arr_data['package_type']) ? $arr_data['package_type'] :'';
            $package_quantity     = isset($arr_data['package_quantity']) ? $arr_data['package_quantity'] :0;
            $package_volume       = isset($arr_data['package_volume']) ? $arr_data['package_volume'] :0;
            $package_weight       = isset($arr_data['package_weight']) ? $arr_data['package_weight'] :0;
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
                    $arr_tmp_driver_details = [];
                    if(isset($arr_driver_details) && sizeof($arr_driver_details)>0)
                    {
                        /*
                        |manually fliter data also remove driver which does not verify their indivital vechiles 
                        |also remove drivers which are in restricted area.
                        */
                        foreach ($arr_driver_details as $key => $driver_details) 
                        {
                            
                            $arr_driver_details[$key] = $driver_details;

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
                            unset($arr_driver_details[$key]['distance']);
                        
                        }
                    }
                    $arr_driver_details = array_values($arr_driver_details);
                    $arr_vehicle_type[$vt_key]['arr_driver_details'] = $arr_driver_details;
                    $arr_vehicle_type[$vt_key]['driver_count']       = count($arr_driver_details);
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

                    $this->NotificationsService->send_on_signal_notification($arr_notification_data);

                    $arr_web_notification_data = 
                                                    [
                                                        'title'             => 'Trip requested by customer',
                                                        'notification_type' => 'USER_REQUEST',
                                                        'record_id'         => $load_post_request_id,
                                                        'enc_user_id'       => $enc_user_id,
                                                        'user_type'         => 'WEB',

                                                    ];

                    $this->NotificationsService->send_on_signal_notification($arr_web_notification_data);

                }
            }
        }   
        return true;
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
    
    private function genrate_booking_unique_number()
    {
        $secure = TRUE;    
        $bytes = openssl_random_pseudo_bytes(6, $secure);
        $order_ref_num = "QPB-".bin2hex($bytes);

        return strtoupper($order_ref_num);   
    }

    private function genrate_load_post_request_unique_number()
    {
        $secure = TRUE;    
        $bytes = openssl_random_pseudo_bytes(6, $secure);
        $order_ref_num = "LPR-".bin2hex($bytes);

        return strtoupper($order_ref_num);
    }

    private function get_booking_details($booking_id)
    {
        $arr_booking_master = [];
        $obj_booking_master = $this->BookingMasterModel
                                            ->with(['load_post_request_details'=> function($query){
                                                    $query->select('id','user_id','driver_id','pickup_location','drop_location','pickup_lat','pickup_lng','drop_lat','drop_lng','request_status','load_post_image');
                                                    $query->with(['driver_details'=>function($query){
                                                                $query->select('id','first_name','last_name','email','mobile_no','profile_image','stripe_account_id','company_id','is_company_driver');
                                                                $query->with('company_details');
                                                            }]);
                                                    $query->with(['user_details'=>function($query){
                                                                $query->select('id','first_name','last_name','email','mobile_no','profile_image');
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
}
?>