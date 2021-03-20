<?php
/**
 * 退款协商历史
 */
namespace app\appapi\controller;

use cmf\controller\HomeBaseController;
use think\Db;

class GoodsorderrefundController extends HomebaseController{


    function index(){

        
        $data = $this->request->param();
        $uid=isset($data['uid']) ? $data['uid']: '';
        $token=isset($data['token']) ? $data['token']: '';
        $orderid=isset($data['orderid']) ? $data['orderid']: '';
        $user_type=isset($data['user_type']) ? $data['user_type']: ''; //用户身份 buyer 买家 seller 卖家 platform 平台
        
        $uid=(int)checkNull($uid);
        $token=checkNull($token);
        $orderid=checkNull($orderid);

        if($user_type!='platform'){
            if( !$uid || !$token || checkToken($uid,$token)==700 ){
                $reason='您的登陆状态失效，请重新登陆！';
                $this->assign('reason', $reason);
                return $this->fetch(':error');
            }
        } 
        

        if(!$orderid || !$user_type ||!in_array($user_type, ['buyer','seller','platform'])){
            $reason='参数错误';
            $this->assign('reason', $reason);
            return $this->fetch(':error');
        }

        $where=[];
        if($user_type=='buyer'){
            $where=array( 
                'id'=>$orderid,
                'uid'=>$uid,
            );

            $where1=array(
                'uid'=>$uid,
                'orderid'=>$orderid
                
            );
        }else if($user_type=='sellers'){
            $where=array( 
                'id'=>$orderid,
                'shop_uid'=>$uid,
            );

            $where1=array(
                'orderid'=>$orderid,
                'shop_uid'=>$uid,
                
            );
        }else{
            $where=array(
                'id'=>$orderid
            );

            $where1=array(
                'orderid'=>$orderid
            );
        }

        

        $orderinfo=getShopOrderInfo($where,"total");

        if(!$orderinfo){
            $reason='订单不存在';
            $this->assign('reason', $reason);
            return $this->fetch(':error');
        }


        $refund_info=getShopOrderRefundInfo($where1);

        if(!$refund_info){
            $reason='订单没有发起退款申请';
            $this->assign('reason', $reason);
            return $this->fetch(':error');
        }

        //查询退款协商历史
        $refund_list=getShopOrderRefundList(['orderid'=>$orderid]);

        $refund_info['total']=$orderinfo['total'];

        $this->assign("refund_info",$refund_info);
        $this->assign("refund_list",$refund_list); //协商历史
        return $this->fetch();
    }
    
    

    

    
}