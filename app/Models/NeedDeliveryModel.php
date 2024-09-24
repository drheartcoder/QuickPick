<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class NeedDeliveryModel extends Model
{
    
    protected $table = "need_delivery";

    protected $fillable = ['first_name','last_name','email','phone','subject','is_view'];

    public function delete()
    {	
    	parent::delete();
    }
}
