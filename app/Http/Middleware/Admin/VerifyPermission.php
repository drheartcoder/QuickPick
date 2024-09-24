<?php

namespace App\Http\Middleware\Admin;


use Closure;
use Sentinel;
use Session;
use Flash;

class VerifyPermission {

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @param $permission
     * @return mixed
     */
    public function handle($request, Closure $next,$permission =false)
    {
        // $obj_user = Sentinel::check();
        
        // $flag =1;

        // if($obj_user !=False)
        // {       
        //     $arr_current_user_access =  $request->user()->permissions;
            
        //     if(array_key_exists($permission, $arr_current_user_access))
        //     {
        //        $flag = 1; //return $next($request);                   
        //     }
        //     else
        //     {
        //         $flag = 0;
        //     }

        //     $explode_arr = explode('|', $permission);
        //     if($explode_arr)
        //     {
        //         foreach ($explode_arr as $key => $value)
        //         {
        //             if(array_key_exists($value, $arr_current_user_access))
        //             {
        //                $flag = 1; //return $next($request);                   
        //             }
        //             else
        //             {
        //                 $flag = 0;
        //             }
        //         }                    
        //     }
        //     if($flag==1) {
        //         return $next($request);
        //     } else {
        //         Flash::error("SORRY , You don't have access to do this action !");
        //         return redirect(url()->previous());
        //     }            
        // }
        // else
        // {
        //      Flash::error('Not Sufficient Privileges');
        //     return redirect(url('admin/login'));
        // }
       
       return $next($request);

        
        
    }
}