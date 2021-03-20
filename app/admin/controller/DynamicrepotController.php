<?php

/* 动态举报 */
namespace app\admin\controller;

use cmf\controller\AdminBaseController;
use think\Db;

class DynamicrepotController extends AdminBaseController
{

    protected function getStatus($k=''){
        $status=array("0"=>"审核中","1"=>"已处理");
        if($k==''){
            return $status;
        }
        return isset($status[$k])? $status[$k] : '' ;
    }
    
    
    public function index(){
        
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
            $map[]=['uid','=',$uid];
            $lianguid=getLianguser($uid);
            if($lianguid){
                $map[]=['uid',['=',$uid],['in',$lianguid],'or'];
            }else{
                $map[]=['uid','=',$uid];
            }
        }
        
        $touid=isset($data['touid']) ? $data['touid']: '';
        if($touid!=''){
            $lianguid=getLianguser($touid);
            if($lianguid){
                $map[]=['touid',['=',$touid],['in',$lianguid],'or'];
            }else{
                $map[]=['touid','=',$touid];
            }
        }
        
        $dynamicid=isset($data['dynamicid']) ? $data['dynamicid']: '';
        if($dynamicid!=''){
            $map[]=['dynamicid','=',$dynamicid];
        }
        
        
        $list = Db::name('dynamic_report')
            ->where($map)
            ->order("id desc")
            ->paginate(20);
        
        $list->each(function($v,$k){
           $v['userinfo']= getUserInfo($v['uid']);
           $v['touserinfo']= getUserInfo($v['touid']);
           //获取动态是否下架
           $isdel=Db::name("dynamic")->where("id={$v['dynamicid']}")->value("isdel");
           isset($isdel)?$v['isdel']=$isdel:$v['isdel']=0;
           return $v; 
        });
        
        $list->appends($data);
        
        $page = $list->render();
        $this->assign("page", $page);
            
        $this->assign('list', $list);
        $this->assign('status', $this->getStatus());

        return $this->fetch();
    }


    public function setstatus()
    {
        $id = $this->request->param('id', 0, 'intval');
        if(!$id){
            $this->error("数据传入失败！");
        }
        $status = $this->request->param('status', 0, 'intval');
        
        $rs=DB::name("dynamic_report")->where("id={$id}")->update(['status'=>$status]);
        if($rs===false){
            $this->error("操作失败");
        }
		
		
		$action="标记处理动态举报列表ID：{$id}";
        setAdminLog($action);
        
        $this->success("操作成功");        
    }
    
    public function del()
    {
        $id = $this->request->param('id', 0, 'intval');
        if(!$id){
            $this->error("数据传入失败！");
        }
        
        $result=DB::name("dynamic_report")->where("id={$id}")->delete();
        if(!$result){
            $this->error("删除失败！");
        }
		
		$action="删除动态举报列表ID：{$id}";
        setAdminLog($action);

        $this->success("删除成功！");
    }

}