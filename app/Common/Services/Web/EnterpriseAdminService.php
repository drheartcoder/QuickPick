<?php

namespace App\Common\Services\Web;

use App\Models\UserModel;

use App\Common\Services\EmailService;
use App\Common\Services\CommonDataService;
use App\Common\Services\NotificationsService;

use Validator;
use Sentinel;

class EnterpriseAdminService
{
         public function __construct(
                                    UserModel                          $user,
                                    EmailService                       $email_service,
                                    CommonDataService                  $common_data_service,
                                    NotificationsService               $notifications_service
                               )   
    {
        $this->UserModel            = $user;
        $this->CommonDataService    = $common_data_service;
        $this->EmailService         = $email_service;
        $this->NotificationsService = $notifications_service;
    }
    
    public function get_enterprise_users_list($request)
    {
        $user_id = validate_user_login_id();
        if ($user_id == 0) 
        {
            $arr_response['status'] = 'error';
            $arr_response['msg']    = 'Invalid user token';
            $arr_response['data']    = [];
            return $arr_response;
        }    
        
        $arr_enterprise_users = [];

        $obj_enterprise_users = $this->UserModel
                                            ->select('id','first_name','last_name','email','mobile_no','country_code','user_type','dob','address','is_active','created_at')
                                            ->whereHas('roles',function($query){
                                                $query->where('slug','enterprise_user');
                                            })
                                            ->where([
                                                        'user_type'       => 'ENTERPRISE_USER',
                                                        'is_company_user' => '1',
                                                        'company_id'      => $user_id,
                                                    ])
                                            ->get();
        if($obj_enterprise_users)
        {
            $arr_enterprise_users = $obj_enterprise_users->toArray();
        }
        
        if(isset($arr_enterprise_users) && count($arr_enterprise_users)>0)
        {
            $arr_response['status'] = 'success';
            $arr_response['msg']    = 'Enterprise users details found';
            $arr_response['data']    = $arr_enterprise_users;
            return $arr_response;
        }

        $arr_response['status'] = 'error';
        $arr_response['msg']    = 'Enterprise users details not found';
        $arr_response['data']    = $arr_enterprise_users;
        return $arr_response;
    }

    public function get_enterprise_user_details($request)
    {
        $user_id = validate_user_login_id();
        if ($user_id == 0) 
        {
            $arr_response['status'] = 'error';
            $arr_response['msg']    = 'Invalid user token';
            $arr_response['data']    = [];
            return $arr_response;
        }    
        
        $enc_id = base64_decode($request->input('enc_id'));
        if ($enc_id == '') 
        {
            $arr_response['status'] = 'error';
            $arr_response['msg']    = 'Enterprise User identifier is missing,unable to process request.';
            $arr_response['data']    = [];
            return $arr_response;
        } 

        $arr_enterprise_user_details = [];

        $obj_enterprise_user_details = $this->UserModel
                                            ->select('id','first_name','last_name','email','mobile_no','country_code','user_type','dob','address','post_code','country_name','state_name','city_name','latitude','longitude','is_active','created_at')
                                            ->whereHas('roles',function($query){
                                                $query->where('slug','enterprise_user');
                                            })
                                            ->where('id',$enc_id)
                                            ->first();
        if($obj_enterprise_user_details)
        {
            $arr_enterprise_user_details = $obj_enterprise_user_details->toArray();
        }
        
        if(isset($arr_enterprise_user_details) && count($arr_enterprise_user_details)>0)
        {
            $arr_response['status'] = 'success';
            $arr_response['msg']    = 'Enterprise users details found';
            $arr_response['data']    = $arr_enterprise_user_details;
            return $arr_response;
        }

        $arr_response['status'] = 'error';
        $arr_response['msg']    = 'Enterprise users details not available';
        $arr_response['data']    = $arr_enterprise_users;
        return $arr_response;
    }

