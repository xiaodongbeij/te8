<?php

/**
 * 背景音乐分类管理
 */
namespace app\admin\controller;

use cmf\controller\AdminBaseController;
use think\Db;

class MusiccatController extends AdminbaseController {
    protected function getDel($k=''){
        $type=array(
            '0'=>'否',
            '1'=>'是',
        );
        if($k==''){
            return $type;
        }
        return isset($type[$k])?$type[$k]:'';
    }
    
    /*分类列表*/
	function index(){
        $data = $this->request->param();
        $map=[];
        
        
        $keyword=isset($data['keyword']) ? $data['keyword']: '';
        if($keyword!=''){
            $map[]=['title','like','%'.$keyword.'%'];
        }
			

    	$lists = Db::name("music_classify")
                ->where($map)
                ->order("list_order asc,id DESC")
                ->paginate(20);
        
        $lists->each(function($v,$k){
			$v['img_url']=get_upload_path($v['img_url']);
            return $v;           
        });
        
        $lists->appends($data);
        $page = $lists->render();

    	$this->assign('lists', $lists);

    	$this->assign("page", $page);
        
    	$this->assign("isdel", $this->getDel());
    	
    	return $this->fetch();
	}
    
    //分类排序
    function listOrder() { 
        $model = DB::name('music_classify');
        parent::listOrders($model);
        
		$action="视频管理-音乐分类更新排序";
		setAdminLog($action);
		
        $this->success("排序更新成功！");
    }


	/*分类删除*/
	function del(){
        $id = $this->request->param('id', 0, 'intval');
        
        $rs = DB::name('music_classify')->where("id={$id}")->update(['isdel'=>1]);
        if(!$rs){
            $this->error("删除失败！");
        }
		
		
		$action="视频管理-删除音乐分类ID: ".$id;
		setAdminLog($action);
        
        $this->success("删除成功！");
	}

	/*分类取消删除*/
	function canceldel(){
        $id = $this->request->param('id', 0, 'intval');
        
        $rs = DB::name('music_classify')->where("id={$id}")->update(['isdel'=>0]);
        if(!$rs){
            $this->error("取消删除失败！");
        }
		
		$action="视频管理-取消删除音乐分类ID: ".$id;
		setAdminLog($action);
        
        $this->success("取消删除成功！");
	}

	/*分类添加*/
	function add(){
		return $this->fetch();
	}

	/*分类添加提交*/
	function addPost(){
		if ($this->request->isPost()) {
            
            $data      = $this->request->param();
            
			$title=$data['title'];

			if($title==""){
				$this->error("请填写分类名称");
			}
            
            $isexist=DB::name('music_classify')->where(['title'=>$title])->find();
            if($isexist){
                $this->error("分类名称已存在");
            }
            
			$img_url=$data['img_url'];
			if($img_url==""){
				$this->error("请上传分类图标");
			}
            
            $data['addtime']=time();
            
			$id = DB::name('music_classify')->insertGetId($data);
            if(!$id){
                $this->error("添加失败！");
            }
			
			$action="视频管理-添加音乐分类ID: ".$id;
			setAdminLog($action);
            
            $this->success("添加成功！");
            
		}

	}

	/*分类编辑*/
	function edit(){
        
        $id   = $this->request->param('id', 0, 'intval');
        
        $data=Db::name('music_classify')
            ->where("id={$id}")
            ->find();
        if(!$data){
            $this->error("信息错误");
        }
        
        $this->assign('data', $data);
        return $this->fetch();
	}

	/*分类编辑提交*/
	function editPost(){
        if ($this->request->isPost()) {
            
            $data      = $this->request->param();
            
			$title=$data['title'];
			$id=$data['id'];

			if($title==""){
				$this->error("请填写分类名称");
			}
            
            $isexist=DB::name('music_classify')->where([['id','<>',$id],['title','=',$title]])->find();
            if($isexist){
                $this->error("分类名称已存在");
            }
            
			$img_url=$data['img_url'];
			if($img_url==""){
				$this->error("请上传分类图标");
			}
            
            
			$rs = DB::name('music_classify')->update($data);
            if($rs===false){
                $this->error("修改失败！");
            }
			
			$action="视频管理-编辑音乐分类ID: ".$id;
			setAdminLog($action);
            
            $this->success("添加成功！");
            
		}

	}

}
