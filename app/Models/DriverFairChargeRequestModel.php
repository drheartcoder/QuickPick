<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DriverFairChargeRequestModel extends Model
{
    protected $table = 'driver_fair_charge_request';
 	
    protected $fillable = [
                            'driver_id',
                            'fair_charge',
                            'status'
    					  ];
				  
}
