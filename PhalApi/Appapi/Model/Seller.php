<?php

class Model_Seller extends PhalApi_Model_NotORM {

    // 获取卖家首页信息
    public function getHome($uid){
        $wait_payment='0';
        $wait_shipment='0';
        $wait_refund='0';

        $wait_payment=getOrderNums(['shop_uid'=>$uid,'status'=>0]);
        $wait_shipment=getOrderNums(['shop_uid'=>$uid,'status'=>1]);
        $wait_refund=getOrderNums(['shop_uid'=>$uid,'status'=>5,'refund_status'=>0,'refund_shop_result'=>0]);

        $res=array(
            'wait_payment'=>$wait_payment, //待付款订单数
            'wait_shipment'=>$wait_shipment, //待发货订单数
            'wait_refund'=>$wait_refund, //待收货订单数
        );

        return $res;
    }

	// 获取卖家一级经营类目
	public function getGoodsClass($uid) {
		
        $goods_class=DI()->notorm->seller_goods_class
            ->select('goods_classid')
            ->where('uid=? and status=1',$uid)
            ->fetchAll();

        if(!$goods_class){
        	return [];
        }

        $gc_one_ids=array_column($goods_class,"goods_classid");

        //获取这些分类中有三级分类的
        $list1=DI()->notorm->shop_goods_class->select("gc_one_id")->where("gc_grade=3")->where('gc_one_id',$gc_one_ids)->fetchAll();

        $gc_one_ids=array_column($list1,"gc_one_id");

        $ids=array_unique($gc_one_ids);

        $seller_class_list=[];

        $class_list=getcaches("goodsClass");


        if(!$class_list){

        	//获取一级分类
        	$class_list=DI()->notorm->shop_goods_class->select("gc_id,gc_name")->where("gc_parentid=0 and gc_isshow=1")->order("gc_sort")->fetchAll();

        	foreach ($class_list as $k => $v) {
	            //获取二级分类

	            $two_list=DI()->notorm->shop_goods_class->select("gc_id,gc_name")->where("gc_parentid=? and gc_isshow=1",$v['gc_id'])->order("gc_sort")->fetchAll();

	            if($two_list){
	                foreach ($two_list as $k1 => $v1) {
	                    //查询三级分类
	                    $three_list=DI()->notorm->shop_goods_class->select("gc_id,gc_name")->where("gc_parentid=? and gc_isshow=1",$v1['gc_id'])->order("gc_sort")->fetchAll();

	                    if(!$three_list){
	                        $three_list=[];
	                    }

	                    $two_list[$k1]['three_list']=$three_list;
	                }
	            }
	            
	            $class_list[$k]['two_list']=$two_list;

	        }

        	setcaches("goodsClass",$class_list);
        }



    	foreach ($class_list as $k => $v) {
    		if(in_array($v['gc_id'], $ids)){
    			$seller_class_list[]=$v;
    		}
    	}

    
		return $seller_class_list;
	}


    // 发布商品
    public function setGoods($data){
        //判断是否有相同名称的商品
        $uid=$data['uid'];
        $name=$data['name'];
        $exist=DI()->notorm->shop_goods->where("uid=? and name=?",$uid,$name)->fetchOne();

        if($exist){
            return 1001;
        }

        //向商品表写入数据
        $res=DI()->notorm->shop_goods->insert($data);
        return $res;
    }

    
    // 获取不同分类下的商品总数
    public function getGoodsNums($uid){
        $res=array(
            'onsale'=>'0', //在售
            'onexamine'=>'0', //审核
            'remove_shelves'=>'0' //下架
        );

        $onsale=DI()->notorm->shop_goods->where("uid={$uid} and status=1")->count();
        if($onsale){
            $res['onsale']=$onsale;
        }

        $onexamine=DI()->notorm->shop_goods->where("uid={$uid} and (status=0 or status=2)")->count(); //审核
        if($onexamine){
            $res['onexamine']=$onexamine;
        }

        $remove_shelves=DI()->notorm->shop_goods->where("uid={$uid} and (status=-1 or status=-2)")->count();
        if($remove_shelves){
            $res['remove_shelves']=$remove_shelves;
        }

        return $res;
    }
    // 卖家根据类型获取商品列表
    public function getGoodsList($uid,$type,$p){
        
        $where['uid']=$uid;

        switch ($type) {
            case 'onsale': //在售
                $where['status']=1;
                break;

            case 'onexamine': //审核
                $where="uid={$uid} and (status=0 or status=2)";
                break;
            
            case 'remove_shelves': //下架
                $where="uid={$uid} and (status=-1 or status=-2)";

                break;
        }

        $list=handleGoodsList($where,$p);

        return $list;
    }

    // 获取退货地址信息
    public function getReceiverAddress($uid){
        $info=DI()->notorm->shop_apply
                    ->where("uid=?",$uid)
                    ->select("receiver,receiver_phone,receiver_province,receiver_city,receiver_area,receiver_address")
                    ->fetchOne();
        if(!$info){
            return 1001;
        }

        return $info;

    }

