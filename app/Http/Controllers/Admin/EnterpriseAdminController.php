<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\RoleModel;
use App\Models\UserModel;
use App\Models\UserRoleModel;
use App\Common\Traits\MultiActionTrait;
use App\Common\Services\CommonDataService;
use App\Common\Services\EmailService;
use App\Common\Services\NotificationsService;


use DB;
use Flash;
use Validator;
use Sentinel;
use Datatables;

class EnterpriseAdminController extends Controller
{
    use MultiActionTrait;

    public function __construct(
                                    RoleModel $role,
                                    UserModel $user,
                                    UserRoleModel $user_role,
                                    CommonDataService $common_data_service,
                                    EmailService $email_service,
                                    NotificationsService $notifications_service
                                )
    {
        $this->RoleModel                    = $role;
        $this->UserModel                    = $user;
        $this->UserRoleModel                = $user_role;
        $this->BaseModel                    = $this->UserModel;
        $this->CommonDataService            = $common_data_service;
        $this->EmailService                 = $email_service;
        $this->NotificationsService         = $notifications_service;
        
        $this->arr_view_data                  = [];
        $this->module_title                   = "Enterprise Admin";
        $this->module_view_folder             = "admin.enterprise_admin";
        $this->theme_color                    = theme_color();
        $this->admin_panel_slug               = config('app.project.admin_panel_slug');
        $this->module_url_path                = url(config('app.project.admin_panel_slug')."/enterprise_admin");
        $this->user_profile_public_img_path   = url('/').config('app.project.img_path.user_profile_images');
        $this->user_profile_base_img_path     = base_path().config('app.project.img_path.user_profile_images');
        $this->enterprise_license_public_path = url('/').config('app.project.img_path.enterprise_license');
        $this->enterprise_license_base_path   = base_path().config('app.project.img_path.enterprise_license');
    }

    public function index(Request $request)
    {   
        $this->arr_view_data['page_title']      = "Manage ".str_singular($this->module_title);
        $this->arr_view_data['module_title']    = str_singular($this->module_title);
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['theme_color']     = $this->theme_color;

        return view($this->module_view_folder.'.index', $this->arr_view_data);
    }

    public function get_records(Request $request)
    {
        $obj_user        =  $this->get_enterprise_admin_details($request);

        $current_context = $this;

        $json_result     = Datatables::of($obj_user);

        $json_result     = $json_result->blacklist(['id']);
       
        $json_result     = $json_result->editColumn('enc_id',function($data) use ($current_context)
	                        {
	                            return base64_encode($data->id);
	                        })
	                        ->editColumn('build_status_btn',function($data) use ($current_context)
	                        {   
	                            if($data->is_active != null && $data->is_active == "0")
	                            {   
	                                $build_status_btn = '<a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip btn-dangers" title="Lock" href="'.$this->module_url_path.'/activate/'.base64_encode($data->id).'" 
	                                onclick="return confirm_action(this,event,\'Do you really want to activate this record ?\')" ><i class="fa fa-lock"></i></a>';
	                            }
	                            elseif($data->is_active != null && $data->is_active == "1")
	                            {
	                                $build_status_btn = '<a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip " title="Unlock" href="'.$this->module_url_path.'/deactivate/'.base64_encode($data->id).'" onclick="return confirm_action(this,event,\'Do you really want to deactivate this record ?\')" ><i class="fa fa-unlock"></i></a>';
	                            }
	                            return $build_status_btn;

	                        })
                            ->editColumn('build_action_btn',function($data) use ($current_context)
                            {
                                $view_href =  $this->module_url_path.'/view/'.base64_encode($data->id);
                                $build_view_action = '<a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip btn-delets" href="'.$view_href.'" title="View"><i class="fa fa-eye" ></i></a>';

                                $delete_href =  $this->module_url_path.'/delete/'.base64_encode($data->id);
                                $build_delete_action = '<a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip btn-delets" href="'.$delete_href.'" title="Delete" onclick="return confirm_action(this,event,\'Do you really want to delete this record ?\')"><i class="fa fa-trash" ></i></a>';

                                $edit_href =  $this->module_url_path.'/edit/'.base64_encode($data->id);
                                $build_edit_action = '<a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip btn-delets" href="'.$edit_href.'" title="Edit"><i class="fa fa-edit" ></i></a>';

                                return  $build_view_action." ".$build_edit_action." ".$build_delete_action;
                                
                            })
                            ->make(true);

        $build_result = $json_result->getData();
        
        return response()->json($build_result);
    }

