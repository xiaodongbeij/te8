<?php

/**
 * 翻译
 */
class Api_Translate extends PhalApi_Api
{
    protected $url;
    protected $app_id;
    protected $sec_key;

    public function __construct()
    {
        $config = getConfigPri();
        $this->url = $config['bai_du_url'];
        $this->app_id = $config['bai_du_app_id'];
        $this->sec_key = $config['bai_du_sec_key'];
    }
    
    
    public function getRules()
    {
        return array(
            'getTranslate' => array(
                'q' => array('name' => 'q', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '原内容'),
                'to' => array('name' => 'to', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '目标语言'),
            )
        );
    }
    
    /**
     * 翻译
     * @desc 翻译
     * @return int code 操作码，0表示成功
     * @return string src 原内容
     * @return string dst 翻译结果
     */
    public function getTranslate()
    {
        $q = checkNull($this->q);
        $from = 'auto';
        $to = checkNull($this->to);
        if(empty($q) || empty($from) || empty($to)) return '参数错误';
        $args = array(
            'q' => $q,
            'appid' => $this->app_id,
            'salt' => rand(10000,99999),
            'from' => $from,
            'to' => $to,
    
        );
        $args['sign'] = $this->buildSign($q, $this->app_id, $args['salt'], $this->sec_key);
    
        $ret = $this->callOnce($this->url, $args);
        $ret = json_decode($ret, true);
        if(!empty($ret['trans_result']))
        {
            return ['info'=>['src' => $ret['trans_result'][0]['src'], 'dst' => $ret['trans_result'][0]['dst']]];
        }
        return ['info'=>['src' => $q,'dst' => $q]];
    }
    
    protected function buildSign($query, $appID, $salt, $secKey)
    {
        $str = $appID . $query . $salt . $secKey;
        $ret = md5($str);
        return $ret;
    }
    
    

    protected function call($url, $args=null, $method="post", $testflag = 0, $timeout = 10, $headers=array())
    {
    

        $ret = false;
        $i = 0; 
        while($ret === false) 
        {
            if($i > 1)
                break;
            if($i > 0) 
            {
                sleep(1);
            }
            $ret = $this->callOnce($url, $args, $method, false, $timeout, $headers);
            $i++;
        }
        return $ret;
    }
    
    
    protected function callOnce($url, $args=null, $method="post", $withCookie = false, $timeout = 10, $headers=array())
    {
        $ch = curl_init();
        if($method == "post") 
        {
            $data = $this->convert($args);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_POST, 1);
        }
        else 
        {
            $data = $this->convert($args);
            if($data) 
            {
                if(stripos($url, "?") > 0) 
                {
                    $url .= "&$data";
                }
                else 
                {
                    $url .= "?$data";
                }
            }
        }
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if(!empty($headers)) 
        {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        if($withCookie)
        {
            curl_setopt($ch, CURLOPT_COOKIEJAR, $_COOKIE);
        }
        $r = curl_exec($ch);
        curl_close($ch);
        return $r;
    }
    
    protected function convert(&$args)
    {
        $data = '';
        if (is_array($args))
        {
            foreach ($args as $key=>$val)
            {
                if (is_array($val))
                {
                    foreach ($val as $k=>$v)
                    {
                        $data .= $key.'['.$k.']='.rawurlencode($v).'&';
                    }
                }
                else
                {
                    $data .="$key=".rawurlencode($val)."&";
                }
            }
            return trim($data, "&");
        }
        return $args;
    }
}
