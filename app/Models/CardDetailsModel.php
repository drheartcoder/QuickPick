<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CardDetailsModel extends Model
{
    protected $table    = 'card_details';

    protected $fillable = ['user_id' ,'card_id','src_token','masked_card_number','unique_number_identifier','brand','payment_method'];
    
    public function user_details()
    {
        return $this->belongsTo('App\Models\UserModel','user_id','id');
    }

}