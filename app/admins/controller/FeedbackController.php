<?php

/**
 * 用户反馈
 */
namespace app\admin\controller;

use app\admin\model\Feedback;
use cmf\controller\AdminBaseController;
use think\Db;

class FeedbackController extends AdminbaseController {
    
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
		
        $lists = Feedback::where($map)
			->order("id desc")
			->paginate(20);

        $lists->each(function($v,$k){
			$v['userinfo']=getUserInfo($v['uid']);
//            $v['thumb'] = get_upload_path($v['thumb']);
			$v['content']=nl2br($v['content']);
            return $v;           
        });

        $thumb_img = [];
        foreach ($lists as $k => $v){
            $thumb_img[$v['id']] = explode(',', $v['thumb']);
            foreach ($thumb_img[$v['id']] as $key => $val){
                $thumb_img[$v['id']][$key] = get_upload_path($val);
            }
        }

        $lists->appends($data);
        $page = $lists->render();

    	$this->assign('lists', $lists);

    	$this->assign("page", $page);
    	$this->assign("thumb_img", $thumb_img);

        $this->assign('status', $this->getStatus());
    	return $this->fetch();
    }
    
    function setstatus(){
        $id = $this->request->param('id', 0, 'intval');
        
        $rs = DB::name('feedback')->where("id={$id}")->update(['status'=>1,'uptime'=>time()]);
        if(!$rs){
            $this->error("标记失败！");
        }
        
        $action="用户反馈标记处理：{$id}";
        setAdminLog($action);
                    
        $this->success("标记成功！");
        							  			
    }
    
    function del(){
        $id = $this->request->param('id', 0, 'intval');
        
        $rs = DB::name('feedback')->where("id={$id}")->delete();
        if(!$rs){
            $this->error("删除失败！");
        }
        
        $action="删除用户反馈：{$id}";
        setAdminLog($action);
                    
        $this->success("删除成功！");
        							  			
    }
    
}
