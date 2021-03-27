<?php

/**
 * 退款申请列表
 */
namespace app\admin\controller;

use cmf\controller\AdminBaseController;
use think\Db;

class RefundlistController extends AdminbaseController {
    protected function getType($k=''){
        $type=array(
            '0'=>'仅退款',
            '1'=>'退货退款',
        );
        if($k==''){
            return $type;
        }
        return isset($type[$k])?$type[$k]:'';
    }

    protected function getShopResult($k=''){
        $result=array(
            '-1'=>'拒绝',
            '0'=>'处理中',
            '1'=>'同意',
        );
        if($k==''){
            return $result;
        }
        return isset($result[$k])?$result[$k]:'';
    }

    protected function getPlatformResult($k=''){
        $result=array(
            '-1'=>'拒绝',
            '0'=>'待处理',
            '1'=>'同意',
        );
        if($k==''){
            return $result;
        }
        return isset($result[$k])?$result[$k]:'';
    }

    protected function getStatus($k=''){
        $status=array(
            '-1'=>'买家已取消',
            '0'=>'处理中',
            '1'=>'已完成',
        );
        if($k==''){
            return $status;
        }
        return isset($status[$k])?$status[$k]:'';
    }
    
    /*退款申请列表*/
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

        $buyer_uid=isset($data['buyer_uid']) ? $data['buyer_uid']: '';

        if($buyer_uid!=''){
            $lianguid=getLianguser($buyer_uid);
            if($lianguid){
                $map[]=['uid',['=',$buyer_uid],['in',$lianguid],'or'];
            }else{
                $map[]=['uid','=',$buyer_uid];
            }
        }

        $seller_uid=isset($data['seller_uid']) ? $data['seller_uid']: '';
        
        if($seller_uid!=''){
            $lianguid=getLianguser($seller_uid);
            if($lianguid){
                $map[]=['shop_uid',['=',$seller_uid],['in',$lianguid],'or'];
            }else{
                $map[]=['shop_uid','=',$seller_uid];
            }
        }    

        $lists = Db::name("shop_order_refund")
                ->where($map)
                ->order("addtime DESC")
                ->paginate(20);
        
        $lists->each(function($v,$k){
            $v['buyer_info']=getUserInfo($v['uid']);
            $v['seller_info']=getUserInfo($v['shop_uid']);
            $v['platform_interpose_thumb']=get_upload_path($v['platform_interpose_thumb']);
            $orderinfo=getShopOrderInfo(['id'=>$v['orderid']],'orderno');
            $v['orderno']=$orderinfo['orderno'];
            return $v;           
        });

        $lists->appends($data);
        $page = $lists->render();

        $this->assign('lists', $lists);

        $this->assign("page", $page);
        
        $this->assign("status", $this->getStatus());
        $this->assign("type", $this->getType());
        $this->assign("shop_result", $this->getShopResult());
        $this->assign("platform_result", $this->getPlatformResult());
        
