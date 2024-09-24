<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyCommissionModel extends Model
{
    protected $table    = 'company_commission';

    protected $fillable = ['company_id','driver_percentage'];
}
