<?php

/**
 * 直播举报
 */
namespace app\admin\controller;

use cmf\controller\AdminBaseController;
use think\Db;

class ReportController extends AdminbaseController {
    protected function getStatus($k=''){
        $status=[
            '0'=>'待处理',
            '1'=>'已处理',
        ];
        
        if($k==''){
            return $status;
        }
        return $status[$k];
    }
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
        
        $status=isset($data['status']) ? $data['status']: '';
        if($status!=''){
            $map[]=['status','=',$status];
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
			

    	$lists = Db::name("report")
                ->where($map)
                ->order("id DESC")
                ->paginate(20);
        
        $lists->each(function($v,$k){
			$v['userinfo']=getUserInfo($v['uid']);
            $user_status=Db::name("user")->where("id={$v['touid']}")->value('user_status');
            $touserinfo=getUserInfo($v['touid']);
            $touserinfo['user_status']=$user_status;
			$v['touserinfo']=$touserinfo;
            return $v;           
        });
        
        $lists->appends($data);
        $page = $lists->render();

    	$this->assign('lists', $lists);

    	$this->assign("page", $page);
        
    	$this->assign("status", $this->getStatus());
    	
    	return $this->fetch();
    }
		
    function setstatus(){
        $id = $this->request->param('id', 0, 'intval');
        $data['status']=1;
        $data['uptime']=time();
        $data=[
            'status'=>1,
            'uptime'=>time(),
        ];
        $rs = DB::name('report')->where("id={$id}")->update($data);
        if($rs===false){
            $this->error("标记失败！");
        }
        
        $action="直播举报标记处理：{$id}";
        setAdminLog($action);
        
        $this->success("标记成功！");
        							  		
    }		
    
    function del(){
        
        $id = $this->request->param('id', 0, 'intval');
        
        $rs = DB::name('report')->where("id={$id}")->delete();
        if(!$rs){
            $this->error("删除失败！");
        }
        
        $action="删除直播举报：{$id}";
        setAdminLog($action);
        
        $this->success("删除成功！",url("report/index"));								  
    }

    
}
