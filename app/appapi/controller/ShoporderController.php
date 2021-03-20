<?php
/**
 * 商品订单定时处理
 */
namespace app\appapi\controller;

use cmf\controller\HomeBaseController;
use think\Db;
use think\db\Query;

class ShoporderController extends HomebaseController {


	//定时更新商品订单状态
	public function checkOrder(){
		$lastid=$this->request->param('lastid',0,'intval');
		if(!$lastid){
			$lastid=0;
		}
		$limit=1000;


		$order_list=Db::name("shop_order")->where("isdel !=-1 and isdel !=-2 and isdel !=1 and status !=-1 and id>{$lastid} ")->order("id asc")->limit($limit)->select()->toArray();

		$now=time();
        $effective_time=getShopEffectiveTime();
		
		foreach ($order_list as $k => $v) {

			if($v['status']==0){ //待付款要判断是否付款超时
                $pay_end=$v['addtime']+$effective_time['shop_payment_time']*60;
                if($pay_end<=$now){

                    $data=array(
                        'status'=>-1,
                        'cancel_time'=>$now
                    );
                    changeShopOrderStatus($v['uid'],$v['id'],$data); //将订单关闭

                    //商品规格库存回增
                    changeShopGoodsSpecNum($v['goodsid'],$v['spec_id'],$v['nums'],1);

                    //给买家发消息
                    $title="你购买的“".$v['goods_name']."”订单由于超时未付款,已自动关闭";
                    $data1=array(
			            'uid'=>$v['uid'],
			            'orderid'=>$v['id'],
			            'title'=>$title,
			            'addtime'=>$now,
			            'type'=>'0'

			        );

			        addShopGoodsOrderMessage($data1);
			        //发送极光IM
        			jMessageIM($title,$v['uid'],'goodsorder_admin');

                }
            }

            if($v['status']==1){ //买家已付款 判断卖家发货是否超时

            	//如果买家没有申请退款
            	if($v['refund_status']==0){

            		$shipment_end=$v['paytime']+$effective_time['shop_shipment_time']*60*60*24;
	                
            	}else{ //买家申请了退款，判断时间超时，要根据退款最终的处理时间
            	
            		$shipment_end=$v['refund_endtime']+$effective_time['shop_shipment_time']*60*60*24;
            	}

            	if($shipment_end<=$now){

                    $data=array(
                        'status'=>-1,
                        'cancel_time'=>$now
                    );
                    changeShopOrderStatus($v['uid'],$v['id'],$data); //将订单关闭

                    //退还买家货款
                    setUserBalance($v['uid'],1,$v['total']);

                    //添加余额操作记录
                    $data1=array(
                        'uid'=>$v['uid'],
                        'touid'=>$v['shop_uid'],
                        'balance'=>$v['total'],
                        'type'=>1,
                        'action'=>3, //卖家超时未发货,退款给买家
                        'orderid'=>$v['id'],
                        'addtime'=>$now

                    );

                    addBalanceRecord($data1);

                    //店铺逾期发货记录+1
                    Db::name("shop_apply")
                    	->where("uid={$v['shop_uid']}")
                    	->setInc('shipment_overdue_num');

                    //减去商品销量
            		changeShopGoodsSaleNums($v['goodsid'],0,$v['nums']);

                   	//减去店铺销量
        			changeShopSaleNums($v['shop_uid'],0,$v['nums']);

                    //给买家发消息
                    $title="你购买的“".$v['goods_name']."”订单由于卖家超时未发货已自动关闭,货款已退还到余额账户中";
                    $data2=array(
			            'uid'=>$v['uid'],
			            'orderid'=>$v['id'],
			            'title'=>$title,
			            'addtime'=>$now,
			            'type'=>'0'

			        );

			        addShopGoodsOrderMessage($data2);
			        //发送极光IM
        			jMessageIM($title,$v['uid'],'goodsorder_admin');

                }

            }


            if($v['status']==2){ //待收货 判断自动确认收货时间是否已满足

                //如果买家没有申请退款
            	if($v['refund_status']==0){
            		$receive_end=$v['shipment_time']+$effective_time['shop_receive_time']*60*60*24;
            	}else{
            		$receive_end=$v['refund_endtime']+$effective_time['shop_receive_time']*60*60*24;
            	}

                if($receive_end<=$now){

                    $data=array(
                        'status'=>3,
                        'receive_time'=>$now
                    );

                    changeShopOrderStatus($v['uid'],$v['id'],$data); //将订单改为待评价

                    //给买家发消息
                    $title="你购买的“".$v['goods_name']."”订单已自动确认收货";
                    $data1=array(
			            'uid'=>$v['uid'],
			            'orderid'=>$v['id'],
			            'title'=>$title,
			            'addtime'=>$now,
			            'type'=>'0'

			        );

			        addShopGoodsOrderMessage($data1);
			        //发送极光IM
        			jMessageIM($title,$v['uid'],'goodsorder_admin');
                }

            }


            if(($v['status']==3||$v['status']==4)&&$v['settlement_time']==0){  //待评价或已评价 且未结算

            	//判断是否有过退货处理 判断确认收货后是否达到后台设置的给卖家打款的时间
            	if($v['refund_status']==0){
            		$settlement_end=$v['receive_time']+$effective_time['shop_settlement_time']*60*60*24;
            	}else{
            		$settlement_end=$v['refund_endtime']+$effective_time['shop_settlement_time']*60*60*24;	
            	}

            	
            	if($settlement_end<$now){

            		//给卖家增加余额
			        $balance=$v['total'];

			        if($v['order_percent']>0){
			            $balance=$v['total']*(100-$v['order_percent'])/100;
			            $balance=round($balance,2);
			        }


			        $res1=setUserBalance($v['shop_uid'],1,$balance);

			        //更改订单信息
			        $data=array(
			        	'settlement_time'=>$now
			        );

			        changeShopOrderStatus($v['uid'],$v['id'],$data);

			        //添加余额操作记录
                    $data1=array(
                        'uid'=>$v['shop_uid'],
                        'touid'=>$v['uid'],
                        'balance'=>$balance,
                        'type'=>1,
                        'action'=>2, //系统自动结算货款给卖家
                        'orderid'=>$v['id'],
                		'addtime'=>$now

                    );

                    addBalanceRecord($data1);

                    //给卖家发消息
                    $title="买家购买的“".$v['goods_name']."”订单已自动结算到你的账户";
                    $data2=array(
			            'uid'=>$v['shop_uid'],
			            'orderid'=>$v['id'],
			            'title'=>$title,
			            'addtime'=>$now,
			            'type'=>'1'

			        );

			        addShopGoodsOrderMessage($data2);
			        //发送极光IM
        			jMessageIM($title,$v['shop_uid'],'goodsorder_admin');

            	}	


            }


            if($v['status']==5&&$v['refund_status']==0){ //退款 判断等待卖家处理的时间是否超出后台设定的时间，如果超出，自动退款

            	//获取退款申请信息
            	$where=array(
                    'orderid'=>$v['id']
            	);

	            $refund_info=getShopOrderRefundInfo($where);
	            

	            if($refund_info['is_platform_interpose']==0&&$refund_info['shop_result']==0){ //平台未介入且店家未处理

	            	$refund_end=$refund_info['addtime']+$effective_time['shop_refund_time']*60*60*24;


	            	if($refund_end<=$now){

	            		//更改订单退款状态
	            		$data=array(
	                        'refund_status'=>1,
	                        'refund_endtime'=>$now
	                    );

	                    changeShopOrderStatus($v['uid'],$v['id'],$data);

	                    //更改订单退款记录信息

	                    $data1=array(
	                    	'system_process_time'=>$now,
	                    	'status'=>1,

	                    );

	                    changeGoodsOrderRefund($where,$data1);

	            	
	            		//退还买家货款
	                    setUserBalance($v['uid'],1,$v['total']);

	                    //添加余额操作记录
	                    $data1=array(
	                        'uid'=>$v['uid'],
	                        'touid'=>$v['shop_uid'],
	                        'balance'=>$v['total'],
	                        'type'=>1,
	                        'action'=>4, //买家发起退款，卖家超时未处理，系统自动退款
	                        'orderid'=>$v['id'],
                    		'addtime'=>$now

	                    );

	                    addBalanceRecord($data1);

	                    //减去商品销量
            			changeShopGoodsSaleNums($v['goodsid'],0,$v['nums']);

            			//减去店铺销量
        				changeShopSaleNums($v['shop_uid'],0,$v['nums']);

        				//商品规格库存回增
        				changeShopGoodsSpecNum($v['goodsid'],$v['spec_id'],$v['nums'],1);

            			//给买家发消息
	                    $title="你申请的“".$v['goods_name']."”订单退款卖家超时未处理,已自动退款到你的余额账户中";
	                    $data2=array(
				            'uid'=>$v['uid'],
				            'orderid'=>$v['id'],
				            'title'=>$title,
				            'addtime'=>$now,
				            'type'=>'0'

				        );

				        addShopGoodsOrderMessage($data2);
				        //发送极光IM
	        			jMessageIM($title,$v['uid'],'goodsorder_admin');


	            	}
	            	
	            }

	            if($refund_info['is_platform_interpose']==0&&$refund_info['shop_result']==-1){ //未申请平台介入且店家已拒绝
	            	//超时，退款自动完成,订单自动进入退款前状态
	            	$finish_endtime=$refund_info['shop_process_time']+$effective_time['shop_refund_finish_time']*60*60*24;
	            	if($finish_endtime<=$now){

	            		//更改退款订单状态

	            		$data=array(
	            			'status'=>1,
	            			'system_process_time'=>$now
	            		);

	            		changeGoodsOrderRefund($where,$data);


	            		//更改订单状态
	            		$data1=array(
	            			'refund_endtime'=>$now,
	            			'refund_status'=>-1
	            		);

	            		if($v['receive_time']>0){
	            			$data1['status']=3; //待评价
	            		}else{

	            			if($v['shipment_time']>0){
		            			$data1['status']=2; //待收货
		            		}else{
		            			$data1['status']=1; //待发货
		            		}

	            		}

	            		changeShopOrderStatus($v['uid'],$v['id'],$data1);

	            		//给买家发消息
	                    $title="你购买的“".$v['goods_name']."”订单退款申请被卖家拒绝后,".$effective_time['shop_refund_finish_time']."天内你没有进一步操作,系统自动处理结束";
	                    $data2=array(
				            'uid'=>$v['uid'],
				            'orderid'=>$v['id'],
				            'title'=>$title,
				            'addtime'=>$now,
				            'type'=>'0'

				        );

				        addShopGoodsOrderMessage($data2);
				        //发送极光IM
	        			jMessageIM($title,$v['uid'],'goodsorder_admin');

	            	}
	            }

            }



			$lastid=$v['id'];
			
		}

		//file_put_contents(CMF_ROOT.'data/shoporderauto/record_'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').'  lastid:'.$lastid.PHP_EOL.PHP_EOL,FILE_APPEND);

		$list_nums=count($order_list);



		if($list_nums<$limit){
			echo "NO";
            exit;  
		}

		echo 'OK-'.$lastid;
        exit;
	}
}