<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VehicleTypeModel extends Model
{
    protected $table = 'vehicle_type';
    protected $primaryKey = 'id';
    
    protected $fillable =  [
    							'vehicle_type',
    							'vehicle_type_slug',
                                'starting_price',
                                'per_miles_price',
                                'per_minute_price',
    							'minimum_price',
    							'cancellation_base_price',
                                'is_usdot_required',
                                'is_mcdoc_required',
    							'vehicle_min_length',
    							'vehicle_max_length',
    							'vehicle_min_height',
    							'vehicle_max_height',
    							'vehicle_min_breadth',
    							'vehicle_max_breadth',
    							'vehicle_min_weight',
    							'vehicle_max_weight',
    							'vehicle_min_volume',
                                'vehicle_max_volume',
    							'no_of_pallet',
    							'is_active'
    						];
        
}
