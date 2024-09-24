<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DriverStatusModel extends Model
{
    protected $table = 'driver_available_status';
 	
    protected $fillable = [
                            'driver_id',
                            'status',
                            'current_latitude',
                            'current_longitude'
    					  ];

    public function driver_details()
    {
        return $this->belongsTo('App\Models\UserModel','driver_id','id')->select('id','is_user_login','device_id','reset_password_mandatory');
    }

}