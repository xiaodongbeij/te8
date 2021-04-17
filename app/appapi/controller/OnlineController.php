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
        if(!getcache('onlineo:' . $id))
        {
            Db::name('user')->where('id', $id)->update(['last_login_time' => time()]);
        }
        setcaches('onlineo:' . $id, $id,$time);
        return json(['code' => 0, 'msg' => '成功']);
    }
    
    public function onlineNumber()
    {
        $redis = $GLOBALS['redisdb'];
        $key = $redis->keys('onlineo:*');
        $num = count($key);
        return json(['code' => 0,'num' => $num]);

    }
}