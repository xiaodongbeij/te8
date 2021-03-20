<?php


namespace app\admin\controller;

use app\admin\model\Active;
use cmf\controller\AdminBaseController;
use think\Db;
use think\Request;
use think\Validate;

class ActiveController extends AdminBaseController
{
    public function index(Request $request)
    {

        $list = Active::order('id desc')->paginate(20);

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
                'cate'  => 'require',
                'img'  => 'require',
                'url'  => 'require',
                'start'  => 'require',
                'end'  => 'require',
            ];

            $msg = [
                'title.require' => '标题必须',
                'cate.require' => '分类必须',
                'img.require' => '封面必须',
                'url.require' => '跳转地址必须',
                'start.require' => '开始时间必须',
                'end.require' => '结束时间必须',
            ];
            $data = input();
            $validate = new Validate($rule, $msg);
            $result   = $validate->check($data);
            if(!$result) $this->error($validate->getError());

            $data['start'] = strtotime($data['start']);
            $data['end'] = strtotime($data['end']);

            $res = Active::create($data);
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
                'cate'  => 'require',
                'img'  => 'require',
                'url'  => 'require',
                'start'  => 'require',
                'end'  => 'require',
            ];

            $msg = [
                'title.require' => '标题必须',
                'cate.require' => '分类必须',
                'img.require' => '封面必须',
                'url.require' => '跳转地址必须',
                'start.require' => '开始时间必须',
                'end.require' => '结束时间必须',
            ];
            $data = input();
            $validate = new Validate($rule, $msg);
            $result   = $validate->check($data);
            if(!$result) $this->error($validate->getError());

            $data['start'] = strtotime($data['start']);
            $data['end'] = strtotime($data['end']);

            $res = Active::update($data);
            if(!$res) $this->error("error");
            $this->success("ok");
        }
        $id = input('id');
        $info = Active::get($id);
        $this->assign('info', $info);
        return $this->fetch();
    }

    public function del()
    {
        $ids = input();
        $res = Active::where('id', 'in', $ids)->delete();
        if(!$res) $this->error("error");
        $this->success("ok");
    }
}