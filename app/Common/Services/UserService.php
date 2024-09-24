<?php

namespace App\Common\Services;

use App\Models\UserModel;
use App\Models\BookingMasterModel;
use App\Models\BookingPackageModel;

use App\Models\VehicleModel;
use App\Models\VehicleTypeModel;
use App\Models\DriverCarRelationModel;
use App\Models\DriverStatusModel;
use App\Models\RoleModel;
use App\Models\UserRoleModel;
use App\Models\DriverFairChargeModel;

use App\Common\Services\EmailService;
use App\Common\Services\CommonDataService;
use App\Common\Services\NotificationsService;

use Validator;
use Sentinel;
use DB;

class UserService
{
    public function __construct(
                                    UserModel $user,
                                    BookingMasterModel $booking_master,
                                    BookingPackageModel $booking_package,
                                    VehicleTypeModel $vehicle_type_model,
                                    DriverCarRelationModel $driver_car,
                                    VehicleModel $vehicle_model,
                                    DriverStatusModel $driver_status,
                                    RoleModel $role,
                                    UserRoleModel $user_roles,
                                    DriverFairChargeModel $driver_fair_charge,
                                    CommonDataService $common_data_service,
                                    EmailService $email_service,
                                    NotificationsService $notification
                                )
    {
        $this->UserModel                 = $user;
        $this->BookingMasterModel        = $booking_master;
        $this->BookingPackageModel       = $booking_package;
        $this->VehicleTypeModel          = $vehicle_type_model;
        $this->VehicleModel              = $vehicle_model;
        $this->DriverStatusModel         = $driver_status;
        $this->DriverCarRelationModel    = $driver_car;
        $this->RoleModel                 = $role;
        $this->UserRoleModel             = $user_roles;
        $this->DriverFairChargeModel     = $driver_fair_charge;
        $this->CommonDataService         = $common_data_service;
        $this->NotificationsService      = $notification;
        $this->EmailService              = $email_service;

        $this->user_profile_public_img_path = url('/').config('app.project.img_path.user_profile_images');
        $this->user_profile_base_img_path  = base_path().config('app.project.img_path.user_profile_images');

    }

    public function show_trip_details($request)
    {
        $user_id     = validate_user_jwt_token();
        if ($user_id == 0) 
        {
            $arr_response['status'] = 'error';
            $arr_response['msg']    = 'Invalid user token';
            $arr_response['data']   = [];
            return $arr_response;        
        }
        
        $booking_id     = $request->input('booking_id');
        $request_status = $request->input('request_status');

        if($booking_id == '' || $request_status == '')
        {
            $arr_response['status'] = 'error';
            $arr_response['msg']    = 'Something went wrong,Please try again.';
            $arr_response['data']   = [];
            return $arr_response;        
        }
        if($request_status == 'ONGOING'){
            $arr_ongoing_booking_details = $this->get_ongoing_booking_details($user_id,$booking_id);
            if(isset($arr_ongoing_booking_details) && sizeof($arr_ongoing_booking_details)>0){
                $arr_response['status'] = 'success';
                $arr_response['msg']    = 'Ongoing trip details found successfully.';
                $arr_response['data']   = $arr_ongoing_booking_details;
                return $arr_response;        
            }
            $arr_response['status'] = 'error';
            $arr_response['msg']    = 'Something went wrong,Booking details not found Please try again.';
            $arr_response['data']   = [];
            return $arr_response;        

        }
        $arr_response['status'] = 'error';
        $arr_response['msg']    = 'Something went wrong,Booking details not found Please try again.';
        $arr_response['data']   = [];
        return $arr_response;        

        dd($request->all(),$user_id);
    }
    
