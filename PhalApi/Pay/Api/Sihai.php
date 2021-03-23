<?php
/**
 * 支付所有接口
 *
 * @author: dogstar <chanzonghuang@gmail.com> 2014-10-04
 */

class Api_Sihai extends PhalApi_Api {

    private $_privKey;
    private $_pubKey;
    public $publicKey = '';
    public $privateKey = 'MIICdgIBADANBgkqhkiG9w0BAQEFAASCAmAwggJcAgEAAoGBAKqvljRus4fy7ZaUkxvKOjlFuYtlGyYPQjY1BNaiv7pa8ypBWJyfxQWr8/yz4WsgJKy0FlHNP/Iq6UhSVCNDNKBIT1ifiPdnxKOqNWCAP9h7ImFxGlo8k7rBQegnoenI1vTScSGkMKFH+NDSdrswIpc0JNNU4m/khuAT5dg8nE6nAgMBAAECgYAGh378dHujqLRT/Uz/VCYUCMnHPk/ijdTfk/miaEljTJVeuW81VptH00MbGJp36Zvdi2oLKQnYLrIL1TJowuphYz+DmbID4H+YlRMvOCfw+CpliyaAUh/8/iH3dECNmib9Q9Cfg8uATW5ZADDqvv5o7+UNtTtv6c4POo2D8MO2eQJBANu2vm6bWT4jiUPLZtISbqX5ammYo68Lrn7NYnoQvDK11mrHPs1sDj9qCtZdyH7wYvRhRUZWuj8ASdQ53jmPZUUCQQDG3/7arlnLvPjWjx1ist0byRwKdqL8MQjgok+9Srv2qzyMb6W7ZlAn1yDPr6zAzbscVM08e3VkYTWwOkX0yjT7AkBmja/pdL19EZ06dbBykYPwGLEgxMxyIiO6sCctDq6phNKmWIXp4GvuEZMpZ/Dzv0SRCO4K3ORmD75mPvSJLXN5AkAY+KVChiPmTiscncm9y+GxjHYF5lGewvVvZ1IF3a1uUp/+rkIsHrOv3PZUvaU+bFazPv6qOoJKAV7Bav+/tegjAkEAgnROlO32bbqRZoe1tl5/cYr/KF2QC79M4tnyyI6ec9+w0t1m69xuBZk5KTxseVNVteE6fcoKOklRSCnv2RTNFg==';
    
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
            $type = 'zfbwap';
        }
        if ($info['pay_type'] == 2) {
            $type = 'wxwap';
        }
        if (!isset($type)) return ['code' => 0, 'msg' => '配置错误'];
        $data = [
            'merId' => $info['shop_id'],
            'orderId' => $info['order_id'],
            'orderAmt' => $info['money'],
            'channel' => $type,
            'desc' => '会员充值',
            'attch' =>'111',
            'smstyle' => 1,
            'ip' => '127.0.0.1',
            'userId' => rand(111,999),
            'notifyUrl' => $info['notify_url'],
            'returnUrl' => $info['return_url'],
            'nonceStr' => 'te' . rand(111,999),
        ];
      
        $data['sign'] = $this->get_sign($data, $info['key']);
        
   

        $res = Post($data, $info['action']);
        $res = json_decode($res,true);
        
        
        if(!empty($res['code']) && $res['code'] == 1)
        {
            return ['code' => 1, 'msg' => '成功', 'data' => [
                'order_id' => $info['order_id'],
                'pay_url' => $res['data']['payurl']
            ]];
        }
        
        
        return ['code' => 0, 'msg' => '通道异常'];
	}

    //md5生成签名
    protected function get_sign($data, $keys)
    {
        
        ksort($data);
        reset($data);
        $arg = '';
        foreach ($data as $key => $val) {
            //空值不参与签名
            if ($val == '' || $key == 'sign') {
                continue;
            }
            $arg .= ($key . '=' . $val . '&');
        }
        $arg = $arg . 'key=' . $keys;
 
        //签名数据转换为大写
        $sig_data = strtoupper(md5($arg));
        return $this->sign($sig_data);

    }
    
    
    public function sign($dataString)
    {
        $this->setupPrivKey();
        $signature = false;
        
        openssl_sign($dataString, $signature, $this->_privKey,OPENSSL_ALGO_SHA256);
      
        return base64_encode($signature);
    }
    
    /**
     * * setup the private key
     */
    private function setupPrivKey()
    {
        if (is_resource($this->_privKey)) {
            return true;
        }
        $pem = chunk_split($this->privateKey, 64, "\n");
        $pem = "-----BEGIN PRIVATE KEY-----\n" . $pem . "-----END PRIVATE KEY-----\n";
        $this->_privKey = $pem;
        return true;
    }

    /**
     * * setup the public key
     */
    private function setupPubKey()
    {
        if (is_resource($this->_pubKey)) {
            return true;
        }
        $pem = chunk_split($this->publicKey, 64, "\n");
        $pem = "-----BEGIN PUBLIC KEY-----\n" . $pem . "-----END PUBLIC KEY-----\n";
        $this->_pubKey = openssl_pkey_get_public($pem);
        return true;
    }
} 
