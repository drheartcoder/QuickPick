<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class ContactEnquiryModel extends Model
{
    
    protected $table = "contact_enquiry";

    protected $fillable = ['first_name','last_name','email','phone','subject','comments','address','is_view'];

    public function delete()
    {	
    	parent::delete();
    }
}