    private function get_ongoing_booking_details($user_id,$booking_id)
    {
        $obj_booking_master = $this->BookingMasterModel
                                            ->whereHas('load_post_request_details',function($query) use ($user_id){
                                                $query->where('user_id',$user_id);
                                                $query->where('request_status','ACCEPT_BY_USER');
                                            })
                                            ->with(['load_post_request_details' => function($query) use ($user_id){
                                                    $query->where('user_id',$user_id);
                                                    $query->where('request_status','ACCEPT_BY_USER');
                                                    $query->with('driver_current_location_details');

                                            }])
                                            ->where('id',$booking_id)
                                            ->first();
        if($obj_booking_master!=null && $obj_booking_master != false){
            
            $arr_booking_master = $obj_booking_master->toArray();

            if(isset($arr_booking_master) && sizeof($arr_booking_master)>0){
                
                $arr_tmp_data                      = [];
                $arr_tmp_data['booking_id']        = isset($arr_booking_master['id'])?$arr_booking_master['id']:0;
                $arr_tmp_data['pickup_location']   = isset($arr_booking_master['load_post_request_details']['pickup_location'])?$arr_booking_master['load_post_request_details']['pickup_location']:'';
                $arr_tmp_data['drop_location']     = isset($arr_booking_master['load_post_request_details']['drop_location'])?$arr_booking_master['load_post_request_details']['drop_location']:'';
                $arr_tmp_data['pickup_lat']        = isset($arr_booking_master['load_post_request_details']['pickup_lat'])?$arr_booking_master['load_post_request_details']['pickup_lat']:'0';
                $arr_tmp_data['pickup_lng']        = isset($arr_booking_master['load_post_request_details']['pickup_lng'])?$arr_booking_master['load_post_request_details']['pickup_lng']:'0';
                $arr_tmp_data['drop_lat']          = isset($arr_booking_master['load_post_request_details']['drop_lat'])?$arr_booking_master['load_post_request_details']['drop_lat']:'0';
                $arr_tmp_data['drop_lng']          = isset($arr_booking_master['load_post_request_details']['drop_lng'])?$arr_booking_master['load_post_request_details']['drop_lng']:'0';
                $arr_tmp_data['trip_status']       = isset($arr_booking_master['booking_status'])?$arr_booking_master['booking_status']:'';
                $arr_tmp_data['driver_status']     = isset($arr_booking_master['load_post_request_details']['driver_current_location_details']['status'])?$arr_booking_master['load_post_request_details']['driver_current_location_details']['status']:'';
                $arr_tmp_data['current_latitude']  = isset($arr_booking_master['load_post_request_details']['driver_current_location_details']['current_latitude'])?$arr_booking_master['load_post_request_details']['driver_current_location_details']['current_latitude']:'';
                $arr_tmp_data['current_longitude'] = isset($arr_booking_master['load_post_request_details']['driver_current_location_details']['current_longitude'])?$arr_booking_master['load_post_request_details']['driver_current_location_details']['current_longitude']:'';

                return $arr_tmp_data;
            }   
            else
            {
                return [];       
            }
        }
        return [];
    }
    public function store_booking_details($request)
    {
        $arr_data         = [];
        if(!empty($request))
        {
            $arr_rules                      = [];
            $arr_rules['user_id']           = "required";
            $arr_rules['booking_type']      = "required";

            $arr_rules['pickup_location']   = "required";
            $arr_rules['pickup_lat']        = "required";
            $arr_rules['pickup_long']       = "required";
            $arr_rules['drop_location']     = "required";
            $arr_rules['drop_lat']          = "required";
            $arr_rules['drop_long']         = "required";
            $arr_rules['distance']          = "required";
            
            $arr_rules['is_promo_code_applied']     = "required";
            // $arr_rules['promo_code']                = "required";
            // $arr_rules['promo_percentage']          = "required";
            // $arr_rules['promo_max_amount']          = "required";
            // $arr_rules['applied_promo_code_charge'] = "required";

            // Package details
            $arr_rules['package_type']      = "required";
            $arr_rules['package_length']    = "required";
            $arr_rules['package_breadth']   = "required";
            $arr_rules['package_height']    = "required";
            // $arr_rules['package_volume']    = "required";
            $arr_rules['package_weight']    = "required";
            $arr_rules['package_quantity']  = "required";

            // Payment details
            $arr_rules['card_id']                   = "required";
            
            $validator = Validator::make($request->all(),$arr_rules);
                   
            if($validator->fails())
            {
                return
                    [
                        'status' => 'error',
                        'msg'    => 'Please fill all the required field.'
                    ];
            }
                    
            $user_id        = $request->input('user_id');
            $booking_type        = $request->input('booking_type');
            
            $pickup_location    = $request->input('pickup_location');
            $pickup_lat         = $request->input('pickup_lat');
            $pickup_long        = $request->input('pickup_long');
            $drop_location      = $request->input('drop_location');
            $drop_lat           = $request->input('drop_lat');
            $drop_long          = $request->input('drop_long');
            $distance           = $request->input('distance');

            $is_promo_code_applied      = $request->input('is_promo_code_applied');
            $promo_code                 = $request->input('promo_code');
            $promo_percentage           = $request->input('promo_percentage');
            $promo_max_amount           = $request->input('promo_max_amount');
            $applied_promo_code_charge  = $request->input('applied_promo_code_charge');

            // Package details
            $package_type      = $request->input('package_type');
            $package_length    = $request->input('package_length');
            $package_breadth   = $request->input('package_breadth');
            $package_height    = $request->input('package_height');
            $package_weight    = $request->input('package_weight');
            $package_quantity  = $request->input('package_quantity');

            $card_id           = $request->input('card_id');


            $arr_data['booking_unique_id']         = $this->genrate_event_ride_number();
            $arr_data['user_id']            = $user_id;
            $arr_data['booking_type']       = $booking_type;

            $arr_data['pickup_location']    = $pickup_location;
            $arr_data['pickup_lat']         = $pickup_lat;
            $arr_data['pickup_long']        = $pickup_long;
            $arr_data['drop_location']      = $drop_location;
            $arr_data['drop_lat']           = $drop_lat;
            $arr_data['drop_long']          = $drop_long;
            $arr_data['distance']           = $distance;

            $arr_data['is_promo_code_applied']      = $is_promo_code_applied;
            $arr_data['promo_code']                 = $promo_code;
            $arr_data['promo_percentage']           = $promo_percentage;
            $arr_data['promo_max_amount']           = $promo_max_amount;
            $arr_data['applied_promo_code_charge']  = $applied_promo_code_charge;

            // Payment Details
            $arr_data['card_id']                    = $card_id;

            if (isset($arr_data)) 
            {
                $ins_booking = $this->BookingMasterModel->create($arr_data);
                $booking_arr = $ins_booking->toArray();

                if(isset($booking_arr) && count($booking_arr) > 0 )
                {
                    $booking_id = $booking_arr['id'];

                    // Package details
                    $arr_package_data['package_type']      = $package_type;
                    $arr_package_data['package_length']    = $package_length;
                    $arr_package_data['package_breadth']   = $package_breadth;
                    $arr_package_data['package_height']    = $package_height;
                    
                    $package_volume = $package_length * $package_breadth * $package_height;
                    $arr_package_data['package_volume']    = $package_volume;
                    $arr_package_data['package_weight']    = $package_weight;
                    $arr_package_data['package_quantity']  = $package_quantity;

                    $arr_package_data['booking_id']  = $booking_id;

                    $ins_package = $this->BookingPackageModel->create($arr_package_data);

                    if($package_volume > 0 && $package_weight >0 )
                    {
                        $drivers_matched_qry = "SELECT
                                                    `D`.`id` AS `user_id`,
                                                    `D`.`latitude` AS `user_latitude`,
                                                    `D`.`longitude` AS `user_longitude`,
                                                    `D`.`email`,
                                                    `V`.`id` AS `vehicle_id`,
                                                    `V`.`vehicle_name`,
                                                    `VT`.`id` AS `vehicle_type_id`,
                                                    `VT`.`vehicle_type`
                                                FROM
                                                    `users` AS `D`
                                                JOIN `driver_car_relation` AS `DCR` ON `DCR`.`driver_id` = `D`.`id`
                                                JOIN `vehicle` AS `V`ON `V`.`id` = `DCR`.`vehicle_id`
                                                JOIN `vehicle_type` AS `VT` ON `VT`.`id` = `V`.`vehicle_type_id`
                                                WHERE
                                                    `VT`.`vehicle_min_volume` <= ".$package_volume." AND
                                                    `VT`.`vehicle_max_volume` >= ".$package_volume." AND
                                                    `VT`.`vehicle_min_weight` <= ".$package_weight." AND
                                                    `VT`.`vehicle_max_weight` >= ".$package_weight." AND
                                                    (
                                                    6371 * ACOS(
                                                        COS(RADIANS(".$pickup_lat.")) * COS(RADIANS(`latitude`)) * COS(
                                                            RADIANS(`longitude`) - RADIANS(".$pickup_long.")
                                                        ) + SIN(RADIANS(".$pickup_lat.")) * SIN(RADIANS(`latitude`))
                                                    )
                                                ) <= 20";
                        $obj_drivers_matched = DB::select($drivers_matched_qry);
                        // dd($obj_drivers_matched);
                        if ($obj_drivers_matched) 
                        {
                            // Send notification to these drivers here
                            $arr_response['status'] = 'success';
                            $arr_response['msg']    = 'All matching drivers fetched successfully.';
                            $arr_response['data']   = $obj_drivers_matched;
                            // dd($arr_response);
                        }
                        else
                        {
                            $arr_response['status'] = 'error';
                            $arr_response['msg']    = 'No driver present to handle this request.';
                            $arr_response['data']   = [];      
                        }
                        return $arr_response;
                    }

                    /*$arr_notification_data = $this->built_notification_data($arr_data,'RIDER_SEND_TO_DRIVER_REQUEST'); 
                    $this->NotificationsService->store_notification($arr_notification_data);*/

                    /*return 
                        [
                            'status' => 'success',
                            'msg'    => 'Booking details stored successfully'
                        ];*/
                }
                else
                {
                    return 
                        [
                            'status' => 'error',
                            'msg'    => 'Problem occurred while storing booking details.'
                        ];
                }
            }
        }
        return 
            [
                'status' => 'error',
                'msg'    => 'Please fill all the required field.'
            ];
    }

