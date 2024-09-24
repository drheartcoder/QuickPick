<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PackageTypeModel extends Model
{
    protected $table = 'package_type';
    protected $primaryKey = 'id';
    
    protected $fillable =  [
    							'name',
    							'slug',
                                'is_special_type',
                                'is_active'
    						];
}
