<?php

/**
 * 积分消费记录
 */
namespace app\admin\controller;

use cmf\controller\AdminBaseController;
use think\Db;

class ScorerecordController extends AdminbaseController {
    
    protected function getTypes($k=''){
        $type=array(
            '0'=>'支出',
            '1'=>'收入',
        );
        if($k===''){
            return $type;
        }
        
        return isset($type[$k]) ? $type[$k]: '';
    }
    
    protected function getAction($k=''){
        $action=array(
            '4'=>'购买VIP',
            '5'=>'购买坐骑',
            '18'=>'购买靓号',
            '21'=>'游戏获胜',
        );
        if($k===''){
            return $action;
        }
        
        return isset($action[$k]) ? $action[$k]: '未知';
    }

    protected function getGame($k=''){
        $game=array(
            '1'=>'智勇三张',
			'2'=>'海盗船长',
			'3'=>'转盘',
			'4'=>'开心牛仔',
			'5'=>'二八贝',
        );
        if($k===''){
            return $game;
        }
        
        return isset($game[$k]) ? $game[$k]: '';
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
        
        $type=isset($data['type']) ? $data['type']: '';
        if($type!=''){
            $map[]=['type','=',$type];
        }
        
        $action=isset($data['action']) ? $data['action']: '';
        if($action!=''){
            $map[]=['action','=',$action];
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
        
        $touid=isset($data['touid']) ? $data['touid']: '';
        if($touid!=''){
            $map[]=['touid','=',$touid];
        }
        
        $lists = Db::name("user_scorerecord")
            ->where($map)
			->order("id desc")
			->paginate(20);
        
        $lists->each(function($v,$k){
			$v['userinfo']=getUserInfo($v['uid']);
			$v['touserinfo']=getUserInfo($v['touid']);
            
            $action=$v['action'];
            if($action=='4'){
                $info=Db::name("vip")->field("name")->where("id='{$v['giftid']}'")->find();
                $giftinfo['giftname']=$info['name'];
            }else if($action=='5'){
                $info=Db::name("car")->field("name")->where("id='{$v['giftid']}'")->find();
                $giftinfo['giftname']=$info['name'];
            }else if($action=='18'){
                $info=Db::name("liang")->field("name")->where("id='{$v['giftid']}'")->find();
                $giftinfo['giftname']=$info['name'];
            }else if($action=='21'){
                $info=Db::name("game")->field("action")->where("id='{$v['giftid']}'")->find();
                $giftinfo['giftname']=$this->getGame($info['action']);
            }else{
                $giftinfo['giftname']=$this->getAction($action);
                
            }
            $v['giftinfo']= $giftinfo;
                
            return $v;           
        });
    	
        $lists->appends($data);
        $page = $lists->render();

    	$this->assign('lists', $lists);

    	$this->assign("page", $page);
        
        $this->assign('action', $this->getAction());
        $this->assign('type', $this->getTypes());
        
    	return $this->fetch();
    
    }
		
    function del(){
        $id = $this->request->param('id', 0, 'intval');
        
        $rs = DB::name('user_scorerecord')->where("id={$id}")->delete();
        if(!$rs){
            $this->error("删除失败！");
        }
                    
        $this->success("删除成功！");
        							  			
    }    	
}
