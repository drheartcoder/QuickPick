<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminCommissionModel extends Model
{
    protected $table    = 'admin_commission';

    protected $fillable = ['admin_driver_percentage','company_percentage','individual_driver_percentage'];
}
