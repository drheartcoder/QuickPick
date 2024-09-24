<?php
namespace App\Common\Services\Cron;


use App\Models\UserModel;
use App\Models\LoadPostRequestModel;
use App\Common\Services\CommonDataService;
use App\Common\Services\NotificationsService;

class FutureBookingRequestService
{

    public function __construct(
                                    UserModel $user,
                                    LoadPostRequestModel $load_post_request,
                                    CommonDataService $common_data_service,
                                    NotificationsService $notifications_service
                                )
    {
        $this->distance             = 50;
        $this->UserModel            = $user;
        $this->LoadPostRequestModel = $load_post_request;
        $this->CommonDataService    = $common_data_service;
        $this->NotificationsService = $notifications_service;
    }

    public function check_future_bookings()
    {
        //$this->NotificationsService->store_notification(['title'=>'test cron job working']);

        $arr_load_post_request = $this->get_future_request_list_details(); 
        
        if(isset($arr_load_post_request) && sizeof($arr_load_post_request)>0)
        {
            foreach ($arr_load_post_request as $key => $value) 
            {
                $load_post_request_id = isset($value['id']) ? $value['id'] : 0;
                $pickup_lat           = isset($value['pickup_lat']) ? $value['pickup_lat'] :'';
                $pickup_lng           = isset($value['pickup_lng']) ? $value['pickup_lng'] :'';
                $package_type         = isset($value['load_post_request_package_details']['package_type']) ? $value['load_post_request_package_details']['package_type'] : '';
                $package_quantity     = isset($value['load_post_request_package_details']['package_quantity']) ? $value['load_post_request_package_details']['package_quantity'] : 0;
                $package_volume       = isset($value['load_post_request_package_details']['package_volume']) ? $value['load_post_request_package_details']['package_volume'] : 0;
                $package_weight       = isset($value['load_post_request_package_details']['package_weight']) ? $value['load_post_request_package_details']['package_weight'] : 0;
                
                $user_id = isset($value['user_id']) ? $value['user_id'] : 0;
                $str_except_driver_id = $this->get_user_driver_id($user_id);

                $arr_driver_search_data = 
                                            [
                                                'pickup_lat'           => $pickup_lat,
                                                'pickup_lng'           => $pickup_lng,
                                                'package_type'         => $package_type,
                                                'package_quantity'     => intval($package_quantity),
                                                'package_volume'       => $package_volume,
                                                'package_weight'       => $package_weight,
                                                'distance'             => $this->distance,
                                                'load_post_request_id' => $load_post_request_id,
                                                'str_except_driver_id' => strval($str_except_driver_id)
                                            ];

                $arr_driver_details = $this->cron_search_available_driver($arr_driver_search_data);
                
                $this->LoadPostRequestModel->where('id',$load_post_request_id)->update([
                                                                                                'is_request_process'=>'1',
                                                                                                'created_at'        => date('Y-m-d h:i:s')
                                                                                            ]);

                if(isset($arr_driver_details) && sizeof($arr_driver_details)>0)
                {
                    $user_id            = isset($value['user_id']) ? $value['user_id'] :0;
                    // send notification to user
                    $arr_notification_data = 
                                            [
                                                'title'             => 'Future booking request send to driver,will notify soon.',
                                                'notification_type' => 'FUTURE_BOOKING',
                                                'record_id'         => $load_post_request_id,
                                                'enc_user_id'       => $user_id,
                                                'user_type'         => 'USER',

                                            ];

                    $this->NotificationsService->send_on_signal_notification($arr_notification_data);
                    
                    dump('notification send to driver');
                }
                else
                {
                    dump('drivers not avalible');
                }
            }
        }
        dump('future booking records not avalible');
        dd('--------------------');
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
    private function get_future_request_list_details()
    {
        $current_date       = date('Y-m-d');
        
        $start_date_time = new \DateTime();
        $start_date_time->modify('+10 min');
        $start_time = $start_date_time->format('H:i:s');
        
        $end_date_time = new \DateTime();
        $end_date_time->modify('+30 min');
        $end_time = $end_date_time->format('H:i:s');

        $arr_load_post_request = [];

        $obj_load_post_request = $this->LoadPostRequestModel
                                            ->select('id','user_id','date','pickup_lat','pickup_lng')
                                            ->with('load_post_request_package_details')
                                            ->where([
                                                        'is_future_request'  => '1',
                                                        'is_request_process' => '0',
                                                        'request_status'     => 'USER_REQUEST'
                                                    ])
                                            ->whereRaw('DATE(date) = "'.$current_date.'"')
                                            ->whereRaw('TIME(request_time) >= "'.$start_time.'"')
                                            ->whereRaw('TIME(request_time) <= "'.$end_time.'"')
                                            ->get();
        if($obj_load_post_request)
        {
            $arr_load_post_request = $obj_load_post_request->toArray();
        }
        return $arr_load_post_request;
    }
    private function cron_search_available_driver($arr_data)
    {
        if(isset($arr_data) && sizeof($arr_data)>0){

            $pickup_lat           = isset($arr_data['pickup_lat']) ? $arr_data['pickup_lat'] :0;
            $pickup_lng           = isset($arr_data['pickup_lng']) ? $arr_data['pickup_lng'] :0;
            $package_type         = isset($arr_data['package_type']) ? $arr_data['package_type'] :'';
            $package_quantity     = isset($arr_data['package_quantity']) ? $arr_data['package_quantity'] :0;
            $package_volume       = isset($arr_data['package_volume']) ? $arr_data['package_volume'] :0;
            $package_weight       = isset($arr_data['package_weight']) ? $arr_data['package_weight'] :0;
            $distance             = isset($arr_data['distance']) ? $arr_data['distance'] :0;
            $load_post_request_id = isset($arr_data['load_post_request_id']) ? $arr_data['load_post_request_id'] :0;
            $str_except_driver_id = isset($arr_data['str_except_driver_id']) ? $arr_data['str_except_driver_id'] :'';
            
            $sql_query = '';
            $sql_query .= "Select ";
            $sql_query .= "users.id AS driver_id, ";
            $sql_query .= "CONCAT( users.first_name, ' ',users.last_name ) AS driver_name, ";
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
            
            if($package_type == 'PALLET') {
                $sql_query .=  "VT.no_of_pallet >= ".$package_quantity." ";
            }
            else {
                $sql_query .=  "VT.vehicle_min_volume <= ".$package_volume." AND ";
                $sql_query .=  "VT.vehicle_max_volume >= ".$package_volume." AND ";
                $sql_query .=  "VT. vehicle_min_weight <= ".$package_weight." AND ";
                $sql_query .=  "VT.vehicle_max_weight >= ".$package_weight." ";
                
            }


            $sql_query .=  "HAVING distance <=".$distance;
            
            $obj_driver_details =  \DB::select($sql_query);
            
            $arr_driver_details = json_decode(json_encode($obj_driver_details), true);

            if(isset($arr_driver_details) && sizeof($arr_driver_details)>0){
                
                /*
                |manually fliter data also remove driver which does not verify their indivital vechiles 
                |also remove drivers which are in restricted area.
                */
                foreach ($arr_driver_details as $key => $driver_details) 
                {
                    $arr_driver_details[$key]['load_post_request_id'] = intval($load_post_request_id);

                    if( isset($driver_details['is_individual_vehicle']) && $driver_details['is_individual_vehicle'] == '1')
                    {
                        if(isset($driver_details['is_verified']) && $driver_details['is_verified'] == '0')
                        {
                            unset($arr_driver_details[$key]);
                        }
                    }
                }
                $this->send_notification_to_drivers($arr_driver_details,$load_post_request_id);
            }
            return $arr_driver_details;
        }
        return [];
    }
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
                                                'enc_user_id'       => $enc_user_id,
                                                'user_type'         => 'DRIVER',

                                            ];

                    $this->NotificationsService->send_on_signal_notification($arr_notification_data);
                }
            }
        }   
        return true;
    }
}