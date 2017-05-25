<?php

namespace App\Http\Controllers\Feedback;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Feedback;

class FeedbackController extends Controller {

	protected $pageSize = 30;

	public function get(Request $request) {
		$feedbackModels = Feedback::with('users')->orderBy('created_at', 'desc')->paginate($this->pageSize);
		$tableData = [];
		foreach ($feedbackModels as $model) {
			if(! isset($model->users)) continue;
			$tableData[] = [
				'id'	 		=> $model->id,
				'content' 		=> $model->content,
				'connect_phone' => $model->connect_phone,
				'created_at'	=> $model->created_at->format("Y-m-d H:i:s"),
				'user_id'		=> $model->users->user_id,
				'user_name'		=> $model->users->user_name,
			];
		}

		$data['tableData'] 	= $tableData;
		$data['total'] 		= $feedbackModels->total();
		$data['pageSize'] 	= $this->pageSize;

		return returnData($data);
	}
}