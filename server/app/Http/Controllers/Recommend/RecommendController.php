<?php

namespace App\Http\Controllers\Recommend;

use App\Models\Discuss;
use App\Models\Topic;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Recommend;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Requests\CreateRecommendFormRequest;

class RecommendController extends Controller {

    protected $pageSize = 30;
    protected $recommendType = [ 1 => 'topic',  2 => 'discuss'];
    protected $filters = ['recommend_id' => '=', 'display' => '=', 'type' => '='];

    protected function retrieveRawData($recommends, &$rawData, &$topicIds, &$discussIds) {
        foreach($recommends as $recommend) {
            if(! isset($this->recommendType[$recommend->type])) {
                continue;
            }

            switch($this->recommendType[$recommend->type]) {
                case 'topic':
                    $topicIds[] = $recommend->recommend_id;

                case 'discuss':
                    $discussIds[] = $recommend->recommend_id;
                    break;
                default: 
                    break;
            }
         
            $rawData[$recommend->id] = [
                'id'            => $recommend->id,
                'recommend_id'  => $recommend->recommend_id,
                'type'          => $recommend->type,
                'recommentType' => $recommend->type == 1 ? '话题' : '讨论',
                'status'        => $recommend->display,
                'sort'          => $recommend->sort,
                'updated_at'    => $recommend->updated_at ? $recommend->updated_at->format("Y-m-d H:i:s") : null,
            ];
        }
    }

    protected function associateEtlData($data, $recommendType) {
        $etlData = [];
        switch($recommendType) {
            case 1:
                $key = 'topic_id';
                break;
            case 2:
                $key = 'discuss_id';
                break;
            default: 
                return false;
        }

        foreach($data as $v) {
            $etlData[$v->$key] = [
                'title' => trim($v->title, '<p></p>'),
                'description' => trim($v->description, '<p></p>')
            ];
        }

        return $etlData;
    }

    protected function etlData(&$rawData, &$etlTopicData, &$etlDiscussData) {
        $etlData = [];
        foreach($rawData as $key => $val) {
            $joinInfo = [];
            switch($this->recommendType[$val['type']]) {
                case 'topic':
                    $joinInfo = isset($etlTopicData[$val['recommend_id']]) ?  $etlTopicData[$val['recommend_id']] : [];
                    break;
                case 'discuss':
                    $joinInfo = isset($etlDiscussData[$val['recommend_id']]) ? $etlDiscussData[$val['recommend_id']] : [];
                    break;
                default:
                    break;
            }
            $etlData[] = array_merge($val, $joinInfo);
        }
        return $etlData;
    }

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
        if(isset($request->status)) {
            $queryParams['display'] = $request->status;
        }
        $whereFilters = $this->filtersParse($queryParams);

        $recommends = Recommend::where($whereFilters)->orderBy('created_at', 'desc')->paginate($this->pageSize);
        $rawData = $topicIds = $discussIds = [];

        $this->retrieveRawData($recommends, $rawData, $topicIds, $discussIds);
        $rawTopicData = Topic::getTopicByIds($topicIds);
        $rawDiscussData = Discuss::getDiscussByIds($discussIds);
        
        $etlTopicData = $this->associateEtlData($rawTopicData, 1);
        $etlDiscussData = $this->associateEtlData($rawDiscussData, 2);
        $etlData = $this->etlData($rawData, $etlTopicData, $etlDiscussData);

        $data['tableData'] = $etlData;
        $data['total'] = $recommends->total();
        $data['pageSize'] = $this->pageSize;
        
        return returnData($data);
    }

    public function update(Request $request) {
        $ids = $request->ids;
        $data = [];
        if(empty($ids) || !isset($request->status)) return returnData($data, 1, 'Invalid parameters!');
        $display = $request->status;
        $affectedRows = Recommend::whereIn('id', $ids)->update(['display' => $display]);
        if($affectedRows === count($ids)) {
            $ret = 0;
            $msg = 'all update success';
        } elseif($affectedRows === 0) {
            $ret = 1;
            $msg = 'all update failde';
        }else {
            $failedModels = Recommend::whereIn('id', $ids)->where(['display', '!=', $display])->get();
            $failedIds = [];
            foreach($failedModels as $model) {
                $failedIds[] = $model->id;
            }

            $data['failedIds'] = $failedIds;
            $data['successedIds'] = array_diff($ids, $failedIds);

            $ret = 2;
            $msg = 'not all success';
        }
        $data['updateStatus'] = $display;  
        return returnData($data, $ret, $msg);
    }

    public function create(Request $request) {
        $recommendIndexId = (int)$request->id;
        $data = [];
        $recommendForm = new CreateRecommendFormRequest();

        if($recommendIndexId) {
            $recommendModel = Recommend::find($recommendIndexId);
            if($topicModel) {
              
            }
        }

        $data['formModel'] = $recommendForm;
        return returnData($data);
    }

    public function store(Request $request) {
        $recommendForm = $request->formModel;
        $recommendFormModel = new CreateRecommendFormRequest();
        $recommendFormModel->setAttributes($recommendFormModel, $recommendForm);
        if($recommendFormModel->type == 1) {
            $model = Topic::checkExistById($recommendFormModel->recommend_id);
        } elseif($recommendFormModel->type == 2) {
            $model = Discuss::checkExistById($recommendFormModel->recommend_id);
        } else {
            return returnData([], 1, 'unknown type');
        }

        if(! $model) {
            return returnData([], 1, 'unknown recommend_id');
        }

        if(Recommend::checkExist($recommendFormModel->recommend_id)) {
            return returnData([], 1, '已存在');
        }

        
        $recommendModel = new Recommend();
        $successMsg = 'create successed';
        $failMsg = 'create failed';
    
        $recommendModel->recommend_id = $recommendFormModel->recommend_id;
        $recommendModel->type = $recommendFormModel->type;
        $recommendModel->sort = (int)$recommendFormModel->sort;  
        $recommendModel->display = (int)$recommendFormModel->status;
        $recommendModel->created_at = Carbon::createFromTimestamp(time());
        $recommendModel->updated_at = Carbon::createFromTimestamp(time());

        if($recommendModel->save()) {
            return returnData([], 0, $successMsg);
        } else {
            return returnData([], 1, $failMsg);
        }
    }


}



