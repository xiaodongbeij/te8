<?php

/**
 * 经验等级
 */
namespace app\admin\controller;

use cmf\controller\AdminBaseController;
use think\Db;

class LevelController extends AdminbaseController {
	
    function index(){
        
    	$lists = Db::name("level")
			->order("levelid asc")
			->paginate(20);
        
        $lists->each(function($v,$k){
			$v['thumb']=get_upload_path($v['thumb']);
			$v['thumb_mark']=get_upload_path($v['thumb_mark']);
			$v['bg']=get_upload_path($v['bg']);
            return $v;           
        });
        
        $page = $lists->render();

    	$this->assign('lists', $lists);

    	$this->assign("page", $page);
    	
    	return $this->fetch();
    }
    
    function del(){
        
        $id = $this->request->param('id', 0, 'intval');
        
        $rs = DB::name('level')->where("id={$id}")->delete();
        if(!$rs){
            $this->error("删除失败！");
        }
        
        $action="删除会员等级：{$id}";
        setAdminLog($action);
                    
        $this->resetcache();
        $this->success("删除成功！",url("level/index"));
            
	}		

	function add(){
		return $this->fetch();
	}
    
	function addPost(){
		if ($this->request->isPost()) {
            
            $data = $this->request->param();
            
			$levelid=$data['levelid'];
            $levelname=$data['levelname'];

			if($levelid==""){
				$this->error("等级不能为空");
			}

            if(!is_numeric($levelid)){
                $this->error("等级必须为数字");
            }

            if($levelid<1){
                $this->error("等级必须大于1");
            }

            if($levelid>99999999){
                $this->error("等级必须在1-99999999之间");
            }

            if(floor($levelid)!=$levelid){
                $this->error("等级必须为整数");
            }
            
            $check = Db::name('level')->where(["levelid"=>$levelid])->find();
            if($check){
                $this->error('等级不能重复');
            }

            if($levelname==''){
                $this->error('请填写等级名称');
            }
                
			$level_up=$data['level_up'];
			if($level_up==""){
				$this->error("请填写等级经验上限");
			}

            if(!is_numeric($level_up)){
                $this->error("等级经验上限必须为数字");
            }

            if($level_up<1||$level_up>99999999){
                $this->error("等级经验上限必须为1-99999999数字");
            }

            if(floor($level_up)!=$level_up){
                $this->error("等级经验上限必须为整数");
            }

			$colour=$data['colour'];
			if($colour==""){
				$this->error("请填写昵称颜色");
			}
            
            $thumb=$data['thumb'];
			if($thumb==""){
				$this->error("请上传图标");
			}
            
            $thumb_mark=$data['thumb_mark'];
			if($thumb_mark==""){
				$this->error("请上传头像角标");
			}

            $bg=$data['bg'];
            if($bg==""){
                $this->error("请上传背景图片");
            }
            
            $data['addtime']=time();
            
			$id = DB::name('level')->insertGetId($data);
            if(!$id){
                $this->error("添加失败！");
            }
            
            $action="添加会员等级：{$id}";
            setAdminLog($action);
            
            $this->resetcache();
            $this->success("添加成功！");
            
		}			
	}
        
	function edit(){
        
        $id   = $this->request->param('id', 0, 'intval');
        
        $data=Db::name('level')
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
            
            $data = $this->request->param();
            
			$id=$data['id'];
			$levelid=$data['levelid'];
            $levelname=$data['levelname'];

			if($levelid==""){
				$this->error("等级不能为空");
			}

            if($levelid==""){
                $this->error("等级不能为空");
            }

            if(!is_numeric($levelid)){
                $this->error("等级必须为数字");
            }

            if($levelid<1){
                $this->error("等级必须大于1");
            }

            if($levelid>99999999){
                $this->error("等级必须在1-99999999之间");
            }

            if(floor($levelid)!=$levelid){
                $this->error("等级必须为整数");
            }
            
            $check = Db::name('level')->where([['levelid','=',$levelid],['id','<>',$id]])->find();
            if($check){
                $this->error('等级不能重复');
            }

            if($levelname==''){
                $this->error('请填写等级名称');
            }
                
			$level_up=$data['level_up'];
			if($level_up==""){
				$this->error("请填写等级经验上限");
			}

            if(!is_numeric($level_up)){
                $this->error("等级经验上限必须为数字");
            }

            if($level_up<1||$level_up>99999999){
                $this->error("等级经验上限必须为1-99999999数字");
            }

            if(floor($level_up)!=$level_up){
                $this->error("等级经验上限必须为整数");
            }

			$colour=$data['colour'];
			if($colour==""){
				$this->error("请填写昵称颜色");
			}
            
            $thumb=$data['thumb'];
			if($thumb==""){
				$this->error("请上传图标");
			}
            
            $thumb_mark=$data['thumb_mark'];
			if($thumb_mark==""){
				$this->error("请上传头像角标");
			}

            $bg=$data['bg'];
            if($bg==""){
                $this->error("请上传背景图片");
            }
            
			$rs = DB::name('level')->update($data);
            if($rs===false){
                $this->error("修改失败！");
            }
            
            $action="修改会员等级：{$data['id']}";
            setAdminLog($action);
            
            $this->resetcache();
            $this->success("修改成功！");
		}
	}
    
    function resetcache(){
		$key='level';
        
        $level= Db::name("level")->order("level_up asc")->select();
        if($level){
            setcaches($key,$level);
        }else{
			delcache($key);
		}
        
        return 1;
    }
		
}