    private function built_mail_data($arr_data)
    {
        if(isset($arr_data) && sizeof($arr_data)>0)
        {
            $arr_built_content = [
                                  'EMAIL'    => $arr_data['email'],
                                  'LINK'     => url('/')];                                  

            $arr_mail_data                      = [];
            $arr_mail_data['email_template_id'] = '13';
            $arr_mail_data['arr_built_content'] = $arr_built_content;
            $arr_mail_data['user']              = $arr_data;

            return $arr_mail_data;
        }
        return FALSE;
    }

    public function rider_send_driver_request($request)
    {
        $arr_data         = [];
        $request_status   = $request->input('request_status');
        $driver_id        = $request->input('driver_id');
        
        if(!empty($request_status))
        {
            if ($request_status=='RIDER_REQUEST') 
            {    

                $arr_rules                     = [];
                $arr_rules['driver_id']        = "required";
                $arr_rules['rider_id']         = "required";
                $arr_rules['pick_up_location'] = "required";
                $arr_rules['drop_location']    = "required";
                $arr_rules['pick_up_lat']      = "required";
                $arr_rules['pick_up_long']     = "required";
                $arr_rules['drop_lat']         = "required";
                $arr_rules['drop_long']        = "required";
                $arr_rules['vehicle_id']       = "required";
                $arr_rules['request_status']   = "required";
               /* $arr_rules['latitude ']      = "required";
                $arr_rules['longitude']        = "required";*/

                $validator = Validator::make($request->all(),$arr_rules);
                       
                if($validator->fails())
                {
                    return 
                        [
                            'status' => 'error',
                            'msg'    => 'Please fill all the required field.'
                        ];
                }
                        
                $pick_up_location = $request->input('pick_up_location');
                $drop_location    = $request->input('drop_location');
                $driver_id        = $request->input('driver_id');
                $rider_id         = $request->input('rider_id');
                $vehicle_id       = $request->input('vehicle_id');
                $pick_up_lat      = $request->input('pick_up_lat');
                $pick_up_long     = $request->input('pick_up_long');
                $drop_lat         = $request->input('drop_lat');
                $drop_long        = $request->input('drop_long');
                
                $arr_data['driver_id']        = $driver_id;
                $arr_data['rider_id']         = $rider_id;
                $arr_data['vehicle_id']       = $vehicle_id;
                $arr_data['pick_up_location'] = $pick_up_location;
                $arr_data['drop_location']    = $drop_location;
                $arr_data['request_status']   = $request_status;
                $arr_data['pick_up_lat']      = $pick_up_lat;
                $arr_data['pick_up_long']     = $pick_up_long;
                $arr_data['drop_lat']         = $drop_lat;
                $arr_data['drop_long']        = $drop_long;
               // dd($request->all());
                if (isset($arr_data)) 
                {
                    $result = $this->RiderToDriverRequestModel->create($arr_data);
                    if($result)
                    {
                        $arr_notification_data = $this->built_notification_data($arr_data,'RIDER_SEND_TO_DRIVER_REQUEST'); 
                        $this->NotificationsService->store_notification($arr_notification_data);

                        return 
                            [
                                'status' => 'success',
                                'msg'    => 'Rider send to request on driver.'
                            ];
                    }
                    else
                    {
                        return 
                            [
                                'status' => 'error',
                                'msg'    => 'Problem Occurred, While send to Rider request to driver.'
                            ];
                    }
                }
            }
           
            if ($request_status == "ACCEPT_BY_DRIVER" || $request_status == "REJECT_BY_DRIVER" ) 
            {
                $arr_data  = [];
                $id        = $request->input('request_id');
                $obj_data  = $this->RiderToDriverRequestModel
                                                            ->where('id',$id)
                                                            ->first();
                if ($obj_data) 
                {
                    $arr_data['driver_id'] = $obj_data->driver_id;
                    $arr_data['rider_id']  = $obj_data->rider_id;  
                }

                $result = $this->RiderToDriverRequestModel->where('id',$id)
                                                          ->update(['request_status'=>$request_status]);

                if ($result) 
                {
                    $arr_notification_data = $this->built_notification_data($arr_data,'DRIVER_ACCEPT_REJECT_BY_REQUEST'); 
                    $this->NotificationsService->store_notification($arr_notification_data);
                        
                        if ($request_status == "ACCEPT_BY_DRIVER")
                        {  
                            $obj_driver_status = $this->DriverStatusModel
                                                        ->where('driver_id',$driver_id)
                                                        ->update(['status'=>'BUSY']);

                            if(isset($obj_driver_status))
                            {
                                return 
                                [
                                    'status' => 'success',
                                    'msg'    => 'Driver accepted by the rider request.'
                                ];    
                            }
                                     
                        }
                        else
                        {
                            $obj_driver_status = $this->DriverStatusModel
                                                        ->where('driver_id',$driver_id)
                                                        ->update(['status'=>'AVAILABLE']);

                            return 
                                [
                                    'status' => 'error',
                                    'msg'    => 'Driver rejected by the rider request.'
                                ];                        
                        }
                }
                else
                {                    
                      return 
                        [
                            'status' => 'error',
                            'msg'    => 'Invalid request id.'
                        ];                        
                }        
            }

            if ($request_status == "ACCEPT_BY_RIDER" || $request_status == "REJECT_BY_RIDER") 
            {
                $id     = $request->input('request_id');

                if ($request_status == "ACCEPT_BY_RIDER") 
                {
                    $obj_update = $this->RiderToDriverRequestModel
                                                            ->where('id',$id)
                                                            ->update(['request_status'=>$request_status]);
                    if($obj_update) 
                    {    
                        $obj_ride = $this->RiderToDriverRequestModel
                                                            ->where('id',$id)
                                                            ->first();
                        
                        $arr_data['driver_id']          = $obj_ride->driver_id;
                        $arr_data['rider_id']           = $obj_ride->rider_id;
                        $arr_data['vehicle_id']         = $obj_ride->vehicle_id;
                        $arr_data['ride_unique_id']     = $this->genrate_event_ride_number();
                        $arr_data['pick_up_location']   = $obj_ride->pick_up_location; 
                        $arr_data['drop_location']      = $obj_ride->drop_location;
                        $arr_data['status']             = 'TO_BE_PICKED';
                        $arr_data['is_service_used']    = $request->input('is_service_used');
                        
                        $result = $this->RideModel->create($arr_data);   
                        if ($result) 
                        {
                            return 
                                [
                                    'status' => 'success',
                                    'msg'    => 'Rider accepted the ride.'
                                ];        
                        }                              
                    }              
                }
                else
                {
                    $obj_update = $this->RiderToDriverRequestModel
                                                            ->where('id',$id)
                                                            ->update(['request_status'=>$request_status]);
                    if ($obj_update) 
                    {
                        return 
                            [
                                'status' => 'error',
                                'msg'    => 'Rider rejected the ride.'
                            ];   
                    }                       
                }       
            }                                  
        }
        return 
            [
                'status' => 'error',
                'msg'    => 'Please fill all the required field.'
            ];        
    } 