    private function get_enterprise_admin_details(Request $request)
    {     
        $role                     = 'enterprise_admin';

        $user_details             = $this->BaseModel->getTable();
        $prefixed_user_details    = DB::getTablePrefix().$this->BaseModel->getTable();

        $user_role_table          = $this->UserRoleModel->getTable();
        $prefixed_user_role_table = DB::getTablePrefix().$this->UserRoleModel->getTable();

        $role_table               = $this->RoleModel->getTable();
        $prefixed_role_table      = DB::getTablePrefix().$this->RoleModel->getTable();

        $obj_user = DB::table($user_details)
                                ->select(DB::raw($prefixed_user_details.".id as id,".
                                                $prefixed_user_details.".email as email, ".
                                                  "CONCAT(".$prefixed_user_details.".country_code,' ',"
                                                          .$prefixed_user_details.".mobile_no) as contact_number,".
                                                $prefixed_user_details.".is_active as is_active, ".
                                                "CONCAT(".$prefixed_user_details.".company_name) as user_name"
                                                 ))
                                ->join($user_role_table,$user_details.'.id','=',$user_role_table.'.user_id')
                                ->join($role_table, function ($join) use($role_table,$user_role_table,$role) {
                                    $join->on($role_table.'.id', ' = ',$user_role_table.'.role_id')
                                         ->where('slug','=',$role);
                                })
                                ->whereNull($user_details.'.deleted_at')
                                ->orderBy($user_details.'.created_at','DESC');

        /* ---------------- Filtering Logic ----------------------------------*/                    

        $arr_search_column = $request->input('column_filter');
        
        if(isset($arr_search_column['q_name']) && $arr_search_column['q_name']!="")
        {
            $search_term      = $arr_search_column['q_name'];
            $obj_user = $obj_user->having('user_name','LIKE', '%'.$search_term.'%');
        }

        if(isset($arr_search_column['q_email']) && $arr_search_column['q_email']!="")
        {
            $search_term      = $arr_search_column['q_email'];
            $obj_user = $obj_user->where($user_details.'.email','LIKE', '%'.$search_term.'%');
        }
        if(isset($arr_search_column['q_contact_number']) && $arr_search_column['q_contact_number']!="")
        {
            $search_term      = $arr_search_column['q_contact_number'];
            $obj_user = $obj_user->where($user_details.'.mobile_no','LIKE', '%'.$search_term.'%');
        }
        return $obj_user;
    } 

    public function create()
    {
        $this->arr_view_data['page_title']      = "Create ".str_singular( $this->module_title);
        $this->arr_view_data['module_title']    = str_singular($this->module_title);
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['theme_color']     = $this->theme_color;
        
        return view($this->module_view_folder.'.create',$this->arr_view_data);
    }