    public function store_enterprise_user($request)
    {   
        $arr_data = $arr_rules = $arr_response = array();

        try
        {
            $user_id = validate_user_login_id();
            if ($user_id == 0) 
            {
                $arr_response['status'] = 'error';
                $arr_response['msg']    = 'Invalid user token';
                $arr_response['data']    = [];
                return $arr_response;
            }    

            $arr_rules['first_name']   = "required";
            $arr_rules['last_name']    = "required";
            $arr_rules['email']        = "required";
            $arr_rules['address']      = "required";
            $arr_rules['mobile_no']    = "required";
            $arr_rules['new_password'] = "required";
            
            $validator = Validator::make($request->all(),$arr_rules);

            if($validator->fails())
            {
                $arr_response['status'] = 'error';
                $arr_response['msg']    = 'Please fill all the required field';
                $arr_response['data']   = [];
                return $arr_response;
            }

            $first_name        = $request->input('first_name');
            $last_name         = $request->input('last_name');
            $dob               = date('Y-m-d',strtotime($request->input('dob')));   
            $address           = $request->input('address');
            $city_name         = $request->input('city_name');
            $state_name        = $request->input('state_name');
            $post_code         = $request->input('post_code');
            $country_name      = $request->input('country_name');
            $latitude          = $request->input('lat');
            $longitude         = $request->input('long');
            $country_code      = trim($request->input('country_code'));
            $mobile_no         = trim(str_replace(' ','',$request->input('mobile_no')));
            $email             = trim($request->input('email'));
            $password          = $request->input('new_password');
            $user_type         = 'ENTERPRISE_USER';

            $is_email_duplicate = $this->UserModel
                                            ->where('email',$email)
                                            ->first();

            if($is_email_duplicate)
            {
                $arr_response['status'] = 'existing_account';
                $arr_response['msg']    = 'This email address already exists';
                $arr_response['data']   = [];
                return $arr_response;
            }

            $is_mobile_no_duplicate = $this->UserModel
                                            ->where('mobile_no',$mobile_no)
                                            ->first();

            if($is_mobile_no_duplicate)
            {
                $arr_response['status'] = 'existing_account';
                $arr_response['msg']    = 'This mobile number already exists';
                $arr_response['data']   = [];
                return $arr_response;              
            }
            
            $arr_login_user_details = get_login_user_details();
            $company_id = isset($arr_login_user_details['id']) ? $arr_login_user_details['id'] : 0;

            $arr_data['first_name']     = $first_name;
            $arr_data['last_name']      = $last_name;
            
            if($request->has('dob') && $request->input('dob')!='')
            {
                $arr_data['dob']            = $dob;
            }
            $arr_data['company_id']     = $company_id;
            $arr_data['address']        = $address;
            $arr_data['city_name']      = $city_name;
            $arr_data['state_name']     = $state_name;
            $arr_data['post_code']      = $post_code;
            $arr_data['country_name']   = $country_name;
            $arr_data['latitude']       = $latitude;
            $arr_data['longitude']      = $longitude;
            $arr_data['email']          = $email;
            $arr_data['password']       = $password;
            $arr_data['mobile_no']      = $mobile_no;
            $arr_data['country_code']   = $country_code;
            $arr_data['is_active']      = 1;
            $arr_data['is_company_user']= 1;
            $arr_data['user_type']      = $user_type;
            
            $generated_otp    = $this->generate_otp();
            $arr_data['otp']  = $generated_otp;

            $obj_user = Sentinel::registerAndActivate($arr_data);
            
            if($obj_user)
            { 

                $role = Sentinel::findRoleBySlug('enterprise_user');
                $obj_user->roles()->attach($role);

                $arr_data['company_name'] = isset($arr_login_user_details['company_name']) ? $arr_login_user_details['company_name'] : '';

                /*send notification to admin*/
                $arr_notification_data = $this->built_admin_notification_data($arr_data); 
                $this->NotificationsService->store_notification($arr_notification_data);

                /*send notification to enterprise admin*/
                $arr_enterprise_admin_notification_data = $this->built_enterprise_admin_notification_data($arr_data); 
                $this->NotificationsService->store_notification($arr_enterprise_admin_notification_data);

                /*send email to enterprise user*/
                $arr_mail_data = $this->built_enterprise_user_mail_data($arr_data); 
                $this->EmailService->send_mail($arr_mail_data);

                $arr_response['status'] = 'success';
                $arr_response['msg']    = 'Enterprise user details store successfully.';
                $arr_response['data']   = [];
                return $arr_response;
            }
            else
            {

                $arr_response['status'] = 'error';
                $arr_response['msg']    = 'Problem occurred while storing details';
                $arr_response['data']   = [];
                return $arr_response;
            }

            $arr_response['status'] = 'error';
            $arr_response['msg']    = 'Problem occurred while storing details';
            $arr_response['data']   = [];
            return $arr_response;
        }
        catch (\Exception $e) 
        {
            $arr_response['status'] = 'error';
            $arr_response['msg']    = $e->getMessage();
            $arr_response['data']   = [];
            return $arr_response;
        }
    }

