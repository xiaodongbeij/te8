<?php

/**
 * 幸运礼物中奖设置
 */
namespace app\admin\controller;

use cmf\controller\AdminBaseController;
use think\Db;

class LuckrateController extends AdminbaseController {
    
    protected function getNums($k=''){
        $status=['1','10','66','88','100','520','1314'];
        if($k===''){
            return $status;
        }
        
        return isset($status[$k]) ? $status[$k]: '';
    }
    
    function index(){
        
        $data = $this->request->param();
        $map=[];
		
        $giftid=isset($data['giftid']) ? $data['giftid']: '';
        if($giftid!=''){
            $map[]=['giftid','=',$giftid];
        }
        
        
        $lists = Db::name("gift_luck_rate")
            ->where($map)
			->order("id desc")
			->paginate(20);
        
        $lists->appends($data);
        
        $page = $lists->render();

    	$this->assign('lists', $lists);

    	$this->assign("page", $page);
        
    	$this->assign("giftid", $giftid);
        
        $giftinfo=Db::name('gift')
            ->field('giftname')
            ->where(["id"=>$giftid])
            ->find();
        $this->assign('giftinfo', $giftinfo);   
    	
    	return $this->fetch();
        
    }
   
    function del(){
        
        $id = $this->request->param('id', 0, 'intval');
		
		//礼物id
		$giftid=Db::name('gift_luck_rate')
            ->field('giftid')
            ->where(["id"=>$id])
            ->find();
        
        $rs = DB::name('gift_luck_rate')->where("id={$id}")->delete();
        if(!$rs){
            $this->error("删除失败！");
        }
		
		$action="礼物管理-礼物ID：".$giftid['giftid']." 中奖设置删除ID: ".$id;
		setAdminLog($action);
                    
        $this->resetcache();
        $this->success("删除成功！");
            
	}
    
    function add(){
        $giftid = $this->request->param('giftid', 0, 'intval');
        $this->assign('giftid', $giftid);
        $this->assign('numslist', $this->getNums());
        
		return $this->fetch();
	}
	function addPost(){
		if ($this->request->isPost()) {
            
            $data      = $this->request->param();
            
			$giftid=$data['giftid'];
			$nums=$data['nums'];
			$times=$data['times'];

			if($times<=0){
				$this->error("中奖倍数不能小于等于0");
			}
            $where=[];
            $where[]=['giftid','=',$giftid];
            $where[]=['nums','=',$nums];
            $where[]=['times','=',$times];

            $check = DB::name('gift_luck_rate')->where($where)->find();
            if($check){
                $this->error('相同数量、倍数的配置已存在');
            }
            
            
			$id = DB::name('gift_luck_rate')->insertGetId($data);
            if(!$id){
                $this->error("添加失败！");
            }
            
			
			$action="礼物管理-礼物ID：".$giftid." 中奖设置添加ID: ".$id;
			setAdminLog($action);
            
            $this->resetcache();
            $this->success("添加成功！");
            
		}
	}
    
    function edit(){
        
        $id   = $this->request->param('id', 0, 'intval');
        
        $data=Db::name('gift_luck_rate')
            ->where("id={$id}")
            ->find();
        if(!$data){
            $this->error("信息错误");
        }
        
        $this->assign('numslist', $this->getNums());
        $this->assign('data', $data);
        return $this->fetch();
	}
	
	function editPost(){
		if ($this->request->isPost()) {
            
            $data      = $this->request->param();
            
			$giftid=$data['giftid'];
			$nums=$data['nums'];
			$times=$data['times'];
			$id=$data['id'];

			if($times<=0){
				$this->error("中奖倍数不能小于等于0");
			}
            $where=[];
            $where[]=['giftid','=',$giftid];
            $where[]=['nums','=',$nums];
            $where[]=['times','=',$times];
            $where[]=['id','<>',$id];
            
            $check = DB::name('gift_luck_rate')->where($where)->find();
            if($check){
                $this->error('相同数量、倍数的配置已存在');
            }
            
			$rs = DB::name('gift_luck_rate')->update($data);
            if($rs===false){
                $this->error("修改失败！");
            }
            
			$action="礼物管理-礼物ID：".$giftid." 中奖设置修改ID: ".$id;
			setAdminLog($action);
			
            $this->resetcache();
            $this->success("修改成功！");
		}
	}
    
     function resetcache(){
		$key='gift_luck_rate';

        $level= Db::name("gift_luck_rate")->order("id desc")->select();
        if($level){
            setcaches($key,$level);
        }else{
			delcache($key);
		}
       
        return 1;
    }       

}
