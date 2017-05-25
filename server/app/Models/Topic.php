<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Topic extends Model
{
    protected $table = 'topic';
    protected $connection = 'm17web';



    public function setAttributes(&$model, $data) {
        foreach($data as $key => $val) {
            if(property_exists($model, $key)) {
                $model->$key = $val;
            }
        }
    }

    public static function getTopicMap($key, $value) {
        $models = static::all();
        $data = [];
        foreach($models as $model) {
            if(! isset($model->$key, $model->$value)) {
                break;
            }
            $data[$model->$key] = $model->$value;
        }

        return $data;
    }

    public static function getTopicByIds($ids) {
        return static::whereIn('topic_id', $ids)->get();
    }

    public static function checkExistById($id) {
        return static::where('topic_id', '=', $id)->first() !== null;
    }
}
