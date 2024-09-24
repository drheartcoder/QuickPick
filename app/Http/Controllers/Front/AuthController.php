<?php

namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Common\Services\ReviewService;
use App\Common\Services\Web\AuthService;
use App\Common\Services\StripeService;
use App\Common\Services\CommonDataService;
use App\Models\StaticPageModel;
use App\Models\UserModel;
use App\Models\DriverCarRelationModel;


use Validator;
use Sentinel;
use Flash;

class AuthController extends Controller
{
    public function __construct(
                                    ReviewService $review_service,
                                    AuthService   $auth_service,
                                    StripeService $stripe_service,
                                    CommonDataService $common_data_service,
                                    StaticPageModel $static_page,
                                    DriverCarRelationModel $driver_car_relation
                                )
    {
        $this->ReviewService          = $review_service;
        $this->AuthService            = $auth_service;
        $this->StripeService          = $stripe_service;
        $this->CommonDataService      = $common_data_service;
        $this->StaticPageModel        = $static_page;
        $this->DriverCarRelationModel = $driver_car_relation;

        $this->arr_view_data      = [];
        $this->module_title       = "Login";
        $this->module_view_folder = "front.auth";

        $this->vehicle_doc_public_path = url('/').config('app.project.img_path.vehicle_doc');
        $this->vehicle_doc_base_path   = base_path().config('app.project.img_path.vehicle_doc');

        $this->driving_license_public_path = url('/').config('app.project.img_path.driving_license');
        $this->driving_license_base_path   = base_path().config('app.project.img_path.driving_license');

        $this->stripe_client_id = config('services.stripe_client_id');
        $this->stripe_token_url = config('services.stripe_token_url');
        $this->stripe_authorize_url = config('services.stripe_authorize_url');

        $this->stripe_driver_redirect_url = url('/driver/redirect_from_stripe');
    }
    
    public function index(Request $request)
    {	
        $country_code = 'US';
        $this->arr_view_data['page_title']     = "Login";
    	$this->arr_view_data['country_code']     = $country_code;

        return view($this->module_view_folder.'.login',$this->arr_view_data);
    }

    public function register(Request $request)
    {   
        $arr_prev_data = [];
        
        $first_name   = $request->input('first_name');
        $last_name    = $request->input('last_name');
        $email        = $request->input('email');
        $country_code = $request->input('country_code');
        $mobile_no    = $request->input('mobile_no');
        
        $arr_prev_data = 
                            [
                                'first_name'   => (isset($first_name) && $first_name!=null) ? $first_name : '',
                                'last_name'    => (isset($last_name) && $last_name!=null) ? $last_name : '',
                                'email'        => (isset($email) && $email!=null) ? $email : '',
                                'country_code' => (isset($country_code) && $country_code!=null) ? '+'.$country_code : '',
                                'mobile_no'    => (isset($mobile_no) && $mobile_no!=null) ? $mobile_no : ''
                            ];

        $obj_data = $this->StaticPageModel->where('page_slug','terms-and-conditions')->first();
        $this->arr_view_data['terms_conditions'] = $obj_data;

        $this->arr_view_data['page_title']     = "Register";
        $this->arr_view_data['arr_prev_data']  = $arr_prev_data;
        
        return view($this->module_view_folder.'.register',$this->arr_view_data);
    }

    public function register_enterprise_admin(Request $request)
    {   
        $arr_prev_data = [];
        
        $first_name   = $request->input('first_name');
        $last_name    = $request->input('last_name');
        $email        = $request->input('email');
        $country_code = $request->input('country_code');
        $mobile_no    = $request->input('mobile_no');
        
        $arr_prev_data = 
                            [
                                'first_name'   => (isset($first_name) && $first_name!=null) ? $first_name : '',
                                'last_name'    => (isset($last_name) && $last_name!=null) ? $last_name : '',
                                'email'        => (isset($email) && $email!=null) ? $email : '',
                                'country_code' => (isset($country_code) && $country_code!=null) ? '+'.$country_code : '',
                                'mobile_no'    => (isset($mobile_no) && $mobile_no!=null) ? $mobile_no : ''
                            ];

        $obj_data = $this->StaticPageModel->where('page_slug','terms-and-conditions')->first();
        $this->arr_view_data['terms_conditions'] = $obj_data;

        $this->arr_view_data['page_title']     = "Register Enterprise Admin";
        $this->arr_view_data['arr_prev_data']  = $arr_prev_data;
        
        return view($this->module_view_folder.'.register_enterprise_admin',$this->arr_view_data);
    }
    
