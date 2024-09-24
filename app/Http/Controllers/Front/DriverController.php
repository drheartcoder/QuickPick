<?php

namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Common\Services\Web\AuthService;
use App\Common\Services\Web\DriverService;
use App\Common\Services\CommonDataService;


use App\Models\UserModel;
use App\Models\VehicleModelModel;
use App\Models\VehicleBrandModel;
use App\Models\VehicleTypeModel;
use App\Models\DriverCarRelationModel;

use Flash;
use Twilio\Rest\Client;

class DriverController extends Controller
{
    public function __construct(
                                    AuthService   $auth_service,
                                    DriverService $driver_service,
                                    CommonDataService $common_data_service,
                                    UserModel $user,
                                    VehicleModelModel $vehicle_model,
                                    VehicleBrandModel $vehicle_brand,
                                    VehicleTypeModel $vehicle_type,
                                    DriverCarRelationModel $driver_car_relation
                                )
    {
        $this->arr_view_data      = [];
        $this->module_title       = "Driver";
        $this->module_view_folder = "front.driver.";

        $this->AuthService            = $auth_service;
        $this->DriverService          = $driver_service;
        $this->UserModel              = $user;
        $this->CommonDataService      = $common_data_service;
        $this->VehicleModelModel      = $vehicle_model;
        $this->VehicleBrandModel      = $vehicle_brand;
        $this->VehicleTypeModel       = $vehicle_type;
        $this->DriverCarRelationModel = $driver_car_relation;

        $this->user_profile_public_img_path = url('/').config('app.project.img_path.user_profile_images');
        $this->user_profile_base_img_path   = base_path().config('app.project.img_path.user_profile_images');
        
        $this->driving_license_public_path = url('/').config('app.project.img_path.driving_license');
        $this->driving_license_base_path   = base_path().config('app.project.img_path.driving_license');

        $this->vehicle_doc_public_path = url('/').config('app.project.img_path.vehicle_doc');
        $this->vehicle_doc_base_path   = base_path().config('app.project.img_path.vehicle_doc');

        $this->receipt_image_public_path = url('/').config('app.project.img_path.payment_receipt');
        $this->receipt_image_base_path   = base_path().config('app.project.img_path.payment_receipt');

        $this->module_url_path               = url(config('app.project.role_slug.driver_role_slug')."/dashboard");

        $this->stripe_client_id    = config('services.stripe_client_id');
        $this->stripe_authorize_url = config('services.stripe_authorize_url');
        $this->stripe_token_url = config('services.stripe_token_url');
        
        $this->invoice_public_img_path = url('/').config('app.project.img_path.invoice');
        $this->invoice_base_img_path   = base_path().config('app.project.img_path.invoice');
        
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
    }
    
    public function index()
    {	
        $this->arr_view_data['module_url_path']     = $this->module_url_path;
    	$this->arr_view_data['page_title']     = "Driver Dashboard";
        return view($this->module_view_folder.'dashboard',$this->arr_view_data);
    }

    public function my_profile_view()
    {   
        $arr_response = $this->AuthService->get_profile();
        
        $arr_data =  [];

        if(isset($arr_response['status']) && $arr_response['status'] == 'success')
        {
            $arr_data = isset($arr_response['data']) ? $arr_response['data'] : [];
        }
        else
        {
            $msg = isset($arr_response['msg']) ? $arr_response['msg'] : '';
            Flash::error($msg);
            return redirect(url('/driver/dashboard'));
        }

        $this->arr_view_data['module_url_path']     = $this->module_url_path;

        $this->arr_view_data['page_title']     = "View Profile";
        $this->arr_view_data['arr_data']     = $arr_data;
        
        $this->arr_view_data['user_profile_public_img_path'] = $this->user_profile_public_img_path;
        $this->arr_view_data['user_profile_base_img_path']   = $this->user_profile_base_img_path;

        $this->arr_view_data['driving_license_public_path'] = $this->driving_license_public_path;
        $this->arr_view_data['driving_license_base_path']   = $this->driving_license_base_path;

        return view($this->module_view_folder.'my_profile_view',$this->arr_view_data);
    }

