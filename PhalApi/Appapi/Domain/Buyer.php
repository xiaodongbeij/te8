<?php

class Domain_Buyer {

	public function getHome($uid) {
		$rs = array();

		$model = new Model_Buyer();
		$rs = $model->getHome($uid);

		return $rs;
	}

	public function addAddress($data) {
		$rs = array();

		$model = new Model_Buyer();
		$rs = $model->addAddress($data);

		return $rs;
	}

	public function editAddress($addressid,$data) {
		$rs = array();

		$model = new Model_Buyer();
		$rs = $model->editAddress($addressid,$data);

		return $rs;
	}

	public function addressList($uid){
		$rs = array();

		$model = new Model_Buyer();
		$rs = $model->addressList($uid);

		return $rs;
	}

	public function getAddress($uid,$addressid){
		$rs = array();

		$model = new Model_Buyer();
		$rs = $model->getAddress($uid,$addressid);

		return $rs;
	}

	public function delAddress($uid,$addressid){
		$rs = array();

		$model = new Model_Buyer();
		$rs = $model->delAddress($uid,$addressid);

		return $rs;
	}

	public function addGoodsVisitRecord($uid,$goodsid){

		$rs = array('code'=>0,'msg'=>'商品浏览记录添加成功','info'=>array());
		$model = new Model_Buyer();
		$data=array(
			'uid'=>$uid,
			'goodsid'=>$goodsid,
			'addtime'=>time(),
			'time_format'=>date('Y-m-d')
		);
		$res = $model->addGoodsVisitRecord($data);


		if(!$res){
			$rs['code']=1001;
			$rs['msg']='商品浏览记录添加失败';
			return $rs;
		}

        $now=time();
        $end=date("Y-m-d 23:59:59",$now);
        $cha=$end-$now;

		setcaches($uid.'_'.$goodsid,'1',$cha); //当晚失效

		return $rs;

	}

	public function delGoodsVisitRecord($uid,$record_arr){
		$rs = array();

		$model = new Model_Buyer();
		$rs = $model->delGoodsVisitRecord($uid,$record_arr);

		return $rs;
	}
	
	public function getGoodsVisitRecord($uid,$p){
		$rs = array();

		$model = new Model_Buyer();
		$list = $model->getGoodsVisitRecord($uid,$p);

		if($list){
            $time_formats=array_column($list,'time_format');
            $time_formats=array_unique($time_formats);

            $new_list=array();
            $model_shop=new Model_Shop();

            

            foreach ($time_formats as $k => $v) {

            	$arr=[];
            	$arr['date']=$v;

                foreach ($list as $k1 => $v1) {

                	$where['id']=$v1['goodsid'];
                	$goodsinfo=$model_shop->getGoods($where);
                	$goodsinfo=handleGoods($goodsinfo);

                	$v1['goods_name']=$goodsinfo['name'];
                	$v1['goods_thumb']=$goodsinfo['thumbs_format'][0];
                	
                    $v1['addtime']=date("Y-m-d H:i:s",$v1['addtime']);
                    $v1['goods_status']=$goodsinfo['status'];
                    $v1['type']=$goodsinfo['type'];
                    $v1['href']=$goodsinfo['href'];
                    $v1['original_price']="￥".$goodsinfo['original_price'];

                    if($goodsinfo['type']==1){ //外链商品
                        $v1['goods_price']="￥".$goodsinfo['present_price'];
                    }else{
                        $v1['goods_price']="￥".$goodsinfo['specs_format'][0]['price'];
                    }

                    if($v1['time_format']==$v){
                    	$arr['list'][]=$v1;
                    	unset($v1['time_format']);
                        unset($list[$k1]);
                    }

                   
                }

                $new_list[]=$arr;
            }

            return $new_list;

        }

		return $list;
	}


	//生成商品订单
	public function createGoodsOrder($order_data){
		$rs=array();
		$model=new Model_Buyer();
		$rs=$model->createGoodsOrder($order_data);

		return $rs;
	}

