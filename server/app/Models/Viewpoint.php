<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Viewpoint extends Model
{   
    const DELETED = 1;
    const UNDELETED = 0;
    protected $connection = 'm17web';
    protected $table = 'viewpoint';

     public function discuss() {
        return $this->belongsTo('App\Models\Discuss', 'discuss_id', 'discuss_id');
    }

    public function viewpoint() {
        return $this->belongsTo('App\Models\Viewpoint', 'parent_id', 'viewpoint_id');
    }

    public static function reportAssociate($ids) {
        return static::whereIn('viewpoint_id', $ids)->get();
    }

    public static function getModelByIds($ids) {
        return static::whereIn('viewpoint_id', $ids)->get();
    }

    public static function deletedByIds($ids) {
        if(empty($ids)) return true;
        return static::whereIn('viewpoint_id', $ids)->update(['deleted' => static::DELETED]);
    }

    public static function undeletedByIds($ids) {
        if(empty($ids)) return true;
        return static::whereIn('viewpoint_id', $ids)->update(['deleted' => static::UNDELETED]);
    }

}
