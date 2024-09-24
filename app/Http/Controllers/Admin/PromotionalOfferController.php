<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\PromotionalOfferModel;
use App\Common\Traits\MultiActionTrait;

use Flash;
use Validator;

class PromotionalOfferController extends Controller
{
    use MultiActionTrait;
    public function __construct(
    								PromotionalOfferModel $promotional_offer
    							)
    {
        $this->PromotionalOfferModel 	= $promotional_offer;
        $this->BaseModel                = $this->PromotionalOfferModel;

        $this->arr_view_data                = [];
        $this->module_title                 = "Promotional Offer";
        $this->module_view_folder           = "admin.promotional_offer";
        $this->theme_color                  = theme_color();
        $this->admin_panel_slug   			= config('app.project.admin_panel_slug');
        $this->module_url_path              = url(config('app.project.admin_panel_slug')."/promotional_offer");
        $this->banner_image_public_img_path = url('/').config('app.project.img_path.banner_image');
        $this->banner_image_base_img_path   = base_path().config('app.project.img_path.banner_image');

    } 
            
    public function index(Request $request)
    {   
    	$arr_data = [];

        $obj_data = $this->PromotionalOfferModel
        								->orderBy('id','DESC')
        								->get();  
        if($obj_data)
        {
			$arr_data = $obj_data->toArray();
        }

		$this->arr_view_data['page_title']                   = "Manage ".str_plural($this->module_title);
		$this->arr_view_data['module_title']                 = str_plural($this->module_title);
		$this->arr_view_data['module_url_path']              = $this->module_url_path;
		$this->arr_view_data['theme_color']                  = $this->theme_color;
		$this->arr_view_data['arr_data']                     = $arr_data;
		$this->arr_view_data['banner_image_public_img_path'] = $this->banner_image_public_img_path;
		$this->arr_view_data['banner_image_base_img_path']   = $this->banner_image_base_img_path;

        return view($this->module_view_folder.'.index', $this->arr_view_data);
    }
    
    public function create()
    {
    	$this->arr_view_data['page_title']      = "Add ".str_plural($this->module_title);
        $this->arr_view_data['module_title']    = str_plural($this->module_title);
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['theme_color']     = $this->theme_color;

        return view($this->module_view_folder.'.create', $this->arr_view_data);
    }

