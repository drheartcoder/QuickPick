<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;

use App\Http\Controllers\Controller;
use App\Common\Traits\MultiActionTrait;
use App\Common\Services\CommonDataService;
use App\Common\Services\EmailService;
use App\Common\Services\NotificationsService;
use App\Common\Services\StripeService;


use App\Models\DepositMoneyModel;

use App\Models\RoleModel;
use App\Models\UserModel;
use App\Models\UserRoleModel;
use App\Models\BookingMasterModel;
use App\Models\CompanyCommissionModel;



use DB;
use URL;
use Image;
use Flash;
use Validator;
use Sentinel;
use Datatables;

class CompanyController extends Controller
{
    use MultiActionTrait;

    public function __construct(
                                    RoleModel $role,
                                    UserModel $user,
                                    UserRoleModel $user_role,
                                    DepositMoneyModel $deposit_money,
                                    CommonDataService $common_data_service,
                                    EmailService $email_service,
                                    NotificationsService $notifications_service,
                                    StripeService $stripe_service,
                                    BookingMasterModel $booking_master,
                                    CompanyCommissionModel $company_commission
                                )
    {
        $this->RoleModel                    = $role;
        $this->UserModel                    = $user;
        $this->UserRoleModel                = $user_role;
        $this->BaseModel                    = $this->UserModel;
        $this->DepositMoneyModel            = $deposit_money;
        $this->CommonDataService            = $common_data_service;
        $this->EmailService                 = $email_service;
        $this->NotificationsService         = $notifications_service;
        $this->StripeService                = $stripe_service;
        $this->BookingMasterModel           = $booking_master;
        $this->CompanyCommissionModel       = $company_commission;

        $this->arr_view_data                = [];
        $this->module_title                 = "Company";
        $this->module_titles                = "Company Deposit Receipt";
        $this->module_title_receipt         = "Company Deposit Receipt";
        $this->module_view_folder           = "admin.company";
        $this->theme_color                  = theme_color();
        $this->admin_panel_slug             = config('app.project.admin_panel_slug');
        $this->company_panel_slug             = config('app.project.company_panel_slug');
        $this->module_url_path              = url(config('app.project.admin_panel_slug')."/company");
        
        $this->user_profile_public_img_path = url('/').config('app.project.img_path.user_profile_images');
        $this->user_profile_base_img_path   = base_path().config('app.project.img_path.user_profile_images');

        $this->driving_license_public_path = url('/').config('app.project.img_path.driving_license');
        $this->driving_license_base_path   = base_path().config('app.project.img_path.driving_license');

        $this->receipt_image_public_path = url('/').config('app.project.img_path.payment_receipt');
        $this->receipt_image_base_path   = base_path().config('app.project.img_path.payment_receipt');
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

        $obj_user        =  $this->get_normal_company_details($request);

        $role            =  $request->input('role');


        $current_context = $this;

        $json_result     = Datatables::of($obj_user);

        $json_result     = $json_result->blacklist(['id']);
       
        if(array_key_exists('company.update', $arr_current_user_access))
        {
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
        }                    

        $json_result    = $json_result->editColumn('build_action_btn',function($data) use ($current_context)
                            {       
                                $view_href =  $this->module_url_path.'/view/'.base64_encode($data->id);
                                $build_view_action = '<a class="btn btn-warning btn-sm show-tooltip call_loader btn-delets" href="'.$view_href.'" title="View"><i class="fa fa-eye" ></i></a>';
                            })    
                            ->editColumn('build_action_btn',function($data) use ($current_context,$role,$arr_current_user_access)
                            {
                                      
                                $view_href =  $this->module_url_path.'/view/'.base64_encode($data->id);
                                $build_view_action = '<a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip btn-delets" href="'.$view_href.'" title="View"><i class="fa fa-eye" ></i></a>';

                                $delete_href =  $this->module_url_path.'/delete/'.base64_encode($data->id);
                                $build_delete_action = '<a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip btn-delets" href="'.$delete_href.'" title="Delete" onclick="return confirm_action(this,event,\'Do you really want to delete this record ?\')"><i class="fa fa-trash" ></i></a>';

                                if(array_key_exists('company.update', $arr_current_user_access))
                                {
                                    $edit_href =  $this->module_url_path.'/edit/'.base64_encode($data->id);
                                    $build_edit_action = '<a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip btn-delets" href="'.$edit_href.'" title="Edit"><i class="fa fa-edit" ></i></a>';
                                }
                                else
                                {
                                    $build_edit_action="";
                                }   

                                $deposit_receipt_href =  $this->module_url_path.'/deposit_receipt/'.base64_encode($data->id);
                                $build_deposit_receipt_action = '<a class="btn btn-circle btn-to-success btn-bordered btn-fill review-cars show-tooltip" href="'.$deposit_receipt_href.'" title="Deposit Money & Upload Receipt"><i class="fa fa-bus"></i></a>';

                               /* $reviews_href =  $this->module_url_path.'/review/'.base64_encode($data->id);
                                $build_reviews_action = '<a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip review-stars" href="'.$reviews_href.'" title="View Reviews"><i class="fa fa-star"></i></a>';*/

                               // return  $build_view_action." ".$build_edit_action." ".$build_reviews_action;

                                 return  $build_view_action." ".$build_edit_action." ".$build_delete_action." ".$build_deposit_receipt_action;
                                
                            })
                            ->make(true);

        $build_result = $json_result->getData();
        
        return response()->json($build_result);
    }

