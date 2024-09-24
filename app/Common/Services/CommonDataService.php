<?php
namespace App\Common\Services;

use App\Models\CountryModel;
use App\Models\StateModel;
use App\Models\CityModel;
use App\Models\VehicleTypeModel;
use App\Models\PackageTypeModel;
use App\Models\VehicleModel;
use App\Models\AdminCommissionModel;
use App\Models\CompanyCommissionModel;

use App\Models\ModulesModel;
use App\Models\UserModel;

use App\Models\PromoOfferModel;

use App\Models\ReviewTagModel;
use App\Models\AdminBonusModel;
use App\Models\DriverCarRelationModel;
use App\Models\DriverFairChargeModel;
use App\Models\DriverStatusModel;
use App\Models\BookingMasterModel;
use App\Models\LoadPostRequestModel;

use App\Models\VehicleBrandModel;
use App\Models\VehicleModelModel;
use App\Models\VehicleYearModel;
use App\Models\PromotionalOfferModel;




class CommonDataService
{
    public function __construct(
                                    CountryModel $country,
                                    StateModel $state,
                                    CityModel $city,
                                    VehicleModel $vehicle,
                                    VehicleTypeModel $vehicle_type,
                                    PackageTypeModel $package_type,
                                    AdminCommissionModel  $admin_commission,
                                    CompanyCommissionModel $company_commission,
                                    UserModel $user,
                                    PromoOfferModel $promo_offer,
                                    ReviewTagModel $review_tags,
                                    AdminBonusModel $admin_bonus,
                                    DriverCarRelationModel $driver_car_relation,
                                    DriverFairChargeModel $driver_fair_charge,
                                    DriverStatusModel $driver_status,
                                    BookingMasterModel $booking_master,
                                    LoadPostRequestModel $load_post_request,

                                    VehicleBrandModel $vehicle_brand,
                                    VehicleModelModel $vehicle_model,
                                    VehicleYearModel $vehicle_year,
                                    PromotionalOfferModel $promotional_offer
                                )
    {
        $this->CountryModel             = $country;
        $this->StateModel               = $state;
        $this->CityModel                = $city;
        $this->VehicleModel             = $vehicle;
        $this->PackageTypeModel         = $package_type;
        $this->VehicleTypeModel         = $vehicle_type;
        $this->AdminCommissionModel     = $admin_commission;
        $this->CompanyCommissionModel   = $company_commission;
        $this->UserModel                = $user;
        $this->PromoOfferModel          = $promo_offer;
        $this->ReviewTagModel           = $review_tags;
        $this->AdminBonusModel          = $admin_bonus;
        $this->DriverCarRelationModel   = $driver_car_relation;
        $this->DriverFairChargeModel    = $driver_fair_charge;
        $this->DriverStatusModel        = $driver_status;
        $this->BookingMasterModel       = $booking_master;
        $this->LoadPostRequestModel     = $load_post_request;

        $this->VehicleBrandModel     = $vehicle_brand;
        $this->VehicleModelModel     = $vehicle_model;
        $this->VehicleYearModel      = $vehicle_year;
        $this->PromotionalOfferModel = $promotional_offer;

        $this->review_tag_public_path       = url('/').config('app.project.img_path.review_tag');
        $this->review_tag_base_path         = base_path().config('app.project.img_path.review_tag');

        $this->banner_image_public_img_path = url('/').config('app.project.img_path.banner_image');
        $this->banner_image_base_img_path   = base_path().config('app.project.img_path.banner_image');

    }

    public function decrypt_value($value)
    {
        $decrypted = decrypt($value);
        return $decrypted;
    }
    public function encrypt_value($value)
    {
        $encrypted = encrypt($value);
        return $encrypted;
    }
    public function change_driver_status($driver_id,$status)
    {
        if($driver_id!='' && $status!=''){
            $this->DriverStatusModel
                            ->where('driver_id',$driver_id)
                            ->update(['status'=>$status]);
            return true;
        }
        return true;
    }

    public function get_promotional_offers($request)
    {
        $arr_promotional_offer = [];
        $obj_promotional_offer = $this->PromotionalOfferModel
                                                    ->select('id','banner_title','banner_image','is_active')
                                                    ->where('is_active','1')
                                                    ->paginate(10);
        if($obj_promotional_offer){
            $arr_promotional_offer = $obj_promotional_offer->toArray();
        }
        if(isset($arr_promotional_offer['data']) && sizeof($arr_promotional_offer['data'])>0)
        {
            foreach ($arr_promotional_offer['data'] as $key => $value) 
            {
                $banner_image = '';
                if(isset($value['banner_image']) && $value['banner_image']!=''){
                    if(file_exists($this->banner_image_base_img_path.$value['banner_image'])){
                        $banner_image = $this->banner_image_public_img_path.$value['banner_image'];
                    }
                }
                $arr_promotional_offer['data'][$key]['banner_image'] = $banner_image;
            }
        }
        return $arr_promotional_offer;
    }

