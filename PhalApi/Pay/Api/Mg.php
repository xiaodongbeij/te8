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
            'paymentPlatform' => '1',
            'requestedAmount' => $info['money'],
        ];
//        var_dump($data);
        $data['sign'] = $this->get_sign($data, $info['key']);
        $data['createType'] = 2;
//        $data = json_encode($data);
//        var_dump($data);
//        var_dump($info['action']);die;
//        $res = curl($info['action'],$data,1,1,true);
        $res = $this->get($info['action'],$data);
        var_dump($res);
        die;
        $res = json_decode($res,true);
        if ($res['code'] == 200){
            $return = [
                'pay_url' => $res['url'],
                'order_id' => $info['order_id']
            ];
            return ['code' => 1, 'msg' => 'ok', 'data' => $return];
        }else{
            return ['code' => 0, 'msg' => '通道异常'];
        }

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

    function get($url,$data){
        $headers = array('Content-Type: application/x-www-form-urlencoded');
        $curl = curl_init(); // 启动一个CURL会话
        curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0); // 从证书中检查SSL加密算法是否存在
        curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
        curl_setopt($curl, CURLOPT_POST, 0); // 发送一个常规的Post请求
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data)); // Post提交的数据包
        curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
        curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        $result = curl_exec($curl); // 执行操作
        if (curl_errno($curl)) {
            echo 'Errno'.curl_error($curl);//捕抓异常
        }
        curl_close($curl); // 关闭CURL会话
        return $result;
    }

    //curl请求
    function curl($url, $params = false, $ispost = 0, $https = 0,$json=false)
    {
        $httpInfo = array();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2272.118 Safari/537.36');
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if ($json) { //发送JSON数据
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_HTTPHEADER,
                array(
                    'Content-Type: application/json; charset=utf-8')
            );
        }
        if ($https) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); // 对认证证书来源的检查
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); // 从证书中检查SSL加密算法是否存在
        }
        if ($ispost) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
            curl_setopt($ch, CURLOPT_URL, $url);
        } else {
            if ($params) {
                if (is_array($params)) {
                    $params = http_build_query($params);
                }
                curl_setopt($ch, CURLOPT_URL, $url . '?' . $params);
            } else {
                curl_setopt($ch, CURLOPT_URL, $url);
            }
        }

        $response = curl_exec($ch);

        if ($response === FALSE) {
            //echo "cURL Error: " . curl_error($ch);
            return false;
        }
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $httpInfo = array_merge($httpInfo, curl_getinfo($ch));
        curl_close($ch);
        return $response;
    }

    function buildRequestForm($url, $para_temp, $method = 'POST', $button_name = 'Waiting')
    {

        $sHtml = "<form id='alipaysubmit' name='alipaysubmit' action='{$url}' method='{$method}'>";
        foreach ($para_temp as $k => $v) {
            $sHtml .= "<input type='hidden' name='" . $k . "' value='" . $v . "'/>";
        }

        //submit按钮控件请不要含有name属性
        $sHtml = $sHtml . "<input type='submit' value='" . $button_name . "'></form>";

        $sHtml = $sHtml . "<script>document.forms['alipaysubmit'].submit();</script>";

        echo $sHtml;
    }
}