    public function store(Request $request)
    {
    	$arr_rules                    = [];
    	$arr_rules['enterprise_name'] = "required";
    	$arr_rules['email']      	  = "required|email";
    	$arr_rules['mobile_no']  	  = "required";
    	$arr_rules['password']        = "required";
    	$arr_rules['post_code']   	  = "required";
    	$arr_rules['address']    	  = "required";

        $validator = Validator::make($request->all(),$arr_rules);
        
        if($validator->fails())
        {
            return redirect()->back()->withErrors($validator)->withInput($request->all());
        }

        /* Duplication Check */
        $is_email_duplicate = $this->UserModel
        									->where('email',trim($request->input('email')))
                                            ->count();

        if($is_email_duplicate>0)
        {
            Flash::error('User with this email address already exists.');
            return redirect()->back()->withInput($request->all());
        }

        $is_mobile_no_duplicate = $this->UserModel
        									->where('mobile_no',trim($request->input('mobile_no')))
                                            ->count();

        if($is_mobile_no_duplicate>0)
        {
            Flash::error('User with this mobile number already exists.');
            return redirect()->back()->withInput($request->all());
        }

        $arr_data                 = [];
        $arr_data['company_name'] = $request->input('enterprise_name');
        $arr_data['email']        = $request->input('email');
        $arr_data['password']     = $request->input('password');
        $arr_data['country_code'] = $request->input('country_code');
        $arr_data['mobile_no']    = $request->input('mobile_no');
        $arr_data['country_name'] = $request->input('country_name');
        $arr_data['state_name']   = $request->input('state_name');
        $arr_data['city_name']    = $request->input('city_name');
        $arr_data['address']      = $request->input('address');
        $arr_data['post_code']    = $request->input('post_code');
        $arr_data['is_active']    = 1;
        $arr_data['user_type']    = 'ENTERPRISE_ADMIN';
        
        if($request->hasFile('profile_image'))
        {
            $file_name = $request->input('profile_image');
            $file_extension = strtolower($request->file('profile_image')->getClientOriginalExtension());
            if(in_array($file_extension,['png','jpg','jpeg']))
            {
                $file_name = time().uniqid().'.'.$file_extension;
                $isUpload = $request->file('profile_image')->move($this->user_profile_base_img_path , $file_name);
                if($isUpload)
                {
                   
                }
                $arr_data['profile_image'] = $file_name;
            }
            else
            {
                Flash::error('Invalid File type, While creating '.str_singular($this->module_title));
                return redirect()->back();
            }
        }
        if($request->hasFile('enterprise_license'))
        {
            $enterprise_license = $request->input('enterprise_license');
            $file_extension = strtolower($request->file('enterprise_license')->getClientOriginalExtension());
            if(in_array($file_extension,['png','jpg','jpeg','pdf']))
            {
                $enterprise_license = time().uniqid().'.'.$file_extension;
                $isUpload = $request->file('enterprise_license')->move($this->enterprise_license_base_path , $enterprise_license);
                $arr_data['enterprise_license'] = $enterprise_license;
            }
            else
            {
                Flash::error('Invalid File type, While creating '.str_singular($this->module_title));
                return redirect()->back()->withInput($request->all());
            }
        }
        
        $obj_user = Sentinel::registerAndActivate($arr_data);
        if($obj_user)
        {
            $role = Sentinel::findRoleBySlug('enterprise_admin');
            $obj_user->roles()->attach($role);
            $obj_user->save();

            if($obj_user)
            {
                $arr_notification_data = $this->built_notification_data($arr_data); 
                $this->NotificationsService->store_notification($arr_notification_data);

                $arr_mail_data = $this->built_mail_data($arr_data); 
                $this->EmailService->send_mail($arr_mail_data);

                Flash::success(str_singular($this->module_title).' Created Successfully');
            }
            else
            {
                Flash::error('Problem Occurred, While Creating '.str_singular($this->module_title));
            }
        }
        else
        {
            Flash::error('Problem Occurred, While Creating '.str_singular($this->module_title));
        }

        return redirect()->back();
    }

    public function view($enc_id)
    {
        $enc_id = base64_decode($enc_id);
        
        $arr_user = [];

        $obj_user = $this->UserModel
                                ->where('id',$enc_id)
                                ->first();

        if($obj_user)
        {
            $arr_user = $obj_user->toArray();
        }
        
        $this->arr_view_data['page_title']      = "View ".str_singular( $this->module_title);
        $this->arr_view_data['module_title']    = str_singular($this->module_title);
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['theme_color']     = $this->theme_color;
        
        $this->arr_view_data['enterprise_license_public_path'] = $this->enterprise_license_public_path;
        $this->arr_view_data['enterprise_license_base_path']   = $this->enterprise_license_base_path;
        $this->arr_view_data['arr_user']        = $arr_user;

        return view($this->module_view_folder.'.view',$this->arr_view_data);
    }

    public function edit($enc_id)
    {
        $id = base64_decode($enc_id);

        $arr_data = [];

        $obj_user = $this->UserModel
                                ->where('id',$id)
                                ->first();
        if($obj_user)
        {
            $arr_data = $obj_user->toArray();
        }

        $this->arr_view_data['edit_mode']                    = TRUE;
        $this->arr_view_data['page_title']                   = "Edit ".str_singular($this->module_title);
        $this->arr_view_data['module_title']                 = str_singular($this->module_title);
        $this->arr_view_data['theme_color']                  = $this->theme_color;
        $this->arr_view_data['module_url_path']              = $this->module_url_path;
        $this->arr_view_data['enc_id']                       = $enc_id;
        $this->arr_view_data['arr_data']                     = $arr_data;

        $this->arr_view_data['user_profile_public_img_path'] = $this->user_profile_public_img_path;
        

        return view($this->module_view_folder.'.edit', $this->arr_view_data);    
    }

