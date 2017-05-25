<?php

namespace App\Http\Controllers\TopicCategory;

use App\Http\Controllers\Controller;
use App\Models\Users;
use Illuminate\Http\Request;
use App\Models\Topic;
use App\Models\Library;
use App\Http\Requests\CreateTopicCategoryFormRequest;
use App\Models\TopicCategory;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\McLibs\Classes\IdFactory;

class TopicCategoryController extends Controller {

	const ACTIVE_CANCLE = 1;
	const ACYIVE_SET = 0;
	const ACTIVE = 1;
	const INACTIVE = 2;

	protected $pageSize = 10;
	protected $filters = ['parent_id' => '=', 'status' => '=', 'cate_name' => 'like'];
	protected $inactiveStatus = 3;
	protected $activeStaus = 1;
	protected $topCategoryIcon = [
		1 => '\upload\topic\cate\icon_game.png',
		2 => '\upload\topic\cate\icon_recreation.png',
		3 => '\upload\topic\cate\icon_anime.png',
		4 => '\upload\topic\cate\icon_wikipedia.png',
		5 => '\upload\topic\cate\icon_recreation_life.png',
	];

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
		$categoryName = $request->categoryName;
		$categoryLevel = $request->categoryLevel;
		
		if($categoryLevel == 1) {
			$queryParams['parent_id'] = 0;
		} elseif($categoryLevel == 2) {
			$queryParams['parent_id'] = 0;
			$this->filters['parent_id'] = '!=';
		}
		$queryParams['cate_name'] = $categoryName;
		
		$whereFilters = $this->filtersParse($queryParams);
	
		$this->pageSize = (int) $request->pageSize;
		$tableData = [];
		
		$topicCategoryModels = TopicCategory::with('parentCategory')->where($whereFilters)->orderBy('cate_id', 'asc')->orderBy('sort', 'desc')->paginate($this->pageSize);
		foreach($topicCategoryModels as $model) {
			$tableData[] = [
				'id'				=> $model->id,
				'cate_id'			=> $model->cate_id,
				'cate_name'			=> $model->cate_name,
				'parent_cate_id'	=> $model->parent_id,
				'parent_cate_name'	=> isset($model->parentCategory) ? $model->parentCategory->cate_name : '-',
				'status'			=> $model->status,
				'sort'				=> $model->sort,
				'display'			=> $model->display,
				'updated_at'		=> $model->updated_at ? $model->updated_at->format('Y-m-d H:i:s') : 
															$model->created_at->format('Y-m-d H:i:s'),
			];
		}
		$total = $topicCategoryModels->total();

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

		// $status = $updateStatus === static::ACYIVE_SET ? 1 : 3;
		$affectedRows = TopicCategory::whereIn('id', $ids)->update(['status' => $updateStatus]);
		if($affectedRows === count($ids)) {
			$ret = 0;
			$msg = 'all update success';
		} elseif($affectedRows === 0) {
			$ret = 1;
			$msg = 'all update failed';
		}else {
			$failedModels = Topic::whereIn('id', $ids)->where(['status', '!=', $status])->get();
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
		$topicCategoryForm = $request->topicCategoryForm;
		$topicCategiryFormModel = new CreateTopicCategoryFormRequest();
		$topicCategiryFormModel->setAttributes($topicCategiryFormModel, $topicCategoryForm);
		
		if($topicCategiryFormModel->id) {
			// 更新操作
			$topicCategoryModel = TopicCategory::find($topicCategiryFormModel->id);
			$successMsg = 'update successed';
			$failMsg = 'update failed';
		} else {
			$topicCategoryModel = new TopicCategory();
			$successMsg = 'create successed';
			$failMsg = 'create failed';
		}

		$topicCategoryModel->cate_id = (int)$topicCategiryFormModel->cate_id;
		$topicCategoryModel->sort = (int)$topicCategiryFormModel->sort;
		$topicCategoryModel->cate_name = $topicCategiryFormModel->cate_name;
		$topicCategoryModel->created_at = Carbon::createFromTimestamp(strtotime($topicCategiryFormModel->created_at));
		$topicCategoryModel->status = $topicCategiryFormModel->status == 1 ? 1 : 2;
		$topicCategoryModel->parent_id = $topicCategiryFormModel->category_level == 1 ? 0 : $topicCategiryFormModel->parent_id;
		$topicCategoryModel->icon = isset($this->topCategoryIcon[$topicCategiryFormModel->cate_id]) ? $this->topCategoryIcon[$topicCategiryFormModel->cate_id] : '';

       	if($topicCategoryModel->save()) {
       		return returnData([], 0, $successMsg);
       	} else {
       		return returnData([], 1, $failMsg);
       	}
    }

	public function create(Request $request) {
		$topicCategoryIndexId = (int)$request->id;
		$topCategoryOptions = $options = $data = [];
		$options = TopicCategory::where('parent_id', '=', 0)->get()->toArray();
		$topicCategoryForm = new CreateTopicCategoryFormRequest();

		if($topicCategoryIndexId) {
			// 更新操作
			$topicCategoryModel = TopicCategory::find($topicCategoryIndexId);
			if($topicCategoryModel) {
				$topicCategoryForm->id = $topicCategoryModel->id;
				$topicCategoryForm->cate_id = (string)$topicCategoryModel->cate_id;
				$topicCategoryForm->cate_name = $topicCategoryModel->cate_name;
				$topicCategoryForm->created_at = $topicCategoryModel->created_at->format('Y-m-d');
				$topicCategoryForm->status = (string)$topicCategoryModel->status;
				$topicCategoryForm->sort = (string)$topicCategoryModel->sort;
				$topicCategoryForm->category_level = $topicCategoryModel->parent_id == 0 ? 1 : 2;
				if($topicCategoryForm->category_level == 2) {
					$topicCategoryForm->parent_id = $topicCategoryModel->parent_id;
				} 
			}
		}

		$data['topicCategoryForm'] = $topicCategoryForm;
		array_walk($options, function($val) use (&$topCategoryOptions) {
			$topCategoryOptions[] = ['value' => $val['cate_id'], 'label' => $val['cate_name']];
		});
		$data['topCategoryOptions'] = $topCategoryOptions;
		return returnData($data);
	}

	public function edit(Request $request) {
		echo "hello world";
	}

}