    public function check_bonus_applicable($id)
    {
        $is_bonus_applicable = 'NO';

        $obj_user = $this->UserModel
                                ->select('id','my_points')
                                ->where('id',$id)
                                ->first();

        if($obj_user)
        {
            if(isset($obj_user->my_points) && $obj_user->my_points>0)
            {
                $is_bonus_applicable = 'YES';
            }
        }
        return $is_bonus_applicable;

    }

    public function get_user_details($id)
    {
        $arr_driver = [];

        $obj_driver = $this->UserModel
                        // ->select('id','first_name','last_name','email','company_name')
                        ->where('id',$id)
                        ->first();
        if(isset($obj_driver))
        {
            $arr_driver = $obj_driver->toArray();
        }
        return $arr_driver;

    }

    public function get_driver_details($id)
    {
        $arr_driver = [];

        $obj_driver = $this->UserModel
                        ->select('id','first_name','last_name','email','country_code','mobile_no')
                        ->with('driver_vehicle_details','driver_status_details')
                        ->where('id',$id)
                        ->first();
        if(isset($obj_driver))
        {
            $arr_driver = $obj_driver->toArray();
        }
        return $arr_driver;
    }

    public function get_driver_current_lat_lng_details($id)
    {
        $arr_driver = [];

        $obj_driver = $this->DriverStatusModel
                        ->select('id','driver_id','current_latitude','current_longitude')
                        ->where('driver_id',$id)
                        ->first();
        if(isset($obj_driver))
        {
            $arr_driver = $obj_driver->toArray();
        }
        return $arr_driver;
    }

    /*function will return array of is_individual_vehicle and admin km charge and driver km charge*/
    public function check_is_individual_vehicle_from_driver_car_relation($driver_id)
    {
        $obj_driver_car_relation = $this->DriverCarRelationModel
                                        ->where('driver_id',$driver_id)
                                        ->first();

        $is_individual_vehicle = isset($obj_driver_car_relation->is_individual_vehicle) ? $obj_driver_car_relation->is_individual_vehicle : '0';
        
        return $is_individual_vehicle;

    }

    /*function will return array of is_individual_vehicle and admin km charge and driver km charge*/
    public function check_is_individual_vehicle($vehicle_id)
    {
        $arr_result['company_id']            = 0;
        $arr_result['is_individual_vehicle'] = '0';
        $arr_result['is_company_vehicle']    = '0';

        $obj_vehicle = $this->VehicleModel
                                        ->where('id',$vehicle_id)
                                        ->first();
        if(isset($obj_vehicle))
        {
            $arr_result['company_id']            = isset($obj_vehicle->company_id) ? $obj_vehicle->company_id :0;
            $arr_result['is_individual_vehicle'] = isset($obj_vehicle->is_individual_vehicle) ? $obj_vehicle->is_individual_vehicle :'0';
            $arr_result['is_company_vehicle']    = isset($obj_vehicle->is_company_vehicle) ? $obj_vehicle->is_company_vehicle :'0';
        }
        return $arr_result;
    }
    
    public function get_driver_vehicle_id($id)
    {
        $driver_vehicle_id = 0;

        $obj_driver = $this->DriverCarRelationModel
                                        ->where('driver_id',$id)
                                        ->first();
        if(isset($obj_driver))
        {
            $driver_vehicle_id = isset($obj_driver->vehicle_id) ? $obj_driver->vehicle_id :0;
        }
        return $driver_vehicle_id;

    }

    public function get_driver_vehicle_type_details($id)
    {
        $arr_data = [];
        $obj_driver = $this->DriverCarRelationModel
                                        ->with(['vehicle_details'=>function($query){
                                            // $query->select('id','vehicle_type_id');
                                            $query->with(['vehicle_type_details'=>function($query){
                                            // $query->select('id','vehicle_type','vehicle_min_weight','vehicle_max_weight','vehicle_min_volume','vehicle_max_volume');
                                            }]);
                                        },'driver_details'=>function($query){
                                            $query->select('id','mobile_no');
                                        }])
                                        ->where('driver_id',$id)
                                        ->first();
        
        if(isset($obj_driver))
        {
            $arr_data = $obj_driver->toArray();
        }
        return $arr_data;

    }

