<?php
/**
 * 支付所有接口
 *
 * @author: dogstar <chanzonghuang@gmail.com> 2014-10-04
 */

class Api_Yy2 extends PhalApi_Api {

	public function getRules() {
        return array(
        );
	}

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
            $type = 'alipay';
        }
        if (!isset($type)) return ['code' => 0, 'msg' => '配置错误'];
        $data = [
            'pid' => $info['shop_id'],
            'type' => $type,
            'out_trade_no' => $info['order_id'],
            'notify_url' => $info['notify_url'],
            'return_url' => $info['return_url'],
            'name' => '充值',
            'money' => $info['money'],
            'sitename' => 'yunbao',
        ];
        $data['sign'] = $this->get_sign($data, $info['key']);
        $data['sign_type'] = 'MD5';
        $res = Post($data, $info['action']);
        if (strpos($res, '订单创建成功') !== false) {
            $pay_url = substr($res, strpos($res, 'https://payplf-gate.yy.com'), strpos($res, '.do') - strpos($res, 'https://payplf-gate.yy.com') + 3);
            $order_id = substr($res, strpos($res, 'oid=') + 4, strpos($res, '[payUrl]') - (strpos($res, 'oid=') + 4));
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
        $str = substr($str, 0, -1);
        $str .= $key;
        return md5($str);
    }
    
} 
