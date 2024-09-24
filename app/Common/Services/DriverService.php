<?php

namespace App\Common\Services;

use App\Models\UserModel;
use App\Models\DriverFairChargeRequestModel;

use App\Models\DriverCarRelationModel;
use App\Models\DriverStatusModel;
use App\Models\DepositMoneyModel;
use App\Models\DriverDepositModel;
use App\Models\VehicleModel;
use App\Models\BookingMasterModel;
use App\Models\BookingMasterCoordinateModel;
use App\Models\DriverFairChargeModel;
use App\Common\Services\CommonDataService;
use App\Common\Services\NotificationsService;

use Validator;
use Sentinel;

class DriverService
{
    public function __construct(
                                    UserModel $user,
                                    DriverFairChargeRequestModel $driver_fair_charge_request,
                                    DriverCarRelationModel $driver_car_relation,
                                    DepositMoneyModel $deposit_money,
                                    DriverStatusModel $driver_status,
                                    DriverDepositModel $driver_deposit,
                                    VehicleModel $vehicle,
                                    BookingMasterModel $booking_master,
                                    BookingMasterCoordinateModel $booking_master_coordinate,
                                    DriverFairChargeModel $driver_fair_charge,
                                    CommonDataService $common_data_service,
                                    NotificationsService $notifications_service
                               )
    {

        $this->UserModel                    = $user;
        $this->DriverFairChargeRequestModel = $driver_fair_charge_request;
        $this->DriverCarRelationModel       = $driver_car_relation;
        $this->DepositMoneyModel            = $deposit_money;
        $this->DriverStatusModel            = $driver_status;
        $this->DriverDepositModel           = $driver_deposit;
        $this->VehicleModel                 = $vehicle;
        $this->BookingMasterModel           = $booking_master;
        $this->BookingMasterCoordinateModel = $booking_master_coordinate;
        $this->DriverFairChargeModel        = $driver_fair_charge;
        $this->CommonDataService            = $common_data_service;
        $this->NotificationsService         = $notifications_service;
       
        $this->user_profile_public_img_path           = url('/').config('app.project.img_path.user_profile_images');
        $this->user_profile_base_img_path             = base_path().config('app.project.img_path.user_profile_images');
        
        $this->driver_deposit_receipt_public_img_path = url('/').config('app.project.img_path.driver_deposit_receipt');
        $this->driver_deposit_receipt_base_img_path   = base_path().config('app.project.img_path.driver_deposit_receipt');

        $this->vehicle_doc_public_path = url('/').config('app.project.img_path.vehicle_doc');
        $this->vehicle_doc_base_path   = base_path().config('app.project.img_path.vehicle_doc');

        $this->receipt_image_public_path = url('/').config('app.project.img_path.payment_receipt');
        $this->receipt_image_base_path   = base_path().config('app.project.img_path.payment_receipt');

        $this->driving_license_public_path = url('/').config('app.project.img_path.driving_license');
        $this->driving_license_base_path   = base_path().config('app.project.img_path.driving_license');

        $this->trip_lat_lng_base_img_path   = base_path().config('app.project.img_path.trip_lat_lng'); 
        $this->trip_lat_lng_public_img_path = url('/').config('app.project.img_path.trip_lat_lng');
    }

    public function update_lat_lng($request,$client=null)
    {   
        $arr_response = [];
        $driver_id    = validate_user_jwt_token();
        if ($driver_id == 0) 
        {
            $arr_response['status'] = 'error';
            $arr_response['msg']    = 'Invalid user token';
            return $arr_response;        
        }
        $driver_status = $request->input('status');
        $lat           = $request->input('lat');
        $lng           = $request->input('lng');
        // $booking_id = $request->input('booking_id');
        $device_id     = $request->input('device_id');
        $is_update     = $request->input('is_update');

        $driver_status = 'AVAILABLE';

        $is_user_login = '0';
        $reset_password_mandatory = '0';

        $msg = '-';

        $obj_status  = $this->DriverStatusModel
                                    ->where('driver_id',$driver_id)
                                    ->with('driver_details')
                                    ->first();
        if($obj_status)
        {
            $driver_status = isset($obj_status->status) ? $obj_status->status :'AVAILABLE';

            // $is_user_login            = isset($obj_status->driver_details->is_user_login) ? strval($obj_status->driver_details->is_user_login) :'0';
            $reset_password_mandatory = isset($obj_status->driver_details->reset_password_mandatory) ? strval($obj_status->driver_details->reset_password_mandatory) :'0';
            if($reset_password_mandatory == '1')
            {
                $msg = 'Your password was changed by QuickPick Admin, email is sent with the new password.';
            }

            $db_device_id = isset($obj_status->driver_details->device_id) ? strval($obj_status->driver_details->device_id) :'';

            if($db_device_id!='' && $device_id!='')
            {
                if($db_device_id!=$device_id)
                {
                    $is_user_login = '1';
                    $msg = 'You logged in from another device, your current session expired';
                }
            }

            if($db_device_id!='' && $device_id!='')
            {
                if($db_device_id==$device_id)
                {
                    $obj_status->current_latitude = $lat;
                    $obj_status->current_longitude = $lng;
                    $obj_status->save();
                }
            }
        }
        $arr_response['status']        = 'success';
        $arr_response['msg']           = $msg;
        
        $arr_data                  = [];

        $arr_data['driver_status']            = $driver_status;
        $arr_data['is_user_login']            = $is_user_login;
        $arr_data['reset_password_mandatory'] = $reset_password_mandatory;
        
        // $arr_driver_current_trip = 
        //                                         [
        //                                             'driver_id'  => $driver_id,
        //                                             'lat'        => $lat,
        //                                             'lng'        => $lng,
        //                                             'client'     => isset($client) ? $client : null
        //                                         ];

        // $arr_trip_data = $this->check_driver_current_trip_details($arr_driver_current_trip);
        
        // dd($arr_trip_data);

        if(isset($is_update) && $is_update == 'YES')
        {
            if($db_device_id!='' && $device_id!='')
            {
                if($db_device_id==$device_id)
                {
                    $arr_driver_current_trip = 
                                                [
                                                    'driver_id'  => $driver_id,
                                                    'lat'        => $lat,
                                                    'lng'        => $lng,
                                                    'client'     => isset($client) ? $client : null
                                                ];

                    $arr_trip_data = $this->check_driver_current_trip_details($arr_driver_current_trip);
                }
            }
        }

        
        $arr_response['data']          = $arr_data;
        
        return $arr_response;       
    }
    
    public function update_availability_status($request)
    {   
        $arr_response = [];
        $driver_id    = validate_user_jwt_token();
        if ($driver_id == 0) 
        {
            $arr_response['status'] = 'error';
            $arr_response['msg']    = 'Invalid user token';
            $arr_response['data']   = [];
            return $arr_response;        
        }
        $status     = $request->input('status');

        $status  = $this->UserModel
                                    ->where('id',$driver_id)
                                    ->update([
                                              'availability_status' => $status
                                            ]);

        $arr_response['status']     = 'success';
        $arr_response['msg']        = 'availability status updated successfully.';
        $arr_response['arr_data']   = [];
        return $arr_response;       
    }   

    public function get_driver_availability_status($request)
    {   
        $arr_response = [];
        $driver_id    = validate_user_jwt_token();
        if ($driver_id == 0) 
        {
            $arr_response['status'] = 'error';
            $arr_response['msg']    = 'Invalid user token';
            $arr_response['data']   = [];
            return $arr_response;        
        }
        
        $obj_user  = $this->UserModel
                                    ->select('availability_status')
                                    ->where('id',$driver_id)
                                    ->first();
        

        $arr_data = [];
        if($obj_user){
            $arr_data = $obj_user->toArray();
        }
        if(isset($arr_data['availability_status']) && $arr_data['availability_status']!=''){
            $arr_response['status'] = 'success';
            $arr_response['msg']    = 'availability status get successfully.';
            $arr_response['data']   = $arr_data;
            return $arr_response;       
        }else{
            $arr_response['status']  = 'error';
            $arr_response['msg']    = 'availability status not found.';
            $arr_response['data']   = [];    
            return $arr_response;       
        }
        $arr_response['status']    = 'error';
        $arr_response['msg']       = 'availability status not found.';
        $arr_response['data']      = [];
        return $arr_response;       
    }

