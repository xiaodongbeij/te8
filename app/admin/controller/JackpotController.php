<?php

/**
 * 奖池设置
 */
namespace app\admin\controller;

use cmf\controller\AdminBaseController;
use think\Db;

class JackpotController extends AdminbaseController {

    function set(){

        $config=Db::name("option")->where("option_name='jackpot'")->value("option_value");

		$this->assign('config',json_decode($config,true) );
    	
    	return $this->fetch();
    }
    
    function setPost(){
        if ($this->request->isPost()) {
            
            $config = $this->request->param('post/a');

            $luck_anchor=$config['luck_anchor'];
            $luck_jackpot=$config['luck_jackpot'];

            if($luck_anchor==''){
                $this->error("请填写主播比例");
            }

            if(!is_numeric($luck_anchor)){
                $this->error("主播比例必须填写数字");
            }

            if($luck_anchor<0||$luck_anchor>100){
                $this->error("主播比例必须在0-100之间");
            }

            if(floor($luck_anchor)!=$luck_anchor){
                $this->error("主播比例必须填写整数");
            }

            if($luck_jackpot==''){
                $this->error("请填写奖池比例");
            }

            if(!is_numeric($luck_jackpot)){
                $this->error("奖池比例必须填写数字");
            }

            if($luck_jackpot<0||$luck_jackpot>100){
                $this->error("奖池比例必须在0-100之间");
            }

            if(floor($luck_jackpot)!=$luck_jackpot){
                $this->error("奖池比例必须填写整数");
				
            }
			
			
			//查询已存在的内容
			$info=DB::name("option")->where("option_name='jackpot'")->value("option_value");
            
			$rs = DB::name('option')->where("option_name='jackpot'")->update(['option_value'=>json_encode($config)] );
            if($rs===false){
                $this->error("保存失败！");
            }
            $key='jackpotset';
            setcaches($key,$config);
			
			
			
			if($info){
				$option_value=json_decode($info,true);
				
				$action="修改奖池管理 ";
				
				if($config['switch'] !=$option_value['switch']){
					$switch=$config['switch']?'开':'关';
					$action.='奖池开关 '.$switch.' ';
				}
				
		
				if($config['luck_anchor'] !=$option_value['luck_anchor']){
					$action.='幸运礼物-主播比例 '.$config['luck_anchor'].' ';
				}
				
				if($config['luck_jackpot'] !=$option_value['luck_jackpot']){
					$action.='幸运礼物-奖池比例 '.$config['luck_jackpot'].' ';
				}
				
				setAdminLog($action);

			}
			
			
            
            $this->success("保存成功！");
            
		}
    }
    
    function index(){
        
        $lists = Db::name("jackpot_level")
			->order("levelid asc")
			->paginate(20);
        
        
        $page = $lists->render();

    	$this->assign('lists', $lists);

    	$this->assign("page", $page);
    	
    	return $this->fetch();
        
    }
    
	function del(){
        
        $id = $this->request->param('id', 0, 'intval');
        
        $rs = DB::name('jackpot_level')->where("id={$id}")->delete();
        if(!$rs){
            $this->error("删除失败！");
        }
        
		$action="删除奖池管理-列表ID: ".$id;
		setAdminLog($action);
                    
        $this->resetcache();
        $this->success("删除成功！");
            
	}
	
    function add(){
		return $this->fetch();
	}
    function addPost(){
		if ($this->request->isPost()) {
            
            $data      = $this->request->param();
            
			$levelid=$data['levelid'];

			if($levelid==""){
				$this->error("请填写等级");
			}
            
            $check = DB::name('jackpot_level')->where(["levelid"=>$levelid])->find();
            if($check){
                $this->error('等级不能重复');
            }
                
			$level_up=$data['level_up'];
			if($level_up==""){
				$this->error("请填写等级下限");
			}
            
            $data['addtime']=time();
            
			$id = DB::name('jackpot_level')->insertGetId($data);
            if(!$id){
                $this->error("添加失败！");
            }
            $action="添加奖池管理-列表等级: ".$id;
			setAdminLog($action);
            
            $this->resetcache();
            $this->success("添加成功！");
            
		}
	}
    
    function edit(){
        
        $id   = $this->request->param('id', 0, 'intval');
        
        $data=Db::name('jackpot_level')
            ->where("id={$id}")
            ->find();
        if(!$data){
            $this->error("信息错误");
        }
        
        $this->assign('data', $data);
        return $this->fetch();
	}
	
	function editPost(){
		if ($this->request->isPost()) {
            
            $data      = $this->request->param();
            
			$id=$data['id'];
			$levelid=$data['levelid'];

			if($levelid==""){
				$this->error("请填写等级");
			}
            
            $check = DB::name('jackpot_level')->where([["levelid",'=',$levelid],['id','<>',$id]])->find();
            if($check){
                $this->error('等级不能重复');
            }
                
			$level_up=$data['level_up'];
			if($level_up==""){
				$this->error("请填写等级下限");
			}

            if($level_up<0){
                $this->error("等级下限不能小于0");
            }

            if(!is_numeric($level_up)){
                $this->error("等级下限必须为数字");
            }

            if(floor($level_up)!=$level_up){
                $this->error("等级下限必须为整数");
            }
            
			$rs = DB::name('jackpot_level')->update($data);
            if($rs===false){
                $this->error("修改失败！");
            }
            
            $action="修改奖池管理-列表等级：{$data['id']}";
            setAdminLog($action);
            
            $this->resetcache();
            $this->success("修改成功！");
		}
	}
    
    function resetcache(){
        $key='jackpot_level';

        $level= DB::name("jackpot_level")->order("level_up asc")->select();
        if($level){
            setcaches($key,$level);
        }else{
			delcache($key);
		}
        
        return 1;
    }       

}
