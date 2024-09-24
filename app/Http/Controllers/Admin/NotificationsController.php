<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\NotificationsModel;

class NotificationsController extends Controller
{	
	public function __construct(NotificationsModel $notification)
	{

        $this->NotificationsModel           = $notification;

        $this->arr_view_data                = [];
        $this->module_title                 = "Notifications";
        $this->module_titles                = "Admin Notifications";
        $this->module_view_folder           = "admin.notification";
        $this->theme_color                  = theme_color();
        $this->admin_panel_slug             = config('app.project.admin_panel_slug');
        $this->module_url_path              = url(config('app.project.admin_panel_slug')."/");       
    } 

    public function index(Request $request)
    {   	
    	$arr_notifications = [];

    	$obj_notifications = $this->NotificationsModel
    										->where('user_id', '1')    										
    										->where('user_type','ADMIN')
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