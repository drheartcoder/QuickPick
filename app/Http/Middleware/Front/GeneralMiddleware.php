<?php

namespace App\Http\Middleware\Front;

use Closure;
use Sentinel;
use Session;

use App\Models\SiteSettingModel;
use App\Models\StaticPageModel;

use App;
use DB;


class GeneralMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */

    public function handle($request, Closure $next )
    {
        $cache_time = 30; /* minutes */

        if(!Session::has('locale'))
        {   
           Session::put('locale', \Config::get('app.locale'));
        }

        App::setLocale(Session::get('locale'));

        view()->share('selected_lang',Session::get('locale'));

        view()->share('google_map_api_key',config('app.project.google_map_api_key'));


        $current_url_route = app()->router->getCurrentRoute()->uri();
        
        /* Site Setting*/    
        $arr_site_settings = [];
        $site_setting = SiteSettingModel::first();
       
        if($site_setting) 
        {
            $arr_site_settings = $site_setting->toArray();

            if($arr_site_settings['site_status']==0 && $request->path() != 'site_offline')
            {
                return view('site_offline');
            }
            elseif($arr_site_settings['site_status']==1 && $request->path() == 'site_offline')
            {
                return view('comming_soon');
            }
        }

        view()->share('arr_site_settings',$arr_site_settings); 

        /* Static Pages */ 
            
        $arr_static_pages = [];
        //static pages links share in footer
        $obj_static_pages = StaticPageModel::remember($cache_time)
                                            ->where('is_active',1)
                                            ->with(['translations'=>function($query) use ($cache_time)
                                            {
                                                return $query->remember($cache_time);
                                            }])
                                            ->get();
        
        if ($obj_static_pages)
        {
            $arr_tmp_static_pages = $obj_static_pages->toArray();
            
            if(isset($arr_tmp_static_pages) && sizeof($arr_tmp_static_pages))
            {
                $arr_static_pages = $arr_tmp_static_pages;
            }   
        }
        
        view()->share('arr_static_pages',$arr_static_pages); 
        view()->share('oneSignalAppId',config('app.project.one_signal_credentials.website_app_id'));
        return $next($request);
    }
}
