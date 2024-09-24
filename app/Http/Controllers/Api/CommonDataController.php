<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Common\Services\CommonDataService;
use App\Common\Services\StripeService;
use App\Models\StaticPageModel;
use App\Models\UserModel;
use App\Models\MessagesModel;
use App\Models\BookingMasterModel;
use App\Common\Services\NotificationsService;

use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;




class CommonDataController extends Controller
{
    public function __construct(CommonDataService $common_data_service,StripeService $stripe_service,StaticPageModel $static_page,UserModel $user,MessagesModel $messages,BookingMasterModel $booking_master,NotificationsService $notifications_service)
    {
        $this->CommonDataService  = $common_data_service;
        $this->StripeService      = $stripe_service;
        $this->StaticPageModel    = $static_page;
        $this->UserModel          = $user;
        $this->MessagesModel      = $messages;
        $this->BookingMasterModel = $booking_master;
        $this->NotificationsService = $notifications_service;
        

        if(env('IS_LIVE_MODE') == 'YES')
        {
            /*LIVE KEYS*/
            $this->publish_key  = config('services.stripe_live.api_key');
            $this->secret_key   = config('services.stripe_live.api_secret');
        }
        else if(env('IS_LIVE_MODE') == 'NO')
        {
            /*TEST KEYS*/
            $this->publish_key  = config('services.stripe_test.api_key');
            $this->secret_key   = config('services.stripe_test.api_secret');
        }
        
        $this->stripe_client_id = config('services.stripe_client_id');
        $this->stripe_token_url = config('services.stripe_token_url');

        $this->user_profile_public_img_path = url('/').config('app.project.img_path.user_profile_images');
        $this->user_profile_base_img_path   = base_path().config('app.project.img_path.user_profile_images');
        $this->per_page = 10;

    }

    public function apply_promo_code(Request $request)
    {
        $arr_response    = $this->CommonDataService->check_valid_promo_code($request);
        return $this->build_response($arr_response['status'],$arr_response['msg'],$arr_response['data']);   
    }

    public function get_promotional_offers(Request $request)
    {
        $arr_response = [];

        $arr_response['status']   = 'error';
        $arr_response['msg']      = 'No Promotional Offers available';
        $arr_response['data']     = [];
        
        $arr_promotional_offers = $this->CommonDataService->get_promotional_offers($request);

        if(isset($arr_promotional_offers['data']) && sizeof($arr_promotional_offers['data'])>0)
        {
            $arr_response['status']  = 'success';
            $arr_response['msg']     = 'Promotional Offers fetched successfully';
            $arr_response['data']    = $arr_promotional_offers;
        }

        return $this->build_response($arr_response['status'], $arr_response['msg'], $arr_response['data']);   
    }
    
    public function get_review_tags(Request $request)
    {
        $arr_response = [];

        $arr_response['status']           = 'error';
        $arr_response['msg']              = 'No review tags available';
        $arr_response['arr_review_tags']     = [];
        
        $arr_review_tags = $this->CommonDataService->get_review_tags();

        if(isset($arr_review_tags) && sizeof($arr_review_tags)>0)
        {
            $arr_response['status']           = 'success';
            $arr_response['msg']              = 'Review tags fetched successfully';
            $arr_response['arr_review_tags'] = $arr_review_tags;
        }

        return $this->build_response($arr_response['status'], $arr_response['msg'], $arr_response['arr_review_tags']);   
    }
    
    public function get_vehicle_type()
    {
        $arr_response['status']  = 'error';
        $arr_response['msg']     = 'Vehicle type not found';
        $arr_response['data']    = [];
        
        $arr_result    = $this->CommonDataService->get_vehicle_types();
        if(sizeof($arr_result)>0){
            $arr_response['status']  = 'success';
            $arr_response['msg']     = 'Vehicle type found successfully';
            $arr_response['data']    = $arr_result;

        }
        return $this->build_response($arr_response['status'],$arr_response['msg'],$arr_response['data']);   
    }
    
    public function get_package_type()
    {
        $arr_response['status']  = 'error';
        $arr_response['msg']     = 'Package type not found';
        $arr_response['data']    = [];
        
        $arr_result    = $this->CommonDataService->get_package_type();
        if(sizeof($arr_result)>0){
            $arr_response['status']  = 'success';
            $arr_response['msg']     = 'Package type found successfully';
            $arr_response['data']    = $arr_result;

        }
        return $this->build_response($arr_response['status'],$arr_response['msg'],$arr_response['data']);   
    }

    
    public function get_vehicle_brand()
    {
        $arr_response['status']  = 'error';
        $arr_response['msg']     = 'Vehicle brand not found';
        $arr_response['data']    = [];
        
        $arr_result    = $this->CommonDataService->get_vehicle_brand();
        if(sizeof($arr_result)>0){
            $arr_response['status']  = 'success';
            $arr_response['msg']     = 'Vehicle brand found successfully';
            $arr_response['data']    = $arr_result;

        }
        return $this->build_response($arr_response['status'],$arr_response['msg'],$arr_response['data']);   
    }

