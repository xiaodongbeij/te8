<?php

/**
 * 红包
 */
namespace app\admin\controller;

use cmf\controller\AdminBaseController;
use think\Db;

class RedController extends AdminbaseController {
    
    protected function getTypes($k=''){
        $status=array(
            '0'=>'平均',
            '1'=>'手气',
        );
        if($k===''){
            return $status;
        }
        
        return isset($status[$k]) ? $status[$k]: '';
    }
    
    protected function getTypegrant($k=''){
        $type=array(
            '0'=>'立即',
            '1'=>'延迟',
        );
        if($k===''){
            return $type;
        }
        
        return isset($type[$k]) ? $type[$k]: '';
    }
    
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
        
		$lists = DB::name("red")
            ->where($map)
            ->order('id desc')
            ->paginate(20);
        
        $lists->each(function($v,$k){
            $v['userinfo']=getUserInfo($v['uid']);
            $v['anchorinfo']=getUserInfo($v['liveuid']);
            return $v;
        });	
    	
        $lists->appends($data);
        $page = $lists->render();
        
        $this->assign('lists', $lists);

    	$this->assign('type', $this->getTypes());
    	$this->assign('type_grant', $this->getTypegrant());
    	$this->assign("page", $page);
    	
    	return $this->fetch();
    }

    function index2(){
        
        $data = $this->request->param();
        $map=[];

        
        
        $redid=isset($data['redid']) ? $data['redid']: '';
        if($redid!=''){
            $map[]=['redid','=',$redid];
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
        
		$lists = DB::name("red_record")
            ->where($map)
            ->order('id desc')
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
	
}
