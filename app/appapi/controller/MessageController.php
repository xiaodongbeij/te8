<?php

namespace app\appapi\controller;

use cmf\controller\HomeBaseController;
use think\Db;
use think\Queue;

class MessageController extends HomebaseController{
    
    public function send()
    {
        $message = input();
        $res = $this->verifyMessage($message);
        if($res === true)
        {
            $isPushed = Queue::push('app\appapi\job\Msg' , $message , 'msgJobQueue' );	
            return json(['code' => 0, 'msg' => '发送成功']);
        }
        return $res;
    }
    
    
    public function getIsRead()
    {
        $userid = input('userid/d');
        $res = Db::name('message')->where('to_userid', $userid)->where('status', 2)->count();
        return json(['code' => 0, 'msg' => '获取成功', 'isread' => $res ? 1 : 0]);
    }
    
    
    public function getUserlist()
    {
        $userid = input('userid/d');
        $p = input('p/d');
        $uid = input('uid/d');
        $where = [];
        if($userid)
        {
            if($uid) $where[] = ['id', '=', $uid];
  
            $list = Db::name('user')->where('parent_id', $userid)->where($where)->field('id,user_nicename')->page($p,20)->select();
            return json(['code' => 0, 'data' => $list, 'msg' => '成功']);
        }
        return json(['code' => 1, 'msg' => '参数错误']);
    }
    
    public function getMessage()
    {
        $userid = input('userid/d');
        $p = input('p/d');
        if($userid)
        {
            $list = Db::name('message')->where('userid|to_userid','=', $userid)->withAttr('username', function($value, $data) {
	            return Db::name('user')->where('id', $data['userid'])->value('user_nicename');
            })->order('id', 'desc')->page($p,20)->select();
            return json(['code' => 0, 'data' => $list, 'msg' => '成功']);
        }
        return json(['code' => 1, 'msg' => '参数错误']);
    }
    
    
    
    public function verifyMessage($message)
    {
        
        $validate = new \think\Validate;
        $validate->rule([
                'userid|用户ID' => 'require|integer',
                'to_userid|对方ID' => 'require',
                'content|消息内容' => 'require|length:2,240'
            ]);
        if(!$validate->check($message)){
            return json(['code' => 1, 'msg' => $validate->getError()]);
        }
        return true;
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
}