    public function my_profile_edit()
    {   
        $arr_response = $arr_response['data'] = [];
        $arr_response = $this->AuthService->get_profile();

        if(isset($arr_response))
        {
            if($arr_response['status']=='success')
            {
                $this->arr_view_data['arr_data']     = $arr_response['data'];
            }
        }   
        $this->arr_view_data['arr_data']     = $arr_response['data'];
        $this->arr_view_data['module_url_path']     = $this->module_url_path;
        $this->arr_view_data['user_profile_public_img_path']     = $this->user_profile_public_img_path;
        $this->arr_view_data['user_profile_base_img_path']       = $this->user_profile_base_img_path;

        $this->arr_view_data['driving_license_public_path']     = $this->driving_license_public_path;
        $this->arr_view_data['driving_license_base_path']       = $this->driving_license_base_path;
        $this->arr_view_data['page_title']            = "My Profile Edit";
        return view($this->module_view_folder.'my_profile_edit',$this->arr_view_data);
    }

    public function update_profile(Request $request)
    {   
        $arr_response = [];
        $arr_response = $this->AuthService->update_profile($request);

        if(isset($arr_response['status']) && $arr_response['status'] == 'success')
        {
            $msg = isset($arr_response['msg']) ? $arr_response['msg'] : '';

            Flash::success($msg);
            return redirect(url('driver/my_profile_edit'));

        }
        elseif (isset($arr_response['status']) && $arr_response['status'] == 'error') 
        {
            $msg = isset($arr_response['msg']) ? $arr_response['msg'] : '';
            Flash::error($msg);
            return redirect()->back();          
        }
        Flash::error('Something went wrong,Please try again.');
        return redirect()->back();          
    }
    
    public function verify_mobile_number(Request $request)
    {
        $arr_response = $this->AuthService->verify_mobile_number($request);
        return response()->json($arr_response);    
    }

    public function update_mobile_no(Request $request)
    {
        $arr_response = $this->AuthService->update_mobile_no($request);
        return response()->json($arr_response);    
    }

    public function vehicle()
    {   
        $arr_response = $arr_response['data'] = [];
        $arr_response = $this->DriverService->get_vehicle_details();
      // dd($arr_response);
        if(isset($arr_response['status']) && $arr_response['status'] == 'success')
        {
            $this->arr_view_data['arr_data']     = $arr_response['data'];
        }   
        else{
            $msg = isset($arr_response['msg']) ? $arr_response['msg'] : '';
            // Flash::error($msg);
            $this->arr_view_data['msg']     = $msg;
        }

        $this->arr_view_data['arr_data']     = $arr_response['data'];

        $this->arr_view_data['vehicle_doc_public_path']     = $this->vehicle_doc_public_path;
        $this->arr_view_data['vehicle_doc_base_path']       = $this->vehicle_doc_base_path;

        $this->arr_view_data['module_url_path']     = $this->module_url_path;
        $this->arr_view_data['page_title']     = "Manage Vehicle";
        return view($this->module_view_folder.'vehicle',$this->arr_view_data);
    }

