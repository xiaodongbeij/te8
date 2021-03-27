<?php

/**
 * 大转盘 价格配置
 */
namespace app\admin\controller;

use cmf\controller\AdminBaseController;
use think\Db;

class TurntableconController extends AdminbaseController {
    
    function index(){
        
    	$lists = Db::name("turntable_con")
			->order("list_order asc,id asc")
			->paginate(20);
        
        $page = $lists->render();

    	$this->assign('lists', $lists);

    	$this->assign("page", $page);
    	
    	return $this->fetch();
    }
    
    //排序
    public function listOrder() { 
		
        $model = DB::name('turntable_con');
        parent::listOrders($model);
		
		$action="更新大转盘价格列表排序 ";
        setAdminLog($action);
        $this->resetcache();
        $this->success("排序更新成功！");
        
    }

    function edit(){
        $id   = $this->request->param('id', 0, 'intval');
        
        $data=Db::name('turntable_con')
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
            
			$times=$data['times'];

			if($times<1){
				$this->error("请输入正确的次数");
			}
			$coin=$data['coin'];
			if($coin<1){
				$this->error("请输入正确的价格");
			}

            
			$rs = DB::name('turntable_con')->update($data);
            if($rs===false){
                $this->error("修改失败！");
            }
			
			
			$action="修改大转盘价格列表排序ID: ".$data['id'];
			setAdminLog($action);
            
            $this->resetcache();
            $this->success("修改成功！");
		}
    }
        
    function resetcache(){
        $key='turntable_con';
        $list=DB::name('turntable_con')
                ->field("id,times,coin")
                ->order('list_order asc,id asc')
                ->select();
        if($list){
            setcaches($key,$list);
        }else{
			delcache($key);
		}
        return 1;
    }
}