    public function process_register(Request $request)
    {
        $arr_response = $this->AuthService->store($request);
        if(isset($arr_response['status']) && $arr_response['status'] == 'success')
        {
            $msg = isset($arr_response['msg']) ? $arr_response['msg'] : '';
            Flash::success($msg);
            // $mobile_number = base64_encode($request->input('mobile_no'));
            $mobile_number         = trim(str_replace(' ','',$request->input('mobile_no')));
            $mobile_number = base64_encode($mobile_number);
            $user_type     = $request->input('user_type');
            $redirect_url = url('/verify_otp?enc_code='.$mobile_number.'&user_type='.$user_type);
            return redirect($redirect_url);
        }
        else
        {
            $msg = isset($arr_response['msg']) ? $arr_response['msg'] : '';
            Flash::error($msg);
            return redirect()->back();          
        }
        Flash::error('Something went wrong,Please try again.');
        return redirect()->back();          
    }
    
    public function process_enterprise_admin_register(Request $request)
    {
        $arr_response = $this->AuthService->process_enterprise_admin_register($request);
        if(isset($arr_response['status']) && $arr_response['status'] == 'success')
        {
            $msg = isset($arr_response['msg']) ? $arr_response['msg'] : '';
            Flash::success($msg);
            
            $mobile_number = trim(str_replace(' ','',$request->input('mobile_no')));
            $mobile_number = base64_encode($mobile_number);
            $user_type     = $request->input('user_type');

            $redirect_url = url('/verify_otp?enc_code='.$mobile_number.'&user_type='.$user_type);
            return redirect($redirect_url);
        }
        else
        {
            $msg = isset($arr_response['msg']) ? $arr_response['msg'] : '';
            Flash::error($msg);
            return redirect()->back();          
        }
        Flash::error('Something went wrong,Please try again.');
        return redirect()->back();          
    }
    
    public function update_driver_vehicle_details(Request $request)
    {
        $arr_response = $this->AuthService->update_driver_vehicle_details($request);

        if(isset($arr_response['status']) && $arr_response['status'] == 'success')
        {
            $msg = isset($arr_response['msg']) ? $arr_response['msg'] : '';
            Flash::success($msg);
            \Sentinel::logout();
            return redirect(url('login'));
            // return redirect()->back();
        }
        elseif (isset($arr_response['status']) && $arr_response['status'] == 'error') 
        {
            \Sentinel::logout();
            $msg = isset($arr_response['msg']) ? $arr_response['msg'] : '';
            Flash::error($msg);
            return redirect()->back();          
        }
        \Sentinel::logout();
        Flash::error('Something went wrong,Please try again.');
        return redirect()->back();  
    }
    public function update_driver_previous_vehicle_details(Request $request)
    {
        $arr_response = $this->AuthService->update_driver_previous_vehicle_details($request);

        if(isset($arr_response['status']) && $arr_response['status'] == 'success')
        {
            $msg = isset($arr_response['msg']) ? $arr_response['msg'] : '';
            Flash::success($msg);
            \Sentinel::logout();
            //return redirect(url('login'));
            return redirect()->back();
        }
        elseif (isset($arr_response['status']) && $arr_response['status'] == 'error') 
        {
            \Sentinel::logout();
            $msg = isset($arr_response['msg']) ? $arr_response['msg'] : '';
            Flash::error($msg);
            return redirect()->back();          
        }
        \Sentinel::logout();
        Flash::error('Something went wrong,Please try again.');
        return redirect()->back();  
    }

    public function update_admin_driver_previous_vehicle_details(Request $request)
    {
        $arr_response = $this->AuthService->update_admin_driver_previous_vehicle_details($request);

        if(isset($arr_response['status']) && $arr_response['status'] == 'success')
        {
            $msg = isset($arr_response['msg']) ? $arr_response['msg'] : '';
            Flash::success($msg);
            \Sentinel::logout();
            return redirect(url('login'));
        }
        elseif (isset($arr_response['status']) && $arr_response['status'] == 'error') 
        {
            \Sentinel::logout();
            $msg = isset($arr_response['msg']) ? $arr_response['msg'] : '';
            Flash::error($msg);
            return redirect()->back();          
        }
        \Sentinel::logout();
        Flash::error('Something went wrong,Please try again.');
        return redirect()->back();  
    }
    public function update_not_assigned_driver_vehicle_details(Request $request)
    {
        $arr_response = $this->AuthService->update_not_assigned_driver_vehicle_details($request);
        if(isset($arr_response['status']) && $arr_response['status'] == 'success')
        {
            $msg = isset($arr_response['msg']) ? $arr_response['msg'] : '';
            Flash::success($msg);
            \Sentinel::logout();
            return redirect(url('login'));
        }
        elseif (isset($arr_response['status']) && $arr_response['status'] == 'error') 
        {
            \Sentinel::logout();
            $msg = isset($arr_response['msg']) ? $arr_response['msg'] : '';
            Flash::error($msg);
            return redirect()->back();          
        }
        \Sentinel::logout();
        Flash::error('Something went wrong,Please try again.');
        return redirect()->back();  
    }
    