    public function get_vehicle_model(Request $request)
    {
        $arr_response['status']  = 'error';
        $arr_response['msg']     = 'Vehicle model not found';
        $arr_response['data']    = [];
        
        $arr_result    = $this->CommonDataService->get_vehicle_model($request);
        if(sizeof($arr_result)>0){
            $arr_response['status']  = 'success';
            $arr_response['msg']     = 'Vehicle model found successfully';
            $arr_response['data']    = $arr_result;

        }
        return $this->build_response($arr_response['status'],$arr_response['msg'],$arr_response['data']);   
    }
    
    public function get_vehicle_year(Request $request)
    {
        $arr_response['status']  = 'error';
        $arr_response['msg']     = 'Vehicle year not found';
        $arr_response['data']    = [];
        
        $arr_result    = $this->CommonDataService->get_vehicle_year($request);
        if(sizeof($arr_result)>0){
            $arr_response['status']  = 'success';
            $arr_response['msg']     = 'Vehicle year found successfully';
            $arr_response['data']    = $arr_result;

        }
        return $this->build_response($arr_response['status'],$arr_response['msg'],$arr_response['data']);   
    }

    public function about_us()
    {
        $arr_data = [];

        /*$obj_data = $this->StaticPageModel
                                    ->where('page_slug','about-us')
                                    ->first();
        if($obj_data)
        {
            $arr_data = $obj_data->toArray();
        }*/

        $arr_data['page_desc'] = url('/about_us');
        if(isset($arr_data) && sizeof($arr_data)>0)
        {
            $arr_response['status']  = 'success';
            $arr_response['msg']     = 'About us page details found.';
            $arr_response['data']    = $arr_data;
        }
        else
        {
            $arr_response['status']  = 'error';
            $arr_response['msg']     = 'About us page details not found.';
            $arr_response['data']    = [];   
        }
        return $this->build_response($arr_response['status'],$arr_response['msg'],$arr_response['data']);   
    }

    public function help()
    {
        $arr_data = [];

        /*$obj_data = $this->StaticPageModel
                                    ->where('page_slug','help')
                                    ->first();
        if($obj_data)
        {
            $arr_data = $obj_data->toArray();
        }*/
        $arr_data['page_desc'] = url('/help');
        if(isset($arr_data) && sizeof($arr_data)>0)
        {
            $arr_response['status']  = 'success';
            $arr_response['msg']     = 'About us page details found.';
            $arr_response['data']    = $arr_data;
        }
        else
        {
            $arr_response['status']  = 'error';
            $arr_response['msg']     = 'About us page details not found.';
            $arr_response['data']    = [];   
        }
        return $this->build_response($arr_response['status'],$arr_response['msg'],$arr_response['data']);   
    }

    public function terms_and_conditions()
    {
        $arr_data = [];

        /*$obj_data = $this->StaticPageModel
                                    ->where('page_slug','terms-and-conditions')
                                    ->first();
        if($obj_data)
        {
            $arr_data = $obj_data->toArray();
        }*/
        $arr_data['page_desc'] = url('/terms_and_conditions');
        if(isset($arr_data) && sizeof($arr_data)>0)
        {
            $arr_response['status']  = 'success';
            $arr_response['msg']     = 'About us page details found.';
            $arr_response['data']    = $arr_data;
        }
        else
        {
            $arr_response['status']  = 'error';
            $arr_response['msg']     = 'About us page details not found.';
            $arr_response['data']    = [];   
        }
        return $this->build_response($arr_response['status'],$arr_response['msg'],$arr_response['data']);   
    }

    public function get_terms_and_conditions()
    {
        $arr_data = [];

        $obj_data = $this->StaticPageModel
                                    ->where('page_slug','terms-and-conditions')
                                    ->first();
        if($obj_data)
        {
            $arr_data = $obj_data->toArray();
        }
        if(isset($arr_data) && sizeof($arr_data)>0)
        {
            $arr_response['status']  = 'success';
            $arr_response['msg']     = 'About us page details found.';
            $arr_response['data']    = $arr_data;
        }
        else
        {
            $arr_response['status']  = 'error';
            $arr_response['msg']     = 'About us page details not found.';
            $arr_response['data']    = [];   
        }
        return $this->build_response($arr_response['status'],$arr_response['msg'],$arr_response['data']);   
    }

