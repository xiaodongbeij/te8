<?php

/**
 * 提现
 */
namespace app\admin\controller;

use cmf\controller\AdminBaseController;
use think\Db;

class CashController extends AdminbaseController {
    protected function getStatus($k=''){
        $status=array(
            '0'=>'未处理',
            '1'=>'提现成功',
            '2'=>'拒绝提现',
        );
        if($k===''){
            return $status;
        }
        
        return isset($status[$k]) ? $status[$k]: '';
    }
    
    protected function getTypes($k=''){
        $type=array(
            '1'=>'支付宝',
            '2'=>'微信',
            '3'=>'银行卡',
        );
        if($k===''){
            return $type;
        }
        
        return isset($type[$k]) ? $type[$k]: '';
    }
    
    function index(){
        $data = $this->request->param();
        $map=[];
		
        $status=isset($data['status']) ? $data['status']: '';
        if($status!=''){
            $map[]=['status','=',$status];
            $cash['type']=1;
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
        
        $keyword=isset($data['keyword']) ? $data['keyword']: '';
        if($keyword!=''){
            $map[]=['orderno|trade_no','like',"%".$keyword."%"];
        }
        
    	$lists = DB::name("cash_record")
            ->where($map)
            ->order('id desc')
            ->paginate(20);
        
        $lists->each(function($v,$k){
            $v['userinfo']=getUserInfo($v['uid']);
            return $v;
        });
        
        $lists->appends($data);
        $page = $lists->render();

        $cashrecord_total = DB::name("cash_record")->where($map)->sum("money");
        if($status=='')
        {
            $success=$map;
            $success[]=['status','=',1];
            $fail=$map;
            $fail[]=['status','=',2];
            $cashrecord_success = DB::name("cash_record")->where($success)->sum("money");
            $cashrecord_fail = DB::name("cash_record")->where($fail)->sum("money");
            $cash['success']=$cashrecord_success;
            $cash['fail']=$cashrecord_fail;
            $cash['type']=0;
        }
        $cash['total']=$cashrecord_total;
            
    	$this->assign('cash', $cash);
        
    	$this->assign('lists', $lists);

    	$this->assign('type', $this->getTypes());
    	$this->assign('status', $this->getStatus());
    	$this->assign("page", $page);
    	
    	return $this->fetch();
    }
		
    function del(){
        $id = $this->request->param('id', 0, 'intval');
        if($id){
            $result=DB::name("cash_record")->delete($id);				
            if($result){
                $action="删除提现记录：{$id}";
                setAdminLog($action);
                $this->success('删除成功');
             }else{
                $this->error('删除失败');
             }
        }else{				
            $this->error('数据传入失败！');
        }				
    }		

	function edit(){
        
        $id   = $this->request->param('id', 0, 'intval');
        
        $data=Db::name('cash_record')
            ->where("id={$id}")
            ->find();
        if(!$data){
            $this->error("信息错误");
        }
        
        $data['userinfo']=getUserInfo($data['uid']);
        
        $this->assign('type', $this->getTypes());
        $this->assign('status', $this->getStatus());
            
        $this->assign('data', $data);
        return $this->fetch();
	}
    
    function editPost(){
		if ($this->request->isPost()) {
            
            $data      = $this->request->param();
            
			$status=$data['status'];
			$uid=$data['uid'];
			$votes=$data['votes'];
			$id=$data['id'];

			if($status=='0'){
				$this->success("修改成功！");
			}

            
            $data['uptime']=time();
            
			$rs = DB::name('cash_record')->update($data);
            if($rs===false){
                $this->error("修改失败！");
            }
            
            if($status=='2'){
                 DB::name("user")->where(["id"=>$uid])->setInc("votes",$votes);
                $action="修改提现记录：{$id} - 拒绝";
            }else if($status=='1'){
                $action="修改提现记录：{$id} - 同意";
            }
            
            setAdminLog($action);
            
            $this->success("修改成功！");
		}
	}
    
    function export()
    {
        $data = $this->request->param();
        $map=[];
		
        $status=isset($data['status']) ? $data['status']: '';
        if($status!=''){
            $map[]=['status','=',$status];
            $cash['type']=1;
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
        
        $keyword=isset($data['keyword']) ? $data['keyword']: '';
        if($keyword!=''){
            $map[]=['orderno|trade_no','like',"%".$keyword."%"];
        }
        
        $xlsName  = "提现";
        
        $xlsData=DB::name("cash_record")
            ->where($map)
            ->order('id desc')
            ->select()
            ->toArray();

        foreach ($xlsData as $k => $v)
        {
            $userinfo=getUserInfo($v['uid']);
            $xlsData[$k]['user_nicename']= $userinfo['user_nicename']."(".$v['uid'].")";
            $xlsData[$k]['addtime']=date("Y-m-d H:i:s",$v['addtime']); 
            $xlsData[$k]['uptime']=date("Y-m-d H:i:s",$v['uptime']); 
            $xlsData[$k]['status']=$this->getStatus($v['status']);
        }


        $action="导出提现记录：".DB::name("cash_record")->getLastSql();
        setAdminLog($action);
        $cellName = array('A','B','C','D','E','F','G','H');
        $xlsCell  = array(
            array('id','序号'),
            array('user_nicename','主播名称'),
            array('votes','兑换映票'),
			array('money','提现金额'),
            array('trade_no','第三方支付订单号'),
            array('status','状态'),
            array('addtime','提交时间'),
            array('uptime','处理时间'),
        );
        exportExcel($xlsName,$xlsCell,$xlsData,$cellName);
    }
    
}
