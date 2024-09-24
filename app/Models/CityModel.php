<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CityModel extends Model
{
    protected $table = 'city';
    protected $primaryKey = 'id';
    protected $fillable = ['city_name','is_active'];
}