    public function get_ride_request($request)
    {    
        $arr_data       = [];
        $driver_id      = $request->input('driver_id');
        $request_status = $request->input('request_status');

        $obj_data       = $this->RiderToDriverRequestModel
                                                        ->where('driver_id',$driver_id)
                                                        ->where('request_status', $request_status)
                                                        ->get();
        if ($obj_data) 
        {
            $arr_data = $obj_data->toArray();
            return 
            [
                'status'    => 'success',
                'msg'       => 'Rider request are found.',
                'arr_data'  =>  $arr_data
            ];
        }
        else
        {
            return 
            [
                'status'    => 'error',
                'msg'       => 'Rider request not found.',
                'arr_data'  => []
            ];      
        }
    }

    private function built_notification_data($arr_data,$type)
    {
        $arr_notification = [];
        if(isset($arr_data) && sizeof($arr_data)>0)
        {
            /*$arr_notification['user_id']           = $this->CommonDataService->get_admin_id();*/
            $arr_notification['is_read']           = 0;
            $arr_notification['is_show']           = 0;
            $arr_notification['user_type']         = 'driver';

            if($type == 'RIDER_SEND_TO_DRIVER_REQUEST')
            {
                $driver_id  = isset($arr_data['driver_id']) ? $arr_data['driver_id'] :0;
                $rider_id   = isset($arr_data['rider_id']) ? $arr_data['rider_id'] :0;
                $arr_rider = $this->CommonDataService->get_user_details($rider_id);

                $first_name = isset($arr_rider['first_name']) ? $arr_rider['first_name'] :'';
                $last_name  = isset($arr_rider['last_name']) ? $arr_rider['last_name'] :'';

                $full_name  = $first_name.' '.$last_name;
                $full_name  = ($full_name!=' ') ? $full_name : '-';
                
                $arr_notification['user_type']         = 'RIDER';
                $arr_notification['driver_id']         = $driver_id;
                $arr_notification['rider_id']          = $rider_id;
                $arr_notification['user_id']           = $rider_id;                               
                $arr_notification['notification_type'] = 'rider send to driver request';
                $arr_notification['title']             = $full_name.' rider send to driver request on '.config('app.project.name').'.';
                $arr_notification['view_url']          = url(config('app.project.admin_panel_slug')."/driver/rider_send_to_driver_request").'/'.base64_encode($rider_id);
               
            }

            if($type == 'DRIVER_ACCEPT_REJECT_BY_REQUEST')
            {
                $driver_id  = isset($arr_data['driver_id']) ? $arr_data['driver_id'] :0;
                $rider_id   = isset($arr_data['rider_id']) ? $arr_data['rider_id'] :0;
                
                $arr_driver = $this->CommonDataService->get_user_details($driver_id);

                $first_name = isset($arr_driver['first_name']) ? $arr_driver['first_name'] :'';
                $last_name  = isset($arr_driver['last_name']) ? $arr_driver['last_name'] :'';

                $full_name  = $first_name.' '.$last_name;
                $full_name  = ($full_name!=' ') ? $full_name : '-';
                
                $arr_notification['user_type']         = 'rider';
                $arr_notification['driver_id']         = $driver_id;
                $arr_notification['rider_id']          = $rider_id;
                $arr_notification['user_id']           = $driver_id;
                $arr_notification['notification_type'] = 'driver accepted and rejected the rider request';
                $arr_notification['title']             = $full_name.' driver accepted and rejected the rider request';
                $arr_notification['view_url']          = url(config('app.project.admin_panel_slug')."/driver/rider_send_to_driver_request").'/'.base64_encode($driver_id);               
            }
            
        }
        return $arr_notification;
    }

