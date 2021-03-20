<?php
/**
 * 装备中心
 */
namespace app\appapi\controller;

use cmf\controller\HomeBaseController;
use think\Db;

class EquipmentController extends HomebaseController {

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
        
        
		/* 靓号信息 */
		$liang_list=Db::name('liang')->where(["uid"=>$uid])->order("buytime desc")->select();

		$this->assign("liang_list",$liang_list);
        
        
		/* 坐骑信息 */
        $car_key='carinfo';
        $car_list=getcaches($car_key);
        if(!$car_list){
            $car_list=Db::name("car")->order("list_order asc")->select()->toArray();
            if($car_list){
                setcaches($car_key,$car_list);
            }
        }
        
        foreach($car_list as $k=>$v){
            $v['thumb']=get_upload_path($v['thumb']);
            $v['swf']=get_upload_path($v['swf']);
            
            $carlist2[$v['id']]=$v;
        }

		/* 用户坐骑 */
		$nowtime=time();
        $where=[
            ['uid','=',$uid],
            ['endtime','>',$nowtime],
        ];
        
		$user_carlist=Db::name('car_user')->where($where)->select()->toArray();
		
		foreach($user_carlist as $k=>$v){
			if( isset($carlist2[$v['carid']]) ){
				$user_carlist[$k]['carinfo']=$carlist2[$v['carid']];
				$user_carlist[$k]['endtime_date']=date("Y-m-d",$v['endtime']);
			}else{
				unset($user_carlist[$k]);
			}
		}

		$this->assign("user_carlist",$user_carlist);

		return $this->fetch();
	    
	}
    
    /* 设置靓号 */
	function setliang(){
        
		$rs=array('code'=>0,'info'=>array(),'msg'=>'更换成功');
        
        $data = $this->request->param();
        $uid=isset($data['uid']) ? $data['uid']: '';
        $token=isset($data['token']) ? $data['token']: '';
        $liangid=isset($data['liangid']) ? $data['liangid']: '';
        $state=isset($data['state']) ? $data['state']: '';
        $uid=(int)checkNull($uid);
        $token=checkNull($token);
        $liangid=(int)checkNull($liangid);
        $state=(int)checkNull($state);
        

		
		if( !$uid || !$token || checkToken($uid,$token)==700){
			$rs['code']=700;
			$rs['msg']='您的登陆状态失效，请重新登陆！';
			echo json_encode($rs);
			exit;
		}
        
        $isexist=Db::name('liang')->where(["uid"=>$uid, "id"=>$liangid])->find();
        if(!$isexist){
            $rs['code']=1001;
			$rs['msg']='信息错误';
			echo json_encode($rs);
			exit;
        }
        
		Db::name('liang')->where(["uid"=>$uid])->update(array('state'=>0) );
		
		$setstatus=$state?0:1;
		$data=array(
			'state'=>$setstatus,
		);
		$list=Db::name('liang')->where(["uid"=>$uid, "id"=>$liangid])->update( $data );
		
        $goodnum=$isexist['name'];
		$key='liang_'.$uid;
		if($setstatus==1){
			Db::name('user')->where(["id"=>$uid])->setField("goodnum",$goodnum);
			
			$isexist=Db::name("liang")->where(["uid"=>$uid, "status"=>1, "state"=>1])->find();
			if($isexist){
				setcaches($key,$isexist);
			}
            
		}else{
			Db::name('user')->where(["id"=>$uid])->setField("goodnum",0);
			delcache($key);
		}
		
		echo json_encode($rs);
		exit;
	}
    
	/* 装备坐骑 */
	function setcar(){
        
        $data = $this->request->param();
        $uid=isset($data['uid']) ? $data['uid']: '';
        $token=isset($data['token']) ? $data['token']: '';
        $carid=isset($data['carid']) ? $data['carid']: '';
        $status=isset($data['status']) ? $data['status']: '';
        $uid=(int)checkNull($uid);
        $token=checkNull($token);
        $carid=(int)checkNull($carid);
        $status=(int)checkNull($status);
        
		
		$rs=array('code'=>0,'info'=>array(),'msg'=>'更换成功');
		
		if( !$uid || !$token || checkToken($uid,$token)==700){
			$rs['code']=700;
			$rs['msg']='您的登陆状态失效，请重新登陆！';
			echo json_encode($rs);
			exit;
		} 
		$setstatus=$status?0:1;
        
        $isexist=Db::name('car_user')->where(["uid"=>$uid,"carid"=>$carid])->find();
        if(!$isexist){
            $rs['code']=1001;
			$rs['msg']='信息错误';
			echo json_encode($rs);
			exit;
        }

		$data1=array(
			'status'=>0,
		);
		Db::name('car_user')->where(["uid"=>$uid])->update($data1);

		
		$data=array(
			'status'=>$setstatus,
		);
		$result=Db::name('car_user')->where(["uid"=>$uid,"carid"=>$carid])->update($data);
		
		
		$key='car_'.$uid;
		if($setstatus){
			$isexist=Db::name('car_user')->where(["uid"=>$uid,"status"=>1])->find();
			if($isexist){
				setcaches($key,$isexist);
			}
		}else{
			delcache($key);
        }
        
		echo json_encode($rs);
		exit;
		
	}

}