    public function policy()
    {
        $arr_data = [];

        /*$obj_data = $this->StaticPageModel
                                    ->where('page_slug','policy')
                                    ->first();
        if($obj_data)
        {
            $arr_data = $obj_data->toArray();
        }*/
        
        $arr_data['page_desc'] = url('/policy');
        if(isset($arr_data) && sizeof($arr_data)>0)
        {
            $arr_response['status']  = 'success';
            $arr_response['msg']     = 'About us page details found.';
            $arr_response['data']    = $arr_data;
        }
        else
        {
            $arr_response['status']  = 'error';
            $arr_response['msg']     = 'About us page details not found.';
            $arr_response['data']    = [];   
        }
        return $this->build_response($arr_response['status'],$arr_response['msg'],$arr_response['data']);   
    }
    public function check_trip(Request $request)
    {   
        $arr_response    = $this->CommonDataService->check_trip($request);         
        return response()->json($arr_response,200,[],JSON_UNESCAPED_UNICODE);    
    }

    public function get_current_trip_users(Request $request)
    {   
        $arr_response = [];
        $arr_response['status']  = 'error';
        $arr_response['msg']     = 'chat list not found';
        $arr_response['data']    = [];

        $arr_tmp_ids    = $this->CommonDataService->get_current_trip_users($request);         
        
        if(isset($arr_tmp_ids) && sizeof($arr_tmp_ids)>0)
        {
            $arr_response['status'] = 'success';
            $arr_response['msg']    = 'chat list found';
            $arr_response['data'] = $arr_tmp_ids;
        }
        return $this->build_response($arr_response['status'],$arr_response['msg'],$arr_response['data']); 
    }
    
    public function check_latest_accepted_load_post(Request $request)
    {   
        $arr_response = [];
        $arr_response['status']  = 'error';
        $arr_response['msg']     = 'load_post request id not found';
        $arr_response['data']    = [];

        $arr_data    = $this->CommonDataService->check_latest_accepted_load_post($request);         
        
        $load_post_request_id = isset($arr_data['load_post_request_id']) ? $arr_data['load_post_request_id'] : 0;
        $driver_id = isset($arr_data['driver_id']) ? $arr_data['driver_id'] : 0;
        
        if($load_post_request_id!=0 && $driver_id!=0)
        {
            $arr_response['status'] = 'success';
            $arr_response['msg']    = 'Your driver is not responding to your shipment request,sorry for the inconvenience, '.config('app.project.name').' Admin replaced the driver for you.';
            $arr_response['data']   = $arr_data;
        }
        return $this->build_response($arr_response['status'],$arr_response['msg'],$arr_response['data']); 
    }

    public function check_user_login_status(Request $request)
    {   
        $arr_response = [];
        $login_user_id    = validate_user_jwt_token();
        if ($login_user_id == 0) 
        {
            $arr_response['status'] = 'error';
            $arr_response['msg']    = 'Invalid user token';
            $arr_response['data']    = [];
            return $arr_response;        
        }
        
        $device_id     = $request->input('device_id');

        $is_user_login = '0';
        $reset_password_mandatory = '0';
        $msg = '-';

        $obj_data  = $this->UserModel
                                    ->select('id','is_user_login','device_id','reset_password_mandatory')
                                    ->where('id',$login_user_id)
                                    ->first();

        if($obj_data)
        {
            // $is_user_login            = isset($obj_data->is_user_login) ? strval($obj_data->is_user_login) :'0';
            $reset_password_mandatory = isset($obj_data->reset_password_mandatory) ? strval($obj_data->reset_password_mandatory) :'0';
            
            if($reset_password_mandatory == '1')
            {
                $msg = 'Your password was changed by QuickPick Admin, email is sent with the new password.';
            }
            $db_device_id = isset($obj_data->device_id) ? strval($obj_data->device_id) :'';

            if($db_device_id!='' && $device_id!='')
            {
                if($db_device_id!=$device_id)
                {
                    $is_user_login = '1';
                    $msg = 'You logged in from another device, your current session expired';
                }
            }

        }
        $arr_response['status'] = 'success';
        $arr_response['msg']    =  $msg;
        $arr_response['data']   = ['is_user_login'=>$is_user_login,'reset_password_mandatory'=>$reset_password_mandatory];
    
        return $this->build_response($arr_response['status'],$arr_response['msg'],$arr_response['data']); 
    }

    public function check_driver_latest_trip(Request $request)
    {   
        $arr_response = [];
        $arr_response['status']  = 'error';
        $arr_response['msg']     = 'details not found';
        $arr_response['data']    = [];

        $arr_booking_master    = $this->CommonDataService->check_driver_latest_trip($request);         
        
        if(isset($arr_booking_master) && sizeof($arr_booking_master)>0)
        {
            $arr_response['status'] = 'success';
            $arr_response['msg']    = 'details found.';
            $arr_response['data']   = $arr_booking_master;
        }
        return $this->build_response($arr_response['status'],$arr_response['msg'],$arr_response['data']); 
    }

