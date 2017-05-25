<?php 

namespace App\Http\Requests;

use Illuminate\Http\Request;

class CreateTopicFormRequest extends Request
{
    public $id;
    public $title;
    public $alias_id;
    public $created_at;
    public $username;
    public $categoryOptions = [];
    public $icon;
    public $description;
    public $status;
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