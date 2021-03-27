<?php

/**
 * 贴纸礼物
 */
namespace app\admin\controller;

use cmf\controller\AdminBaseController;
use think\Db;

class StickerController extends AdminbaseController {
    protected function getTypes($k=''){
        $type=[
            '2'=>'贴纸礼物',
        ];
        if($k==''){
            return $type;
        }
        return isset($type[$k]) ? $type[$k]: '';
    }
    protected function getMark($k=''){
        $mark=[
            '0'=>'普通',
            '1'=>'热门',
            '2'=>'守护',
        ];
        if($k==''){
            return $mark;
        }
        return isset($mark[$k]) ? $mark[$k]: '';
    }
    
    protected function getSwftype($k=''){
        $swftype=[
            '0'=>'GIF',
            '1'=>'SVGA',
        ];
        if($k==''){
            return $swftype;
        }
        return isset($swftype[$k]) ? $swftype[$k]: '';
    }
    
    function index(){

    	$lists = Db::name("gift")
            ->where('type=2')
			->order("list_order asc,id desc")
			->paginate(20);
        
        $lists->each(function($v,$k){
			$v['gifticon']=get_upload_path($v['gifticon']);
			$v['swf']=get_upload_path($v['swf']);
            return $v;           
        });
        
        $page = $lists->render();

    	$this->assign('lists', $lists);

    	$this->assign("page", $page);
        
    	$this->assign("type", $this->getTypes());
    	$this->assign("mark", $this->getMark());
    	$this->assign("swftype", $this->getSwftype());
    	
    	return $this->fetch();
    }
    
	function del(){
        
        $id = $this->request->param('id', 0, 'intval');
        
        $rs = DB::name('gift')->where("id={$id}")->delete();
        if(!$rs){
            $this->error("删除失败！");
        }
        
        $action="删除贴纸礼物：{$id}";
        setAdminLog($action);
                    
        $this->resetcache();
        $this->success("删除成功！");
        
	}
    
    //排序
    public function listOrder() { 
		
        $model = DB::name('gift');
        parent::listOrders($model);
        
        $action="更新贴纸礼物排序";
        setAdminLog($action);
        
        $this->resetcache();
        $this->success("排序更新成功！");
        
    }

    function add(){
        
        $this->assign("type", $this->getTypes());
    	$this->assign("mark", $this->getMark());
    	$this->assign("swftype", $this->getSwftype());
        
        return $this->fetch();				
    }

	function addPost(){
		if ($this->request->isPost()) {
            
            $data      = $this->request->param();
            
            $giftname=$data['giftname'];
            if($giftname == ''){
                $this->error('请输入名称');
            }else{
                $check = Db::name('gift')->where("giftname='{$giftname}'")->find();
                if($check){
                    $this->error('名称已存在');
                }
            }
            
            
            $needcoin=$data['needcoin'];
            $gifticon=$data['gifticon'];
            $sticker_id=$data['sticker_id'];
            $swftime=$data['swftime'];
            
            if($needcoin==''){
                $this->error('请输入价格');
            }

            if($gifticon==''){
                $this->error('请上传图片');
            }
            
            if($sticker_id==''){
                $this->error('请填写贴纸ID');
            }

            if($swftime==''){
                $this->error('请填写动画时长');
            }
            
            
            $data['addtime']=time();
            
			$id = DB::name('gift')->insertGetId($data);
            if(!$id){
                $this->error("添加失败！");
            }
            
            $action="添加贴纸礼物：{$id}";
            setAdminLog($action);
            
            $this->resetcache();
            $this->success("添加成功！");
            
		}			
	}
    
    function edit(){

        $id   = $this->request->param('id', 0, 'intval');
        
        $data=Db::name('gift')
            ->where("id={$id}")
            ->find();
        if(!$data){
            $this->error("信息错误");
        }
        
        $this->assign("type", $this->getTypes());
    	$this->assign("mark", $this->getMark());
    	$this->assign("swftype", $this->getSwftype());
        
        $this->assign('data', $data);
        return $this->fetch();            
    }
    
	function editPost(){
		if ($this->request->isPost()) {
            
            $data      = $this->request->param();

            $id=$data['id'];
            $giftname=$data['giftname'];
            if($giftname == ''){
                $this->error('请输入名称');
            }else{
                $check = Db::name('gift')->where("giftname='{$giftname}' and id!={$id}")->find();
                if($check){
                    $this->error('名称已存在');
                }
            }
            
            
            $needcoin=$data['needcoin'];
            $gifticon=$data['gifticon'];
            $sticker_id=$data['sticker_id'];
            
            if($needcoin==''){
                $this->error('请输入价格');
            }

            if($gifticon==''){
                $this->error('请上传图片');
            }
            
            if($sticker_id==''){
                $this->error('请填写贴纸ID');
            }
            
            
			$rs = DB::name('gift')->update($data);
            if($rs===false){
                $this->error("修改失败！");
            }
            
            $action="修改贴纸礼物：{$data['id']}";
            setAdminLog($action);
            
            $this->resetcache();
            $this->success("修改成功！");
		}	
	}
        
    function resetcache(){
        $key='getPropgiftList';
        
		$rs=DB::name('gift')
			->field("id,type,mark,giftname,needcoin,gifticon,sticker_id,swftime,isplatgift")
            ->where('type=2')
			->order("list_order asc,id desc")
			->select();
        if($rs){
            setcaches($key,$rs);
        }else{
			delcache($key);
		}
        return 1;
    }
}
