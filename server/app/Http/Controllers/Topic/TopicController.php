<?php

namespace App\Http\Controllers\Topic;

use App\Http\Controllers\Controller;
use App\Models\Users;
use Illuminate\Http\Request;
use App\Models\Topic;
use App\Models\Library;
use App\Http\Requests\CreateTopicFormRequest;
use App\Models\TopicCategory;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\McLibs\Classes\IdFactory;
use Illuminate\Support\Facades\File;

class TopicController extends Controller {

	const ACTIVE_CANCLE = 1;
	const ACTIVE_SET = 0;
	const ACTIVE = 1;
	const INACTIVE = 3;
	
	protected $pageSize = 30;
	protected $filters = ['alias_id' => '=', 'status' => null, 'title' => 'like'];
	protected $webTopicIconPath = '\upload\topic\\';
	protected $subdir = 'topic-icon/';
	protected $tmpAdmin = ['user_id' => 1, 'username'	=> "admin"];

	protected function filtersParse($params) {
		$whereFilters = [];
		if(isset($params['status'])) {
			if($params['status'] == static::ACYIVE_SET) {
				$this->filters['status'] = '!=';
			} elseif($params['status'] == static::ACTIVE_CANCLE) {
				$this->filters['status'] = '=';
			}
			$params['status'] = static::INACTIVE;
		}


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
		$whereFilters = $this->filtersParse($request->all());
		$this->pageSize = (int) $request->pageSize;
		$topicModels = Topic::where($whereFilters)->orderBy('sort', 'desc')->orderBy('created_at', 'desc')->paginate($this->pageSize);
		$tableData = [];
		foreach($topicModels as $model) {
			$questionNum = Library::where('topic_id', '=', $model->topic_id)->count();
			$tableData[] = [
				'id'				=> $model->id,
				'topic_id'			=> $model->topic_id,
				'alias_id'			=> $model->alias_id,
				'title'				=> $model->title,
				'sort'				=> $model->sort,
				'user_id'			=> $model->user_id,
				'user_nick'			=> $model->user_nick,
				'cate_parent_id' 	=> $model->cate_parent_id,
				'cate_parent_name'	=> $model->cate_parent_name,
				'cate_id'			=> $model->cate_id,
				'cate_name' 		=> $model->cate_name,
				'topic_img'			=> "/storage/" . $this->subdir .basename($model->icon),
				'status'			=> $model->status,
				'updated_at'		=> $model->updated_at ? $model->updated_at->format('Y-m-d H:i:s') : 
															$model->created_at->format('Y-m-d H:i:s'),
				'libraryStatus'		=> $questionNum > 0 ? 0 : 1
			];
		}

		$data['tableData'] = $tableData;
		$data['total'] = $topicModels->total();
		$data['pageSize'] = $this->pageSize;
 
		return returnData($data);
	}

	public function update(Request $request) {
		$ids = $request->ids;
		$data = [];
		if(empty($ids) || !isset($request->status)) return returnData($data, 1, 'Invalid parameters!');
		$status = $request->status;
		$updateStatus = $status == static::INACTIVE ? static::ACTIVE : static::INACTIVE;
		$affectedRows = Topic::whereIn('id', $ids)->update(['status' => $updateStatus]);
		if($affectedRows === count($ids)) {
			$ret = 0;
			$msg = 'all update success';
		} elseif($affectedRows === 0) {
			$ret = 1;
			$msg = 'all update failde';
		}else {
			$failedModels = Topic::whereIn('id', $ids)->where(['status', '!=', $status])->get();
			$failedIds = [];
			foreach($failedModels as $model) {
				$failedIds[] = $model->id;
			}

			$data['failedIds'] = $failedIds;
			$data['successedIds'] = array_diff($ids, $failedIds);

			$ret = 2;
			$msg = 'not all success';
		}
		$data['updateStatus'] = $updateStatus;	
		return returnData($data, $ret, $msg);
	}


	protected function getNodeTree(&$list,&$tree,$pid=0) {
	    foreach($list as $key=>$value){
	        if($pid == $value['parent_id']){
	            $tree[$value['cate_id']] = ['value' => $value['id'], 'label' => $value['cate_name']];
	            unset($list[$key]);
	            $this->getNodeTree($list,$tree[$value['cate_id']]['children'],$value['cate_id']);
	        }
	    }
	}