    function genrate_event_ride_number()
    {
        $secure = TRUE;    
        $bytes = openssl_random_pseudo_bytes(6, $secure);
        $order_ref_num = "".bin2hex($bytes);

        return strtoupper($order_ref_num);
    }


    public function get_all_drivers($request)
    {
        $arr_driver_details     = [];
        $arr_data               = [];
        $arr_driver_name        = [];
        $arr_driver_fair_charge = [];

        $vehicle_type_id        = $request->input('cat_id');
        
        $source_address         = $request->input('source_address');
        $source_lat             = $request->input('source_lat');
        $source_lng             = $request->input('source_lng');
        
        $destination_address    = $request->input('destination_address');
        
        $destination_lat        = $request->input('destination_lat');
        $destination_lng        = $request->input('destination_lng');

        $distance                = 15;

        $user_tbl                = $this->UserModel->getTable();
        $role_tbl                = $this->RoleModel->getTable();
        $user_roles_tbl          = $this->UserRoleModel->getTable();
        $driver_car_relation_tbl = $this->DriverCarRelationModel->getTable();
        $vehicle_tbl             = $this->VehicleModel->getTable();
        $driver_status_tbl       = $this->DriverStatusModel->getTable();
        $driver_fair_charge_tbl  = $this->DriverFairChargeModel->getTable();
        $vehicle_type_tbl        = $this->VehicleTypeModel->getTable();

        $sql_query = "Select
                            ".$user_tbl.".id AS driver_id,
                        CONCAT(
                                ".$user_tbl.".first_name,
                                ' ',
                                ".$user_tbl.".last_name
                            ) AS driver_name,
                            ".$driver_fair_charge_tbl.".fair_charge as fair_charge,
                            ".$driver_status_tbl.".current_latitude as current_latitude,
                            ".$driver_status_tbl.".current_longitude as current_longitude,

                            
                        ROUND( 6371 * acos (
                                cos ( radians(".$source_lat.") )
                                * cos( radians( `current_latitude` ) )
                                * cos( radians( `current_longitude` ) - radians(".$source_lng.") )
                                + sin ( radians(".$source_lat.") )
                                * sin( radians( `current_latitude` ) )
                              )) as distance

                        FROM
                            ".$user_tbl."
                        JOIN
                            ".$user_roles_tbl." ON ".$user_roles_tbl.".user_id = ".$user_tbl.".id
                        JOIN
                            ".$role_tbl." ON ".$role_tbl.".id = ".$user_roles_tbl.".role_id
                        JOIN
                            ".$driver_car_relation_tbl." ON ".$driver_car_relation_tbl.".driver_id = ".$user_tbl.".id
                        JOIN
                            ".$driver_status_tbl." ON ".$driver_status_tbl.".driver_id = ".$user_tbl.".id
                        JOIN
                            ".$vehicle_tbl." ON ".$vehicle_tbl.".id = ".$driver_car_relation_tbl.".vehicle_id
                        JOIN
                            ".$vehicle_type_tbl." ON ".$vehicle_type_tbl.".id = ".$vehicle_tbl.".vehicle_type_id
                        JOIN
                           ".$driver_fair_charge_tbl." ON ".$driver_fair_charge_tbl.".driver_id = ".$user_tbl.".id
                        WHERE 
                            ".$user_tbl.".is_active = '1' AND
                            ".$user_tbl.".is_user_block_by_admin = '0' AND
                            ".$role_tbl.".slug = 'driver' AND
                            ".$driver_status_tbl.".status = 'AVAILABLE' AND
                            ".$driver_car_relation_tbl.".is_car_assign = '1' AND
                            ".$vehicle_type_tbl.".id = ".$vehicle_type_id."
                        HAVING distance <=".$distance;
                        
        $obj_driver_details =  DB::select($sql_query);

        if ($obj_driver_details) 
        {
            $arr_response['status'] = 'success';
            $arr_response['msg']    = 'Driver details are found.';
            $arr_response['data']   = $obj_driver_details;
            return $arr_response;
        }
        else
        {
            $arr_response['status'] = 'error';
            $arr_response['msg']    = 'Driver details are not found.';
            $arr_response['data']   = [];
            return $arr_response;      
        }
        
        $arr_response['status'] = 'error';
        $arr_response['msg']    = 'Driver details are not found.';
        $arr_response['data']   = [];
        return $arr_response;      
    }