    public function store(Request $request)
    {
    	$arr_rules                 = [];
		$arr_rules['banner_title'] = "required";
        
        $validator = Validator::make($request->all(),$arr_rules);
    	
    	if($validator->fails())
    	{
    		return redirect()->back()->withErrors($validator)->withInput($request->all());
    	}

        if(!$request->file('banner_image'))
        {
            Flash::error('Please upload banner image');
            return redirect()->back()->withInput($request->all());
        }

        $arr_data                           = [];
        $arr_data['banner_title']              = $request->input('banner_title');
        $arr_data['is_active']              = 1;

       	if($request->hasFile('banner_image'))
        {
            $file_name = $request->input('banner_image');
            $file_extension = strtolower($request->file('banner_image')->getClientOriginalExtension());
            if(in_array($file_extension,['png','jpg','jpeg']))
            {
                $file_name = time().uniqid().'.'.$file_extension;
                $isUpload = $request->file('banner_image')->move($this->banner_image_base_img_path , $file_name);
                if($isUpload)
                {
                   
                }
                $arr_data['banner_image'] = $file_name;
            }
            else
            {
                Flash::error('Invalid File type, While creating '.str_singular($this->module_title));
                return redirect()->back();
            }
        }

        $result = $this->PromotionalOfferModel->create($arr_data);
    	if($result)
    	{
        	Flash::success(str_singular($this->module_title).' Created Successfully');
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

        $enc_id = base64_decode($enc_id); 

        $obj_data = $this->PromotionalOfferModel
        							->where('id',$enc_id)
        							->first();  
        if($obj_data)
        {
        	$arr_data = $obj_data->toArray();
        }
        
		$this->arr_view_data['page_title']                   = "Edit ".str_plural($this->module_title);
		$this->arr_view_data['module_title']                 = str_plural($this->module_title);
		$this->arr_view_data['module_url_path']              = $this->module_url_path;
		$this->arr_view_data['theme_color']                  = $this->theme_color;
		$this->arr_view_data['arr_data']                     = $arr_data;
		$this->arr_view_data['enc_id']                       = $enc_id;
		$this->arr_view_data['banner_image_public_img_path'] = $this->banner_image_public_img_path;
		$this->arr_view_data['banner_image_base_img_path']   = $this->banner_image_base_img_path;

        return view($this->module_view_folder.'.edit', $this->arr_view_data);
    }

    public function update(Request $request)
    {
        $arr_rules                           = [];
        $arr_rules['banner_title']           = "required";

        $validator = Validator::make($request->all(),$arr_rules);
        if($validator->fails())
        {
            return redirect()->back()->withErrors($validator)->withInput($request->all());
        }

        $enc_id           = base64_decode($request->input('enc_id'));
        $old_banner_image = $request->input('old_banner_image');

        $arr_update                 = [];
        $arr_update['banner_title'] = $request->input('banner_title');
		
		if($request->hasFile('banner_image'))
        {
            $file_name = $request->input('banner_image');
            $file_extension = strtolower($request->file('banner_image')->getClientOriginalExtension());
            if(in_array($file_extension,['png','jpg','jpeg']))
            {
                $file_name = time().uniqid().'.'.$file_extension;
                $isUpload = $request->file('banner_image')->move($this->banner_image_base_img_path , $file_name);
                if($isUpload){
                	if($old_banner_image!=''){
	                   	@unlink($this->banner_image_base_img_path.$old_banner_image);
                	}
                }
                $arr_update['banner_image'] = $file_name;
            }
            else
            {
                Flash::error('Invalid File type, While creating '.str_singular($this->module_title));
                return redirect()->back();
            }
        }

        $result = $this->PromotionalOfferModel
		                        ->where('id',$enc_id)
        		                ->update($arr_update);
        if($result)
        {
            Flash::success(str_singular($this->module_title).' Updated Successfully');
        }
        else
        {
            Flash::error('Problem Occurred, While Creating '.str_singular($this->module_title));
        }

        return redirect($this->module_url_path);
    }
    
    public function multi_action(Request $request)
    {
        $arr_rules = array();
        $arr_rules['multi_action'] = "required";
        $arr_rules['checked_record'] = "required";

        $validator = Validator::make($request->all(),$arr_rules);

        if($validator->fails())
        {
            Flash::error('Please Select '.$this->module_title.' To Perform Multi Actions');
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $multi_action   = $request->input('multi_action');
        $checked_record = $request->input('checked_record');

        /* Check if array is supplied*/
        if(is_array($checked_record) && sizeof($checked_record)<=0)
        {
            Flash::error('Problem Occurred, While Doing Multi Action');
            return redirect()->back();
        }

        foreach ($checked_record as $key => $record_id) 
        {  
            if($multi_action=="delete")
            {
               $this->perform_delete(base64_decode($record_id));    
               Flash::success($this->module_title.' Deleted Successfully'); 
            } 
            elseif($multi_action=="activate")
            {
               $this->perform_activate(base64_decode($record_id)); 
               Flash::success($this->module_title.' Activated Successfully'); 
            }
            elseif($multi_action=="deactivate")
            {
               $this->perform_deactivate(base64_decode($record_id));    
               Flash::success($this->module_title.' Deactivated Successfully');  
            }
        }

        return redirect()->back();
    }
    public function delete($enc_id = FALSE)
    {
        if(!$enc_id)
        {
            return redirect()->back();
        }

        if($this->perform_delete(base64_decode($enc_id)))
        {
            Flash::success($this->module_title.' Deleted Successfully');
        }
        else
        {
            Flash::error('Problem Occured While '.$this->module_title.' Deletion ');
        }

        return redirect()->back();
    }
	public function perform_delete($id)
    {
		$delete = $this->BaseModel->where('id',$id)->first();
        if($delete)
        {
        	if(isset($delete->banner_image) && $delete->banner_image!=''){
        		if(file_exists($this->banner_image_base_img_path.$delete->banner_image)){
        			@unlink($this->banner_image_base_img_path.$delete->banner_image);
        		}
        	}
        	$delete->delete();
            return TRUE;
        }

        return FALSE;
    }
}
