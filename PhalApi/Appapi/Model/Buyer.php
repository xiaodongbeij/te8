<?php

class Model_Buyer extends PhalApi_Model_NotORM {

    /*获取买家首页信息*/
    public function getHome($uid){

        $wait_payment='0';
        $wait_shipment='0';
        $wait_receive='0';
        $wait_evaluate='0';
        $refund='0';

        $base_where="isdel !=-1 and isdel !=1";

        $payment_where="uid={$uid} and status=0 and ".$base_where;
        $shipment_where="uid={$uid} and status=1 and ".$base_where;
        $receive_where="uid={$uid} and status=2 and ".$base_where;
        $evaluate_where="uid={$uid} and status=3 and ".$base_where;
        $refund_where="uid={$uid} and status=5 and refund_endtime=0 and ".$base_where; //退款处理中

        $wait_payment=getOrderNums($payment_where);
        $wait_shipment=getOrderNums($shipment_where);
        $wait_receive=getOrderNums($receive_where);
        $wait_evaluate=getOrderNums($evaluate_where);
        $refund=getOrderNums($refund_where);


        $res=array(
            'wait_payment'=>$wait_payment, //待付款订单数
            'wait_shipment'=>$wait_shipment, //待发货订单数
            'wait_receive'=>$wait_receive, //待收货订单数
            'wait_evaluate'=>$wait_evaluate, //待评价订单数
            'refund'=>$refund, //退款订单数
        );

        return $res;
    }
    
	/* 添加收货地址 */
	public function addAddress($data) {
        
        $uid=$data['uid'];
        $isdefault=$data['is_default'];

        //判断之前是否添加过收货地址
        $count=DI()->notorm->shop_address->where("uid=?",$uid)->count();

        if(!$count){
          $data['is_default']=1;  
        }
        
        //添加地址
        $result=DI()->notorm->shop_address->insert($data);

        if(!$result){
            return 1001;
        }

        if($isdefault&&$count){
            $id=$result['id'];
            DI()->notorm->shop_address->where("uid=? and id !=? and is_default=1",$uid,$id)->update(array("is_default"=>0));
        }

        return 1;
	}		
    
    

	/* 收货地址修改 */
	public function editAddress($addressid,$data) {
       
        $isdefault=$data['is_default'];
        $uid=$data['uid'];

        $result=DI()->notorm->shop_address->where("id=?",$addressid)->update($data);

        if(!$result){
            return 1001;
        }


        if($isdefault){

            $res=DI()->notorm->shop_address->where("uid=? and id !=? and is_default=1",$uid,$addressid)->update(array("is_default"=>0));

        }

        return 1;
        
	}

    /* 收货地址列表 */
    public function addressList($uid){
        $list=DI()->notorm->shop_address
                ->select("id,name,country,area,address,country_code,phone,is_default")
                ->where('uid = ? ',$uid)
                ->order('is_default desc,addtime desc')
                ->fetchAll();
        return $list;
    }

    //获取用户的收货地址
    public function getAddress($uid,$addressid){
        $address_info=DI()->notorm->shop_address
            ->where("uid=? and id=?",$uid,$addressid)
            ->fetchOne();
        return $address_info;
    }		

    public function delAddress($uid,$addressid){

        //判断用户的收货地址是否超过2个
        $count=DI()->notorm->shop_address->where("uid=?",$uid)->count();
        if($count<=1){
            return 1002; //收货地址不能为空，无法删除
        }

        $address=DI()->notorm->shop_address->where("id=? and uid=?",$addressid,$uid)->fetchOne();
        $result=DI()->notorm->shop_address->where("id=? and uid=?",$addressid,$uid)->delete();
        if(!$result){
            return 1001;
        }

        $isdefault=$address['is_default'];

        if($isdefault){
           $last_address=DI()->notorm->shop_address->where("uid=?",$uid)->order("addtime desc")->fetchOne();
           if($last_address){
                $last_id=$last_address['id'];

                //更新default信息
                DI()->notorm->shop_address->where("id=?",$last_id)->update(array("is_default"=>1));
           }
        }

        return 1;
    }

    // 添加商品浏览记录
    public function addGoodsVisitRecord($data){
        $res=DI()->notorm->user_goods_visit->insert($data);

        //增加商品的访问量
        DI()->notorm->shop_goods->where("id=?",$data['goodsid'])->update(array('hits'=>new NotORM_Literal("hits+1")));
        
        return $res;
    }

    //删除商品浏览记录
    public function delGoodsVisitRecord($uid,$record_arr){
        $res=DI()->notorm->user_goods_visit->where('id',$record_arr)->where('uid=?',$uid)->delete();
        return $res;
    }