	public function store(Request $request) {
		$topicForm = $request->formModel;
		$topicFormModel = new CreateTopicFormRequest();
		$topicFormModel->setAttributes($topicFormModel, $topicForm);

		if($topicFormModel->id) {
			$topicModel = Topic::find($topicFormModel->id);
			$successMsg = 'update successed';
			$failMsg = 'update failed';
		} else {
			$topicModel = new Topic();
			$topicModel->topic_id = IdFactory::nextId();
			$successMsg = 'create successed';
			$failMsg = 'create failed';
		}


		$topicModel->title = $topicFormModel->title;
		$topicModel->alias_id = (int)$topicFormModel->alias_id;
		$topicModel->sort = (int)$topicFormModel->sort;
		$topicModel->description = $topicFormModel->description;
		$topicModel->created_at = Carbon::createFromTimestamp(strtotime($topicFormModel->created_at));
		$topicModel->icon = $topicFormModel->icon;
		$topicModel->status = $topicFormModel->status == 1 ? 1 : 3;

        $user =$this->tmpAdmin;
        if($user) {
        	$topicModel->user_id = $user['user_id'];
	    	$topicModel->user_nick = $user['username'];
        } else {
        	$topicModel->user_id = '';
	    	$topicModel->user_nick = '';
        }
	   
	    $topicCategories = TopicCategory::find($topicFormModel->categoryOptions);

	    //应该验证category 长度


        $topicModel->cate_parent_id = $topicCategories[0]->cate_id;
        $topicModel->cate_parent_name = $topicCategories[0]->cate_name;

        $topicModel->cate_id = $topicCategories[1]->cate_id;
        $topicModel->cate_name = $topicCategories[1]->cate_name;



       	if($topicModel->save()) {
       		return returnData([], 0, $successMsg);
       	} else {
       		return returnData([], 1, $failMsg);
       	}
    }

	public function create(Request $request) {
		$topicIndexId = (int)$request->id;
		$options = $data = [];
		$categories = TopicCategory::all()->toArray();
		$this->getNodeTree($categories,$options);
		$options = array_values($options);
		$topicForm = new CreateTopicFormRequest();

		if($topicIndexId) {
			$topicModel = Topic::find($topicIndexId);
			if($topicModel) {
				$topicForm->title = $topicModel->title;
				$topicForm->alias_id = (string)$topicModel->alias_id;
				$topicForm->sort = (string)$topicModel->sort;
				$topicForm->description = $topicModel->description;
				$topicForm->created_at = $topicModel->created_at->format('Y-m-d');
				$topicForm->status = $topicModel->status == 1 ? '1' : '3';
				$topicForm->icon = $topicModel->icon;
				$topicCategories = TopicCategory::whereIn('cate_id', [$topicModel->cate_parent_id, $topicModel->cate_id])->get();
				if(count($topicCategories) == 2) {
					$topicForm->categoryOptions = [$topicCategories[0]->id, $topicCategories[1]->id];
				} else {
					$topicForm->categoryOptions = [];
				}
				$topicForm->username = 'admin';
				// $topicForm->fileList = [$topicModel->icon];
				$topicForm->id = $topicModel->id;
			}
		}

		$data['formModel'] = $topicForm;
		array_walk($options, function(&$val) {
			if(isset($val['children'])) {
				$val['children'] = array_values($val['children']);
			}
		});
		$data['options'] = $options;
		return returnData($data);
	}

	public function upload(Request $request) {
		if($request->hasFile('file')) {
			$file = $request->file;
			$subdir = $this->subdir;
			$extension = $file->getClientOriginalExtension();
			$newFileName = sprintf('topic-icon-%s.%s', time(), $extension);
			Storage::disk('public')->put($subdir . $newFileName, File::get($file));
			$data['url'] = $this->webTopicIconPath . '/' . $subdir . $newFileName;
			return returnData($data);
		} else {
			return returnData([], 1, 'empty upload');
		}
	}
}