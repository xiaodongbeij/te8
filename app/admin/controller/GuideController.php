<?php

/**
 * 引导页
 */
namespace app\admin\controller;

use cmf\controller\AdminBaseController;
use think\Db;

class GuideController extends AdminbaseController {

    function set(){

        $config=DB::name("option")->where("option_name='guide'")->value("option_value");

		$this->assign('config',json_decode($config,true) );
    	
    	return $this->fetch();
    }
    
    function setPost(){
        if ($this->request->isPost()) {
            
            $config = $this->request->param('post/a');
            $time=$config['time'];

            if(!$time||$time<1){
                $this->error("图片展示时间错误");
            }

            if(floor($time)!=$time){
                $this->error("图片展示时间错误");
            }
			
			//查询已存在的内容
			$info=DB::name("option")->where("option_name='guide'")->value("option_value");
            
			$rs = DB::name('option')->where("option_name='guide'")->update(['option_value'=>json_encode($config)] );
            if($rs===false){
                $this->error("保存失败！");
            }
			
			
			if($info){
				$option_value=json_decode($info,true);
				
				$action="修改引导页 ";
				
				if($config['switch'] !=$option_value['switch']){
					$switch=$config['switch']?'开':'关';
					$action.='引导页开关 '.$switch.' ';
				}
				
				if($config['type'] !=$option_value['type']){
					$type=$config['type']?'视频':'图片';
					$action.='引导页类型 '.$type.' ';
				}
				
				if($config['time'] !=$option_value['time']){
					$action.='图片展示时间 '.$config['time'].' ';
				}
				
				setAdminLog($action);

			}
            
            $this->success("保存成功！");
            
		}
    }
    
    function index(){

        $config=DB::name("option")->where("option_name='guide'")->value("option_value");
        
        $config = json_decode($config,true);
        
        $type=$config['type'];
        
        $map['type']=$type;
        
        
        $lists = Db::name("guide")
            ->where($map)
			->order("list_order asc, id desc")
			->paginate(20);
        
        $lists->each(function($v,$k){
			$v['thumb']=get_upload_path($v['thumb']);
            return $v;           
        });
        
        $page = $lists->render();

    	$this->assign('lists', $lists);

    	$this->assign("page", $page);
        
        $this->assign('type', $type);
    	
    	return $this->fetch();
    }
	
    function del(){
        $id = $this->request->param('id', 0, 'intval');
        
        $rs = DB::name('guide')->where("id={$id}")->delete();
        if(!$rs){
            $this->error("删除失败！");
        }

		$action="删除引导页ID: ".$id;
		setAdminLog($action);
        
        $this->success("删除成功！");
        
    }
    //排序
    public function listOrder() { 
		
        $model = DB::name('guide');
        parent::listOrders($model);
		
		
		$action="修改引导页排序 ";
		setAdminLog($action);
        
        $this->success("排序更新成功！");
        
    }
    
    function add(){
        $config=DB::name("option")->where("option_name='guide'")->value("option_value");
        
        $config = json_decode($config,true);
        
        $type=$config['type'];
        
        if($type==1){
            $map['type']=$type;
            
            $count=DB::name("guide")->where($map)->count();
            if($count>=1){
                $this->error("引导页视频只能存在一个");
            }
        }
        
        $this->assign('type', $type);
        
		return $this->fetch();
	}
    function addPost(){
		if ($this->request->isPost()) {
            
            $data = $this->request->param();

            $thumb=$data['thumb'];

            if(!$thumb){
                $this->error("请上传引导页图片/视频");
            }
            
            $data['href']=html_entity_decode($data['href']);
            $data['addtime']=time();
            $data['uptime']=time();
            
			$id = DB::name('guide')->insertGetId($data);
            if(!$id){

                $this->error("添加失败！");
            }
			
			$action="添加引导页ID: ".$id;
			setAdminLog($action);
            
            $this->success("添加成功！");
            
		}
	}
    
	function edit(){
        
        $id   = $this->request->param('id', 0, 'intval');
        
        $data=Db::name('guide')
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
            
            $thumb=$data['thumb'];

            if(!$thumb){
                $this->error("请上传引导页图片");
            }
            
            $data['href']=html_entity_decode($data['href']);
            $data['uptime']=time();
            
			$rs = DB::name('guide')->update($data);
            if($rs===false){

                $this->error("修改失败！");
            }
			
			
			$action="编辑引导页ID: ".$data['id'];
			setAdminLog($action);
            
            $this->success("修改成功！");
		}
	}
}