    // 更新退货地址信息
    public function upReceiverAddress($uid,$data){
        $res=DI()->notorm->shop_apply->where("uid=?",$uid)->update($data);

        if($res===false){
            return 0;
        }

        return 1;
    }

    // 更新商品规格
    public function upGoodsSpecs($uid,$goodsid,$specs){
        $goodsinfo=DI()->notorm->shop_goods->select("uid,specs,status")->where("id=?",$goodsid)->fetchOne();

        if(!$goodsinfo){
            return 1001;
        }

        if($uid!=$goodsinfo['uid']){
            return 1002;
        }

        $status=$goodsinfo['status']; 
        if($status==0){
            return 1003;
        }

        if($status==-1||$status==-2){
            return 1004;
        }
		
		
		//获取规格最低价格
		$specArr=json_decode($specs,true);
		$low_price=$specArr[0]['price'];

        $rs=DI()->notorm->shop_goods->where("id=?",$goodsid)->update(array('specs'=>$specs,'low_price'=>$low_price));
        if($rs===false){
            return -1;
        }

        return 1;
    }

    // 修改商品
    public function upGoods($uid,$goodsid,$data){
        //判断商品是否存在
        $goodsinfo=DI()->notorm->shop_goods->select("name")->where("id=? and uid=?",$goodsid,$uid)->fetchOne();
        if(!$goodsinfo){
            return 1001;
        }

        $res=DI()->notorm->shop_goods->where("id=?",$goodsid)->update($data);
        if($res===false){
            return 1002;
        }

        return 1;
    }

    // 删除商品信息
    public function delGoods($where=[]){

        $result=DI()->notorm->shop_goods
                    ->where($where)
                    ->delete();

        return $result;
    }

    // 上架/下架商品
    public function upStatus($goodsid,$status){
        $res=DI()->notorm->shop_goods->where("id=?",$goodsid)->update(array('status'=>$status));
        return $res;
    }

    // 获取物流公司列表
    public function getExpressList(){
        $express_list=DI()->notorm->shop_express
            ->select("id,express_name,express_phone,express_thumb")
            ->where("express_status=1")
            ->order("list_order asc,id desc")
            ->fetchAll();

        return $express_list;
    }

    // 卖家根据不同订单类型获取订单列表
    public function getGoodsOrderList($uid,$type,$p){

        //订单自动处理
        goodsOrderAutoProcess($uid,array('shop_uid'=>$uid));


        if($p<1){
            $p=1;
        }

        $pnums=50;
        $start=($p-1)*$pnums;
        $now=time();

        $where=array(
            'shop_uid'=>$uid
        );

       
        switch ($type) {
            case 'wait_payment': //待付款
                $where['status']=0;
                break;

            case 'wait_shipment': //待发货
                $where['status']=1;
                break;
            
            case 'wait_receive': //已发货,待收货
                $where['status']=2;
                break;

            case 'wait_evaluate': //已签收,待评价
                $where['status']=3;
                break;

            case 'wait_refund': //待退款
                $where['status']=5;
                $where['refund_status']=0;
                $where['refund_shop_result']=0;
                break;

            case 'all_refund': //全部退款
                $where['status']=5;
                break;

            case 'closed': //已关闭
                $where['status']=-1;
                break;

            case 'finished': //已完成

                $where="shop_uid={$uid} and settlement_time>0";
                break;
            
        }


        $list=DI()->notorm->shop_order
            ->select("id,uid,shop_uid,goodsid,goods_name,spec_name,spec_thumb,nums,price,total,postage,status,orderno,refund_status,username,phone,refund_shop_result,addtime")
            ->where($where)
            ->where("isdel !=-2 and isdel !=1") //排除卖家删除的 和买家 卖家都删除的
            ->order("addtime desc")
            ->limit($start,$pnums)
            ->fetchAll();

        $shopEffectiveTime=getShopEffectiveTime();

        foreach ($list as $k => $v) {

            switch ($v['status']) {

                case '-1':
                    $list[$k]['status_name']='已关闭';
                    break;

                case '0':
                    $end=$shopEffectiveTime['shop_payment_time']*60+$v['addtime'];
                    $cha=$end-$now;
                    $list[$k]['status_name']='等待买家付款'.getSeconds($cha,1);
                    break;

                case '1':
                    
                    $list[$k]['status_name']='待发货';
                    break;

                case '2':
                    
                    $list[$k]['status_name']='已发货';
                    break;

                case '3':
                    
                    $list[$k]['status_name']='已签收';
                    break;

                case '4':
                    
                    $list[$k]['status_name']='已评价';
                    break;

                case '5':

                    if($v['refund_status']==0){
                        $list[$k]['status_name']='退款中';

                        if($v['refund_shop_result']==-1){
                            $list[$k]['status_name']='已拒绝';
                        }

                    }else if($v['refund_status']==-1){
                        $list[$k]['status_name']='退款失败';
                    }else{
                        $list[$k]['status_name']='退款成功';
                    }
                    
                    break;
                
               
            }

            $list[$k]['spec_thumb']=get_upload_path($v['spec_thumb']);

            //判断卖家是否关注了买家
            $isattention=isAttention($uid,$v['uid']);
            $list[$k]['isattention']=$isattention;

            $userinfo=getUserInfo($v['uid']);
            $list[$k]['user_nicename']=$userinfo['user_nicename'];
            $list[$k]['avatar']=$userinfo['avatar'];
            $list[$k]['avatar_thumb']=$userinfo['avatar_thumb'];

            $refund_type=0;

            if($v['status']==5){
                //获取订单退款类型
                $type=DI()->notorm->shop_order_refund->where("orderid={$v['id']}")->fetchOne("type");
                if($type){
                    $refund_type=$type;
                }
            }

            $list[$k]['refund_type']=$refund_type;

        }

        return $list;

    }

