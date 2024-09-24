<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Common\Traits\MultiActionTrait;

use App\Common\Services\EmailService;
use App\Common\Services\CommonDataService;
use App\Common\Services\NotificationsService;
use App\Common\Services\StripeService;


use App\Models\RoleModel;
use App\Models\UserModel;
use App\Models\UserRoleModel;
use App\Models\VehicleModel;
use App\Models\DriverStatusModel;
use App\Models\DepositMoneyModel;
use App\Models\BookingMasterModel;
use App\Models\LoadPostRequestModel;
use App\Models\DriverFairChargeModel;
use App\Models\DriverCarRelationModel;
use App\Models\DriverFairChargeRequestModel;


use DB;
use URL;
use Image;
use Flash;
use Validator;
use Sentinel;
use Datatables;
use Mail;

class DriverController extends Controller
{
	use MultiActionTrait;

	public function __construct(
									EmailService $email_service,
									CommonDataService $common_data_service,
									NotificationsService $notifications_service,
									StripeService $stripe_service,
									RoleModel $role,
									UserModel $user,
									UserRoleModel $user_role,
									VehicleModel $vehicle,
									DriverStatusModel $driverstatus,
									DepositMoneyModel $deposit_money,
									BookingMasterModel $booking_master,
									LoadPostRequestModel $load_post_request,
									DriverFairChargeModel $driver_fair_charge,
									DriverCarRelationModel $driver_car_relation,
									DriverFairChargeRequestModel $driver_fair_charge_request
								)
	{
		
		$this->RoleModel                    = $role;
		$this->UserModel                    = $user;
		$this->UserRoleModel                = $user_role;
		$this->BaseModel                    = $this->UserModel;
		$this->VehicleModel 				= $vehicle;
		
		$this->DriverFairChargeModel        = $driver_fair_charge;
		$this->DriverFairChargeRequestModel = $driver_fair_charge_request;
		$this->DriverCarRelationModel       = $driver_car_relation;
		
		$this->DepositMoneyModel       		= $deposit_money;
		$this->DriverStatusModel            = $driverstatus;
		
		$this->CommonDataService            = $common_data_service;
		$this->EmailService                 = $email_service;
		$this->NotificationsService         = $notifications_service;
		$this->StripeService 				= $stripe_service;

		$this->BookingMasterModel 			= $booking_master;
		$this->LoadPostRequestModel 		= $load_post_request;

		$this->arr_view_data                = [];
		$this->module_title                 = "Driver";
		$this->module_title_earning         = "Driver Earning";
		$this->module_titles                = "Driver Deposit Receipt";
		$this->module_title_receipt         = "Driver Deposit Receipt";
		$this->module_view_folder           = "admin.driver";
		$this->theme_color                  = theme_color();
		$this->admin_panel_slug             = config('app.project.admin_panel_slug');
		$this->module_url_path              = url(config('app.project.admin_panel_slug')."/driver");
		$this->module_url_earning_paths     = url(config('app.project.admin_panel_slug')."/driver/driver_earning/");
	 	
		$this->user_profile_public_img_path = url('/').config('app.project.img_path.user_profile_images');
		$this->user_profile_base_img_path   = base_path().config('app.project.img_path.user_profile_images');

		$this->driver_deposit_receipt_public_img_path = url('/').config('app.project.img_path.driver_deposit_receipt');
		$this->driver_deposit_receipt_base_img_path   = base_path().config('app.project.img_path.driver_deposit_receipt');

		$this->driving_license_public_path = url('/').config('app.project.img_path.driving_license');
		$this->driving_license_base_path   = base_path().config('app.project.img_path.driving_license');

		$this->vehicle_doc_public_path = url('/').config('app.project.img_path.vehicle_doc');
        $this->vehicle_doc_base_path   = base_path().config('app.project.img_path.vehicle_doc');

		$this->receipt_image_public_path = url('/').config('app.project.img_path.payment_receipt');
		$this->receipt_image_base_path   = base_path().config('app.project.img_path.payment_receipt');

	} 

	public function index(Request $request)
	{   
		$driver_type = 'admin';
		if($request->has('driver_type') && $request->input('driver_type')!=''){
			$driver_type = $request->input('driver_type');
		}

		if($driver_type == 'company'){
			$arr_company = [];
			$obj_company = $this->UserModel
									->whereHas('roles',function($query){
										$query->where('slug','company');
									})
									->select('id','company_name')
									->get();
			if($obj_company)
			{
				$arr_company = $obj_company->toArray();
			}		
			$this->arr_view_data['arr_company']     = $arr_company;		
		}
		$this->arr_view_data['page_title']      = "Manage ".str_plural($this->module_title);
		$this->arr_view_data['module_title']    = str_plural($this->module_title);
		$this->arr_view_data['module_url_path'] = $this->module_url_path;
		$this->arr_view_data['theme_color']     = $this->theme_color;
		$this->arr_view_data['driver_type']     = $driver_type;

		return view($this->module_view_folder.'.index', $this->arr_view_data);
	}

	public function get_records(Request $request)
	{
		
		$driver_type = 'admin';
		if($request->has('driver_type') && $request->input('driver_type')!=''){
			$driver_type = $request->input('driver_type');
		}

		$obj_user        = $this->get_driver_details($request);

		$current_context = $this;

		$json_result     = Datatables::of($obj_user);
		$json_result     = $json_result->blacklist(['id']);
		
		
		$json_result = $json_result
										->editColumn('enc_id',function($data) use ($current_context)
										{
											return base64_encode($data->id);
										})
										->editColumn('email',function($data) use ($current_context)
										{
											$email = '-';
											if(isset($data->email) && $data->email!=''){
												$email = $data->email;
											}
											return $email;
										});
		if($driver_type == 'admin' || $driver_type == 'individual')
		{
			$json_result = $json_result->editColumn('is_individual_vehicle',function($data) use ($current_context)
											{
												$vehicle_owner = '-';
												if(isset($data->is_individual_vehicle) && $data->is_individual_vehicle == '1'){
													$vehicle_owner = 'Individual Vehicle';
												}
												elseif(isset($data->is_individual_vehicle) && $data->is_individual_vehicle == '0'){
													 $vehicle_owner = config('app.project.name').' Vehicle';
												}
												return $vehicle_owner;
											});
		}

		if($driver_type == 'company')
		{
			$json_result = $json_result->editColumn('company_name',function($data) use ($current_context)
											{
												$company_name = config('app.project.name');
												if(isset($data->is_company_driver) && $data->is_company_driver == '1'){
													if(isset($data->company_name) && $data->company_name!=''){
														$company_name = $data->company_name;
													}
												}
												return $company_name;
											});
		}

		$json_result = $json_result->editColumn('build_status_btn',function($data) use ($current_context)
										{
											if($data->is_active != null && $data->is_active == "0")
											{   
												$build_status_btn = '<a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip btn-dangers" title="Lock" href="'.$this->module_url_path.'/activate/'.base64_encode($data->id).'" 
												onclick="return confirm_action(this,event,\'Do you really want to activate this record ?\')" ><i class="fa fa-lock"></i></a>';
											}
											elseif($data->is_active != null && $data->is_active == "1")
											{
												$build_status_btn = '<a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip" title="Unlock" href="'.$this->module_url_path.'/deactivate/'.base64_encode($data->id).'" onclick="return confirm_action(this,event,\'Do you really want to deactivate this record ?\')" ><i class="fa fa-unlock"></i></a>';
											}
											return $build_status_btn;

										})
										->editColumn('build_status_check',function($data) use ($current_context)
										{
											if($data->account_status != null && $data->account_status == "unapproved")
											{   
												$build_status_check = '<a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip btn-dangers" title="Click to approve" href="'.$this->module_url_path.'/approve/'.base64_encode($data->id).'" 
												onclick="return confirm_action(this,event,\'Do you really want to Approve this record ?\')" ><i class="fa fa-close"></i></a>';
											}
											elseif($data->account_status != null && $data->account_status == "approved")
											{
												$build_status_check = '<a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip" title="Approved" href="'.$this->module_url_path.'/unapprove/'.base64_encode($data->id).'" onclick="return confirm_action(this,event,\'Do you really want to unapprove this record ?\')" ><i class="fa fa-check"></i></a>';
											}
											return $build_status_check;
										})
										->editColumn('build_action_btn',function($data) use ($current_context)
										{
											$vehicle_view_href = 'javascript:void(0);';
											if(isset($data->vehicle_id) && $data->vehicle_id!=0)
											{
												$vehicle_view_href =  url(config('app.project.admin_panel_slug')."/vehicle").'/view/'.base64_encode($data->vehicle_id);
											}
											$build_vehicle_view_action = '<a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip btn-delets" href="'.$vehicle_view_href.'" title="View Vehicle Details"><i class="fa fa-car" ></i></a>';

											$view_href =  $this->module_url_path.'/view/'.base64_encode($data->id);
											$build_view_action = '<a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip btn-delets" href="'.$view_href.'" title="View"><i class="fa fa-eye" ></i></a>';

											$edit_href =  $this->module_url_path.'/edit/'.base64_encode($data->id);
											$build_edit_action = '<a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip btn-delets" href="'.$edit_href.'" title="Edit"><i class="fa fa-edit" ></i></a>';

											if(isset($data->is_company_driver) && $data->is_company_driver == '0')
											{
												$delete_href =  $this->module_url_path.'/delete/'.base64_encode($data->id);
				                                $build_delete_action = '<a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip btn-delets" href="'.$delete_href.'" title="Delete" onclick="return confirm_action(this,event,\'Do you really want to delete this record ?\')"><i class="fa fa-trash" ></i></a>';
											}
											else
											{
												$build_delete_action = '';    
											}

											$reviews_href =  $this->module_url_path.'/review/'.base64_encode($data->id);
											$build_reviews_action = '<a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip review-stars" href="'.$reviews_href.'" title="View Reviews"><i class="fa fa-star"></i></a>';



											/*if(isset($data->is_company_driver) && $data->is_company_driver == '0')
											{*/
												$deposit_receipt_href =  $this->module_url_path.'/deposit_receipt/'.base64_encode($data->id);
												$build_deposit_receipt_action = '<a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip review-cars" href="'.$deposit_receipt_href.'" title="Deposit Money & Upload Receipt"><i class="fa fa-bus"></i></a>';
											/*}
											else
											{
												$build_deposit_receipt_action = '';    
											}*/

											/*if(isset($data->is_individual_vehicle) && $data->is_individual_vehicle == '1')
											{
												$fair_charge_href =  $this->module_url_path.'/fair_charge_request/'.base64_encode($data->id);
												$build_fair_charge_action = '<a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip review-cars" href="'.$fair_charge_href.'" title="View Fare Charge Request"><i class="fa fa-exchange"></i></a>';
											}
											else
											{
												$build_fair_charge_action = '';    
											}*/
											
											$driver_earning_href =  $this->module_url_path.'/driver_earning/'.base64_encode($data->id);
											$build_driver_earning_action = '<a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip" href="'.$driver_earning_href.'" title="View Driver Earning"><i class="fa fa-money"></i></a>';

											// return  $build_view_action." ".$build_edit_action." ".$build_delete_action." ".$build_reviews_action." ".$build_fair_charge_action." ".$build_deposit_receipt_action." ".$build_driver_earning_action;
											return  $build_vehicle_view_action." ".$build_view_action." ".$build_edit_action." ".$build_delete_action." ".$build_reviews_action." ".$build_deposit_receipt_action." ".$build_driver_earning_action;
											
										})
										->make(true);

		$build_result = $json_result->getData();
		return response()->json($build_result);
	}

