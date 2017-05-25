<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminUsers extends Model
{
    // protected $connection = 'm17admin';
    // protected $table = 'admin_users';


    public static function getUserByName($username) {
    	//临时数据
    	return [
    		'id' 		=> 1,
    		'username'	=> "admin",
    	];
    }
}
