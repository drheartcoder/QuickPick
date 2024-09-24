<?php

namespace App\Http\Controllers\Company;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\CompanyCommissionModel;

use Validator;
use Flash;
use Sentinel;

class CompanyCommissionController extends Controller
{

	public function __construct(CompanyCommissionModel $company_commission)
	{
		
		$this->CompanyCommissionModel    = $company_commission;
		$this->BaseModel               = $this->CompanyCommissionModel;
		
		$this->arr_view_data           = [];
		$this->module_url_path         = url(config('app.project.company_panel_slug')."/company_commission");
		
		$this->module_title            = "Company Commission";
		$this->modyle_url_slug         = "Company Commission";
		$this->module_view_folder      = "company.company_commission";
		$this->theme_color             = theme_color();

		$this->company_id = 0;

        $user = Sentinel::check();
        if($user){
            $this->company_id = isset($user->id) ? $user->id :0;
        }

	}

	public function index()
	{

		$arr_data = [];   

		$obj_data =  $this->CompanyCommissionModel->where('company_id',$this->company_id)->first();

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
		$arr_data['driver_percentage']      = "required";

		$validator = Validator::make($request->all(),$arr_rules);
		if($validator->fails())
		{       
			return back()->withErrors($validator)->withInput();  
		} 

		$arr_data['driver_percentage']      = floatval($request->input('driver_percentage'));

		$result = $this->CompanyCommissionModel->where('id',$id)->update($arr_data);
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
