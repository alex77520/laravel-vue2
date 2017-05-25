<?php 

namespace App\Http\Requests;

use Illuminate\Http\Request;

class CreateQuestionReportFormRequest extends Request
{
    public $id;
    public $topic_id;
    public $question_id;
    public $question;
    public $right_answer;
    public $total_answer;
    public $reason;
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