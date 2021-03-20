<?php

class Model_Message extends PhalApi_Model_NotORM {
	/* 信息列表 */
	public function getList($uid,$p) {
        if($p<1){
            $p=1;
        }
		$pnum=50;
		$start=($p-1)*$pnum;
        
		$list=DI()->notorm->pushrecord
                    ->select('content,addtime')
                    ->where("(type=0 and (touid='' or( touid!='' and (touid = '{$uid}' or touid like '{$uid},%' or touid like '%,{$uid},%' or touid like '%,{$uid}') ))) or (type=1 and touid='{$uid}')")
                    ->order('addtime desc')
                    ->limit($start,$pnum)
                    ->fetchAll();

		return $list;
	}

    //店铺订单信息列表
    public function getShopOrderList($uid,$p){
        if($p<1){
            $p=1;
        }
        $pnum=50;
        $start=($p-1)*$pnum;

        $list=DI()->notorm->shop_order_message
                ->select("title,orderid,addtime,type")
                ->where("uid=?",$uid)
                ->order("addtime desc")
                ->limit($start,$pnum)
                ->fetchAll();

        foreach ($list as $k => $v) {
            $list[$k]['addtime']=date("Y-m-d H:i",$v['addtime']);
            $list[$k]['avatar']=get_upload_path('/orderMsg.png');

            $where['id']=$v['orderid'];
            $order_info=getShopOrderInfo($where,'status');
            $list[$k]['status']=$order_info['status'];
        }

        return $list;
    }		

}