    public function get_vehicle_details($request)
    {   

        $arr_response = [];
        $driver_id    = validate_user_jwt_token();
        if ($driver_id == 0) 
        {
            $arr_response['status'] = 'error';
            $arr_response['msg']    = 'Invalid user token';
            $arr_response['data']   = [];
            return $arr_response;        
        }
        
        $obj_driver_car_relation  = $this->DriverCarRelationModel
                                                ->with(['vehicle_details'=>function($query){
                                                    $query->with('vehicle_brand_details','vehicle_model_details');
                                                },'driver_details'])
                                                ->where('driver_id',$driver_id)
                                                ->first();
        

        $arr_driver_car_relation = [];
        if($obj_driver_car_relation){
            $arr_driver_car_relation = $obj_driver_car_relation->toArray();
        }
        
        $obj_driver_fare_charge_list = $this->DriverFairChargeModel 
                                                    ->whereHas('driver_fair_charge_request_details',function($query){
                                                            $query->orderBy('id','desc');
                                                    })  
                                                    ->with(['driver_fair_charge_request_details'=>function($query){
                                                        $query->orderBy('id','desc');
                                                    }])
                                                    ->where('driver_id',$driver_id)
                                                    ->first();
  
        if(isset($arr_driver_car_relation['vehicle_details']) && sizeof($arr_driver_car_relation['vehicle_details'])>0){

            $arr_vehicle_details = $arr_driver_car_relation['vehicle_details'];

            $arr_vehicle_details['vehicle_name'] = isset($arr_driver_car_relation['vehicle_details']['vehicle_brand_details']['name']) ? $arr_driver_car_relation['vehicle_details']['vehicle_brand_details']['name'] : '';
            $arr_vehicle_details['vehicle_model_name'] = isset($arr_driver_car_relation['vehicle_details']['vehicle_model_details']['name']) ? $arr_driver_car_relation['vehicle_details']['vehicle_model_details']['name'] : '';

            $arr_vehicle_details['vehicle_type'] = isset($arr_vehicle_details['vehicle_type_details']['vehicle_type']) ? $arr_vehicle_details['vehicle_type_details']['vehicle_type'] :'';
            unset($arr_vehicle_details['vehicle_type_details']);
            
            $driving_license = $vehicle_image = $registration_doc = $proof_of_inspection_doc = $insurance_doc = $dmv_driving_record = $usdot_doc = $mc_doc = '';

            $is_driving_license_verified = isset($arr_driver_car_relation['driver_details']['is_driving_license_verified']) ? $arr_driver_car_relation['driver_details']['is_driving_license_verified'] : '';

            if(isset($arr_driver_car_relation['driver_details']['driving_license']) && $arr_driver_car_relation['driver_details']['driving_license']!=''){
                $tmp_driving_license = isset($arr_driver_car_relation['driver_details']['driving_license']) ? $arr_driver_car_relation['driver_details']['driving_license'] :'';
                if(file_exists($this->driving_license_base_path.$tmp_driving_license))
                {
                    $driving_license = $this->driving_license_public_path.$tmp_driving_license;
                }   
            }

            if(isset($arr_vehicle_details['registration_doc']) && $arr_vehicle_details['registration_doc']!=''){
                $tmp_registration_doc = isset($arr_vehicle_details['registration_doc']) ? $arr_vehicle_details['registration_doc'] :'';
                if(file_exists($this->vehicle_doc_base_path.$tmp_registration_doc))
                {
                    $registration_doc = $this->vehicle_doc_public_path.$tmp_registration_doc;
                }   
            }
            
            if(isset($arr_vehicle_details['vehicle_image']) && $arr_vehicle_details['vehicle_image']!=''){
                $tmp_vehicle_image = isset($arr_vehicle_details['vehicle_image']) ? $arr_vehicle_details['vehicle_image'] :'';
                if(file_exists($this->vehicle_doc_base_path.$tmp_vehicle_image)){
                    $vehicle_image = $this->vehicle_doc_public_path.$tmp_vehicle_image;
                }
            }
            
            if(isset($arr_vehicle_details['proof_of_inspection_doc']) && $arr_vehicle_details['proof_of_inspection_doc']!=''){
                $tmp_proof_of_inspection_doc = isset($arr_vehicle_details['proof_of_inspection_doc']) ? $arr_vehicle_details['proof_of_inspection_doc'] :'';
                if(file_exists($this->vehicle_doc_base_path.$tmp_proof_of_inspection_doc)){
                    $proof_of_inspection_doc = $this->vehicle_doc_public_path.$tmp_proof_of_inspection_doc;
                }
            }

            if(isset($arr_vehicle_details['insurance_doc']) && $arr_vehicle_details['insurance_doc']!=''){
                $tmp_insurance_doc = isset($arr_vehicle_details['insurance_doc']) ? $arr_vehicle_details['insurance_doc'] :'';
                
                if(file_exists($this->vehicle_doc_base_path.$tmp_insurance_doc)){
                    $insurance_doc = $this->vehicle_doc_public_path.$tmp_insurance_doc;
                }
            }

            if(isset($arr_vehicle_details['dmv_driving_record']) && $arr_vehicle_details['dmv_driving_record']!=''){
                $tmp_dmv_driving_record = isset($arr_vehicle_details['dmv_driving_record']) ? $arr_vehicle_details['dmv_driving_record'] :'';
                if(file_exists($this->vehicle_doc_base_path.$tmp_dmv_driving_record)){
                    $dmv_driving_record = $this->vehicle_doc_public_path.$tmp_dmv_driving_record;
                }
            }
            
            if(isset($arr_vehicle_details['usdot_doc']) && $arr_vehicle_details['usdot_doc']!=''){
                $tmp_usdot_doc = isset($arr_vehicle_details['usdot_doc']) ? $arr_vehicle_details['usdot_doc'] :'';
                if(file_exists($this->vehicle_doc_base_path.$tmp_usdot_doc)){
                    $usdot_doc = $this->vehicle_doc_public_path.$tmp_usdot_doc;
                }
            }

            if(isset($arr_vehicle_details['mc_doc']) && $arr_vehicle_details['mc_doc']!=''){
                $tmp_mc_doc = isset($arr_vehicle_details['mc_doc']) ? $arr_vehicle_details['mc_doc'] :'';
                if(file_exists($this->vehicle_doc_base_path.$tmp_mc_doc)){
                    $mc_doc = $this->vehicle_doc_public_path.$tmp_mc_doc;
                }
            }

            $arr_vehicle_details['driving_license']             = $driving_license;
            $arr_vehicle_details['is_driving_license_verified'] = $is_driving_license_verified;
            $arr_vehicle_details['vehicle_insurance_doc']       = $insurance_doc;
            $arr_vehicle_details['proof_of_inspection_doc']     = $proof_of_inspection_doc;
            $arr_vehicle_details['vehicle_image']               = $vehicle_image;
            $arr_vehicle_details['registration_doc']            = $registration_doc;
            $arr_vehicle_details['dmv_driving_record']          = $dmv_driving_record;
            $arr_vehicle_details['usdot_doc']                   = $usdot_doc;
            $arr_vehicle_details['mc_doc']                      = $mc_doc;
            
            $fair_charge = 0; $status = '';

            if(isset($arr_vehicle_details['is_individual_vehicle']) && $arr_vehicle_details['is_individual_vehicle'] == '1')
            {
                $fair_charge = isset($obj_driver_fare_charge_list->driver_fair_charge_request_details->fair_charge) ? floatval($obj_driver_fare_charge_list->driver_fair_charge_request_details->fair_charge) :0;
                $status      = isset($obj_driver_fare_charge_list->driver_fair_charge_request_details->status) ? $obj_driver_fare_charge_list->driver_fair_charge_request_details->status:'NOT_REQUEST';
            }
            
            if(isset($arr_vehicle_details['is_individual_vehicle']) && $arr_vehicle_details['is_individual_vehicle'] == '0')
            {
                $fair_charge = isset($arr_vehicle_details['driver_per_kilometer_charge']) ? floatval($arr_vehicle_details['driver_per_kilometer_charge']) :0;
                $status      = 'ASSIGN';
            }

            unset($arr_vehicle_details['admin_per_kilometer_charge']);
            unset($arr_vehicle_details['driver_per_kilometer_charge']);
            unset($arr_vehicle_details['vehicle_brand_details']);
            unset($arr_vehicle_details['vehicle_model_details']);

            $arr_vehicle_details['fair_charge'] = $fair_charge;
            $arr_vehicle_details['status']      = $status;
            
            $arr_response['status'] = 'success';
            $arr_response['msg']    = 'availability status get successfully.';
            $arr_response['data']   = $arr_vehicle_details;
            return $arr_response;       
        }
        else
        {
            $arr_response['status']  = 'error';
            $arr_response['msg']    = 'Vehicle not assigned by admin.';
            $arr_response['data']   = [];    
            return $arr_response;       
        }
        $arr_response['status']    = 'error';
        $arr_response['msg']       = 'Vehicle not assigned by admin.';
        $arr_response['data']      = [];
        return $arr_response;       
    }
    
