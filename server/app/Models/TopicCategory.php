<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TopicCategory extends Model {
    protected $connection = 'm17web';
    protected $table = 'topic_cate';

  	public function parentCategory() {
    	return $this->belongsTo('App\Models\TopicCategory', 'parent_id', 'cate_id');
    }
}