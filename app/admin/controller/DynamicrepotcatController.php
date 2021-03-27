<?php

/* 动态举报 */
namespace app\admin\controller;

use cmf\controller\AdminBaseController;
use think\Db;

class DynamicrepotcatController extends AdminBaseController
{

    public function index()
    {
        
        $list = Db::name('dynamic_report_classify')
            ->order("list_order asc")
            ->paginate(20);
        
        $page = $list->render();
        $this->assign("page", $page);
            
        $this->assign('list', $list);

        return $this->fetch();
    }


    public function add()
    {
        return $this->fetch();
    }

    public function addPost()
    {
        if ($this->request->isPost()) {
            $data      = $this->request->param();
            
            $name=$data['name'];
            
            if($name == ''){
                $this->error('请填写名称');
            }
            
            $map[]=['name','=',$name];
            $isexist = DB::name('dynamic_report_classify')->where($map)->find();
            if($isexist){
                $this->error('同名已存在');
            }

            $id = DB::name('dynamic_report_classify')->insertGetId($data);
            if(!$id){
                $this->error("添加失败！");
            }
			
			
			$action="添加动态举报类型：{$id}";
			setAdminLog($action);
            $this->resetcache();
            $this->success("添加成功！");
        }
    }

    public function edit()
    {
        $id   = $this->request->param('id', 0, 'intval');
        
        $data=Db::name('dynamic_report_classify')
            ->where("id={$id}")
            ->find();
        if(!$data){
            $this->error("信息错误");
        }
        
        $this->assign('data', $data);
        return $this->fetch();
    }

    public function editPost()
    {
        if ($this->request->isPost()) {
            $data      = $this->request->param();

            $id=$data['id'];
            $name=$data['name'];
            
            if($name == ''){
                $this->error('请填写名称');
            }
            
            $map[]=['name','=',$name];
            $map[]=['id','<>',$id];
            $isexist = DB::name('dynamic_report_classify')->where($map)->find();
            if($isexist){
                $this->error('同名已存在');
            }

            $rs = DB::name('dynamic_report_classify')->update($data);

            if($rs === false){
                $this->error("保存失败！");
            }
			
			$action="修改动态举报类型：{$id}";
			setAdminLog($action);
            $this->resetcache();
            $this->success("保存成功！");
        }
    }
    
    public function listOrder()
    {
        $model = DB::name('dynamic_report_classify');
        parent::listOrders($model);
        $this->resetcache();
		
		
		$action="更新动态举报类型排序";
		setAdminLog($action);
        $this->success("排序更新成功！");
    }

    public function del()
    {
        $id = $this->request->param('id', 0, 'intval');
        
        $rs = DB::name('dynamic_report_classify')->where('id',$id)->delete();
        if(!$rs){
            $this->error("删除失败！");
        }
		
		
		$action="删除动态举报类型：{$id}";
        setAdminLog($action);
        $this->resetcache();
        $this->success("删除成功！");
    }


    protected function resetcache(){
        $key='getDynamicreportClass';

        $list=DB::name('dynamic_report_classify')
                ->order("list_order asc,id desc")
                ->select();
        if($list){
            setcaches($key,$list);
        }else{
			delcache($key);
		}
    }
}