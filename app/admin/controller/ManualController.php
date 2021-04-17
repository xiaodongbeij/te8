<?php

/**
 * 管理员手动充值记录
 */
namespace app\admin\controller;

use cmf\controller\AdminBaseController;
use think\Db;

class ManualController extends AdminbaseController {
    function index(){
        $data = $this->request->param();
        $map=[];
        
        $start_time=isset($data['start_time']) ? $data['start_time']: '';
        $end_time=isset($data['end_time']) ? $data['end_time']: '';
        
        if($start_time!=""){
           $map[]=['addtime','>=',strtotime($start_time)];
        }

        if($end_time!=""){
           $map[]=['addtime','<=',strtotime($end_time) + 60*60*24];
        }
        
        $status=isset($data['status']) ? $data['status']: '';
        if($status!=''){
            $map[]=['status','=',$status];
        }

        $type=isset($data['type']) ? $data['type']: '';
        if($type!=''){
            $map[]=['type','=',$type];
        }
        
        $uid=isset($data['uid']) ? $data['uid']: '';
        if($uid!=''){
            $lianguid=getLianguser($uid);
            if($lianguid){
                $map[]=['touid',['=',$uid],['in',$lianguid],'or'];
            }else{
                $map[]=['touid','=',$uid];
            }
        }

        $lists = Db::name("charge_admin")
            ->where($map)
			->order("id desc")
			->paginate(20);
        
        $lists->each(function($v,$k){
			$v['userinfo']=getUserInfo($v['touid']);
			$v['ip']=long2ip($v['ip']);
            return $v;           
        });
        
        $lists->appends($data);
        $page = $lists->render();

    	$this->assign('lists', $lists);

    	$this->assign("page", $page);
    	
        $coin = Db::name("charge_admin")
            ->where($map)
			->sum('coin');
        if(!$coin){
            $coin=0;
        }

    	$this->assign('coin', $coin);
        
    	return $this->fetch();
    }
		
	function add(){
		return $this->fetch();
	}
	function addPost(){
		if ($this->request->isPost()) {
            
            $data      = $this->request->param();
//            dump($data);die;
			$touid=$data['touid'];

			if($touid==""){
				$this->error("请填写用户ID");
			}
            
            $uid=Db::name("user")->where(["id"=>$touid])->value("id");
            if(!$uid){
                $this->error("会员不存在，请更正");
                
            }

            $type = isset($data['type']) ? $data['type']: '';
            if ($type == '' || !in_array($type,[1,2,3])){
                $this->error("类型错误");
            }
            // unset($data['type']);

			$coin=$data['coin'];
			if($coin=="" || $coin<0){
				$this->error("请填写正数点数");
			}
			if(!cmf_google_token_check(session('google_token'),$data['google_token'])) {
                $this->error('令牌验证失败');
            }
            unset($data['google_token']);
            $adminid=cmf_get_current_admin_id();
            $admininfo=Db::name("user")->where(["id"=>$adminid])->value("user_login");
           
            $data['admin']=$admininfo;
            $ip=get_client_ip(0,true);
            
            $data['ip']=ip2long($ip);
            
            $data['addtime']=time();

//            if(!$id){
//                $this->error("充值失败！");
//            }

            $change_type = 1;
            if ($type == 2){
                $coin = -1 * $coin;
            }elseif ($type == 3){
                $change_type = 6;
            }

            //开启事务
            Db::startTrans();
            try {
                //手动充值账变记录
                $id = DB::name('charge_admin')->insertGetId($data);
                $res = user_change_action($touid,$change_type,$coin,$data['remarks'],$id);

                if ($id && $res){
                    Db::commit();
                    //增加累计充值
                    if ($type == 1){
                        Db::table('cmf_user')->where('id',$touid)->setInc('count_money',$coin);
                    }
                    $action="手动充值虚拟币ID：".$id;
                    setAdminLog($action);
                    $this->success("充值成功！");
                }else{
                    Db::rollback();
                    $this->error("充值失败！");
                }

            }catch (\Exception $e) {
                Db::rollback();
                $this->error("充值失败！");
            }

            
		}
	}
    
    function export(){
        $data = $this->request->param();
        $map=[];
        
        $start_time=isset($data['start_time']) ? $data['start_time']: '';
        $end_time=isset($data['end_time']) ? $data['end_time']: '';
        
        if($start_time!=""){
           $map[]=['addtime','>=',strtotime($start_time)];
        }

        if($end_time!=""){
           $map[]=['addtime','<=',strtotime($end_time) + 60*60*24];
        }
        
        $status=isset($data['status']) ? $data['status']: '';
        if($status!=''){
            $map[]=['status','=',$status];
        }
        
        $uid=isset($data['uid']) ? $data['uid']: '';
        if($uid!=''){
            $lianguid=getLianguser($uid);
            if($lianguid){
                $map[]=['touid',['=',$uid],['in',$lianguid],'or'];
            }else{
                $map[]=['touid','=',$uid];
            }
        }
        
        $xlsName  = "手动充值记录";
        $xlsData = Db::name("charge_admin")
            ->where($map)
			->order("id desc")
			->select()
            ->toArray();

        $type_list = [
            1 => '手动增',
            2 => '手动减',
            3 => '赠送',
        ];    


        foreach ($xlsData as $k => $v){

            $userinfo=getUserInfo($v['touid']);

            $xlsData[$k]['ip']=long2ip($v['ip']);
            $xlsData[$k]['user_nicename']= $userinfo['user_nicename'].'('.$v['touid'].')';
            $xlsData[$k]['addtime']=date("Y-m-d H:i:s",$v['addtime']); 
            if(in_array($v['type'],[1,2,3])) $xlsData[$k]['type'] = $type_list[$v['type']];
        }
        
        $action="导出手动充值记录：".Db::name("charge_admin")->getLastSql();
        setAdminLog($action);
        $cellName = array('A','B','C','D','E','F','G','H');
        $xlsCell  = array(
            array('id','序号'),
            array('admin','管理员'),
            array('user_nicename','会员 (账号)(ID)'),
            array('coin','充值点数'),
            array('ip','IP'),
            array('type','类型'),
            array('remarks','备注'),
            array('addtime','时间'),
        );
        exportExcel($xlsName,$xlsCell,$xlsData,$cellName);
    }
    

}
