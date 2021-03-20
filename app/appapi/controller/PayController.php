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
 * 支付回调
 */
class PayController extends HomebaseController {
	
	
	//支付宝 回调
	public function notify_ali() {
        $configpri=getConfigPri();
		require_once(CMF_ROOT."sdk/alipay_app/alipay.config.php");
        $alipay_config['partner'] = $configpri['aliapp_partner'];
		require_once(CMF_ROOT."sdk/alipay_app/lib/alipay_core.function.php");
		require_once(CMF_ROOT."sdk/alipay_app/lib/alipay_rsa.function.php");
		require_once(CMF_ROOT."sdk/alipay_app/lib/alipay_notify.class.php");

		//计算得出通知验证结果
		$alipayNotify = new \AlipayNotify($alipay_config);
		$verify_result = $alipayNotify->verifyNotify();
		$this->logali("ali_data:".json_encode($_POST));
		if($verify_result) {//验证成功
			//商户订单号
			$out_trade_no = $_POST['out_trade_no'];
			//支付宝交易号
			$trade_no = $_POST['trade_no'];
			//交易状态
			$trade_status = $_POST['trade_status'];
			
			//交易金额
			$total_fee = $_POST['total_fee'];
			
			if($_POST['trade_status'] == 'TRADE_FINISHED') {
				//判断该笔订单是否在商户网站中已经做过处理
				//如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
				//如果有做过处理，不执行商户的业务程序
					
				//注意：
				//退款日期超过可退款期限后（如三个月可退款），支付宝系统发送该交易状态通知
				//请务必判断请求时的total_fee、seller_id与通知时获取的total_fee、seller_id为一致的

				//调试用，写文本函数记录程序运行情况是否正常
				//logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
		
			}else if ($_POST['trade_status'] == 'TRADE_SUCCESS') {
				//判断该笔订单是否在商户网站中已经做过处理
				//如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
				//如果有做过处理，不执行商户的业务程序
					
				//注意：
				//付款完成后，支付宝系统发送该交易状态通知
				//请务必判断请求时的total_fee、seller_id与通知时获取的total_fee、seller_id为一致的

				//调试用，写文本函数记录程序运行情况是否正常
				//logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
                $where['orderno']=$out_trade_no;
                $where['money']=$total_fee;
                $where['type']=1;
                
                $data=[
                    'trade_no'=>$trade_no
                ];

				$this->logali("where:".json_encode($where));	
                $res=handelCharge($where,$data);
				if($res==0){
                    $this->logali("orderno:".$out_trade_no.' 订单信息不存在');	
                    echo "fail";
                    exit;
				}
                
                $this->logali("成功");
                echo "success";		//请不要修改或删除
                exit;    
			}
			//——请根据您的业务逻辑来编写程序（以上代码仅作参考）——

			echo "fail";		//请不要修改或删除
			exit;
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		}else {
			$this->logali("验证失败");		
			//验证失败
			echo "fail";
            exit;
			//调试用，写文本函数记录程序运行情况是否正常
			//logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
		}			
		
	}
	/* 支付宝支付 */
	
	/* 微信支付 */	
    private $wxDate = null;	
	public function notify_wx(){
		$config=getConfigPri();

		//$xmlInfo = $GLOBALS['HTTP_RAW_POST_DATA'];

		$xmlInfo=file_get_contents("php://input"); 

		//解析xml
		$arrayInfo = $this -> xmlToArray($xmlInfo);
		
		$this -> wxDate = $arrayInfo;
		$this -> logwx("wx_data:".json_encode($arrayInfo));//log打印保存
		if($arrayInfo['return_code'] == "SUCCESS"){
			// if(isset($arrayInfo['return_msg']) && $arrayInfo['return_msg'] != null){
			// 	echo $this -> returnInfo("FAIL","签名失败");
			// 	$this -> logwx("签名失败:".$sign);//log打印保存
			// 	exit;
			// }else{
				$wxSign = $arrayInfo['sign'];
				unset($arrayInfo['sign']);
				$arrayInfo['appid']  =  $config['wx_appid'];
				$arrayInfo['mch_id'] =  $config['wx_mchid'];
				$key =  $config['wx_key'];
				ksort($arrayInfo);//按照字典排序参数数组
				$sign = $this -> sign($arrayInfo,$key);//生成签名
				$this -> logwx("数据打印测试签名signmy:".$sign.":::微信sign:".$wxSign);//log打印保存
				if($this -> checkSign($wxSign,$sign)){
					echo $this -> returnInfo("SUCCESS","OK");
					$this -> logwx("签名验证结果成功:".$sign);//log打印保存
					$this -> orderServer();//订单处理业务逻辑
					exit;
				}else{
					echo $this -> returnInfo("FAIL","签名失败");
					$this -> logwx("签名验证结果失败:本地加密：".$sign.'：：：：：三方加密'.$wxSign);//log打印保存
					exit;
				}
			//}
		}else{
			echo $this -> returnInfo("FAIL","签名失败");
			$this -> logwx($arrayInfo['return_code']);//log打印保存
			exit;
		}			
	}
	
