<?php

/**
 * 登录奖励
 */
namespace app\admin\controller;

use cmf\controller\AdminBaseController;
use think\Db;

class LoginbonusController extends AdminbaseController {
    function index(){
        
        $lists = Db::name("loginbonus")
			->order("day asc")
			->paginate(20);
        
        
        $page = $lists->render();

    	$this->assign('lists', $lists);

    	$this->assign("page", $page);
   
    	return $this->fetch();
        
    }
    
    function del(){
        
        $id = $this->request->param('id', 0, 'intval');
        
        $rs = DB::name('loginbonus')->where("id={$id}")->delete();
        if(!$rs){
            $this->error("删除失败！");
        }
        
        $action="删除登录奖励：{$id}";
        setAdminLog($action);
                    
        $this->resetcache();
        $this->success("删除成功！");
            
	}
    		
	function add(){
		return $this->fetch();
	}
    function addPost(){
		if ($this->request->isPost()) {
            
            $data      = $this->request->param();
            
			
            $data['addtime']=time();
            
			$id = DB::name('loginbonus')->insertGetId($data);
            if(!$id){
                $this->error("添加失败！");
            }
            
            $action="添加登录奖励：{$id}";
            setAdminLog($action);
            
            $this->resetcache();
            $this->success("添加成功！");
            
		}
	}
    
    function edit(){
        
        $id   = $this->request->param('id', 0, 'intval');
        
        $data=Db::name('loginbonus')
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
            
			$data['uptime']=time();
            
			$rs = DB::name('loginbonus')->update($data);
            if($rs===false){
                $this->error("修改失败！");
            }
            
            $action="编辑登录奖励：{$data['id']}";
            setAdminLog($action);
            
            $this->resetcache();
            $this->success("修改成功！");
		}
	}

	
        
    function resetcache(){
        $key='loginbonus';
        $list=DB::name('loginbonus')
                ->field("day,coin")
                ->order('day asc')
                ->select();
        if($list){
            setcaches($key,$list);
        }else{
			delcache($key);
		}
        return 1;
    }
        
    function index2(){
        $data = $this->request->param();
        
        $map[]=['type','=','1'];
        $map[]=['action','=','3'];
        
        $uid=isset($data['uid']) ? $data['uid']: '';
        if($uid!=''){
            $map[]=['uid','=',$uid];
        }
            
        $lists = Db::name("user_coinrecord")
            ->where($map)
			->order("id desc")
			->paginate(20);
        
        $lists->each(function($v,$k){
			$v['userinfo']=getUserInfo($v['uid']);
            $name='第'.$v['giftid'].'天';
            $v['name']=$name;
            return $v;           
        });
        
        $lists->appends($data);
        
        $page = $lists->render();

    	$this->assign('lists', $lists);

    	$this->assign("page", $page);
    	
    	return $this->fetch();
            
    }
		
}
