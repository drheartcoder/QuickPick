<?php

namespace App\Http\Middleware\Admin;

use Closure;
use Sentinel;
use Session;
use Flash;

class AuthenticateMiddleware
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
        $arr_except = array();

        $admin_path = config('app.project.admin_panel_slug');

        $arr_except[] =  $admin_path;
        $arr_except[] =  $admin_path.'/login';
        $arr_except[] =  $admin_path.'/process_login';
        $arr_except[] =  $admin_path.'/forgot_password';
        $arr_except[] =  $admin_path.'/process_forgot_password';
        $arr_except[] =  $admin_path.'/validate_admin_reset_password_link';
        $arr_except[] =  $admin_path.'/reset_password';

        $profile_image_public_img_path = url('/').config('app.project.img_path.user_profile_images');
        /*-----------------------------------------------------------------
            Code for {enc_id} or {extra_code} in url
        ------------------------------------------------------------------*/
        $request_path = $request->route()->getCompiled()->getStaticPrefix();
        $request_path = substr($request_path,1,strlen($request_path));
        
        /*-----------------------------------------------------------------
                End
        -----------------------------------------------------------------*/        

        if(!in_array($request_path, $arr_except))
        {
            $user = Sentinel::check();
            if($user)
            {

                /*if($user->inRole(config('app.project.role_slug.admin_role_slug')))
                {
                    return $next($request);    
                }*/
                
                /*if($user->inRole(config('app.project.role_slug.subadmin_role_slug')))
                {   
                    if($user->is_active == '1')
                    {
                        return $next($request);    
                    }
                    else
                    {
                        Sentinel::logout();
                        Session::flush();
                        Flash::error('Your Account Blocked By Admin.');
                        return redirect(url(config('app.project.admin_panel_slug')));
                    }
                }*/

                /*if($user->inRole(config('app.project.role_slug.company_role_slug')))
                {
                    if($user->is_active == '1')
                    {
                        return $next($request);    
                    }
                    else
                    {
                        Sentinel::logout();
                        Session::flush();
                        Flash::error('Your Account Blocked By Admin.');
                        return redirect(url(config('app.project.admin_panel_slug')));
                    }
                }
                else
                {   
                    Sentinel::logout();
                    Session::flush();   
                    Flash::error('Not Sufficient Privileges');
                    return redirect(url(config('app.project.admin_panel_slug')));
                } */



                if($user->inRole('admin'))
                {
                    $arr_data = $user->toArray();
                    view()->share('arr_auth_user',$arr_data);
                    view()->share('profile_image_public_img_path',$profile_image_public_img_path);
                    return $next($request);    
                }
                else
                {
                    return redirect('/admin');
                }    
            }
            else
            {
                return redirect('/admin');
            }
            
        }
        else
        {
            return $next($request); 
        }
    }

   
}
