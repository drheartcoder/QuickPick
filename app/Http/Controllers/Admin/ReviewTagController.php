<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Common\Traits\MultiActionTrait;

use App\Models\ReviewTagModel;

use Flash;
use Validator;



class ReviewTagController extends Controller
{
 	use MultiActionTrait;

 	public function __construct(ReviewTagModel $reviewtag)
 	{
        $this->ReviewTagModel        		= $reviewtag;
        $this->BaseModel                    = $this->ReviewTagModel;
     
        $this->arr_view_data                = [];
        $this->module_title                 = "Review Tags";
        $this->module_view_folder           = "admin.review_tag";
        $this->theme_color                  = theme_color();
        $this->admin_panel_slug             = config('app.project.admin_panel_slug');
        $this->module_url_path              = url(config('app.project.admin_panel_slug')."/review_tag");	      

        $this->review_tag_public_path 		= url('/').config('app.project.img_path.review_tag');
        $this->review_tag_base_path   		= base_path().config('app.project.img_path.review_tag');
    }

	public function index()
    {
    	$arr_review_tag = [];
        $obj_review_tag = $this->ReviewTagModel->get();

        if($obj_review_tag)
        {
            $arr_review_tag = $obj_review_tag->toArray();
        }

        $this->arr_view_data['page_title']      = "Manage ".str_plural($this->module_title);
        $this->arr_view_data['module_title']    = str_plural($this->module_title);
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['theme_color']     = $this->theme_color;
        $this->arr_view_data['arr_review_tag'] = $arr_review_tag; 
       
       return view($this->module_view_folder.'.index', $this->arr_view_data);
    }

    public function create()
    {
        $this->arr_view_data['page_title']      = "Create ".str_plural($this->module_title);
        $this->arr_view_data['module_title']    = str_plural($this->module_title);
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['theme_color']     = $this->theme_color;

        return view($this->module_view_folder.'.create', $this->arr_view_data);
    }

    public function store(Request $request)
    {
        $arr_rules = [];

        $arr_rules['tag_name'] = "required";

        $validator = Validator::make($request->all(),$arr_rules);
        
        if($validator->fails())
        {
            return redirect()->back()->withErrors($validator)->withInput($request->all());
        }

        if($request->hasFile('review_image'))
        {
            $file_name = $request->input('review_image');
            $file_extension = strtolower($request->file('review_image')->getClientOriginalExtension());
            if(in_array($file_extension,['png','jpg','jpeg']))
            {
                $file_name = time().uniqid().'.'.$file_extension;
                $isUpload = $request->file('review_image')->move($this->review_tag_base_path , $file_name);
                $review_image = $file_name;
            }
            else
            {
                Flash::error('Invalid File type, While creating '.str_singular($this->module_title));
                return redirect()->back();
            }
        }


        $arr_data               	= [];
        $arr_data['review_image'] 	= $review_image;
        $arr_data['tag_name']   	= $request->input('tag_name');
        $arr_data['is_active']  	= 1;


        $reviewtag = $this->ReviewTagModel->create($arr_data);
        if($reviewtag)      
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
        $id = base64_decode($enc_id);
        
        $obj_review_tag = $this->ReviewTagModel
                                    ->where('id', $id)
                                    ->first();

        $arr_review_tag = [];

        if($obj_review_tag)
        {
           $arr_review_tag = $obj_review_tag->toArray(); 
        }

        $this->arr_view_data['page_title']      = "Edit ".str_plural($this->module_title);
        $this->arr_view_data['module_title']    = str_plural($this->module_title);
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['theme_color']     = $this->theme_color;
        $this->arr_view_data['enc_id']          = $enc_id;
        $this->arr_view_data['arr_review_tag']  = $arr_review_tag;
        $this->arr_view_data['review_tag_public_path'] = $this->review_tag_public_path;

        return view($this->module_view_folder.'.edit', $this->arr_view_data);
    }

    public function update(Request $request, $enc_id)
    {
    	
    	$id = base64_decode($enc_id);

    	$arr_rules = [];
        $arr_rules['tag_name']    = "required";
       
        $validator = Validator::make($request->all(),$arr_rules);
        
        if($validator->fails())
        {
            return redirect()->back()->withErrors($validator)->withInput($request->all());
        }
       
        $file_name = '';
        $oldImage = $request->input('oldimage');
        if($request->hasFile('review_image'))
        {
            $file_name = $request->input('review_image');
            $file_extension = strtolower($request->file('review_image')->getClientOriginalExtension());
            if(in_array($file_extension,['png','jpg','jpeg']))
            {
                $file_name = time().uniqid().'.'.$file_extension;
                $isUpload = $request->file('review_image')->move($this->review_tag_base_path , $file_name);
                if($isUpload)
                {
                    @unlink($this->review_tag_base_path.$oldImage);
                }
            }
            else
            {
                Flash::error('Invalid File type, While creating '.str_singular($this->module_title));
                return redirect()->back();
            }
        }
        else
        {
             $file_name = $oldImage;
        }


        $arr_update                 	= [];
        $arr_update['tag_name']   		= $request->input('tag_name');
        $arr_update['review_image'] 	= $file_name;

       
        $status = $this->ReviewTagModel->where('id',$id)->update($arr_update);
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
