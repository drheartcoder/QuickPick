<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class VehicleModelModel extends Model
{
  	use SoftDeletes;
	protected $table 	  = 'vehicle_model';
    protected $primaryKey = 'id';

    protected $fillable   = [
                                'id',
                                'vehicle_brand_id',
                                'name'
                            ];
}