    public function vehicle_edit()
    {   
        $driver_id    = validate_user_login_id();

        if($driver_id!='')
        {
            $obj_edit_data = $arr_edit_data = [];

            // $obj_edit_data = $this->DriverCarRelationModel->where('driver_id','=',$driver_id)->first();

            // if(isset($obj_edit_data) && sizeof($obj_edit_data))
            // {
            //     $arr_edit_data = $obj_edit_data->toarray();
            //     if($arr_edit_data['is_individual_vehicle']=='0')
            //     {
            //         Flash::error('Something went wrong,Please try again.');
            //         return redirect()->back();          
            //     }
            // }

            $arr_response = $obj_brand = $arr_brand = $obj_type = $arr_type = $arr_response['data'] = [];

            $obj_brand = $this->VehicleBrandModel->select('id','name')->get();
            if(isset($obj_brand) && sizeof($obj_brand))
            {
                $arr_brand = $obj_brand->toarray();
            }

            $obj_type = $this->VehicleTypeModel->select('id','vehicle_type','is_usdot_required','is_mcdoc_required')->get();
            if(isset($obj_type) && sizeof($obj_type))
            {
                $arr_type = $obj_type->toarray();
            }

            $arr_response = $this->DriverService->get_vehicle_details();

            if(isset($arr_response))
            {
                if($arr_response['status']=='success')
                {
                    $this->arr_view_data['arr_data']     = $arr_response['data'];
                }
            }   

            $this->arr_view_data['arr_data']     = $arr_response['data'];
            $this->arr_view_data['module_url_path']     = $this->module_url_path;
            $this->arr_view_data['user_profile_public_img_path']     = $this->user_profile_public_img_path;
            $this->arr_view_data['user_profile_base_img_path']       = $this->user_profile_base_img_path;

            $this->arr_view_data['driving_license_public_path']     = $this->driving_license_public_path;
            $this->arr_view_data['driving_license_base_path']       = $this->driving_license_base_path; 

            $this->arr_view_data['arr_type']     = $arr_type;
            $this->arr_view_data['arr_brand']     = $arr_brand;

           $this->arr_view_data['page_title']            = "Vehicle Edit";
            return view($this->module_view_folder.'vehicle_edit',$this->arr_view_data);
        }
        else
        {
            Flash::error('Something went wrong,Please try again.');
            return redirect()->back();          

        }
    }

    public function vehicle_update(Request $request)
    {   
        $arr_response = [];

        $arr_response = $this->DriverService->update_vehicle_details($request);

        if(isset($arr_response['status']) && $arr_response['status'] == 'success')
        {
            $msg = isset($arr_response['msg']) ? $arr_response['msg'] : '';

            Flash::success($msg);
            if(isset($arr_response['data']['is_make_all_document_clear']) && $arr_response['data']['is_make_all_document_clear'] == 'YES')
            {
                \Sentinel::logout();
                return redirect(url('/login'));
            }
            return redirect(url('driver/vehicle'));

        }
        elseif (isset($arr_response['status']) && $arr_response['status'] == 'error') 
        {
            $msg = isset($arr_response['msg']) ? $arr_response['msg'] : '';
            Flash::error($msg);
            return redirect()->back();          
        }
        Flash::error('Something went wrong,Please try again.');
        return redirect()->back();          
    }

    public function my_job(Request $request)
    {   
        $arr_data = $arr_pagination = $arr_response['data'] = [];

        $trip_type = 'COMPLETED';

        if($request->has('trip_type') && $request->input('trip_type')!='')
        {
            $trip_type = $request->input('trip_type');
        }

        $arr_response = $this->DriverService->get_filter_trips($request,$trip_type);
        if(isset($arr_response['status']) && $arr_response['status'] == 'success')
        {
            if(isset($arr_response['data']))
            {
                $arr_data = $arr_response['data'];
            }
            if(isset($arr_response['data']['arr_pagination']))
            {
                $arr_pagination = $arr_response['data']['arr_pagination'];
            }
        }
        $this->arr_view_data['module_url_path']     = $this->module_url_path;
        $this->arr_view_data['page_title']     = "My Jobs";
        $this->arr_view_data['arr_data']       = $arr_data;
        $this->arr_view_data['arr_pagination'] = $arr_pagination;
        $this->arr_view_data['trip_type']      = $trip_type;

        if($trip_type == 'PENDING'){
            return view($this->module_view_folder.'pending_jobs',$this->arr_view_data);
        }
        return view($this->module_view_folder.'my_job',$this->arr_view_data);
    }

