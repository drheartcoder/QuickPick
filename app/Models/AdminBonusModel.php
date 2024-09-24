<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminBonusModel extends Model
{
 	protected $table    = 'admin_bonus_points';

    protected $fillable = ['referral_points' , 'referral_points_price'];
}