    public function get_driver_fair_charge($id)
    {
        $is_individual_vehicle = '0';
        $driver_fair_charge = 0;

        $obj_driver_car_relation = $this->DriverCarRelationModel->with('vehicle_details')->where('driver_id',$id)->first();
        if($obj_driver_car_relation){
           $driver_fair_charge = isset($obj_driver_car_relation->vehicle_details->vehicle_type_details->per_miles_price) ? floatval($obj_driver_car_relation->vehicle_details->vehicle_type_details->per_miles_price) :0;
        }
        
        return $driver_fair_charge;


        /*if($is_individual_vehicle == '0')
        {
            $vehicle_id = isset($obj_driver_car_relation->vehicle_id) ? $obj_driver_car_relation->vehicle_id :'0';
            
            $obj_vehicle = $this->VehicleModel->where('id',$vehicle_id)->first();
            if($obj_vehicle){
                $driver_fair_charge = isset($obj_vehicle->admin_per_kilometer_charge) ? $obj_vehicle->admin_per_kilometer_charge :0;
            }

        }
        else if($is_individual_vehicle == '1')
        {
            $obj_driver_fair_charge = $this->DriverFairChargeModel
                                            ->where('driver_id',$id)
                                            ->first();

            if(isset($obj_driver_fair_charge))
            {
                $driver_fair_charge = isset($obj_driver_fair_charge->fair_charge) ? doubleval($obj_driver_fair_charge->fair_charge) :0;
            }
        }
        return $driver_fair_charge;*/

    }

    public function get_company_details($company_id)
    {
        $arr_driver = [];

        $obj_driver = $this->UserModel
                        ->select('id','company_name','email')
                        ->where('id',$company_id)
                        ->first();
        if(isset($obj_driver))
        {
            $arr_driver = $obj_driver->toArray();
        }
        return $arr_driver;
    }

    public function get_promo_code_details($promo_code_id)
    {
        $arr_promo_offer = [];
        $obj_promo_offer = $this->PromoOfferModel
                                        ->where('id',$promo_code_id)
                                        ->first();
        if($obj_promo_offer!=FALSE)
        {
            $arr_promo_offer = $obj_promo_offer->toArray();
        }
        return $arr_promo_offer;
    }
    public function get_admin_id()
    {
        $admin_id = 0;

        $obj_admin = $this->UserModel
                        ->select('id')
                        ->whereHas('roles',function($query){
                            $query->where('slug','admin');
                        })
                        ->first();
        if(isset($obj_admin->id) && $obj_admin->id!=0)
        {
            $admin_id = $obj_admin->id;
        }
        return $admin_id;
    }

    public function get_countries($country_id = false)
    {
        $arr_countries = array();

        $obj_countries = $this->CountryModel
                                ->select('id','phone_code','country_name');
        if($country_id!=false && $country_id != '')
        {
            $obj_countries = $obj_countries->where('id',$country_id);
        }
        $obj_countries = $obj_countries->get();

        if($obj_countries != FALSE)
        {
            $arr_countries =  $obj_countries->toArray();
        }
        return $arr_countries;
    }

    public function get_states($country_id)
    {
        $arr_states = array();

        $obj_states = $this->StateModel
                                ->where('country_id',$country_id)
                                ->select('id','state_name')
                                ->get();

        if($obj_states != FALSE)
        {
            $arr_states =  $obj_states->toArray();
        }
        return $arr_states;
    }
    
    public function get_cities($state_id)
    {
        $arr_cities = array();

        $obj_cities = $this->CityModel
                                ->where('state_id',$state_id)
                                ->select('id','city_name')
                                ->get();

        if($obj_cities != FALSE)
        {
            $arr_cities =  $obj_cities->toArray();
        }
        return $arr_cities;
    }

    public function get_vehicle_details($vehicle_id)
    {
        $arr_vehicles = array();

        $obj_vehicles = $this->VehicleModel
                                ->where('id',$vehicle_id)
                                // ->select('id','vehicle_name','vehicle_model_name','vehicle_number')
                                ->get();

        if($obj_vehicles != FALSE)
        {
            $arr_vehicles =  $obj_vehicles->toArray();
        }
        return $arr_vehicles;
    }

    public function get_vehicle_types()
    {
        $arr_vehicle_type = array();
        $obj_vehicle_type = $this->VehicleTypeModel
                                    ->select('id','vehicle_type','vehicle_type_slug','is_usdot_required','is_mcdoc_required')
                                    ->where('is_active','1')
                                    ->get();

        if($obj_vehicle_type != FALSE)
        {
            $arr_vehicle_type =  $obj_vehicle_type->toArray();
        }
        
        return $arr_vehicle_type;
    }
    
    public function get_package_type()
    {
        $arr_package_type = array();
        $obj_package_type = $this->PackageTypeModel
                                    ->select('id','name','slug','is_special_type')
                                    ->where('is_active','1')
                                    ->get();

        if($obj_package_type != FALSE)
        {
            $arr_package_type =  $obj_package_type->toArray();
        }
        
        return $arr_package_type;
    }