    public function redirect_to_facebook() 
    {
        return \Socialite::driver('facebook')->redirect();   
    }

    public function process_login(Request $request)
    {
        $user_type    = $request->input('user_type');
        $arr_response = $this->AuthService->process_login($request);

        if(isset($arr_response['status']) && $arr_response['status'] == 'success')
    	{
    		$msg = isset($arr_response['msg']) ? $arr_response['msg'] : '';
      	    Flash::success($msg);
            if($user_type == 'USER'){
    			return redirect(url('user/delivery_request'));
            }
            else if($user_type == 'DRIVER'){

                /*if(isset($arr_response['data']['stripe_account_id']) && $arr_response['data']['stripe_account_id'] == '')
                {
                    if(isset($arr_response['data']['driver_id']) && $arr_response['data']['driver_id']!=0)
                    {
                        $encrypted_driver_id = $this->CommonDataService->encrypt_value($arr_response['data']['driver_id']);
                        return redirect($this->stripe_authorize_url.'?response_type=code&client_id='.$this->stripe_client_id.'&scope=read_write&state='.$encrypted_driver_id.'&redirect_uri='.$this->stripe_driver_redirect_url);
                    }
                    
                }*/
                return redirect(url('driver/dashboard'));
            }
            else if($user_type == 'ENTERPRISE_ADMIN'){
                return redirect(url('enterprise_admin/dashboard'));
            }
            else{
                
                \Sentinel::logout();
                Flash::error('Something went wrong,Please try again.');
                return redirect()->back();
            }
    	}
        else if(isset($arr_response['status']) && $arr_response['status'] == 'reset_password')
        {
            \Sentinel::logout();
            $msg = isset($arr_response['msg']) ? $arr_response['msg'] : '';
            Flash::success($msg);
            $user_id = isset($arr_response['data']['user_id']) ? $arr_response['data']['user_id'] : 0;
            $redirect_url = url('/reset_password?user_id='.base64_encode($user_id));
            return redirect($redirect_url);
        }
        else if(isset($arr_response['status']) && $arr_response['status'] == 'not_verified')
        {
            \Sentinel::logout();
            $msg = isset($arr_response['msg']) ? $arr_response['msg'] : '';
            $msg = $msg.', Please enter OTP to verify your account';
            Flash::success($msg);
            
            $mobile_number         = trim(str_replace(' ','',$request->input('mobile_no')));
            $mobile_number = base64_encode($mobile_number);

            $user_type     = $request->input('user_type');
            $redirect_url = url('/verify_otp?enc_code='.$mobile_number.'&user_type='.$user_type);
            return redirect($redirect_url);
        }
        else if(isset($arr_response['status']) && $arr_response['status'] == 'new_driver')
        {
            $msg = isset($arr_response['msg']) ? $arr_response['msg'] : '';
            Flash::success($msg);
            
            $driver_id = isset($arr_response['data']['driver_id']) ? $arr_response['data']['driver_id'] : 0;
            $status    = isset($arr_response['status']) ? $arr_response['status'] : '';
            
            $is_individual_vehicle = isset($arr_response['data']['is_individual_vehicle']) ? $arr_response['data']['is_individual_vehicle'] : '';

            $redirect_url = url('/new_driver_vehicle?driver_id='.base64_encode($driver_id).'&status='.$status.'&is_individual_vehicle='.$is_individual_vehicle);
            return redirect($redirect_url);
        }
        else if(isset($arr_response['status']) && $arr_response['status'] == 'not_assigned')
        {
            $msg = isset($arr_response['msg']) ? $arr_response['msg'] : '';
            Flash::error($msg);
            
            $driver_id             = isset($arr_response['data']['driver_id']) ? $arr_response['data']['driver_id'] : 0;
            $status                = isset($arr_response['status']) ? $arr_response['status'] : '';
            $is_individual_vehicle = isset($arr_response['data']['is_individual_vehicle']) ? $arr_response['data']['is_individual_vehicle'] : '';

            $redirect_url = url('/not_assigned_driver_vehicle?driver_id='.base64_encode($driver_id).'&status='.$status.'&is_individual_vehicle='.$is_individual_vehicle);
            return redirect($redirect_url);
        }
        else if(isset($arr_response['status']) && $arr_response['status'] == 'not_approved')
        {
            $msg = isset($arr_response['msg']) ? $arr_response['msg'] : '';
            Flash::success($msg);
            
            $driver_id             = isset($arr_response['data']['driver_id']) ? $arr_response['data']['driver_id'] : 0;
            $status                = isset($arr_response['status']) ? $arr_response['status'] : '';
            $is_individual_vehicle = isset($arr_response['data']['is_individual_vehicle']) ? $arr_response['data']['is_individual_vehicle'] : '';

            $redirect_url = url('/new_driver_vehicle?driver_id='.base64_encode($driver_id).'&status='.$status.'&is_individual_vehicle='.$is_individual_vehicle);
            return redirect($redirect_url);
        }
    	elseif (isset($arr_response['status']) && $arr_response['status'] == 'error') 
    	{
            \Sentinel::logout();
            $msg = isset($arr_response['msg']) ? $arr_response['msg'] : '';
			Flash::error($msg);
			return redirect()->back();    		
    	}
        \Sentinel::logout();
    	Flash::error('Something went wrong,Please try again.');
		return redirect()->back();    		
    }

