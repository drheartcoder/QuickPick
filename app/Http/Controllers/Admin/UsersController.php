<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\RoleModel;
use App\Models\UserModel;
use App\Models\UserRoleModel;
use App\Models\PromoOfferModel;
use App\Models\BookingMasterModel;

use App\Common\Traits\MultiActionTrait;
use App\Common\Services\CommonDataService;
use App\Common\Services\EmailService;
use App\Common\Services\NotificationsService;


use DB;
use URL;
use Image;
use Flash;
use Validator;
use Sentinel;
use Datatables;

class UsersController extends Controller
{
    use MultiActionTrait;

    public function __construct(
                                    RoleModel $role,
                                    UserModel $user,
                                    UserRoleModel $user_role,
                                    BookingMasterModel $booking_master,
                                    PromoOfferModel $promo_offer,
                                    CommonDataService $common_data_service,
                                    EmailService $email_service,
                                    NotificationsService $notifications_service
                                )
    {
        $this->RoleModel                    = $role;
        $this->UserModel                    = $user;
        $this->UserRoleModel                = $user_role;
        $this->BookingMasterModel           = $booking_master;
        $this->BaseModel                    = $this->UserModel;
        $this->PromoOfferModel              = $promo_offer;
        $this->CommonDataService            = $common_data_service;
        $this->EmailService                 = $email_service;
        $this->NotificationsService         = $notifications_service;

        $this->arr_view_data                = [];
        $this->module_title                 = "Users";
        $this->module_view_folder           = "admin.users";
        $this->theme_color                  = theme_color();
        $this->admin_panel_slug             = config('app.project.admin_panel_slug');
        $this->module_url_path              = url(config('app.project.admin_panel_slug')."/users");
        
        $this->user_profile_public_img_path = url('/').config('app.project.img_path.user_profile_images');
        $this->user_profile_base_img_path   = base_path().config('app.project.img_path.user_profile_images');
    } 