     private function get_normal_company_details(Request $request)
    {     
        $role                     = 'company';

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
        $this->arr_view_data['module_title']    = str_plural($this->module_title);
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['theme_color']     = $this->theme_color;
        
        return view($this->module_view_folder.'.create',$this->arr_view_data);
    }

    public function store(Request $request)
    {
        $arr_rules = [];
        $arr_rules['company_name'] 	= "required";
        $arr_rules['email']      	= "required|email";
        $arr_rules['mobile_no']  	= "required";
        $arr_rules['password']      = "required";
        $arr_rules['post_code']   	= "required";
        $arr_rules['address']    	= "required";

        $validator = Validator::make($request->all(),$arr_rules);
        
        if($validator->fails())
        {
            return redirect()->back()->withErrors($validator)->withInput($request->all());
        }

        /* Duplication Check */
        $is_email_duplicate = Sentinel::createModel()->where('email',trim($request->input('email')))
                                               ->count();

        if($is_email_duplicate>0)
        {
            Flash::error('User with this email address already exists.');
            return redirect()->back()->withInput($request->all());
        }

        $is_mobile_no_duplicate = Sentinel::createModel()->where('mobile_no',trim($request->input('mobile_no')))
                                               ->count();

        if($is_mobile_no_duplicate>0)
        {
            Flash::error('User with this mobile number already exists.');
            return redirect()->back()->withInput($request->all());
        }

        $arr_data                 = [];
        $arr_data['company_name'] = $request->input('company_name');
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
        $arr_data['user_type']    = 'COMPANY';
        
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
        if($request->hasFile('driving_license'))
        {
            $driving_license = $request->input('driving_license');
            $file_extension = strtolower($request->file('driving_license')->getClientOriginalExtension());
            if(in_array($file_extension,['png','jpg','jpeg','pdf']))
            {
                $driving_license = time().uniqid().'.'.$file_extension;
                $isUpload = $request->file('driving_license')->move($this->driving_license_base_path , $driving_license);
                $arr_data['driving_license'] = $driving_license;
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
            $role = Sentinel::findRoleBySlug('company');
            $obj_user->roles()->attach($role);
            $obj_user->save();

            if($obj_user)
            {
                $company_id = isset($obj_user->id) ? $obj_user->id : 0;

                $this->CompanyCommissionModel->create(['company_id'=>$company_id,'driver_percentage' => 0]);

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
                                ->select('id','company_name','profile_image','email','mobile_no','country_code','address','driving_license')
                                ->where('id',$enc_id)
                                ->first();

        if($obj_user)
        {
            $arr_user = $obj_user->toArray();
        }
        //dd($arr_user);
        
        $this->arr_view_data['page_title']      = "View ".str_singular( $this->module_title);
        $this->arr_view_data['module_title']    = str_plural($this->module_title);
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['theme_color']     = $this->theme_color;
        
        $this->arr_view_data['driving_license_public_path'] = $this->driving_license_public_path;
        $this->arr_view_data['driving_license_base_path']   = $this->driving_license_base_path;
        $this->arr_view_data['arr_user']        = $arr_user;

        return view($this->module_view_folder.'.view',$this->arr_view_data);
    }

    public function edit($enc_id)
    {
        $id = base64_decode($enc_id);

        $arr_data = [];

        $obj_user = $this->UserModel
                                ->select('id','company_name','profile_image','email','country_code','mobile_no','country_name','state_name','city_name','address','post_code')
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
        $arr_rules['company_name']  = "required";
        $arr_rules['email']         = "required|email";
        $arr_rules['mobile_no']     = "required|min:10|max:15";
        $arr_rules['address']       = "required";
        $arr_rules['post_code']     = "required";

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
        $is_email_duplicate = Sentinel::createModel()->where('email',trim($request->input('email')))
                                               ->where('id','!=',$user_id)
                                               ->count();

        if($is_email_duplicate>0)
        {
            Flash::error('User with this email address already exists.');
            return redirect()->back()->withInput($request->all());
        }

        $is_mobile_no_duplicate = Sentinel::createModel()->where('mobile_no',trim($request->input('mobile_no')))
                                               ->where('id','!=',$user_id)
                                               ->count();

        if($is_mobile_no_duplicate>0)
        {
            Flash::error('User with this mobile number already exists.');
            return redirect()->back()->withInput($request->all());
        }
        $arr_update                 = [];
        $arr_update['company_name'] = $request->input('company_name');
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

    public function deposit_receipt($enc_id)
    {
        $company_id = base64_decode($enc_id);
        
        $arr_deposit_money = [];

        $obj_company_deposit_money = $this->DepositMoneyModel
                                                ->with('booking_master_details','to_user_details')
                                                ->where('to_user_id',$company_id)
                                                ->where('to_user_type','COMPANY')
                                                ->orderBy('id','DESC')
                                                ->get();


        $arr_company_deposit_money = [];
        if($obj_company_deposit_money){
            $arr_company_deposit_money = $obj_company_deposit_money->toArray();
        } 

        // $obj_company_driver_deposit_money = $this->DepositMoneyModel
        //                                         ->with('booking_master_details','to_user_details')
        //                                         ->where('from_user_id',$company_id)
        //                                         ->where('from_user_type','COMPANY')
        //                                         ->orderBy('id','DESC')
        //                                         ->get();

        // $arr_company_driver_deposit_money =[];
        // if($obj_company_driver_deposit_money)
        // {
        //     $arr_company_driver_deposit_money = $obj_company_driver_deposit_money->toArray();
        // }

        // $arr_deposit_money = array_merge($arr_company_deposit_money,$arr_company_driver_deposit_money);
        
        $obj_company    = $this->UserModel
                                    ->select('id','first_name','last_name','profile_image','company_name')
                                    ->where('id',$company_id)
                                    ->first();
        $arr_company = [];
        if ($obj_company) 
        {
            $arr_company = $obj_company->toArray();
        }

        $arr_company_balance_information = $this->get_company_balance_information($company_id);
        
        $this->arr_view_data['arr_company_balance_information'] = $arr_company_balance_information;
        $this->arr_view_data['page_title']                      = "Deposit Money & Upload Receipt";
        $this->arr_view_data['module_title']                    = str_plural($this->module_titles);
        $this->arr_view_data['module_title_deposit']            = str_plural($this->module_title);
        $this->arr_view_data['module_url_path']                 = $this->module_url_path;
        $this->arr_view_data['theme_color']                     = $this->theme_color;
        $this->arr_view_data['company_id']                      = base64_encode($company_id);
        $this->arr_view_data['arr_deposit_money']               = $arr_company_deposit_money;
        $this->arr_view_data['arr_company']                     = $arr_company;
        $this->arr_view_data['receipt_image_public_path']       = $this->receipt_image_public_path ;
        $this->arr_view_data['receipt_image_base_path']         = $this->receipt_image_base_path   ;

        return view($this->module_view_folder.'.deposit_receipt',$this->arr_view_data);
    }

    public function make_payment(Request $request)
    {
        $arr_rules['amount_paid'] = "required";
        $validator = Validator::make($request->all(),$arr_rules);
        if($validator->fails())
        {
            Flash::error('Please fill all required fields.');
            return redirect()->back()->withErrors($validator)->withInput($request->all());
        }
        $company_id = base64_decode($request->input('company_id'));

        $arr_data                   = [];
        $arr_data['from_user_id']   = $this->CommonDataService->get_admin_id();
        $arr_data['to_user_id']     = $company_id;
        $arr_data['transaction_id'] = $this->genrate_transaction_unique_number();
        $arr_data['amount_paid']    = doubleval($request->input('amount_paid'));
        $arr_data['note']           = $request->input('note');
        $arr_data['from_user_type'] = 'ADMIN';
        $arr_data['to_user_type']   = 'COMPANY';
        $arr_data['status']         = 'PENDING';
        $arr_data['date']           = date('Y-m-d');
        
        if($request->hasFile('receipt_image'))
        {
            $receipt_image = $request->input('receipt_image');
            $file_extension = strtolower($request->file('receipt_image')->getClientOriginalExtension());
            if(in_array($file_extension,['png','jpg','jpeg','pdf']))
            {
                $receipt_image = time().uniqid().'.'.$file_extension;
                $isUpload = $request->file('receipt_image')->move($this->receipt_image_base_path , $receipt_image);
                $arr_data['receipt_image'] = $receipt_image;
            }
            else
            {
                Flash::error('Invalid File type, While creating '.str_singular($this->module_title));
                return redirect()->back();
            }
        }
        
        $status = $this->DepositMoneyModel->create($arr_data);

        if($status)
        {
            $arr_data_info  = $this->CommonDataService->get_company_details($company_id);

            $arr_data_details = array_merge($arr_data_info,$arr_data);          

            $arr_notification_data = $this->built_notification_data_info($arr_data_details); 
            $this->NotificationsService->store_notification($arr_notification_data);

            /*$arr_mail_data = $this->built_mail_data_payment($arr_data_details); 
            $this->EmailService->send_mail_with_attachments($arr_mail_data);*/

            Flash::success(str_singular($this->module_title).' deposit money receipt uploaded successfully.');
            return redirect()->back();
        }
        else
        {
            Flash::error('Problem Occurred, While Creating '.str_singular($this->module_title));
        }    
    }

    public function make_driver_payment(Request $request)
    {
        $enc_id = base64_decode($request->input('enc_id'));

        if($enc_id == '')
        {
            Flash::error('Unable to process request,Please try again.');
            return redirect()->back();
        }
        $obj_deposit_money = $this->DepositMoneyModel
                                            ->with('to_user_details','booking_master_details')
                                            ->where('id',$enc_id)
                                            ->where('status','FAILED')
                                            ->first();
        $arr_deposit_money = [];
        if($obj_deposit_money)
        {
            $arr_deposit_money = $obj_deposit_money->toArray();
        }

        if(isset($arr_deposit_money) && count($arr_deposit_money)>0)
        {
            $company_stripe_account_id = isset($arr_deposit_money['to_user_details']['stripe_account_id'])  ?$arr_deposit_money['to_user_details']['stripe_account_id'] : '';
            if(isset($company_stripe_account_id) && $company_stripe_account_id!='')
            {
                $company_id                = isset($arr_deposit_money['to_user_id']) ? $arr_deposit_money['to_user_id'] : 0;
                $company_name              = isset($arr_deposit_money['to_user_details']['company_name']) ? $arr_deposit_money['to_user_details']['company_name'] : '';
                $company_email             = isset($arr_deposit_money['to_user_details']['email']) ? $arr_deposit_money['to_user_details']['email'] : '';
                $company_mobile_no         = isset($arr_deposit_money['to_user_details']['mobile_no']) ? $arr_deposit_money['to_user_details']['mobile_no'] : '';
                $booking_id                = isset($arr_deposit_money['booking_master_details']['id']) ? $arr_deposit_money['booking_master_details']['id'] : '';
                $booking_unique_id         = isset($arr_deposit_money['booking_master_details']['booking_unique_id']) ? $arr_deposit_money['booking_master_details']['booking_unique_id'] : '';
                $company_earning_amount    = isset($arr_deposit_money['amount_paid']) ? round($arr_deposit_money['amount_paid'],2) : 0;
                $total_company_cent_charge = (round($company_earning_amount,2) * 100);

                $arr_company_payment_details = [
                                              'company_id'                => $company_id,
                                              'company_name'              => $company_name,
                                              'company_email'             => $company_email,
                                              'company_mobile_no'         => $company_mobile_no,
                                              'booking_id'                => $booking_id,
                                              'booking_unique_id'         => $booking_unique_id,
                                              'company_stripe_account_id' => $company_stripe_account_id,
                                              'company_earning_amount'    => $company_earning_amount,
                                              'total_company_cent_charge' => $total_company_cent_charge
                                            ];
                
                $arr_stripe_response = $this->StripeService->make_company_payment($arr_company_payment_details);
                
                $driver_deposit_money_status = 'FAILED';
                if(isset($arr_stripe_response['status']) && $arr_stripe_response['status'] == 'success') 
                {
                    $driver_deposit_money_status = 'SUCCESS';    
                } 
                
                unset($arr_deposit_money['to_user_details']); 
                unset($arr_deposit_money['booking_master_details']); 

                $arr_new_deposit_money_history = [];

                $arr_deposit_money_history = isset($arr_deposit_money['deposit_money_history']) ? json_decode($arr_deposit_money['deposit_money_history'],true) : [];

                if(isset($arr_deposit_money_history) && count($arr_deposit_money_history)>0)
                {
                    array_push($arr_new_deposit_money_history,$arr_deposit_money_history);
                }
                array_push($arr_new_deposit_money_history,$arr_deposit_money);

                
                $obj_deposit_money->note                  = isset($arr_stripe_response['msg']) ? $arr_stripe_response['msg'] : '';
                $obj_deposit_money->status                = $driver_deposit_money_status;
                $obj_deposit_money->date                  = date('Y-m-d');
                $obj_deposit_money->payment_data          = isset($arr_stripe_response['payment_data']) ? $arr_stripe_response['payment_data'] : '';
                $obj_deposit_money->deposit_money_history = json_encode($arr_new_deposit_money_history);
                
                $payment_status = $obj_deposit_money->save();
                if($payment_status)
                {
                    $arr_data_details = array_merge($arr_deposit_money,$arr_company_payment_details);     
                
                    /*build admin notification data*/
                    $arr_admin_notification_data = $this->built_payment_notification_data($arr_data_details,'ADMIN_COMPANY');                 
                    $this->NotificationsService->store_notification($arr_admin_notification_data);
                    
                    /*build company notification data*/
                    $arr_company_notification_data = $this->built_payment_notification_data($arr_data_details,'COMPANY'); 
                    $this->NotificationsService->store_notification($arr_company_notification_data);
 
                    $flash_msg = '';
                    $transaction_id = isset($arr_deposit_money['transaction_id']) ? $arr_deposit_money['transaction_id'] : '';
                    if(isset($driver_deposit_money_status) && $driver_deposit_money_status == 'SUCCESS')
                    {   
                        $flash_msg = 'Payment for tripe #'.$booking_unique_id.' is successfully done with transaction id #'.$transaction_id;
                    }
                    else if(isset($driver_deposit_money_status) && $driver_deposit_money_status == 'FAILED')
                    {
                        $flash_msg = 'Payment for tripe #'.$booking_unique_id.' is failed  with the transaction id #'.$transaction_id.' Please contact '.config('app.project.name').' Admin';
                    }
                    else if(isset($driver_deposit_money_status) && $driver_deposit_money_status == 'PENDING')
                    {
                        $flash_msg = 'Payment for tripe #'.$booking_unique_id.' is pending with the transaction id #'.$transaction_id.' Please contact '.config('app.project.name').' Admin';
                    } 

                    if($driver_deposit_money_status == 'SUCCESS')
                    {
                        Flash::success($flash_msg);
                    }
                    elseif($driver_deposit_money_status == 'FAILED')
                    {
                        Flash::error($flash_msg);
                    }
                    return redirect()->back();
                }
                else
                {
                    Flash::error('Unable to make company payment Please try again.');
                    return redirect()->back();
                }
            }
            else
            {
                Flash::error('Company Stripe account id is empty,cannot process request.');
                return redirect()->back();
            }
        }
        Flash::error('Unable to process request,Please try again.');
        return redirect()->back();
    }
    private function built_payment_notification_data($arr_data,$type)
    {
        $arr_notification = [];
        if(isset($arr_data) && sizeof($arr_data)>0)
        {
            if(isset($type) && $type == 'ADMIN_DRIVER')
            {
                $id                = isset($arr_data['driver_id']) ? $arr_data['driver_id'] :'';
                $transaction_id    = isset($arr_data['transaction_id']) ? $arr_data['transaction_id'] :'';
                $booking_unique_id = isset($arr_data['booking_unique_id']) ? $arr_data['booking_unique_id'] :'';
                
                $is_company_driver = isset($arr_data['is_company_driver']) ? $arr_data['is_company_driver'] :'';
                $company_name      = isset($arr_data['company_name']) ? $arr_data['company_name'] :'';

                $first_name = isset($arr_data['driver_first_name']) ? $arr_data['driver_first_name'] :'';
                $last_name  = isset($arr_data['driver_last_name']) ? $arr_data['driver_last_name'] :'';

                $full_name  = $first_name.' '.$last_name;
                $full_name  = ($full_name!=' ') ? $full_name : '-';

                $notification_title = $notification_type = '';

                if($is_company_driver == '0')
                {
                    $notification_type = 'Driver Online Payment';
                    if(isset($arr_data['status']) && $arr_data['status'] == 'SUCCESS')
                    {   
                        $notification_title = 'Payment for trip #'.$booking_unique_id.' is successfully sent to - '.$full_name.' account with transaction id #'.$transaction_id;
                    }
                    else if(isset($arr_data['status']) && $arr_data['status'] == 'FAILED')
                    {
                        $notification_title = 'Payment for trip #'.$booking_unique_id.' is failed while sending to - '.$full_name.' account with transaction id #'.$transaction_id;
                    }
                    else if(isset($arr_data['status']) && $arr_data['status'] == 'PENDING')
                    {
                        $notification_title = 'Payment for trip #'.$booking_unique_id.' is pending to - '.$full_name.' account with transaction id #'.$transaction_id;
                    }
                }
                else if($is_company_driver == '1')
                {
                    $notification_type = '('.$company_name.') Driver Online Payment';

                    $sent_to_name = $full_name.' ('.$company_name.' Driver)';

                    if(isset($arr_data['status']) && $arr_data['status'] == 'SUCCESS')
                    {   
                        $notification_title = 'Payment for trip #'.$booking_unique_id.' is successfully sent to - '.$sent_to_name.' account with transaction id #'.$transaction_id;
                    }
                    else if(isset($arr_data['status']) && $arr_data['status'] == 'FAILED')
                    {
                        $notification_title = 'Payment for trip #'.$booking_unique_id.' is failed while sending to - '.$sent_to_name.' account with transaction id #'.$transaction_id;
                    }
                    else if(isset($arr_data['status']) && $arr_data['status'] == 'PENDING')
                    {
                        $notification_title = 'Payment for trip #'.$booking_unique_id.' is pending to - '.$sent_to_name.' account with transaction id #'.$transaction_id;
                    }
                }

                $arr_notification['user_id']           = $this->CommonDataService->get_admin_id();
                $arr_notification['is_read']           = 0;
                $arr_notification['is_show']           = 0;
                $arr_notification['user_type']         = 'ADMIN';
                $arr_notification['notification_type'] = $notification_type;
                $arr_notification['title']             = $notification_title;
                
                $arr_notification['view_url']          = '/'.config('app.project.admin_panel_slug').'/driver/deposit_receipt/'.base64_encode($id); 
            }
            else if(isset($type) && $type == 'ADMIN_COMPANY')
            {
                $id                = isset($arr_data['company_id']) ? $arr_data['company_id'] :'';
                $transaction_id    = isset($arr_data['transaction_id']) ? $arr_data['transaction_id'] :'';
                $booking_unique_id = isset($arr_data['booking_unique_id']) ? $arr_data['booking_unique_id'] :'';
                
                $company_name      = isset($arr_data['company_name']) ? $arr_data['company_name'] :'';

                $notification_title = $notification_type = '';

                $notification_type = $company_name.' Company Online Payment';
                if(isset($arr_data['status']) && $arr_data['status'] == 'SUCCESS')
                {   
                    $notification_title = 'Payment for trip #'.$booking_unique_id.' is successfully sent to - ('.$company_name.') Company account with transaction id #'.$transaction_id;
                }
                else if(isset($arr_data['status']) && $arr_data['status'] == 'FAILED')
                {
                    $notification_title = 'Payment for trip #'.$booking_unique_id.' is failed while sending to - ('.$company_name.') Company account with transaction id #'.$transaction_id;
                }
                else if(isset($arr_data['status']) && $arr_data['status'] == 'PENDING')
                {
                    $notification_title = 'Payment for trip #'.$booking_unique_id.' is pending to - ('.$company_name.') Company account with transaction id #'.$transaction_id;
                }

                $arr_notification['user_id']           = $this->CommonDataService->get_admin_id();
                $arr_notification['is_read']           = 0;
                $arr_notification['is_show']           = 0;
                $arr_notification['user_type']         = 'ADMIN';
                $arr_notification['notification_type'] = $notification_type;
                $arr_notification['title']             = $notification_title;
                
                $arr_notification['view_url']          = '/'.config('app.project.admin_panel_slug').'/company/deposit_receipt/'.base64_encode($id); 
            }
            else if(isset($type) && $type == 'DRIVER')
            {
                $id                    = isset($arr_data['driver_id']) ? $arr_data['driver_id'] :'';
                $transaction_id        = isset($arr_data['transaction_id']) ? $arr_data['transaction_id'] :'';
                $booking_unique_id     = isset($arr_data['booking_unique_id']) ? $arr_data['booking_unique_id'] :'';
                $driver_earning_amount = isset($arr_data['driver_earning_amount']) ? $arr_data['driver_earning_amount']: '-';

                $is_company_driver = isset($arr_data['is_company_driver']) ? $arr_data['is_company_driver'] :'';
                $company_name      = isset($arr_data['company_name']) ? $arr_data['company_name'] :'';

                $notification_title = $notification_type = '';

                if($is_company_driver == '0')
                {
                    $notification_type = 'Driver Online Payment';
                    if(isset($arr_data['status']) && $arr_data['status'] == 'SUCCESS')
                    {   
                        $notification_title = 'Payment for trip #'.$booking_unique_id.' is successfully received to your account with transaction id #'.$transaction_id;
                    }
                    else if(isset($arr_data['status']) && $arr_data['status'] == 'FAILED')
                    {
                        $notification_title = 'Payment for trip #'.$booking_unique_id.' is failed  with the transaction id #'.$transaction_id.' Please contact '.config('app.project.name').' Admin';
                    }
                    else if(isset($arr_data['status']) && $arr_data['status'] == 'PENDING')
                    {
                        $notification_title = 'Payment for trip #'.$booking_unique_id.' is pending with the transaction id #'.$transaction_id.' Please contact '.config('app.project.name').' Admin';
                    }
                }
                else if($is_company_driver == '1')
                {
                    $notification_type = '('.$company_name.') Driver Online Payment';

                    if(isset($arr_data['status']) && $arr_data['status'] == 'SUCCESS')
                    {   
                        $notification_title = 'Payment for trip #'.$booking_unique_id.' is successfully received to your account with transaction id #'.$transaction_id;
                    }
                    else if(isset($arr_data['status']) && $arr_data['status'] == 'FAILED')
                    {
                        $notification_title = 'Payment for trip #'.$booking_unique_id.' is failed  with the transaction id #'.$transaction_id.' Please contact '.$company_name.' Admin';
                    }
                    else if(isset($arr_data['status']) && $arr_data['status'] == 'PENDING')
                    {
                        $notification_title = 'Payment for trip #'.$booking_unique_id.' is pending with the transaction id #'.$transaction_id.' Please contact '.$company_name.' Admin';
                    }
                }

                $arr_notification['user_id']           = $id;
                $arr_notification['is_read']           = 0;
                $arr_notification['is_show']           = 0;
                $arr_notification['user_type']         = 'DRIVER';
                $arr_notification['notification_type'] = $notification_type;
                $arr_notification['title']             = $notification_title;
                $arr_notification['view_url']          = '';
            }
            else if(isset($type) && $type == 'COMPANY')
            {
                $id                    = isset($arr_data['company_id']) ? $arr_data['company_id'] :'';
                $transaction_id        = isset($arr_data['transaction_id']) ? $arr_data['transaction_id'] :'';
                $booking_unique_id     = isset($arr_data['booking_unique_id']) ? $arr_data['booking_unique_id'] :'';
                
                $notification_title = $notification_type = '';

                $notification_type = 'Company Online Payment';
                if(isset($arr_data['status']) && $arr_data['status'] == 'SUCCESS')
                {   
                    $notification_title = 'Payment for trip #'.$booking_unique_id.' is successfully received to your account with transaction id #'.$transaction_id;
                }
                else if(isset($arr_data['status']) && $arr_data['status'] == 'FAILED')
                {
                    $notification_title = 'Payment for trip #'.$booking_unique_id.' is failed  with the transaction id #'.$transaction_id.' Please contact '.config('app.project.name').' Admin';
                }
                else if(isset($arr_data['status']) && $arr_data['status'] == 'PENDING')
                {
                    $notification_title = 'Payment for trip #'.$booking_unique_id.' is pending with the transaction id #'.$transaction_id.' Please contact '.config('app.project.name').' Admin';
                }

                $arr_notification['user_id']           = $id;
                $arr_notification['is_read']           = 0;
                $arr_notification['is_show']           = 0;
                $arr_notification['user_type']         = 'COMPANY';
                $arr_notification['notification_type'] = $notification_type;
                $arr_notification['title']             = $notification_title;
                $arr_notification['view_url']          = '/'.config('app.project.company_panel_slug').'/deposit_money';
            }
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

    private function genrate_transaction_unique_number()
    {
        $secure = TRUE;    
        $bytes = openssl_random_pseudo_bytes(6, $secure);
        $order_ref_num = "TRAN-".bin2hex($bytes);

        return strtoupper($order_ref_num);   
    }

    public function built_mail_data($arr_data)
    {
        if(isset($arr_data) && sizeof($arr_data)>0)
        {
            $arr_built_content = [
                                  'FIRST_NAME'       => $arr_data['company_name'],
                                  'LAST_NAME'        => $arr_data['company_name'],
                                  'EMAIL'            => $arr_data['email'],
                                  'PASSWORD'         => $arr_data['password'],
                                  'PROJECT_NAME'     => config('app.project.name')];

            $arr_mail_data                      = [];
            $arr_mail_data['email_template_id'] = '9';
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
            $company_name = isset($arr_data['company_name']) ? $arr_data['company_name'] :'';
            $full_name  = $company_name;
            $full_name  = ($full_name!=' ') ? $full_name : '-';

            $arr_notification['user_id']           = $this->CommonDataService->get_admin_id();
            $arr_notification['is_read']           = 0;
            $arr_notification['is_show']           = 0;
            $arr_notification['user_type']         = 'ADMIN';
            $arr_notification['notification_type'] = 'Company Registration';
            $arr_notification['title']             = $full_name.' register as a Company on Quickpick.';
            $arr_notification['view_url']          = '/'.config('app.project.admin_panel_slug')."/company"; // $this->module_url_path;
        }
        return $arr_notification;
    }

    private function built_notification_data_info($arr_data_details)
    {
            $arr_notification = [];
            if(isset($arr_data_details) && sizeof($arr_data_details)>0)
            {
                $transaction_id = $arr_data_details['transaction_id'];
                $full_name = isset($arr_data_details['company_name']) ? $arr_data_details['company_name'] :'';
                $full_name  = ($full_name!=' ') ? $full_name : '-';

                $arr_notification['user_id']           = $arr_data_details['id'];
                $arr_notification['is_read']           = 0;
                $arr_notification['is_show']           = 0;
                $arr_notification['user_type']         = 'COMPANY';
                $arr_notification['notification_type'] = 'Payment Success';
                $arr_notification['title']             = $full_name.' Payment Successfull #'.$transaction_id;
                $arr_notification['view_url']          = '/'.config('app.project.company_panel_slug')."/my_earning"; //$this->module_url_path;

        }
        return $arr_notification;
    }

    public function built_mail_data_payment($arr_data_details)
    {
        if(isset($arr_data_details) && sizeof($arr_data_details)>0)
        {
            $currency   = config('app.project.currency');

            $amount_paid = $currency.$arr_data_details['amount_paid'];

            $attachment = $this->receipt_image_public_path.$arr_data_details['receipt_image'];

            $arr_built_content = [
                'COMPANY_NAME'     => $arr_data_details['company_name'],
                'TRANSACTION_ID'   => $arr_data_details['transaction_id'],
                'AMOUNT_PAID'      => $amount_paid,
                'EMAIL'            => $arr_data_details['email'],
                'PROJECT_NAME'     => config('app.project.name')];

                $arr_mail_data                      = [];
                $arr_mail_data['email_template_id'] = '16';
                $arr_mail_data['arr_built_content'] = $arr_built_content;
                $arr_mail_data['user']              = $arr_data_details;
                $arr_mail_data['attachment']        = $attachment;

                return $arr_mail_data;
            }
            return FALSE;
    }
    private function get_company_balance_information($company_id)
    {
        $company_total_amount   = 0;
        $company_paid_amount    = 0;
        $company_unpaid_amount  = 0;
        $arr_result = [];
        
        $arr_result['company_total_amount']  = $company_total_amount;
        $arr_result['company_paid_amount']   = $company_paid_amount;
        $arr_result['company_unpaid_amount'] = $company_unpaid_amount;

        $obj_driver_account_balance = $this->BookingMasterModel
                                                ->whereHas('load_post_request_details',function($query) use($company_id){
                                                    $query->whereHas('driver_details',function($query) use($company_id){
                                                        $query->where('company_id',$company_id);
                                                    });
                                                })           
                                                ->where('booking_status','COMPLETED')
                                                ->get();
        if($obj_driver_account_balance)
        {
            $arr_driver_account_balance = $obj_driver_account_balance->toArray();
        }
        
        if(isset($arr_driver_account_balance) && sizeof($arr_driver_account_balance)>0)
        {
            foreach ($arr_driver_account_balance as $key => $value) 
            {
                $booking_status           = isset($value['booking_status']) ? $value['booking_status'] :'';
                $total_amount             = isset($value['total_amount']) ? floatval($value['total_amount']):0.00;
                $admin_amount             = isset($value['admin_amount']) ? floatval($value['admin_amount']):0;
                $company_amount           = isset($value['company_amount']) ? floatval($value['company_amount']):0;
                $admin_driver_amount      = isset($value['admin_driver_amount']) ? floatval($value['admin_driver_amount']):0;
                $company_driver_amount    = isset($value['company_driver_amount']) ? floatval($value['company_driver_amount']):0;
                $individual_driver_amount = isset($value['individual_driver_amount']) ? floatval($value['individual_driver_amount']):0;
                $is_company_driver        = isset($value['is_company_driver']) ? $value['is_company_driver']:0;
                $is_individual_vehicle    = isset($value['is_individual_vehicle']) ? $value['is_individual_vehicle']:0;

                $company_earning_amount =    0;

                if($is_individual_vehicle == '0')
                {    
                    if($is_company_driver == '1')
                    {
                        $company_earning_amount = $company_amount + $company_driver_amount;    
                    }
                }
                $company_total_amount = (floatval($company_total_amount) + floatval($company_earning_amount));
            }
        }
        
        $obj_company_paid_amount = $this->DepositMoneyModel
                                                ->select('id','to_user_id','amount_paid','status')
                                                ->where([
                                                            'to_user_id'   => $company_id,
                                                            'to_user_type' => 'COMPANY',
                                                            'status'       => 'SUCCESS'
                                                        ])
                                                ->get();
        
        $arr_company_paid_amount =[];
        if($obj_company_paid_amount)
        {
            $arr_company_paid_amount = $obj_company_paid_amount->toArray();
        }

        if(isset($arr_company_paid_amount) && sizeof($arr_company_paid_amount)>0)
        {
            foreach ($arr_company_paid_amount as $key => $value) 
            {
                $amount_paid = isset($value['amount_paid']) ? $value['amount_paid'] :0;

                $company_paid_amount = (floatval($company_paid_amount) + floatval($amount_paid));
            }
        }  

        $obj_company_driver_paid_amount = $this->DepositMoneyModel
                                                ->select('id','from_user_id','amount_paid','status')
                                                ->where([
                                                            'from_user_id'   => $company_id,
                                                            'from_user_type' => 'COMPANY',
                                                            'status'         => 'SUCCESS'
                                                        ])
                                                ->get();

        $arr_company_driver_paid_amount =[];
        if($obj_company_driver_paid_amount)
        {
            $arr_company_driver_paid_amount = $obj_company_driver_paid_amount->toArray();
        }

        if(isset($arr_company_driver_paid_amount) && sizeof($arr_company_driver_paid_amount)>0)
        {
            foreach ($arr_company_driver_paid_amount as $key => $value) 
            {
                $amount_paid = isset($value['amount_paid']) ? $value['amount_paid'] :0;

                $company_paid_amount = (floatval($company_paid_amount) + floatval($amount_paid));
            }
        }  

        if($company_total_amount>$company_paid_amount)
        {
            $company_unpaid_amount = (floatval($company_total_amount) - floatval($company_paid_amount));
            $company_unpaid_amount = $company_unpaid_amount;
        }

        $arr_result['company_total_amount']  = $company_total_amount;
        $arr_result['company_paid_amount']   = $company_paid_amount;
        $arr_result['company_unpaid_amount'] = $company_unpaid_amount;

        return $arr_result;
    }
}