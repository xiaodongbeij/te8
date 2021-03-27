<?php

/**
 * 店铺物流公司管理
 */
namespace app\admin\controller;

use cmf\controller\AdminBaseController;
use think\Db;

class ExpressController extends AdminbaseController {
    protected function getStatus($k=''){
        $status=array(
            '0'=>'隐藏',
            '1'=>'显示',
        );
        if($k==''){
            return $status;
        }
        return isset($status[$k])?$status[$k]:'';
    }
    
    /*分类列表*/
	function index(){
        $data = $this->request->param();
        $map=[];
        
        
        $keyword=isset($data['keyword']) ? $data['keyword']: '';
        if($keyword!=''){
            $map[]=['express_name','like','%'.$keyword.'%'];
        }
			

    	$lists = Db::name("shop_express")
                ->where($map)
                ->order("list_order asc,id DESC")
                ->paginate(20);
        
        $lists->each(function($v,$k){
			$v['express_thumb']=get_upload_path($v['express_thumb']);
            return $v;           
        });
        
        $lists->appends($data);
        $page = $lists->render();

    	$this->assign('lists', $lists);

    	$this->assign("page", $page);
        
    	$this->assign("status", $this->getStatus());
    	
    	return $this->fetch();
	}
    
    //分类排序
    function listOrder() { 
        $model = DB::name('shop_express');
        parent::listOrders($model);
        
        $this->resetcache();
		
		
		$action="更新物流公司列表顺序";
        setAdminLog($action);

        $this->success("排序更新成功！");
    }


	/*分类删除*/
	function del(){
        $id = $this->request->param('id', 0, 'intval');
        
        $rs = DB::name('shop_express')->where("id={$id}")->delete();
        if(!$rs){
            $this->error("删除失败！");
        }

        $this->resetcache();
        
		
		$action="删除物流公司ID: ".$id;
        setAdminLog($action);
        $this->success("删除成功！");
	}


	/*分类添加*/
	function add(){
        $this->assign("status", $this->getStatus());
		return $this->fetch();
	}

	/*分类添加提交*/
	function add_post(){
		if ($this->request->isPost()) {
            
            $data      = $this->request->param();
            
			$express_name=$data['express_name'];

			if($express_name==""){
				$this->error("请填写快递公司名称");
			}
            
            $isexist=DB::name('shop_express')->where(['express_name'=>$express_name])->find();
            if($isexist){
                $this->error("快递公司名称已存在");
            }
            
			/*$express_thumb=$data['express_thumb'];
			if($express_thumb==""){
				$this->error("请上传快递公司图标");
			}*/

            $express_phone=$data['express_phone'];
            if($express_phone==""){
                $this->error("请填写快递公司电话");
            }
			if(!preg_match("/^\d*$/",$express_phone)){
				$this->error("请填写正确的快递公司电话");
			}
            $express_code=$data['express_code'];
            if($express_code==""){
                $this->error("请填写快递公司编码");
            }
            
            $data['addtime']=time();
            
			$id = DB::name('shop_express')->insertGetId($data);
            if(!$id){
                $this->error("添加失败！");
            }
			
			
			$action="添加物流公司ID: ".$id;
			setAdminLog($action);

            $this->resetcache();
            
            $this->success("添加成功！");
            
		}

	}

	/*分类编辑*/
	function edit(){
        
        $id   = $this->request->param('id', 0, 'intval');
        
        $data=Db::name('shop_express')
            ->where("id={$id}")
            ->find();
        if(!$data){
            $this->error("信息错误");
        }
        
        $this->assign('status',$this->getStatus());
        $this->assign('data', $data);
        return $this->fetch();
	}

	/*分类编辑提交*/
	function edit_post(){
        if ($this->request->isPost()){
            
            $data = $this->request->param();
            
			$express_name=$data['express_name'];
			$id=$data['id'];

			if($express_name==""){
				$this->error("请填写物流公司名称");
			}
            
            $isexist=DB::name('shop_express')->where([['id','<>',$id],['express_name','=',$express_name]])->find();
            if($isexist){
                $this->error("物流公司名称已存在");
            }
            
			$express_thumb=$data['express_thumb'];
			if($express_thumb==""){
				$this->error("请上传物流公司图标");
			}
            
            $express_phone=$data['express_phone'];

            if($express_phone==""){
                $this->error("请填写物流公司电话");
            }

            $express_code=$data['express_code'];
            if($express_code==""){
                $this->error("请填写快递公司编码");
            }

            $data['edittime']=time();
            
			$rs = DB::name('shop_express')->update($data);
            if($rs===false){
                $this->error("修改失败！");
            }

            $this->resetcache();
			
			
			$action="编辑物流公司ID: ".$data['id'];
			setAdminLog($action);
            
            $this->success("添加成功！");
            
		}

	}

    //获取物流公司编码列表
    function expresslist(){

        $json_string=file_get_contents(CMF_ROOT."/public/static/express.json");
        $expresslist = json_decode($json_string, true);
        $lists=$expresslist['data'];
        $keyword=$this->request->param("keyword");
        if($keyword){
            $newlist=[];
            foreach ($lists as $k => $v) {
                if(strpos($v['name'],$keyword)!==false){
                   $newlist[]=$v; 
                }
            }

            

          $lists=$newlist;  
        }
        

        $this->assign('lists',$lists);

        return $this->fetch();
    }

    // 写入物流信息缓存
    function resetcache(){
        $key='getExpressList';
        
        $rs=DB::name('shop_express')
            ->field("id,express_name,express_phone,express_thumb")
            ->where('express_status=1')
            ->order("list_order asc,id desc")
            ->select();
        if($rs){
            setcaches($key,$rs);
        }   
        return 1;
    }

}