    public function index(Request $request)
    {   
        $this->arr_view_data['page_title']      = "Manage ".str_plural($this->module_title);
        $this->arr_view_data['module_title']    = str_plural($this->module_title);
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['theme_color']     = $this->theme_color;

        return view($this->module_view_folder.'.index', $this->arr_view_data);
    }
    public function get_records(Request $request)
    {

        $arr_current_user_access =[];
        $arr_current_user_access = $request->user()->permissions;

        $obj_user        =  $this->get_normal_rider_details($request);

        $role            =  $request->input('role');

        $current_context = $this;

        $json_result     = Datatables::of($obj_user);

        $json_result     = $json_result->blacklist(['id']);
       
        // if(array_key_exists('rider.update', $arr_current_user_access))
        // {
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

                            });
        // }                    

        $json_result    = $json_result->editColumn('build_action_btn',function($data) use ($current_context)
                            {       
                                $view_href =  $this->module_url_path.'/view/'.base64_encode($data->id);
                                $build_view_action = '<a class="btn btn-warning btn-sm show-tooltip call_loader btn-delets" href="'.$view_href.'" title="View"><i class="fa fa-eye" ></i></a>';
                            })    
                            ->editColumn('build_action_btn',function($data) use ($current_context,$role,$arr_current_user_access)
                            {
                                      
                                $view_href =  $this->module_url_path.'/view/'.base64_encode($data->id);
                                $build_view_action = '<a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip btn-delets" href="'.$view_href.'" title="View"><i class="fa fa-eye" ></i></a>';

                                if(array_key_exists('rider.update', $arr_current_user_access))
                                {
                                    $edit_href =  $this->module_url_path.'/edit/'.base64_encode($data->id);
                                    $build_edit_action = '<a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip btn-delets" href="'.$edit_href.'" title="Edit"><i class="fa fa-edit" ></i></a>';
                                }
                                else
                                {
                                    $build_edit_action="";
                                }   

                                $delete_href =  $this->module_url_path.'/delete/'.base64_encode($data->id);
                                $build_delete_action = '<a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip btn-delets" href="'.$delete_href.'" title="Delete" onclick="return confirm_action(this,event,\'Do you really want to delete this record ?\')"><i class="fa fa-trash" ></i></a>';

                                $build_notification_btn = '<a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip review-stars" href="javascript:void(0);" data-user-id="'.base64_encode($data->id).'" data-user-name="'.$data->user_name.'" onclick="open_notitication_modal(this); " title="Send Notification"><i class="fa fa-bullhorn" ></i></a>';

                                // $reviews_href =  $this->module_url_path.'/review/'.base64_encode($data->id);
                                // $build_reviews_action = '<a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip review-stars" href="'.$reviews_href.'" title="View Reviews"><i class="fa fa-star"></i></a>';

                                return  $build_view_action." ".$build_edit_action." ".$build_delete_action." ".$build_notification_btn;
                                //return  $build_view_action." ".$build_edit_action." ".$build_reviews_action;
                                
                            })
                            ->make(true);

        $build_result = $json_result->getData();
        //dd($build_result);
        return response()->json($build_result);
    }
    private function get_normal_rider_details(Request $request)
    {     
        $role                     = 'user';

        $user_details             = $this->BaseModel->getTable();
        $prefixed_user_details    = DB::getTablePrefix().$this->BaseModel->getTable();

        $user_role_table          = $this->UserRoleModel->getTable();
        $prefixed_user_role_table = DB::getTablePrefix().$this->UserRoleModel->getTable();

        $role_table               = $this->RoleModel->getTable();
        $prefixed_role_table      = DB::getTablePrefix().$this->RoleModel->getTable();

        $obj_user = DB::table($user_details)
                                ->select(DB::raw($prefixed_user_details.".id as id,".
                                                 $prefixed_user_details.".email as email, ".
                                                // $prefixed_user_details.".mobile_no as contact_number, ".
                                                 "CONCAT(".$prefixed_user_details.".country_code,' ',"
                                                          .$prefixed_user_details.".mobile_no) as contact_number,".
                                                 $prefixed_user_details.".is_active as is_active, ".
                                                 "CONCAT(".$prefixed_user_details.".first_name,' ',"
                                                          .$prefixed_user_details.".last_name) as user_name"
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
        $arr_countries = $this->CommonDataService->get_countries();
        
        $this->arr_view_data['page_title']      = "Create ".str_singular( $this->module_title);
        $this->arr_view_data['module_title']    = str_plural($this->module_title);
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['theme_color']     = $this->theme_color;
        
        $this->arr_view_data['arr_countries']   = $arr_countries;

        return view($this->module_view_folder.'.create',$this->arr_view_data);
    }
    public function store(Request $request)
    {
        $arr_rules = [];

        $arr_rules['first_name'] = "required";
        $arr_rules['last_name']  = "required";
        $arr_rules['email']      = "required";
        $arr_rules['mobile_no']  = "required";
        $arr_rules['password']   = "required";
        //$arr_rules['country']    = "required";
        //$arr_rules['state']      = "required";
        //$arr_rules['city']       = "required";
        $arr_rules['address']    = "required";

        $validator = Validator::make($request->all(),$arr_rules);
        
        if($validator->fails())
        {
            return redirect()->back()->withErrors($validator)->withInput($request->all());
        }

        $arr_user_role = ['ADMIN','COMPANY','USER'];
        /* Duplication Check */
        $is_email_duplicate = $this->UserModel
                                        ->where('email',trim($request->input('email')))
                                        ->whereIn('user_type',$arr_user_role)
                                        ->count();

        
        if($is_email_duplicate>0)
        {
            Flash::error('User with this email address already exists.');
            return redirect()->back()->withInput($request->all());
        }

        $is_mobile_no_duplicate = $this->UserModel
                                            ->where('mobile_no',trim($request->input('mobile_no')))
                                            ->whereIn('user_type',$arr_user_role)
                                            ->count();

        if($is_mobile_no_duplicate>0)
        {
            Flash::error('User with this mobile number already exists.');
            return redirect()->back()->withInput($request->all());
        }

        $arr_data                   = [];
        $arr_data['first_name']     = $request->input('first_name');
        $arr_data['last_name']      = $request->input('last_name');
        $arr_data['email']          = $request->input('email');
        $arr_data['password']       = $request->input('password');
        $arr_data['country_code']   = $request->input('country_code');
        $arr_data['mobile_no']      = $request->input('mobile_no');
        $arr_data['country_name']   = $request->input('country_name');
        $arr_data['state_name']     = $request->input('state_name');
        $arr_data['city_name']      = $request->input('city_name');
        $arr_data['address']        = $request->input('address');
        $arr_data['latitude']       = $request->input('lat');
        $arr_data['longitude']      = $request->input('long');
        $arr_data['post_code']      = $request->input('post_code');
        $arr_data['user_type']      = 'USER';
        
        $reset_password_mandatory = '0';
        if($request->has('reset_password_mandatory') && $request->input('reset_password_mandatory') == 'on')
        {
            $reset_password_mandatory = '1';
        }
        $arr_data['reset_password_mandatory']       = $reset_password_mandatory;

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
        
        $obj_user = Sentinel::registerAndActivate($arr_data);
        if($obj_user)
        {
            $role = Sentinel::findRoleBySlug('user');
            $obj_user->roles()->attach($role);             
            
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

        return redirect()->back();
    }
    public function view($enc_id)
    {
        $enc_id = base64_decode($enc_id);
        
        $arr_user = [];

        $obj_user = $this->UserModel
                                //->with('country_details','state_details','city_details')
                                ->select('id','first_name','last_name','profile_image','email','mobile_no','country_code','password','country_name','state_name','city_name','address','my_points','created_at')
                                ->where('id',$enc_id)
                                ->first();
        if($obj_user)
        {
            $arr_user = $obj_user->toArray();
        }
        
        $this->arr_view_data['page_title']      = "View ".str_singular( $this->module_title);
        $this->arr_view_data['module_title']    = str_plural($this->module_title);
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['theme_color']     = $this->theme_color;
        $this->arr_view_data['arr_user']        = $arr_user;

        return view($this->module_view_folder.'.view',$this->arr_view_data);
    }
    public function edit($enc_id)
    {
        $id = base64_decode($enc_id);

        $arr_data = [];

        $obj_user = $this->UserModel
                                ->select('id','first_name','last_name','profile_image','email','country_code','mobile_no','password','country_name','state_name','city_name','address','post_code')
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
        
        $this->arr_view_data['user_profile_public_img_path'] = $this->user_profile_public_img_path;
        

        return view($this->module_view_folder.'.edit', $this->arr_view_data);    
    }
    public function update(Request $request)
    {
        $arr_rules = [];

        $arr_rules['first_name'] = "required";
        $arr_rules['last_name']  = "required";
        $arr_rules['mobile_no']  = "required";
        $arr_rules['address']    = "required";
       
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

        $email = trim($request->input('email'));
        
        /* Duplication Check */

        if($email != '') 
        {
            $is_email_duplicate = $this->UserModel
                                            ->where('email',$email)
                                            // ->where('user_type','USER')
                                            ->where('id','!=',$user_id)
                                            ->count();

            if($is_email_duplicate>0)
            {
                Flash::error('User with this email address already exists.');
                return redirect()->back()->withInput($request->all());
            }
        }

        $is_mobile_no_duplicate = $this->UserModel
                                            ->where('mobile_no',trim($request->input('phone')))
                                            // ->where('user_type','USER')
                                            ->where('id','!=',$user_id)
                                            ->count();

        if($is_mobile_no_duplicate>0)
        {
            Flash::error('User with this mobile number already exists.');
            return redirect()->back()->withInput($request->all());
        }
        
        $arr_update                 = [];
        $arr_update['first_name']   = $request->input('first_name');
        $arr_update['last_name']    = $request->input('last_name');
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
    public function reset_password($enc_id)
    {
        $user_id = base64_decode($enc_id);
        if($user_id == '')
        {
            Flash::error('User indetifier not found,Please try again');
            return redirect($this->module_url_path);
        }
        $arr_data = [];

        $obj_data = $this->UserModel
                                ->where('id',$user_id)
                                ->first();
        
        if($obj_data)
        {
            $arr_data = $obj_data->toArray();
        }       
        
        $this->arr_view_data['page_title']                   = "Reset Password ".str_singular($this->module_title);
        $this->arr_view_data['module_title']                 = str_plural($this->module_title);
        $this->arr_view_data['theme_color']                  = $this->theme_color;
        $this->arr_view_data['module_url_path']              = $this->module_url_path;
        $this->arr_view_data['enc_id']                       = $enc_id;
        $this->arr_view_data['arr_data']                     = $arr_data;
        
        return view($this->module_view_folder.'.reset_password', $this->arr_view_data);                             
    }
    public function process_reset_password(Request $request)
    {
        $arr_rules                     = array();
        // $arr_rules['enc_id']           = "required";
        $arr_rules['password']         = "required";
        $arr_rules['confirm_password'] = "required";
      
        $validator = Validator::make($request->all(),$arr_rules);
        if($validator->fails())
        {
            Flash::error('Please fill all required fields.');
            return redirect()->back()->withErrors($validator)->withInput($request->all());
        }

        $enc_id = base64_decode($request->input('enc_id'));
        
        if($enc_id == '')
        {
            Flash::error('User indetifier not found,Please try again');
            return redirect($this->module_url_path);
        }

        $password         = $request->input('password');
        $confirm_password = $request->input('confirm_password');

        $reset_password_mandatory = '0';
        if($request->has('reset_password_mandatory') && $request->input('reset_password_mandatory') == 'on')
        {
            $reset_password_mandatory = '1';
        }

        if($password != $confirm_password)
        {
            Flash::error('Password & Confirm Password must be same.');
            return redirect()->back();
        }

        if($this->check_user_current_trip($enc_id))
        {
            Flash::error('User is currently in busy in trip,cannot reset password');
            return redirect()->back();
        }
        try 
        {
            $obj_user = $this->UserModel->where('id',$enc_id)->first();
            if ($obj_user) 
            {  
                $new_credentials = [];
                $new_credentials['password'] = $request->input('password');
                if(Sentinel::update($obj_user,$new_credentials))
                {
                    if($reset_password_mandatory == '1'){
                        $obj_user->reset_password_mandatory = $reset_password_mandatory;
                        $obj_user->save();
                    }

                    $arr_data = $obj_user->toArray();

                    $arr_data['new_password'] = $request->input('password');
                    
                    $arr_mail_data = $this->built_admin_reset_password_mail_data($arr_data);
                    $this->EmailService->send_mail($arr_mail_data);
                    
                    $arr_notification_data = 
                                                [
                                                    'title'             => config('app.project.name').' Admin reset your account password.',
                                                    'notification_type' => 'RESET_PASSWORD',
                                                    'enc_user_id'       => $enc_id,
                                                    'user_type'         => 'USER',

                                                ];
                    $this->NotificationsService->send_on_signal_notification($arr_notification_data);


                    Flash::success('Users password changed successfully.');
                    return redirect()->back();
                }
                else
                {
                    Flash::error('Problem Occurred, While changing users password');
                    return redirect()->back();
                }
            } 
            else
            {
                Flash::error('Unable to reset users password,Please try again.');
                return redirect()->back();
            }            
        } 
        catch (\Exception $e) 
        {
            Flash::error($e->getMessage());
            return redirect()->back();
        }

        Flash::error('Something went wrong ! Please try again !');
        return redirect()->back();
    }
    public function get_promo_codes(Request $request)
    {
        $code_type = '';
        if($request->has('code_type') && $request->input('code_type')!='') 
        {
            $code_type = $request->input('code_type');
        }
        $arr_promo_offer = [];

        $obj_promo_offer = $this->PromoOfferModel
                                            ->select('id','code_type','validity_from','validity_to','percentage','max_amount','code')
                                            ->where('code_type',$code_type)
                                            ->where('is_used',0)                    
                                            ->where('is_active',1)                    
                                            // ->whereRaw('DATE(validity_from) <= ?', [date('Y-m-d')])
                                            ->whereRaw('DATE(validity_to) >= ?', [date('Y-m-d')])
                                            ->get();

        if($obj_promo_offer){
            $arr_promo_offer = $obj_promo_offer->toArray();
        }                                    
        
        if(isset($arr_promo_offer) && sizeof($arr_promo_offer)>0){
            $arr_response['status'] = 'success';
            $arr_response['data'] = $arr_promo_offer;
            return response()->json($arr_response);    
        }
        else{
            $arr_response['status'] = 'error';
            $arr_response['data'] = 'Promo Offer not available';
            return response()->json($arr_response);    
        }
        $arr_response['status'] = 'error';
        $arr_response['data'] = 'Promo Offer not available';
        return response()->json($arr_response);    
        // $code_type
    }
    public function built_mail_data($arr_data)
    {
        if(isset($arr_data) && sizeof($arr_data)>0)
        {
            $arr_built_content = [
                                  'FIRST_NAME'       => isset($arr_data['first_name']) ? $arr_data['first_name'] : '',
                                  'LAST_NAME'        => isset($arr_data['last_name']) ? $arr_data['last_name'] : '',
                                  'EMAIL'            => isset($arr_data['email']) ? $arr_data['email'] : '',
                                  'MOBILE_NO'        => isset($arr_data['mobile_no']) ? $arr_data['mobile_no'] : '',
                                  'PASSWORD'         => isset($arr_data['password']) ? $arr_data['password'] : '',
                                  'PROJECT_NAME'     => config('app.project.name')];

            $arr_mail_data                      = [];
            $arr_mail_data['email_template_id'] = '7';
            $arr_mail_data['arr_built_content'] = $arr_built_content;
            $arr_mail_data['user']              = $arr_data;

            return $arr_mail_data;
        }
        return FALSE;
    }

    public function built_admin_reset_password_mail_data($arr_data)
    {
        if(isset($arr_data) && sizeof($arr_data)>0)
        {
            $arr_built_content = [
                                  'FIRST_NAME'   => isset($arr_data['first_name']) ? $arr_data['first_name'] : '',
                                  'LAST_NAME'    => isset($arr_data['last_name']) ? $arr_data['last_name'] : '',
                                  'EMAIL'        => isset($arr_data['email']) ? $arr_data['email'] : '',
                                  'MOBILE_NO'    => isset($arr_data['mobile_no']) ? $arr_data['mobile_no'] : '',
                                  'PASSWORD'     => isset($arr_data['new_password']) ? $arr_data['new_password'] : '',
                                  'ACCOUNT_TYPE' => 'Customer',
                                  'PROJECT_NAME' => config('app.project.name')];

            $arr_mail_data                      = [];
            $arr_mail_data['email_template_id'] = '18';
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
            $first_name = isset($arr_data['first_name']) ? $arr_data['first_name'] :'';
            $last_name  = isset($arr_data['last_name']) ? $arr_data['last_name'] :'';
            $full_name  = $first_name.' '.$last_name;
            $full_name  = ($full_name!=' ') ? $full_name : '-';

            $arr_notification['user_id']           = $this->CommonDataService->get_admin_id();
            $arr_notification['is_read']           = 0;
            $arr_notification['is_show']           = 0;
            $arr_notification['user_type']         = 'ADMIN';
            $arr_notification['notification_type'] = 'Customer Registration';
            $arr_notification['title']             = $full_name.' register as a Customer on Quickpick.';
            $arr_notification['view_url']          = '/'.config('app.project.admin_panel_slug')."/users"; // $this->module_url_path;
        }
        return $arr_notification;
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
            if($multi_action=="deactivate")
            {
               $this->perform_deactivate(base64_decode($record_id));    
               Flash::success($this->module_title.' Deactivated Successfully');  
            }
            elseif($multi_action=="activate")
            {
               $this->perform_activate(base64_decode($record_id)); 
               Flash::success($this->module_title.' Activated Successfully'); 
            }
        }
        
        return redirect()->back();
    }

    public function deactivate($enc_id=false)
    {
        $user_id  = base64_decode($enc_id);

        if(!$enc_id)
        {
            return redirect()->back();
        }

        if($this->perform_deactivate(base64_decode($enc_id)))
        {
            Flash::success($this->module_title. ' deactivated successfully');
        }
        else
        {
            Flash::error('Problem Occured While '. $this->module_title .' deactivation ');
        }

        
        Flash::success(str_singular($this->module_title).' deactivated successfully');
        return redirect($this->module_url_path);         
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
            return TRUE;
        }

        return FALSE;
    }

    public function perform_deactivate($id)
    {
        $static_page = $this->BaseModel->where('id',$id)->first();
        
        if($static_page)
        {
             $arr_notification_data = 
                                        [
                                            'title'             => 'Your account is blocked by admin',
                                            'notification_type' => 'USER_BLOCK',
                                            'enc_user_id'       => $id,
                                            'user_type'         => 'USER',

                                        ];
             $this->NotificationsService->send_on_signal_notification($arr_notification_data);


             $arr_notification_data = [];
             $arr_notification_data = 
                                        [
                                            'title'             => 'Your account is blocked by admin',
                                            'notification_type' => 'USER_BLOCK',
                                            'enc_user_id'       => $id,
                                            'user_type'         => 'WEB',

                                        ];
             $this->NotificationsService->send_on_signal_notification($arr_notification_data);

             return $static_page->update(['is_active'=>0]);
            }

        return FALSE;
    }

    public function perform_activate($id)
    {
        $static_page = $this->BaseModel->where('id',$id)->first();
        
        if($static_page)
        {

            return $static_page->update(['is_active'=>1]);
        }

        return FALSE;
    } 
    public function check_user_current_trip($user_id)
    {
        $arr_booking_master = [];
        $obj_booking_master = $this->BookingMasterModel
                                                        ->select('id','booking_status')
                                                        ->whereHas('load_post_request_details',function($query) use($user_id){
                                                            $query->where('user_id',$user_id);
                                                        })
                                                        ->whereIn('booking_status',['TO_BE_PICKED','IN_TRANSIT'])
                                                        ->orderBy('id','DESC')
                                                        ->first();
        if($obj_booking_master){
            return true;
        }
        return false;
    }   
}
