<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class QuestionReport extends Model
{
    protected $table = 'examination';
    protected $connection = 'm17web';

    public function topic() {
        return $this->belongsTo('App\Models\Topic', 'topic_id', 'topic_id');
    }

    public function question() {
        return $this->belongsTo('App\Models\Library', 'question_id', 'question_id');
    }

    public function user() {
    	return $this->belongsTo('App\Models\Users', 'user_id', 'user_id');
    }
}