    public function getGoodsVisitRecord($uid,$p){
        if($p<1){
            $p=1;
        }
        $nums=20;
        $start=($p-1)*$nums;


        $list=DI()->notorm->user_goods_visit
                ->where("uid=?",$uid)
                ->order("addtime desc")
                ->limit($start,$nums)
                ->fetchAll();

        return $list;

    }

    //创建商品订单
    public function createGoodsOrder($order_data){
        $result=DI()->notorm->shop_order->insert($order_data);

        return $result;
    }


    //用户使用余额支付
    public function goodsBalancePay($uid,$orderid){

        //获取订单信息
        $where=array(
            'id'=>$orderid,
            'uid'=>$uid

        );
        $order_info=getShopOrderInfo($where);


        //扣除用户余额
        $res=setUserBalance($uid,0,$order_info['total']);


        if(!$res){
            return 0;
        }

        $now=time();

        //更改订单信息
        $data=array(
            'status'=>1,
            'type'=>3,
            'paytime'=>$now
        );
        $status=changeShopOrderStatus($uid,$orderid,$data);

        if(!$status){ //订单状态修改失败

            //返回用户余额
            setUserBalance($uid,1,$order_info['total']);
            return 0;
        }

        //增加商品销量
        changeShopGoodsSaleNums($order_info['goodsid'],1,$order_info['nums']);

        //增加店铺销量
        changeShopSaleNums($order_info['shop_uid'],1,$order_info['nums']);

        //写入订单消息列表

        $title="你的商品“".$order_info['goods_name']."”收到一笔新订单,订单编号:".$order_info['orderno'];

        $data1=array(
            'uid'=>$order_info['shop_uid'],
            'orderid'=>$orderid,
            'title'=>$title,
            'addtime'=>$now,
            'type'=>'1'

        );

        addShopGoodsOrderMessage($data1);
        //发送极光IM
        jMessageIM($title,$order_info['shop_uid'],'goodsorder_admin');

        return 1;

    }  

    // 根据订单类型获取订单列表
    public function getGoodsOrderList($uid,$type,$p){

        //订单自动处理
        goodsOrderAutoProcess($uid,array('uid'=>$uid));

        if($p<1){
            $p=1;
        }

        $pnums=50;
        $start=($p-1)*$pnums;
        $now=time();

        $where=array(
            'uid'=>$uid
        );

        switch ($type) {
            case 'wait_payment': //待付款
                $where['status']=0;
                break;

            case 'wait_shipment': //待发货
                $where['status']=1;
                break;

            case 'wait_receive': //待收货
                $where['status']=2;
                break;

            case 'wait_evaluate': //待评价
                $where['status']=3;
                break;

            case 'refund': //退款
                 $where['status']=5;
                break;
            
            
        }


        $list=DI()->notorm->shop_order
            ->select("id,uid,shop_uid,goodsid,goods_name,spec_name,spec_thumb,nums,price,total,status,is_append_evaluate,refund_status,addtime,paytime")
            ->where($where)
            ->where("isdel !=-1 and isdel !=1") //排除买家删除的 和买家 卖家都删除的
            ->order("addtime desc")
            ->limit($start,$pnums)
            ->fetchAll();

        $shopEffectiveTime=getShopEffectiveTime();

        $model_shop=new Model_Shop();

        foreach ($list as $k => $v) {

            switch ($v['status']) {
                case '-1':
                    $list[$k]['status_name']='交易已关闭';
                    break;

                case '0':
                    $end=$shopEffectiveTime['shop_payment_time']*60+$v['addtime'];
                    $cha=$end-$now;
                    $list[$k]['status_name']='等待买家付款'.getSeconds($cha,1);
                    break;

                case '1':
                    
                    $list[$k]['status_name']='买家已付款';
                    break;

                case '2':
                    
                    $list[$k]['status_name']='卖家已发货';
                    break;

                case '3':
                    
                    $list[$k]['status_name']='已收货';
                    break;

                case '4':
                    
                    $list[$k]['status_name']='已评价';
                    break;

                case '5':

                    if($v['refund_status']==0){
                        $list[$k]['status_name']='申请退款中';
                    }else if($v['refund_status']==-1){
                        $list[$k]['status_name']='退款失败';
                    }else{
                        $list[$k]['status_name']='退款成功';
                    }
                    
                    break;
                
               
            }


            $list[$k]['spec_thumb']=get_upload_path($v['spec_thumb']);

            $shop_info=$model_shop->getShop($v['shop_uid']);
            $list[$k]['shop_name']=$shop_info['name']; //android使用
            $list[$k]['shop_info']=$shop_info;


        }

        return $list;

    }

