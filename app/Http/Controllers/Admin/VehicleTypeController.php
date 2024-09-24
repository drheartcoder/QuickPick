<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\VehicleTypeModel;

use App\Common\Traits\MultiActionTrait;

use Flash;
use Validator;



class VehicleTypeController extends Controller
{
	use MultiActionTrait;

    public function __construct(VehicleTypeModel $vehicletype)

    {
        $this->VehicleTypeModel             = $vehicletype;
        $this->BaseModel                    = $this->VehicleTypeModel;
        $this->arr_view_data                = [];
        $this->module_title                 = "Vehicle Type";
        $this->module_view_folder           = "admin.vehicle_type";
        $this->theme_color                  = theme_color();
        $this->admin_panel_slug             = config('app.project.admin_panel_slug');
        $this->module_url_path              = url(config('app.project.admin_panel_slug')."/vehicle_type");
    }

    public function index(Request $request)
    {   
    	$arr_vehicle_type = [];
        $obj_vehicle_type = $this->VehicleTypeModel
        								->orderBy('id','ASC')
                                		->get();

        if($obj_vehicle_type)
    	{
    		$arr_vehicle_type = $obj_vehicle_type->toArray();
    	}

        $this->arr_view_data['page_title']       = "Manage ".str_plural($this->module_title);
        $this->arr_view_data['module_title']     = str_plural($this->module_title);
        $this->arr_view_data['module_url_path']  = $this->module_url_path;
        $this->arr_view_data['theme_color']      = $this->theme_color;
        $this->arr_view_data['arr_vehicle_type'] = $arr_vehicle_type;

        return view($this->module_view_folder.'.index', $this->arr_view_data);                        		

    }

    public function create()
    {
        $this->arr_view_data['page_title']        = "Create ".str_singular( $this->module_title);
        $this->arr_view_data['module_title']      = str_plural($this->module_title);
        $this->arr_view_data['module_url_path']   = $this->module_url_path;
        $this->arr_view_data['theme_color']       = $this->theme_color;

        return view($this->module_view_folder.'.create',$this->arr_view_data);
    }

    public function store(Request $request)
    {
    	$arr_rules = [];

        $arr_rules['vehicle_type']            = "required";
        $arr_rules['starting_price']          = "required";
        $arr_rules['per_miles_price']         = "required";
        $arr_rules['per_minute_price']        = "required";
        $arr_rules['minimum_price']           = "required";
        $arr_rules['cancellation_base_price'] = "required";
        $arr_rules['no_of_pallet']            = "required";
        $arr_rules['is_usdot_required']       = "required";
        $arr_rules['is_mcdoc_required']       = "required";
        $arr_rules['vehicle_min_length']      = "required";
        $arr_rules['vehicle_max_length']      = "required";
        $arr_rules['vehicle_min_height']      = "required";
        $arr_rules['vehicle_max_height']      = "required";
        $arr_rules['vehicle_min_breadth']     = "required";
        $arr_rules['vehicle_max_breadth']     = "required";
        $arr_rules['vehicle_min_weight']      = "required";
        $arr_rules['vehicle_max_weight']      = "required";
        $arr_rules['vehicle_min_volume']      = "required";
        $arr_rules['vehicle_max_volume']      = "required";

        $validator = Validator::make($request->all(),$arr_rules);
        
        if($validator->fails())
        {
            return redirect()->back()->withErrors($validator)->withInput($request->all());
        }

        $is_exist = $this->VehicleTypeModel->where('vehicle_type_slug',str_slug(strtolower($request->input('vehicle_type'))))->count();
        
        if($is_exist>0)
        {
            Flash::error(str_singular($this->module_title).' with this name already exists.Cannot add duplicate vehicle type');
            return redirect()->back();
        }

        $arr_insert                            = [];
        $arr_insert['vehicle_type_slug']       = str_slug(strtolower($request->input('vehicle_type')));
        $arr_insert['vehicle_type']            = trim($request->input('vehicle_type'));
        $arr_insert['starting_price']          = floatval($request->input('starting_price'));
        $arr_insert['per_miles_price']         = floatval($request->input('per_miles_price'));
        $arr_insert['per_minute_price']        = floatval($request->input('per_minute_price'));
        $arr_insert['minimum_price']           = floatval($request->input('minimum_price'));
        $arr_insert['cancellation_base_price'] = floatval($request->input('cancellation_base_price'));
        $arr_insert['no_of_pallet']            = intval($request->input('no_of_pallet'));
        $arr_insert['is_usdot_required']       = $request->input('is_usdot_required');
        $arr_insert['is_mcdoc_required']       = $request->input('is_mcdoc_required');
        $arr_insert['vehicle_min_length']      = doubleval($request->input('vehicle_min_length'));
        $arr_insert['vehicle_max_length']      = doubleval($request->input('vehicle_max_length'));
        $arr_insert['vehicle_min_height']      = doubleval($request->input('vehicle_min_height'));
        $arr_insert['vehicle_max_height']      = doubleval($request->input('vehicle_max_height'));
        $arr_insert['vehicle_min_breadth']     = doubleval($request->input('vehicle_min_breadth'));
        $arr_insert['vehicle_max_breadth']     = doubleval($request->input('vehicle_max_breadth'));
        $arr_insert['vehicle_min_weight']      = doubleval($request->input('vehicle_min_weight'));
        $arr_insert['vehicle_max_weight']      = doubleval($request->input('vehicle_max_weight'));
        $arr_insert['vehicle_min_volume']      = doubleval($request->input('vehicle_min_volume'));
        $arr_insert['vehicle_max_volume']      = doubleval($request->input('vehicle_max_volume'));
        $arr_insert['is_active']               = 1;

        $status = $this->VehicleTypeModel->create($arr_insert);

		if($status)
        {
            Flash::success(str_singular($this->module_title).' Created Successfully');
            return redirect()->back();
        }
        else
        {
            Flash::error('Problem Occurred, While Creating '.str_singular($this->module_title));
        }

        return redirect()->back();
    }