    public function update_vehicle_details($request)
    {
        $arr_response = [];
        $driver_id    = validate_user_jwt_token();
        if ($driver_id == 0) 
        {
            $arr_response['status'] = 'error';
            $arr_response['msg']    = 'Invalid user token';
            $arr_response['data']   = [];
            return $arr_response;        
        }

        $arr_rules                     = [];
        $arr_rules['is_individual_vehicle']   = "required";

        if($request->has('is_individual_vehicle') && $request->input('is_individual_vehicle') == '1')
        {
            $arr_rules['vehicle_id']       = "required";
            $arr_rules['vehicle_type_id']  = "required";
            $arr_rules['vehicle_brand_id'] = "required";
            $arr_rules['vehicle_model_id'] = "required";
            $arr_rules['vehicle_number']   = "required";
        }
        
        $validator = Validator::make($request->all(),$arr_rules);
        if($validator->fails())
        {
            $arr_response['status'] = 'error';
            $arr_response['msg']    = 'Please fill all the required field';
            $arr_response['data']   = [];
            return $arr_response;
        }
        $vehicle_id            = $request->input('vehicle_id');
        $is_individual_vehicle = $request->input('is_individual_vehicle');
        
        if($is_individual_vehicle == '1')
        {
            $obj_driver_car_relation  = $this->DriverCarRelationModel
                                                    ->where('driver_id',$driver_id)
                                                    ->where('vehicle_id',$vehicle_id)
                                                    ->count();
            

            if($obj_driver_car_relation == 0)
            {
                $arr_response['status'] = 'error';
                $arr_response['msg']    = 'Vehicle and driver details missmach, cannot update vehicle details, Please try again.';
                $arr_response['data']   = [];
                return $arr_response;
            }
            
            $obj_vehicle_details = $this->VehicleModel
                                                ->where('id',$vehicle_id)
                                                ->first();
            
            if($obj_vehicle_details == null || $obj_vehicle_details == false)
            {
                $arr_response['status'] = 'error';
                $arr_response['msg']    = 'Something went wrong, cannot update vehicle details, Please try again.';
                $arr_response['data']   = [];
                return $arr_response;
            }

            $is_make_all_document_clear = 'NO';
            if(isset($obj_vehicle_details))
            {
                $db_vehicle_type_id  = isset($obj_vehicle_details->vehicle_type_id) ? $obj_vehicle_details->vehicle_type_id : 0;
                $db_vehicle_brand_id = isset($obj_vehicle_details->vehicle_brand_id) ? $obj_vehicle_details->vehicle_brand_id : 0;
                $db_vehicle_model_id = isset($obj_vehicle_details->vehicle_model_id) ? $obj_vehicle_details->vehicle_model_id : 0;
                $db_vehicle_number   = isset($obj_vehicle_details->vehicle_number) ? $obj_vehicle_details->vehicle_number : '';

                $vehicle_type_id  = $request->input('vehicle_type_id');
                $vehicle_brand_id = $request->input('vehicle_brand_id');
                $vehicle_model_id = $request->input('vehicle_model_id');
                $vehicle_number   = $request->input('vehicle_number');

                if(intval($db_vehicle_type_id)!=intval($vehicle_type_id))
                {
                    $is_make_all_document_clear = 'YES';
                }
                if(intval($db_vehicle_brand_id)!=intval($vehicle_brand_id))
                {
                    $is_make_all_document_clear = 'YES';
                }
                if(intval($db_vehicle_model_id)!=intval($vehicle_model_id))
                {
                    $is_make_all_document_clear = 'YES';
                }
                if($db_vehicle_number!=$vehicle_number)
                {
                    $is_make_all_document_clear = 'YES';
                }
            }

            $arr_update_vehicle = [];

            $arr_update_vehicle['vehicle_type_id']       = $request->input('vehicle_type_id');
            // $arr_update_vehicle['vehicle_name']          = $request->input('vehicle_name');
            // $arr_update_vehicle['vehicle_model_name']    = $request->input('vehicle_model_name');
            $arr_update_vehicle['vehicle_brand_id']      = $request->input('vehicle_brand_id');
            $arr_update_vehicle['vehicle_model_id']      = $request->input('vehicle_model_id');
            $arr_update_vehicle['vehicle_year_id']       = $request->input('vehicle_year_id');
            $arr_update_vehicle['vehicle_number']        = $request->input('vehicle_number');
            $arr_update_vehicle['is_active']             = 1;
            $arr_update_vehicle['is_verified']           = 0;
            
            /*if driver change any of details then make all documents empty*/
            if($is_make_all_document_clear == 'YES')
            {
                $obj_driver_data = $this->UserModel->where('id',$driver_id)->first();
                if($obj_driver_data)
                {
                    if(isset($obj_driver_data->driving_license) && $obj_driver_data->driving_license!='')
                    {
                        if(file_exists($this->driving_license_base_path.$obj_driver_data->driving_license))
                        {
                                @unlink($this->driving_license_base_path.$obj_driver_data->driving_license);
                        }
                    }
                    $obj_driver_data->driving_license             = '';
                    $obj_driver_data->is_driving_license_verified = 'NOTAPPROVED';
                    $obj_driver_data->save();
                }

                $arr_update_vehicle['vehicle_image']                       = '';
                $arr_update_vehicle['is_vehicle_image_verified']           = 'NOTAPPROVED';

                if(isset($obj_vehicle_details->vehicle_image) && $obj_vehicle_details->vehicle_image!='')
                {
                    if(file_exists($this->vehicle_doc_base_path.$obj_vehicle_details->vehicle_image))
                    {
                        @unlink($this->vehicle_doc_base_path.$obj_vehicle_details->vehicle_image);
                    }
                }

                $arr_update_vehicle['registration_doc']                    = '';
                $arr_update_vehicle['is_registration_doc_verified']        = 'NOTAPPROVED';

                if(isset($obj_vehicle_details->registration_doc) && $obj_vehicle_details->registration_doc!=''){
                    if(file_exists($this->vehicle_doc_base_path.$obj_vehicle_details->registration_doc)){
                        @unlink($this->vehicle_doc_base_path.$obj_vehicle_details->registration_doc);
                    }
                }


                $arr_update_vehicle['insurance_doc']                       = '';
                $arr_update_vehicle['is_insurance_doc_verified']           = 'NOTAPPROVED';

                if(isset($obj_vehicle_details->insurance_doc) && $obj_vehicle_details->insurance_doc!=''){
                        if(file_exists($this->vehicle_doc_base_path.$obj_vehicle_details->insurance_doc)){
                            @unlink($this->vehicle_doc_base_path.$obj_vehicle_details->insurance_doc);
                        }
                    }

                $arr_update_vehicle['proof_of_inspection_doc']             = '';
                $arr_update_vehicle['is_proof_of_inspection_doc_verified'] = 'NOTAPPROVED';

                if(isset($obj_vehicle_details->proof_of_inspection_doc) && $obj_vehicle_details->proof_of_inspection_doc!=''){
                    if(file_exists($this->vehicle_doc_base_path.$obj_vehicle_details->proof_of_inspection_doc)){
                        @unlink($this->vehicle_doc_base_path.$obj_vehicle_details->proof_of_inspection_doc);
                    }
                }

                $arr_update_vehicle['dmv_driving_record']                  = '';
                $arr_update_vehicle['is_dmv_driving_record_verified']      = 'NOTAPPROVED';

                if(isset($obj_vehicle_details->dmv_driving_record) && $obj_vehicle_details->dmv_driving_record!=''){
                    if(file_exists($this->vehicle_doc_base_path.$obj_vehicle_details->dmv_driving_record)){
                        @unlink($this->vehicle_doc_base_path.$obj_vehicle_details->dmv_driving_record);
                    }
                }

                $arr_update_vehicle['usdot_doc']                           = '';
                $arr_update_vehicle['is_usdot_doc_verified']               = 'NOTAPPROVED';

                if(isset($obj_vehicle_details->usdot_doc) && $obj_vehicle_details->usdot_doc!=''){
                    if(file_exists($this->vehicle_doc_base_path.$obj_vehicle_details->usdot_doc)){
                        @unlink($this->vehicle_doc_base_path.$obj_vehicle_details->usdot_doc);
                    }
                }

                $arr_update_vehicle['mc_doc']                           = '';
                $arr_update_vehicle['is_mcdoc_doc_verified']            = 'NOTAPPROVED';

                if(isset($obj_vehicle_details->mc_doc) && $obj_vehicle_details->mc_doc!=''){
                    if(file_exists($this->vehicle_doc_base_path.$obj_vehicle_details->mc_doc)){
                        @unlink($this->vehicle_doc_base_path.$obj_vehicle_details->mc_doc);
                    }
                }
            }
            
            if($request->hasFile('driving_license'))
            {
                $obj_driver_data = $this->UserModel->where('id',$driver_id)->first();

                $driving_license = $request->input('driving_license');
                $file_extension = strtolower($request->file('driving_license')->getClientOriginalExtension());
                if(in_array($file_extension,['png','jpg','jpeg','pdf','heic','HEIC']))
                {
                    $driving_license = time().uniqid().'.'.$file_extension;
                    $isUpload = $request->file('driving_license')->move($this->driving_license_base_path , $driving_license);
                    if($isUpload && isset($obj_driver_data->driving_license) && $obj_driver_data->driving_license!=''){

                        if(file_exists($this->driving_license_base_path.$obj_driver_data->driving_license)){
                            @unlink($this->driving_license_base_path.$obj_driver_data->driving_license);
                        }
                    }
                    if($obj_driver_data)
                    {
                        $obj_driver_data->driving_license             = $driving_license;
                        $obj_driver_data->is_driving_license_verified = 'PENDING';
                        $obj_driver_data->save();
                    }
                }
            }

            if($request->hasFile('vehicle_image'))
            {
                $vehicle_image = $request->input('vehicle_image');
                $file_extension = strtolower($request->file('vehicle_image')->getClientOriginalExtension());
                if(in_array($file_extension,['png','jpg','jpeg','pdf','heic','HEIC']))
                {
                    $vehicle_image = time().uniqid().'.'.$file_extension;
                    $isUpload = $request->file('vehicle_image')->move($this->vehicle_doc_base_path , $vehicle_image);
                    $arr_update_vehicle['vehicle_image'] = $vehicle_image;
                    $arr_update_vehicle['is_vehicle_image_verified'] = 'PENDING';
                    if($isUpload && isset($obj_vehicle_details->vehicle_image) && $obj_vehicle_details->vehicle_image!=''){

                        if(file_exists($this->vehicle_doc_base_path.$obj_vehicle_details->vehicle_image)){
                            @unlink($this->vehicle_doc_base_path.$obj_vehicle_details->vehicle_image);
                        }
                    }
                    
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
                    $arr_update_vehicle['registration_doc'] = $registration_doc;
                    $arr_update_vehicle['is_registration_doc_verified'] = 'PENDING';
                    if($isUpload && isset($obj_vehicle_details->registration_doc) && $obj_vehicle_details->registration_doc!=''){
                        if(file_exists($this->vehicle_doc_base_path.$obj_vehicle_details->registration_doc)){
                            @unlink($this->vehicle_doc_base_path.$obj_vehicle_details->registration_doc);
                        }
                    }

                }
            }
            
            if($request->hasFile('vehicle_insurance_doc'))
            {
                $insurance_doc = $request->input('vehicle_insurance_doc');
                $file_extension = strtolower($request->file('vehicle_insurance_doc')->getClientOriginalExtension());
                if(in_array($file_extension,['png','jpg','jpeg','pdf','heic','HEIC']))
                {
                    $insurance_doc = time().uniqid().'.'.$file_extension;
                    $isUpload = $request->file('vehicle_insurance_doc')->move($this->vehicle_doc_base_path , $insurance_doc);
                    $arr_update_vehicle['insurance_doc'] = $insurance_doc;
                    $arr_update_vehicle['is_insurance_doc_verified'] = 'PENDING';
                    if($isUpload && isset($obj_vehicle_details->insurance_doc) && $obj_vehicle_details->insurance_doc!=''){
                        if(file_exists($this->vehicle_doc_base_path.$obj_vehicle_details->insurance_doc)){
                            @unlink($this->vehicle_doc_base_path.$obj_vehicle_details->insurance_doc);
                        }
                    }

                }
            }

            if($request->hasFile('proof_of_inspection_doc'))
            {
                $insurance_doc = $request->input('proof_of_inspection_doc');
                $file_extension = strtolower($request->file('proof_of_inspection_doc')->getClientOriginalExtension());
                if(in_array($file_extension,['png','jpg','jpeg','pdf','heic','HEIC']))
                {
                    $proof_of_inspection_doc = time().uniqid().'.'.$file_extension;
                    $isUpload = $request->file('proof_of_inspection_doc')->move($this->vehicle_doc_base_path , $proof_of_inspection_doc);
                    $arr_update_vehicle['proof_of_inspection_doc'] = $proof_of_inspection_doc;
                    $arr_update_vehicle['is_proof_of_inspection_doc_verified'] = 'PENDING';
                    if($isUpload && isset($obj_vehicle_details->proof_of_inspection_doc) && $obj_vehicle_details->proof_of_inspection_doc!=''){
                        if(file_exists($this->vehicle_doc_base_path.$obj_vehicle_details->proof_of_inspection_doc)){
                            @unlink($this->vehicle_doc_base_path.$obj_vehicle_details->proof_of_inspection_doc);
                        }
                    }

                }
            }

            if($request->hasFile('dmv_driving_record'))
            {
                $dmv_driving_record = $request->input('dmv_driving_record');
                $file_extension = strtolower($request->file('dmv_driving_record')->getClientOriginalExtension());
                if(in_array($file_extension,['png','jpg','jpeg','pdf','heic','HEIC']))
                {
                    $dmv_driving_record = time().uniqid().'.'.$file_extension;
                    $isUpload = $request->file('dmv_driving_record')->move($this->vehicle_doc_base_path , $dmv_driving_record);
                    $arr_update_vehicle['dmv_driving_record'] = $dmv_driving_record;
                    $arr_update_vehicle['is_dmv_driving_record_verified'] = 'PENDING';
                    if($isUpload && isset($obj_vehicle_details->dmv_driving_record) && $obj_vehicle_details->dmv_driving_record!=''){
                        if(file_exists($this->vehicle_doc_base_path.$obj_vehicle_details->dmv_driving_record)){
                            @unlink($this->vehicle_doc_base_path.$obj_vehicle_details->dmv_driving_record);
                        }
                    }

                }
            }

            if($request->hasFile('usdot_doc'))
            {
                $insurance_doc = $request->input('usdot_doc');
                $file_extension = strtolower($request->file('usdot_doc')->getClientOriginalExtension());
                if(in_array($file_extension,['png','jpg','jpeg','pdf','heic','HEIC']))
                {
                    $usdot_doc = time().uniqid().'.'.$file_extension;
                    $isUpload = $request->file('usdot_doc')->move($this->vehicle_doc_base_path , $usdot_doc);
                    $arr_update_vehicle['usdot_doc'] = $usdot_doc;
                    $arr_update_vehicle['is_usdot_doc_verified'] = 'PENDING';
                    if($isUpload && isset($obj_vehicle_details->usdot_doc) && $obj_vehicle_details->usdot_doc!=''){
                        if(file_exists($this->vehicle_doc_base_path.$obj_vehicle_details->usdot_doc)){
                            @unlink($this->vehicle_doc_base_path.$obj_vehicle_details->usdot_doc);
                        }
                    }

                }
            }

            if($request->hasFile('mc_doc'))
            {
                $insurance_doc = $request->input('mc_doc');
                $file_extension = strtolower($request->file('mc_doc')->getClientOriginalExtension());
                if(in_array($file_extension,['png','jpg','jpeg','pdf','heic','HEIC']))
                {
                    $mc_doc = time().uniqid().'.'.$file_extension;
                    $isUpload = $request->file('mc_doc')->move($this->vehicle_doc_base_path , $mc_doc);
                    $arr_update_vehicle['mc_doc'] = $mc_doc;
                    $arr_update_vehicle['is_mcdoc_doc_verified'] = 'PENDING';
                    if($isUpload && isset($obj_vehicle_details->mc_doc) && $obj_vehicle_details->mc_doc!=''){
                        if(file_exists($this->vehicle_doc_base_path.$obj_vehicle_details->mc_doc)){
                            @unlink($this->vehicle_doc_base_path.$obj_vehicle_details->mc_doc);
                        }
                    }

                }
            }

            $status = $this->VehicleModel
                                ->where('id',$vehicle_id)
                                ->update($arr_update_vehicle);

            if($status)
            {
                $arr_notification_data = $this->built_notification_data(['driver_id' => $driver_id],'VEHICLE_DETAILS_UPDATE'); 
                $this->NotificationsService->store_notification($arr_notification_data);
                
                $arr_response['status'] = 'success';
                $arr_response['msg']    = 'Vehicle details updated successfully, Vehicle verification request successfully sent to Admin.';
                $arr_response['data']   = [];
                return $arr_response;
            }
        }
        else if($is_individual_vehicle == '0')
        {
            if($request->hasFile('driving_license'))
            {
                $obj_driver_data = $this->UserModel->where('id',$driver_id)->first();

                $driving_license = $request->input('driving_license');
                $file_extension = strtolower($request->file('driving_license')->getClientOriginalExtension());
                if(in_array($file_extension,['png','jpg','jpeg','pdf','heic','HEIC']))
                {
                    $driving_license = time().uniqid().'.'.$file_extension;
                    $isUpload = $request->file('driving_license')->move($this->driving_license_base_path , $driving_license);
                    if($isUpload && isset($obj_driver_data->driving_license) && $obj_driver_data->driving_license!=''){

                        if(file_exists($this->driving_license_base_path.$obj_driver_data->driving_license)){
                            @unlink($this->driving_license_base_path.$obj_driver_data->driving_license);
                        }
                    }
                    if($obj_driver_data)
                    {
                        $obj_driver_data->driving_license             = $driving_license;
                        $obj_driver_data->is_driving_license_verified = 'PENDING';
                        $obj_driver_data->account_status              = 'unapproved';
                        $obj_driver_data->save();
                    }
                }
            }
            
            $arr_notification_data = $this->built_notification_data(['driver_id' => $driver_id],'VEHICLE_DETAILS_UPDATE'); 
            $this->NotificationsService->store_notification($arr_notification_data);

            $arr_response['status'] = 'success';
            $arr_response['msg']    = 'Driving license successfully sent for verification.';
            $arr_response['data']   = [];
            return $arr_response;
        }
        $arr_response['status'] = 'error';
        $arr_response['msg']    = 'Problem Occurred, while updating vehicle details,Please try again.';
        $arr_response['data']   = [];
        return $arr_response;
    }