	//用户余额支付商品订单
	public function goodsBalancePay($uid,$orderid){
		$rs=array();
		$model=new Model_Buyer();
		$rs=$model->goodsBalancePay($uid,$orderid);
		return $rs;
	}

	//根据订单类型获取订单列表
	public function getGoodsOrderList($uid,$type,$p){
		$rs=array();
		$model=new Model_Buyer();
		$rs=$model->getGoodsOrderList($uid,$type,$p);
		return $rs;
	}

	//商品评价
	public function evaluateGoodsOrder($data){
		$rs=array();
		$model=new Model_Buyer();
		$rs=$model->evaluateGoodsOrder($data);
		return $rs;
	}

	//商品追评
	public function appendEvaluateGoodsOrder($data){
		$rs=array();
		$model=new Model_Buyer();
		$rs=$model->appendEvaluateGoodsOrder($data);
		return $rs;
	}

	//获取退款原因
	public function getRefundReason(){
		$list=array();

		$key="getRefundReason";
		$list=getcaches($key);
		$list=[];

		if(!$list){
			$model=new Model_Buyer();
			$list=$model->getRefundReason();

			if($list){
				setcaches($key,$list);
			}

		}

		
		return $list;
	}



	//买家申请订单退款
	public function applyRefundGoodsOrder($uid,$orderid,$data){
		$rs=array('code'=>0,'msg'=>'申请退款成功','info'=>array());

		$now=time();
		$where=array(
            'id'=>$orderid,
            'uid'=>$uid
        );
        $order_info=getShopOrderInfo($where);
        if(!$order_info){
            $rs['code']=1001;
            $rs['msg']='订单不存在';
            return $rs;
        }

        $status=$order_info['status'];
        $effective_time=getShopEffectiveTime();

        switch ($status) {
            case '-1':
                $rs['code']=1001;
                $rs['msg']='订单已关闭';
                return $rs;
                break;
            
            case '0':
                $rs['code']=1001;
                $rs['msg']='该订单未付款';
                return $rs;
                break;

            case '1':
                if($order_info['refund_status']!=0){ //已经发起过退款
                    $rs['code']=1001;
                    $rs['msg']='该订单已发起过退款';
                    return $rs;
                }

                break;

            case '2':
                if($order_info['refund_status']!=0){ //已经发起过退款
                    $rs['code']=1001;
                    $rs['msg']='该订单已发起过退款';
                    return $rs;
                }
                break;

            case '3': //已收货

                if($order_info['refund_status']!=0){ //已经发起过退款
                    $rs['code']=1001;
                    $rs['msg']='该订单已发起过退款';
                    return $rs;
                }

                //判断是否超过后台设置的退货退款时间
                $refund_end=$order_info['receive_time']+$effective_time['shop_receive_refund_time']*24*60*60;

                if($refund_end<$now){
                    $rs['code']=1001;
                    $rs['msg']='确认收货已超过'.$effective_time['shop_receive_refund_time'].'天,无法发起退款';
                    return $rs;
                }
                
                
                break;

            case '4':
                $rs['code']=1001;
                $rs['msg']='订单已完成,无法发起退款';
                return $rs;
                break;

            case '5':


                //获取退款详情
                $where1=array(
                    'uid'=>$uid,
                    'orderid'=>$orderid

                );

                $refund_info=getShopOrderRefundInfo($where1);

                if($refund_info['status']==-1){ //买家已取消

                    $rs['code']=1001;
                    $rs['msg']='您已取消退款申请,不可重复发起';
                    return $rs;

                }elseif($refund_info['status']==0){ //处理中


                    if($refund_info['is_platform_interpose']){ //平台介入中
                        if($refund_info['platform_result']==-1){ //平台拒绝
                            $rs['code']=1001;
                            $rs['msg']='平台已拒绝';
                            return $rs;
                        }elseif($refund_info['platform_result']==0){ //平台处理中
                            $rs['code']=1001;
                            $rs['msg']='等待平台处理中';
                            return $rs;
                        }
                    }else{ //卖家处理

                        if($refund_info['shop_result']==0){ //卖家处理中

                            $rs['code']=1001;
                            $rs['msg']='等待卖家处理中';
                            return $rs;

                        }elseif($refund_info['shop_result']==-1){ //卖家已拒绝

                            if($refund_info['shop_process_num']<3){ //店铺拒绝次数少于3次
                                $rs['code']=1001;
                                $rs['msg']='请到退款详情页点击重新申请或平台介入';
                                return $rs;
                            }else{
                                $rs['code']=1001;
                                $rs['msg']='请到退款详情页点击平台介入';
                                return $rs;
                            }
                        }


                    }   

                    
                }elseif($refund_info['status']==1){
                    $rs['code']=1001;
                    $rs['msg']='退款申请已完成';
                    return $rs;
                }
                
                break;

        }

        if($order_info['status']==1){ //待发货订单，退款类型强制处理为仅退款
            $type=0;
        }

        $data1=array(
            'uid'=>$uid,
            'orderid'=>$orderid,
            'goodsid'=>$order_info['goodsid'],
            'shop_uid'=>$order_info['shop_uid'],
            'reason'=>$data['reason'],
            'content'=>$data['content'],
            'thumb'=>$data['thumb'],
            'type'=>$data['type'],
            'addtime'=>$now
        );

        $model=new Model_Buyer();
		$res=$model->applyRefundGoodsOrder($data1);

		if(!$res){
            $rs['code']=1002;
            $rs['msg']='申请退款失败,请重试';
            return $rs;
        }

        //更改订单状态
        $data1=array(
            'status'=>5,
            'refund_starttime'=>$now
        );

        changeShopOrderStatus($uid,$orderid,$data1);


        //写入订单消息列表

        $title="买家将“".$order_info['goods_name']."”商品发起了退款申请,退款原因:".$data['reason'].",退款理由:".$data['content'];

        $data2=array(
            'uid'=>$order_info['shop_uid'],
            'orderid'=>$orderid,
            'title'=>$title,
            'addtime'=>$now,
            'type'=>'1'

        );

        addShopGoodsOrderMessage($data2);

        //发送极光IM
        jMessageIM($title,$order_info['shop_uid'],'goodsorder_admin');

        return $rs;

	}

