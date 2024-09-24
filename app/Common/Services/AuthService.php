<?php

namespace App\Common\Services;

use App\Models\UserModel;

use App\Models\NotificationsModel;

use App\Common\Services\CommonDataService;
use App\Common\Services\JwtUserService;
use App\Common\Services\EmailService;
use App\Common\Services\NotificationsService;
use App\Common\Services\ReviewService;
use App\Models\DriverCarRelationModel;
use App\Models\DriverStatusModel;
use App\Models\DriverFairChargeModel;

use App\Models\UserReferralHistoryModel;
use App\Models\UserPointsHistoryModel;
use App\Models\VehicleModel;
use App\Models\CardDetailsModel;
use App\Models\BookingMasterModel;



use Validator;
use Sentinel;

use Hash;
use Cartalyst\Sentinel\Hashing\NativeHasher;

class AuthService
{
	public function __construct(
									UserModel $user,
									NotificationsModel $notification,
									NativeHasher $NativeHasher,
									EmailService $email_service,
									CommonDataService $common_data_service,
									JwtUserService $jwt_user_service,
									NotificationsService $notifications_service,
									ReviewService $review_service,
									DriverCarRelationModel $driver_car_relation,
									DriverStatusModel $driver_status,
									DriverFairChargeModel $driver_fair_charge,
									UserReferralHistoryModel $user_referral_history,
									UserPointsHistoryModel $user_points_history,
									VehicleModel $vehicle,
									CardDetailsModel $card_details,
									BookingMasterModel $booking_master
								)
	{
		$this->UserModel                = $user;
		$this->NotificationsModel       = $notification;
		$this->EmailService             = $email_service;
		$this->CommonDataService        = $common_data_service;
		$this->JwtUserService  			= $jwt_user_service;
		$this->NotificationsService     = $notifications_service;
		$this->ReviewService            = $review_service;
		$this->NativeHasher             = $NativeHasher;
		$this->DriverCarRelationModel   = $driver_car_relation;
		$this->DriverStatusModel        = $driver_status;
		$this->DriverFairChargeModel    = $driver_fair_charge;
		$this->UserReferralHistoryModel = $user_referral_history;
		$this->UserPointsHistoryModel   = $user_points_history;
		$this->VehicleModel             = $vehicle;
		$this->CardDetailsModel 		= $card_details;
		$this->BookingMasterModel 		= $booking_master;

		$this->user_profile_public_img_path = url('/').config('app.project.img_path.user_profile_images');
		$this->user_profile_base_img_path  = base_path().config('app.project.img_path.user_profile_images');

		$this->driving_license_public_path = url('/').config('app.project.img_path.driving_license');
		$this->driving_license_base_path   = base_path().config('app.project.img_path.driving_license');

		$this->vehicle_doc_public_path = url('/').config('app.project.img_path.vehicle_doc');
		$this->vehicle_doc_base_path   = base_path().config('app.project.img_path.vehicle_doc');

		$this->review_tag_public_path = url('/').config('app.project.img_path.review_tag');

		/*$this->twilio_sid 	    = "AC61ac3fc858aa01a7e5f2db7bc71e2c21"; // Your Account SID from www.twilio.com/console
		$this->twilio_token     = "259c02f7e11d602186d09e2cf0b2b1c7"; // Your Auth Token from www.twilio.com/console
		$this->from_user_mobile = '+17032152456';*/

		$this->twilio_sid       = config('app.project.twilio_credentials.twilio_sid');
		$this->twilio_token     = config('app.project.twilio_credentials.twilio_token');
		$this->from_user_mobile = config('app.project.twilio_credentials.from_user_mobile');

	}