    public function get_vehicle_brand()
    {
        $arr_vehicle_brand = array();
        $obj_vehicle_brand = $this->VehicleBrandModel
                                    ->select('id','name')
                                    ->get();

        if($obj_vehicle_brand != FALSE)
        {
            $arr_vehicle_brand =  $obj_vehicle_brand->toArray();
        }
        return $arr_vehicle_brand;
    }
    
    public function get_vehicle_model($request)
    {
        $vehicle_brand_id = $request->input('vehicle_brand_id');

        $arr_vehicle_type = array();
        $obj_vehicle_type = $this->VehicleModelModel
                                    ->select('id','vehicle_brand_id','name')
                                    ->where('vehicle_brand_id',$vehicle_brand_id)
                                    ->get();

        if($obj_vehicle_type != FALSE)
        {
            $arr_vehicle_type =  $obj_vehicle_type->toArray();
        }
        return $arr_vehicle_type;
    }
    
    public function get_web_vehicle_model($vehicle_brand_id)
    {
        $arr_vehicle_type = array();
        $obj_vehicle_type = $this->VehicleModelModel
                                    ->select('id','vehicle_brand_id','name')
                                    ->where('vehicle_brand_id',$vehicle_brand_id)
                                    ->get();

        if($obj_vehicle_type != FALSE)
        {
            $arr_vehicle_type =  $obj_vehicle_type->toArray();
        }
        return $arr_vehicle_type;
    }

    public function get_vehicle_year($request)
    {
        $vehicle_model_id = $request->input('vehicle_model_id');

        $arr_vehicle_type = array();
        $obj_vehicle_type = $this->VehicleYearModel
                                    ->select('id','vehicle_model_id','year')
                                    ->where('vehicle_model_id',$vehicle_model_id)
                                    ->get();

        if($obj_vehicle_type != FALSE)
        {
            $arr_vehicle_type =  $obj_vehicle_type->toArray();
        }
        return $arr_vehicle_type;
    }

    public function is_vehicle_available_against_load($arr_load_required_data)
    {
        $package_type     = isset($arr_load_required_data['package_type']) ? $arr_load_required_data['package_type'] : '';
        $package_quantity = isset($arr_load_required_data['package_quantity']) ? $arr_load_required_data['package_quantity'] : 0;
        $package_volume   = isset($arr_load_required_data['package_volume']) ? $arr_load_required_data['package_volume'] : 0;
        $package_weight   = isset($arr_load_required_data['package_weight']) ? $arr_load_required_data['package_weight'] : 0;

        if($package_type!='')
        {
            $sql_query = '';
            $sql_query .= "Select id,vehicle_type,vehicle_type_slug,vehicle_min_volume,vehicle_max_volume,vehicle_min_weight,vehicle_max_weight,no_of_pallet ";
            $sql_query .= "FROM ";
            $sql_query .=   "vehicle_type "; 
            $sql_query .= "WHERE ";
            $sql_query .=  "vehicle_type.is_active = '1' AND ";
            
            if($package_type == "PALLET")
            {
                $sql_query .=  "vehicle_type.no_of_pallet >= ".$package_quantity." ";
            }
            else
            {
                $sql_query .=  "vehicle_type.vehicle_min_volume <= ".$package_volume." AND ";
                $sql_query .=  "vehicle_type.vehicle_max_volume >= ".$package_volume." AND ";
                $sql_query .=  "vehicle_type.vehicle_min_weight <= ".$package_weight." AND ";
                $sql_query .=  "vehicle_type.vehicle_max_weight >= ".$package_weight." ";
            }

            
            $obj_vehicle_details =  \DB::select($sql_query);
            if(count($obj_vehicle_details)>0)
            {
                return false;
            }
        }
        return true;
    }
    public function get_all_next_vehicle_types_against_load($arr_vehicle_types_required_data)
    {
        // $sql_query = '';
        // $sql_query .= "Select id,vehicle_type,vehicle_type_slug,vehicle_min_volume,vehicle_max_volume,vehicle_min_weight,vehicle_max_weight ";
        // $sql_query .= "FROM ";
        // $sql_query .=   "vehicle_type "; 
        // $sql_query .= "WHERE ";
        // $sql_query .=  "vehicle_type.is_active = '1' ";
        // $sql_query .=  "vehicle_type.vehicle_min_volume <= ".$package_volume." AND ";
        // $sql_query .=  "vehicle_type.vehicle_max_volume >= ".$package_volume." AND ";  
        // $sql_query .=  "vehicle_type.vehicle_min_weight <= ".$package_weight."  ";
        // $sql_query .=  "vehicle_type.vehicle_max_weight >= ".$package_weight." ";
        $package_type     = isset($arr_vehicle_types_required_data['package_type']) ? $arr_vehicle_types_required_data['package_type'] : '';
        $package_quantity = isset($arr_vehicle_types_required_data['package_quantity']) ? $arr_vehicle_types_required_data['package_quantity'] : 0;
        $package_volume   = isset($arr_vehicle_types_required_data['package_volume']) ? $arr_vehicle_types_required_data['package_volume'] : 0;
        $package_weight   = isset($arr_vehicle_types_required_data['package_weight']) ? $arr_vehicle_types_required_data['package_weight'] : 0;

        $sql_query = '';
        $sql_query .= "Select id,vehicle_type,vehicle_type_slug,per_miles_price,vehicle_min_volume,vehicle_max_volume,vehicle_min_weight,vehicle_max_weight ";
        $sql_query .= "FROM ";
        $sql_query .=   "vehicle_type "; 
        $sql_query .= "WHERE ";
        $sql_query .=  "vehicle_type.is_active = '1'  AND ";

        if($package_type == "PALLET")
        {
            $sql_query .=  "vehicle_type.no_of_pallet >= ".$package_quantity." ";
        }
        else
        {
            $sql_query .=  "vehicle_type.vehicle_max_volume >= ".$package_volume." AND ";  
            $sql_query .=  "vehicle_type.vehicle_max_weight >= ".$package_weight." ";
        }
        

        $obj_vehicle_details =  \DB::select($sql_query);
        
        if(count($obj_vehicle_details)>0)
        {
            return json_decode(json_encode($obj_vehicle_details), true);
        }
        return [];
    }

