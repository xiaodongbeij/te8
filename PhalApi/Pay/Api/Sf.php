<?php
/**
 * 支付所有接口
 *
 * @author: dogstar <chanzonghuang@gmail.com> 2014-10-04
 */

class Api_Sf extends PhalApi_Api {

	public function getRules() {
        return array(
        );
	}

	
	public function pay($info)
    {
        // $uid = checkNull($this->uid);
        // $token = checkNull($this->token);
        // $coin = checkNull($this->money);
        // $checkToken = checkToken($uid,$token);
        // if($checkToken == 700){
        //     $rs['code'] = $checkToken;
        //     $rs['msg'] = '您的登陆状态失效，请重新登陆！';
        //     return $rs;
        // }
        
        // $info = DI()->notorm->channel->where('shop_id = ?','sh892')->fetchOne();
        
        // if(!$info) return ['code' => 1, 'msg' => '支付通道异常'];
        // if($info['status'] == 0) return ['code' => 1, 'msg' => '该支付通道已被禁用'];
        // if($coin < $info['min_money']) return ['code' => 1, 'msg' => '单笔最小充值' . $info['min_money'] . '元哦'];
        // if($coin > $info['max_money']) return ['code' => 1, 'msg' => '单笔最大充值' . $info['max_money'] . '元哦'];
        
        // var_dump($info);die;
        
        $key = $info['shop_id'];
        $secret = $info['key'];
        $url = $info['action'];
        
        // $order_id = $this->getOrderid($uid);
        $order_id = $info['order_id'];
        $ip = getIP();

        //3.设置请求参数
        $arrAccount=array(
            "merchantCode"=>$key,
            "orderNo"=>$order_id,
            "orderPrice"=>round($info['money']) * 100, 
            "clientIp"=>$ip, //交易类型
            "returnUrl"=>$info['return_url'], 
            "notifyUrl"=>$info['notify_url'], 
            "payType"=>'10000', 
            "terminal"=>0,//页面通知地址
        );
        
        $arrAccount["sign"] = md5Sign($arrAccount,$secret);
        
        // $order = [
        //     'order_sn' => $order_id,
        //     'order_status' => 1,
        //     'pay_status' => 0,
        //     'user_id' => $uid,
        //     'payway' => $info['pay_type'],
        //     'channel_id' => $info['id'],
        //     'order_money' => round($coin),
        //     'addtime' => time(),
        //     'type' => 1
        // ];
        
        // $res = DI()->notorm->order->insert($order);
        
        $pageContents = buildRequestForm($url, $arrAccount);
        die;
    }
    
} 
