<?php
/**
 * 我的明细
 */
namespace app\appapi\controller;

use cmf\controller\HomeBaseController;
use think\Db;

class DetailController extends HomebaseController {

	function index(){       
		$data = $this->request->param();
        $uid=isset($data['uid']) ? $data['uid']: '';
        $token=isset($data['token']) ? $data['token']: '';
        $uid=(int)checkNull($uid);
        $token=checkNull($token);
        
        $checkToken=checkToken($uid,$token);
		if($checkToken==700){
			$reason='您的登陆状态失效，请重新登陆！';
			$this->assign('reason', $reason);
			return $this->fetch(':error');
		}
        
		$this->assign("uid",$uid);
		$this->assign("token",$token);
		
		$list=Db::name('user_voterecord')->field("fromid,actionid,sum(nums) as num,sum(total) as totalall")->where(["action"=>'1',"uid"=>$uid])->group("fromid,actionid,showid")->order("addtime desc")->limit(0,50)->select()->toArray();
		foreach($list as $k=>$v){
			$giftinfo=Db::name('gift')->field("giftname")->where("id={$v['actionid']}")->find();
			if(!$giftinfo){
				$giftinfo=array(
					"giftname"=>'礼物已删除'
				);
			}
			$list[$k]['giftinfo']=$giftinfo;
			$list[$k]['totalall']=number_format($v['totalall']);
			$userinfo=getUserInfo($v['fromid']);
			if(!$userinfo){
				$userinfo=array(
					"user_nicename"=>'用户已删除'
				);
			}
			$list[$k]['userinfo']=$userinfo;
		}
		
		$this->assign("list",$list);
		
		$list_live=Db::name('live_record')->field("starttime,endtime")->where(["uid"=>$uid])->order("starttime desc")->limit(0,50)->select()->toArray();
		foreach($list_live as $k=>$v){
            
			$cha=$v['endtime']-$v['starttime'];
			$list_live[$k]['length']=getSeconds($cha,1);
            
            $list_live[$k]['starttime']=date("Y-m-d H:i",$v['starttime']);
			$list_live[$k]['endtime']=date("Y-m-d H:i",$v['endtime']);
		}

		$this->assign("list_live",$list_live);
		
		return $this->fetch();
	    
	}
	
	public function receive_more()
	{
		$data = $this->request->param();
        $uid=isset($data['uid']) ? $data['uid']: '';
        $token=isset($data['token']) ? $data['token']: '';
        $p=isset($data['page']) ? $data['page']: '1';
        $uid=(int)checkNull($uid);
        $token=checkNull($token);
        $p=checkNull($p);
		
		$result=array(
			'data'=>array(),
			'nums'=>0,
			'isscroll'=>0,
		);
	
		if(checkToken($uid,$token)==700){
			echo json_encode($result);
			exit;
		} 
		
		$pnums=50;
		$start=($p-1)*$pnums;
		
		$list=Db::name('user_voterecord')->field("fromid,actionid,sum(nums) as num,sum(total) as totalall")->where(["action"=>'1',"uid"=>$uid])->group("fromid,actionid,showid")->order("addtime desc")->limit($start,$pnums)->select()->toArray();
		foreach($list as $k=>$v){
			$giftinfo=Db::name('gift')->field("giftname")->where("id={$v['actionid']}")->find();
			if(!$giftinfo){
				$giftinfo=array(
					"giftname"=>'礼物已删除'
				);
			}
			$list[$k]['giftinfo']=$giftinfo;
            $list[$k]['totalall']=number_format($v['totalall']);
			$userinfo=getUserInfo($v['fromid']);
			if(!$userinfo){
				$userinfo=array(
					"user_nicename"=>'用户已删除'
				);
			}
			$list[$k]['userinfo']=$userinfo;
		}
		
		$nums=count($list);
		if($nums<$pnums){
			$isscroll=0;
		}else{
			$isscroll=1;
		}
		
		$result=array(
			'data'=>$list,
			'nums'=>$nums,
			'isscroll'=>$isscroll,
		);

		echo json_encode($result);
		exit;
	}
	
	public function liverecord_more()
	{
		$data = $this->request->param();
        $uid=isset($data['uid']) ? $data['uid']: '';
        $token=isset($data['token']) ? $data['token']: '';
        $p=isset($data['page']) ? $data['page']: '1';
        $uid=(int)checkNull($uid);
        $token=checkNull($token);
        $p=checkNull($p);
		
		$result=array(
			'data'=>array(),
			'nums'=>0,
			'isscroll'=>0,
		);
	
		if(checkToken($uid,$token)==700){
			echo json_encode($result);
			exit;
		} 
		
		$pnums=50;
		$start=($p-1)*$pnums;
		
		$list=Db::name('live_record')->field("starttime,endtime")->where(["uid"=>$uid])->order("starttime desc")->limit($start,$pnums)->select()->toArray();
		foreach($list as $k=>$v){
			$list[$k]['starttime']=date("Y-m-d H:i",$v['starttime']);
			$list[$k]['endtime']=date("Y-m-d H:i",$v['endtime']);
			$cha=$v['endtime']-$v['starttime'];
			$list[$k]['length']=getSeconds($cha,1);
		}

		$nums=count($list);
		if($nums<$pnums){
			$isscroll=0;
		}else{
			$isscroll=1;
		}
		
		$result=array(
			'data'=>$list,
			'nums'=>$nums,
			'isscroll'=>$isscroll,
		);

		echo json_encode($result);
		exit;
	}
	

}