    public function distance($lat1, $lon1, $lat2, $lon2, $unit) 
    {
          $theta = $lon1 - $lon2;
          $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
          $dist = acos($dist);
          $dist = rad2deg($dist);
          $miles = $dist * 60 * 1.1515;
          $unit = strtoupper($unit);
          $distance_in_km = $miles * 1.609344;
          if($distance_in_km>15)
          {
            return 'error';
          }
          else
          {
            return 'success';
          }
    }

    public function get_booking_details($request)
    {
        $booking_id      = $request->input('booking_id');
        $arr_data        = [];
        $arr_response    = [];
      
        $obj_rider_booking_details = $this->RideModel
                                                 ->with('rider_details')
                                                 ->with('driver_details')
                                                 ->with('vehicle_details')
                                                 ->where('ride_unique_id',$booking_id)
                                                 ->get();

        if ($obj_rider_booking_details) 
        {
            $arr_rider_booking_details = $obj_rider_booking_details->toArray();    
            
            foreach ($arr_rider_booking_details as $key => $value) 
            {
                $driver_first_name = isset($value['driver_details']['first_name']) ? $value['driver_details']['first_name']:'';
                $driver_last_name  = isset($value['driver_details']['last_name']) ? $value['driver_details']['last_name']:'';
                $driver_name       = $driver_first_name.' '.$driver_last_name;

                $rider_first_name  = isset($value['rider_details']['first_name']) ? $value['driver_details']['first_name']:'';
                $rider_last_name   = isset($value['driver_details']['last_name']) ? $value['driver_details']['last_name']:'';
                $rider_name        = $rider_first_name.' '.$rider_last_name;

                $arr_data[$key]['driver_name']               = $driver_name;
                $arr_data[$key]['rider_name']                = $rider_name;
                $arr_data[$key]['booking_id']                = isset($value['ride_unique_id']) ? $value['ride_unique_id']:'';
                $arr_data[$key]['vehicle_name']              = isset($value['vehicle_details']['vehicle_name']) ? $value['vehicle_details']['vehicle_name']:'';
                $arr_data[$key]['vehicle_number']            = isset($value['vehicle_details']['vehicle_number']) ? $value['vehicle_details']['vehicle_number']:'';
                $arr_data[$key]['distance']                  = isset($value['distance']) ? $value['distance']:0;
                $arr_data[$key]['pick_up_location']          = isset($value['pick_up_location']) ? $value['pick_up_location']:'';
                $arr_data[$key]['drop_location']             = isset($value['drop_location']) ? $value['drop_location']:'';
                $arr_data[$key]['charge']                    = isset($value['charge']) ? $value['charge']:'';
                $arr_data[$key]['driver_fair_charge']        = isset($value['driver_fair_charge']) ? $value['driver_fair_charge']:'';
                $arr_data[$key]['admin_commission']          = isset($value['admin_commission']) ? $value['admin_commission']:'';
                $arr_data[$key]['promo_code']                = isset($value['promo_code']) ? $value['promo_code']:'';
                $arr_data[$key]['is_promo_code_applied']     = isset($value['is_promo_code_applied']) ? $value['is_promo_code_applied']:'';
                $arr_data[$key]['promo_percentage']          = isset($value['promo_percentage']) ? $value['promo_percentage']:'';
                $arr_data[$key]['applied_promo_code_charge'] = isset($value['applied_promo_code_charge']) ? $value['applied_promo_code_charge']:'';
                $arr_data[$key]['total_amount']              = isset($value['total_amount']) ? $value['total_amount']:'';
                $arr_data[$key]['final_amount']              = isset($value['final_amount']) ? $value['final_amount']:0;
                $arr_data[$key]['confirmation_credit_card']  = isset($value['confirmation_credit_card']) ? $value['confirmation_credit_card']:'';
                $arr_data[$key]['payment_status']            = isset($value['payment_status']) ? $value['payment_status']:'';
                $arr_data[$key]['status']                    = isset($value['status']) ? $value['status']:'';

            }   
            $arr_response['status']     = 'success';
            $arr_response['msg']        = 'Rider booking details are found.';
            $arr_response['arr_data']   = $arr_data;
            
            return $arr_response;
        }
        else
        {
            $arr_response['status']   = 'success';
            $arr_response['msg']      = 'Invalid booking id.';
            $arr_response['arr_data'] = $arr_data;
            
            return $arr_response;
        }         
    }

