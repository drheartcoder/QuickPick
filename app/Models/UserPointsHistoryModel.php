<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPointsHistoryModel extends Model
{
    protected $table 	  = 'user_points_history';
    protected $primaryKey = 'id';
    protected $fillable   = ['user_id',
    						'points',
    						'type',
    						'origin',
    						'origin_id'
    						];
}
