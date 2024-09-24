<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingPackageModel extends Model
{
     protected $table 	= 'booking_package';
     protected $fillable = [
                             'booking_id',
                             'package_type',
                             'package_length',
                             'package_breadth',
                             'package_height',
                             'package_volume',
                             'package_weight',
                             'package_quantity'
                           ];

}
