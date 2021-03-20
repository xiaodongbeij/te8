<?php
/**
 * 个人主页
 */
namespace app\appapi\controller;

use cmf\controller\HomeBaseController;
use think\Db;

class HomeController extends HomebaseController {
	
	function index(){       
        $touid = $this->request->param('touid', 0, 'intval');
        
        if(!$touid){
            $this->assign("reason",'信息错误');
			return $this->fetch(':error');
        }

		$info=getUserInfo($touid);	

        if(!$info){
            $this->assign("reason",'信息错误');
			return $this->fetch(':error');
        }        

		$info['follows']=NumberFormat(getFollownums($touid));
		$info['fans']=NumberFormat(getFansnums($touid));
        
        $this->assign('info',$info);

		/* 贡献榜前三 */

		$contribute=Db::name("user_coinrecord")
				->field("uid,sum(totalcoin) as total")
				->where(["action"=>'1' , "touid"=>$touid])
				->group("uid")
				->order("total desc")
				->limit(0,3)
				->select();
		foreach($contribute as $k=>$v){
			$userinfo=getUserInfo($v['uid']);
			$v['avatar']=$userinfo['avatar'];
			$contribute[$k]=$v;
		}		

        $this->assign('contribute',$contribute);
		
        /* 视频数 */
        $info['videonums']='0';
        /* 直播数 */
        $livenums=Db::name("live_record")
					->where(["uid"=>$touid])
					->count();

        $this->assign('livenums',$livenums);
		/* 直播记录 */
		$record=array();
		$record=Db::name("live_record")
					->field("id,uid,nums,starttime,endtime,title,city")
					->where(["uid"=>$touid])
					->order("id desc")
					->limit(0,20)
					->select()
                    ->toArray();
		foreach($record as $k=>$v){
            if($v['title']==''){
                $record[$k]['title']='无标题';
            }
			$record[$k]['datestarttime']=date("Y.m.d",$v['starttime']);
			$record[$k]['dateendtime']=date("Y.m.d",$v['endtime']);
            $cha=$v['endtime']-$v['starttime'];
            $record[$k]['length']=getSeconds($cha);
		}			

        $this->assign('liverecord',$record);
        
        
        /* 标签 */

        $label=getMyLabel($touid);
        
        $labels=array_slice($label,0,3);
        
		$this->assign('labels',$labels);

		
		return $this->fetch();
	    
	}

}