<?php


namespace app\user\controller;

use app\user\model\UserChannel;
use cmf\controller\AdminBaseController;
use think\Db;
use think\Request;
use think\Validate;

class UserChannelController extends AdminBaseController
{
    public function index(Request $request)
    {
        $list = UserChannel::where('del_status', 0)->order('id desc')->paginate(20);

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
                'name'  => 'require',
                'min_money'  => 'require',
                'channel_id'  => 'require',
            ];

            $msg = [
                'name.require' => '等级名称必须',
                'min_money.require' => '累计充值最小金额必须',
                'channel_id.require' => '等级通道必须',
            ];
            $data = input();

            $validate = new Validate($rule, $msg);
            $result   = $validate->check($data);
            if(!$result) $this->error($validate->getError());
            $data['channel_id'] = implode('|', $data['channel_id']);

            $res = UserChannel::create($data);
            if(!$res) $this->error("添加失败");
            $this->success("ok");
        }

        $channels = Db::name('channel')->where('status', 1)->where('del_status', 0)->field('id,channel_name')->select();

        $this->assign('channels', $channels);
        return $this->fetch();
    }

    public function edit(Request $request)
    {
        if($request->isPost()){
            $rule = [
                'name'  => 'require',
                'min_money'  => 'require',
                'channel_id'  => 'require',
            ];

            $msg = [
                'name.require' => '等级名称必须',
                'min_money.require' => '累计充值最小金额必须',
                'channel_id.require' => '等级通道必须',
            ];
            $data = input();
            $validate = new Validate($rule, $msg);
            $result   = $validate->check($data);
            if(!$result) $this->error($validate->getError());
            $data['channel_id'] = implode('|', $data['channel_id']);
            $res = UserChannel::update($data);
            if(!$res) $this->error("error");
            $this->success("ok");
        }
        $id = input('id');
        $channels = Db::name('channel')->where('status', 1)->where('del_status', 0)->field('id,channel_name')->all()->toArray();
        $info = UserChannel::get($id);
        $info['channel_id'] = explode('|', $info['channel_id']);
        $this->assign('channels', $channels);
        $this->assign('info', $info);
        return $this->fetch();
    }

    public function del()
    {
        $ids = input();
        $res = UserChannel::where('id', 'in', $ids)->update(['del_status' => 1]);
        if(!$res) $this->error("error");
        $this->success("ok");
    }
}