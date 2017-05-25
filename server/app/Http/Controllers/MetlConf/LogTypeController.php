<?php

namespace App\Http\Controllers\MetlConf;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\LogTypeConfig;

/**
 * metl 日志类型-数据库-表 配置
 * @author linkailian
 * @date 2017-03-08
 */
class LogTypeController extends Controller
{
    public function index()
    {
        $res = LogTypeConfig::all()->toArray();
        if($res) {
            return returnData($res);
        } else {
            return returnError('没有数据');
        }
    }

    public function show($id)
    {
        $res = LogTypeConfig::findOrFail($id);
        if($res) {
            return returnData($res);
        } else {
            return returnError('没有数据');
        }
    }

    public function store(Request $request)
    {
        $logTypeConfig = new LogTypeConfig;
        $logTypeConfig->app_id = $request->app_id;
        $logTypeConfig->log_type = $request->log_type;
        $logTypeConfig->version = $request->version;
        $logTypeConfig->database_name = $request->database_name;
        $logTypeConfig->table_name = $request->table_name;
        $res = $logTypeConfig->save();
        if($res) {
            return returnData($res, 0, '新增成功');
        } else {
            return returnError('操作失败');
        }
    }

    public function update(Request $request, $id)
    {
        $data['log_type'] = $request->log_type;
        $data['version'] = $request->version;
        $data['database_name'] = $request->database_name;
        $data['table_name'] = $request->table_name;
        $res = LogTypeConfig::where('id', $id)->update($data);
        if($res) {
            return returnMsg('更新成功');
        } else {
            return returnError('操作失败');
        }
    }

    public function destroy($id)
    {
        $res = LogTypeConfig::delete($id);
        if($res) {
            return returnMsg('删除成功');
        } else {
            return returnError('删除失败');
        }
    }
}