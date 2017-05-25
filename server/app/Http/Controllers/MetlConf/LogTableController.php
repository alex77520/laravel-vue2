<?php

namespace App\Http\Controllers\MetlConf;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\LogTableConfig;

/**
 * metl 表字段 分区配置
 * @author linkailian
 * @date 2017-03-08
 */
class LogTableController extends Controller
{
    public function index()
    {
        $res = LogTableConfig::all()->toArray();
        if($res) {
            return returnData($res);
        } else {
            return returnError('没有数据');
        }
    }

    public function show($id)
    {
        $res = LogTableConfig::findOrFail($id);
        if($res) {
            return returnData($res);
        } else {
            return returnError('没有数据');
        }
    }

    public function store(Request $request)
    {
        $logTableConfig = new LogTableConfig;
        $logTableConfig->app_id = $request->app_id;
        $logTableConfig->log_type_id = $request->log_type_id;
        $logTableConfig->field_config = $request->field_config;
        $logTableConfig->partition = $request->partition;
        $logTableConfig->table_comment = $request->table_comment;
        $res = $logTableConfig->save();
        if($res) {
            return returnData($res, 0, '新增成功');
        } else {
            return returnError('操作失败');
        }
    }

    public function update(Request $request, $id)
    {
        $data['field_config'] = $request->field_config;
        $data['partition'] = $request->partition;
        $data['table_comment'] = $request->table_comment;
        $res = LogTableConfig::where('id', $id)->update($data);
        if($res) {
            return returnMsg('更新成功');
        } else {
            return returnError('操作失败');
        }
    }

    public function destroy($id)
    {
        $res = LogTableConfig::delete($id);
        if($res) {
            return returnMsg('删除成功');
        } else {
            return returnError('删除失败');
        }
    }
}