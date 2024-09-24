<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingMasterCoordinateModel extends Model
{
    protected $table 	= 'booking_master_coordinate';
    protected $fillable = [
                            'booking_master_id',
                            'start_location_lat',
                            'start_location_lng',
							'tmp_start_location_lat',
							'tmp_start_location_lng',
							'total_distance_in_km',
                            'coordinates',
                            'map_image'
                           ];

                            
}
