<?php

/**
 * 游戏记录
 */
namespace app\admin\controller;

use cmf\controller\AdminBaseController;
use think\Db;
    
class GameController extends AdminbaseController {
    
    protected function getTypes($k=''){
        $type=array(
            '0'=>'否',
            '1'=>'是',
        );
        if($k===''){
            return $type;
        }
        
        return isset($type[$k]) ? $type[$k]: '';
    }
    
    protected function getStatus($k=''){
        $type=array(
            "0"=>"进行中",
            "1"=>"正常结束",
            "2"=>"主播关闭",
            "3"=>"意外结束"
        );
        if($k===''){
            return $type;
        }
        
        return isset($type[$k]) ? $type[$k]: '';
    }
    
    protected function getAction($k=''){
        $type=array(
            "1"=>"智勇三张",
            "2"=>"海盗船长",
            "3"=>"转盘",
            "4"=>"开心牛仔",
            "5"=>"二八贝"
        );
        if($k===''){
            return $type;
        }
        
        return isset($type[$k]) ? $type[$k]: '';
    }
    
    protected function getRs($k=''){
        $type=array(
            "1"=>"未中奖",
            "2"=>"中奖"
        );
        if($k===''){
            return $type;
        }
        
        return isset($type[$k]) ? $type[$k]: '';
    }
    
    function index(){

		$data = $this->request->param();
        $map=[];
		
        $action=isset($data['action']) ? $data['action']: '';
        if($action!=''){
            $map[]=['action','=',$action];
        }
        
        $liveuid=isset($data['liveuid']) ? $data['liveuid']: '';
        if($liveuid!=''){
            $lianguid=getLianguser($liveuid);
            if($lianguid){
                $map[]=['liveuid',['=',$liveuid],['in',$lianguid],'or'];
            }else{
                $map[]=['liveuid','=',$liveuid];
            }
        }
        
		
    	$lists = Db::name("game")
			->where($map)
			->order("id DESC")
			->paginate(20);
        $lists->each(function($v,$k){

			$v['userinfo']=getUserInfo($v['liveuid']);
            
            return $v;           
        });
			
    	$lists->appends($data);
        $page = $lists->render();


    	$this->assign('lists', $lists);

    	$this->assign("page", $page);
        
    	$this->assign("action", $this->getAction());
    	$this->assign("status", $this->getStatus());
    	$this->assign("type", $this->getTypes());
    	
    	return $this->fetch();
    }

    function index2(){

		$data = $this->request->param();
        $map=[];
		
        $this->result=$result=isset($data['result']) ? $data['result']: '';
        
        $gameid=isset($data['gameid']) ? $data['gameid']: '';
        if($gameid!=''){
            $map[]=['gameid','=',$gameid];
        }
        
        $result_n=$result;
		if(strstr($result,',')){
			$result_a=explode(',',$result);
			$result_n='';
			foreach($result_a as $k=>$v){
				if($v==3){
					$result_n.=($k+1).':赢 ';
				}else{
					$result_n.=($k+1).':输 ';
				}
				
			}
		}
        
        
        $rs=isset($data['rs']) ? $data['rs']: '';
        if($rs!=''){
            if(strstr($result,',')){
				$result_a=explode(',',$result);
				$string=1;
				foreach($result_a as $k=>$v){
					$n=$k+1;
					if($rs==2){
						if($v==3){
							$map[]=["coin_{$n}",'>','0'];
                            $string=0;
						}
					}else{
						if($v==3){
							$map[]=["coin_{$n}",'=','0'];
                            $string=0;
						}
					}
					
				}
				if($string==1){
					if($rs==1){
                        $map[]=["coin_4",'=','0'];
					}else{
                        $map[]=["coin_4",'>','0'];
					}
				}
				
			}else{
				if($rs==1){
                    $map[]=["coin_{$result}",'=','0'];
				}else{
                    $map[]=["coin_{$result}",'>','0'];
				}
				
			}
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
        
        $configpri=getConfigPri();
        $this->game_pump=$configpri['game_pump'];

    	$lists = Db::name("gamerecord")
			->where($map)
			->order("id DESC")
			->paginate(20);
            
        $lists->each(function($v,$k){

			$v['userinfo']=getUserInfo($v['uid']);
            
            $total=0;
            $total2=0;
            $result=$this->result;
            if(strstr($result,',')){
                $result_a=explode(',',$result);
                foreach($result_a as $k1=>$v1){
                    $total2+=$v['coin_'.($k1+1)];
                    if($v1==3){
                        $total+=$v['coin_'.($k1+1)];
                    }
                }
            }else{
                $total=$v['coin_'.$result];
            }
            
            $win=$total + floor($total*(2-1)*(100 - $this->game_pump)*0.01) - $total2;
            
            $v['win']=$win;
            
            return $v;           
        });
			
    	$lists->appends($data);
        $page = $lists->render();

    	$this->assign('lists', $lists);

    	$this->assign("page", $page);
        
        $this->assign("action", $this->getAction());
    	$this->assign("rs", $this->getRs());
    	$this->assign("gameid", $gameid);
    	$this->assign("result", $result);
        
    	$this->assign("result_n", $result_n);
    	
    	return $this->fetch();
    }

		
}
