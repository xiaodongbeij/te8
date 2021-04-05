<?php

class Api_Mg extends PhalApi_Api {

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
            $type = '3';
        }
        if (!isset($type)) return ['code' => 0, 'msg' => '配置错误'];

        $data = [
            'merchantNumber' => $info['shop_id'],
            'paymentMethod' => $type,
            'merchantOrderNumber' => $info['order_id'],
            'callbackUrl' => $info['notify_url'],
            'customerRequestedIp' => getIP(),
            'paymentPlatform' => '2',
            'requestedAmount' => $info['money'],
        ];
//        var_dump($data);
        $data['sign'] = $this->get_sign($data, $info['key']);
        $data['createType'] = 1;

        $url = trim($info['action']) . '?';
        foreach ($data as $k => $v){
            $url .= $k . '=' . $v .'&';
        }
        $url = substr($url,0,-1);
        $return = [
            'pay_url' => $url,
            'order_id' => $info['order_id']
        ];
        return ['code' => 1, 'msg' => 'ok', 'data' => $return];

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
        $str .= 'merchantKey=' . $key;
        return strtoupper(md5($str));
    }
}