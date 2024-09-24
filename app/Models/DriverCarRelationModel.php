<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DriverCarRelationModel extends Model
{
    protected $table = 'driver_car_relation';
 	
    protected $fillable = [
                            'driver_id',
                            'vehicle_id',
                            'is_car_assign',
                            'is_individual_vehicle',
    					  ];

   	public function driver_details()
    {
        return $this->belongsTo('App\Models\UserModel','driver_id','id')->select('id','first_name','last_name','email','mobile_no','company_id','country_code','driving_license','is_driving_license_verified','account_status','is_deleted');
    }
    public function vehicle_details()
    {
        return $this->belongsTo('App\Models\VehicleModel','vehicle_id','id')->withTrashed()->with('vehicle_type_details')->select('id','vehicle_type_id','is_individual_vehicle','vehicle_brand_id','vehicle_model_id','vehicle_year_id','vehicle_number','is_verified','vehicle_image','registration_doc','proof_of_inspection_doc','insurance_doc','dmv_driving_record','usdot_doc','mc_doc','is_vehicle_image_verified','is_registration_doc_verified','is_insurance_doc_verified','is_proof_of_inspection_doc_verified','is_dmv_driving_record_verified','is_usdot_doc_verified','is_mcdoc_doc_verified','is_deleted');
    }
    /*public function vehicle_details_company()
    {
        return $this->belongsTo('App\Models\VehicleModel','vehicle_id','id')->select('id','driver_id','company_id','vehicle_type_id','vehicle_name','vehicle_model_name','vehicle_number');
    }*/
}