    //获取不同订单类型下的订单总数
    public function getTypeListNums($uid,$type_arr){

        $type_list_nums=array(
            'wait_payment_nums'=>0,
            'wait_shipment_nums'=>0,
            'wait_refund_nums'=>0,
            'all_refund_nums'=>0,
            'wait_receive_nums'=>0,
            'wait_evaluate_nums'=>0,
            'closed_nums'=>0,
            'finished_nums'=>0,
            'all_nums'=>0,
        );

        $all_where="shop_uid={$uid} and isdel !=-2 and isdel !=1";

        $type_list_nums['all_nums']=getOrderNums($all_where);


        foreach ($type_arr as $k => $v) {

            switch ($v) {
                
                case 'wait_payment': //待付款
                    
                    $where="shop_uid={$uid} and status=0 and isdel !=-2 and isdel !=1";
                    $type_list_nums['wait_payment_nums']=getOrderNums($where);

                    break;

                case 'wait_shipment': //待返货

                    $where1="shop_uid={$uid} and status=1 and isdel !=-2 and isdel !=1";
                    $type_list_nums['wait_shipment_nums']=getOrderNums($where1);

                    break;

                case 'wait_refund': //待退款

                    $where2="shop_uid={$uid} and status=5 and refund_status=0 and refund_shop_result=0 and isdel !=-2 and isdel !=1";
                    $type_list_nums['wait_refund_nums']=getOrderNums($where2);
                    break;

                case 'all_refund': //所有退款
                    
                    $where3="shop_uid={$uid} and status=5 and isdel !=-2 and isdel !=1";
                    $type_list_nums['all_refund_nums']=getOrderNums($where3);
                    break;
                
                case 'wait_receive': //已发货,待签收

                    $where4="shop_uid={$uid} and status=2 and isdel !=-2 and isdel !=1";
                    $type_list_nums['wait_receive_nums']=getOrderNums($where4);
                    break;

                case 'wait_evaluate': //已签收,待评价

                    $where5="shop_uid={$uid} and status=3 and isdel !=-2 and isdel !=1";
                    $type_list_nums['wait_evaluate_nums']=getOrderNums($where5);
                    break;

                case 'closed':

                    $where6="shop_uid={$uid} and status=-1 and isdel !=-2 and isdel !=1";
                    $type_list_nums['closed_nums']=getOrderNums($where6);
                    break;
                case 'finished':

                    $where7="shop_uid={$uid} and settlement_time>0 and isdel !=-2 and isdel !=1";
                    $type_list_nums['finished_nums']=getOrderNums($where7);
                    break;
                
            }


        }

        return $type_list_nums;

    }

    //获取拒绝退款原因列表
    public function getRefundRefuseReason(){
        $list=DI()->notorm->shop_refuse_reason
                ->select("id,name")
                ->where('status=1')
                ->order("list_order asc,id desc")
                ->fetchAll();
        return $list;
    }

    //卖家获取结算记录
    public function getSettlementList($uid,$p){

        if($p<1){
            $p=1;
        }

        $pnums=50;
        $start=($p-1)*$pnums;

        $list=DI()->notorm->user_balance_record
                ->where("uid=? and type=1 and action =2",$uid)
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

    //获取卖家待结算总金额
    public function getWaitSettlementTotal($uid){
        $sum=DI()->notorm->shop_order
                ->where("shop_uid=? and status>0 and refund_status !=1 and settlement_time=0",$uid)
                ->sum('total');
        if(!$sum){
            $sum='0.00';
        }
        return $sum;
    }

    public function setOutsideGoods($data){

        //判断是否有相同名称的商品
        $uid=$data['uid'];
        $name=$data['name'];
        $exist=DI()->notorm->shop_goods->where("uid=? and name=?",$uid,$name)->fetchOne();

        if($exist){
            return 1001;
        }

        //向商品表写入数据
        $res=DI()->notorm->shop_goods->insert($data);
        return $res;
    }

    //外链商品编辑提交
    public function upOutsideGoods($goodsid,$data){

        //判断是否有相同名称的商品
        $uid=$data['uid'];
        $name=$data['name'];
        $exist=DI()->notorm->shop_goods->where("uid=? and name=? and id !=?",$uid,$name,$goodsid)->fetchOne();

        if($exist){
            return 1001;
        }
        
        $res=DI()->notorm->shop_goods->where("id=?",$goodsid)->update($data);
        if($res===false){
            return 1002;
        }

        return 1;
    }

    

}
