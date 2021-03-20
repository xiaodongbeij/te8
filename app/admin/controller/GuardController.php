<?php

/**
 * 守护
 */
namespace app\admin\controller;

use cmf\controller\AdminBaseController;
use think\Db;

class GuardController extends AdminbaseController {
    
    protected function getTypes($k=''){
        $type=array(
            '1'=>'普通守护',
            '2'=>'尊贵守护',
        );
        if($k==''){
            return $type;
        }
        return isset($type[$k])?$type[$k]:'';
    }
    
    protected function getLengthtype($k=''){
        $length=array(
            '0'=>'天',
            '1'=>'月',
            '2'=>'年',
        );
        if($k==''){
            return $length;
        }
        return isset($length[$k])?$length[$k]:'';
    }
    
    protected function getLengthtime($k=''){
        $type=array(
            '0'=>60*60*24,
            '1'=>60*60*24*30,
            '2'=>60*60*24*365,
        );
        if($k==''){
            return $type;
        }
        return isset($type[$k])?$type[$k]:0;
    }
    
    function index(){
        $lists = Db::name("guard")
			->order("list_order asc")
			->paginate(20);
        
        $page = $lists->render();

    	$this->assign('lists', $lists);

    	$this->assign("page", $page);
        
    	$this->assign("type_a", $this->getTypes());
    	$this->assign("length_type_a", $this->getLengthtype());
    	
    	return $this->fetch();
    }		
		
	function del(){
        
        $id = $this->request->param('id', 0, 'intval');
        
        $rs = DB::name('guard')->where("id={$id}")->delete();
        if(!$rs){
            $this->error("删除失败！");
        }
        
        $action="删除守护：{$id}";
        setAdminLog($action);
                    
        $this->resetcache();
        $this->success("删除成功！",url("guard/index"));		
	}

    //排序
    public function listOrder() { 
		
        $model = DB::name('guard');
        parent::listOrders($model);
        
        $action="更新守护排序";
        setAdminLog($action);
        
        $this->resetcache();
        $this->success("排序更新成功！");
        
    }
	
    function add(){
        $this->assign('type_a', $this->getTypes());
    	$this->assign('length_type_a', $this->getLengthtype());
		return $this->fetch();
    }

	function addPost(){
		if ($this->request->isPost()) {
            
            $data      = $this->request->param();
            
			$name=$data['name'];

			if($name==""){
				$this->error("请输入名称");
			}
			$coin=intval($data['coin']);
			if($coin=="" || $coin<1){
				$this->error("请输入有效价格");
			}
            
            $length=intval($data['length']);
			if($length=="" || $length<1){
				$this->error("请输入有效时长");
			}
            
            $length_type=$data['length_type'];
            
            $data['addtime']=time();
            $data['uptime']=time();
            $data['length_time']=$length * $this->getLengthtime($length_type);
            
			$id = DB::name('guard')->insertGetId($data);
            if(!$id){
                $this->error("添加失败！");
            }
            
            $action="添加守护：{$id}";
            setAdminLog($action);
            
            $this->resetcache();
            $this->success("添加成功！");
            
		}
	}
		
    function edit(){
        $id   = $this->request->param('id', 0, 'intval');
        
        $data=Db::name('guard')
            ->where("id={$id}")
            ->find();
        if(!$data){
            $this->error("信息错误");
        }
        
        $this->assign('data', $data);
        $this->assign('type_a', $this->getTypes());
        $this->assign('length_type_a', $this->getLengthtype());
        
        return $this->fetch();
    }

	function editPost(){
		if ($this->request->isPost()) {
            
            $data      = $this->request->param();
            
			$name=$data['name'];

			if($name==""){
				$this->error("请输入名称");
			}
			$coin=intval($data['coin']);
			if($coin=="" || $coin<1){
				$this->error("请输入有效价格");
			}
            
            $length=intval($data['length']);
			if($length=="" || $length<1){
				$this->error("请输入有效时长");
			}
            $length_type=$data['length_type'];
            $data['uptime']=time();
            $data['length_time']=$length * $this->getLengthtime($length_type);
            
			$rs = DB::name('guard')->update($data);
            if($rs===false){
                $this->error("修改失败！");
            }
            
            $action="修改守护：{$data['id']}";
            setAdminLog($action);
            
            $this->resetcache();
            $this->success("修改成功！");
            
		}
	}

    function resetCache(){
        $key='guard_list';
        $list= DB::name('guard')
            ->field('id,name,type,coin')
            ->order('list_order asc')
            ->select();
        if($list){
            setcaches($key,$list);
        }else{
			delcache($key);
		}
        
        return 1;
    }
}
