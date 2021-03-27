<?php


namespace app\admin\controller;


use app\admin\model\Paytype;
use app\user\model\UserBank;
use cmf\controller\AdminBaseController;
use think\Db;
use think\Request;
use think\Validate;

class PaytypeController extends AdminBaseController
{
    public function index(Request $request)
    {
        $where = [];

        $list = Paytype::where($where)->order('id desc')->paginate(20);
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
                'title'  => 'require',
                'src'  => 'require',
                'notice'  => 'require',
                'status'  => 'require',

            ];

            $msg = [
                'title.require' => '标题必须',
                'src.require' => '图标必须',
                'notice.require' => '公告必须',
                'status.require' => '状态必须',
            ];
            $data = input();
            $validate = new Validate($rule, $msg);
            $result   = $validate->check($data);
            if(!$result) $this->error($validate->getError());

            $res = Paytype::create($data);
            if(!$res) $this->error("添加失败");
            $this->success("ok");
        }

        return $this->fetch();
    }

    public function edit(Request $request)
    {
        if($request->isPost()){
            $rule = [
                'title'  => 'require',
                'src'  => 'require',
                'notice'  => 'require',
                'status'  => 'require',

            ];

            $msg = [
                'title.require' => '标题必须',
                'src.require' => '图标必须',
                'notice.require' => '公告必须',
                'status.require' => '状态必须',
            ];
            $data = input();
            $validate = new Validate($rule, $msg);
            $result   = $validate->check($data);
            if(!$result) $this->error($validate->getError());

            $res = Paytype::update($data);
            if(!$res) $this->error("error");
            $this->success("ok");
        }
        $id = input('id');
        $info = Paytype::get($id);
        $this->assign('info', $info);
        return $this->fetch();
    }

    public function del()
    {
        $ids = input();
        $res = Paytype::where('id', 'in', $ids)->delete();
        if(!$res) $this->error("error");
        $this->success("ok");
    }
}