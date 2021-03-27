<?php

/**
 * 视频分类
 */
namespace app\admin\controller;

use cmf\controller\AdminBaseController;
use app\admin\model\Videoclass;
use think\Db;

class VideoclassController extends AdminbaseController {
    function index(){
        $lists = Videoclass::order("list_order asc")
			->paginate(20);
        
        
        $page = $lists->render();

    	$this->assign('lists', $lists);

    	$this->assign("page", $page);
    	
    	return $this->fetch();
        
    }
		
    function del(){
        $id = $this->request->param('id', 0, 'intval');
        
        $rs = DB::name('video_class')->where("id={$id}")->delete();
        if($rs===false){
            $this->error("删除失败！");
        }
        
        $action="删除视频分类：{$id}";
        setAdminLog($action);
        $this->resetCache();
                
        $this->success("删除成功！");				
    }		
    //排序
    public function listOrder() { 
		
        $model = DB::name('video_class');
        parent::listOrders($model);
        
        $action="更新视频分类排序";
        setAdminLog($action);
        $this->resetCache();
            
        $this->success("排序更新成功！");
    }	
    

    function add(){        
        return $this->fetch();
    }
    function addPost(){
		if ($this->request->isPost()) {
            
            $data      = $this->request->param();
            
			$name=$data['name'];

			if($name==""){
				$this->error("请填写名称");
			}
            
            $isexit=DB::name("video_class")->where(['name'=>$name])->find();	
			if($isexit){
				$this->error('该名称已存在');
			}
			
            
			$id = DB::name('video_class')->insertGetId($data);
            if(!$id){
                $this->error("添加失败！");
            }
            
            $action="添加视频分类：{$id}";
            setAdminLog($action);
            $this->resetCache();
            
            $this->success("添加成功！");
            
		}
	}	
    
    function edit(){
        
        $id   = $this->request->param('id', 0, 'intval');
        
        $data=Videoclass::where("id={$id}")
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
            
			$name=$data['name'];
			$id=$data['id'];

			if($name==""){
				$this->error("请填写名称");
			}
            $where=[];
            $where[]=['id','<>',$id];
            $where[]=['name','=',$name];
            $isexit=Db::name("video_class")->where($where)->find();	
			if($isexit){
				$this->error('该名称已存在');
			}
			
            
			$rs = DB::name('video_class')->update($data);
            if($rs===false){
                $this->error("修改失败！");
            }
            
            $action="修改视频分类：{$id}";
            setAdminLog($action);
            $this->resetCache();
                
            $this->success("修改成功！");
		}
	}
    
    function resetCache(){
        $key='getVideoClass';
        $rules= Db::name("video_class")
            ->order('list_order asc,id desc')
            ->select();
        if($rules){
            setcaches($key,$rules);
        }else{
			delcache($key);
		}
        
        return 1;
    }
}
