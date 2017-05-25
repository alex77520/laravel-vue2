<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Review extends Model
{
    protected $table = 'reviews';
    protected $connection = 'm17web';

    public function discuss() {
        return $this->belongsTo('App\Models\Discuss', 'review_id', 'discuss_id');
    }

    public function viewpoint() {
        return $this->belongsTo('App\Models\Viewpoint', 'review_id', 'viewpoint_id');
    }
}
