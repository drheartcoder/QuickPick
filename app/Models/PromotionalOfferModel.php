<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromotionalOfferModel extends Model
{
    protected $table = 'promotional_offer';
 	
    protected $fillable = [
                            'banner_title',
    						'banner_image',
                            'is_active'
    					  ];
}
