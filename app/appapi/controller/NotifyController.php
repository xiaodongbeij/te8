<?php


namespace app\appapi\controller;


use cmf\controller\HomeBaseController;
use think\cache\driver\Redis;
use think\Db;
use think\Queue;

class NotifyController extends HomebaseController
{

    //芒果回调
    public function mg_notify(){

        $path = CMF_DATA . 'paylog/mg/'.date('Ym').'/';
        $filename = date('dH').'.txt';
        if(!is_dir($path)){
            $flag = mkdir($path,0777,true);
        }

        $data = file_get_contents("php://input");
        if (!$data) {
            echo 'failure';
            exit;
        }
        //收到回调！
        file_put_contents( $path.$filename,'收到回调：'.$data.PHP_EOL,FILE_APPEND);

        $data = json_decode($data, true);
        if ($data['code'] != 'S00') die('failure');

        $order = Db::table('cmf_order')->where('order_sn', $data['merchantOrderNumber'])->field('channel_id,order_status,pay_status,user_id')->find();
        if (!$order) die('订单异常');
        if ($order['order_status'] == 4 && $order['pay_status'] == 1) die('已处理');

        $message = "天鹅芒果支付收款通知:\n 平台单号：" . $data['orderNumber'] . "\n 商户订单号:" . $data['merchantOrderNumber'] ."\n 会员账号:" . $order['user_id'] . "\n 充值金额:" . $data['actualAmount'];

        //验签
        $md5key = Db::table('cmf_channel')->where('id', $order['channel_id'])->value('key');
        $old_sign = $data['sign'];
        unset($data['sign']);
        ksort($data);
        $md5str = "";
        foreach ($data as $key => $val) {
            $md5str = $md5str . $key . "=" . $val . "&";
        }
        $sign = strtoupper(md5($md5str . "merchantKey=" . $md5key));
        if ($sign != $old_sign) die('sign error');
        $result = $this->call_logic($data['merchantOrderNumber'], $data['actualAmount'], $data['orderNumber']);
        if ($result){
            $this->telegram($message);
            $str = "交易成功！订单号：".$data["merchantOrderNumber"];
            file_put_contents( $path.$filename,$str.PHP_EOL,FILE_APPEND);
            exit("SUCCESS");
        }else{
            $str = "订单号：".$data["merchantOrderNumber"].'本地数据库异常';
            file_put_contents( $path.$filename,$str.PHP_EOL,FILE_APPEND);
            die('FAIL');
        }
    }

    //U付回调
    public function uf_notify()
    {

        $path = CMF_DATA . 'paylog/uf/'.date('Ym').'/';
        $filename = date('dH').'.txt';
        if(!is_dir($path)){
            $flag = mkdir($path,0777,true);
        }
        
        $returnArray = array( // 返回字段
            "memberid" => $_REQUEST["memberid"], // 商户ID
            "orderid" =>  $_REQUEST["orderid"], // 订单号
            "amount" =>  $_REQUEST["amount"], // 交易金额
            "datetime" =>  $_REQUEST["datetime"], // 交易时间
            "transaction_id" =>  $_REQUEST["transaction_id"], // 支付流水号
            "returncode" => $_REQUEST["returncode"],
        );
        
        
       
        
        //收到回调！
        file_put_contents( $path.$filename,'收到回调：'.json_encode($returnArray).PHP_EOL,FILE_APPEND);

        $order = Db::table('cmf_order')->where('order_sn', $returnArray['orderid'])->field('channel_id,order_status,pay_status,user_id,pay_money')->find();
        
         $message = "天鹅UU支付收款通知:\n 平台单号：" . $returnArray['orderid'] . "\n 商户订单号:" . $returnArray['memberid'] ."\n 会员账号:" . $order['user_id'] . "\n 充值金额:" . $returnArray['amount'];
         
        if (!$order) die('订单异常');
        if ($order['order_status'] == 4 && $order['pay_status'] == 1) die('已处理');

        $md5key = Db::table('cmf_channel')->where('id', $order['channel_id'])->value('key');
        ksort($returnArray);
        reset($returnArray);
        $md5str = "";
        foreach ($returnArray as $key => $val) {
            $md5str = $md5str . $key . "=" . $val . "&";
        }
        $sign = strtoupper(md5($md5str . "key=" . $md5key));
        if ($sign == $_REQUEST["sign"]) {
            if ($_REQUEST["returncode"] == "00") {
                $result = $this->call_logic($returnArray['orderid'], $returnArray['amount'], $returnArray['transaction_id']);
                if ($result){
                    $this->telegram($message);
                    $str = "交易成功！订单号：".$_REQUEST["orderid"];
                    file_put_contents( $path.$filename,$str.PHP_EOL,FILE_APPEND);
                    exit("OK");
                }else{
                    $str = "订单号：".$_REQUEST["orderid"].'本地数据库异常';
                    file_put_contents( $path.$filename,$str.PHP_EOL,FILE_APPEND);
                    die('error');
                }

            }
        }
    }
    
    
    //熊猫回调
    public function xm_notify()
    {

        $data = file_get_contents('php://input');
        if (!$data) {
            echo 'failure';
            exit;
        }
        $data = json_decode($data, true);
        
        if (!$data['success'] || $data['data']['status'] != 3) {
            echo "failure";
            exit;
        }
        $data = $data['data'];
        
        $order = Db::table('cmf_order')->where('order_sn', $data['request_no'])->field('channel_id,order_status,pay_status,user_id')->find();
      
        $message = "天鹅熊猫支付收款通知:\n 平台单号：" . $data['order_no'] . "\n 商户订单号:" . $data['request_no'] ."\n 会员账号:" . $order['user_id'] . "\n 充值金额:" . $data['amount'];
        
        if (!$order) die('订单异常');
        if ($order['order_status'] == 4 && $order['pay_status'] == 1) die('已处理');
  
        $result = $this->call_logic($data['request_no'], $data['order_amount'], $data['order_no']);
    
        if ($result) {
            
            $this->telegram($message);
            echo 'success';
        } else {
            echo 'error';
        }
    }
    
