<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromoOfferAppliedDetailsModel extends Model
{
    protected $table = 'promo_offer_applied_details';
 	
    protected $fillable = [
    						'promo_code_id',
                            'user_id',
                            'promo_applied_date'
    					  ];
}