	//买家取消退款申请
	public function cancelRefundGoodsOrder($uid,$orderid){
		$rs=array('code'=>0,'msg'=>'订单退款取消成功','info'=>array());

		$now=time();

		$where=array(
            'id'=>$orderid,
            'uid'=>$uid
        );

        $order_info=getShopOrderInfo($where);
        if(!$order_info){
            $rs['code']=1001;
            $rs['msg']='订单不存在';
            return $rs;
        }

        $status=$order_info['status'];
        $refund_status=$order_info['refund_status'];

        if($refund_status==-2){
            $rs['code']=1001;
            $rs['msg']='订单已取消申请退款';
            return $rs;
        }

        if($status!=5){
            $rs['code']=1001;
            $rs['msg']='订单没有申请退款';
            return $rs;
        }

        if($refund_status==-1){
            $rs['code']=1001;
            $rs['msg']='订单退款已失败,不可取消';
            return $rs;
        }

        if($refund_status==1){
            $rs['code']=1001;
            $rs['msg']='订单退款已成功,不可取消';
            return $rs;
        }

         //更改订单状态
        $data=array(
            'refund_endtime'=>$now,
            'refund_status'=>-2
        );

        if($order_info['receive_time']>0){ //订单已收货
            $data['status']=3;
        }else{

            if($order_info['shipment_time']>0){ //订单已发货
                $data['status']=2;
            }else{
                $data['status']=1; //待发货
            }
        }

        $res=changeShopOrderStatus($uid,$orderid,$data);

        if(!$res){
            $rs['code']=1001;
            $rs['msg']='订单退款取消失败';
            return $rs;
        }

        //更改订单退款详情信息
        $where1=array(
            'uid'=>$uid,
            'orderid'=>$orderid

        );

        $data1=array(
            'status'=>-1
        );
        changeGoodsOrderRefund($where1,$data1);

        //添加订单处理记录
        $data2=array(
            'orderid'=>$orderid,
            'type'=>1, //处理方 1 买家 2 卖家 3 平台 4 系统
            'addtime'=>$now,
            'desc'=>'买家取消订单退款'
        );

        setGoodsOrderRefundList($data2);

        //写入订单消息列表

        $title="买家取消了“".$order_info['goods_name']."”商品的退款申请";

        $data3=array(
            'uid'=>$order_info['shop_uid'],
            'orderid'=>$orderid,
            'title'=>$title,
            'addtime'=>$now,
            'type'=>'1'

        );

        addShopGoodsOrderMessage($data3);

        //发送极光IM
        jMessageIM($title,$order_info['shop_uid'],'goodsorder_admin');

		return $rs;
	}