    public function request_list(Request $request)
    {   
        $arr_data = $arr_pagination = $arr_response['data'] = [];

        $trip_type = 'PENDING';

        $arr_response = $this->DriverService->get_filter_trips($request,$trip_type);
        if(isset($arr_response['status']) && $arr_response['status'] == 'success')
        {
            if(isset($arr_response['data']))
            {
                $arr_data = $arr_response['data'];
            }
            if(isset($arr_response['data']['arr_pagination']))
            {
                $arr_pagination = $arr_response['data']['arr_pagination'];
            }
        }
        $this->arr_view_data['module_url_path']     = $this->module_url_path;
        $this->arr_view_data['page_title']     = "Request List";
        $this->arr_view_data['arr_data']       = $arr_data;
        $this->arr_view_data['arr_pagination'] = $arr_pagination;
        $this->arr_view_data['trip_type']      = $trip_type;

        return view($this->module_view_folder.'pending_jobs',$this->arr_view_data);
        
    }
    public function job_details(Request $request)
    {   
        $arr_trip_details = [];

        $arr_response = $this->DriverService->trip_details($request);
        
        if(isset($arr_response['status']) && $arr_response['status'] == 'success')
        {
            if(isset($arr_response['data']) && count($arr_response['data'])>0)
            {
                $arr_trip_details = $arr_response['data'];
            }
        }
        $this->arr_view_data['module_url_path']  = $this->module_url_path;
        $this->arr_view_data['page_title']       = "My Job Details";
        $this->arr_view_data['arr_trip_details'] = $arr_trip_details;
        
        return view($this->module_view_folder.'job_details',$this->arr_view_data);
    }
    
    public function load_post_details(Request $request)
    {   
        $arr_load_post_details = [];

        $active_tab = 'my_job';
        if($request->has('active_tab') && $request->input('active_tab')!=''){
            $active_tab = $request->input('active_tab');
        }
        $arr_response = $this->DriverService->load_post_details($request);

        if(isset($arr_response['status']) && $arr_response['status'] == 'success')
        {
            if(isset($arr_response['data']) && count($arr_response['data'])>0)
            {
                $arr_load_post_details = $arr_response['data'];
            }
        }
        $this->arr_view_data['module_url_path']  = $this->module_url_path;
        $this->arr_view_data['page_title']       = "Request Details";
        $this->arr_view_data['arr_load_post_details'] = $arr_load_post_details;
        $this->arr_view_data['active_tab'] = $active_tab;
        
        return view($this->module_view_folder.'load_post_details',$this->arr_view_data);
    }
    
    public function accept_pending_load_post(Request $request,Client $client)
    {
        $arr_response = $this->DriverService->accept_pending_load_post($request,$client);
        if(isset($arr_response['status']) && $arr_response['status'] == 'success')
        {
            $msg = isset($arr_response['msg']) ? $arr_response['msg'] : '';
            Flash::success($msg);
            $return_url = url('driver/request_list');
            return redirect($return_url);
        }
        elseif(isset($arr_response['status']) && $arr_response['status'] == 'error')
        {
            $msg = isset($arr_response['msg']) ? $arr_response['msg'] : '';
            Flash::error($msg);
            return redirect()->back();          
        }
        Flash::error('Something went wrong,Please try again.');
        return redirect()->back();          
    }

    public function cancel_pending_load_post(Request $request)
    {
        $arr_response = $this->DriverService->cancel_pending_load_post($request);
        if(isset($arr_response['status']) && $arr_response['status'] == 'success')
        {
            $msg = isset($arr_response['msg']) ? $arr_response['msg'] : '';
            Flash::success($msg);
            $return_url = url('driver/request_list');
            return redirect($return_url);
        }
        elseif(isset($arr_response['status']) && $arr_response['status'] == 'error')
        {
            $msg = isset($arr_response['msg']) ? $arr_response['msg'] : '';
            Flash::error($msg);
            return redirect()->back();          
        }
        Flash::error('Something went wrong,Please try again.');
        return redirect()->back();          
    }

    public function track_trip(Request $request)
    {
        $arr_trip_details = [];

        $arr_response = $this->DriverService->trip_details($request,'live_trip');
        
        if(isset($arr_response['status']) && $arr_response['status'] == 'success')
        {
            if(isset($arr_response['data']) && count($arr_response['data'])>0)
            {
                $arr_trip_details = $arr_response['data'];
            }
        }

        $this->arr_view_data['page_title']       = "Track Trip";
        $this->arr_view_data['module_url_path']  = $this->module_url_path;
        $this->arr_view_data['arr_trip_details'] = $arr_trip_details;

        return view($this->module_view_folder.'track_trip',$this->arr_view_data);
    }

