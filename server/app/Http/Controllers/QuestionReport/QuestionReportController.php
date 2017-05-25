<?php

namespace App\Http\Controllers\QuestionReport;

use App\Http\Controllers\Controller;
use App\Models\Users;
use Illuminate\Http\Request;
use App\Models\Topic;
use App\Models\Library;
use App\Http\Requests\CreateQuestionReportFormRequest;
use App\Models\QuestionReport;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\McLibs\Classes\IdFactory;

class QuestionReportController extends Controller {

	protected $pageSize = 30;
	protected $filters = ['status' => '='];
	
	protected function filtersParse($params) {
		$whereFilters = [];

		foreach ($params as $key => $value) {
			if(isset($this->filters[$key]) && $this->filters[$key] !== null && $value !== null) {
				if($this->filters[$key] === 'like') {
					$value = "%" . $value . "%";
				}
				$whereFilters[] = [$key, $this->filters[$key], $value];
			}
		}
		return $whereFilters;
	}

	
	public function get(Request $request) {
		$queryParams = $request->all();
		$whereFilters = $this->filtersParse($queryParams);
	
		$this->pageSize = (int) $request->pageSize;
		$tableData = [];
		
		$questionReportModels = QuestionReport::with('user', 'topic')->with(['question' => function($query) use($queryParams) {
			if(isset($queryParams['answer'])) {
				$filterValue = '%'. $queryParams['answer'] . '%';
				$query->where([['right_answer', 'like', $filterValue], ['total_answer', 'like', $filterValue]]);
			}

			if(isset($queryParams['question'])) {
				$filterValue = '%'. $queryParams['question'] . '%';
				$query->where('question', 'like', $filterValue);
			}

		}])->where($whereFilters)->orderBy('created_at', 'desc')->paginate($this->pageSize);


		foreach($questionReportModels as $model) {
			if(! $model->topic || ! $model->user || ! $model->question) {
				continue;
			}
			$tableData[] = [
				'id'				=> $model->id,
				'topic_id'			=> $model->topic_id,
				'topic_title'		=> $model->topic->title,
				'question_id'		=> $model->question_id,
				'question'			=> $model->question->question,
				'right_answer'		=> $model->question->right_answer,
				'total_answer'		=> $model->question->total_answer,
				'user_id'			=> $model->user->user_id,
				'user_nick'			=> $model->user->nick,
				'status'			=> $model->status,
				'reason'			=> $model->reason,
				'created_at'		=> $model->created_at->format('Y-m-d H:i:s'),
			];
		}
		$total = $questionReportModels->total();

		$data['tableData'] = $tableData;
		$data['total'] = $total;
		$data['pageSize'] = $this->pageSize;
 	
		return returnData($data);
	}

	public function update(Request $request) {
		$ids = $request->ids;
		$updateStatus = (int)$request->status;
		$data = [];
		if(empty($ids)) return returnData($data, 1, 'no data selected');

		$affectedRows = QuestionReport::whereIn('id', $ids)->update(['status' => $updateStatus]);
		if($affectedRows === count($ids)) {
			$ret = 0;
			$msg = 'all update success';
		} elseif($affectedRows === 0) {
			$ret = 1;
			$msg = 'all update failed';
		}else {
			$failedModels = QuestionReport::whereIn('id', $ids)->where(['status', '!=', $status])->get();
			$failedIds = [];
			foreach($failedModels as $model) {
				$failedIds[] = $model->id;
			}

			$data['failedIds'] = $failedIds;
			$data['successedIds'] = array_diff($ids, $failedIds);
			$ret = 2;
			$msg = 'not all successed';
		}
		$data['updateStatus'] = $updateStatus;
		return returnData($data, $ret, $msg);
	}

	public function create(Request $request) {
		$questionReportIndexId = (int)$request->id;
		$data = [];
		$questionReportForm = new CreateQuestionReportFormRequest();

		if($questionReportIndexId) {
			// 更新操作
			$questionReportModel = QuestionReport::find($questionReportIndexId);
			if($questionReportModel) {
				$questionModel = Library::getQuestionByQuestionId($questionReportModel->question_id);
				if(! empty($questionModel)) {
					$questionReportForm->id 			= $questionReportModel->id;
					$questionReportForm->question_id 	= $questionReportModel->question_id;
					$questionReportForm->topic_id 		= $questionReportModel->topic_id;
					$questionReportForm->question 		= $questionModel->question;
					$questionReportForm->right_answer 	= $questionModel->right_answer;
					$questionReportForm->total_answer	= $questionModel->total_answer;
					$questionReportForm->reason 		= $questionReportModel->reason;
					$questionReportForm->status 		= (string) $questionReportModel->status;
				}
			}
		}

		$data['formModel'] = $questionReportForm;
		return returnData($data);
	}

	public function store(Request $request) {
		$formModel = $request->formModel;
		$questionReportForm = new CreateQuestionReportFormRequest();
		$questionReportForm->setAttributes($questionReportForm, $formModel);
		
		if($questionReportForm->id && $questionReportModel = QuestionReport::find($questionReportForm->id)) {
			// 更新操作
			$successMsg = 'update successed';
			$failMsg = 'update failed';
		} else {
			return returnData([], 1, '服务端数据异常');
		}

		$questionReportModel->reason = $questionReportForm->reason;
		$questionReportModel->status = $questionReportForm->status;

		$questionModel = Library::getQuestionByQuestionId($questionReportForm->question_id);
		if(empty($questionModel)) {
			return returnData([], 1, '服务端数据异常');
		}
		$questionModel->question 		= $questionReportForm->question;
		$questionModel->right_answer 	= $questionReportForm->right_answer;
		$questionModel->total_answer	= $questionReportForm->total_answer;

       	if($questionReportModel->save() && $questionModel->save()) {
       		return returnData([], 0, $successMsg);
       	} else {
       		return returnData([], 1, $failMsg);
       	}
    }
}