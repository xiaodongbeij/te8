<?php
/**
 * 分销
 */
namespace app\appapi\controller;

use cmf\controller\HomeBaseController;
use think\Db;

class AgentController extends HomebaseController {
	
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
		  
		$nowtime=time();

		$userinfo=getUserInfo($uid);
		$code=Db::name('agent_code')->where(["uid"=>$uid])->value('code');
		
		if(!$code){
			$code=createCode();
            $ifok=Db::name('agent_code')->where(["uid"=>$uid])->update(array("code"=>$code));
            if(!$ifok){
                Db::name('agent_code')->insert(array('uid'=>$uid,"code"=>$code));
            }
			
		}

		$code_a=str_split($code);

		$this->assign("code",$code);
		$this->assign("code_a",$code_a);
		$agentinfo=array();
        
        /* 是否是分销下级 */
        $users_agent=Db::name("agent")->where(["uid"=>$uid])->find();
		if($users_agent){
			$agentinfo= getUserInfo($users_agent['one_uid']);
		}
		
		
		$agentprofit=Db::name("agent_profit")->where(["uid"=>$uid])->find();
		
		$one_profit=$agentprofit['one_profit'];
		if(!$one_profit){
			$one_profit=0;
		}

		$agnet_profit=array(
			'one_profit'=>number_format($one_profit),
		);

		$this->assign("uid",$uid);
		$this->assign("token",$token);
		$this->assign("userinfo",$userinfo);
		$this->assign("agentinfo",$agentinfo);
		$this->assign("agnet_profit",$agnet_profit);

		return $this->fetch();
	    
	}
	
	function agent(){
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
		
		$agentinfo=array();
		
		$users_agent=Db::name('agent')->where(["uid"=>$uid])->find();
		if($users_agent){
			$agentinfo=getUserInfo($users_agent['one_uid']);
			
			$code=Db::name('agent_code')->where("uid={$users_agent['one_uid']}")->value('code');
			
			$agentinfo['code']=$code;
			$code_a=str_split($code);

			$this->assign("code_a",$code_a);
		}
	
		
		$this->assign("uid",$uid);
		$this->assign("token",$token);

		$this->assign("agentinfo",$agentinfo);

		return $this->fetch();
	}
	
	function setAgent(){
		$data = $this->request->param();
        $uid=isset($data['uid']) ? $data['uid']: '';
        $token=isset($data['token']) ? $data['token']: '';
        $code=isset($data['code']) ? $data['code']: '';
        $uid=(int)checkNull($uid);
        $token=checkNull($token);
        $code=checkNull($code);
		
		$rs=array('code'=>0,'info'=>array(),'msg'=>'设置成功');
		
		if(checkToken($uid,$token)==700){
			$rs['code']=700;
			$rs['msg']='您的登陆状态失效，请重新登陆！';
			echo json_encode($rs);
			exit;
		} 

		if($code==""){
			$rs['code']=1001;
			$rs['msg']='邀请码不能为空';
			echo json_encode($rs);
			exit;
		}
		
		$isexist=Db::name('agent')->where(["uid"=>$uid])->find();
		if($isexist){
			$rs['code']=1001;
			$rs['msg']='已设置';
			echo json_encode($rs);
			exit;
		}
		
		$oneinfo=Db::name('agent_code')->field("uid")->where(["code"=>$code])->find();
		if(!$oneinfo){
			$rs['code']=1002;
			$rs['msg']='邀请码错误';
			echo json_encode($rs);
			exit;
		}
		
		if($oneinfo['uid']==$uid){
			$rs['code']=1003;
			$rs['msg']='不能填写自己的邀请码';
			echo json_encode($rs);
			exit;
		}
		
		$one_agent=Db::name('agent')->where("uid={$oneinfo['uid']}")->find();
		if(!$one_agent){
			$one_agent=array(
				'uid'=>$oneinfo['uid'],
				'one_uid'=>0,
			);
		}else{

			if($one_agent['one_uid']==$uid){
				$rs['code']=1004;
				$rs['msg']='您已经是该用户的上级';
				echo json_encode($rs);
				exit;
			}
		}
		
		$data=array(
			'uid'=>$uid,
			'one_uid'=>$one_agent['uid'],
			'addtime'=>time(),
		);
		Db::name('agent')->insert($data);

		echo json_encode($rs);
		exit;
	}

	function quit(){
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
		
		$isexist=Db::name('agent')->where(["uid"=>$uid])->delete();

		echo json_encode($rs);
		exit;
	}
	
	function one(){
		$data = $this->request->param();
        $uid=isset($data['uid']) ? $data['uid']: '';
        $token=isset($data['token']) ? $data['token']: '';
        $uid=(int)checkNull($uid);
        $token=checkNull($token);
		
		if(checkToken($uid,$token)==700){
			$this->assign("reason",'您的登陆状态失效，请重新登陆！');
			$this->display(':error');
			exit;
		} 
		
		$list=Db::name('agent_profit_recode')->field("uid,sum(one_profit) as total")->where(["one_uid"=>$uid])->group("uid")->order("addtime desc")->limit(0,50)->select()->toArray();
		foreach($list as $k=>$v){
			$list[$k]['userinfo']=getUserInfo($v['uid']);
			$list[$k]['total']=NumberFormat($v['total']);
		}
		$this->assign("uid",$uid);
		$this->assign("token",$token);
		$this->assign("list",$list);
		return $this->fetch();
	}

	function one_more(){
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
		
		$list=Db::name('agent_profit_recode')->field("uid,sum(one_profit) as total")->where(["one_uid"=>$uid])->group("uid")->order("addtime desc")->limit($start,$pnums)->select()->toArray();
		foreach($list as $k=>$v){
			$list[$k]['userinfo']=getUserInfo($v['uid']);
			$list[$k]['total']=NumberFormat($v['total']);
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