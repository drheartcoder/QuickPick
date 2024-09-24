<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CountryModel extends Model
{
    protected $table = 'country';
    protected $primaryKey = 'id';
    protected $fillable = ['country_code','country_name','phone_code','is_active'];

    public function states()
    {
        return $this->hasMany('App\Models\StateModel','country_id','id');
    }

   	public function cities()
    {
        return $this->hasMany('App\Models\CityModel','country_id','id');
    }

    public function delete()
    {
        $this->states()->delete();
        $this->cities()->delete();
        return parent::delete();
    }
}
