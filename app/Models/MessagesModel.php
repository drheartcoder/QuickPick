<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MessagesModel extends Model
{
    protected $table	=	"message";
    protected $fillable	=	['request_id','from_user_id','to_user_id','message','is_read'];
}