    public function get_vehicle_type_available_against_load($arr_load_required_data)
    {
        $arr_vehicle_type = [];

        $package_type     = isset($arr_load_required_data['package_type']) ? $arr_load_required_data['package_type'] : '';
        $package_quantity = isset($arr_load_required_data['package_quantity']) ? $arr_load_required_data['package_quantity'] : 0;
        $package_volume   = isset($arr_load_required_data['package_volume']) ? $arr_load_required_data['package_volume'] : 0;
        $package_weight   = isset($arr_load_required_data['package_weight']) ? $arr_load_required_data['package_weight'] : 0;

        $sql_query = '';
        $sql_query .= "Select id,vehicle_type,vehicle_type_slug,starting_price,per_miles_price,per_minute_price,minimum_price,is_active,no_of_pallet ";
        $sql_query .= "FROM ";
        $sql_query .=   "vehicle_type "; 
        $sql_query .= "WHERE ";
        $sql_query .=  "vehicle_type.is_active = '1' AND ";
        
        if($package_type == "PALLET")
        {
            $sql_query .=  "vehicle_type.no_of_pallet >= ".$package_quantity." ";
        }
        else
        {
            $sql_query .=  "vehicle_type.vehicle_min_volume <= ".$package_volume." AND ";
            $sql_query .=  "vehicle_type.vehicle_max_volume >= ".$package_volume." AND ";
            $sql_query .=  "vehicle_type.vehicle_min_weight <= ".$package_weight." AND ";
            $sql_query .=  "vehicle_type.vehicle_max_weight >= ".$package_weight." ";
        }

        // $sql_query .=  "vehicle_type.vehicle_min_volume <= ".$package_volume." AND ";
        // $sql_query .=  "vehicle_type.vehicle_max_volume >= ".$package_volume." AND ";
        // $sql_query .=  "vehicle_type.vehicle_min_weight <= ".$package_weight." AND ";
        // $sql_query .=  "vehicle_type.vehicle_max_weight >= ".$package_weight." ";
        
        $obj_vehicle_type =  \DB::select($sql_query);
        
        if(isset($obj_vehicle_type[0]) && count($obj_vehicle_type[0])>0)
        {
            $arr_vehicle_type =  json_decode(json_encode($obj_vehicle_type[0]), true);
        }
        return $arr_vehicle_type;
    }

    

    public function assign_module_permission_to_admin()
    {
        $obj_modules    =   ModulesModel::
                        where('is_active','1')
                        ->orderBy('title','ASC')
                        ->get();

        if($obj_modules != FALSE)
        {
            $arr_modules = $obj_modules->toArray();
        }
        $arr_permission = [];

        if (count($arr_modules) > 0)
        {
            foreach ($arr_modules as $module => $submodule) 
            {
                $arr_permission[$submodule['slug'].'.list'] = true;
                $arr_permission[$submodule['slug'].'.create'] = true;
                $arr_permission[$submodule['slug'].'.update'] = true;
                $arr_permission[$submodule['slug'].'.delete'] = true;
            }     
        }
        UserModel::where('id',1)->update(['permissions'=>json_encode($arr_permission)]);
    }
    