    //商品订单评价
    public function evaluateGoodsOrder($data){
        $res=DI()->notorm->shop_order_comments->insert($data);
        if(!$res){
            return 0;
        }

        //更改订单状态
        $uid=$data['uid'];
        $orderid=$data['orderid'];
        $data1=array(
            'status'=>4,
            'evaluate_time'=>time()
        );
        changeShopOrderStatus($uid,$orderid,$data1);

        //更新商品总评分
        $shop_uid=$data['shop_uid'];
        $quality_points=$data['quality_points'];
        $service_points=$data['service_points'];
        $express_points=$data['express_points'];

        if($quality_points>0||$service_points>0||$express_points>0){

            DI()->notorm->shop_points
                ->where("shop_uid=?",$shop_uid)
                ->update(
                    array(
                        'evaluate_total'=>new NotORM_Literal("evaluate_total+1"),
                        'quality_points_total'=>new NotORM_Literal("quality_points_total+{$quality_points}"),
                        'service_points_total'=>new NotORM_Literal("service_points_total+{$service_points}"),
                        'express_points_total'=>new NotORM_Literal("express_points_total+{$express_points}"),
                    )
                );

        }

        

        //计算店铺的三项分数
        $shop_points_info=DI()->notorm->shop_points->where("shop_uid=?",$shop_uid)->fetchOne();

        $evaluate_total=$shop_points_info['evaluate_total'];  
        $quality_points_total=$shop_points_info['quality_points_total'];
        $service_points_total=$shop_points_info['service_points_total'];
        $express_points_total=$shop_points_info['express_points_total'];

        if($evaluate_total){


           $quality_points=number_format($quality_points_total/$evaluate_total,'1'); //商品质量平均分
           $service_points=number_format($service_points_total/$evaluate_total,'1'); //服务质量平均分
           $express_points=number_format($express_points_total/$evaluate_total,'1'); //物流速度平均分

           DI()->notorm->shop_apply
                    ->where("uid=?",$shop_uid)
                    ->update(
                        array(

                            'quality_points'=>$quality_points,
                            'service_points'=>$service_points,
                            'express_points'=>$express_points
                        )
                    );
        }

        return 1;
    }

    //订单追评
    public function appendEvaluateGoodsOrder($data){

        $res=DI()->notorm->shop_order_comments->insert($data);
        if(!$res){
            return 0;
        }

        //更改订单追评状态
        $uid=$data['uid'];
        $orderid=$data['orderid'];
        $data1=array(
            'is_append_evaluate'=>0
        );
        changeShopOrderStatus($uid,$orderid,$data1);

        return 1;
    }

    //获取退货原因列表
    public function getRefundReason(){
        $res=DI()->notorm->shop_refund_reason
            ->select("id,name")
            ->where('status=1')
            ->order("list_order asc,id desc")
            ->fetchAll();

        return $res;
    }

    //申请退款
    public function applyRefundGoodsOrder($data){
        $res=DI()->notorm->shop_order_refund->insert($data);
        return $res;
    }

    //获取申请平台介入的原因列表
    public function getPlatformReasonList(){
        $list=DI()->notorm->shop_platform_reason
            ->select("id,name")
            ->where('status=1')
            ->order("list_order asc,id desc")
            ->fetchAll();
        return $list;
    }

    //获取买家退款列表
    public function getRefundList($uid,$p){
        if($p<1){
            $p=1;
        }

        $pnums=50;
        $start=($p-1)*$pnums;

        $list=DI()->notorm->user_balance_record
                ->where("uid=? and type=1 and action in (3,4,5,6)",$uid)
                ->limit($start,$pnums)
                ->fetchAll();

        foreach ($list as $k => $v) {
            $list[$k]['addtime']=date("Y-m-d H:i",$v['addtime']);
            $list[$k]['balance']='￥'.$v['balance'];
            $list[$k]['result']='已到账';

            unset($list[$k]['orderid']);
        }

        return $list;
    }

    //获取在平台未介入 且 卖家拒绝退款时的最新拒绝原因和理由
    public function getRefundSellerRefuse($orderid){

        $res=array(
            'shop_refuse_reason'=>'',
            'shop_handle_desc'=>''
        );

        $where['orderid']=$orderid;
        $refund_info=getShopOrderRefundInfo($where);


        if($refund_info['shop_result']==-1){
            $info=DI()->notorm->shop_order_refund_list
                ->where("orderid=? and type=2",$orderid)
                ->order("addtime desc")
                ->fetchOne();

            $res['shop_refuse_reason']=$info['refuse_reason'];
            $res['shop_handle_desc']=$info['handle_desc'];
        }

        return $res;

        


    }
    
}