    public function update(Request $request)
    {
        $arr_rules                    = [];
        $arr_rules['enterprise_name'] = "required";
        $arr_rules['email']           = "required|email";
        $arr_rules['mobile_no']       = "required|min:10|max:15";
        $arr_rules['address']         = "required";
        $arr_rules['post_code']       = "required";

        $validator = Validator::make($request->all(),$arr_rules);
        
        if($validator->fails())
        {
            return redirect()->back()->withErrors($validator)->withInput($request->all());
        }

        $user_id = $request->input('user_id');
        
        if($user_id == '')
        {
            Flash::error('Something went wrong, cannot update details. ');
            return redirect()->back()->withInput($request->all());
        }
        
        /* Duplication Check */
        $is_email_duplicate = $this->UserModel
        									->where('email',trim($request->input('email')))
                                            ->where('id','!=',$user_id)
                                            ->count();

        if($is_email_duplicate>0)
        {
            Flash::error('User with this email address already exists.');
            return redirect()->back()->withInput($request->all());
        }

        $is_mobile_no_duplicate = $this->UserModel
        									->where('mobile_no',trim($request->input('mobile_no')))
                                            ->where('id','!=',$user_id)
                                            ->count();

        if($is_mobile_no_duplicate>0)
        {
            Flash::error('User with this mobile number already exists.');
            return redirect()->back()->withInput($request->all());
        }
        $arr_update                 = [];
        $arr_update['company_name'] = $request->input('enterprise_name');
        $arr_update['email']        = $request->input('email');
        $arr_update['country_code'] = $request->input('country_code');
        $arr_update['mobile_no']    = $request->input('mobile_no');
        $arr_update['country_name'] = $request->input('country_name');
        $arr_update['state_name']   = $request->input('state_name');
        $arr_update['city_name']    = $request->input('city_name');
        $arr_update['address']      = $request->input('address');
        $arr_update['post_code']    = $request->input('post_code');

        $file_name = '';
        $oldImage = $request->input('oldimage');
        if($request->hasFile('profile_image'))
        {
            $file_name = $request->input('profile_image');
            $file_extension = strtolower($request->file('profile_image')->getClientOriginalExtension());
            if(in_array($file_extension,['png','jpg','jpeg']))
            {
                $file_name = time().uniqid().'.'.$file_extension;
                $isUpload = $request->file('profile_image')->move($this->user_profile_base_img_path , $file_name);
                if($isUpload)
                {
                    @unlink($this->user_profile_base_img_path.$oldImage);
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

        $arr_update['profile_image'] = $file_name;
		
		if($request->hasFile('enterprise_license'))
        {
            $enterprise_license = $request->input('enterprise_license');
            $file_extension = strtolower($request->file('enterprise_license')->getClientOriginalExtension());
            if(in_array($file_extension,['png','jpg','jpeg','pdf']))
            {
                $enterprise_license = time().uniqid().'.'.$file_extension;
                $isUpload = $request->file('enterprise_license')->move($this->enterprise_license_base_path , $enterprise_license);
                $arr_update['enterprise_license'] = $enterprise_license;
            }
            else
            {
                Flash::error('Invalid File type, While creating '.str_singular($this->module_title));
                return redirect()->back()->withInput($request->all());
            }
        }
               
        $status = $this->UserModel->where('id',$user_id)->update($arr_update);
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
        $delete = $this->BaseModel->where('id',$id)->delete();
        
        if($delete)
        {
            return true;
        }

        return FALSE;
    }

    public function built_mail_data($arr_data)
    {
        if(isset($arr_data) && sizeof($arr_data)>0)
        {
            $arr_built_content = [
                                  'FIRST_NAME'       => $arr_data['enterprise_name'],
                                  'EMAIL'            => $arr_data['email'],
                                  'PASSWORD'         => $arr_data['password'],
                                  'PROJECT_NAME'     => config('app.project.name')];

            $arr_mail_data                      = [];
            $arr_mail_data['email_template_id'] = '21';
            $arr_mail_data['arr_built_content'] = $arr_built_content;
            $arr_mail_data['user']              = $arr_data;

            return $arr_mail_data;
        }
        return FALSE;
    }

    private function built_notification_data($arr_data)
    {
        $arr_notification = [];
        if(isset($arr_data) && sizeof($arr_data)>0)
        {
            $enterprise_name = isset($arr_data['enterprise_name']) ? $arr_data['enterprise_name'] :'';
            $full_name  = $enterprise_name;
            $full_name  = ($full_name!=' ') ? $full_name : '-';

            $arr_notification['user_id']           = $this->CommonDataService->get_admin_id();
            $arr_notification['is_read']           = 0;
            $arr_notification['is_show']           = 0;
            $arr_notification['user_type']         = 'ADMIN';
            $arr_notification['notification_type'] = 'Enterprise Admin Registration';
            $arr_notification['title']             = $full_name.' register as a Enterprise Admin on '.config('app.project.name').'.';
            $arr_notification['view_url']          = '/'.config('app.project.admin_panel_slug')."/enterprise_admin";
        }
        return $arr_notification;
    }
}
