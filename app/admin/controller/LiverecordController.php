<?php

/**
 * 直播记录
 */
namespace app\admin\controller;

use cmf\controller\AdminBaseController;
use think\Db;

class LiverecordController extends AdminbaseController {
    function index(){
        
        $config=getConfigPub();
	
        $data = $this->request->param();
        $map=[];
        
        $start_time=isset($data['start_time']) ? $data['start_time']: '';
        $end_time=isset($data['end_time']) ? $data['end_time']: '';
        
        if($start_time!=""){
           $map[]=['starttime','>=',strtotime($start_time)];
        }

        if($end_time!=""){
           $map[]=['starttime','<=',strtotime($end_time) + 60*60*24];
        }
        
        $uid=isset($data['uid']) ? $data['uid']: '';
        if($uid!=''){
            $lianguid=getLianguser($uid);
            if($lianguid){
                $map[]=['uid',['=',$uid],['in',$lianguid],'or'];
            }else{
                $map[]=['uid','=',$uid];
            }
        }	
			

    	$lists = Db::name("live_record")
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
    	$this->assign("config", $config);
    	
    	return $this->fetch();
    }
    
    function del()
    {
        $id = $this->request->param('id', 0, 'intval');
        
        $rs = DB::name('live_record')->where("id={$id}")->delete();
        if(!$rs){
            $this->error("删除失败！");
        }
        
        $this->success("删除成功！",url("liverecord/index"));		
    }
		
}
