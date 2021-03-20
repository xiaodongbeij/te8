<?php
/**
 * 物流信息
 */
namespace app\appapi\controller;

use cmf\controller\HomeBaseController;
use think\Db;

class ExpressController extends HomebaseController{
	
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

        

        $orderinfo=getShopOrderInfo($where,"express_name,express_phone,express_thumb,express_code,express_number,status,province,city,area,address");

        if(!$orderinfo){
            $reason='订单不存在';
            $this->assign('reason', $reason);
            return $this->fetch(':error');
        }

        if($orderinfo['status']<0){
            $reason='订单未发货';
            $this->assign('reason', $reason);
            return $this->fetch(':error');
        }

        if(!$orderinfo['express_code'] || !$orderinfo['express_number']){
            $reason='物流参数错误';
            $this->assign('reason', $reason);
            return $this->fetch(':error');
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

        
        $this->assign("express_status",$express_status); //查询状态
        $this->assign("desc",$desc);
        $this->assign("express_list",$express_list);
        $this->assign("express_state",isset($result['State'])?$result['State']:'0'); //物流运输状态 2-在途中,3-签收,4-问题件
        $this->assign("orderinfo",$orderinfo); //物流运输状态 2-在途中,3-签收,4-问题件
        $this->assign("express_list_num",count($express_list)); //物流运输状态 2-在途中,3-签收,4-问题件
        return $this->fetch();
	}
	
    

    

	
}