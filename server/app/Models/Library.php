<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Library extends Model
{
    protected $connection = 'm17web';
    protected $table = 'question';

    public function users() {
    	return $this->belongsTo('App\Models\Users', 'user_id', 'user_id');
    }

    public function topic() {
    	return $this->belongsTo('App\Models\Topic', 'topic_id', 'topic_id');
    }

    public static function fetchModelById($id) {
    	return static::find($id);
    }

    public static function getQuestionByQuestionId($questionId) {
        return static::where('question_id', '=', $questionId)->first();
    }
}
