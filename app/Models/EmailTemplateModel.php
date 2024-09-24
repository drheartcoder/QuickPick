<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Watson\Rememberable\Rememberable;
use \Dimsav\Translatable\Translatable;

class EmailTemplateModel extends Model
{
	use Rememberable;
	use Translatable;

    protected $table   =  'email_template';

    public $translationModel      = 'App\Models\EmailTemplateTranslationModel';
    public $translationForeignKey = 'email_template_id';
    public $translatedAttributes  = ['email_template_id',
                                     'template_subject',
                                     'template_html'];

    protected $fillable = [
                            'template_name',
                            'template_from',
                            'template_from_mail',
                            'template_variables'
                          ];

    public function template_details()
    {
        return $this->hasMany('App\Models\EmailTemplateTranslationModel','email_template_id','id');
    }                      
}