	private function get_driver_details($request)
	{     
		$driver_type = 'admin';
		if($request->has('driver_type') && $request->input('driver_type')!=''){
			$driver_type = $request->input('driver_type');
		}

		$role = 'driver';
		$user_details             = $this->BaseModel->getTable();
		$prefixed_user_details    = DB::getTablePrefix().$this->BaseModel->getTable();

		$user_role_table          = $this->UserRoleModel->getTable();
		$prefixed_user_role_table = DB::getTablePrefix().$this->UserRoleModel->getTable();

		$role_table               = $this->RoleModel->getTable();
		$prefixed_role_table      = DB::getTablePrefix().$this->RoleModel->getTable();

		$driver_car_relation_table          = $this->DriverCarRelationModel->getTable();
		$prefixed_driver_car_relation_table = DB::getTablePrefix().$this->DriverCarRelationModel->getTable();

		$obj_user = DB::table($user_details)
									->select(DB::raw(
														$prefixed_user_details.".id as id,".
														"CONCAT(".$prefixed_user_details.".first_name,' ',".$prefixed_user_details.".last_name) as user_name,".
														$prefixed_user_details.".email,".
														// $prefixed_user_details.".mobile_no as contact_number,".
														"CONCAT(".$prefixed_user_details.".country_code,'',".$prefixed_user_details.".mobile_no) as contact_number,".

														$prefixed_user_details.".company_id,".
														$prefixed_user_details.".is_company_driver,".
														"company.company_name as company_name,".
														$prefixed_user_details.".is_active,".
														$prefixed_user_details.".account_status,".
														$prefixed_driver_car_relation_table.".vehicle_id, ".
														$prefixed_driver_car_relation_table.".is_individual_vehicle"

												))
									->join($user_role_table,$user_details.'.id','=',$user_role_table.'.user_id')
									->leftjoin("users AS company", "company.id", '=', $user_details.'.company_id')
									->join($prefixed_driver_car_relation_table,$user_details.'.id','=',$prefixed_driver_car_relation_table.'.driver_id')
									->join($role_table, function ($join) use($role_table,$user_role_table,$role) {
	                                    $join->on($role_table.'.id', ' = ',$user_role_table.'.role_id')
	                                         ->where('slug','=',$role);
	                                });
									// ->where($user_details.'.is_deleted','0');

		if($driver_type == 'admin'){
			$obj_user = $obj_user->where($user_details.'.is_company_driver','0')
  								 ->where($prefixed_driver_car_relation_table.'.is_individual_vehicle','0');
		}
		if($driver_type == 'individual'){
			$obj_user = $obj_user->where($user_details.'.is_company_driver','0')
  								 ->where($prefixed_driver_car_relation_table.'.is_individual_vehicle','1');
		}
		
		if($driver_type == 'company'){
			$obj_user = $obj_user->where($user_details.'.is_company_driver','1');
		}
		$obj_user = $obj_user->whereNull($user_details.'.deleted_at')
							 ->orderBy($user_details.'.id','DESC');

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

		if(isset($arr_search_column['q_company_id']) && $arr_search_column['q_company_id']!="")
		{
			$search_term      = $arr_search_column['q_company_id'];
			$obj_user = $obj_user->where($user_details.'.company_id',$search_term);
		}

		if(isset($arr_search_column['q_contact_number']) && $arr_search_column['q_contact_number']!="")
		{
			$search_term      = $arr_search_column['q_contact_number'];
			/*$obj_user = $obj_user->where($user_details.'.mobile_no','LIKE', '%'.$search_term.'%');*/
			$obj_user = $obj_user->whereRaw(" ( CONCAT(".$prefixed_user_details.".country_code,'',".$prefixed_user_details.".mobile_no)  LIKE  '%".$search_term."%' ) "); 
		}
		return $obj_user;
	} 

