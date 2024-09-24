<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Cartalyst\Sentinel\Users\EloquentUser as CartalystUser;

use Illuminate\Database\Eloquent\SoftDeletes;

class UserModel extends CartalystUser
{
    use SoftDeletes;
    
    protected $fillable = [
                    		'email',
                            'password',
                            'user_type',
                            'last_name',
                            'first_name',
                            'is_company_user',
                            'permissions',
                            'profile_image',
                            'gender',
                            'dob',
                            'is_active',
                            'is_otp_verified',
                            'is_user_block_by_admin',
                            'via_social',
                            'country_name',
                            'state_name',
                            'city_name',
                            'latitude',
                            'longitude',
                            /*'country_id',
                            'state_id',
                            'city_id',*/
                            'mobile_no',
                            'country_code',
                            'address',
                            'post_code',
                            'is_driving_license_verified',
                            'driving_license',
                            'enterprise_license',
                            'contact_otp',
                            'otp',
                            'email_verification',
                            // 'contact_verification',
                            'company_name',
                            'company_id',
                            'driver_count',
                            'account_status',
                            'referral_code',
                            'is_company_driver',
                            // 'new_otp',
                            // 'is_mobile_otp_verified',
                            'my_points',
                            'is_user_login',
                            'device_id',
                            'reset_password_mandatory',
                            'stripe_customer_id',
                            'stripe_account_id',
                            'stripe_account_response',
                            'is_deleted'
                        ];
    
    protected $loginNames = [ 'email', 'mobile_no','user_type'];

    public function country_details()
    {
        return $this->belongsTo('App\Models\CountryModel','country_id','id')->select('id','country_name');
    }
    
    public function state_details()
    {
        return $this->belongsTo('App\Models\StateModel','state_id','id')->select('id','state_name');
    }
    
    public function city_details()
    {
        return $this->belongsTo('App\Models\CityModel','city_id','id')->select('id','city_name');
    }

    public function company_details()
    {
        return $this->belongsTo('App\Models\UserModel','company_id','id')->select('id','company_name','email','mobile_no','stripe_account_id');
    }

    public function driver_car_relations()
    {
        return $this->hasOne('App\Models\DriverCarRelationModel','driver_id','id')->select('id','driver_id');
    }
    // -------------------------------------------------------------------------------------------------------
    public function driver_car_details()
    {
        return $this->belongsTo('App\Models\DriverCarRelationModel','id','driver_id');
    }
    public function driver_vehicle_details()
    {
        return $this->belongsTo('App\Models\DriverCarRelationModel','id','driver_id')->with('vehicle_details');
    }
    public function driver_fair_charge_details()
    {
        return $this->belongsTo('App\Models\DriverFairChargeModel','id','driver_id')->select('id','driver_id','fair_charge');
    }
    public function user_role_details()
    {
        return $this->belongsTo('App\Models\UserRoleModel','id','user_id')->with('user_role_type_details');
    }
    public function driver_status_details()
    {
        return $this->belongsTo('App\Models\DriverStatusModel','id','driver_id')->select('id','driver_id','status','current_latitude','current_longitude');
    }
    public function ride_details()
    {
        return $this->belongsTo('App\Models\RideModel','id','driver_id')->select('id','driver_id','status');
    }

    public function message_details()
    {
        return $this->hasMany('App\Models\MessagesModel','from_user_id','id');
    }

    public function unread_message_count()
    {
        return $this->belongsTo('App\Models\MessagesModel','id','from_user_id')->select('id','from_user_id','to_user_id',\DB::raw('count(*) as message_count'))->where('is_read','0')->groupBy('from_user_id');
    }
}