    public function get_admin_commission()
    {
        $admin_commission = array();
        $commission_data['driver_commission'] = $commission_data['company_commission'] = 1;
        $admin_commission = $this->AdminCommissionModel->first();
        if(isset($admin_commission) && sizeof($admin_commission)>0)
        {
            $commission_data['driver_commission'] = $admin_commission['driver_percentage']/100;
            $commission_data['company_commission'] = $admin_commission['company_percentage']/100;
        }
        return $commission_data;
    }

    public function get_admin_commission_percentage()
    {
        
        $admin_commission_data = array();
        
        $obj_admin_commission = $this->AdminCommissionModel->first();
        if(isset($obj_admin_commission) && sizeof($obj_admin_commission)>0)
        {
            $admin_commission_data['admin_driver_percentage']      = isset($obj_admin_commission->admin_driver_percentage) ? $obj_admin_commission->admin_driver_percentage : 0;
            $admin_commission_data['company_percentage']           = isset($obj_admin_commission->company_percentage) ? $obj_admin_commission->company_percentage : 0;
            $admin_commission_data['individual_driver_percentage'] = isset($obj_admin_commission->individual_driver_percentage) ? $obj_admin_commission->individual_driver_percentage : 0;
        }
        return $admin_commission_data;
    }

    public function get_company_commission_percentage($company_id)
    {
        $company_commission_data = array();
        
        $obj_company_commission = $this->CompanyCommissionModel->where('company_id',$company_id)->first();

        if(isset($obj_company_commission) && sizeof($obj_company_commission)>0)
        {
            $company_commission_data['company_driver_percentage']      = isset($obj_company_commission->driver_percentage) ? $obj_company_commission->driver_percentage : 0;
        }
        return $company_commission_data;
    }

    public function check_valid_promo_code($request)
    {
        $arr_result['status']  = 'error';
        $arr_result['msg']     = 'Promo Code is invalid';
        $arr_result['data']    = [];

        $promo_code   = $request->input('promo_code');
        $request_type = $request->input('request_type');
        $user_id = 0;
        if($request_type == 'API'){
            $user_id     = validate_user_jwt_token();
        }
        else if($request_type == 'WEB'){
            $enc_id = $request->input('enc_id');
            $user_id = base64_decode($enc_id);
            if($user_id!=''){
                $user_id = intval($user_id);
            }
        }
        if ($user_id == 0) 
        {
            $arr_result['status'] = 'error';
            $arr_result['msg']    = 'Invalid driver token';
            $arr_result['data']    = [];
            return $arr_result;
        }
        
        if($promo_code !='')
        {
            $arr_promo_offer = [];
            $obj_promo_offer = $this->PromoOfferModel
                                            ->select('id','code','percentage','promo_code_usage_limit')
                                            ->with(['promo_offer_applied_details'=>function($query)use($user_id){
                                                $query->where('user_id',$user_id);
                                            }])
                                            ->whereRaw('DATE(validity_from) <= ?', [date('Y-m-d')])
                                            ->whereRaw('DATE(validity_to) >= ?', [date('Y-m-d')])
                                            ->where('is_used',0)                    
                                            ->where('is_active',1)                    
                                            ->where('code',$promo_code)
                                            ->first();
            if($obj_promo_offer!=FALSE)
            {
                $arr_promo_offer = $obj_promo_offer->toArray();
            }
            if(isset($arr_promo_offer) && sizeof($arr_promo_offer)>0)
            {
                $promo_code_usage_limit = isset($arr_promo_offer['promo_code_usage_limit']) ? $arr_promo_offer['promo_code_usage_limit'] : 0;
                if(isset($arr_promo_offer['promo_offer_applied_details'])){
                    if(count($arr_promo_offer['promo_offer_applied_details']) >= $promo_code_usage_limit){
                        $arr_result['status']  = 'error';
                        $arr_result['msg']     = 'Promo Code usage limit exceed.';
                        $arr_result['data']    = [];
                        return $arr_result;
                    }
                }
                unset($arr_promo_offer['promo_offer_applied_details']);
                $arr_result['status']  = 'success';
                $arr_result['msg']     = 'Promo Code is valid.';
                $arr_result['data']    = $arr_promo_offer;                
            }

        }
        return $arr_result;
    }

    public function get_review_tags()
    {
        $arr_review_tags = array();

        $obj_review_tags = $this->ReviewTagModel
                                    ->select('id','tag_name', 'review_image')
                                    ->where('is_active','1')
                                    ->get();
        if($obj_review_tags != FALSE)
        {
            $arr_review_tags =  $obj_review_tags->toArray();
            if(isset($arr_review_tags) && sizeof($arr_review_tags)>0){
                foreach ($arr_review_tags as $key => $value) 
                {
                    $review_image = '';
                    if(isset($value['review_image']) && $value['review_image']!=''){
                        if(file_exists($this->review_tag_base_path.$value['review_image'])){
                            $review_image = $this->review_tag_public_path.$value['review_image'];       
                        }
                    }
                    $arr_review_tags[$key]['review_image'] = $review_image;
                }
            }
        }
        return $arr_review_tags;
    }

