<?php

/**
 * 付费内容列表
 */
namespace app\admin\controller;

use cmf\controller\AdminBaseController;
use think\Db;

class PaidprogramController extends AdminbaseController {


    protected function getType($k=''){
        $type=[
            '0'=>'单视频',
            '1'=>'多视频',
        ];
        if($k==''){
            return $type;
        }
        return isset($type[$k]) ? $type[$k]: '';
    }


    protected function getStatus($k=''){
        $mark=[
            '-1'=>'拒绝',
            '0'=>'审核中',
            '1'=>'同意',
        ];
        if($k==''){
            return $mark;
        }
        return isset($mark[$k]) ? $mark[$k]: '';
    }


    protected function getOrderType($k=''){
        $type=[
            '1'=>'支付宝',
            '2'=>'微信',
            '3'=>'余额',
        ];
        if($k==''){
            return $type;
        }
        return isset($type[$k]) ? $type[$k]: '';
    }


    protected function getOrderStatus($k=''){
        $mark=[
            '0'=>'未支付',
            '1'=>'已支付',
        ];

        if($k===''){
            return $mark;
        }

        return isset($mark[$k]) ? $mark[$k]: '';
    }

    
    function applylist(){

        $data = $this->request->param();
        $map=[];
        
        $status=isset($data['status']) ? $data['status']: '';
        if($status!=''){
            $map[]=['status','=',$status];
        }

        $uid=isset($data['uid']) ? $data['uid']: '';
        if($uid!=''){
            $lianguid=getLianguser($uid);
            if($lianguid){
                $map[]=['uid',['=',$uid],['in',$lianguid],'or'];
            }else{
                $map[]=['uid','=',$uid];
            }
        }

        $start_time=isset($data['start_time']) ? $data['start_time']: '';
        $end_time=isset($data['end_time']) ? $data['end_time']: '';
        
        if($start_time!=""){
           $map[]=['addtime','>=',strtotime($start_time)];
        }

        if($end_time!=""){
           $map[]=['addtime','<=',strtotime($end_time) + 60*60*24];
        }

        $lists = Db::name("paidprogram_apply")
            ->where($map)
            ->order("addtime desc")
            ->paginate(20);

        $lists->each(function($v,$k){
            $v['userinfo']=getUserInfo($v['uid']);
            return $v;           
        });

        $page = $lists->render();
        $this->assign('lists', $lists);
        $this->assign('page', $page);
        $this->assign('status', $this->getStatus());

        return $this->fetch();
    }

    //申请编辑
    function apply_edit(){
        $id = $this->request->param('id', 0, 'intval');
        
        $data=Db::name('paidprogram_apply')
            ->where("id={$id}")
            ->find();
        if(!$data){
            $this->error("信息错误");
        }

        $data['userinfo']=getUserInfo($data['uid']);
        
        $this->assign('status',$this->getStatus());
        $this->assign('data', $data);
        return $this->fetch();
    }

    //申请编辑提交
    function apply_edit_post(){
        if ($this->request->isPost()){
            $data = $this->request->param();
            $percent=$data['percent'];

            if($percent<0||$percent>100){
                $this->error("抽水比例应在0-100之间");
            }

            if(floor($percent)!=$percent){
                $this->error("抽水比例必须为整数");
            }

            $data['uptime']=time();

            $rs = DB::name('paidprogram_apply')->update($data);
            if($rs===false){
                $this->error("修改失败！");
            }
			
			
			$action="修改付费内容申请列表ID: ".$data['id'];
			setAdminLog($action);
            
            $this->success("修改成功！");

        }
        

    }


    //申请删除
    function apply_del(){
        $id = $this->request->param('id', 0, 'intval');
        $rs = DB::name('paidprogram_apply')->where("id={$id}")->delete();
        if(!$rs){
            $this->error("删除失败！");
        }
		
		$action="删除付费内容申请列表ID: ".$id;
		setAdminLog($action);

        $this->success("删除成功！");
    }
    
    //付费内容列表
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

        $uid=isset($data['uid']) ? $data['uid']: '';
        if($uid!=''){
            $lianguid=getLianguser($uid);
            if($lianguid){
                $map[]=['uid',['=',$uid],['in',$lianguid],'or'];
            }else{
                $map[]=['uid','=',$uid];
            }
        }

        $status=isset($data['status']) ? $data['status']: '';
        if($status!=''){
            $map[]=['status','=',$status];
        }

    	$lists = Db::name("paidprogram")
            ->where($map)
			->order("addtime desc")
			->paginate(20);
        
        $lists->each(function($v,$k){
            $v['userinfo']=getUserInfo($v['uid']);
			$v['thumb']=get_upload_path($v['thumb']);
			$v['classname']=Db::name("paidprogram_class")->where("id={$v['classid']}")->value("name");
            $video_arr=json_decode($v['videos'],true);
            foreach ($video_arr as $k1 => $v1) {
                $video_arr[$k1]['video_url']=get_upload_path($v1['video_url']);
            }

            $v['video_arr']=$video_arr;

            if($v['evaluate_nums']==0){
                $v['evaluate_point']=0;
            }else{
                $v['evaluate_point']=floor($v['evaluate_total']/$v['evaluate_nums']);
            }

            return $v;           
        });
        
