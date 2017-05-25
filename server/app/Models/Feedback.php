<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feedback extends Model {
    protected $connection = 'm17web';
    protected $table = 'feedback';

     public function users() {
    	return $this->belongsTo('App\Models\Users', 'user_id', 'user_id');
    }
}