<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromoOfferModel extends Model
{
    protected $table = 'promo_offer';
 	
    protected $fillable = [
    						'code_type',
                            'validity_from',
                            'validity_to',
                            'percentage',
                            'max_amount',
                            'code',
                            'promo_code_usage_limit',
                            'is_active'
    					  ];

    public function promo_offer_applied_details()
    {
        return $this->hasMany('App\Models\PromoOfferAppliedDetailsModel','promo_code_id','id');
    }
}
