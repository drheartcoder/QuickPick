<?php

namespace App\Common\Services\Web;

use App\Models\UserModel;

use App\Models\DriverCarRelationModel;
use App\Models\DriverFairChargeModel;

use App\Models\DepositMoneyModel;

use App\Models\VehicleModel;
use App\Models\BookingMasterModel;
use App\Models\LoadPostRequestModel;
use App\Models\LoadPostRequestHistoryModel;

use App\Common\Services\NotificationsService;
use App\Common\Services\CommonDataService;

use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;

use Validator;
use Sentinel;

class DriverService
{
    public function __construct(
                                    UserModel $user,
                                    DriverCarRelationModel $driver_car_relation,
                                    DriverFairChargeModel $driver_fair_charge,
                                    DepositMoneyModel $deposit_money,

                                    VehicleModel $vehicle,
                                    LoadPostRequestModel $load_post_request,
                                    LoadPostRequestHistoryModel $load_post_request_history,
                                    BookingMasterModel $booking_master,

                                    CommonDataService $common_data_service,
                                    NotificationsService $notifications_service
                               )
    {

        $this->UserModel                    = $user;

        $this->DriverCarRelationModel       = $driver_car_relation;
        $this->DepositMoneyModel            = $deposit_money;


        $this->VehicleModel                 = $vehicle;
        $this->LoadPostRequestModel         = $load_post_request;
        $this->LoadPostRequestHistoryModel  = $load_post_request_history;
        $this->BookingMasterModel           = $booking_master;
        $this->DriverFairChargeModel        = $driver_fair_charge;

        $this->NotificationsService         = $notifications_service;
        $this->CommonDataService            = $common_data_service;

        $this->user_profile_public_img_path           = url('/').config('app.project.img_path.user_profile_images');
        $this->user_profile_base_img_path             = base_path().config('app.project.img_path.user_profile_images');
        
        $this->driver_deposit_receipt_public_img_path = url('/').config('app.project.img_path.driver_deposit_receipt');
        $this->driver_deposit_receipt_base_img_path   = base_path().config('app.project.img_path.driver_deposit_receipt');

        $this->vehicle_doc_public_path = url('/').config('app.project.img_path.vehicle_doc');
        $this->vehicle_doc_base_path   = base_path().config('app.project.img_path.vehicle_doc');

        $this->load_post_img_public_path    = url('/').config('app.project.img_path.load_post_img');
        $this->load_post_img_base_path      = base_path().config('app.project.img_path.load_post_img');
        
        $this->receipt_image_public_path = url('/').config('app.project.img_path.payment_receipt');
        $this->receipt_image_base_path   = base_path().config('app.project.img_path.payment_receipt');

        $this->driving_license_public_path = url('/').config('app.project.img_path.driving_license');
        $this->driving_license_base_path   = base_path().config('app.project.img_path.driving_license');

        $this->invoice_public_img_path = url('/').config('app.project.img_path.invoice');
        $this->invoice_base_img_path   = base_path().config('app.project.img_path.invoice');
        
        $this->per_page = 5;
        $this->distance = 20;

    }
    public function get_vehicle_details()
    {   
        $arr_response = [];
        $driver_id    = validate_user_login_id();
        if ($driver_id == 0) 
        {
            $arr_response['status'] = 'error';
            $arr_response['msg']    = 'Invalid user token';
            $arr_response['data']   = [];
            return $arr_response;        
        }
        
        $obj_driver_car_relation  = $this->DriverCarRelationModel
                                                ->with(['vehicle_details' => function($query){
                                                    //$query->select('id','vehicle_type_id','is_individual_vehicle','vehicle_brand_id','vehicle_model_id','vehicle_year_id','vehicle_number','is_verified','vehicle_image','registration_doc','proof_of_inspection_doc','insurance_doc','dmv_driving_record','usdot_doc','is_deleted');
                                                    $query->with(['vehicle_brand_details','vehicle_model_details']);
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
            
            
            $arr_vehicle_details['vehicle_type'] = isset($arr_vehicle_details['vehicle_type_details']['vehicle_type']) ? $arr_vehicle_details['vehicle_type_details']['vehicle_type'] :'';
            $arr_vehicle_details['is_usdot_required'] = isset($arr_vehicle_details['vehicle_type_details']['is_usdot_required']) ? $arr_vehicle_details['vehicle_type_details']['is_usdot_required'] :'';
            $arr_vehicle_details['is_mcdoc_required'] = isset($arr_vehicle_details['vehicle_type_details']['is_mcdoc_required']) ? $arr_vehicle_details['vehicle_type_details']['is_mcdoc_required'] :'';
            unset($arr_vehicle_details['vehicle_type_details']);
            
            $driving_license_orginal_path = $driving_license = $vehicle_image = $registration_doc = $insurance_doc = $proof_of_inspection_doc = $dmv_driving_record = $usdot_doc = $mc_doc = '';

            $is_driving_license_verified = isset($arr_driver_car_relation['driver_details']['is_driving_license_verified']) ? $arr_driver_car_relation['driver_details']['is_driving_license_verified'] : '';

            if(isset($arr_driver_car_relation['driver_details']['driving_license']) && $arr_driver_car_relation['driver_details']['driving_license']!=''){
                $tmp_driving_license = isset($arr_driver_car_relation['driver_details']['driving_license']) ? $arr_driver_car_relation['driver_details']['driving_license'] :'';
                if(file_exists($this->driving_license_base_path.$tmp_driving_license))
                {
                    $driving_license = $this->driving_license_public_path.$tmp_driving_license;
                } 
                $driving_license_orginal_path =   $tmp_driving_license;
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

            $arr_vehicle_details['driver_id']                    = isset($arr_driver_car_relation['driver_id']) ? $arr_driver_car_relation['driver_id'] : 0;
            $arr_vehicle_details['driving_license']              = $driving_license;
            $arr_vehicle_details['driving_license_orginal_path'] = $driving_license_orginal_path;
            $arr_vehicle_details['is_driving_license_verified']  = $is_driving_license_verified;
            $arr_vehicle_details['vehicle_insurance_doc_path']   = $insurance_doc;
            $arr_vehicle_details['proof_of_inspection_doc_path'] = $proof_of_inspection_doc;
            $arr_vehicle_details['vehicle_image_path']           = $vehicle_image;
            $arr_vehicle_details['registration_doc_path']        = $registration_doc;
            $arr_vehicle_details['dmv_driving_record_path']      = $dmv_driving_record;
            $arr_vehicle_details['usdot_doc_path']               = $usdot_doc;
            $arr_vehicle_details['mcdoc_doc_path']               = $mc_doc;
            
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
        // dd($request->all());
        $arr_response = [];
        $driver_id    = validate_user_login_id();
        if ($driver_id == 0) 
        {
            $arr_response['status'] = 'error';
            $arr_response['msg']    = 'Invalid user token';
            $arr_response['data']   = [];
            return $arr_response;        
        }

        /*
        $arr_rules                       = [];
        $arr_rules['vehicle_type_id']    = "required";
        $arr_rules['vehicle_brand_id']   = "required";
        $arr_rules['vehicle_model']      = "required";
        $arr_rules['vehicle_number']     = "required";
        
        
        $validator = Validator::make($request->all(),$arr_rules);
        if($validator->fails())
        {
            $arr_response['status'] = 'error';
            $arr_response['msg']    = 'Please fill all the required field';
            $arr_response['data']   = [];
            return $arr_response;
        }
        */

        $vehicle_id = $request->input('vehicle_id');
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
                $vehicle_model_id = $request->input('vehicle_model');
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
            $arr_update_vehicle['vehicle_brand_id']      = $request->input('vehicle_brand_id');
            $arr_update_vehicle['vehicle_model_id']      = $request->input('vehicle_model');
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

                $arr_update_vehicle['mc_doc']                              = '';
                $arr_update_vehicle['is_mcdoc_doc_verified']               = 'NOTAPPROVED';

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
                $driving_license = '';
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
                // $vehicle_image = $request->input('vehicle_image');
                $vehicle_image = '';
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
                // $registration_doc = $request->input('registration_doc');
                $registration_doc = '';
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
                $insurance_doc = '';
                // dd($request->file('vehicle_insurance_doc'));
                $file_extension = strtolower($request->file('vehicle_insurance_doc')->getClientOriginalExtension());

                if(in_array($file_extension,['png','jpg','jpeg','pdf','heic','HEIC']))
                {
                    $insurance_doc = time().uniqid().'.'.$file_extension;
                    $isUpload = $request->file('vehicle_insurance_doc')->move($this->vehicle_doc_base_path , $insurance_doc);
                    $arr_update_vehicle['insurance_doc'] = $insurance_doc;
                    $arr_update_vehicle['is_vehicle_image_verified'] = 'PENDING';
                    if($isUpload && isset($obj_vehicle_details->insurance_doc) && $obj_vehicle_details->insurance_doc!=''){
                        if(file_exists($this->vehicle_doc_base_path.$obj_vehicle_details->insurance_doc)){
                            @unlink($this->vehicle_doc_base_path.$obj_vehicle_details->insurance_doc);
                        }
                    }

                }
            }

            if($request->hasFile('proof_of_inspection_doc'))
            {
                // $proof_of_inspection_doc = $request->input('proof_of_inspection_doc');
                $proof_of_inspection_doc = '';
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
                //$dmv_driving_record = $request->input('dmv_driving_record');
                $dmv_driving_record = '';
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
                // $insurance_doc = $request->input('usdot_doc');
                $insurance_doc = '';
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
                $arr_notification_data = $this->built_notification_data(['driver_id' => $driver_id,'vehicle_id'=>$vehicle_id],'VEHICLE_DETAILS_UPDATE'); 
                $this->NotificationsService->store_notification($arr_notification_data);
                
                $arr_response['status'] = 'success';
                $arr_response['msg']    = 'Vehicle details updated successfully, Vehicle verification request successfully sent to Admin.';
                $arr_response['data']   = ['is_make_all_document_clear'=>$is_make_all_document_clear];
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
    
    public function get_filter_trips($request,$trip_type = false)
    {
        $arr_response = [];
        $enc_user_id     = validate_user_login_id();
        if ($enc_user_id == 0) 
        {
            $arr_response['status'] = 'error';
            $arr_response['msg']    = 'Invalid driver token';
            $arr_response['data']    = [];
            return $arr_response;
        }

        if($trip_type == false)
        {
            $trip_type = 'COMPLETED';
        }

        if($request->has('trip_type') && $request->input('trip_type')!='')
        {
            $trip_type = $request->input('trip_type');
        }
        
        $arr_trips = [];
        $arr_trip_status = ['PENDING','COMPLETED','CANCELED'];
       
        if(!in_array($trip_type, $arr_trip_status)){
            
            $arr_response['status'] = 'error';
            $arr_response['msg']    = 'Invalid request type';
            $arr_response['data']    = [];
            return $arr_response;

        }   

        $arr_cancel_load_post = [];
        if($trip_type == 'PENDING')
        {
            $driver_id = $enc_user_id;
            $date = new \DateTime();
        
            $date->modify('-24 hours');
            $formatted_date = $date->format('Y-m-d H:i:s');
            
            $arr_vehicle_type_details = $this->CommonDataService->get_driver_vehicle_type_details($driver_id);
            $driver_mobile_no   = isset($arr_vehicle_type_details['driver_details']['mobile_no']) ? $arr_vehicle_type_details['driver_details']['mobile_no'] :'';  
            $vehicle_type_id    = isset($arr_vehicle_type_details['vehicle_details']['vehicle_type_details']['id']) ? $arr_vehicle_type_details['vehicle_details']['vehicle_type_details']['id'] :0;
     
            $arr_pending_load_post = [];
            
            $arr_driver_current_lat_lng =  $this->CommonDataService->get_driver_current_lat_lng_details($driver_id);
            
            $driver_current_lat  = isset($arr_driver_current_lat_lng['current_latitude']) ? $arr_driver_current_lat_lng['current_latitude'] : 0;
            $driver_current_lng = isset($arr_driver_current_lat_lng['current_longitude']) ? $arr_driver_current_lat_lng['current_longitude'] : 0;

            $obj_pending_load_post  = $this->LoadPostRequestModel
                                                    ->select(\DB::raw('ROUND((
                                                                              6371 * ACOS(
                                                                                COS(RADIANS('.$driver_current_lat.')) * COS(RADIANS(pickup_lat)) * COS(
                                                                                  RADIANS(pickup_lng) - RADIANS('.$driver_current_lng.')
                                                                                ) + SIN(RADIANS('.$driver_current_lat.')) * SIN(RADIANS(pickup_lat))
                                                                              )
                                                                            ),2) AS driver_distance
                                                                          '),'id','user_id','driver_id','created_at','request_status','pickup_location','pickup_lat','pickup_lng','drop_location','is_future_request','is_request_process')
                                                    
                                                    ->whereHas('load_post_request_history_details',function($query){
                                                            $query->whereIn('status',['USER_REQUEST','TIMEOUT','ACCEPT_BY_DRIVER','REJECT_BY_DRIVER']);
                                                    })
                                                    ->whereHas('load_post_request_package_details',function($query) use($vehicle_type_id){
                                                        $query->where('selected_vehicle_type_id', $vehicle_type_id);
                                                    })
                                                    ->with('load_post_request_package_details')
                                                    ->with(['load_post_request_history_details'=>function($query){
                                                            $query->whereIn('status',['USER_REQUEST','TIMEOUT','REJECT_BY_DRIVER']);
                                                    }])
                                                    ->with(['user_details'=>function($query){
                                                        $query->select('id','first_name','last_name','profile_image','mobile_no');
                                                    }])
                                                    ->whereIn('request_status',['USER_REQUEST','TIMEOUT','REJECT_BY_USER','ACCEPT_BY_DRIVER','REJECT_BY_DRIVER'])
                                                    ->where('created_at', '>',$formatted_date) /*latest 24 hours records will be shown */
                                                    ->having('driver_distance','<=',$this->distance)
                                                    ->where('is_admin_assistant','NO')
                                                    ->orderBy('id','DESC')
                                                    ->get();

            if($obj_pending_load_post)
            {
                $arr_tmp_pending_load_post = $obj_pending_load_post->toArray();
            }
            /*filter data based on cutom array*/
            if(isset($arr_tmp_pending_load_post) && sizeof($arr_tmp_pending_load_post)>0)
            {
                foreach ($arr_tmp_pending_load_post as $main_key => $main_value) 
                {
                    $is_record_show = 'yes';
                    if(isset($main_value['load_post_request_history_details']) && sizeof($main_value['load_post_request_history_details'])>0)
                    {
                        foreach ($main_value['load_post_request_history_details'] as $sub_key => $sub_value) 
                        {
                            if(isset($sub_value['driver_id']) && ($sub_value['driver_id']===$driver_id))
                            {
                                if(isset($sub_value['status']) && $sub_value['status'] == 'REJECT_BY_DRIVER'){
                                    $is_record_show = 'no';
                                }
                            }
                        }
                    }
                    
                    if($is_record_show == 'no'){
                        unset($arr_tmp_pending_load_post[$main_key]);
                    }
                    if((isset($main_value['is_future_request']) && $main_value['is_future_request'] == '1') && (isset($main_value['is_request_process']) && $main_value['is_request_process'] == '0')){
                        unset($arr_tmp_pending_load_post[$main_key]);
                    }
                    if(isset($main_value['user_details']['mobile_no']) && $main_value['user_details']['mobile_no']!='')
                    {
                        if($main_value['user_details']['mobile_no'] == $driver_mobile_no){
                            unset($arr_tmp_pending_load_post[$main_key]);
                        }
                    }
                    
                }
            }

            $arr_pending_load_post = [];

            $arr_pending_load_post = array_values($arr_tmp_pending_load_post);
            
            // dd($arr_pending_load_post);

            if(isset($arr_pending_load_post) && sizeof($arr_pending_load_post)>0){
                foreach ($arr_pending_load_post as $key => $value) 
                {
                    if(isset($value['request_status']) && ($value['request_status'] == 'REJECT_BY_DRIVER' || $value['request_status'] == 'TIMEOUT')){
                        $arr_pending_load_post[$key]['request_status'] = 'USER_REQUEST';
                    }
                    $arr_pending_load_post[$key]['first_name'] = isset($value['user_details']['first_name']) ? $value['user_details']['first_name'] :'';
                    $arr_pending_load_post[$key]['last_name']  = isset($value['user_details']['last_name']) ? $value['user_details']['last_name'] :'';
                    $profile_image = url('/uploads/default-profile.png');
                    if(isset($value['user_details']['profile_image']) && $value['user_details']['profile_image']!=''){
                        if(file_exists($this->user_profile_base_img_path.$value['user_details']['profile_image'])){
                            $profile_image = $this->user_profile_public_img_path.$value['user_details']['profile_image'];
                        }
                    }
                    $arr_pending_load_post[$key]['profile_image']  = $profile_image;
                    
                    $datetime1   = new \DateTime();
                    $datetime2   = isset($value['created_at']) ? new \DateTime($value['created_at']) :'';

                    $arr_pending_load_post[$key]['time_ago']  = get_time_difference($datetime1,$datetime2);

                    unset($arr_pending_load_post[$key]['user_details']);
                    unset($arr_pending_load_post[$key]['created_at']);
                    unset($arr_pending_load_post[$key]['user_id']);
                    unset($arr_pending_load_post[$key]['driver_id']);
                    unset($arr_pending_load_post[$key]['driver_distance']);
                    // unset($arr_pending_load_post[$key]['request_status']);
                    unset($arr_pending_load_post[$key]['load_post_request_history_details']);
                    unset($arr_pending_load_post[$key]['load_post_request_package_details']);
                }
                
                $arr_paginate_pending_load_post = [];

                $obj_paginate_pending_load_post =  $this->make_pagination_links($arr_pending_load_post,$this->per_page);
                if($obj_paginate_pending_load_post)
                {
                    $arr_paginate_pending_load_post = $obj_paginate_pending_load_post->toArray();
                }
                
                if(isset($arr_paginate_pending_load_post['data']) && sizeof($arr_paginate_pending_load_post['data'])>0)
                {
                    $arr_paginate_pending_load_post['data'] = array_values($arr_paginate_pending_load_post['data']);
                    $arr_response['status'] = 'success';
                    $arr_response['msg']    = 'pending request available.';
                    $arr_response['data']    = $arr_paginate_pending_load_post;
                    return $arr_response;
                }
                else
                {
                    $arr_response['status'] = 'error';
                    $arr_response['msg']    = 'No pending request available.';
                    $arr_response['data']    = [];
                    return $arr_response;
                }

            }
            $arr_response['status'] = 'error';
            $arr_response['msg']    = 'No pending request available.';
            $arr_response['data']    = [];
            return $arr_response;
        }
        if($trip_type == 'CANCELED')
        {
            $obj_cancel_load_post  = $this->LoadPostRequestModel
                                        ->with(['load_post_request_package_details',
                                                'driver_details'=>function($query){
                                                        $query->select('id','first_name','last_name','email','country_code','mobile_no','profile_image');
                                                    },
                                                'user_details'=>function($query){
                                                        $query->select('id','first_name','last_name','email','country_code','mobile_no','profile_image');
                                                    },
                                                'driver_current_location_details'=>function($query){
                                                        $query->select('id','driver_id','status','current_latitude','current_longitude');
                                                    }
                                                ])
                                            ->select("id","user_id","driver_id","date as booking_date","pickup_location","drop_location","pickup_lat","pickup_lng","drop_lat","drop_lng","load_post_image","request_status as booking_status","updated_at");
            
            $obj_cancel_load_post = $obj_cancel_load_post
                                            ->with(['load_post_request_history_details'=>function($query) use ($enc_user_id) {
                                                $query->where('driver_id',$enc_user_id);
                                                $query->whereIn('status',['REJECT_BY_DRIVER','REJECT_BY_USER']);
                                            }])
                                            ->whereHas('load_post_request_history_details',function($query) use ($enc_user_id) {
                                                $query->where('driver_id',$enc_user_id);
                                                $query->whereIn('status',['REJECT_BY_DRIVER','REJECT_BY_USER']);
                                            }); 
        
            $obj_cancel_load_post = $obj_cancel_load_post
                                        ->orderBy('id','DESC')
                                        ->get();

            if($obj_cancel_load_post)
            {
                $arr_cancel_load_post = $obj_cancel_load_post->toArray();
            }

            if(isset($arr_cancel_load_post) && count($arr_cancel_load_post)>0)
            {
                foreach ($arr_cancel_load_post as $load_post_key => $load_post_value) 
                {
                    $arr_cancel_load_post[$load_post_key]['type'] = 'load_post';
                }
            }
        }
        
        $obj_trips  = $this->BookingMasterModel
                                    ->select('id','load_post_request_id','booking_unique_id','booking_date','booking_status','total_charge','updated_at')
                                    ->whereHas('load_post_request_details',function($query) use($enc_user_id) {
                                            $query->whereHas('driver_details',function($query){
                                            });
                                            $query->whereHas('user_details',function($query){
                                            });
                                            $query->whereHas('driver_current_location_details',function($query){
                                            });
                                            $query->where('request_status','ACCEPT_BY_USER');
                                            $query->where('driver_id',$enc_user_id);
                                            
                                    })
                                    ->with(['load_post_request_details'=> function($query) use($enc_user_id) {
                                            $query->select('id','user_id','driver_id','pickup_location','drop_location','pickup_lat','pickup_lng','drop_lat','drop_lng','request_status','load_post_image');
                                            $query->where('request_status','ACCEPT_BY_USER');
                                            $query->where('driver_id',$enc_user_id);
                                            
                                            $query->with(['driver_details'=>function($query){
                                                        $query->select('id','first_name','last_name','email','country_code','mobile_no','profile_image');
                                                    }]);
                                            $query->with(['user_details'=>function($query){
                                                        $query->select('id','first_name','last_name','email','country_code','mobile_no','profile_image');
                                                    }]);
                                            $query->with(['driver_current_location_details'=>function($query){
                                                        $query->select('id','driver_id','status','current_latitude','current_longitude');
                                                    },'load_post_request_package_details']);
                                    },'review_details'=>function($query) use($enc_user_id){
                                        $query->where('from_user_id',$enc_user_id);
                                    }]);

        
        if($trip_type == 'COMPLETED')
        {
            $obj_trips = $obj_trips->where('booking_status','COMPLETED');
        }
        else if($trip_type == 'CANCELED')
        {
            $obj_trips = $obj_trips->whereIn('booking_status',['CANCEL_BY_USER','CANCEL_BY_DRIVER','CANCEL_BY_ADMIN']);
            $obj_trips = $obj_trips->orderBy('id','DESC');
            $obj_trips = $obj_trips->get();
            
            $arr_tmp_trips = [];
            if($obj_trips)
            {
                $arr_tmp_trips = $obj_trips->toArray();
                if(isset($arr_tmp_trips) && count($arr_tmp_trips)>0)
                {
                    foreach ($arr_tmp_trips as $tmp_key => $tmp_value) 
                    {
                        $arr_tmp_trips[$tmp_key]['type'] = 'normal_booking';
                    }
                }
            }
            
            $arr_all_cancel_trips = array_merge($arr_cancel_load_post,$arr_tmp_trips);
            if(count($arr_all_cancel_trips)>0){
                usort($arr_all_cancel_trips, function($a, $b){
                      $t1 = strtotime($a['updated_at']);
                      $t2 = strtotime($b['updated_at']);
                      return $t1 < $t2;
                });
            }
            
            $obj_all_cancel_trips = $this->make_pagination_links($arr_all_cancel_trips,$this->per_page);

            if($obj_all_cancel_trips)
            {
                $arr_trips = $obj_all_cancel_trips->toArray();
                
                $arr_pagination = $obj_all_cancel_trips->appends(['trip_type'=>$trip_type])->links();
                $arr_trips['data'] = array_values($arr_trips['data']);
                $arr_trips['arr_pagination'] = $arr_pagination;

            }
            $arr_data = $this->filter_canceled_trips($arr_trips);
            return $arr_data;
        }
        else
        {
            /*if any of status not found then  return back with empty array */
            // return [];
            $arr_response['status'] = 'error';
            $arr_response['msg']    = 'trips details not available.';
            $arr_response['data']    = [];
            return $arr_response;

        }
        $obj_trips = $obj_trips->orderBy('id','DESC');
        $obj_trips = $obj_trips->paginate($this->per_page);
        
        $arr_pagination = [];

        if($obj_trips)
        {
            // $arr_pagination = $obj_trips->render();
            $arr_trips = $obj_trips->toArray();
            $arr_pagination = $obj_trips->appends(['trip_type'=>$trip_type])->links();
        }
        
        if(isset($arr_trips['data']) && sizeof($arr_trips['data'])>0)
        {
            foreach ($arr_trips['data'] as $key => $value) 
            {
                // dd($value);
                $arr_trips['data'][$key]['booking_status'] = isset($value['booking_status']) ? $value['booking_status'] : '';
                $arr_trips['data'][$key]['booking_date']   = isset($value['booking_date']) ? date('d M Y',strtotime($value['booking_date'])) : '';
                
                $arr_trips['data'][$key]['first_name']     = isset($value['load_post_request_details']['user_details']['first_name']) ? $value['load_post_request_details']['user_details']['first_name'] :'';
                $arr_trips['data'][$key]['last_name']      = isset($value['load_post_request_details']['user_details']['last_name']) ? $value['load_post_request_details']['user_details']['last_name'] :'';
                $profile_image = url('/uploads/default-profile.png');
                if(isset($value['load_post_request_details']['user_details']['profile_image']) && $value['load_post_request_details']['user_details']['profile_image']!=''){
                    if(file_exists($this->user_profile_base_img_path.$value['load_post_request_details']['user_details']['profile_image'])){
                        $profile_image = $this->user_profile_public_img_path.$value['load_post_request_details']['user_details']['profile_image'];
                    }
                }
                
                $arr_trips['data'][$key]['profile_image']     = $profile_image;
                
                $country_code   = isset($value['load_post_request_details']['user_details']['country_code']) ? $value['load_post_request_details']['user_details']['country_code'] : '';
                $mobile_no      = isset($value['load_post_request_details']['user_details']['mobile_no']) ? $value['load_post_request_details']['user_details']['mobile_no'] : '';
                $full_mobile_no = $country_code.''.$mobile_no;
                $full_mobile_no = ($full_mobile_no!='')?$full_mobile_no :'';

                $arr_trips['data'][$key]['mobile_no'] = $full_mobile_no;


                $arr_trips['data'][$key]['pickup_location']   = isset($value['load_post_request_details']['pickup_location']) ? $value['load_post_request_details']['pickup_location'] :'';
                $arr_trips['data'][$key]['drop_location']     = isset($value['load_post_request_details']['drop_location']) ? $value['load_post_request_details']['drop_location'] :'';
                $arr_trips['data'][$key]['pickup_lat']        = isset($value['load_post_request_details']['pickup_lat']) ? doubleval($value['load_post_request_details']['pickup_lat']) :doubleval(0.0);
                $arr_trips['data'][$key]['pickup_lng']        = isset($value['load_post_request_details']['pickup_lng']) ? doubleval($value['load_post_request_details']['pickup_lng']) :doubleval(0.0);
                $arr_trips['data'][$key]['drop_lat']          = isset($value['load_post_request_details']['drop_lat']) ? doubleval($value['load_post_request_details']['drop_lat']) :doubleval(0.0);
                $arr_trips['data'][$key]['drop_lng']          = isset($value['load_post_request_details']['drop_lng']) ? doubleval($value['load_post_request_details']['drop_lng']) :doubleval(0.0);
                $arr_trips['data'][$key]['driver_lat']        = isset($value['load_post_request_details']['driver_current_location_details']['current_latitude']) ? doubleval($value['load_post_request_details']['driver_current_location_details']['current_latitude']) :doubleval(0.0);
                $arr_trips['data'][$key]['driver_lng']        = isset($value['load_post_request_details']['driver_current_location_details']['current_longitude']) ? doubleval($value['load_post_request_details']['driver_current_location_details']['current_longitude']) :doubleval(0.0);
            
                $is_review_given = '0';
                if(isset($value['review_details']) && count($value['review_details'])>0){
                    $is_review_given = '1';
                }
                $arr_trips['data'][$key]['is_review_given']  = $is_review_given;

                $arr_trips['data'][$key]['package_type']      = isset($value['load_post_request_details']['load_post_request_package_details']['package_type']) ? $value['load_post_request_details']['load_post_request_package_details']['package_type'] : '';
                $arr_trips['data'][$key]['package_length']    = isset($value['load_post_request_details']['load_post_request_package_details']['package_length']) ? doubleval($value['load_post_request_details']['load_post_request_package_details']['package_length']) : doubleval(0.0);
                $arr_trips['data'][$key]['package_breadth']   = isset($value['load_post_request_details']['load_post_request_package_details']['package_breadth']) ? doubleval($value['load_post_request_details']['load_post_request_package_details']['package_breadth']) : doubleval(0.0);
                $arr_trips['data'][$key]['package_height']    = isset($value['load_post_request_details']['load_post_request_package_details']['package_height']) ? doubleval($value['load_post_request_details']['load_post_request_package_details']['package_height']) : doubleval(0.0);
                $arr_trips['data'][$key]['package_weight']    = isset($value['load_post_request_details']['load_post_request_package_details']['package_weight']) ? doubleval($value['load_post_request_details']['load_post_request_package_details']['package_weight']) : doubleval(0.0);
                $arr_trips['data'][$key]['package_quantity']  = isset($value['load_post_request_details']['load_post_request_package_details']['package_quantity']) ? intval($value['load_post_request_details']['load_post_request_package_details']['package_quantity']) : 0;
                

                $load_post_image = '';
                if(isset($value['load_post_request_details']['load_post_image']) && $value['load_post_request_details']['load_post_image']!=''){
                    if(file_exists($this->load_post_img_base_path.$value['load_post_request_details']['load_post_image'])){
                        $load_post_image = $this->load_post_img_public_path.$value['load_post_request_details']['load_post_image'];
                    }
                }
                $arr_trips['data'][$key]['load_post_image']  = $load_post_image;

                unset($arr_trips['data'][$key]['review_details']);
                unset($arr_trips['data'][$key]['load_post_request_details']);
                
                $arr_trips['arr_pagination'] = $arr_pagination;
            }
            $arr_response['status'] = 'success';
            $arr_response['msg']    = 'completed trips available.';
            $arr_response['data']    = $arr_trips;
            return $arr_response;
        }
        $arr_response['status'] = 'error';
        $arr_response['msg']    = 'trips details not available.';
        $arr_response['data']    = $arr_trips;
        return $arr_response;
    }

    public function filter_canceled_trips($arr_canceled_trips)
    {
        // dd($arr_canceled_trips);

        if(isset($arr_canceled_trips['data']) && sizeof($arr_canceled_trips['data'])>0){
            foreach ($arr_canceled_trips['data'] as $key => $value) 
            {
                $record_type = isset($value['type']) ? $value['type'] : '';
                
                $booking_status = isset($value['booking_status']) ? $value['booking_status'] : '';

                if(isset($arr_canceled_trips['load_post_request_history_details']) && count($arr_canceled_trips['load_post_request_history_details'])>0){
                    foreach ($arr_canceled_trips['load_post_request_history_details'] as $history_key => $history_value) {
                        if(isset($history_value['status']) && $history_value['status'] == 'REJECT_BY_DRIVER'){
                            $booking_status = 'REJECT_BY_DRIVER';
                        }
                    }
                }

                $arr_canceled_trips['data'][$key]['type']           = isset($value['type']) ? $value['type'] : '';
                $arr_canceled_trips['data'][$key]['booking_status'] = isset($value['booking_status']) ? $value['booking_status'] : '';
                $arr_canceled_trips['data'][$key]['booking_date']   = isset($value['booking_date']) ? date('d M Y',strtotime($value['booking_date'])) : '';
                
                if($record_type == 'normal_booking')
                {
                    $arr_canceled_trips['data'][$key]['first_name']     = isset($value['load_post_request_details']['user_details']['first_name']) ? $value['load_post_request_details']['user_details']['first_name'] :'';
                    $arr_canceled_trips['data'][$key]['last_name']      = isset($value['load_post_request_details']['user_details']['last_name']) ? $value['load_post_request_details']['user_details']['last_name'] :'';
                    
                    $profile_image = url('/uploads/default-profile.png');
                    if(isset($value['load_post_request_details']['user_details']['profile_image']) && $value['load_post_request_details']['user_details']['profile_image']!=''){
                        if(file_exists($this->user_profile_base_img_path.$value['load_post_request_details']['user_details']['profile_image'])){
                            $profile_image = $this->user_profile_public_img_path.$value['load_post_request_details']['user_details']['profile_image'];
                        }
                    }

                    if(isset($value['booking_status']) && $value['booking_status'] == 'CANCEL_BY_ADMIN')
                    {
                        $arr_canceled_trips['data'][$key]['first_name']     = 'Canceled by '.config('app.project.name').' Admin';
                        $arr_canceled_trips['data'][$key]['last_name']      = '';
                        $profile_image = url('/uploads/listing-default-logo.png');
                    }
                    
                    
                    $arr_canceled_trips['data'][$key]['profile_image']     = $profile_image;
                    
                    $country_code   = isset($value['load_post_request_details']['user_details']['country_code']) ? $value['load_post_request_details']['user_details']['country_code'] : '';
                    $mobile_no      = isset($value['load_post_request_details']['user_details']['mobile_no']) ? $value['load_post_request_details']['user_details']['mobile_no'] : '';
                    $full_mobile_no = $country_code.''.$mobile_no;
                    $full_mobile_no = ($full_mobile_no!='')?$full_mobile_no :'';

                    $arr_canceled_trips['data'][$key]['mobile_no'] = $full_mobile_no;
                }
                else if($record_type == 'load_post')
                {
                    $arr_canceled_trips['data'][$key]['first_name']     = 'Canceled by you';
                    $arr_canceled_trips['data'][$key]['last_name']      = '';

                    if(isset($value['booking_status']) && $value['booking_status'] == 'REJECT_BY_USER'){
                        $arr_canceled_trips['data'][$key]['first_name']     = 'Canceled by Customer';
                        $arr_canceled_trips['data'][$key]['last_name']      = '';
                    }
                    else if(isset($value['booking_status']) && $value['booking_status'] == 'CANCEL_BY_USER'){
                        $arr_canceled_trips['data'][$key]['first_name']     = 'Canceled by Customer';
                        $arr_canceled_trips['data'][$key]['last_name']      = '';
                    }
                    else if(isset($value['booking_status']) && $value['booking_status'] == 'REJECT_BY_DRIVER'){
                        $arr_canceled_trips['data'][$key]['first_name']     = 'Declined by you';
                        $arr_canceled_trips['data'][$key]['last_name']      = '';
                    }

                    $profile_image = url('/uploads/default-profile.png');
                    if(isset($value['user_details']['profile_image']) && $value['user_details']['profile_image']!=''){
                        if(file_exists($this->user_profile_base_img_path.$value['user_details']['profile_image'])){
                            $profile_image = $this->user_profile_public_img_path.$value['user_details']['profile_image'];
                        }
                    }
                    
                    $arr_canceled_trips['data'][$key]['profile_image']     = $profile_image;
                    
                    $country_code   = isset($value['user_details']['country_code']) ? $value['user_details']['country_code'] : '';
                    $mobile_no      = isset($value['user_details']['mobile_no']) ? $value['user_details']['mobile_no'] : '';
                    $full_mobile_no = $country_code.''.$mobile_no;
                    $full_mobile_no = ($full_mobile_no!='')?$full_mobile_no :'';

                    $arr_canceled_trips['data'][$key]['mobile_no'] = $full_mobile_no;   
                }
                if($record_type == 'normal_booking')
                {
                    $arr_canceled_trips['data'][$key]['pickup_location']   = isset($value['load_post_request_details']['pickup_location']) ? $value['load_post_request_details']['pickup_location'] :'';
                    $arr_canceled_trips['data'][$key]['drop_location']     = isset($value['load_post_request_details']['drop_location']) ? $value['load_post_request_details']['drop_location'] :'';
                    $arr_canceled_trips['data'][$key]['pickup_lat']        = isset($value['load_post_request_details']['pickup_lat']) ? doubleval($value['load_post_request_details']['pickup_lat']) :doubleval(0.0);
                    $arr_canceled_trips['data'][$key]['pickup_lng']        = isset($value['load_post_request_details']['pickup_lng']) ? doubleval($value['load_post_request_details']['pickup_lng']) :doubleval(0.0);
                    $arr_canceled_trips['data'][$key]['drop_lat']          = isset($value['load_post_request_details']['drop_lat']) ? doubleval($value['load_post_request_details']['drop_lat']) :doubleval(0.0);
                    $arr_canceled_trips['data'][$key]['drop_lng']          = isset($value['load_post_request_details']['drop_lng']) ? doubleval($value['load_post_request_details']['drop_lng']) :doubleval(0.0);
                    
                    $arr_canceled_trips['data'][$key]['driver_lat']        = isset($value['load_post_request_details']['driver_current_location_details']['current_latitude']) ? doubleval($value['load_post_request_details']['driver_current_location_details']['current_latitude']) :doubleval(0.0);
                    $arr_canceled_trips['data'][$key]['driver_lng']        = isset($value['load_post_request_details']['driver_current_location_details']['current_longitude']) ? doubleval($value['load_post_request_details']['driver_current_location_details']['current_longitude']) :doubleval(0.0);

                    $arr_canceled_trips['data'][$key]['package_type']      = isset($value['load_post_request_details']['load_post_request_package_details']['package_type']) ? $value['load_post_request_details']['load_post_request_package_details']['package_type'] : '';
                    $arr_canceled_trips['data'][$key]['package_length']    = isset($value['load_post_request_details']['load_post_request_package_details']['package_length']) ? doubleval($value['load_post_request_details']['load_post_request_package_details']['package_length']) : doubleval(0.0);
                    $arr_canceled_trips['data'][$key]['package_breadth']   = isset($value['load_post_request_details']['load_post_request_package_details']['package_breadth']) ? doubleval($value['load_post_request_details']['load_post_request_package_details']['package_breadth']) : doubleval(0.0);
                    $arr_canceled_trips['data'][$key]['package_height']    = isset($value['load_post_request_details']['load_post_request_package_details']['package_height']) ? doubleval($value['load_post_request_details']['load_post_request_package_details']['package_height']) : doubleval(0.0);
                    $arr_canceled_trips['data'][$key]['package_weight']    = isset($value['load_post_request_details']['load_post_request_package_details']['package_weight']) ? doubleval($value['load_post_request_details']['load_post_request_package_details']['package_weight']) : doubleval(0.0);
                    $arr_canceled_trips['data'][$key]['package_quantity']  = isset($value['load_post_request_details']['load_post_request_package_details']['package_quantity']) ? intval($value['load_post_request_details']['load_post_request_package_details']['package_quantity']) : 0;

                    $load_post_image = '';
                    if(isset($value['load_post_request_details']['load_post_image']) && $value['load_post_request_details']['load_post_image']!=''){
                        if(file_exists($this->load_post_img_base_path.$value['load_post_request_details']['load_post_image'])){
                            $load_post_image = $this->load_post_img_public_path.$value['load_post_request_details']['load_post_image'];
                        }
                    }
                    $arr_canceled_trips['data'][$key]['load_post_image']  = $load_post_image;
                    unset($arr_canceled_trips['data'][$key]['review_details']);
                    unset($arr_canceled_trips['data'][$key]['load_post_request_details']);
                }
                else if($record_type == 'load_post')
                {
                    $arr_canceled_trips['data'][$key]['pickup_location']   = isset($value['pickup_location']) ? $value['pickup_location'] :'';
                    $arr_canceled_trips['data'][$key]['drop_location']     = isset($value['drop_location']) ? $value['drop_location'] :'';
                    $arr_canceled_trips['data'][$key]['pickup_lat']        = isset($value['pickup_lat']) ? doubleval($value['pickup_lat']) :doubleval(0.0);
                    $arr_canceled_trips['data'][$key]['pickup_lng']        = isset($value['pickup_lng']) ? doubleval($value['pickup_lng']) :doubleval(0.0);
                    $arr_canceled_trips['data'][$key]['drop_lat']          = isset($value['drop_lat']) ? doubleval($value['drop_lat']) :doubleval(0.0);
                    $arr_canceled_trips['data'][$key]['drop_lng']          = isset($value['drop_lng']) ? doubleval($value['drop_lng']) :doubleval(0.0);
                    
                    $arr_canceled_trips['data'][$key]['driver_lat']        = isset($value['driver_current_location_details']['current_latitude']) ? doubleval($value['driver_current_location_details']['current_latitude']) :doubleval(0.0);
                    $arr_canceled_trips['data'][$key]['driver_lng']        = isset($value['driver_current_location_details']['current_longitude']) ? doubleval($value['driver_current_location_details']['current_longitude']) :doubleval(0.0);

                    $arr_canceled_trips['data'][$key]['package_type']      = isset($value['load_post_request_package_details']['package_type']) ? $value['load_post_request_package_details']['package_type'] : '';
                    $arr_canceled_trips['data'][$key]['package_length']    = isset($value['load_post_request_package_details']['package_length']) ? doubleval($value['load_post_request_package_details']['package_length']) : doubleval(0.0);
                    $arr_canceled_trips['data'][$key]['package_breadth']   = isset($value['load_post_request_package_details']['package_breadth']) ? doubleval($value['load_post_request_package_details']['package_breadth']) : doubleval(0.0);
                    $arr_canceled_trips['data'][$key]['package_height']    = isset($value['load_post_request_package_details']['package_height']) ? doubleval($value['load_post_request_package_details']['package_height']) : doubleval(0.0);
                    $arr_canceled_trips['data'][$key]['package_weight']    = isset($value['load_post_request_package_details']['package_weight']) ? doubleval($value['load_post_request_package_details']['package_weight']) : doubleval(0.0);
                    $arr_canceled_trips['data'][$key]['package_quantity']  = isset($value['load_post_request_package_details']['package_quantity']) ? intval($value['load_post_request_package_details']['package_quantity']) : 0;

                    $load_post_image = '';
                    if(isset($value['load_post_image']) && $value['load_post_image']!=''){
                        if(file_exists($this->load_post_img_base_path.$value['load_post_image'])){
                            $load_post_image = $this->load_post_img_public_path.$value['load_post_image'];
                        }
                    }
                    $arr_canceled_trips['data'][$key]['load_post_image']  = $load_post_image;
                    unset($arr_canceled_trips['data'][$key]['load_post_request_package_details']);
                    unset($arr_canceled_trips['data'][$key]['user_details']);
                    unset($arr_canceled_trips['data'][$key]['driver_details']);
                }
            }
            $arr_response['status'] = 'success';
            $arr_response['msg']    = 'canceled trips available.';
            $arr_response['data']    = $arr_canceled_trips;
            return $arr_response;
        }
        $arr_response['status'] = 'error';
        $arr_response['msg']    = 'No canceled trips available.';
        $arr_response['data']    = [];
        return $arr_response;
    }

    public function make_pagination_links($items,$perPage)
    {
        $pageStart = \Request::get('page', 1);
        // Start displaying items from this number;
        $offSet = ($pageStart * $perPage) - $perPage; 

        // Get only the items you need using array_slice
        $itemsForCurrentPage = array_slice($items, $offSet, $perPage, true);

        return new LengthAwarePaginator($itemsForCurrentPage, count($items), $perPage,Paginator::resolveCurrentPage(), array('path' => Paginator::resolveCurrentPath()));
    } 
    
    public function load_post_details($request)
    {
        $user_id     = validate_user_login_id();
        if ($user_id == 0) 
        {
            $arr_response['status'] = 'error';
            $arr_response['msg']    = 'Invalid token';
            $arr_response['data']    = [];
            return $arr_response;
        }
        
        $load_post_request_id = base64_decode($request->input('load_post_request_id'));
        $type = $request->input('type');

        if($load_post_request_id == '')
        {
            $arr_response['status'] = 'error';
            $arr_response['msg']    = 'Invalid shipment post token,cannot process request.';
            $arr_response['data']    = [];
            return $arr_response;
        }
       
        $obj_load_post_request = $this->LoadPostRequestModel
                                                ->with(['load_post_request_package_details','user_details','driver_details','driver_current_location_details','vehicle_details.vehicle_type_details','load_post_request_history_details'=>function($query) use($user_id){
                                                    $query->where('driver_id',$user_id);
                                                    $query->whereIn('status',['REJECT_BY_DRIVER','REJECT_BY_USER']);
                                                }])
                                                ->where('id',$load_post_request_id)
                                                ->first();

        if(isset($obj_load_post_request) && $obj_load_post_request!=null)
        {
            $arr_load_post_request = $obj_load_post_request->toArray();

            if(isset($arr_load_post_request) && sizeof($arr_load_post_request)>0){

                $arr_result                         = [];
                $arr_result['first_name']           = '';
                $arr_result['last_name']            = '';
                $arr_result['email']                = '';
                $arr_result['mobile_no']            = '';
                $arr_result['profile_image']        = '';
                // $arr_result['fair_charge']          = $per_miles_price;
                $arr_result['load_post_request_id'] = isset($arr_load_post_request['id']) ? $arr_load_post_request['id'] : 0;
                $arr_result['user_id']              = isset($arr_load_post_request['user_id']) ? $arr_load_post_request['user_id'] : 0;
                $arr_result['driver_id']            = isset($arr_load_post_request['driver_id']) ? $arr_load_post_request['driver_id'] : 0;
                $arr_result['pickup_location']      = isset($arr_load_post_request['pickup_location']) ? $arr_load_post_request['pickup_location'] : '';
                $arr_result['pickup_lat']           = isset($arr_load_post_request['pickup_lat']) ? doubleval($arr_load_post_request['pickup_lat']) : floatval(0);
                $arr_result['pickup_lng']           = isset($arr_load_post_request['pickup_lng']) ? doubleval($arr_load_post_request['pickup_lng']) : floatval(0);
                $arr_result['drop_lat']             = isset($arr_load_post_request['drop_lat']) ? doubleval($arr_load_post_request['drop_lat']) : floatval(0);
                $arr_result['drop_lng']             = isset($arr_load_post_request['drop_lng']) ? doubleval($arr_load_post_request['drop_lng']) : floatval(0);
                $arr_result['drop_location']        = isset($arr_load_post_request['drop_location']) ? $arr_load_post_request['drop_location'] : '';
                $arr_result['booking_date']         = isset($arr_load_post_request['date']) ? date('d M Y',strtotime($arr_load_post_request['date'])) : '';
                $arr_result['request_status']       = isset($arr_load_post_request['request_status']) ? $arr_load_post_request['request_status'] : '';
                $arr_result['package_type']         = isset($arr_load_post_request['load_post_request_package_details']['package_type']) ? $arr_load_post_request['load_post_request_package_details']['package_type'] : '';
                $arr_result['package_length']       = isset($arr_load_post_request['load_post_request_package_details']['package_length']) ? $arr_load_post_request['load_post_request_package_details']['package_length'] : 0;
                $arr_result['package_breadth']      = isset($arr_load_post_request['load_post_request_package_details']['package_breadth']) ? $arr_load_post_request['load_post_request_package_details']['package_breadth'] : 0;
                $arr_result['package_height']       = isset($arr_load_post_request['load_post_request_package_details']['package_height']) ? $arr_load_post_request['load_post_request_package_details']['package_height'] : 0;
                $arr_result['package_weight']       = isset($arr_load_post_request['load_post_request_package_details']['package_weight']) ? $arr_load_post_request['load_post_request_package_details']['package_weight'] : 0;
                $arr_result['package_quantity']     = isset($arr_load_post_request['load_post_request_package_details']['package_quantity']) ? $arr_load_post_request['load_post_request_package_details']['package_quantity'] : 0;

                $driver_id                 = isset($arr_load_post_request['driver_id']) ? $arr_load_post_request['driver_id'] : 0;

                $arr_vehicle_type_details  = $this->CommonDataService->get_driver_vehicle_type_details($driver_id);
                
                $per_miles_price = isset($arr_vehicle_type_details['vehicle_details']['vehicle_type_details']['per_miles_price']) ? $arr_vehicle_type_details['vehicle_details']['vehicle_type_details']['per_miles_price'] :0;
                $arr_result['fair_charge']          = $per_miles_price;

                $arr_result['first_name']    = isset($arr_load_post_request['user_details']['first_name']) ? $arr_load_post_request['user_details']['first_name'] : '';
                $arr_result['last_name']     = isset($arr_load_post_request['user_details']['last_name']) ? $arr_load_post_request['user_details']['last_name'] : '';
                $arr_result['email']         = isset($arr_load_post_request['user_details']['email']) ? $arr_load_post_request['user_details']['email'] : '';
                
                $country_code   = isset($arr_load_post_request['user_details']['country_code']) ? $arr_load_post_request['user_details']['country_code'] : '';
                $mobile_no      = isset($arr_load_post_request['user_details']['mobile_no']) ? $arr_load_post_request['user_details']['mobile_no'] : '';
                $full_mobile_no = $country_code.''.$mobile_no;
                $full_mobile_no = ($full_mobile_no!='')?$full_mobile_no :'';

                $arr_result['mobile_no']     = $full_mobile_no;
                
                $profile_image = url('/uploads/default-profile.png');
                $tmp_profile_image = isset($arr_load_post_request['user_details']['profile_image']) ? $arr_load_post_request['user_details']['profile_image'] : '';

                if($tmp_profile_image!='' && file_exists($this->user_profile_base_img_path.$tmp_profile_image))
                {
                   $profile_image = $this->user_profile_public_img_path.$tmp_profile_image;
                }
                $arr_result['profile_image'] = $profile_image;

                if($type == 'canceled')
                {
                    $first_name = 'Canceled by you';
                    $request_status = isset($arr_load_post_request['request_status']) ? $arr_load_post_request['request_status'] : '';;
                    
                    // dd($arr_load_post_request['load_post_request_history_details']);

                    if(isset($arr_load_post_request['load_post_request_history_details']) && count($arr_load_post_request['load_post_request_history_details'])>0)
                    {
                        foreach ($arr_load_post_request['load_post_request_history_details'] as $key => $value) 
                        {   
                            if(isset($value['status']) && $value['status'] == 'REJECT_BY_DRIVER'){
                                $first_name = 'Declined by you';
                                $request_status = 'REJECT_BY_DRIVER';
                            }
                            else if(isset($value['status']) && $value['status'] == 'REJECT_BY_USER'){
                                $first_name = 'Canceled by Customer';
                                $request_status = 'REJECT_BY_USER';
                            }
                            else if(isset($value['status']) && $value['status'] == 'CANCEL_BY_USER'){
                                $first_name = 'Canceled by Customer';
                                $request_status = 'CANCEL_BY_USER';
                            }
                        }
                    }

                    $arr_result['first_name']     = $first_name;
                    $arr_result['last_name']      = '';
                    $arr_result['request_status'] = $request_status;
                }
                if($type == 'request_list')
                {
                    $request_status = isset($arr_load_post_request['request_status']) ? $arr_load_post_request['request_status'] : '';;
                    if($request_status!='ACCEPT_BY_USER' && $request_status!='ACCEPT_BY_DRIVER'){

                        if(isset($arr_load_post_request['load_post_request_history_details']) && count($arr_load_post_request['load_post_request_history_details'])<=0)
                        {
                            $request_status = 'USER_REQUEST';
                        }

                        $arr_result['request_status'] = $request_status;
                    }

                }
                $arr_response['status'] = 'success';
                $arr_response['msg']    = 'Shipment post details found successfully.';
                $arr_response['data']   = $arr_result;
                return $arr_response;   

                
            }
            else
            {
                $arr_response['status'] = 'error';
                $arr_response['msg']    = 'Problem occurred, fetching shipment post details,Please try again.';
                $arr_response['data']   = [];
                return $arr_response;   
            }
        }
        $arr_response['status'] = 'error';
        $arr_response['msg']    = 'Problem occurred, fetching shipment post details,Please try again.';
        $arr_response['data']   = [];
        return $arr_response;
    }

    public function trip_details($request,$type = false)
    {
        $user_id     = validate_user_login_id();
        if ($user_id == 0) 
        {
            $arr_response['status'] = 'error';
            $arr_response['msg']    = 'Invalid driver token';
            $arr_response['data']    = [];
            return $arr_response;
        }
        
        $user_type  = 'DRIVER';
        if($type!=false && $type == 'live_trip'){
            $booking_id = $this->check_driver_current_trip($user_id);
        }
        else{
            $booking_id = base64_decode($request->input('booking_id'));
        }
        
        if ($booking_id == "") 
        {
            $arr_response['status'] = 'error';
            $arr_response['msg']    = 'Invalid booking ID';
            $arr_response['data']    = [];
            return $arr_response;
        }
        
        $arr_trip_details  = [];
        $obj_trip_details  = $this->BookingMasterModel
                                                // ->select('id','load_post_request_id','booking_unique_id','booking_date','booking_status','total_charge')
                                                ->whereHas('load_post_request_details',function($query) {
                                                        $query->whereHas('driver_details',function($query){
                                                        });
                                                        $query->whereHas('user_details',function($query){
                                                        });
                                                })
                                                ->with(['load_post_request_details'=> function($query) use($user_id) {
                                                        $query->select('id','user_id','driver_id','pickup_location','drop_location','pickup_lat','pickup_lng','drop_lat','drop_lng','request_status');
                                                        $query->where('request_status','ACCEPT_BY_USER');
                                                        $query->with(['driver_details'=>function($query){
                                                                    $query->select('id','first_name','last_name','email','country_code','mobile_no','profile_image');
                                                                }]);
                                                        $query->with(['user_details'=>function($query){
                                                                    $query->select('id','first_name','last_name','email','country_code','mobile_no','profile_image');
                                                                }]);
                                                        $query->with(['load_post_request_package_details'=>function($query){
                                                                    // $query->select('id','first_name','last_name','email','mobile_no','profile_image');
                                                                }]);
                                                }])
                                                ->where('id',$booking_id)
                                                ->first();

        if($obj_trip_details)
        {
            $arr_trip_details = $obj_trip_details->toArray();
        }
        
        if(isset($arr_trip_details) && sizeof($arr_trip_details)>0)
        {
            $driver_fare_charge = 0;

            $arr_result                      = [];
            $arr_result['first_name']        = '';
            $arr_result['last_name']         = '';
            $arr_result['email']             = '';
            $arr_result['mobile_no']         = '';
            $arr_result['profile_image']     = '';
            $arr_result['booking_id']        = isset($arr_trip_details['id']) ? $arr_trip_details['id'] : 0 ;
            $arr_result['booking_unique_id'] = isset($arr_trip_details['booking_unique_id']) ? $arr_trip_details['booking_unique_id'] : '';
            $arr_result['booking_status']    = isset($arr_trip_details['booking_status']) ? $arr_trip_details['booking_status'] : '';
            $arr_result['booking_date']      = isset($arr_trip_details['booking_date']) ? $arr_trip_details['booking_date'] : '';
            $arr_result['total_charge']      = isset($arr_trip_details['total_charge']) ? $arr_trip_details['total_charge'] : 0;
            $arr_result['user_id']           = isset($arr_trip_details['load_post_request_details']['user_id']) ? strval($arr_trip_details['load_post_request_details']['user_id']) : '0';
            $arr_result['driver_id']         = isset($arr_trip_details['load_post_request_details']['driver_id']) ? strval($arr_trip_details['load_post_request_details']['driver_id']) : '0';
            $arr_result['pickup_location']   = isset($arr_trip_details['load_post_request_details']['pickup_location']) ? $arr_trip_details['load_post_request_details']['pickup_location'] :'';
            $arr_result['drop_location']     = isset($arr_trip_details['load_post_request_details']['drop_location']) ? $arr_trip_details['load_post_request_details']['drop_location'] :'';
            $arr_result['pickup_lat']        = isset($arr_trip_details['load_post_request_details']['pickup_lat']) ? doubleval($arr_trip_details['load_post_request_details']['pickup_lat']) :doubleval(0.0);
            $arr_result['pickup_lng']        = isset($arr_trip_details['load_post_request_details']['pickup_lng']) ? doubleval($arr_trip_details['load_post_request_details']['pickup_lng']) :doubleval(0.0);
            $arr_result['drop_lat']          = isset($arr_trip_details['load_post_request_details']['drop_lat']) ? doubleval($arr_trip_details['load_post_request_details']['drop_lat']) :doubleval(0.0);
            $arr_result['drop_lng']          = isset($arr_trip_details['load_post_request_details']['drop_lng']) ? doubleval($arr_trip_details['load_post_request_details']['drop_lng']) :doubleval(0.0);
           
            $arr_result['package_type']     = isset($arr_trip_details['load_post_request_details']['load_post_request_package_details']['package_type']) ? $arr_trip_details['load_post_request_details']['load_post_request_package_details']['package_type'] : '';
            $arr_result['package_length']   = isset($arr_trip_details['load_post_request_details']['load_post_request_package_details']['package_length']) ? doubleval($arr_trip_details['load_post_request_details']['load_post_request_package_details']['package_length']) : doubleval(0.0);
            $arr_result['package_breadth']  = isset($arr_trip_details['load_post_request_details']['load_post_request_package_details']['package_breadth']) ? doubleval($arr_trip_details['load_post_request_details']['load_post_request_package_details']['package_breadth']) : doubleval(0.0);
            $arr_result['package_height']   = isset($arr_trip_details['load_post_request_details']['load_post_request_package_details']['package_height']) ? doubleval($arr_trip_details['load_post_request_details']['load_post_request_package_details']['package_height']) : doubleval(0.0);
            $arr_result['package_weight']   = isset($arr_trip_details['load_post_request_details']['load_post_request_package_details']['package_weight']) ? doubleval($arr_trip_details['load_post_request_details']['load_post_request_package_details']['package_weight']) : doubleval(0.0);
            $arr_result['package_quantity'] = isset($arr_trip_details['load_post_request_details']['load_post_request_package_details']['package_quantity']) ? intval($arr_trip_details['load_post_request_details']['load_post_request_package_details']['package_quantity']) : 0;
            $profile_image = '';

            if($user_type == 'DRIVER')
            {
                $arr_result['first_name']    = isset($arr_trip_details['load_post_request_details']['user_details']['first_name']) ? $arr_trip_details['load_post_request_details']['user_details']['first_name'] :'';
                $arr_result['last_name']     = isset($arr_trip_details['load_post_request_details']['user_details']['last_name']) ? $arr_trip_details['load_post_request_details']['user_details']['last_name'] :'';
                $arr_result['email']         = isset($arr_trip_details['load_post_request_details']['user_details']['email']) ? $arr_trip_details['load_post_request_details']['user_details']['email'] : '';
                
                $country_code   = isset($arr_trip_details['load_post_request_details']['user_details']['country_code']) ? $arr_trip_details['load_post_request_details']['user_details']['country_code'] : '';
                $mobile_no      = isset($arr_trip_details['load_post_request_details']['user_details']['mobile_no']) ? $arr_trip_details['load_post_request_details']['user_details']['mobile_no'] : '';
                $full_mobile_no = $country_code.''.$mobile_no;
                $full_mobile_no = ($full_mobile_no!='')?$full_mobile_no :'';

                $arr_result['mobile_no'] = $full_mobile_no;

                // $arr_result['mobile_no']     = isset($arr_trip_details['load_post_request_details']['user_details']['mobile_no']) ? $arr_trip_details['load_post_request_details']['user_details']['mobile_no'] : '';

                $profile_image = url('/uploads/default-profile.png');
                if(isset($arr_trip_details['load_post_request_details']['user_details']['profile_image']) && $arr_trip_details['load_post_request_details']['user_details']['profile_image']!=''){
                    if(file_exists($this->user_profile_base_img_path.$arr_trip_details['load_post_request_details']['user_details']['profile_image'])){
                        $profile_image = $this->user_profile_public_img_path.$arr_trip_details['load_post_request_details']['user_details']['profile_image'];
                    }
                }
                
                if(isset($arr_trip_details['booking_status']) && $arr_trip_details['booking_status'] == 'CANCEL_BY_ADMIN')
                {
                    $arr_result['first_name']     = 'Canceled by '.config('app.project.name').' Admin';
                    $arr_result['last_name']      = '';
                    $profile_image = url('/uploads/listing-default-logo.png');
                    
                }

                $arr_result['profile_image'] = $profile_image;
                
                $download_receipt = '';

                if($arr_trip_details['booking_status'] == 'COMPLETED'){
                    $receiptName = "TRIP_INVOICE_".$booking_id.".pdf";
                    if(file_exists($this->invoice_base_img_path.$receiptName)){
                        $download_receipt = $receiptName;
                    }
                } 
                $arr_result['download_receipt'] = $download_receipt;

                $arr_data = filter_completed_trip_details($arr_trip_details);
                
                $arr_result['po_no']              = isset($arr_data['po_no']) ? $arr_data['po_no'] : '';
                $arr_result['receiver_name']      = isset($arr_data['receiver_name']) ? $arr_data['receiver_name'] : '';
                $arr_result['receiver_no']        = isset($arr_data['receiver_no']) ? $arr_data['receiver_no'] : '';
                $arr_result['app_suite']          = isset($arr_data['app_suite']) ? $arr_data['app_suite'] : '';
                $arr_result['distance']           = isset($arr_data['distance']) ? number_format($arr_data['distance'],2) : '0,0';
                $arr_result['total_minutes_trip'] = isset($arr_data['total_minutes_trip']) ? $arr_data['total_minutes_trip'] : ''; 
                $arr_result['total_amount']      = isset($arr_data['total_amount']) ? number_format($arr_data['total_amount'],2) : '0,0';
                $arr_result['discount_amount']   = isset($arr_data['applied_promo_code_charge']) ? number_format($arr_data['applied_promo_code_charge'],2) : '0,0';
                $arr_result['final_amount']      = isset($arr_data['total_charge']) ? number_format($arr_data['total_charge'],2) : '0,0';

            }
            $arr_response['status'] = 'success';
            $arr_response['msg']    = 'trip details found.';
            $arr_response['data']    = $arr_result;
            return $arr_response;
        }
        $arr_response['status'] = 'error';
        $arr_response['msg']    = 'No ongoing trip details available.';
        $arr_response['data']    = [];
        return $arr_response;
    }
    
    public function accept_pending_load_post($request,$client = null)
    {
        $login_user_id     = validate_user_login_id();
        if ($login_user_id == 0) 
        {
            $arr_response['status'] = 'error';
            $arr_response['msg']    = 'Invalid driver token';
            $arr_response['data']    = [];
            return $arr_response;
        }
        
        $load_post_request_id = base64_decode($request->input('load_post_request_id'));

        if($login_user_id!='' && $load_post_request_id!='')
        {
            $driver_id = $login_user_id;
            $request_status = 'ACCEPT_BY_DRIVER';

            /*check for load post status if 'accept_by_user' or not*/
            $obj_load_post_request = $this->LoadPostRequestModel/*->with('load_post_request_history_details')*/
                                                    ->with(['load_post_request_history_details'=>function($query) use($driver_id){
                                                        $query->where(function($query){
                                                            $query->where('status','=','REJECT_BY_DRIVER');
                                                            // $query->orwhere('status','=','TIMEOUT');
                                                        });
                                                        // $query->where('status','=','REJECT_BY_DRIVER');
                                                        $query->where('driver_id',$driver_id);
                                                    }])
                                                    ->with(['user_details'=>function($query){
                                                        $query->select('id','first_name','last_name','country_code','mobile_no');
                                                    }])
                                                    ->where('id',$load_post_request_id)
                                                    // ->whereIn('request_status',['USER_REQUEST','REJECT_BY_USER'])
                                                    ->first();
            
            if(isset($obj_load_post_request) && $obj_load_post_request!=null)
            {
                if(isset($obj_load_post_request->request_status) && $obj_load_post_request->request_status!=''){
                    
                    /*check if user already rejected load post request*/
                    if($obj_load_post_request->request_status == 'CANCEL_BY_USER')
                    {
                        $arr_response['status'] = 'error';
                        $arr_response['msg']    = 'Sorry for inconvenience, customer canceled the trip.';
                        $arr_response['data']   = [];
                        return $arr_response;
                    }
                    /*check if trip has been already accepted by another driver*/
                    if($obj_load_post_request->request_status == 'ACCEPT_BY_DRIVER')
                    {
                        $arr_response['status'] = 'error';
                        $arr_response['msg']    = 'Sorry for inconvenience, shipment request has been already in process.';
                        $arr_response['data']   = [];
                        return $arr_response;
                    }

                    $arr_required_status = ['USER_REQUEST','REJECT_BY_DRIVER','TIMEOUT'];
                    
                    if(in_array($obj_load_post_request->request_status, $arr_required_status)){
                        
                        $enc_user_id = isset($obj_load_post_request->user_id) ? $obj_load_post_request->user_id :0;
                        
                        $is_previous_load_post_accepted_status_pending =  $this->check_user_previous_load_post_accepted_status($enc_user_id);
                        if($is_previous_load_post_accepted_status_pending>0)
                        {
                            $arr_response['status'] = 'error';
                            $arr_response['msg']    = 'Currently,Customer is busy,Please try again after some time.';
                            $arr_response['data']   = [];
                            return $arr_response;
                        }

                        $driver_status = 'BUSY';
                        $this->CommonDataService->change_driver_status($driver_id,$driver_status);

                        $obj_load_post_request->driver_id      = $driver_id;
                        $obj_load_post_request->request_status = 'ACCEPT_BY_DRIVER';

                        $status = $obj_load_post_request->save();
                        if($status){
                            $arr_load_post_request_history = [];
                            $arr_load_post_request_history['load_post_request_id'] = $load_post_request_id;
                            $arr_load_post_request_history['user_id']              = 0;
                            $arr_load_post_request_history['driver_id']            = $driver_id;
                            $arr_load_post_request_history['status']               = 'ACCEPT_BY_DRIVER';
                            $arr_load_post_request_history['reason']               = '';

                            $this->LoadPostRequestHistoryModel->create($arr_load_post_request_history);
                            
                            //send twilio sms eta notifications
                            if(isset($client)){
                                //send sms to user
                                $user_country_code   = isset($obj_load_post_request->user_details->country_code) ? $obj_load_post_request->user_details->country_code :'';
                                $user_mobile_no      = isset($obj_load_post_request->user_details->mobile_no) ? $obj_load_post_request->user_details->mobile_no :'';
                                $user_full_mobile_no = $user_country_code.''.$user_mobile_no;

                                if($user_full_mobile_no!=''){
                                    
                                    $arr_driver_details = $this->get_driver_details($login_user_id);
                                    $driver_first_name  = isset($arr_driver_details['first_name']) ? $arr_driver_details['first_name'] :'';
                                    $driver_last_name   = isset($arr_driver_details['last_name']) ? $arr_driver_details['last_name'] :'';
                                    $driver_full_name   = $driver_first_name.' '.$driver_last_name;

                                    $pickup_lat  = isset($obj_load_post_request->pickup_lat) ? $obj_load_post_request->pickup_lat : '';
                                    $pickup_lng  = isset($obj_load_post_request->pickup_lng) ? $obj_load_post_request->pickup_lng : '';

                                    $driver_latitude  = isset($arr_driver_details['lat']) ? $arr_driver_details['lat'] : '';
                                    $driver_longitude = isset($arr_driver_details['lng']) ? $arr_driver_details['lng'] : '';

                                    $driver_origins      = $driver_latitude.",".$driver_longitude;
                                    $driver_destinations = $pickup_lat.",".$pickup_lng;
                                    
                                    $arr_driver_distance = $this->calculate_distance($driver_origins,$driver_destinations);
                                    $driver_duration = isset($arr_driver_distance['duration']) ? $arr_driver_distance['duration'] :'';

                                    $messageBody         = 'Your trip has been accepted by '.$driver_full_name.' and has an eta of '.$driver_duration.' to the pickup location.';

                                    $this->sendEtaNotificationsMessage(
                                                            $client,
                                                            $user_full_mobile_no,
                                                            $messageBody,
                                                            ''
                                                        );
                                }                 
                            }

                            /*Send on signal notification to user that driver accepted your request*/
                            $arr_notification_data = 
                                                    [
                                                        'title'             => 'New shipment post request accepted by driver',
                                                        'record_id'         => $load_post_request_id,
                                                        'enc_user_id'       => $enc_user_id,
                                                        'notification_type' => 'ACCEPT_BY_DRIVER',
                                                        'user_type'         => 'USER',
                                                    ];

                            $this->NotificationsService->send_on_signal_notification($arr_notification_data);

                            /*Send on signal notification to user that driver accepted your request*/
                            $arr_notification_data = 
                                                    [
                                                        'title'             => 'New shipment post request accepted by driver',
                                                        'record_id'         => $load_post_request_id,
                                                        'enc_user_id'       => $enc_user_id,
                                                        'notification_type' => 'ACCEPT_BY_DRIVER',
                                                        'user_type'         => 'WEB',
                                                    ];

                            $this->NotificationsService->send_on_signal_notification($arr_notification_data);

                            $response_msg = '';
                            if($request_status == 'ACCEPT_BY_DRIVER'){
                                $response_msg = 'You have accepted the trip. Once the customer confirms, you may begin.';
                            }
                            else if($request_status == 'REJECT_BY_DRIVER'){
                                $response_msg = 'You have rejected shipment post request.';
                            }
                            else if($request_status == 'TIMEOUT'){
                                $response_msg = 'Shipment Request is timeout, you can check that shipment in request list if it is not accepted by another driver.';
                            }

                            $arr_response['status'] = 'success';
                            $arr_response['msg']    = $response_msg;
                            $arr_response['data']   = [];
                            return $arr_response;

                        }else{
                            $arr_response['status'] = 'error';
                            $arr_response['msg']    = 'Problem occurred, while processing shipment post request';
                            $arr_response['data']   = [];
                            return $arr_response;
                        }
                    }
                    else{
                        
                        $request_status    = isset($obj_load_post_request->request_status) ? $obj_load_post_request->request_status :'';
                        $request_driver_id = isset($obj_load_post_request->driver_id) ? $obj_load_post_request->driver_id :'';
                        
                        $message = '';

                        if($request_status == 'ACCEPT_BY_USER'){
                            $message = 'Shipment post request is already In-Progress cannot process further.';
                        }
                        
                        if($request_driver_id == $driver_id){
                            
                            if($request_status == 'ACCEPT_BY_DRIVER'){
                            $message = 'Shipment post request already accepted by you,cannot process this trip again.';
                            } 
                            if($request_status == 'REJECT_BY_DRIVER'){
                                $message = 'Trip No Longer Avalible! you have already rejected this trip request.';
                            }
                        }
                        else{

                            if($request_status == 'ACCEPT_BY_DRIVER'){
                            $message = 'Trip No Longer Avalible! Another driver has accepted this trip request.';
                            } 
                            if($request_status == 'REJECT_BY_DRIVER'){
                                $message = 'Trip No Longer Avalible! you have already rejected this trip request.';
                            }   
                        }
                        $arr_response['status'] = 'error';
                        $arr_response['msg']    = $message;
                        $arr_response['data']   = [];
                        return $arr_response;
                    }
                }else{
                    $arr_response['status'] = 'error';
                    $arr_response['msg']    = 'Problem occurred, while processing shipment post request';
                    $arr_response['data']   = [];
                    return $arr_response;   
                }
            }
            $arr_response['status'] = 'error';
            $arr_response['msg']    = 'Problem occurred, while processing shipment post request';
            $arr_response['data']   = [];
            return $arr_response;
        }
        $arr_response['status'] = 'error';
        $arr_response['msg']    = 'Problem occurred, while processing request, Please try again.';
        $arr_response['data']   = [];
        return $arr_response;   
    }
    public function cancel_pending_load_post($request)
    {
        $login_user_id     = validate_user_login_id();
        if ($login_user_id == 0) 
        {
            $arr_response['status'] = 'error';
            $arr_response['msg']    = 'Invalid driver token';
            $arr_response['data']    = [];
            return $arr_response;
        }
        
        $load_post_request_id = base64_decode($request->input('load_post_request_id'));

        if($login_user_id!='' && $load_post_request_id!='')
        {
            $driver_id = $login_user_id;
            /*check for load post status if 'accept_by_user' or not*/
            $obj_load_post_request = $this->LoadPostRequestModel/*->with('load_post_request_history_details')*/
                                                    ->with(['load_post_request_history_details'=>function($query) use($driver_id){
                                                        $query->where(function($query){
                                                            $query->where('status','=','REJECT_BY_DRIVER');
                                                            // $query->orwhere('status','=','TIMEOUT');
                                                        });
                                                        // $query->where('status','=','REJECT_BY_DRIVER');
                                                        $query->where('driver_id',$driver_id);

                                                    }])
                                                    ->where('id',$load_post_request_id)
                                                    // ->whereIn('request_status',['USER_REQUEST','REJECT_BY_USER'])
                                                    ->first();
            
            if(isset($obj_load_post_request) && $obj_load_post_request!=null)
            {
                if(isset($obj_load_post_request->request_status) && $obj_load_post_request->request_status!=''){
                    
                    /*check if user already rejected load post request*/
                    if($obj_load_post_request->request_status == 'CANCEL_BY_USER')
                    {
                        $arr_response['status'] = 'error';
                        $arr_response['msg']    = 'Sorry for inconvenience, customer canceled the trip.';
                        $arr_response['data']   = [];
                        return $arr_response;
                    }
                    /*check if trip has been already accepted by another driver*/
                    if($obj_load_post_request->request_status == 'ACCEPT_BY_DRIVER')
                    {
                        $arr_response['status'] = 'error';
                        $arr_response['msg']    = 'Sorry for inconvenience, shipment request has been already in process.';
                        $arr_response['data']   = [];
                        return $arr_response;
                    }

                    $arr_required_status = ['USER_REQUEST','REJECT_BY_DRIVER','TIMEOUT'];
                    
                    if(in_array($obj_load_post_request->request_status, $arr_required_status)){
                        /*requested driver already rejected request so cannot process further need to check*/
                        if(isset($obj_load_post_request->load_post_request_history_details) && sizeof($obj_load_post_request->load_post_request_history_details)>0){
                            $arr_response['status'] = 'error';
                            $arr_response['msg']    = 'You have already rejected this request, cannot process further';
                            $arr_response['data']   = [];
                            return $arr_response;
                        }
                        
                        $enc_user_id = isset($obj_load_post_request->user_id) ? $obj_load_post_request->user_id :0;

                        $obj_load_post_request->driver_id = 0;
                        $obj_load_post_request->request_status = 'REJECT_BY_DRIVER';

                        $driver_status = 'AVAILABLE';
                        $this->CommonDataService->change_driver_status($driver_id,$driver_status);

                        $status = $obj_load_post_request->save();
                        if($status){
                            $arr_load_post_request_history = [];
                            $arr_load_post_request_history['load_post_request_id'] = $load_post_request_id;
                            $arr_load_post_request_history['user_id']              = 0;
                            $arr_load_post_request_history['driver_id']            = $driver_id;
                            $arr_load_post_request_history['status']               = 'REJECT_BY_DRIVER';
                            // $arr_load_post_request_history['is_admin_assign']      = '1';
                            $arr_load_post_request_history['reason']               = '';

                            $this->LoadPostRequestHistoryModel->create($arr_load_post_request_history);
                         
                            $arr_response['status'] = 'success';
                            $arr_response['msg']    = 'You have rejected shipment post request.';
                            $arr_response['data']   = [];
                            return $arr_response;

                        }else{
                            $arr_response['status'] = 'error';
                            $arr_response['msg']    = 'Problem occurred, while processing shipment post request';
                            $arr_response['data']   = [];
                            return $arr_response;
                        }
                    }
                    else{
                        
                        $request_status    = isset($obj_load_post_request->request_status) ? $obj_load_post_request->request_status :'';
                        $request_driver_id = isset($obj_load_post_request->driver_id) ? $obj_load_post_request->driver_id :'';
                        
                        $message = '';
                        if($request_driver_id == $driver_id){
                            $message = 'Trip No Longer Avalible! you have already rejected this trip request.';
                        }
                        else{
                            $message = 'Trip No Longer Avalible! you have already rejected this trip request.';
                        }
                        $arr_response['status'] = 'error';
                        $arr_response['msg']    = $message;
                        $arr_response['data']   = [];
                        return $arr_response;
                    }
                }else{
                    $arr_response['status'] = 'error';
                    $arr_response['msg']    = 'Problem occurred, while processing shipment post request';
                    $arr_response['data']   = [];
                    return $arr_response;   
                }
            }
            $arr_response['status'] = 'error';
            $arr_response['msg']    = 'Problem occurred, while processing shipment post request';
            $arr_response['data']   = [];
            return $arr_response;
        }
        $arr_response['status'] = 'error';
        $arr_response['msg']    = 'Problem occurred, while processing request, Please try again.';
        $arr_response['data']   = [];
        return $arr_response;   
    }

    public function process_cancel_trip_status_by_driver($request,$client = null)
    {
        $login_user_id     = validate_user_login_id();
        if ($login_user_id == 0) 
        {
            $arr_response['status'] = 'error';
            $arr_response['msg']    = 'Invalid token';
            $arr_response['data']    = [];
            return $arr_response;
        }
        
        $booking_status = 'CANCEL_BY_DRIVER';
        $booking_id        = $request->input('booking_id');
        $reason            = $request->input('reason');
        

        if ($booking_id == '' || $booking_status == '') 
        {
            $arr_response['status'] = 'error';
            $arr_response['msg']    = 'Invalid required details.';
            $arr_response['data']   = [];
            return $arr_response;
        }

        if($login_user_id!='' && $booking_id!='' && $booking_status!='')
        {
            /*check for load post status if 'accept_by_user' or not*/
            
            $obj_booking_master = $this->BookingMasterModel
                                                ->whereHas('load_post_request_details',function($query){
                                                })
                                                ->with(['load_post_request_details'=>function($query){
                                                    $query->with(['user_details'=>function($query){
                                                        $query->select('id','first_name','last_name','country_code','mobile_no');
                                                    }]);
                                                    $query->with(['driver_details'=>function($query){
                                                        $query->select('id','first_name','last_name','country_code','mobile_no');
                                                    }]);
                                                }]) 
                                                ->where('id',$booking_id)
                                                ->first();

            if(isset($obj_booking_master) && $obj_booking_master!=null)
            {
                $enc_user_id = isset($obj_booking_master->load_post_request_details->user_id) ? $obj_booking_master->load_post_request_details->user_id :0;

                if(isset($obj_booking_master->booking_status) && $obj_booking_master->booking_status == 'CANCEL_BY_USER'){
                    $arr_response['status'] = 'error';
                    $arr_response['msg']    = 'Customer have canceled this trip.';
                    $arr_response['data']   = [];
                    return $arr_response;
                }

                if(isset($obj_booking_master->booking_status) && $obj_booking_master->booking_status == 'CANCEL_BY_DRIVER'){
                    $arr_response['status'] = 'error';
                    $arr_response['msg']    = 'You have already cancel trip, cannot cancel trip again.';
                    $arr_response['data']   = [];
                    return $arr_response;
                }

                $obj_booking_master->booking_status = 'CANCEL_BY_DRIVER';
                $obj_booking_master->reason         = $reason;
                $status = $obj_booking_master->save();
                if($status){

                    //send twilio sms eta notifications to user
                    if(isset($client)){
                        $user_country_code   = isset($obj_booking_master->load_post_request_details->user_details->country_code) ? $obj_booking_master->load_post_request_details->user_details->country_code :'';
                        $user_mobile_no      = isset($obj_booking_master->load_post_request_details->user_details->mobile_no) ? $obj_booking_master->load_post_request_details->user_details->mobile_no :'';
                        $user_full_mobile_no = $user_country_code.''.$user_mobile_no;

                        if($user_full_mobile_no!=''){

                            $driver_first_name  = isset($obj_booking_master->load_post_request_details->driver_details->first_name) ? $obj_booking_master->load_post_request_details->driver_details->first_name :'';
                            $driver_last_name   = isset($obj_booking_master->load_post_request_details->driver_details->last_name) ? $obj_booking_master->load_post_request_details->driver_details->last_name :'';
                            $driver_full_name   = $driver_first_name.' '.$driver_last_name;
                            
                            $messageBody         = $driver_full_name.' has cancelled the current trip, sorry for inconvenience.';

                            $this->sendEtaNotificationsMessage(
                                                    $client,
                                                    $user_full_mobile_no,
                                                    $messageBody,
                                                    ''
                                                );
                        }
                    }

                    /*Send on signal notification from user to specific driver*/
                    $arr_notification_data = 
                                            [
                                                'title'             => 'Driver cancel booking request.',
                                                'record_id'         => $booking_id,
                                                'enc_user_id'       => $enc_user_id,
                                                'notification_type' => 'TRIP_CANCEL_BY_DRIVER',
                                                'user_type'         => 'USER',
                                            ];

                    $this->NotificationsService->send_on_signal_notification($arr_notification_data);

                    /*Send on signal notification from user to specific driver*/
                    $arr_notification_data = 
                                            [
                                                'title'             => 'Driver cancel booking request.',
                                                'record_id'         => $booking_id,
                                                'enc_user_id'       => $enc_user_id,
                                                'notification_type' => 'TRIP_CANCEL_BY_DRIVER',
                                                'user_type'         => 'WEB',
                                            ];

                    $this->NotificationsService->send_on_signal_notification($arr_notification_data);

                    $driver_status = 'AVAILABLE';
                    $this->CommonDataService->change_driver_status($login_user_id,$driver_status);

                    $arr_response['status'] = 'success';
                    $arr_response['msg']    = 'You have cancel current trip.';
                    $arr_response['data']   = [];
                    return $arr_response;

                }else{
                    $arr_response['status'] = 'error';
                    $arr_response['msg']    = 'Problem occurred, while update booking request details, Please try again.';
                    $arr_response['data']   = [];
                    return $arr_response;
                }
            }
            $arr_response['status'] = 'error';
            $arr_response['msg']    = 'Problem occurred, while update booking request details, Please try again.';
            $arr_response['data']   = [];
            return $arr_response;
        }
        $arr_response['status'] = 'error';
        $arr_response['msg']    = 'Problem occurred, while update booking request details, Please try again.';
        $arr_response['data']   = [];
        return $arr_response;   
    }

    public function track_live_trip($request)
    {
        $user_id     = validate_user_login_id();
        if ($user_id == 0) 
        {
            $arr_response['status'] = 'error';
            $arr_response['msg']    = 'Invalid driver token';
            $arr_response['data']    = [];
            return $arr_response;
        }
        
        $booking_id = $request->input('booking_id');
        
        if ($booking_id == "") 
        {
            $arr_response['status'] = 'error';
            $arr_response['msg']    = 'Invalid booking ID';
            $arr_response['data']    = [];
            return $arr_response;
        }

        $arr_ongoing_trips = [];
        $obj_ongoing_trips  = $this->BookingMasterModel
                                                ->select('id','load_post_request_id','booking_status')
                                                ->whereHas('load_post_request_details',function($query) use($user_id) {
                                                        $query->whereHas('driver_current_location_details',function($query){
                                                        });
                                                        $query->where('request_status','ACCEPT_BY_USER');
                                                        $query->where('driver_id',$user_id);
                                                })
                                                ->with(['load_post_request_details'=> function($query) use($user_id) {
                                                        $query->select('id','driver_id','vehicle_id','pickup_lat','pickup_lng','drop_lat','drop_lng');
                                                        $query->where('request_status','ACCEPT_BY_USER');
                                                        $query->where('driver_id',$user_id);
                                                        $query->with(['driver_current_location_details'=>function($query){
                                                                    $query->select('id','driver_id','status','current_latitude','current_longitude');
                                                                },
                                                                'vehicle_details'=>function($query){
                                                                    $query->select('id','vehicle_type_id');
                                                                    $query->with(['vehicle_type_details'=>function($query){
                                                                        $query->select('id','vehicle_type','vehicle_type_slug');
                                                                    }]);
                                                                }]);
                                                }])
                                                ->where('id',$booking_id)
                                                ->first();

        if($obj_ongoing_trips)
        {
            $arr_ongoing_trips = $obj_ongoing_trips->toArray();
        }

        if(isset($arr_ongoing_trips) && sizeof($arr_ongoing_trips)>0){
            
            $arr_ongoing_trips['booking_id']        = isset($arr_ongoing_trips['id']) ? $arr_ongoing_trips['id'] : 0 ;
            $arr_ongoing_trips['driver_id']         = isset($arr_ongoing_trips['load_post_request_details']['driver_id']) ? doubleval($arr_ongoing_trips['load_post_request_details']['driver_id']) :0;
            $arr_ongoing_trips['driver_lat']        = isset($arr_ongoing_trips['load_post_request_details']['driver_current_location_details']['current_latitude']) ? doubleval($arr_ongoing_trips['load_post_request_details']['driver_current_location_details']['current_latitude']) :doubleval(0.0);
            $arr_ongoing_trips['driver_lng']        = isset($arr_ongoing_trips['load_post_request_details']['driver_current_location_details']['current_longitude']) ? doubleval($arr_ongoing_trips['load_post_request_details']['driver_current_location_details']['current_longitude']) :doubleval(0.0);
            $arr_ongoing_trips['vehicle_type_slug'] = isset($arr_ongoing_trips['load_post_request_details']['vehicle_details']['vehicle_type_details']['vehicle_type_slug']) ? $arr_ongoing_trips['load_post_request_details']['vehicle_details']['vehicle_type_details']['vehicle_type_slug'] :'';

            $arr_ongoing_trips['driver_distance'] = '';
            $arr_ongoing_trips['driver_duration'] = '';
            
            $driver_latitude  = isset($arr_ongoing_trips['load_post_request_details']['driver_current_location_details']['current_latitude']) ? $arr_ongoing_trips['load_post_request_details']['driver_current_location_details']['current_latitude'] : '';
            $driver_longitude = isset($arr_ongoing_trips['load_post_request_details']['driver_current_location_details']['current_longitude']) ? $arr_ongoing_trips['load_post_request_details']['driver_current_location_details']['current_longitude'] : '';
            $pickup_lat       = isset($arr_ongoing_trips['load_post_request_details']['pickup_lat']) ? doubleval($arr_ongoing_trips['load_post_request_details']['pickup_lat']) : floatval(0);
            $pickup_lng       = isset($arr_ongoing_trips['load_post_request_details']['pickup_lng']) ? doubleval($arr_ongoing_trips['load_post_request_details']['pickup_lng']) : floatval(0);
            $drop_lat         = isset($arr_ongoing_trips['load_post_request_details']['drop_lat']) ? doubleval($arr_ongoing_trips['load_post_request_details']['drop_lat']) : floatval(0);
            $drop_lng         = isset($arr_ongoing_trips['load_post_request_details']['drop_lng']) ? doubleval($arr_ongoing_trips['load_post_request_details']['drop_lng']) : floatval(0);
            $booking_status   = isset($arr_ongoing_trips['booking_status']) ? $arr_ongoing_trips['booking_status'] : '';

            if($booking_status == 'TO_BE_PICKED')
            {
                $driver_origins      = $driver_latitude.",".$driver_longitude;
                $driver_destinations = $pickup_lat.",".$pickup_lng;
                $arr_driver_distance = $this->calculate_distance($driver_origins,$driver_destinations);
                $arr_ongoing_trips['driver_distance'] = isset($arr_driver_distance['distance']) ? $arr_driver_distance['distance'] :'';
                $arr_ongoing_trips['driver_duration'] = isset($arr_driver_distance['duration']) ? $arr_driver_distance['duration'] :'';
            }
            else if($booking_status == 'IN_TRANSIT')
            {
                $driver_origins      = $driver_latitude.",".$driver_longitude;
                $driver_destinations = $drop_lat.",".$drop_lng;
                $arr_driver_distance = $this->calculate_distance($driver_origins,$driver_destinations);
                $arr_ongoing_trips['driver_distance'] = isset($arr_driver_distance['distance']) ? $arr_driver_distance['distance'] :'';
                $arr_ongoing_trips['driver_duration'] = isset($arr_driver_distance['duration']) ? $arr_driver_distance['duration'] :'';
            }
            unset($arr_ongoing_trips['id']);
            unset($arr_ongoing_trips['load_post_request_id']);
            unset($arr_ongoing_trips['load_post_request_details']);
            
            $arr_response['status'] = 'success';
            $arr_response['msg']    = 'track driver details found.';
            $arr_response['data']    = $arr_ongoing_trips;
            return $arr_response;

        }
        $arr_response['status'] = 'error';
        $arr_response['msg']    = 'No track driver details available.';
        $arr_response['data']    = [];
        return $arr_response;
    }

    private function calculate_distance($origins,$destinations)
    {
        $arr_result = [];
        
        $arr_result['distance']        = '';
        $arr_result['actual_distance'] = 0;
        $arr_result['duration']        = '';
        $arr_result['actual_duration'] = 0;

        $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=".$origins."&destinations=".$destinations."&mode=driving&key=AIzaSyCTScU19j-YU1Gt5xrFWlo4dwHoFF1wl-s";
   
        $json = @file_get_contents($url);
        $data = json_decode($json);

        if(isset($data->status) && $data->status == 'OK')
        {
            if(isset($data->rows[0]) && sizeof($data->rows[0])>0)
            {
                if((isset($data->rows[0]->elements[0]) && sizeof($data->rows[0]->elements[0])>0) && $data->rows[0]->elements[0]->status == 'OK')
                {
                    $actual_distance = isset($data->rows[0]->elements[0]->distance->value) ? $data->rows[0]->elements[0]->distance->value :0;
                    $actual_distance_im_miles = 0;
                    
                    if($actual_distance>0){
                        $actual_distance_im_miles = ($actual_distance/1609.344);
                        $actual_distance_im_miles = round($actual_distance_im_miles,2);
                    }   
                    $actual_distance_im_miles = $actual_distance_im_miles.' Miles';

                    $arr_result['distance']        = $actual_distance_im_miles;
                    $arr_result['actual_distance'] = isset($data->rows[0]->elements[0]->distance->value) ? $data->rows[0]->elements[0]->distance->value :0;
                    $arr_result['duration']        = isset($data->rows[0]->elements[0]->duration->text) ? $data->rows[0]->elements[0]->duration->text :'';
                    $arr_result['actual_duration'] = isset($data->rows[0]->elements[0]->duration->value) ? $data->rows[0]->elements[0]->duration->value :0;
                }
            }
        }
        return $arr_result;
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
            return isset($obj_booking_master->id) ? $obj_booking_master->id : 0;
        }
        return 0;
    }

    public function get_driver_deposit_money($request)
    {
        $driver_id     = validate_user_login_id();

        if ($driver_id == 0) 
        {
            $arr_response['status'] = 'error';
            $arr_response['msg']    = 'Invalid user token';
            $arr_response['data']   = [];
            return $arr_response;        
        }
        
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

        $arr_pagination = [];

        $obj_receipt_list = $this->DepositMoneyModel
                                            ->select('id','transaction_id','amount_paid','receipt_image','note','status','date')
                                            ->where('to_user_id',$driver_id)
                                            ->orderBy('id','DESC')
                                            ->paginate($this->per_page);
        
        $arr_receipt_list = [];
        if($obj_receipt_list)
        {
            $arr_receipt_list = $obj_receipt_list ->toArray();
            $arr_pagination = $obj_receipt_list->links();

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
                $arr_data["arr_receipt_list"]['arr_pagination'] = $arr_pagination;

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

    private function check_user_previous_load_post_accepted_status($user_id)
    {
        $date = new \DateTime();        
        $date->modify('-1 hours');
        $formatted_date = $date->format('Y-m-d H:i:s');
        
        $obj_pending_previous_load_post_count = $this->LoadPostRequestModel
                                                    ->where('user_id',$user_id)
                                                    ->where('date', '>',$formatted_date) /*latest 1 hours records will be shown */
                                                    ->where('request_status','ACCEPT_BY_DRIVER')
                                                    ->count();

        return $obj_pending_previous_load_post_count;

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

    public function get_driver_details($driver_id)
    {
        $arr_data = [];

        $obj_data = $this->UserModel
                                ->select('id','first_name','last_name','country_code','mobile_no')
                                ->with('driver_status_details')
                                ->where('id',$driver_id)
                                ->first();

        if($obj_data)
        {
            $arr_data = $obj_data->toArray();
        }
        
        if(isset($arr_data) && count($arr_data)>0)
        {
            $arr_data['lat'] = isset($arr_data['driver_status_details']['current_latitude'])  ?$arr_data['driver_status_details']['current_latitude'] : '';
            $arr_data['lng'] = isset($arr_data['driver_status_details']['current_longitude'])  ?$arr_data['driver_status_details']['current_longitude'] : '';
            unset($arr_data['driver_status_details']);
        }
        return $arr_data;
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
                $vehicle_id = isset($arr_data['vehicle_id']) ? $arr_data['vehicle_id'] :0;

                $arr_driver = $this->CommonDataService->get_user_details($driver_id);

                $first_name = isset($arr_driver['first_name']) ? $arr_driver['first_name'] :'';
                $last_name  = isset($arr_driver['last_name']) ? $arr_driver['last_name'] :'';

                $full_name  = $first_name.' '.$last_name;
                $full_name  = ($full_name!=' ') ? $full_name : '-';
                
                $arr_notification['notification_type'] = 'Driver update vehicle details.';
                $arr_notification['title']             = $full_name.' updated vehicle details,Check document and verify  vehicle.';
                //$arr_notification['view_url']          = '/'.config('app.project.admin_panel_slug')."/driver_vehicle?vehicles_type=individual";

                 $view_url = '';
                if($driver_id!=0 && $vehicle_id!=0)
                {
                    $view_url = '/'.config('app.project.admin_panel_slug')."/vehicle/view/".base64_encode($vehicle_id);  
                }
                else
                {
                    $view_url = '/'.config('app.project.admin_panel_slug')."/driver_vehicle";   
                }
                $arr_notification['view_url']          = $view_url;

            }
        }   
        return $arr_notification;
    }

}   
?>