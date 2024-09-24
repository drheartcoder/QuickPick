<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\SiteSettingModel;

use Validator;
use Flash;
use Input;
use Sentinel;
 
class SiteSettingController extends Controller
{
    
    public function __construct(
                                    SiteSettingModel $siteSetting
                                )
    {
        $this->SiteSettingModel   = $siteSetting;
        $this->arr_view_data      = [];
        $this->BaseModel          = $this->SiteSettingModel;
        
        $this->module_title       = "Site Settings";
        $this->module_view_folder = "admin.site_settings";
        $this->module_url_path    = url(config('app.project.admin_panel_slug')."/site_settings");
        $this->theme_color        = theme_color();

        $this->profile_image_public_img_path = url('/').config('app.project.img_path.user_profile_images');
        $this->profile_image_base_img_path   = base_path().config('app.project.img_path.user_profile_images');
    }

    public function index()
    {
        $arr_data = array();   

        $obj_data =  $this->BaseModel->first();

        if($obj_data != FALSE)
        {
            $arr_data = $obj_data->toArray();    
        }

        $this->arr_view_data['arr_data']        = $arr_data;
        $this->arr_view_data['page_title']      = str_singular($this->module_title);
        $this->arr_view_data['module_title']    = str_plural($this->module_title);
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['theme_color']     = $this->theme_color;
        $this->arr_view_data['profile_image_public_img_path']     = $this->profile_image_public_img_path;
        return view($this->module_view_folder.'.index',$this->arr_view_data);
    }

    public function update(Request $request, $enc_id)
    {
        $id = base64_decode($enc_id);

        $arr_rules = array();

        $arr_data['site_name']            = "required";
        $arr_rules['site_email_address']  = "email|required";
        $arr_rules['site_contact_number'] = "required|min:7|max:16";  
        $arr_rules['site_address']        = "required";         
        $arr_rules['fb_url']              = "required";  
        $arr_rules['google_plus_url']     = "required"; 
        $arr_rules['twitter_url']         = "required"; 
        $arr_rules['linked_in_url']       = "required"; 
        $arr_rules['emergency_contact']   = "required"; 


        // $arr_rules['site_status']         = "required";  

        $validator = Validator::make($request->all(),$arr_rules);

        if($validator->fails())
        {       
            return back()->withErrors($validator)->withInput();  
        } 

        $oldImage = $request->input('oldimage');

        /*if($request->hasFile('image'))
        {
            $file_name = $request->input('image');
            $file_extension = strtolower($request->file('image')->getClientOriginalExtension());
            if(in_array($file_extension,['png','jpg','jpeg']))
            {
                $file_name = time().uniqid().'.'.$file_extension;
                $isUpload = $request->file('image')->move($this->profile_image_base_img_path , $file_name);
                if($isUpload)
                {
                    @unlink($this->profile_image_base_img_path.$oldImage);
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
        }*/


        // $arr_data['site_banner_image']   = $file_name;
        $arr_data['site_name']           = $request->input('site_name');
        $arr_data['site_address']        = $request->input('site_address');
        $arr_data['site_contact_number'] = $request->input('site_contact_number');
        $arr_data['meta_desc']           = $request->input('meta_desc');
        $arr_data['meta_keyword']        = $request->input('meta_keyword');
        $arr_data['site_email_address']  = $request->input('site_email_address');
        $arr_data['fb_url']              = $request->input('fb_url');
        $arr_data['google_plus_url']     = $request->input('google_plus_url');
        $arr_data['twitter_url']         = $request->input('twitter_url');
        $arr_data['linked_in_url']       = $request->input('linked_in_url');
        $arr_data['emergency_contact']       = $request->input('emergency_contact');
        
        // $arr_data['site_status']         = $request->input('site_status');

        $entity = $this->BaseModel->where('site_setting_id',$id)->update($arr_data);

        if($entity)
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
