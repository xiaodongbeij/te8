<?php

/**
 * 验证码管理
 */
namespace app\admin\controller;

use cmf\controller\AdminBaseController;
use think\Db;

class SendcodeController extends AdminbaseController {
    
    protected function getTypes($k=''){
        $type=array(
            '1'=>'短信验证码',
            '2'=>'邮箱验证码',
        );
        if($k===''){
            return $type;
        }
        
        return isset($type[$k]) ? $type[$k]: '';
    }
    
    function index(){

        $data = $this->request->param();
        $map=[];
		
        $type=isset($data['type']) ? $data['type']: '';
        if($type!=''){
            $map[]=['type','=',$type];
        }
        
        $start_time=isset($data['start_time']) ? $data['start_time']: '';
        $end_time=isset($data['end_time']) ? $data['end_time']: '';
        
        if($start_time!=""){
           $map[]=['addtime','>=',strtotime($start_time)];
        }

        if($end_time!=""){
           $map[]=['addtime','<=',strtotime($end_time) + 60*60*24];
        }
        
        $keyword=isset($data['keyword']) ? $data['keyword']: '';
        if($keyword!=''){
            $map[]=['account','like',"%".$keyword."%"];
        }
        
    	$lists = DB::name("sendcode")
            ->where($map)
            ->order('id desc')
            ->paginate(20);
        
        $lists->each(function($v,$k){
            $v['account']=m_s($v['account']);
            return $v;
        });
        
        $lists->appends($data);
        $page = $lists->render();
        
        $this->assign('lists', $lists);

    	$this->assign('type', $this->getTypes());
    	$this->assign("page", $page);
    	
    	return $this->fetch();
    }
    
    function del(){
        $id = $this->request->param('id', 0, 'intval');
        if($id){
            $result=DB::name("sendcode")->delete($id);				
            if($result){
                $this->success('删除成功');
             }else{
                $this->error('删除失败');
             }
        }else{				
            $this->error('数据传入失败！');
        }				
    }	
    
    function export(){
        
        $data = $this->request->param();
        $map=[];
		
        $type=isset($data['type']) ? $data['type']: '';
        if($type!=''){
            $map[]=['type','=',$type];
        }
        
        $start_time=isset($data['start_time']) ? $data['start_time']: '';
        $end_time=isset($data['end_time']) ? $data['end_time']: '';
        
        if($start_time!=""){
           $map[]=['addtime','>=',strtotime($start_time)];
        }

        if($end_time!=""){
           $map[]=['addtime','<=',strtotime($end_time) + 60*60*24];
        }
        
        $keyword=isset($data['keyword']) ? $data['keyword']: '';
        if($keyword!=''){
            $map[]=['account','like',"%".$keyword."%"];
        }
        
        $xlsName  = "验证码";
        
    	$xlsData = DB::name("sendcode")
            ->where($map)
            ->order('id desc')
            ->select()
            ->toArray();
            
        
        foreach ($xlsData as $k => $v)
        {
            $xlsData[$k]['account']=m_s($v['account']);
            $xlsData[$k]['msg_type']=$this->getTypes($v['type']);
            $xlsData[$k]['addtime']=date("Y-m-d H:i:s",$v['addtime']);             
        }
		
		
		$action="验证码管理列表：".Db::name("sendcode")->getLastSql();
        setAdminLog($action);
        $cellName = array('A','B','C','D','E');
        $xlsCell  = array(
            array('id','序号'),
            array('msg_type','信息类型'),
            array('account','接收账号'),
            array('content','信息内容'),
            array('addtime','提交时间'),
        );
        exportExcel($xlsName,$xlsCell,$xlsData,$cellName);
    }
    
}