	//获取退款详情
	public function getGoodsOrderRefundInfo($uid,$orderid){

		$rs=array('code'=>0,'msg'=>'','info'=>array());

		$now=time();

		$where=array(
            'id'=>$orderid,
            'uid'=>$uid
        );

        $order_info=getShopOrderInfo($where);
        if(!$order_info){
            $rs['code']=1001;
            $rs['msg']='订单不存在';
            return $rs;
        }


        $status=$order_info['status'];
        $refund_status=$order_info['refund_status'];

        if(($status==1||$status==2||$status==3) && $refund_status==0){ //待发货 待收货 已收货的 可以申请退款
            $rs['code']=1001;
            $rs['msg']='订单未申请退款';
            return $rs;
        }

        if(($status==5)&& ($refund_status!=0 && $refund_status!=1)){
            $rs['code']=1001;
            $rs['msg']='订单未申请退款';
            return $rs;
        }

         //获取退款详情
        $where1=array(
            'uid'=>$uid,
            'orderid'=>$orderid

        );

        $refund_info=getShopOrderRefundInfo($where1);

        $refund_shop_result=$refund_info['shop_result'];
        $refund_platform_result=$refund_info['platform_result'];
        $is_platform_interpose=$refund_info['is_platform_interpose'];
        $shop_process_num=$refund_info['shop_process_num'];
        $status_name='';
        $status_time='';
        $status_desc='';
        $is_reapply='0'; //是否可重新申请
        $is_platform='0'; //平台是否可介入
        $shop_refuse_reason=''; //卖家拒绝原因
        $shop_handle_desc=''; //卖家处理备注

        $effective_time=getShopEffectiveTime();

        if($refund_info['status']==-1){ //买家取消

            $status_name='已取消退款申请';
            $status_time=date("Y-m-d H:i:s",$order_info['refund_endtime']);
            $status_desc='订单回归原状态';

        }elseif($refund_info['status']==0){ //处理中

            if($is_platform_interpose==0){  //平台未介入

                if($refund_shop_result==0){

                    $status_name='已提交退款申请,请耐心等待卖家处理';
                    $end=$refund_info['addtime']+$effective_time['shop_refund_finish_time']*24*60*60;
                    $cha=$end-$now;
                    $status_time='还剩'.getSeconds($cha);

                }elseif($refund_shop_result==-1){ //卖家拒绝

                    $status_name='卖家拒绝了您的退款申请';
                    $status_time='您可重新提交申请或申请平台介入';

                    if($shop_process_num>=3){
                    	$status_time='您可申请平台介入';
                    }

                    $is_platform='1';

                }

            }else{

                $status_name='已申请平台介入';
                $status_time='平台客服将尽快解决您的问题,请保持电话网络畅通';
            }
            
            

            if($shop_process_num<3 && $refund_info['shop_result']==-1){ //卖家拒绝低于三次 且卖家处理结果为拒绝
                $is_reapply='1';
            }

            if($is_platform_interpose){ //平台已介入
                $is_reapply='0';
            }   

        }else{ //处理完成

            if($is_platform_interpose){ //平台介入

                if($refund_platform_result==-1){ //拒绝
                    $status_name='平台拒绝了您的退款申请';
                    $status_time=date("Y-m-d H:i:s",$refund_info['platform_process_time']); //平台处理时间
                }elseif($refund_platform_result==1){
                    $status_name='平台同意了您的退款申请';
                    $status_time=date("Y-m-d H:i:s",$refund_info['platform_process_time']); //平台处理时间
                }

            }else{

                if($refund_shop_result==1){ //卖家同意
                   $status_name='退款成功'; 
                   $status_time=date("Y-m-d H:i:s",$refund_info['shop_process_time']); //店铺处理时间
                   $status_desc='退款金额已退回到您的账户余额中';

                }elseif($refund_shop_result==-1){ //卖家拒绝
                    $status_name='卖家拒绝了您的退款申请';
                    $status_time=date("Y-m-d H:i:s",$refund_info['system_process_time']); //系统自动处理时间
                }

            }

            
        }

        $model=new Model_Buyer();

        //获取卖家的处理意见
        $seller_refuse=$model->getRefundSellerRefuse($orderid);

        $shop_refuse_reason=$seller_refuse['shop_refuse_reason'];
        $shop_handle_desc=$seller_refuse['shop_handle_desc'];

        $refund_info['status_name']=$status_name;
        $refund_info['status_time']=$status_time;
        $refund_info['status_desc']=$status_desc;
        $refund_info['is_reapply']=$is_reapply;
        $refund_info['is_platform']=$is_platform;
        $refund_info['addtime']=date("Y-m-d H:i:s",$refund_info['addtime']);
        $refund_info['shop_refuse_reason']=$shop_refuse_reason;
        $refund_info['shop_handle_desc']=$shop_handle_desc;


        $rs['info'][0]['order_info']=handleGoodsOrder($order_info);
        $rs['info'][0]['refund_info']=$refund_info;

        $model=new Model_Shop();
        $shop_info=$model->getShop($order_info['shop_uid']);

        //判断用户是否关注了店铺主播
        $isattention=isAttention($uid,$touid);
        $shop_info['isattention']=$isattention;
        
        $rs['info'][0]['shop_info']=$shop_info;

        return $rs;
	}


