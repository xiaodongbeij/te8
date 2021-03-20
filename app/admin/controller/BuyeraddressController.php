<?php

/**
 * 买家收货地址
 */
namespace app\admin\controller;

use cmf\controller\AdminBaseController;
use think\Db;

class BuyeraddressController extends AdminbaseController {

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
        
        $keyword=isset($data['keyword']) ? $data['keyword']: '';
        if($keyword!=''){
            $map[]=['name','like','%'.$keyword.'%'];
        }
			

    	$lists = Db::name("shop_address")
                ->where($map)
                ->order("id DESC")
                ->paginate(20);
        
        $lists->each(function($v,$k){
			$v['userinfo']=getUserInfo($v['uid']);
            return $v;           
        });
        
        $lists->appends($data);
        $page = $lists->render();


    	$this->assign('lists', $lists);

    	$this->assign("page", $page);
    	
    	return $this->fetch();
    }
    
    
		
    function del(){
        $id = $this->request->param('id', 0, 'intval');
        $info=Db::name("shop_address")->where("id={$id}")->find();

        if(!$info){
        	$this->error("地址不存在！");
        }

        $uid=$info['uid'];
        
        $rs = DB::name('shop_address')->where("id={$id}")->delete();
        if(!$rs){
            $this->error("删除失败！");
        }

        $info1=Db::name("shop_address")->where("uid={$uid}")->order("addtime asc")->find();

        if($info1){
        	Db::name("shop_address")->where("id={$info1['id']}")->update(array('is_default'=>1));
        }
		
		
		$action="删除收货地址管理：{$id}";
        setAdminLog($action);
        
        $this->success("删除成功！",url("Buyeraddress/index"));
    }
    
}
