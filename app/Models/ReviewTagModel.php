<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReviewTagModel extends Model
{
    protected $table = 'review_tags';
 	
    protected $fillable = [
                            'tag_name',
                            'review_image',
                            'is_active'
    					  ];
}
