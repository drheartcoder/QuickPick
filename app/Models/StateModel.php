<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StateModel extends Model
{
    protected $table = 'state';
    protected $primaryKey = 'id';
    protected $fillable = ['state_name','is_active'];

 
   	public function cities()
    {
        return $this->hasMany('App\Models\CityModel','state_id','id');
    }
}
