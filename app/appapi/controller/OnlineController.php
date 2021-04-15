<?php
/**
 * 用户反馈
 */
namespace app\appapi\controller;

use cmf\controller\HomeBaseController;
use think\Db;
use cmf\lib\Upload;

class OnlineController extends HomebaseController{
    
    public function index()
    {
        $id = input('id');
        $time = 30;
        if(empty($id)) return json(['code' => 1, 'msg' => '参数错误']);
        $user = Db::name('user')->where('id', $id)->find();
        if(empty($user)) return json(['code' => 1, 'msg' => '用户信息不存在']);
        setcaches('online:' . $id, 1,$time);
        return json(['code' => 0, 'msg' => '成功']);
    }
    
    public function onlineNumber()
    {
        $keys = 'onlines';
        if(!$num = getcaches($keys))
        {
            $redis = $GLOBALS['redisdb'];
            $key = $redis->keys('online:*');
            $num = count($key);
            setcaches($keys,$num,30);
        }
        return json(['code' => 0,'num' => $num]);
        // echo "在线人数: $num \n";
        // foreach ($key as $v){
        //     $res = explode(':',$v);
        //     echo "用户id：$res[1] \n";
        // }
    }
}