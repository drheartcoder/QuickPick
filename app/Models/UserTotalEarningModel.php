<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserTotalEarningModel extends Model
{
     protected $table 	 = 'user_total_earning';
     protected $fillable = [
     						 'user_id',
     						 'user_type',
     						 'total_earning'
     					   ];

	public function driver_details()
   	{
        	return $this->belongsTo('App\Models\UserModel','user_id','id')->select('id','first_name','last_name');
    }
   
}
