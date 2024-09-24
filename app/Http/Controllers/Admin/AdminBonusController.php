<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\AdminBonusModel;

use Flash;
use Validator;




class AdminBonusController extends Controller
{
 	public function __construct(AdminBonusModel $admin_bonus)

	{
		
		$this->AdminBonusModel    			= $admin_bonus;
		$this->BaseModel               		= $this->AdminBonusModel;
        $this->arr_view_data                = [];
        $this->module_title                 = "Bonus Points";
        $this->module_view_folder           = "admin.admin_bonus";
        $this->theme_color                  = theme_color();
        $this->admin_panel_slug             = config('app.project.admin_panel_slug');
        $this->module_url_path              = url(config('app.project.admin_panel_slug')."/admin_bonus");       

	}

	public function index(Request $request)
    {   
    	$arr_data = [];   

		$obj_data =  $this->BaseModel->first();

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

		$arr_rules                          = array();
		$arr_data['referral_points']      	= "required";
		$arr_data['referral_points_price']  = "required";

		$validator = Validator::make($request->all(),$arr_rules);
		if($validator->fails())
		{       
			return back()->withErrors($validator)->withInput();  
		} 

		$arr_data['referral_points']  = $request->input('referral_points');
		$arr_data['referral_points_price'] = $request->input('referral_points_price');

		$result = $this->BaseModel->where('id',$id)->update($arr_data);
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