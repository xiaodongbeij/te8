<?php
/*
阿里云SMS发送类  
@author：leafrainy
@time：2018年09月28日
@notice：参考于阿里云180928之时的官方API demo
@食用方式：

@单一手机号

$config  = array(
    'accessKeyId' => "xxxx", 
    'accessKeySecret' => "xxxxx", 
    'PhoneNumbers' => "13812345678", 
    'SignName' => "叶雨梧桐", 
    'TemplateCode' => "SMS_14681201106", 
    'TemplateParam' => array("info"=>"哈哈哈") 
    );
$go = new AliSmsApi($config);

$go->send_sms();

@多个手机号
$config  = array(
    'accessKeyId' => "xxxx", 
    'accessKeySecret' => "xxxxx", 
    'PhoneNumbers' => "13812345678,13898765432", 
    'SignName' => "叶雨梧桐", 
    'TemplateCode' => "SMS_1468121106", 
    'TemplateParam' => array("info"=>"哈哈哈") 
    );
$go = new AliSmsApi($config);

$go->send_sms();

*/


class AliSmsApi {


    //必填：是否启用https,false为不启用
    private $security = false;

    //阿里授权ak
    private $accessKeyId = "";
    //阿里授权aks
    private $accessKeySecret = "";
    //短信签名
    private $SignName = "";
    //短信模板
    private $TemplateCode = "";
    //短信内容
    private $TemplateParam = "";
    //接受手机号
    private $PhoneNumbers = "";


    public function __construct($config =array()){
        $this->accessKeyId = $config['accessKeyId'];
        $this->accessKeySecret = $config['accessKeySecret'];
        $this->SignName = $config['SignName'];
        $this->TemplateCode = $config['TemplateCode'];
        $this->TemplateParam = json_encode($config["TemplateParam"], JSON_UNESCAPED_UNICODE);
        $this->PhoneNumbers = $config['PhoneNumbers'];
    }

    //发送短信
    public function send_sms(){
		
        $signData = $this->sign();
        $url = ($this->security ? 'https' : 'http')."://dysmsapi.aliyuncs.com/";
        $content = $this->fetchContent($url, $signData['method'], "Signature=".$signData['signature'].$signData['sortedQueryStringTmp']);
        $res = json_decode($content,true);
		
		
        return $res;
        //日志记录
        // $res1=$this->infoLog($res);
		

    }

    //生成签名
    private function sign($method='POST'){
        $params = array(
            "PhoneNumbers" => $this->PhoneNumbers, 
            "SignName"     => $this->SignName,
            "TemplateCode" => $this->TemplateCode,
            "TemplateParam"=> $this->TemplateParam,
            "SignatureMethod" => "HMAC-SHA1",
            "SignatureNonce" => uniqid(mt_rand(0,0xffff), true),
            "SignatureVersion" => "1.0",
            "AccessKeyId" => $this->accessKeyId,
            "Timestamp" => gmdate("Y-m-d\TH:i:s\Z"),
            "Format" => "JSON",
            "RegionId" => "cn-hangzhou",
            "Action" => "SendSms",
            "Version" => "2017-05-25",
            );
        ksort($params);
        $sortedQueryStringTmp = "";
        foreach ($params as $key => $value) {
            $sortedQueryStringTmp .= "&" . $this->encode($key) . "=" . $this->encode($value);
        }
        $stringToSign = "${method}&%2F&" . $this->encode(substr($sortedQueryStringTmp, 1));

        $sign = base64_encode(hash_hmac("sha1", $stringToSign, $this->accessKeySecret . "&",true));

        $signature = $this->encode($sign);

        return array(
            "method" => "POST",
            "signature"=>$signature,
            "sortedQueryStringTmp"=>$sortedQueryStringTmp,
            );

    }

    //编码
	private function encode($str){
        $res = urlencode($str);
        $res = preg_replace("/\+/", "%20", $res);
        $res = preg_replace("/\*/", "%2A", $res);
        $res = preg_replace("/%7E/", "~", $res);
        return $res;
    }

    //发送请求
    private function fetchContent($url, $method, $body){
        $ch = curl_init();

        if($method == 'POST') {
            curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
            curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        } else {
            $url .= '?'.$body;
        }

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "x-sdk-client" => "php/2.0.0"
        ));

        if(substr($url, 0,5) == 'https') {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }

        $rtn = curl_exec($ch);

        if($rtn === false) {
            trigger_error("[CURL_" . curl_errno($ch) . "]: " . curl_error($ch), E_USER_ERROR);
        }

        curl_close($ch);

        return $rtn;
    }

    //返回信息,这里可以自己写日志
    private function infoLog($res){
        $status = $res['Code'];
        $message = array(
            "isp.RAM_PERMISSION_DENY" => "RAM权限DENY",
            "isv.OUT_OF_SERVICE" => "业务停机",
            "isv.PRODUCT_UN_SUBSCRIPT" => "未开通云通信产品的阿里云客户",
            "isv.PRODUCT_UNSUBSCRIBE" => "产品未开通",
            "isv.ACCOUNT_NOT_EXISTS" => "账户不存在",
            "isv.ACCOUNT_ABNORMAL" => "账户异常",
            "isv.SMS_TEMPLATE_ILLEGAL" => "短信模板不合法",
            "isv.SMS_SIGNATURE_ILLEGAL" => "短信签名不合法",
            "isv.INVALID_PARAMETERS" => "参数异常",
            "isp.SYSTEM_ERROR"=>"系统错误",
            "isv.MOBILE_NUMBER_ILLEGAL"=>"非法手机号",
            "isv.MOBILE_COUNT_OVER_LIMIT"=>"手机号码数量超过限制",
            "isv.TEMPLATE_MISSING_PARAMETERS"=>"模板缺少变量",
            "isv.BUSINESS_LIMIT_CONTROL"=>"业务限流",
            "isv.INVALID_JSON_PARAM"=>"JSON参数不合法，只接受字符串值",
            "isv.BLACK_KEY_CONTROL_LIMIT"=>"黑名单管控",
            "isv.PARAM_LENGTH_LIMIT"=>"参数超出长度限制",
            "isv.PARAM_NOT_SUPPORT_URL"=>"不支持URL",
            "isv.AMOUNT_NOT_ENOUGH"=>"账户余额不足",
        );
        if(isset($message[$status])){
            return $message[$status];
        }
        return $status;
    }
}

?>