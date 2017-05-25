<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Report extends Model
{
    protected $table = 'tip_off';
    protected $connection = 'm17web';

    public function discuss() {
        return $this->belongsTo('App\Models\Discuss', 'target_id', 'discuss_id');
    }

    public function viewpoint() {
        return $this->belongsTo('App\Models\Viewpoint', 'target_id', 'viewpoint_id');
    }

    public function users() {
    	return $this->belongsTo('App\Models\Users', 'user_id', 'user_id');
    }
}
