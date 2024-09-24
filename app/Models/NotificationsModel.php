<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationsModel extends Model
{
    protected $table 	= 'notifications';
    protected $fillable = ['user_id','is_read','is_show','user_type','notification_type','title', 'description','view_url'];

	/*public function user_details()
	{
	    return $this->belongsTo('App\Models\UserModel','user_id','id')->select('id','first_name','last_name');
	}*/
}
