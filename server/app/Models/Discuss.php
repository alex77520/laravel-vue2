<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Discuss extends Model
{
    const UNDELETED = 1;
    const DELETED = 1;
    protected $table = 'discuss';
    protected $connection = 'm17web';
    

    public static function getDiscussByIds($ids) {
    	return static::whereIn('discuss_id', $ids)->get();
    }

    public static function checkExistById($id) {
        return static::where('discuss_id', '=', $id)->first() !== null;
    }

    public static function getModelByIds($ids) {
        return static::whereIn('discuss_id', $ids)->get();
    }

    public static function deletedByIds($ids) {
        if(empty($ids)) return true;
        return static::whereIn('discuss_id', $ids)->update(['deleted' => static::DELETED]);
    }

    public static function undeletedByIds($ids) {
        if(empty($ids)) return true;
        return static::whereIn('discuss_id', $ids)->update(['deleted' => static::UNDELETED]);
    }

  
}