    public function get_driver_fair_charge($request)
    {
        $driver_id    = validate_user_jwt_token();
        if ($driver_id == 0) 
        {
            $arr_response['status'] = 'error';
            $arr_response['msg']    = 'Invalid user token';
            $arr_response['data']   = [];
            return $arr_response;        
        }
        $arr_data['fair_charge'] = 0;
        $arr_data['status']      = 'NOT_REQUEST';

        $obj_driver_fare_charge_list = $this->DriverFairChargeModel 
                                                    ->whereHas('driver_fair_charge_request_details',function($query){
                                                            $query->orderBy('id','desc');
                                                    })  
                                                    ->with(['driver_fair_charge_request_details'=>function($query){
                                                        $query->orderBy('id','desc');
                                                    }])
                                                    ->where('driver_id',$driver_id)
                                                    ->first();
                            
        if($obj_driver_fare_charge_list)
        {
            $arr_driver_fare_charge_list = $obj_driver_fare_charge_list->toArray();
            
            $arr_data['fair_charge'] = isset($arr_driver_fare_charge_list['driver_fair_charge_request_details']['fair_charge']) ? floatval($arr_driver_fare_charge_list['driver_fair_charge_request_details']['fair_charge']) :0;                                                                                      
            $arr_data['status']      = isset($arr_driver_fare_charge_list['driver_fair_charge_request_details']['status']) ? $arr_driver_fare_charge_list['driver_fair_charge_request_details']['status']:'';
                                                                                                                                  
            $arr_response['status'] = 'success';
            $arr_response['msg']    = 'Fair charge details found successfully.';
            $arr_response['data']   = $arr_data;
            return $arr_response;

        }
        else
        {
            $arr_response['status']  = 'error';
            $arr_response['msg']     = 'Problem Occurred, While getting fair charge details.';
            $arr_response['data']    = $arr_data;
            return $arr_response;
        }           
        $arr_response['status']  = 'error';
        $arr_response['msg']     = 'Problem Occurred, While getting fair charge details.';
        $arr_response['data']    = $arr_data;
        return $arr_response;
    }

