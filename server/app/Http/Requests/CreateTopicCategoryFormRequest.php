<?php 

namespace App\Http\Requests;

use Illuminate\Http\Request;

class CreateTopicCategoryFormRequest extends Request
{
    // public $topiId;
    public $id;
    public $cate_id;
    public $cate_name;
    public $parent_id;
    public $created_at;
    public $status;
    public $category_level;
    public $sort;

    public function rules()
    {
        return [
            
        ];
    }

    public function setAttributes(&$model, $data) {
        foreach($data as $key => $val) {
            if(property_exists($model, $key)) {
                $model->$key = $val;
            }
        }
    }
}