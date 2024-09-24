<?php

namespace App\Http\Middleware\Admin;

use Closure;
use Session;
use Sentinel;

use App\Models\NotificationsModel;

class GeneralMiddleware
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
        Session::put('locale','en');
        view()->share('admin_panel_slug',config('app.project.admin_panel_slug'));
        view()->share('arr_current_user_access',$this->current_user_access($request));
        view()->share('arr_notifications',$this->get_notifications($request));

        view()->share('google_map_api_key',config('app.project.google_map_api_key'));
        
        view()->share('arr_login_user_details',$this->login_user_details($request));

        return $next($request);
    }
    
    public function current_user_access($request)
    {
        $data =[];
        
        $user = Sentinel::check();
        
        if($user)
        {
           $data = $request->user()->permissions;
        }
    //dd($data);
        return  $data;
    }
    public function get_notifications()
    {
        $user = Sentinel::check();
        if($user)
        {
            $user_type = '';
            $user_id = isset($user->id) ? $user->id :0;

            if($user->inRole('admin'))
            {   
                $user_type = 'ADMIN';
            }
            else if($user->inRole('sub_admin'))
            {   
                $user_type = 'SUB_ADMIN';
            }
            else if($user->inRole('company'))
            {   
                $user_type = 'COMPANY';
            }
            $arr_notifications = [];

            $obj_notifications = NotificationsModel::select('id','notification_type','title','view_url')
                                                    ->where('user_id',$user_id)
                                                    ->where('user_type',$user_type)
                                                    ->where('is_read','0')
                                                    ->orderBy('id','DESC')
                                                    ->get();
            if($obj_notifications)
            {
                $arr_notifications = $obj_notifications->toArray();
            }
            return $arr_notifications;
        }
        return [];
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

        $user = Sentinel::check();
        if($user)
        {   
            $user_type = '';
            $arr_login_user_details['user_id'] = isset($user->id) ? $user->id :0;
            $arr_login_user_details['email'] = isset($user->email) ? $user->email :'';
            $arr_login_user_details['mobile_no'] = isset($user->mobile_no) ? $user->mobile_no :'';
            
            if(isset($user->profile_image) && $user->profile_image!='' && file_exists($user_profile_base_img_path.$user->profile_image))
            {
                $arr_login_user_details['profile_image'] = $user_profile_public_img_path.$user->profile_image;
            }
            if($user->inRole('admin'))
            {   
                $user_type = 'admin';
                $arr_login_user_details['full_name']     = config('app.project.name').':Admin';
            }
            else if($user->inRole('company'))
            {   
                $user_type = 'company';
                
                $company_name = (isset($user->company_name) && $user->company_name!='') ? $user->company_name : '';
                $full_name    = 'Comapny:Admin';
                if($company_name!='')
                {
                    $full_name    = $company_name.':Admin';
                }

                $arr_login_user_details['full_name']     = $full_name;
            }
            
            $arr_login_user_details['user_type'] = $user_type;
        }
        return $arr_login_user_details;
    }
}
