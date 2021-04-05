<?php
namespace app\game\controller;



use app\game\model\GameCate;
use cmf\controller\AdminBaseController;
use think\Request;
use think\Validate;

class GameCateController extends AdminBaseController
{
    public function index()
    {
        $where = [];
        $where[] = ['del_status', '=', 0];
        $data = input();

        $list = GameCate::where($where)->order('id desc')->paginate(20);

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
                'name'  => 'require',
                'icon'  => 'require',
                'platform'  => 'require',
            ];

            $msg = [
                'name.require' => '分类名称必须',
                'icon.require' => '图标必须',
                'platform.require' => '平台编码必须',
            ];
            $data = input();
            $validate = new Validate($rule, $msg);
            $result   = $validate->check($data);
            if(!$result) $this->error($validate->getError());

            $res = GameCate::create($data);
            if(!$res) $this->error("添加失败");
       
            delcache('getGameCate');
            $this->success("添加成功");
        }
        return $this->fetch();
    }

    public function edit(Request $request)
    {
        if($request->isPost()){
            $rule = [
                'name'  => 'require',
                'icon'  => 'require',
                'platform'  => 'require',
            ];

            $msg = [
                'name.require' => '分类名称必须',
                'icon.require' => '图标必须',
                'platform.require' => '平台编码必须',
            ];
            $data = input();
            $validate = new Validate($rule, $msg);
            $result   = $validate->check($data);
            if(!$result) $this->error($validate->getError());

            $res = GameCate::update($data);
            if(!$res) $this->error("编辑失败");
        
            $this->success("编辑成功");
        }
        delcache('getGameCate');
        $id = input('id');
        $info = GameCate::get($id);
        $this->assign('info', $info);
        return $this->fetch();
    }

    public function del()
    {
        $ids = input();
        $res = GameCate::where('id', 'in', $ids)->update(['del_status' => 1]);
        if(!$res) $this->error("删除失败");
        delcache('getGameCate');
        $this->success("删除成功");
    }
}