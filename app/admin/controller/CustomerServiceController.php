<?php


namespace app\admin\controller;


use app\admin\model\CustomerService;
use cmf\controller\AdminBaseController;
use think\Db;
use think\Request;
use think\Validate;


class CustomerServiceController extends AdminBaseController
{
    public function index(Request $request)
    {
        $list = CustomerService::order('id desc')->paginate(20);

        $list->each(function($v,$k){
            $v['avatar']=get_upload_path($v['avatar']);
            return $v;           
        });

        // $list->appends();
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
                'desc'  => 'require',
                'status'  => 'require',
                'url'  => 'require',

            ];

            $msg = [
                'name.require' => '客服名称必须',
                'desc.require' => '介绍必须',
                'status.require' => '状态必须',
                'url.require' => '跳转地址必须',
            ];
            $data = input();
            $validate = new Validate($rule, $msg);
            $result   = $validate->check($data);
            if(!$result) $this->error($validate->getError());

            $res = CustomerService::create($data);
            if(!$res) $this->error("添加失败");
            $this->success("ok");
        }

        return $this->fetch();
    }

    public function edit(Request $request)
    {
        if($request->isPost()){
            $rule = [
                'name'  => 'require',
                'desc'  => 'require',
                'status'  => 'require',
                'url'  => 'require',

            ];

            $msg = [
                'name.require' => '客服名称必须',
                'desc.require' => '介绍必须',
                'status.require' => '状态必须',
                'url.require' => '跳转地址必须',
            ];
            $data = input();
            $validate = new Validate($rule, $msg);
            $result   = $validate->check($data);
            if(!$result) $this->error($validate->getError());

            $res = CustomerService::update($data);
            if(!$res) $this->error("error");
            $this->success("ok");
        }
        $id = input('id');
        $info = CustomerService::get($id);
        $this->assign('info', $info);
        return $this->fetch();
    }

    public function del()
    {
        $ids = input();
        $res = CustomerService::where('id', 'in', $ids)->delete();
        if(!$res) $this->error("error");
        $this->success("ok");
    }
}