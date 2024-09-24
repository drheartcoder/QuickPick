<?php

namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Common\Services\Web\AuthService;
use App\Common\Services\Web\EnterpriseAdminService;

use Validator;
use Sentinel;
use Flash;

class EnterpriseAdminController extends Controller
{
    public function __construct(
                                    AuthService   			$auth_service,
                                    EnterpriseAdminService  $enterprise_admin_service
                                )
    {
        $this->AuthService            = $auth_service;
        $this->EnterpriseAdminService = $enterprise_admin_service;
        
        $this->arr_view_data                = [];
        $this->module_title                 = "Enterprise Admin";
        $this->module_view_folder           = "front.enterprise_admin.";
        $this->module_url_path              = url(config('app.project.role_slug.enterprise_admin_role_slug')."/dashboard");
        $this->user_profile_public_img_path = url('/').config('app.project.img_path.user_profile_images');
        $this->user_profile_base_img_path   = base_path().config('app.project.img_path.user_profile_images');
    }
    
    public function index()
    {	
        $this->arr_view_data['page_title']     = "Enterprise Admin";
    	$this->arr_view_data['module_url_path']= $this->module_url_path;

        return view($this->module_view_folder.'dashboard',$this->arr_view_data);
    }

    public function my_profile_view(Request $request)
    {   
        $arr_response = $arr_response['data'] = [];
        $arr_response = $this->AuthService->get_profile();

        $arr_data = [];
        if(isset($arr_response['status']) && $arr_response['status'] == 'success')
        {
        	if(isset($arr_response['data']) && count($arr_response['data'])>0)
        	{
        		$arr_data = $arr_response['data'];
        	}
        }  

        $this->arr_view_data['arr_data']        = $arr_data;
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['page_title']      = "My Profile View";

        return view($this->module_view_folder.'my_profile_view',$this->arr_view_data);
    }

    public function my_profile_edit()
    {   
    	$arr_response = $arr_response['data'] = [];
        $arr_response = $this->AuthService->get_profile();

        $arr_data = [];
        if(isset($arr_response['status']) && $arr_response['status'] == 'success')
        {
        	if(isset($arr_response['data']) && count($arr_response['data'])>0)
        	{
        		$arr_data = $arr_response['data'];
        	}
        }  
        $this->arr_view_data['arr_data']        = $arr_data;
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['page_title']      = "My Profile Edit";

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
            return redirect()->back();

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
    
    public function change_password()
    {   
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['page_title']      = "Change Password";

        return view($this->module_view_folder.'change_password',$this->arr_view_data);
    }

    public function update_password(Request $request)
    {   
        $arr_response = $this->AuthService->change_password($request);
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
        Flash::error('Something went wrong,Please try again.');
        return redirect()->back();          
    }

    public function manage_users(Request $request)
    {   
    	$arr_data = [];
    	$arr_response = $this->EnterpriseAdminService->get_enterprise_users_list($request);

        if(isset($arr_response['status']) && $arr_response['status'] == 'success')
        {
        	if(isset($arr_response['data']) && count($arr_response['data'])>0)
        	{
        		$arr_data = $arr_response['data'];
        	}
        } 
        $this->arr_view_data['page_title']      = "Manage Enterprise Users";
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['arr_data'] 		= $arr_data;

        return view($this->module_view_folder.'manage_users',$this->arr_view_data);
    }

    public function add_users()
    {   
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['page_title']      = "Add Enterprise Users";
        
        return view($this->module_view_folder.'add_users',$this->arr_view_data);
    }

    public function store_enterprise_user(Request $request)
    {
    	$arr_response = $this->EnterpriseAdminService->store_enterprise_user($request);
    	
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
        Flash::error('Something went wrong,Please try again.');
        return redirect()->back();  
    }

    public function edit_user(Request $request)
    {   
    	$arr_data = [];
    	$arr_response = $this->EnterpriseAdminService->get_enterprise_user_details($request);

    	if(isset($arr_response['status']) && $arr_response['status'] == 'error')
        {
        	$msg = isset($arr_response['msg']) ? $arr_response['msg'] : '';
            Flash::error($msg);
            return redirect(url(config('app.project.role_slug.enterprise_admin_role_slug')."/manage_users"));          
        } 

        if(isset($arr_response['status']) && $arr_response['status'] == 'success')
        {
        	if(isset($arr_response['data']) && count($arr_response['data'])>0)
        	{
        		$arr_data = $arr_response['data'];
        	}
        } 
        $this->arr_view_data['page_title']      = "Edit Enterprise Users";
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['arr_data'] 		= $arr_data;

        return view($this->module_view_folder.'edit_user',$this->arr_view_data);
    }

    public function update_enterprise_user(Request $request)
    {
    	$arr_response = $this->EnterpriseAdminService->update_enterprise_user($request);
    	
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
        Flash::error('Something went wrong,Please try again.');
        return redirect()->back();  
    }
    
    public function change_status(Request $request)
    {
    	$arr_response = $this->EnterpriseAdminService->change_status($request);
    	
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