	//买家重新申请退款
	public function reapplyRefundGoodsOrder($uid,$orderid){

		$rs=array('code'=>0,'msg'=>'重新申请成功','info'=>array());

		$now=time();
		$where=array(
            'id'=>$orderid,
            'uid'=>$uid
        );

		$order_info=getShopOrderInfo($where);
        if(!$order_info){
            $rs['code']=1001;
            $rs['msg']='订单不存在';
            return $rs;
        }

        $status=$order_info['status'];

        if($status!=5){
            $rs['code']=1001;
            $rs['msg']='订单未申请退款';
            return $rs;
        }

        //获取退款详情
        $where1=array(
            'uid'=>$uid,
            'orderid'=>$orderid

        );

        $refund_info=getShopOrderRefundInfo($where1);
        $refund_status=$refund_info['status'];
        $is_platform_interpose=$refund_info['is_platform_interpose'];
        $shop_process_num=$refund_info['shop_process_num'];
        $shop_result=$refund_info['shop_result'];

        if($refund_status==-1){
            $rs['code']=1001;
            $rs['msg']='退款申请已取消,不可重新申请';
            return $rs;
        }

        if($refund_status==1){
            $rs['code']=1001;
            $rs['msg']='退款申请已完成,不可重新申请';
            return $rs;
        }

        if($is_platform_interpose){ //平台介入
            $rs['code']=1001;
            $rs['msg']='已申请平台介入,不可重新申请';
            return $rs;
        }else{

            if($shop_process_num>=3){
               $rs['code']=1001;
                $rs['msg']='卖家已拒绝'.$shop_process_num.'次'.',不可重新申请';
                return $rs; 
            }

            if(!$shop_result){
            	$rs['code']=1001;
	            $rs['msg']='请等待商家处理';
	            return $rs;
            }
        }

        //将订单列表中的卖家处理状态改为待处理
        $data=array(
        	'refund_shop_result'=>0
        );

        changeShopOrderStatus($uid,$orderid,$data);

        $where=array(
            'uid'=>$uid,
            'orderid'=>$orderid
        );

        $data1=array(
            'shop_process_time'=>0,
            'shop_result'=>0,
        );

        //更改退款详情信息
        changeGoodsOrderRefund($where,$data1);

        $refund_history_data=array(
        	'orderid'=>$orderid,
        	'type'=>1,
        	'addtime'=>$now,
        	'desc'=>'买家重新申请退款',
        );

        //添加退款处理历史记录
        setGoodsOrderRefundList($refund_history_data);

        //写入订单消息列表

        $title="买家将“".$order_info['goods_name']."”商品重新发起了退款申请";

        $data2=array(
            'uid'=>$order_info['shop_uid'],
            'orderid'=>$orderid,
            'title'=>$title,
            'addtime'=>$now,
            'type'=>'1'

        );

        addShopGoodsOrderMessage($data2);

        //发送极光IM
        jMessageIM($title,$order_info['shop_uid'],'goodsorder_admin');

        return $rs;
	}

