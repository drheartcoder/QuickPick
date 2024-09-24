<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\SubscribersModel;

use App\Common\Traits\MultiActionTrait;
use Flash;

class SubscriberController extends Controller
{
    use MultiActionTrait;

	public function __construct(SubscribersModel $subscriber)  
	{
        $this->arr_view_data 		= [];
		$this->SubscribersModel 	= $subscriber;

        $this->BaseModel            = $this->SubscribersModel;

		$this->module_url_path 		= url(config('app.project.admin_panel_slug')."/subscriber");
        $this->module_view_folder   = "admin.subscriber";
        $this->module_title         = "Subscriber";
        $this->theme_color          = theme_color();
	}

	public function index() 
	{	
		$arr_data = array();
		$obj_data = $this->BaseModel->orderBy('id','ASC')->get();

		if($obj_data != FALSE)
		{
			$arr_data = $obj_data->toArray();
		}

		$this->arr_view_data['arr_data']         = $arr_data;
		$this->arr_view_data['page_title'] 		 = "Manage ".str_plural($this->module_title);
		$this->arr_view_data['module_title'] 	 = str_plural($this->module_title);
		$this->arr_view_data['module_url_path']  = $this->module_url_path;

        return view($this->module_view_folder.'.index',$this->arr_view_data);
	}
}