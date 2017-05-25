<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recommend extends Model {
    protected $connection = 'm17web';
    protected $table = 'recommend';

    public static function checkExist($id) {
    	return static::where('recommend_id', '=', $id)->first();
    }

}