	public function store($request)
	{	
		$arr_data = $arr_rules = $arr_response = array();

		try
		{
			$arr_rules['first_name']       = "required";
			$arr_rules['last_name']        = "required";
			$arr_rules['email']           = "required";
			$arr_rules['address']          = "required";
			$arr_rules['latitude']         = "required";
			$arr_rules['longitude']        = "required";
			$arr_rules['mobile_no']        = "required";
			$arr_rules['password']         = "required";
			$arr_rules['user_type']        = "required";

			
			$validator = Validator::make($request->all(),$arr_rules);
			if($validator->fails())
			{
				$arr_response['status'] = 'error';
				$arr_response['msg']    = 'Please fill all the required field';
				$arr_response['data']   = [];
				return $arr_response;
			}

			$first_name        = $request->input('first_name');
			$last_name         = $request->input('last_name');
			$gender            = $request->input('gender');	
			$dob               = date('Y-m-d',strtotime($request->input('dob')));	
			$address           = $request->input('address');
			$city_name         = $request->input('city_name');
			$state_name        = $request->input('state_name');
			$post_code         = $request->input('post_code');
			$country_name      = $request->input('country_name');
			$latitude          = $request->input('latitude');
			$longitude         = $request->input('longitude');
			$country_code      = trim($request->input('country_code'));
			$mobile_no         = trim($request->input('mobile_no'));
			$email             = trim($request->input('email'));	
			$password          = $request->input('password');
			$user_type         = $request->input('user_type');   // user/driver
			$referral_code     = $request->input('referral_code');
			$is_driver_vehicle = $request->input('is_driver_vehicle');


			if($user_type != "USER" && $user_type != "DRIVER")
			{
				$arr_response['status'] = 'error';
				$arr_response['msg']    = 'Invalid Customer';
				$arr_response['data']   = [];
				return $arr_response;
			}
			
			$arr_user_role = ['ADMIN','COMPANY',$user_type];

			$is_email_duplicate = $this->UserModel
											->where('email',$email)
											->whereIn('user_type',$arr_user_role)
											->first();

			if($is_email_duplicate)
			{
				$arr_response['status'] = 'existing_account';
				$arr_response['msg']    = 'This email address already exists';
				$arr_response['data']   = [];
				return $arr_response;
			}

			$is_mobile_no_duplicate = $this->UserModel
											->where('mobile_no',$mobile_no)
											->whereIn('user_type',$arr_user_role)
											->first();
			/*check for existing user otp verified or not if not then sent otp and procced further*/

			if($is_mobile_no_duplicate)
			{
				if(isset($is_mobile_no_duplicate->is_otp_verified) && $is_mobile_no_duplicate->is_otp_verified == 1)
				{
					$arr_response['status'] = 'existing_account';
					$arr_response['msg']    = 'This mobile number already exists';
					$arr_response['data']   = [];
					return $arr_response;
				}
				elseif(isset($is_mobile_no_duplicate->is_otp_verified) && $is_mobile_no_duplicate->is_otp_verified == 0)
				{
					$generated_otp = $this->generate_otp();
					$is_mobile_no_duplicate->first_name		  = $first_name;
					$is_mobile_no_duplicate->last_name		  = $last_name;
					$is_mobile_no_duplicate->dob			  = $dob;
					$is_mobile_no_duplicate->email			  = $email;
					$is_mobile_no_duplicate->address		  = $address;
					$is_mobile_no_duplicate->city_name		  = $city_name;
					$is_mobile_no_duplicate->state_name		  = $state_name;
					$is_mobile_no_duplicate->post_code		  = $post_code;
					$is_mobile_no_duplicate->country_name	  = $country_name;
					$is_mobile_no_duplicate->latitude		  = $latitude;
					$is_mobile_no_duplicate->longitude		  = $longitude;
					// $is_mobile_no_duplicate->password		  = $password;
					$is_mobile_no_duplicate->country_code	  = $country_code;
					$is_mobile_no_duplicate->mobile_no		  = $mobile_no;
					$is_mobile_no_duplicate->otp    		  = $generated_otp;
					$is_mobile_no_duplicate->is_active		  = 1;
					$is_mobile_no_duplicate->user_type		  = $user_type;
					
					$is_mobile_no_duplicate->save();

					Sentinel::update($is_mobile_no_duplicate,array('password' => $password));
					
					$arr_tmp = [];
					$arr_tmp['mobile_no'] = $mobile_no;
					$arr_tmp['otp']       = $generated_otp;

					$to_user_mobile = $country_code.''.$mobile_no;
					$message = 'Your OTP is '.$generated_otp.' Thanks.'.config('app.project.name');
					
					$arr_twilio_data = [
											'mobile_no' => $to_user_mobile,
											'message' 	=> $message
									   ];

					$this->get_sms_to_user($arr_twilio_data);
					
					// Thank you for signing up with QuickPick.
					// For verification purposes and to complete the registration process, a one-time code is being sent to your registered mobile phone. Please enter this code.

					$arr_response['status'] = 'success';
					$arr_response['msg']    = 'Thank you for signing up with '.config('app.project.name').'. For verification purposes and to complete the registration process, a one-time code is being sent to your registered mobile phone. Please enter this code.';
					$arr_response['data']   = $arr_tmp;
					return $arr_response;

				}
				else
				{
					$arr_response['status'] = 'existing_account';
					$arr_response['msg']    = 'This mobile number already exists';
					$arr_response['data']   = [];
					return $arr_response;
				}				
			}

			$referral_id = $this->validate_referral_code($referral_code);

			$arr_data['first_name']		= $first_name;
			$arr_data['last_name']		= $last_name;
			// $arr_data['gender']			= $gender;
			$arr_data['dob']			= $dob;

			$arr_data['address']		= $address;
			$arr_data['city_name']		= $city_name;
			$arr_data['state_name']		= $state_name;
			$arr_data['post_code']		= $post_code;
			$arr_data['country_name']	= $country_name;
			$arr_data['latitude']		= $latitude;
			$arr_data['longitude']		= $longitude;

			$arr_data['email']		    = $email;
			$arr_data['password']		= $password;
			$arr_data['mobile_no']		= $mobile_no;
			$arr_data['country_code']	= $country_code;
			$arr_data['is_active']		= 1;
			$arr_data['user_type']		= $user_type;

			/*if($request->hasFile('driving_license'))
			{
				$driving_license = $request->input('driving_license');
				$file_extension = strtolower($request->file('driving_license')->getClientOriginalExtension());
				if(in_array($file_extension,['png','jpg','jpeg','pdf']))
				{
					$driving_license = time().uniqid().'.'.$file_extension;
					$isUpload = $request->file('driving_license')->move($this->driving_license_base_path , $driving_license);
					$arr_data['driving_license'] = $driving_license;
				}
			}*/

			$generated_otp    = $this->generate_otp();
			$arr_data['otp']  = $generated_otp;

			// Generate referral code for individual user
			if($user_type == 'USER')
			{
				$arr_data['referral_code']  = $this->generate_referral_code();
			}

			$obj_user = Sentinel::registerAndActivate($arr_data);
			
			if($obj_user)
			{ 
				$arr_data['user_type']  = $user_type;

				$arr_notification_data = $this->built_notification_data($arr_data);
				$this->NotificationsService->store_notification($arr_notification_data);

				// Send OTP SMS

				if($user_type=="USER")
				{
					$role = Sentinel::findRoleBySlug('user');
					$obj_user->roles()->attach($role);

					$user_id = isset($obj_user->id) ? $obj_user->id : 0;

					if($referral_code != "")
					{
						// Add to his referral's history
						$arr_referral_history = [
													'user_id'       => $user_id,
													'referral_id'   => $referral_id,
													'referral_code' => $referral_code
												];

						$this->UserReferralHistoryModel->create($arr_referral_history);
						
						// Add earned points from referral
						$arr_user_points = [
												'referral_user_id' => $referral_id,
												'points'           => $this->CommonDataService->admin_referral_points(),
												'type'             => 'CREDIT',
												'origin'           => 'REFERRAL',
												'user_id'          => $user_id
											];

						$this->UserPointsHistoryModel->create($arr_user_points);

						$obj_referral = $this->UserModel
													->select('id','my_points')
													->where('id',$referral_id)
													->first();
						if($obj_referral){
							$my_points = isset($obj_referral->my_points) ? $obj_referral->my_points :0;
							$admin_referral_points = $this->CommonDataService->admin_referral_points();
							$total_points = floatval($my_points) + floatval($admin_referral_points);
							$obj_referral->my_points = $total_points;
							$obj_referral->save();
						}	
					}
				}
				else
				{
					$role = Sentinel::findRoleBySlug('driver');
					$obj_user->roles()->attach($role);

					$driver_id  = isset($obj_user->id) ? $obj_user->id :0;
					$vehicle_id = $is_individual_vehicle = '0';

					if(isset($is_driver_vehicle) && $is_driver_vehicle == 'YES'){

						$is_individual_vehicle = '1';

						// $arr_vehicle = [];
						
						// $arr_vehicle['company_id']            = 0;
						// $arr_vehicle['is_individual_vehicle'] = 1;
						// $arr_vehicle['vehicle_type_id']       = $request->input('vehicle_type_id');
						// $arr_vehicle['vehicle_brand_id']      = $request->input('vehicle_brand_id');
						// $arr_vehicle['vehicle_model_id']      = $request->input('vehicle_model_id');
						// $arr_vehicle['vehicle_year_id']       = $request->input('vehicle_year_id');
						// $arr_vehicle['vehicle_number']        = $request->input('vehicle_number');
						// $arr_vehicle['is_active']             = 1;
						// $arr_vehicle['is_verified']           = 0;

						// if($request->hasFile('vehicle_image'))
						// {
						// 	$vehicle_image = $request->input('vehicle_image');
						// 	$file_extension = strtolower($request->file('vehicle_image')->getClientOriginalExtension());
						// 	if(in_array($file_extension,['png','jpg','jpeg','pdf']))
						// 	{
						// 		$vehicle_image = time().uniqid().'.'.$file_extension;
						// 		$isUpload = $request->file('vehicle_image')->move($this->vehicle_doc_base_path , $vehicle_image);
						// 		$arr_vehicle['vehicle_image'] = $vehicle_image;
						// 	}
						// }

						// if($request->hasFile('registration_doc'))
						// {
						// 	$registration_doc = $request->input('registration_doc');
						// 	$file_extension = strtolower($request->file('registration_doc')->getClientOriginalExtension());
						// 	if(in_array($file_extension,['png','jpg','jpeg','pdf']))
						// 	{
						// 		$registration_doc = time().uniqid().'.'.$file_extension;
						// 		$isUpload = $request->file('registration_doc')->move($this->vehicle_doc_base_path , $registration_doc);
						// 		$arr_vehicle['registration_doc'] = $registration_doc;
						// 	}
						// }
						
						// if($request->hasFile('vehicle_insurance_doc'))
						// {
						// 	$vehicle_insurance_doc = $request->input('vehicle_insurance_doc');
						// 	$file_extension = strtolower($request->file('vehicle_insurance_doc')->getClientOriginalExtension());
						// 	if(in_array($file_extension,['png','jpg','jpeg','pdf']))
						// 	{
						// 		$vehicle_insurance_doc = time().uniqid().'.'.$file_extension;
						// 		$isUpload = $request->file('vehicle_insurance_doc')->move($this->vehicle_doc_base_path , $vehicle_insurance_doc);
						// 		$arr_vehicle['insurance_doc'] = $vehicle_insurance_doc;
						// 	}
						// }
						
						// if($request->hasFile('proof_of_inspection_doc'))
						// {
						// 	$proof_of_inspection_doc = $request->input('proof_of_inspection_doc');
						// 	$file_extension = strtolower($request->file('proof_of_inspection_doc')->getClientOriginalExtension());
						// 	if(in_array($file_extension,['png','jpg','jpeg','pdf']))
						// 	{
						// 		$proof_of_inspection_doc = time().uniqid().'.'.$file_extension;
						// 		$isUpload = $request->file('proof_of_inspection_doc')->move($this->vehicle_doc_base_path , $proof_of_inspection_doc);
						// 		$arr_vehicle['proof_of_inspection_doc'] = $proof_of_inspection_doc;
						// 	}
						// }
						
						// if($request->hasFile('dmv_driving_record'))
						// {
						// 	$dmv_driving_record = $request->input('dmv_driving_record');
						// 	$file_extension = strtolower($request->file('dmv_driving_record')->getClientOriginalExtension());
						// 	if(in_array($file_extension,['png','jpg','jpeg','pdf']))
						// 	{
						// 		$dmv_driving_record = time().uniqid().'.'.$file_extension;
						// 		$isUpload = $request->file('dmv_driving_record')->move($this->vehicle_doc_base_path , $dmv_driving_record);
						// 		$arr_vehicle['dmv_driving_record'] = $dmv_driving_record;
						// 	}
						// }

						// if($request->hasFile('usdot_doc'))
						// {
						// 	$usdot_doc = $request->input('usdot_doc');
						// 	$file_extension = strtolower($request->file('usdot_doc')->getClientOriginalExtension());
						// 	if(in_array($file_extension,['png','jpg','jpeg','pdf']))
						// 	{
						// 		$usdot_doc = time().uniqid().'.'.$file_extension;
						// 		$isUpload = $request->file('usdot_doc')->move($this->vehicle_doc_base_path , $usdot_doc);
						// 		$arr_vehicle['usdot_doc'] = $usdot_doc;
						// 	}
						// }
						
						// $vehicle_status = $this->VehicleModel->create($arr_vehicle);
						// if($vehicle_status){
						// 	$vehicle_id = isset($vehicle_status->id)?$vehicle_status->id:0;
						// 	$is_individual_vehicle = 1;
						// }
					}

					/*$arr_fair_charge = 
										[
			                				'driver_id' => $driver_id,
			                				'fair_charge' => 0
			                			];

                	$this->DriverFairChargeModel->create($arr_fair_charge);  */

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
												'driver_id'      => $driver_id,
												'status'         => 'AVAILABLE',
												'current_latitude'=> $request->input('latitude'),
												'current_longitude'  => $request->input('longitude')
											];


					$this->DriverStatusModel->create($arr_driver_status);
				}

				$arr_tmp = [];
				$arr_tmp['mobile_no'] = $mobile_no;
				$arr_tmp['otp']       = $generated_otp;

				$to_user_mobile = $country_code.''.$mobile_no;
				$message = 'Your OTP is '.$generated_otp.' Thanks.'.config('app.project.name');
				
				$arr_twilio_data = [
										'mobile_no' => $to_user_mobile,
										'message' 	=> $message
								   ];

				$this->get_sms_to_user($arr_twilio_data);

				$arr_mail_data = $this->built_custom_template_mail_data($arr_data); 
	            $this->EmailService->send_custom_template_mail($arr_mail_data);

				$arr_response['status'] = 'success';
				$arr_response['msg']    = 'Thank you for signing up with '.config('app.project.name').'. For verification purposes and to complete the registration process, a one-time code is being sent to your registered mobile phone. Please enter this code.';
				$arr_response['data']   = $arr_tmp;
				return $arr_response;
			}
			else
			{

				$arr_response['status'] = 'error';
				$arr_response['msg']    = 'Problem occurred while storing details';
				$arr_response['data']   = [];
				return $arr_response;
			}

			$arr_response['status'] = 'error';
			$arr_response['msg']    = 'Problem occurred while storing details';
			$arr_response['data']   = [];
			return $arr_response;
		}
		catch (\Exception $e) 
		{
			$arr_response['status'] = 'error';
			$arr_response['msg']    = $e->getMessage();
			$arr_response['data'] 	= [];
			return $arr_response;
		}
	}

	public function update_driver_vehicle_details($request)
	{
		$arr_data = $arr_rules = $arr_response = array();

		try
		{
			$arr_rules['driver_id']             = "required";
			$arr_rules['vehicle_type_id']       = "required";
			$arr_rules['vehicle_brand_id']      = "required";
			$arr_rules['vehicle_model_id']      = "required";
			$arr_rules['vehicle_number']        = "required";
			$arr_rules['is_usdot_doc_required'] = "required";
			$arr_rules['is_mc_doc_required']    = "required";

			$validator = Validator::make($request->all(),$arr_rules);
			if($validator->fails())
			{
				$arr_response['status'] = 'error';
				$arr_response['msg']    = 'Please fill all the required field';
				$arr_response['data']   = [];
				return $arr_response;
			}

			$driver_id             = $request->input('driver_id');
			$vehicle_type_id       = $request->input('vehicle_type_id');
			$vehicle_brand_id      = $request->input('vehicle_brand_id');
			$vehicle_model_id      = $request->input('vehicle_model_id');
			$vehicle_number        = $request->input('vehicle_number');
			$is_usdot_doc_required = $request->input('is_usdot_doc_required');
			$is_mc_doc_required    = $request->input('is_mc_doc_required');

			$arr_data = [];

			$arr_data['driving_license'] = '';
			$arr_data['is_driving_license_verified'] = 'NOTAPPROVED';

			if($request->hasFile('driving_license'))
			{
				$driving_license = $request->input('driving_license');
				$file_extension = strtolower($request->file('driving_license')->getClientOriginalExtension());
				if(in_array($file_extension,['png','jpg','jpeg','pdf']))
				{
					$driving_license = time().uniqid().'.'.$file_extension;
					$isUpload = $request->file('driving_license')->move($this->driving_license_base_path , $driving_license);
					$arr_data['is_driving_license_verified'] = 'PENDING';
					$arr_data['driving_license'] = $driving_license;
				}
			}
			
			$obj_status = $this->UserModel->where('id',$driver_id)->update($arr_data);

			if($obj_status)
			{ 
				$arr_vehicle = [];
						
				$arr_vehicle['vehicle_type_id']       = $vehicle_type_id;
				$arr_vehicle['vehicle_brand_id']      = $vehicle_brand_id;
				$arr_vehicle['vehicle_model_id']      = $vehicle_model_id;
				//$arr_vehicle['vehicle_year_id']       = $request->input('vehicle_year_id');
				$arr_vehicle['vehicle_number']        = $request->input('vehicle_number');
				$arr_vehicle['is_active']             = 1;
				$arr_vehicle['is_verified']           = 0;
				$arr_vehicle['is_individual_vehicle'] = 1;

				if($request->hasFile('vehicle_image'))
				{
					$vehicle_image = $request->input('vehicle_image');
					$file_extension = strtolower($request->file('vehicle_image')->getClientOriginalExtension());
					if(in_array($file_extension,['png','jpg','jpeg','pdf']))
					{
						$vehicle_image = time().uniqid().'.'.$file_extension;
						$isUpload = $request->file('vehicle_image')->move($this->vehicle_doc_base_path , $vehicle_image);
						$arr_vehicle['vehicle_image'] = $vehicle_image;
						$arr_vehicle['is_vehicle_image_verified']           = 'PENDING';
					}
				}

				if($request->hasFile('registration_doc'))
				{
					$registration_doc = $request->input('registration_doc');
					$file_extension = strtolower($request->file('registration_doc')->getClientOriginalExtension());
					if(in_array($file_extension,['png','jpg','jpeg','pdf']))
					{
						$registration_doc = time().uniqid().'.'.$file_extension;
						$isUpload = $request->file('registration_doc')->move($this->vehicle_doc_base_path , $registration_doc);
						$arr_vehicle['registration_doc'] = $registration_doc;
						$arr_vehicle['is_registration_doc_verified']        = 'PENDING';
					}
				}
				
				if($request->hasFile('vehicle_insurance_doc'))
				{
					$vehicle_insurance_doc = $request->input('vehicle_insurance_doc');
					$file_extension = strtolower($request->file('vehicle_insurance_doc')->getClientOriginalExtension());
					if(in_array($file_extension,['png','jpg','jpeg','pdf']))
					{
						$vehicle_insurance_doc = time().uniqid().'.'.$file_extension;
						$isUpload = $request->file('vehicle_insurance_doc')->move($this->vehicle_doc_base_path , $vehicle_insurance_doc);
						$arr_vehicle['insurance_doc'] = $vehicle_insurance_doc;
						$arr_vehicle['is_insurance_doc_verified']           = 'PENDING';
					}
				}
				
				if($request->hasFile('proof_of_inspection_doc'))
				{
					$proof_of_inspection_doc = $request->input('proof_of_inspection_doc');
					$file_extension = strtolower($request->file('proof_of_inspection_doc')->getClientOriginalExtension());
					if(in_array($file_extension,['png','jpg','jpeg','pdf']))
					{
						$proof_of_inspection_doc = time().uniqid().'.'.$file_extension;
						$isUpload = $request->file('proof_of_inspection_doc')->move($this->vehicle_doc_base_path , $proof_of_inspection_doc);
						$arr_vehicle['proof_of_inspection_doc'] = $proof_of_inspection_doc;
						$arr_vehicle['is_proof_of_inspection_doc_verified'] = 'PENDING';
					}
				}
				
				if($request->hasFile('dmv_driving_record'))
				{
					$dmv_driving_record = $request->input('dmv_driving_record');
					$file_extension = strtolower($request->file('dmv_driving_record')->getClientOriginalExtension());
					if(in_array($file_extension,['png','jpg','jpeg','pdf']))
					{
						$dmv_driving_record = time().uniqid().'.'.$file_extension;
						$isUpload = $request->file('dmv_driving_record')->move($this->vehicle_doc_base_path , $dmv_driving_record);
						$arr_vehicle['dmv_driving_record'] = $dmv_driving_record;
						$arr_vehicle['is_dmv_driving_record_verified']      = 'PENDING';
					}
				}

				if($request->hasFile('usdot_doc'))
				{
					$usdot_doc = $request->input('usdot_doc');
					$file_extension = strtolower($request->file('usdot_doc')->getClientOriginalExtension());
					if(in_array($file_extension,['png','jpg','jpeg','pdf']))
					{
						$usdot_doc = time().uniqid().'.'.$file_extension;
						$isUpload = $request->file('usdot_doc')->move($this->vehicle_doc_base_path , $usdot_doc);
						$arr_vehicle['usdot_doc'] = $usdot_doc;
						$arr_vehicle['is_usdot_doc_verified']               = 'PENDING';
					}
				}

				if($request->hasFile('mc_doc'))
				{
					$mc_doc = $request->input('mc_doc');
					$file_extension = strtolower($request->file('mc_doc')->getClientOriginalExtension());
					if(in_array($file_extension,['png','jpg','jpeg','pdf']))
					{
						$mc_doc = time().uniqid().'.'.$file_extension;
						$isUpload = $request->file('mc_doc')->move($this->vehicle_doc_base_path , $mc_doc);
						$arr_vehicle['mc_doc'] = $mc_doc;
						$arr_vehicle['is_mcdoc_doc_verified']               = 'PENDING';
					}
				}

				// if($is_usdot_doc_required == 'YES')
		  //       {
		  //       	$arr_vehicle['is_usdot_doc_verified']               = 'APPROVED';
		  //       }
		        
		        $vehicle_status = $this->VehicleModel->create($arr_vehicle);
				if($vehicle_status){
					$vehicle_id = isset($vehicle_status->id)?$vehicle_status->id:0;
					$this->DriverCarRelationModel->where('driver_id',$driver_id)->update(['vehicle_id' => $vehicle_id,'is_individual_vehicle'=>'1']);
				}

				/*$arr_response['status'] = 'success';
				$arr_response['msg']    = 'Your documents have been sent successfully for verification.';
				$arr_response['data']	= [];
				return $arr_response;*/

				$arr_response_data = $this->JwtUserService->generate_user_jwt_token($driver_id);		
				if(isset($arr_response_data) && sizeof($arr_response_data)>0){
					$arr_response['status'] = 'success';
					$arr_response['msg']    = 'Your documents have been sent successfully for verification.';
					$arr_response['data']	= $arr_response_data;
					return $arr_response;
				}
				else{
					$arr_response['status'] = 'error';
					$arr_response['msg']    = 'Something went wrong. Please try again.';
					$arr_response['data']	= [];
					return $arr_response;
				}
			}
			else
			{

				$arr_response['status'] = 'error';
				$arr_response['msg']    = 'Problem occurred while storing driver vehicle details';
				$arr_response['data']   = [];
				return $arr_response;
			}

			$arr_response['status'] = 'error';
			$arr_response['msg']    = 'Problem occurred while storing driver vehicle details';
			$arr_response['data']   = [];
			return $arr_response;
		}
		catch (\Exception $e) 
		{
			$arr_response['status'] = 'error';
			$arr_response['msg']    = $e->getMessage();
			$arr_response['data'] 	= [];
			return $arr_response;
		}
	}
	public function process_login($request)
	{
		$arr_rule     = [];
		$arr_response = [];

		$arr_rule['mobile_no'] = 'required';
		$arr_rule['password']  = 'required';
		$arr_rule['user_type'] = 'required';
		/*dd($request->all());*/
		$validator = Validator::make($request->all(), $arr_rule);

		if($validator->fails())
		{
			$arr_response['status']   = 'error';
			$arr_response['msg']      = 'Please fill all the required field.';
			$arr_response['data']     = [];
			return $arr_response;
		}

		$arr_credentials = [];
		
		$mobile_no    = $request->input('mobile_no');
		$password     = $request->input('password');
		$user_type    = $request->input('user_type');
		$device_id    = $request->input('device_id');
		$is_confirm   = $request->input('is_confirm');
		
		if(isset($mobile_no) && isset($password))
		{
			try 
			{
				$obj_user = $this->UserModel
									->where('mobile_no', $mobile_no)
									->where('user_type', $user_type)
									->first();

				if ($obj_user == null) 
				{
					$arr_response['status']   = 'error';
					$arr_response['msg']      = 'Invalid credentials';
					$arr_response['data']     = [];
					return $arr_response;
				} 
				if(isset($obj_user->via_social) && $obj_user->via_social=='1')
				{
					$arr_response['status']   = 'error';
					$arr_response['msg']      = 'You have to login through facebook';
					$arr_response['data']     = [];
					return $arr_response;
				}
				$instance = false;

				if ($obj_user!=false) 
				{
					$instance  = $this->NativeHasher->check($password, $obj_user->password);
				}
				if ($instance == false) 
				{
					$arr_response['status']   = 'error';
					$arr_response['msg']      = 'Invalid credentials';
					$arr_response['data'] 	  = [];
					return $arr_response;
				}
				if($obj_user != false)
				{   
					$new_mobile_no = isset($obj_user->mobile_no)? $obj_user->mobile_no :'';
					$arr_credentials = [];
					
					$arr_credentials['mobile_no']  = $new_mobile_no;
					$arr_credentials['password']   = $password;
					$arr_credentials['user_type']  = $user_type;
					
					//check whether user has any role
					$obj_user  = Sentinel::stateless($arr_credentials);
					if($obj_user)
					{
						$is_valid_user = false;
						$user_role = "";
						$user = Sentinel::check();

						if($user_type == 'DRIVER' && $user->inRole('driver'))
						{
							$is_valid_user = true;
							$user_role = 'DRIVER';
						}
						elseif($user_type == 'USER' && $user->inRole('user'))
						{
							$is_valid_user = true;
							$user_role = 'USER';
						}

						if ($is_valid_user == false) 
						{
							$arr_response['status']   = 'error';
							$arr_response['msg']      = 'Not sufficient privileges';
							$arr_response['data']     = [];
							return $arr_response;
						}

						//checking whether user is verified or not
						$status = isset($obj_user->is_otp_verified) ? $obj_user->is_otp_verified : 0;

						if($status == 0)
						{
							// resend verification code
							$generated_otp    = $this->generate_otp();

							$up_data 		= array(	'otp'			  => $generated_otp,
														'is_otp_verified' => 0
													);
							
							$result = $obj_user->update($up_data);

							$arr_tmp = [];
							$arr_tmp['mobile_no'] = isset($obj_user->mobile_no) ? $obj_user->mobile_no :'';

							$country_code = isset($obj_user->country_code) ? $obj_user->country_code :'';
							$mobile_no = isset($obj_user->mobile_no) ? $obj_user->mobile_no :'';

							$to_user_mobile = $country_code.''.$mobile_no;
							$message = 'Your OTP is '.$generated_otp.' Thanks.'.config('app.project.name');
							
							$arr_twilio_data = [
													'mobile_no' => $to_user_mobile,
													'message' 	=> $message
											   ];

							$this->get_sms_to_user($arr_twilio_data);

							$arr_response['status']   = 'not_verified';
							$arr_response['msg']      = 'Your account is not verified';
							$arr_response['data']     = $arr_tmp;
							return $arr_response;
						}

						if ($user && isset($user->is_active) && $user->is_active == '0') 
						{
							$arr_response['status']   = 'error';
							$arr_response['msg']      = 'Your account is blocked by Admin.';
							$arr_response['data']     = [];
							return $arr_response;
						}

						if ($user && isset($user->reset_password_mandatory) && $user->reset_password_mandatory == '1') 
						{
							// $arr_response['status']   = 'reset_password';
							// $arr_response['msg']      = 'Your account is blocked by Admin.';
							// $arr_response['data']     = [];
							// return $arr_response;
							$user_id = isset($obj_user->id) ? $obj_user->id : 0;
							$arr_response_data = $this->JwtUserService->generate_user_jwt_token($user_id);
						
							if(isset($arr_response_data) && sizeof($arr_response_data)>0)
							{
								$arr_response['status']   = 'reset_password';
								$arr_response['msg']      = 'Please reset your password.';
								$arr_response['data']	= $arr_response_data;
								return $arr_response;
							}
							else
							{
								$arr_response['status'] = 'error';
								$arr_response['msg']    = 'Something went wrong. Please try again.';
								$arr_response['data']	= [];
								return $arr_response;
							}
						}

						if($is_valid_user == true && $user_type == 'DRIVER'){
								
							$driver_id = isset($obj_user->id) ? $obj_user->id : 0;
							if($driver_id!=0)
							{
								$arr_driver_vehicle_details = $this->CommonDataService->get_driver_vehicle_type_details($driver_id);
								
								if(isset($arr_driver_vehicle_details['is_individual_vehicle']) && $arr_driver_vehicle_details['is_individual_vehicle'] == '1')
								{
									if(isset($arr_driver_vehicle_details['vehicle_details']) == false)
									{
										$is_individual_vehicle = isset($arr_driver_vehicle_details['is_individual_vehicle']) ? $arr_driver_vehicle_details['is_individual_vehicle'] : '1';
										$arr_response['status'] = 'new_driver';
										$arr_response['msg']    = 'Please add your vehicle details';
										$arr_response['data']	= ['driver_id' => $driver_id,'is_individual_vehicle' => strval($is_individual_vehicle)];
										return $arr_response;
									}
								}
								else if(isset($arr_driver_vehicle_details['is_individual_vehicle']) && $arr_driver_vehicle_details['is_individual_vehicle'] == '0')
								{
									$is_individual_vehicle = isset($arr_driver_vehicle_details['is_individual_vehicle']) ? $arr_driver_vehicle_details['is_individual_vehicle'] : '0';
									if(isset($arr_driver_vehicle_details['vehicle_details']) == false)
									{
										$arr_response['status'] = 'not_assigned';
										$arr_response['msg']    = 'Admin has not assigned any vehicle to you yet, if you have your own vehicle then select yes option';
										$arr_response['data']	= ['driver_id' => $driver_id,'is_individual_vehicle'=>$is_individual_vehicle];
										return $arr_response;

									}	
								}
							}
						}

						if($user_role == 'DRIVER')
						{
							if ($user) 
							{	
								$driver_id = isset($obj_user->id) ? $obj_user->id : 0;

								$arr_driver_vehicle_details = $this->CommonDataService->get_driver_vehicle_type_details($driver_id);
								//dd($arr_driver_vehicle_details);
								$is_usdot_doc_verified = 'APPROVED';

								if(isset($arr_driver_vehicle_details['vehicle_details']['vehicle_type_details']['is_usdot_required']) && $arr_driver_vehicle_details['vehicle_details']['vehicle_type_details']['is_usdot_required'] == '1')
								{
									$is_usdot_doc_verified = isset($arr_driver_vehicle_details['vehicle_details']['is_usdot_doc_verified']) ? $arr_driver_vehicle_details['vehicle_details']['is_usdot_doc_verified'] : '';
								}

								$is_mcdoc_verified = 'APPROVED';

								if(isset($arr_driver_vehicle_details['vehicle_details']['vehicle_type_details']['is_mcdoc_required']) && $arr_driver_vehicle_details['vehicle_details']['vehicle_type_details']['is_mcdoc_required'] == '1')
								{
									$is_mcdoc_verified = isset($arr_driver_vehicle_details['vehicle_details']['is_mcdoc_doc_verified']) ? $arr_driver_vehicle_details['vehicle_details']['is_mcdoc_doc_verified'] : '';
								}

								$is_driving_license_verified         = isset($user->is_driving_license_verified) ? $user->is_driving_license_verified : '';
						        $is_vehicle_image_verified           = isset($arr_driver_vehicle_details['vehicle_details']['is_vehicle_image_verified']) ? $arr_driver_vehicle_details['vehicle_details']['is_vehicle_image_verified'] : '';
						        $is_registration_doc_verified        = isset($arr_driver_vehicle_details['vehicle_details']['is_registration_doc_verified']) ? $arr_driver_vehicle_details['vehicle_details']['is_registration_doc_verified'] : '';
						        $is_insurance_doc_verified           = isset($arr_driver_vehicle_details['vehicle_details']['is_insurance_doc_verified']) ? $arr_driver_vehicle_details['vehicle_details']['is_insurance_doc_verified'] : '';
						        $is_proof_of_inspection_doc_verified = isset($arr_driver_vehicle_details['vehicle_details']['is_proof_of_inspection_doc_verified']) ? $arr_driver_vehicle_details['vehicle_details']['is_proof_of_inspection_doc_verified'] : '';
						        $is_dmv_driving_record_verified      = isset($arr_driver_vehicle_details['vehicle_details']['is_dmv_driving_record_verified']) ? $arr_driver_vehicle_details['vehicle_details']['is_dmv_driving_record_verified'] : '';
						        //$is_usdot_doc_verified               = isset($arr_driver_vehicle_details['vehicle_details']['is_usdot_doc_verified']) ? $arr_driver_vehicle_details['vehicle_details']['is_usdot_doc_verified'] : '';

						        $is_all_document_verified = 'NO';

						        if( 
						            $is_driving_license_verified          == 'APPROVED' && 
						            $is_vehicle_image_verified            == 'APPROVED' &&
						            $is_registration_doc_verified         == 'APPROVED' &&
						            $is_insurance_doc_verified            == 'APPROVED' &&
						            $is_proof_of_inspection_doc_verified  == 'APPROVED' &&
						            $is_dmv_driving_record_verified       == 'APPROVED' &&
						            $is_usdot_doc_verified                == 'APPROVED' &&
						            $is_mcdoc_verified                    == 'APPROVED'

						           )
						        {
						            $is_all_document_verified = 'YES';
						        }
								
								/*dd($is_all_document_verified);*/

								if($is_all_document_verified == 'NO')
								{
									$arr_response_data = $this->JwtUserService->generate_user_jwt_token($driver_id);
								
									if(isset($arr_response_data) && sizeof($arr_response_data)>0)
									{
										$arr_response['status']   = 'not_approved';
										$arr_response['msg']      = 'Your vehicle is not verified by admin yet.';
										$arr_response['data']	= $arr_response_data;
										return $arr_response;
									}
									else
									{
										$arr_response['status'] = 'error';
										$arr_response['msg']    = 'Something went wrong. Please try again.';
										$arr_response['data']	= [];
										return $arr_response;
									}
								}
								if(isset($arr_driver_vehicle_details['vehicle_details']['is_verified']) && $arr_driver_vehicle_details['vehicle_details']['is_verified'] == '0')
								{
									$arr_response_data = $this->JwtUserService->generate_user_jwt_token($driver_id);
						
									if(isset($arr_response_data) && sizeof($arr_response_data)>0)
									{
										$arr_response['status']   = 'not_approved';
										$arr_response['msg']      = 'Your vehicle is not verified by admin yet.';
										$arr_response['data']	= $arr_response_data;
										return $arr_response;
									}
									else
									{
										$arr_response['status'] = 'error';
										$arr_response['msg']    = 'Something went wrong. Please try again.';
										$arr_response['data']	= [];
										return $arr_response;
									}
								}
							}

							if ($user && isset($user->account_status) && $user->account_status == 'unapproved') 
							{
								$arr_response['status']   = 'error';
								$arr_response['msg']      = 'Your account is not approved yet.';
								$arr_response['data']     = [];
								return $arr_response;
							}
						}
						
						if($is_valid_user == true)                        
						{	
							$user_id = isset($obj_user->id) ? $obj_user->id :0;

							if(isset($is_confirm) && $is_confirm == '0')
							{
								if(
									(isset($obj_user->device_id) && $obj_user->device_id!='') && 
									(isset($device_id) && $device_id!='') && 
									($obj_user->device_id != $device_id)
									)
								{
									$arr_response['status'] = 'already_logged_in';
									$arr_response['msg']    = 'You already have an ongoing session with this account on another device, Do you really want to proceed further with this current session?';
									$arr_response['data']	= [];
									return $arr_response;
								}
							}
							/*check for driver cuurent trip ongoing if yes then not allowed to login him from another mobile*/
							/*if($user_role == 'DRIVER')
							{
								if($this->check_driver_current_trip($driver_id))
								{
									$obj_data = $this->UserModel->select('id','device_id')->where('id',$user_id)->first();

									if((isset($obj_data->device_id) && $obj_data->device_id!='') && $obj_data->device_id!=$device_id)
					                {
					                    $arr_response['status'] = 'error';
										$arr_response['msg']    = 'Trip is already in progress from another device';
										$arr_response['data']	= [];
										return $arr_response;
					                }
								}
							}*/

							if($device_id!='')
							{
								$this->update_user_device_id($user_id,$device_id);
							}
							$arr_response_data = $this->JwtUserService->generate_user_jwt_token($user_id);
						
							if(isset($arr_response_data) && sizeof($arr_response_data)>0){
								$arr_response['status'] = 'success';
								$arr_response['msg']    = 'You have been logged in successfully.';
								$arr_response['data']	= $arr_response_data;
								return $arr_response;
							}
							else{
								$arr_response['status'] = 'error';
								$arr_response['msg']    = 'Something went wrong. Please try again.';
								$arr_response['data']	= [];
								return $arr_response;
							}
						}
						else
						{
							$arr_response['status']   = 'error';
							$arr_response['msg']      = 'Not Sufficient Privileges.';
							$arr_response['data']     = [];
							return $arr_response;
						}
					} 
					else 
					{
						$arr_response['status'] = "error";
						$arr_response['msg']    = 'Error while login to your account';
						$arr_response['data']   = [];
						return $arr_response;
					}
				}                
			} 
			catch (\Exception $e) 
			{
				$arr_response['status'] = 'error';
				$arr_response['msg']    = $e->getMessage();
				$arr_response['data'] = [];
				return $arr_response;
			}
		}  
		$arr_response['status'] = 'error';
		$arr_response['msg']    = 'Error while login to your account.';
		$arr_response['data']   = [];
		return $arr_response;
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
    public function update_user_device_id($user_id,$device_id)
	{
		if($user_id!=0 && $device_id!=''){
			$this->UserModel->where('id',$user_id)->update(['device_id'=>$device_id]);
			return true;
		}	
	}
	public function login_facebook($request)
	{
		$arr_response = $arr_credentials = $arr_rules = $arr_empty = array();
		$temp_obj = $obj_user = $obj_empty = (Object) $arr_empty;

		$arr_rule['email']		= 'required';
		$arr_rule['user_type']	= 'required';

		$validator = Validator::make($request->all(), $arr_rule);

		if($validator->fails())
		{                                   
			$arr_response['status']	  = 'error';
			$arr_response['msg']	  = 'Please fill all the required field';
			$arr_response['data']	  = [];
			return $arr_response;
		}

		$email 		= strtolower($request->input('email'));
		$password 	= 'Admin@123';
		$user_type 	= $request->input('user_type');

		$device_id    = $request->input('device_id');

		if($request->has('first_name'))
		{
			$first_name = $request->input('first_name'); 
		}else
		{
			$first_name = "";
		}

		if($request->has('last_name'))
		{
			$last_name = $request->input('last_name'); 
		}
		else
		{
			$last_name = "";
		}

		$arr_user_role = ['ADMIN','COMPANY','USER'];
		
		$is_email_duplicate = $this->UserModel
										->where('email', $email)
										->whereIn('user_type',$arr_user_role)
										->where('via_social', 0)
										->count();
		if($is_email_duplicate > 0)
		{
			$arr_response['status']	  = 'existing_account';
			$arr_response['msg']	  = 'This email is already registered with us. Please login to continue';
			$arr_response['data']	  = [];
			return $arr_response;
		}

        if(isset($email) && isset($password))
        {
        	try 
        	{
        		$user = Sentinel::createModel();
                $obj_user = $user->where('email', '=', $email)
                				->whereIn('user_type',$arr_user_role);

                $obj_user =  $obj_user->first();
                // dd($obj_user);
                if(isset($obj_user->via_social) && $obj_user->via_social == '0')
                {
                	$arr_response['status']   = 'error';
                	$arr_response['msg']      = "You can't login through facebook";
                	$arr_response['data'] = [];
                	return $arr_response;
                }

                if ($obj_user == null) 
                {
                	// Send OTP SMS
                	$arr_tmp               = [];
                	$arr_tmp['first_name'] = $first_name;
                	$arr_tmp['last_name']  = $last_name;
                	$arr_tmp['email']      = $email;

                	$arr_response['status']		= 'new_user';
					$arr_response['msg']		= 'Please Register';
					$arr_response['data']	= $arr_tmp;
                	return $arr_response;
                }
                if(isset($obj_user->is_otp_verified) && $obj_user->is_otp_verified == 0)
                {
                	$otp    = $this->generate_otp();
					
					$up_data['otp']				        = $otp;
					$up_data['is_active']				= 1;
                	$up_data['is_otp_verified']			= 0;
                	$result = $obj_user->update($up_data);

                	if($result)
                	{
                		$mobile_no = isset($obj_user->mobile_no) ? $obj_user->mobile_no :'';

                		$arr_tmp               = [];
	                	$arr_tmp['mobile_no']   = $mobile_no;

	                	$country_code = isset($obj_user->country_code) ? $obj_user->country_code :'';
						$mobile_no = isset($obj_user->mobile_no) ? $obj_user->mobile_no :'';

						$to_user_mobile = $country_code.''.$mobile_no;
						$message = 'Your OTP is '.$otp.' Thanks.'.config('app.project.name');
						
						$arr_twilio_data = [
												'mobile_no' => $to_user_mobile,
												'message' 	=> $message
										   ];

						$this->get_sms_to_user($arr_twilio_data);

	                	$arr_response['status']	= 'not_verfied';
						$arr_response['msg']	= 'Please verify your account to continue';
						$arr_response['data']	= $arr_tmp;
	                	return $arr_response;

	                }
	                else
	                {
	                	$arr_response['status']   = 'error';
	                	$arr_response['msg']      = 'Please try again';
	                	$arr_response['data'] = [];
	                	return $arr_response;
	                }
                }

                $instance = false;

                if ($obj_user != false) 
                {
                	$instance  = $this->NativeHasher->check($password, $obj_user->password);
                }

                if ($instance == false) 
                {
                	$arr_response['status']   = 'error';
                	$arr_response['msg']      = 'Invalid credentials';
                	$arr_response['data'] = [];
                	return $arr_response;
                }

                if($obj_user != false)
                {
                	// account activation check.
                	
                	$arr_credentials = [];

                	if($obj_user)
                	{
                		$arr_result = $obj_user->toArray();
                	}
                	$arr_credentials['mobile_no']	= $arr_result['mobile_no'];
                	$arr_credentials['password']	= $password;
                	$arr_credentials['user_type']	= 'USER';
                		
                	//check whether user has any role
                	$obj_user  = Sentinel::stateless($arr_credentials);

                	if($obj_user)
                	{
                		$is_valid_user = false;
                		$user_role = "";
                		$user = Sentinel::check();
                		if($user_type == 'USER' && $user->inRole('user'))
                		{
                			$is_valid_user = true;
                			$user_role = 'USER';
                		}

                		if ($is_valid_user == false) 
                		{
                			$arr_response['status']   = 'error';
                			$arr_response['msg']      = 'Not sufficient privileges';
                			$arr_response['data'] = [];
                			return $arr_response;
                		}
                			
                		if ($user && isset($user->is_user_block_by_admin) && $user->is_user_block_by_admin == 1) 
                		{
                			$arr_response['status']   = 'error';
                			$arr_response['msg']      = 'Your account is blocked by Admin';
                			$arr_response['data'] = [];
                			return $arr_response;
                		}
                									      
                		if($is_valid_user == true)                        
                		{
                			$user_id = isset($arr_result['id']) ? $arr_result['id'] : 0;

                			if($device_id!='')
							{
								$this->update_user_device_id($user_id,$device_id);
							}

							$arr_response_data = $this->JwtUserService->generate_user_jwt_token($user_id);

							if(isset($arr_response_data) && sizeof($arr_response_data)>0)
							{
								$arr_response['status'] = 'success';
								$arr_response['msg']    = 'You have been logged in successfully.';
								$arr_response['data']	= $arr_response_data;
								return $arr_response;
							}
							else
							{
								$arr_response['status'] = 'error';
								$arr_response['msg']    = 'Something went wrong. Please try again.';
								$arr_response['data']	= [];
								return $arr_response;
							}
                        }
                        else
                        {
                        	$arr_response['status']   = 'error';
                        	$arr_response['msg']      = 'Not sufficient privileges';
                        	$arr_response['data'] = [];
                        	return $arr_response;
                        }
                    } 
                    else 
                    {
                    	$arr_response['status'] = "error";
                    	$arr_response['msg']    = 'Error while login to your account';
                    	$arr_response['data'] = [];
                    	return $arr_response;
                    }
                }                
            }
            catch (\Exception $e) 
            {
            	$arr_response['msg']		= $e->getMessage();
            	$arr_response['status']		= 'error';
            	$arr_response['data']	= [];
            	return $arr_response;
            }
        }

        $arr_response['status'] 	= 'error';
        $arr_response['msg']    	= 'Error while login to your account.';
        $arr_response['data']	= [];
        return $arr_response;
    }

	public function register_facebook($request)
	{
		$arr_response = $arr_credentials = $arr_rules = $arr_empty = array();
		$obj_temp = $obj_user = $obj_empty = (Object) $arr_empty;

		$arr_rule['email']		= 'required';
		$arr_rule['mobile_no']	= 'required';
		$arr_rule['user_type']	= 'required';

		$validator = Validator::make($request->all(), $arr_rule);

		if($validator->fails())
		{                                   
			$arr_response['status']	= 'error';
			$arr_response['msg']	= 'Please fill all the required field';
			$arr_response['data']	= [];
			return $arr_response;
		}

		$email 		 = strtolower($request->input('email'));
		$mobile_no 	 = $request->input('mobile_no');
		$password 	 = 'Admin@123';
		$user_type 	 = $request->input('user_type');
		$country_code= $request->input('country_code');

		if($request->has('first_name'))
		{
			$first_name = $request->input('first_name'); 
		}else
		{
			$first_name = "";
		}

		if($request->has('last_name'))
		{
			$last_name = $request->input('last_name'); 
		}
		else
		{
			$last_name = "";
		}

		$arr_user_role = ['ADMIN','COMPANY','USER'];

		$is_mobile_no_duplicate = $this->UserModel
										->where('mobile_no', $mobile_no)
										->whereIn('user_type',$arr_user_role)
										// ->where('via_social', 0)
										->count();
		if($is_mobile_no_duplicate > 0)
		{
			$arr_response['status']	= 'existing_account';
			$arr_response['msg']	= 'This number is already registered with us. Please login to continue';
			$arr_response['data']	= [];
			return $arr_response;
		}

        if(isset($mobile_no) && isset($password))
        {
        	try
        	{
        		$user = Sentinel::createModel();
                $obj_user =  $user
                				->where('mobile_no', '=', $mobile_no)
				                ->whereIn('user_type',$arr_user_role);;

                $obj_user =  $obj_user->first(); 
                if(isset($obj_user->via_social) && $obj_user->via_social == 0)
                {
                	$arr_response['status']   = 'error';
                	$arr_response['msg']      = "This number is already registered with us. Please login to continue";
                	$arr_response['arr_user'] = $obj_user;
                	return $arr_response;
                }

                if ($obj_user == null)
                {
                	$arr_data =[];
                	$arr_data['email']		 = $email;
                	$arr_data['country_code']= isset($country_code) ? $country_code :'';
                	$arr_data['mobile_no']	 = $mobile_no;

                	$otp    = $this->generate_otp();

					$arr_data['otp']                = $otp;
					$arr_data['password']			= $password;
					$arr_data['first_name']			= $first_name;
					$arr_data['last_name']			= $last_name;
					$arr_data['via_social']			= '1';
					$arr_data['is_active']			= '1';
					$arr_data['is_otp_verified']	= '0';
					$arr_data['user_type']			= 'USER';

                	$obj_user = Sentinel::registerAndActivate($arr_data);
                	if($obj_user)
                	{ 
                		if($user_type=="USER")
                		{
                			$role = Sentinel::findRoleBySlug('user');
                			$obj_user->roles()->attach($role);
                		}

                		$arr_data['user_type']  = $user_type;

                		$arr_notification_data = $this->built_notification_data($arr_data); 
                		$this->NotificationsService->store_notification($arr_notification_data);

                		$arr_data['password']   = "";

                		/*$arr_mail_data = $this->built_mail_data($arr_data); 
                		$this->EmailService->send_mail($arr_mail_data);*/
                		
                		$arr_tmp = [];
						$arr_tmp['mobile_no'] = $mobile_no;

						$country_code = isset($obj_user->country_code) ? $obj_user->country_code :'';
						$mobile_no = isset($obj_user->mobile_no) ? $obj_user->mobile_no :'';

						$to_user_mobile = $country_code.''.$mobile_no;
						$message = 'Your OTP is '.$otp.' Thanks.'.config('app.project.name');
						
						$arr_twilio_data = [
												'mobile_no' => $to_user_mobile,
												'message' 	=> $message
										   ];

						$this->get_sms_to_user($arr_twilio_data);
						
                		$arr_response['status'] = 'success';
                		$arr_response['msg']    = 'You have been registered successfully. Please verify your account.';
                		$arr_response['data']   = $arr_tmp;
                    	return $arr_response;
                	}
                	else
                	{
                		$arr_response['status']   = 'error';
                		$arr_response['msg']      = 'Problem occurred while storing details';
                		$arr_response['data'] = [];
                		return $arr_response;
                	}
                }
            }
            catch (\Exception $e){
            	$arr_response['msg']	  = $e->getMessage();
            	$arr_response['status']	  = 'error';
            	$arr_response['data']	  = [];
            	return $arr_response;
            }
        }

        $arr_response['status']  = 'error';
        $arr_response['msg']     = 'Error while login to your account.';
        $arr_response['data']	 = [];
        return $arr_response;
    }

    public function verify_otp($request)
	{
		$arr_data = $arr_rules = $arr_empty = array();
		$obj_user = $obj_empty = (Object) $arr_empty;
		
		$mobile_no 	= $request->input('mobile_no');
		$otp 		= $request->input('otp');
		$user_type  = $request->input('user_type');
		$device_id  = $request->input('device_id');
		
		if( $mobile_no == "")
		{
			$arr_response['status'] = 'error';
			$arr_response['msg']    = 'Invalid request';
			$arr_response['data']   = [];
			return $arr_response;
		}

		if( $otp == "" )
		{
			$arr_response['status'] = 'error';
			$arr_response['msg']    = 'OTP field is required';
			$arr_response['data']   = [];
			return $arr_response;
		}

		$obj_user_existance = $this->UserModel
											->where("mobile_no",$mobile_no)
											->where('user_type',$user_type)
											->first();
		
		if($obj_user_existance!=null)
		{
			$arr_user_existance = $obj_user_existance->toArray();
			if(count($arr_user_existance)<1)
			{
				$arr_response['status'] = 'error';
				$arr_response['msg']    = 'This user is not registered with us';
				$arr_response['data']    = $obj_user;
				return $arr_response;
			}
			if(isset($arr_user_existance['otp']))
			{
				if($arr_user_existance['otp'] == $otp)
				{
					$user_id = isset($arr_user_existance['id']) ? $arr_user_existance['id'] :0;
					$verified = $this->UserModel
									->where("id",$user_id)
									->update(	array( "is_otp_verified"		=> 1,
														"otp"					=> '',
														"otp_type"				=> '',
														"availability_status"   => 'ONLINE',
														'device_id'				=> $device_id
													)
											);
					if($verified)
					{
						/*if($obj_user_existance->inRole('driver'))
						{
							$driver_id = isset($arr_user_existance['id']) ? $arr_user_existance['id'] : 0;
							if($driver_id!=0)
							{
								$arr_driver_vehicle_details = $this->CommonDataService->get_driver_vehicle_type_details($driver_id);

								if(isset($arr_driver_vehicle_details['is_individual_vehicle']) && $arr_driver_vehicle_details['is_individual_vehicle'] == '1')
								{
									if(isset($arr_driver_vehicle_details['vehicle_details']) == false)
									{
										$arr_response['status'] = 'new_driver';
										$arr_response['msg']    = 'Please upload your vehicle details';
										$arr_response['data']	= ['driver_id' => $driver_id];
										return $arr_response;
									}
								}
								else if(isset($arr_driver_vehicle_details['is_individual_vehicle']) && $arr_driver_vehicle_details['is_individual_vehicle'] == '0')
								{
									if(isset($arr_driver_vehicle_details['vehicle_details']) == false)
									{
										$arr_response['status'] = 'error';
										$arr_response['msg']    = 'Admin has not assigned any vehicle to you yet.';
										return $arr_response;
									}	
								}
							}
						}*/
						$arr_response_data = $this->JwtUserService->generate_user_jwt_token($user_id);
						
						if(isset($arr_response_data) && sizeof($arr_response_data)>0){
							$arr_response['status'] = 'success';
							$arr_response['msg']    = 'OTP verified successfully.';
							$arr_response['data']	= $arr_response_data;
							return $arr_response;
						}
						else{
							$arr_response['status'] = 'error';
							$arr_response['msg']    = 'Something went wrong. Please try again.';
							$arr_response['data']	= [];
							return $arr_response;
						}
					}
					else
					{
						$arr_response['status'] = 'error';
						$arr_response['msg']    = 'Something went wrong. Please try again.';
						$arr_response['data']	= [];
						return $arr_response;
					}
					
				}
				else
				{
					$arr_response['status'] = 'error';
					$arr_response['msg']    = 'Please enter valid OTP.';
					$arr_response['data']	= [];
					return $arr_response;        
				}    
			}
			else
			{
				$arr_response['status'] = 'error';
				$arr_response['msg']    = 'Something went wrong.';
				$arr_response['data']	= [];
				return $arr_response;        
			}                        
		}
		$arr_response['status'] = 'error';
		$arr_response['msg']    = 'This user is not registered with us';
		$arr_response['data']	= [];
		return $arr_response;
	}

	public function resend_otp($request)
	{
		$mobile_no = $request->input('mobile_no');
		$user_type = $request->input('user_type');

		if($mobile_no == "" || $user_type == "")
		{
			$arr_response['status']   = 'error';
			$arr_response['msg']      = 'Invalid user';
			$arr_response['data']	= [];
			return $arr_response;
		}
		$user_type = strtolower($request->input('user_type'));
		$new_otp = $this->generate_otp();
		$obj_user = $this->UserModel
								->where('mobile_no',$mobile_no)
								->where('user_type',strtoupper($user_type))
								->first();
		
		if($obj_user)
		{
			if($obj_user->inRole($user_type) == false){
				
				$msg = '';
				if($user_type == 'user'){
					$msg = 'Mobile number is not registered with customer app.';
				}	
				else if($user_type == 'driver'){
					$msg = 'Mobile number is not registered with driver app.';
				}

				$arr_response['status'] = 'error';
				$arr_response['msg']    = $msg;
				$arr_response['data']	= [];
				return $arr_response;        
			}

			$user_id = isset($obj_user->id) ? $obj_user->id :0;

			$result = $this->UserModel->where('id',$user_id)->update(array('otp' => $new_otp));
			if($result)
			{
				$arr_tmp = [];
				$arr_tmp['mobile_no'] = $mobile_no;
				$arr_tmp['otp']       = $new_otp;

				$country_code = isset($obj_user->country_code) ? $obj_user->country_code :'';
				$mobile_no = isset($obj_user->mobile_no) ? $obj_user->mobile_no :'';

				$to_user_mobile = $country_code.''.$mobile_no;
				$message = 'Your OTP is '.$new_otp.' Thanks.'.config('app.project.name');
				
				$arr_twilio_data = [
										'mobile_no' => $to_user_mobile,
										'message' 	=> $message
								   ];

				$this->get_sms_to_user($arr_twilio_data);
				
				$arr_response['status']   = 'success';
				$arr_response['msg']      = 'An OTP has been sent to your registered mobile number.';
				$arr_response['data']	= $arr_tmp;
				return $arr_response;    
			}
			else
			{
				$arr_response['status']   = 'error';
				$arr_response['msg']      = 'Something went wrong.Please try again.';
				$arr_response['data']	= [];
				return $arr_response;
			}
		}
		else
		{
			$arr_response['status']   = 'error';
			$arr_response['msg']      = 'Invalid user';
			$arr_response['data']	= [];
			return $arr_response;
		}
	}

	public function reset_password($request)
	{
		$user_id     = validate_user_jwt_token();
		if ($user_id == 0) 
		{
			$arr_response['status'] = 'error';
			$arr_response['msg']    = 'Invalid user token';
			$arr_response['data']	= [];
			return $arr_response;        
		}

		$arr_rules['password']      = "required";

		$validator = Validator::make($request->all(),$arr_rules);

		if($validator->fails())
		{
			$arr_response['status'] = 'error';
			$arr_response['msg']    = 'All fields are required.';
			$arr_response['data']	= [];
			return $arr_response;        
		}

		$password   = $request->input('password');

		$user = $this->UserModel->where('id',$user_id)->first();
		if( $user == null)
		{
			$arr_response['status'] = 'error';
			$arr_response['msg']    = 'Invaild request';
			$arr_response['data']	= [];
			return $arr_response;        
		} 
													                                                                                                       
		if(Sentinel::update($user,array('password' => $password)))
		{
			$user->reset_password_mandatory = '0';
			$user->save();
			
			$arr_response['status'] = 'success';
			$arr_response['msg']    = 'Password changed successfully';
			$arr_response['data']	= [];
			return $arr_response;        
		}
		
		else 
		{
			$arr_response['status'] = 'error';
			$arr_response['msg']    = 'Problem occured while resetting password';
			$arr_response['data']	= [];
			return $arr_response;        
		}   
				
		$arr_response['status'] = 'error';
		$arr_response['msg']    = 'Problem occured while resetting password';
		$arr_response['data']	= [];
		return $arr_response;        
	}

	public function forget_password($request)
	{
		$arr_empty = array();

		$obj_temp = $obj_user = $obj_empty = (Object) $arr_empty;

		$arr_rules['mobile_no']      = "required";
		$arr_rules['user_type']      = "required";

		$validator = Validator::make($request->all(),$arr_rules);

		if($validator->fails())
		{
			$arr_response['status'] = 'error';
			$arr_response['msg']    = 'Please enter valid mobile number';
			$arr_response['data']	= [];
			return $arr_response;        
		}

		$mobile_no = $request->input('mobile_no');
		$user_type = strtolower($request->input('user_type'));

		// $obj_user  = Sentinel::findByCredentials(['mobile_no' => $mobile_no]);

		$obj_user = $this->UserModel
							->where("mobile_no",$mobile_no)
							->where("user_type",strtoupper($user_type))
							->first();


		if($obj_user == null)
		{
			$arr_response['status'] = 'error';
			$arr_response['msg']    = 'This mobile number is not registered with us';
			$arr_response['data']	= [];
			return $arr_response;        
		}

		if($obj_user->inRole($user_type) == false){
			
			$msg = '';
			if($user_type == 'user')
			{
				$msg = 'Mobile number is not registered with customer app.';
			}	
			else if($user_type == 'driver'){
				$msg = 'Mobile number is not registered with driver app.';
			}
			
			$arr_response['status'] = 'error';
			$arr_response['msg']    = $msg;
			$arr_response['data']	= [];
			return $arr_response;        
		}

		$otp = $this->generate_otp();

		$otp_changed = $this->UserModel
								->where("mobile_no", $mobile_no)
								->where("user_type", strtoupper($user_type))
								->update(	array
											(	
												"otp"	   => $otp, 
												"otp_type" => "FORGET_PASSWORD"
											)
										);
		if($otp_changed)
		{
			$arr_tmp = [];
			$arr_tmp['mobile_no'] = $mobile_no;
			$arr_tmp['otp']       = $otp;

			$country_code = isset($obj_user->country_code) ? $obj_user->country_code :'';
			$mobile_no    = isset($obj_user->mobile_no) ? $obj_user->mobile_no :'';

			$to_user_mobile = $country_code.''.$mobile_no;
			$message = 'Your OTP is '.$otp.' Thanks.'.config('app.project.name');
			
			$arr_twilio_data = [
									'mobile_no' => $to_user_mobile,
									'message' 	=> $message
							   ];

			$this->get_sms_to_user($arr_twilio_data);

			$arr_response['status'] = 'success';
			$arr_response['msg']    = 'An OTP has been sent to your registered mobile number.';
			$arr_response['data']	= $arr_tmp;
			return $arr_response;        
		}
		else
		{
			$arr_response['status'] = 'error';
			$arr_response['msg']    = 'Error occured in forget password process.';
			$arr_response['data']	= [];
			return $arr_response;        
		}
		                                                                                                                                                 
		$arr_response['status'] = 'error';
		$arr_response['msg']    = 'Something went wrong,Please try again.';
		$arr_response['data']	= [];
		return $arr_response;        
	}

	public function change_password($request)
	{
		$arr_response = [];

		$user_id     = validate_user_jwt_token();
		if ($user_id == 0) 
		{
			$arr_response['status'] = 'error';
			$arr_response['msg']    = 'Invalid user token';
			return $arr_response;        
		}

		$arr_rules                      = array();
		$arr_rules['old_password']      = "required";
		$arr_rules['new_password']      = "required";
		
		$validator = Validator::make($request->all(),$arr_rules);

		if($validator->fails())
		{
			$arr_response['status'] = 'error';
			$arr_response['msg']    = 'All fields are required.';
			return $arr_response;        
		}

		$old_password          = $request->input('old_password');
		$new_password          = $request->input('new_password');
		
		try 
		{
			$user = Sentinel::findById($user_id);

			if(!$user)
			{
				$arr_response['status'] = 'error';
				$arr_response['msg']    = 'Invalid Request.';
				return $arr_response;        
			}

			$hasher = Sentinel::getHasher();
			if(!$hasher->check($old_password, $user['password']))
			{
				$arr_response['status'] = 'error';
				$arr_response['msg']    = 'Invalid old password.';
				return $arr_response;        
			} 
			else
			{
				if(Sentinel::update($user,array('password' => $new_password)))
				{
					$arr_response['status'] = 'success';
					$arr_response['msg']    = 'Password Changed Successfully.';
					return $arr_response;        
				}
				else
				{
					$arr_response['status'] = 'error';
					$arr_response['msg']    = 'Problem Occurred, While Changing Password.';
					return $arr_response;        
				} 
			}	
		}
		catch (\Exception $e) 
        {
        	$arr_response['msg']		= $e->getMessage();
        	$arr_response['status']		= 'error';
        	return $arr_response;
        }
        $arr_response['status'] = 'error';
		$arr_response['msg']    = 'Problem Occurred, While Changing Password.';
		return $arr_response;        
	}

	public function get_profile($request)
	{
		$arr_response = [];

		$user_id     = validate_user_jwt_token();
		if ($user_id == 0) 
		{
			$arr_response['status'] = 'error';
			$arr_response['msg']    = 'Invalid token';
			$arr_response['data']   = [];
			return $arr_response;        
		}

		$arr_response = $this->get_profile_details($user_id);
		
		return $arr_response;
	}

	public function update_profile($request)
	{
		$user_id     = validate_user_jwt_token();

		if ($user_id == 0) 
		{
			$arr_response['status'] = 'error';
			$arr_response['msg']    = 'Invalid token';
			$arr_response['data']   = [];
			return $arr_response;        
		}
		
		$arr_rules = [];

		$arr_rules['first_name'] = "required";
		$arr_rules['last_name']  = "required";
		// $arr_rules['gender']     = "required";
	    $arr_rules['address']    = "required";
		$arr_rules['latitude']   = "required";
		$arr_rules['longitude']  = "required";

		$validator = Validator::make($request->all(),$arr_rules);
		
		if($validator->fails())
		{
			$arr_response['status'] = 'error';
			$arr_response['msg']    = 'Please fill all the required field.';
			$arr_response['data']   = [];
			return $arr_response;        
		}

		$arr_update                 = [];

		$arr_update['first_name']   = $request->input('first_name');
		$arr_update['last_name']    = $request->input('last_name');
		// $arr_update['gender']       = $request->input('gender');
		if($request->input('dob')!='')
		{
			$arr_update['dob']			= date('Y-m-d',strtotime($request->input('dob')));
		}
		$arr_update['address']      = $request->input('address');
		$arr_update['city_name']    = $request->input('city_name');
		$arr_update['state_name']   = $request->input('state_name');
		$arr_update['post_code']    = $request->input('post_code');
		$arr_update['country_name'] = $request->input('country_name');
		$arr_update['latitude']     = $request->input('latitude');
		$arr_update['longitude']    = $request->input('longitude');

		$user_type = $request->input('user_type');

		if($request->has('email') && $request->input('email')!=''){
			$is_email_duplicate = $this->UserModel
									->where('email',trim($request->input('email')))
									->where('user_type',$user_type)
									->where('id','!=',$user_id)
									->count();
			if($is_email_duplicate>0)
			{
				$arr_response['status'] = 'error';
				$arr_response['msg']    = 'This email address already exists.';
				$arr_response['data']   = [];
				return $arr_response;        

			}
			$arr_update['email']        = $request->input('email');
		}

		$file_name = '';

		$arr_user = $this->UserModel->select('profile_image')->where('id',$user_id)->first();
		
		$oldImage = isset($arr_user->profile_image)? $arr_user->profile_image:"";
		$olddriving_license = isset($arr_user->driving_license)? $arr_user->driving_license:"";

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
				$arr_update['profile_image'] = $file_name;
			}
			else
			{
				$arr_response['status'] = 'error';
				$arr_response['msg']    = 'Please upload valid file.';
				$arr_response['data']   = [];
				return $arr_response;        
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
				if($isUpload)
				{
					@unlink($this->driving_license_base_path.$olddriving_license);
				}
				$arr_update['driving_license'] = $driving_license;
			}
			else
			{
				$arr_response['status'] = 'error';
				$arr_response['msg']    = 'Please upload valid file of drivig license.';
				$arr_response['data']   = [];
				return $arr_response;        
			}
		}
		$status = $this->UserModel->where('id',$user_id)->update($arr_update);
		if($status)
		{
			$arr_response = $this->get_profile_details($user_id);
			if(isset($arr_response['status']) && $arr_response['status'] == 'success'){
				$arr_response['msg'] = 'Profile details updated successfully.';
			}
			return $arr_response;
		}
		else
		{
			$arr_response['status'] = 'error';
			$arr_response['msg']    = 'Problem Occurred, While Updating Profile.';
			$arr_response['data']   = [];
			return $arr_response;
		}
		$arr_response['status'] = 'error';
		$arr_response['msg']    = 'Problem Occurred, While Updating Profile.';
		$arr_response['data']   = [];
		return $arr_response;
	}

	private function get_profile_details($user_id)
	{

		$select_arr = array('id as user_id',
							'first_name',
							'last_name',
							'dob',
							'gender',
							'profile_image',
							'email',
							'country_code',
							'mobile_no',
							'address',
							'city_name',
							'state_name',
							'post_code',
							'country_name',
							'latitude',
							'longitude',
							'driving_license',
							'my_points',
							'via_social'
						);
		$obj_user_info = $this->UserModel
							->select($select_arr)
							->where('id',$user_id)
							->first(); 

		$arr_user_info = [];

		if($obj_user_info)
		{
			$arr_user_info = $obj_user_info->toArray();

			$arr_user_info['latitude']  = (isset($arr_user_info['latitude']) && $arr_user_info['latitude']!='') ? $arr_user_info['latitude'] :'0.0';
			$arr_user_info['longitude'] = (isset($arr_user_info['longitude']) && $arr_user_info['longitude']!='') ? $arr_user_info['longitude'] :'0.0';

			if(isset($arr_user_info['profile_image']) && !empty($arr_user_info['profile_image']))
			{
				$arr_user_info['profile_image'] = $this->user_profile_public_img_path.$arr_user_info['profile_image'];  
			}
			else
			{
				$arr_user_info['profile_image']=""; 
			}
			if(isset($arr_user_info['driving_license']) && !empty($arr_user_info['driving_license']))
			{
				$arr_user_info['driving_license'] = $this->driving_license_public_path.$arr_user_info['driving_license'];  
			}
			else
			{
				$arr_user_info['driving_license']=""; 
			}
			
			$arr_response['status'] = 'success';
			$arr_response['msg']    = 'Details Found Successfully.';
			$arr_response['data']   = $arr_user_info;
			return $arr_response;
		}
		else
		{
			$arr_response['status'] = 'error';
			$arr_response['msg']    = 'No data available.';
			$arr_response['data']   = [];
			return $arr_response;
		}                  
		$arr_response['status'] = 'error';
		$arr_response['msg']    = 'No data available.';
		$arr_response['data']   = [];
		return $arr_response;
	}

	public function verify_mobile_number($request)
	{
		$arr_response = [];
		$user_id     = validate_user_jwt_token();
		if ($user_id == 0) 
		{
			$arr_response['status'] = 'error';
			$arr_response['msg']    = 'Invalid user token';
			return $arr_response;        
		}

		$mobile_no = $request->input('mobile_no');
		$user_type = $request->input('user_type');

		$is_mobile_no_duplicate = $this->UserModel
									->where('mobile_no',trim($mobile_no))
									//->where('user_type',$user_type)
									->where('id','!=',$user_id)
									->count();
		
		if($is_mobile_no_duplicate>0)
		{
			$arr_response['status'] = 'error';
			$arr_response['msg']    = 'This mobile number already exists.';
			return $arr_response;        
		}

		$obj_user_existance = $this->UserModel
											->where("id",$user_id)
											->first();
											
		if(isset($obj_user_existance->mobile_no) && $obj_user_existance->mobile_no == $mobile_no){
			
			$arr_response['status'] = 'error';
			$arr_response['msg']    = 'Mobile number should not be same as old mobile number.';
			return $arr_response;        
		}

		$otp = $this->generate_otp();

		$status = $this->UserModel
								->where('id',$user_id)
								->update(['otp'=>$otp]);

		if($status)
		{
			$country_code = isset($obj_user_existance->country_code) ? $obj_user_existance->country_code :'';
			
			$to_user_mobile = $country_code.''.$mobile_no;
			$message = 'Your OTP is '.$otp.' Thanks.'.config('app.project.name');
			
			$arr_twilio_data = [
									'mobile_no' => $to_user_mobile,
									'message' 	=> $message
							   ];

			$this->get_sms_to_user($arr_twilio_data);

			$arr_response['status'] = 'success';
			$arr_response['msg']    = 'OTP is sent your mobile number for further process.';
			return $arr_response;
		}
		else
		{
			$arr_response['status'] = 'error';
			$arr_response['msg']    = 'Something went wrong,Please try again.';
			return $arr_response;
		}
		$arr_response['status'] = 'error';
		$arr_response['msg']    = 'Something went wrong,Please try again.';
		return $arr_response;
	}

	public function update_mobile_no($request)
	{
		$arr_response = [];
		$user_id     = validate_user_jwt_token();
		if ($user_id == 0) 
		{
			$arr_response['status'] = 'error';
			$arr_response['msg']    = 'Invalid user token';
			return $arr_response;        
		}
		$country_code = $request->input('country_code');
		$mobile_no    = $request->input('mobile_no');
		$otp 		  = $request->input('otp');
		$user_type 	  = $request->input('user_type');
		
		if( $otp == "" )
		{
			$arr_response['status'] = 'error';
			$arr_response['msg']    = 'OTP field is required';
			return $arr_response;
		}

		if( $mobile_no == "" )
		{
			$arr_response['status'] = 'error';
			$arr_response['msg']    = 'Mobile Number field is required';
			return $arr_response;
		}

		$is_mobile_no_duplicate = $this->UserModel
									->where('mobile_no',trim($mobile_no))
									//->where('user_type',$user_type)
									->where('id','!=',$user_id)
									->count();
		
		if($is_mobile_no_duplicate>0)
		{
			$arr_response['status'] = 'error';
			$arr_response['msg']    = 'This mobile number already exists.';
			return $arr_response;
		}

		$obj_user_existance = $this->UserModel
											->where("id",$user_id)
											->first();
		
		if($obj_user_existance)
		{
			if(isset($obj_user_existance->otp)){
				if($obj_user_existance->otp == $otp){
					$obj_user_existance->otp = '';
					// $obj_user_existance->country_code = $country_code;
					$obj_user_existance->mobile_no = $mobile_no;
							                                                                                                                      
					$status = $obj_user_existance->save();
					if($status){
						$arr_response['status'] = 'success';
						$arr_response['msg']    = 'Mobile Number Updated successfully.';
						return $arr_response;
					} else {
						$arr_response['status'] = 'error';
						$arr_response['msg']    = 'Problem occurred, while updating mobile number.';
						return $arr_response;
					}				


				}else{
					$arr_response['status'] = 'error';
					$arr_response['msg']    = 'Please enter valid OTP.';
					return $arr_response;        
				}

			}
			else{
				$arr_response['status'] = 'error';
				$arr_response['msg']    = 'Something went wrong.';
				return $arr_response;        
			}                    
		}
		$arr_response['status'] = 'error';
		$arr_response['msg']    = 'Invalid User.';
		return $arr_response;
	}

	public function get_notification($request)
	{
		$arr_response = [];
		$user_id     = validate_user_jwt_token();
		if ($user_id == 0) 
		{
			$arr_response['status'] = 'error';
			$arr_response['msg']    = 'Invalid user token';
			$arr_response['data']   = [];
			return $arr_response;        
		}

		$per_page = 10;
		
		$user_type  = $request->input('user_type');
		if($user_type == "" || ( $user_type != 'DRIVER' && $user_type != 'USER') )
		{
			$arr_response['status'] = 'error';
			$arr_response['msg']    = 'User type is required';
			$arr_response['data']    = [];
			return $arr_response;        
		}
		
		$arr_notifications = [];

		$obj_notifications = $this->NotificationsModel
									->select('id','notification_type','title','description','created_at')
									->where('user_id',$user_id)
									->where('is_read',0);
		
		if($user_type=="USER")
		{
			$obj_notifications = $obj_notifications->where('user_type','RIDER');
		}

		if($user_type=="DRIVER")
		{
			$obj_notifications = $obj_notifications->where('user_type','DRIVER');
		}

		$obj_notifications = $obj_notifications
								->paginate($per_page); 	//->get();
		$arr_notifications = [];

		if($obj_notifications)
		{
			$arr_notifications = $obj_notifications->toArray();
		}
		
		if(isset($arr_notifications['data']) && sizeof($arr_notifications['data'])>0){
			
			foreach ($arr_notifications['data'] as $key => $notifications) {
				$arr_notifications['data'][$key]['created_at'] = dateDifference($notifications['created_at'],date('Y-m-d H:i:s'));
			}
			
			$arr_response['status'] = 'success';
			$arr_response['msg']    = 'Notifications details found.';
			$arr_response['data']    = $arr_notifications;
			return $arr_response;  

		}else{
			$arr_response['status'] = 'error';
			$arr_response['msg']    = 'Notifications details not found';
			$arr_response['data']    = [];
			return $arr_response;  
		}

		$arr_response['status'] = 'error';
		$arr_response['msg']    = 'Notifications details not found';
		$arr_response['data']    = [];
		return $arr_response;  
	}
	
	public function get_review($request)
	{
		$arr_response = [];
		$user_id     = validate_user_jwt_token();
		
		if ($user_id == 0) 
		{
			$arr_response['status'] = 'error';
			$arr_response['msg']    = 'Invalid user token';
			$arr_response['data']   = [];
			return $arr_response;        
		}

		$arr_review = $this->ReviewService->api_get_review($user_id,$request);
		
		$arr_final_data                  = [];
		$arr_final_data["total"]         = $arr_review["total"];
		$arr_final_data["per_page"]      = $arr_review["per_page"];
		$arr_final_data["current_page"]  = $arr_review["current_page"];
		$arr_final_data["last_page"]     = $arr_review["last_page"];
		$arr_final_data["next_page_url"] = $arr_review["next_page_url"];
		$arr_final_data["prev_page_url"] = $arr_review["prev_page_url"];       
		
		$arr_final = [];    
		if(isset($arr_review["data"]) && count($arr_review["data"])>0)
		{
			foreach($arr_review["data"] as $review)
			{   
				$arr_data = [];

				if(count($arr_review)>0)
				{
					$arr_data['rating_id'] = $review['id'];					
					$first_name             = isset($review['from_user_details']['first_name']) ? $review['from_user_details']['first_name'] :"" ;
					$last_name              = isset($review['from_user_details']['last_name'])  ? $review['from_user_details']['last_name']  :"" ;
					$arr_data['name']       = $first_name." ".$last_name;

					$arr_data['profile_image'] = isset($review['from_user_details']['profile_image']) ? $this->user_profile_public_img_path.$review['from_user_details']['profile_image'] : '';

					$arr_data['rating']     = number_format($review['rating'],2);
					$arr_data['tag_name']   = isset($review['rating_tag_details']['tag_name'])  ? $review['rating_tag_details']['tag_name']  :"" ;
					$arr_data['tag_img']    = isset($review['rating_tag_details']['review_image']) ? $this->review_tag_public_path.$review['rating_tag_details']['review_image']  :"" ;

					$arr_data['rating_msg'] = $review['rating_msg'];
					$arr_data['rating_date'] = date('M d, Y',strtotime($review['created_at']));
				}
				array_push($arr_final, $arr_data);
			}
		}
		$arr_final_data['data'] = $arr_final;
		if(sizeof($arr_final_data["data"])>0){
			$arr_response['status'] = 'success';
			$arr_response['msg']    = 'Notifications details found.';
			$arr_response['data']   = $arr_final_data;
			return $arr_response;  
		}
		else{
			$arr_response['status'] = 'error';
			$arr_response['msg']    = 'Notifications details not found.';
			$arr_response['data']   = [];
			return $arr_response;  
		}
		$arr_response['status'] = 'error';
		$arr_response['msg']    = 'Notifications details not found.';
		$arr_response['data']   = [];
		return $arr_response;  
	}

	public function get_card_details($request)
	{
		$arr_response = [];
		$user_id     = validate_user_jwt_token();
		if ($user_id == 0) 
		{
			$arr_response['status'] = 'error';
			$arr_response['msg']    = 'Invalid user token';
			$arr_response['data']   = [];
			return $arr_response;        
		}
		$arr_card_details = [];

		$obj_card_details = $this->CardDetailsModel
										->select('id','card_id','masked_card_number','unique_number_identifier','brand')
										->where('user_id',$user_id)
										->get();
		if($obj_card_details)
		{
			$arr_card_details = $obj_card_details->toArray();
		}
		if(isset($arr_card_details) && sizeof($arr_card_details)>0)
		{
			$arr_response['status'] = 'success';
			$arr_response['msg']    = 'card details found successfully.';
			$arr_response['data']   = $arr_card_details;
			return $arr_response;        
		}
		$arr_response['status'] = 'error';
		$arr_response['msg']    = 'Card details not found.';
		$arr_response['data']   = [];
		return $arr_response;        
	}

	public function get_bonus_points($request)
	{
		$arr_response = [];
		$user_id     = validate_user_jwt_token();
		if ($user_id == 0) 
		{
			$arr_response['status'] = 'error';
			$arr_response['msg']    = 'Invalid user token';
			$arr_response['data']   = [];
			return $arr_response;        
		}

		$arr_user_details = [];
		$obj_user_details = $this->UserModel
										->select('id','my_points','referral_code')
										->where('id',$user_id)
										->first();
		if($obj_user_details)
		{
			$arr_user_details = $obj_user_details->toArray();
		}
		if(isset($arr_user_details) && sizeof($arr_user_details)>0)
		{
			$arr_response['status'] = 'success';
			$arr_response['msg']    = 'Bonus Point found successfully.';
			$arr_response['data']   = $arr_user_details;
			return $arr_response;        
		}
		$arr_response['status'] = 'error';
		$arr_response['msg']    = 'Bonus Point not found.';
		$arr_response['data']   = [];
		return $arr_response;        
	}
	

	public function generate_otp()
	{
		$str1 = "0123456789";
		$str2 = str_shuffle($str1);
		return substr($str2,0,4); 
	}

	

	public function generate_referral_code()
	{
		$available_referrals = array(); $random_string = '';

		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$random_string_length = 10;

		$user_codes = $this->UserModel->select('referral_code')->get();
		$user_codes_arr = $user_codes->toArray();
		
		foreach ($user_codes_arr as $key => $ref_value)
		{
			if($ref_value['referral_code'] != "")
				$available_referrals[] = $ref_value['referral_code'];
		}

		for ($i = 0; $i < $random_string_length; $i++) {
			$random_string .= $characters[rand(0, strlen($characters) - 1)];
		}
		$shuffled_string = str_shuffle($random_string);

		if (is_array($available_referrals) && in_array($shuffled_string, $available_referrals))
		{
			return $this->generate_referral_code();
		}
		else{
			return $shuffled_string;
		} 
	}

	public function validate_referral_code($referral_code = "")
	{
		$referral_code_id = 0;	
		if($referral_code != "")
		{
			$obj_referral = $this->UserModel
									->select('id', 'referral_code')
									->where("referral_code", $referral_code)
									->first();
			if (isset($obj_referral->id)) {
				$referral_code_id = isset($obj_referral->id) ? $obj_referral->id :0;
			}
		}
		return $referral_code_id;
	}

	public function get_review_details($request)
	{
		$review_id = $request->input('review_id');

		if($review_id=="")
		{
			return 
			[
				'status' => 'error',
				'msg'    => 'Review Id is required.',
				'data'   => []
			];    
		}

		$arr_review   = $this->ReviewService->get_review_details($review_id);

		$arr_data =[];
		
		if(count($arr_review)>0)
		{
			if(isset($arr_review['from_user_details']['first_name']))
			{
				$first_name = $arr_review['from_user_details']['first_name'];
			}
			else
			{
				$first_name = "";   
			}
			if(isset($review['from_user_details']['last_name']))
			{
				$last_name = $arr_review['from_user_details']['last_name']; 
			}
			else
			{
				$last_name = "";
			}

			if(isset($arr_review['from_user_details']['profile_image']))
			{
				$profile_image = $this->user_profile_public_img_path.$arr_review['from_user_details']['profile_image'];
			}
			else
			{
				$profile_image = "";   
			}

			$arr_data['name']       = $first_name." ".$last_name;
			$arr_data['profile_image']  = $profile_image;

			$arr_data['rating']     = $arr_review['rating'];
			$arr_data['rating_msg'] = $arr_review['rating_msg'];
			
			$arr_data['tag_name']   = isset($arr_review['rating_tag_details']['tag_name'])  ? $arr_review['rating_tag_details']['tag_name']  :"" ;
			$arr_data['tag_img']    = isset($arr_review['rating_tag_details']['review_image']) ? $this->review_tag_public_path.$arr_review['rating_tag_details']['review_image']  :"" ;
			$arr_data['rating_date'] = date('M d, Y',strtotime($arr_review['created_at']));
		}
		
		return 
		[
			'status' => 'success',
			'msg'    => 'Reviews.',
			'data'   => $arr_data
		];        
	}

	private function built_mail_data($arr_data)
	{
		if(isset($arr_data) && sizeof($arr_data)>0)
		{
			$arr_built_content = [
			  'FIRST_NAME'       => $arr_data['first_name'],
			  'LAST_NAME'        => $arr_data['last_name'],
			  'EMAIL'            => $arr_data['email'],
			  'PASSWORD'         => $arr_data['password'],
			  'OTP'              => $arr_data['otp'],
			  'PROJECT_NAME'     => config('app.project.name')];


			  $arr_mail_data                      = [];
			  if($arr_data['user_type']=="USER")
			  {
				$arr_mail_data['email_template_id'] = '10';
			}
			else
			{
				$arr_mail_data['email_template_id'] = '11';
			}    
			$arr_mail_data['arr_built_content'] = $arr_built_content;
			$arr_mail_data['user']              = $arr_data;

			return $arr_mail_data;
		}
		return FALSE;
	}

	private function built_custom_template_mail_data($arr_data)
	{
		if(isset($arr_data) && sizeof($arr_data)>0)
		{
			$arr_built_content = [
			  'FIRST_NAME'       => $arr_data['first_name'],
			  'LAST_NAME'        => $arr_data['last_name'],
			  'EMAIL'            => $arr_data['email'],
			  'PASSWORD'         => $arr_data['password'],
			  'OTP'              => $arr_data['otp'],
			  'PROJECT_NAME'     => config('app.project.name')
			];

			$arr_mail_data                      = [];
			
			$arr_mail_data['email_from']       = 'info@quick-pick.com';
			if($arr_data['user_type']=="USER")
			{
				$arr_mail_data['template_subject']    = 'Customer Registration';
				$arr_mail_data['email_template_name'] = 'customer-emailer';
			}
			else
			{
				$arr_mail_data['template_subject']    = 'Driver Registration';
				$arr_mail_data['email_template_name'] = 'driver-emailer';
			}    
			$arr_mail_data['arr_built_content'] = $arr_built_content;
			$arr_mail_data['user']              = $arr_data;
			return $arr_mail_data;
		}
		return FALSE;
	}
	
	public function forget_pwd_mail_data($arr_data)
	{
		if(isset($arr_data) && sizeof($arr_data)>0)
		{
			$arr_built_content = [
				'FIRST_NAME'       => $arr_data['first_name'],
				'EMAIL'            => $arr_data['email'],
				'OTP'              => $arr_data['otp']
			];

			$arr_mail_data                      = [];
			$arr_mail_data['email_template_id'] = '14';
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
			$full_name  = ($full_name!=' ') ? $full_name : '';
			
			if($arr_data['user_type'] == 'USER')
			{
				$user_type = "User";
			}else
			{
				$user_type = "Driver";
			}
			$arr_notification['user_id']           = $this->CommonDataService->get_admin_id();
			$arr_notification['is_read']           = 0;
			$arr_notification['is_show']           = 0;
			$arr_notification['user_type']         = 'ADMIN';
			$arr_notification['notification_type'] = $user_type.' Registration';
			$arr_notification['title']             = $full_name." register as ".$user_type." on Quickpick";
			
			if($arr_data['user_type'] == 'USER')
			{
				$arr_notification['view_url']          = '/'.config('app.project.admin_panel_slug')."/users";
			}else
			{
				$arr_notification['view_url']          = '/'.config('app.project.admin_panel_slug')."/driver";    
			}
		}
		return $arr_notification;
	}

	public function build_otp_email($arr_data)
	{
		if(isset($arr_data) && sizeof($arr_data)>0)
		{
			$arr_built_content = [
				'NAME'       => $arr_data['user']['first_name'].' '.$arr_data['user']['last_name'],
				'OTP'              => $arr_data['OTP'],
				'PROJECT_NAME'		=> config('app.project.name')
			];

			$arr_mail_data                      = [];
			$arr_mail_data['email_template_id'] = '3';
			$arr_mail_data['arr_built_content'] = $arr_built_content;
			$arr_mail_data['user']              = $arr_data['user'];

			return $arr_mail_data;
		}
		return FALSE;
	}

	private function get_sms_to_user($arr_data)
	{
		$mobile_no = isset($arr_data['mobile_no']) ? $arr_data['mobile_no'] :'';
		$tmp_message   = isset($arr_data['message']) ? $arr_data['message'] :'';
		
		if($mobile_no!='' && $tmp_message!='')
		{
			try
			{
				$client = new \Twilio\Rest\Client($this->twilio_sid, $this->twilio_token);
				
				$message = $client->messages->create(
						  $mobile_no,
						  array(
						    'from' => $this->from_user_mobile,
						    'body' => $tmp_message,
						  )
						);

				if(isset($message->sid))
				{
					return true;
				}

			}
			catch (\Exception $e) 
			{
				return false;
			}
		}
		return false;
	}

}
?>