    public function new_driver_vehicle(Request $request)
    {
        // dd($request->all());
        \Sentinel::logout();

        $driver_id             = base64_decode($request->input('driver_id'));
        $status                = $request->input('status');
        $is_individual_vehicle = $request->input('is_individual_vehicle');
        
        if($driver_id == '')
        {
            Flash::error('Something went wrong, cannot process request,Please try again.');
            return redirect(url('/login'));
        }
        
        if($is_individual_vehicle == '0' || $is_individual_vehicle == '1')
        {
            $arr_vehicle_details = $arr_vehicle_model = [];

            if($status == 'not_approved')
            {
                $arr_vehicle_details = $this->get_driver_vehicle_details($driver_id);
                $vehicle_brand_id = isset($arr_vehicle_details['vehicle_brand_id']) ? $arr_vehicle_details['vehicle_brand_id'] : 0;
                $arr_vehicle_model   =   $this->CommonDataService->get_web_vehicle_model($vehicle_brand_id); 
            }
            $this->arr_view_data['page_title']          = "Driver Vehicle Details";
            $this->arr_view_data['driver_id']           = $driver_id;
            $this->arr_view_data['status']              = $status;
            $this->arr_view_data['arr_vehicle_type']    =  $this->CommonDataService->get_vehicle_types();
            $this->arr_view_data['arr_vehicle_brand']   = $this->CommonDataService->get_vehicle_brand();
            $this->arr_view_data['arr_vehicle_details'] = $arr_vehicle_details;
            $this->arr_view_data['arr_vehicle_model']   = $arr_vehicle_model;

            if($is_individual_vehicle == '0'){
                return view($this->module_view_folder.'.admin_driver_vehicle_details',$this->arr_view_data);
            }
            else if($is_individual_vehicle == '1'){
                return view($this->module_view_folder.'.new_driver_vehicle_details',$this->arr_view_data);
            }
            else{
                Flash::error('Something went wrong, cannot process request,Please try again.');
                return redirect(url('/login'));
            }
        }

        Flash::error('Something went wrong, cannot process request,Please try again.');
        return redirect(url('/login'));
    }

    public function not_assigned_driver_vehicle(Request $request)
    {
        \Sentinel::logout();

        $driver_id             = base64_decode($request->input('driver_id'));
        $status                = $request->input('status');
        $is_individual_vehicle = $request->input('is_individual_vehicle');
        
        if($driver_id == '')
        {
            Flash::error('Something went wrong, cannot process request,Please try again.');
            return redirect(url('/login'));
        }
        
        if($is_individual_vehicle == '0')
        {
            $this->arr_view_data['page_title']          = "Driver Vehicle Details";
            $this->arr_view_data['driver_id']           = $driver_id;
            $this->arr_view_data['status']              = $status;
            $this->arr_view_data['arr_vehicle_type']    =  $this->CommonDataService->get_vehicle_types();
            $this->arr_view_data['arr_vehicle_brand']   = $this->CommonDataService->get_vehicle_brand();

            return view($this->module_view_folder.'.not_assigned_driver_vehicle',$this->arr_view_data);

        }

        Flash::error('Something went wrong, cannot process request,Please try again.');
        return redirect(url('/login'));
    }
    
    public function forget_password(Request $request)
    {
        $country_code = 'US';
        $this->arr_view_data['page_title']     = "Forget Password";
        $this->arr_view_data['country_code']     = $country_code;
        return view($this->module_view_folder.'.forget_password',$this->arr_view_data);
    }

