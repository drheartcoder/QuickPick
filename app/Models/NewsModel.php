<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use \Dimsav\Translatable\Translatable;

class NewsModel extends Model
{
    use Translatable;
    use SoftDeletes;

    protected $table = "news";

    
    public $translationModel 	  = 'App\Models\NewsTranslationModel';
    public $translationForeignKey = 'news_id';
    public $translatedAttributes  = ['title','description'];

    protected $fillable = ['is_active','image'];

    public function delete()
    {
        $this->translations()->delete();
        return parent::delete();
    }
}
