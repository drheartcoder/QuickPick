<?php

namespace App\Http\Middleware\Front;

use Closure;
use Flash;

class UserAuthenticateMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $obj_user = \Sentinel::check();
        
        if($obj_user!=false)
        {
            if($obj_user->inRole('user'))
            {
                if(\Request::segment(1) == 'driver' || \Request::segment(1) == 'enterprise_admin'){
                    \Flash::error('Not Sufficient Privileges');
                    return redirect()->back();
                }
                else{
                    $first_name = isset($obj_user->first_name)? $obj_user->first_name:'';
                    $last_name = isset($obj_user->last_name)? $obj_user->last_name:'';
                    view()->share('user_name',$first_name.' '.$last_name);
                    view()->share('arr_login_user_details',$this->login_user_details($request));
                    return $next($request);    
                }
            }
            else if($obj_user->inRole('driver'))
            {
                if(\Request::segment(1) == 'user' || \Request::segment(1) == 'enterprise_admin'){
                    \Flash::error('Not Sufficient Privileges');
                    return redirect()->back();
                }
                else{
                    $first_name = isset($obj_user->first_name)? $obj_user->first_name:'';
                    $last_name = isset($obj_user->last_name)? $obj_user->last_name:'';
                    view()->share('user_name',$first_name.' '.$last_name);
                    view()->share('arr_login_user_details',$this->login_user_details($request));
                    return $next($request);    
                }
            }
            else if($obj_user->inRole('enterprise_admin'))
            {
                if(\Request::segment(1) == 'user' || \Request::segment(1) == 'driver'){
                    \Flash::error('Not Sufficient Privileges');
                    return redirect()->back();
                }
                else{
                    $company_name = isset($obj_user->company_name)? $obj_user->company_name:'';
                    view()->share('user_name',$company_name);
                    view()->share('arr_login_user_details',$this->login_user_details($request));
                    return $next($request);    
                }
            }
            
            else
            {
                Flash::error('Not Sufficient Privileges');
                return redirect(url('/'));
            } 
        }
        else
        {
            return redirect(url('/'));
        } 
    }

    public function login_user_details()
    {
        $user_profile_public_img_path = url('/').config('app.project.img_path.user_profile_images');
        $user_profile_base_img_path  = base_path().config('app.project.img_path.user_profile_images');

        $arr_login_user_details = [];
        
        $arr_login_user_details['user_id']       = 0;
        $arr_login_user_details['user_type']     = '';
        $arr_login_user_details['email']         = '';
        $arr_login_user_details['mobile_no']     = '';
        $arr_login_user_details['full_name']     = '';
        $arr_login_user_details['profile_image'] = '';
        $arr_login_user_details['via_social']    = '0';
        
        $user = \Sentinel::check();
        if($user)
        {   
            $user_type                           = '';
            $arr_login_user_details['user_id']   = isset($user->id) ? $user->id :0;
            $arr_login_user_details['email']     = isset($user->email) ? $user->email :'';
            $arr_login_user_details['mobile_no'] = isset($user->mobile_no) ? $user->mobile_no :'';
            
            if(isset($user->profile_image) && $user->profile_image!='' && file_exists($user_profile_base_img_path.$user->profile_image))
            {
                $arr_login_user_details['profile_image'] = $user_profile_public_img_path.$user->profile_image;
            }
            
            $arr_login_user_details['via_social'] = isset($user->via_social) ? $user->via_social :'0';

            $first_name = isset($user->first_name) ? $user->first_name :'';
            $last_name  = isset($user->last_name) ? $user->last_name :'';
            $full_name  = $first_name.' '.$last_name;
            $full_name  = ($full_name!=' ')?$full_name : '-';

            $arr_login_user_details['full_name'] = $full_name;

            if($user->inRole('user'))
            {   
                $user_type = 'user';
            }
            else if($user->inRole('driver'))
            {   
                $user_type = 'driver';
            }
            
            $arr_login_user_details['user_type'] = $user_type;
            $arr_login_user_details['oneSignalAppId'] = config('app.project.one_signal_credentials.website_app_id');
        }
        return $arr_login_user_details;
    }
}
