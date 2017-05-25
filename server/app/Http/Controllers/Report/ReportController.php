<?php

namespace App\Http\Controllers\Report;

use App\Models\Discuss;
use App\Models\Viewpoint;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\Request;

class ReportController extends Controller {

    protected $pageSize = 30;
    protected $reportType = [ /*1 => 'topic',*/  2 => 'discuss', 3 => 'viewpoint'];
    protected $types = [ 1 => '广告', 2 => '色情', 3 => '虚假', 4 => '政治', 5 => '人身攻击', 6 => '其他'];


    protected function retrieveRawData($reports, &$rawData, &$discussIds, &$viewpointIds) {
        foreach($reports as $report) {
            if(! isset($this->reportType[$report->target_type]) || ! $report->users) {
                continue;
            }

            switch($this->reportType[$report->target_type]) {
                case 'discuss':
                    $discussIds[] = $report->target_id;
                    break;
                case 'viewpoint':
                    $viewpointIds[] = $report->target_id;
                    break;
            }
         
            $rawData[$report->id] = [
                'id' => $report->id,
                'user_id' => $report->users->user_id ?? '',
                'user_nick' => $report->users->nick ?? '',
                'content' => $report->content,
                'status' => $report->status,
                'type' => isset($this->types[$report->type]) ? $this->types[$report->type] : '未知',
                'created_at' => $report->created_at->format("Y-m-d H:i:s"),
                'target_id' => $report->target_id,
                'target_type' => $report->target_type,
            ];
        }
    }

    protected function associateEtlData($data, $reportType) {
        $etlData = [];

        switch($reportType) {
            case 2:
                $key = 'discuss_id';
                $contentKey = 'description';
                break;
            case 3:
                $key = 'viewpoint_id';
                $contentKey = 'content';
                break;
            default: 
                return false;
        }

        foreach($data as $v) {
            $etlData[$v->$key] = [
                'report_content' => trim($v->$contentKey, '<p></p>'),
                'report_user_id' => $v->user_id,
                'report_user_nick' => $v->user_nick
            ];
        }

        return $etlData;
    }

    protected function etlData(&$rawData, &$etlDiscussData, &$etlViewpointData) {
        $etlData = [];
        foreach($rawData as $key => $val) {
            $joinInfo = [];
            switch($this->reportType[$val['target_type']]) {
                case 'discuss':
                    $joinInfo = isset($etlDiscussData[$val['target_id']]) ?  $etlDiscussData[$val['target_id']] :  [];
                    break;
                case 'viewpoint':
                    $joinInfo = isset($etlViewpointData[$val['target_id']]) ?  $etlViewpointData[$val['target_id']] :  [];
                    break;
            }
            $etlData[] = array_merge($val, $joinInfo);
        }
        return $etlData;
    }

    protected function updateStatus($target_type, $target_id, $deleted) {
        $model = null;

        if($target_type == 1) {

        } elseif($target_type == 2) {
            $model = Discuss::where('discuss_id', '=', $target_id)->first();
        } else {
            $model = Viewpoint::where('viewpoint_id', '=', $target_id)->first();
        }

        if(! $model) {
            return false;
        }
        
        $model->deleted = $deleted;
        return $model->save();
    }

    public function get(Request $request) {
        $reports = Report::with('users')->orderBy('created_at', 'desc')->paginate($this->pageSize);
        $rawData = $discussIds = $viewpointIds = [];

        $this->retrieveRawData($reports, $rawData, $discussIds, $viewpointIds);
        $rawDiscussData = Discuss::getDiscussByIds($discussIds);
        $rawViewpointData = Viewpoint::reportAssociate($viewpointIds);
    
        $etlDiscussData = $this->associateEtlData($rawDiscussData, 2);
        $etlViewpointData = $this->associateEtlData($rawViewpointData, 3);
        $etlData = $this->etlData($rawData, $etlDiscussData, $etlViewpointData);

        $data['tableData'] = $etlData;
        $data['total'] = $reports->total();
        $data['pageSize'] = $this->pageSize;
        
        return returnData($data);
    }

    public function post(Request $request) {
        $id = $request->id;
        $status = (int)$request->status;
        $report = Report::find($id);
        $report->status = $status;
        $deleted = $status == 2 ? 1 : 0;
        if($this->updateStatus($report->target_type, $report->target_id, $deleted) && $report->save()) {
            return returnData([], 0, 'update success');
        }else {
            return  returnData([], 1, 'update failed');
        }
     
    }

}



