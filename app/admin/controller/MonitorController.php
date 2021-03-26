<?php

/**
 * 直播监控
 */
namespace app\admin\controller;

use cmf\controller\AdminBaseController;
use think\Db;

class MonitorController extends AdminbaseController {
    function index(){

		$config=getConfigPri();
		$this->config=$config;
		$this->assign('config', $config);
        
        $lists = Db::name("live")->alias('lv')->leftJoin('cmf_user u','u.id=lv.uid')
            ->field('showid,uid,user_nicename,starttime,pull,stream,isvideo')
            ->where(['islive'=>1])
			->order("starttime desc")
			->paginate(20);
        
        $lists->each(function($v,$k){
    
            // $v['userinfo']=getUserInfo($v['uid']);
            if($v['isvideo'] != 1){
                $auth_url=PrivateKeyA('http',$v['stream'].'.flv',0);
                $v['url']=$auth_url;
            }else{
                $v['url'] = $v['pull'];
            }
            
            return $v; 
		
        });

        
        $page = $lists->render();

    	$this->assign('lists', $lists);

    	$this->assign("page", $page);
    	
    	return $this->fetch();

    }
    
	public function full()
	{
        $uid = $this->request->param('uid', 0, 'intval');
        
        $where['islive']=1;
        $where['uid']=$uid;
        
		$live=Db::name("live")->where($where)->find();

        
		if($live['title']=="")
		{
			$live['title']="直播监控后台";
		}
        
        $pull=urldecode(PrivateKeyA('http',$live['stream'].'.flv',0));
		$live['pull']=$pull;
		$this->assign('config', $config);
		$this->assign('live', $live);
        
		return $this->fetch();
	}
	public function stopRoom(){
        
		$uid = $this->request->param('uid', 0, 'intval');
        
        $where['islive']=1;
        $where['uid']=$uid;
        
		$liveinfo=Db::name("live")->field("uid,showid,starttime,title,province,city,stream,lng,lat,type,type_val,liveclassid")->where($where)->find();
        
		Db::name("live")->where(" uid='{$uid}'")->delete();
        
		if($liveinfo){
			$liveinfo['endtime']=time();
			$liveinfo['time']=date("Y-m-d",$liveinfo['showid']);
            
            $where2=[];
            $where2['touid']=$uid;
            $where2['showid']=$liveinfo['showid'];
            
			$votes=Db::name("user_coinrecord")
				->where($where2)
				->sum('totalcoin');
			$liveinfo['votes']=0;
			if($votes){
				$liveinfo['votes']=$votes;
			}
            
            $stream=$liveinfo['stream'];
			$nums=zSize('user_'.$stream);

			hDel("livelist",$uid);
			delcache($uid.'_zombie');
			delcache($uid.'_zombie_uid');
			delcache('attention_'.$uid);
			delcache('user_'.$stream);
			delcache($uid.":nums");
			
			$liveinfo['nums']=$nums;
			
			Db::name("live_record")->insert($liveinfo);
            
           
		}
        //$redis -> close();
        $action="监控 关闭直播间：{$uid}";
        setAdminLog($action);
		
//		echo "{'status':0,'data':{},'info':''}";
//		exit;
		//echo  json_encode( array("status"=>'1','info'=>'') );
		$this->success("操作成功！");
	}				
}
