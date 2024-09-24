<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssignedAreaModel extends Model
{
    protected $table = 'assigned_area';
 	
    protected $fillable = [
                            'id',
    						'user_id',
    						'user_type',
                            'is_active',
                            'co-ordinates',
                            'name'
    					  ];

    public function user_details()
	{
    	return $this->belongsTo('App\Models\UserModel','user_id','id')->select('id','first_name','last_name');

	}					  
}