    public function edit($enc_id)
    {
        $arr_data = [];
        $id = base64_decode($enc_id);

        $obj_user = $this->VehicleTypeModel	
                                ->where('id',$id)
                                ->first();
        if($obj_user)
        {
            $arr_data = $obj_user->toArray();
        }
        
        $this->arr_view_data['edit_mode']                    = TRUE;
        $this->arr_view_data['page_title']                   = "Edit ".str_singular($this->module_title);
        $this->arr_view_data['module_title']                 = str_plural($this->module_title);
        $this->arr_view_data['theme_color']                  = $this->theme_color;
        $this->arr_view_data['module_url_path']              = $this->module_url_path;
        $this->arr_view_data['enc_id']                       = $enc_id;
        $this->arr_view_data['arr_data']                     = $arr_data;
        return view($this->module_view_folder.'.edit', $this->arr_view_data);    
    }
	
	public function update(Request $request)
    {
        $arr_rules = [];

        $arr_rules['vehicle_type']            = "required";
        $arr_rules['starting_price']          = "required";
        $arr_rules['per_miles_price']         = "required";
        $arr_rules['per_minute_price']        = "required";
        $arr_rules['minimum_price']           = "required";
        $arr_rules['cancellation_base_price'] = "required";
        $arr_rules['no_of_pallet']            = "required";
        $arr_rules['is_usdot_required']       = "required";
        $arr_rules['is_mcdoc_required']       = "required";
        $arr_rules['vehicle_min_length']      = "required";
        $arr_rules['vehicle_max_length']      = "required";
        $arr_rules['vehicle_min_height']      = "required";
        $arr_rules['vehicle_max_height']      = "required";
        $arr_rules['vehicle_min_breadth']     = "required";
        $arr_rules['vehicle_max_breadth']     = "required";
        $arr_rules['vehicle_min_weight']      = "required";
        $arr_rules['vehicle_max_weight']      = "required";
        $arr_rules['vehicle_min_volume']      = "required";
        $arr_rules['vehicle_max_volume']      = "required";


        $validator = Validator::make($request->all(),$arr_rules);
        
        if($validator->fails())
        {
            return redirect()->back()->withErrors($validator)->withInput($request->all());
        }
        $enc_id = base64_decode($request->input('enc_id'));
        
        $is_exist = $this->VehicleTypeModel
                                    ->where('id','!=',$enc_id)
                                    ->where('vehicle_type_slug',str_slug(strtolower($request->input('vehicle_type'))))
                                    ->count();
        if($is_exist>0)
        {
            Flash::error(str_singular($this->module_title).' with this name already exists.Cannot add duplicate vehicle type');
            return redirect()->back();
        }

        $arr_update                            = [];
        $arr_update['vehicle_type_slug']       = str_slug(strtolower($request->input('vehicle_type')));
        $arr_update['vehicle_type']            = trim($request->input('vehicle_type'));
        $arr_update['starting_price']          = floatval($request->input('starting_price'));
        $arr_update['per_miles_price']         = floatval($request->input('per_miles_price'));
        $arr_update['per_minute_price']        = floatval($request->input('per_minute_price'));
        $arr_update['minimum_price']           = floatval($request->input('minimum_price'));
        $arr_update['cancellation_base_price'] = floatval($request->input('cancellation_base_price'));
        $arr_update['no_of_pallet']            = intval($request->input('no_of_pallet'));
        $arr_update['is_usdot_required']       = $request->input('is_usdot_required');
        $arr_update['is_mcdoc_required']       = $request->input('is_mcdoc_required');
        $arr_update['vehicle_min_length']      = doubleval($request->input('vehicle_min_length'));
        $arr_update['vehicle_max_length']      = doubleval($request->input('vehicle_max_length'));
        $arr_update['vehicle_min_height']      = doubleval($request->input('vehicle_min_height'));
        $arr_update['vehicle_max_height']      = doubleval($request->input('vehicle_max_height'));
        $arr_update['vehicle_min_breadth']     = doubleval($request->input('vehicle_min_breadth'));
        $arr_update['vehicle_max_breadth']     = doubleval($request->input('vehicle_max_breadth'));
        $arr_update['vehicle_min_weight']      = doubleval($request->input('vehicle_min_weight'));
        $arr_update['vehicle_max_weight']      = doubleval($request->input('vehicle_max_weight'));
        $arr_update['vehicle_min_volume']      = doubleval($request->input('vehicle_min_volume'));
        $arr_update['vehicle_max_volume']      = doubleval($request->input('vehicle_max_volume'));

        $status = $this->VehicleTypeModel->where('id',$enc_id)->update($arr_update);
        if($status)
        {   
            Flash::success(str_singular($this->module_title).' Updated Successfully');
        }
        else
        {
            Flash::error('Problem Occurred, While Updating '.str_singular($this->module_title));
        }
        return redirect()->back();
    }
}
