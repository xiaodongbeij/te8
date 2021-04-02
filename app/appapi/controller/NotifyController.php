<?php


namespace app\appapi\controller;


use cmf\controller\HomeBaseController;
use think\cache\driver\Redis;
use think\Db;

class NotifyController extends HomebaseController
{

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

    //回调逻辑
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
//        echo '000000';die;
        $config = getConfigPri();
        
        $key = $config['tripartite_game_key'];

        $req = input();

        $path = CMF_DATA . 'paylog/ticket/'.date('Ym').'/';
        $filename = date('dH').'.txt';
        if(!is_dir($path)){
            $flag = mkdir($path,0777,true);
        }
        if (empty($req)){
            file_put_contents( $path.$filename,'----------------------回调参数错误----------------------'.PHP_EOL,FILE_APPEND);
            die('参数错误');
        }

        file_put_contents( $path.$filename,'----------------------notify_start----------------------'.PHP_EOL,FILE_APPEND);
        file_put_contents( $path.$filename,json_encode($req).PHP_EOL,FILE_APPEND);
        $sign = $req['sign'];
        unset($req['sign']);
        //验签
        $sign_res = $this->ver_sign($req,$sign,$key);
        if (!$sign_res){
            file_put_contents($path.$filename, '验签失败'.PHP_EOL,FILE_APPEND);
            echo '验签失败';die;
        }
        $data = json_decode($req['data'],true);
//        var_dump($data);die;
        $result = true;
        $openCode = true;
        foreach ($data['list'] as $k => $v){
            //查找订单信息
            $order = Db::table('cmf_game_ticket')->where('order_id',$v['billNo'])->find();

            
            if ($order['status'] == 1 || $order['status'] == 2) continue;
            if ($v['status'] == 3){
                //中奖
                //开启事务
                Db::startTrans();
                try {
                    //用户信息
                    $user = Db::table('cmf_user')->where('id',$order['user_id'])->find();
                    //更新订单
                    $res1 = Db::table('cmf_game_ticket')
                        ->where('order_id',$v['billNo'])
                        ->update([
                            'ok' => 1,
                            'status' => 1,
                            'prize' => $v['prize'],
                            'prize_codes' => $v['openCode']
                        ]);

                    $res2 = user_change_action($order['user_id'],3,$v['prize'],'彩票中奖');

                    if ($res1 && $res2) {
                        Db::commit();

                        $msg = [
                            'msg' => [[
                                '_method_' => 'winning',
                                'ct' =>  $order['show_name'].'中奖' . $v['prize'] . '元',
                                'user_nicename' => $user['user_nicename'],
                                'level' => getLevel($user['consumption']),
                                'money' => $v['prize'],
                                'show_name' => $order['show_name'],
                            ]]
                        ];
                        
                        $msg['msg'][0]['type'] = $v['prize'] > 500 ? 1 : 0;
                        redisPush($msg);

                        file_put_contents( $path.$filename,'订单:'. $v['billNo'] .'更新成功'.PHP_EOL,FILE_APPEND);
                    } else {
                        Db::rollback();
                        file_put_contents( $path.$filename,'订单:'. $v['billNo'] .'更新失败'.PHP_EOL,FILE_APPEND);
                        $result = false;
                    }
                }catch (\Exception $e) {
                    Db::rollback();
                    file_put_contents( $path.$filename,'订单:'. $v['billNo'] .'更新失败'.PHP_EOL,FILE_APPEND);
                    $result = false;
                }
            }else{
                $up = [
                    'ok' => 2,
                    'status' => 1,
                    'prize_codes' => $v['openCode']
                ];
                $res = Db::table('cmf_game_ticket')
                    ->where('order_id',$v['billNo'])
                    ->update($up);
                if ($res){
                    file_put_contents( $path.$filename,'订单:'. $v['billNo'] .'更新成功'.PHP_EOL,FILE_APPEND);
                }else{
                    file_put_contents( $path.$filename,'订单:'. $v['billNo'] .'更新失败'.PHP_EOL,FILE_APPEND);
                    $result = false;
                }
            }
        }

        file_put_contents( $path.$filename,'----------------------notify_end----------------------'.PHP_EOL,FILE_APPEND);
        if ($result){
            echo '000000';
        }else{
            echo '错误';
        }
        die;
//        file_put_contents($path.$filename, '验签失败:'.$str.'|'.$temp.'|'.$sign.PHP_EOL,FILE_APPEND);
//        file_put_contents( $path.$filename,'----------------------notify_end----------------------'.PHP_EOL,FILE_APPEND);
    }
    
    function sf_notify()
    {
        $data = file_get_contents("php://input");
    
        $data = json_decode($data, true);
    
        $sign = $data['sign'];
        unset($data['sign']);
        $order = Db::table('cmf_order')->where('order_sn', $data['merchantOrder'])->find();
        if (!$order) die('error');
        if ($order['order_status'] == 4) die('error');
        $key = Db::table('cmf_channel')->where('id', $order['channel_id'])->value('key');
        $sign_new = md5Sign($data, $key);
    
        if ($sign != $sign_new) die('error');
        if ($data['orderStatus'] != 2) die('success');
        Db::startTrans();
    //更新订单
        try {
            $order_insert = [
                'order_status' => 4,
                'pay_status' => 1,
                'pay_time' => time(),
                'pay_money' => $data['transAmount'] / 100, //分
                'third_order_sn' => $data['outTradeNo']
            ];
            $res1 = Db::table('cmf_order')->where('order_sn', $data['merchantOrder'])->update($order_insert);
            $user = Db::table('cmf_user')->where('id', $order['user_id'])->find();
    //change记录
            $change = [
                'user_id' => $order['user_id'],
                'change_type' => 1,
                'money' => $user['coin'],
                'next_money' => $user['coin'] + ($data['transAmount'] / 100),
                'change_money' => $data['transAmount'] / 100,
                'remark' => '充值',
                'addtime' => time()
            ];
            $res2 = Db::table('cmf_user_change')->insert($change);
    //更新用户信息
            $userinfo = [
                'coin' => $user['coin'] + ($data['transAmount'] / 100),
                'count_money' => $user['count_money'] + ($data['transAmount'] / 100),
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
                die('success');
            } else {
                Db::rollback();
                die('error');
            }
        } catch (\Exception $e) {
            Db::rollback();
            die('success');
        }
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