    public function send_driver_fair_charge($request)
    {
        $arr_response = [];
        $driver_id    = validate_user_jwt_token();
        if ($driver_id == 0) 
        {
            $arr_response['status'] = 'error';
            $arr_response['msg']    = 'Invalid user token';
            $arr_response['data']   = [];
            return $arr_response;        
        }
        $arr_rules = [];
        $arr_rules['fair_charge']     = "required";
        
        $validator = Validator::make($request->all(),$arr_rules);
        
        if($validator->fails())
        {
            $arr_response['status'] = 'error';
            $arr_response['msg']    = 'Fair charge cannot be empty.';
            $arr_response['data']   = [];
            return $arr_response;        
        }
        
        $fair_charge     = $request->input('fair_charge');
        
        $arr_data                 = [];
        $arr_data['driver_id']    = $driver_id;
        $arr_data['fair_charge']  = $fair_charge;
        $arr_data['status']       = 'REQUEST';
        $result = $this->DriverFairChargeRequestModel->create($arr_data);
        if($result)
        {
            $arr_notification_data = $this->built_notification_data($arr_data,'DRIVER_FAIR_CHARGE_REQUEST'); 
            $this->NotificationsService->store_notification($arr_notification_data);

            $arr_response['status'] = 'success';
            $arr_response['msg']    = 'Driver fair charge request successfully send to admin.';
            $arr_response['data']   = [];
            return $arr_response;        
        }
        else
        {
            $arr_response['status'] = 'error';
            $arr_response['msg']    = 'Problem Occurred, While send to Driver fair charge request to admin.';
            $arr_response['data']   = [];
            return $arr_response;        
        }
        
        $arr_response['status'] = 'error';
        $arr_response['msg']    = 'Problem Occurred, While send to Driver fair charge request to admin.';
        $arr_response['data']   = [];
        return $arr_response;        
    }
    
    public function get_driver_deposit_money($request)
    {
        $driver_id    = validate_user_jwt_token();
        if ($driver_id == 0) 
        {
            $arr_response['status'] = 'error';
            $arr_response['msg']    = 'Invalid user token';
            $arr_response['data']   = [];
            return $arr_response;        
        }
        
        // $driver_id = 6;

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
        
        $arr_data = [];
        $arr_data = $arr_driver_balance_information;
        $arr_data['list_status'] = 'error';
        $arr_data["arr_receipt_list"] = array('data'=>[]);
        // $arr_data["arr_receipt_list"] = array();

        $obj_receipt_list = $this->DepositMoneyModel
                                            ->select('id','transaction_id','amount_paid','receipt_image','note','status','date')
                                            ->where('to_user_id',$driver_id)
                                            ->orderBy('id','DESC')
                                            ->paginate(10);
        
        $arr_receipt_list = [];
        if($obj_receipt_list)
        {
            $arr_receipt_list = $obj_receipt_list ->toArray();

            if(isset($arr_receipt_list['data']) && sizeof($arr_receipt_list['data'])>0)
            {
                foreach($arr_receipt_list['data'] as $key=>$value)
                {
                    $arr_receipt_list['data'][$key]['transaction_id'] = isset($value['transaction_id']) ? $value['transaction_id'] :'';
                    $arr_receipt_list['data'][$key]['amount_paid'] = isset($value['amount_paid']) ? floatval($value['amount_paid']) :floatval(0.0);
                    $arr_receipt_list['data'][$key]['status'] = isset($value['status']) ? $value['status'] : "" ;
                    $receipt_image = '';
                    if(isset($value['receipt_image']) && $value['receipt_image']!='')
                    {
                        if(file_exists($this->receipt_image_base_path.$value['receipt_image']))
                        {
                            $receipt_image = $this->receipt_image_public_path.$value['receipt_image'];
                        }
                    }
                    $arr_receipt_list['data'][$key]['receipt_image']  = $receipt_image;
                    $arr_receipt_list['data'][$key]['date'] = isset($value['date']) ? date('d M Y',strtotime($value['date'])) :'';
                    $arr_receipt_list['data'][$key]['note'] = isset($value['note']) ? $value['note'] : "" ;
                }
                $arr_data["arr_receipt_list"] = $arr_receipt_list;
            }
        }

        if(isset($arr_receipt_list['data']) && sizeof($arr_receipt_list['data'])>0)
        {
            $arr_data['list_status'] = 'success';
            $arr_response['status']  = 'success';
            $arr_response['msg']     = 'data available';
            $arr_response['data']    = $arr_data;
            return $arr_response;
        }
        $arr_response['status'] = 'success';
        $arr_response['msg']    = 'no data available';
        $arr_response['data']   = $arr_data;
        return $arr_response;
    }

