<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoadPostRequestModel extends Model
{
    protected $table = 'load_post_request';
 	
    protected $fillable = [
    						'load_post_request_unique_id',
                            'card_id',
	 						'user_id',
							'driver_id',
							'vehicle_id',
                            'promo_code_id',
                            'is_bonus',
							'date',
                            'request_time',
							'pickup_location',
							'drop_location',
							'pickup_lat',	
							'pickup_lng',
							'drop_lat',	
							'drop_lng',	
                        	'request_status',	
							'reason',
							'request_type',
							'load_post_image',
                            'is_admin_assistant',
                            'is_admin_assign',
                            'is_future_request',
                            'is_request_process'
    					  ];

    public function load_post_request_history_details()
    {
        return $this->hasMany('App\Models\LoadPostRequestHistoryModel','load_post_request_id','id');
    }
    public function load_post_request_package_details()
    {
        return $this->belongsTo('App\Models\LoadPostRequestPackageDetailsModel','id','load_post_request_id');
    }
    public function driver_current_location_details()
    {
        return $this->belongsTo('App\Models\DriverStatusModel','driver_id','driver_id');
    }
    public function driver_details()
    {
        return $this->belongsTo('App\Models\UserModel','driver_id','id')->withTrashed();
    }
    public function user_details()
    {
        return $this->belongsTo('App\Models\UserModel','user_id','id')->withTrashed();
    }
    public function vehicle_details()
    {
        return $this->belongsTo('App\Models\VehicleModel','vehicle_id','id')->withTrashed();
    }
}