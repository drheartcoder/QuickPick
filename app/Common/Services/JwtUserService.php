<?php 

namespace App\Common\Services;

use Illuminate\Http\Request;
use App\Models\UserModel;
use Tymon\JWTAuth\Exceptions\JWTException;

use App\Models\SiteSettingModel;

use App\Common\Services\CommonDataService;

use JWTAuth;

class JwtUserService
{
	public function __construct(UserModel $user,SiteSettingModel $site_setting,CommonDataService $common_data_service)
	{
		$this->UserModel = $user;
		$this->CommonDataService =$common_data_service;
		$this->SiteSettingModel = $site_setting;
		$this->user_profile_public_img_path = url('/').config('app.project.img_path.user_profile_images');
		$this->user_profile_base_img_path  = base_path().config('app.project.img_path.user_profile_images');
	}

	public function generate_user_jwt_token($user_id=null)
	{
		if (isset($user_id) && $user_id!=null) 
		{
			$arr_user     = array();
			$jwt_response = array();

			$obj_data     = $this->UserModel
									->with('roles','driver_vehicle_details','driver_status_details')
									->where('id',$user_id)
									->first();

			if($obj_data)
			{
				$arr_user           = $obj_data->toArray();

				$arr_data = [];

				$arr_data['user_id']       = isset($arr_user['id']) ? $arr_user['id'] : "";
				$arr_data['email']         = isset($arr_user['email']) ? $arr_user['email'] : "";
				$arr_data['first_name']    = isset($arr_user['first_name']) ? $arr_user['first_name'] : "";
				$arr_data['last_name']     = isset($arr_user['last_name']) ? $arr_user['last_name'] : "";
				$arr_data['country_code']  = isset($arr_user['country_code']) ? $arr_user['country_code'] : "";
				$arr_data['mobile_no']     = isset($arr_user['mobile_no']) ? $arr_user['mobile_no'] : "";

				if(isset($arr_user['profile_image']) && !empty($arr_user['profile_image']))
				{
					$arr_data['profile_image'] = $this->user_profile_public_img_path.$arr_user['profile_image'];   
				}
				else
				{
					$arr_data['profile_image'] = "";
				}
				
				$arr_data['gender']                   = isset($arr_user['gender']) ? $arr_user['gender'] : "";
				$arr_data['dob']                      = isset($arr_user['dob']) ? date('Y-m-d',strtotime($arr_user['dob'])) : "";
				$arr_data['address']                  = isset($arr_user['address']) ? $arr_user['address'] : "";
				$arr_data['city_name']                = isset($arr_user['city_name']) ? $arr_user['city_name'] : "";
				$arr_data['latitude']                 = isset($arr_user['latitude']) ? $arr_user['latitude'] : "";
				$arr_data['longitude']                = isset($arr_user['longitude']) ? $arr_user['longitude'] : "";
				$arr_data['availability_status']      = isset($arr_user['availability_status']) ? $arr_user['availability_status'] : "";
				$arr_data['reset_password_mandatory'] = isset($arr_user['reset_password_mandatory']) ? $arr_user['reset_password_mandatory'] : "0";
				$arr_data['device_id']                = isset($arr_user['device_id']) ? $arr_user['device_id'] : "";
				$arr_data['is_user_login']            = isset($arr_user['is_user_login']) ? $arr_user['is_user_login'] : "";
				$arr_data['via_social']               = isset($arr_user['via_social']) ? $arr_user['via_social'] : "0";
				$arr_data['referral_code']            = isset($arr_user['referral_code']) ? $arr_user['referral_code'] : "";
				
				$user_type = '';
				
				if(isset($arr_user['roles'][0]['slug']) && !empty($arr_user['roles'][0]['slug']))
				{
					if($arr_user['roles'][0]['slug'] == 'driver')
					{
						$user_type = 'DRIVER';
					}
					else if($arr_user['roles'][0]['slug'] == 'user')
					{
						$user_type = 'USER';
					}
				}
					
				$arr_data['user_type'] = $user_type;

				$is_company_driver = isset($arr_user['is_company_driver']) ? $arr_user['is_company_driver'] : "0";

				if($is_company_driver == '1'){
					$arr_data['admin_id'] = isset($arr_user['company_id']) ? $arr_user['company_id'] : "0";
				}
				if($is_company_driver == '0'){
					$arr_data['admin_id'] = 1;
				}
				
				$obj_site_setting = $this->SiteSettingModel->select('emergency_contact')->first();
				$arr_data['emergency_contact'] = isset($obj_site_setting->emergency_contact) ? $obj_site_setting->emergency_contact : '';				

				if($user_type == 'DRIVER')
				{
					$arr_data['vehicle_type_id']       = isset($arr_user['driver_vehicle_details']['vehicle_details']['vehicle_type_id']) ? $arr_user['driver_vehicle_details']['vehicle_details']['vehicle_type_id'] : 0;
					$arr_data['vehicle_id']            = isset($arr_user['driver_vehicle_details']['vehicle_id']) ? $arr_user['driver_vehicle_details']['vehicle_id'] : 0;
					$arr_data['driver_status']         = isset($arr_user['driver_status_details']['status']) ? $arr_user['driver_status_details']['status'] : "";
					
					$arr_data['is_individual_vehicle'] = isset($arr_user['driver_vehicle_details']['is_individual_vehicle']) ? strval($arr_user['driver_vehicle_details']['is_individual_vehicle']) : '1';
					
					$arr_data['is_car_assign'] 		   = isset($arr_user['driver_vehicle_details']['is_car_assign']) ? $arr_user['driver_vehicle_details']['is_car_assign'] : '0';
					$arr_data['is_verified'] 		   = isset($arr_user['driver_vehicle_details']['vehicle_details']['is_verified']) ? $arr_user['driver_vehicle_details']['vehicle_details']['is_verified'] : '0';
					
					$arr_data['starting_price']          = isset($arr_user['driver_vehicle_details']['vehicle_details']['vehicle_type_details']['starting_price']) ? number_format($arr_user['driver_vehicle_details']['vehicle_details']['vehicle_type_details']['starting_price'],2) : '0';
					$arr_data['per_miles_price']         = isset($arr_user['driver_vehicle_details']['vehicle_details']['vehicle_type_details']['per_miles_price']) ? number_format($arr_user['driver_vehicle_details']['vehicle_details']['vehicle_type_details']['per_miles_price'],2) : '0';
					$arr_data['per_minute_price']        = isset($arr_user['driver_vehicle_details']['vehicle_details']['vehicle_type_details']['per_minute_price']) ? number_format($arr_user['driver_vehicle_details']['vehicle_details']['vehicle_type_details']['per_minute_price'],2) : '0';
					$arr_data['minimum_price']           = isset($arr_user['driver_vehicle_details']['vehicle_details']['vehicle_type_details']['minimum_price']) ? number_format($arr_user['driver_vehicle_details']['vehicle_details']['vehicle_type_details']['minimum_price'],2) : '0';
					$arr_data['cancellation_base_price'] = isset($arr_user['driver_vehicle_details']['vehicle_details']['vehicle_type_details']['cancellation_base_price']) ? number_format($arr_user['driver_vehicle_details']['vehicle_details']['vehicle_type_details']['cancellation_base_price'],2) : '0';
				
					$user_encoded_id = isset($arr_user['id']) ? $arr_user['id'] : "0";
					$arr_data['user_encoded_id']   = $this->CommonDataService->encrypt_value($user_encoded_id);

					$arr_data['stripe_account_id'] = isset($arr_user['stripe_account_id']) ? $arr_user['stripe_account_id'] : "";
				}
				try 
				{
					$token                      = JWTAuth::fromUser($obj_data);
					$jwt_response   			= $arr_data;
					$jwt_response['user_token'] = $token;
					return $jwt_response;
				} 
				
				catch (JWTException $e) 
				{
					return false;
				}
			}
		}
		return false;
	}
}