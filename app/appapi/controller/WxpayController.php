<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2014 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Dean <zxxjjforever@163.com>
// +----------------------------------------------------------------------

namespace app\appapi\controller;

use cmf\controller\HomeBaseController;
use think\Db;
/**
 * 微信公众号支付
 */
class WxpayController extends HomebaseController {
	
	
	//微信公众号 扫码支付  回调
	public function notify_native() {
        require_once CMF_ROOT."sdk/wxpay/lib/WxPay.Api.php";
        require_once CMF_ROOT."sdk/wxpay/lib/WxPay.Notify.php";
        
        //获取通知的数据
		//$xml = $GLOBALS['HTTP_RAW_POST_DATA'];
		$xml=file_get_contents("php://input");
        
        $this->logwx("xml:".$xml);
/* 		$xml='<xml><appid><![CDATA[wxdbba8ae5cc57d71e]]></appid>
<attach><![CDATA[充值钻石,价值为600]]></attach>
<bank_type><![CDATA[OTHERS]]></bank_type>
<cash_fee><![CDATA[1]]></cash_fee>
<fee_type><![CDATA[CNY]]></fee_type>
<is_subscribe><![CDATA[Y]]></is_subscribe>
<mch_id><![CDATA[1230458202]]></mch_id>
<nonce_str><![CDATA[8foicjpqo7nokvbf1zm5n27tkdeez3xl]]></nonce_str>
<openid><![CDATA[oT36SuBF-U-oGxaySyaIzN9omiRA]]></openid>
<out_trade_no><![CDATA[22776_22776_1221161133_4409]]></out_trade_no>
<result_code><![CDATA[SUCCESS]]></result_code>
<return_code><![CDATA[SUCCESS]]></return_code>
<sign><![CDATA[DADE6279C771A953E20DD6A4ACD01520]]></sign>
<time_end><![CDATA[20191221161149]]></time_end>
<total_fee>1</total_fee>
<trade_type><![CDATA[NATIVE]]></trade_type>
<transaction_id><![CDATA[4200000432201912210816702746]]></transaction_id>
</xml>'; */
        
		//如果返回成功则验证签名
		try {
			$res = \WxPayResults::Init($xml);
		} catch (WxPayException $e){
			$msg = $e->errorMessage();
			return false;
		}
        
        $transaction_id=$res['transaction_id'];
        $this->logwx("transaction_id:".$transaction_id);
        
        $input = new \WxPayOrderQuery();
		$input->SetTransaction_id($transaction_id);
		$result = \WxPayApi::orderQuery($input);
        
        $this->logwx("result:".json_encode($result));
        
		//Log::DEBUG("query:" . json_encode($result));
		if(array_key_exists("return_code", $result)
			&& array_key_exists("result_code", $result)
			&& $result["return_code"] == "SUCCESS"
			&& $result["result_code"] == "SUCCESS")
		{
            
            
            $attach=$result['attach'];
			$out_trade_no=$result['out_trade_no'];
			$fee=$result['total_fee'];
			$transaction_id=$result['transaction_id'];
            
            $where['orderno']=$out_trade_no;
            $where['type']=2;
            
            $data=[
                'trade_no'=>$transaction_id
            ];
            
            $this->logwx("where:".json_encode($where));	
            $res=handelCharge($where,$data);
            if($res==0){
                $this->logwx("orderno:".$out_trade_no.' 订单信息不存在');	
                echo $this -> returnInfo("FAIL","订单信息不存在");
                exit;	
            }
            
            $this->logwx("成功");
            echo $this -> returnInfo("SUCCESS","OK");
            exit;
		}
        
        echo $this -> returnInfo("FAIL","签名失败");
        exit;										
	}

	//微信公众号内支付 回调
	public function notify_jsapi() {
        require_once CMF_ROOT."sdk/wxpay/lib/WxPay.Api.php";
        require_once CMF_ROOT."sdk/wxpay/lib/WxPay.Notify.php";
        
        //获取通知的数据
		//$xml = $GLOBALS['HTTP_RAW_POST_DATA'];
		$xml=file_get_contents("php://input");
        
        $this->logwx2("xml:".$xml);
        
		//如果返回成功则验证签名
		try {
			$res = \WxPayResults::Init($xml);
		} catch (WxPayException $e){
			$msg = $e->errorMessage();
			return false;
		}
        
        $transaction_id=$res['transaction_id'];
        
        $input = new \WxPayOrderQuery();
		$input->SetTransaction_id($transaction_id);
		$result = \WxPayApi::orderQuery($input);
        
		//Log::DEBUG("query:" . json_encode($result));
		if(array_key_exists("return_code", $result)
			&& array_key_exists("result_code", $result)
			&& $result["return_code"] == "SUCCESS"
			&& $result["result_code"] == "SUCCESS")
		{
            $attach=$result['attach'];
			$out_trade_no=$result['out_trade_no'];
			$fee=$result['total_fee'];
			$transaction_id=$result['transaction_id'];
            
            $where['orderno']=$out_trade_no;
            $where['type']=2;
            
            $data=[
                'trade_no'=>$transaction_id
            ];
            
            $this->logwx2("where:".json_encode($where));	
            $res=handelCharge($where,$data);
            if($res==0){
                $this->logwx2("orderno:".$out_trade_no.' 订单信息不存在');	
                echo $this -> returnInfo("FAIL","订单信息不存在");
                exit;	
            }
            
            $this->logwx2("成功");
            echo $this -> returnInfo("SUCCESS","OK");
            exit;
		}
		
        echo $this -> returnInfo("FAIL","签名失败");
        exit;										
	}

	private function returnInfo($type,$msg){
		if($type == "SUCCESS"){
			return $returnXml = "<xml><return_code><![CDATA[{$type}]]></return_code></xml>";
		}else{
			return $returnXml = "<xml><return_code><![CDATA[{$type}]]></return_code><return_msg><![CDATA[{$msg}]]></return_msg></xml>";
		}
	}
    
	/* PC扫码 */
	public function logwx($msg){
		file_put_contents(CMF_ROOT.'data/paylog/logwxpay_'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').'  msg:'.$msg."\r\n",FILE_APPEND);
	}	
    
    /* 公众号 */
	public function logwx2($msg){
		file_put_contents(CMF_ROOT.'data/paylog/logwxpay2_'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').'  msg:'.$msg."\r\n",FILE_APPEND);
	}		
						

}