    public function process_deposit_money_request($request)
    {
        $driver_id    = validate_user_jwt_token();
        if ($driver_id == 0) 
        {
            $arr_response['status'] = 'error';
            $arr_response['msg']    = 'Invalid user token';
            return $arr_response;        
        }
        
        $arr_rules           = [];
        $arr_rules['enc_id'] = "required";
        $arr_rules['status'] = "required";
        
        $arr_response = [];

        $validator = Validator::make($request->all(),$arr_rules);
        
        if($validator->fails())
        {
            $arr_response['status'] = 'error';
            $arr_response['msg']    = 'Please fill all the required field.';
            return $arr_response;
        }

        $enc_id = $request->input('enc_id');
        $status = $request->input('status');

        $obj_deposit_money = $this->DepositMoneyModel->where('id',$enc_id)->first();
        if($obj_deposit_money)
        {
            $transaction_id = isset($obj_deposit_money->transaction_id) ? $obj_deposit_money->transaction_id :'';
            $obj_deposit_money->status = $status;
            $is_updated = $obj_deposit_money->save();
            if($is_updated)
            {
                $arr_data = $obj_deposit_money->toArray();
                
                $arr_notification_data = $this->built_notification_data($arr_data,'DRIVER_DEPOSIT_REQUEST'); 
                $this->NotificationsService->store_notification($arr_notification_data);
                
                $msg = '';
                $msg_status = '';
                if($status == 'APPROVED')
                {
                    $msg = 'You have successfully approved deposit money request.';
                    $msg_status = 'success';
                }
                else if($status == 'REJECTED')
                {
                    $msg = 'You have rejected deposit money request.';
                    $msg_status = 'error';
                }
                $arr_response['status'] = $msg_status;
                $arr_response['msg']    = $msg;
                return $arr_response;   
            }
            else
            {
                $arr_response['status'] = 'error';
                $arr_response['msg']    = 'Problem Occurred, processing deposit money request.';
                return $arr_response;
            }
        }
        $arr_response['status'] = 'error';
        $arr_response['msg']    = 'Something went wrong,Please try again.';
        return $arr_response;
    }
    
   
    public function get_driver_details($request)
    {
         $arr_data      = [];
         $arr_response  = [];
         $driver_id     = $request->input('driver_id');
         // dd($this->CommonDataService->encrypt_value($driver_id));
         // $driver_id     = $this->CommonDataService->decrypt_value($driver_id);

         $obj_user  = $this->UserModel
                                    ->select('id','first_name','last_name','mobile_no')
                                    ->with('driver_fair_charge_details')
                                    ->with('driver_car_details.vehicle_details')
                                    ->where('id',$driver_id)
                                    ->first();

         if ($obj_user) 
         {
            $arr_user = $obj_user->toArray();  
            $first_name = isset($arr_user['first_name'])?$arr_user['first_name']:'';
            $last_name  = isset($arr_user['last_name'])?$arr_user['last_name']:'';
            $full_name  = $first_name.' '.$last_name;
            $arr_data['driver_name']         = $full_name;
            $arr_data['mobile_no']           = $arr_user['mobile_no'];
            $arr_data['driver_fair_charge']  = $arr_user['driver_fair_charge_details']['fair_charge'];
            $arr_data['vehicle_name']        = $arr_user['driver_car_details']['vehicle_details']['vehicle_name'];
            $arr_data['vehicle_model_name']  = $arr_user['driver_car_details']['vehicle_details']['vehicle_model_name'];
            $arr_data['vehicle_number']      = $arr_user['driver_car_details']['vehicle_details']['vehicle_number'];

            $arr_response['status']    = 'success';
            $arr_response['msg']       = 'Driver details are found';
            $arr_response['arr_data']  = $arr_data;
            return $arr_response;             
         }
         else
         {
            $arr_response['status']    = 'error';
            $arr_response['msg']       = 'Driver details are not found.';
            $arr_response['arr_data']  = $arr_data;
            return $arr_response;                         
         }             
    }
    
    private function check_driver_current_trip_details($arr_driver_current_trip)
    {
        $driver_id  = isset($arr_driver_current_trip['driver_id']) ? $arr_driver_current_trip['driver_id'] :'';
        $lat        = isset($arr_driver_current_trip['lat']) ? $arr_driver_current_trip['lat'] :'';
        $lng        = isset($arr_driver_current_trip['lng']) ? $arr_driver_current_trip['lng'] :'';
        $client     = isset($arr_driver_current_trip['client']) ? $arr_driver_current_trip['client'] :null;

        $arr_booking_master = [];
       
        $obj_booking_master = $this->BookingMasterModel
                                        ->select('id','load_post_request_id','booking_status','is_eta_notification_send')
                                        ->whereHas('load_post_request_details',function($query) use ($driver_id){
                                            $query->where('driver_id',$driver_id);
                                            $query->where('request_status','ACCEPT_BY_USER');
                                        })
                                        ->with(['load_post_request_details'=>function($query){
                                            $query->select('id','user_id','driver_id','pickup_lat','pickup_lng');
                                            $query->with(['user_details'=>function($query){
                                                $query->select('id','first_name','last_name','country_code','mobile_no');
                                            }]);
                                            $query->with(['driver_details'=>function($query){
                                                $query->select('id','first_name','last_name','country_code','mobile_no');
                                            }]);
                                        }])
                                        ->whereIn('booking_status',['TO_BE_PICKED','IN_TRANSIT'])
                                        ->first();
        
        // dd($obj_booking_master->toArray());
        if($obj_booking_master!=null && $obj_booking_master != false){
            $arr_booking_master = $obj_booking_master->toArray();
            if(isset($arr_booking_master) && sizeof($arr_booking_master)>0){
                
                /*code for sending sms to user*/
                if(
                    (isset($arr_booking_master['booking_status']) && isset($arr_booking_master['is_eta_notification_send'])) && 
                    ($arr_booking_master['booking_status'] == 'TO_BE_PICKED') && ($arr_booking_master['is_eta_notification_send'] == '0')
                )
                {
                    $booking_id = isset($arr_booking_master['id']) ? $arr_booking_master['id'] : 0;
                    $pickup_lat = isset($arr_booking_master['load_post_request_details']['pickup_lat']) ? $arr_booking_master['load_post_request_details']['pickup_lat'] : '0';
                    $pickup_lng = isset($arr_booking_master['load_post_request_details']['pickup_lng']) ? $arr_booking_master['load_post_request_details']['pickup_lng'] : '0';
                    
                    $distance_in_meter = $this->findDistance($lat, $lng, $pickup_lat, $pickup_lng);

                    if($distance_in_meter<=400){
                        //send twilio sms eta notifications
                        if(isset($client)){
                            //send sms to user
                            $user_country_code   = isset($arr_booking_master['load_post_request_details']['user_details']['country_code']) ? $arr_booking_master['load_post_request_details']['user_details']['country_code'] :'';
                            $user_mobile_no      = isset($arr_booking_master['load_post_request_details']['user_details']['mobile_no']) ? $arr_booking_master['load_post_request_details']['user_details']['mobile_no'] :'';
                            $user_full_mobile_no = $user_country_code.''.$user_mobile_no;

                            if($user_full_mobile_no!=''){
                                
                                $driver_first_name  = isset($arr_booking_master['load_post_request_details']['driver_details']['first_name']) ? $arr_booking_master['load_post_request_details']['driver_details']['first_name'] :'';
                                $driver_last_name   = isset($arr_booking_master['load_post_request_details']['driver_details']['last_name']) ? $arr_booking_master['load_post_request_details']['driver_details']['last_name'] :'';
                                $driver_full_name   = $driver_first_name.' '.$driver_last_name;
                                
                                $messageBody         = $driver_full_name.' has arrived at the pickup location.';

                                $this->sendEtaNotificationsMessage(
                                                        $client,
                                                        $user_full_mobile_no,
                                                        $messageBody,
                                                        ''
                                                    );

                                $this->BookingMasterModel->where('id',$booking_id)->update(['is_eta_notification_send'=>'1']);
                            }                 
                        }
                    }
                }

                $arr_driver_current_trip['booking_id']     = isset($arr_booking_master['id']) ? $arr_booking_master['id'] : 0;
                $arr_driver_current_trip['booking_status'] = isset($arr_booking_master['booking_status']) ? $arr_booking_master['booking_status'] : '';
                
                $this->update_booking_coordinates($arr_driver_current_trip);
                return true;       
                // // if(isset($arr_booking_master['booking_status']) && $arr_booking_master['booking_status'] == 'IN_TRANSIT'){
                // // }

                // $arr_tmp_data                    = [];
                // $arr_tmp_data['booking_id']      = isset($arr_booking_master['id'])?$arr_booking_master['id']:0;
                // $arr_tmp_data['pickup_location'] = isset($arr_booking_master['load_post_request_details']['pickup_location'])?$arr_booking_master['load_post_request_details']['pickup_location']:'';
                // $arr_tmp_data['drop_location']   = isset($arr_booking_master['load_post_request_details']['drop_location'])?$arr_booking_master['load_post_request_details']['drop_location']:'';
                // $arr_tmp_data['pickup_lat']      = isset($arr_booking_master['load_post_request_details']['pickup_lat']) ? doubleval($arr_booking_master['load_post_request_details']['pickup_lat']) :doubleval(0);
                // $arr_tmp_data['pickup_lng']      = isset($arr_booking_master['load_post_request_details']['pickup_lng']) ? doubleval($arr_booking_master['load_post_request_details']['pickup_lng']) :doubleval(0);
                // $arr_tmp_data['drop_lat']        = isset($arr_booking_master['load_post_request_details']['drop_lat']) ? doubleval($arr_booking_master['load_post_request_details']['drop_lat']) :doubleval(0);
                // $arr_tmp_data['drop_lng']        = isset($arr_booking_master['load_post_request_details']['drop_lng']) ? doubleval($arr_booking_master['load_post_request_details']['drop_lng']) : doubleval(0);
                // $arr_tmp_data['trip_status']     = isset($arr_booking_master['booking_status'])?$arr_booking_master['booking_status']:'';
                // return $arr_tmp_data;
            }   
            else
            {
                return true;       
            }
        }
       
        return true;
    }