    public function check_driver_stripe_account_id(Request $request)
    {   
        $arr_response = [];
        $login_user_id    = validate_user_jwt_token();
        if ($login_user_id == 0) 
        {
            $arr_response['status'] = 'error';
            $arr_response['msg']    = 'Invalid user token';
            $arr_response['data']    = [];
            return $arr_response;        
        }
        
        $arr_user_details = $this->CommonDataService->get_user_details($login_user_id);
        if(isset($arr_user_details['stripe_account_id']) && $arr_user_details['stripe_account_id']!='')
        {
            $arr_response = $this->StripeService->check_stripe_account_exist($arr_user_details['stripe_account_id']);
        }
        else
        {
            $arr_response['status']  = 'error';
            $arr_response['msg']     = 'stripe account id is empty';
            $arr_response['data']    = [];
        }
        return $this->build_response($arr_response['status'],$arr_response['msg'],$arr_response['data']); 
    }

    public function redirect_from_stripe(Request $request)
    {
        $arr_response = [];

        if($request->has('error') && $request->input('access_denied'))
        {
            $error_description = $request->get('error_description');
        
            $arr_response['status']  = 'error';
            $arr_response['msg']     = $error_description;
            $arr_response['data']    = [];
            return $arr_response;
        }
        $code  = $request->input('code');
        $scope = $request->input('scope');

        if($request->input('code') == '')
        {
            $arr_response['status']  = 'error';
            $arr_response['msg']     = 'Stripe code field is empty';
            $arr_response['data']    = [];
            return $arr_response;
        }

        if($request->input('scope') == '')
        {
            $arr_response['status']  = 'error';
            $arr_response['msg']     = 'Stripe scope field is empty';
            $arr_response['data']    = [];
            return $arr_response;
        }

        $user_id = $request->input('state');
        if($user_id!='')
        {
            $user_id = $this->CommonDataService->decrypt_value($user_id);
        }

        $arr_user_details = $this->CommonDataService->get_user_details($user_id);
        if(sizeof($arr_user_details) == 0)
        {
            $arr_response['status']  = 'error';
            $arr_response['msg']     = 'User details not found,Please try again.';
            $arr_response['data']    = [];
            return $arr_response;
        }

        $arr_token_request_body = [
                                        'grant_type'    => 'authorization_code',
                                        'client_id'     => $this->stripe_client_id,
                                        'code'          => $code,
                                        'client_secret' => $this->secret_key
                                  ];

        $arr_data = $this->check_stripe_user($arr_token_request_body);

        if(isset($arr_data['stripe_user_id']) && $arr_data['stripe_user_id'] != "")
        {
            $arr_update['stripe_account_id']       = isset($arr_data['stripe_user_id']) ? $arr_data['stripe_user_id'] : '';
            $arr_update['stripe_account_response'] = isset($arr_data) ? json_encode($arr_data) : '';

            $status = $this->update_stripe_user_details($arr_update,$user_id);
            if($status)
            {
                $arr_response['status']  = 'success';
                $arr_response['msg']     = 'User Stripe account details updated successfully';
                $arr_response['data']    = [];
                return $arr_response;
            }
            else
            {
                $arr_response['status']  = 'error';
                $arr_response['msg']     = 'Something went wrong,Please try again.';
                $arr_response['data']    = [];
                return $arr_response;
            }
        }
        elseif(isset($arr_data['error']) && $arr_data['error'] != "")
        {
            $arr_update['stripe_account_response'] = isset($arr_data) ? json_encode($arr_data) : '';

            $this->update_stripe_user_details($arr_update,$user_id);
            
            $error_description = isset($arr_data['error_description']) ? $arr_data['error_description'] : 'Something went wrong,Please try again.';
            
            $arr_response['status']  = 'error';
            $arr_response['msg']     = $error_description;
            $arr_response['data']    = [];
            return $arr_response;

        }
        else
        {
            $arr_update['stripe_account_response'] = isset($arr_data) ? json_encode($arr_data) : '';
            
            $this->update_stripe_user_details($arr_update,$user_id);
            
            $arr_response['status']  = 'error';
            $arr_response['msg']     = 'Something went wrong,Please try again.';
            $arr_response['data']    = [];
            return $arr_response;
        }   
        
        $arr_response['status']  = 'error';
        $arr_response['msg']     = 'Something went wrong,Please try again.';
        $arr_response['data']    = [];
        return $arr_response;
    }
    
