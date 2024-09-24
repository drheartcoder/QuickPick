<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\AssignedAreaModel;
use App\Common\Traits\MultiActionTrait;

use Validator;
use Flash;
use Session;

class AssignedAreaController extends Controller
{
	use MultiActionTrait;

	public function __construct(AssignedAreaModel $assigned_area)
	{
		$this->AssignedAreaModel      = $assigned_area;
		$this->BaseModel              = $this->AssignedAreaModel;
		$this->arr_view_data          = [];
		$this->module_title           = "Assigned Area";
		$this->module_view_folder     = "admin.assigned_area";
		$this->theme_color            = theme_color();
		$this->admin_panel_slug       = config('app.project.admin_panel_slug');
		$this->module_url_path        = url(config('app.project.admin_panel_slug')."/assigned_area");
	}

	public function index(Request $request)
	{
		$this->arr_view_data['page_title']      = "Manage ".str_plural($this->module_title);
		$this->arr_view_data['module_title']    = str_plural($this->module_title);
		$this->arr_view_data['module_url_path'] = $this->module_url_path;
		$this->arr_view_data['theme_color']     = $this->theme_color;

		$obj_data = $this->AssignedAreaModel
								->with('user_details')
								->where("user_type","driver")
								->get();
		$user_type = "driver";

		if($obj_data)
		{
			$arr_data = $obj_data->toArray();
		}
		$this->arr_view_data['user_type']    = $user_type;
		$this->arr_view_data['arr_data']     = $arr_data;
		return view($this->module_view_folder.'.index', $this->arr_view_data);
	}

	public function view_map($enc_id = FALSE)
	{
		if($enc_id=='')
		{
			redirect($this->module_url_path);
		}
		else
		{
			$id    = base64_decode($enc_id);
			$obj_areas  = $this->AssignedAreaModel
									->where('id',$id)
									->with('user_details')
									->first();
			if($obj_areas)
			{
				$arr_areas  =  $obj_areas->toArray();
			}
		}
		$this->arr_view_data['page_title']           = "Edit ".str_plural($this->module_title);
		$this->arr_view_data['module_title']         = str_plural($this->module_title);
		$this->arr_view_data['module_url_path']      = $this->module_url_path;
		$this->arr_view_data['theme_color']          = $this->theme_color;
		$this->arr_view_data['arr_restricted_areas'] = $arr_areas;

		return view($this->module_view_folder.'.view_map', $this->arr_view_data);
	}

	public function create(Request $request)
	{
		$arr_users = [];

		$this->arr_view_data['page_title']           = "Create ".str_plural($this->module_title);
		$this->arr_view_data['module_title']         = str_plural($this->module_title);
		$this->arr_view_data['module_url_path']      = $this->module_url_path;
		$this->arr_view_data['theme_color']          = $this->theme_color;
		$this->arr_view_data['arr_users']            = $arr_users;
		
		return view($this->module_view_folder.'.create', $this->arr_view_data);
	}

	public function create_existing(Request $request)
	{
		$arr_users = $arr_restricted_areas =[];
		
		$obj_restricted_areas = $this->AssignedAreaModel
									->groupBy('name')
									->get();

		if($obj_restricted_areas)
		{
			$arr_restricted_areas = $obj_restricted_areas->toArray(); 
		}   

		$this->arr_view_data['page_title']           = "Create ".str_plural($this->module_title);
		$this->arr_view_data['module_title']         = str_plural($this->module_title);
		$this->arr_view_data['module_url_path']      = $this->module_url_path;
		$this->arr_view_data['theme_color']          = $this->theme_color;
		$this->arr_view_data['arr_restricted_areas'] = $arr_restricted_areas;
		
		return view($this->module_view_folder.'.create_existing', $this->arr_view_data);
	}

	public function store(Request $request)
	{ 
		$arr_rules = [];

		$arr_rules['zone_name']    = "required";
		$arr_rules['co_ordinates'] = "required";

		$validator = Validator::make($request->all(),$arr_rules);
		
		$status       = "error";                
		if($validator->fails())
		{
			echo $status;
			return;
		}
		
		$zone_name    = $request->input('zone_name') ;
		$co_ordinates = $request->input('co_ordinates');

		$check_existance = $this->AssignedAreaModel
								->where('co-ordinates',$co_ordinates)
								->orWhere('name' ,$zone_name)
								->first();
		if($check_existance)
		{
			$status = "already_exist";
			echo $status;
			return;
		}                         

		$arr_data = array( 'co-ordinates' => $co_ordinates,
							'name'    => $zone_name 
						);
		$status = $this->AssignedAreaModel->create($arr_data);
		if($status)
		{
			$status ="success";
		}
			
		echo $status;
		exit;
	}

	public function update(Request $request)
	{
		$id = $request->input('id');
		$arr_data['co-ordinates'] = $request->input('co_ordinates');
		$arr_data['name'] = $request->input('name');
		
		if($id!="")
		{
			$result = $this->AssignedAreaModel
			->where('id',$id)
			->update($arr_data);
			if($result)
			{
				echo "success"; 
				return;           
			}
			else
			{
				echo "error";
				return;
			}
		}
		else
		{
			echo "error";
			return;
		}
	}

	public function delete_area($enc_id=FALSE)
	{
		$id = $enc_id;

		if($id!="")
		{
			$id= base64_decode($id);
			$result = $this->AssignedAreaModel
			->where('id',$id)
			->delete();
			if($result)
			{
				Flash::success("Deleted record successfully");
				return redirect()->back();
			}
			else
			{
				Flash::error("Problem occured while deleting record");
				return redirect()->back();
			}
		}
		else
		{
			Flash::error('Problem occured while deleting records');
			return redirect()->back();
		}
	}

	
}
