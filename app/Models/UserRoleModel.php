<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserRoleModel extends Model
{

    protected $table = 'role_users';
 	
    protected $fillable = [
                            'user_id',
                            'role_id',
    					  ];
}
