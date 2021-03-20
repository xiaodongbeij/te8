<?php

/**
 * VIP管理
 */
namespace app\admin\controller;

use cmf\controller\AdminBaseController;
use think\Db;

class VipController extends AdminbaseController {

    protected function getLong($k=''){
        $long=array(
            '1'=>'1个月',
            '3'=>'3个月',
            '6'=>'6个月',
            '12'=>'12个月',
        );
        if($k==''){
            return $long;
        }
        return $long[$k];
    }

    function index(){
        $lists = Db::name("vip")
			->order("list_order asc")
			->paginate(20);
        
        $page = $lists->render();

    	$this->assign('lists', $lists);

    	$this->assign("page", $page);
        
        $this->assign('long', $this->getLong());
    	
    	return $this->fetch();
        
    }

	function del(){
        
        $id = $this->request->param('id', 0, 'intval');
        
        $rs = DB::name('vip')->where("id={$id}")->delete();
        if(!$rs){
            $this->error("删除失败！");
        }
        
        $action="删除VIP：{$id}";
        setAdminLog($action);
        
        $this->success("删除成功！",url("vip/index"));
            
	}
	
    //排序
    public function listOrder() { 
		
        $model = DB::name('vip');
        parent::listOrders($model);
        
        $action="更新VIP排序";
        setAdminLog($action);
        
        $this->success("排序更新成功！");
        
    }    

	function add(){
        $this->assign('long', $this->getLong());
        return $this->fetch();				
	}
    
    function addPost(){
		if ($this->request->isPost()) {
            
            $configpub=getConfigPub();
            $data      = $this->request->param();
            
			$length=$data['length'];
            
            $isexist=DB::name('vip')->where(['length'=>$length])->find();
			
			if($isexist){
				$this->error('已存在相同类型 时长的设置');
			}
            
            $coin=$data['coin'];
			if($coin==""){
				$this->error("请填写所需".$configpub['name_coin']);
			}

			if(!is_numeric($coin)){
				$this->error("请确认所需".$configpub['name_coin']);
			}
            
            $score=$data['score'];
			if($score==""){
				$this->error("请填写所需".$configpub['name_score']);
			}

			if(!is_numeric($score)){
				$this->error("请确认所需".$configpub['name_score']);
			}
            
            $data['addtime']=time();
            
			$id = DB::name('vip')->insertGetId($data);
            if(!$id){
                $this->error("添加失败！");
            }
            
            $action="添加VIP：{$id}";
            setAdminLog($action);
            
            $this->success("添加成功！");
            
		}			
	}
    
	
	function edit(){        
        $id   = $this->request->param('id', 0, 'intval');
        
        $data=Db::name('vip')
            ->where("id={$id}")
            ->find();
        if(!$data){
            $this->error("信息错误");
        }
        
        $this->assign('data', $data);
        
        $this->assign('long', $this->getLong());
        
        return $this->fetch();
	}
    
    function editPost(){
		if ($this->request->isPost()) {
            
            $configpub=getConfigPub();
            
            $data      = $this->request->param();
            
            /* $length=$data['length'];
            
            $isexist=DB::name('vip')->where(['length'=>$length])->find();
			
			if($isexist){
				$this->error('已存在相同类型 时长的设置');
			} */
            
            $coin=$data['coin'];
			if($coin==""){
				$this->error("请填写所需".$configpub['name_coin']);
			}

			if(!is_numeric($coin)){
				$this->error("请确认所需".$configpub['name_coin']);
			}
            
            $score=$data['score'];
			if($score==""){
				$this->error("请填写所需".$configpub['name_score']);
			}

			if(!is_numeric($score)){
				$this->error("请确认所需".$configpub['name_score']);
			}
            
			$rs = DB::name('vip')->update($data);
            if($rs===false){
                $this->error("修改失败！");
            }
            
            $action="修改VIP：{$data['id']}";
            setAdminLog($action);
            
            $this->success("修改成功！");
		}
	}
	
}
