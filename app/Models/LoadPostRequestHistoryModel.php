<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoadPostRequestHistoryModel extends Model
{
    protected $table = 'load_post_request_history';
 	
    protected $fillable = [
    						'load_post_request_id',
	 						'user_id',
							'driver_id',
							'vehicle_id',
							'status',
							'is_admin_assign',
							'reason',
    					  ];
}
