<?php

namespace App\Http\Middleware\Company;

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
        // dd($request, $next);
        $arr_except = array();

        $company_path = config('app.project.admin_panel_slug');
        // dd($company_path);
        $arr_except[] =  $company_path;
        $arr_except[] =  $company_path.'/login';
        $arr_except[] =  $company_path.'/process_login';
        $arr_except[] =  $company_path.'/forgot_password';
        $arr_except[] =  $company_path.'/process_forgot_password';
        $arr_except[] =  $company_path.'/validate_company_reset_password_link';
        $arr_except[] =  $company_path.'/reset_password';
        // dd($arr_except, $request);
        $profile_image_public_img_path = url('/').config('app.project.img_path.user_profile_images');
        /*-----------------------------------------------------------------
            Code for {enc_id} or {extra_code} in url
        ------------------------------------------------------------------*/
        $request_path = $request->route()->getCompiled()->getStaticPrefix();
        $request_path = substr($request_path,1,strlen($request_path));
        // dd('in auth');
        /*-----------------------------------------------------------------
                End
        -----------------------------------------------------------------*/        
        // dd($arr_except);
        if(!in_array($request_path, $arr_except))
        {
            $user = Sentinel::check();
            if($user)
            {

                if($user->inRole(config('app.project.role_slug.company_role_slug')))
                {
                    return $next($request);    
                }
                else
                {   
                    Sentinel::logout();
                    Session::flush();   
                    Flash::error('Not Sufficient Privileges');
                    return redirect(url(config('app.project.admin_panel_slug')));
                }

                if($user->inRole('company'))
                {
                    $arr_data = $user->toArray();
                    view()->share('arr_auth_user',$arr_data);
                    view()->share('profile_image_public_img_path',$profile_image_public_img_path);
                    return $next($request);    
                }
                else
                {
                    Flash::error('Not Sufficient Privileges');
                    return redirect('/admin');
                }    
            }
            else
            {
                // dd('5');

                Flash::error('Not valid user');
                return redirect('/admin');
            }
            
        }else
        {
            // dd('6');

            /*Flash::error('Go with valid request');
            dd($request, $next);*/
            // dd($request);
            return $next($request); 
        }
    }

   
}