    public function process_cancel_trip(Request $request,Client $client)
    {
        $arr_response = $this->DriverService->process_cancel_trip_status_by_driver($request,$client);
        return $this->build_response($arr_response['status'],$arr_response['msg'],$arr_response['data']);   
    }
    
    public function track_live_trip(Request $request)
    {
        $arr_trip_details = [];
        $arr_response = $this->DriverService->track_live_trip($request);
        return $this->build_response($arr_response['status'],$arr_response['msg'],$arr_response['data']);   
    }

    public function my_earning(Request $request)
    {   
        $arr_earning_data = $arr_data = $arr_pagination = $arr_response['data'] = [];

        $arr_response = $this->DriverService->get_driver_deposit_money($request);
        
        if(isset($arr_response['status']) && $arr_response['status'] == 'success')
        {
            if(isset($arr_response['data']))
            {
                $arr_earning_data = $arr_response['data'];
                
                if(isset($arr_earning_data['list_status']))
                {
                    unset($arr_earning_data['list_status']);
                }

                if(isset($arr_earning_data['arr_receipt_list']))
                {
                    unset($arr_earning_data['arr_receipt_list']);
                }

            }

            if(isset($arr_response['data']['list_status']) && $arr_response['data']['list_status'] == 'success')
            {
                $arr_data = isset($arr_response['data']['arr_receipt_list']) ? $arr_response['data']['arr_receipt_list'] : [];
                $arr_pagination = isset($arr_response['data']['arr_receipt_list']['arr_pagination']) ? $arr_response['data']['arr_receipt_list']['arr_pagination'] : '';
            }
        }

        $this->arr_view_data['module_url_path']     = $this->module_url_path;
        $this->arr_view_data['page_title']       = "My Earning";
        $this->arr_view_data['arr_earning_data'] = $arr_earning_data;
        $this->arr_view_data['arr_data']         = $arr_data;
        $this->arr_view_data['arr_pagination']   = $arr_pagination;

        return view($this->module_view_folder.'my_earning',$this->arr_view_data);
    }

    public function change_password()
    {   
        $this->arr_view_data['module_url_path']     = $this->module_url_path;
        $this->arr_view_data['page_title']     = "Change Password";
        return view($this->module_view_folder.'change_password',$this->arr_view_data);
    }

    public function update_password(Request $request)
    {   
        $arr_response = $this->AuthService->change_password($request);
        
        if(isset($arr_response['status']) && $arr_response['status'] == 'success')
        {
            $msg = isset($arr_response['msg']) ? $arr_response['msg'] : '';

            Flash::success($msg);
            return redirect(url('driver/change_password'));

        }
        elseif (isset($arr_response['status']) && $arr_response['status'] == 'error') 
        {
            $msg = isset($arr_response['msg']) ? $arr_response['msg'] : '';
            Flash::error($msg);
            return redirect()->back();          
        }
        Flash::error('Something went wrong,Please try again.');
        return redirect()->back();          
    }

    public function notification(Request $request)
    {   
        $user_type = 'DRIVER';

        $arr_pagination = $arr_data = $arr_response['data'] = [];
        
        $arr_response = $this->AuthService->get_notification($request,$user_type);
        
        if(isset($arr_response['status']) && $arr_response['status'] == 'success')
        {
            if(isset($arr_response['data']))
            {
                $arr_data = $arr_response['data'];
            }
            if(isset($arr_response['data']['arr_pagination']))
            {
                $arr_pagination = $arr_response['data']['arr_pagination'];
            }
        }
        
        $this->arr_view_data['module_url_path']     = $this->module_url_path;
        $this->arr_view_data['page_title']     = "Notification";
        $this->arr_view_data['arr_data']         = $arr_data;
        $this->arr_view_data['arr_pagination']   = $arr_pagination;
        return view($this->module_view_folder.'notification',$this->arr_view_data);
    }

