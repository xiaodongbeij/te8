<?php

/**
 * 分销
 */
namespace app\admin\controller;

use cmf\controller\AdminBaseController;
use think\Db;
    
class AgentController extends AdminbaseController {
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
        
        $one_uid=isset($data['one_uid']) ? $data['one_uid']: '';
        if($one_uid!=''){
            $lianguid=getLianguser($one_uid);
            if($lianguid){
                $map[]=['one_uid',['=',$one_uid],['in',$lianguid],'or'];
            }else{
                $map[]=['one_uid','=',$one_uid];
            }
        }
        
		
    	$lists = Db::name("agent")
			->where($map)
			->order("id DESC")
			->paginate(20);
        $lists->each(function($v,$k){

			$v['userinfo']=getUserInfo($v['uid']);
            
			if($v['one_uid']){
				$oneuserinfo=getUserInfo($v['one_uid']);
			}else{
				$oneuserinfo['user_nicename']='未设置';
			}
			$v['oneuserinfo']=$oneuserinfo;
            
            return $v;           
        });
			
    	$lists->appends($data);
        $page = $lists->render();

    	$this->assign('lists', $lists);

    	$this->assign("page", $page);
    	
    	return $this->fetch();
    }

    function index2(){

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

    	$lists = Db::name("agent_profit")
			->where($map)
			->order("id DESC")
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
	
	
	function del()
	{
        $id = $this->request->param('id', 0, 'intval');
        
        $rs = DB::name('agent')->where("id={$id}")->delete();
        if(!$rs){
            $this->error("删除失败！");
        }

        $this->success("删除成功！");		
        								  	
	}
		
}
