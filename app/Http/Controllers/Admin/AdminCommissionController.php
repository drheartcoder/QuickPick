<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\AdminCommissionModel;

use Validator;
use Flash;
class AdminCommissionController extends Controller
{

	public function __construct(AdminCommissionModel $admin_commission)
	{
		
		$this->AdminCommissionModel    = $admin_commission;
		$this->BaseModel               = $this->AdminCommissionModel;
		
		$this->arr_view_data           = [];
		$this->module_url_path         = url(config('app.project.admin_panel_slug')."/admin_commission");
		
		$this->module_title            = "Admin Commission";
		$this->modyle_url_slug         = "Admin Commission";
		$this->module_view_folder      = "admin.admin_commission";
		$this->theme_color             = theme_color();

	}

	public function index()
	{

		$arr_data = [];   

		$obj_data =  $this->AdminCommissionModel->first();

		if($obj_data)
		{
			$arr_data = $obj_data->toArray();    
		}

		$this->arr_view_data['arr_data']        = $arr_data;
		$this->arr_view_data['page_title']      = str_singular($this->module_title);
		$this->arr_view_data['module_title']    = str_plural($this->module_title);
		$this->arr_view_data['module_url_path'] = $this->module_url_path;
		$this->arr_view_data['theme_color']     = $this->theme_color;
		return view($this->module_view_folder.'.index',$this->arr_view_data);
	}

	public function update(Request $request)
	{
		$id = $request->input('enc_id');
		$id = base64_decode($id);	
		
		$arr_rules                                = array();
		$arr_data['admin_driver_percentage']      = "required";
		$arr_data['individual_driver_percentage'] = "required";
		$arr_data['company_percentage']           = "required";

		$validator = Validator::make($request->all(),$arr_rules);
		if($validator->fails())
		{       
			return back()->withErrors($validator)->withInput();  
		} 

		$arr_data['admin_driver_percentage']      = floatval($request->input('admin_driver_percentage'));
		$arr_data['individual_driver_percentage'] = floatval($request->input('individual_driver_percentage'));
		$arr_data['company_percentage']           = floatval($request->input('company_percentage'));

		$result = $this->AdminCommissionModel->where('id',$id)->update($arr_data);
		if($result)
		{
			Flash::success(str_singular($this->module_title).' Updated Successfully'); 
		}
		else
		{
			Flash::error('Problem Occured, While Updating '.str_singular($this->module_title));  
		} 
		
		return redirect()->back()->withInput();
	}

}