    public function admin_referral_points()
    {
        $admin_bonus = $this->AdminBonusModel->first();
        if(isset($admin_bonus) && sizeof($admin_bonus)>0)
        {
            return isset($admin_bonus['referral_points']) ? floatval($admin_bonus['referral_points']) :0;
        }else
        {
            return 0;
        }
    }

    public function get_admin_referral_points_details()
    {
        $arr_admin_referral_points_details = [];

        $obj_admin_referral_points_details = $this->AdminBonusModel
                                                        ->select('referral_points','referral_points_price')
                                                        ->first();
        
        if($obj_admin_referral_points_details)
        {
            $arr_admin_referral_points_details = $obj_admin_referral_points_details->toArray();
        }

        return $arr_admin_referral_points_details;
    }

    public function get_user_bonus_points($id)
    {
        $my_points = 0;
        $obj_user = $this->UserModel
                                ->select('id','my_points')
                                ->where('id',$id)
                                ->first();

        if($obj_user)
        {
            if(isset($obj_user->my_points) && $obj_user->my_points>0)
            {
                $my_points = isset($obj_user->my_points) ? intval($obj_user->my_points) :0;   
            }
        }
        return $my_points;
    }
    public function deduct_user_bonus_points($enc_user_id,$user_bonus_points)
    {
        $obj_user = $this->UserModel
                                ->select('id','my_points')
                                ->where('id',$enc_user_id)
                                ->first();

        if($obj_user)
        {
            if(isset($obj_user->my_points) && $obj_user->my_points>0)
            {
                $current_db_user_bonus_points = isset($obj_user->my_points) ? intval($obj_user->my_points) :0;   

                if($current_db_user_bonus_points>$user_bonus_points)
                {
                    $updated_user_bonus_points = ($current_db_user_bonus_points - $user_bonus_points);
                    $obj_user->my_points = $updated_user_bonus_points;
                }
                else
                {
                    $obj_user->my_points = 0;
                }
                $obj_user->save();
                return true;
            }
        }

        return true;

    }
    public function check_trip($request)
    {
        $from_user_id    = validate_user_jwt_token();

        if ($from_user_id == 0) 
        {
            $arr_response['status'] = 'error';
            $arr_response['msg']    = 'Invalid user token';
            return $arr_response;        
        }
        $from_user_type = $request->input('user_type');
        $to_user_id     = $request->input('to_user_id');

        $sql_query = '';
        $sql_query .= "Select ";
        $sql_query .= "users.id, ";
        $sql_query .= "CONCAT( users.first_name, ' ',users.last_name ) AS name, ";
        $sql_query .= "roles.slug as role ";

        $sql_query .= "FROM ";
        $sql_query .=   "users "; 
        $sql_query .=  "JOIN ";
        $sql_query .=   "role_users ON role_users.user_id = users.id ";
        $sql_query .=  "JOIN ";
        $sql_query .=    "roles ON roles.id = role_users.role_id ";

        $sql_query .= "WHERE ";

        $sql_query .=  "users.id = ".$to_user_id;

        $obj_data =  \DB::select($sql_query);
        
        if(isset($obj_data[0]->role) && ($obj_data[0]->role == 'admin' || $obj_data[0]->role == 'company'))
        {
            $arr_response['status'] = 'success';
            return $arr_response;
        }
        
        $to_user_type = '';

        if(isset($obj_data[0]->role) && $obj_data[0]->role == 'user')
        {
            $to_user_type = 'USER';
        }
        if(isset($obj_data[0]->role) && $obj_data[0]->role == 'driver')
        {
            $to_user_type = 'DRIVER';
        }


        if($from_user_type == 'USER' && $to_user_type == 'DRIVER')
        {
            $user_id   = $from_user_id;
            $driver_id = $to_user_id;
            $obj_booking = $this->BookingMasterModel
                                    ->whereHas('load_post_request_details',function($query)use($user_id,$driver_id){
                                        $query->where('user_id',$user_id);
                                        $query->where('driver_id',$driver_id);
                                    })
                                    ->with('load_post_request_details')
                                    ->whereIn('booking_status',['TO_BE_PICKED','IN_TRANSIT'])
                                    ->first();

            if($obj_booking)
            {
                $arr_response['status'] = 'success';
                return $arr_response;
            }
            $arr_response['status'] = 'error';
            return $arr_response;

        }

        if($from_user_type == 'DRIVER' && $to_user_type == 'USER')
        {
            $user_id   = $to_user_id;
            $driver_id = $from_user_id;
            
            $obj_booking = $this->BookingMasterModel
                                    ->whereHas('load_post_request_details',function($query)use($user_id,$driver_id){
                                        $query->where('user_id',$user_id);
                                        $query->where('driver_id',$driver_id);
                                    })
                                    ->with('load_post_request_details')
                                    ->whereIn('booking_status',['TO_BE_PICKED','IN_TRANSIT'])
                                    ->first();

            if($obj_booking)
            {
                $arr_response['status'] = 'success';
                return $arr_response;
            }
            $arr_response['status'] = 'error';
            return $arr_response;

        }

        $arr_response['status'] = 'error';
        return $arr_response;
    }
    public function get_current_trip_users($request)
    {
        $login_user_id    = validate_user_jwt_token();

        if ($login_user_id == 0) 
        {
            $arr_response['status'] = 'error';
            $arr_response['msg']    = 'Invalid user token';
            return $arr_response;        
        }
        
        $user_type = $request->input('user_type');
        
        $obj_booking = $this->BookingMasterModel
                                ->select('id','load_post_request_id','booking_status')
                                ->whereHas('load_post_request_details',function($query)use($login_user_id,$user_type){
                                    if($user_type == 'USER')
                                    {
                                        $query->where('user_id',$login_user_id);
                                    }
                                    if($user_type == 'DRIVER')
                                    {
                                        $query->where('driver_id',$login_user_id);
                                    }
                                })
                                ->with(['load_post_request_details'=>function($query){
                                    $query->select('id','user_id','driver_id');
                                }])
                                ->whereIn('booking_status',['TO_BE_PICKED','IN_TRANSIT'])
                                ->get();

        // dd($obj_booking->toArray());

        if($obj_booking)
        {
            $arr_booking = $obj_booking->toArray();
        }
        $arr_tmp_ids = [];

        if(isset($arr_booking) && sizeof($arr_booking)>0){
            foreach ($arr_booking as $key => $value) 
            {
                if($user_type == 'USER')
                {
                    if(isset($value['load_post_request_details']['driver_id']) && $value['load_post_request_details']['driver_id']!='')
                    {
                        $arr_tmp =  [];
                        $arr_tmp['id'] = $value['load_post_request_details']['driver_id'];
                        array_push($arr_tmp_ids, $arr_tmp);
                    }
                }
                if($user_type == 'DRIVER')
                {
                    if(isset($value['load_post_request_details']['user_id']) && $value['load_post_request_details']['user_id']!='')
                    {
                        $arr_tmp =  [];
                        $arr_tmp['id'] = $value['load_post_request_details']['user_id'];
                        array_push($arr_tmp_ids, $arr_tmp);
                    }
                }
            }
        }
        
        return $arr_tmp_ids;
    }

