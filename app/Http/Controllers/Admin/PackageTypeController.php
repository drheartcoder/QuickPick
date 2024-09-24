<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\PackageTypeModel;

use App\Common\Traits\MultiActionTrait;

use Flash;
use Validator;

class PackageTypeController extends Controller
{
    use MultiActionTrait;

    public function __construct(PackageTypeModel $package_type)

    {
        $this->PackageTypeModel             = $package_type;
        $this->BaseModel                    = $this->PackageTypeModel;
        $this->arr_view_data                = [];
        $this->module_title                 = "Package Type";
        $this->module_view_folder           = "admin.package_type";
        $this->theme_color                  = theme_color();
        $this->admin_panel_slug             = config('app.project.admin_panel_slug');
        $this->module_url_path              = url(config('app.project.admin_panel_slug')."/package_type");
    }

    public function index(Request $request)
    {   
    	$arr_package_type = [];
        $obj_package_type = $this->PackageTypeModel
        								->orderBy('id','ASC')
                                		->get();

        if($obj_package_type)
    	{
    		$arr_package_type = $obj_package_type->toArray();
    	}
    	$this->arr_view_data['page_title']       = "Manage ".str_plural($this->module_title);
        $this->arr_view_data['module_title']     = str_plural($this->module_title);
        $this->arr_view_data['module_url_path']  = $this->module_url_path;
        $this->arr_view_data['theme_color']      = $this->theme_color;
        $this->arr_view_data['arr_package_type'] = $arr_package_type;

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

        $arr_rules['package_type']            = "required";
        $arr_rules['is_special_type']         = "required";
        
        $validator = Validator::make($request->all(),$arr_rules);
        
        if($validator->fails())
        {
            return redirect()->back()->withErrors($validator)->withInput($request->all());
        }

        $slug = strtoupper(str_replace(' ','_',trim($request->input('package_type'))));

        $is_exist = $this->PackageTypeModel
        						->where('slug',$slug)
        						->count();
        
        if($is_exist>0)
        {
            Flash::error(str_singular($this->module_title).' with this name already exists.Cannot add duplicate package type');
            return redirect()->back();
        }

        $arr_insert                    = [];
        $arr_insert['name']            = trim($request->input('package_type'));
        $arr_insert['slug']            = $slug;
        $arr_insert['is_special_type'] = $request->input('is_special_type');
        $arr_insert['is_active']       = 1;

        $status = $this->PackageTypeModel->create($arr_insert);

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

        $obj_user = $this->PackageTypeModel	
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

        $arr_rules['package_type']            = "required";
        $arr_rules['is_special_type']         = "required";
        
        $validator = Validator::make($request->all(),$arr_rules);
        
        if($validator->fails())
        {
            return redirect()->back()->withErrors($validator)->withInput($request->all());
        }
        $enc_id = base64_decode($request->input('enc_id'));

        $slug = strtoupper(str_replace(' ','_',trim($request->input('package_type'))));

        $is_exist = $this->PackageTypeModel
        						->where('id','!=',$enc_id)
        						->where('slug',$slug)
        						->count();
        
        if($is_exist>0)
        {
            Flash::error(str_singular($this->module_title).' with this name already exists.Cannot add duplicate package type');
            return redirect()->back();
        }

        $arr_update                    = [];
        $arr_update['name']            = trim($request->input('package_type'));
        $arr_update['slug']            = $slug;
        $arr_update['is_special_type'] = $request->input('is_special_type');


        $status = $this->PackageTypeModel->where('id',$enc_id)->update($arr_update);
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
