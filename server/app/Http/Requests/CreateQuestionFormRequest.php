<?php 

namespace App\Http\Requests;

use Illuminate\Http\Request;

class CreateQuestionFormRequest extends Request
{
    public $id;
    public $question;
    public $right_answer;
    public $total_answer;
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