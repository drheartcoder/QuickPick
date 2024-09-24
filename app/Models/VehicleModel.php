<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class VehicleModel extends Model
{
    use SoftDeletes;
    
    protected $table 	  = 'vehicle';
    protected $primaryKey = 'id';

    protected $fillable   = [
                                'company_id',
                                'is_individual_vehicle',
                                'is_company_vehicle',
                                'vehicle_type_id',
                                'vehicle_brand_id',
                                'vehicle_model_id',
                                'vehicle_year_id',
                                'vehicle_number',
                                'is_active',
                                'is_verified',
                                'vehicle_image',
                                'registration_doc',
                                'insurance_doc',
                                'proof_of_inspection_doc',
                                'dmv_driving_record',
                                'usdot_doc',
                                'mc_doc',
                                'is_vehicle_image_verified',
                                'is_registration_doc_verified',
                                'is_insurance_doc_verified',
                                'is_proof_of_inspection_doc_verified',
                                'is_dmv_driving_record_verified',
                                'is_usdot_doc_verified',
                                'is_mcdoc_doc_verified',
                                'is_deleted',
                            ];

    public function vehicle_type_details()
    {
        return $this->belongsTo('App\Models\VehicleTypeModel','vehicle_type_id','id')->select('id','vehicle_type','vehicle_type_slug','starting_price','per_miles_price','per_minute_price','minimum_price','cancellation_base_price','is_usdot_required','is_mcdoc_required','vehicle_min_weight','vehicle_max_weight','vehicle_min_volume','vehicle_max_volume','no_of_pallet');
    }

    public function driver_car_details()
    {
        return $this->belongsTo('App\Models\DriverCarRelationModel','id','vehicle_id');
    }

    public function company_details()
    {
        return $this->belongsTo('App\Models\UserModel','company_id','id');
    }
    
    public function car_driver_details()
    {
        return $this->belongsToMany('App\Models\UserModel','driver_car_relation','vehicle_id','driver_id','id');
    }
    public function vehicle_brand_details()
    {
        return $this->belongsTo('App\Models\VehicleBrandModel','vehicle_brand_id','id');
    }
    public function vehicle_model_details()
    {
        return $this->belongsTo('App\Models\VehicleModelModel','vehicle_model_id','id');
    }
    /*public function driver_details()
    {
        return $this->belongsTo('App\Models\UserModel','driver_id','id');
    }*/
}
