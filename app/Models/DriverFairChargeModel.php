<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DriverFairChargeModel extends Model
{
    protected $table = 'driver_fair_charges';
 	
    protected $fillable = [
                            'driver_id',
                            'fair_charge'
    					  ];

    public function driver_fair_charge_request_details()
    {
        return $this->hasOne('App\Models\DriverFairChargeRequestModel','driver_id','driver_id');
    }
}
