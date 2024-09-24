<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DepositMoneyModel extends Model
{
   	protected $table = 'deposit_money';
 	
    protected $fillable = [
                            'booking_master_id',
                            'from_user_id',
                            'to_user_id',
                            'transaction_id',
                            'amount_paid',
                            'receipt_image',
                            'note',
                            'status',
                            'from_user_type',
                            'to_user_type',
                            'date',
                            'payment_data',
                            'deposit_money_history'
    					  ];

    public function booking_master_details()
    {
        return $this->belongsTo('App\Models\BookingMasterModel','booking_master_id','id')->select('id','booking_unique_id');
    }
    public function to_user_details()
    {
        return $this->belongsTo('App\Models\UserModel','to_user_id','id')->select('id','first_name','last_name','email','mobile_no','company_name','is_company_driver','stripe_account_id');
    }
}