    //yy回调
    public function yy_notify()
    {
        $data = $_GET;
        $sign = $data['sign'];
        unset($data['sign']);
        unset($data['sign_type']);
        $order = Db::table('cmf_order')->where('order_sn', $data['out_trade_no'])->field('channel_id,order_status,pay_status,user_id,pay_money')->find();
        
        $message = "天鹅收款通知:\n 平台单号：" . $data['out_trade_no'] . "\n 商户订单号:" . $data['trade_no'] ."\n 会员账号:" . $order['user_id'] . "\n 充值金额:" . $data['money'];
        
        if (!$order) die('订单异常');
        if ($order['order_status'] == 4 && $order['pay_status'] == 1) die('已处理');
        $key = Db::table('cmf_channel')->where('id', $order['channel_id'])->value('key');
        $res = $this->ver_sign($data, $sign, $key);
        if (!$res) die('验签失败');
        $result = $this->call_logic($data['out_trade_no'], $data['money'], $data['trade_no']);
        if ($result && $data['trade_status'] == "TRADE_SUCCESS") {
            
            $this->telegram($message);
            echo 'success';
        } else {
            echo 'error';
        }
    }

    //md5验签名
    protected function ver_sign($data, $sign, $key)
    {
        $str = '';
        ksort($data);
        foreach ($data as $k => $v) {
            $str .= $k . '=' . $v . '&';
        }
        $str = substr($str, 0, -1);
        $str .= $key;
        $temp = md5($str);
        if ($temp == $sign) {
            return true;
        } else {
            return false;
        }
    }

    //回调逻辑,参数：天鹅订单号，金额，三方订单号
    protected function call_logic($order_sn, $money, $trade_no)
    {
        //开启事务
        Db::startTrans();
        try {
//            $order = Db::table('cmf_order')->where('id','=','1_202012030258166891')->find();
            $order = Db::table('cmf_order')->where('order_sn', '=', $order_sn)->find();
            //更新订单
            $data = [
                'order_status' => 4,
                'pay_status' => 1,
                'pay_time' => time(),
                'pay_money' => $money,
                'third_order_sn' => $trade_no
            ];
            $res1 = Db::table('cmf_order')->where('order_sn', $order_sn)->update($data);
            $user = Db::table('cmf_user')->where('id', $order['user_id'])->find();
            //change记录
            $change = [
                'user_id' => $order['user_id'],
                'change_type' => 1,
//                'money' => $user['user_money'],
                'money' => $user['coin'],
//                'next_money' => $user['user_money'] + $order['order_money'],
                'next_money' => $user['coin'] + $order['order_money'],
                'change_money' => $order['order_money'],
                'remark' => '充值',
                'addtime' => time()
            ];
            $res2 = Db::table('cmf_user_change')->insert($change);
            //更新用户信息
            $userinfo = [
//                'user_money' => $user['user_money'] + $order['order_money'],
                'coin' => $user['coin'] + $order['order_money'],
                'count_money' => $user['count_money'] + $order['order_money'],
            ];
            $level = $user['level'];
            $user_channel = Db::table('cmf_user_channel')->select();
            foreach ($user_channel as $v) {
                if ($user['count_money'] > $v['min_money']) {
                    $level = $v['id'];
                }
            }
            if ($level != $user['level']) {
                $userinfo['level'] = $level;
            }

            $res3 = Db::table('cmf_user')->where('id', $order['user_id'])->update($userinfo);

            if ($res1 && $res2 && $res3) {
                Db::commit();
                return true;
            } else {
                Db::rollback();
                return false;
            }
        } catch (\Exception $e) {
            Db::rollback();
            return false;
        }
    }

    //彩票回调
    public function ticket_notify()
    {
        
        $jobData = input();
        if(empty($jobData)) die('数据不能为空');
        $jobHandlerClassName  = 'app\appapi\job\Ticket'; 
 
        $jobQueueName  	  =  "ticketJobQueue"; 
        
        $isPushed = Queue::push($jobHandlerClassName , $jobData , $jobQueueName );	
        return '000000';

    }
    
    
    //四海回调
    public function sihai_notify()
    {
        $data = input();
        
        if($data['status'] != 1)
        {
            die('参数错误');
        }
        
        // file_put_contents('sihai.txt',json_encode($data));
        $order = Db::table('cmf_order')->where('order_sn', $data['orderId'])->field('channel_id,order_status,pay_status,user_id,pay_money')->find();
        
        $message = "天鹅四海支付收款通知:\n 平台单号：" . $data['orderId'] . "\n 商户订单号:" . $data['sysOrderId'] ."\n 会员账号:" . $order['user_id'] . "\n 充值金额:" . $data['orderAmt'];
        
        if (!$order) die('订单异常');
        if ($order['order_status'] == 4 && $order['pay_status'] == 1) die('已处理');
        $key = Db::table('cmf_channel')->where('id', $order['channel_id'])->value('key');
        
   
        $result = $this->call_logic($data['orderId'], $data['orderAmt'], $data['sysOrderId']);
        if ($result) {
            $this->telegram($message);
            echo 'success';
        } else {
            echo 'error';
        }
    }
    
    
    public function telegram($message = '测试')
    {
        $telegram =  config('telegram');
        $message = urlencode($message);
        $url = $telegram . $message;
        file_get_contents($url);
    }

}