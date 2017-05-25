<?php

namespace App\Http\Controllers\Review;

use App\Http\Controllers\Controller;
use App\Models\Users;
use Illuminate\Http\Request;
use App\Models\Discuss;
use App\Models\Viewpoint;
use App\Models\Notification;
use App\Models\Review;
use Carbon\Carbon;

class ReviewController extends Controller {

	protected $pageSize = 30;
	protected $filters = ['status' => '=', 'review_id' => '=', 'type' => '='];
	protected $typeItems = [1 => 'discuss', 2 => 'viewpoint'];
	protected $displayTypeText = ['discuss' => '讨论', 'viewpoint' => '观点'];
	protected $deteled = 1;
	
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

	 protected function retrieveRawData($models, $typeColumnName, $idColumnName, $rawList, &$rawData, &$discussIds, &$viewpointIds) {
        foreach($models as $model) {
            if(! isset($this->typeItems[$model->$typeColumnName])) {
                continue;
            }

            switch($this->typeItems[$model->$typeColumnName]) {
                case 'discuss':
                    $discussIds[] = $model->$idColumnName;
                    break;
                case 'viewpoint':
                    $viewpointIds[] = $model->$idColumnName;
                    break;
            }
         	
            $list = [];
            foreach($rawList as $key => $column) {
            	if(in_array($key, ['created_at', 'updated_at'])) {
            		$list[$key] = $model->$column->format("Y-m-d H:i:s");
            		continue;
            	}
            	if($key === 'type') {
            		$list['type'] = isset($this->typeItems[$model->$typeColumnName]) ? $model->$typeColumnName : '未知';
            		$list['displayType'] = isset($this->typeItems[$model->$typeColumnName]) ? $this->displayTypeText[$this->typeItems[$model->$typeColumnName]] : '未知';
            		continue;
            	}
            	$list[$key] = $model->$column;
            }

            $rawData[$model->id] = $list;
        }
    }

    protected function associateEtlData($data, $type, $etlList) {
        $etlData = [];
        $idKey = '';
        switch($this->typeItems[$type]) {
            case 'discuss':
                $idKey = 'discuss_id';
                $contentKey = 'description';
                break;
            case 'viewpoint':
                $idKey = 'viewpoint_id';
                $contentKey = 'content';
                break;
            default: 
                return false;
        }

        foreach($data as $model) {
        	$list = [];
        	foreach($etlList as $key => $column) {
        		if(in_array($column, ['content', 'description'])) {
        			$list[$key] = trim($model->$column, '<p></p>') ?? '-';
        			continue;
        		}

        		$list[$key] = $model->$column ?? '-';
        	}
        	$etlData[$model->$idKey] = $list;
        }

        return $etlData;
    }

    protected function etlData($typeColumnName, $idColumnName, &$rawData, &$etlDiscussData, &$etlViewpointData) {
        $etlData = [];
        foreach($rawData as $key => $val) {
            $joinInfo = [];
            switch($this->typeItems[$val[$typeColumnName]]) {
                case 'discuss':
                    $joinInfo = $etlDiscussData[$val[$idColumnName]] ?? [];
                    break;
                case 'viewpoint':
                    $joinInfo = $etlViewpointData[$val[$idColumnName]] ?? [];
                    break;
            }
            $etlData[] = array_merge($val, $joinInfo);
        }
        return $etlData;
    }
	
	public function get(Request $request) {
		$queryParams = $request->all();
		$whereFilters = $this->filtersParse($queryParams);
	
		$this->pageSize = (int) $request->pageSize;
		$tableData = [];

	 	$reviews = Review::where($whereFilters)->orderBy('created_at', 'desc')->paginate($this->pageSize);
	 	$typeColumnName = 'type';
	 	$idColumnName = 'review_id';
	 	$rawList = ['id' => 'id', 'review_id' => 'review_id', 'type' => 'type', 'status' => 'status', 'created_at' => 'created_at'];
        $rawData = $discussIds = $viewpointIds = [];

        $this->retrieveRawData($reviews, $typeColumnName, $idColumnName, $rawList, $rawData, $discussIds, $viewpointIds);
        $rawDiscussData = Discuss::getModelByIds($discussIds);
        $rawViewpointData = Viewpoint::getModelByIds($viewpointIds);

    
    	$discussRawList = ['user_id' => 'user_id', 'user_nick' => 'user_nick', 'title' => 'title', 'view_a' => 'view_a', 'view_b' => 'view_b', 'description' => 'description'];

    	$viewpointRawList = ['user_id' => 'user_id', 'user_nick' => 'user_nick', 'title' => 'title', 'view_a' => 'view_a', 'view_b' => 'view_b', 'description' => 'content'];
        $etlDiscussData = $this->associateEtlData($rawDiscussData, 1, $discussRawList);
        $etlViewpointData = $this->associateEtlData($rawViewpointData, 2, $viewpointRawList);
       
        $etlData = $this->etlData('type', 'review_id', $rawData, $etlDiscussData, $etlViewpointData);

        $data['tableData'] = $etlData;
        $data['total'] = $reviews->total();
        $data['pageSize'] = $this->pageSize;		
 	
		return returnData($data);
	}

	public function update(Request $request) {
		$ids = $request->ids;
		$updateStatus = (int)$request->status;
		$data = [];
		if(empty($ids)) return returnData($data, 1, 'no data selected');

		$successedIds = $this->batchUpdateById('\App\Models\Review', $ids, 'status', $updateStatus);

		$reviews = Review::whereIn('id', $successedIds)->get();
    	$discussIds = $viewpointIds = [];
    	foreach($reviews as $model) {
    		if($model->type == 1) {
    			$discussIds[] = $model->review_id;
    		} else if($model->type == 2) {
    			$viewpointIds[] = $model->review_id;
    		}
    	}


		if($updateStatus == 1 && ! empty($successedIds)) {
	    	if(! (Discuss::undeletedByIds($discussIds) && 
	    		Viewpoint::undeletedByIds($viewpointIds))) {
	    		$successedIds = [];
	    	}

		} elseif($updateStatus == 2 && ! empty($successedIds)) {	
	    	if(!( Discuss::deletedByIds($discussIds) && 
	    		Viewpoint::deletedByIds($viewpointIds) && 
	    		Notification::batchInsert($discussIds, 'discuss') && 
	    		Notification::batchInsert($viewpointIds, 'viewpoint'))) {
					$successedIds = [];
	    	}	    
		}

		if(count($successedIds) === count($ids)) {
			$ret = 0;
			$msg = 'all update success';
		} elseif(count($successedIds) === 0) {
			$ret = 1;
			$msg = 'all update failed';
		}else {
			$msg = 'not all successed';
			$data["successedIds"] = $successedIds;
			$data['failedIds'] = array_diff($ids, $successedIds);
			$ret = 2;
		}
		$data['updateStatus'] = $updateStatus;

		return returnData($data, $ret, $msg);
	}
}