        $page = $lists->render();

    	$this->assign('lists', $lists);

    	$this->assign("page", $page);
        
    	$this->assign("status", $this->getStatus());
        $this->assign("type", $this->getType());
    	
    	return $this->fetch();
    }

    //付费内容视频观看
    function videoplay(){
        $data=$this->request->param();
        $url=$data['url'];
        $this->assign("url", $url);
        return $this->fetch();
    }
    
    //删除付费内容
	function del(){
        
        $id = $this->request->param('id', 0, 'intval');
        
        $rs = DB::name('paidprogram')->where("id={$id}")->delete();
        if(!$rs){
            $this->error("删除失败！");
        }
        
        $action="删除付费内容ID：{$id}";
        setAdminLog($action);
        
        //删除评论内容         
        Db::name("paidprogram_comment")->where("object_id={$id}")->delete();
        //修改付费项目订单
        Db::name("paidprogram_order")->where("object_id={$id}")->update(array('isdel'=>1));

        //修改视频的绑定信息
        Db::name("video")->where("type=2 and goodsid={$id}")->update(array('type'=>0,'goodsid'=>0));

        $this->success("删除成功！");
        
	}
    
    //编辑付费内容
    function edit(){

        $id   = $this->request->param('id', 0, 'intval');
        
        $data=Db::name('paidprogram')
            ->where("id={$id}")
            ->find();
        if(!$data){
            $this->error("信息错误");
        }

        $data['userinfo']=getUserInfo($data['uid']);
        $data['thumb']=get_upload_path($data['thumb']);
        $data['classname']=Db::name("paidprogram_class")->where("id={$data['classid']}")->value("name");
        if($data['evaluate_nums']==0){
            $data['evaluate_point']=0;
        }else{
            $data['evaluate_point']=floor($data['evaluate_total']/$data['evaluate_nums']);
        }

        $video_arr=json_decode($data['videos'],true);
        foreach ($video_arr as $k1 => $v1) {
            $video_arr[$k1]['video_url']=get_upload_path($v1['video_url']);
        }

        $data['video_arr']=$video_arr;
        
        $this->assign("type", $this->getType());
    	$this->assign("status", $this->getStatus());        
        $this->assign('data', $data);

        return $this->fetch();            
    }
    
	function edit_post(){
		if ($this->request->isPost()) {
            
            $data= $this->request->param();

            $id=$data['id'];
            $status=$data['status'];
            $uid=$data['uid'];

            //判断付费内容发布者是否注销
            $is_destroy=checkIsDestroy($uid);
            if($is_destroy){
                $this->error("该用户已注销,付费内容状态不可调整");
            }
                        
			$info = DB::name('paidprogram')->find($id);
			$rs = DB::name('paidprogram')->update($data);
            if($rs===false){
                $this->error("修改失败！");
            }
			
			
			
			if($info['status']!=$status){
				$action="修改付费内容ID：{$id} 审核状态：".$this->getStatus($status);
				setAdminLog($action);
			}
            
            
            
            $this->success("修改成功！");
		}	
	}
    
    //付费内容订单
    function orderlist(){
        $data = $this->request->param();
        $map=[];
        
        $status=isset($data['status']) ? $data['status']: '';
        if($status!=''){
            $map[]=['status','=',$status];
        }

        $type=isset($data['type']) ? $data['type']: '';
        if($type!=''){
            $map[]=['type','=',$type];
        }
        
        $start_time=isset($data['start_time']) ? $data['start_time']: '';
        $end_time=isset($data['end_time']) ? $data['end_time']: '';
        
        if($start_time!=""){
           $map[]=['addtime','>=',strtotime($start_time)];
        }

        if($end_time!=""){
           $map[]=['addtime','<=',strtotime($end_time) + 60*60*24];
        }
        
        $uid=isset($data['uid']) ? $data['uid']: '';
        if($uid!=''){
            $lianguid=getLianguser($uid);
            if($lianguid){
                $map[]=['uid',['=',$uid],['in',$lianguid],'or'];
            }else{
                $map[]=['uid','=',$uid];
            }
        }
        
        $keyword=isset($data['keyword']) ? $data['keyword']: '';
        if($keyword!=''){
            $map[]=['orderno|trade_no','like',"%".$keyword."%"];
        }
        
        $lists = DB::name("paidprogram_order")
            ->where($map)
            ->order('id desc')
            ->paginate(20);
        
        $lists->each(function($v,$k){
            $v['userinfo']=getUserInfo($v['uid']);
            $v['touserinfo']=getUserInfo($v['touid']);
            $v['object_name']=Db::name("paidprogram")->where("id={$v['object_id']}")->value("title");
            return $v;
        });
        
        $lists->appends($data);
        $page = $lists->render();
        
        $this->assign('lists', $lists);

        $this->assign('type', $this->getOrderType());
        $this->assign('status', $this->getOrderStatus());
        $this->assign("page", $page);
        
        return $this->fetch();
    }

    //确认支付
    function setPay(){
        $id = $this->request->param('id', 0, 'intval');
        if($id){
            $orderinfo=Db::name("paidprogram_order")->where(["id"=>$id,"status"=>0])->find();
            if($orderinfo){

                $now=time();

                /* 更新 订单状态 */
                $data['status']=1;
                $data['edittime']=$now;
                
                Db::name("paidprogram_order")->where("id='{$orderinfo['id']}'")->update($data);

                $uid=$orderinfo['uid'];
                $touid=$orderinfo['touid'];
                $object_id=$orderinfo['object_id'];

                //获取用户的商城累计消费
                $balance_consumption=Db::name("user")->where("id={$uid}")->value("balance_consumption");

                //增加用户的商城累计消费
                Db::name("user")->where("id={$uid}")->setField('balance_consumption',$balance_consumption+$orderinfo['money']);

                //增加付费内容的销量
                Db::name("paidprogram")->where("id={$object_id}")->setInc('sale_nums');

                //给付费内容作者增加余额
                $apply_info=Db::name("paidprogram_apply")->where("uid={$touid}")->find();
                $percent=$apply_info['percent'];

                $balance=$orderinfo['money'];

                if($percent>0){
                    $balance=$balance*(100-$percent)/100;
                    $balance=round($balance,2);
                }

                //给发布者增加余额
                setUserBalance($touid,1,$balance);

                $data1=array(
                    
                    'uid'=>$touid,
                    'touid'=>$uid,
                    'balance'=>$balance,
                    'type'=>1,
                    'action'=>8, //付费内容收入
                    'orderid'=>$id,
                    'addtime'=>$now
                );

                addBalanceRecord($data1);


				$action="确认支付付费内容：{$id}";
				setAdminLog($action);

            }else{
                $this->error('数据传入失败！');
            }
        }else{
            $this->error('数据传入失败！');
        }
    }

    //导出订单记录
    function export(){

        $data = $this->request->param();
        $map=[];
        
        $status=isset($data['status']) ? $data['status']: '';
        if($status!=''){
            $map[]=['status','=',$status];
        }

        $type=isset($data['type']) ? $data['type']: '';
        if($type!=''){
            $map[]=['type','=',$type];
        }
        
        $start_time=isset($data['start_time']) ? $data['start_time']: '';
        $end_time=isset($data['end_time']) ? $data['end_time']: '';
        
        if($start_time!=""){
           $map[]=['addtime','>=',strtotime($start_time)];
        }

        if($end_time!=""){
           $map[]=['addtime','<=',strtotime($end_time) + 60*60*24];
        }
        
        $uid=isset($data['uid']) ? $data['uid']: '';
        if($uid!=''){
            $lianguid=getLianguser($uid);
            if($lianguid){
                $map[]=['uid',['=',$uid],['in',$lianguid],'or'];
            }else{
                $map[]=['uid','=',$uid];
            }
        }
        
        $keyword=isset($data['keyword']) ? $data['keyword']: '';
        if($keyword!=''){
            $map[]=['orderno|trade_no','like',"%".$keyword."%"];
        }
        
        $xlsName  = "付费内容订单";
        
        $xlsData=DB::name("paidprogram_order")
            ->where($map)
            ->order('id desc')
            ->select()
            ->toArray();

        foreach ($xlsData as $k => $v){

            $userinfo=getUserInfo($v['uid']);
            $touserinfo=getUserInfo($v['touid']);
            $xlsData[$k]['user_nicename']= $userinfo['user_nicename']."(".$v['uid'].")";
            $xlsData[$k]['touser_nicename']= $touserinfo['user_nicename']."(".$v['touid'].")";
            $xlsData[$k]['addtime']=date("Y-m-d H:i:s",$v['addtime']); 
            $xlsData[$k]['edittime']=$v['edittime']>0?date("Y-m-d H:i:s",$v['edittime']):''; 
            $xlsData[$k]['status']=$this->getOrderStatus($v['status']);
            $xlsData[$k]['type']=$this->getOrderType($v['type']);
            $xlsData[$k]['object_name']=Db::name("paidprogram")->where("id={$v['object_id']}")->value("title");
        }

        $action="导出付费内容订单列表：".DB::name("paidprogram_order")->getLastSql();
        setAdminLog($action);
        $cellName = array('A','B','C','D','E','F','G','H','I','J','K');
        $xlsCell  = array(
            array('id','序号'),
            array('user_nicename','购买用户'),
            array('touser_nicename','发布用户'),
            array('object_name','付费内容标题'),
            array('type','支付方式'),
            array('money','金额'),
            array('orderno','订单编号'),
            array('trade_no','第三方支付订单号'),
            array('status','订单状态'),
            array('addtime','提交时间'),
            array('edittime','处理时间'),
        );
        exportExcel($xlsName,$xlsCell,$xlsData,$cellName);
    }
   
}
