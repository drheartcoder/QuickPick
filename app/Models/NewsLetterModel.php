<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsLetterModel extends Model
{	
    protected $table = 'newsletters';
    protected $fillable = ['title','subject','news_message','is_active'];
}