    public function update_enterprise_user($request)
    {   
        $arr_data = $arr_rules = $arr_response = array();

        try
        {
            $user_id = validate_user_login_id();
            if ($user_id == 0) 
            {
                $arr_response['status'] = 'error';
                $arr_response['msg']    = 'Invalid user token';
                $arr_response['data']    = [];
                return $arr_response;
            }    

            $arr_rules['first_name']   = "required";
            $arr_rules['last_name']    = "required";
            $arr_rules['email']        = "required";
            $arr_rules['address']      = "required";
            $arr_rules['mobile_no']    = "required";
            
            $validator = Validator::make($request->all(),$arr_rules);

            if($validator->fails())
            {
                $arr_response['status'] = 'error';
                $arr_response['msg']    = 'Please fill all the required field';
                $arr_response['data']   = [];
                return $arr_response;
            }

            $enc_id = base64_decode($request->input('enc_id'));
            if ($enc_id == '') 
            {
                $arr_response['status'] = 'error';
                $arr_response['msg']    = 'Enterprise User identifier is missing,unable to process request.';
                $arr_response['data']    = [];
                return $arr_response;
            } 

            $first_name        = $request->input('first_name');
            $last_name         = $request->input('last_name');
            $dob               = date('Y-m-d',strtotime($request->input('dob')));   
            $address           = $request->input('address');
            $city_name         = $request->input('city_name');
            $state_name        = $request->input('state_name');
            $post_code         = $request->input('post_code');
            $country_name      = $request->input('country_name');
            $latitude          = $request->input('lat');
            $longitude         = $request->input('long');
            $country_code      = trim($request->input('country_code'));
            $mobile_no         = trim(str_replace(' ','',$request->input('mobile_no')));
            $email             = trim($request->input('email'));

            $is_email_duplicate = $this->UserModel
                                            ->where('email',$email)
                                            ->where('id','!=',$enc_id)
                                            ->first();

            if($is_email_duplicate)
            {
                $arr_response['status'] = 'existing_account';
                $arr_response['msg']    = 'This email address already exists';
                $arr_response['data']   = [];
                return $arr_response;
            }

            $is_mobile_no_duplicate = $this->UserModel
                                            ->where('mobile_no',$mobile_no)
                                            ->where('id','!=',$enc_id)
                                            ->first();

            if($is_mobile_no_duplicate)
            {
                $arr_response['status'] = 'existing_account';
                $arr_response['msg']    = 'This mobile number already exists';
                $arr_response['data']   = [];
                return $arr_response;              
            }
            
            $arr_update = [];
            $arr_update['first_name']     = $first_name;
            $arr_update['last_name']      = $last_name;
            if($request->has('dob') && $request->input('dob')!='')
            {
                $arr_update['dob']            = $dob;
            }
            $arr_update['email']          = $email;
            $arr_update['mobile_no']      = $mobile_no;
            $arr_update['country_code']   = $country_code;
            $arr_update['address']        = $address;
            $arr_update['city_name']      = $city_name;
            $arr_update['state_name']     = $state_name;
            $arr_update['post_code']      = $post_code;
            $arr_update['country_name']   = $country_name;
            $arr_update['latitude']       = $latitude;
            $arr_update['longitude']      = $longitude;

            $obj_status = $this->UserModel
                                    ->where('id',$enc_id)
                                    ->update($arr_update);
            
            if($obj_status)
            { 
                $arr_response['status'] = 'success';
                $arr_response['msg']    = 'Enterprise user details updated successfully.';
                $arr_response['data']   = [];
                return $arr_response;
            }
            else
            {

                $arr_response['status'] = 'error';
                $arr_response['msg']    = 'Problem occurred while updating enterprise user details';
                $arr_response['data']   = [];
                return $arr_response;
            }

            $arr_response['status'] = 'error';
            $arr_response['msg']    = 'Problem occurred while updating enterprise use details';
            $arr_response['data']   = [];
            return $arr_response;
        }
        catch (\Exception $e) 
        {
            $arr_response['status'] = 'error';
            $arr_response['msg']    = $e->getMessage();
            $arr_response['data']   = [];
            return $arr_response;
        }
    }
    
