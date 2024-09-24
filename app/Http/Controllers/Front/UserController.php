<?php

namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Common\Services\Web\AuthService;
use App\Common\Services\Web\UserService;
use App\Common\Services\StripeService;

use Validator;
use Sentinel;
use Flash;

class UserController extends Controller
{
    public function __construct(
                                    AuthService   $auth_service,
                                    UserService   $user_service,
                                    StripeService $stripeservice
                                )
    {
        $this->arr_view_data      = [];
        $this->module_title       = "User";
        $this->module_view_folder = "front.user.";

        $this->AuthService        = $auth_service;
        $this->UserService        = $user_service;
        $this->StripeService      = $stripeservice;

        $this->user_profile_public_img_path  = url('/').config('app.project.img_path.user_profile_images');
        $this->user_profile_base_img_path    = base_path().config('app.project.img_path.user_profile_images');
        $this->module_url_path               = url(config('app.project.role_slug.user_role_slug')."/dashboard");

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

        $this->user_profile_public_img_path = url('/').config('app.project.img_path.user_profile_images');
        $this->user_profile_base_img_path  = base_path().config('app.project.img_path.user_profile_images');

        $this->invoice_public_img_path = url('/').config('app.project.img_path.invoice');
        $this->invoice_base_img_path   = base_path().config('app.project.img_path.invoice');

    }
    
    public function index()
    {	
        $this->arr_view_data['page_title']     = "User";
    	$this->arr_view_data['module_url_path']     = $this->module_url_path;
        return view($this->module_view_folder.'dashboard',$this->arr_view_data);
    }

    public function my_profile_view(Request $request)
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
        $this->arr_view_data['page_title']             = "My Profile View";
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
            return redirect(url('user/my_profile_edit'));

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
    
    public function my_booking(Request $request)
    {   
        $arr_response = $arr_response['data'] = [];
        // $trip_type = $request->input('trip_type');

        $trip_type = 'PENDING';

        if($request->has('trip_type') && $request->input('trip_type')!='')
        {
            $trip_type = $request->input('trip_type');
        }

        if($trip_type == 'ONGOING'){
            $arr_response = $this->UserService->ongoing_trips();
        }
        if($trip_type == 'PENDING'){
            $arr_response = $this->UserService->pending_trips();
        }
        if(!$request->has('trip_type'))
        {
            if(isset($arr_response['status']) && $arr_response['status'] == 'error'){
                $arr_response = $this->UserService->ongoing_trips();
                $trip_type = 'ONGOING';
            }
        }
        if($trip_type == 'COMPLETED'){
    
            $arr_response = $this->UserService->completed_trips();
        }
        if($trip_type == 'CANCELED'){
    
            $arr_response = $this->UserService->canceled_trips();
        }
        
        if(isset($arr_response['status']) && $arr_response['status'] == 'success')
        {
            if($arr_response['status']=='success')
            {
                $this->arr_view_data['arr_data']           = $arr_response['data'];
            }
        }   
        $this->arr_view_data['trip_type']       = $trip_type;
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['page_title']      = "My Booking";
        return view($this->module_view_folder.'my_booking',$this->arr_view_data);
    }
    
