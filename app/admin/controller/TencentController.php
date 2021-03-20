<?php


namespace app\admin\controller;


use app\admin\model\Tencent;
use app\user\model\UserBank;
use cmf\controller\AdminBaseController;
use think\Db;
use think\Request;
use think\Validate;

class TencentController extends AdminBaseController
{
    public function index()
    {
        $where = [];

        $list = Tencent::where($where)->order('id desc')->paginate(20);

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
                'push_key'  => 'require',
                'play_key'  => 'require',
                'play_time'  => 'require',
                'push'  => 'require',
                'pull'  => 'require',
                'play_key_switch'  => 'require',
                'status'  => 'require',

            ];

            $msg = [
                'push_key.require' => '腾讯云推流防盗链key必须',
                'play_key.require' => '腾讯云直播播流鉴权key必须',
                'play_time.require' => '腾讯云直播播流鉴权时间(秒)必须',
                'push.require' => '腾讯云直播推流域名必须',
                'pull.require' => '腾讯云直播播流域名必须',
                'play_key_switch.require' => '鉴权必须',
                'status.require' => '状态必须',
            ];
            $data = input();
            $validate = new Validate($rule, $msg);
            $result   = $validate->check($data);
            if(!$result) $this->error($validate->getError());

            $res = Tencent::create($data);
            if(!$res) $this->error("添加失败");
            $this->success("ok");
        }
        return $this->fetch();
    }

    public function edit(Request $request)
    {
        if($request->isPost()){
            $rule = [
                'push_key'  => 'require',
                'play_key'  => 'require',
                'play_time'  => 'require',
                'push'  => 'require',
                'pull'  => 'require',
                'play_key_switch'  => 'require',
                'status'  => 'require',

            ];

            $msg = [
                'push_key.require' => '腾讯云推流防盗链key必须',
                'play_key.require' => '腾讯云直播播流鉴权key必须',
                'play_time.require' => '腾讯云直播播流鉴权时间(秒)必须',
                'push.require' => '腾讯云直播推流域名必须',
                'pull.require' => '腾讯云直播播流域名必须',
                'play_key_switch.require' => '鉴权必须',
                'status.require' => '状态必须',
            ];
            $data = input();
            $validate = new Validate($rule, $msg);
            $result   = $validate->check($data);
            if(!$result) $this->error($validate->getError());


            $res = Tencent::update($data);
            if(!$res) $this->error("error");
            $this->success("ok");
        }
        $id = input('id');
        $info = Tencent::get($id);
        $this->assign('info', $info);
        return $this->fetch();
    }

    public function del()
    {
        $ids = input();
        $res = Tencent::where('id', 'in', $ids)->delete();
        if(!$res) $this->error("error");
        $this->success("ok");
    }

    public function status()
    {
        $id = input('id');
        $info = Tencent::where('id',$id)->find();
        if(!$info) $this->error("该数据不存在");
        if($info['status'] != 1) $this->error("该数据无法切换");

        Db::startTrans();
        $info->status = 2;
        $res1 = $info->save();

        $res2 = Tencent::where('id', '<>', $id)->where('status', 2)->update(['status' => 3]);

        if ($res1 && $res2) {
            Db::commit();
            $this->success('ok');
        }else
        {
            Db::rollback();
            $this->error('error');
        }
    }
}