    public function get_booking_history($request)
    {
        $user_id      = $request->input('user_id');
        $user_id      = $this->CommonDataService->decrypt_value($user_id);
        $arr_data     = [];
        $arr_response = [];

        $obj_rider_details = $this->RideModel
                                             ->with('rider_details')
                                             ->with('driver_details')
                                             ->with('vehicle_details')
                                             ->where('rider_id',$user_id)
                                             ->select('rider_id','driver_id','vehicle_id','final_amount','ride_unique_id','pick_up_location','drop_location','distance','is_promo_code_applied','promo_code','status')
                                             ->paginate(2);
        if ($obj_rider_details) 
        {
            $arr_rider_details = $obj_rider_details->toArray();    
            
            foreach ($arr_rider_details['data'] as $key => $value) 
            {
                $first_name = isset($value['driver_details']['first_name']) ? $value['driver_details']['first_name']:'';
                $last_name  = isset($value['driver_details']['last_name']) ? $value['driver_details']['last_name']:'';
                $full_name  = $first_name.' '.$last_name;

                $arr_data[$key]['driver_name']      = $full_name;
                $arr_data[$key]['booking_id']       = isset($value['ride_unique_id']) ? $value['ride_unique_id']:'';
                $arr_data[$key]['vehicle_name']     = isset($value['vehicle_details']['vehicle_name']) ? $value['vehicle_details']['vehicle_name']:'';
                $arr_data[$key]['vehicle_number']   = isset($value['vehicle_details']['vehicle_number']) ? $value['vehicle_details']['vehicle_number']:'';
                $arr_data[$key]['distance']         = isset($value['distance']) ? $value['distance']:0;
                $arr_data[$key]['pick_up_location'] = isset($value['pick_up_location']) ? $value['pick_up_location']:'';
                $arr_data[$key]['drop_location']    = isset($value['drop_location']) ? $value['drop_location']:'';
                $arr_data[$key]['final_amount']     = isset($value['final_amount']) ? $value['final_amount']:0;
                $arr_data[$key]['promo_code']       = isset($value['promo_code']) ? $value['promo_code']:'';
            }   
            
            $arr_response['status'] = 'success';
            $arr_response['msg']    = 'Rider booking history are found.';
            $arr_response['arr_data']   = $arr_data;
            
            return $arr_response;
        }
        else
        {
            $arr_response['status'] = 'error';
            $arr_response['msg']    = 'Rider booking history are not found.';
            $arr_response['arr_data']   = $arr_data;   
            
            return $arr_response;
        }         
    }