    public function delivery_request(Request $request)
    {
        $arr_card_details = [];
        $arr_response = $this->AuthService->get_all_card_details($request);
        if(isset($arr_response['status']) && $arr_response['status'] == 'success'){
            if(isset($arr_response['data']) && count($arr_response['data'])>0){
                $arr_card_details = $arr_response['data'];
            }
        }
        $this->arr_view_data['module_url_path']       = $this->module_url_path;
        $this->arr_view_data['page_title']            = "Delivery Request";
        $this->arr_view_data['arr_card_details']      = $arr_card_details;
        $this->arr_view_data['publish_key']           = $this->publish_key;
        
        return view($this->module_view_folder.'delivery_request',$this->arr_view_data);
    }
    public function store_load_post_request(Request $request)
    {
        $arr_response = $this->UserService->store_load_post_request($request);
        if($request->has('action_type') && $request->input('action_type') == 'book'){
            if(isset($arr_response['status']) && $arr_response['status'] == 'success')
            {
                $msg = isset($arr_response['msg']) ? $arr_response['msg'] : '';
                Flash::success($msg);
            }
        }
        return $this->build_response($arr_response['status'],$arr_response['msg'],$arr_response['data']);   
    }
    public function book_driver_request(Request $request)
    {
        if($request->input('load_post_request_id')=='')
        {
            Flash::error('Shipment request identifier missing,unable to process request.');
            return redirect()->back();  
        }
        
        $arr_load_post_details = [];

        $arr_response = $this->UserService->load_post_book_driver_details($request);
        
        if(isset($arr_response['status']) && $arr_response['status'] == 'success')
        {
            if(isset($arr_response['data']) && count($arr_response['data'])>0)
            {
                $arr_load_post_details = $arr_response['data'];
            }
        }
        $this->arr_view_data['page_title']       = "Book Driver";
        $this->arr_view_data['module_url_path']  = $this->module_url_path;
        $this->arr_view_data['arr_load_post_details'] = $arr_load_post_details;

        return view($this->module_view_folder.'book_driver_request',$this->arr_view_data);

    }
    public function process_to_book_driver(Request $request)
    {
        $arr_response = $this->UserService->process_to_book_driver($request);
        if(isset($arr_response['status']) && $arr_response['status'] == 'success')
        {
            $msg = isset($arr_response['msg']) ? $arr_response['msg'] : '';
            Flash::success($msg);
        }
        return $this->build_response($arr_response['status'],$arr_response['msg'],$arr_response['data']);   
    }
    public function track_driver(Request $request)
    {
        $arr_data = [];
        $arr_response = $this->UserService->ongoing_trips();
        if(isset($arr_response['status']) && $arr_response['status'] == 'success')
        {
            if(isset($arr_response['data']) && count($arr_response['data'])>0)
            {
                $arr_data = $arr_response['data'];
            }
        }   
        $this->arr_view_data['arr_data']        = $arr_data;
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['page_title']      = "Track Driver";

        return view($this->module_view_folder.'track_driver',$this->arr_view_data);
    }
    
    public function track_trip(Request $request)
    {
        $redirect_back = 'booking';
        if($request->has('redirect') && $request->input('redirect')!='')
        {
            $redirect_back = $request->input('redirect');
        }

        if($request->input('booking_id')=='')
        {
            Flash::error('Booking identifier missing,unable to process request.');
            return redirect()->back();  
        }
        
        $arr_trip_details = [];

        $arr_response = $this->UserService->trip_details($request);
        
        if(isset($arr_response['status']) && $arr_response['status'] == 'success')
        {
            if(isset($arr_response['data']) && count($arr_response['data'])>0)
            {
                $arr_trip_details = $arr_response['data'];
            }
        }

        $this->arr_view_data['page_title']       = "Track Booking";
        $this->arr_view_data['module_url_path']  = $this->module_url_path;
        $this->arr_view_data['redirect_back']    = $redirect_back;
        $this->arr_view_data['arr_trip_details'] = $arr_trip_details;

        return view($this->module_view_folder.'track_trip',$this->arr_view_data);
    }
    
    public function process_cancel_trip(Request $request)
    {
        $arr_response = $this->UserService->process_cancel_trip_status_by_user($request);
        return $this->build_response($arr_response['status'],$arr_response['msg'],$arr_response['data']);   
    }
    
    public function track_live_trip(Request $request)
    {
        $arr_trip_details = [];
        $arr_response = $this->UserService->track_live_trip($request);
        return $this->build_response($arr_response['status'],$arr_response['msg'],$arr_response['data']);   
    }