    private function check_stripe_user($arr_token_request_body)
    {
        $req = curl_init($this->stripe_token_url);
        curl_setopt($req, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($req, CURLOPT_POST, true );
        curl_setopt($req, CURLOPT_POSTFIELDS, http_build_query($arr_token_request_body));
        $respCode = curl_getinfo($req, CURLINFO_HTTP_CODE);
        $resp = curl_exec($req);
        curl_close($req);

        $arr_result = json_decode($resp, true);
        return $arr_result;
    }
    
    private function update_stripe_user_details($arr_update,$user_id)
    {
        $status = $this->UserModel
                            ->where('id',$user_id)
                            ->update($arr_update);
        if($status){
            return true;
        }
        return false;
    }

    public function get_chat_list(Request $request)
    {
        $user_type = $request->input('user_type');
        $arr_jwt_user_details    = get_jwt_user_details();
        
        // dd($arr_jwt_user_details,$user_type);


        $from_user_id      = isset($arr_jwt_user_details['id']) ? $arr_jwt_user_details['id']  :0;
        $is_company_driver = isset($arr_jwt_user_details['is_company_driver']) ? $arr_jwt_user_details['is_company_driver']  :0;
        $company_id        = isset($arr_jwt_user_details['company_id']) ? $arr_jwt_user_details['company_id']  :0;
        

        $arr_chat_list = [];

        /*make admin details chat list array*/
        $arr_tmp_admin_details = get_admin_details();
        $admin_id              = isset($arr_tmp_admin_details['id']) ? $arr_tmp_admin_details['id'] : 0;
        
        $arr_admin_details['id']        = $admin_id;
        $first_name                     = isset($arr_tmp_admin_details['first_name']) ? $arr_tmp_admin_details['first_name'] : '';
        $last_name                      = isset($arr_tmp_admin_details['last_name']) ? $arr_tmp_admin_details['last_name'] : '';
        $full_name                      = $first_name.' '.$last_name;
        $arr_admin_details['full_name'] = $full_name;
        $admin_profile_image            = '';
        if((isset($arr_tmp_admin_details['profile_image']) && $arr_tmp_admin_details['profile_image']!='') && file_exists($this->user_profile_base_img_path.$arr_tmp_admin_details['profile_image']))
        {
            $admin_profile_image = $this->user_profile_public_img_path.$arr_tmp_admin_details['profile_image'];
        }
        $arr_admin_details['profile_image'] = $admin_profile_image;
        $arr_admin_details['is_chat_enable'] = 'YES';

        $arr_admin_last_message = $this->get_last_message_details($from_user_id,$admin_id);

        $arr_admin_details['message'] = isset($arr_admin_last_message['message']) ? $arr_admin_last_message['message'] : '';
        $arr_admin_details['date']    = isset($arr_admin_last_message['date']) ? $arr_admin_last_message['date'] : '';

        $arr_admin_details['unread_msg_count'] = $this->get_unread_message_count($admin_id,$from_user_id);

        array_push($arr_chat_list, $arr_admin_details);

        /*make company details chat list array*/

        if($is_company_driver == '1' && $company_id!=0){
            $arr_tmp_company                  = company_profile($company_id);
            $arr_company_details['id']        = isset($arr_tmp_company['id']) ? $arr_tmp_company['id'] : 0;
            $company_name                     = isset($arr_tmp_company['company_name']) ? $arr_tmp_company['company_name'] : '';
            $arr_company_details['full_name'] = $company_name;

            $company_profile_image            = '';
            if((isset($arr_tmp_company['profile_image']) && $arr_tmp_company['profile_image']!='') && file_exists($this->user_profile_base_img_path.$arr_tmp_company['profile_image']))
            {
                $company_profile_image = $this->user_profile_public_img_path.$arr_tmp_company['profile_image'];
            }
            $arr_company_details['profile_image'] = $company_profile_image;
            $arr_company_details['is_chat_enable'] = 'YES';

            $arr_company_last_message       = $this->get_last_message_details($from_user_id,$company_id);
            $arr_company_details['message'] = isset($arr_company_last_message['message']) ? $arr_company_last_message['message'] : '';
            $arr_company_details['date']    = isset($arr_company_last_message['date']) ? $arr_company_last_message['date'] : '';
            $arr_company_details['unread_msg_count'] = $this->get_unread_message_count($company_id,$from_user_id);
            
            array_push($arr_chat_list, $arr_company_details);
        }
        
        // dd($from_user_id);

        $obj_booking = $this->BookingMasterModel::select('id','load_post_request_id','booking_status','updated_at')
                                ->whereHas('load_post_request_details',function($query)use($from_user_id,$user_type){
                                    if($user_type == 'USER')
                                    {
                                        $query->where('user_id',$from_user_id);
                                    }
                                    if($user_type == 'DRIVER')
                                    {
                                        $query->where('driver_id',$from_user_id);
                                    }
                                })
                                ->with(['load_post_request_details'=>function($query)use($from_user_id,$user_type){
                                    $query->select('id','user_id','driver_id');
                                    if($user_type == 'USER')
                                    {
                                        $query->where('user_id',$from_user_id);
                                        $query->with(['driver_details'=>function($query) use($from_user_id){
                                            $query->select('id','first_name','last_name','profile_image');
                                            $query->with(['unread_message_count'=>function($query)use($from_user_id){
                                                $query->where('to_user_id',$from_user_id);
                                            }]);
                                        }]);
                                    }
                                    if($user_type == 'DRIVER')
                                    {
                                        $query->where('driver_id',$from_user_id);
                                        $query->with(['user_details'=>function($query)use($from_user_id){
                                            $query->select('id','first_name','last_name','profile_image');
                                            $query->with(['unread_message_count'=>function($query)use($from_user_id){
                                                $query->where('to_user_id',$from_user_id);
                                            }]);
                                        }]);
                                    }
                                }])
                                ->orderBy('updated_at','DESC')
                                ->get();
        
        if(isset($obj_booking) && count($obj_booking)>0)
        {
            foreach ($obj_booking as $key => $value) 
            {
                if($user_type == 'USER' && (isset($value->load_post_request_details->driver_details) && count($value->load_post_request_details->driver_details)>0))
                {
                    $arr_tmp_driver = [];

                    $first_name         = isset($value->load_post_request_details->driver_details->first_name) ? $value->load_post_request_details->driver_details->first_name : '';
                    $last_name          = isset($value->load_post_request_details->driver_details->last_name) ? $value->load_post_request_details->driver_details->last_name : '';
                    $full_name          = $first_name.' '.$last_name;

                    $arr_tmp_driver['id']        = isset($value->load_post_request_details->driver_details->id) ? $value->load_post_request_details->driver_details->id : 0;
                    $arr_tmp_driver['full_name'] = ($full_name!=' ') ? $full_name : '-';

                    $user_profile_image = '';
                    $is_chat_enable     = 'NO';

                    if(isset($value->booking_status) && ($value->booking_status == 'TO_BE_PICKED' || $value->booking_status == 'IN_TRANSIT'))
                    {
                        $is_chat_enable = 'YES';
                    }
                    if((isset($value->load_post_request_details->driver_details->profile_image) && $value->load_post_request_details->driver_details->profile_image!='') && file_exists($this->user_profile_base_img_path.$value->load_post_request_details->driver_details->profile_image))
                    {
                        $user_profile_image = $this->user_profile_public_img_path.$value->load_post_request_details->driver_details->profile_image;
                    }
                    $arr_tmp_driver['profile_image'] = $user_profile_image;
                    $arr_tmp_driver['is_chat_enable'] = $is_chat_enable;

                    $driver_id = isset($value->load_post_request_details->driver_details->id) ? $value->load_post_request_details->driver_details->id : 0;
                    $arr_last_message       = $this->get_last_message_details($from_user_id,$driver_id);
                    $arr_tmp_driver['message'] = isset($arr_last_message['message']) ? $arr_last_message['message'] : '';
                    $arr_tmp_driver['date']    = isset($arr_last_message['date']) ? $arr_last_message['date'] : '';

                    $arr_tmp_driver['unread_msg_count']   = isset($value->load_post_request_details->driver_details->unread_message_count->message_count) ?  intval($value->load_post_request_details->driver_details->unread_message_count->message_count) : 0;
                   
                    array_push($arr_chat_list, $arr_tmp_driver);
                }
                if($user_type == 'DRIVER' && (isset($value->load_post_request_details->user_details) && count($value->load_post_request_details->user_details)>0))
                {
                    $arr_tmp_user = [];

                    $first_name         = isset($value->load_post_request_details->user_details->first_name) ? $value->load_post_request_details->user_details->first_name : '';
                    $last_name          = isset($value->load_post_request_details->user_details->last_name) ? $value->load_post_request_details->user_details->last_name : '';
                    $full_name          = $first_name.' '.$last_name;

                    $arr_tmp_user['id']        = isset($value->load_post_request_details->user_details->id) ? $value->load_post_request_details->user_details->id : 0;
                    $arr_tmp_user['full_name'] = ($full_name!=' ') ? $full_name : '-';

                    $user_profile_image = '';
                    $is_chat_enable     = 'NO';

                    if(isset($value->booking_status) && ($value->booking_status == 'TO_BE_PICKED' || $value->booking_status == 'IN_TRANSIT'))
                    {
                        $is_chat_enable = 'YES';
                    }
                    if((isset($value->load_post_request_details->user_details->profile_image) && $value->load_post_request_details->user_details->profile_image!='') && file_exists($this->user_profile_base_img_path.$value->load_post_request_details->user_details->profile_image))
                    {
                        $user_profile_image = $this->user_profile_public_img_path.$value->load_post_request_details->user_details->profile_image;
                    }
                    
                    $arr_tmp_user['profile_image']      = $user_profile_image;
                    $arr_tmp_user['is_chat_enable']     = $is_chat_enable;
                    
                    $user_id = isset($value->load_post_request_details->user_details->id) ? $value->load_post_request_details->user_details->id : 0;
                    $arr_last_message       = $this->get_last_message_details($from_user_id,$user_id);

                    $arr_tmp_user['message']          = isset($arr_last_message['message']) ? $arr_last_message['message'] : '';
                    $arr_tmp_user['date']             = isset($arr_last_message['date']) ? $arr_last_message['date'] : '';
                    $arr_tmp_user['unread_msg_count'] = isset($value->load_post_request_details->user_details->unread_message_count->message_count) ?  intval($value->load_post_request_details->user_details->unread_message_count->message_count) : 0;
                    
                    array_push($arr_chat_list, $arr_tmp_user);
                }
            }
        }
        if(isset($arr_chat_list) && count($arr_chat_list)>0){

            usort($arr_chat_list, function($a, $b){
                $t1 = $a['is_chat_enable'];
                $t2 = $b['is_chat_enable'];
                if ($t1 == $t2) return 0;
                return ($t1 > $t2) ? -1 : 1;
            });
        }
        $arr_chat_list = $this->get_unique_array_by_key($arr_chat_list,'id');

        if(isset($arr_chat_list) && sizeof($arr_chat_list)>0)
        {
            $obj_list = $this->make_pagination_links($arr_chat_list,$this->per_page);
            $arr_list = $obj_list->toArray();
            if(isset($arr_list['data']) && sizeof($arr_list['data'])>0)
            {
                $arr_list['data'] = array_values($arr_list['data']);
                $arr_response['status'] = 'success';
                $arr_response['msg']    = 'chat list available';
                $arr_response['data']   = $arr_list;
                return $this->build_response($arr_response['status'],$arr_response['msg'],$arr_response['data']); 
            }
            else
            {
                $arr_response['status'] = 'error';
                $arr_response['msg'] = 'chat list not available';
                return $this->build_response($arr_response['status'],$arr_response['msg']); 
            }

        }
        
        $arr_response['status'] = 'error';
        $arr_response['msg'] = 'chat list not available';
        return $this->build_response($arr_response['status'],$arr_response['msg']); 
    }

    private function get_last_message_details($from_user_id,$to_user_id)
    {
        $arr_last_message_details = 
                                    [
                                        'message' => '',
                                        'date' => ''
                                    ];

        if($from_user_id!='' && $to_user_id!='')
        {
            $select_query = "SELECT id,from_user_id as from_user_id,to_user_id as to_user_id,message,DATE_FORMAT(created_at,'%d %b, %h:%i %p') as date  FROM message WHERE 
                                        (from_user_id = ".$from_user_id." AND to_user_id = ".$to_user_id." )
                                        OR
                                        (from_user_id = ".$to_user_id." AND to_user_id = ".$from_user_id." )  ORDER BY id DESC LIMIT 1";   

            $obj_chat_details =  \DB::select($select_query);
            
            if($select_query!=''){
                if(isset($obj_chat_details) && sizeof($obj_chat_details)>0){
                    $arr_data = json_decode(json_encode($obj_chat_details), true);
                    $arr_last_message_details['message'] = isset($arr_data[0]['message']) ? $arr_data[0]['message'] : '';
                    $arr_last_message_details['date'] = isset($arr_data[0]['date']) ? $arr_data[0]['date'] : '';

                }
            }
        }
        return $arr_last_message_details;
    }
    
    private function get_unread_message_count($from_user_id,$to_user_id)
    {
        $obj_message_count = $this->MessagesModel
                                        ->where('from_user_id',$from_user_id)
                                        ->where('to_user_id',$to_user_id)
                                        ->where('is_read','0')
                                        ->count();
        
        return intval($obj_message_count);

    }

    private function get_unique_array_by_key($array,$keyname){
        $new_array = array();
        foreach($array as $key=>$value){
            if(!isset($new_array[$value[$keyname]])){
                $new_array[$value[$keyname]] = $value;
            }
        }
        $new_array = array_values($new_array);
        return $new_array;
    }
    public function make_pagination_links($items,$perPage)
    {
        $pageStart = 1;
        if(\Request::has('page') && \Request::input('page')!=''){
            $pageStart = \Request::get('page');
        }
        // Start displaying items from this number;
        $offSet = ($pageStart * $perPage) - $perPage; 

        // Get only the items you need using array_slice
        $itemsForCurrentPage = array_slice($items, $offSet, $perPage, true);

        return new LengthAwarePaginator($itemsForCurrentPage, count($items), $perPage,Paginator::resolveCurrentPage(), array('path' => Paginator::resolveCurrentPath()));
    }  


    public function get_chat_details(Request $request)
    {
        $from_user_id = $request->input('from_user_id');
        $to_user_id   = $request->input('to_user_id');
        
        // dd($from_user_id,$to_user_id);
        if($from_user_id!='' && $to_user_id!='')
        {
            $this->read_unread_message($to_user_id,$from_user_id);

            $select_query = '';
            $select_query = "SELECT id,request_id,from_user_id as from_user_id,to_user_id as to_user_id,message,DATE_FORMAT(created_at,'%d %b, %h:%i %p') as date  FROM message WHERE 
                                        (from_user_id = ".$from_user_id." AND to_user_id = ".$to_user_id." )
                                        OR
                                        (from_user_id = ".$to_user_id." AND to_user_id = ".$from_user_id." )  ORDER BY id ASC";   

            $arr_chat_details = [];
            if($select_query!='')
            {
                $obj_chat_details =  \DB::select($select_query);

                if(isset($obj_chat_details) && sizeof($obj_chat_details)>0){
                    $arr_chat_details = json_decode(json_encode($obj_chat_details), true);
                }
            }
        }
        
        if(isset($arr_chat_details) && sizeof($arr_chat_details)>0)
        {
            $arr_response['status'] = 'success';
            $arr_response['msg']    = 'chat details available';
            $arr_response['data']   = $arr_chat_details;
            return $this->build_response($arr_response['status'],$arr_response['msg'],$arr_response['data']); 
        }
        
        $arr_response['status'] = 'error';
        $arr_response['msg'] = 'chat details not available';
        return $this->build_response($arr_response['status'],$arr_response['msg']); 
    }    
    
    private function read_unread_message($from_user_id = false,$to_user_id= false)
    {
        if($from_user_id!=false && $to_user_id!=false)
        {
            return  $this->MessagesModel
                            ->where('from_user_id',$from_user_id)
                            ->where('to_user_id',$to_user_id)
                            // ->get();
                            ->update(['is_read'=>'1']);

        }
        return true;
    }

    public function store_message(Request $request)
    {
        $arr_rules                     = [];
        $arr_rules['request_id']       = "required";
        $arr_rules['from_user_id']     = "required";
        $arr_rules['to_user_id']       = "required";
        $arr_rules['message']          = "required";
        $arr_rules['user_type']        = "required";

        $validator = \Validator::make($request->all(),$arr_rules);
       
        if($validator->fails())
        {
          $arr_response = array('status'=>'error','msg'=>'Please fill all the required field.');
          return $this->build_response($arr_response['status'],$arr_response['msg']);
        }

        $to_user_type = '';

        $user_type = $request->input('user_type');

        if($user_type == 'DRIVER'){
            $to_user_type = 'USER';
        }
        else if($user_type == 'USER'){
            $to_user_type = 'DRIVER';
        }
        $arr_create = [
                        'request_id'   => $request->input('request_id'),
                        'from_user_id' => $request->input('from_user_id'),
                        'to_user_id'   => $request->input('to_user_id'),
                        'message'      => trim($request->input('message')),
                        'is_read'      => 0,
                      ];

        $from_user_id = $request->input('from_user_id');
        $to_user_id   = $request->input('to_user_id');

        $obj_data = $this->UserModel->select('id','first_name','last_name')->where('id',$from_user_id)->first();

        $first_name         = isset($obj_data->first_name) ? $obj_data->first_name : '';
        $last_name          = isset($obj_data->last_name) ? $obj_data->last_name : '';
        $full_name          = $first_name.' '.$last_name;
        $full_name          = ($full_name!=' ') ? $full_name : '';

        $success = $this->MessagesModel->create($arr_create);

        $message = trim($request->input('message'));
        
        $title = '';

        if($user_type == 'USER')
        {
            $title = 'New message from Customer ('.$full_name.') : '."\n".$message;
        }
        else
        {
            $title = 'New message from Driver ('.$full_name.') : '."\n".$message;
        }
        
        $arr_notification_data = 
                                        [
                                            'title'             => $title,
                                            'notification_type' => 'DATABASE_MESSAGE',
                                            'enc_user_id'       => $to_user_id,
                                            'user_type'         => $to_user_type
                                        ];

        $this->NotificationsService->send_on_signal_notification($arr_notification_data);
        
        $arr_response['status'] = 'success';
        $arr_response['msg']    = 'message send successfully.';
        return $this->build_response($arr_response['status'],$arr_response['msg']);
    }
    
}   