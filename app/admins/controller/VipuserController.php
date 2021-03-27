<?php

/**
 * VIP用户管理
 */
namespace app\admin\controller;

use cmf\controller\AdminBaseController;
use think\Db;

class VipuserController extends AdminbaseController {

		
    function index(){
        $data = $this->request->param();
        $map=[];
        
        $uid=isset($data['uid']) ? $data['uid']: '';
        if($uid!=''){
            $lianguid=getLianguser($uid);
            if($lianguid){
                $map[]=['uid',['=',$uid],['in',$lianguid],'or'];
            }else{
                $map[]=['uid','=',$uid];
            }
        }
        
    	$lists = Db::name("vip_user")
            ->where($map)
			->order("endtime desc")
			->paginate(20);
        
        $lists->each(function($v,$k){
			$v['userinfo']=getUserInfo($v['uid']);
            return $v;           
        });
        
        $lists->appends($data);
        $page = $lists->render();

    	$this->assign('lists', $lists);

    	$this->assign("page", $page);
    	
    	return $this->fetch();
    }		
	
    function del(){
        
        $id = $this->request->param('id', 0, 'intval');
        
        $rs = DB::name('vip_user')->where("id={$id}")->delete();
        if(!$rs){
            $this->error("删除失败！");
        }
        
        $action="删除用户VIP：{$id}";
        setAdminLog($action);
        
        $this->success("删除成功！",url("vipuser/index"));
            
	}	
			
    function add(){	
    	return $this->fetch();
    }
    
	function addPost(){
		if ($this->request->isPost()) {
            
            $data      = $this->request->param();
            
			$uid=$data['uid'];
            
            if($uid==''){
				$this->error('用户ID不能为空');
			}
            
            $isexist=DB::name("user")->field("id")->where("id={$uid}")->find();
			if(!$isexist){
				$this->error('该用户不存在');
			}
			
			$isexist2=DB::name('vip_user')->field("id")->where(['uid'=>$uid])->find();
			if($isexist2){
				$this->error('该用户已购买过会员');
			}
			
            $data['addtime']=time();
            $data['endtime']=strtotime($data['endtime']);
            
			$id = DB::name('vip_user')->insertGetId($data);
            if(!$id){
                $this->error("添加失败！");
            }
            
            $action="添加用户VIP：{$uid}";
            setAdminLog($action);
			
			$key="vip_".$uid;
            delcache($key);
            
            $this->success("添加成功！");
            
		}
	}
    
    function edit(){
        $id   = $this->request->param('id', 0, 'intval');
        
        $data=Db::name('vip_user')
            ->where("id={$id}")
            ->find();
        if(!$data){
            $this->error("信息错误");
        }
        $data['userinfo']=getUserInfo($data['uid']);
        
        $this->assign('data', $data);
        return $this->fetch();
    }			
	function editPost(){
        if ($this->request->isPost()) {
            
            $data      = $this->request->param();
			
            $data['endtime']=strtotime($data['endtime']);
            
			$rs = DB::name('vip_user')->update($data);
            if($rs===false){
                $this->error("添加失败！");
            }
            
            $action="修改用户VIP：{$data['uid']}";
            setAdminLog($action);
			
			$key="vip_".$data['uid'];
            delcache($key);
            
            $this->success("添加成功！");
            
		}
    }
}
