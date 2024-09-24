<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class VehicleYearModel extends Model
{
    use SoftDeletes;
	protected $table 	  = 'vehicle_year';
    protected $primaryKey = 'id';

    protected $fillable   = [
                                'id',
                                'vehicle_model_id',
                                'year'
                            ];
}
