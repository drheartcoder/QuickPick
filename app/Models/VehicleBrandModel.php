<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class VehicleBrandModel extends Model
{
	use SoftDeletes;
	protected $table 	  = 'vehicle_brand';
    protected $primaryKey = 'id';

    protected $fillable   = [
                                'id',
                                'name'
                            ];
}
