<?php

/**
 * 禁播列表
 */
namespace app\admin\controller;

use cmf\controller\AdminBaseController;
use think\Db;

class LivebanController extends AdminbaseController {
    function index(){
        $data = $this->request->param();
        $map=[];
        
        $start_time=isset($data['start_time']) ? $data['start_time']: '';
        $end_time=isset($data['end_time']) ? $data['end_time']: '';
        
        if($start_time!=""){
           $map[]=['addtime','>=',strtotime($start_time)];
        }

        if($end_time!=""){
           $map[]=['addtime','<=',strtotime($end_time) + 60*60*24];
        }
        
        $uid=isset($data['uid']) ? $data['uid']: '';
        if($uid!=''){
            $lianguid=getLianguser($uid);
            if($lianguid){
                $map[]=['liveuid',['=',$uid],['in',$lianguid],'or'];
            }else{
                $map[]=['liveuid','=',$uid];
            }
        }
		
        
    	$lists = Db::name("live_ban")
            ->where($map)
            ->order("addtime DESC")
            ->paginate(20);
        
        $lists->each(function($v,$k){
			$v['liveinfo']=getUserInfo($v['liveuid']);
			$v['superinfo']=getUserInfo($v['superid']);
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
		
		
        
        $rs = DB::name('live_ban')->where("liveuid={$id}")->delete();
        if(!$rs){
            $this->error("删除失败！");
        }
        $action="直播管理-禁播管理删除主播ID：{$id}";
		setAdminLog($action);
        $this->success("删除成功！",url("liveban/index"));
        						  			
    }		


    
}