	private function returnInfo($type,$msg){
		if($type == "SUCCESS"){
			return $returnXml = "<xml><return_code><![CDATA[{$type}]]></return_code></xml>";
		}else{
			return $returnXml = "<xml><return_code><![CDATA[{$type}]]></return_code><return_msg><![CDATA[{$msg}]]></return_msg></xml>";
		}
	}		
	
	//签名验证
	private function checkSign($sign1,$sign2){
		return trim($sign1) == trim($sign2);
	}
	/* 订单查询加值业务处理
	 * @param orderNum 订单号	   
	 */
	private function orderServer(){
		$info = $this -> wxDate;
		$this->logwx("info:".json_encode($info));
        $where['orderno']=$info['out_trade_no'];
        $where['type']=2;
        
        $trade_no=$info['transaction_id'];
        
        $data=[
            'trade_no'=>$trade_no
        ];
        
        $this->logwx("where:".json_encode($where));	
        $res=handelCharge($where,$data);
        if($res==0){
            $this->logwx("orderno:".$out_trade_no.' 订单信息不存在');	
            return false;
        }
        
        $this->logwx("成功");
        return true;
	}		
	/**
	* sign拼装获取
	*/
	private function sign($param,$key){
		
		$sign = "";
		foreach($param as $k => $v){
			$sign .= $k."=".$v."&";
		}
	
		$sign .= "key=".$key;
		$sign = strtoupper(md5($sign));
		return $sign;
	
	}
	/**
	* xml转为数组
	*/
	private function xmlToArray($xmlStr){
		$msg = array(); 
		$postStr = $xmlStr; 
		$msg = (array)simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA); 
		return $msg;
	}
	
	/* 微信支付 */

	/* 苹果支付 */
	
	public function notify_ios(){
		$content=file_get_contents("php://input");  
		$data = json_decode($content,true); 

        $this->logios("data:".json_encode($data));
        
		$receipt = isset($data["receipt-data"])?$data["receipt-data"]:'';
		$isSandbox = isset($data["sandbox"])?$data["sandbox"]:'0';
		$out_trade_no = isset($data["out_trade_no"])?$data["out_trade_no"]:'';
		$version_ios = isset($data["version_ios"])?$data["version_ios"]:'';
        
		$info = $this->getReceiptData($receipt, $version_ios);   
		
		$this->logios("info:".json_encode($info));
		
		$iforderinfo=Db::name("charge_user")->where(["trade_no"=>$info['transaction_id'],"type"=>'3'])->find();

		if($iforderinfo){
			echo '{"status":"fail","info":"非法提交-001"}';
            exit;
		}
        
        $chargeinfo=Db::name("charge_rules")->where(["product_id"=>$info['product_id']])->find();
        if(!$chargeinfo){
            echo '{"status":"fail","info":"非法提交-002"}';
            exit;
        }

		//判断订单是否存在
        $where['orderno']=$out_trade_no;
        $where['coin']=$chargeinfo['coin_ios'];
        $where['type']=3;
        
        $trade_no=$info['transaction_id'];
        $ambient=$info['ambient'];
        
        $data=[
            'trade_no'=>$trade_no,
            'ambient'=>$ambient,
        ];
        
        $this->logios("where:".json_encode($where));	
        
        $res=handelCharge($where,$data);
        if($res==0){
            $this->logios("orderno:".$out_trade_no.' 订单信息不存在');	
            echo '{"status":"fail","info":"订单信息不存在-003"}'; 		
			exit;
        }
        
        $this->logios("成功");
        echo '{"status":"success","info":"充值支付成功"}';
		exit;
	}		
	public function getReceiptData($receipt, $version_ios){ 
		$config=getConfigPub();
        
        $ios_shelves=$config['ios_shelves'];
        
        $this->logios("version_ios:".$version_ios);
        $this->logios("ios_shelves:".$ios_shelves);
        $ambient=0;
		if ($version_ios == $ios_shelves) {   
			//沙盒
			$endpoint = 'https://sandbox.itunes.apple.com/verifyReceipt';
            $ambient=0;
		}else {  
			//生产
			$endpoint = 'https://buy.itunes.apple.com/verifyReceipt'; 
            $ambient=1;
		}   

		$postData = json_encode(   
				array('receipt-data' => $receipt)   
		);   

		$ch = curl_init($endpoint);

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);	//关闭安全验证
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);  	//关闭安全验证
		curl_setopt($ch, CURLOPT_POST, true);   
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);   

		$response = curl_exec($ch);   
		$errno    = curl_errno($ch);   
		$errmsg   = curl_error($ch);   
		curl_close($ch);   
        
        $this->logios("getReceiptData response:".json_encode($response));
        $this->logios("getReceiptData errno:".json_encode($errno));
        $this->logios("getReceiptData errmsg:".json_encode($errmsg));

		if($errno != 0) {   
			echo '{"status":"fail","info":"服务器出错，请联系管理员"}';
			exit;
		}   
		$data = json_decode($response,1);   

		if (!is_array($data)) {   
			echo '{"status":"fail","info":"验证失败,如有疑问请联系管理"}';
			exit;
		}   

		if (!isset($data['status']) || $data['status'] != 0) {   
			echo '{"status":"fail","info":"验证失败,如有疑问请联系管理"}';
			exit;
		}   

        $newdata=end($data['receipt']['in_app']);
		return array(     
			'product_id'     =>  $newdata['product_id'],   
			'transaction_id' =>  $newdata['transaction_id'],   
			'ambient' =>  $ambient,   
		);
	}   
		
	/* 苹果支付 */

	//微信小程序支付回调
	private $wxMiniDate = null;
	public function notify_wx_mini(){

		$xmlInfo=file_get_contents("php://input"); 

		//解析xml
		$arrayInfo = $this -> xmlToArray($xmlInfo);
		
		$this -> wxMiniDate = $arrayInfo;
		$this -> logwx_mini("wxmini_data:".json_encode($arrayInfo));//log打印保存

		$config=getConfigPri();

		if($arrayInfo['return_code'] == "SUCCESS"){
	
			$wxSign = $arrayInfo['sign']; //支付返回的签名
			unset($arrayInfo['sign']);
			$arrayInfo['appid']  =  $config['wx_mini_appid'];
			$arrayInfo['mch_id'] =  $config['wx_mini_mchid'];
			$key =  $config['wx_mini_key'];
			ksort($arrayInfo);//按照字典排序参数数组

			$sign = $this -> sign($arrayInfo,$key);//生成签名

			$this -> logwx_mini("数据打印对比签名signmy:".$sign.":::微信sign:".$wxSign);//log打印保存
			if($this -> checkSign($wxSign,$sign)){
				echo $this -> returnInfo("SUCCESS","OK");
				$this -> logwx_mini("签名验证结果成功:".$sign);//log打印保存
				$this -> wxMiniOrderServer();//微信小程序订单处理业务逻辑
				exit;
			}else{
				echo $this -> returnInfo("FAIL","签名失败");
				$this -> logwx_mini("签名验证结果失败:本地加密：".$sign.'：：：：：三方加密'.$wxSign);//log打印保存
				exit;
			}

		}else{
			echo $this -> returnInfo("FAIL","签名失败");
			$this -> logwx_mini($arrayInfo['return_code']);//log打印保存
			exit;
		}	

	}

	/* 微信小程序订单查询加值业务处理
	 * @param orderNum 订单号	   
	 */
	private function wxMiniOrderServer(){
		$info = $this -> wxMiniDate;
		$this->logwx_mini("wxMiniOrderServer info:".json_encode($info));
		$out_trade_no=$info['out_trade_no'];
        $where['orderno']=$info['out_trade_no'];
        $where['type']=4;
        
        $trade_no=$info['transaction_id']; //微信支付订单号
        
        $data=[
            'trade_no'=>$trade_no
        ];
        
        $this->logwx_mini("where:".json_encode($where));	
        $res=handelCharge($where,$data);
        if($res==0){
            $this->logwx_mini("orderno:".$out_trade_no.' 订单信息不存在');	
            return false;
        }
        
        $this->logwx_mini("成功");
        return true;
	}
			
	/* 打印log */
	public function logali($msg){
		file_put_contents(CMF_ROOT.'data/paylog/logali_'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').'  msg:'.$msg."\r\n",FILE_APPEND);
	}		
	/* 打印log */
	public function logwx($msg){
		file_put_contents(CMF_ROOT.'data/paylog/logwx_'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').'  msg:'.$msg."\r\n",FILE_APPEND);
	}			
	/* 打印log */
	public function logios($msg){
		file_put_contents(CMF_ROOT.'data/paylog/logios_'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').'  msg:'.$msg."\r\n",FILE_APPEND);
	}

	/* 打印log */
	public function logwx_mini($msg){
		file_put_contents(CMF_ROOT.'data/paylog/logwx_mini_'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').'  msg:'.$msg."\r\n",FILE_APPEND);
	}					

}


