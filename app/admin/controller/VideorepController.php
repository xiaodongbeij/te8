<?php

/**
 * 短视频-举报
 */
namespace app\admin\controller;

use cmf\controller\AdminBaseController;
use think\Db;

class VideorepController extends AdminbaseController {
    protected function getStatus($k=''){
        $status=array(
            '0'=>'未处理',
            '1'=>'已处理',
        );
        if($k===''){
            return $status;
        }
        
        return isset($status[$k]) ? $status[$k]: '';
    }
    
    function index(){
        $data = $this->request->param();
        $map=[];
		
        $status=isset($data['status']) ? $data['status']: '';
        if($status!=''){
            $map[]=['status','=',$status];
        }
        
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
                $map[]=['uid',['=',$uid],['in',$lianguid],'or'];
            }else{
                $map[]=['uid','=',$uid];
            }
        }
        
        $lists = DB::name("video_report")
            ->where($map)
            ->order('id desc')
            ->paginate(20);
        
        $lists->each(function($v,$k){
            $v['userinfo']=getUserInfo($v['uid']);
            $v['touserinfo']=getUserInfo($v['touid']);
            $v['content']=nl2br($v['content']);
            //判断视频是否下架
            $isdel=Db::name("video")->where("id={$v['videoid']}")->value("isdel");
            isset($isdel)?$v['isdel']=$isdel:$v['isdel']=0;
            return $v;
        });
        
        $lists->appends($data);
        $page = $lists->render();
        
        $this->assign('lists', $lists);
        
    	$this->assign('status', $this->getStatus());
    	$this->assign("page", $page);
    	
    	return $this->fetch();
		
    }
    
    public function setstatus(){

        $id = $this->request->param('id', 0, 'intval');
        if(!$id){
            $this->error("数据传入失败！");
        }
        
        $nowtime=time();
        
        $rs=DB::name("video_report")->where("id={$id}")->update(['status'=>1,'uptime'=>$nowtime]);
        if($rs===false){
            $this->error("操作失败");
        }
		
		$action="视频管理-标记处理举报列表ID: ".$id;
		setAdminLog($action);
        
        $this->success("操作成功");        
    }

    function del(){
        $id = $this->request->param('id', 0, 'intval');
        if($id){
            $result=DB::name("video_report")->delete($id);				
            if($result){
				
				
				$action="视频管理-删除举报列表ID: ".$id;
				setAdminLog($action);
				
                $this->success('删除成功');
             }else{
                $this->error('删除失败');
             }
        }else{				
            $this->error('数据传入失败！');
        }				
    }

}
