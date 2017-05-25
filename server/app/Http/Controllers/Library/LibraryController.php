<?php

namespace App\Http\Controllers\Library;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Library;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use App\Models\Topic;
use App\Models\Users;
use app\Models\AdminUsers;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\CreateQuestionFormRequest;
use Carbon\Carbon;

class LibraryController extends Controller {

	const TOPIC_VS = 1;
	const QUESTION_ACTIVE = 1;
	protected $filters = ['topic_id' => '=', 'status' => '=', 'question' => 'like'];
	protected $pageSize = 30;
	protected $topicAliasIdLength = 7;
	protected $tmpAdmin = ['user_id' => 1, 'username'	=> "admin"];
	protected $libraryTitle = ['question_id', 'question', 'right_answer', 'answer1', 'answer2', 'answer3', 'answer4'];

	protected function filtersParse($params) {
		$whereFilters = [];
	
		foreach ($params as $key => $value) {
			if(isset($this->filters[$key]) && $this->filters[$key] !== null && ! empty($value)) {
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
		$this->pageSize = (int) $request->pageSize;

		if($request->title) {
			$topicModel = Topic::where('title', '=', $request->title)->first();
			if(! $topicModel) {
				return returnData([], 1, 'topic not exist');
			}

			unset($queryParams['title']);
			$queryParams['topic_id'] = $topicModel->topic_id;
		}

		if($request->id) {
			$topicModel = Topic::find($request->id);
			if(! $topicModel) {
				return returnData([], 1, 'topic not exist');
			}
			unset($queryParams['id']);
			$queryParams['topic_id'] = $topicModel->topic_id;
		}

		if($request->answer) {
			$queryParams['total_answer'] = $request->answer;
			$queryParams['right_answer'] = $request->answer;
			$this->filters['total_answer'] = 'like';
			$this->filters['right_answer'] = 'like';
		}

		$whereFilters = $this->filtersParse($queryParams);
		$libraryModels = Library::with('users')->where($whereFilters)->orderBy('created_at', 'desc')->paginate($this->pageSize);
		$topicList = Topic::getTopicMap('topic_id', 'title');

		$tableData = [];
		foreach($libraryModels as $model) {
			$tableData[] = [
				'id'				=> $model->id,
				'topic_title'		=> isset($topicList[$model->topic_id]) ? $topicList[$model->topic_id] : '未知',
				'user_nick'			=> isset($model->users->user_name) ? $model->users->user_name : '',
				'question'			=> $model->question,
				'right_answer'		=> $model->right_answer,
				'total_answer'		=> $model->total_answer,
				'right_rate'		=> $model->right_rate,
				'status'			=> $model->status,
				'updated_at'		=> $model->updated_at ? $model->updated_at->format('Y-m-d H:i:s') : 
															$model->created_at->format('Y-m-d H:i:s'),
			];
		}

		$data['tableData'] = $tableData;
		$data['total'] = $libraryModels->total();
		$data['pageSize'] = $this->pageSize;
 
		return returnData($data);
	}

	public function update(Request $request) {
		$ids = $request->ids;
		$status = (int) $request->status;
		$data = [];
		if(empty($ids)) return returnData($data, 1, 'no data selected');

		$data['updateStatus'] = $status;
		$affectedRows = Library::whereIn('id', $ids)->update(['status' => $status]);
		if($affectedRows === count($ids)) {
			$ret = 0;
			$msg = 'all update success';
		} elseif($affectedRows === 0) {
			$ret = 1;
			$msg = 'all update failde';
		}else {
			$failedModels = Library::whereIn('id', $ids)->where(['status', '!=', $status])->get();
			$failedIds = [];
			foreach($failedModels as $model) {
				$failedIds[] = $model->id;
			}

			$data['failedIds'] = $failedIds;
			$data['successedIds'] = array_diff($ids, $failedIds);
			$ret = 2;
			$msg = 'not all successed';
		}

		return returnData($data, $ret, $msg);
	}

	public function import() {
		$dir = storage_path('app/public') . '/question-library/';
		$files = File::allFiles($dir);
		$user =$this->tmpAdmin;
		$topicList = Topic::getTopicMap('alias_id', 'topic_id');
		$success = $fails = 0;
		$rowData = $etlData = [];
		$questionIds = [];
		foreach($files as $file) {
			$path = $file->getRealPath();
			$rawData = Excel::load($path, function($reader) {})->get()->toArray();
			
			//数据读取成功后删除源文件
			File::delete($path);

			if(count($rawData) < 1) {
				continue;
			}

			$firstRaw = $rawData[0];

			//检查title是否存在
			if(! isset($firstRaw[$this->libraryTitle[0]], $firstRaw[$this->libraryTitle[1]], $firstRaw[$this->libraryTitle[2]], 
						$firstRaw[$this->libraryTitle[3]], $firstRaw[$this->libraryTitle[4]], $firstRaw[$this->libraryTitle[5]], 
						$firstRaw[$this->libraryTitle[6]])) 
			{
				//excel头部title不匹配 无法解析
				break;
			}

			$question_id = $firstRaw['question_id'];
			array_walk($rawData, function($eData) use (&$questionIds) {
				$questionIds[] = $eData['question_id'];
			});

			$exists = Library::whereIn('question_id', $questionIds)->get(['question_id'])->toArray();

			$existIds = [];
			foreach ($exists as $value) {
				$existIds[] = $value['question_id'];
			}

			$existTopic = [];
			array_walk($rawData, function($eData) use (&$etlData, &$existIds, &$user, &$topicList, &$existTopic) {
				//去重 且 获取topic_id
				$aliasId = substr($eData['question_id'], 0, $this->topicAliasIdLength);
				if(! in_array($eData['question_id'], $existIds) && isset($topicList[$aliasId])) {
					$existTopic[] = $topicList[$aliasId];
					$total_answer = [$eData['answer1'], $eData['answer2'], $eData['answer3'], $eData['answer4']];
					$etlData[] = [
						'question_id' 	=> $eData['question_id'],
						'question'		=> $eData['question'],
						'right_answer'	=> $eData['right_answer'],
						'total_answer'	=> sprintf('["%s"]', implode('","', $total_answer)),
						'user_id'		=> isset($user['user_id']) ? $user['user_id'] : '',
						'topic_id'		=> $topicList[$aliasId],
						'status'		=> static::QUESTION_ACTIVE,
						'created_at'	=> Carbon::now(),
						'updated_at'	=> Carbon::now(),
					];
				}
			});

			if(Library::insert($etlData)) {
				//题库更新成功后 将话题挑战字段vs 更新为可挑战状态
				Topic::whereIn('topic_id', $existTopic)->update(['vs' => static::TOPIC_VS]);
				++$success;
			} else {
				++$fails;
			}
		}
		return ['success' => $success, 'fails' => $fails];
	}


	public function upload(Request $request) {
		$data = [];
		if($request->hasFile('file')) {
			$file = $request->file;
			$extension = $file->getClientOriginalExtension();
			$newFileName = time() . '.' . $extension;
			$path = Storage::disk('public')->put('question-library/' . $newFileName, File::get($file));
			$data['path'] = $path;
			$data['url'] = '..' . Storage::url($path);

			$data['import'] = $this->import();
			if($data['import']['success'] == 0) {
				$msg = 'import failed';
				$data['import']['importStatus'] = 1;
			} else {
				$msg = 'import successed';
				$data['import']['importStatus'] = 0;
			}
			return returnData($data, 0, $msg);

		} else {
			$msg = 'empty upload';
			return returnData($data, 1, $msg);
		}
	}

	public function create(Request $request) {
		$id = $request->id;
		if(empty($id)) return returnData($data, 1, 'no id specified');

		$model = Library::fetchModelById($id);
		if(! $model) {
			return returnData($data, 1, 'qiestion not exist');
		}

		$questionForm = new CreateQuestionFormRequest();
		$questionForm->id = $model->id;
		$questionForm->question = $model->question;
		$questionForm->right_answer = $model->right_answer;
		$questionForm->total_answer = $model->total_answer;
		$questionForm->updated_at = $model->updated_at ? $model->updated_at->format('Y-m-d H:i:s') : $model->created_at->format('Y-m-d H:i:s');
		$questionForm->status = $model->status == 1 ? 1 : 2;

		$data['formModel'] = $questionForm;
		return returnData($data);
	}

	public function store(Request $request) {
		$libraryForm = $request->formModel;
		$questionFormModel = new CreateQuestionFormRequest();
		$questionFormModel->setAttributes($questionFormModel, $libraryForm);

		// 更新操作
		$libraryModel = Library::find($questionFormModel->id);
		$successMsg = 'update successed';
		$failMsg = 'update failed';
		
		if(! $libraryModel) {
			return returnData([], 1, 'question not exist!');
		}

		$libraryModel->question = $questionFormModel->question;
		$libraryModel->total_answer = $questionFormModel->total_answer;
		$libraryModel->right_answer = $questionFormModel->right_answer;
		$libraryModel->status = $questionFormModel->status;
		$libraryModel->updated_at = Carbon::createFromTimestamp(time());

       	if($libraryModel->save()) {
       		return returnData([], 0, $successMsg);
       	} else {
       		return returnData([], 1, $failMsg);
       	}
	}
}