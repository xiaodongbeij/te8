<?php

/**
 * 靓号管理
 */
namespace app\admin\controller;

use cmf\controller\AdminBaseController;
use think\Db;

class LiangController extends AdminbaseController {
    
    protected function getStatus($k=''){
        $status=[
            '0'=>'出售中',
            '1'=>'已售',
            '2'=>'停售',
        ];
        
        if($k==''){
            return $status;
        }
        
        return $status[$k];
    }

    function index(){
        
        $data = $this->request->param();
        $map=[];
        
        $status=isset($data['status']) ? $data['status']: '';
        if($status!=''){
            $map[]=['status','=',$status];
        }
        
        $length=isset($data['length']) ? $data['length']: '';
        if($length!=''){
            $map[]=['length','=',$length];
        }
        
        $uid=isset($data['uid']) ? $data['uid']: '';
        if($uid!=''){
            $map[]=['uid','=',$uid];
        }
        
        $keyword=isset($data['keyword']) ? $data['keyword']: '';
        if($keyword!=''){
            $map[]=['name','like','%'.$keyword.'%'];
        }
			

    	$lists = Db::name("liang")
                ->where($map)
                ->order("id DESC")
                ->paginate(20);
                
        $lists->each(function($v,$k){
			if($v['uid']>0){
				$v['userinfo']=getUserInfo($v['uid']);
			}
            return $v;           
        });
                
        $lists->appends($data);
        $page = $lists->render();

    	$this->assign('lists', $lists);

    	$this->assign("page", $page);
        
        $this->assign('status', $this->getStatus());
        
        $length=Db::name("liang")
			->field("length")
			->order("length asc")
			->group("length")
			->select();

    	$this->assign('length', $length);
        
    	
    	return $this->fetch();

    }

	function del(){
        
        $id = $this->request->param('id', 0, 'intval');
        
        $info = DB::name('liang')->where("id={$id}")->find();
        $rs = DB::name('liang')->where("id={$id}")->delete();
        if(!$rs){
            $this->error("删除失败！");
        }
        
        if($info['uid']>0){
            $key='liang_'.$info['uid'];
            delcache($key);
        }
                    
        $action="删除靓号：{$id}";
        setAdminLog($action);
        
        $this->success("删除成功！",url("liang/index"));
            
	}


	function setStatus(){
        
        $id = $this->request->param('id', 0, 'intval');
        $status = $this->request->param('status', 0, 'intval');
        

        $result=DB::name('liang')->where(["id"=>$id])->setField("status",$status);				
        if($result!==false){
            if($status==1){
                $action="修改靓号状态：{$id} - 已售";
            }else if($status==2){
                $action="修改靓号状态：{$id} - 停售";
            }else{
                $action="修改靓号状态：{$id} - 出售中";
            }

            setAdminLog($action);
            $this->success('操作成功');
        }
            
        $this->error('操作失败');		
	}
    
    //排序
    public function listOrder() { 
		
        $model = DB::name('liang');
        parent::listOrders($model);
        
        $action="修改靓号排序";
        setAdminLog($action);
        
        $this->success("排序更新成功！");
        
    }


	function add(){
        return $this->fetch();			
	}
    
	function addPost(){
		if ($this->request->isPost()) {
            
            $configpub=getConfigPub();
            
            $data      = $this->request->param();
            
			$name=$data['name'];

			if($name==""){
				$this->error("靓号不能为空");
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
            
            $isexist=DB::name('liang')->where(["name"=>$name])->find();
			
			if($isexist){
				$this->error('该靓号已存在');
			}

            $data['length']=mb_strlen($name);
            $data['addtime']=time();
            
			$id = DB::name('liang')->insertGetId($data);
            if(!$id){
                $this->error("添加失败！");
            }
            
            $action="添加靓号：{$id}";
            setAdminLog($action);
            
            $this->success("添加成功！");
            
		}			
	}    
	
	function edit(){
        $id   = $this->request->param('id', 0, 'intval');
        
        $data=Db::name('liang')
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
            
            $configpub=getConfigPub();
            
            $data      = $this->request->param();
            
			$id=$data['id'];
			$name=$data['name'];

			if($name==""){
				$this->error("靓号不能为空");
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
            
            
            $isexist=DB::name('liang')->where([['id','<>',$id],['name','=',$name]])->find();
			
			if($isexist){
				$this->error('该靓号已存在');
			}
            
			$rs = DB::name('liang')->update($data);
            if($rs===false){
                $this->error("修改失败！");
            }
            
            $action="编辑靓号：{$data['id']}";
            setAdminLog($action);
            
            $this->success("修改成功！");
		}
	}
		
}
