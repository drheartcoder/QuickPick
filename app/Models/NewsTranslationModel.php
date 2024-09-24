<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Watson\Rememberable\Rememberable;

class NewsTranslationModel extends Model
{
    use Rememberable;
	
    protected $table='news_translation';
    
    public $timestamps = false;

    protected $fillable = ['news_id','locale','title','description'];
}
