<?php

/**
 * 支付
 */
class Api_Pay extends PhalApi_Api
{
    public function getRules()
    {
        return array(
            'getPay' => array(
                'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
                'token' => array('name' => 'token', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => 'token'),
            ),
            'getChannelOrder' => array(
                'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
                'token' => array('name' => 'token', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => 'token'),
                'channelid' => array('name' => 'channelid', 'type' => 'int', 'require' => true, 'desc' => '通道id'),
                'money' => array('name' => 'money', 'type' => 'string', 'require' => true, 'desc' => '充值金额'),
                'name' => array('name' => 'name', 'type' => 'string',  'desc' => '姓名(通道id为3传入)'),
                'postscript' => array('name' => 'postscript', 'type' => 'string',  'desc' => '附言(通道id为3传入)'),
            ),
            
        );
    }


    /**
     * 获取支付页
     * @desc 用于获取该用户支付页
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string info[0] 支付信息
     * @return string msg 提示信息
     */
    public function getPay()
    {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());


        $uid = $this->uid;
        $info =DI()->notorm->user->where('id', $uid)->select('level')->fetchOne();

        if (!$info) {
            $rs['code'] = 1001;
            $rs['msg'] = '用户不存在';
        }

        $list = DI()->notorm->paytype->select('id,title,src,notice')->where('status',1)->fetchAll();
        foreach ($list as &$v)
        {
            $v['src'] = get_upload_path($v['src']);
            $v['list'] = pay($info['level'],$v['id']);
        }
        $rs['info'] = $list;
        return $rs;
    }

    /**
     * 通道支付
     * @desc 用于 通道支付 获取订单号
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string info[0] 支付信息
     * @return string msg 提示信息
     */
    public function getChannelOrder()
    {

        $rs = array('code' => 0, 'msg' => '', 'info' => array());
     
        $uid = $this->uid;
        $channelid = $this->channelid;
        $money = checkNull($this->money);
        $token=checkNull($this->token);
        $name = checkNull($this->name);
        $postscript = checkNull($this->postscript);

        $channel_model = new Model_Channel();
      
        $channel_info = $channel_model->get_channel($channelid);

        if ($channel_info == 1003) {
            $rs['code'] = 1003;
            $rs['msg'] = '通道信息有误或已关闭，请重新提交';
            return $rs;
        }
        
        if ($channel_info['is_range'] == 1){
            if ($money < $channel_info['min_money'] || $money > $channel_info['max_money'] || !is_numeric($money)) {
                $rs['code'] = 1003;
                $rs['msg'] = '金额错误，请重新提交';
                return $rs;
            }
        }else{
            $money_array = explode('|',$channel_info['quick_money']);
            if (!in_array(intval($money),$money_array)){
                $rs['code'] = 1003;
                $rs['msg'] = '金额错误，请重新提交';
                return $rs;
            }
        }


        $info = $channel_info;
       
        //订单号
        $info['order_id'] = $this->getOrderid($uid);
        //金额
        $info['money'] = $money;

        //判断首冲二充三充
        $charge_num = 0;
        $order_num = DI()->notorm->order->where('user_id = ?', $uid)->count();
        switch ($order_num) {
            case 0:
                $charge_num = 1;
                break;
            case 1:
                $charge_num = 2;
                break;
            case 2:
                $charge_num = 3;
                break;    
        }
    
        //生成订单
        $order = [
            'order_sn' => $info['order_id'],
//            'third_order_sn' => $res['data']['order_id'],
            'order_status' => 1,
            'pay_status' => 0,
            'user_id' => $uid,
            'payway' => $info['pay_type'],
            'channel_id' => $info['id'],
            'order_money' => $info['money'],
            'addtime' => time(),
            'type' => 1,
            'charge_num' => $charge_num,
        ];

        // 判断银行卡充值
        if($channel_info['pay_type'] == 3)
        {
            $order['name'] = $name;
            $order['postscript'] = $postscript;
        }
        
        $result = DI()->notorm->order->insert($order);
        if (!$result){
            $rs['code'] = 1003;
            $rs['msg'] = '订单生成异常';
            return $rs;
        }

        $res = ['code' => 0, 'msg' => ''];
        // 判断银行卡充值
        if($channel_info['pay_type'] == 3)
        {
            $res['msg'] = '提交成功，等待客服审核';
            return $res;
        }
        
    
        $con = new $info['controller']();
        $res = $con->pay($info);


        if ($res['code'] == 0) {
            $rs['code'] = 1003;
            $rs['msg'] = $res['msg'];
            return $rs;
        }

        $rs['info'][0] = [
            'pay_url' => $res['data']['pay_url'],
            'order_sn' => $order['order_sn']
        ];
        return $rs;

    }

    /* 获取订单号 */
    protected function getOrderid($uid)
    {
        $orderid = $uid . '_' . date('YmdHis') . rand(1000, 9999);
        return $orderid;
    }
    
    
    
}