    public function check_latest_accepted_load_post($request)
    {
        $login_user_id    = validate_user_jwt_token();

        $arr_data['load_post_request_id'] = 0;
        $arr_data['driver_id']            = 0;

        if ($login_user_id == 0) 
        {
            return $arr_data;    
        }
        
        $obj_pending_load_post_id = $this->LoadPostRequestModel
                                                        ->select('id','user_id','driver_id')
                                                        ->whereHas('user_details',function($query){
                                                        })
                                                        ->whereHas('driver_details',function($query){
                                                        })
                                                        ->where('user_id',$login_user_id)
                                                        ->where('request_status','ACCEPT_BY_DRIVER')
                                                        // ->where('is_admin_assign','1')
                                                        ->orderBy('id','DESC')
                                                        ->first();

        $arr_data['load_post_request_id'] = isset($obj_pending_load_post_id->id) ? $obj_pending_load_post_id->id :0;
        $arr_data['driver_id']            = isset($obj_pending_load_post_id->driver_id) ? $obj_pending_load_post_id->driver_id :0;
        return $arr_data;
    }
    
    public function check_driver_latest_trip($request)
    {
        $login_user_id    = validate_user_jwt_token();
        
        if ($login_user_id == 0) 
        {
            $arr_response['status'] = 'error';
            $arr_response['msg']    = 'Invalid user token';
            return $arr_response;        
        }
        $arr_booking_master = [];
        $obj_booking_master = $this->BookingMasterModel
                                                        ->select('id','booking_status')
                                                        ->whereHas('load_post_request_details',function($query) use($login_user_id){
                                                            $query->whereHas('driver_details',function($query){
                                                            });
                                                            $query->whereHas('user_details',function($query){
                                                            });
                                                            $query->where('driver_id',$login_user_id);
                                                        })
                                                        ->whereIn('booking_status',['TO_BE_PICKED','IN_TRANSIT'])
                                                        ->orderBy('id','DESC')
                                                        ->first();
        if($obj_booking_master){
            $arr_booking_master = $obj_booking_master->toArray();
        }
        return $arr_booking_master;
    }
}