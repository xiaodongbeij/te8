<?php

/**
 * 印象标签
 */
namespace app\admin\controller;

use cmf\controller\AdminBaseController;
use think\Db;

class ImpressionController extends AdminbaseController {
    function index(){
        
        $lists = Db::name("label")
			->order("list_order asc,id desc")
			->paginate(20);
        
        
        $page = $lists->render();

    	$this->assign('lists', $lists);

    	$this->assign("page", $page);
    	
    	return $this->fetch();

    }
    
    function del(){
        
        $id = $this->request->param('id', 0, 'intval');
        
        $rs = DB::name('label')->where("id={$id}")->delete();
        if(!$rs){
            $this->error("删除失败！");
        }
        
        $action="删除印象标签：{$id}";
        setAdminLog($action);
                    
        $this->resetcache();
        $this->success("删除成功！");
            
	}
    
    //排序
    public function listOrder() { 
		
        $model = DB::name('label');
        parent::listOrders($model);
        
        $action="更新印象标签排序";
        setAdminLog($action);
        
        $this->resetcache();
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
			$colour=$data['colour'];
			if($colour==""){
				$this->error("请选择首色");
			}

			$colour2=$data['colour2'];
			if($colour2==""){
				$this->error("请选择尾色");
			}
            
			$id = DB::name('label')->insertGetId($data);
            if(!$id){
                $this->error("添加失败！");
            }
            
            $action="添加印象标签：{$id}";
            setAdminLog($action);
            
            $this->resetcache();
            $this->success("添加成功！");
            
		}
	}
    
	function edit(){
        
        $id   = $this->request->param('id', 0, 'intval');
        
        $data=Db::name('label')
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
            
			$name=$data['name'];

			if($name==""){
				$this->error("请填写名称");
			}
			$colour=$data['colour'];
			if($colour==""){
				$this->error("请选择首色");
			}

			$colour2=$data['colour2'];
			if($colour2==""){
				$this->error("请选择尾色");
			}
            
			$rs = DB::name('label')->update($data);
            if($rs===false){
                $this->error("修改失败！");
            }
            
            $action="修改印象标签：{$data['id']}";
            setAdminLog($action);
            
            $this->resetcache();
            $this->success("修改成功！");
		}
	}
    
    
    function resetCache(){
        $key='getImpressionLabel';
        $rules= DB::name("label")
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
