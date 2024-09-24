<?php

namespace App\Http\Controllers\Company;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\NotificationsModel;

use Sentinel;
class NotificationsController extends Controller
{	
	public function __construct(NotificationsModel $NotificationModel)
	{
        $this->NotificationsModel           = $NotificationModel;
     
        $this->arr_view_data                = [];
        $this->module_title                 = "Notifications";
        $this->module_titles                = "Company Notifications";
        $this->module_view_folder           = "company.notification";
        $this->theme_color                  = theme_color();
        $this->admin_panel_slug             = config('app.project.company_panel_slug');
        $this->module_url_path              = url(config('app.project.company_panel_slug')."/");   

        $this->company_id = 0;
        $this->company_name = '';

        $user = Sentinel::check();
        if($user){
            $this->company_id   = isset($user->id) ? $user->id :0;
            $this->company_name = isset($user->company_name) ? $user->company_name :'';
        }
    } 

    public function index(Request $request)
    {   	
    
        $company_id = $this->company_id;
    	$arr_notifications = [];

    	$obj_notifications = $this->NotificationsModel
    										->where('user_id', $company_id)    										
    										->where('user_type','COMPANY')
    										->orderBy('id','DESC')
    										->get();
		if($obj_notifications)
		{
			$arr_notifications = $obj_notifications->toArray();
		}
        $this->arr_view_data['page_title']      = "Manage ".str_plural($this->module_title);
        $this->arr_view_data['module_title']    = str_plural($this->module_title);
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['arr_notification']= $arr_notifications;
        $this->arr_view_data['theme_color']     = $this->theme_color;

        return view($this->module_view_folder.'.index', $this->arr_view_data);
    }
}