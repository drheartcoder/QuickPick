<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserReferralHistoryModel extends Model
{
    protected $table 	  = 'user_referral_history';
    protected $primaryKey = 'id';
    protected $fillable   = ['user_id',
    						'referral_id',
    						'referral_code'
    						];
}