    public function change_status($request)
    {
        $user_id = validate_user_login_id();
        if ($user_id == 0) 
        {
            $arr_response['status'] = 'error';
            $arr_response['msg']    = 'Invalid user token';
            $arr_response['data']    = [];
            return $arr_response;
        }   

        $enc_id = base64_decode($request->input('enc_id'));
        if ($enc_id == '') 
        {
            $arr_response['status'] = 'error';
            $arr_response['msg']    = 'Enterprise User identifier is missing,unable to process request.';
            $arr_response['data']    = [];
            return $arr_response;
        } 
        
        $obj_enterprise_users = $this->UserModel
                                            ->select('id','is_active')
                                            ->whereHas('roles',function($query){
                                                $query->where('slug','enterprise_user');
                                            })
                                            ->where('id',$enc_id)
                                            ->first();

        if(isset($obj_enterprise_users->is_active))
        {
            $msg = '';
            $is_update = false;
            if($obj_enterprise_users->is_active == '1'){
                $obj_enterprise_users->is_active = '0';
                $status = $obj_enterprise_users->save();
                if($status){
                    $is_update = true;
                    $msg = 'Enterprise user record deactivated successfully.';
                }else{
                    $msg = 'Problem occurred while Enterprise user deactivation.';
                }
            }
            else if($obj_enterprise_users->is_active == '0'){
                $obj_enterprise_users->is_active = '1';
                $status = $obj_enterprise_users->save();
                if($status){
                    $is_update = true;
                    $msg = 'Enterprise user record activated successfully.';
                }else{
                    $msg = 'Problem occurred while Enterprise user activation.';
                }
            }

            if($is_update){
                $arr_response['status'] = 'success';
                $arr_response['msg']    = $msg;
                $arr_response['data']   = [];
                return $arr_response;
            }
            $arr_response['status'] = 'error';
            $arr_response['msg']    = $msg;
            $arr_response['data']   = [];
            return $arr_response;
        }
        $arr_response['status'] = 'error';
        $arr_response['msg']    = 'Problem occurred,while changing enterprise user status,Please try again.';
        $arr_response['data']   = [];
        return $arr_response;
    }

    private function generate_otp()
    {
        $str1 = "0123456789";
        $str2 = str_shuffle($str1);
        return substr($str2,0,4); 
    }

    private function built_admin_notification_data($arr_data)
    {
        $arr_notification = [];
        if(isset($arr_data) && sizeof($arr_data)>0)
        {
            $company_name = isset($arr_data['company_name']) ? $arr_data['company_name'] :'';
            
            $first_name = isset($arr_data['first_name']) ? $arr_data['first_name'] :'';
            $last_name  = isset($arr_data['last_name']) ? $arr_data['last_name'] :'';
            $full_name  = $first_name.' '.$last_name;
            $full_name  = ($full_name!=' ') ? $full_name : '-';

            $arr_notification['user_id']           = $this->CommonDataService->get_admin_id();
            $arr_notification['is_read']           = 0;
            $arr_notification['is_show']           = 0;
            $arr_notification['user_type']         = 'ADMIN';
            $arr_notification['notification_type'] = 'Enterprise User Registration';
            $arr_notification['title']             = $full_name.' register as a Enterprise user by '.$company_name.' on '.config('app.project.name').'.';
            $arr_notification['view_url']          = '/'.config('app.project.admin_panel_slug')."/users?type=?ssssenterprise_user";
        }
        return $arr_notification;
    }
    
    private function built_enterprise_admin_notification_data($arr_data)
    {
        $arr_notification = [];
        if(isset($arr_data) && sizeof($arr_data)>0)
        {
            $company_id = isset($arr_data['company_id']) ? $arr_data['company_id'] :0;
            
            $first_name = isset($arr_data['first_name']) ? $arr_data['first_name'] :'';
            $last_name  = isset($arr_data['last_name']) ? $arr_data['last_name'] :'';
            $full_name  = $first_name.' '.$last_name;
            $full_name  = ($full_name!=' ') ? $full_name : '-';

            $arr_notification['user_id']           = $company_id;
            $arr_notification['is_read']           = 0;
            $arr_notification['is_show']           = 0;
            $arr_notification['user_type']         = 'ENTERPRISE_ADMIN';
            $arr_notification['notification_type'] = 'Enterprise User Registration';
            $arr_notification['title']             = $full_name.' register as a Enterprise user on '.config('app.project.name').'.';
            $arr_notification['view_url']          = '';
        }
        return $arr_notification;
    }
    
    private function built_enterprise_user_mail_data($arr_data)
    {
        if(isset($arr_data) && sizeof($arr_data)>0)
        {
            $first_name = isset($arr_data['first_name']) ? $arr_data['first_name'] :'';
            $last_name  = isset($arr_data['last_name']) ? $arr_data['last_name'] :'';
            $full_name  = $first_name.' '.$last_name;
            $full_name  = ($full_name!=' ') ? $full_name : '';

            $arr_built_content = [
                                  'FULL_NAME'    => $full_name,
                                  'COMPANY_NAME' => isset($arr_data['company_name']) ? $arr_data['company_name'] : '',
                                  'EMAIL'        => isset($arr_data['email']) ? $arr_data['email'] : '',
                                  'MOBILE_NO'    => isset($arr_data['mobile_no']) ? $arr_data['mobile_no'] : '',
                                  'PASSWORD'     => $arr_data['password'],
                                  'PROJECT_NAME' => config('app.project.name')];

            $arr_mail_data                      = [];
            $arr_mail_data['email_template_id'] = '22';
            $arr_mail_data['arr_built_content'] = $arr_built_content;
            $arr_mail_data['user']              = $arr_data;

            return $arr_mail_data;
        }
        return FALSE;
    }
}
?>