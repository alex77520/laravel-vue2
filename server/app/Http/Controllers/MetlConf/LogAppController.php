<?php
namespace App\Http\Controllers\MetlConf;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\LogAppConfig;

/**
 * metl 项目配置
 * @author linkailian
 * @date 2017-03-08
 */
class LogAppController extends Controller
{
    public function index()
    {
        $res = LogAppConfig::all()->toArray();
        if($res) {
            return returnData($res);
        } else {
            return returnError('没有数据');
        }
    }

    public function show($id)
    {
        $res = LogAppConfig::findOrFail($id);
        if($res) {
            return returnData($res);
        } else {
            return returnError('没有数据');
        }
    }

    public function store(Request $request)
    {
        $appConf = new LogAppConfig;
        $appConf->app_id = $request->app_id;
        $appConf->app_name = $request->app_name;
        $appConf->app_key = $request->app_key;
        $appConf->status = 1;
        $res = $appConf->save();
        if($res) {
            return returnData($res, 0, '新增成功');
        } else {
            return returnError('操作失败');
        }
    }

    public function update(Request $request, $id)
    {
        if($request->has('app_name')) {
            $data['app_name'] = $request->app_name;
        }
        if($request->has('app_key')) {
            $data['app_key'] = $request->app_key;
        }
        if($request->has('status')) {
            $data['status'] = $request->status;
        }
        $res = LogAppConfig::where('id', $id)->update($data);
        if($res) {
            return returnMsg('更新成功');
        } else {
            return returnError('操作失败');
        }
    }

    public function destroy($id)
    {
        $res = LogAppConfig::delete($id);
        if($res) {
            return returnMsg('删除成功');
        } else {
            return returnError('删除失败');
        }
    }
}