<?php

namespace App\Http\Controllers\Company;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\UserModel;

use Validator;
use Flash;
use Sentinel;
use Hash;
use Image;

class ProfileController extends Controller
{
    public function __construct(
                                UserModel $user
                               )
    {
        $this->UserModel                     = $user;
        $this->arr_view_data                 = [];
        $this->module_title                  = "Profile";
        $this->module_view_folder            = "company.profile";
        $this->module_icon                   = "fa-user";
        $this->theme_color                   = theme_color();
        $this->admin_url_path                = url(config('app.project.company_panel_slug'));
        $this->module_url_path               = $this->admin_url_path."/profile";
        $this->profile_image_public_img_path = url('/').config('app.project.img_path.user_profile_images');
        $this->profile_image_base_img_path   = base_path().config('app.project.img_path.user_profile_images');
    }
    public function index()
    {
        $arr_data = array();
        
        $obj_data = Sentinel::getUser();
        
        if($obj_data)
        {
           $arr_data = $obj_data->toArray();    
        }

        if(sizeof($arr_data)<=0)
        {
            return redirect($this->admin_url_path.'/login');
        }

        $this->arr_view_data['arr_data']                      = $arr_data;
        $this->arr_view_data['page_title']                    = $this->module_title;
        $this->arr_view_data['module_title']                  = $this->module_title;
        $this->arr_view_data['module_url_path']               = $this->module_url_path;
        $this->arr_view_data['theme_color']                   = $this->theme_color;
        $this->arr_view_data['module_icon']                   = $this->module_icon;
        $this->arr_view_data['profile_image_public_img_path'] = $this->profile_image_public_img_path;

        return view($this->module_view_folder.'.index',$this->arr_view_data);
    }
    public function update(Request $request)
    {

     // /   dd($request->all(), $request->hasFile('image'));
        $arr_rules = array();
        
        $arr_rules['company_name']            = "required";
        $arr_rules['phone']                 = "required|min:7|max:16";
        $arr_rules['address']               = "required";
       
        $validator = Validator::make($request->all(),$arr_rules);

        if($validator->fails())
        {       
            return redirect()->back()->withErrors($validator)->withInput();  
        }

        $id      = $request->input('enc_id');
        $user_id = base64_decode($id);

        // $exist_count = $this->UserModel
        //                         ->where('email',$request->input('email'))
        //                         ->where('id','!=',$user_id)
        //                         ->count();

        // if($exist_count>0)
        // {
        //     Flash::error('This Email id already present in our system, please try another one');
        //     return redirect()->back();
        // }
        


        $oldImage = $request->input('oldimage');

        if($request->hasFile('image'))
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
                    @unlink($this->profile_image_base_img_path.'/thumb_50X50_'.$oldImage);
                    $res= $this->attachmentThumb(file_get_contents($this->profile_image_base_img_path.$file_name), $file_name, 50, 50);
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

        $arr_data['profile_image']  = $file_name;
        $arr_data['company_name']   = $request->input('company_name');
        $arr_data['mobile_no']      = $request->input('phone');
        $arr_data['address']	    = trim($request->input('address'));

        $obj_data = $this->UserModel->where('id',$user_id)->update($arr_data);
        if($obj_data)
        {
            Flash::success(str_singular($this->module_title).' Updated Successfully'); 
        }
        else
        {
            Flash::error('Problem Occurred, While Updating '.str_singular($this->module_title));  
        } 
      
        return redirect()->back();
    }
    public function attachmentThumb($input, $name, $width, $height)
    {
        $thumb_img = Image::make($input)->resize($width,$height);
        $thumb_img->fit($width,$height, function ($constraint) {
            $constraint->upsize();
        });
        $thumb_img->save($this->profile_image_base_img_path.'/thumb_'.$width.'X'.$height.'_'.$name);         
    }
}
