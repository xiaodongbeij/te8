<?php


namespace app\admin\controller;


use cmf\controller\AdminBaseController;
use think\Db;

class WordController extends AdminbaseController
{
    function index(){
        $data = $this->request->param();
        $map=[];

        if (isset($data['content'])){
            $map[] = ['content','like','%'.$data['content'].'%'];
        }
        randMessage();
        $lists = Db::name("word")
            ->where($map)
            ->order("addtime desc")
            ->paginate(20);

        $lists->appends($data);
        $page = $lists->render();

        $this->assign('lists', $lists);

        $this->assign("page", $page);

        return $this->fetch();
    }

    function add(){
//        $this->assign('long', $this->getLong());
        return $this->fetch();
    }

    function addPost(){
        if ($this->request->isPost()) {

            $data      = $this->request->param();

            $word=$data['content'];
            if($word==""){
                $this->error("话术不能为空");
            }

            $data['addtime']=time();

            $id = DB::name('word')->insertGetId($data);
            if(!$id){
                $this->error("添加失败！");
            }
            
            $key = 'message';
            $GLOBALS['redisdb']->sadd($key,$word);
            $action="添加VIP：{$id}";
            setAdminLog($action);

            $this->success("添加成功！");

        }
    }

    function del(){

        $id = $this->request->param('id', 0, 'intval');

        $rs = DB::name('word')->where("id={$id}")->delete();
        if(!$rs){
            $this->error("删除失败！");
        }

        $action="删除话术：{$id}";
        setAdminLog($action);

        $this->success("删除成功！",url("word/index"));

    }

    function edit(){
        $id   = $this->request->param('id', 0, 'intval');

        $data=Db::name('word')
            ->where("id={$id}")
            ->find();
        if(!$data){
            $this->error("信息错误");
        }

        $this->assign('data', $data);

        return $this->fetch();
    }

    function editPost(){
        if ($this->request->isPost()) {

            $data      = $this->request->param();

            $content=$data['content'];
            if($content==""){
                $this->error('请填写内容');
            }

            $rs = DB::name('word')->update($data);
            if($rs===false){
                $this->error("修改失败！");
            }

            $action="修改话术：{$data['id']}";
            setAdminLog($action);

            $this->success("修改成功！");
        }
    }
}