        return $this->fetch();
    }
    


    /*退款编辑*/
    function edit(){
        
        $id   = $this->request->param('id', 0, 'intval');
        
        $data=Db::name('shop_order_refund')
            ->where("id={$id}")
            ->find();
        if(!$data){
            $this->error("信息错误");
        }

        $orderinfo=getShopOrderInfo(['id'=>$data['orderid']],'orderno,phone');
        $data['orderno']=$orderinfo['orderno'];
        $data['phone']=$orderinfo['phone'];
        $data['buyer_info']=getUserInfo($data['uid']);
        $data['seller_info']=getUserInfo($data['shop_uid']);
        $data['platform_interpose_thumb']=get_upload_path($data['platform_interpose_thumb']);

        //获取平台的处理意见
        $platform_info=Db::name("shop_order_refund_list")->where("orderid={$data['orderid']} and type=3")->find();
        $data['platform_handle_desc']=isset($platform_info['handle_desc'])?$platform_info['handle_desc']:'';
        
        $this->assign("status", $this->getStatus());
        $this->assign("type", $this->getType());
        $this->assign("shop_result", $this->getShopResult());
        $this->assign("platform_result", $this->getPlatformResult());
        $this->assign('data', $data);
        return $this->fetch();
    }

    /*退款编辑提交*/
    function edit_post(){
        if ($this->request->isPost()){
            
            $data = $this->request->param();
            
            $platform_result=$data['platform_result'];
            $id=$data['id'];
            $desc=trim($data['desc']);
            $orderid=$data['orderid'];

            //判断订单是否存在
            $order_info=getShopOrderInfo(['id'=>$orderid],"*");

            if(!$order_info){
                $this->error("订单不存在");
            }

            $status=$order_info['status'];

            if($status!=5){
                $this->error("订单未申请退款");
            }

            $refund_info=getShopOrderRefundInfo(['orderid'=>$orderid]);

            $refund_status=$refund_info['status'];

            if($refund_status==-1){
                $this->error("买家已取消退款申请");
            }

            if($refund_status==1){ //已经完成
                $this->error("退款已处理完成");
            }

            if($platform_result==""){
                $this->error("请选择处理结果");
            }

            if($desc){
                if(mb_strlen($desc)>300){
                    $this->error("处理意见在300字以内");
                }
            }

            $now=time();

            if(!$platform_result){
            	$platform_result=-1;
            }

            $data['platform_process_time']=$now;
            $data['platform_result']=$platform_result;
            $data['admin']=cmf_get_current_admin_id();
            $data['ip']= ip2long($_SERVER["REMOTE_ADDR"]) ;
            $data['status']=1;

            unset($data['orderid']);
            unset($data['desc']);
            
            $res = DB::name('shop_order_refund')->update($data);

            if($res===false){
                $this->error("修改失败！");
            }

            
            if($platform_result==1){ //平台同意退款

                //将钱退给买家
                setUserBalance($order_info['uid'],1,$order_info['total']);

                //添加余额操作记录
                $data1=array(
                    'uid'=>$order_info['uid'],
                    'touid'=>$order_info['shop_uid'],
                    'balance'=>$order_info['total'],
                    'type'=>1,
                    'action'=>6, //买家发起退款，平台介入后同意
                    'orderid'=>$orderid,
                    'addtime'=>$now

                );

                addBalanceRecord($data1);

                //处理订单状态
                $data2=array(
                    'refund_status'=>1,
                    'refund_endtime'=>$now,
                );

                changeShopOrderStatus($order_info['uid'],$orderid,$data2);

                //减去商品的销量
                changeShopGoodsSaleNums($order_info['goodsid'],0,$order_info['nums']);
                //减去店铺销量
                changeShopSaleNums($order_info['shop_uid'],0,$order_info['nums']);

                //给买家发送消息
                $title="你的商品“".$order_info['goods_name']."”发起的退款,平台已同意退款";

                //写入订单消息列表
                $data3=array(
                    'title'=>$title,
                    'orderid'=>$orderid,
                    'uid'=>$order_info['uid'],
                    'addtime'=>$now,
                    'type'=>'0'
                );

                addShopGoodsOrderMessage($data3);
                //发送极光IM
                jMessageIM($title,$order_info['uid'],'goodsorder_admin');


                //给卖家发送消息
                $title1="买家商品“".$order_info['goods_name']."”发起的退款,平台已同意退款";

                $data4=array(
                    'title'=>$title1,
                    'orderid'=>$orderid,
                    'uid'=>$order_info['shop_uid'],
                    'addtime'=>$now,
                    'type'=>'1'
                );

                addShopGoodsOrderMessage($data4);
                //发送极光IM
                jMessageIM($title,$order_info['shop_uid'],'goodsorder_admin');


                //退款协商记录
                $refund_history_data=array(
                    'orderid'=>$orderid,
                    'type'=>3, //平台
                    'addtime'=>$now,
                    'desc'=>'平台同意退款',
                    'handle_desc'=>$desc
                );
				
				
				$action='编辑退款列表ID: '.$orderid.'--平台同意';
				setAdminLog($action);

            }else{ //平台拒绝

                //修改订单状态
                $data1=array(
                    'refund_status'=>-1, //退款失败
                    'refund_endtime'=>$now,
                );

                if($order_info['receive_time']>0){

                    $data1['status']=3; //待评价
                    
                }else{

                    if($order_info['shipment_time']>0){
                        $data1['status']=2; //待收货
                    }else{
                        $data1['status']=1; //待发货
                    } 
                }

                changeShopOrderStatus($order_info['uid'],$orderid,$data1);


                //给买家发送消息
                
                $title="你的商品“".$order_info['goods_name']."”发起的退款,平台已拒绝";

                //写入订单消息列表
                $data2=array(
                    'title'=>$title,
                    'orderid'=>$orderid,
                    'uid'=>$order_info['uid'],
                    'addtime'=>$now,
                    'type'=>'0'
                );

                addShopGoodsOrderMessage($data2);
                //发送极光IM
                jMessageIM($title,$order_info['uid'],'goodsorder_admin');

                //给卖家发送消息
                $title1="买家商品“".$order_info['goods_name']."”发起的退款,平台已拒绝";

                $data3=array(
                    'title'=>$title1,
                    'orderid'=>$orderid,
                    'uid'=>$order_info['shop_uid'],
                    'addtime'=>$now,
                    'type'=>'1'
                );

                addShopGoodsOrderMessage($data3);
                //发送极光IM
                jMessageIM($title,$order_info['shop_uid'],'goodsorder_admin');


                //退款协商记录
                $refund_history_data=array(
                    'orderid'=>$orderid,
                    'type'=>3,
                    'addtime'=>$now,
                    'desc'=>'平台拒绝退款',
                    'handle_desc'=>$desc
                );
				
				$action='编辑退款列表ID: '.$orderid.'--平台拒绝';
				setAdminLog($action);

            }

            //写入退款协商记录
            setGoodsOrderRefundList($refund_history_data);
            
            $this->success("退款处理成功！");
            
        }

    }


}
