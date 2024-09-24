<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DriverCarRelationHistoryModel extends Model
{
	protected $table = 'driver_car_relation_history';
 	
 	protected $fillable = [
                            'driver_id',
                            'vehicle_id',
                            'status'
    					  ];

}
