<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DriverDepositModel extends Model
{
   protected $table = 'driver_deposit';
 	
    protected $fillable = [
                            'driver_id',
                            'amount',
                            'message',
                            'receipt_image',
                            'receipt_uploaded_date',
                            'status',
                            'is_last_entry'
    					  ];

    	public function driver_details()
		  {
		  	return $this->hasOne('App\Models\UserModel','id','driver_id');
		  }

}
