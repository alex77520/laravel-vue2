<?php 

namespace App\Http\Requests;

use Illuminate\Http\Request;

class CreateRecommendFormRequest extends Request
{
    public $recommend_id;
    public $sort;
    public $type;
    public $status;
   
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