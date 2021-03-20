<?php

/**
 * 管理员日志
 */
namespace app\admin\controller;

use cmf\controller\AdminBaseController;
use think\Db;

class AdminlogController extends AdminbaseController {

    function index(){
        
        $data = $this->request->param();
        $map=['plat'=>1];
        
        $start_time=isset($data['start_time']) ? $data['start_time']: '';
        $end_time=isset($data['end_time']) ? $data['end_time']: '';
        
        if($start_time!=""){
           $map[]=['addtime','>=',strtotime($start_time)];
        }

        if($end_time!=""){
           $map[]=['addtime','<=',strtotime($end_time) + 60*60*24];
        }
        
        $adminid=isset($data['adminid']) ? $data['adminid']: '';
        if($adminid!=''){
            $map[]=['adminid','=',$adminid];
        }
			

    	$lists = Db::name("admin_log")
                ->where($map)
                ->order("id DESC")
                ->paginate(20);
                
        $lists->appends($data);
        $page = $lists->render();

    	$this->assign('lists', $lists);

    	$this->assign("page", $page);
    	
    	return $this->fetch();
    }
		
    function del(){
        $id = $this->request->param('id', 0, 'intval');
        
        $rs = DB::name('admin_log')->where("id={$id}")->delete();
        if(!$rs){
            $this->error("删除失败！");
        }
        
        $this->success("删除成功！");
        							  			
    }
    
    function export(){

        $data = $this->request->param();
        $map=['plat'=>1];
        
        $start_time=isset($data['start_time']) ? $data['start_time']: '';
        $end_time=isset($data['end_time']) ? $data['end_time']: '';
        
        if($start_time!=""){
           $map[]=['addtime','>=',strtotime($start_time)];
        }

        if($end_time!=""){
           $map[]=['addtime','<=',strtotime($end_time) + 60*60*24];
        }
        
        $adminid=isset($data['adminid']) ? $data['adminid']: '';
        if($adminid!=''){
            $map[]=['adminid','=',$adminid];
        }
			
        
        $xlsName  = "管理员日志";
        $xlsData=Db::name("admin_log")->where($map)->order("addtime DESC")->select()->toArray();
        
        foreach ($xlsData as $k => $v)
        {
            $xlsData[$k]['ip']=long2ip($v['ip']);
            $xlsData[$k]['addtime']=date("Y-m-d H:i:s",$v['addtime']);             
        }
                $cellName = array('A','B','C','D','E');
                $xlsCell  = array(
                    array('id','序号'),
                    array('admin','管理员'),
                    array('action','行为'),
                    array('ip','IP'),
                    array('addtime','提交时间'),
        );
        exportExcel($xlsName,$xlsCell,$xlsData,$cellName);
    }

    function index_home(){

        $data = $this->request->param();
        $map=['plat'=>2];

        $start_time=isset($data['start_time']) ? $data['start_time']: '';
        $end_time=isset($data['end_time']) ? $data['end_time']: '';

        if($start_time!=""){
            $map[]=['addtime','>=',strtotime($start_time)];
        }

        if($end_time!=""){
            $map[]=['addtime','<=',strtotime($end_time) + 60*60*24];
        }

        $adminid=isset($data['adminid']) ? $data['adminid']: '';
        if($adminid!=''){
            $map[]=['adminid','=',$adminid];
        }


        $lists = Db::name("admin_log")
            ->where($map)
            ->order("id DESC")
            ->paginate(20);

        $lists->appends($data);
        $page = $lists->render();

        $this->assign('lists', $lists);

        $this->assign("page", $page);

        return $this->fetch();
    }

    function export_home(){

        $data = $this->request->param();
        $map=['plat'=>2];

        $start_time=isset($data['start_time']) ? $data['start_time']: '';
        $end_time=isset($data['end_time']) ? $data['end_time']: '';

        if($start_time!=""){
            $map[]=['addtime','>=',strtotime($start_time)];
        }

        if($end_time!=""){
            $map[]=['addtime','<=',strtotime($end_time) + 60*60*24];
        }

        $adminid=isset($data['adminid']) ? $data['adminid']: '';
        if($adminid!=''){
            $map[]=['adminid','=',$adminid];
        }


        $xlsName  = "前台日志";
        $xlsData=Db::name("admin_log")->where($map)->order("addtime DESC")->select()->toArray();

        foreach ($xlsData as $k => $v)
        {
            $xlsData[$k]['ip']=long2ip($v['ip']);
            $xlsData[$k]['addtime']=date("Y-m-d H:i:s",$v['addtime']);
        }
        $cellName = array('A','B','C','D','E');
        $xlsCell  = array(
            array('id','序号'),
            array('admin','用户'),
            array('action','行为'),
            array('ip','IP'),
            array('addtime','提交时间'),
        );
        exportExcel($xlsName,$xlsCell,$xlsData,$cellName);
    }
}
