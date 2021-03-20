<?php

/**
 * 奖池中奖设置
 */
namespace app\admin\controller;

use cmf\controller\AdminBaseController;
use think\Db;

class JackpotrateController extends AdminbaseController {
    protected function getNums($k=''){
        $status=['1','10','66','88','100','520','1314'];
        if($k===''){
            return $status;
        }
        
        return isset($status[$k]) ? $status[$k]: '';
    }
    
    protected function getJackpotLevel(){
        $jackpot=Db::name("jackpot_level");
    	$lists = $jackpot
            ->order("levelid asc")
            ->select();
            
        return $lists;
        
    }
    
    function index(){
        
        $data = $this->request->param();
        $map=[];
		
        $giftid=isset($data['giftid']) ? $data['giftid']: '';
        if($giftid!=''){
            $map[]=['giftid','=',$giftid];
        }
        
        
        $lists = Db::name("jackpot_rate")
            ->where($map)
			->order("id desc")
			->paginate(20);
            
        $lists->each(function($v,$k){
			$v['rate_jackpot']=json_decode($v['rate_jackpot'],true);
            return $v;           
        });
        
        $lists->appends($data);
        
        $page = $lists->render();

    	$this->assign('lists', $lists);

    	$this->assign("page", $page);
        
    	$this->assign("giftid", $giftid);
        
        $this->assign('jackpot_level', $this->getJackpotLevel());
        
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
		$giftid=Db::name('jackpot_rate')
            ->field('giftid')
            ->where(["id"=>$id])
            ->find();
		
		
        $rs = DB::name('jackpot_rate')->where("id={$id}")->delete();
        if(!$rs){
            $this->error("删除失败！");
        }
        
		
		$action="礼物管理-礼物ID：".$giftid['giftid']." 奖池设置删除ID: ".$id;
		setAdminLog($action);
		
        $this->resetcache();
        $this->success("删除成功！");
            
	}
    
    
    function add(){
        $giftid = $this->request->param('giftid', 0, 'intval');
        $this->assign('giftid', $giftid);
        $this->assign('numslist', $this->getNums());
        $this->assign('jackpot_level', $this->getJackpotLevel());
        
		return $this->fetch();
	}
    
    function addPost(){
		if ($this->request->isPost()) {
            
            $data      = $this->request->param();
            
			$giftid=$data['giftid'];
			$nums=$data['nums'];
			
            $where=[];
            $where[]=['giftid','=',$giftid];
            $where[]=['nums','=',$nums];
            
            $check = DB::name('jackpot_rate')->where($where)->find();
            if($check){
                $this->error('相同数量的配置已存在');
            }
            
            $rate_jackpot = $this->request->param('rate_jackpot/a');
            $data['rate_jackpot']=json_encode($rate_jackpot);
            
            
			$id = DB::name('jackpot_rate')->insertGetId($data);
            if(!$id){
                $this->error("添加失败！");
            }
            
			$action="礼物管理-礼物ID：".$giftid." 奖池设置添加ID: ".$id;
			setAdminLog($action);
            
            $this->resetcache();
            $this->success("添加成功！");
            
		}
	}
    
    function edit(){
        
        $id   = $this->request->param('id', 0, 'intval');
        
        $data=Db::name('jackpot_rate')
            ->where("id={$id}")
            ->find();
        if(!$data){
            $this->error("信息错误");
        }
        
        $data['rate_jackpot']=json_decode($data['rate_jackpot'],true);
        
        $this->assign('numslist', $this->getNums());
        $this->assign('jackpot_level', $this->getJackpotLevel());
        $this->assign('data', $data);
        return $this->fetch();
	}
    
    function editPost(){
		if ($this->request->isPost()) {
            
            $data      = $this->request->param();
            
			$giftid=$data['giftid'];
			$nums=$data['nums'];
			$id=$data['id'];

            $where=[];
            $where[]=['giftid','=',$giftid];
            $where[]=['nums','=',$nums];
            $where[]=['id','<>',$id];
            
            $check = DB::name('jackpot_rate')->where($where)->find();
            if($check){
                $this->error('相同数量、倍数的配置已存在');
            }
            
            $rate_jackpot = $this->request->param('rate_jackpot/a');
            
            $data['rate_jackpot']=json_encode($rate_jackpot);
            
			$rs = DB::name('jackpot_rate')->update($data);
            if($rs===false){
                $this->error("修改失败！");
            }
			
			
			$action="礼物管理-礼物ID：".$giftid." 奖池设置修改ID: ".$id;
			setAdminLog($action);
            
            $this->resetcache();
            $this->success("修改成功！");
		}
	}
    
    function resetcache(){
		$key='jackpot_rate';

        $level= DB::name("jackpot_rate")->order("id desc")->select();
        if($level){
            setcaches($key,$level);
        }else{
			delcache($key);
		}
       
        return 1;
    }       

}