    public function process_forget_password(Request $request)
    {
        $arr_response = $this->AuthService->forget_password($request);
        if(isset($arr_response['status']) && $arr_response['status'] == 'success')
        {
            $msg = isset($arr_response['msg']) ? $arr_response['msg'] : '';
            Flash::success($msg);
            
            $mobile_number         = trim(str_replace(' ','',$request->input('mobile_no')));
            $mobile_number = base64_encode($mobile_number);

            //$mobile_number = base64_encode($request->input('mobile_no'));
            $user_type     = $request->input('user_type');
            $redirect_url = url('/verify_otp?enc_code='.$mobile_number.'&user_type='.$user_type.'&redirect=reset_password');
            return redirect($redirect_url);
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

    public function verify_otp(Request $request)
    {
        $mobile_no = base64_decode($request->input('enc_code'));
        $user_type = strtolower($request->input('user_type'));

        //dd($mobile_no,$user_type);
        
        $redirect = 'login';

        if($request->has('redirect') && $request->input('redirect'))
        {
            $redirect = $request->input('redirect');
        }
        $arr_user = $this->AuthService->get_user_details_by_mobile_number($mobile_no);
        if(sizeof($arr_user)==0)
        {
            Flash::error('User details not found,cannot process request,Please try again.');
            return redirect()->back();          
        }

        $this->arr_view_data['page_title']  = "Verify OTP";
        $this->arr_view_data['mobile_no']   = $mobile_no;
        $this->arr_view_data['user_type']   = $user_type;
        $this->arr_view_data['arr_user']    = $arr_user;
        $this->arr_view_data['redirect_to'] = $redirect;

        return view($this->module_view_folder.'.verify_otp',$this->arr_view_data);
    }

    public function resend_otp(Request $request)
    {
        $arr_response = $this->AuthService->resend_otp($request);

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

    public function process_verify_otp(Request $request)
    {
        $redirect_to = $request->input('redirect_to');

        $arr_response = $this->AuthService->verify_otp($request);

        if(isset($arr_response['status']) && $arr_response['status'] == 'success')
        {
            $msg = isset($arr_response['msg']) ? $arr_response['msg'] : '';
            Flash::success($msg);
            $redirect_url = url('/login');
            if(isset($redirect_to) && $redirect_to == 'reset_password')
            {
                $user_id = isset($arr_response['data']['user_id']) ? $arr_response['data']['user_id'] : '';
                $redirect_url = url('/reset_password?user_id='.base64_encode($user_id));
            }
            return redirect($redirect_url);

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

    public function reset_password(Request $request)
    {
        $user_id = base64_decode($request->input('user_id'));
     
        $arr_user = $this->AuthService->get_user_details_by_id($user_id);
        
        if(sizeof($arr_user)==0)
        {
            Flash::error('User details not found,cannot process request,Please try again.');
            return redirect()->back();          
        }
        
        $this->arr_view_data['page_title']        = "Reset Password";
        $this->arr_view_data['user_id']   = $user_id;
        $this->arr_view_data['arr_user']    = $arr_user;
        
        return view($this->module_view_folder.'.reset_password',$this->arr_view_data);

    }

    public function process_reset_password(Request $request)
    {
        $arr_response = $this->AuthService->reset_password($request);
        if(isset($arr_response['status']) && $arr_response['status'] == 'success')
        {
            $msg = isset($arr_response['msg']) ? $arr_response['msg'] : '';
            Flash::success($msg);
            return redirect(url('/login'));
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

    public function login_facebook(Request $request)
    {
        try 
        {
            $obj_user = \Socialite::driver('facebook')->user();
            // dd($obj_user);
            if((isset($obj_user->id) && $obj_user->id!='') && (isset($obj_user->email) && $obj_user->email!=''))
            {
                $arr_user              = [];
                $arr_user['name']      = isset($obj_user->name) ? $obj_user->name : '';
                $arr_user['email']     = isset($obj_user->email) ? $obj_user->email : '';
                $arr_user['user_type'] = 'USER';
                
                $arr_response = $this->AuthService->login_facebook($arr_user);
                
                if(isset($arr_response['status']) && $arr_response['status'] == 'success')
                {
                    $msg = isset($arr_response['msg']) ? $arr_response['msg'] : '';
                    Flash::success($msg);
                    return redirect(url('user/dashboard'));
                }
                else if(isset($arr_response['status']) && $arr_response['status'] == 'new_user')
                {
                    //$msg = isset($arr_response['msg']) ? $arr_response['msg'] : '';
                    Flash::success('Please complete your registration process with following details.');
                    $arr_data = isset($arr_response['data']) ? $arr_response['data'] : [];
                    
                    $this->arr_view_data['page_title'] = "Facebook Register";
                    $this->arr_view_data['arr_data']   = $arr_data;
                    return view($this->module_view_folder.'.facebook_register',$this->arr_view_data);    
                }
                else if(isset($arr_response['status']) && $arr_response['status'] == 'not_verfied')
                {
                    \Sentinel::logout();
                    $msg = isset($arr_response['msg']) ? $arr_response['msg'] : '';
                    $msg = $msg;
                    Flash::success($msg);
                    $mobile_number = isset($arr_response['data']['mobile_no']) ? base64_encode($arr_response['data']['mobile_no']) : '';
                    $user_type     = 'USER';
                    $redirect_url = url('/verify_otp?enc_code='.$mobile_number.'&user_type='.$user_type);
                    return redirect($redirect_url);
                }
                elseif (isset($arr_response['status']) && $arr_response['status'] == 'error') 
                {
                    \Sentinel::logout();
                    $msg = isset($arr_response['msg']) ? $arr_response['msg'] : '';
                    Flash::error($msg);
                    return redirect()->back();          
                }
                \Sentinel::logout();
                Flash::error('Something went wrong,Please try again.');
                return redirect()->back(); 
            }
            else
            {
                Flash::error('Problem occured, while login with facebook,Please try again ');
                return redirect(url('/login')); 
            }
           
        } 
        catch (\Exception $e) 
        {
            Flash::error('Problem occured, while login with facebook,Please try again ');
            return redirect(url('/login')); 
        }
        
        Flash::error('Something went wrong,Please try again.');
        return redirect(url('/login')); 
    }

    public function process_facebook_register(Request $request)
    {
        $arr_response = $this->AuthService->register_facebook($request);

        if(isset($arr_response['status']) && $arr_response['status'] == 'success')
        {
            $msg = isset($arr_response['msg']) ? $arr_response['msg'] : '';
            Flash::success($msg);

            $mobile_number         = trim(str_replace(' ','',$request->input('mobile_no')));
            $mobile_number = base64_encode($mobile_number);

            //$mobile_number = base64_encode($request->input('mobile_no'));
            $user_type     = $request->input('user_type');
            $redirect_url = url('/verify_otp?enc_code='.$mobile_number.'&user_type='.$user_type);
            return redirect($redirect_url);
        }
        else
        {
            $msg = isset($arr_response['msg']) ? $arr_response['msg'] : '';
            Flash::error($msg);
            return redirect()->back();          
        }
        Flash::error('Something went wrong,Please try again.');
        return redirect()->back();  
    }

    // public function forget_password(Request $request)
    // {
    //     $arr_response = $this->AuthService->forget_password($request);
    //     return $this->build_response($arr_response['status'],$arr_response['msg'],$arr_response['data']);
    // }

    public function change_password(Request $request)
    {
        $arr_response = $this->AuthService->change_password($request);
        return $this->build_response($arr_response['status'],$arr_response['msg']);
    }

    public function get_profile(Request $request)
    {
        $arr_response = $this->AuthService->get_profile($request);
        return $this->build_response($arr_response['status'],$arr_response['msg'],$arr_response['data']);
    }

    public function update_profile(Request $request)
    {
        $arr_response = $this->AuthService->update_profile($request);
        return $this->build_response($arr_response['status'],$arr_response['msg'],$arr_response['data']);
    }

    public function verify_mobile_number(Request $request)
    {
        $arr_response = $this->AuthService->verify_mobile_number($request);
        return $this->build_response($arr_response['status'],$arr_response['msg']);
    }

    public function update_mobile_no(Request $request)
    {
        $arr_response = $this->AuthService->update_mobile_no($request);
        return $this->build_response($arr_response['status'],$arr_response['msg']);
    }
    
    public function get_notification(Request $request)
    {
        $arr_response = $this->AuthService->get_notification($request);
        return $this->build_response($arr_response['status'],$arr_response['msg'],$arr_response['data']);
    }
    
    public function store_review(Request $request)
    {
        $arr_response = $this->ReviewService->store_review($request);
        return $this->build_response($arr_response['status'],$arr_response['msg']);
    }
    public function get_review(Request $request)
    {
        $arr_response = $this->AuthService->get_review($request);
        return $this->build_response($arr_response['status'],$arr_response['msg'],$arr_response['data']);
    }
    
    public function get_card_details(Request $request)
    {
        $arr_response = $this->AuthService->get_card_details($request);
        return $this->build_response($arr_response['status'],$arr_response['msg'],$arr_response['data']);
    }
    public function store_card_details(Request $request)
    {
        $user_id     = validate_user_jwt_token();
        if ($user_id == 0) 
        {
            $arr_response['status'] = 'error';
            $arr_response['msg']    = 'Invalid user token';
            $arr_response['data']    = [];
            return $this->build_response($arr_response['status'],$arr_response['msg'],$arr_response['data']);
        }

        if ($request->input('stripe_token') == '') 
        {
            $arr_response['status'] = 'error';
            $arr_response['msg']    = 'Invalid Stripe token';
            $arr_response['data']    = [];
            return $this->build_response($arr_response['status'],$arr_response['msg'],$arr_response['data']);
        }

        $stripe_token = $request->input('stripe_token');
            
        $arr_customer = [];
        $arr_customer['user_id']      = $user_id;
        $arr_customer['stripe_token'] = $stripe_token;

        $arr_response = $this->StripeService->register_customer($arr_customer);

        return $this->build_response($arr_response['status'],$arr_response['msg'],$arr_response['data']);
    }
    public function delete_card(Request $request)
    {
        $user_id     = validate_user_jwt_token();
        if ($user_id == 0) 
        {
            $arr_response['status'] = 'error';
            $arr_response['msg']    = 'Invalid user token';
            $arr_response['data']    = [];
            return $this->build_response($arr_response['status']);
        }

        if ($request->input('card_id') == '') 
        {
            $arr_response['status'] = 'error';
            $arr_response['msg']    = 'Card identifier not found.';
            $arr_response['data']    = [];
            return $this->build_response($arr_response['status']);
        }
        $arr_card = [];
        $arr_card['user_id'] = $user_id;
        $arr_card['card_id'] = $request->input('card_id');

        $arr_response = $this->StripeService->delete_card($arr_card);
        return $this->build_response($arr_response['status'],$arr_response['msg']);   
    }
    
    public function get_bonus_points(Request $request)
    {
        $arr_response = $this->AuthService->get_bonus_points($request);
        return $this->build_response($arr_response['status'],$arr_response['msg'],$arr_response['data']);
    }
    
    public function get_review_details(Request $request)
    {
        $arr_response = $this->AuthService->get_review_details($request);
        return $this->build_response($arr_response['status'],$arr_response['msg'],$arr_response['data']);
    }

    public function logout()
    {
        Sentinel::logout();
        return redirect(url('/'));
    }
    public function get_driver_vehicle_details($driver_id)
    {   

        $arr_response = [];
        
        $obj_driver_car_relation  = $this->DriverCarRelationModel
                                                ->with(['vehicle_details' => function($query){
                                                    // $query->select('id','vehicle_type_id','is_individual_vehicle','vehicle_brand_id','vehicle_model_id','vehicle_year_id','vehicle_number','is_verified','vehicle_image','registration_doc','proof_of_inspection_doc','insurance_doc','dmv_driving_record','usdot_doc','is_deleted');
                                                    $query->with(['vehicle_brand_details','vehicle_model_details']);
                                                },'driver_details'])
                                                ->where('driver_id',$driver_id)
                                                ->first();
        

        $arr_driver_car_relation = [];
        if($obj_driver_car_relation){
            $arr_driver_car_relation = $obj_driver_car_relation->toArray();
        }

        if(isset($arr_driver_car_relation['vehicle_details']) && sizeof($arr_driver_car_relation['vehicle_details'])>0){
            
            $arr_vehicle_details = $arr_driver_car_relation['vehicle_details'];
            
            $arr_vehicle_details['vehicle_type'] = isset($arr_vehicle_details['vehicle_type_details']['vehicle_type']) ? $arr_vehicle_details['vehicle_type_details']['vehicle_type'] :'';
            $arr_vehicle_details['is_usdot_required'] = isset($arr_vehicle_details['vehicle_type_details']['is_usdot_required']) ? $arr_vehicle_details['vehicle_type_details']['is_usdot_required'] :'';
            $arr_vehicle_details['is_mcdoc_required'] = isset($arr_vehicle_details['vehicle_type_details']['is_mcdoc_required']) ? $arr_vehicle_details['vehicle_type_details']['is_mcdoc_required'] :'';
            

            unset($arr_vehicle_details['vehicle_type_details']);
            
            $driving_license = $vehicle_image = $registration_doc = $insurance_doc = $proof_of_inspection_doc = $dmv_driving_record = $usdot_doc = $mc_doc = '';

            $is_driving_license_verified = isset($arr_driver_car_relation['driver_details']['is_driving_license_verified']) ? $arr_driver_car_relation['driver_details']['is_driving_license_verified'] : '';

            if(isset($arr_driver_car_relation['driver_details']['driving_license']) && $arr_driver_car_relation['driver_details']['driving_license']!=''){
                $tmp_driving_license = isset($arr_driver_car_relation['driver_details']['driving_license']) ? $arr_driver_car_relation['driver_details']['driving_license'] :'';
                if(file_exists($this->driving_license_base_path.$tmp_driving_license))
                {
                    $driving_license = $this->driving_license_public_path.$tmp_driving_license;
                }   
            }

            if(isset($arr_vehicle_details['registration_doc']) && $arr_vehicle_details['registration_doc']!=''){
                $tmp_registration_doc = isset($arr_vehicle_details['registration_doc']) ? $arr_vehicle_details['registration_doc'] :'';
                if(file_exists($this->vehicle_doc_base_path.$tmp_registration_doc))
                {
                    $registration_doc = $this->vehicle_doc_public_path.$tmp_registration_doc;
                }   
            }
            
            if(isset($arr_vehicle_details['vehicle_image']) && $arr_vehicle_details['vehicle_image']!=''){
                $tmp_vehicle_image = isset($arr_vehicle_details['vehicle_image']) ? $arr_vehicle_details['vehicle_image'] :'';
                if(file_exists($this->vehicle_doc_base_path.$tmp_vehicle_image)){
                    $vehicle_image = $this->vehicle_doc_public_path.$tmp_vehicle_image;
                }
            }
            
            if(isset($arr_vehicle_details['proof_of_inspection_doc']) && $arr_vehicle_details['proof_of_inspection_doc']!=''){
                $tmp_proof_of_inspection_doc = isset($arr_vehicle_details['proof_of_inspection_doc']) ? $arr_vehicle_details['proof_of_inspection_doc'] :'';
                if(file_exists($this->vehicle_doc_base_path.$tmp_proof_of_inspection_doc)){
                    $proof_of_inspection_doc = $this->vehicle_doc_public_path.$tmp_proof_of_inspection_doc;
                }
            }

            if(isset($arr_vehicle_details['insurance_doc']) && $arr_vehicle_details['insurance_doc']!=''){
                $tmp_insurance_doc = isset($arr_vehicle_details['insurance_doc']) ? $arr_vehicle_details['insurance_doc'] :'';
                if(file_exists($this->vehicle_doc_base_path.$tmp_insurance_doc)){
                    $insurance_doc = $this->vehicle_doc_public_path.$tmp_insurance_doc;
                }
            }

            if(isset($arr_vehicle_details['dmv_driving_record']) && $arr_vehicle_details['dmv_driving_record']!=''){
                $tmp_dmv_driving_record = isset($arr_vehicle_details['dmv_driving_record']) ? $arr_vehicle_details['dmv_driving_record'] :'';
                if(file_exists($this->vehicle_doc_base_path.$tmp_dmv_driving_record)){
                    $dmv_driving_record = $this->vehicle_doc_public_path.$tmp_dmv_driving_record;
                }
            }
            
            if(isset($arr_vehicle_details['usdot_doc']) && $arr_vehicle_details['usdot_doc']!=''){
                $tmp_usdot_doc = isset($arr_vehicle_details['usdot_doc']) ? $arr_vehicle_details['usdot_doc'] :'';
                if(file_exists($this->vehicle_doc_base_path.$tmp_usdot_doc)){
                    $usdot_doc = $this->vehicle_doc_public_path.$tmp_usdot_doc;
                }
            }
         

            if(isset($arr_vehicle_details['mc_doc']) && $arr_vehicle_details['mc_doc']!=''){
                $tmp_mc_doc = isset($arr_vehicle_details['mc_doc']) ? $arr_vehicle_details['mc_doc'] :'';
                if(file_exists($this->vehicle_doc_base_path.$tmp_mc_doc)){
                    $mc_doc = $this->vehicle_doc_public_path.$tmp_mc_doc;
                }
            }

            $arr_vehicle_details['driving_license_orginal_name'] = isset($arr_driver_car_relation['driver_details']['driving_license']) ? $arr_driver_car_relation['driver_details']['driving_license'] : '';
            $arr_vehicle_details['driving_license']              = $driving_license;
            $arr_vehicle_details['is_driving_license_verified']  = $is_driving_license_verified;
            $arr_vehicle_details['vehicle_insurance_doc_path']   = $insurance_doc;
            $arr_vehicle_details['proof_of_inspection_doc_path'] = $proof_of_inspection_doc;
            $arr_vehicle_details['vehicle_image_path']           = $vehicle_image;
            $arr_vehicle_details['registration_doc_path']        = $registration_doc;
            $arr_vehicle_details['dmv_driving_record_path']      = $dmv_driving_record;
            $arr_vehicle_details['usdot_doc_path']               = $usdot_doc;
            $arr_vehicle_details['mc_doc_path']                  = $mc_doc;
            
            $fair_charge = 0; $status = '';

            if(isset($arr_vehicle_details['is_individual_vehicle']) && $arr_vehicle_details['is_individual_vehicle'] == '1')
            {
                $fair_charge = isset($obj_driver_fare_charge_list->driver_fair_charge_request_details->fair_charge) ? floatval($obj_driver_fare_charge_list->driver_fair_charge_request_details->fair_charge) :0;
                $status      = isset($obj_driver_fare_charge_list->driver_fair_charge_request_details->status) ? $obj_driver_fare_charge_list->driver_fair_charge_request_details->status:'NOT_REQUEST';
            }
            
            if(isset($arr_vehicle_details['is_individual_vehicle']) && $arr_vehicle_details['is_individual_vehicle'] == '0')
            {
                $fair_charge = isset($arr_vehicle_details['driver_per_kilometer_charge']) ? floatval($arr_vehicle_details['driver_per_kilometer_charge']) :0;
                $status      = 'ASSIGN';
            }

            unset($arr_vehicle_details['admin_per_kilometer_charge']);
            unset($arr_vehicle_details['driver_per_kilometer_charge']);
            unset($arr_vehicle_details['vehicle_brand_details']);
            unset($arr_vehicle_details['vehicle_model_details']);

            $arr_vehicle_details['fair_charge'] = $fair_charge;
            $arr_vehicle_details['status']      = $status;
  
            return $arr_vehicle_details;       
        }
        return [];       
    }
}
