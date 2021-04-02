<?php

class Api_Xm extends PhalApi_Api {

    public function getRules() {
        return array(
        );
    }



    /**
     * 默认接口服务
     * @return string title 标题
     * @return string content 内容
     * @return string version 版本，格式：X.X.X
     * @return int time 当前时间戳
     */
    public function pay($info) {
        $pay_channel  = 'WXH5';
        $amount = $info['money'];
        $url = $info['action'];

        $data = [
            'merchant_no' => $info['shop_id'],
            'pay_channel' => $pay_channel,
            'request_no' => $info['order_id'],
            'notify_url' => $info['notify_url'],
            'return_url' => $info['return_url'],
            'amount' => $info['money'],
            'request_time' => time(),
            'nonce_str' => session_create_id(), 
        ];

        $data['sign'] = $this->get_sign($data, $info['key']);

        $res = curl_post($url,$data);

        $res = json_decode($res,true);

        if (!empty($res['data']['bank_url'])){
            $return = [
                'pay_url' => $res['data']['bank_url'],
                'order_id' => $info['order_id']
            ];
            return ['code' => 1, 'msg' => 'ok', 'data' => $return];
        }else{
            return ['code' => 0, 'msg' => '通道异常'];
        }
    }

    //md5生成签名
    protected function get_sign($params, $key)
    {
        if (isset($params['sign'])) unset($params['sign']);
        $arr = array_diff($params, ['']);
        ksort($arr);
        return strtoupper(md5(urldecode(http_build_query($arr))."&key=".$key));
    }
    
    
    
}