    private function sendEtaNotificationsMessage($client, $to, $messageBody, $callbackUrl)
    {

        $twilioNumber = config('app.project.twilio_credentials.from_user_mobile');
        try {
            $client->messages->create(
                $to, // Text any number
                [
                    'from' => $twilioNumber, // From a Twilio number in your account
                    'body' => $messageBody,
                    'statusCallback' => $callbackUrl
                ]
            );
            return true;
        } catch (\Exception $e) {
            return true;
        }
        return true;
    }

    private function findDistance($lat1, $lon1, $lat2, $lon2) {
        if (($lat1 == $lat2) && ($lon1 == $lon2)) {
            return 0;
        }
        else {
            $theta = $lon1 - $lon2;
            $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
            $dist = acos($dist);
            $dist = rad2deg($dist);
            $miles = $dist * 60 * 1.1515;
            if($miles>0){
                return round(($miles /0.00062137));
            }
            return 0;
        }
    }

    function findDistanceInKms($lat1, $lon1, $lat2, $lon2) {
      if (($lat1 == $lat2) && ($lon1 == $lon2)) {
        return 0;
      }
      else {
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        if($miles>0){
            // return round(($miles * 1.609344),3);
            return ($miles * 1.609344);
        }
        return 0;
      }
      return 0;
    }

    private function update_booking_coordinates($arr_driver_current_trip)
    {
        $booking_id     = isset($arr_driver_current_trip['booking_id']) ? $arr_driver_current_trip['booking_id'] :0;
        $booking_status = isset($arr_driver_current_trip['booking_status']) ? $arr_driver_current_trip['booking_status'] :0;
        $lat            = isset($arr_driver_current_trip['lat']) ? $arr_driver_current_trip['lat'] :'';
        $lng            = isset($arr_driver_current_trip['lng']) ? $arr_driver_current_trip['lng'] :'';

        $final_total_distance_in_km  = 0;

        if($booking_status == 'IN_TRANSIT')
        {
            $obj_booking_master_coordinate =  $this->BookingMasterCoordinateModel
                                                                        ->where('booking_master_id',$booking_id)
                                                                        ->first();

            // if($obj_booking_master_coordinate!=null && $obj_booking_master_coordinate!=false && count($obj_booking_master_coordinate)>0)
            if($obj_booking_master_coordinate!=null)
            {
                $tmp_start_location_lat = isset($obj_booking_master_coordinate->tmp_start_location_lat) ? $obj_booking_master_coordinate->tmp_start_location_lat : '';
                $tmp_start_location_lng = isset($obj_booking_master_coordinate->tmp_start_location_lng) ? $obj_booking_master_coordinate->tmp_start_location_lng : '';
                
                $distance_in_km = $this->findDistanceInKms($tmp_start_location_lat,$tmp_start_location_lng,$lat,$lng);
                
                if(!is_nan($distance_in_km))
                {
                    if($distance_in_km>=0.2)
                    {
                        /*if distance is grater than 500 meter then check from google map*/
                        if($distance_in_km>=0.5)
                        {
                            $origins      = $tmp_start_location_lat.",".$tmp_start_location_lng;
                            $destinations = $lat.",".$lng;
                            $google_map_distance_in_km = $this->calculate_distance_from_google_map($origins,$destinations);
                            
                            if($google_map_distance_in_km>0){
                                $distance_in_km = $google_map_distance_in_km;
                            }

                        }
                        $total_distance_in_km = isset($obj_booking_master_coordinate->total_distance_in_km) ? $obj_booking_master_coordinate->total_distance_in_km : 0;
                        
                        $final_total_distance_in_km = $total_distance_in_km + $distance_in_km;
                        
                        $arr_coordinates = [];
                        $arr_lat_lng = [
                                        'lat' => $lat,
                                        'lng' => $lng
                                    ];
                        
                        $str_coordinates = isset($obj_booking_master_coordinate->coordinates) ? $obj_booking_master_coordinate->coordinates : '';
                        if($str_coordinates!=''){
                            $arr_coordinates = json_decode($str_coordinates,true);
                        }
                        array_push($arr_coordinates, $arr_lat_lng);
                        
                        $obj_booking_master_coordinate->tmp_start_location_lat  = $lat;
                        $obj_booking_master_coordinate->tmp_start_location_lng  = $lng;
                        $obj_booking_master_coordinate->total_distance_in_km    = $final_total_distance_in_km;
                        $obj_booking_master_coordinate->coordinates             = json_encode($arr_coordinates);
                        $obj_booking_master_coordinate->save();                        
                    }
                }
            }
            else
            {
                $arr_coordinates = [];
                $arr_lat_lng = ['lat'=>$lat,'lng'=>$lng];
                array_push($arr_coordinates, $arr_lat_lng);

                $arr_create = 
                                [
                                    'booking_master_id'      => $booking_id,
                                    'start_location_lat'     => $lat,
                                    'start_location_lng'     => $lng,
                                    'tmp_start_location_lat' => $lat,
                                    'tmp_start_location_lng' => $lng,
                                    // 'end_location_lat'       => '',
                                    // 'end_location_lng'       => '',
                                    'total_distance_in_km'   => 0,
                                    'coordinates'            => json_encode($arr_coordinates),
                                    'map_image'              => ''
                                ];

                $this->BookingMasterCoordinateModel
                                    ->create($arr_create);
            }
        }
        return true;
    }

    private function calculate_distance_from_google_map($origins,$destinations)
    {
        try
        {
            $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=".$origins."&destinations=".$destinations."&key=AIzaSyCTScU19j-YU1Gt5xrFWlo4dwHoFF1wl-s";
       
            $json = @file_get_contents($url);
            $data = json_decode($json);
         
            $actual_distance_in_km = 0;

            if(isset($data->rows[0]->elements[0]->distance->value))
            {
                $actual_distance_in_meter = $data->rows[0]->elements[0]->distance->value;
                if($actual_distance_in_meter>0){
                    $actual_distance_in_km = $actual_distance_in_meter/1000;
                }
            }
            return $actual_distance_in_km;
        }
        catch (\Exception $e)
        {
            return 0;
        }

    }
    // private function update_booking_coordinates($arr_driver_current_trip)
    // {
    //     $booking_id     = isset($arr_driver_current_trip['booking_id']) ? $arr_driver_current_trip['booking_id'] :0;
    //     $booking_status = isset($arr_driver_current_trip['booking_status']) ? $arr_driver_current_trip['booking_status'] :0;
    //     $lat            = isset($arr_driver_current_trip['lat']) ? $arr_driver_current_trip['lat'] :'';
    //     $lng            = isset($arr_driver_current_trip['lng']) ? $arr_driver_current_trip['lng'] :'';

