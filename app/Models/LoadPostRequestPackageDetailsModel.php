<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoadPostRequestPackageDetailsModel extends Model
{
    protected $table = 'load_post_request_package_details';
 	
    protected $fillable = [
							'load_post_request_id',
							'selected_vehicle_type_id',
							'package_type',
							'package_length',
							'package_breadth',
							'package_height',
							'package_volume',
							'package_weight',
							'package_quantity'
						];
}