	public function create()
	{
		$arr_vehicle_types = $this->CommonDataService->get_vehicle_types();
        $arr_vehicle_brands = $this->CommonDataService->get_vehicle_brand();

		$this->arr_view_data['page_title']      = "Create ".str_singular( $this->module_title);
		$this->arr_view_data['module_title']    = str_plural($this->module_title);
		$this->arr_view_data['module_url_path'] = $this->module_url_path;
		$this->arr_view_data['theme_color']     = $this->theme_color;
		
		$this->arr_view_data['arr_vehicle_types'] = $arr_vehicle_types;
        $this->arr_view_data['arr_vehicle_brands'] = $arr_vehicle_brands;

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
		$arr_rules['post_code']  = "required";

		if($request->input('is_own_vehicle') && $request->input('is_own_vehicle') == '1')
		{
			$arr_rules['vehicle_type']                = "required";
	        $arr_rules['vehicle_brand']               = "required";
	        $arr_rules['vehicle_model']               = "required";
	        $arr_rules['vehicle_number']              = "required";
		}

		$validator = Validator::make($request->all(),$arr_rules);
		
		if($validator->fails())
		{
			Flash::error('Please fill all required fields.');
			return redirect()->back()->withErrors($validator)->withInput($request->all());
		}

		$arr_user_role = ['ADMIN','COMPANY','DRIVER'];
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
		$arr_data['is_active']      = 1;
		$arr_data['account_status'] = 'approved';
		$arr_data['user_type'] 	    = 'DRIVER';

		$reset_password_mandatory = '0';
        if($request->has('reset_password_mandatory') && $request->input('reset_password_mandatory') == 'on')
        {
        	$reset_password_mandatory = '1';
        }
		$arr_data['reset_password_mandatory'] 	    = $reset_password_mandatory;
		
		if($request->hasFile('profile_image'))
		{
			$file_name = $request->input('profile_image');
			$file_extension = strtolower($request->file('profile_image')->getClientOriginalExtension());
			if(in_array($file_extension,['png','jpg','jpeg']))
			{
				$file_name = time().uniqid().'.'.$file_extension;
				$isUpload = $request->file('profile_image')->move($this->user_profile_base_img_path , $file_name);
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
				$arr_data['is_driving_license_verified'] = 'APPROVED';
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
			$role = Sentinel::findRoleBySlug('driver');
			$obj_user->roles()->attach($role);             
			
			/*Driver car mapping*/
			$driver_id = isset($obj_user->id) ? $obj_user->id :0;
			$vehicle_id = 0;

			$is_individual_vehicle = '0';

			if($request->input('is_own_vehicle') && $request->input('is_own_vehicle') == '1')
			{
				$arr_vehicle_insert                                = [];
		        $arr_vehicle_insert['vehicle_type_id']             = $request->input('vehicle_type');
		        $arr_vehicle_insert['vehicle_brand_id']            = $request->input('vehicle_brand');
		        $arr_vehicle_insert['vehicle_model_id']            = $request->input('vehicle_model');
		        $arr_vehicle_insert['vehicle_number']              = $request->input('vehicle_number');
		        $arr_vehicle_insert['is_active']                   = 1;
		        $arr_vehicle_insert['is_verified']                 = 1;
		        $arr_vehicle_insert['is_individual_vehicle']       = '1';
			
		        if($request->hasFile('vehicle_image'))
		        {
		            $vehicle_image = $request->input('vehicle_image');
		            $file_extension = strtolower($request->file('vehicle_image')->getClientOriginalExtension());
		            if(in_array($file_extension,['png','jpg','jpeg','pdf']))
		            {
		                $vehicle_image = time().uniqid().'.'.$file_extension;
		                $isUpload = $request->file('vehicle_image')->move($this->vehicle_doc_base_path, $vehicle_image);
		                if($isUpload)
		                {    
		                    $arr_vehicle_insert['vehicle_image'] = $vehicle_image;
		        			$arr_vehicle_insert['is_vehicle_image_verified']           = 'APPROVED';
		                }
		            }
		        }

		        if($request->hasFile('registration_doc'))
		        {
		            $registration_doc = $request->file('registration_doc');

		            $file_extension = strtolower($request->file('registration_doc')->getClientOriginalExtension());
		            if(in_array($file_extension,['png','jpg','jpeg','pdf']))
		            {
		                $registration_doc = time().uniqid().'.'.$file_extension;
		                $isUpload = $request->file('registration_doc')->move($this->vehicle_doc_base_path, $registration_doc);
		                if($isUpload)
		                {    
		                    $arr_vehicle_insert['registration_doc'] = $registration_doc;
					        $arr_vehicle_insert['is_registration_doc_verified']        = 'APPROVED';
		                }
		              
		            }
		        }

		        if($request->hasFile('proof_of_inspection'))
		        {
		            $proof_of_inspection = $request->file('proof_of_inspection');

		            $file_extension = strtolower($request->file('proof_of_inspection')->getClientOriginalExtension());
		            if(in_array($file_extension,['png','jpg','jpeg','pdf']))
		            {
		                $proof_of_inspection = time().uniqid().'.'.$file_extension;
		                $isUpload = $request->file('proof_of_inspection')->move($this->vehicle_doc_base_path, $proof_of_inspection);
		                if($isUpload)
		                {    
		                    $arr_vehicle_insert['proof_of_inspection_doc'] = $proof_of_inspection;
		                    $arr_vehicle_insert['is_insurance_doc_verified']           = 'APPROVED';
		                }
		              
		            }
		        }

		        if($request->hasFile('insurance_doc'))
		        {
		            $insurance_doc = $request->file('insurance_doc');
		            $file_extension = strtolower($request->file('insurance_doc')->getClientOriginalExtension());
		            if(in_array($file_extension,['png','jpg','jpeg','pdf']))
		            {
		                $insurance_doc = time().uniqid().'.'.$file_extension;
		                $isUpload = $request->file('insurance_doc')->move($this->vehicle_doc_base_path, $insurance_doc);
		                if($isUpload)
		                {    
		                    $arr_vehicle_insert['insurance_doc'] = $insurance_doc;
		                    $arr_vehicle_insert['is_proof_of_inspection_doc_verified'] = 'APPROVED';
		                }
		               // dd($insurance_doc);
		            }
		        }

		        if($request->hasFile('driving_doc'))
		        {
		            $driving_doc = $request->file('driving_doc');
		            $file_extension = strtolower($request->file('driving_doc')->getClientOriginalExtension());
		            if(in_array($file_extension,['png','jpg','jpeg','pdf']))
		            {
		                $driving_doc = time().uniqid().'.'.$file_extension;
		                $isUpload = $request->file('driving_doc')->move($this->vehicle_doc_base_path, $driving_doc);
		                if($isUpload)
		                {    
		                    $arr_vehicle_insert['dmv_driving_record'] = $driving_doc;
		                    $arr_vehicle_insert['is_dmv_driving_record_verified']      = 'APPROVED';
		                }
		               // dd($driving_doc);
		            }
		        }

		        if($request->hasFile('usdot_doc'))
		        {
		            $usdot_doc = $request->file('usdot_doc');
		            $file_extension = strtolower($request->file('usdot_doc')->getClientOriginalExtension());
		            if(in_array($file_extension,['png','jpg','jpeg','pdf']))
		            {
		                $usdot_doc = time().uniqid().'.'.$file_extension;
		                $isUpload = $request->file('usdot_doc')->move($this->vehicle_doc_base_path, $usdot_doc);
		                if($isUpload)
		                {    
		                    $arr_vehicle_insert['usdot_doc'] = $usdot_doc;
		                    $arr_vehicle_insert['is_usdot_doc_verified'] = 'APPROVED';
		                }
		            }
		        }

		        if($request->hasFile('mc_doc'))
		        {
		            $mc_doc = $request->file('mc_doc');
		            $file_extension = strtolower($request->file('mc_doc')->getClientOriginalExtension());
		            if(in_array($file_extension,['png','jpg','jpeg','pdf']))
		            {
		                $mc_doc = time().uniqid().'.'.$file_extension;
		                $isUpload = $request->file('mc_doc')->move($this->vehicle_doc_base_path, $mc_doc);
		                if($isUpload)
		                {    
		                    $arr_vehicle_insert['mc_doc'] = $mc_doc;
		                    $arr_vehicle_insert['is_mcdoc_doc_verified'] = 'APPROVED';
		                }
		            }
		        }

		        $obj_vehicle = $this->VehicleModel->create($arr_vehicle_insert);

		        $vehicle_id = isset($obj_vehicle->id) ? $obj_vehicle->id : 0;
		        $is_individual_vehicle = '1';
			}
			
			$arr_driver_car_relation =
										[
											'driver_id'             => $driver_id,
											'vehicle_id'            => $vehicle_id,
											'is_car_assign'         => 0,
											'is_individual_vehicle' => $is_individual_vehicle
										];

			$this->DriverCarRelationModel->create($arr_driver_car_relation);

			$arr_driver_status =
									[
										'driver_id'         => $driver_id,
										'status'            => 'AVAILABLE',
										'current_latitude'  => $request->input('lat'),
										'current_longitude' => $request->input('long')
									];
			$this->DriverStatusModel->create($arr_driver_status);

			$arr_fair_charge = 
										[
			                				'driver_id'   => $driver_id,
			                				'fair_charge' => 0
			                			];

            $this->DriverFairChargeModel->create($arr_fair_charge);  

			$arr_notification_data = $this->built_notification_data_info($arr_data,'REGISTER'); 
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
								->select('id','first_name','last_name','driving_license','profile_image','email','mobile_no','country_code','password','company_name','country_name','state_name','city_name','address','post_code','dob')
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

		$this->arr_view_data['driving_license_public_path'] = $this->driving_license_public_path;
		$this->arr_view_data['driving_license_base_path']   = $this->driving_license_base_path;

		return view($this->module_view_folder.'.view',$this->arr_view_data);
	}
	
	public function edit($enc_id)
	{
		$id = base64_decode($enc_id);

		$arr_data = [];

		$obj_user = $this->UserModel
							->with('driver_car_details.vehicle_details')
							->select('id','first_name','last_name','driving_license','profile_image','email','country_code','mobile_no','password','country_name','state_name','city_name','company_name','address','post_code','latitude','longitude')
							->where('id',$id)
							->first();
		if($obj_user)
		{
			$arr_data = $obj_user->toArray();
		}
		
		// dd($arr_data);
		$arr_vehicle_types = $this->CommonDataService->get_vehicle_types();
        $arr_vehicle_brands = $this->CommonDataService->get_vehicle_brand();


		$this->arr_view_data['edit_mode']                    = TRUE;
		$this->arr_view_data['page_title']                   = "Edit ".str_singular($this->module_title);
		$this->arr_view_data['module_title']                 = str_plural($this->module_title);
		$this->arr_view_data['theme_color']                  = $this->theme_color;
		$this->arr_view_data['module_url_path']              = $this->module_url_path;
		$this->arr_view_data['enc_id']                       = $enc_id;
		$this->arr_view_data['arr_data']                     = $arr_data;
		$this->arr_view_data['arr_vehicle_types'] = $arr_vehicle_types;
        $this->arr_view_data['arr_vehicle_brands'] = $arr_vehicle_brands;

		$this->arr_view_data['user_profile_public_img_path'] = $this->user_profile_public_img_path;
		

		return view($this->module_view_folder.'.edit', $this->arr_view_data);    
	}

	public function update(Request $request)
	{
		$arr_rules = [];
		$arr_rules['first_name'] = "required";
		$arr_rules['last_name']  = "required";
		$arr_rules['mobile_no']  = "required";
		$arr_rules['post_code']  = "required";

		if($request->input('is_individual_vehicle') && $request->input('is_individual_vehicle') == '1')
		{
			$arr_rules['vehicle_type']                = "required";
	        $arr_rules['vehicle_brand']               = "required";
	        $arr_rules['vehicle_model']               = "required";
	        $arr_rules['vehicle_number']              = "required";
		}

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
											->where('id','!=',$user_id)
											->where('user_type','DRIVER')
											->count();
		
			if($is_email_duplicate>0)
			{
				Flash::error('User with this email address already exists.');
				return redirect()->back()->withInput($request->all());
			}
		}

		$is_mobile_no_duplicate = $this->UserModel
											->where('mobile_no',trim($request->input('mobile_no')))
											->where('user_type','DRIVER')
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
		//$arr_update['company_name'] = $request->input('company_name');
		$arr_update['country_name'] = $request->input('country_name');
		$arr_update['state_name']   = $request->input('state_name');
		$arr_update['city_name']    = $request->input('city_name');
		$arr_update['address']      = $request->input('address');
		$arr_update['latitude']    = $request->input('lat');
		$arr_update['longitude']    = $request->input('long');
		$arr_update['post_code']    = $request->input('post_code');
		
		$file_name = $driving_license =  '';
		$oldImage = $request->input('oldimage');
		$olddriving_license = $request->input('olddriving_license');

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
		if($request->hasFile('driving_license'))
		{
			$driving_license = $request->input('driving_license');
			$file_extension = strtolower($request->file('driving_license')->getClientOriginalExtension());
			if(in_array($file_extension,['png','jpg','jpeg','pdf']))
			{
				$driving_license = time().uniqid().'.'.$file_extension;
				$isUpload = $request->file('driving_license')->move($this->driving_license_base_path , $driving_license);
				if($isUpload)
				{
					@unlink($this->driving_license_base_path.$olddriving_license);
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
			$driving_license = $olddriving_license;
		}

		$arr_update['profile_image']   = $file_name;
		$arr_update['driving_license'] = $driving_license;
		$arr_update['is_driving_license_verified'] = 'APPROVED';

		$status = $this->UserModel->where('id',$user_id)->update($arr_update);
		if($status)
		{
			if($request->input('is_individual_vehicle') && $request->input('is_individual_vehicle') == '1')
			{
				$vehicle_id = $request->input('vehicle_id');

				$arr_vehicle_update                                = [];
		        $arr_vehicle_update['vehicle_type_id']             = $request->input('vehicle_type');
		        $arr_vehicle_update['vehicle_brand_id']            = $request->input('vehicle_brand');
		        $arr_vehicle_update['vehicle_model_id']            = $request->input('vehicle_model');
		        $arr_vehicle_update['vehicle_number']              = $request->input('vehicle_number');
		        $arr_vehicle_update['is_active']                   = 1;
		        $arr_vehicle_update['is_verified']                 = 1;
			
		        if($request->hasFile('vehicle_image'))
		        {
		            $vehicle_image = $request->input('vehicle_image');
		            $file_extension = strtolower($request->file('vehicle_image')->getClientOriginalExtension());
		            if(in_array($file_extension,['png','jpg','jpeg','pdf']))
		            {
		                $vehicle_image = time().uniqid().'.'.$file_extension;
		                $isUpload = $request->file('vehicle_image')->move($this->vehicle_doc_base_path, $vehicle_image);
		                if($isUpload)
		                {    
		                    $arr_vehicle_update['vehicle_image'] = $vehicle_image;
		        			$arr_vehicle_update['is_vehicle_image_verified']           = 'APPROVED';
		                }
		            }
		        }

		        if($request->hasFile('registration_doc'))
		        {
		            $registration_doc = $request->file('registration_doc');

		            $file_extension = strtolower($request->file('registration_doc')->getClientOriginalExtension());
		            if(in_array($file_extension,['png','jpg','jpeg','pdf']))
		            {
		                $registration_doc = time().uniqid().'.'.$file_extension;
		                $isUpload = $request->file('registration_doc')->move($this->vehicle_doc_base_path, $registration_doc);
		                if($isUpload)
		                {    
		                    $arr_vehicle_update['registration_doc'] = $registration_doc;
					        $arr_vehicle_update['is_registration_doc_verified']        = 'APPROVED';
		                }
		              
		            }
		        }

		        if($request->hasFile('proof_of_inspection'))
		        {
		            $proof_of_inspection = $request->file('proof_of_inspection');

		            $file_extension = strtolower($request->file('proof_of_inspection')->getClientOriginalExtension());
		            if(in_array($file_extension,['png','jpg','jpeg','pdf']))
		            {
		                $proof_of_inspection = time().uniqid().'.'.$file_extension;
		                $isUpload = $request->file('proof_of_inspection')->move($this->vehicle_doc_base_path, $proof_of_inspection);
		                if($isUpload)
		                {    
		                    $arr_vehicle_update['proof_of_inspection_doc'] = $proof_of_inspection;
		                    $arr_vehicle_update['is_insurance_doc_verified']           = 'APPROVED';
		                }
		              
		            }
		        }

		        if($request->hasFile('insurance_doc'))
		        {
		            $insurance_doc = $request->file('insurance_doc');
		            $file_extension = strtolower($request->file('insurance_doc')->getClientOriginalExtension());
		            if(in_array($file_extension,['png','jpg','jpeg','pdf']))
		            {
		                $insurance_doc = time().uniqid().'.'.$file_extension;
		                $isUpload = $request->file('insurance_doc')->move($this->vehicle_doc_base_path, $insurance_doc);
		                if($isUpload)
		                {    
		                    $arr_vehicle_update['insurance_doc'] = $insurance_doc;
		                    $arr_vehicle_update['is_proof_of_inspection_doc_verified'] = 'APPROVED';
		                }
		               // dd($insurance_doc);
		            }
		        }

		        if($request->hasFile('driving_doc'))
		        {
		            $driving_doc = $request->file('driving_doc');
		            $file_extension = strtolower($request->file('driving_doc')->getClientOriginalExtension());
		            if(in_array($file_extension,['png','jpg','jpeg','pdf']))
		            {
		                $driving_doc = time().uniqid().'.'.$file_extension;
		                $isUpload = $request->file('driving_doc')->move($this->vehicle_doc_base_path, $driving_doc);
		                if($isUpload)
		                {    
		                    $arr_vehicle_update['dmv_driving_record'] = $driving_doc;
		                    $arr_vehicle_update['is_dmv_driving_record_verified']      = 'APPROVED';
		                }
		               // dd($driving_doc);
		            }
		        }

		        if($request->hasFile('usdot_doc'))
		        {
		            $usdot_doc = $request->file('usdot_doc');
		            $file_extension = strtolower($request->file('usdot_doc')->getClientOriginalExtension());
		            if(in_array($file_extension,['png','jpg','jpeg','pdf']))
		            {
		                $usdot_doc = time().uniqid().'.'.$file_extension;
		                $isUpload = $request->file('usdot_doc')->move($this->vehicle_doc_base_path, $usdot_doc);
		                if($isUpload)
		                {    
		                    $arr_vehicle_update['usdot_doc'] = $usdot_doc;
		                    $arr_vehicle_update['is_usdot_doc_verified'] = 'APPROVED';
		                }
		            }
		        }

		        if($request->hasFile('mc_doc'))
		        {
		            $mc_doc = $request->file('mc_doc');
		            $file_extension = strtolower($request->file('mc_doc')->getClientOriginalExtension());
		            if(in_array($file_extension,['png','jpg','jpeg','pdf']))
		            {
		                $mc_doc = time().uniqid().'.'.$file_extension;
		                $isUpload = $request->file('mc_doc')->move($this->vehicle_doc_base_path, $mc_doc);
		                if($isUpload)
		                {    
		                    $arr_vehicle_update['mc_doc'] = $mc_doc;
		                    $arr_vehicle_update['is_mcdoc_doc_verified'] = 'APPROVED';
		                }
		            }
		        }

		        if($vehicle_id == 0)
		        {
		        	$obj_create_status = $this->VehicleModel->create($arr_vehicle_update);
		        	$new_vehicle_id =  isset($obj_create_status->id) ? $obj_create_status->id : 0;
		        	$this->DriverCarRelationModel->where('driver_id',$user_id)->update(['vehicle_id'=>$new_vehicle_id]);
		        }
		        else
		        {
		        	$this->VehicleModel->where('id',$vehicle_id)->update($arr_vehicle_update);
		        }
			}
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
        
        $reset_password_mandatory = '0';
        if($request->has('reset_password_mandatory') && $request->input('reset_password_mandatory') == 'on')
        {
        	$reset_password_mandatory = '1';
        }
        if($enc_id == '')
        {
            Flash::error('Driver indetifier not found,Please try again');
            return redirect($this->module_url_path);
        }

        $password         = $request->input('password');
        $confirm_password = $request->input('confirm_password');
        if($password != $confirm_password)
        {
            Flash::error('Password & Confirm Password must be same.');
            return redirect()->back();
        }

        if($this->check_driver_current_trip($enc_id))
        {
            Flash::error('Driver is currently in busy in trip,cannot reset password');
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
                                                    'user_type'         => 'DRIVER',

                                                ];
                    $this->NotificationsService->send_on_signal_notification($arr_notification_data);


                    Flash::success('Driver password changed successfully.');
                    return redirect()->back();
                }
                else
                {
                    Flash::error('Problem Occurred, While changing driver password');
                    return redirect()->back();
                }
            } 
            else
            {
                Flash::error('Unable to reset driver password,Please try again.');
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

	public function fair_charge_request($enc_id)
	{
		$enc_id = base64_decode($enc_id);
		
		$arr_user = $arr_data = [];

		$obj_data = $this->DriverFairChargeRequestModel
								->where('driver_id',$enc_id)
								->orderBy('id','DESC')
								->get();
		if($obj_data)
		{
			$arr_data = $obj_data->toArray();
		}

		if(isset($arr_data) && sizeof($arr_data)>0){
            foreach ($arr_data as $key => $value) 
            {
                if($key == 0){
                    $arr_data[$key]['is_editable'] = 1;
                }else{
                    $arr_data[$key]['is_editable'] = 0;
                }
            }
        }
        
		$obj_user = $this->UserModel
		->where('id',$enc_id)
		->first();
		if ($obj_user) 
		{
			$arr_user = $obj_user->toArray();             
		}                                                     

		$this->arr_view_data['edit_mode']                    = TRUE;
		$this->arr_view_data['enc_id']                       = base64_encode($enc_id);
		$this->arr_view_data['page_title']                   = str_singular($this->module_title)." Fare Charges";
		$this->arr_view_data['module_title']                 = str_plural($this->module_title)." Fare Charges";
		$this->arr_view_data['main_module_title']            = str_plural($this->module_title);
		$this->arr_view_data['theme_color']                  = $this->theme_color;
		$this->arr_view_data['module_url_path']              = $this->module_url_path;
		$this->arr_view_data['arr_data']                     = $arr_data;
		$this->arr_view_data['arr_user']                     = $arr_user;

		return view($this->module_view_folder.'.driver_fair_charge.driver_fair_charge_request_list',$this->arr_view_data);
	}

	public function change_request_status($enc_id,$type)
	{
		$enc_id = base64_decode($enc_id);
		$type   = base64_decode($type);

		$update_status = 'REQUEST';
		
		if($type == 'approve')
		{
			$update_status = 'APPROVE';
		}        
		else if($type == 'reject')
		{
			$update_status = 'REJECT';
		}        

		$obj_data = $this->DriverFairChargeRequestModel
		->where('id',$enc_id)
		->first();
		if($obj_data)
		{
			$obj_data->status       = $update_status;
			$obj_data->save();
			
			$driver_id              = isset($obj_data->driver_id)?$obj_data->driver_id:0;

			if($update_status == 'APPROVE' || $update_status == 'REJECT')
			{
				$fair_charge = isset($obj_data->fair_charge)?$obj_data->fair_charge:0;

				$arr_create_or_update['driver_id']   = $driver_id;
				$arr_create_or_update['fair_charge'] = $fair_charge;

				$arr_where['driver_id'] = $driver_id;

				$this->DriverFairChargeModel->updateOrCreate($arr_where,$arr_create_or_update);

				//$arr_data['user_id']           = $this->CommonDataService->get_admin_id();;
				//$arr_data['user_type']         = 'ADMIN';
				$arr_data['user_id']           = $driver_id;
				$arr_data['user_type']         = 'DRIVER';
				$arr_data['notification_type'] = 'Fair Charge Request ';
				
				$title = '';
				if($update_status == 'APPROVE')
				{
					$title = 'Fair Charge request successfully accepted by Admin.';
				}
				else if($update_status == 'REJECT')
				{
					$title = 'Fair Charge request rejected by Admin.';   
				}

				$arr_data['title']             = $title;
				$arr_data['view_url']          = '/'.config('app.project.admin_panel_slug')."/driver/fair_charge_request".'/'.base64_encode($driver_id);
				
				$this->NotificationsService->store_notification($arr_data);

				//send one signal notification to driver
                $arr_notification_data = 
                                            [
                                                'title'             => $title,
                                                'notification_type' => 'FARE_CHARGE',
                                                'enc_user_id'       => $driver_id,
                                                'user_type'         => 'DRIVER',

                                            ];
                 $this->NotificationsService->send_on_signal_notification($arr_notification_data);
                 
				$flash_msg = '';
				if($update_status == 'APPROVE')
				{
					$flash_msg = str_singular($this->module_title).' Fare charge request approved successfully.';
				}
				else if($update_status == 'REJECT')
				{
					$flash_msg = str_singular($this->module_title).' Fare charge request rejected successfully.';
				}
				Flash::success($flash_msg);    
			}
			else
			{
				Flash::success(str_singular($this->module_title).' fare charge request rejected.');   
			}
			
		}
		else
		{
			Flash::error('Problem Occurred, While Updating '.str_singular($this->module_title));
		}

		return redirect()->back();
	}

	public function deposit_receipt($enc_id)
	{
		$driver_id = base64_decode($enc_id);
		
		$obj_driver    = $this->UserModel
									->with('company_details')
									->select('id','company_id','is_company_driver','first_name','last_name','profile_image')
									->where('id',$driver_id)
									->first();
		$arr_driver = [];
		if ($obj_driver) 
		{
			$arr_driver = $obj_driver->toArray();
		}
		
		$is_company_driver = isset($arr_driver['is_company_driver']) ? $arr_driver['is_company_driver'] :'';

		$arr_admin_deposit_money = [];
	
		$to_user_type = '';
		if(isset($is_company_driver) && $is_company_driver == '1'){
			$to_user_type = 'COMPANY_DRIVER';
		}
		else if(isset($is_company_driver) && $is_company_driver == '0'){
			$to_user_type = 'DRIVER';
		}
		
		$obj_admin_deposit_money = $this->DepositMoneyModel
												->with('booking_master_details')
												->where('to_user_id',$driver_id)
												->where('to_user_type',$to_user_type)
												->orderBy('id','DESC')
												->get();


		if($obj_admin_deposit_money){
			$arr_admin_deposit_money = $obj_admin_deposit_money->toArray();
		} 
		
		$arr_driver_balance_information = $this->get_driver_balance_information($driver_id,$is_company_driver);
		
		$this->arr_view_data['arr_driver_balance_information'] = $arr_driver_balance_information;
		$this->arr_view_data['page_title']                     = "Deposit Money & Upload Receipt";
		$this->arr_view_data['module_title']                   = str_plural($this->module_titles);
		$this->arr_view_data['module_title_deposit']           = str_plural($this->module_title);
		$this->arr_view_data['module_url_path']                = $this->module_url_path;
		$this->arr_view_data['theme_color']                    = $this->theme_color;
		$this->arr_view_data['driver_id']                      = base64_encode($driver_id);
		$this->arr_view_data['arr_admin_deposit_money']        = $arr_admin_deposit_money;
		$this->arr_view_data['arr_driver']                     = $arr_driver;
		$this->arr_view_data['receipt_image_public_path']      = $this->receipt_image_public_path ;
		$this->arr_view_data['receipt_image_base_path']        = $this->receipt_image_base_path   ;

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
		$driver_id = base64_decode($request->input('driver_id'));

		$arr_data               	= [];
		$arr_data['from_user_id'] 	= $this->CommonDataService->get_admin_id();
		$arr_data['to_user_id'] 	= $driver_id;
		$arr_data['transaction_id'] = $this->genrate_transaction_unique_number();
		$arr_data['amount_paid'] 	= doubleval($request->input('amount_paid'));
		$arr_data['note'] 			= $request->input('note');
		$arr_data['from_user_type'] = 'ADMIN';
		$arr_data['to_user_type'] 	= 'DRIVER';
		$arr_data['status'] 		= 'PENDING';
		$arr_data['date'] 		    = date('Y-m-d');

		$money = 0;
		$money = doubleval($request->input('amount_paid'));

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
			$arr_driver  = $this->CommonDataService->get_user_details($driver_id);
			$arr_data_details = array_merge($arr_driver,$arr_data);		

			$arr_notification_data = $this->built_notification_data_info($arr_data_details,'ADMIN_PAYMENT'); 
	        $this->NotificationsService->store_notification($arr_notification_data);

	        $arr_notification_data = $this->built_notification_data_info($arr_data_details,'DRIVER_PAYMENT'); 
	        $this->NotificationsService->store_notification($arr_notification_data);

	        //send one signal notification to driver
	        $arr_notification_data = 
                                        [
                                            'title'             => 'Admin deposited money of $'.$money,
                                            'notification_type' => 'DEPOSIT_MONEY',
                                            'enc_user_id'       => $driver_id,
                                            'user_type'         => 'DRIVER',

                                        ];
             $this->NotificationsService->send_on_signal_notification($arr_notification_data);

  			//$arr_mail_data = $this->built_mail_data_payment($arr_data_details); 
			// $this->EmailService->send_mail_with_attachments($arr_mail_data);

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
			$driver_stripe_account_id = isset($arr_deposit_money['to_user_details']['stripe_account_id'])  ?$arr_deposit_money['to_user_details']['stripe_account_id'] : '';
			if(isset($driver_stripe_account_id) && $driver_stripe_account_id!='')
			{
				$enc_driver_id            = isset($arr_deposit_money['to_user_id']) ? $arr_deposit_money['to_user_id'] : 0;
				$driver_first_name        = isset($arr_deposit_money['to_user_details']['first_name']) ? $arr_deposit_money['to_user_details']['first_name'] : '';
				$driver_last_name         = isset($arr_deposit_money['to_user_details']['last_name']) ? $arr_deposit_money['to_user_details']['last_name'] : '';
				$driver_email             = isset($arr_deposit_money['to_user_details']['email']) ? $arr_deposit_money['to_user_details']['email'] : '';
				$driver_mobile_no         = isset($arr_deposit_money['to_user_details']['mobile_no']) ? $arr_deposit_money['to_user_details']['mobile_no'] : '';
				$company_name             = isset($arr_deposit_money['to_user_details']['company_name']) ? $arr_deposit_money['to_user_details']['company_name'] : '';
				$is_company_driver        = isset($arr_deposit_money['to_user_details']['is_company_driver']) ? $arr_deposit_money['to_user_details']['is_company_driver'] : '';
				$booking_id               = isset($arr_deposit_money['booking_master_details']['id']) ? $arr_deposit_money['booking_master_details']['id'] : '';
				$booking_unique_id        = isset($arr_deposit_money['booking_master_details']['booking_unique_id']) ? $arr_deposit_money['booking_master_details']['booking_unique_id'] : '';
				$driver_earning_amount    = isset($arr_deposit_money['amount_paid']) ? round($arr_deposit_money['amount_paid'],2) : 0;
				$total_driver_cent_charge = (round($driver_earning_amount,2) * 100);

        		$arr_payment_details = [
                                      'driver_id'                => $enc_driver_id,
                                      'driver_first_name'        => $driver_first_name,
                                      'driver_last_name'         => $driver_last_name,
                                      'driver_email'             => $driver_email,
                                      'driver_mobile_no'         => $driver_mobile_no,
                                      'is_company_driver'        => $is_company_driver,
                                      'company_name'             => $company_name,
                                      'booking_id'               => $booking_id,
                                      'booking_unique_id'        => $booking_unique_id,
                                      'driver_stripe_account_id' => $driver_stripe_account_id,
                                      'driver_earning_amount'    => $driver_earning_amount,
                                      'total_driver_cent_charge' => $total_driver_cent_charge
                                    ];

        		$arr_stripe_response = $this->StripeService->make_driver_payment($arr_payment_details);
        		
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
		        	$arr_data_details = array_merge($arr_deposit_money,$arr_payment_details);     

		            /*build admin notification data*/            
		            $arr_admin_notification_data = $this->built_payment_notification_data($arr_data_details,'ADMIN_DRIVER'); 
		            
		            $this->NotificationsService->store_notification($arr_admin_notification_data);
		            
		            /*build driver notification data*/
		            $arr_driver_notification_data = $this->built_payment_notification_data($arr_data_details,'DRIVER'); 

		            $this->NotificationsService->store_notification($arr_driver_notification_data);

		            //send one signal notification to driver
		            $title = '';
		            $transaction_id = isset($arr_deposit_money['transaction_id']) ? $arr_deposit_money['transaction_id'] : '';

		            if($is_company_driver == '0')
		            {
		                if(isset($driver_deposit_money_status) && $driver_deposit_money_status == 'SUCCESS')
		                {   
		                    $title = 'Payment for tripe #'.$booking_unique_id.' is successfully received to your account with transaction id #'.$transaction_id;
		                }
		                else if(isset($driver_deposit_money_status) && $driver_deposit_money_status == 'FAILED')
		                {
		                    $title = 'Payment for tripe #'.$booking_unique_id.' is failed  with the transaction id #'.$transaction_id.' Please contact '.config('app.project.name').' Admin';
		                }
		                else if(isset($driver_deposit_money_status) && $driver_deposit_money_status == 'PENDING')
		                {
		                    $title = 'Payment for tripe #'.$booking_unique_id.' is pending with the transaction id #'.$transaction_id.' Please contact '.config('app.project.name').' Admin';
		                }   
		            }
		            else if($is_company_driver == '1')
		            {
		                if(isset($driver_deposit_money_status) && $driver_deposit_money_status == 'SUCCESS')
		                {   
		                    $title = 'Payment for tripe #'.$booking_unique_id.' is successfully received to your account with transaction id #'.$transaction_id;
		                }
		                else if(isset($driver_deposit_money_status) && $driver_deposit_money_status == 'FAILED')
		                {
		                    $title = 'Payment for tripe #'.$booking_unique_id.' is failed  with the transaction id #'.$transaction_id.' Please contact '.$company_name.' Admin';
		                }
		                else if(isset($driver_deposit_money_status) && $driver_deposit_money_status == 'PENDING')
		                {
		                    $title = 'Payment for tripe #'.$booking_unique_id.' is pending with the transaction id #'.$transaction_id.' Please contact '.$company_name.' Admin';
		                }   
		            }

		            $arr_notification_data = 
		                                        [
		                                            'title'             => $title,
		                                            'notification_type' => 'DEPOSIT_MONEY',
		                                            'enc_user_id'       => $enc_driver_id,
		                                            'user_type'         => 'DRIVER',

		                                        ];
		            $this->NotificationsService->send_on_signal_notification($arr_notification_data);
		            
		            $flash_msg = '';

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
					Flash::error('Unable to make driver payment Please try again.');
					return redirect()->back();
				}
			}
			else
			{
				Flash::error('Driver Stripe account id is empty,cannot process request.');
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
    
	public function driver_earning($enc_id)
	{
		$driver_id = base64_decode($enc_id);

		$obj_driver    = $this->UserModel
									->with('company_details')
									->select('id','company_id','is_company_driver','first_name','last_name','profile_image')
									->where('id',$driver_id)
									->first();
		$arr_driver = [];
		if ($obj_driver) 
		{
			$arr_driver = $obj_driver->toArray();
		}
		
		$is_company_driver = isset($arr_driver['is_company_driver']) ? $arr_driver['is_company_driver'] :'';

		$arr_driver_balance_information = $this->get_driver_balance_information($driver_id,$is_company_driver);

		$this->arr_view_data['arr_driver_balance_information'] = $arr_driver_balance_information;
		$this->arr_view_data['page_title']                     = "Manage ".str_plural($this->module_title_earning);
		$this->arr_view_data['module_title']                   = str_plural($this->module_title);
		$this->arr_view_data['module_title_earning']           = str_plural($this->module_title_earning);
		$this->arr_view_data['module_url_path']                = $this->module_url_path;
		$this->arr_view_data['theme_color']                    = $this->theme_color;
		$this->arr_view_data['arr_driver']                     = $arr_driver;
		$this->arr_view_data['driver_id']                      = $enc_id;
		$this->arr_view_data['is_company_driver']              = $is_company_driver;
		
		return view($this->module_view_folder.'.driver_earning', $this->arr_view_data);
	}

	public function get_earning_records(Request $request)
	{
		$arr_current_user_access =[];
		$arr_current_user_access = $request->user()->permissions;

		$obj_bookings        =  $this->get_booking_details($request);

		$json_result     = Datatables::of($obj_bookings);
		
		$json_result     = $json_result->blacklist(['id']);
		$current_context = $this;

		        $json_result    = $json_result
                                    ->editColumn('per_miles_price',function($data) use ($current_context)
                                    {
                                        if($data->booking_status == 'CANCEL_BY_USER')
                                        {
                                            return '-';
                                        }
                                        $per_miles_price       = isset($data->per_miles_price) ? number_format($data->per_miles_price,2) :0;
                                        $per_miles_price = '<i class="fa fa-usd"> </i> '.$per_miles_price;
                                        return $per_miles_price;
                                        
                                    })
                                    ->editColumn('distance',function($data) use ($current_context)
                                    {      
                                        if($data->booking_status == 'CANCEL_BY_USER')
                                        {
                                            return '-';
                                        }           
                                        $distance = isset($data->distance) ? $data->distance:0;
                                        $distance = number_format($distance,2);
                                        $distance = $distance.' <strong>Miles</strong>';
                                        return  $distance;                                        
                                    })
                                    ->editColumn('total_amount',function($data) use ($current_context)
                                    {                 
                                        $total_amount = isset($data->total_amount) ? $data->total_amount:0;
                                        $total_amount = number_format($total_amount,2);
                                        $total_amount = '<i class="fa fa-usd"> </i> '.$total_amount;
                                        return  $total_amount;                                        
                                    })
                                    ->editColumn('applied_promo_code_charge',function($data) use ($current_context)
                                    {       
                                        if($data->booking_status == 'CANCEL_BY_USER')
                                        {
                                            return '-';
                                        } 

                                        $applied_promo_code_charge    = isset($data->applied_promo_code_charge) ? $data->applied_promo_code_charge:0;
                                        $user_bonus_points_usd_amount = isset($data->user_bonus_points_usd_amount) ? $data->user_bonus_points_usd_amount:0;
                                        $total_discount_amount        = ($applied_promo_code_charge + $user_bonus_points_usd_amount);
                                        $total_discount_amount        = number_format($total_discount_amount,2);

                                        $applied_promo_code_charge = '<i class="fa fa-usd"> </i> '.$total_discount_amount;
                                        return  $applied_promo_code_charge;                                        
                                    })
                                    ->editColumn('total_charge',function($data) use ($current_context)
                                    {                 
                                        $total_charge = isset($data->total_charge) ? $data->total_charge:0;
                                        $total_charge = number_format($total_charge,2);
                                        $total_charge = '<i class="fa fa-usd"> </i> '.$total_charge;
                                        return  $total_charge;                                        
                                    })
                                    ->editColumn('driver_amount',function($data) use ($current_context)
                                    {     
                                        if($data->booking_status == 'CANCEL_BY_USER')
                                        {
                                            return '-';
                                        } 
                                        $driver_amount = 0;

                                        if($data->is_individual_vehicle == '1')
                                        {
                                            $driver_amount = isset($data->individual_driver_amount) ? $data->individual_driver_amount : 0;
                                        }
                                        else if($data->is_individual_vehicle == '0')
                                        {
                                            if($data->is_company_driver == '0')
                                            {
                                                $driver_amount = isset($data->admin_driver_amount) ? $data->admin_driver_amount : 0;
                                            }
                                            else if($data->is_company_driver == '1')
                                            {
                                                $company_amount        = isset($data->company_amount) ? $data->company_amount : 0;
                                                $company_driver_amount = isset($data->company_driver_amount) ? $data->company_driver_amount : 0;
                                                $driver_amount         = $company_amount + $company_driver_amount;
                                            }
                                        }
                                        $driver_amount = number_format($driver_amount,2);
                                        $driver_amount = '<i class="fa fa-usd"> </i> '.$driver_amount;
                                        return  $driver_amount;                                        
                                    })
                                    ->editColumn('admin_amount',function($data) use ($current_context)
                                    { 
                                        $admin_amount = isset($data->admin_amount) ? $data->admin_amount:0;
                                        
                                        if($data->booking_status == 'CANCEL_BY_USER')
                                        {
                                            $admin_amount = isset($data->total_amount) ? $data->total_amount:0;
                                        } 
                                        $admin_amount = number_format($admin_amount,2);
                                        $admin_amount = '<i class="fa fa-usd"> </i> '.$admin_amount;
                                        return  $admin_amount;                                        
                                    })
                                    ->editColumn('payment_status',function($data) use ($current_context,$arr_current_user_access)
                                    {
                                        $payment_status = '';
                                        if($data->payment_status == 'PENDING')
                                        {
                                            $payment_status = '<span class="badge badge-warning" style="width:100px">Pending</span>';
                                        }
                                        else if($data->payment_status == 'SUCCESS')
                                        {
                                            $payment_status = '<span class="badge badge-success" style="width:100px">Success</span>';
                                        }
                                        else if($data->payment_status == 'FAILED')
                                        {
                                            $payment_status = '<span class="badge badge-important" style="width:100px">Failed</span>';
                                        }
                                        return  $payment_status;
                                    })
                                    ->editColumn('build_action_btn',function($data) use ($current_context,$arr_current_user_access)
                                    {                 
                                        $build_view_action = '<a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip" href="#" data-id="'.$data->id.'" onclick="earning_details(this); " data-toggle="modal" title="View"><i class="fa fa-eye" ></i></a>';
                                        return  $build_view_action;                                        
                                    })
                                ->make(true);

		$build_result = $json_result->getData();   
		return response()->json($build_result);
	}    

	private function get_booking_details($request)
	{     	
		$driver_id = base64_decode($request->input('driver_id'));

		$booking_details                    = $this->BookingMasterModel->getTable();
        $prefixed_booking_details           = DB::getTablePrefix().$this->BookingMasterModel->getTable();

        $load_post_request_details          = $this->LoadPostRequestModel->getTable();
        $prefixed_load_post_request_details = DB::getTablePrefix().$this->LoadPostRequestModel->getTable();
        
        $user_details                       = $this->UserModel->getTable();
        $prefixed_user_details              = DB::getTablePrefix().$this->UserModel->getTable();

        $obj_user = DB::table($booking_details)
                                ->select(DB::raw(   

                                                    $prefixed_booking_details.".id ,".
                                                    "CONCAT(".$prefixed_user_details.".first_name,' ',".$prefixed_user_details.".last_name) as driver_name,".
                                                    $prefixed_user_details.".id as driver_id,".
                                                    $prefixed_user_details.".company_name,".
                                                    $prefixed_booking_details.".booking_unique_id,".
                                                    "DATE_FORMAT(".$prefixed_booking_details.".booking_date,'%d %b %Y') as booking_date,".
                                                    $prefixed_booking_details.".is_promo_code_applied,".
                                                    $prefixed_booking_details.".promo_code,".
                                                    $prefixed_booking_details.".promo_percentage,".
                                                    $prefixed_booking_details.".promo_max_amount,".
                                                    $prefixed_booking_details.".applied_promo_code_charge,".
                                                    $prefixed_booking_details.".is_company_driver,".
                                                    $prefixed_booking_details.".is_individual_vehicle,".
                                                    $prefixed_booking_details.".starting_price,".
                                                    $prefixed_booking_details.".per_miles_price,".
                                                    $prefixed_booking_details.".per_minute_price,".
                                                    $prefixed_booking_details.".minimum_price,".
                                                    $prefixed_booking_details.".cancellation_base_price,".
                                                    $prefixed_booking_details.".admin_driver_percentage,".
                                                    $prefixed_booking_details.".admin_company_percentage,".
                                                    $prefixed_booking_details.".individual_driver_percentage,".
                                                    $prefixed_booking_details.".company_driver_percentage,".
                                                    $prefixed_booking_details.".is_bonus_used,".
                                                    $prefixed_booking_details.".admin_referral_points,".
                                                    $prefixed_booking_details.".admin_referral_points_price_per_usd,".
                                                    $prefixed_booking_details.".user_bonus_points,".
                                                    $prefixed_booking_details.".user_bonus_points_usd_amount,".
                                                    $prefixed_booking_details.".distance,".
                                                    $prefixed_booking_details.".total_charge,".
                                                    $prefixed_booking_details.".total_amount,".
                                                    $prefixed_booking_details.".admin_amount,".
                                                    $prefixed_booking_details.".company_amount,".
                                                    $prefixed_booking_details.".admin_driver_amount,".
                                                    $prefixed_booking_details.".company_driver_amount,".
                                                    $prefixed_booking_details.".individual_driver_amount,".
                                                    $prefixed_booking_details.".admin_payment_status,".
                                                    $prefixed_booking_details.".booking_status,".
                                                    $prefixed_booking_details.".payment_status"
                                                ))

                                ->join($prefixed_load_post_request_details,$prefixed_load_post_request_details.'.id','=',$booking_details.'.load_post_request_id')
                                ->join($prefixed_user_details,$user_details.'.id','=',$prefixed_load_post_request_details.'.driver_id')                               
                                ->where($booking_details.'.booking_status','COMPLETED')
                                ->where($prefixed_load_post_request_details.'.driver_id',$driver_id)
                                // ->where($booking_details.'.payment_status','SUCCESS')
                                ->orderBy($booking_details.'.created_at','DESC');
        
        /* ---------------- Filtering Logic ----------------------------------*/                    

        $arr_search_column = $request->input('column_filter');
        
        if(isset($arr_search_column['booking_unique_id']) && $arr_search_column['booking_unique_id']!="")
        {
            $search_term      = $arr_search_column['booking_unique_id'];
            $obj_user        = $obj_user->where('booking_unique_id','LIKE', '%'.$search_term.'%');
        }

        if(isset($arr_search_column['booking_date']) && $arr_search_column['booking_date']!="")
        {
            $search_term     = $arr_search_column['booking_date'];
            $obj_user        = $obj_user->having('booking_date','LIKE', '%'.$search_term.'%');
        }

        if(isset($arr_search_column['payment_status']) && $arr_search_column['payment_status']!="")
        {
            $ride_status      = $arr_search_column['payment_status'];
            $obj_user        = $obj_user->where($booking_details.'.payment_status', $ride_status);
        }
        return $obj_user;
	} 

	public function earning_info(Request $request)
    {
    	$id 		 = $request->input('id');

        $arr_bookings = [];

        $obj_bookings   = $this->BookingMasterModel
                                    ->with(['load_post_request_details'=>function($query){
                                        $query->with(['driver_details'=>function($query){
                                            $query->select('id','company_id','first_name','last_name','email','mobile_no','profile_image','is_company_driver','company_name','country_code');
                                            $query->with('driver_car_details.vehicle_details','company_details');

                                        }]);
                                        $query->with(['user_details'=>function($query){
                                            $query->select('id','first_name','last_name','email','country_code','mobile_no','profile_image');
                                        }]);
                                        $query->with('vehicle_details.vehicle_type_details');

                                    }])
                                    ->where('id',$id)
                                    ->first();

        if($obj_bookings)
        {
            $arr_bookings = $obj_bookings->toArray();
        }    
        $arr_data = filter_completed_trip_details($arr_bookings);
        $build_html = '';

        if(isset($arr_data) && sizeof($arr_data)>0)
        {
            $build_html .=  '<div class="review-detais">';
            $build_html .=  '<div class="boldtxts">Booking ID : </div>';
            $build_html .=  '<div class="rightview-txt">';
            $build_html .=  isset($arr_data['booking_unique_id']) ? $arr_data['booking_unique_id'] : '';
            $build_html .=  '</div>';
            $build_html .=  '<div class="clearfix"></div>';
            $build_html .=  '</div>';

            $build_html .=  '<div class="review-detais">';
            $build_html .=  '<div class="boldtxts">Booking Date : </div>';
            $build_html .=  '<div class="rightview-txt">';
            $build_html .=  isset($arr_data['booking_date']) ? $arr_data['booking_date'] : '';
            $build_html .=  '</div>';
            $build_html .=  '<div class="clearfix"></div>';
            $build_html .=  '</div>';

            $build_html .=  '<div class="review-detais">';
            $build_html .=  '<div class="boldtxts">Ride Start Date : </div>';
            $build_html .=  '<div class="rightview-txt">';
            $build_html .=  isset($arr_data['start_datetime']) ? $arr_data['start_datetime'] : '';
            $build_html .=  '</div>';
            $build_html .=  '<div class="clearfix"></div>';
            $build_html .=  '</div>';
            
            $build_html .=  '<div class="review-detais">';
            $build_html .=  '<div class="boldtxts">Ride End Date : </div>';
            $build_html .=  '<div class="rightview-txt">';
            $build_html .=  isset($arr_data['end_datetime']) ? $arr_data['end_datetime'] : '';
            $build_html .= '</div>';
            $build_html .=  '<div class="clearfix"></div>';
            $build_html .=  '</div>';

            $build_html .=  '<div class="review-detais">';
            $build_html .=  '<div class="boldtxts">User Name : </div>';
            $build_html .=  '<div class="rightview-txt">';
            $build_html .=  isset($arr_data['user_name']) ? $arr_data['user_name'] : '';
            $build_html .= '</div>';
            $build_html .=  '<div class="clearfix"></div>';
            $build_html .=  '</div>';


            $build_html .=  '<div class="review-detais">';
            $build_html .=  '<div class="boldtxts">User Contact No / Email : </div>';
            $build_html .=  '<div class="rightview-txt">';
            $build_html .=  isset($arr_data['user_country_code']) ? $arr_data['user_country_code'] : '';
            $build_html .=  isset($arr_data['user_contact_no']) ? $arr_data['user_contact_no'] : '';
            
            if(isset($arr_data['user_email']) && $arr_data['user_email']!='')
            {
                $build_html .=  ' / ';
                $build_html .=  isset($arr_data['user_email']) ? $arr_data['user_email'] : '';
            }
            
            $build_html .= '</div>';
            $build_html .=  '<div class="clearfix"></div>';
            $build_html .=  '</div>';

            $driver_name  = isset($arr_data['driver_name']) ? $arr_data['driver_name'] : '';
            $company_name = isset($arr_data['company_name']) ? $arr_data['company_name'] : '';
            $driver_name  = $driver_name.' ('.$company_name.')';

            $build_html .=  '<div class="review-detais">';
            $build_html .=  '<div class="boldtxts">Driver Name : </div>';
            $build_html .=  '<div class="rightview-txt">';
            $build_html .=  $driver_name;
            $build_html .= '</div>';
            $build_html .=  '<div class="clearfix"></div>';
            $build_html .=  '</div>';

            $build_html .=  '<div class="review-detais">';
            $build_html .=  '<div class="boldtxts">Driver Contact No / Email : </div>';
            $build_html .=  '<div class="rightview-txt">';
            $build_html .=  isset($arr_data['driver_country_code']) ? $arr_data['driver_country_code'] : '';
            $build_html .=  isset($arr_data['driver_contact_no']) ? $arr_data['driver_contact_no'] : '';
            
            if(isset($arr_data['driver_email']) && $arr_data['driver_email']!='')
            {
                $build_html .=  ' / ';
                $build_html .=  isset($arr_data['driver_email']) ? $arr_data['driver_email'] : '';
            }
            
            $build_html .= '</div>';
            $build_html .=  '<div class="clearfix"></div>';
            $build_html .=  '</div>';

            $build_html .=  '<div class="review-detais">';
            $build_html .=  '<div class="boldtxts">Pick Up Location : </div>';
            $build_html .=  '<div class="rightview-txt">';
            $build_html .=  isset($arr_data['pickup_location']) ? $arr_data['pickup_location'] : '';
            $build_html .= '</div>';
            $build_html .=  '<div class="clearfix"></div>';
            $build_html .=  '</div>';

            $build_html .=  '<div class="review-detais">';
            $build_html .=  '<div class="boldtxts">Drop Up Location : </div>';
            $build_html .=  '<div class="rightview-txt">';
            $build_html .=  isset($arr_data['drop_location']) ? $arr_data['drop_location'] : '';
            $build_html .= '</div>';
            $build_html .=  '<div class="clearfix"></div>';
            $build_html .=  '</div>';

            if(isset($arr_data['booking_status']) && $arr_data['booking_status'] == 'CANCEL_BY_USER')
            {
                $build_html .=  '<div class="review-detais">';
                $build_html .=  '<div class="boldtxts">Cancellation Base Price : </div>';
                $build_html .=  '<div class="rightview-txt"> <i class="fa fa-usd"></i> ';
                $build_html .=  isset($arr_data['cancellation_base_price']) ? number_format($arr_data['cancellation_base_price'],2) : '0.0';
                $build_html .= '</div>';
                $build_html .=  '<div class="clearfix"></div>';
                $build_html .=  '</div>';
            }
            
            if(isset($arr_data['booking_status']) && $arr_data['booking_status'] == 'COMPLETED')
            {

                $build_html .=  '<div class="review-detais">';
                $build_html .=  '<div class="boldtxts">Total Traveled Distance : </div>';
                $build_html .=  '<div class="rightview-txt">';
                $build_html .=  isset($arr_data['distance']) ? number_format($arr_data['distance'],2) : '0.0';
                $build_html .= ' <strong>Miles</strong></div>';
                $build_html .=  '<div class="clearfix"></div>';
                $build_html .=  '</div>';

                $build_html .=  '<div class="review-detais">';
                $build_html .=  '<div class="boldtxts">Vehicle Owner : </div>';
                $build_html .=  '<div class="rightview-txt">';
                $build_html .=  isset($arr_data['vehicle_owner']) ? $arr_data['vehicle_owner'] : '';
                $build_html .= '</div>';
                $build_html .=  '<div class="clearfix"></div>';
                $build_html .=  '</div>';

                if(isset($arr_data['is_bonus_used']) && $arr_data['is_bonus_used'] == 'YES')
                {
                    $build_html .=  '<div class="review-detais">';
                    $build_html .=  '<div class="boldtxts">Per USD Bonus point Charge : </div>';
                    $build_html .=  '<div class="rightview-txt"></i> ';
                    $build_html .=  isset($arr_data['admin_referral_points_price_per_usd']) ? intval($arr_data['admin_referral_points_price_per_usd']) : '0';
                    $build_html .= ' <strong>Points</strong> </div>';
                    $build_html .=  '<div class="clearfix"></div>';
                    $build_html .=  '</div>';

                    $build_html .=  '<div class="review-detais">';
                    $build_html .=  '<div class="boldtxts">Bonus Points Used : </div>';
                    $build_html .=  '<div class="rightview-txt"></i> ';
                    $build_html .=  isset($arr_data['user_bonus_points']) ? intval($arr_data['user_bonus_points']) : '0';
                    $build_html .= ' <strong>Points</strong> </div>';
                    $build_html .=  '<div class="clearfix"></div>';
                    $build_html .=  '</div>';
                }

                if(isset($arr_data['is_individual_vehicle']) && $arr_data['is_individual_vehicle'] == '1')
                {
                    $build_html .=  '<div class="review-detais">';
                    $build_html .=  '<div class="boldtxts">Commssion % from Individual Driver : </div>';
                    $build_html .=  '<div class="rightview-txt"> ';
                    $build_html .=  isset($arr_data['individual_driver_percentage']) ? number_format($arr_data['individual_driver_percentage'],2) : '0.0';
                    $build_html .= ' <strong>%</strong></div>';
                    $build_html .=  '<div class="clearfix"></div>';
                    $build_html .=  '</div>';
                }
                elseif(isset($arr_data['is_individual_vehicle']) && $arr_data['is_individual_vehicle'] == '0')
                {
                    if(isset($arr_data['is_company_driver']) && $arr_data['is_company_driver'] == '1')
                    {
                        $build_html .=  '<div class="review-detais">';
                        $build_html .=  '<div class="boldtxts">Commssion % from company : </div>';
                        $build_html .=  '<div class="rightview-txt">  ';
                        $build_html .=  isset($arr_data['admin_company_percentage']) ? number_format($arr_data['admin_company_percentage'],2) : '0.0';
                        $build_html .= ' <strong>%</strong></div>';
                        $build_html .=  '<div class="clearfix"></div>';
                        $build_html .=  '</div>';

                        $build_html .=  '<div class="review-detais">';
                        $build_html .=  '<div class="boldtxts">Company Commssion % to driver : </div>';
                        $build_html .=  '<div class="rightview-txt"> ';
                        $build_html .=  isset($arr_data['company_driver_percentage']) ? number_format($arr_data['company_driver_percentage'],2) : '0.0';
                        $build_html .= ' <strong>%</strong></div>';
                        $build_html .=  '<div class="clearfix"></div>';
                        $build_html .=  '</div>';

                               
                    }
                    elseif(isset($arr_data['is_company_driver']) && $arr_data['is_company_driver'] == '0')
                    {
                        $build_html .=  '<div class="review-detais">';
                        $build_html .=  '<div class="boldtxts">Commssion % to Admin Driver : </div>';
                        $build_html .=  '<div class="rightview-txt"> ';
                        $build_html .=  isset($arr_data['admin_driver_percentage']) ? number_format($arr_data['admin_driver_percentage'],2) : '0.0';
                        $build_html .= ' <strong>%</strong></div>';
                        $build_html .=  '<div class="clearfix"></div>';
                        $build_html .=  '</div>';
                    }
                }

                $build_html .=  '<div class="review-detais">';
                $build_html .=  '<div class="boldtxts">Total Amount : </div>';
                $build_html .=  '<div class="rightview-txt"> <i class="fa fa-usd"></i> ';
                $build_html .=  isset($arr_data['total_amount']) ? number_format($arr_data['total_amount'],2) : '0.0';
                $build_html .= '</div>';
                $build_html .=  '<div class="clearfix"></div>';
                $build_html .=  '</div>';
      
                $build_html .=  '<div class="review-detais">';
                $build_html .=  '<div class="boldtxts">Discount Amount : </div>';
                $build_html .=  '<div class="rightview-txt"> <i class="fa fa-usd"></i> ';
                $build_html .=  isset($arr_data['applied_promo_code_charge']) ? number_format($arr_data['applied_promo_code_charge'],2) : '0.0';
                $build_html .= '</div>';
                $build_html .=  '<div class="clearfix"></div>';
                $build_html .=  '</div>';

                if(isset($arr_data['is_bonus_used']) && $arr_data['is_bonus_used'] == 'YES')
                {
                    $build_html .=  '<div class="review-detais">';
                    $build_html .=  '<div class="boldtxts">Bonus Points Amount Applied : </div>';
                    $build_html .=  '<div class="rightview-txt"></i> <i class="fa fa-usd"></i> ';
                    $build_html .=  isset($arr_data['user_bonus_points_usd_amount']) ? number_format($arr_data['user_bonus_points_usd_amount'],2) : '0.0';
                    $build_html .= '</div>';
                    $build_html .=  '<div class="clearfix"></div>';
                    $build_html .=  '</div>';
                }
        

                $build_html .=  '<div class="review-detais">';
                $build_html .=  '<div class="boldtxts">User Paid Amount : </div>';
                $build_html .=  '<div class="rightview-txt"> <i class="fa fa-usd"></i> ';
                $build_html .=  isset($arr_data['total_charge']) ? number_format($arr_data['total_charge'],2) : '0.0';
                $build_html .= '</div>';
                $build_html .=  '<div class="clearfix"></div>';
                $build_html .=  '</div>';
        
                if(isset($arr_data['is_individual_vehicle']) && $arr_data['is_individual_vehicle'] == '1')
                {
                    $build_html .=  '<div class="review-detais">';
                    $build_html .=  '<div class="boldtxts">Admin Commission Amount : </div>';
                    $build_html .=  '<div class="rightview-txt"> <i class="fa fa-usd"></i> ';
                    $build_html .=  isset($arr_data['admin_earning_amount']) ? number_format($arr_data['admin_earning_amount'],2) : '0.0';
                    $build_html .= '</div>';
                    $build_html .=  '<div class="clearfix"></div>';
                    $build_html .=  '</div>';

                    $build_html .=  '<div class="review-detais">';
                    $build_html .=  '<div class="boldtxts">Driver Earning Amount : </div>';
                    $build_html .=  '<div class="rightview-txt"> <i class="fa fa-usd"></i> ';
                    $build_html .=  isset($arr_data['driver_earning_amount']) ? number_format($arr_data['driver_earning_amount'],2) : '0.0';
                    $build_html .= '</div>';
                    $build_html .=  '<div class="clearfix"></div>';
                    $build_html .=  '</div>';

                }
                elseif(isset($arr_data['is_individual_vehicle']) && $arr_data['is_individual_vehicle'] == '0')
                {
                    if(isset($arr_data['is_company_driver']) && $arr_data['is_company_driver'] == '1')
                    {
                        $build_html .=  '<div class="review-detais">';
                        $build_html .=  '<div class="boldtxts">Admin Commission Amount : </div>';
                        $build_html .=  '<div class="rightview-txt"> <i class="fa fa-usd"></i> ';
                        $build_html .=  isset($arr_data['admin_earning_amount']) ? number_format($arr_data['admin_earning_amount'],2) : '0.0';
                        $build_html .= '</div>';
                        $build_html .=  '<div class="clearfix"></div>';
                        $build_html .=  '</div>';

                        $build_html .=  '<div class="review-detais">';
                        $build_html .=  '<div class="boldtxts">Company Earning Amount : </div>';
                        $build_html .=  '<div class="rightview-txt"> <i class="fa fa-usd"></i> ';
                        $build_html .=  isset($arr_data['company_earning_amount']) ? number_format($arr_data['company_earning_amount'],2) : '0.0';
                        $build_html .= '</div>';
                        $build_html .=  '<div class="clearfix"></div>';
                        $build_html .=  '</div>';

                        $build_html .=  '<div class="review-detais">';
                        $build_html .=  '<div class="boldtxts">Company Driver Earning Amount : </div>';
                        $build_html .=  '<div class="rightview-txt"> <i class="fa fa-usd"></i> ';
                        $build_html .=  isset($arr_data['driver_earning_amount']) ? number_format($arr_data['driver_earning_amount'],2) : '0.0';
                        $build_html .= '</div>';
                        $build_html .=  '<div class="clearfix"></div>';
                        $build_html .=  '</div>';

                        $build_html .=  '<div class="review-detais">';
                        $build_html .=  '<div class="boldtxts">Total Company Earning Amount : </div>';
                        $build_html .=  '<div class="rightview-txt"> <i class="fa fa-usd"></i> ';
                        $build_html .=  isset($arr_data['total_company_earning_amount']) ? number_format($arr_data['total_company_earning_amount'],2) : '0.0';
                        $build_html .= '</div>';
                        $build_html .=  '<div class="clearfix"></div>';
                        $build_html .=  '</div>';
                    }
                    elseif(isset($arr_data['is_company_driver']) && $arr_data['is_company_driver'] == '0')
                    {
                        $build_html .=  '<div class="review-detais">';
                        $build_html .=  '<div class="boldtxts">Admin Earning Amount : </div>';
                        $build_html .=  '<div class="rightview-txt"> <i class="fa fa-usd"></i> ';
                        $build_html .=  isset($arr_data['admin_earning_amount']) ? number_format($arr_data['admin_earning_amount'],2) : '0.0';
                        $build_html .= '</div>';
                        $build_html .=  '<div class="clearfix"></div>';
                        $build_html .=  '</div>';

                        $build_html .=  '<div class="review-detais">';
                        $build_html .=  '<div class="boldtxts">Driver Commission Amount : </div>';
                        $build_html .=  '<div class="rightview-txt"> <i class="fa fa-usd"></i> ';
                        $build_html .=  isset($arr_data['driver_earning_amount']) ? number_format($arr_data['driver_earning_amount'],2) : '0.0';
                        $build_html .= '</div>';
                        $build_html .=  '<div class="clearfix"></div>';
                        $build_html .=  '</div>';
                    }
                }             
            }

            $payment_status = '';
            if(isset($arr_data['payment_status']) && $arr_data['payment_status'] == 'PENDING')
            {
                $payment_status = '<span class="badge badge-warning" style="width:100px">Pending</span>';
            }
            else if(isset($arr_data['payment_status']) && $arr_data['payment_status'] == 'SUCCESS')
            {
                $payment_status = '<span class="badge badge-success" style="width:100px">Success</span>';
            }
            else if(isset($arr_data['payment_status']) && $arr_data['payment_status'] == 'FAILED')
            {
                $payment_status = '<span class="badge badge-important" style="width:100px">Failed</span>';
            }

            $build_html .=  '<div class="review-detais">';
            $build_html .=  '<div class="boldtxts">Payment Status : </div>';
            $build_html .=  '<div class="rightview-txt">';
            $build_html .=  $payment_status;
            $build_html .= '</div>';
            $build_html .=  '<div class="clearfix"></div>';
            $build_html .=  '</div>';


            $booking_status = '';
            if(isset($arr_data['booking_status']) && $arr_data['booking_status'] == 'TO_BE_PICKED')
            {
                $booking_status = '<span class="badge badge-info" style="width:100px">To be picked</span>';
            }
            else if(isset($arr_data['booking_status']) && $arr_data['booking_status'] == 'IN_TRANSIT')
            {
                $booking_status = '<span class="badge badge-warning" style="width:100px">In transit</span>';
            }
            else if(isset($arr_data['booking_status']) && $arr_data['booking_status'] == 'COMPLETED')
            {
                $booking_status = '<span class="badge badge-success" style="width:100px">Completed</span>';
            }
            else if(isset($arr_data['booking_status']) && $arr_data['booking_status'] == 'CANCEL_BY_USER')
            {
                $booking_status = '<span class="badge badge-important" style="width:100px">Cancel by user</span>';
            }
            else if(isset($arr_data['booking_status']) && $arr_data['booking_status'] == 'CANCEL_BY_DRIVER')
            {
                $booking_status = '<span class="badge badge-important" style="width:100px">Cancel by driver</span>';
            }
            

            $build_html .=  '<div class="review-detais">';
            $build_html .=  '<div class="boldtxts">Booking status : </div>';
            $build_html .=  '<div class="rightview-txt">';
            $build_html .=  $booking_status;
            $build_html .= '</div>';
            $build_html .=  '<div class="clearfix"></div>';
            $build_html .=  '</div>';

            $payment_type = '<span class="badge badge-warning" style="width:100px">NA</span>';

            if(isset($arr_data['payment_type']) && $arr_data['payment_type'] == 'STRIPE')
            {
                $payment_type = '<span class="badge badge-success" style="width:100px">Stripe</span>';
            }
            else if(isset($arr_data['payment_type']) && $arr_data['payment_type'] == 'PAYPAL')
            {
                $payment_type = '<span class="badge badge-info" style="width:100px">Paypal</span>';
            }
            
            $build_html .=  '<div class="review-detais">';
            $build_html .=  '<div class="boldtxts">Payment Type : </div>';
            $build_html .=  '<div class="rightview-txt">';
            $build_html .=  $payment_type;
            $build_html .= '</div>';
            $build_html .=  '<div class="clearfix"></div>';
            $build_html .=  '</div>';
        }

      	$arr_response = [];

      	$arr_response['status']         = 'error';
      	$arr_response['generated_html'] = '';
      	
      	if($build_html!='')
      	{
      		$arr_response['status']         = 'success';
	      	$arr_response['generated_html'] = $build_html;
      	}

      	return response()->json($arr_response);	
    }

    private function genrate_transaction_unique_number()
    {
        $secure = TRUE;    
        $bytes = openssl_random_pseudo_bytes(6, $secure);
        $order_ref_num = "TRAN-".bin2hex($bytes);

        return strtoupper($order_ref_num);   
    }

	private function get_driver_balance_information($driver_id,$is_company_driver)
	{
		$driver_total_amount   = 0;
		$driver_paid_amount    = 0;
		$driver_unpaid_amount  = 0;

		$arr_result = [];
		
		$arr_result['driver_total_amount']  = $driver_total_amount;
		$arr_result['driver_paid_amount']   = $driver_paid_amount;
		$arr_result['driver_unpaid_amount'] = $driver_unpaid_amount;

		$obj_driver_account_balance = $this->BookingMasterModel
		                                        ->whereHas('load_post_request_details',function($query) use($driver_id){
		                                                    $query->where('driver_id',$driver_id);
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

                $driver_earning_amount = 0;

                if($booking_status == 'CANCEL_BY_USER')
                {
                    $driver_earning_amount = 0;
                }
                else
                {
                    if($is_individual_vehicle == '1')
                    {
                        $driver_earning_amount = $individual_driver_amount;
                    }
                    else if($is_individual_vehicle == '0')
                    {    
                        if($is_company_driver == '1')
                        {
                            $driver_earning_amount = $company_driver_amount;    
                        }
                        else if($is_company_driver == '0')
                        {
                            $driver_earning_amount = $admin_driver_amount;
                        }
                    }
                }
				$driver_total_amount   = (floatval($driver_total_amount) + floatval($driver_earning_amount));
			}
		}
		
		$to_user_type = '';
		if(isset($is_company_driver) && $is_company_driver == '1'){
			$to_user_type = 'COMPANY_DRIVER';
		}
		else if(isset($is_company_driver) && $is_company_driver == '0'){
			$to_user_type = 'DRIVER';
		}
		$obj_driver_paid_amount = $this->DepositMoneyModel
												->select('id','to_user_id','amount_paid','status')
												->where([
															'to_user_id'   => $driver_id,
															'to_user_type' => $to_user_type,
															'status'       => 'SUCCESS'
														])
												->get();
		$arr_driver_paid_amount =[];
		if($obj_driver_paid_amount)
		{
			$arr_driver_paid_amount = $obj_driver_paid_amount->toArray();
		}
		if(isset($arr_driver_paid_amount) && sizeof($arr_driver_paid_amount)>0)
		{
			foreach ($arr_driver_paid_amount as $key => $value) 
			{
				$amount_paid = isset($value['amount_paid']) ? $value['amount_paid'] :0;

				$driver_paid_amount = (floatval($driver_paid_amount) + floatval($amount_paid));
			}
		}	
		if($driver_total_amount>$driver_paid_amount)
		{
			$driver_unpaid_amount = (floatval($driver_total_amount) - floatval($driver_paid_amount));
			$driver_unpaid_amount = $driver_unpaid_amount;
		}
		$arr_result['driver_total_amount']  = $driver_total_amount;
		$arr_result['driver_paid_amount']   = $driver_paid_amount;
		$arr_result['driver_unpaid_amount'] = $driver_unpaid_amount;

		return $arr_result;
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

    public function perform_deactivate($id)
    {
        $static_page = $this->BaseModel->where('id',$id)->first();
        
        if($static_page)
        {
             $arr_notification_data = 
                                        [
                                            'title'             => 'Your account is blocked by admin',
                                            'notification_type' => 'DRIVER_BLOCK',
                                            'enc_user_id'       => $id,
                                            'user_type'         => 'DRIVER',

                                        ];
             $this->NotificationsService->send_on_signal_notification($arr_notification_data);

             return $static_page->update(['is_active'=>0]);
            }

        return FALSE;
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
        $delete = $this->BaseModel->with('driver_car_details')->where('id',$id)->first();

        if($delete)
        {
        	if(isset($delete->driver_car_details->is_individual_vehicle) && $delete->driver_car_details->is_individual_vehicle == '1')
        	{
        		$vehicle_id = isset($delete->driver_car_details->vehicle_id) ? $delete->driver_car_details->vehicle_id :0;
        		$obj_vehicle = $this->VehicleModel->where('id',$vehicle_id)->delete();

        	}
        	else if(isset($delete->driver_car_details->is_individual_vehicle) && $delete->driver_car_details->is_individual_vehicle == '0')
        	{
        		$obj_driver_car_relation = $this->DriverCarRelationModel->where('driver_id',$id)->first();
        		$obj_driver_car_relation->update(['vehicle_id'=>0,'is_car_assign'=>'0']);
        	}
        	return $this->BaseModel->where('id',$id)->delete();
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
				$arr_mail_data['email_template_id'] = '8';
				$arr_mail_data['arr_built_content'] = $arr_built_content;
				$arr_mail_data['user']              = $arr_data;

				return $arr_mail_data;
			}
			return FALSE;
	}

	private function built_notification_data_info($arr_data,$type)
	{
		$arr_notification = [];
		if(isset($arr_data) && sizeof($arr_data)>0)
		{
			if(isset($type) && $type == 'REGISTER')
			{
				$first_name = isset($arr_data['first_name']) ? $arr_data['first_name'] :'';
				$last_name  = isset($arr_data['last_name']) ? $arr_data['last_name'] :'';
				$full_name  = $first_name.' '.$last_name;
				$full_name  = ($full_name!=' ') ? $full_name : '-';

				$arr_notification['user_id']           = $this->CommonDataService->get_admin_id();
				$arr_notification['is_read']           = 0;
				$arr_notification['is_show']           = 0;
				$arr_notification['user_type']         = 'ADMIN';
				$arr_notification['notification_type'] = 'Driver Registration';

				$arr_notification['title']             = $full_name.' register as a Driver on Quickpick.';
			    $arr_notification['view_url']          = '/'.config('app.project.admin_panel_slug')."/driver"; //$this->module_url_path;

			}
			else if(isset($type) && $type == 'ADMIN_PAYMENT')
			{
				$transaction_id = isset($arr_data['transaction_id']) ? $arr_data['transaction_id'] :'';
				$id = isset($arr_data['id']) ? $arr_data['id'] :'';
				$first_name = isset($arr_data['first_name']) ? $arr_data['first_name'] :'';
				$last_name  = isset($arr_data['last_name']) ? $arr_data['last_name'] :'';
				$full_name  = $first_name.' '.$last_name;
				$full_name  = ($full_name!=' ') ? $full_name : '-';

				$arr_notification['user_id']           = $this->CommonDataService->get_admin_id();
				$arr_notification['is_read']           = 0;
				$arr_notification['is_show']           = 0;
				$arr_notification['user_type']         = 'ADMIN';
				$arr_notification['notification_type'] = 'Driver Payment Receipt';
				$arr_notification['title']             = 'Payment receipt request send to '.$full_name.' with transaction id #'.$transaction_id;
				
				$arr_notification['view_url']          = '/'.config('app.project.admin_panel_slug').'/driver/deposit_receipt/'.base64_encode($id); 
			}
			else if(isset($type) && $type == 'DRIVER_PAYMENT')
			{
				
				$transaction_id = isset($arr_data['transaction_id']) ? $arr_data['transaction_id'] :'';
				$id = isset($arr_data['id']) ? $arr_data['id'] :'';

				$first_name = isset($arr_data['first_name']) ? $arr_data['first_name'] :'';
				$last_name  = isset($arr_data['last_name']) ? $arr_data['last_name'] :'';
				$full_name  = $first_name.' '.$last_name;
				$full_name  = ($full_name!=' ') ? $full_name : '-';
				$amount     = isset($arr_data['amount_paid']) ? $arr_data['amount_paid']: '-';

				$arr_notification['user_id']           = $id;
				$arr_notification['is_read']           = 0;
				$arr_notification['is_show']           = 0;
				$arr_notification['user_type']         = 'DRIVER';
				$arr_notification['notification_type'] = 'Driver Payment Receipt';
				$arr_notification['title']             = 'Payment Received successfully of $ '.$amount;
				$arr_notification['view_url']          = '/'.config('app.project.admin_panel_slug')."/driver";
			}
		}
		return $arr_notification;
	}

	public function built_mail_data_payment($arr_data_details)
	{
		if(isset($arr_data_details) && sizeof($arr_data_details)>0)
		{
			$currency 	= config('app.project.currency');

			$attachment = $this->receipt_image_public_path.$arr_data_details['receipt_image'];

			$amount_paid = $currency.$arr_data_details['amount_paid'];

			$arr_built_content = [
				'FIRST_NAME'       => $arr_data_details['first_name'],
				'LAST_NAME'        => $arr_data_details['last_name'],
				'TRANSACTION_ID'   => $arr_data_details['transaction_id'],
				'AMOUNT_PAID'      => $amount_paid,
				'EMAIL'            => $arr_data_details['email'],
				'PROJECT_NAME'     => config('app.project.name')];

				$arr_mail_data                      = [];
				$arr_mail_data['email_template_id'] = '15';
				$arr_mail_data['arr_built_content'] = $arr_built_content;
				$arr_mail_data['user']              = $arr_data_details;
				$arr_mail_data['attachment']        = $attachment;
				$arr_mail_data['currency']        	= $currency;

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
                                  'ACCOUNT_TYPE' => 'Driver',
                                  'PROJECT_NAME' => config('app.project.name')];

            $arr_mail_data                      = [];
            $arr_mail_data['email_template_id'] = '18';
            $arr_mail_data['arr_built_content'] = $arr_built_content;
            $arr_mail_data['user']              = $arr_data;

            return $arr_mail_data;
        }
        return FALSE;
    }
	public function check_driver_current_trip($driver_id)
    {
        $arr_booking_master = [];
        $obj_booking_master = $this->BookingMasterModel
                                                        ->select('id','booking_status')
                                                        ->whereHas('load_post_request_details',function($query) use($driver_id){
                                                            $query->where('driver_id',$driver_id);
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