	//获取申请平台介入原因列表
	public function getPlatformReasonList(){
		$list=array();

		$key="getPlatformReason";
		$list=getcaches($key);
		$list=[];

		if(!$list){
			$model=new Model_Buyer();
			$list=$model->getPlatformReasonList();

			if($list){
				setcaches($key,$list);
			}

		}

		
		return $list;
	}
	
	//买家申请平台介入
	public function applyPlatformInterpose($uid,$orderid,$data){
		$rs=array('code'=>0,'msg'=>'申请平台介入成功','info'=>array());

		$now=time();
		$where=array(
            'id'=>$orderid,
            'uid'=>$uid
        );

		$order_info=getShopOrderInfo($where);
        if(!$order_info){
            $rs['code']=1001;
            $rs['msg']='订单不存在';
            return $rs;
        }

        $status=$order_info['status'];

        if($status!=5){
            $rs['code']=1001;
            $rs['msg']='订单未申请退款';
            return $rs;
        }

        //获取退款详情
        $where1=array(
            'uid'=>$uid,
            'orderid'=>$orderid

        );

        $refund_info=getShopOrderRefundInfo($where1);
        $refund_status=$refund_info['status'];
        $is_platform_interpose=$refund_info['is_platform_interpose'];
        $shop_process_num=$refund_info['shop_process_num'];
        $shop_result=$refund_info['shop_result'];

        if($refund_status==-1){
            $rs['code']=1001;
            $rs['msg']='退款申请已取消,不可重新申请';
            return $rs;
        }

        if($refund_status==1){
            $rs['code']=1001;
            $rs['msg']='退款申请已完成,不可重新申请平台介入';
            return $rs;
        }

        if($is_platform_interpose){
        	$rs['code']=1001;
            $rs['msg']='已申请平台介入,请耐心等待平台处理';
            return $rs;
        }

    	if(!$shop_result){
    		$rs['code']=1001;
            $rs['msg']='请先等待商家处理';
            return $rs;
    	}

    	$where=array(
            'uid'=>$uid,
            'orderid'=>$orderid
        );

        $res=changeGoodsOrderRefund($where,$data);

        if(!$res){
        	$rs['code']=1001;
            $rs['msg']='申请平台介入失败,请重试';
            return $rs;
        }

        //加入退款记录
        $data1=array(
        	'orderid'=>$orderid,
        	'type'=>1,
        	'addtime'=>$now,
        	'desc'=>'买家申请平台介入'

        );
        setGoodsOrderRefundList($data1);

        $title="买家将商品“".$order_info['goods_name']."”的退款申请了平台介入";

        //写入订单消息列表
        $data2=array(
            'title'=>$title,
            'orderid'=>$orderid,
            'uid'=>$order_info['shop_uid'],
            'addtime'=>$now,
            'type'=>'1'
        );

        addShopGoodsOrderMessage($data2);
        //发送极光IM
        jMessageIM($title,$order_info['shop_uid'],'goodsorder_admin');

        return $rs;

	}

    //获取买家退款列表
    public function getRefundList($uid,$p){
        $rs=array();
        $model=new Model_Buyer();
        $rs=$model->getRefundList($uid,$p);
        return $rs;
    }
}
