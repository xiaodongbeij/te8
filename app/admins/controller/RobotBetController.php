<?php


namespace app\admin\controller;


use app\admin\model\RobotBet as RB;
use cmf\controller\AdminBaseController;
use think\Db;
use think\Request;
use think\Validate;

class RobotBetController extends AdminBaseController
{
    
    public function index(Request $request)
    {
        $where = [];
        $data = input();
        $uid=isset($data['uid']) ? $data['uid']: '';
        if($uid!=''){
            $where[]=['uid','=',$uid];
        }

        $list = RB::where($where)->order('id desc')->paginate(20);

        $list->appends($data);
        $page = $list->render();
        $this->assign('list', $list);
        $this->assign('page', $page);

        // 渲染模板输出
        return $this->fetch();
    }

    public function add(Request $request)
    {
        if($request->isPost()){
            $rule = [
                'uid'  => 'require',
                'coin'  => 'require',
                'push_frequency'  => 'require',

            ];

            $msg = [
                'uid.require' => '房间号必须',
                'coin.require' => '下注金额必须',
                'push_frequency.require' => '推送频率必须',

            ];
            
            $data = input();
            $validate = new Validate($rule, $msg);
            $result   = $validate->check($data);
            if(!$result) $this->error($validate->getError());
            $key = "ticket:" . $data['uid'];
            
            $res = RB::create($data);
            if(!$res) $this->error("添加失败");
            $this->resetcache($key,$data);
            $this->success("ok");
        }

        $users = Db::name('live')->where('islive', 1)->field('uid,title')->all()->toArray();
        $this->assign('users', $users);
        return $this->fetch();
    }

    public function edit(Request $request)
    {
        if($request->isPost()){
            $rule = [
                'uid'  => 'require',
                'coin'  => 'require',
                'push_frequency'  => 'require',
  
            ];

            $msg = [
                'uid.require' => '房间号必须',
                'coin' => '下注金额必须',
                'push_frequency.require' => '推送频率必须',

            ];
            $data = input();
            $validate = new Validate($rule, $msg);
            $result   = $validate->check($data);
            if(!$result) $this->error($validate->getError());
            $uid = Rb::where('id', $data['id'])->value('uid');
            delcache('ticket:'.$uid);
            $res = RB::update($data);
            if(!$res) $this->error("error");
            $this->resetcache('ticket:'.$data['uid'],$data);
            $this->success("ok");
        }
        $id = input('id');
        $info = RB::get($id);
        $users = Db::name('live')->where('islive', 1)->field('uid,title')->all()->toArray();
        $this->assign('info', $info);
        $this->assign('users', $users);
        return $this->fetch();
    }

    public function del()
    {
        $id = input();
        $uid = RB::where('id', $id['id'])->value('uid');
        if(!$uid) $this->error("error");
        $res = RB::where('id', $id['id'])->delete();
        delcache('ticket:'.$uid);
        if(!$res) $this->error("error");
        $this->success("ok");
    }
    
    
    protected function resetcache($key='',$info=[]){
        if($key!='' && $info){
            delcache($key);
            $list = Db::name('live')->where('uid', $info['uid'])->field('show_name,short_name,c_type')->find();
            
            $info['show_name'] = $list['show_name'];
            $info['short_name'] = $list['short_name'];
            $info['type'] = $list['c_type'];
       
            setcaches($key,$info);
        }
    }
}