    public function review_rating()
    {   
        $arr_pagination = $arr_data = $arr_response['data'] = [];
        
        $arr_response = $this->AuthService->get_review();
        if(isset($arr_response['status']) && $arr_response['status'] == 'success')
        {
            if(isset($arr_response['data']))
            {
                $arr_data = $arr_response['data'];
            }
            if(isset($arr_response['data']['arr_pagination']))
            {
                $arr_pagination = $arr_response['data']['arr_pagination'];
            }
        }
        $this->arr_view_data['module_url_path']     = $this->module_url_path;
        $this->arr_view_data['page_title']     = "Review Rating";
        $this->arr_view_data['arr_data']         = $arr_data;
        $this->arr_view_data['arr_pagination']   = $arr_pagination;

        return view($this->module_view_folder.'review_rating',$this->arr_view_data);
    }
    public function messages(Request $request)
    {
        $arr_chat_list = $arr_chat_details = [];

        $is_chat_enable = 'NO';
        if($request->has('is_chat_enable') && $request->input('is_chat_enable')!='')
        {
            $is_chat_enable = base64_decode($request->input('is_chat_enable'));
        }

        if($request->has('to_user_id'))
        {
            $arr_from_user_details     = get_login_user_details();
            $to_user_id   = base64_decode($request->input('to_user_id'));
            $login_user_id = isset($arr_from_user_details['id']) ? $arr_from_user_details['id'] :0;
            
            $this->AuthService->read_unread_message($to_user_id,$login_user_id);
        }

        $arr_response = $this->AuthService->get_chat_list($request);
        
        if(isset($arr_response['status']) && $arr_response['status'] == 'success')
        {
            if(isset($arr_response['data']) && sizeof($arr_response['data'])>0)
            {
                $arr_chat_list = $arr_response['data'];
            }
        }
        
        $arr_chat_response = $this->AuthService->get_chat_details($request);
        
        if(isset($arr_chat_response['status']) && $arr_chat_response['status'] == 'success')
        {
            if(isset($arr_chat_response['data']) && sizeof($arr_chat_response['data'])>0)
            {
                $arr_chat_details = $arr_chat_response['data'];
            }
        }
        
        $arr_from_user_details = $arr_to_user_details = [];
        
        if($request->has('to_user_id')){
            $to_user_id   = base64_decode($request->input('to_user_id'));
            
            $arr_data = $this->AuthService->get_user_details_by_id($to_user_id);
            if(isset($arr_data) && sizeof($arr_data)>0)
            {
                $arr_to_user_details = $arr_data;
                $profile_image            = url('/uploads/default-profile.png');
                if((isset($arr_to_user_details['profile_image']) && $arr_to_user_details['profile_image']!='') && file_exists($this->user_profile_base_img_path.$arr_to_user_details['profile_image']))
                {
                    $profile_image = $this->user_profile_public_img_path.$arr_to_user_details['profile_image'];
                }
                $arr_to_user_details['profile_image'] = $profile_image;

            }

            $arr_from_user_details     = get_login_user_details();
            if(isset($arr_from_user_details) && sizeof($arr_from_user_details)>0)
            {
                $profile_image            = url('/uploads/default-profile.png');
                if((isset($arr_from_user_details['profile_image']) && $arr_from_user_details['profile_image']!='') && file_exists($this->user_profile_base_img_path.$arr_from_user_details['profile_image']))
                {
                    $profile_image = $this->user_profile_public_img_path.$arr_from_user_details['profile_image'];
                }
                $arr_from_user_details['profile_image'] = $profile_image;
            }
            $login_user_id = isset($arr_from_user_details['id']) ? $arr_from_user_details['id'] :0;
            $this->AuthService->read_unread_message($to_user_id,$login_user_id);
        }
        
        $this->arr_view_data['module_url_path']       = $this->module_url_path;
        $this->arr_view_data['page_title']            = "Messages";
        $this->arr_view_data['is_chat_enable']        = $is_chat_enable;
        $this->arr_view_data['arr_chat_list']         = $arr_chat_list;
        $this->arr_view_data['arr_chat_details']      = $arr_chat_details;
        $this->arr_view_data['arr_to_user_details']   = $arr_to_user_details;
        $this->arr_view_data['arr_from_user_details'] = $arr_from_user_details;

        
        return view($this->module_view_folder.'messages',$this->arr_view_data);
    }
    public function store_chat(Request $request)
    {
        $arr_response = $this->AuthService->store_message($request);
        return response()->json($arr_response);    
    }
    public function get_current_chat_messages(Request $request)
    {
        $from_user_id = $request->input('from_user_id');
        $to_user_id   = $request->input('to_user_id');
        
        if($from_user_id!='' && $to_user_id!='')
        {
            $this->AuthService->read_unread_message($to_user_id,$from_user_id);

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
            
            if(isset($arr_chat_details) && sizeof($arr_chat_details)>0)
            {
                $arr_response['status'] = 'success';
                $arr_response['msg'] = 'chat details available';
                $arr_response['data'] = $arr_chat_details;
                return $this->build_response($arr_response['status'],$arr_response['msg'],$arr_response['data']); 
            }
        }
        
        $arr_response['status'] = 'error';
        $arr_response['msg'] = 'chat details not available';
        return $this->build_response($arr_response['status'],$arr_response['msg']); 
    } 
    public function redirect_from_stripe(Request $request)
    {
        $arr_response = [];

        if($request->has('error') && $request->input('access_denied'))
        {
            $error_description = $request->get('error_description');
            
            \Flash::error($error_description);
            \Sentinel::logout();
            return redirect(url('login'));
        }
        $code  = $request->input('code');
        $scope = $request->input('scope');

        if($request->input('code') == '')
        {
            \Flash::error('Stripe code field is empty');
            \Sentinel::logout();
            return redirect(url('login'));

            $arr_response['status']  = 'error';
            $arr_response['msg']     = 'Stripe code field is empty';
            $arr_response['data']    = [];
            return $arr_response;
        }

        if($request->input('scope') == '')
        {
            \Flash::error('Stripe scope field is empty');
            \Sentinel::logout();
            return redirect(url('login'));
        }

        $user_id = $request->input('state');
        if($user_id!='')
        {
            $user_id = $this->CommonDataService->decrypt_value($user_id);
        }

        $arr_user_details = $this->CommonDataService->get_user_details($user_id);

        if(sizeof($arr_user_details) == 0)
        {
            \Flash::error('Driver details not found,Please try again.');
            \Sentinel::logout();
            return redirect(url('login'));
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
                \Flash::success('Driver Stripe account details updated successfully');
                return redirect(url('driver/dashboard'));
            }
            else
            {
                \Flash::error('Something went wrong,Please try again.');
                \Sentinel::logout();
                return redirect(url('login'));
            }
        }
        elseif(isset($arr_data['error']) && $arr_data['error'] != "")
        {
            $arr_update['stripe_account_response'] = isset($arr_data) ? json_encode($arr_data) : '';

            $this->update_stripe_user_details($arr_update,$user_id);
            
            $error_description = isset($arr_data['error_description']) ? $arr_data['error_description'] : 'Something went wrong,Please try again.';
         
            \Flash::error($error_description);
            \Sentinel::logout();
            return redirect(url('login'));
        }
        else
        {
            $arr_update['stripe_account_response'] = isset($arr_data) ? json_encode($arr_data) : '';
            
            $this->update_stripe_user_details($arr_update,$user_id);
            
            \Flash::error('Something went wrong,Please try again.');
            \Sentinel::logout();
            return redirect(url('login'));
        }   
    
        \Flash::error('Something went wrong,Please try again.');
        \Sentinel::logout();
        return redirect(url('login'));
    }
    
    public function download_invoice(Request $request){

        $booking_id = base64_decode($request->input('booking_id'));

        if($booking_id == '')
        {
            Flash::error('Error while downloading the invoice');
            return redirect()->back();    
        }
       
        if(isset($booking_id))
        {
            $receiptName = "TRIP_INVOICE_".$booking_id.".pdf";
            $pathToFile  =  $this->invoice_base_img_path.$receiptName;
            if(file_exists($pathToFile)){
                return response()->download($pathToFile); 
            }else{
                Flash::error("Something went wrong while downloading the file");
                return redirect()->back();
            }
        }
        else
        {
           Flash::error('Error while downloading the invoice');
        }
        return redirect()->back();
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
}
