<?php

/**
 * 退款申请平台介入理由
 */
namespace app\admin\controller;

use cmf\controller\AdminBaseController;
use think\Db;

class PlatformreasonController extends AdminbaseController {
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
            $map[]=['name','like','%'.$keyword.'%'];
        }
			

    	$lists = Db::name("shop_platform_reason")
                ->where($map)
                ->order("list_order asc,id DESC")
                ->paginate(20);
        
        
        $lists->appends($data);
        $page = $lists->render();

    	$this->assign('lists', $lists);

    	$this->assign("page", $page);
        
    	$this->assign("status", $this->getStatus());
    	
    	return $this->fetch();
	}
    
    //分类排序
    function listOrder() { 
        $model = DB::name('shop_platform_reason');
        parent::listOrders($model);
        
        $this->resetcache();
		
		$action="更新退款申请平台介入原因列表排序";
        setAdminLog($action);

        $this->success("排序更新成功！");
    }


	/*分类删除*/
	function del(){
        $id = $this->request->param('id', 0, 'intval');
        
        $rs = DB::name('shop_platform_reason')->where("id={$id}")->delete();
        if(!$rs){
            $this->error("删除失败！");
        }

        $this->resetcache();
		
		
		$action="删除退款申请平台介入原因列表ID: ".$id;
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
            
            $data = $this->request->param();
            
			$name=$data['name'];

			if($name==""){
				$this->error("请填写退款原因");
			}
            
            $isexist=DB::name('shop_platform_reason')->where(['name'=>$name])->find();
            if($isexist){
                $this->error("退款原因已存在");
            }
            
            $data['addtime']=time();
            
			$id = DB::name('shop_platform_reason')->insertGetId($data);
            if(!$id){
                $this->error("添加失败！");
            }

            $this->resetcache();
			
			$action="添加退款申请平台介入原因列表ID: ".$id;
			setAdminLog($action);
            
            $this->success("添加成功！");
            
		}

	}

	/*分类编辑*/
	function edit(){
        
        $id   = $this->request->param('id', 0, 'intval');
        
        $data=Db::name('shop_platform_reason')
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
            
			$name=$data['name'];
			$id=$data['id'];

			if($name==""){
				$this->error("请填写退款原因");
			}
            
            $isexist=DB::name('shop_platform_reason')->where([['id','<>',$id],['name','=',$name]])->find();
            if($isexist){
                $this->error("退款原因已存在");
            }
            
            if(mb_strlen($name)>30){
                $this->error("字数不超过30字");
            }

            $data['edittime']=time();
            
			$rs = DB::name('shop_platform_reason')->update($data);
            if($rs===false){
                $this->error("修改失败！");
            }

            $this->resetcache();
			
			$action="修改退款申请平台介入原因列表ID: ".$id;
			setAdminLog($action);
            
            $this->success("修改成功！");
            
		}

	}


    // 写入物流信息缓存
    function resetcache(){
        $key='getPlatformReason';
        
        $rs=DB::name('shop_platform_reason')
            ->field("id,name")
            ->where('status=1')
            ->order("list_order asc,id desc")
            ->select();
        if($rs){
            setcaches($key,$rs);
        }   
        return 1;
    }

}
