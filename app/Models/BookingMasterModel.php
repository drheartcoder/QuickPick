<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingMasterModel extends Model
{
     protected $table 	= 'booking_master';
     protected $fillable = [
                                'id',
                                'load_post_request_id',
                                'booking_unique_id',
                                'transaction_unique_id',
                                'booking_date',
                                'start_datetime',
                                'end_datetime',
                                'total_minutes_trip',
                                'start_trip_image',
                                'invoice_image',
                                'po_no',
                                'receiver_name',
                                'receiver_no',
                                'app_suite',
                                'end_trip_image',
                                'card_id',
                                'is_promo_code_applied',
                                'promo_code',
                                'promo_percentage',
                                'promo_max_amount',
                                'applied_promo_code_charge',
                                'is_company_driver',
                                'is_individual_vehicle',
                                'starting_price',
                                'per_miles_price',
                                'per_minute_price',
                                'minimum_price',
                                'cancellation_base_price',
                                'admin_driver_percentage',
                                'admin_company_percentage',
                                'individual_driver_percentage',
                                'company_driver_percentage',
                                'is_bonus_used',
                                'admin_referral_points',
                                'admin_referral_points_price_per_usd',
                                'user_bonus_points',
                                'user_bonus_points_usd_amount',
                                'distance',
                                'total_charge',
                                'total_amount',
                                'admin_amount',
                                'company_amount',
                                'admin_driver_amount',
                                'company_driver_amount',
                                'individual_driver_amount',
                                'admin_payment_status',
                                'payment_type',
                                'payment_status',
                                'payment_response',
                                'booking_status',
                                'reason',
                                'is_eta_notification_send'
                           ];
    
    public function driver_details()
    {
        return $this->belongsTo('App\Models\UserModel','driver_id','id')->select('id','first_name','last_name','profile_image','email','mobile_no','company_id');
    }
    public function rider_details()
    {
        return $this->belongsTo('App\Models\UserModel','user_id','id')->select('id','first_name','last_name','profile_image','email','mobile_no');
    }

    /* public function driver_company_details()
    {
        return $this->belongsTo('App\Models\UserModel','driver_id','id')->select('id','first_name','last_name','profile_image','email','mobile_no','company_id');
    } */

    public function vehicle_details()
    {
        return $this->belongsTo('App\Models\VehicleModel','vehicle_id','id')->select('id','vehicle_type_id','vehicle_name','vehicle_model_name','vehicle_number')->with('vehicle_brand_details','vehicle_model_details');    
    }
    
    public function vehicle_info()
    {
        return $this->belongsTo('App\Models\VehicleModel','vehicle_id','id')->with("vehicle_type_details");
    }

    public function load_post_request_details()
    {
        return $this->belongsTo('App\Models\LoadPostRequestModel','load_post_request_id','id');
    }

    // public function booking_master_coordinate_details()
    // {
    //     return $this->belongsTo('App\Models\BookingMasterCoordinateModel','id','booking_master_id');
    // }

    public function booking_master_coordinate_details()
    {
        return $this->hasOne('App\Models\BookingMasterCoordinateModel','booking_master_id','id');
    }
    
        
    public function review_details()
    {
        return $this->belongsTo('App\Models\ReviewModel','id','booking_id');
    }   
}