    /* NOT IN USE
    public function store_family_member($request)
    {
        $arr_rules = [];
        $arr_rules['user_id']          = "required";
        $arr_rules['email']            = "required";
        $arr_rules['mobile_no']        = "required";
        
        $validator = Validator::make($request->all(),$arr_rules);

        if($validator->fails())
        {
            return 
                    [
                        'status' => 'error',
                        'msg'    => 'Please fill all the required field.'
                    ];
        }
   
        $user_id          = $request->input('user_id');
        $user_id          = $this->CommonDataService->decrypt_value($user_id);

        $email            = trim($request->input('email'));
        $mobile_no        = trim($request->input('mobile_no'));

        $is_member_exist = $this->UserModel->where('email',$email)
                                                           ->first();
        
        if($is_member_exist)
        {
           $family_member_id = $is_member_exist->id ;

           $arr_data = [];
           $arr_data['rider_id'] = $user_id;
           $arr_data['family_member_id'] = $family_member_id;
           $already_exist = $this->RiderFamilyMemberModel->where('rider_id',$user_id)->where('family_member_id',$family_member_id)->count();
           if($already_exist>0)
           {
                return 
                    [
                        'status' => 'error',
                        'msg'    => 'Member already added.'
                    ];
           }
           else
           {
               $result = $this->RiderFamilyMemberModel->create($arr_data);
               if($result)
               {
                    return 
                        [
                            'status' => 'success',
                            'msg'    => 'Family member details store successfully.'
                        ];
               }
               else
               {
                    return 
                    [
                        'status' => 'error',
                        'msg'    => 'Problem Occurred, While storing Family member.'
                    ]; 
               }
           } 
        }
        else
        {
            $arr_data               = [];
            $arr_data['user_id']    = $user_id;
            $arr_data['email']      = $email;
            $arr_data['mobile_no']  = $mobile_no;
                
            $already_exist = $this->FamilyMemberInfoModel->where('user_id',$user_id)->where('email',$email)->count();
            if($already_exist>0)
            {
                return 
                    [
                        'status' => 'error',
                        'msg'    => 'Member already added.'
                    ];
            }

            $status = $this->FamilyMemberInfoModel->create($arr_data);
            if($status)
            {
                $email_data['email'] = $email;
                $arr_mail_data = $this->built_mail_data($email_data); 
                $email_status  = $this->EmailService->send_mail($arr_mail_data);
                    return 
                            [
                                'status' => 'success',
                                'msg'    => 'Family member details store successfully. Family member will add after registration with app.'
                            ];    
                
                
                
            }
            else
            {      
                return 
                    [
                        'status' => 'error',
                        'msg'    => 'Problem Occurred, While storing Family member.'
                    ];
            }
        }
    }

    public function get_family_members_info($request)
    {
        $arr_response['status']  = 'error';
        $arr_response['msg']     = 'No family member details found.';
        $arr_response['data']    = [];

        $arr_data = [];

        $rider_id = $request->input('rider_id');
        $rider_id = $this->CommonDataService->decrypt_value($rider_id);
        $obj_data = $this->RiderFamilyMemberModel
                             ->with('family_member_details')
                             ->where('rider_id',$rider_id)
                             ->groupBy('family_member_id') 
                             ->paginate(10);

        $arr_final_data =[];                     
        if($obj_data)
        {
            $arr_members = $obj_data->toArray();
         
            $arr_final_data["total"]         = $arr_members["total"];
            $arr_final_data["per_page"]      = $arr_members["per_page"];
            $arr_final_data["current_page"]  = $arr_members["current_page"];
            $arr_final_data["last_page"]     = $arr_members["last_page"];
            $arr_final_data["next_page_url"] = $arr_members["next_page_url"];
            $arr_final_data["prev_page_url"] = $arr_members["prev_page_url"];

            $arr_final = [];    
            if(count($arr_members["data"])>0)
            {
                foreach($arr_members["data"] as $value)
                {   
                    if(isset($value['family_member_details']) && $value['family_member_details']!=null)
                    {
                          if(isset($value['family_member_details']['profile_image']) && !empty($value['family_member_details']['profile_image']))
                          {
                                $value['family_member_details']['profile_image'] = $this->user_profile_public_img_path.$value['family_member_details']['profile_image'] ;
                          }
                          
                          array_push($arr_data,$value['family_member_details']);
                    }
                }
                $arr_final_data['data'] = $arr_data;
            }       
        }
        if(isset($arr_final_data['data']) && sizeof($arr_final_data['data'])>0)
        {
            $arr_response['status']  = 'success';
            $arr_response['msg']     = 'Members list fetched successfully';
            $arr_response['data']    = $arr_final_data;
        }    
        return $arr_response;
    }*/
}
?>