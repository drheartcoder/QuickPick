<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReviewModel extends Model
{
    protected $table = 'review';
 	
    protected $fillable = [
                            'from_user_id',
                            'to_user_id',
                            'booking_id',
                            'user_type',
                            'rating_tag_id',
                            'rating',
                            'rating_msg'
    					  ];

    public function from_user_details()
    {
        return $this->belongsTo('App\Models\UserModel','from_user_id','id')->select('id','first_name','last_name', 'profile_image');
    }

    public function to_user_details()
    {
        return $this->belongsTo('App\Models\UserModel','to_user_id','id')->select('id','first_name','last_name','country_name','profile_image');
    }

    public function rating_tag_details()
    {
        return $this->belongsTo('App\Models\ReviewTagModel','rating_tag_id','id')->select('id','review_image','tag_name');
    }
}
