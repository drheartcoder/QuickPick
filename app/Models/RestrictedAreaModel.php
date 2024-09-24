<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RestrictedAreaModel extends Model
{
     protected $table = 'restricted_area';
 	
    protected $fillable = [
                            'id',
    						'user_id',
    						'user_type',
                            'is_active',
                            'co-ordinates',
                            'name'
    					  ];

}