    public function pending_load_post(Request $request)
    {
        $arr_load_post_details = [];

        $arr_response = $this->UserService->load_post_details($request);

        if(isset($arr_response['status']) && $arr_response['status'] == 'success')
        {
            if(isset($arr_response['data']) && count($arr_response['data'])>0)
            {
                $arr_load_post_details = $arr_response['data'];
            }
        }
        

        $this->arr_view_data['module_url_path']       = $this->module_url_path;
        $this->arr_view_data['page_title']            = "Shipment Post Details";
        $this->arr_view_data['arr_load_post_details'] = $arr_load_post_details;

        return view($this->module_view_folder.'load_post_details',$this->arr_view_data);
    }
    public function accept_load_post(Request $request)
    {
        $arr_response = $this->UserService->accept_load_post_by_user($request);
        
        if(isset($arr_response['status']) && $arr_response['status'] == 'success')
        {
            $msg = isset($arr_response['msg']) ? $arr_response['msg'] : '';
            Flash::success($msg);
        }
        return $this->build_response($arr_response['status'],$arr_response['msg'],$arr_response['data']);   
    }
    public function reject_load_post(Request $request)
    {
        $arr_response = $this->UserService->reject_load_post($request);
        
        if(isset($arr_response['status']) && $arr_response['status'] == 'success')
        {
            $msg = isset($arr_response['msg']) ? $arr_response['msg'] : '';
            Flash::success($msg);
            $return_url = url('user/my_booking?trip_type=PENDING');
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
        $arr_response = $this->UserService->cancel_pending_load_post($request);
        
        if(isset($arr_response['status']) && $arr_response['status'] == 'success')
        {
            $msg = isset($arr_response['msg']) ? $arr_response['msg'] : '';
            Flash::success($msg);
            $return_url = url('user/my_booking?trip_type=PENDING');
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
    public function booking_details(Request $request)
    {
        $arr_trip_details = [];

        $arr_response = $this->UserService->trip_details($request);
        
        if(isset($arr_response['status']) && $arr_response['status'] == 'success')
        {
            if(isset($arr_response['data']) && count($arr_response['data'])>0)
            {
                $arr_trip_details = $arr_response['data'];
            }
        }

        $this->arr_view_data['module_url_path']  = $this->module_url_path;
        $this->arr_view_data['page_title']       = "My Booking Details";
        $this->arr_view_data['arr_trip_details'] = $arr_trip_details;

        return view($this->module_view_folder.'booking_details',$this->arr_view_data);
    }
    public function payment()
    {   
        $arr_response = [];
        $arr_response = $this->AuthService->get_card_details();
        
        $arr_data = $arr_pagination = [];

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
        $this->arr_view_data['arr_data']           = $arr_data;
        $this->arr_view_data['arr_pagination']           = $arr_pagination;
        $this->arr_view_data['module_url_path']     = $this->module_url_path;
        $this->arr_view_data['page_title']     = "Payment";
        return view($this->module_view_folder.'payment',$this->arr_view_data);
    }

    public function add_card()
    {   
        $arr_response = $arr_response['data'] = [];
        $arr_response = $this->AuthService->get_card_details();
        if(isset($arr_response))
        {
            if($arr_response['status']=='success')
            {
                $this->arr_view_data['arr_data']           = $arr_response['data'];
            }
        }   

        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['page_title']      = "Add Card";
        $this->arr_view_data['publish_key']     = $this->publish_key;
        
        return view($this->module_view_folder.'add_card',$this->arr_view_data);
    }

    public function delete_card(Request $request)
    {
        $user_id     = validate_user_login_id();

        if ($user_id == 0) 
        {
            Flash::error('Invalid user token');
            return redirect()->back();
        }

        if ($request->input('card_id') == '') 
        {
            Flash::error('Card identifier not found.');
            return redirect()->back();
        }

        $arr_card = [];
        $arr_card['user_id'] = $user_id;
        $arr_card['card_id'] = base64_decode($request->input('card_id'));
        
        $arr_response = $this->StripeService->delete_card($arr_card);

        if(isset($arr_response['status']) && $arr_response['status'] == 'success')
        {
            $msg = isset($arr_response['msg']) ? $arr_response['msg'] : '';
            Flash::success($msg);
            return redirect()->back();

        }
        elseif (isset($arr_response['status']) && $arr_response['status'] == 'error') 
        {
            $msg = isset($arr_response['msg']) ? $arr_response['msg'] : '';
            Flash::error($msg);
            return redirect()->back();
        }
        Flash::error('Problem occured, while deleting card details,Please try again.');
        return redirect()->back();
    }

    public function store(Request $request)
    {   
        $arr_response = [];
        $arr_response = $this->AuthService->store_card($request);
        
        if(isset($arr_response['status']) && $arr_response['status'] == 'success')
        {
            $msg = isset($arr_response['msg']) ? $arr_response['msg'] : '';
            Flash::success($msg);
            return response()->json($arr_response);

        }
        elseif (isset($arr_response['status']) && $arr_response['status'] == 'error') 
        {
            return response()->json($arr_response);    
        }
        return response()->json($arr_response);    

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
            return redirect(url('user/change_password'));

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
        $user_type = 'USER';

        $arr_pagination = $arr_data = $arr_response['data'] = [];
        
        $arr_response = $this->AuthService->get_notification($request,$user_type);
        
        // dd($arr_response);

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

   /* public function review_rating()
    {   
        $this->arr_view_data['page_title']     = "Review Rating";
        return view($this->module_view_folder.'review_rating',$this->arr_view_data);
    }*/
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
        $this->arr_view_data['arr_data']        = $arr_response['data'];
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['page_title']      = "Review Rating";
        $this->arr_view_data['arr_data']        = $arr_data;
        $this->arr_view_data['arr_pagination']  = $arr_pagination;
        
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
        
        $arr_response['status'] = 'error';
        $arr_response['msg'] = 'chat details not available';
        return $this->build_response($arr_response['status'],$arr_response['msg']); 
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
}
