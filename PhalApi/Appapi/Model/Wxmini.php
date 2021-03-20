<?php

class Model_Wxmini extends PhalApi_Model_NotORM {

	//获取认证信息
	public function getAuth($uid){
		$auth_info=DI()->notorm->user_auth
			->where("uid=?",$uid)
			->fetchOne();

		if(!$auth_info){
			$auth_info=[];
		}else{
			$auth_info['front_view']=get_upload_path($auth_info['front_view']);
			$auth_info['back_view']=get_upload_path($auth_info['back_view']);
			$auth_info['handset_view']=get_upload_path($auth_info['handset_view']);
			$auth_info['addtime']=date("Y-m-d H:i:s",$auth_info['addtime']);
			if($auth_info['uptime']){
				$auth_info['uptime']=date("Y-m-d H:i:s",$auth_info['uptime']);
			}
		}

		return $auth_info;
	}
	
	//用户认证
	public function userAuth($data) {
		$uid=$data['uid'];
		//判断认证信息是否存在
		$auth_info=DI()->notorm->user_auth
			->where("uid=?",$uid)
			->fetchOne();

		if($auth_info){
			$status=$auth_info['status'];
			if($status==0){ //待审核
				return 1001;
			}
			if($status==1){ //审核通过
				return 1002;
			}
			if($status==2){ //审核失败,重新提交
				unset($data['uid']);
				$data['status']=0;
				$data['uptime']=0;
				$data['addtime']=time();
				$data['reason']='';
				$rs=DI()->notorm->user_auth->where("uid=?",$uid)->update($data);
				if(!$rs){
				return 1004;
			}

			}

		}else{

			//写入数据
			$rs=DI()->notorm->user_auth
			->insert($data);
			if(!$rs){
				return 1004;
			}

			return $rs;
		}
		

	}
	//获取用户的映票提现记录		
	public function profitList($uid,$p){
		if($p<1){
			$p=1;
		}
		$pnum=50;
		$where="uid=".$uid;
		if($p!=1){
			$endtime=$_SESSION['profitlist_endtime'];
            if($endtime){
                $where.=" and addtime < {$endtime}";
            }
		}

		$list=DI()->notorm->cash_record
				->where($where)
				->order("addtime desc")
				->limit(0,$pnum)
				->fetchAll();

		foreach($list as $k=>$v){

			$list[$k]['addtime']=date('Y.m.d',$v['addtime']);
			$list[$k]['status_name']=$this->profitStatus($v['status']);
		}

		if($list){
			$last=end($list);
			$_SESSION['profitlist_endtime']=$last['addtime'];
		}

		return $list;
	}

	protected function profitStatus($k=''){
		$status=array(
	        '0'=>'审核中',
	        '1'=>'成功',
	        '2'=>'失败',
	    );
	    if($k==''){
            return $status;
        }
        return isset($status[$k]) ? $status[$k]: '';
	}

    protected function profitType($k=''){
        $type=array(
            '1'=>'支付宝',
            '2'=>'微信',
            '3'=>'银行卡',
        );
        if($k==''){
            return $type;
        }
        return isset($type[$k]) ? $type[$k]: '';
    }

	//获取退款协商历史
	public function goodsOrderRefundConsult($uid,$orderid,$user_type){
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
            return 1001;
        }

        $refund_info=getShopOrderRefundInfo($where1);
        if(!$refund_info){
        	return 1002;
        }
        $refund_info['addtime']=date('Y-m-d H:i',$refund_info['addtime']);
        //查询退款协商历史
        $refund_list=getShopOrderRefundList(['orderid'=>$orderid]);
        foreach ($refund_list as $k => $v) {
        	$refund_list[$k]['addtime']=date('Y-m-d H:i',$v['addtime']);
        }

        $refund_info['total']=$orderinfo['total'];
        $rs['refund_info']=$refund_info;
        $rs['refund_list']=$refund_list;

        return $rs;

	}

	//获取订单物流信息
	public function getOrderExpressInfo($uid,$orderid,$user_type){
		$where=[];
        if($user_type=='buyer'){
            $where=array( 
                'id'=>$orderid,
                'uid'=>$uid,
            );
        }else if($user_type=='sellers'){
            $where=array( 
                'id'=>$orderid,
                'shop_uid'=>$uid,
            );
        }else{
            $where=array(
                'id'=>$orderid
            );
        }

        $orderinfo=getShopOrderInfo($where,"express_name,express_phone,express_thumb,express_code,express_number,status,province,area,address");

        if(!$orderinfo){
            
            return 1001;
        }

        if($orderinfo['status']<0){
            
            return 1002;
        }

        if(!$orderinfo['express_code'] || !$orderinfo['express_number']){
            
            return 1003;
        }

        if($orderinfo['express_thumb']){
            $orderinfo['express_thumb']=get_upload_path($orderinfo['express_thumb']);
        }

        $result=getExpressInfoByKDN($orderinfo['express_code'],$orderinfo['express_number']);


        $result['Success']=is_true($result['Success']);

        if(!$result['Success']){

           $express_status=0;
           $desc='物流查询失败';

        }else{
           $express_status=1;
           $desc='物流查询成功';
        }

        $traces=isset($result['Traces'])?$result['Traces']:[];
        $express_list=[];

        foreach ($traces as $k => $v) {
            $info=[];
            $info['express_time']=$v['AcceptTime'];
            $info['express_msg']=$v['AcceptStation'];
            $express_list[]=$info;
        }

        $express_list=array_reverse($express_list); //数组倒序
        $express_state=isset($result['State'])?$result['State']:'0';

        $res=[];
        $res['express_status']=$express_status;
        $res['desc']=$desc;
        $res['express_list']=$express_list;
        $res['express_state']=$express_state;
        $res['orderinfo']=$orderinfo;
        $res['express_list_num']=count($express_list);
        return $res;


	}

    //获取提现记录
    public function getShopCashRecord($uid,$p){
        if($p<1){
            $p=1;
        }
        $pnum=50;
        $where="uid=".$uid;
        if($p!=1){
            $endtime=$_SESSION['shopcash_endtime'];
            if($endtime){
                $where.=" and addtime < {$endtime}";
            }
        }

        $list=DI()->notorm->user_balance_cashrecord
                ->where($where)
                ->order("addtime desc")
                ->limit(0,$pnum)
                ->fetchAll();

        /*var_dump($list);
        die;*/

        foreach($list as $k=>$v){
            $list[$k]['addtime_stamp']=$v['addtime'];
            $list[$k]['addtime']=date('Y.m.d',$v['addtime']);
            $list[$k]['status_name']=$this->profitStatus($v['status']);
            $list[$k]['type_name']=$this->profitType($v['type']);
        }

        if($list){
            $last=end($list);
            $_SESSION['shopcash_endtime']=$last['addtime_stamp'];
        }

        return $list;
    }

    
}