    //     if($lat!=0 && $lng!=0){
    //         $arr_lat_lng_data = [];
    //         $arr_tmp_data = [
    //                             'lat' => floatval($lat),
    //                             'lng' => floatval($lng)
    //                         ];

    //         if($booking_status == 'IN_TRANSIT'){
    //             $trip_file_name = 'trip_lat_lng_'.$booking_id.'.json';
    //             $trip_file_path = $this->trip_lat_lng_base_img_path.'/'.$trip_file_name;
                   
    //             if(file_exists($trip_file_path)){
    //                 $trip_file_contents = file_get_contents($trip_file_path);
    //                 $arr_lat_lng_data = json_decode($trip_file_contents,true);
    //                 if(isset($arr_lat_lng_data) && count($arr_lat_lng_data)>0){
    //                     /*if file is not empty then push data old file*/
    //                     array_push($arr_lat_lng_data,$arr_tmp_data);
    //                     $arr_lat_lng_json_data = json_encode($arr_lat_lng_data);
    //                     file_put_contents($trip_file_path, $arr_lat_lng_json_data);
    //                 }
    //                 else{
    //                     /*if file is empty then update data to file*/
    //                     $arr_lat_lng_data = [];
    //                     array_push($arr_lat_lng_data, $arr_tmp_data);
                        
    //                     $arr_lat_lng_json_data = json_encode($arr_lat_lng_data);
    //                     file_put_contents($trip_file_path, $arr_lat_lng_json_data);
    //                 }
    //             }
    //             else {

    //                 array_push($arr_lat_lng_data, $arr_tmp_data);
                    
    //                 /* Create new json file and write content in file */
    //                 $fp = fopen($trip_file_path, 'w');
    //                 fwrite($fp,json_encode($arr_lat_lng_data));
    //                 fclose($fp);
    //             }
    //         }
    //     }
    //     return true;
    // }

    

    private function built_mail_data($arr_data)
    {
        if(isset($arr_data) && sizeof($arr_data)>0)
        {
            $arr_built_content = [
                                  'FIRST_NAME'       => $arr_data['first_name'],
                                  'LAST_NAME'        => $arr_data['last_name'],
                                  'EMAIL'            => $arr_data['email'],
                                  'PASSWORD'         => $arr_data['password'],
                                  'PROJECT_NAME'     => config('app.project.name')];


            $arr_mail_data                      = [];
            $arr_mail_data['email_template_id'] = '10';
            $arr_mail_data['arr_built_content'] = $arr_built_content;
            $arr_mail_data['user']              = $arr_data;

            return $arr_mail_data;
        }
        return FALSE;
    }
    private function built_notification_data($arr_data,$type)
    {
        $arr_notification = [];
        if(isset($arr_data) && sizeof($arr_data)>0)
        {
            $arr_notification['user_id']           = $this->CommonDataService->get_admin_id();
            $arr_notification['is_read']           = 0;
            $arr_notification['is_show']           = 0;
            $arr_notification['user_type']         = 'ADMIN';

            $first_name = $last_name = $full_name = '';
            if($type == 'DRIVER')
            {
                $first_name = isset($arr_data['first_name']) ? $arr_data['first_name'] :'';
                $last_name  = isset($arr_data['last_name']) ? $arr_data['last_name'] :'';
                $full_name  = $first_name.' '.$last_name;
                $full_name  = ($full_name!=' ') ? $full_name : '-';
                
                $arr_notification['notification_type'] = 'Driver Registration';

                $arr_notification['title']             = $full_name.' register as a Driver on Quickpick.';
                $arr_notification['view_url']          = '/'.config('app.project.admin_panel_slug')."/driver";
            }
            if($type == 'DRIVER_FAIR_CHARGE_REQUEST')
            {
                $driver_id = isset($arr_data['driver_id']) ? $arr_data['driver_id'] :0;

                $arr_driver = $this->CommonDataService->get_user_details($driver_id);

                $first_name = isset($arr_driver['first_name']) ? $arr_driver['first_name'] :'';
                $last_name  = isset($arr_driver['last_name']) ? $arr_driver['last_name'] :'';

                $full_name  = $first_name.' '.$last_name;
                $full_name  = ($full_name!=' ') ? $full_name : '-';
                
                $arr_notification['notification_type'] = 'Driver fare charge request';
                $arr_notification['title']             = $full_name.' send a new fare charge request.';
                $arr_notification['view_url']          = '/'.config('app.project.admin_panel_slug')."/driver/fair_charge_request".'/'.base64_encode($driver_id);
            }
            if($type == 'DRIVER_DEPOSIT_REQUEST')
            {
                $driver_id  = isset($arr_data['to_user_id']) ? $arr_data['to_user_id'] :0;

                $arr_driver = $this->CommonDataService->get_user_details($driver_id);


                $first_name = isset($arr_driver['first_name']) ? $arr_driver['first_name'] :'';
                $last_name  = isset($arr_driver['last_name']) ? $arr_driver['last_name'] :'';

                $full_name  = $first_name.' '.$last_name;
                $full_name  = ($full_name!=' ') ? $full_name : '-';
                
                $title = $notification_type = '';

                $status         = isset($arr_data['status']) ? $arr_data['status'] :'';
                $transaction_id = isset($arr_data['transaction_id']) ? $arr_data['transaction_id'] :'';

                if($status == 'APPROVED')
                {
                    $title = $full_name.' approved deposited payment receipt #'.$transaction_id;
                    $notification_type = 'Driver Payment Receipt Approved';
                }
                else if($status == 'REJECTED')
                {
                    $title = $full_name.' rejected deposited payment receipt #'.$transaction_id;
                    $notification_type = 'Driver Payment Receipt Rejected';
                }

                $arr_notification['notification_type'] = $notification_type;
                $arr_notification['title']             = $title;
                $arr_notification['view_url']          = '/'.config('app.project.admin_panel_slug')."/driver/deposit_receipt".'/'.base64_encode($driver_id);
            }
            if($type == 'DRIVER_PAYMENT_STATUS')
            {
                $driver_id  = isset($arr_data['driver_id']) ? $arr_data['driver_id'] :0;
                $transaction_id  = (isset($arr_data['transaction_id']) && ($arr_data['transaction_id'] != '')) ? $arr_data['transaction_id'] :0;
                
                if($arr_data['status'] == 'APPROVED'){
                    $status  = 'Approved';
                }else{
                    $status  = 'Unpproved';
                }

                $arr_driver = $this->CommonDataService->get_user_details($driver_id);

                $first_name = isset($arr_driver['first_name']) ? $arr_driver['first_name'] :'';
                $last_name  = isset($arr_driver['last_name']) ? $arr_driver['last_name'] :'';

                $full_name  = $first_name.' '.$last_name;
                $full_name  = ($full_name!=' ') ? $full_name : '-';
                
                $arr_notification['notification_type'] = 'Driver Payment';
                //$arr_notification['title']             = $full_name.' payment '.$status.' of the #'. $transaction_id;
                $arr_notification['title']             = '#'.$transaction_id.' '.$status.' by '. $full_name;
                $arr_notification['view_url']          = '/'.config('app.project.admin_panel_slug')."/driver";
            }
            if($type == 'VEHICLE_DETAILS_UPDATE')
            {
                $driver_id = isset($arr_data['driver_id']) ? $arr_data['driver_id'] :0;

                $arr_driver = $this->CommonDataService->get_user_details($driver_id);

                $first_name = isset($arr_driver['first_name']) ? $arr_driver['first_name'] :'';
                $last_name  = isset($arr_driver['last_name']) ? $arr_driver['last_name'] :'';

                $full_name  = $first_name.' '.$last_name;
                $full_name  = ($full_name!=' ') ? $full_name : '-';
                
                $arr_notification['notification_type'] = 'Driver update vehicle details.';
                $arr_notification['title']             = $full_name.' updated vehicle details,Check document and verify  vehicle.';
                $arr_notification['view_url']          = '/'.config('app.project.admin_panel_slug')."/driver_vehicle?vehicles_type=individual";
            }
        }   
        return $arr_notification;
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
            
                $driver_total_amount   = (floatval($driver_total_amount) + floatval($driver_earning_amount));

                // $arr_data = filter_completed_trip_details($value);

                // $driver_earning_amount = isset($arr_data['driver_earning_amount']) ? $arr_data['driver_earning_amount'] :0;
                // $driver_total_amount   = (floatval($driver_total_amount) + floatval($driver_earning_amount));
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

}   
?>