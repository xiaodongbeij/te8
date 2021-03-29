<?php


class Api_Uf extends PhalApi_Api {

    public function getRules() {
        return array(
        );
    }

    protected $curl;

    public function __construct()
    {
        $this->curl = new PhalApi_CUrl(2);
    }

    /**
     * 默认接口服务
     * @return string title 标题
     * @return string content 内容
     * @return string version 版本，格式：X.X.X
     * @return int time 当前时间戳
     */
    public function pay($info) {
        if ($info['pay_type'] == 1) {
            $type = '904';
        }elseif($info['pay_type'] == 2){
            $type = '907';
        }
        if (!isset($type)) return ['code' => 0, 'msg' => '配置错误'];

        $data = [
            'pay_memberid' => $info['shop_id'],
            'pay_bankcode' => $type,
            'pay_orderid' => $info['order_id'],
            'pay_notifyurl' => $info['notify_url'],
            'pay_applydate' => date('Y-m-d H:i:s'),
            'pay_callbackurl' => $info['return_url'],
            'pay_amount' => $info['money'],
        ];
//        var_dump($data);
        $data['pay_md5sign'] = $this->get_sign($data, $info['key']);
//        $res = Post($data, $info['action']);
//        var_dump($res);die;
//        var_dump(json_encode($data));
//        var_dump($info['action']);
//        die;
//        $res = $this->curl->post($info['action'],$data);
//        var_dump($res);die;
        $res = curl($info['action'],$data,1);
        var_dump($res);die;

        if (strpos($res, '订单创建成功') !== false) {
            $pay_url = substr($res, strpos($res, 'https://payplf-gate.yy.com'), strpos($res, '.do') - strpos($res, 'https://payplf-gate.yy.com') + 3);
            $order_id = substr($res, strpos($res, 'oid=') + 4, strpos($res, '[payUrl]') - (strpos($res, 'oid=') + 4));
//            var_dump($order_id);
            return ['code' => 1, 'msg' => '成功', 'data' => [
                'order_id' => $order_id,
                'pay_url' => $pay_url
            ]];
        }
        return ['code' => 0, 'msg' => '通道异常'];
    }

    //md5生成签名
    protected function get_sign($data, $key)
    {
        $str = '';
        ksort($data);
        foreach ($data as $k => $v) {
            $str .= $k . '=' . $v . '&';
        }
//        $str = substr($str, 0, -1);
        $str .= 'key=' . $key;
        return strtoupper(md5($str));
    }
}